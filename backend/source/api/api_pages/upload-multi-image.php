<?php
// echo 854;
// header('Content-Type:multipart/form-data');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// echo "hello";
// echo "<pre>";
// print_r($_SERVER);
// echo "--------------------------------------------";
// print_r($_FILES);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES) && count($_FILES)>0) {
	//$targetDir = "./../../../media/";

	// if (($_POST["formName"]) == "userProfile") {
	// 	$targetDir = "./../../../image/user/";
	// }
	// else if (($_POST["formName"]) == "receive") {
	// 	$targetDir = "./../../../image/receive/";
	// }else if (($_POST["formName"]) == "transactionImg") {
		$targetDir = "./../../../image/transaction/";
	// }


	// $timestamp = $_POST["timestamp"];
	// $timestamp = $_POST["timestamp"];


	if (!is_dir($targetDir)) {
		mkdir($targetDir, 0755, true);
	}


	$chk = 1;
	foreach($_FILES as $key=>$FILE){
		// $timestamp = time();// $_POST["timestamp"];
		$fileName = $key . "_" . basename($FILE["name"]);
		$targetFilePath = $targetDir . $fileName;

		// echo "Path::".$targetFilePath;
		// // echo "=================";
		// echo ",tmp_name:".$FILE["tmp_name"];
		if(!(move_uploaded_file($FILE["tmp_name"], $targetFilePath))){
			$chk = 0;
		}
	}
	

	if ($chk == 1) {
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "File uploaded successfully.",
			"filename" =>  ""//$fileName
		];
	} else {
		$returnData = [
			"success" => 0,
			"status" => 401,
			"message" => "Error uploading file.",
			"filename" =>  ""//$fileName
		];
	}
} else {
	$returnData = [
		"success" => 0,
		"status" => 401,
		"message" => "Invalid request.",
		"filename" =>  ""
	];
}
