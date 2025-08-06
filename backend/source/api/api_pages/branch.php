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

	
	$ClientId = trim($data->ClientId); //client which are assigned under login user
	//$BranchId = trim($data->BranchId); 

	try{
		$dbh = new Db();
		$query = "SELECT a.BranchId AS id,a.ClientId,a.BranchName, a.PhoneNo, a.BranchAddress, a.Email
		FROM t_branch a
		where a.ClientId=$ClientId
		ORDER BY a.BranchName ASC;";		
		
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

		$BranchId = $data->rowData->id;
		$BranchName = $data->rowData->BranchName;
		$BranchAddress = isset($data->rowData->BranchAddress) && ($data->rowData->BranchAddress !== "") ? $data->rowData->BranchAddress : NULL;
		$Email = isset($data->rowData->Email) && ($data->rowData->Email !== "")? $data->rowData->Email : NULL;
		$PhoneNo = $data->rowData->PhoneNo;
		  

		try{

			$dbh = new Db();
			$aQuerys = array();

			if($BranchId == ""){
				$q = new insertq();
				$q->table = 't_branch';
				$q->columns = ['ClientId','BranchName','PhoneNo','Email','BranchAddress'];
				$q->values = [$ClientId,$BranchName,$PhoneNo,$Email,$BranchAddress];
				$q->pks = ['BranchId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys = array($q); 
			}else{
				$u = new updateq();
				$u->table = 't_branch';
				$u->columns = ['BranchName','PhoneNo','Email','BranchAddress'];
				$u->values = [$BranchName,$PhoneNo,$Email,$BranchAddress];
				$u->pks = ['BranchId'];
				$u->pk_values = [$BranchId];
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
		
		$BranchId = $data->rowData->id;
		$lan = trim($data->lan); 
		$UserId = trim($data->UserId); 
		//$ClientId = trim($data->ClientId); 
		//$BranchId = trim($data->BranchId); 

		try{

			$dbh = new Db();
			
            $d = new deleteq();
            $d->table = 't_branch';
            $d->pks = ['BranchId'];
            $d->pk_values = [$BranchId];
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