<?php
preg_match("/[^\/]+$/", $_SERVER["REQUEST_URI"], $matches);
$PageNameWithParams = $matches[0];
$PageName = explode("?",$PageNameWithParams)[0];

$data = array();
if($_SERVER["REQUEST_METHOD"] == "GET"){
	$data = $_REQUEST;	
}
else if($_SERVER["REQUEST_METHOD"] == "POST"){
	$data = (array)json_decode(file_get_contents("php://input"));
}

require('../api/pdolibs/pdo_lib.php');
require('pages/global_methods.php');
include("pages/".$PageName.".php");