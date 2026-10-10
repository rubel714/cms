<?php

require_once(__DIR__ . '/mrworkflow_common.php');

const MR_STEP_THIS = 6;

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = mrGetStepDataList(MR_STEP_THIS);
		break;

	case "getUnpaidInvoices":
		$returnData = getUnpaidInvoices($data);
		break;

	case "addInvoicesToPayment":
		$returnData = addInvoicesToPayment($data);
		break;

	case "deletePaymentItem":
		$returnData = deletePaymentItem($data);
		break;

	case "getDataSingle":
		$returnData = getDataSingle($data);
		break;

	case "dataAddEdit":
		$returnData = dataAddEdit($data);
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

function deletePaymentItem($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : "";
	$PaymentItemId = isset($data->PaymentItemId) ? trim($data->PaymentItemId) : "";
	$InvoiceItemId = isset($data->InvoiceItemId) ? trim($data->InvoiceItemId) : "";

	if ($PaymentId == "" || $PaymentItemId == "") {
		return $returnData = msg(0, 422, 'Invalid payment item.');
	}

	$check = mrCheckStep($PaymentId, MR_STEP_THIS);
	if ($check !== true) {
		return $check;
	}

	try {
		$aQuerys = array();

		$d = new deleteq();
		$d->table = 't_paymentitems';
		$d->pks = ['PaymentItemId'];
		$d->pk_values = [$PaymentItemId];
		$d->build_query();
		$aQuerys[] = $d;

		$res = exec_query($aQuerys, $UserId, $lan);
		$success = ($res['msgType'] == 'success') ? 1 : 0;
		$status = ($res['msgType'] == 'success') ? 200 : 500;

		$returnData = [
			"success" => $success,
			"status" => $status,
			"message" => $res['msg'],
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function getUnpaidInvoices($data)
{
	$CustomerId = $data->CustomerId;
	// $BuyerId = isset($data->BuyerId) ? trim($data->BuyerId) : '';
	// $MerchantId = isset($data->MerchantId) ? trim($data->MerchantId) : '';
	// $PaymentId = isset($data->PaymentId) && trim($data->PaymentId) !== "" ? trim($data->PaymentId) : 0;

	$DateFilter = "";
	if (isset($data->InvoiceStartDate) && isset($data->InvoiceEndDate)) {
		if ($data->InvoiceStartDate != "" && $data->InvoiceEndDate != "") {

			$StartDate = trim($data->InvoiceStartDate);
			$EndDate = trim($data->InvoiceEndDate) . " 23:59:59";

			$DateFilter = " AND (STR_TO_DATE(a.TransactionDate, '%d%m%Y') between '$StartDate' and '$EndDate') ";
		}
	}

	$BillNumber = isset($data->BillNumber) ? trim($data->BillNumber) : '';


	try {
		$dbh = new Db();

		if ($BillNumber != "") {
			$query = "SELECT a.InvoiceItemId,
			DATE_FORMAT(STR_TO_DATE(CONCAT(RIGHT(a.AccountingPeriod,4), '-',LPAD(LEFT(a.AccountingPeriod, LENGTH(a.AccountingPeriod)-4),2,'0'), '-01'),'%Y-%m-%d'),'%M-%Y') as AccountingPeriod,
			a.AccountCode, a.Description, DATE_FORMAT(STR_TO_DATE(a.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
			a.TransactionReference,	a.TransactionAmount, a.ExchangeRate, a.BaseAmount
			FROM t_invoiceitems a
			inner join t_billitems aa on a.InvoiceItemId=aa.InvoiceItemId
			inner join t_bill aaa on aa.BillId=aaa.BillId and aaa.BillNumber='$BillNumber'
			inner join t_customer c on a.AccountCode=c.CustomerCode
			left join t_paymentitems p on a.InvoiceItemId=p.InvoiceItemId
			where c.CustomerId = $CustomerId
			and a.IsPaid = 0
			and p.PaymentItemId is null and (a.AdjFlag is null or a.AdjFlag = 'Approved')
			$DateFilter
			ORDER BY STR_TO_DATE(a.TransactionDate, '%d%m%Y') DESC;";
		} else {
			$query = "SELECT 0 AutoNumber, 0 InvoiceItemId, m.PaymentExtendId,'' AccountingPeriod, '' AccountCode, '' `Description`, DATE_FORMAT(n.PaymentDate, '%d/%m/%Y') AS TransactionDate,
					CONCAT(m.RptNumber,' - ',o.RptPreFix) AS TransactionReference,0 AS TransactionAmount, 0 ExchangeRate, (m.Amount*o.IsWillPay) AS BaseAmount, 0 InvType
					FROM t_paymentextend m
					INNER JOIN t_payment n ON m.PaymentId=n.PaymentId
					INNER JOIN t_payment_extend_type o ON m.PaymentExtendTypeId=o.PaymentExtendTypeId
					LEFT JOIN t_paymentitems p ON m.PaymentExtendId=p.PaymentExtendId
					WHERE n.CustomerId = $CustomerId
					AND m.IsFinished = 0
					AND p.PaymentExtendId IS NULL

					UNION ALL

					SELECT 0 AutoNumber,a.InvoiceItemId, 0 PaymentExtendId,
					DATE_FORMAT(STR_TO_DATE(CONCAT(RIGHT(a.AccountingPeriod,4), '-',LPAD(LEFT(a.AccountingPeriod, LENGTH(a.AccountingPeriod)-4),2,'0'), '-01'),'%Y-%m-%d'),'%M-%Y') as AccountingPeriod,
					a.AccountCode, a.Description, DATE_FORMAT(STR_TO_DATE(a.TransactionDate, '%d%m%Y'), '%d/%m/%Y') AS TransactionDate,
					a.TransactionReference,	a.TransactionAmount, a.ExchangeRate, a.BaseAmount, 1 InvType
					FROM t_invoiceitems a
					INNER JOIN t_customer c ON a.AccountCode=c.CustomerCode
					LEFT JOIN t_paymentitems p ON a.InvoiceItemId=p.InvoiceItemId
					WHERE c.CustomerId = $CustomerId
					AND a.IsPaid = 0
					AND p.PaymentItemId IS NULL and (a.AdjFlag is null or a.AdjFlag = 'Approved')
					$DateFilter
					
					ORDER BY InvType ASC, TransactionDate DESC;";
		}

		$resultdata = $dbh->query($query);

		$dataList = array();
		foreach ($resultdata as $key => $obj) {
			$obj['AutoNumber'] = $key+1;
			$dataList[] = $obj;
		}

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $dataList
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function addInvoicesToPayment($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : "";
	$invoices = isset($data->invoices) ? $data->invoices : [];

	if ($PaymentId == "" || empty($invoices)) {
		return $returnData = msg(0, 422, 'Please select at least one invoice.');
	}

	$check = mrCheckStep($PaymentId, MR_STEP_THIS);
	if ($check !== true) {
		return $check;
	}

	try {
		$dbh = new Db();
		$aQuerys = array();

		foreach ($invoices as $obj) {


			$InvoiceItemId = isset($obj->InvoiceItemId) ? $obj->InvoiceItemId : null;
			$PaymentExtendId = isset($obj->PaymentExtendId) ? $obj->PaymentExtendId : null;
			if (!$InvoiceItemId && !$PaymentExtendId) {
				continue;
			}

			if($InvoiceItemId > 0){
				//if already added to payment items, skip to avoid duplicate entry
				$chk = "SELECT count(1) as Cnt FROM t_paymentitems WHERE InvoiceItemId=$InvoiceItemId;";
				$chkRes = $dbh->query($chk);
				if (!empty($chkRes) && $chkRes[0]['Cnt'] > 0) {
					continue;
				}

				$q = new insertq();
				$q->table = 't_paymentitems';
				$q->columns = ['PaymentId', 'InvoiceItemId'];
				$q->values = [$PaymentId, $InvoiceItemId];
				$q->pks = ['PaymentItemId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;
			}



			if($PaymentExtendId > 0){
				//if already added to payment items, skip to avoid duplicate entry
				$chk = "SELECT count(1) as Cnt FROM t_paymentitems WHERE PaymentExtendId=$PaymentExtendId;";
				$chkRes = $dbh->query($chk);
				if (!empty($chkRes) && $chkRes[0]['Cnt'] > 0) {
					continue;
				}

				$q = new insertq();
				$q->table = 't_paymentitems';
				$q->columns = ['PaymentId', 'PaymentExtendId'];
				$q->values = [$PaymentId, $PaymentExtendId];
				$q->pks = ['PaymentItemId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;
			}


		}

		if (count($aQuerys) === 0) {
			return $returnData = msg(0, 422, 'No new invoices were added.');
		}

		$res = exec_query($aQuerys, $UserId, $lan);
		$success = ($res['msgType'] == 'success') ? 1 : 0;
		$status = ($res['msgType'] == 'success') ? 200 : 500;

		$returnData = [
			"success" => $success,
			"status" => $status,
			"message" => $res['msg'],
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
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

/** Calculation fields edited on the Allocate Invoice screen. */
function allocationValues($rowData)
{
	$columns = ['Remarks', 'RebatePercent', 'RebateAmount', 'CNPercent', 'CNAmount', 'AitPercent', 'AitDeduction', 'VatPercent', 'VatAmount', 'AdvanceAmount', 'DueAmount'];
	$values = [
		$rowData->Remarks ? $rowData->Remarks : null,
		$rowData->RebatePercent ? $rowData->RebatePercent : 0,
		$rowData->RebateAmount ? $rowData->RebateAmount : 0,
		$rowData->CNPercent ? $rowData->CNPercent : 0,
		$rowData->CNAmount ? $rowData->CNAmount : 0,
		$rowData->AitPercent ? $rowData->AitPercent : 0,
		$rowData->AitDeduction ? $rowData->AitDeduction : 0,
		$rowData->VatPercent ? $rowData->VatPercent : 0,
		$rowData->VatAmount ? $rowData->VatAmount : 0,
		$rowData->AdvanceAmount ? $rowData->AdvanceAmount : 0,
		$rowData->DueAmount ? $rowData->DueAmount : 0,
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
	$PaymentId = isset($data->rowData->id) ? trim($data->rowData->id) : '';

	if ($PaymentId == '') {
		return msg(0, 422, 'Invalid MR.');
	}

	$check = mrCheckStep($PaymentId, MR_STEP_THIS);
	if ($check !== true) {
		return $check;
	}

	list($columns, $values) = allocationValues($data->rowData);

	try {
		$u = new updateq();
		$u->table = 't_payment';
		$u->columns = $columns;
		$u->values = $values;
		$u->pks = ['PaymentId'];
		$u->pk_values = [$PaymentId];
		$u->build_query();

		$returnData = mrExecResult([$u], $UserId, $lan, 'Data Updated Successfully');
		$returnData["PaymentId"] = $PaymentId;
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

/** Saves the latest calculation and moves the MR to Collection Verification (step 7). */
function approveData($data)
{
	$PaymentId = isset($data->PaymentId) ? trim($data->PaymentId) : '';
	if ($PaymentId == '' || !isset($data->rowData)) {
		return msg(0, 422, 'Invalid MR.');
	}

	list($columns, $values) = allocationValues($data->rowData);

	$dbh = new Db();
	$rows = $dbh->query("SELECT COUNT(1) AS Cnt FROM t_paymentitems WHERE PaymentId = :PaymentId;", ['PaymentId' => $PaymentId]);
	$itemCount = is_array($rows) ? (int)$rows[0]['Cnt'] : 0;
	$AdvanceAmount = $data->rowData->AdvanceAmount ? (float)$data->rowData->AdvanceAmount : 0;

	if ($itemCount == 0 && $AdvanceAmount <= 0) {
		return msg(0, 422, 'Please allocate at least one invoice before approving.');
	}

	return mrApproveStep($data, MR_STEP_THIS, $columns, $values);
}
