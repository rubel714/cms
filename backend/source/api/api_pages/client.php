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

		$query = "SELECT a.CustomerId AS id, a.CustomerCode,a.CustomerName, 
		a.Designation,  a.ContactPhone, a.CompanyName, a.NatureOfBusiness,
		a.CompanyEmail,a.CompanyAddress,a.IsActive,a.UserId
		, case when a.IsActive=1 then 'Active' else 'In Active' end IsActiveName
		,a.CustomerGroupId,b.CustomerGroupName
		FROM t_customer a
		inner join t_customergroup b on a.CustomerGroupId=b.CustomerGroupId
		ORDER BY a.CustomerName ASC;";

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

		$CustomerId = $data->rowData->id;
		$CustomerCode = isset($data->rowData->CustomerCode) && ($data->rowData->CustomerCode !== "") ? $data->rowData->CustomerCode : NULL;
		if(!$CustomerCode){
			$CustomerCode = date('YmdHis');
		}

		$CustomerName = $data->rowData->CustomerName;
		$CustomerGroupId = $data->rowData->CustomerGroupId;
		$Designation = isset($data->rowData->Designation) && ($data->rowData->Designation !== "") ? $data->rowData->Designation : NULL;
		$ContactPhone = isset($data->rowData->ContactPhone) && ($data->rowData->ContactPhone !== "")? $data->rowData->ContactPhone : NULL;
		$CompanyName = isset($data->rowData->CompanyName) && ($data->rowData->CompanyName !== "")? $data->rowData->CompanyName : NULL;
		// $NatureOfBusiness = isset($data->rowData->NatureOfBusiness) && ($data->rowData->NatureOfBusiness !== "")? $data->rowData->NatureOfBusiness : NULL;
		$CompanyEmail = isset($data->rowData->CompanyEmail) && ($data->rowData->CompanyEmail !== "")? $data->rowData->CompanyEmail : NULL;
		$CompanyAddress = isset($data->rowData->CompanyAddress) && ($data->rowData->CompanyAddress !== "")? $data->rowData->CompanyAddress : NULL;
		$IsActive = 1;//isset($data->rowData->IsActive) ? $data->rowData->IsActive : 0;

		try{

			$dbh = new Db();
			$aQuerys = array();

			if($CustomerId == ""){
				$q = new insertq();
				$q->table = 't_customer';
				$q->columns = ['ClientId','CustomerCode','CustomerName','CustomerGroupId','Designation','ContactPhone','CompanyName','CompanyEmail','CompanyAddress','IsActive','UserId'];
				$q->values = [$ClientId,$CustomerCode,$CustomerName,$CustomerGroupId,$Designation,$ContactPhone,$CompanyName,$CompanyEmail,$CompanyAddress,$IsActive,$UserId];
				$q->pks = ['CustomerId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys = array($q);

			}else{
				$u = new updateq();
				$u->table = 't_customer';
				$u->columns = ['CustomerCode','CustomerName','CustomerGroupId','Designation','ContactPhone','CompanyName','CompanyEmail','CompanyAddress'];
				$u->values = [$CustomerCode,$CustomerName,$CustomerGroupId,$Designation,$ContactPhone,$CompanyName,$CompanyEmail,$CompanyAddress];
				$u->pks = ['CustomerId'];
				$u->pk_values = [$CustomerId];
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
		
		$CustomerId = $data->rowData->id;
		$lan = trim($data->lan); 
		$UserId = trim($data->UserId);

		try{

			// $dbh = new Db();
			
            $d = new deleteq();
            $d->table = 't_customer';
            $d->pks = ['CustomerId'];
            $d->pk_values = [$CustomerId];
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