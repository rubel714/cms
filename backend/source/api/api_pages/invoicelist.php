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

	// case "deleteData":
	// 	$returnData = deleteData($data);
	// break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{
	// $InvoiceId = trim($data->InvoiceId); 
	// $LastInvoiceLimit = trim($data->LastInvoiceLimit); 
	$StartDate = trim($data->StartDate);
	$EndDate = trim($data->EndDate) . " 23-59-59";

	try {
		$dbh = new Db();
	 	$query = "SELECT a.*,DATE_FORMAT(STR_TO_DATE(a.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate, 
		b.UserName as CustomerUserName,concat(a.AccountCode, ' - ', c.CustomerName) as CustomerName
		FROM t_invoiceitems a
		left join t_users b on a.CustomerUserId=b.UserId
		left join t_customer c on a.AccountCode=c.CustomerCode
		
	   where (STR_TO_DATE(a.TransactionDate, '%d%m%Y') between '$StartDate' and '$EndDate')
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



function dataAddEdit($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} else {

		$lan = trim($data->lan);
		$UserId = trim($data->UserId);
		$InvoiceItemId = $data->rowData->InvoiceItemId;
		$CustomerUserId = $data->rowData->CustomerUserId?$data->rowData->CustomerUserId:null;
		$BaseAmountWithoutVat = $data->rowData->BaseAmountWithoutVat?$data->rowData->BaseAmountWithoutVat:null;
		$VatAmount = $data->rowData->VatAmount?$data->rowData->VatAmount:null;

		try {

			$dbh = new Db();
			$aQuerys = array();
 
			$u = new updateq();
			$u->table = 't_invoiceitems';
			$u->columns = ['CustomerUserId','BaseAmountWithoutVat','VatAmount'];
			$u->values = [$CustomerUserId, $BaseAmountWithoutVat, $VatAmount];
			$u->pks = ['InvoiceItemId'];
			$u->pk_values = [$InvoiceItemId];
			$u->build_query();
			$aQuerys = array($u);
		
			$res = exec_query($aQuerys, $UserId, $lan);
			$success=($res['msgType']=='success')?1:0;
			$status=($res['msgType']=='success')?200:500;

			$returnData = [
			    "success" => $success ,
				"status" => $status,
				"UserId"=> $UserId,
				"message" => $res['msg']
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}