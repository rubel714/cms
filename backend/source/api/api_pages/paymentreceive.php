<?php

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
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
		if($data->InvoiceStartDate != "" && $data->InvoiceEndDate != "") {

		$StartDate = trim($data->InvoiceStartDate);
		$EndDate = trim($data->InvoiceEndDate) . " 23:59:59";

		$DateFilter = " AND (STR_TO_DATE(a.TransactionDate, '%d%m%Y') between '$StartDate' and '$EndDate') ";
		}
	}

	try {
		$dbh = new Db();
		$query = "SELECT a.InvoiceItemId,a.AccountingPeriod, a.AccountCode, a.Description,
			DATE_FORMAT(STR_TO_DATE(a.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
			a.TransactionReference,	a.TransactionAmount, a.ExchangeRate, a.BaseAmount

			FROM t_invoiceitems a
			inner join t_customer c on a.AccountCode=c.CustomerCode
			left join t_paymentitems p on a.InvoiceItemId=p.InvoiceItemId
			where c.CustomerId = $CustomerId
			and a.IsPaid = 0
			and p.PaymentItemId is null
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

	try {
		$dbh = new Db();
		$aQuerys = array();

		foreach ($invoices as $obj) {

		
			$InvoiceItemId = isset($obj->InvoiceItemId) ? $obj->InvoiceItemId : null;
			// if ($InvoiceItemId == null) {
			if (!$InvoiceItemId) {
				continue;
			}


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


function getDataList($data)
{

	try {
		$dbh = new Db();

		$query = "SELECT a.PaymentId AS id,  DATE_FORMAT(a.PaymentDate, '%Y-%m-%d') as PaymentDate,
		a.CustomerId,b.CustomerName, a.CustomerGroupId,c.CustomerGroupName,a.BankId,d.BankName,
		a.Remarks,a.StatusId,a.MRNo,a.RefNo,a.ChequeNumber,a.ChequeDate,a.BankBranchName
		,a.TotalBaseAmount,a.TotalTransactionAmount,a.PaymentReceiveAmount,a.RebateAmount,a.AitDeduction

		FROM t_payment a
		LEFT JOIN t_customer b ON a.CustomerId = b.CustomerId
		LEFT JOIN t_customergroup c ON a.CustomerGroupId = c.CustomerGroupId
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


function getDataSingle($data)
{
	$PaymentId = trim($data->id);

	try {
		$dbh = new Db();

		/**Master Data */
		// $query = "SELECT PaymentId AS id, `PaymentDate`, `CustomerId`, `CustomerGroupId`, `BankId`, 
		//  `Remarks`, `UserId`
		// FROM t_payment
		// where PaymentId=$PaymentId;";

		$resultdataMaster = []; // $dbh->query($query);


		/**Items Data */
		$query = "SELECT a.PaymentItemId as autoId, a.`PaymentItemId`, a.`PaymentId`, a.`InvoiceItemId`,b.AccountingPeriod, 
		b.AccountCode, b.Description, 
		DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate, b.TransactionReference, 
		b.TransactionAmount, b.ExchangeRate,b.BaseAmount
		FROM t_paymentitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.PaymentId=$PaymentId
		order by a.PaymentItemId ASC;";
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
		$Remarks = $data->rowData->Remarks ? $data->rowData->Remarks : null;
		$StatusId = $data->rowData->StatusId ? $data->rowData->StatusId : 1;
		// $TotalBaseAmount = $data->rowData->TotalBaseAmount ? $data->rowData->TotalBaseAmount : 0;
		// $TotalTransactionAmount = $data->rowData->TotalTransactionAmount ? $data->rowData->TotalTransactionAmount : 0;
		$PaymentReceiveAmount = $data->rowData->PaymentReceiveAmount ? $data->rowData->PaymentReceiveAmount : 0;
		$RebateAmount = $data->rowData->RebateAmount ? $data->rowData->RebateAmount : 0;
		$AitDeduction = $data->rowData->AitDeduction ? $data->rowData->AitDeduction : 0;


		// $items = isset($data->items) ? $data->items : [];

		// $query = "SELECT count(a.PaymentId) DraftCount
		// FROM t_payment a
		// where a.CustomerId=$CustomerId
		// and a.StatusId=1;";
		// $resultdatalist = $dbh->query($query);
		// if ($resultdatalist[0]['DraftCount'] >= 1 && $PaymentId == "") {
		// 	return $returnData = msg(0, 500, 'There is already a draft payment for this customer. Please complete or delete the existing draft payment before creating a new one.');
		// }



		try {

			$dbh = new Db();
			$aQuerys = array();

			if ($PaymentId == "") {

				// $query3 = "SELECT ifnull(max(MRNo),0) + 1 as NextMRNo FROM t_payment;";
				// $result3 = $dbh->query($query3);
				// $MRNo = $result3[0]['NextMRNo'];

				$NextMR = getNextMRNumber();
				$MRNo = $NextMR['MRNo'];

				$q = new insertq();
				$q->table = 't_payment';
				$q->columns = ['MRNo','RefNo','PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId','BankBranchName', 'ChequeNumber', 'ChequeDate',  'Remarks', 'UserId', 'StatusId','PaymentReceiveAmount','RebateAmount','AitDeduction'];
				$q->values = [$MRNo, $RefNo, $PaymentDate, $CustomerId, $CustomerGroupId, $BankId, $BankBranchName, $ChequeNumber, $ChequeDate, $Remarks, $UserId, $StatusId, $PaymentReceiveAmount, $RebateAmount, $AitDeduction];
				$q->pks = ['PaymentId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;
			} else {
				// $StatusId = 5; //Completed
				$u = new updateq();
				$u->table = 't_payment';
				$u->columns = ['RefNo','PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId','BankBranchName', 'ChequeNumber', 'ChequeDate', 'Remarks', 'StatusId','PaymentReceiveAmount','RebateAmount','AitDeduction'];
				$u->values = [$RefNo, $PaymentDate, $CustomerId, $CustomerGroupId, $BankId, $BankBranchName, $ChequeNumber, $ChequeDate, $Remarks, $StatusId, $PaymentReceiveAmount, $RebateAmount, $AitDeduction];
				$u->pks = ['PaymentId'];
				$u->pk_values = [$PaymentId];
				$u->build_query();
				$aQuerys[] = $u;

			}


			if($StatusId == 5){
				
				if($RebateAmount > 0){
					$PaymentExtendTypeId = 1;
					$RptPreFix = "REBATE";
					$RptNumber = getNextPaymentExtendNumber($PaymentExtendTypeId, $RptPreFix)['NextRptNumber'];
					$Amount = $RebateAmount;
					$q = new insertq();
					$q->table = 't_paymentextend';
					$q->columns = ['PaymentId','PaymentExtendTypeId','RptNumber', 'Amount'];
					$q->values = [$PaymentId, $PaymentExtendTypeId, $RptNumber, $Amount];
					$q->pks = ['PaymentExtendId'];
					$q->bUseInsetId = true;
					$q->build_query();
					$aQuerys[] = $q;
				}

				if($AitDeduction > 0){
					$PaymentExtendTypeId = 2;
					$RptPreFix = "AIT";
					$RptNumber = getNextPaymentExtendNumber($PaymentExtendTypeId, $RptPreFix)['NextRptNumber'];
					$Amount = $AitDeduction;
					$q = new insertq();
					$q->table = 't_paymentextend';
					$q->columns = ['PaymentId','PaymentExtendTypeId','RptNumber', 'Amount'];
					$q->values = [$PaymentId, $PaymentExtendTypeId, $RptNumber, $Amount];
					$q->pks = ['PaymentExtendId'];
					$q->bUseInsetId = true;
					$q->build_query();
					$aQuerys[] = $q;
				}
			}


			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;

			//when post then set billed flag in invoice
			if ($success == 1 && $PaymentId != "" && $StatusId == 5) {
				//when payment is completed then mark the related invoice items as paid
				$query1 = "update t_invoiceitems set IsPaid = 1 
					where InvoiceItemId in (select InvoiceItemId from t_paymentitems where PaymentId = $PaymentId);";
					$dbh->query($query1);


					// 'TotalBaseAmount','TotalTransactionAmount',

					//when payment completed then calculate and update total base amount and total transaction amount in payment table
				$query2 = "UPDATE t_payment a 
							INNER JOIN (SELECT m.PaymentId, SUM(n.BaseAmount) SumBaseAmount, SUM(n.TransactionAmount) SumTransactionAmount 
								FROM t_paymentitems m
								INNER JOIN t_invoiceitems n ON m.InvoiceItemId=n.InvoiceItemId
								WHERE m.PaymentId = $PaymentId
								GROUP BY m.PaymentId) b ON a.PaymentId=b.PaymentId
							SET a.TotalBaseAmount = b.SumBaseAmount, a.TotalTransactionAmount = b.SumTransactionAmount
							WHERE a.PaymentId = $PaymentId;";
					$dbh->query($query2);
			}


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

		$PaymentId = $data->rowData->id;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		try {

			$d = new deleteq();
			$d->table = 't_paymentitems';
			$d->pks = ['PaymentId'];
			$d->pk_values = [$PaymentId];
			$d->build_query();
			$aQuerys[] = $d;

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
}



function getNextMRNumber()
{

	try {
		$dbh = new Db();

		$query3 = "SELECT CONCAT('MR-',LPAD((IFNULL(MAX(SUBSTRING_INDEX(MRNo, '-', -1)),0) + 1),6,'0')) AS NextMRNo FROM t_payment;";
		$result3 = $dbh->query($query3);
		$MRNo = $result3[0]['NextMRNo'];
		
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



function getNextPaymentExtendNumber($PaymentExtendTypeId, $RptPreFix)
{

	try {
		$dbh = new Db();

		$query3 = "SELECT CONCAT('$RptPreFix-',LPAD((IFNULL(MAX(SUBSTRING_INDEX(RptNumber, '-', -1)),0) + 1),6,'0')) AS NextRptNumber
		FROM t_paymentextend where PaymentExtendTypeId = $PaymentExtendTypeId;";
		$result3 = $dbh->query($query3);
		$NextRptNumber = $result3[0]['NextRptNumber'];
		
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"NextRptNumber" => $NextRptNumber
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}