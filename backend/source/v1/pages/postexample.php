<?php

// print_r($data);
// exit;
$datalist = array();
$datalist[] = array(
				"SysValue"=> 1,
				"SysMessage"=> "Successful",
				"EmployeeID"=> 124,
				"EmployeeName"=> "Kangkan",
				"DesignationName"=> "Programming",
				"DepartmentName"=> "IT",
				"LocationName"=> "Dhaka",
				"ContactNumber"=> "",
				"EmailAddress"=> ""
			);
$datalist[] = array(
				"SysValue"=> 1,
				"SysMessage"=> "Successful",
				"EmployeeID"=> 124,
				"EmployeeName"=> "Kangkan",
				"DesignationName"=> "Programming",
				"DepartmentName"=> "IT",
				"LocationName"=> "Dhaka",
				"ContactNumber"=> "",
				"EmailAddress"=> ""
			);
echo json_encode($datalist);


// try{
	// $dbh = new Db();
	// $query = "SELECT a.ClientId AS id, a.ClientName, a.ClientCode, a.PhoneNo, 
	// a.Email, a.ClientAddress,a.ClientLogo,a.IsActive
	// , case when a.IsActive=1 then 'Active' else 'In Active' end IsActiveName
	// FROM t_client a
	// ORDER BY a.ClientCode ASC;";		
	
	// $resultdata = $dbh->query($query);
	
	// $returnData = [
		// "success" => 1,
		// "status" => 200,
		// "message" => "",
		// "datalist" => $resultdata
	// ];

// }catch(PDOException $e){
	// $returnData = msg(0,500,$e->getMessage());
// }

// return $returnData;

?>