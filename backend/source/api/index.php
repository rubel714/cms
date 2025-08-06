<?php

if(isset($_SERVER["HTTP_ORIGIN"])){
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}else{
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if($_SERVER["REQUEST_METHOD"] == "OPTIONS"){
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

// INCLUDING DATABASE AND MAKING OBJECT
require __DIR__.'/../classes/Database.php';
require __DIR__.'/../middlewares/Auth.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$auth = new Auth($conn, $allHeaders);

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];


if($auth->isAuth()){

    
	preg_match("/[^\/]+$/", $_SERVER["REQUEST_URI"], $matches);
	$PageName = $matches[0];
    if(isset($data->params)){ 
    
        // $tempDataarr=(array)$data->params->queryKey[0];
        $tempDataarr=(array)$data->params;
        foreach($tempDataarr as $key=>$value) {
            $data->$key= $value; 
        }
       
    } 

	// $menukey = $data->menukey;    
	$lan = isset($data->lan)?$data->lan:'en_GB';
	require('pdolibs/pdo_lib.php');
	require('pdolibs/function_global.php');
	require('languages/lang_switcher_custom.php'); 
	include("api_pages/".$PageName.".php");
	

}else{

	preg_match("/[^\/]+$/", $_SERVER["REQUEST_URI"], $matches);
	$PageName = $matches[0];
    if(isset($data->params)){ 
    
        // $tempDataarr=(array)$data->params->queryKey[0];
        $tempDataarr=(array)$data->params;
        foreach($tempDataarr as $key=>$value) {
            $data->$key= $value; 
        }
       
    } 

	// $menukey = $data->menukey;    
	$lan = isset($data->lan)?$data->lan:'en_GB';
	require('pdolibs/pdo_lib.php');
	require('pdolibs/function_global.php');
	require('languages/lang_switcher_custom.php'); 
	include("api_pages/".$PageName.".php");
}

echo json_encode($returnData);

