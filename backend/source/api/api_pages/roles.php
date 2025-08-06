<?php
/*
$task = '';
if(isset($data->action)) {
	$task = trim($data->action);
}

switch($task){
	
	case "getRoleList":
		$returnData = getRoleList($data);
	break;
	
	case "geRole":
		$returnData = geRole($data);
	break;

	
 	case "dataInsert":
		$returnData = dataInsert($data);
	break;
	

	case "dataUpdate":
	  $returnData = dataUpdate($data);
	break; 
	
	
	case "dataDalete":
		$returnData = dataDalete($data);
	break;	
	

	default :
		echo "{failure:true}";
		break;
}

function getRoleList($data){
	try{
		$dbh = new Db();
		
		//$query = "SELECT `id`,`role`,`defaultredirect` FROM roles ORDER BY role;";		
		$query = "SELECT a.`id`, a.`role`, a.`defaultredirect`, a.`ItemGroupId`, b.`GroupName` FROM roles a
		INNER JOIN `t_itemgroup` b ON a.`ItemGroupId` = b.`ItemGroupId`
		ORDER BY role;";		
		
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


function dataInsert($data) {

	if($_SERVER["REQUEST_METHOD"] != "POST"){
		return $returnData = msg(0,404,'Page Not Found!');
	}

	// CHECKING EMPTY FIELDS
	elseif(!isset($data->role)
	){
		$fields = ['fields' => ['role','ItemGroupId']];
		return $returnData = msg(0,422,'Please Fill in all Required Fields!', $fields);
	
	}else{
		
		$role = trim($data->role);
		$defaultredirect = trim($data->defaultredirect);
		$bFacilitySelected = empty(trim($data->bFacilitySelected)) ? 0 : trim($data->bFacilitySelected);
		$lan = trim($data->lan); 
		$UserName = trim($data->UserName);		
		$ItemGroupId = trim($data->ItemGroupId);		
		try{

			$dbh = new Db();

			$aQuerys = array();

			$q = new insertq();
			$q->table = 'roles';
			$q->columns = ['role','defaultredirect','bFacilitySelected','ItemGroupId'];
			$q->values = [$role,$defaultredirect,$bFacilitySelected, $ItemGroupId];
			$q->pks = ['id'];
			$q->bUseInsetId = false;
			$q->build_query();
			$aQuerys = array($q); 

			$res = exec_query($aQuerys, $UserName, $lan);  
			$success=($res['msgType']=='success')?1:0;
			$status=($res['msgType']=='success')?200:500;  
			
			$returnData = [
				"success" => $success ,
				"status" => $status,
				"UserName"=> $UserName,
				"message" => $res['msg']
			];

		}catch(PDOException $e){
			$returnData = msg(0,500,$e->getMessage());
		}
		
		return $returnData;
	}
}


function dataUpdate($data) {

	if($_SERVER["REQUEST_METHOD"] != "PUT"){
		return $returnData = msg(0,404,'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif(!isset($data->role)
	){
		$fields = ['fields' => ['role','ItemGroupId']];
		return $returnData = msg(0,422,'Please Fill in all Required Fields!', $fields);
	
	}else{

		$id = trim($data->id);
		
		$role = trim($data->role);
		$defaultredirect = trim($data->defaultredirect);
		$bFacilitySelected = empty(trim($data->bFacilitySelected)) ? 0 : trim($data->bFacilitySelected);
		$lan = trim($data->lan); 
		$UserName = trim($data->UserName);		
		$ItemGroupId = trim($data->ItemGroupId);		
		
		try{
 
			$dbh = new Db();

			$aQuerys = array();
            $u = new updateq();
            $u->table = 'roles';
            $u->columns = ['role','defaultredirect','bFacilitySelected','ItemGroupId'];
            $u->values = [$role,$defaultredirect,$bFacilitySelected,$ItemGroupId];
            $u->pks = ['id'];
            $u->pk_values = [$id];
            $u->build_query();
            $aQuerys = array($u);
			
			$res = exec_query($aQuerys, $UserName, $lan);  
			$success=($res['msgType']=='success')?1:0;
			$status=($res['msgType']=='success')?200:500;  
			
			$returnData = [
				"success" => $success ,
				"status" => $status,
				"UserName"=> $UserName,
				"message" => $res['msg']
			];

		}catch(PDOException $e){
			$returnData = msg(0,500,$e->getMessage());
		}
		
		return $returnData;
	}
}

function dataDalete($data) {

	if($_SERVER["REQUEST_METHOD"] != "POST"){
		return $returnData = msg(0,404,'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif(!isset($data->id)){
		
		$fields = ['fields' => ['id']];
		return $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);
		
	}else{
		
		$id = trim($data->id);
		$lan = trim($data->lan); 
		$UserName = trim($data->UserName);
		try{

			$dbh = new Db();
			
            $d = new deleteq();
            $d->table = 'roles';
            $d->pks = ['id'];
            $d->pk_values = [$id];
            $d->build_query();
            $aQuerys = array($d);

			$res = exec_query($aQuerys, $UserName, $lan);  
			$success=($res['msgType']=='success')?1:0;
			$status=($res['msgType']=='success')?200:500;  
			
			$returnData = [
				"success" => $success ,
				"status" => $status,
				"UserName"=> $UserName,
				"message" => $res['msg']
			];
			
		}catch(PDOException $e){
			$returnData = msg(0,500,$e->getMessage());
		}
		
		return $returnData;
	}
}

function geRole($data) {

	$dbh = new Db();

	$id = trim($data->id);

	$query = "SELECT * FROM roles WHERE id = $id;"; 

	$returnData = $dbh->query($query);

	return $returnData[0];
}
*/
?>