<?php
try {

	apiLogWrite("\n\n========$PageName=======Called (" . date('Y_m_d_H_i_s') . ")===================");
	apiLogWrite("Params (" . date('Y_m_d_H_i_s') . "): " . json_encode($data));

	$db = new Db();
 
	$CheckId = isset($data['CheckId']) ? checkNull($data['CheckId']) : "";
	$CheckName = isset($data['CheckName']) ? checkNull($data['CheckName']) : "";
	$CurrentDate = date('Y-m-d H:i:s');
	// $TransactionDate = isset($data['NextVisitDateTime']) ? convertAppToDBDateTime(checkNull($data['NextVisitDateTime'])) : null; //21-Aug-2024 5:15 AM

	if ($CheckName == "" ) {
		$apiResponse = json_encode(recordNotFoundMsg(0, "CheckName param is missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}

	if($CheckId==""){
		$query = "INSERT INTO t_checklist(CheckId, CheckName, UpdateTs, CreateTs) 
			VALUES (:CheckId, :CheckName, :UpdateTs, :CreateTs);";

		$pList = array(
			'CheckId' => $CheckId,
			'CheckName' => $CheckName,	
			'UpdateTs' => $CurrentDate,
			'CreateTs' => $CurrentDate
		);

	}else{
		$query = "UPDATE t_checklist set CheckName=:CheckName
		where CheckId=:CheckId;";
		
		$pList = array(
			'CheckName' => $CheckName,
			'CheckId' => $CheckId
		);
	}

	$db->bindMore($pList);
	$resultdata = $db->query($query);

	if (is_object($resultdata)) {
		$errormsg = $resultdata->errorInfo;
		$apiResponse = json_encode(recordNotFoundMsg(0, $errormsg[2]));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
	} else {
		$response = array(["SysValue" => 1, "SysMessage" => "Data has been saved successfully"]);
		$apiResponse = json_encode($response);
		echo $apiResponse;
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): ".$apiResponse);
	}
} catch (PDOException $e) {
	$apiResponse = json_encode(recordNotFoundMsg(0, $e->getMessage()));
	apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
	echo $apiResponse;
}