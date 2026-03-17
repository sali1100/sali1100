//+------------------------------------------------------------------+
//|                                          ScalpRecoveryEA.mq5     |
//|                                                                  |
//|  Rebuilt from backtest data - LS553 profit-calc fix              |
//|                                                                  |
//|  KEY FIX: The original bot's recovery system (1.5x martingale)  |
//|  was inflating both gross profit and gross loss symmetrically,   |
//|  leaving net profit at only 8.4% of gross profit. Fix approach: |
//|  1. Momentum-confirmed recovery entries (not just distance)      |
//|  2. Dynamic basket retrace threshold (tighter in weak sessions)  |
//|  3. Session quality scoring to skip low-value trade windows      |
//|  4. Recovery cooldown after consecutive failures                 |
//+------------------------------------------------------------------+
#property copyright "ScalpRecovery EA - Rebuilt LS553"
#property version   "2.00"
#property description "High-frequency scalper with smart recovery basket management"

#include <Trade\Trade.mqh>
#include <Trade\PositionInfo.mqh>
#include <Trade\OrderInfo.mqh>

CTrade         trade;
CPositionInfo  pos;

//============================================================
//  INPUT PARAMETERS  (preserved from original backtest data)
//============================================================

input group "=== POSITION LIMITS ==="
input int      MaxPositions          = 10;      // Max total positions per symbol

input group "=== RECOVERY SYSTEM ==="
input int      MaxRecoveryTrades     = 3;       // Max recovery trades in one cycle (0=disable)
input double   LotMultiplier         = 1.5;     // Lot multiplier for recovery trades (1.0=normal)
input double   RecoveryThreshold     = -50.0;   // Loss threshold to activate recovery (USD)
input int      RecoveryDistance      = 200;     // Min distance from last recovery trade (points)
input bool     RequireMomentumForRec = true;    // [NEW FIX] Require momentum alignment on recovery
input int      RecoveryCooldownBars  = 3;       // [NEW FIX] Cooldown bars after failed recovery chain

input group "=== STOP LOSS ==="
input bool     UsePerTradeSL         = true;    // Use per-trade stop loss
input int      SweetSpot             = 500;     // SL in points (sweet spot - not too wide/tight)

input group "=== BASKET / TARGET MANAGEMENT ==="
input double   BasketRetraceFactor   = 0.7;     // Basket close at retrace (0.7 = close at 20% retrace from peak)
input double   HardTarget            = 100.0;   // Optional hard target (USD)
input double   WeakSessionRetrace    = 0.85;    // [NEW FIX] Tighter retrace in weak sessions (less drawdown tolerated)

input group "=== BREAKEVEN & TRAILING ==="
input bool     MoveToBreakeven       = true;    // Move to breakeven after X points
input int      BreakevenPoints       = 600;     // Trigger breakeven after this many points profit
input bool     TrailAfterBreakeven   = true;    // Trail stop after breakeven
input int      TrailDistance         = 500;     // Keep SL this many points behind price

input group "=== LOT SIZING ==="
input double   BaseLot               = 0.01;    // Base lot size
input double   RiskPercent           = 0.0;     // Risk % per trade (0=use BaseLot fixed)

input group "=== ENTRY SIGNAL ==="
input int      MomentumBars          = 3;       // Bars for momentum confirmation
input int      BreakoutLookback      = 4;       // Bars to define breakout range
input int      MaxSpreadPoints       = 30;      // Max allowed spread (points)

input group "=== SESSION FILTER (based on backtest hour distribution) ==="
input bool     TradeHour00           = true;
input bool     TradeHour01           = true;
input bool     TradeHour02           = false;
input bool     TradeHour03           = false;
input bool     TradeHour04           = false;
input bool     TradeHour05           = true;    // Peak hour - London open
input bool     TradeHour06           = true;
input bool     TradeHour07           = true;
input bool     TradeHour08           = true;
input bool     TradeHour09           = true;
input bool     TradeHour10           = true;
input bool     TradeHour11           = true;
input bool     TradeHour12           = false;
input bool     TradeHour13           = true;    // Peak - NY open
input bool     TradeHour14           = true;    // Peak - NY open
input bool     TradeHour15           = true;
input bool     TradeHour16           = true;
input bool     TradeHour17           = false;
input bool     TradeHour18           = false;
input bool     TradeHour19           = false;
input bool     TradeHour20           = false;
input bool     TradeHour21           = false;
input bool     TradeHour22           = false;
input bool     TradeHour23           = false;

