<?php

/**
 * Shared helpers for the MR (t_payment) multi-step workflow.
 * Collection track (StepId): 1 Payment Receive, 2 MR Verification, 3 Collector Assignment,
 * 4 MR Collection Status, 5 MR Collection Status Check, 6 = E done waiting for F,
 * 7 Collection Verification (posting), 8 Transaction Audit, 9 Completed, 99 Rejected.
 * Allocation track (AllocationStepId): 0 not open, 6 F is open, 7 F approved.
 * After B, C→D→E and F run in parallel. G opens only when both E and F are done.
 * Extend rows posted by G can be picked in F only after H approves (StepId 9).
 */

const MR_STEP_PAYMENT_RECEIVE = 1;
const MR_STEP_ALLOCATE_INVOICE = 6;
const MR_STEP_COLLECTION_VERIFICATION = 7;
const MR_STEP_TRANSACTION_AUDIT = 8;
const MR_STEP_COMPLETED = 9;
const MR_STEP_REJECTED = 99;

function mrNextStep($stepId)
{
	return $stepId >= MR_STEP_TRANSACTION_AUDIT ? MR_STEP_COMPLETED : $stepId + 1;
}

function mrPaymentListFromSql()
{
	return "SELECT a.PaymentId AS id, st.StepName AS CurrentStep,
			DATE_FORMAT(a.PaymentDate, '%Y-%m-%d') as PaymentDate,
			a.CustomerId, b.CustomerCode, b.CustomerName, a.CustomerGroupId, a.BankId, d.BankName,
			a.Remarks, a.StatusId, a.StepId, a.AllocationStepId, a.MRNo, a.RefNo, a.ChequeNumber, a.ChequeDate, a.BankBranchName,
			a.PaymentModeId, pm.PaymentModeName, a.CurrencyId, cur.CurrencyName,
			a.PaymentReceiveCurrencyAmount, a.ExchangeRate, a.PaymentReceiveAmount,
			a.CollectedMethodId, cm.CollectedMethod, a.CollectionAddress, a.CollectionPersonName, a.CollectionPersonPhone,
			a.TotalBaseAmount, a.TotalTransactionAmount,
			a.RebatePercent, a.RebateAmount, a.CNPercent, a.CNAmount, a.AitPercent, a.AitDeduction,
			a.VatPercent, a.VatAmount, a.DueAmount, a.AdvanceAmount,
			a.CollectorUserId, cu.UserName AS CollectorName, DATE_FORMAT(a.CollectorAssignDate, '%Y-%m-%d %H:%i') AS CollectorAssignDate,
			a.CollectionStatusId, cs.CollectionStatusName, DATE_FORMAT(a.CollectionDate, '%Y-%m-%d') AS CollectionDate,
			a.CollectedAmount, a.CollectionNote
		FROM t_payment a
		LEFT JOIN t_customer b ON a.CustomerId = b.CustomerId
		LEFT JOIN t_bank d ON a.BankId = d.BankId
		LEFT JOIN t_paymentmode pm ON a.PaymentModeId = pm.PaymentModeId
		LEFT JOIN t_currency cur ON a.CurrencyId = cur.CurrencyId
		LEFT JOIN t_collectedmethod cm ON a.CollectedMethodId = cm.CollectedMethodId
		LEFT JOIN t_users cu ON a.CollectorUserId = cu.UserId
		LEFT JOIN t_collection_status cs ON a.CollectionStatusId = cs.CollectionStatusId
		LEFT JOIN t_payment_step st ON a.StepId = st.StepId";
}

