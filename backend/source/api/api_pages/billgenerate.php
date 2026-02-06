<?php

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;
	case "getUnbilledInvoices":
		$returnData = getUnbilledInvoices($data);
		break;
	case "addInvoicesToBill":
		$returnData = addInvoicesToBill($data);
		break;
	case "deleteBillItem":
		$returnData = deleteBillItem($data);
		break;



	case "getDataSingle":
		$returnData = getDataSingle($data);
		break;

	case "dataAddEdit":
		$returnData = dataAddEdit($data);
		break;

	case "deleteData":
		$returnData = deleteData($data);
		break;

	// case "getNextMRNumber":
	// 	$returnData = getNextMRNumber();
	// 	break;

	default:
		echo "{failure:true}";
		break;
}

function addInvoicesToBill($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$BillId = isset($data->PaymentId) ? trim($data->PaymentId) : "";
	$invoices = isset($data->invoices) ? $data->invoices : [];

	if ($BillId == "" || empty($invoices)) {
		return $returnData = msg(0, 422, 'Please select at least one invoice.');
	}

	try {
		$dbh = new Db();
		$aQuerys = array();

		foreach ($invoices as $obj) {
			$InvoiceItemId = isset($obj->InvoiceItemId) ? $obj->InvoiceItemId : null;
			if (!$InvoiceItemId) {
				continue;
			}

			$chk = "SELECT count(1) as Cnt FROM t_billitems WHERE BillId=$BillId AND InvoiceItemId=$InvoiceItemId;";
			$chkRes = $dbh->query($chk);
			if (!empty($chkRes) && $chkRes[0]['Cnt'] > 0) {
				continue;
			}

			$q = new insertq();
			$q->table = 't_billitems';
			$q->columns = ['BillId', 'InvoiceItemId'];
			$q->values = [$BillId, $InvoiceItemId];
			$q->pks = ['BillItemId'];
			$q->bUseInsetId = true;
			$q->build_query();
			$aQuerys[] = $q;

			// $u = new updateq();
			// $u->table = 't_invoiceitems';
			// $u->columns = ['IsBilled'];
			// $u->values = [1];
			// $u->pks = ['InvoiceItemId'];
			// $u->pk_values = [$InvoiceItemId];
			// $u->build_query();
			// $aQuerys[] = $u;
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

function deleteBillItem($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$BillId = isset($data->BillId) ? trim($data->BillId) : "";
	$BillItemId = isset($data->BillItemId) ? trim($data->BillItemId) : "";
	$InvoiceItemId = isset($data->InvoiceItemId) ? trim($data->InvoiceItemId) : "";

	if ($BillId == "" || $BillItemId == "") {
		return $returnData = msg(0, 422, 'Invalid bill item.');
	}

	try {
		$aQuerys = array();

		$d = new deleteq();
		$d->table = 't_billitems';
		$d->pks = ['BillItemId'];
		$d->pk_values = [$BillItemId];
		$d->build_query();
		$aQuerys[] = $d;

		// if ($InvoiceItemId != "") {
		// 	$u = new updateq();
		// 	$u->table = 't_invoiceitems';
		// 	$u->columns = ['IsBilled'];
		// 	$u->values = [0];
		// 	$u->pks = ['InvoiceItemId'];
		// 	$u->pk_values = [$InvoiceItemId];
		// 	$u->build_query();
		// 	$aQuerys[] = $u;
		// }

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

function getDataList($data)
{

	try {
		$dbh = new Db();

		$query = "SELECT a.BillId AS id,a.BillNumber, DATE_FORMAT(a.BillDate, '%Y-%m-%d') as BillDate,
		a.CustomerId,b.CustomerCode,b.CustomerName, 
		case when a.InvoiceStartDate is not null then DATE_FORMAT(a.InvoiceStartDate, '%Y-%m-%d') else null end as InvoiceStartDate,
		case when a.InvoiceEndDate is not null then DATE_FORMAT(a.InvoiceEndDate, '%Y-%m-%d') else null end as InvoiceEndDate,
		a.BuyerName,a.MerchantName,
		a.BusinessLine,a.Remarks,a.StatusId
		FROM t_bill a
		INNER JOIN t_customer b ON a.CustomerId = b.CustomerId
		ORDER BY a.BillDate DESC, a.BillId DESC;";

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

function getUnbilledInvoices($data)
{

	$CustomerId = $data->CustomerId; 
	$BuyerId = isset($data->BuyerId)?trim($data->BuyerId): ''; 
	$MerchantId = isset($data->MerchantId)?trim($data->MerchantId): ''; 

	$DateFilter = "";
	if(isset($data->StartDate) && isset($data->EndDate)) {
		$StartDate = trim($data->StartDate);
		$EndDate = trim($data->EndDate) . " 23:59:59";

		$DateFilter = " AND (STR_TO_DATE(a.TransactionDate, '%d%m%Y') between '$StartDate' and '$EndDate') ";
	}
	// $StartDate ="2025-01-01";
	// $EndDate = "2027-01-01 23:59:59";

	try {
		$dbh = new Db();
	 	$query = "SELECT a.*,DATE_FORMAT(STR_TO_DATE(a.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate, b.UserName as CustomerUserName
		FROM t_invoiceitems a
		left join t_users b on a.CustomerUserId=b.UserId
		inner join t_customer c on a.AccountCode=c.CustomerCode
		left join t_billitems d on a.InvoiceItemId=d.InvoiceItemId
	    where c.CustomerId = $CustomerId 
		and (a.GeneralDescription11 = '$BuyerId' OR '$BuyerId' = '')
		and (a.GeneralDescription14 = '$MerchantId' OR '$MerchantId' = '')
		and a.IsBilled=0
		and d.BillItemId is null
		$DateFilter
		ORDER BY STR_TO_DATE(a.TransactionDate, '%d%m%Y') DESC;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function getDataSingle($data)
{
	$BillId = trim($data->id);

	try {
		$dbh = new Db();

		$resultdataMaster = [];

		/**Items Data */
		$query = "SELECT a.BillItemId as autoId, a.BillItemId, a.`BillId`, a.`InvoiceItemId`, 
		DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
		
		b.GeneralDescription9,b.TransactionReference,b.GeneralDescription11,b.GeneralDescription17,
		null OrderNumber,b.TransactionAmount, b.ExchangeRate, b.BaseAmount,b.GeneralDescription14
		FROM t_billitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.BillId=$BillId
		order by a.BillItemId ASC;";
		$resultdataItems = $dbh->query($query);


		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => array("master" => $resultdataMaster, "items" => $resultdataItems)
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
	} else {

		$dbh = new Db();

		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		$BillId = $data->rowData->id;
		// $MRNo = $data->rowData->MRNo;
		// $RefNo = $data->rowData->RefNo ? $data->rowData->RefNo : null;

		$BillDate = $data->rowData->BillDate;
		$CustomerId = $data->rowData->CustomerId ? $data->rowData->CustomerId : null;
		// $CustomerGroupId = $data->rowData->CustomerGroupId ? $data->rowData->CustomerGroupId : null;
		// $BankId = $data->rowData->BankId ? $data->rowData->BankId : null;
		// $BankBranchName = $data->rowData->BankBranchName ? $data->rowData->BankBranchName : null;
		// $ChequeNumber = $data->rowData->ChequeNumber ? $data->rowData->ChequeNumber : null;
		// $ChequeDate = $data->rowData->ChequeDate ? $data->rowData->ChequeDate : null;
		// $TotalPaymentAmount = $data->rowData->TotalPaymentAmount ? $data->rowData->TotalPaymentAmount : 0;
		$Remarks = $data->rowData->Remarks ? $data->rowData->Remarks : null;
		$StatusId = $data->rowData->StatusId ? $data->rowData->StatusId : 1;

		// $items = isset($data->items) ? $data->items : [];
		
		$BillIdCheck = "";
		if($BillId != "") {
			$BillIdCheck = " and a.BillId != $BillId ";
		}

		$query = "SELECT count(a.BillId) DraftCount
		FROM t_bill a
		where a.CustomerId=$CustomerId
		and a.StatusId=1
		$BillIdCheck;";

		$resultdatalist = $dbh->query($query);
		if ($resultdatalist[0]['DraftCount'] >= 1 && $BillId == "") {
			return $returnData = msg(0, 500, 'There is already a draft bill for this customer. Please complete or delete the existing draft bill before creating a new one.');
		}


		try {

			$dbh = new Db();
			$aQuerys = array();

			if ($BillId == "") {

				$query = "SELECT ifnull(max(BillNumber), 0)+1 NextBillNumber FROM t_bill;";
				$resultdatalist = $dbh->query($query);
				$BillNumber = $resultdatalist[0]['NextBillNumber'];

				$q = new insertq();
				$q->table = 't_bill';
				$q->columns = ['BillNumber','BillDate', 'CustomerId', 'Remarks', 'UserId', 'StatusId'];
				$q->values = [$BillNumber, $BillDate, $CustomerId, $Remarks, $UserId, $StatusId];
				$q->pks = ['BillId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;
			} else {
				// $StatusId = 5; //Completed
				$u = new updateq();
				$u->table = 't_bill';
				$u->columns = ['BillDate', 'CustomerId','Remarks', 'StatusId'];
				$u->values = [$BillDate, $CustomerId, $Remarks, $StatusId];
				$u->pks = ['BillId'];
				$u->pk_values = [$BillId];
				$u->build_query();
				$aQuerys[] = $u;

			}


			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;

			//when post then set billed flag in invoice
			if ($success == 1 && $BillId != "" && $StatusId == 5) {
				$query1 = "update t_invoiceitems set IsBilled = 1 
					where InvoiceItemId in (select InvoiceItemId from t_billitems where BillId = $BillId);";
					$dbh->query($query1);
			}



			$returnData = [
				"success" => $success,
				"status" => $status,
				"BillId" => $BillId == "" ? $res['BillId'] : $BillId,
				"UserId" => $UserId,
				"message" => $res['msg'],
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}


function deleteData($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif (!isset($data->rowData->id)) {
		$fields = ['fields' => ['id']];
		return $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
	} else {

		$BillId = $data->rowData->id;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		try {

			$d = new deleteq();
			$d->table = 't_billitems';
			$d->pks = ['BillId'];
			$d->pk_values = [$BillId];
			$d->build_query();
			$aQuerys[] = $d;

			$d = new deleteq();
			$d->table = 't_bill';
			$d->pks = ['BillId'];
			$d->pk_values = [$BillId];
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
}



// function getNextMRNumber()
// {

// 	try {
// 		$dbh = new Db();

// 		$query3 = "SELECT ifnull(max(MRNo),0) + 1 as NextMRNo FROM t_payment;";
// 		$result3 = $dbh->query($query3);
// 		$MRNo = $result3[0]['NextMRNo'];
		
// 		$returnData = [
// 			"success" => 1,
// 			"status" => 200,
// 			"message" => "",
// 			"MRNo" => $MRNo
// 		];
// 	} catch (PDOException $e) {
// 		$returnData = msg(0, 500, $e->getMessage());
// 	}

// 	return $returnData;
// }
