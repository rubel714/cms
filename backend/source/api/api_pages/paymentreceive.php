<?php

require_once(__DIR__ . '/mrworkflow_common.php');

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = mrGetStepDataList(MR_STEP_PAYMENT_RECEIVE);
		break;

	case "dataAddEdit":
		$returnData = dataAddEdit($data);
		break;

	case "submitData":
		$returnData = submitData($data);
		break;

	case "deleteData":
		$returnData = deleteData($data);
		break;

	case "getNextMRNumber":
		$returnData = getNextMRNumber();
		break;

	case "getApprovalLog":
		$returnData = mrGetApprovalLog($data);
		break;

	default:
		echo "{failure:true}";
		break;
}

function paymentFormValues($rowData)
{
	$columns = ['RefNo', 'PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId', 'BankBranchName', 'ChequeNumber', 'ChequeDate', 'PaymentModeId', 'CurrencyId', 'PaymentReceiveCurrencyAmount', 'ExchangeRate', 'Remarks', 'PaymentReceiveAmount', 'CollectedMethodId', 'CollectionAddress', 'CollectionPersonName', 'CollectionPersonPhone'];
	$values = [
		$rowData->RefNo ? $rowData->RefNo : null,
		$rowData->PaymentDate,
		$rowData->CustomerId ? $rowData->CustomerId : null,
		$rowData->CustomerGroupId ? $rowData->CustomerGroupId : null,
		$rowData->BankId ? $rowData->BankId : null,
		$rowData->BankBranchName ? $rowData->BankBranchName : null,
		$rowData->ChequeNumber ? $rowData->ChequeNumber : null,
		$rowData->ChequeDate ? $rowData->ChequeDate : null,
		$rowData->PaymentModeId ? $rowData->PaymentModeId : null,
		$rowData->CurrencyId ? $rowData->CurrencyId : null,
		$rowData->PaymentReceiveCurrencyAmount ? $rowData->PaymentReceiveCurrencyAmount : 0,
		$rowData->ExchangeRate ? $rowData->ExchangeRate : 1,
		$rowData->Remarks ? $rowData->Remarks : null,
		$rowData->PaymentReceiveAmount ? $rowData->PaymentReceiveAmount : 0,
		$rowData->CollectedMethodId ? $rowData->CollectedMethodId : null,
		$rowData->CollectionAddress ? $rowData->CollectionAddress : null,
		$rowData->CollectionPersonName ? $rowData->CollectionPersonName : null,
		$rowData->CollectionPersonPhone ? $rowData->CollectionPersonPhone : null,
	];

	return [$columns, $values];
}

function dataAddEdit($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$PaymentId = $data->rowData->id;

	list($paymentColumns, $paymentValues) = paymentFormValues($data->rowData);

	if ($PaymentId != "") {
		$check = mrCheckStep($PaymentId, MR_STEP_PAYMENT_RECEIVE);
		if ($check !== true) {
			return $check;
		}
	}

	try {
		$aQuerys = array();

		if ($PaymentId == "") {
			$NextMR = getNextMRNumber();
			$MRNo = $NextMR['MRNo'];

			$q = new insertq();
			$q->table = 't_payment';
			$q->columns = array_merge(['MRNo', 'UserId', 'StatusId', 'StepId'], $paymentColumns);
			$q->values = array_merge([$MRNo, $UserId, 1, MR_STEP_PAYMENT_RECEIVE], $paymentValues);
			$q->pks = ['PaymentId'];
			$q->bUseInsetId = true;
			$q->build_query();
			$aQuerys[] = $q;
		} else {
			$u = new updateq();
			$u->table = 't_payment';
			$u->columns = $paymentColumns;
			$u->values = $paymentValues;
			$u->pks = ['PaymentId'];
			$u->pk_values = [$PaymentId];
			$u->build_query();
			$aQuerys[] = $u;
		}

		$res = exec_query($aQuerys, $UserId, $lan);
		$success = ($res['msgType'] == 'success') ? 1 : 0;
		$status = ($res['msgType'] == 'success') ? 200 : 500;

		$returnData = [
			"success" => $success,
			"status" => $status,
			"PaymentId" => $PaymentId == "" ? $res['PaymentId'] : $PaymentId,
			"UserId" => $UserId,
			"message" => $res['msg'],
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

/** Saves the latest form data and sends the MR to MR Verification (step 2). */
function submitData($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$PaymentId = isset($data->rowData->id) ? trim($data->rowData->id) : '';
	$Comments = isset($data->Comments) ? trim($data->Comments) : '';

	if ($PaymentId == '') {
		return msg(0, 422, 'Please save the MR before submitting.');
	}

	$check = mrCheckStep($PaymentId, MR_STEP_PAYMENT_RECEIVE);
	if ($check !== true) {
		return $check;
	}

	list($paymentColumns, $paymentValues) = paymentFormValues($data->rowData);

	try {
		$aQuerys = array();

		$u = new updateq();
		$u->table = 't_payment';
		$u->columns = array_merge($paymentColumns, ['StepId', 'ReceiveSubmitDate']);
		$u->values = array_merge($paymentValues, [mrNextStep(MR_STEP_PAYMENT_RECEIVE), date('Y-m-d H:i:s')]);
		$u->pks = ['PaymentId'];
		$u->pk_values = [$PaymentId];
		$u->build_query();
		$aQuerys[] = $u;

		$aQuerys[] = mrLogQuery($PaymentId, MR_STEP_PAYMENT_RECEIVE, 'Submit', $Comments, $UserId);

		return mrExecResult($aQuerys, $UserId, $lan, 'MR submitted for verification.');
	} catch (PDOException $e) {
		return msg(0, 500, $e->getMessage());
	}
}

function deleteData($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} elseif (!isset($data->rowData->id)) {
		$fields = ['fields' => ['id']];
		return $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
	}

	$PaymentId = $data->rowData->id;
	$lan = trim($data->lan);
	$UserId = trim($data->UserId);

	$check = mrCheckStep($PaymentId, MR_STEP_PAYMENT_RECEIVE);
	if ($check !== true) {
		return $check;
	}

	try {
		$aQuerys = array();

		$d = new deleteq();
		$d->table = 't_payment';
		$d->pks = ['PaymentId'];
		$d->pk_values = [$PaymentId];
		$d->build_query();
		$aQuerys[] = $d;

		$res = exec_query($aQuerys, $UserId, $lan);
		$success = ($res['msgType'] == 'success') ? 1 : 0;
		$status = ($res['msgType'] == 'success') ? 200 : 500;

		$returnData = [
			"success" => $success,
			"status" => $status,
			"UserId" => $UserId,
			"message" => $res['msg']
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function getNextMRNumber()
{
	try {
		$dbh = new Db();

		$query = "SELECT CONCAT('MR-',LPAD((IFNULL(MAX(SUBSTRING_INDEX(MRNo, '-', -1)),0) + 1),6,'0')) AS NextMRNo FROM t_payment;";
		$result = $dbh->query($query);
		$MRNo = $result[0]['NextMRNo'];

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"MRNo" => $MRNo
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}
