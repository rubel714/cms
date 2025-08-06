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
	case "approveAll":
		$returnData = approveAll($data);
		break;
		// case "deleteData":
		// 	$returnData = deleteData($data);
		// 	break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{

	//$ClientId = trim($data->ClientId);
	$UserId = trim($data->UserId);
	//$BranchId = trim($data->BranchId); 
	$Search =  isset($data->Search) ? $data->Search : 0; //0=All, Y=LM approved, N=LM not approved

	try {
		$dbh = new Db();

		$sWhere = "";
		if ($Search === "Y") {
			$sWhere = " AND a.IsLinemanFeedback='Y' ";
		} else if ($Search === "N") {
			$sWhere = " AND a.IsLinemanFeedback='N' ";
		}

		$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
	a.TransactionId AS id, ifnull(PunchLocation,'') AS PunchLocation, 
	ifnull(Longitude,'') AS Longitude, ifnull(Latitude,'') AS Latitude,
	ifnull(a.DropDownListIDForPurpose,'') AS DropDownListIDForPurpose,ifnull(b.DisplayName,'') AS Purpose,
	ifnull(a.CustomerId,'') AS CustomerID,
	 (case when a.CustomerId=38 then concat(c.CustomerName,'-',a.DummyCustomerDesc) else c.CustomerName end) CustomerName,

	ifnull(a.ContactPersonName,'') AS ContactPersonName,ifnull(a.ContactPersonDesignation,'') AS ContactPersonDesignation,
	ifnull(a.ContactPersonMobileNumber,'') AS ContactPersonMobileNumber,
	ifnull(a.DropDownListIDForTransportation,'') AS DropDownListIDForTransportation,
	ifnull(d.DisplayName,'') AS Transportation,
	ifnull(a.ConveyanceAmount,'') AS ConveyanceAmount,
	 ifnull(a.RefreshmentAmount,'') AS RefreshmentAmount,
	 ifnull(a.DinnerBillAmount,'') AS DinnerBillAmount,

	case when a.ApprovedRefreshmentAmount is null then a.RefreshmentAmount else a.ApprovedRefreshmentAmount end AS ApprovedRefreshmentAmount,
	case when a.ApprovedConveyanceAmount is null then a.ConveyanceAmount else a.ApprovedConveyanceAmount end AS ApprovedConveyanceAmount,
	case when a.ApprovedDinnerBillAmount is null then a.DinnerBillAmount else a.ApprovedDinnerBillAmount end AS ApprovedDinnerBillAmount,

	DATE_FORMAT(a.TransactionDate, '%d-%b-%Y %h:%i:%s %p') AS VisitDate,
	a.IsVisitorFeedback,a.IsLinemanFeedback,
	ifnull(a.PublicTransportDesc,'') AS PublicTransportDesc, ifnull(a.DummyCustomerDesc,'') AS DummyCustomerDesc,
	ifnull(e.MachineName,'') AS MachineName,ifnull(f.MachineModelName,'') AS MachineModelName,
	ifnull(a.SelfDiscussion,'') AS SelfDiscussion,
	case when a.SelfFollowUpDate is null then '' else DATE_FORMAT(a.SelfFollowUpDate, '%d-%b-%Y') end AS SelfFollowUpDate,
	ifnull(a.LMAdvice,'') AS LMAdvice, 
	case when a.LMFollowUpDate is null then '' else DATE_FORMAT(a.LMFollowUpDate, '%d-%b-%Y') end AS LMFollowUpDate,
	ifnull(a.MachineSerial,'') AS MachineSerial,ifnull(a.MachineComplain,'') AS MachineComplain,
	g.UserName AS VisitorName

	FROM t_transaction a
	inner join t_users g on a.UserId=g.UserId
	left join t_dropdownlist b on a.DropDownListIDForPurpose=b.DropDownListID
	left join t_customer c on a.CustomerId=c.CustomerId
	left join t_dropdownlist d on a.DropDownListIDForTransportation=d.DropDownListID
	left join t_machine e on a.MachineId=e.MachineId
	left join t_machinemodel f on a.MachineModelId=f.MachineModelId
	where (g.LinemanUserId=$UserId OR $UserId=0)
	and a.TransactionTypeId=1
	and a.IsVisitorFeedback='Y'
	$sWhere
	and (a.ConveyanceAmount>0 OR a.RefreshmentAmount>0 OR a.ApprovedRefreshmentAmount>0 OR a.ApprovedConveyanceAmount>0 OR a.DinnerBillAmount>0 OR a.ApprovedDinnerBillAmount>0)
	ORDER BY a.TransactionDate DESC;";

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
		// $ClientId = trim($data->ClientId);
		//$BranchId = trim($data->BranchId); 

		$TransactionId = $data->rowData->id;
		$ApprovedRefreshmentAmount = $data->rowData->ApprovedRefreshmentAmount;
		$ApprovedConveyanceAmount = $data->rowData->ApprovedConveyanceAmount;
		$ApprovedDinnerBillAmount = $data->rowData->ApprovedDinnerBillAmount;

		$LMAdvice = $data->rowData->LMAdvice ? $data->rowData->LMAdvice : null;
		$LMFollowUpDate = $data->rowData->LMFollowUpDate ? $data->rowData->LMFollowUpDate : null;
		$IsLinemanFeedback = "Y";

		try {

			$dbh = new Db();
			$aQuerys = array();

			// if ($MachineId == "") {
			// 	$q = new insertq();
			// 	$q->table = 't_machine';
			// 	$q->columns = ['MachineName'];
			// 	$q->values = [$MachineName];
			// 	$q->pks = ['MachineId'];
			// 	$q->bUseInsetId = false;
			// 	$q->build_query();
			// 	$aQuerys = array($q);
			// } else {
			$u = new updateq();
			$u->table = 't_transaction';
			$u->columns = ['ApprovedRefreshmentAmount', 'ApprovedConveyanceAmount', 'ApprovedDinnerBillAmount', 'LMAdvice', 'LMFollowUpDate', 'IsLinemanFeedback'];
			$u->values = [$ApprovedRefreshmentAmount, $ApprovedConveyanceAmount, $ApprovedDinnerBillAmount, $LMAdvice, $LMFollowUpDate, $IsLinemanFeedback];
			$u->pks = ['TransactionId'];
			$u->pk_values = [$TransactionId];
			$u->build_query();
			$aQuerys = array($u);
			// }

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


function approveAll($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} else {


		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		$IsLinemanFeedback = "Y";

		try {

			$dbh = new Db();
			$aQuerys = array();

			$query = "SELECT a.TransactionId,
			ifnull(a.ConveyanceAmount,'') AS ConveyanceAmount,
			ifnull(a.RefreshmentAmount,'') AS RefreshmentAmount,
			ifnull(a.DinnerBillAmount,'') AS DinnerBillAmount
		
			FROM t_transaction a
			inner join t_users g on a.UserId=g.UserId
			where (g.LinemanUserId=$UserId OR $UserId=0)
			and a.TransactionTypeId=1
			and a.IsVisitorFeedback='Y'
			AND a.IsLinemanFeedback='N'
			and (a.ConveyanceAmount>0 OR a.RefreshmentAmount>0 OR a.ApprovedRefreshmentAmount>0 OR a.ApprovedConveyanceAmount>0 OR a.DinnerBillAmount>0 OR a.ApprovedDinnerBillAmount>0);";

			$resultdata = $dbh->query($query);

			foreach ($resultdata as $row) {
				$TransactionId = $row["TransactionId"];
				$ApprovedRefreshmentAmount = $row["RefreshmentAmount"];
				$ApprovedConveyanceAmount = $row["ConveyanceAmount"];
				$ApprovedDinnerBillAmount = $row["DinnerBillAmount"];

				$u = new updateq();
				$u->table = 't_transaction';
				$u->columns = ['ApprovedRefreshmentAmount', 'ApprovedConveyanceAmount', 'ApprovedDinnerBillAmount', 'IsLinemanFeedback'];
				$u->values = [$ApprovedRefreshmentAmount, $ApprovedConveyanceAmount, $ApprovedDinnerBillAmount, $IsLinemanFeedback];
				$u->pks = ['TransactionId'];
				$u->pk_values = [$TransactionId];
				$u->build_query();
				$aQuerys[] = $u;
			}


			$res = exec_query($aQuerys, ($UserId == 0 ? 1 : $UserId), $lan);
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


// function deleteData($data)
// {

// 	if ($_SERVER["REQUEST_METHOD"] != "POST") {
// 		return $returnData = msg(0, 404, 'Page Not Found!');
// 	}
// 	// CHECKING EMPTY FIELDS
// 	elseif (!isset($data->rowData->id)) {
// 		$fields = ['fields' => ['id']];
// 		return $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
// 	} else {

// 		$MachineId = $data->rowData->id;
// 		$lan = trim($data->lan);
// 		$UserId = trim($data->UserId);

// 		try {

// 			$dbh = new Db();

// 			$d = new deleteq();
// 			$d->table = 't_machine';
// 			$d->pks = ['MachineId'];
// 			$d->pk_values = [$MachineId];
// 			$d->build_query();
// 			$aQuerys = array($d);

// 			$res = exec_query($aQuerys, $UserId, $lan);
// 			$success = ($res['msgType'] == 'success') ? 1 : 0;
// 			$status = ($res['msgType'] == 'success') ? 200 : 500;

// 			$returnData = [
// 				"success" => $success,
// 				"status" => $status,
// 				"UserId" => $UserId,
// 				"message" => $res['msg']
// 			];
// 		} catch (PDOException $e) {
// 			$returnData = msg(0, 500, $e->getMessage());
// 		}

// 		return $returnData;
// 	}
// }
