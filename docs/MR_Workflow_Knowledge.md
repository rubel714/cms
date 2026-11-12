# MR (Money Receipt) Workflow: Quick Knowledge

Short summary to read first. The full reference is `docs/MR_Workflow_Current.md`.
`docs/MR_Workflow_Plan.md` is the original design and is out of date: it describes a sequential A-to-G flow where Completed is 8.

## Key facts
- Table `t_payment`:
  - `StepId` tracks the collection track.
  - `AllocationStepId` tracks Allocate Invoice: 0 not open, 6 open, 7 approved.
- StepId values:
  - 1 Payment Receive
  - 2 MR Verification
  - 3 Collector Assignment
  - 4 MR Collection Status
  - 5 MR Collection Status Check
  - 6 Collection Status Check is done and the MR is waiting for Allocate Invoice
  - 7 Collection Verification (posting)
  - 8 Transaction Audit
  - 9 Completed
  - 99 Rejected
- After MR Verification, two tracks run in parallel:
  - Collector Assignment, then MR Collection Status, then MR Collection Status Check.
  - Allocate Invoice.

  Collection Verification opens only when both are done (`mrJoinGIfReadyQuery`).
- Shared backend helpers: `backend/source/api/api_pages/mrworkflow_common.php` (step constants, list, approve, reject, log).
- Shared frontend: `src/components/MRWorkflow/` (`MRStepScreen`, `MRStepPage`, `ApproveRejectModal`, `ApprovalHistory`, `MRHeaderView`, `MRAllocationView`).
- Every Submit, Approve, and Reject is logged in `t_payment_approval_log`.
- A normal reject deletes the MR's `t_paymentitems` rows and sets `StepId = 99`.

## Collection Verification (step 7): posting
One transaction:
1. Insert `t_paymentextend` rows (`RptNumber = MRNo`) for each amount greater than 0: AIT (type 3), VAT (4), Advance (5), Due (6).
2. Set `t_invoiceitems.IsPaid = 1` for the allocated invoice items.
3. Set `t_paymentextend.IsFinished = 1` for the extend rows from earlier MRs that this MR used.
4. Set `TotalBaseAmount` and `TotalTransactionAmount` from the invoice items only.
5. Set `StatusId = 5`, `PostUserId` to the current user, and `StepId = 8`.
6. Write the log row.

## Transaction Audit (step 8)
- API `transactionaudit.php`, screen `src/views/screens/transactionaudit/`, menu key `transactionaudit`.
- Approve sets `StepId = 9` (Completed).
- Reject requires a comment and rolls back Collection Verification in one transaction:
  1. Delete this MR's own `t_paymentextend` rows.
  2. Set `IsPaid = 0` on its invoices.
  3. Set `IsFinished = 0` on the extend rows it used.
  4. Set `StatusId = 1`, `PostUserId = NULL`, and `StepId = 7`, which sends the MR back to Collection Verification.

  The `t_paymentitems` allocation is kept. The reject is refused if this MR's extend rows are already allocated to another MR.
- Pick-list rule: `t_paymentextend` rows appear in the Allocate Invoice pick list (`getUnpaidInvoices`) only when their MR is Completed (`n.StepId = MR_STEP_COMPLETED`, which is 9). They never appear before Transaction Audit approves.

## Migrations (run manually in `cms_db1`, in this order)
1. `docs/MR_Workflow_Migration.sql`
2. `docs/MR_Workflow_Parallel_Tracks.sql`
3. `docs/MR_Workflow_Transaction_Audit.sql`: moves already-completed MRs from 8 to 9, renames step 8, adds step 9, and adds the menu and role permissions. Run it before deploying the Transaction Audit code.

## Environment
- Windows with XAMPP. PHP 7.4 is at `D:\xampp_74\php\php.exe`. The React frontend is in `src/`, and the database is `cms_db1`.
- Database writes go through `exec_query` with `insertq`, `updateq`, and `deleteq` objects. Each batch runs as one transaction.
