<?php

include_once('../env.php');
include_once('../source/api/pdolibs/pdo_lib.php');

$tableProperties = array("header_list" => array(), "query_field" => array(), "table_header" => array(), "align" => array(), "width_print_pdf" => array(), "width_excel" => array(), "precision" => array(), "total" => array(), "report_save_name" => "");

// $menukey = $_REQUEST['menukey'];
// $lan = $_REQUEST['lan'];
// include_once ('../source/api/languages/lang_switcher_custom.php');

$siteTitle = reportsitetitleeng;

$task = '';

if (isset($_POST['action'])) {
	$task = $_POST['action'];
} else if (isset($_GET['action'])) {
	$task = $_GET['action'];
}

switch ($task) {
	case "UserExport":
		UserExport();
		break;
	case "TeamExport":
		TeamExport();
		break;
	case "DesignationExport":
		DesignationExport();
		break;
	case "CheckListExport":
		CheckListExport();
		break;
	case "DepartmentExport":
		DepartmentExport();
		break;
	case "BusinessLinetExport":
		BusinessLinetExport();
		break;
	case "MachineExport":
		MachineExport();
		break;
	case "MachinepartsExport":
		MachinepartsExport();
		break;
	case "MachinemodelExport":
		MachinemodelExport();
		break;
	case "MachineserialExport":
		MachineserialExport();
		break;
	case "FeedbackExport":
		FeedbackExport();
		break;






	case "ClientExport":
		ClientExport();
		break;

	case "ReferenceExport":
		ReferenceExport();
		break;

	case "RoleExport":
		RoleExport();
		break;



	case "RoleToMenuPermissionExport":
		RoleToMenuPermissionExport();
		break;


	// case "ClientExport":
	// 	ClientExport();
	// 	break;

	case "BranchExport":
		BranchExport();
		break;


	default:
		echo "{failure:true}";
		break;
}


function UserExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;

	$ClientId = $_REQUEST['ClientId'];
	$BranchId = $_REQUEST['BranchId'];
	$sql = "SELECT a.UserCode,a.`UserName`,a.LoginName,a.Email,a.PhoneNo,c.RoleName,d.DesignationName,
	e.DepartmentName,h.BusinessLineName,g.`UserName` as LinemanUserName,a.Address,
	case when a.IsActive=1 then 'Yes' else 'No' end Status

	FROM t_users a
	inner join t_user_role_map b on a.UserId=b.UserId
	inner join t_roles c on b.RoleId=c.RoleId
	inner join t_designation d on a.DesignationId=d.DesignationId
	INNER JOIN `t_department` e ON a.`DepartmentId` = e.`DepartmentId`
	INNER JOIN `t_businessline` h ON a.`BusinessLineId` = h.`BusinessLineId`
	LEFT JOIN `t_users` g ON a.`LinemanUserId` = g.`UserId`
	where a.ClientId=$ClientId 
	and a.BranchId=$BranchId 
	ORDER BY a.UserName;";

	$tableProperties["query_field"] = array("UserCode", "UserName", "LoginName", "Email", "PhoneNo", "RoleName", "DesignationName", "DepartmentName", "BusinessLineName", "LinemanUserName", "Address", "Status");
	$tableProperties["table_header"] = array('User Id', 'User Name', 'Login Name', 'Email', 'Phone No', 'Role Name', 'Designation', "Department", "Business Line", "Lineman (N+1)", "Address", 'Active');
	$tableProperties["align"] = array("left", "left", "left", "left", "left", "left", "left", "left", "left", "left", "left", "left");
	$tableProperties["width_print_pdf"] = array("15%", "30%",  "20%", "20%", "10%", "10%", "10%", "10%", "10%", "10%", "10%", "10%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("15", "30", "20", "20", "20", "20", "15", "15",  "15", "15", "15", "10");
	$tableProperties["precision"] = array("string", "string", "string", "string", "string", "string", "string", "string", "string", "string", "string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1
	// exit;
	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'User Information';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'User_Information';
}

function TeamExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT `TeamName`
	FROM t_team 
	ORDER BY `TeamName`;";

	$tableProperties["query_field"] = array("TeamName");
	$tableProperties["table_header"] = array('Team Name');
	$tableProperties["align"] = array("left");
	$tableProperties["width_print_pdf"] = array("100%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("80");
	$tableProperties["precision"] = array("string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0); //not total=0, total=1
	$tableProperties["color_code"] = array(0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Team';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Team';
}


function DesignationExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT `DesignationName`
	FROM t_designation 
	ORDER BY `DesignationName`;";

	$tableProperties["query_field"] = array("DesignationName");
	$tableProperties["table_header"] = array('Designation');
	$tableProperties["align"] = array("left");
	$tableProperties["width_print_pdf"] = array("100%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("80");
	$tableProperties["precision"] = array("string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0); //not total=0, total=1
	$tableProperties["color_code"] = array(0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Designation';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Designation';
}


function CheckListExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT `CheckName`
	FROM t_checklist 
	ORDER BY `CheckName`;";

	$tableProperties["query_field"] = array("CheckName");
	$tableProperties["table_header"] = array('Check Name');
	$tableProperties["align"] = array("left");
	$tableProperties["width_print_pdf"] = array("100%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("80");
	$tableProperties["precision"] = array("string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0); //not total=0, total=1
	$tableProperties["color_code"] = array(0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'CheckList';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'CheckList';
}


function DepartmentExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT `DepartmentName`
	FROM t_department 
	ORDER BY `DepartmentName`;";

	$tableProperties["query_field"] = array("DepartmentName");
	$tableProperties["table_header"] = array('Department');
	$tableProperties["align"] = array("left");
	$tableProperties["width_print_pdf"] = array("100%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("80");
	$tableProperties["precision"] = array("string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0); //not total=0, total=1
	$tableProperties["color_code"] = array(0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Department';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Department';
}


function BusinessLinetExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT `BusinessLineName`
	FROM t_businessline 
	ORDER BY `BusinessLineName`;";

	$tableProperties["query_field"] = array("BusinessLineName");
	$tableProperties["table_header"] = array('Business Line');
	$tableProperties["align"] = array("left");
	$tableProperties["width_print_pdf"] = array("100%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("80");
	$tableProperties["precision"] = array("string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0); //not total=0, total=1
	$tableProperties["color_code"] = array(0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Business Line';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'BusinessLine';
}


function MachineExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT `MachineName`
	FROM t_machine 
	ORDER BY `MachineName`;";

	$tableProperties["query_field"] = array("MachineName");
	$tableProperties["table_header"] = array('Machine');
	$tableProperties["align"] = array("left");
	$tableProperties["width_print_pdf"] = array("100%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("80");
	$tableProperties["precision"] = array("string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0); //not total=0, total=1
	$tableProperties["color_code"] = array(0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Machine';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Machine';
}

function MachinepartsExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT b.MachineName,a.`MachinePartsName`
		FROM t_machineparts a
		INNER JOIN t_machine b on a.MachineId=b.MachineId
		ORDER BY b.MachineName ASC, a.MachinePartsName ASC;";

	$tableProperties["query_field"] = array("MachineName", "MachinePartsName");
	$tableProperties["table_header"] = array('Machine Name', 'Machine Parts Name');
	$tableProperties["align"] = array("left", "left");
	$tableProperties["width_print_pdf"] = array("20%", "80%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("25", "80");
	$tableProperties["precision"] = array("string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Machine Parts';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Machine_Parts';
}



function MachinemodelExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT b.MachineName,a.`MachineModelName`
		FROM t_machinemodel a
		INNER JOIN t_machine b on a.MachineId=b.MachineId
		ORDER BY b.MachineName ASC, a.MachineModelName ASC;";

	$tableProperties["query_field"] = array("MachineName", "MachineModelName");
	$tableProperties["table_header"] = array('Machine Name', 'Machine Model Name');
	$tableProperties["align"] = array("left", "left");
	$tableProperties["width_print_pdf"] = array("20%", "80%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("25", "80");
	$tableProperties["precision"] = array("string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Machine Model';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Machine_Model';
}


function MachineserialExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT b.MachineName,c.`MachineModelName`,a.MachineSerial
		FROM t_machineserial a
		INNER JOIN t_machine b on a.MachineId=b.MachineId
		INNER JOIN t_machinemodel c on a.MachineModelId=c.MachineModelId
		ORDER BY b.MachineName ASC, c.MachineModelName ASC, a.MachineSerial ASC;";

	$tableProperties["query_field"] = array("MachineName", "MachineModelName", "MachineSerial");
	$tableProperties["table_header"] = array('Machine Name', 'Machine Model Name', 'Machine Serial');
	$tableProperties["align"] = array("left", "left", "left");
	$tableProperties["width_print_pdf"] = array("20%", "80%", "80%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("20", "40", "40");
	$tableProperties["precision"] = array("string", "string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Machine Serial';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Machine_Serial';
}


function FeedbackExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;
	$UserId = $_REQUEST['UserId'];
	$Search = $_REQUEST['Search'];

	$sWhere = "";
		if ($Search === "Y") {
			$sWhere = " AND a.IsLinemanFeedback='Y' ";
		} else if ($Search === "N") {
			$sWhere = " AND a.IsLinemanFeedback='N' ";
		}

		$sql = "SELECT  
	 (case when a.CustomerId=38 then concat(c.CustomerName,'-',a.DummyCustomerDesc) else c.CustomerName end) CustomerName,

		DATE_FORMAT(a.TransactionDate, '%d-%b-%Y %h:%i:%s %p') AS VisitDate,g.UserName AS VisitorName,
		ifnull(b.DisplayName,'') AS Purpose, 
		ifnull(d.DisplayName,'') AS Transportation, ifnull(a.PublicTransportDesc,'') AS PublicTransportDesc,
		ifnull(a.SelfDiscussion,'') AS SelfDiscussion,ifnull(a.ConveyanceAmount,'') AS ConveyanceAmount, 
	 	ifnull(a.RefreshmentAmount,'') AS RefreshmentAmount,ifnull(a.DinnerBillAmount,'') AS DinnerBillAmount,	
		case when a.ApprovedRefreshmentAmount is null then a.RefreshmentAmount else a.ApprovedRefreshmentAmount end AS ApprovedRefreshmentAmount,
		case when a.ApprovedConveyanceAmount is null then a.ConveyanceAmount else a.ApprovedConveyanceAmount end AS ApprovedConveyanceAmount,
		case when a.ApprovedDinnerBillAmount is null then a.DinnerBillAmount else a.ApprovedDinnerBillAmount end AS ApprovedDinnerBillAmount,

		a.IsLinemanFeedback

	FROM t_transaction a
	inner join t_users g on a.UserId=g.UserId
	left join t_dropdownlist b on a.DropDownListIDForPurpose=b.DropDownListID
	left join t_customer c on a.CustomerId=c.CustomerId
	left join t_dropdownlist d on a.DropDownListIDForTransportation=d.DropDownListID
	left join t_machine e on a.MachineId=e.MachineId
	left join t_machinemodel f on a.MachineModelId=f.MachineModelId
	where (g.LinemanUserId=$UserId or $UserId=0)
	and a.TransactionTypeId=1
	and a.IsVisitorFeedback='Y'
	$sWhere
	and (a.ConveyanceAmount>0 OR a.RefreshmentAmount>0 OR a.ApprovedRefreshmentAmount>0 OR a.ApprovedConveyanceAmount>0 OR a.DinnerBillAmount>0 OR a.ApprovedDinnerBillAmount>0)
	ORDER BY a.TransactionDate DESC;";


	$tableProperties["query_field"] = array("CustomerName", "VisitDate","VisitorName", "Purpose","Transportation","PublicTransportDesc","SelfDiscussion","ConveyanceAmount","RefreshmentAmount","DinnerBillAmount","ApprovedConveyanceAmount","ApprovedRefreshmentAmount","ApprovedDinnerBillAmount","IsLinemanFeedback");
	$tableProperties["table_header"] = array('Customer Name', 'Visit Date','Employee', 'Purpose','Transportation','Transportation Description','Discussion','Conveyance','Refreshment','Dinner Bill','Approved Conveyance','Approved Refreshment','Approved Dinner Bill','Approved');
	$tableProperties["align"] = array("left", "left", "left");
	$tableProperties["width_print_pdf"] = array("10%", "10%", "10%", "10%", "10%", "10%", "10%","10%", "10%", "10%", "10%", "5%","5%", "5%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("25", "22", "20","20", "15", "15", "15", "15", "15","16", "20", "20", "20", "12");
	$tableProperties["precision"] = array("string", "string","string", "string", "string", "string", "string",2,2,2,2, 2,2, "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0,0,0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0,0,0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Feedback';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Feedback';
}























function ClientExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;

	$sql = "SELECT a.CustomerCode,a.CustomerName,a.CompanyAddress, a.NatureOfBusiness, a.CompanyName, 
	a.Designation,  a.ContactPhone,	a.CompanyEmail
		FROM t_customer a
		ORDER BY a.CustomerName ASC;";

	$tableProperties["query_field"] = array("CustomerCode", "CustomerName", "CompanyAddress", "NatureOfBusiness", "CompanyName", "Designation",  "ContactPhone", "CompanyEmail");
	$tableProperties["table_header"] = array('Code', 'Client Name', 'Address', 'Type', 'Contact Person', 'Designation', 'Phone', 'Email');
	$tableProperties["align"] = array("left", "left", "left", "left", "left", "left", "left", "left");
	$tableProperties["width_print_pdf"] = array("10%", "10%", "10%", "10%",  "10%", "10%", "10%", "10%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("18", "30", "30", "20",  "20", "20", "20", "20");
	$tableProperties["precision"] = array("string", "string", "string", "string", "string", "string", "string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0, 0, 0, 0, 0, 0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0, 0, 0, 0, 0, 0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Client Information';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Client_Information';
}


function ReferenceExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;

	$ClientId = $_REQUEST['ClientId'];
	$sql = "SELECT a.`ReferenceCode`,a.ReferenceName,a.Phone,a.Email,a.Commission,a.Address
	FROM t_reference a
	where a.ClientId=$ClientId 
	ORDER BY a.ReferenceCode;";

	$tableProperties["query_field"] = array("ReferenceCode", "ReferenceName", "Phone", "Email", "Commission", "Address");
	$tableProperties["table_header"] = array('Reference Code', 'Reference Name', 'Phone', 'Email', 'Commission', 'Address');
	$tableProperties["align"] = array("left", "left", "left", "left", "right", "left");
	$tableProperties["width_print_pdf"] = array("10%", "20%", "10%", "10%", "10%", "40%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("20", "25", "20", "20", "20", "40");
	$tableProperties["precision"] = array("string", "string", "string", "string", 2, "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0, 0, 0, 0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0, 0, 0, 0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Reference Information';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Reference_Information';
}


function RoleExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;

	// $ClientId = $_REQUEST['ClientId'];
	$sql = "SELECT a.`RoleName`,a.DefaultRedirect
	FROM t_roles a
	ORDER BY a.RoleName;";

	$tableProperties["query_field"] = array("RoleName", "DefaultRedirect");
	$tableProperties["table_header"] = array('Role Name', 'Default Redirect');
	$tableProperties["align"] = array("left", "left");
	$tableProperties["width_print_pdf"] = array("30%", "70%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("30", "40");
	$tableProperties["precision"] = array("string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Role Information';
	// $tableProperties["header_list"][1] = 'Heading 2';

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Role_Information';
}




function RoleToMenuPermissionExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;

	$ClientId = $_REQUEST['ClientId'];
	$BranchId = $_REQUEST['BranchId'];
	$RoleId = $_REQUEST['RoleId'];
	$RoleName = $_REQUEST['RoleName'];


	$sql = "SELECT case WHEN b.PermissionType = 1 THEN 'View' ELSE 'Edit' END PermissionType,
	IF(MenuLevel='menu_level_2',CONCAT(' -', a.MenuTitle),
		IF(MenuLevel='menu_level_3',CONCAT(' --', a.MenuTitle),a.MenuTitle)) menuname,MenuType

			   FROM `t_menu` a
			   LEFT JOIN t_role_menu_map b ON b.`MenuId` = a.`MenuId` 
				AND b.ClientId = $ClientId 
				AND b.BranchId = $BranchId 
				and b.RoleId = $RoleId
		ORDER BY MenuType DESC, SortOrder ASC;";

	$tableProperties["query_field"] = array("PermissionType", "menuname", "MenuType");
	$tableProperties["table_header"] = array('Access', 'Menu Name', 'Menu For');
	$tableProperties["align"] = array("left", "left");
	$tableProperties["width_print_pdf"] = array("20%", "50%", "20%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("20", "50", "20");
	$tableProperties["precision"] = array("string", "string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1
	// exit;
	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Role To Menu Permission Information';
	$tableProperties["header_list"][2] = $RoleName;

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Role_To_Menu_Permission_Information';
}






// function ClientExport()
// {

// 	global $sql, $tableProperties, $TEXT, $siteTitle;

// 	// $ClientId = $_REQUEST['ClientId'];
// 	$sql = "SELECT a.ClientCode, a.ClientName, a.PhoneNo, a.Email, a.ClientAddress
// 		FROM t_client a
// 		ORDER BY a.ClientCode ASC;";


// 	$tableProperties["query_field"] = array("ClientCode", "ClientName", "PhoneNo", "Email", "ClientAddress");
// 	$tableProperties["table_header"] = array('Client Code', 'Client Name', 'Phone', 'Email', 'Address');
// 	$tableProperties["align"] = array("left", "left", "left", "right", "left");
// 	$tableProperties["width_print_pdf"] = array("10%", "20%", "20%", "10%", "10%"); //when exist serial then here total 95% and 5% use for serial
// 	$tableProperties["width_excel"] = array("15", "20", "18", "18", "30");
// 	$tableProperties["precision"] = array("string", "string", "string", "string", "string"); //string,date,datetime,0,1,2,3,4
// 	$tableProperties["total"] = array(0, 0, 0, 0, 0); //not total=0, total=1
// 	$tableProperties["color_code"] = array(0, 0, 0, 0, 0); //colorcode field = 1 not color code field = 0
// 	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
// 	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

// 	//Report header list
// 	$tableProperties["header_list"][0] = $siteTitle;
// 	$tableProperties["header_list"][1] = 'Client List';
// 	// $tableProperties["header_list"][2] = "Date from ". $StartDate . " to ".$EndDate;

// 	//Report save name. Not allow any type of special character
// 	$tableProperties["report_save_name"] = 'Client_List';
// }



function BranchExport()
{

	global $sql, $tableProperties, $TEXT, $siteTitle;

	$ClientId = $_REQUEST['ClientId'];

	$sql = "SELECT a.BranchName, a.PhoneNo, a.Email, a.BranchAddress
		FROM t_branch a
		where a.ClientId=$ClientId
		ORDER BY a.BranchName ASC;";

	$tableProperties["query_field"] = array("BranchName", "PhoneNo", "Email", "BranchAddress");
	$tableProperties["table_header"] = array('Branch Name', 'Phone', 'Email', 'Address');
	$tableProperties["align"] = array("left", "left", "left", "right");
	$tableProperties["width_print_pdf"] = array("10%", "20%", "20%", "10%"); //when exist serial then here total 95% and 5% use for serial
	$tableProperties["width_excel"] = array("20", "18", "18", "30");
	$tableProperties["precision"] = array("string", "string", "string", "string"); //string,date,datetime,0,1,2,3,4
	$tableProperties["total"] = array(0, 0, 0, 0); //not total=0, total=1
	$tableProperties["color_code"] = array(0, 0, 0, 0); //colorcode field = 1 not color code field = 0
	$tableProperties["header_logo"] = 0; //include header left and right logo. 0 or 1
	$tableProperties["footer_signatory"] = 0; //include footer signatory. 0 or 1

	//Report header list
	$tableProperties["header_list"][0] = $siteTitle;
	$tableProperties["header_list"][1] = 'Branch List';
	// $tableProperties["header_list"][2] = "Date from ". $StartDate . " to ".$EndDate;

	//Report save name. Not allow any type of special character
	$tableProperties["report_save_name"] = 'Branch_List';
}














//==================================================================================
//=================Dynamic Export Print, Excel, Pdf, CSV============================
//==================================================================================


$db = new Db();

//Execute sql command
$result = $db->query($sql);



$serial = 0;
$useSl = 1;
$columnTotalList = array();
$reportHeaderList = $tableProperties["header_list"];

$reportType = $_REQUEST['reportType'];
$reportSaveName = str_replace(' ', '_', $tableProperties["report_save_name"]);


//Table Header Start
if ($reportType == 'print' || $reportType == 'pdf') {

	echo '<!DOCTYPE html>
		<html>
			 <head>
				<meta name="viewport" content="width=device-width, initial-scale=1.0" />	
				<meta http-equiv="content-type" content="text/html; charset=utf-8" />
				<link href="css/bootstrap.min.css" rel="stylesheet"/>
				<link href="css/font-awesome.min.css" rel="stylesheet"/>
				<link href="css/custom.css" rel="stylesheet"/>
				<link href="css/base.css" rel="stylesheet"/>
				<link href="css/exportstyle.css" rel="stylesheet"/>
			<style>
				body {
					color:#727272;
				}
				table.display tr.even.row_selected td {
    				background-color: #4DD4FD;
			    }    
			    table.display tr.odd.row_selected td {
			    	background-color: #4DD4FD;
			    }
			    .SL{
			        text-align: center !important;
			    }
				.right-aln{
					text-align: right !important;
				}
				.left-aln{
					text-align: left !important;
				}
				.center-aln {
					text-align: center !important;
				}
			    td.Countries{cursor: pointer;}
			    th, td {
					border: 1px solid #e4e4e4 !important;
				}
				.margin-bottom {
					margin-bottom: 40px;
				}


				.BottomDiv{
					width:30%;
					text-align:center;
					display: block;
				}
				.content_area {
						text-align: center;
						font-size: 14px;
						font-weight: bold;
					}
	
					.margin_top{
						margin-top: 10px;
					}
					.margin_button{
						margin-bottom:120px;
					}
					.footer_Padding{
						border: 1px solid #ccc;
						min-height: 130px;
						padding-top: 10px;
					}
					.marginTop0{
						margin-top: 0px;
					}
			</style>
			</head>
			<body><div class="container-fluid margin-bottom">
			<div class="row"> 
			<div class="col-md-12">
          	<div class="table-responsive">
           	<div class="panel-heading" style="text-align:center;">';

	$reportHeaderListCount = count($reportHeaderList);

	//Report Header
	for ($i = 0; $i < $reportHeaderListCount; $i++) {
		if ($i == 0) {
			if ($tableProperties["header_logo"] == 1) {
				echo '<div class="row margin_top">

						<div class="col-md-4 col-sm-4 col-lg-4">
							<div class="content-body" style="text-align:left;">
							<img src="images/logo.png" alt="National health family logo" style="width: 90px;height: auto;">
							</div>
						</div>

						<div class="col-md-4 col-sm-4 col-lg-4">
							<div class="content-body_text">
								<div class="content_area">
								
									<h4>' . $reportHeaderList[$i] . '</h4>
									
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-4 col-lg-4">
							<div class="content-body">
							<img src="images/benin_logo.png" alt="National health family logo" style="float: right;width: 65px;height: auto;">
							</div>
						</div>
					</div>';
			} else {
				echo '<div class="row margin_top">

						<div class="col-md-12 col-sm-12 col-lg-12">
							<div class="content-body_text">
								<div class="content_area">
								
									<h4>' . $reportHeaderList[$i] . '</h4>
									
								</div>
							</div>
						</div>

					</div>';
			}
		}


		//echo '<h2>'.$reportHeaderList[$i].'</h2>';
		else if ($i == 1)
			echo '<h5 class="marginTop0">' . $reportHeaderList[$i] . '</h5>';
		else
			echo '<h5>' . $reportHeaderList[$i] . '</h5>';
	}

	echo '</div>';


	$fontsize = "";
	if ($reportType == 'pdf') {
		$fontsize = "font-size:10px;";
	}
	echo '<table class="table table-striped table-bordered display" cellspacing="0" cellpadding="5" width="100%" border="0.5" style="margin:0 auto; ' . $fontsize . '">    	
				<tbody><tr>';

	if ($useSl > 0) {
		echo '<th style="width:5%; text-align:center;"><strong>SL.</strong></th>';
	}

	foreach ($tableProperties["table_header"] as $index => $header) {
		echo '<th style="width:' . $tableProperties["width_print_pdf"][$index] . '; text-align:' . $tableProperties["align"][$index] . ';"><strong>' . $header . '</strong></th>';
	}
	echo '</tr>';
} else if ($reportType == 'excel') {

	//include xlsxwriter
	set_include_path(get_include_path() . PATH_SEPARATOR);
	include_once("xlsxwriter/xlsxwriter.class.php");

	///////////for logo left and right header 29/03/2023
	require_once("xlsxwriter/xlsxwriterplus.class.php");
	///////////for logo left and right header 29/03/2023


	$sheetName = "Data";
	$rowStyle = array('border' => 'left,right,top,bottom', 'border-style' => 'thin');

	///////////for logo left and right header 29/03/2023. off first line and add 2nd line
	// $writer = new XLSXWriter();
	$writer = new XLSWriterPlus();
	///////////for logo left and right header 29/03/2023

	$tableHeaderList = array();

	if ($useSl > 0) {
		$tableHeaderList["SL."] = '0';
		array_unshift($tableProperties["width_excel"], 8);
	}

	foreach ($tableProperties["table_header"] as $index => $header) {

		$header = remove_html_tag($header);

		if (is_numeric($tableProperties["precision"][$index])) {
			// $tableHeaderList[$fieldLabelList[getActualFieldName($val)]] = '0.0';
			$precision = $tableProperties["precision"][$index];
			$format = "#,##0";
			if ($precision > 0) {
				$decimalPoint = ".";
				$decimalPoint = str_pad($decimalPoint, ($precision + 1), "0", STR_PAD_RIGHT);
				$format = "#,##0" . $decimalPoint;
			}
			// $tableHeaderList[$fieldLabelList[getActualFieldName($val)]] = '#,##0.0';
			$tableHeaderList[$header] = $format;
		} else {
			$tableHeaderList[$header] = '@';
		}
	}

	//For multiline report title 13/11/2022
	$reporttitle = $reportHeaderList[0];
	$reporttitlelist = explode('<br/>', $reporttitle);
	if (count($reporttitlelist) > 1) {
		$reportHeaderList[0] = $reporttitlelist[count($reporttitlelist) - 1];
		for ($h = (count($reporttitlelist) - 2); $h >= 0; $h--) {
			array_unshift($reportHeaderList, $reporttitlelist[$h]);
		}
	}
	//For multiline report title 13/11/2022


	////////last column width max 8 because of logo width 29/03/2023
	if ($tableProperties["header_logo"] == 1) {
		$lastcolw = $tableProperties["width_excel"][count($tableProperties["width_excel"]) - 1];
		if ($lastcolw > 8) {
			$tableProperties["width_excel"][count($tableProperties["width_excel"]) - 1] = 10;
		}
	}
	////////last column width max 8 because of logo width 29/03/2023


	$writer->writeSheetHeader(
		$sheetName,
		$tableHeaderList,
		array(
			// 'widths'=>array(5,20,20,20,20,20,15,15,15,15,15,10,10),
			'widths' => $tableProperties["width_excel"],
			'font-style' => 'bold',
			'font-size' => 11,
			'fill' => '#b4c6e7',
			'border' => 'left,right,top,bottom',
			'border-style' => 'thin',
			'halign' => 'left',
			'fitToWidth' => '1',
			// 'report_headers'=>array('Health Comodity Mangement','Stock status data', 'Year: 2018, Month: January')
			'report_headers' => $reportHeaderList
		)
	);
	//Report Header and table header end
} else if ($reportType == 'csv') {

	$writer = WriterFactory::create(Type::CSV);
	$writer->openToFile("media/$reportSaveName.csv");

	//Report Header start
	foreach ($reportHeaderList as $val) {
		$writer->addRow([$val]);
	}
	//Report Header end
	//Table Header start
	$tableHeaderList = array();
	if ($useSl > 0) {
		$tableHeaderList[] = "SL.";
	}

	foreach ($tableProperties["table_header"] as $index => $header) {
		$tableHeaderList[] = $header;
	}
	$writer->addRow($tableHeaderList); //$writer->addRow(['A','B']);
	//Table Header end
}
//Table Header End
//Data list start
foreach ($result as $row) {
	if ($reportType == 'print' || $reportType == 'pdf') {
		echo '<tr>';

		if ($useSl > 0) {
			echo '<td style="width:5%; text-align:center;">' . ++$serial . '</td>';
		}

		foreach ($tableProperties["query_field"] as $index => $fieldName) {

			if ($tableProperties["color_code"][$index] == 1) {
				echo '<td style="width:' . $tableProperties["width_print_pdf"][$index] . '; background-color:' . $row[$fieldName] . ';"></td>';
			} else {
				echo '<td style="width:' . $tableProperties["width_print_pdf"][$index] . '; text-align:' . $tableProperties["align"][$index] . ';">' . getValueFormat($row[$fieldName], $tableProperties["precision"][$index], $reportType) . '</td>';
			}

			if ($tableProperties["total"][$index] == 1) {
				if (array_key_exists($index, $columnTotalList)) {
					$columnTotalList[$index] += $row[$fieldName];
				} else {
					$columnTotalList[$index] = $row[$fieldName];
				}
			} else {
				$columnTotalList[$index] = "";
			}
		}
		echo '</tr>';
	} else if ($reportType == 'excel') {

		$isColorCode = false;
		if (in_array(1, $tableProperties["color_code"])) {
			$isColorCode = true;
		}

		$rowStyleModify = array();
		$rowStyleModify = $rowStyle;

		$rowdata = array();
		if ($useSl > 0) {
			$rowdata[] = ++$serial;

			if ($isColorCode) {
				$rowStyleModify[] = ['fill' => ''];
			}
		}

		foreach ($tableProperties["query_field"] as $index => $fieldName) {

			if ($tableProperties["color_code"][$index] == 1) {
				$rowStyleModify[] = ['fill' => $row[$fieldName]];
				$rowdata[] = "";
			} else {
				if ($isColorCode) {
					$rowStyleModify[] = ['fill' => ''];
				}
				$rowdata[] = getValueFormat(remove_html_tag($row[$fieldName]), $tableProperties["precision"][$index], $reportType);
			}


			if ($tableProperties["total"][$index] == 1) {
				if (array_key_exists($index, $columnTotalList)) {
					$columnTotalList[$index] += $row[$fieldName];
				} else {
					$columnTotalList[$index] = $row[$fieldName];
				}
			} else {
				$columnTotalList[$index] = "";
			}
		}

		$writer->writeSheetRow($sheetName, $rowdata, $rowStyleModify);
	} else if ($reportType == 'csv') {
		$rowdata = array();
		if ($useSl > 0) {
			$rowdata[] = ++$serial;
		}

		foreach ($tableProperties["query_field"] as $index => $fieldName) {
			$rowdata[] = getValueFormat($row[$fieldName], $tableProperties["precision"][$index], $reportType);

			if ($tableProperties["total"][$index] == 1) {
				if (array_key_exists($index, $columnTotalList)) {
					$columnTotalList[$index] += $row[$fieldName];
				} else {
					$columnTotalList[$index] = $row[$fieldName];
				}
			} else {
				$columnTotalList[$index] = "";
			}
		}
		$writer->addRow($rowdata);
	}
}
//Data list end

if ($reportType == 'print' || $reportType == 'pdf') {

	if (in_array(1, $tableProperties["total"])) {
		echo '<tr>';

		if ($useSl > 0) {
			echo '<td></td>';
		}

		foreach ($columnTotalList as $index => $totalValue) {
			echo '<td style="width:' . $tableProperties["width_print_pdf"][$index] . '; text-align:' . $tableProperties["align"][$index] . ';">' . getValueFormat($totalValue, $tableProperties["precision"][$index], $reportType) . '</td>';
		}
		echo '</tr>';
	}

	echo '</tbody></table>';

	if ($tableProperties["footer_signatory"] == 1) {
		echo	'<div class="row margin_top">
						<div class="col-md-12 col-lg-12">
							<div class="footer_Padding">
						
								<div class="col-md-6 col-lg-6">
									<div class="footer_section">
										<p> ' . $TEXT["Nom et signature du gestionnaire"] . ' </p>
									</div>
								</div>	
								<div class="col-md-6 col-lg-6">
									<div class="footer_section text-right">
										<p> ' . $TEXT["Nom et signature du responsable du site de PEC"] . ' </p>
									</div>
								</div> 
							</div>
						</div>
					</div>';
	}


	echo '	</div>
				</div>   
				</div>  
			 </div>
		 </body></html>';
} else if ($reportType == 'excel') {

	if (in_array(1, $tableProperties["total"])) {
		$rowTotalStyle = array('font-style' => 'bold', 'border' => 'left,right,top,bottom', 'border-style' => 'thin');
		$rowdata = array();

		if ($useSl > 0) {
			$rowdata[] = "";
		}

		foreach ($columnTotalList as $index => $totalValue) {
			$rowdata[] = getValueFormat($totalValue, $tableProperties["precision"][$index], $reportType);
		}
		$writer->writeSheetRow($sheetName, $rowdata, $rowTotalStyle);
	}

	/* Report header merge */
	$end_col = count($tableProperties["query_field"]) - 1; //column count - 1
	if ($useSl > 0) {
		$end_col++;
	}

	foreach ($reportHeaderList as $start_row => $val) {
		$writer->markMergedCell($sheetName, $start_row, $start_col = 0, $end_row = $start_row, $end_col);
		// $writer->markMergedCell($sheetName, $start_row=0, $start_col=0, $end_row=0, $end_col=12);
		// $writer->markMergedCell($sheetName, $start_row=1, $start_col=0, $end_row=1, $end_col=12);
	}



	///////////for logo left and right header 29/03/2023
	if ($tableProperties["header_logo"] == 1) {
		$writer->addImage($sheetName, realpath('./images/logo.png'), 'logo.png', 1, [
			'startColNum' => 0,
			'endColNum' => 1,
			'startRowNum' => 0,
			'endRowNum' => 3,
		]);
		// $columnTotalCount = count($columnTotalList);
		$columnTotalCount = count($tableProperties["query_field"]);

		$writer->addImage($sheetName, realpath('./images/benin_logo.png'), 'benin_logo.png', 2, [
			'startColNum' => $columnTotalCount,
			'endColNum' => ($columnTotalCount + 1),
			'startRowNum' => 0,
			'endRowNum' => 3,
		]);
	}
	///////////for logo left and right header 29/03/2023



	/////////////////for footer signator//////////////////////// 29/03/2023/////////////////
	if ($tableProperties["footer_signatory"] == 1) {
		// $writer->writeSheetRow($sheetName, [], []);/// for a blank row

		// $rowdata = array();
		// $rowdata[] = 'Nom et signature du gestionnaire';
		// // $rowdata[3] = 'Nom et signature du responsable du site de PEC';

		// $rowTypeOverwrite = array();
		$middleColIdx = (int)(count($tableProperties["table_header"]) / 2);
		// for($f=0; $f<count($tableProperties["table_header"]); $f++){
		// 	$rowTypeOverwrite[] = 'string';

		// 	if($f == $middleColIdx){
		// 		$rowdata[] = 'Nom et signature du responsable du site de PEC';
		// 	}
		// 	else{
		// 		$rowdata[] = '';
		// 	}

		// }
		// $writer->setSheetColumnTypes($sheetName,['string','string','string','string','string']);
		// $writer->setSheetColumnTypes($sheetName,$rowTypeOverwrite);

		// $rowTotalStyle = array('font-style' => 'normal', 'border' => 'left,right,top,bottom', 'border-style' => 'thin','height'=>'80','valign'=>'top');
		// $writer->writeSheetRow($sheetName, $rowdata, $rowTotalStyle);

		// echo 'Rubel:';
		$footerrowposition = 0;
		if (in_array(1, $tableProperties["total"])) {
			//when total available
			$footerrowposition = count($reportHeaderList) + ($tableHearC = 1) + count($result) + ($tableTotalC = 1) + ($gaprowC = 1);
		} else {
			//when total not available
			$footerrowposition = count($reportHeaderList) + ($tableHearC = 1) + count($result) + ($gaprowC = 1);
		}


		// $rowTotalStyle = array('font-style' => 'normal', 'border' => 'left,right,top,bottom', 'border-style' => 'thin','height'=>'80','valign'=>'top','halign'=>'left');
		$rowTotalStyle = array('height' => '80', 'valign' => 'top', 'halign' => 'left');
		$writer->writeCellextra($sheetName, $footerrowposition, 0, $TEXT["Nom et signature du gestionnaire"], $rowTotalStyle);

		$rowTotalStyle = array('height' => '80', 'valign' => 'top', 'halign' => 'right');
		$writer->writeCellextra($sheetName, $footerrowposition, ($middleColIdx + 1), $TEXT["Nom et signature du responsable du site de PEC"], $rowTotalStyle);

		$writer->markMergedCell($sheetName, $footerrowposition, $start_col = 0, $end_row = $footerrowposition, ($middleColIdx));
		$writer->markMergedCell($sheetName, $footerrowposition, $start_col = ($middleColIdx + 1), $end_row = $footerrowposition, count($tableProperties["table_header"]));


		///////// $writer->writeCellextra($sheetName,75, 0, 'Rubel');
		///////// $writer->writeCellextra($sheetName,75, 5, 'Softworks');
	}



	/////////////////for footer signator//////////////////////// 29/03/2023/////////////////



	$exportTime = date("Y_m_d_H_i_s", time());
	$exportFilePath = $reportSaveName . '_' . $exportTime . ".xlsx";
	$writer->writeToFile("media/$exportFilePath");
	header('Location:media/' . $exportFilePath); //File open location	

} else if ($reportType == 'csv') {

	if (in_array(1, $tableProperties["total"])) {
		$rowdata = array();

		if ($useSl > 0) {
			$rowdata[] = "";
		}

		foreach ($columnTotalList as $index => $totalValue) {
			$rowdata[] = getValueFormat($totalValue, $tableProperties["precision"][$index], $reportType);
		}
		$writer->addRow($rowdata);
	}

	$writer->close();
	// header('Location:../media/' . $reportSaveName . ".csv"); //File open location

	$exportTime = date("Y_m_d_H_i_s", time());
	$exportFilePath = $reportSaveName . '_' . $exportTime . ".xlsx";
	$writer->writeToFile("media/$exportFilePath");
	header('Location:media/' . $exportFilePath); //File open location			
}


function getValueFormat($value, $precision, $reportType)
{
	$retVal = "";

	if ($precision === 'date') {
		if (validateDate($value, 'Y-m-d')) {
			$retVal = date('d-m-Y', strtotime($value));
		} else {
			$retVal = $value;
		}
	} else if ($precision === 'datetime') {
		if (validateDate($value, 'Y-m-d H:i:s')) {
			$retVal = date('d-m-Y', strtotime($value));
		} else {
			$retVal = $value;
		}
	} else if ($precision === 'string') {
		$retVal = $value;
	} else if (is_numeric($precision)) {
		if ($value == "") {
			$retVal = "";
		} else {
			// $retVal = getNumberFormat($value, $precision);
			$retVal = number_format($value, $precision);
			//when Excel then no need to COMA it is will auto format
			if ($reportType === 'excel') {
				$retVal = str_replace(",", "", $retVal);
			}
		}
	} else {
		$retVal = $value;
	}

	return $retVal;
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function remove_html_tag($text)
{
	if ($text == "") {
		return "";
	} else {
		$text = str_replace('<', ' < ', $text);
		return trim(strip_tags($text));
	}
}
