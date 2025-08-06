<?php
// echo 854;
// header('Content-Type:multipart/form-data');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
	//$targetDir = "./../../../media/";

	if (($_POST["formName"]) == "userProfile") {
		$targetDir = "./../../../image/user/";
	}
	else if (($_POST["formName"]) == "receive") {
		$targetDir = "./../../../image/receive/";
	}else if (($_POST["formName"]) == "transactionImg") {
		$targetDir = "./../../../image/transaction/";
	}


	$timestamp = $_POST["timestamp"];


	if (!is_dir($targetDir)) {
		mkdir($targetDir, 0755, true);
	}

	$fileName = $timestamp . "_" . basename($_FILES["file"]["name"]);
	$targetFilePath = $targetDir . $fileName;
	move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath);


	if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "File uploaded successfully.",
			"filename" =>  $fileName
		];
	} else {
		$returnData = [
			"success" => 0,
			"status" => 401,
			"message" => "Error uploading file.",
			"filename" =>  $fileName
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
