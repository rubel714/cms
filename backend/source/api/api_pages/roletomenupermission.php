<?php
include("posttransaction.php");

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;

	case "assignData":
		$returnData = assignData($data);
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

	$RoleId = trim($data->RoleId);
	$ClientId = trim($data->ClientId);
	$BranchId = trim($data->BranchId);
	
	try {
		$dbh = new Db();


		$query = "SELECT a.MenuId,IF(MenuLevel='menu_level_2',CONCAT(' -', a.MenuTitle),
		IF(MenuLevel='menu_level_3',CONCAT(' --', a.MenuTitle),a.MenuTitle)) menuname,
					CASE WHEN b.MenuId IS NULL THEN 0 ELSE b.PermissionType END PermissionType, RoleMenuId,a.MenuType
			   FROM `t_menu` a
			   LEFT JOIN t_role_menu_map b ON b.`MenuId` = a.`MenuId` AND b.ClientId = $ClientId AND b.BranchId = $BranchId and b.RoleId = $RoleId
			   ORDER BY a.MenuType desc, SortOrder asc;";

	/* 	echo $query ;
		exit; 
		*/

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



function assignData($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}{

		$RoleMenuId = $data->rowData->RoleMenuId;
		$MenuId = $data->rowData->MenuId;
		$PermissionType = $data->rowData->PermissionType;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);
		$RoleId = trim($data->RoleId);
		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);


		try {

			$dbh = new Db();
			$aQuerys = array();

			if($PermissionType === 0){
				$q = new insertq();
				$q->table = 't_role_menu_map';
				$q->columns = ['ClientId','BranchId','RoleId', 'MenuId','PermissionType'];
				$q->values = [$ClientId,$BranchId,$RoleId, $MenuId,1];
				$q->pks = ['RoleMenuId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys[] = $q;

			}else if($PermissionType === 1){
				$u = new updateq();
				$u->table = 't_role_menu_map';
				$u->columns = ['PermissionType'];
				$u->values = [2];
				$u->pks = ['RoleMenuId'];
				$u->pk_values = [$RoleMenuId];
				$u->build_query();
				$aQuerys[] = $u;
			}else if($PermissionType === 2){
				$d = new deleteq();
				$d->table = 't_role_menu_map';
				$d->pks = ['RoleMenuId'];
				$d->pk_values = [$RoleMenuId];
				$d->build_query();
				$aQuerys[] = $d;
			}
			
// echo "<pre>";
// print_r($aQuerys);
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



// function deleteData($data)
// {

// 	if ($_SERVER["REQUEST_METHOD"] != "POST") {
// 		return $returnData = msg(0, 404, 'Page Not Found!');
// 	}
// 	// CHECKING EMPTY FIELDS
// 	elseif (!isset($data->rowData->RoleMenuId)) {
// 		$fields = ['fields' => ['RoleMenuId']];
// 		return $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
// 	} else {

// 		$RoleMenuId = $data->rowData->RoleMenuId;
// 		$lan = trim($data->lan);
// 		$UserId = trim($data->UserId);


// 		try {

// 			$dbh = new Db();

// 			$aQuerys = array();


// 			$d = new deleteq();
// 			$d->table = 't_role_menu_map';
// 			$d->pks = ['RoleMenuId'];
// 			$d->pk_values = [$RoleMenuId];
// 			$d->build_query();
// 			$aQuerys[] = $d;

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
