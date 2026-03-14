//+------------------------------------------------------------------+
//|          Master_Multi_Asset_Sniper_RECOVERY_v3b.mq5              |
//|       High-frequency entries + intelligent recovery               |
//|                                                                  |
//|  FIXES vs v2 (trade count preserved):                           |
//|  [FIX 1] peakProfit resets only when profit goes negative,       |
//|           not on every dip below activeFloor                     |
//|  [FIX 2] Recovery only fires on a bounce ABOVE/BELOW the        |
//|           extreme level, not while price still breaking          |
//|  [FIX 3] activeFloor for GOLD raised 10 → 25                    |
//|                                                                  |
//|  NOTE: Entry frequency (OnTick firing) is unchanged.            |
//|        No bar guard — high-frequency stacking preserved.         |
//+------------------------------------------------------------------+
#property strict
#include <Trade\Trade.mqh>
CTrade trade;
int    maHandle;
double peakProfit        = 0;
int    activeGap;
double activeFloor;
int    recoveryCount     = 0;
double lastRecoveryPrice = 0;
input int      MaxTradesPerSymbol   = 10;
input int      MaxRecoveryTrades    = 3;
input double   RecoveryMultiplier   = 1.5;
input double   DDTrigger            = -50.0;
input int      RecoveryGapPoints    = 200;
input int      RecoveryBouncePoints = 30;     // [FIX 2] bounce buffer in points
input bool     UseStopLoss          = true;
input int      StopLossPoints       = 500;
input double   BasketTrailPercent   = 0.70;
input double   BasketProfitTarget   = 100.0;
//+------------------------------------------------------------------+
int OnInit()
{
   maHandle = iMA(_Symbol, _Period, 20, 0, MODE_EMA, PRICE_CLOSE);
   if(maHandle == INVALID_HANDLE) { Print("Failed EMA handle"); return INIT_FAILED; }
   string sym = _Symbol;
   if(StringFind(sym, "BTC") >= 0)
   {
      activeGap = 2000; activeFloor = 50.0;
      Print("BTC Profile Loaded");
   }
   else if(StringFind(sym, "XAU") >= 0 || StringFind(sym, "GOLD") >= 0)
   {
      activeGap = 150;
      activeFloor = 25.0;   // [FIX 3] was 10.0 — too low, trail reset on noise
      Print("GOLD Profile Loaded");
   }
   else { activeGap = 300; activeFloor = 15.0; }
   return INIT_SUCCEEDED;
}
//+------------------------------------------------------------------+
void OnDeinit(const int reason)
{
   if(maHandle != INVALID_HANDLE) IndicatorRelease(maHandle);
}
//+------------------------------------------------------------------+
bool IsGoldTrading()
{
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      ulong ticket = PositionGetTicket(i);
      if(PositionSelectByTicket(ticket))
      {
         string sym = PositionGetString(POSITION_SYMBOL);
         if(StringFind(sym, "XAU") >= 0 || StringFind(sym, "GOLD") >= 0) return true;
      }
   }
   return false;
}
//+------------------------------------------------------------------+
double GetRecentLow()
{
   double lowArray[];
   ArraySetAsSeries(lowArray, true);
   if(CopyLow(_Symbol, _Period, 1, 15, lowArray) < 15) return 0;
   return lowArray[ArrayMinimum(lowArray)];
}
//+------------------------------------------------------------------+
double GetRecentHigh()
{
   double highArray[];
   ArraySetAsSeries(highArray, true);
   if(CopyHigh(_Symbol, _Period, 1, 15, highArray) < 15) return 0;
   return highArray[ArrayMaximum(highArray)];
}
//+------------------------------------------------------------------+
void CloseSymbolPositions()
{
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      ulong ticket = PositionGetTicket(i);
      if(PositionSelectByTicket(ticket) && PositionGetString(POSITION_SYMBOL) == _Symbol)
         trade.PositionClose(ticket);
   }
   recoveryCount = 0;
   lastRecoveryPrice = 0;
}
//+------------------------------------------------------------------+
bool CanEntry(ENUM_ORDER_TYPE type)
{
   double currentPrice = (type == ORDER_TYPE_BUY) ? SymbolInfoDouble(_Symbol, SYMBOL_ASK)
                                                  : SymbolInfoDouble(_Symbol, SYMBOL_BID);
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      ulong ticket = PositionGetTicket(i);
      if(PositionSelectByTicket(ticket) && PositionGetString(POSITION_SYMBOL) == _Symbol)
      {
         if((ENUM_POSITION_TYPE)PositionGetInteger(POSITION_TYPE) == (ENUM_POSITION_TYPE)type)
            continue;
         double openPrice = PositionGetDouble(POSITION_PRICE_OPEN);
         if(MathAbs(currentPrice - openPrice) < activeGap * _Point) return false;
      }
   }
   return true;
}
//+------------------------------------------------------------------+
void OpenTrade(ENUM_ORDER_TYPE type, double lot, bool isRecovery = false)
{
   if(isRecovery && lastRecoveryPrice != 0)
   {
      double currentPrice = (type == ORDER_TYPE_BUY) ? SymbolInfoDouble(_Symbol, SYMBOL_ASK)
                                                     : SymbolInfoDouble(_Symbol, SYMBOL_BID);
      if(MathAbs(currentPrice - lastRecoveryPrice) < RecoveryGapPoints * _Point)
      {
         Print("Recovery skipped – too close to last recovery");
         return;
      }
   }
   double bid = SymbolInfoDouble(_Symbol, SYMBOL_BID);
   double ask = SymbolInfoDouble(_Symbol, SYMBOL_ASK);
   double sl  = 0;
   if(UseStopLoss)
   {
      sl = (type == ORDER_TYPE_BUY) ? bid - StopLossPoints * _Point
                                    : ask + StopLossPoints * _Point;
   }
   if(type == ORDER_TYPE_BUY)
      trade.Buy(lot, _Symbol, 0, sl, 0, isRecovery ? "Recovery" : "");
   else
      trade.Sell(lot, _Symbol, 0, sl, 0, isRecovery ? "Recovery" : "");
   if(trade.ResultRetcode() == TRADE_RETCODE_DONE && isRecovery)
   {
      recoveryCount++;
      lastRecoveryPrice = (type == ORDER_TYPE_BUY) ? ask : bid;
   }
}
//+------------------------------------------------------------------+
void ManageStack(ENUM_ORDER_TYPE type)
{
   int count = 0;
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      ulong ticket = PositionGetTicket(i);
      if(PositionSelectByTicket(ticket) && PositionGetString(POSITION_SYMBOL) == _Symbol)
         if((ENUM_POSITION_TYPE)PositionGetInteger(POSITION_TYPE) == (ENUM_POSITION_TYPE)type)
            count++;
   }
   if(count >= MaxTradesPerSymbol) return;
   double lot = (count >= 8) ? 0.02 : 0.01;
   if(CanEntry(type)) OpenTrade(type, lot, false);
}
//+------------------------------------------------------------------+
//| Recovery — only on bounce, not on continued breakout            |
//| [FIX 2] bid must be ABOVE low (not at or below) for buy         |
//|         recovery, within RecoveryBouncePoints of the low         |
//+------------------------------------------------------------------+
void CheckRecovery()
{
   if(MaxRecoveryTrades > 0 && recoveryCount >= MaxRecoveryTrades) return;
   double symbolProfit = 0;
   int    buyCount = 0, sellCount = 0;
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      ulong ticket = PositionGetTicket(i);
      if(PositionSelectByTicket(ticket) && PositionGetString(POSITION_SYMBOL) == _Symbol)
      {
         symbolProfit += PositionGetDouble(POSITION_PROFIT);
         if(PositionGetInteger(POSITION_TYPE) == POSITION_TYPE_BUY) buyCount++;
         else sellCount++;
      }
   }
   if(symbolProfit > DDTrigger || (buyCount + sellCount) == 0) return;
   bool majorityBuy  = (buyCount > sellCount);
   bool majoritySell = (sellCount > buyCount);
   double bid    = SymbolInfoDouble(_Symbol, SYMBOL_BID);
   double low    = GetRecentLow();
   double high   = GetRecentHigh();
   double buffer = RecoveryBouncePoints * _Point;
   // [FIX 2] BUY recovery: price touched the low and is now bouncing up
   if(majorityBuy && bid > low && bid <= low + buffer)
   {
      Print("Recovery BUY triggered (bounce confirmation)");
      OpenTrade(ORDER_TYPE_BUY, 0.01 * RecoveryMultiplier, true);
   }
   // [FIX 2] SELL recovery: price touched the high and is now dropping
   else if(majoritySell && bid < high && bid >= high - buffer)
   {
      double maArray[];
      ArraySetAsSeries(maArray, true);
      if(CopyBuffer(maHandle, 0, 0, 1, maArray) > 0)
      {
         if(bid < maArray[0] + 5000 * _Point)
         {
            Print("Recovery SELL triggered (bounce confirmation)");
            OpenTrade(ORDER_TYPE_SELL, 0.01 * RecoveryMultiplier, true);
         }
      }
   }
}
//+------------------------------------------------------------------+
void OnTick()
{
   if(StringFind(_Symbol, "BTC") >= 0 && IsGoldTrading())
   {
      Comment("BTC Paused: Gold active.");
      return;
   }
   else Comment("");
   double symbolProfit = 0;
   for(int i = PositionsTotal() - 1; i >= 0; i--)
   {
      ulong ticket = PositionGetTicket(i);
      if(PositionSelectByTicket(ticket) && PositionGetString(POSITION_SYMBOL) == _Symbol)
         symbolProfit += PositionGetDouble(POSITION_PROFIT);
   }
   // Hard profit target
   if(symbolProfit >= BasketProfitTarget)
   {
      Print("Basket Profit Target Hit (", symbolProfit, ")");
      CloseSymbolPositions();
      peakProfit = 0;
      return;
   }
   // Basket trailing
   if(symbolProfit > activeFloor)
   {
      if(symbolProfit > peakProfit) peakProfit = symbolProfit;
      if(symbolProfit < peakProfit * BasketTrailPercent)
      {
         Print("Trailing stop triggered (", symbolProfit, ")");
         CloseSymbolPositions();
         peakProfit = 0;
         return;
      }
   }
   else
   {
      // [FIX 1] Only wipe peakProfit when profit is actually negative.
      // Between 0 and activeFloor: keep the peak so trail re-engages on recovery.
      if(symbolProfit <= 0)
         peakProfit = 0;
   }
   // EMA
   double maArray[];
   ArraySetAsSeries(maArray, true);
   if(CopyBuffer(maHandle, 0, 0, 1, maArray) <= 0) return;
   double maValue = maArray[0];
   double bid     = SymbolInfoDouble(_Symbol, SYMBOL_BID);
   double lowest  = GetRecentLow();
   double highest = GetRecentHigh();
   // Normal entries — full tick frequency preserved (no bar guard)
   if(bid >= highest && bid < maValue + (5000 * _Point))
      ManageStack(ORDER_TYPE_SELL);
   if(bid <= lowest)
      ManageStack(ORDER_TYPE_BUY);
   // Recovery
   CheckRecovery();
}
//+------------------------------------------------------------------+