input group "=== IDENTIFICATION ==="
input int      MagicNumber           = 553;
input string   TradeComment          = "SR_LS553";

//============================================================
//  GLOBAL STATE
//============================================================

double   g_point;
int      g_digits;

// Basket tracking
double   g_lastEntryPrice    = 0;
double   g_basketPeakProfit  = 0;
int      g_recoveryCount     = 0;
int      g_failedRecoveries  = 0;
datetime g_lastRecoveryFail  = 0;

// Session quality scoring
bool     g_hourFilter[24];

//+------------------------------------------------------------------+
int OnInit()
{
   g_point  = _Point;
   g_digits = _Digits;

   // 5-digit / 3-digit broker normalization
   if(_Digits == 3 || _Digits == 5)
      g_point *= 10;

   trade.SetExpertMagicNumber(MagicNumber);
   trade.SetDeviationInPoints(10);
   trade.SetTypeFilling(ORDER_FILLING_IOC);

   // Load hour filter into array for fast lookup
   bool hours[24] = {
      TradeHour00, TradeHour01, TradeHour02, TradeHour03,
      TradeHour04, TradeHour05, TradeHour06, TradeHour07,
      TradeHour08, TradeHour09, TradeHour10, TradeHour11,
      TradeHour12, TradeHour13, TradeHour14, TradeHour15,
      TradeHour16, TradeHour17, TradeHour18, TradeHour19,
      TradeHour20, TradeHour21, TradeHour22, TradeHour23
   };
   ArrayCopy(g_hourFilter, hours);

   Print("ScalpRecovery EA v2.00 initialized | Symbol:", _Symbol,
         " | Point:", g_point, " | Magic:", MagicNumber);
   return INIT_SUCCEEDED;
}

//+------------------------------------------------------------------+
void OnTick()
{
   // 1. Manage open positions (BE, trail)
   ManagePositions();

   // 2. Manage basket (target, retrace, recovery trigger)
   ManageBasket();

   // 3. Check if new entry is allowed
   if(!CanOpenNewTrade()) return;

   // 4. Get directional signal
   int signal = GetEntrySignal();
   if(signal == 0) return;

   // 5. Spread guard
   int spread = (int)(SymbolInfoInteger(_Symbol, SYMBOL_SPREAD));
   if(_Digits == 5 || _Digits == 3) spread = spread; // already in points for 5-digit
   if(spread > MaxSpreadPoints) return;

   // 6. Execute entry
   OpenTrade(signal, BaseLot, false);
}

//============================================================
//  TRADE PERMISSION
//============================================================

bool CanOpenNewTrade()
{
   if(CountPositions() >= MaxPositions) return false;
   if(!IsAllowedHour()) return false;

   MqlDateTime dt;
   TimeToStruct(TimeCurrent(), dt);
   if(dt.day_of_week == 0 || dt.day_of_week == 6) return false; // weekend guard

   // [NEW FIX] Recovery cooldown: if last recovery chain failed, wait
   if(g_failedRecoveries > 0)
   {
      int cooldownSeconds = RecoveryCooldownBars * PeriodSeconds(PERIOD_M1);
      if((TimeCurrent() - g_lastRecoveryFail) < cooldownSeconds)
         return false;
      g_failedRecoveries = 0; // cooldown expired, reset
   }

   return true;
}

bool IsAllowedHour()
{
   MqlDateTime dt;
   TimeToStruct(TimeCurrent(), dt);
   return g_hourFilter[dt.hour];
}

// [NEW FIX] "Strong" hours are peak activity hours with best win-rate
bool IsStrongHour()
{
   MqlDateTime dt;
   TimeToStruct(TimeCurrent(), dt);
   int h = dt.hour;
   return (h == 0 || h == 1 || h == 5 || h == 6 ||
           h == 13 || h == 14 || h == 15);
}

//============================================================
//  ENTRY SIGNAL
//  Based on backtest data characteristics:
//  - Profit/MFE correlation = 0.93 → entries must go immediately right
//  - Average hold = 14 seconds → momentum scalp on M1 price action
//  - Avg win 1.08 >> avg loss 0.70 → profit:loss ratio is key
//============================================================

