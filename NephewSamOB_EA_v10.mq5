//+------------------------------------------------------------------+
//|   Nephew Sam OB EA  v10 — VISUAL MATCH                          |
//|   Exact match to screenshot:                                     |
//|   GREEN box = Bull OB → BUY when price touches box from above   |
//|   RED box   = Bear OB → SELL when price touches box from below  |
//|   SL = opposite side of box                                      |
//|   Entry = immediate on touch, no confirmation needed             |
//|   FIX: M1 scanning added for more signals (4-15/day)            |
//|   FIX: ZoneDup threshold reduced to 5 PIPs                      |
//|   FIX: Cooldown off-by-one corrected                            |
//+------------------------------------------------------------------+
#property copyright "NephewSam OB EA v10"
#property version   "10.00"
#property strict
#include <Trade\Trade.mqh>
#include <Trade\PositionInfo.mqh>
input group "=== TIMEFRAMES ==="
input ENUM_TIMEFRAMES  HTF              = PERIOD_M15;  // Higher TF context
input ENUM_TIMEFRAMES  MTF              = PERIOD_M5;   // Mid TF zone detection
input ENUM_TIMEFRAMES  LTF              = PERIOD_M1;   // Execution TF (M1)
input group "=== OB DETECTION ==="
input int    FractalBars                = 3;
input int    ScanDepth                  = 300;
input bool   BreakByClose               = true;
input group "=== ENTRY ==="
input double TouchBuffer                = 2.0;   // pips — how close to box edge to trigger
input int    MaxTradesPerZone           = 1;     // 1 trade per box like indicator
input int    CooldownBars               = 5;
input bool   HTFConfirm                 = false;
input group "=== DYNAMIC LOT ==="
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
input int    MinSL                      = 10;
input int    MaxSL                      = 300;
input group "=== TRAILING & BE ==="
input bool   UseTrail                   = true;
input double TrailPips                  = 20.0;
input bool   UseBreakeven               = true;
input double BEPips                     = 15.0;
input group "=== SESSION ==="
input bool   UseSession                 = true;
input int    SessStart                  = 7;
input int    SessEnd                    = 20;
input group "=== ZONES ==="
input int    ZoneExpiry                 = 5000;
input bool   DeleteAfterHit             = true;  // delete box after 1 trade, like indicator
input group "=== DAILY LIMITS ==="
input bool   UseDaily                   = true;
input double DailyTarget                = 100.0;
input double DailyLossMax               = 50.0;
//=============================================================
struct OBZone {
   double   hi, lo;        // box boundaries
   bool     bull;          // green=bull, red=bear
   bool     active;
   int      hits, age, lastHitBar;
};
CTrade        T;
CPositionInfo P;
double        PIP, PT;
int           DIG;
// HTF zones (M15)
OBZone   HZ[200];
int      HZC=0;
datetime lastH=0;
// MTF zones (M5)
OBZone   MZ[200];
int      MZC=0;
datetime lastM=0;
int      currentMBar=0;
// LTF zones (M1) — execution timeframe, most signals
OBZone   LZ[200];
int      LZC=0;
datetime lastL=0;
int      currentLBar=0;
int      totalTrades=0;
double   dayStartBal=0, dailyPnL=0;
datetime lastDay=0;
bool     dayDone=false;
//=============================================================
int OnInit() {
   DIG=(int)SymbolInfoInteger(_Symbol,SYMBOL_DIGITS);
   PT=SymbolInfoDouble(_Symbol,SYMBOL_POINT);
   PIP=(DIG==3||DIG==5)?PT*10:PT;
   T.SetExpertMagicNumber(MagicNumber);
   T.SetDeviationInPoints(Slippage);
   T.SetTypeFilling(ORDER_FILLING_RETURN);
   HZC=0; MZC=0; LZC=0;
   dayStartBal=AccountInfoDouble(ACCOUNT_BALANCE);
   Print("NS OB EA v10 | M15+M5+M1 | VISUAL MATCH | Bal=",dayStartBal);
   return INIT_SUCCEEDED;
}
void OnDeinit(const int r) {
   Print("Stopped. Trades=",totalTrades," Bal=",AccountInfoDouble(ACCOUNT_BALANCE));
}
//=============================================================
void OnTick() {
   UpdateDaily();
   if(UseTrail)     DoTrail();
   if(UseBreakeven) DoBreakeven();
   // HTF scan on new M15 bar
   datetime hBar=iTime(_Symbol,HTF,0);
   if(hBar!=lastH) {
      lastH=hBar;
      ScanZones(HTF,HZ,HZC,"HTF");
   }
   // MTF scan on new M5 bar
   datetime mBar=iTime(_Symbol,MTF,0);
   if(mBar!=lastM) {
      lastM=mBar; currentMBar++;
      ScanZones(MTF,MZ,MZC,"M5");
      AgeAndClean(MZ,MZC);
      AgeAndClean(HZ,HZC);
   }
   // LTF scan on new M1 bar — most signals come from here
   datetime lBar=iTime(_Symbol,LTF,0);
   if(lBar!=lastL) {
      lastL=lBar; currentLBar++;
      ScanZones(LTF,LZ,LZC,"M1");
      AgeAndClean(LZ,LZC);
   }
   if(dayDone) return;
   if(UseSession&&!InSess()) return;
   if(MyTrades()>=MaxTrades) return;
   // Check entries every tick across all timeframes
   CheckEntries();
}
//=============================================================
// ZONE DETECTION
// Green box (Bull OB): last bearish candle before bullish breakout
// Red box  (Bear OB): last bullish candle before bearish breakout
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
      // FRACTAL HIGH broken → BULL OB (green box)
      bool isFH=FractalBars==3?
         (H[i-1]<H[i]&&H[i+1]<H[i]):
         (H[i-2]<H[i]&&H[i-1]<H[i]&&H[i+1]<H[i]&&H[i+2]<H[i]);
      if(isFH&&(BreakByClose?curC>H[i]:curH>H[i])) {
         int bestK=-1; double minL=DBL_MAX;
         for(int k=i-1;k>=2;k--)
            if(C[k]<O[k]&&L[k]<minL){bestK=k;minL=L[k];}
         if(bestK>=0&&ZC<199) {
            double zHi=O[bestK];
            double zLo=L[bestK];
            if(!ZoneDup(Z,ZC,zHi,zLo,true)) {
               OBZone nz;
               nz.hi=zHi; nz.lo=zLo; nz.bull=true;
               nz.active=true; nz.hits=0; nz.age=0; nz.lastHitBar=0;
               Z[ZC++]=nz;
               if(true) Print(lbl," 🟩 BULL BOX hi=",DoubleToString(zHi,DIG),
                  " lo=",DoubleToString(zLo,DIG));
            }
         }
      }
      // FRACTAL LOW broken → BEAR OB (red box)
      bool isFL=FractalBars==3?
         (L[i-1]>L[i]&&L[i+1]>L[i]):
         (L[i-2]>L[i]&&L[i-1]>L[i]&&L[i+1]>L[i]&&L[i+2]>L[i]);
      if(isFL&&(BreakByClose?curC<L[i]:curL<L[i])) {
         int bestK=-1; double maxH=0;
         for(int k=i-1;k>=2;k--)
            if(C[k]>O[k]&&H[k]>maxH){bestK=k;maxH=H[k];}
         if(bestK>=0&&ZC<199) {
            double zHi=H[bestK];
            double zLo=O[bestK];
            if(!ZoneDup(Z,ZC,zHi,zLo,false)) {
               OBZone nz;
               nz.hi=zHi; nz.lo=zLo; nz.bull=false;
               nz.active=true; nz.hits=0; nz.age=0; nz.lastHitBar=0;
               Z[ZC++]=nz;
               if(true) Print(lbl," 🟥 BEAR BOX hi=",DoubleToString(zHi,DIG),
                  " lo=",DoubleToString(zLo,DIG));
            }
         }
      }
   }
   // Deactivate boxes price has blown through completely
   for(int z=0;z<ZC;z++) {
      if(!Z[z].active) continue;
      if( Z[z].bull&&curC<Z[z].lo-10*PIP) Z[z].active=false;
      if(!Z[z].bull&&curC>Z[z].hi+10*PIP) Z[z].active=false;
   }
}
//=============================================================
// ENTRIES — check M5 zones and M1 zones every tick
//=============================================================
void CheckEntries() {
   double ask=SymbolInfoDouble(_Symbol,SYMBOL_ASK);
   double bid=SymbolInfoDouble(_Symbol,SYMBOL_BID);
   double buf=TouchBuffer*PIP;
   bool htfBull=false,htfBear=false;
   for(int z=0;z<HZC;z++) {
      if(!HZ[z].active) continue;
      if(HZ[z].bull) htfBull=true; else htfBear=true;
   }
   // --- Check M5 zones ---
   CheckZoneEntries(MZ,MZC,currentMBar,htfBull,htfBear,ask,bid,buf,"M5");
   // --- Check M1 zones ---
   CheckZoneEntries(LZ,LZC,currentLBar,htfBull,htfBear,ask,bid,buf,"M1");
}
//=============================================================
void CheckZoneEntries(OBZone &Z[], int ZC, int curBar,
                      bool htfBull, bool htfBear,
                      double ask, double bid, double buf, string src) {
   // 🟩 BULL — price drops INTO green box → BUY
   for(int z=0;z<ZC;z++) {
      if(!Z[z].active||!Z[z].bull) continue;
      if(Z[z].hits>=MaxTradesPerZone) continue;
      // FIX: <= instead of < to correct cooldown off-by-one
      if(curBar-Z[z].lastHitBar<=CooldownBars&&Z[z].hits>0) continue;
      if(HTFConfirm&&htfBear&&!htfBull) continue;
      if(MyTrades()>=MaxTrades) break;
      bool touched=bid<=Z[z].hi+buf&&bid>=Z[z].lo-buf;
      if(!touched) continue;
      double sl=NormalizeDouble(Z[z].lo-2*PIP,DIG);
      double slPips=MathAbs(ask-sl)/PIP;
      if(slPips<MinSL){sl=NormalizeDouble(ask-MinSL*PIP,DIG);slPips=MinSL;}
      if(slPips>MaxSL){sl=NormalizeDouble(ask-MaxSL*PIP,DIG);slPips=MaxSL;}
      double tp=NormalizeDouble(ask+slPips*RR*PIP,DIG);
      double lot=CalcLot(slPips);
      if(!ValidSLTP(true,ask,sl,tp)) continue;
      if(T.Buy(lot,_Symbol,ask,sl,tp,"NS10_BUY")) {
         Z[z].hits++; Z[z].lastHitBar=curBar; totalTrades++;
         Print("🟩 BUY [",src,"] #",totalTrades," lot=",lot,
            " @",DoubleToString(ask,DIG),
            " SL=",DoubleToString(sl,DIG),
            " TP=",DoubleToString(tp,DIG),
            " pnl=$",DoubleToString(dailyPnL,2));
         if(DeleteAfterHit) Z[z].active=false;
      } else Print("BUY FAIL:",T.ResultRetcode()," ",T.ResultRetcodeDescription());
      break;
   }
   // 🟥 BEAR — price rises INTO red box → SELL
   for(int z=0;z<ZC;z++) {
      if(!Z[z].active||Z[z].bull) continue;
      if(Z[z].hits>=MaxTradesPerZone) continue;
      // FIX: <= instead of < to correct cooldown off-by-one
      if(curBar-Z[z].lastHitBar<=CooldownBars&&Z[z].hits>0) continue;
      if(HTFConfirm&&htfBull&&!htfBear) continue;
      if(MyTrades()>=MaxTrades) break;
      bool touched=ask>=Z[z].lo-buf&&ask<=Z[z].hi+buf;
      if(!touched) continue;
      double sl=NormalizeDouble(Z[z].hi+2*PIP,DIG);
      double slPips=MathAbs(sl-bid)/PIP;
      if(slPips<MinSL){sl=NormalizeDouble(bid+MinSL*PIP,DIG);slPips=MinSL;}
      if(slPips>MaxSL){sl=NormalizeDouble(bid+MaxSL*PIP,DIG);slPips=MaxSL;}
      double tp=NormalizeDouble(bid-slPips*RR*PIP,DIG);
      double lot=CalcLot(slPips);
      if(!ValidSLTP(false,bid,sl,tp)) continue;
      if(T.Sell(lot,_Symbol,bid,sl,tp,"NS10_SELL")) {
         Z[z].hits++; Z[z].lastHitBar=curBar; totalTrades++;
         Print("🟥 SELL [",src,"] #",totalTrades," lot=",lot,
            " @",DoubleToString(bid,DIG),
            " SL=",DoubleToString(sl,DIG),
            " TP=",DoubleToString(tp,DIG),
            " pnl=$",DoubleToString(dailyPnL,2));
         if(DeleteAfterHit) Z[z].active=false;
      } else Print("SELL FAIL:",T.ResultRetcode()," ",T.ResultRetcodeDescription());
      break;
   }
}
//=============================================================
void UpdateDaily() {
   MqlDateTime dt; TimeToStruct(TimeCurrent(),dt);
   datetime today=StringToTime(IntegerToString(dt.year)+"."+
      IntegerToString(dt.mon)+"."+IntegerToString(dt.day));
   if(today!=lastDay) {
      lastDay=today; dayStartBal=AccountInfoDouble(ACCOUNT_BALANCE);
      dailyPnL=0; dayDone=false;
      Print("=== NEW DAY Bal=",dayStartBal," ===");
   }
   dailyPnL=AccountInfoDouble(ACCOUNT_BALANCE)-dayStartBal;
   if(!dayDone&&UseDaily) {
      if(dailyPnL>=DailyTarget)  { dayDone=true; Print("✅ TARGET +$",DoubleToString(dailyPnL,2)); }
      if(dailyPnL<=-DailyLossMax){ dayDone=true; Print("🛑 LOSS -$",DoubleToString(MathAbs(dailyPnL),2)); }
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
   return MathMin(MathMax(MathMax(lot,minLot),StartLot),maxLot);
}
void DoTrail() {
   double trail=TrailPips*PIP;
   for(int i=0;i<PositionsTotal();i++) {
      if(!P.SelectByIndex(i)) continue;
      if(P.Symbol()!=_Symbol||P.Magic()!=MagicNumber) continue;
      double cSL=P.StopLoss(),bid=SymbolInfoDouble(_Symbol,SYMBOL_BID),ask=SymbolInfoDouble(_Symbol,SYMBOL_ASK);
      if(P.PositionType()==POSITION_TYPE_BUY){double nSL=NormalizeDouble(bid-trail,DIG);if(nSL>cSL+PIP)T.PositionModify(P.Ticket(),nSL,P.TakeProfit());}
      else{double nSL=NormalizeDouble(ask+trail,DIG);if(cSL==0||nSL<cSL-PIP)T.PositionModify(P.Ticket(),nSL,P.TakeProfit());}
   }
}
void DoBreakeven() {
   for(int i=0;i<PositionsTotal();i++) {
      if(!P.SelectByIndex(i)) continue;
      if(P.Symbol()!=_Symbol||P.Magic()!=MagicNumber) continue;
      double open=P.PriceOpen(),cSL=P.StopLoss(),bePips=BEPips*PIP;
      double bid=SymbolInfoDouble(_Symbol,SYMBOL_BID),ask=SymbolInfoDouble(_Symbol,SYMBOL_ASK);
      if(P.PositionType()==POSITION_TYPE_BUY){if(bid-open>=bePips&&cSL<open)T.PositionModify(P.Ticket(),NormalizeDouble(open+PIP,DIG),P.TakeProfit());}
      else{if(open-ask>=bePips&&(cSL>open||cSL==0))T.PositionModify(P.Ticket(),NormalizeDouble(open-PIP,DIG),P.TakeProfit());}
   }
}
void AgeAndClean(OBZone &Z[], int &ZC) {
   for(int z=0;z<ZC;z++){if(!Z[z].active)continue;Z[z].age++;if(ZoneExpiry>0&&Z[z].age>ZoneExpiry)Z[z].active=false;}
}
// FIX: ZoneDup threshold reduced from 10 PIPs to 5 PIPs — allows more distinct zones
bool ZoneDup(OBZone &Z[], int ZC, double hi, double lo, bool bull) {
   for(int z=0;z<ZC;z++)
      if(Z[z].active&&Z[z].bull==bull&&MathAbs(Z[z].hi-hi)<5*PIP&&MathAbs(Z[z].lo-lo)<5*PIP) return true;
   return false;
}
bool ValidSLTP(bool buy,double entry,double sl,double tp){
   double minD=MathMax((double)SymbolInfoInteger(_Symbol,SYMBOL_TRADE_STOPS_LEVEL)*PT,10.0*PT);
   return buy?(entry-sl)>minD&&(tp-entry)>minD:(sl-entry)>minD&&(entry-tp)>minD;
}
bool InSess(){MqlDateTime dt;TimeToStruct(TimeGMT(),dt);return dt.hour>=SessStart&&dt.hour<SessEnd;}
int MyTrades(){
   int n=0;
   for(int i=0;i<PositionsTotal();i++)
      if(P.SelectByIndex(i)&&P.Symbol()==_Symbol&&P.Magic()==MagicNumber)n++;
   return n;
}
