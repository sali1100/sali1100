//+------------------------------------------------------------------+
//|   Nephew Sam OB EA  v9                                           |
//|   M5 Entry + M15 Confirmation                                    |
//|   KEY FIX: Rejection confirmation — candle must CLOSE BACK       |
//|   outside OB zone before entry                                   |
//|   KEY FIX: Continuous zone scanning every bar (no dead months)   |
//+------------------------------------------------------------------+
#property copyright "NephewSam OB EA v9"
#property version   "9.00"
#property strict
#include <Trade\Trade.mqh>
#include <Trade\PositionInfo.mqh>
input group "=== TIMEFRAMES ==="
input ENUM_TIMEFRAMES  HTF              = PERIOD_M15;
input ENUM_TIMEFRAMES  LTF              = PERIOD_M5;
input group "=== NEPHEW SAM OB ==="
input int    FractalBars                = 3;
input int    ScanDepth                  = 300;   // 300 bars = 25hrs on M5
input bool   BreakByClose               = true;
input group "=== ENTRY — REJECTION CONFIRMATION ==="
input double ZoneBuf                    = 3.0;   // pips inside zone to trigger watch
input double RejectionBuf               = 1.0;   // pips outside zone to confirm rejection
input int    MaxTradesPerZone           = 3;
input int    CooldownBars               = 3;
input bool   HTFConfirm                 = false;
input group "=== DYNAMIC LOT SCALING ==="
input double StartLot                   = 0.08;
input double RiskPercent                = 2.0;
input bool   ScaleLots                  = true;
input double MaxLot                     = 5.0;
input group "=== TRADE ==="
input int    MagicNumber                = 20240;
input int    MaxTrades                  = 3;
input int    Slippage                   = 50;
input group "=== RISK ==="
input double RR                         = 2.0;
input int    MinSL                      = 15;
input int    MaxSL                      = 200;
input group "=== TRAILING & BREAKEVEN ==="
input bool   UseTrail                   = true;
input double TrailPips                  = 25.0;
input bool   UseBreakeven               = true;
input double BEPips                     = 20.0;
input group "=== SESSION ==="
input bool   UseSession                 = true;
input int    SessStart                  = 7;
input int    SessEnd                    = 20;
input group "=== ZONE MANAGEMENT ==="
input int    ZoneExpiry                 = 3000;  // long expiry — zones stay alive
input bool   DeleteOnFill               = false;
input group "=== DAILY LIMITS ==="
input bool   StopAtDailyTarget          = true;
input double DailyTarget                = 100.0;
input bool   StopAtDailyLoss            = true;
input double DailyLossLimit             = 50.0;
input group "=== DEBUG ==="
input bool   PrintLogs                  = true;
//=============================================================
struct OBZone {
   double   hi, lo;
   bool     bull, active;
   int      hits, age, lastHitBar;
   bool     priceInZone;    // price has entered zone
   bool     waitingEntry;   // waiting for rejection close
};
CTrade        T;
CPositionInfo P;
double        PIP, PT;
int           DIG;
OBZone   HZ[200], LZ[200];
int      HZC=0, LZC=0;
datetime lastH=0, lastL=0;
int      totalTrades=0;
int      currentBar=0;
double   dayStartBal=0;
datetime lastDay=0;
double   dailyPnL=0;
bool     dayTargetHit=false;
bool     dayLossHit=false;
//=============================================================
int OnInit() {
   DIG = (int)SymbolInfoInteger(_Symbol, SYMBOL_DIGITS);
   PT  = SymbolInfoDouble(_Symbol, SYMBOL_POINT);
   PIP = (DIG==3||DIG==5) ? PT*10 : PT;
   T.SetExpertMagicNumber(MagicNumber);
   T.SetDeviationInPoints(Slippage);
   T.SetTypeFilling(ORDER_FILLING_RETURN);
   HZC=0; LZC=0;
   dayStartBal = AccountInfoDouble(ACCOUNT_BALANCE);
   Print("NS OB EA v9 | Rejection Confirm | M5+M15 | Bal=",dayStartBal);
   return INIT_SUCCEEDED;
}
void OnDeinit(const int r) {
   Print("EA stopped. Trades=",totalTrades," Bal=",AccountInfoDouble(ACCOUNT_BALANCE));
}
//=============================================================
void OnTick() {
   UpdateDaily();
   if(UseTrail)     DoTrail();
   if(UseBreakeven) DoBreakeven();
   // HTF scan
   datetime hBar=iTime(_Symbol,HTF,0);
   if(hBar!=lastH) {
      lastH=hBar;
      ScanZones(HTF,HZ,HZC,"HTF");
   }
   // LTF scan — every new M5 bar
   datetime lBar=iTime(_Symbol,LTF,0);
   if(lBar!=lastL) {
      lastL=lBar;
      currentBar++;
      ScanZones(LTF,LZ,LZC,"LTF");
      AgeAndClean(LZ,LZC);
      AgeAndClean(HZ,HZC);
      // KEY: update rejection state on every new bar
      UpdateRejectionState();
   }
   if(dayTargetHit||dayLossHit) return;
   if(UseSession&&!InSess()) return;
   if(MyTrades()>=MaxTrades) return;
   // Check entries every tick after rejection confirmed
   CheckEntries();
}
//=============================================================
// SCAN ZONES — runs every bar, detects new OBs continuously
//=============================================================
void ScanZones(ENUM_TIMEFRAMES tf, OBZone &Z[], int &ZC, string lbl) {
   int need=ScanDepth+15;
   if(iBars(_Symbol,tf)<need) return;
   double H[],L[],C[],O[];
   ArraySetAsSeries(H,true); ArraySetAsSeries(L,true);
   ArraySetAsSeries(C,true); ArraySetAsSeries(O,true);
   if(CopyHigh(_Symbol,tf,0,need,H)<need) return;
   CopyLow(_Symbol,tf,0,need,L);
   CopyClose(_Symbol,tf,0,need,C);
   CopyOpen(_Symbol,tf,0,need,O);
   double curC=C[1], curH=H[1], curL=L[1];
   for(int i=FractalBars+2; i<=ScanDepth-2; i++) {
      // FRACTAL HIGH → BULLISH OB
      bool isFH = FractalBars==3 ?
         (H[i-1]<H[i] && H[i+1]<H[i]) :
         (H[i-2]<H[i] && H[i-1]<H[i] && H[i+1]<H[i] && H[i+2]<H[i]);
      if(isFH && (BreakByClose ? curC>H[i] : curH>H[i])) {
         int bestK=-1; double minL=DBL_MAX;
         for(int k=i-1;k>=2;k--)
            if(C[k]<O[k] && L[k]<minL) { bestK=k; minL=L[k]; }
         if(bestK>=0 && ZC<199) {
            double zHi=O[bestK], zLo=L[bestK];
            if(!ZoneDup(Z,ZC,zHi,zLo,true)) {
               OBZone nz;
               nz.hi=zHi; nz.lo=zLo; nz.bull=true;
               nz.active=true; nz.hits=0; nz.age=0;
               nz.lastHitBar=0; nz.priceInZone=false; nz.waitingEntry=false;
               Z[ZC++]=nz;
               if(PrintLogs) Print(lbl," BULL OB hi=",DoubleToString(zHi,DIG),
                  " lo=",DoubleToString(zLo,DIG));
            }
         }
      }
      // FRACTAL LOW → BEARISH OB
      bool isFL = FractalBars==3 ?
         (L[i-1]>L[i] && L[i+1]>L[i]) :
         (L[i-2]>L[i] && L[i-1]>L[i] && L[i+1]>L[i] && L[i+2]>L[i]);
      if(isFL && (BreakByClose ? curC<L[i] : curL<L[i])) {
         int bestK=-1; double maxH=0;
         for(int k=i-1;k>=2;k--)
            if(C[k]>O[k] && H[k]>maxH) { bestK=k; maxH=H[k]; }
         if(bestK>=0 && ZC<199) {
            double zHi=H[bestK], zLo=O[bestK];
            if(!ZoneDup(Z,ZC,zHi,zLo,false)) {
               OBZone nz;
               nz.hi=zHi; nz.lo=zLo; nz.bull=false;
               nz.active=true; nz.hits=0; nz.age=0;
               nz.lastHitBar=0; nz.priceInZone=false; nz.waitingEntry=false;
               Z[ZC++]=nz;
               if(PrintLogs) Print(lbl," BEAR OB hi=",DoubleToString(zHi,DIG),
                  " lo=",DoubleToString(zLo,DIG));
            }
         }
      }
   }
   // Invalidate fully filled zones (price closes through)
   for(int z=0;z<ZC;z++) {
      if(!Z[z].active) continue;
      if( Z[z].bull && curC<Z[z].lo-5*PIP) Z[z].active=false;
      if(!Z[z].bull && curC>Z[z].hi+5*PIP) Z[z].active=false;
   }
}
//=============================================================
// REJECTION STATE — called on every new bar close
// Step 1: detect price entered zone
// Step 2: detect candle CLOSED back outside = rejection confirmed
//=============================================================
void UpdateRejectionState() {
   // Use last closed bar (bar 1)
   double lastClose = iClose(_Symbol,LTF,1);
   double lastHigh  = iHigh (_Symbol,LTF,1);
   double lastLow   = iLow  (_Symbol,LTF,1);
   double lastOpen  = iOpen (_Symbol,LTF,1);
   double buf       = ZoneBuf*PIP;
   double rejBuf    = RejectionBuf*PIP;
   for(int z=0;z<LZC;z++) {
      if(!LZ[z].active) continue;
      if(LZ[z].hits>=MaxTradesPerZone) continue;
      if(LZ[z].bull) {
         // BULL zone: price dips into zone from above
         // Entry condition: candle wick entered zone BUT closed back ABOVE zone high
         bool wickInZone  = lastLow <= LZ[z].hi + buf;    // wick touched zone
         bool closeAbove  = lastClose >= LZ[z].hi - rejBuf; // closed back above/at zone top
         bool notTooDeep  = lastLow >= LZ[z].lo - buf;    // didn't close deep below
         if(wickInZone && closeAbove && notTooDeep && !LZ[z].waitingEntry) {
            LZ[z].waitingEntry = true;
            if(PrintLogs) Print("BULL REJECTION confirmed @ zone hi=",
               DoubleToString(LZ[z].hi,DIG)," close=",DoubleToString(lastClose,DIG));
         }
         // Reset if price breaks below zone entirely
         if(lastClose < LZ[z].lo - 5*PIP) {
            LZ[z].waitingEntry = false;
            LZ[z].active = false;
         }
      } else {
         // BEAR zone: price rises into zone from below
         bool wickInZone  = lastHigh >= LZ[z].lo - buf;
         bool closeBelow  = lastClose <= LZ[z].lo + rejBuf;
         bool notTooDeep  = lastHigh <= LZ[z].hi + buf;
         if(wickInZone && closeBelow && notTooDeep && !LZ[z].waitingEntry) {
            LZ[z].waitingEntry = true;
            if(PrintLogs) Print("BEAR REJECTION confirmed @ zone lo=",
               DoubleToString(LZ[z].lo,DIG)," close=",DoubleToString(lastClose,DIG));
         }
         if(lastClose > LZ[z].hi + 5*PIP) {
            LZ[z].waitingEntry = false;
            LZ[z].active = false;
         }
      }
   }
}
//=============================================================
// CHECK ENTRIES — only zones with rejection confirmed
//=============================================================
void CheckEntries() {
   double ask=SymbolInfoDouble(_Symbol,SYMBOL_ASK);
   double bid=SymbolInfoDouble(_Symbol,SYMBOL_BID);
   bool htfBull=false, htfBear=false;
   for(int z=0;z<HZC;z++) {
      if(!HZ[z].active) continue;
      if(HZ[z].bull) htfBull=true; else htfBear=true;
   }
   // BULL — rejection confirmed, enter on pullback
   for(int z=0;z<LZC;z++) {
      if(!LZ[z].active||!LZ[z].bull) continue;
      if(!LZ[z].waitingEntry) continue;
      if(LZ[z].hits>=MaxTradesPerZone) continue;
      if(currentBar-LZ[z].lastHitBar<CooldownBars&&LZ[z].hits>0) continue;
      if(HTFConfirm&&htfBear&&!htfBull) continue;
      if(MyTrades()>=MaxTrades) break;
      // Enter when price is near zone top
      if(bid<=LZ[z].hi+ZoneBuf*PIP && bid>=LZ[z].lo-ZoneBuf*PIP) {
         double sl    = NormalizeDouble(LZ[z].lo-3*PIP,DIG);
         double slPips= MathAbs(ask-sl)/PIP;
         if(slPips<MinSL){sl=NormalizeDouble(ask-MinSL*PIP,DIG);slPips=MinSL;}
         if(slPips>MaxSL){sl=NormalizeDouble(ask-MaxSL*PIP,DIG);slPips=MaxSL;}
         double tp    = NormalizeDouble(ask+slPips*RR*PIP,DIG);
         double lot   = CalcLot(slPips);
         if(!ValidSLTP(true,ask,sl,tp)) continue;
         if(T.Buy(lot,_Symbol,ask,sl,tp,"NS9_BUY")) {
            LZ[z].hits++; LZ[z].lastHitBar=currentBar;
            LZ[z].waitingEntry=false;
            totalTrades++;
            Print("BUY #",totalTrades," lot=",lot,
               " @",DoubleToString(ask,DIG),
               " SL:",DoubleToString(sl,DIG),
               " TP:",DoubleToString(tp,DIG),
               " PnL=$",DoubleToString(dailyPnL,2));
            if(DeleteOnFill&&LZ[z].hits>=MaxTradesPerZone) LZ[z].active=false;
         } else Print("BUY FAIL:",T.ResultRetcode()," ",T.ResultRetcodeDescription());
      }
   }
   // BEAR — rejection confirmed
   for(int z=0;z<LZC;z++) {
      if(!LZ[z].active||LZ[z].bull) continue;
      if(!LZ[z].waitingEntry) continue;
      if(LZ[z].hits>=MaxTradesPerZone) continue;
      if(currentBar-LZ[z].lastHitBar<CooldownBars&&LZ[z].hits>0) continue;
      if(HTFConfirm&&htfBull&&!htfBear) continue;
      if(MyTrades()>=MaxTrades) break;
      if(ask>=LZ[z].lo-ZoneBuf*PIP && ask<=LZ[z].hi+ZoneBuf*PIP) {
         double sl    = NormalizeDouble(LZ[z].hi+3*PIP,DIG);
         double slPips= MathAbs(sl-bid)/PIP;
         if(slPips<MinSL){sl=NormalizeDouble(bid+MinSL*PIP,DIG);slPips=MinSL;}
         if(slPips>MaxSL){sl=NormalizeDouble(bid+MaxSL*PIP,DIG);slPips=MaxSL;}
         double tp    = NormalizeDouble(bid-slPips*RR*PIP,DIG);
         double lot   = CalcLot(slPips);
         if(!ValidSLTP(false,bid,sl,tp)) continue;
         if(T.Sell(lot,_Symbol,bid,sl,tp,"NS9_SELL")) {
            LZ[z].hits++; LZ[z].lastHitBar=currentBar;
            LZ[z].waitingEntry=false;
            totalTrades++;
            Print("SELL #",totalTrades," lot=",lot,
               " @",DoubleToString(bid,DIG),
               " SL:",DoubleToString(sl,DIG),
               " TP:",DoubleToString(tp,DIG),
               " PnL=$",DoubleToString(dailyPnL,2));
            if(DeleteOnFill&&LZ[z].hits>=MaxTradesPerZone) LZ[z].active=false;
         } else Print("SELL FAIL:",T.ResultRetcode()," ",T.ResultRetcodeDescription());
      }
   }
}
//=============================================================
void UpdateDaily() {
   MqlDateTime dt; TimeToStruct(TimeCurrent(),dt);
   datetime today=StringToTime(IntegerToString(dt.year)+"."+
      IntegerToString(dt.mon)+"."+IntegerToString(dt.day));
   if(today!=lastDay) {
      lastDay=today;
      dayStartBal=AccountInfoDouble(ACCOUNT_BALANCE);
      dailyPnL=0; dayTargetHit=false; dayLossHit=false;
      Print("=== NEW DAY | Bal=",dayStartBal," ===");
   }
   dailyPnL=AccountInfoDouble(ACCOUNT_BALANCE)-dayStartBal;
   if(StopAtDailyTarget&&dailyPnL>=DailyTarget&&!dayTargetHit) {
      dayTargetHit=true;
      Print("✅ DAILY TARGET +$",DoubleToString(dailyPnL,2)," — stopping today");
   }
   if(StopAtDailyLoss&&dailyPnL<=-DailyLossLimit&&!dayLossHit) {
      dayLossHit=true;
      Print("🛑 DAILY LOSS -$",DoubleToString(MathAbs(dailyPnL),2)," — stopping today");
   }
}
double CalcLot(double slPips) {
   if(!ScaleLots) return StartLot;
   double bal=AccountInfoDouble(ACCOUNT_BALANCE);
   double riskAmt=bal*(RiskPercent/100.0);
   double tickVal=SymbolInfoDouble(_Symbol,SYMBOL_TRADE_TICK_VALUE);
   double tickSize=SymbolInfoDouble(_Symbol,SYMBOL_TRADE_TICK_SIZE);
   if(tickVal<=0||tickSize<=0||slPips<=0) return StartLot;
   double pipVal=tickVal*(PIP/tickSize);
   double lot=MathFloor((riskAmt/(slPips*pipVal))*100)/100.0;
   double minLot=SymbolInfoDouble(_Symbol,SYMBOL_VOLUME_MIN);
   double maxLot=MathMin(MaxLot,SymbolInfoDouble(_Symbol,SYMBOL_VOLUME_MAX));
   lot=MathMax(MathMax(lot,minLot),StartLot);
   lot=MathMin(lot,maxLot);
   return lot;
}
void DoTrail() {
   double trail=TrailPips*PIP;
   for(int i=0;i<PositionsTotal();i++) {
      if(!P.SelectByIndex(i)) continue;
      if(P.Symbol()!=_Symbol||P.Magic()!=MagicNumber) continue;
      double cSL=P.StopLoss();
      double bid=SymbolInfoDouble(_Symbol,SYMBOL_BID);
      double ask=SymbolInfoDouble(_Symbol,SYMBOL_ASK);
      if(P.PositionType()==POSITION_TYPE_BUY) {
         double nSL=NormalizeDouble(bid-trail,DIG);
         if(nSL>cSL+PIP) T.PositionModify(P.Ticket(),nSL,P.TakeProfit());
      } else {
         double nSL=NormalizeDouble(ask+trail,DIG);
         if(cSL==0||nSL<cSL-PIP) T.PositionModify(P.Ticket(),nSL,P.TakeProfit());
      }
   }
}
void DoBreakeven() {
   double bePips=BEPips*PIP;
   for(int i=0;i<PositionsTotal();i++) {
      if(!P.SelectByIndex(i)) continue;
      if(P.Symbol()!=_Symbol||P.Magic()!=MagicNumber) continue;
      double open=P.PriceOpen(),cSL=P.StopLoss();
      double bid=SymbolInfoDouble(_Symbol,SYMBOL_BID);
      double ask=SymbolInfoDouble(_Symbol,SYMBOL_ASK);
      if(P.PositionType()==POSITION_TYPE_BUY) {
         if(bid-open>=bePips&&cSL<open)
            T.PositionModify(P.Ticket(),NormalizeDouble(open+PIP,DIG),P.TakeProfit());
      } else {
         if(open-ask>=bePips&&(cSL>open||cSL==0))
            T.PositionModify(P.Ticket(),NormalizeDouble(open-PIP,DIG),P.TakeProfit());
      }
   }
}
void AgeAndClean(OBZone &Z[], int &ZC) {
   for(int z=0;z<ZC;z++) {
      if(!Z[z].active) continue;
      Z[z].age++;
      if(ZoneExpiry>0&&Z[z].age>ZoneExpiry) Z[z].active=false;
   }
}
bool ZoneDup(OBZone &Z[], int ZC, double hi, double lo, bool bull) {
   for(int z=0;z<ZC;z++)
      if(Z[z].active&&Z[z].bull==bull&&
         MathAbs(Z[z].hi-hi)<10*PIP&&MathAbs(Z[z].lo-lo)<10*PIP) return true;
   return false;
}
bool ValidSLTP(bool buy, double entry, double sl, double tp) {
   double minD=MathMax((double)SymbolInfoInteger(_Symbol,SYMBOL_TRADE_STOPS_LEVEL)*PT,10.0*PT);
   return buy?(entry-sl)>minD&&(tp-entry)>minD:(sl-entry)>minD&&(entry-tp)>minD;
}
bool InSess() {
   MqlDateTime dt; TimeToStruct(TimeGMT(),dt);
   return dt.hour>=SessStart&&dt.hour<SessEnd;
}
int MyTrades() {
   int n=0;
   for(int i=0;i<PositionsTotal();i++)
      if(P.SelectByIndex(i)&&P.Symbol()==_Symbol&&P.Magic()==MagicNumber) n++;
   return n;
}
