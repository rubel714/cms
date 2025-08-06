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
	$BranchId = trim($data->BranchId); 

	try{
		$dbh = new Db();

		$query = "SELECT a.UserId AS id,a.ClientId ,a.BranchId, a.`UserName`, a.Password,a.LoginName,
		 a.`Email`,a.PhoneNo,a.`IsActive`, CASE WHEN a.IsActive=1 THEN 'Yes' ELSE 'No' END IsActiveName, 
		a.DesignationId, b.DesignationName, c.RoleId, d.RoleName,a.PhotoUrl,a.DepartmentId,e.DepartmentName,
		a.TeamId,f.TeamName,a.`LinemanUserId`,g.`UserName` as LinemanUserName,a.Address,a.BusinessLineId,a.UserCode
	   FROM `t_users` a
	   INNER JOIN `t_designation` b ON a.`DesignationId` = b.`DesignationId`
	   INNER JOIN `t_user_role_map` c ON a.`UserId` = c.`UserId`
	   INNER JOIN `t_roles` d ON c.`RoleId` = d.`RoleId`
	   INNER JOIN `t_department` e ON a.`DepartmentId` = e.`DepartmentId`
	   LEFT JOIN `t_team` f ON a.`TeamId` = f.`TeamId`
	   LEFT JOIN `t_users` g ON a.`LinemanUserId` = g.`UserId`
	   where a.ClientId=$ClientId
	   and a.BranchId=$BranchId
	   ORDER BY a.`UserName` ASC;";
		
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
		
		// echo "<pre>";
		// print_r($data);
		$lan = trim($data->lan); 
		$UserId = trim($data->UserId); 
		$ClientId = trim($data->ClientId); 
		$BranchId = trim($data->BranchId); 

		$id = $data->rowData->id;
		$UserCode = $data->rowData->UserCode;
		$UserName = $data->rowData->UserName;
		$pass_word = $data->rowData->Password;
		$Password = password_hash($pass_word, PASSWORD_DEFAULT);
		$LoginName = $data->rowData->LoginName;
		// $DivisionId = $data->rowData->DivisionId? $data->rowData->DivisionId : null;
		// $DistrictId = $data->rowData->DistrictId? $data->rowData->DistrictId : null;
		// $UpazilaId = $data->rowData->UpazilaId? $data->rowData->UpazilaId : null;
		$Email = $data->rowData->Email;
		$PhoneNo = $data->rowData->PhoneNo? $data->rowData->PhoneNo : null;
		$DesignationId = $data->rowData->DesignationId;
		$RoleId = $data->rowData->RoleId;
		$DepartmentId = $data->rowData->DepartmentId;
		// $BusinessLineId = $data->rowData->BusinessLineId;
		$IsActive = $data->rowData->IsActive ? $data->rowData->IsActive : 0;
		$TeamId = $data->rowData->TeamId? $data->rowData->TeamId : null;
		$LinemanUserId = $data->rowData->LinemanUserId? $data->rowData->LinemanUserId : null;
		$Address = $data->rowData->Address? $data->rowData->Address : null;

		$Cpassword =  empty($data->rowData->Password) ?  '':$data->rowData->Password;	
		$PhotoUrl =  $data->rowData->PhotoUrl? $data->rowData->PhotoUrl : "placeholder.png";
		
		try{

			$dbh = new Db();
			$aQuerys = array();

			if($id == ""){
				$q = new insertq();
				$q->table = 't_users';
				$q->columns = ['ClientId','BranchId','UserCode','UserName','LoginName','Password','Email','PhoneNo','IsActive','DesignationId','PhotoUrl','DepartmentId','TeamId','LinemanUserId','Address'];
				$q->values = [$ClientId,$BranchId,$UserCode,$UserName,$LoginName,$Password,$Email,$PhoneNo,$IsActive,$DesignationId,$PhotoUrl,$DepartmentId,$TeamId,$LinemanUserId,$Address];
				$q->pks = ['UserId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q; 

				$q = new insertq();
				$q->table = 't_user_role_map';
				$q->columns = ['UserId','RoleId'];
				$q->values = ['[LastInsertedId]',$RoleId];
				$q->pks = ['UserRoleId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q; 

			}else{
				$u = new updateq();
				$u->table = 't_users';

					if($Cpassword != ''){
						$u->columns = ['ClientId','BranchId','UserCode','UserName','LoginName','Password','Email','PhoneNo','IsActive','DesignationId','PhotoUrl','DepartmentId','TeamId','LinemanUserId','Address'];
						$u->values = [$ClientId,$BranchId,$UserCode,$UserName,$LoginName,$Password,$Email,$PhoneNo,$IsActive,$DesignationId,$PhotoUrl,$DepartmentId,$TeamId,$LinemanUserId,$Address];
						
					}else{
						$u->columns = ['ClientId','BranchId','UserCode','UserName','LoginName','Email','PhoneNo','IsActive','DesignationId','PhotoUrl','DepartmentId','TeamId','LinemanUserId','Address'];
						$u->values = [$ClientId,$BranchId,$UserCode, $UserName,$LoginName,$Email,$PhoneNo,$IsActive,$DesignationId,$PhotoUrl,$DepartmentId,$TeamId,$LinemanUserId,$Address];
				
					}

				$u->pks = ['UserId'];
				$u->pk_values = [$id];
				$u->build_query();
				$aQuerys[] = $u;




				$u = new updateq();
				$u->table = 't_user_role_map';
				$u->columns = ['RoleId'];
				$u->values = [$RoleId];
				$u->pks = ['UserId'];
				$u->pk_values = [$id];
				$u->build_query();
				$aQuerys[] = $u;
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
		
		$id = $data->rowData->id;
		$lan = trim($data->lan); 
		$UserId = trim($data->UserId); 

		try{

			$dbh = new Db();
			
            $d = new deleteq();
            $d->table = 't_user_role_map';
            $d->pks = ['UserId'];
            $d->pk_values = [$id];
            $d->build_query();
            $aQuerys[] = $d;
			
            $d = new deleteq();
            $d->table = 't_users';
            $d->pks = ['UserId'];
            $d->pk_values = [$id];
            $d->build_query();
            $aQuerys[] = $d;

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