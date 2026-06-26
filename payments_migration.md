# Comprehensive Payments Architecture Migration Guide

This document details the complete architectural migration from the legacy billing system (storing payment data directly on the `follow_ups` table) to a fully decoupled payments ledger (`payments` table).

---

## 1. Database Migration
A database migration was created to drop the legacy column while preserving rollback capability:
*   **File:** `database/migrations/xxxx_xx_xx_xxxxxx_drop_amount_paid_from_follow_ups_table.php`
*   **Implementation:**
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }

    public function down(): void {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0.00)->after('amount_billed');
        });
    }
};
```

---

## 2. Follow-Up Controller Refactoring
**File:** `app/Http/Controllers/FollowUpController.php`

### A. Removal of Auto-Sync Helper
The helper method `syncFollowUpAutoPayment()` was deleted entirely to stop syncing transaction amounts into the `follow_ups` table.

### B. Transaction-Safe Creation (`store` method)
Follow-up creation and initial payment registration are wrapped inside a database transaction to ensure atomicity.
```php
public function store(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:users,id',
        'amount_billed' => 'required|numeric|min:0',
        'amount_paid' => 'nullable|numeric|min:0',
        'payment_method' => 'nullable|in:cash,online',
        ...
    ]);

    DB::transaction(function () use ($request) {
        $followUp = FollowUp::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'amount_billed' => $request->amount_billed,
            'check_up_info' => json_encode($request->check_up_info),
            // 'amount_paid' is omitted here
        ]);

        if ($request->filled('amount_paid') && $request->amount_paid > 0) {
            Payment::create([
                'patient_id' => $followUp->patient_id,
                'follow_up_id' => $followUp->id,
                'amount' => $request->amount_paid,
                'payment_method' => $request->payment_method ?? 'cash',
                'status' => 'posted',
                'paid_at' => now(),
            ]);
        }
    });

    return redirect()->route('followups.index')->with('success', 'Follow-up created successfully.');
}
```

### C. Ledger List Refactoring (`index` method)
We updated how the lists for the ledger summary cards and modals are constructed:
*   **Total Payments Received Modal (`$paidFollowUpsList`):** Changed to query the `payments` table directly to capture standalone, unallocated, and linked payments chronologically.
*   **Outstanding Dues Modal (`$dueFollowUpsList`):** Filters out follow-ups that have been fully paid off globally by the patient (`real_due <= 0`).

---

## 3. Explicit Payment Allocation in Follow-Up Model
**File:** `app/Models/FollowUp.php`

To keep views consistent without rewriting multiple SQL queries, the `amount_paid` attribute dynamically computes payment allocation. Static request caching guarantees performance during loops or tables. The greedy FIFO distribution was removed so that payments are only allocated if they are **explicitly linked** to the follow-up:

```php
protected static $allocatedPaidCache = [];

public function getAmountPaidAttribute()
{
    if (isset(self::$allocatedPaidCache[$this->id])) {
        return self::$allocatedPaidCache[$this->id];
    }

    $patientId = $this->patient_id;
    
    $chronoFollowUps = FollowUp::where('patient_id', $patientId)->orderBy('created_at', 'asc')->get();
    $allPayments = Payment::where('patient_id', $patientId)->where('status', 'posted')->orderBy('paid_at', 'asc')->orderBy('id', 'asc')->get();
    
    $paymentPool = $allPayments->map(function($p) {
        return [
            'model' => $p,
            'remaining' => (float)$p->amount,
        ];
    })->toArray();

    foreach ($chronoFollowUps as $fu) {
        $billed = (float)($fu->amount_billed ?? 0);
        $allocatedForFu = 0.0;
        
        // Allocate payments that are EXPLICITLY linked to this follow-up
        foreach ($paymentPool as &$pItem) {
            if ($pItem['remaining'] > 0 && $pItem['model']->follow_up_id == $fu->id) {
                $alloc = min($pItem['remaining'], $billed - $allocatedForFu);
                if ($alloc > 0) {
                    $pItem['remaining'] -= $alloc;
                    $allocatedForFu += $alloc;
                }
            }
        }
        unset($pItem);
        
        self::$allocatedPaidCache[$fu->id] = $allocatedForFu;
    }

    return self::$allocatedPaidCache[$this->id] ?? 0.0;
}
```

---

## 4. Reconciled Analytics Dashboard
**File:** `app/Http/Controllers/AnalyticsController.php`

Chart 5 (Payment Status Overview) aggregates daily billings and daily payments from their respective tables, sorting and merging them chronologically to handle decoupled transaction events:

```php
// Chart 5: Payment Status (Reconciled from follow-ups billing and payments ledger)
$dailyBilled = FollowUp::selectRaw('DATE(created_at) as raw_date, DATE_FORMAT(created_at, "%d-%m-%y") as date, SUM(amount_billed) as billed')
    ...
    ->groupBy('raw_date', 'date')
    ->orderBy('raw_date', 'asc')
    ->get()
    ->keyBy('raw_date');

