<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
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

	case "amountAddEdit":
		$returnData = amountAddEdit($data);
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
	$CustomerFilter = isset($data->CustomerFilter) ? trim($data->CustomerFilter) : '';
	$AssignedStaffFilter = isset($data->AssignedStaffFilter) ? trim($data->AssignedStaffFilter) : '';
	
	$BillStatusFilter = isset($data->BillStatusFilter) ? trim($data->BillStatusFilter) : '';
	$PaymentStatusFilter = isset($data->PaymentStatusFilter) ? trim($data->PaymentStatusFilter) : '';

	// $BusinessLineFilter = isset($data->BusinessLineFilter) ? trim($data->BusinessLineFilter) : '';

	try {
		$dbh = new Db();
		
		$whereConditions = "(STR_TO_DATE(LPAD(a.TransactionDate, 8, '0'), '%d%m%Y') between '$StartDate' and '$EndDate')";
		
		if (!empty($CustomerFilter)) {
			if ($CustomerFilter == -1) {
				$whereConditions .= " AND c.CustomerId is null ";
			}else{
				$whereConditions .= " AND c.CustomerId = $CustomerFilter ";
			}
		}
		if (!empty($AssignedStaffFilter)) {
			if ($AssignedStaffFilter == -1) {
				$whereConditions .= " AND a.CustomerUserId is null ";
			}else{
				$whereConditions .= " AND a.CustomerUserId = $AssignedStaffFilter ";
			}
		}
		

		if (!empty($BillStatusFilter)) { // Assuming 2 is for 'Not Billed' and 1 is for 'Billed', and '' is for 'All'
			if($BillStatusFilter == 2){
				$BillStatusFilter = 0;
			}
			$whereConditions .= " AND a.IsBilled = $BillStatusFilter ";
		}
		if (!empty($PaymentStatusFilter)) { // Assuming 2 is for 'Not Paid' and 1 is for 'Paid', and '' is for 'All'
			if($PaymentStatusFilter == 2){
				$PaymentStatusFilter = 0;
			}
			$whereConditions .= " AND a.IsPaid = $PaymentStatusFilter ";
		}
		
	 	$query = "SELECT a.*, 
 		DATE_FORMAT(STR_TO_DATE(CONCAT(RIGHT(a.AccountingPeriod,4), '-',LPAD(LEFT(a.AccountingPeriod, LENGTH(a.AccountingPeriod)-4),2,'0'), '-01'),'%Y-%m-%d'),'%M-%Y') as AccountingPeriod,
		DATE_FORMAT(STR_TO_DATE(LPAD(a.TransactionDate, 8, '0'), '%d%m%Y'), '%d/%m/%Y') as TransactionDate, 
		b.UserName as CustomerUserName,c.CustomerId as CustomerId,concat(a.AccountCode, ' - ', c.CustomerName) as CustomerName,
		case when a.IsBilled=1 then 'Yes' else 'No' end as IsBilledText,
		case when a.IsPaid=1 then 'Yes' else 'No' end as IsPaidText
		FROM t_invoiceitems a
		left join t_users b on a.CustomerUserId=b.UserId
		left join t_customer c on a.AccountCode=c.CustomerCode
		
	    where $whereConditions
		ORDER BY STR_TO_DATE(LPAD(a.TransactionDate, 8, '0'), '%d%m%Y') DESC;";

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
		$CustomerId = isset($data->rowData->CustomerId) ? intval($data->rowData->CustomerId) : 0;

		try {

			$dbh = new Db();
			$aQuerys = array();

			$columns = ['CustomerUserId'];
			$values = [$CustomerUserId];

			if ($CustomerId > 0) {
				$customer = $dbh->query("SELECT CustomerCode, CustomerName FROM t_customer WHERE CustomerId = $CustomerId;");
				if (!empty($customer)) {
					$columns[] = 'AccountCode';
					$values[] = $customer[0]['CustomerCode'];
					$columns[] = 'CustomerName';
					$values[] = $customer[0]['CustomerName'];
				}
			}

			$u = new updateq();
			$u->table = 't_invoiceitems';
			$u->columns = $columns;
			$u->values = $values;
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

function amountAddEdit($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} else {

		$lan = trim($data->lan);
		$UserId = trim($data->UserId);
		$InvoiceItemId = $data->rowData->InvoiceItemId;
		$BaseAmountWithoutVat = $data->rowData->BaseAmountWithoutVat?$data->rowData->BaseAmountWithoutVat:null;
		$VatAmount = $data->rowData->VatAmount?$data->rowData->VatAmount:null;

		try {

			$aQuerys = array();

			$u = new updateq();
			$u->table = 't_invoiceitems';
			$u->columns = ['BaseAmountWithoutVat','VatAmount'];
			$u->values = [$BaseAmountWithoutVat, $VatAmount];
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