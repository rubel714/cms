<?php

require_once(__DIR__ . '/mrworkflow_common.php');

const MR_STEP_THIS = 3;

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = mrGetStepDataList(MR_STEP_THIS);
		break;

	case "saveData":
		$returnData = saveData($data);
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

/** Bank and the fields after it, which this step is allowed to change. */
function bankFieldValues($rowData)
{
	$blank = function ($value) {
		return isset($value) && trim((string)$value) !== '' ? $value : null;
	};

	$columns = ['BankId', 'BankBranchName', 'ChequeNumber', 'ChequeDate', 'PaymentModeId', 'CurrencyId', 'PaymentReceiveCurrencyAmount', 'ExchangeRate', 'PaymentReceiveAmount', 'CollectedMethodId', 'CollectionPersonName', 'CollectionPersonPhone', 'CollectionAddress', 'Remarks'];
	$values = [
		$blank($rowData->BankId),
		$blank($rowData->BankBranchName),
		$blank($rowData->ChequeNumber),
		$blank($rowData->ChequeDate),
		$blank($rowData->PaymentModeId),
		$blank($rowData->CurrencyId),
		$rowData->PaymentReceiveCurrencyAmount ? $rowData->PaymentReceiveCurrencyAmount : 0,
		$rowData->ExchangeRate ? $rowData->ExchangeRate : 1,
		$rowData->PaymentReceiveAmount ? $rowData->PaymentReceiveAmount : 0,
		$blank($rowData->CollectedMethodId),
		$blank($rowData->CollectionPersonName),
		$blank($rowData->CollectionPersonPhone),
		$blank($rowData->CollectionAddress),
		$blank($rowData->Remarks),
	];

	return [$columns, $values];
}

function saveData($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : '';
	if ($PaymentId == '' || !isset($data->rowData)) {
		return msg(0, 422, 'Invalid MR.');
	}

	$check = mrCheckStep($PaymentId, MR_STEP_THIS);
	if ($check !== true) {
		return $check;
	}

	list($columns, $values) = bankFieldValues($data->rowData);

	$u = new updateq();
	$u->table = 't_payment';
	$u->columns = $columns;
	$u->values = $values;
	$u->pks = ['PaymentId'];
	$u->pk_values = [$PaymentId];
	$u->build_query();

	return mrExecResult([$u], trim($data->UserId), trim($data->lan), 'Data Updated Successfully');
}

function approveData($data)
{
	$columns = [];
	$values = [];
	if (isset($data->rowData)) {
		list($columns, $values) = bankFieldValues($data->rowData);
	}

	return mrApproveStep($data, MR_STEP_THIS, $columns, $values);
}

