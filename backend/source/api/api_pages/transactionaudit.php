<?php

require_once(__DIR__ . '/mrworkflow_common.php');

const MR_STEP_THIS = MR_STEP_TRANSACTION_AUDIT;

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
		$returnData = mrApproveStep($data, MR_STEP_THIS);
		break;

	case "rejectData":
		$returnData = rejectData($data);
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
 * Rolls back the Collection Verification posting and sends the MR back to step 7.
 * The t_paymentitems allocation is kept. In one transaction:
 * delete this MR's extend rows, invoice IsPaid = 0, used extend rows IsFinished = 0,
 * StatusId = 1, PostUserId = NULL, StepId = 7 and the log row.
 */
function rejectData($data)
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

	$check = mrCheckStep($PaymentId, MR_STEP_THIS);
	if ($check !== true) {
		return $check;
	}

	try {
		$dbh = new Db();
		$aQuerys = array();

		$used = $dbh->query(
			"SELECT COUNT(1) AS Cnt
			FROM t_paymentitems pi
			INNER JOIN t_paymentextend pe ON pi.PaymentExtendId = pe.PaymentExtendId
			WHERE pe.PaymentId = :PaymentId;",
			['PaymentId' => $PaymentId]
		);
		if (is_array($used) && (int)$used[0]['Cnt'] > 0) {
			return msg(0, 409, 'The AIT/VAT/Advance/Due of this MR is already allocated to another MR. It cannot be rejected.');
		}

		$d = new deleteq();
		$d->table = 't_paymentextend';
		$d->pks = ['PaymentId'];
		$d->pk_values = [$PaymentId];
		$d->build_query();
		$aQuerys[] = $d;

		$invoiceItems = $dbh->query(
			"SELECT InvoiceItemId FROM t_paymentitems WHERE PaymentId = :PaymentId AND InvoiceItemId IS NOT NULL;",
			['PaymentId' => $PaymentId]
		);
		foreach ($invoiceItems as $row) {
			$u = new updateq();
			$u->table = 't_invoiceitems';
			$u->columns = ['IsPaid'];
			$u->values = [0];
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
			$u->values = [0];
			$u->pks = ['PaymentExtendId'];
			$u->pk_values = [$row['PaymentExtendId']];
			$u->build_query();
			$aQuerys[] = $u;
		}

		$u = new updateq();
		$u->table = 't_payment';
		$u->columns = ['StatusId', 'PostUserId', 'StepId'];
		$u->values = [1, null, MR_STEP_COLLECTION_VERIFICATION];
		$u->pks = ['PaymentId'];
		$u->pk_values = [$PaymentId];
		$u->build_query();
		$aQuerys[] = $u;

		$aQuerys[] = mrLogQuery($PaymentId, MR_STEP_THIS, 'Reject', $Comments, $UserId);

		return mrExecResult($aQuerys, $UserId, $lan, 'MR rejected and sent back to Collection Verification.');
	} catch (PDOException $e) {
		return msg(0, 500, $e->getMessage());
	}
}
