<?php

try {
	apiLogWrite("\n\n========$PageName=======Called (" . date('Y_m_d_H_i_s') . ")===================");
	apiLogWrite("Params (" . date('Y_m_d_H_i_s') . "): " . json_encode($data));

	$dbh = new Db();
	$CategoryID =  isset($data['CategoryID']) ? $data['CategoryID'] :	'';

	if ($CategoryID == "" ) {
		$apiResponse = json_encode(recordNotFoundMsg(0, "CategoryID param is missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}

	// $CategoryName = '';
	// if ($CategoryID == 1) {
	// 	$CategoryName = 'Visit Purpose';
	// } else if ($CategoryID == 2) {
	// 	$CategoryName = 'Sample Activity';
	// } else if ($CategoryID == 3) {
	// 	$CategoryName = 'Order/Sales';
	// } else if ($CategoryID == 4) {
	// 	$CategoryName = 'Transpotation';
	// } else if ($CategoryID == 5) {
	// 	$CategoryName = 'Activity Action';
	// } else if ($CategoryID == 6) {
	// 	$CategoryName = 'Activity Result';
	// } else if ($CategoryID == 7) {
	// 	$CategoryName = 'Order/Sales Action';
	// } else if ($CategoryID == 8) {
	// 	$CategoryName = 'Visit Action';
	// }

	$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
	a.DropDownListID, a.DisplayName,b.CategoryName
	FROM t_dropdownlist a
	INNER JOIN t_dropdowncategory b ON a.CategoryID=b.CategoryID
	where a.CategoryID=$CategoryID
	ORDER BY a.DropDownListID ASC;";

	$resultdata = $dbh->query($query);

	if (is_object($resultdata)) {
		$errormsg = $resultdata->errorInfo;
		$apiResponse = json_encode(recordNotFoundMsg(0, $errormsg[2]));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
	} else if (count($resultdata) === 0) {
		$apiResponse = json_encode(recordNotFoundMsg());
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
	} else {
		echo json_encode($resultdata);
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): Success");
	}
} catch (PDOException $e) {
	$apiResponse = json_encode(recordNotFoundMsg(0, $e->getMessage()));
	apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
	echo $apiResponse;
}
