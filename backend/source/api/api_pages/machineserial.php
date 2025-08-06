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
		$query = "SELECT a.MachineSerialId AS id,a.MachineId,a.MachineModelId, b.MachineName, 
		c.MachineModelName,a.MachineSerial
		FROM t_machineserial a
		INNER JOIN t_machine b on a.MachineId=b.MachineId
		INNER JOIN t_machinemodel c on a.MachineModelId=c.MachineModelId
		ORDER BY b.MachineName ASC, c.MachineModelName ASC, a.MachineSerial ASC;";		
		
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

		$MachineSerialId = $data->rowData->id;
		$MachineId = $data->rowData->MachineId;
		$MachineModelId = $data->rowData->MachineModelId;
		$MachineSerial = $data->rowData->MachineSerial;

		try{

			$dbh = new Db();
			$aQuerys = array();

			if($MachineSerialId == ""){
				$q = new insertq();
				$q->table = 't_machineserial';
				$q->columns = ['MachineId','MachineModelId','MachineSerial'];
				$q->values = [$MachineId,$MachineModelId,$MachineSerial];
				$q->pks = ['MachineSerialId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys = array($q); 
			}else{
				$u = new updateq();
				$u->table = 't_machineserial';
				$u->columns = ['MachineId','MachineModelId','MachineSerial'];
				$u->values = [$MachineId,$MachineModelId,$MachineSerial];
				$u->pks = ['MachineSerialId'];
				$u->pk_values = [$MachineSerialId];
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
		
		$MachineSerialId = $data->rowData->id;
		$lan = trim($data->lan); 
		$UserId = trim($data->UserId); 
		//$ClientId = trim($data->ClientId); 
		//$BranchId = trim($data->BranchId); 

		try{

			$dbh = new Db();
			
            $d = new deleteq();
            $d->table = 't_machineserial';
            $d->pks = ['MachineSerialId'];
            $d->pk_values = [$MachineSerialId];
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