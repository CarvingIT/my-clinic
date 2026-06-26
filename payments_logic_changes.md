# Payments Ledger Migration: Logic & Workflow Changes

This document summarizes the changes in the application's business logic and workflow behavior during the transition from the legacy billing column to the decoupled payments ledger.

---

## 1. Database Schema Logic
*   **Old Logic:** Financial transaction amounts were stored as a flat value (`amount_paid`) directly on the patient's individual visit (follow-up) records.
*   **New Logic:** The `amount_paid` column is dropped from the database. Financial history is decoupled into a dedicated `payments` ledger table, where each payment is recorded as its own transaction line item.

---

## 2. Follow-Up Payment Allocation Logic
*   **Old Logic:** A chronological First-In, First-Out (FIFO) algorithm automatically allocated any incoming payments to cover historical unpaid follow-ups. If a patient made a standalone payment, it was automatically consumed to pay off older follow-up balances.
*   **New Logic:** Payments are only applied to a follow-up if they are explicitly linked to it. Standalone payments remain completely independent and do not offset other follow-up balances, keeping the visit ledger and payment ledger decoupled.

---

## 3. Patient Group Payment Splitting Logic
*   **Old Logic:** Group payments only updated the primary patient's balance.
*   **New Logic:** When recording a group payment, the system splits the payment among selected family members. If "Payment without follow-up" is selected, the split payment records are saved as standalone (unlinked), and all automatic and direct linkages to follow-ups are completely removed for all members.

---

## 4. Patient Dashboard & Timeline Rendering Logic
*   **Old Logic:** Standalone payments could disappear from the timeline due to rigid null/empty type checks on the database column.
*   **New Logic:** Timeline aggregation handles unlinked payments using robust check logic. Any payment without a linked follow-up ID (regardless of format) is chronologically rendered as a distinct "Standalone Payment" event.

---

## 5. Dashboard Metrics & Analytics Logic
*   **Old Logic:** Today's and total revenue were aggregated by summing the legacy `amount_paid` column directly on the follow-ups query builder.
*   **New Logic:** Revenue metrics are aggregated directly by summing the payment amounts in the new `payments` table.

---

## 6. Backup Import/Export and Branch Sync Logic
*   **Old Logic:** Exports outputted a flat array of patient models. Imports directly updated follow-up records.
*   **New Logic:**
    *   **Export:** Resolves dynamic paid amounts from the model's accessor and explicitly appends them to the exported JSON.
    *   **Import:** Parses any incoming paid amounts and automatically inserts/updates corresponding rows in the `payments` table, ensuring the database ledger matches the synchronized branch data.
