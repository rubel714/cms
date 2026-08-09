<?php
$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;

	case "approveData":
		$returnData = approveData($data);
		break;

	case "rejectData":
		$returnData = rejectData($data);
		break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{
	$CustomerFilter = isset($data->CustomerFilter) ? trim($data->CustomerFilter) : '';

	try {
		$dbh = new Db();

		/**Only adjusted invoices need an approval decision */
		$whereConditions = "(a.AdjFlag is not null and a.AdjFlag <> '')";

		if (!empty($CustomerFilter)) {
			if ($CustomerFilter == -1) {
				$whereConditions .= " AND c.CustomerId is null ";
			} else {
				$whereConditions .= " AND c.CustomerId = " . intval($CustomerFilter) . " ";
			}
		}

		$query = "SELECT a.*,
		DATE_FORMAT(STR_TO_DATE(CONCAT(RIGHT(a.AccountingPeriod,4), '-',LPAD(LEFT(a.AccountingPeriod, LENGTH(a.AccountingPeriod)-4),2,'0'), '-01'),'%Y-%m-%d'),'%M-%Y') as AccountingPeriod,
		DATE_FORMAT(STR_TO_DATE(LPAD(a.TransactionDate, 8, '0'), '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
		b.UserName as CustomerUserName, c.CustomerId as CustomerId, concat(a.AccountCode, ' - ', c.CustomerName) as CustomerName,
		d.UserName as AdjUserName,
		e.UserName as ApproveUserName,
		DATE_FORMAT(a.AdjDateTime, '%d/%m/%Y %H:%i') as AdjDateTimeText,
		DATE_FORMAT(a.ApproveDateTime, '%d/%m/%Y %H:%i') as ApproveDateTimeText,

		case when a.AdjFlag = 'Approved' then 1 else 0 end as IsApproved,
		
		case when a.AdjFlag = 'Adjust' then 'Pending'
		when a.AdjFlag = 'Reject' then 'Reject'
		when a.AdjFlag = 'Approved' then 'Approved' else '' end as IsApprovedText

		FROM t_invoiceitems a
		left join t_users b on a.CustomerUserId=b.UserId
		left join t_customer c on a.AccountCode=c.CustomerCode
		left join t_users d on a.AdjUserId=d.UserId
		left join t_users e on a.ApproveUserId=e.UserId

		where $whereConditions
		ORDER BY (a.AdjFlag = 'Adjust') DESC, a.AdjDateTime DESC;";

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

/**Approve/Reject is a final decision, so only a pending invoice can be changed */
function isPendingApproval($InvoiceItemId)
{
	$dbh = new Db();
	$item = $dbh->query("SELECT AdjFlag FROM t_invoiceitems WHERE InvoiceItemId = " . intval($InvoiceItemId) . ";");

	return !empty($item) && $item[0]['AdjFlag'] === 'Adjust';
}

function approveData($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$InvoiceItemId = $data->rowData->InvoiceItemId;

	try {

		if (!isPendingApproval($InvoiceItemId)) {
			return msg(0, 400, 'This invoice is already approved or rejected!');
		}

		$dbh = new Db();
		$item = $dbh->query("SELECT AdjBaseAmount,AdjBaseAmountWithoutVat,AdjTransactionAmount,AdjVatAmount,
		(ifnull(OriginalBaseAmount, 0)-ifnull(AdjBaseAmount, 0)) as OriginalAndAdjBaseAmountDiff, 
		(ifnull(OriginalTransactionAmount, 0)-ifnull(AdjTransactionAmount, 0)) as OriginalAndAdjTransactionAmountDiff
		FROM t_invoiceitems WHERE InvoiceItemId = " . intval($InvoiceItemId) . ";");
	
		$AdjBaseAmount = $item[0]['AdjBaseAmount'];
		$AdjBaseAmountWithoutVat = $item[0]['AdjBaseAmountWithoutVat'];
		$AdjTransactionAmount = $item[0]['AdjTransactionAmount'];
		$AdjVatAmount = $item[0]['AdjVatAmount'];
		$OriginalAndAdjBaseAmountDiff = $item[0]['OriginalAndAdjBaseAmountDiff'];
		$OriginalAndAdjTransactionAmountDiff = $item[0]['OriginalAndAdjTransactionAmountDiff'];
		$AdjDebitCredit = $OriginalAndAdjBaseAmountDiff<0 ? 'Debit' : 'Credit';

		$u = new updateq();
		$u->table = 't_invoiceitems';
		$u->columns = ['AdjBaseAmount','AdjBaseAmountWithoutVat','AdjTransactionAmount','AdjVatAmount','AdjFlag', 'ApproveUserId', 'ApproveDateTime','OriginalAndAdjBaseAmountDiff','OriginalAndAdjTransactionAmountDiff','AdjDebitCredit'];
		$u->values = [$AdjBaseAmount, $AdjBaseAmountWithoutVat, $AdjTransactionAmount, $AdjVatAmount, 'Approved', $UserId, date('Y-m-d H:i:s'), $OriginalAndAdjBaseAmountDiff, $OriginalAndAdjTransactionAmountDiff, $AdjDebitCredit];
		$u->pks = ['InvoiceItemId'];
		$u->pk_values = [$InvoiceItemId];
		$u->build_query();
		$aQuerys = array($u);

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

function rejectData($data)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$InvoiceItemId = $data->rowData->InvoiceItemId;

	try {

		if (!isPendingApproval($InvoiceItemId)) {
			return msg(0, 400, 'This invoice is already approved or rejected!');
		}

		$u = new updateq();
		$u->table = 't_invoiceitems';
		$u->columns = ['AdjFlag', 'ApproveUserId', 'ApproveDateTime','RejectUserId','RejectDateTime'];
		$u->values = ['Reject', null, null, $UserId, date('Y-m-d H:i:s')];
		$u->pks = ['InvoiceItemId'];
		$u->pk_values = [$InvoiceItemId];
		$u->build_query();
		$aQuerys = array($u);

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

 