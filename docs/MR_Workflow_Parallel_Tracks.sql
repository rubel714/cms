-- Parallel MR tracks after B: Allocate Invoice (F) runs alongside C→D→E.
-- G (Collection Verification) opens only when both F and E are approved.
-- Run once in phpMyAdmin / mysql client after docs/MR_Workflow_Migration.sql.

-- ---------------------------------------------------------------------------
-- 1. Allocation track on t_payment
--    0 = not open, 6 = F is open, 7 = F approved
-- ---------------------------------------------------------------------------
ALTER TABLE t_payment
	ADD COLUMN IF NOT EXISTS AllocationStepId INT NOT NULL DEFAULT 0;

ALTER TABLE t_payment ADD INDEX IF NOT EXISTS IX_payment_AllocationStepId (AllocationStepId);

-- ---------------------------------------------------------------------------
-- 2. Backfill in-progress MRs
-- ---------------------------------------------------------------------------
UPDATE t_payment SET AllocationStepId = 0 WHERE StepId IN (1, 2);
UPDATE t_payment SET AllocationStepId = 6 WHERE StepId IN (3, 4, 5, 6);
UPDATE t_payment SET AllocationStepId = 7 WHERE StepId IN (7, 8);
