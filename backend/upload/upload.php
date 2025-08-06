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
require __DIR__.'/../source/classes/Database.php';
require __DIR__.'/../source/middlewares/Auth.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$auth = new Auth($conn, $allHeaders);

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

/* $returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

if($auth->isAuth()){ */

// echo '<pre>';
// print_r($_FILES);
// exit;

	//Import File Directory
	$target_dir = "uploadedfiles/";

	if(is_array($_FILES)) {
		if(is_uploaded_file($_FILES['FileName']['tmp_name'])) {

			$tempFile = $_FILES['FileName']['tmp_name'];
			$targetPath = $target_dir;
			$path = $_FILES['FileName']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$fileString = $_FILES['FileName']['name']."_".time();
			$targetFile = str_replace('//', '/', $targetPath) . ($fileString.".".$ext);
			$FileName = $fileString.".".$ext;
			
			if (file_exists($targetFile)) {
				unlink($targetFile);
			}
			
			if (move_uploaded_file($tempFile, $targetFile)) {
				$msgList["msgType"] = 'success';
				$msgList['msg'] = 'The file uploaded Successfully';
				$msgList["FileName"] = $FileName;
				
			}else{
				$msgList["msgType"] = 'error';
				$msgList['msg'] = 'Sorry, there was an error uploading your file.';
				$msgList["FileName"] = '';
			}
		}else{
			$msgList["msgType"] = 'error';
			$msgList['msg'] = 'Sorry, there was an error uploading your file.';
			$msgList["FileName"] = '';
		}
		
		$returnData = $msgList;
	}

//}

echo json_encode($returnData);

