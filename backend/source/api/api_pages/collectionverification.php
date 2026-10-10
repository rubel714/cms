<?php

require_once(__DIR__ . '/mrworkflow_common.php');

const MR_STEP_THIS = MR_STEP_COLLECTION_VERIFICATION;

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = mrGetStepDataList(MR_STEP_THIS);
		break;

	case "getDataSingle":
		$returnData = getDataSingle($data);
		break;

	case "approveData":
		$returnData = approveData($data);
		break;

	case "rejectData":
		$returnData = mrRejectStep($data, MR_STEP_THIS);
		break;

	case "getApprovalLog":
		$returnData = mrGetApprovalLog($data);
		break;

	default:
		echo "{failure:true}";
		break;
}

function getDataSingle($data)
{
	$PaymentId = trim($data->id);

	try {
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => array("master" => [], "items" => mrGetPaymentItems($PaymentId))
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

/**
 * Final approval = posting. In one transaction:
 * extend rows (AIT/VAT/Advance/Due), invoice IsPaid, used extend rows IsFinished,
 * MR totals, StatusId = 5 (Post), StepId = 8 (Completed) and the log row.
 */
function approveData($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$UserId = trim($data->UserId);
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : '';

	if ($PaymentId == '') {
		return msg(0, 422, 'Invalid MR.');
	}

	$check = mrCheckStep($PaymentId, MR_STEP_THIS);
	if ($check !== true) {
		return $check;
	}

	try {
		$dbh = new Db();
		$aQuerys = array();

		$payment = $dbh->query(
			"SELECT MRNo, AitDeduction, VatAmount, AdvanceAmount, DueAmount FROM t_payment WHERE PaymentId = :PaymentId;",
			['PaymentId' => $PaymentId]
		)[0];

		$extendAmounts = [
			3 => $payment['AitDeduction'],
			4 => $payment['VatAmount'],
			5 => $payment['AdvanceAmount'],
			6 => $payment['DueAmount'],
		];
		foreach ($extendAmounts as $PaymentExtendTypeId => $Amount) {
			if ((float)$Amount > 0) {
				$q = new insertq();
				$q->table = 't_paymentextend';
				$q->columns = ['PaymentId', 'PaymentExtendTypeId', 'RptNumber', 'Amount'];
				$q->values = [$PaymentId, $PaymentExtendTypeId, $payment['MRNo'], $Amount];
				$q->pks = ['PaymentExtendId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;
			}
		}

		$invoiceItems = $dbh->query(
			"SELECT InvoiceItemId FROM t_paymentitems WHERE PaymentId = :PaymentId AND InvoiceItemId IS NOT NULL;",
			['PaymentId' => $PaymentId]
		);
		foreach ($invoiceItems as $row) {
			$u = new updateq();
			$u->table = 't_invoiceitems';
			$u->columns = ['IsPaid'];
			$u->values = [1];
			$u->pks = ['InvoiceItemId'];
			$u->pk_values = [$row['InvoiceItemId']];
			$u->build_query();
			$aQuerys[] = $u;
		}

		$extendItems = $dbh->query(
			"SELECT PaymentExtendId FROM t_paymentitems WHERE PaymentId = :PaymentId AND PaymentExtendId IS NOT NULL;",
			['PaymentId' => $PaymentId]
		);
		foreach ($extendItems as $row) {
			$u = new updateq();
			$u->table = 't_paymentextend';
			$u->columns = ['IsFinished'];
			$u->values = [1];
			$u->pks = ['PaymentExtendId'];
			$u->pk_values = [$row['PaymentExtendId']];
			$u->build_query();
			$aQuerys[] = $u;
		}

		$totals = $dbh->query(
			"SELECT IFNULL(SUM(n.BaseAmount),0) SumBaseAmount, IFNULL(SUM(n.TransactionAmount),0) SumTransactionAmount
			FROM t_paymentitems m
			INNER JOIN t_invoiceitems n ON m.InvoiceItemId = n.InvoiceItemId
			WHERE m.PaymentId = :PaymentId;",
			['PaymentId' => $PaymentId]
		)[0];

		return mrApproveStep(
			$data,
			MR_STEP_THIS,
			['StatusId', 'PostUserId', 'TotalBaseAmount', 'TotalTransactionAmount'],
			[5, $UserId, $totals['SumBaseAmount'], $totals['SumTransactionAmount']],
			$aQuerys
		);
	} catch (PDOException $e) {
		return msg(0, 500, $e->getMessage());
	}
}