int GetEntrySignal()
{
   // Require at least MomentumBars + BreakoutLookback bars
   if(Bars(_Symbol, PERIOD_M1) < MomentumBars + BreakoutLookback + 2)
      return 0;

   // --- Momentum check: consistent directional closes ---
   bool bullMomentum = true, bearMomentum = true;
   for(int i = 1; i <= MomentumBars; i++)
   {
      double c  = iClose(_Symbol, PERIOD_M1, i);
      double cn = iClose(_Symbol, PERIOD_M1, i + 1);
      if(c <= cn) bullMomentum = false;
      if(c >= cn) bearMomentum = false;
   }
   if(!bullMomentum && !bearMomentum) return 0;

   // --- Breakout of recent range ---
   double rangeHigh = -DBL_MAX, rangeLow = DBL_MAX;
   for(int i = 2; i <= MomentumBars + BreakoutLookback; i++)
   {
      rangeHigh = MathMax(rangeHigh, iHigh(_Symbol, PERIOD_M1, i));
      rangeLow  = MathMin(rangeLow,  iLow(_Symbol,  PERIOD_M1, i));
   }

   double ask = SymbolInfoDouble(_Symbol, SYMBOL_ASK);
   double bid = SymbolInfoDouble(_Symbol, SYMBOL_BID);
   double close1 = iClose(_Symbol, PERIOD_M1, 1);

   // Buy: bull momentum + price breaking above range
   if(bullMomentum && close1 > rangeHigh)
      return 1;

   // Sell: bear momentum + price breaking below range
   if(bearMomentum && close1 < rangeLow)
      return -1;

   return 0;
}

//============================================================
//  ORDER EXECUTION
//============================================================

void OpenTrade(int signal, double lot, bool isRecovery)
{
   double ask = SymbolInfoDouble(_Symbol, SYMBOL_ASK);
   double bid = SymbolInfoDouble(_Symbol, SYMBOL_BID);
   string comment = isRecovery ? TradeComment + "_REC" : TradeComment;

   bool isFirstTrade = (CountPositions() == 0);

   if(signal == 1) // Buy
   {
      double sl = UsePerTradeSL ? NormalizeDouble(ask - SweetSpot * g_point, _Digits) : 0.0;
      if(trade.Buy(lot, _Symbol, ask, sl, 0.0, comment))
      {
         g_lastEntryPrice = ask;
         if(isFirstTrade)
         {
            g_basketPeakProfit = 0;
            g_recoveryCount    = 0;
         }
      }
   }
   else if(signal == -1) // Sell
   {
      double sl = UsePerTradeSL ? NormalizeDouble(bid + SweetSpot * g_point, _Digits) : 0.0;
      if(trade.Sell(lot, _Symbol, bid, sl, 0.0, comment))
      {
         g_lastEntryPrice = bid;
         if(isFirstTrade)
         {
            g_basketPeakProfit = 0;
            g_recoveryCount    = 0;
         }
      }
   }
}

double CalcLot()
{
   if(RiskPercent > 0.0)
   {
      double balance  = AccountInfoDouble(ACCOUNT_BALANCE);
      double riskAmt  = balance * RiskPercent / 100.0;
      double tickVal  = SymbolInfoDouble(_Symbol, SYMBOL_TRADE_TICK_VALUE);
      double tickSize = SymbolInfoDouble(_Symbol, SYMBOL_TRADE_TICK_SIZE);
      double slTicks  = (SweetSpot * g_point) / tickSize;
      if(slTicks <= 0 || tickVal <= 0) return NormalizeLot(BaseLot);
      return NormalizeLot(riskAmt / (slTicks * tickVal));
   }
   return NormalizeLot(BaseLot);
}

double NormalizeLot(double lot)
{
   double mn   = SymbolInfoDouble(_Symbol, SYMBOL_VOLUME_MIN);
   double mx   = SymbolInfoDouble(_Symbol, SYMBOL_VOLUME_MAX);
   double step = SymbolInfoDouble(_Symbol, SYMBOL_VOLUME_STEP);
   lot = MathRound(lot / step) * step;
   return NormalizeDouble(MathMax(mn, MathMin(mx, lot)), 2);
}

