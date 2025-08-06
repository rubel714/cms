<?php
// Allow from any origin
if (isset($_SERVER["HTTP_ORIGIN"])) {
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}
function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

// INCLUDING DATABASE AND MAKING OBJECT
require __DIR__ . '/classes/Database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// echo "<pre>";
// print_r($_SERVER);
//$_SERVER["QUERY_STRING"]; action=BranchList&ClientId=2
// echo $_SERVER["QUERY_STRING"]; 
$params = explode("&", $_SERVER["QUERY_STRING"]);
$data = array();
foreach ($params as $p) {
    $pl = explode("=", $p);
    $data[$pl[0]] = $pl[1];
}
// print_r($data);

if ($data["action"] === "ClientList") {
    $sql = "SELECT  ClientId as id, ClientName as name
    FROM t_client
    WHERE IsActive = 1
    ORDER BY ClientName;";

} else if ($data["action"] === "BranchList") {
    $ClientId = isset($data["ClientId"]) ? $data["ClientId"] : 0;
    
    $sql = "SELECT BranchId as id,  BranchName as name
    FROM t_branch
    WHERE ClientId= $ClientId
    ORDER BY BranchName;";
}


$sth = $conn->prepare($sql);
$sth->execute();
$returnData = $sth->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($returnData);
