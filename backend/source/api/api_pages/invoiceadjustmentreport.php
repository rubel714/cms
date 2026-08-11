<?php
$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{
	$StartDate = isset($data->StartDate) ? trim($data->StartDate) : '';
	$EndDate = isset($data->EndDate) ? trim($data->EndDate) : '';
	$CustomerFilter = isset($data->CustomerFilter) ? trim($data->CustomerFilter) : '';

	try {
		$dbh = new Db();

		/**Only adjusted invoices need an approval decision */
		$whereConditions = "(a.AdjFlag ='Approved')";

		if (!empty($StartDate) && !empty($EndDate)) {
			$whereConditions .= " AND (STR_TO_DATE(LPAD(a.TransactionDate, 8, '0'), '%d%m%Y') between '$StartDate' and '$EndDate') ";
		}

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
		DATE_FORMAT(a.AdjDateTime, '%d/%m/%Y') as AdjDateTimeText,
		DATE_FORMAT(a.ApproveDateTime, '%d/%m/%Y') as ApproveDateTimeText,
		a.AdjDebitCredit

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