//============================================================
//  POSITION MANAGEMENT  (breakeven + trailing)
//============================================================

void ManagePositions()
{
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      if(!pos.SelectByIndex(i)) continue;
      if(pos.Symbol() != _Symbol || pos.Magic() != MagicNumber) continue;

      double openPx = pos.PriceOpen();
      double curSL  = pos.StopLoss();

      if(pos.PositionType() == POSITION_TYPE_BUY)
      {
         double bid = SymbolInfoDouble(_Symbol, SYMBOL_BID);
         double pips = (bid - openPx) / g_point;

         // Move to breakeven
         if(MoveToBreakeven && pips >= BreakevenPoints && curSL < openPx)
         {
            double newSL = NormalizeDouble(openPx + g_point, _Digits);
            trade.PositionModify(pos.Ticket(), newSL, pos.TakeProfit());
         }

         // Trail after breakeven
         if(TrailAfterBreakeven && curSL >= openPx)
         {
            double trailSL = NormalizeDouble(bid - TrailDistance * g_point, _Digits);
            if(trailSL > curSL)
               trade.PositionModify(pos.Ticket(), trailSL, pos.TakeProfit());
         }
      }
      else // SELL
      {
         double ask = SymbolInfoDouble(_Symbol, SYMBOL_ASK);
         double pips = (openPx - ask) / g_point;

         if(MoveToBreakeven && pips >= BreakevenPoints && (curSL == 0.0 || curSL > openPx))
         {
            double newSL = NormalizeDouble(openPx - g_point, _Digits);
            trade.PositionModify(pos.Ticket(), newSL, pos.TakeProfit());
         }

         if(TrailAfterBreakeven && curSL > 0.0 && curSL <= openPx)
         {
            double trailSL = NormalizeDouble(ask + TrailDistance * g_point, _Digits);
            if(trailSL < curSL)
               trade.PositionModify(pos.Ticket(), trailSL, pos.TakeProfit());
         }
      }
   }
}

//============================================================
//  BASKET MANAGEMENT
//  THIS IS WHERE THE NET PROFIT FIX LIVES:
//  The original bot let recovery chains run too long.
//  We now:
//  1. Track peak basket profit (high-water mark)
//  2. Use tighter retrace in weak sessions
//  3. Only trigger recovery if momentum agrees
//  4. Hard-stop recovery chains after MaxRecoveryTrades
//============================================================

void ManageBasket()
{
   if(CountPositions() == 0) return;

   double basketPL = GetBasketPL();

   // Update peak
   if(basketPL > g_basketPeakProfit)
      g_basketPeakProfit = basketPL;

   // Hard target
   if(HardTarget > 0.0 && basketPL >= HardTarget)
   {
      CloseAllPositions("Hard target");
      return;
   }

   // Basket retrace close
   // [NEW FIX] Use tighter retrace (WeakSessionRetrace) in non-peak hours
   // This prevents giving back profit in low-quality session windows
   double retraceFactor = IsStrongHour() ? BasketRetraceFactor : WeakSessionRetrace;
   if(g_basketPeakProfit > 0.0)
   {
      // retraceFactor = 0.7 means close when we give back 30% of peak profit
      // e.g. peak = 10, factor = 0.7 → close if current < 7
      if(basketPL < g_basketPeakProfit * retraceFactor && basketPL > 0.0)
      {
         CloseAllPositions("Basket retrace");
         return;
      }
   }

   // Recovery trigger
   // [NEW FIX] Only trigger recovery if:
   //   a) P&L below threshold
   //   b) We have room for more recovery trades
   //   c) Momentum agrees with our open direction (prevents averaging into losing trend)
   if(MaxRecoveryTrades > 0 && g_recoveryCount < MaxRecoveryTrades)
   {
      if(basketPL <= RecoveryThreshold)
         TryOpenRecovery();
   }
   else if(g_recoveryCount >= MaxRecoveryTrades && basketPL <= RecoveryThreshold * 1.5)
   {
      // [NEW FIX] Recovery chain exhausted and still losing badly → cut loss now
      // This was the source of the large -67.70 consecutive loss runs
      CloseAllPositions("Recovery chain exhausted - cut loss");
      g_failedRecoveries++;
      g_lastRecoveryFail = TimeCurrent();
   }
}

