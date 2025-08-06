<?php

try{
	
		apiLogWrite("\n\n========$PageName=======Called (".date('Y_m_d_H_i_s').")===================");
		apiLogWrite("Params (".date('Y_m_d_H_i_s')."): ".json_encode($data));

		$db = new Db();
		$User_Id =  isset($data['UserInfoID'])?$data['UserInfoID']:'';
		
		if($User_Id != "") {

			$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
			a.UserId, a.UserName, a.LoginName AS LoginID, '' AS LoginPassword, 
			a.Email AS EmailAddress,ifnull(b.DesignationName,'') AS DesignationName,ifnull(c.DepartmentName,'') AS DepartmentName
			,ifnull(a.Address,'') AS LocationName,ifnull(d.RoleId,'') AS PermissionGroupID
			FROM t_users a
			INNER JOIN t_designation b on a.DesignationId=b.DesignationId
			INNER JOIN t_department c on a.DepartmentId=c.DepartmentId
			INNER JOIN t_user_role_map d on a.UserId=d.UserId
			WHERE a.UserId = $User_Id and a.IsActive=1;";		
			
			$resultdata = $db->query($query);
			
			if (is_object($resultdata)) {
				$errormsg = $resultdata->errorInfo;
				$apiResponse = json_encode(recordNotFoundMsg(0,$errormsg[2]));
				apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
				echo $apiResponse;					
			}else if(count($resultdata)===0){
				$apiResponse = json_encode(recordNotFoundMsg());
				apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
				echo $apiResponse;
			}else{
				echo json_encode($resultdata);
				apiLogWrite("Output (".date('Y_m_d_H_i_s')."): Success");
			}

		}else{
			$apiResponse = json_encode(recordNotFoundMsg(0,'Please enter user login id.'));
			apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
			echo $apiResponse;	
		}
		
	
}catch(PDOException $e){
	$apiResponse = json_encode(recordNotFoundMsg(0,$e->getMessage()));
	apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
	echo $apiResponse;	
}

?>