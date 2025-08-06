<?php



try{
	apiLogWrite("\n\n========$PageName=======Called (".date('Y_m_d_H_i_s').")===================");
	apiLogWrite("Params (".date('Y_m_d_H_i_s')."): ".json_encode($data));
	
	$dbh = new Db();
	$DepartmentId =  isset($data['DepartmentId'])?$data['DepartmentId']:'';
	
	if ($DepartmentId == "") {
		$apiResponse = json_encode(recordNotFoundMsg(0, "DepartmentId param are missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}
	
	$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
	a.UserId AS UserInfoID, a.UserName
	FROM t_users a
	where (a.DepartmentId = $DepartmentId OR $DepartmentId=0)
	ORDER BY a.UserId ASC;";		

	$resultdata = $dbh->query($query);
	
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
	
}catch(PDOException $e){
	$apiResponse = json_encode(recordNotFoundMsg(0,$e->getMessage()));
	apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
	echo $apiResponse;	
}

?>