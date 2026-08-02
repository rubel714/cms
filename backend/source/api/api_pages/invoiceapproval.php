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

	case "unapproveData":
		$returnData = unapproveData($data);
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
		a.OriginalBaseAmountWithoutVat, a.OriginalVatAmount, a.OriginalBaseAmount, a.OriginalTransactionAmount, a.ExchangeRate, a.AdjFlag,
		d.UserName as AdjUserName,
		e.UserName as ApproveUserName,
		DATE_FORMAT(a.AdjDateTime, '%d/%m/%Y %H:%i') as AdjDateTimeText,
		DATE_FORMAT(a.ApproveDateTime, '%d/%m/%Y %H:%i') as ApproveDateTimeText,
		case when a.ApproveUserId is null then 0 else 1 end as IsApproved,
		case when a.ApproveUserId is null then 'Pending' else 'Approved' end as IsApprovedText,
		case when a.IsBilled=1 then 'Yes' else 'No' end as IsBilledText,
		case when a.IsPaid=1 then 'Yes' else 'No' end as IsPaidText
		FROM t_invoiceitems a
		left join t_users b on a.CustomerUserId=b.UserId
		left join t_customer c on a.AccountCode=c.CustomerCode
		left join t_users d on a.AdjUserId=d.UserId
		left join t_users e on a.ApproveUserId=e.UserId

		where $whereConditions
		ORDER BY (a.ApproveUserId is not null) ASC, a.AdjDateTime DESC;";

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

function approveData($data)
{
	return saveApproval($data, true);
}

function unapproveData($data)
{
	return saveApproval($data, false);
}

function saveApproval($data, $isApprove)
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$InvoiceItemId = $data->rowData->InvoiceItemId;

	try {

		$u = new updateq();
		$u->table = 't_invoiceitems';
		$u->columns = ['ApproveUserId', 'ApproveDateTime'];
		$u->values = $isApprove ? [$UserId, date('Y-m-d H:i:s')] : [null, null];
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