function mrGetStepDataList($stepId)
{
	try {
		$dbh = new Db();
		$stepId = (int)$stepId;

		// Current step, every later step (passed, read-only), and rejected MRs that reached this step.
		$query = mrPaymentListFromSql() . "
		WHERE a.StepId = $stepId
			OR (a.StepId > $stepId AND a.StepId <> 99)
			OR (a.StepId = 99 AND EXISTS (
				SELECT 1 FROM t_payment_approval_log l
				WHERE l.PaymentId = a.PaymentId AND l.StepId = $stepId
			))
		ORDER BY CASE WHEN a.StepId = $stepId THEN 0 ELSE 1 END, a.PaymentDate DESC, a.MRNo DESC;";

		$resultdatalist = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdatalist
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

/** List for Allocate Invoice: open after B, independent of the C→D→E collection track. */
function mrGetAllocationDataList()
{
	try {
		$dbh = new Db();
		$open = MR_STEP_ALLOCATE_INVOICE;
		$done = MR_STEP_COLLECTION_VERIFICATION;
		$completed = MR_STEP_COMPLETED;
		$rejected = MR_STEP_REJECTED;

		$query = mrPaymentListFromSql() . "
		WHERE (a.AllocationStepId = $open AND a.StepId NOT IN ($completed, $rejected))
			OR a.AllocationStepId = $done
			OR (a.StepId = $rejected AND (
				a.AllocationStepId IN ($open, $done)
				OR EXISTS (
					SELECT 1 FROM t_payment_approval_log l
					WHERE l.PaymentId = a.PaymentId AND l.StepId = $open
				)
			))
		ORDER BY CASE WHEN a.AllocationStepId = $open AND a.StepId NOT IN ($completed, $rejected) THEN 0 ELSE 1 END,
			a.PaymentDate DESC, a.MRNo DESC;";

		$resultdatalist = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdatalist
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

/** Invoice and extend rows allocated to an MR (used by Allocate Invoice and Collection Verification). */
function mrGetPaymentItems($PaymentId)
{
	$dbh = new Db();

	$query = "SELECT m.PaymentItemId as autoId, m.`PaymentItemId`, m.`PaymentId`, m.`InvoiceItemId`, m.PaymentExtendId,
		'' as AccountingPeriod, '' as AccountCode, '' as `Description`,
		DATE_FORMAT(p.PaymentDate, '%d/%m/%Y') AS TransactionDate,
		CONCAT(n.RptNumber,' - ',o.RptPreFix) AS TransactionReference,
		0 as TransactionAmount, 0 as ExchangeRate, (n.Amount*o.IsWillPay) AS BaseAmount
		FROM t_paymentitems m
		INNER JOIN t_paymentextend n ON m.PaymentExtendId = n.PaymentExtendId
		INNER JOIN t_payment_extend_type o ON n.PaymentExtendTypeId = o.PaymentExtendTypeId
		INNER JOIN t_payment p ON n.PaymentId = p.PaymentId
		WHERE m.PaymentId = :PaymentId1

		UNION ALL

		SELECT a.PaymentItemId as autoId, a.`PaymentItemId`, a.`PaymentId`, a.`InvoiceItemId`, a.PaymentExtendId,
		DATE_FORMAT(STR_TO_DATE(CONCAT(RIGHT(b.AccountingPeriod,4), '-',LPAD(LEFT(b.AccountingPeriod, LENGTH(b.AccountingPeriod)-4),2,'0'), '-01'),'%Y-%m-%d'),'%M-%Y') as AccountingPeriod,
		b.AccountCode, b.Description,
		DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate, b.TransactionReference,
		b.TransactionAmount, b.ExchangeRate, b.BaseAmount
		FROM t_paymentitems a
		INNER JOIN t_invoiceitems b ON a.InvoiceItemId = b.InvoiceItemId
		WHERE a.PaymentId = :PaymentId2

		ORDER BY PaymentItemId ASC;";

	return $dbh->query($query, ['PaymentId1' => $PaymentId, 'PaymentId2' => $PaymentId]);
}

function mrGetApprovalLog($data)
{
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : '';

	try {
		$dbh = new Db();

		$query = "SELECT a.LogId AS id, a.StepId, s.StepName, a.Action, a.Comments, u.UserName,
			DATE_FORMAT(a.CreateTs, '%Y-%m-%d %H:%i') AS CreateTs
		FROM t_payment_approval_log a
		LEFT JOIN t_payment_step s ON a.StepId = s.StepId
		LEFT JOIN t_users u ON a.UserId = u.UserId
		WHERE a.PaymentId = :PaymentId
		ORDER BY a.LogId ASC;";

		$resultdatalist = $dbh->query($query, ['PaymentId' => $PaymentId]);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdatalist
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

/** Returns true when the MR is still waiting at $stepId, otherwise an error response. */
function mrCheckStep($PaymentId, $stepId)
{
	$dbh = new Db();
	$rows = $dbh->query("SELECT StepId FROM t_payment WHERE PaymentId = :PaymentId;", ['PaymentId' => $PaymentId]);

	if (!is_array($rows) || count($rows) == 0) {
		return msg(0, 404, 'MR not found.');
	}

	if ((int)$rows[0]['StepId'] !== (int)$stepId) {
		return msg(0, 409, 'This MR has already been processed. Please refresh the list.');
	}

	return true;
}

/** Returns true when Allocate Invoice (F) is still open on this MR. */
function mrCheckAllocationOpen($PaymentId)
{
	$dbh = new Db();
	$rows = $dbh->query(
		"SELECT StepId, AllocationStepId FROM t_payment WHERE PaymentId = :PaymentId;",
		['PaymentId' => $PaymentId]
	);

	if (!is_array($rows) || count($rows) == 0) {
		return msg(0, 404, 'MR not found.');
	}

	$stepId = (int)$rows[0]['StepId'];
	$allocationStepId = (int)$rows[0]['AllocationStepId'];
	if ($allocationStepId !== MR_STEP_ALLOCATE_INVOICE || $stepId === MR_STEP_COMPLETED || $stepId === MR_STEP_REJECTED) {
		return msg(0, 409, 'This MR has already been processed. Please refresh the list.');
	}

	return true;
}

/**
 * Opens G only when collection is waiting at step 6 (E done) and F is already approved (AllocationStepId = 7).
 * Uses distinct bind names so SET StepId and WHERE StepId do not collide.
 */
function mrJoinGIfReadyQuery($PaymentId)
{
	$u = new updateq();
	$u->table = 't_payment';
	$u->columns = ['StepId'];
	$u->values = [MR_STEP_COLLECTION_VERIFICATION];
	$u->pks = ['PaymentId'];
	$u->pk_values = [$PaymentId];
	$u->select = "SELECT * FROM t_payment WHERE PaymentId = :PaymentId";
	$u->wh_params = ['PaymentId' => $PaymentId];
	$u->query = "UPDATE t_payment
		SET StepId = :NextStepId
		WHERE PaymentId = :PaymentId
			AND StepId = :WaitStepId
			AND AllocationStepId = :DoneAllocationStepId";
	$u->params = [
		'NextStepId' => MR_STEP_COLLECTION_VERIFICATION,
		'PaymentId' => $PaymentId,
		'WaitStepId' => MR_STEP_ALLOCATE_INVOICE,
		'DoneAllocationStepId' => MR_STEP_COLLECTION_VERIFICATION,
	];
	return $u;
}

function mrLogQuery($PaymentId, $stepId, $action, $comments, $UserId)
{
	$q = new insertq();
	$q->table = 't_payment_approval_log';
	$q->columns = ['PaymentId', 'StepId', 'Action', 'Comments', 'UserId'];
	$q->values = [$PaymentId, $stepId, $action, $comments === '' ? null : $comments, $UserId];
	$q->pks = ['LogId'];
	$q->bUseInsetId = false;
	$q->build_query();
	return $q;
}

function mrExecResult($aQuerys, $UserId, $lan, $successMessage)
{
	$res = exec_query($aQuerys, $UserId, $lan);
	$success = ($res['msgType'] == 'success') ? 1 : 0;

	return [
		"success" => $success,
		"status" => $success ? 200 : 500,
		"UserId" => $UserId,
		"message" => $success ? $successMessage : $res['msg'],
	];
}

/**
 * Moves the MR from $stepId to the next step in one transaction.
 * $extraCols/$extraVals are written to t_payment together with the new StepId.
 * $extraQuerys run first in the same transaction (used by the final posting step).
 * $afterQuerys run after the StepId update (used to open G when both tracks are done).
 */
function mrApproveStep($data, $stepId, $extraCols = [], $extraVals = [], $extraQuerys = [], $nextStepId = null, $afterQuerys = [])
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : '';
	$Comments = isset($data->Comments) ? trim($data->Comments) : '';

	if ($PaymentId == '') {
		return msg(0, 422, 'Invalid MR.');
	}

	$check = mrCheckStep($PaymentId, $stepId);
	if ($check !== true) {
		return $check;
	}

	try {
		$aQuerys = $extraQuerys;

		$u = new updateq();
		$u->table = 't_payment';
		$u->columns = array_merge($extraCols, ['StepId']);
		$u->values = array_merge($extraVals, [$nextStepId !== null ? $nextStepId : mrNextStep($stepId)]);
		$u->pks = ['PaymentId'];
		$u->pk_values = [$PaymentId];
		$u->build_query();
		$aQuerys[] = $u;

		foreach ($afterQuerys as $afterQuery) {
			$aQuerys[] = $afterQuery;
		}

		$aQuerys[] = mrLogQuery($PaymentId, $stepId, 'Approve', $Comments, $UserId);

		return mrExecResult($aQuerys, $UserId, $lan, 'MR approved successfully.');
	} catch (PDOException $e) {
		return msg(0, 500, $e->getMessage());
	}
}

/**
 * Approves Allocate Invoice without moving the collection StepId, unless E is already done.
 * Sets AllocationStepId = 7, then opens G when StepId is already 6.
 */
function mrApproveAllocation($data, $extraCols = [], $extraVals = [])
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : '';
	$Comments = isset($data->Comments) ? trim($data->Comments) : '';

	if ($PaymentId == '') {
		return msg(0, 422, 'Invalid MR.');
	}

	$check = mrCheckAllocationOpen($PaymentId);
	if ($check !== true) {
		return $check;
	}

	try {
		$aQuerys = array();

		$u = new updateq();
		$u->table = 't_payment';
		$u->columns = array_merge($extraCols, ['AllocationStepId']);
		$u->values = array_merge($extraVals, [MR_STEP_COLLECTION_VERIFICATION]);
		$u->pks = ['PaymentId'];
		$u->pk_values = [$PaymentId];
		$u->build_query();
		$aQuerys[] = $u;
		$aQuerys[] = mrJoinGIfReadyQuery($PaymentId);
		$aQuerys[] = mrLogQuery($PaymentId, MR_STEP_ALLOCATE_INVOICE, 'Approve', $Comments, $UserId);

		return mrExecResult($aQuerys, $UserId, $lan, 'MR approved successfully.');
	} catch (PDOException $e) {
		return msg(0, 500, $e->getMessage());
	}
}

/** Closes the MR (StepId 99) and releases any allocated invoice/extend items. */
function mrRejectStep($data, $stepId, $checkFn = null)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : '';
	$Comments = isset($data->Comments) ? trim($data->Comments) : '';

	if ($PaymentId == '') {
		return msg(0, 422, 'Invalid MR.');
	}
	if ($Comments == '') {
		return msg(0, 422, 'Comments are required to reject.');
	}

	$check = is_callable($checkFn) ? $checkFn($PaymentId) : mrCheckStep($PaymentId, $stepId);
	if ($check !== true) {
		return $check;
	}

	try {
		$aQuerys = array();

		$d = new deleteq();
		$d->table = 't_paymentitems';
		$d->pks = ['PaymentId'];
		$d->pk_values = [$PaymentId];
		$d->build_query();
		$aQuerys[] = $d;

		$u = new updateq();
		$u->table = 't_payment';
		$u->columns = ['StepId'];
		$u->values = [MR_STEP_REJECTED];
		$u->pks = ['PaymentId'];
		$u->pk_values = [$PaymentId];
		$u->build_query();
		$aQuerys[] = $u;

		$aQuerys[] = mrLogQuery($PaymentId, $stepId, 'Reject', $Comments, $UserId);

		return mrExecResult($aQuerys, $UserId, $lan, 'MR rejected.');
	} catch (PDOException $e) {
		return msg(0, 500, $e->getMessage());
	}
}
