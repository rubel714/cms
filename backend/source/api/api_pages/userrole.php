<?php

$task = '';
if(isset($data->action)) {
	$task = trim($data->action);
}

switch($task){
	
	case "getDataList":
		$returnData = getDataList($data);
	break;

	case "dataAddEdit":
		$returnData = dataAddEdit($data);
	break;
	
	case "deleteData":
		$returnData = deleteData($data);
	break;

	default :
		echo "{failure:true}";
		break;
}

function getDataList($data){

	
	$ClientId = trim($data->ClientId); 
	//$BranchId = trim($data->BranchId); 

	try{
		$dbh = new Db();
		$query = "SELECT RoleId AS id, RoleName, DefaultRedirect
		FROM t_roles
		ORDER BY `RoleId` ASC;";		
		
		$resultdata = $dbh->query($query);
		
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];

	}catch(PDOException $e){
		$returnData = msg(0,500,$e->getMessage());
	}
	
	return $returnData;
}



function dataAddEdit($data) {

	if($_SERVER["REQUEST_METHOD"] != "POST"){
		return $returnData = msg(0,404,'Page Not Found!');
	}else{
		
		
		$lan = trim($data->lan); 
		$UserId = trim($data->UserId); 
		$ClientId = trim($data->ClientId); 
		//$BranchId = trim($data->BranchId); 

		$RoleId = $data->rowData->id;
		$RoleName = $data->rowData->RoleName;
		$DefaultRedirect = "/home";
		
		try{

			$dbh = new Db();
			$aQuerys = array();

			if($RoleId == ""){
				$q = new insertq();
				$q->table = 't_roles';
				$q->columns = ['RoleName','DefaultRedirect'];
				$q->values = [$RoleName,$DefaultRedirect];
				$q->pks = ['RoleId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys = array($q); 
			}else{
				$u = new updateq();
				$u->table = 't_roles';
				$u->columns = ['RoleName','DefaultRedirect'];
				$u->values = [$RoleName,$DefaultRedirect];
				$u->pks = ['RoleId'];
				$u->pk_values = [$RoleId];
				$u->build_query();
				$aQuerys = array($u);
			}
			
			$res = exec_query($aQuerys, $UserId, $lan);  
			$success=($res['msgType']=='success')?1:0;
			$status=($res['msgType']=='success')?200:500;

			$returnData = [
			    "success" => $success ,
				"status" => $status,
				"UserId"=> $UserId,
				"message" => $res['msg']
			];

		}catch(PDOException $e){
			$returnData = msg(0,500,$e->getMessage());
		}
		
		return $returnData;
	}
}


function deleteData($data) {
 
	if($_SERVER["REQUEST_METHOD"] != "POST"){
		return $returnData = msg(0,404,'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif(!isset($data->rowData->id)){
		$fields = ['fields' => ['id']];
		return $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);
	}else{
		
		$RoleId = $data->rowData->id;
		$lan = trim($data->lan); 
		$UserId = trim($data->UserId); 
		//$ClientId = trim($data->ClientId); 
		//$BranchId = trim($data->BranchId); 

		try{

			$dbh = new Db();
			
            $d = new deleteq();
            $d->table = 't_roles';
            $d->pks = ['RoleId'];
            $d->pk_values = [$RoleId];
            $d->build_query();
            $aQuerys = array($d);

			$res = exec_query($aQuerys, $UserId, $lan);  
			$success=($res['msgType']=='success')?1:0;
			$status=($res['msgType']=='success')?200:500;

			$returnData = [
				"success" => $success ,
				"status" => $status,
				"UserId"=> $UserId,
				"message" => $res['msg']
			];
			
		}catch(PDOException $e){
			$returnData = msg(0,500,$e->getMessage());
		}
		
		return $returnData;
	}
}


?>