$dailyPaid = \App\Models\Payment::where('status', 'posted')
    ->selectRaw('DATE(paid_at) as raw_date, DATE_FORMAT(paid_at, "%d-%m-%y") as date, SUM(amount) as paid')
    ...
    ->groupBy('raw_date', 'date')
    ->orderBy('raw_date', 'asc')
    ->get()
    ->keyBy('raw_date');

$allDates = $dailyBilled->keys()->concat($dailyPaid->keys())->unique()->sort();

$paymentStatus = $allDates->map(function($raw_date) use ($dailyBilled, $dailyPaid) {
    $billedItem = $dailyBilled->get($raw_date);
    $paidItem = $dailyPaid->get($raw_date);
    
    $billed = (float)($billedItem ? $billedItem->billed : 0);
    $paid = (float)($paidItem ? $paidItem->paid : 0);
    
    return (object)[
        'raw_date' => $raw_date,
        'date' => $billedItem ? $billedItem->date : ($paidItem ? $paidItem->date : Carbon::parse($raw_date)->format('d-m-y')),
        'billed' => $billed,
        'paid' => $paid,
        'due' => $billed - $paid,
    ];
})->values();
```

---

## 5. Dashboard Revenue & API Route Query Fixes
**Files:** `resources/views/dashboard.blade.php`, `routes/api.php`

Direct aggregations summing `amount_paid` on the `follow_ups` table would fail with a SQL exception. They have been updated to target the `payments` table directly:
```php
// Old Code:
$revenue_today = \App\Models\FollowUp::whereDate('created_at', today())->sum('amount_paid');
$total_revenue = \App\Models\FollowUp::sum('amount_paid');

// New Code:
$revenue_today = \App\Models\Payment::where('status', 'posted')->whereDate('paid_at', today())->sum('amount');
$total_revenue = \App\Models\Payment::where('status', 'posted')->sum('amount');
```

---

## 6. Backup Import/Export and Branch Synchronization Upgrades
**Files:** `app/Http/Controllers/ImportExportController.php`, `app/Services/SyncService.php`

To prevent data loss and unsynced ledger balances during data backup/restoration or branch syncs:
*   **Exports:** The serialization map explicitly calls the model accessor to compute and inject `amount_paid` into the exported JSON payloads.
*   **Imports:** The import loop pulls `amount_paid` from the incoming follow-up array. If the paid amount is greater than `0`, a matching transaction is created/updated directly on the `payments` table.

---

## 7. Patient Profile Timeline Updates
**File:** `resources/views/patients/show.blade.php`

To prevent standalone payments from disappearing from the patient's visual timeline, we replaced duplicate FIFO views and updated the timeline aggregation checks to use `empty($p->follow_up_id)` (treating `null`, empty string, and `0` as independent, standalone payment entries).

---

## 8. Test Suite Patches
*   **Group Payment Tests (`tests/Feature/GroupPaymentTest.php`):** Updated test data setups to seed transaction entries via the `Payment` model instead of setting legacy `amount_paid` attributes.
*   **Authentication Tests (`tests/Feature/Auth/AuthenticationTest.php`):** Seeded and injected valid `branch_id` params to satisfy the updated `LoginRequest` validation rules.
*   **Profile Tests (`tests/Feature/ProfileTest.php`):** Seeded role entities to pass user verification middleware, and updated account delete validations to expect soft-deleted (`trashed`) models.
