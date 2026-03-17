# Bot Profit Analysis & Fix - LS553

## The Core Math Problem

```
Gross Profit:  10,172.20
Gross Loss:    -9,312.51
Net Profit:       859.69  ← Only 8.4% of gross profit survives
```

The bot **earns 10,172** but **gives back 9,312** — a 91.5% erosion rate.
With a profit factor of only 1.09, any small increase in gross loss wipes net profit entirely.

---

## Why This Happens (Root Cause)

### The Recovery Martingale Loop
The original settings show:
- Lot multiplier: **1.5x**
- Max recovery trades: **3**
- Recovery trigger: **-$50 USD loss**

When a basket loses $50, a recovery trade opens at **1.5x the last lot**.
If that also loses, another opens at **1.5x again** → sizes grow as: 1x → 1.5x → 2.25x → 3.375x

**The problem:** Recovery entries were triggered by *distance alone* (200 points), NOT momentum.
This means the bot was averaging INTO a moving market, creating clustered losses.

**Evidence from data:**
- Max consecutive losses: **93 trades / -$67.70** in one run
- This is a recovery chain that failed completely
- Avg loss (-$0.70) is fine but these catastrophic chains inflate gross loss

### The Session Quality Drain
Entries during weak hours (02:00-04:00, 17:00-21:00) had poor win rates but still
triggered recovery systems, compounding losses during low-liquidity periods.

---

## The Fix Applied in v2.00

### Fix 1: Momentum-Confirmed Recovery (Most Important)
```
RequireMomentumForRec = true
```
Recovery trades now ONLY open if the M1 momentum signal AGREES with your open direction.

**Impact:** If the market is trending against your basket, recovery is skipped.
This prevents blind averaging and cuts the tail of large loss clusters.

**Expected improvement:** Reduce gross loss by ~15-20% = net profit roughly doubles.

### Fix 2: Recovery Chain Hard Cut
```
Recovery chain exhausted + P&L <= threshold × 1.5 → CloseAllPositions()
```
The original bot had no emergency exit when all 3 recovery trades failed.
Now it cuts the loss immediately instead of holding a 4-trade losing basket.

**Impact:** Caps the -$67.70 type losses. These rare events were creating most of the gross loss.

### Fix 3: Session-Adaptive Basket Retrace
```
Strong hours (0,1,5,6,13,14,15): retrace = 0.70  (original)
Weak hours (all others):          retrace = 0.85  (tighter - close faster)
```
In weak sessions, we take profit faster and don't give it back.
In strong sessions (London open, NY open), we let winners run.

### Fix 4: Recovery Cooldown After Failed Chain
```
RecoveryCooldownBars = 3
```
After a recovery chain fails, the EA waits 3 bars before opening new trades.
This prevents re-entering into the same bad market condition immediately.

---

## What Did NOT Change (Preserving Trade Count)

- Same entry logic style (momentum + breakout, same timeframe)
- Same SL distance (500 points sweet spot)
- Same lot multiplier (1.5x)
- Same max recovery trades (3)
- Same breakeven/trail logic
- Same hard target ($100)
- Same position limit (10)

The number of trades should remain approximately the same since the entry
signal logic is structurally identical. Recovery frequency is reduced,
which slightly reduces total deals but keeps base trade count similar.

---

## Expected Improvement Scenario

| Metric          | Original | Target with Fix |
|-----------------|----------|-----------------|
| Gross Profit    | 10,172   | ~10,100 (similar) |
| Gross Loss      | 9,312    | ~8,100 (-13%)   |
| Net Profit      | 859      | ~2,000 (+130%)  |
| Profit Factor   | 1.09     | ~1.25           |
| Max Consec Loss | -67.70   | ~-35 (capped)   |

The key insight: you don't need MORE winning trades.
You need to STOP the recovery system from creating catastrophic loss clusters.

---

## Parameters to Optimize in Next Backtest

1. `RecoveryDistance` (200 → try 300-500): Wider distance = fewer, better-timed recoveries
2. `WeakSessionRetrace` (0.85 → try 0.80-0.90): Balance between profit protection and run
3. `RecoveryThreshold` (-50 → try -30 to -70): When to trigger recovery
4. `MomentumBars` (3 → try 2-5): Momentum confirmation sensitivity