void TryOpenRecovery()
{
   if(MaxRecoveryTrades == 0) return;

   int domType = GetDominantPositionType();
   if(domType < 0) return;

   double ask = SymbolInfoDouble(_Symbol, SYMBOL_ASK);
   double bid = SymbolInfoDouble(_Symbol, SYMBOL_BID);

   // Distance check from last entry
   double distPts = 0;
   if(domType == POSITION_TYPE_BUY)
      distPts = (ask - g_lastEntryPrice) / g_point;
   else
      distPts = (g_lastEntryPrice - bid) / g_point;

   // Must have moved at least RecoveryDistance away from last entry
   if(MathAbs(distPts) < RecoveryDistance) return;

   // [NEW FIX] Momentum confirmation on recovery entries
   // The original bot entered recovery on distance alone, creating blind averaging.
   // We now require that the M1 momentum AGREES with our open direction.
   // This is the core profit/loss ratio improvement.
   if(RequireMomentumForRec)
   {
      int momentumSignal = GetEntrySignal();
      if(domType == POSITION_TYPE_BUY  && momentumSignal != 1)  return;
      if(domType == POSITION_TYPE_SELL && momentumSignal != -1) return;
   }

   double lastLot = GetLastPositionLot();
   double recovLot = NormalizeLot(lastLot * LotMultiplier);

   if(domType == POSITION_TYPE_BUY)
      OpenTrade(1, recovLot, true);
   else
      OpenTrade(-1, recovLot, true);

   g_recoveryCount++;
}

//============================================================
//  UTILITY FUNCTIONS
//============================================================

double GetBasketPL()
{
   double total = 0.0;
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      if(!pos.SelectByIndex(i)) continue;
      if(pos.Symbol() == _Symbol && pos.Magic() == MagicNumber)
         total += pos.Profit() + pos.Swap() + pos.Commission();
   }
   return total;
}

int CountPositions()
{
   int n = 0;
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      if(!pos.SelectByIndex(i)) continue;
      if(pos.Symbol() == _Symbol && pos.Magic() == MagicNumber) n++;
   }
   return n;
}

int GetDominantPositionType()
{
   int buys = 0, sells = 0;
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      if(!pos.SelectByIndex(i)) continue;
      if(pos.Symbol() != _Symbol || pos.Magic() != MagicNumber) continue;
      if(pos.PositionType() == POSITION_TYPE_BUY) buys++;
      else sells++;
   }
   if(buys >= sells && buys > 0) return POSITION_TYPE_BUY;
   if(sells > buys  && sells > 0) return POSITION_TYPE_SELL;
   return -1;
}

double GetLastPositionLot()
{
   double lastLot = BaseLot;
   datetime lastTime = 0;
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      if(!pos.SelectByIndex(i)) continue;
      if(pos.Symbol() == _Symbol && pos.Magic() == MagicNumber)
         if(pos.Time() > lastTime) { lastTime = pos.Time(); lastLot = pos.Volume(); }
   }
   return lastLot;
}

void CloseAllPositions(string reason)
{
   int count = CountPositions();
   if(count == 0) return;

   double pl = GetBasketPL();
   Print("[Basket Close] Reason:", reason, " | Positions:", count,
         " | P&L:", DoubleToString(pl, 2),
         " | RecoveryCount:", g_recoveryCount);

   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      if(!pos.SelectByIndex(i)) continue;
      if(pos.Symbol() == _Symbol && pos.Magic() == MagicNumber)
         trade.PositionClose(pos.Ticket(), 10);
   }

   // Reset basket state
   g_recoveryCount   = 0;
   g_basketPeakProfit = 0;
   g_lastEntryPrice  = 0;
}

//+------------------------------------------------------------------+
void OnDeinit(const int reason)
{
   string r = "";
   switch(reason)
   {
      case REASON_REMOVE:    r = "Removed"; break;
      case REASON_RECOMPILE: r = "Recompile"; break;
      case REASON_CHARTCLOSE:r = "Chart close"; break;
      default: r = IntegerToString(reason);
   }
   Print("ScalpRecovery EA deinitialized | Reason:", r);
}
//+------------------------------------------------------------------+
