<?php

try{
	
		apiLogWrite("\n\n========$PageName=======Called (".date('Y_m_d_H_i_s').")===================");
		apiLogWrite("Params (".date('Y_m_d_H_i_s')."): ".json_encode($data));

		$db = new Db();
		include("class-phpass.php");
		$wp_hasher = new PasswordHash(8, TRUE);

		$pLoginName =  isset($data['LoginID'])?$data['LoginID']:'';
		$pPassword =  isset($data['Password'])?$data['Password']:'';
		
		if($pLoginName != "" && $pPassword != "") {

			$sql1 = "SELECT UserId, Password 
			FROM t_users 
			WHERE IsActive=1 and (LoginName = '".$pLoginName."' OR Email= '".$pLoginName."');";
			
			$User_Id = 0;
			$User_Pass = '';
			$resultObj = $db->query($sql1);
			foreach ($resultObj as $aRow) {
				$User_Id = $aRow['UserId'];
				$User_Pass = $aRow['Password'];
			}

			if($User_Id == 0){
				$apiResponse = json_encode(recordNotFoundMsg());
				apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
				echo $apiResponse;
				return;
			}
		
			if(!$wp_hasher->CheckPassword($pPassword, $User_Pass)) {
				$Pass_MD5 = MD5(trim($pPassword)); 
				if($Pass_MD5 != $User_Pass){
					$apiResponse = json_encode(recordNotFoundMsg(0, 'Your password is incorrect.'));
					apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
					echo $apiResponse;
					return;
				}
			}

			$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
			a.UserId AS UserInfoID, a.UserName, a.LoginName AS LoginID, '' AS LoginPassword, 
			a.Email AS EmailAddress, b.DesignationName, c.DepartmentName ,ifnull(a.Address,'') AS Address
			,ifnull(d.RoleId,'') AS PermissionGroupID
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
			$apiResponse = json_encode(recordNotFoundMsg(0,'Please enter user login id and password.'));
			apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
			echo $apiResponse;	
		}
		
	
}catch(PDOException $e){
	$apiResponse = json_encode(recordNotFoundMsg(0,$e->getMessage()));
	apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
	echo $apiResponse;	
}

?>