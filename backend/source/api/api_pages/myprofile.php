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
	
	// case "deleteData":
	// 	$returnData = deleteData($data);
	// break;

	default :
		echo "{failure:true}";
		break;
}

function getDataList($data){

	// $ClientId = trim($data->ClientId); 
	// $BranchId = trim($data->BranchId); 
	$UserId = trim($data->UserId); 

	try{
		$dbh = new Db();


		$query = "SELECT a.UserId AS id,a.ClientId ,a.BranchId, a.`UserName`, a.Password,a.LoginName,
		 a.`Email`,a.`IsActive`, CASE WHEN a.IsActive=1 THEN 'Yes' ELSE 'No' END IsActiveName, 
		a.DesignationId, b.DesignationName, c.RoleId, d.RoleName,a.PhoneNo,e.DepartmentName,f.BusinessLineName
		,g.`UserName` AS LinemanUserName,a.Address
	   FROM `t_users` a
	   INNER JOIN `t_designation` b ON a.`DesignationId` = b.`DesignationId`
	   INNER JOIN `t_user_role_map` c ON a.`UserId` = c.`UserId`
	   INNER JOIN `t_roles` d ON c.`RoleId` = d.`RoleId`
	   INNER JOIN `t_department` e ON a.`DepartmentId` = e.`DepartmentId`
	   INNER JOIN `t_businessline` f ON a.`BusinessLineId` = f.`BusinessLineId`
	   LEFT JOIN `t_users` g ON a.`LinemanUserId` = g.`UserId`

	   where a.UserId=$UserId;";
		
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
		// $ClientId = trim($data->ClientId); 
		// $BranchId = trim($data->BranchId); 

		$id = $data->rowData->id;
		$UserName = $data->rowData->UserName;
		$pass_word = $data->rowData->Password;
		$Password = password_hash($pass_word, PASSWORD_DEFAULT);
		$LoginName = $data->rowData->LoginName;
		$Email = $data->rowData->Email;
		$DesignationId = $data->rowData->DesignationId;
		// $RoleId = $data->rowData->RoleId;
		// $IsActive = $data->rowData->IsActive ? $data->rowData->IsActive : 0;

		$Cpassword =  empty($data->rowData->Password) ?  '':$data->rowData->Password;	
		$PhotoUrl = 'rubel.jpg';

		try{

			$dbh = new Db();
			$aQuerys = array();


			$u = new updateq();
			$u->table = 't_users';

				if($Cpassword != ''){
					$u->columns = ['UserName','LoginName','Password','Email','DesignationId'];
					$u->values = [$UserName,$LoginName,$Password,$Email,$DesignationId];
					
				}else{
					$u->columns = ['UserName','LoginName','Email','DesignationId'];
					$u->values = [$UserName,$LoginName,$Email,$DesignationId];
			
				}

			$u->pks = ['UserId'];
			$u->pk_values = [$id];
			$u->build_query();
			$aQuerys[] = $u;




			// $u = new updateq();
			// $u->table = 't_user_role_map';
			// $u->columns = ['RoleId'];
			// $u->values = [$RoleId];
			// $u->pks = ['UserId'];
			// $u->pk_values = [$id];
			// $u->build_query();
			// $aQuerys[] = $u;


			
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


// function deleteData($data) {
 
// 	if($_SERVER["REQUEST_METHOD"] != "POST"){
// 		return $returnData = msg(0,404,'Page Not Found!');
// 	}
// 	// CHECKING EMPTY FIELDS
// 	elseif(!isset($data->rowData->id)){
// 		$fields = ['fields' => ['id']];
// 		return $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);
// 	}else{
		
// 		$id = $data->rowData->id;
// 		$lan = trim($data->lan); 
// 		$UserId = trim($data->UserId); 

// 		try{

// 			$dbh = new Db();
			
//             $d = new deleteq();
//             $d->table = 't_users';
//             $d->pks = ['UserId'];
//             $d->pk_values = [$id];
//             $d->build_query();
//             $aQuerys = array($d);

// 			$res = exec_query($aQuerys, $UserId, $lan);  
// 			$success=($res['msgType']=='success')?1:0;
// 			$status=($res['msgType']=='success')?200:500;

// 			$returnData = [
// 				"success" => $success ,
// 				"status" => $status,
// 				"UserId"=> $UserId,
// 				"message" => $res['msg']
// 			];
			
// 		}catch(PDOException $e){
// 			$returnData = msg(0,500,$e->getMessage());
// 		}
		
// 		return $returnData;
// 	}
// }


?>