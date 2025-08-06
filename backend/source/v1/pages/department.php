<?php

try{
	apiLogWrite("\n\n========$PageName=======Called (".date('Y_m_d_H_i_s').")===================");
	apiLogWrite("Params (".date('Y_m_d_H_i_s')."): ".json_encode($data));
	
	$dbh = new Db();
	//$Search =  isset($data['Search'])?$data['Search']:'';
	
	// $sWhere="";
	// if($Search){
	// 	$sWhere=" where (a.CustomerCode like '%$Search%' 
	// 	OR a.CustomerName like '%$Search%' 
	// 	OR a.ContactPhone like '%$Search%' 
	// 	OR a.CompanyName like '%$Search%' 
	// 	OR a.NatureOfBusiness like '%$Search%' 
	// 	OR a.CompanyEmail like '%$Search%' 
	// 	OR a.CompanyAddress like '%$Search%' 
	// 	)";
	// }
	
	$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
	a.DepartmentId, a.DepartmentName
	FROM t_department a
	$sWhere
	ORDER BY a.DepartmentName ASC;";		


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