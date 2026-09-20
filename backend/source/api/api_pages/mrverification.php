<?php

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;

	case "dataAddEdit":
		$returnData = dataAddEdit($data);
		break;

	case "deleteData":
		$returnData = deleteData($data);
		break;

	case "getNextMRNumber":
		$returnData = getNextMRNumber();
		break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{
	try {
		$dbh = new Db();

		$query = "SELECT a.PaymentId AS id, 
			DATE_FORMAT(a.PaymentDate, '%Y-%m-%d') as PaymentDate,
			a.CustomerId, b.CustomerCode, b.CustomerName, a.CustomerGroupId, a.BankId, d.BankName,
			a.Remarks, a.StatusId, a.MRNo, a.RefNo, a.ChequeNumber, a.ChequeDate, a.BankBranchName,
			a.PaymentModeId, a.CurrencyId, a.PaymentReceiveCurrencyAmount, a.ExchangeRate,
			a.PaymentReceiveAmount, a.CollectedMethodId, a.CollectionAddress,
			a.CollectionPersonName, a.CollectionPersonPhone
		FROM t_payment a
		LEFT JOIN t_customer b ON a.CustomerId = b.CustomerId
		LEFT JOIN t_bank d ON a.BankId = d.BankId
		ORDER BY a.PaymentDate DESC, a.MRNo DESC;";

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

function dataAddEdit($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);

	$PaymentId = $data->rowData->id;
	$MRNo = $data->rowData->MRNo;
	$RefNo = $data->rowData->RefNo ? $data->rowData->RefNo : null;
	$PaymentDate = $data->rowData->PaymentDate;
	$CustomerId = $data->rowData->CustomerId ? $data->rowData->CustomerId : null;
	$CustomerGroupId = $data->rowData->CustomerGroupId ? $data->rowData->CustomerGroupId : null;
	$BankId = $data->rowData->BankId ? $data->rowData->BankId : null;
	$BankBranchName = $data->rowData->BankBranchName ? $data->rowData->BankBranchName : null;
	$ChequeNumber = $data->rowData->ChequeNumber ? $data->rowData->ChequeNumber : null;
	$ChequeDate = $data->rowData->ChequeDate ? $data->rowData->ChequeDate : null;
	$PaymentModeId = $data->rowData->PaymentModeId ? $data->rowData->PaymentModeId : null;
	$CurrencyId = $data->rowData->CurrencyId ? $data->rowData->CurrencyId : null;
	$PaymentReceiveCurrencyAmount = $data->rowData->PaymentReceiveCurrencyAmount ? $data->rowData->PaymentReceiveCurrencyAmount : 0;
	$ExchangeRate = $data->rowData->ExchangeRate ? $data->rowData->ExchangeRate : 1;
	$Remarks = $data->rowData->Remarks ? $data->rowData->Remarks : null;
	$StatusId = $data->rowData->StatusId ? $data->rowData->StatusId : 1;
	$PaymentReceiveAmount = $data->rowData->PaymentReceiveAmount ? $data->rowData->PaymentReceiveAmount : 0;
	$CollectedMethodId = $data->rowData->CollectedMethodId ? $data->rowData->CollectedMethodId : null;
	$CollectionAddress = $data->rowData->CollectionAddress ? $data->rowData->CollectionAddress : null;
	$CollectionPersonName = $data->rowData->CollectionPersonName ? $data->rowData->CollectionPersonName : null;
	$CollectionPersonPhone = $data->rowData->CollectionPersonPhone ? $data->rowData->CollectionPersonPhone : null;

	$paymentColumns = ['RefNo', 'PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId', 'BankBranchName', 'ChequeNumber', 'ChequeDate', 'PaymentModeId', 'CurrencyId', 'PaymentReceiveCurrencyAmount', 'ExchangeRate', 'Remarks', 'StatusId', 'PaymentReceiveAmount', 'CollectedMethodId', 'CollectionAddress', 'CollectionPersonName', 'CollectionPersonPhone'];
	$paymentValues = [$RefNo, $PaymentDate, $CustomerId, $CustomerGroupId, $BankId, $BankBranchName, $ChequeNumber, $ChequeDate, $PaymentModeId, $CurrencyId, $PaymentReceiveCurrencyAmount, $ExchangeRate, $Remarks, $StatusId, $PaymentReceiveAmount, $CollectedMethodId, $CollectionAddress, $CollectionPersonName, $CollectionPersonPhone];

	try {
		$aQuerys = array();

		if ($PaymentId == "") {
			$NextMR = getNextMRNumber();
			$MRNo = $NextMR['MRNo'];

			$q = new insertq();
			$q->table = 't_payment';
			$q->columns = array_merge(['MRNo', 'UserId'], $paymentColumns);
			$q->values = array_merge([$MRNo, $UserId], $paymentValues);
			$q->pks = ['PaymentId'];
			$q->bUseInsetId = true;
			$q->build_query();
			$aQuerys[] = $q;
		} else {
			$PostUserId = $StatusId == 5 ? $UserId : null;
			$u = new updateq();
			$u->table = 't_payment';
			$u->columns = array_merge($paymentColumns, ['PostUserId']);
			$u->values = array_merge($paymentValues, [$PostUserId]);
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
