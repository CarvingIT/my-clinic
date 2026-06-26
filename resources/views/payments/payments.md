Payments module handoff

Current state

- We added a standalone `payments` domain alongside the existing follow-up flow.
- The old follow-up payment fields still exist and still work.
- Standalone payments are now stored in a new `payments` table.

Database

- New migration: `database/migrations/2026_06_23_000001_create_payments_table.php`.
- Columns used:
    - `patient_id` required
    - `follow_up_id` nullable
    - `received_by` nullable
    - `amount` required
    - `payment_method` required
    - `paid_at` required
    - `status` default `posted`
    - `reference_no` nullable
    - `notes` nullable
    - `branch_id` nullable
    - `branch_name` nullable
    - `source` default `manual`
- Foreign keys exist for `patient_id`, `follow_up_id`, `received_by`.

Modeling

- New model: `App\Models\Payment`.
- Relations:
    - `Payment belongsTo Patient`
    - `Payment belongsTo FollowUp`
    - `Payment belongsTo User` as `receiver`
    - `Patient hasMany Payment`
    - `FollowUp hasMany Payment`

Routes and UI

- New resource routes: `payments.*` except `show`.
- Added AJAX routes:
    - `payments.patients.search`
    - `payments.followups`
- New views:
    - `resources/views/payments/create.blade.php`
    - `resources/views/payments/edit.blade.php`
    - `resources/views/payments/index.blade.php`
- `create.blade.php` uses searchable patient lookup instead of a huge dropdown.
- Follow-up options load only after patient selection.

Controllers

- New controller: `app/Http/Controllers/PaymentController.php`.
- Main behaviors:
    - list payments
    - create manual payment-only entries
    - optionally link a payment to a follow-up
    - edit payment
    - void payment by setting `status = void`
    - AJAX patient search
    - AJAX follow-up loading for selected patient

Follow-up payments

- `FollowUpController` creates/updates a linked payment row in the `payments` table within a database transaction when follow-ups are saved or updated.
- Legacy `syncFollowUpAutoPayment()` helper has been entirely removed.
- Payments created during follow-up entry now use `source = manual`.
- Historical imported follow-up payments use `source = followup_legacy` or `followup_auto`.

Backfill command

- Command: `php artisan MC:BackfillFollowUpPayments`.
- Dry run: `php artisan MC:BackfillFollowUpPayments --dry-run`.
- Behavior:
    - copies legacy `follow_ups.amount_paid` into `payments`
    - skips rows that already have a backfilled payment
    - nulls `received_by` if the old user id no longer exists
    - safe for reruns

Balance logic

- Patient outstanding calculations are simplified to: `$outstandingBalance = $totalBilled - $totalPaid`
- `$totalBilled` is the sum of `amount_billed` from follow-ups.
- `$totalPaid` is the sum of `amount` from the `payments` table where `status` is `posted`.

Patient page timeline

- `resources/views/patients/show.blade.php` now merges follow-ups and payments into one timeline table.
- Standalone payments appear inside the same list as follow-ups.
- Payment rows are labeled `Payment` and show amount, method, date, and edit link.

Operational notes

- Payments table migration was already applied successfully.
- Backfill command was already run successfully after fixing historical missing-user rows.
- Full `php artisan migrate` may still hit unrelated older migration conflicts in this repo, so use targeted migration if needed.

Important rules for future changes

- The `amount_paid` database column in `follow_ups` table is kept for database schema stability but is no longer actively written to or queried.
- All reads of follow-up paid amounts are routed through the `amount_paid` model accessor which dynamically queries the `payments` table.
- Keep the patient timeline table as the single combined display for both follow-ups and payment-only visits.

## Family Group Payments & Patient Groups

### Database and Modeling
- New model: `App\Models\PatientGroup`.
- New migration adding `patient_groups` table and a `patient_group_id` column to the `patients` table.
- Relationships:
    - `Patient belongsTo PatientGroup`
    - `PatientGroup hasMany Patient` (as `members`)

### Group Management
- Registered `groups` resource routes mapping to `PatientGroupController`.
- All group management actions (index, store, update, delete) are redirected/integrated directly under the **Patient Groups (Families)** tab of the Payments dashboard at `/payments?tab=groups`.
- Members are added to/removed from groups dynamically via an AJAX patient search in the group create/edit views.

### Group Payments Workflow
- When adding a payment, selecting a patient who belongs to a group automatically loads the **Family/Group Payment** section.
- This section lists all members of the family group, calculates their respective outstanding dues, and provides inputs to allocate the payment amount across any subset of the group.
- On form submission, `PaymentController@store` processes the transaction by automatically splitting the total amount and inserting individual ledger records in the `payments` table for each selected family member. This ensures backward compatibility with the legacy ledger reporting and the combined patient timelines.
- Added a **Record Payment** action link next to each group on the family list table. This button routes directly to `/payments/create?patient_id={member_id}`, which pre-populates the primary payer and triggers auto-rendering of the family group payments section on load.

### UI Styling and Icons
- Consolidated payments action buttons to use unified FontAwesome icons (`<i class="fas fa-edit"></i>`, `<i class="fas fa-trash"></i>`) with appropriate tooltips for actions (`Edit`, `Delete`, `Void`), matching the rest of the application.
