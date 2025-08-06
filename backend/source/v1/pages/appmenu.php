<?php

try {

	apiLogWrite("\n\n========$PageName=======Called (" . date('Y_m_d_H_i_s') . ")===================");
	apiLogWrite("Params (" . date('Y_m_d_H_i_s') . "): " . json_encode($data));

	$CategoryName =  isset($data['CategoryName']) ? $data['CategoryName'] : '';

	$UserId =  isset($data['UserInfoID']) ? $data['UserInfoID'] : '';
	$UserId = ($UserId ? $UserId : 0);

	$dbh = new Db();
	$imgURL = domainurl . "/image/appmenu/";
	$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, 
	a.UserId AS UserInfoID, d.MenuTitle AS MenuName,d.CategoryName
	,d.MenuKey AS ActivityName, concat('$imgURL',d.ICONURL) AS ICONURL
	FROM t_users a
	inner join t_user_role_map b ON a.UserId=b.UserId
	inner join t_role_menu_map c ON b.RoleId=c.RoleId
	inner join t_menu d ON c.MenuId=d.MenuId
	where a.UserId=$UserId
	and d.MenuType='APP'
	and d.CategoryName='$CategoryName'
	ORDER BY d.SortOrder ASC;";

	// "SysValue": 1,
	// "UserInfoID": 124,
	// "MenuName": "My Profile",
	// "CategoryName": "Dashboard",
	// "ActivityName": "MyProfileActivity",
	// "ICONURL": "http://api.artechtive.com/AppIcon/my_profile.png"

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
