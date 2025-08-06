<?php

try{
	
	apiLogWrite("\n\n========$PageName=======Called (".date('Y_m_d_H_i_s').")===================");
	apiLogWrite("Params (".date('Y_m_d_H_i_s')."): ".json_encode($data));
	
	$CompanyCode =  isset($data['Code'])?$data['Code']:'';
		
	$dbh = new Db();
	$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
	ifnull(a.AppName,'') AS AppName, ifnull(a.PoweredBy,'') AS PoweredBy, ifnull(a.DevelopmentBy,'') AS DevelopmentBy,
	ifnull(a.ClientCode,'') AS CompanyCode,ifnull(a.ClientName,'') AS CompanyName,
	ifnull(a.DevelopmentByWebsite,'') AS DevelopmentByWebsite
	FROM t_client a
	where a.ClientCode='$CompanyCode';";		

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