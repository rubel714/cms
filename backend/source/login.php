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


require __DIR__ . '/classes/Database.php';
require __DIR__ . '/classes/JwtHandler.php';

$db_connection = new Database();
$conn = $db_connection->dbConnection();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];
$menu_arr = array();

// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = msg(0, 404, 'Page Not Found!');
}
// CHECKING EMPTY FIELDS
else if (
    // !isset($data->ClientId)
    // || empty(trim($data->ClientId))

    // || !isset($data->BranchId)
    // || empty(trim($data->BranchId))

    // || 
    !isset($data->email)
    || empty(trim($data->email))

    || !isset($data->password)
    || empty(trim($data->password))
) {

    // $fields = ['fields' => ['Client', 'email', 'password']];
    $fields = ['fields' => ['email', 'password']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
}
// IF THERE ARE NO EMPTY FIELDS THEN-
else {
    // $ClientId = trim($data->ClientId);
	// $BranchId = isset($data->BranchId) && ($data->BranchId !== "") ? $data->BranchId : 0;
    $ClientId = 0;
    $BranchId = 0;
    $Email = trim($data->email);
    $Passowrd = trim($data->password);

    $UserId = 0;
    $RoleId = 0;
    $memuList = array();

    try{
        $userRow = array();

        // $sql = "SELECT * FROM t_users 
        //     WHERE ClientId=:ClientId 
        //     AND (Email=:Email OR LoginName =:Email ) 
        //     AND IsActive=1";
    
        $sql = "SELECT * FROM t_users WHERE LoginName=:Email";
        $query_u = $conn->prepare($sql);
        // $query_u->bindValue(':ClientId', $ClientId, PDO::PARAM_STR);
        $query_u->bindValue(':Email', $Email, PDO::PARAM_STR);
        $query_u->execute();


        if($query_u->rowCount() === 0){
            $returnData = msg(0,422,'User not found!');
            echo json_encode($returnData);
            return;

        }else/* if($query_u->rowCount() === 1)*/{
            $uRow = $query_u->fetch(PDO::FETCH_ASSOC);

            $IsActive = $uRow['IsActive'];
            if($IsActive == 0){
                $returnData = msg(0,422,'User is inactive. Please contact with administrator!');
                echo json_encode($returnData);
                return;
            }

            $UserId = $uRow['UserId'];
            $ClientId = $uRow['ClientId'];
            $BranchId = $uRow['BranchId'];
        }

        /*
        else if($query_u->rowCount() > 1){

            // $sql = "SELECT * FROM t_users
            // WHERE ClientId=:ClientId 
            // AND BranchId=:BranchId
            // AND (Email=:Email OR LoginName =:Email ) 
            // AND IsActive=1";

            $sql = "SELECT * FROM t_users WHERE LoginName=:Email AND IsActive=1";
            $query_u2 = $conn->prepare($sql);
            // $query_u2->bindValue(':ClientId', $ClientId, PDO::PARAM_STR);
            // $query_u2->bindValue(':BranchId', $BranchId, PDO::PARAM_STR);
            $query_u2->bindValue(':Email', $Email, PDO::PARAM_STR);
            $query_u2->execute();

            if($query_u2->rowCount() === 0){
                $returnData = msg(0,422,'User not found!');
                echo json_encode($returnData);
                return;
            }else{
                $uRow = $query_u2->fetch(PDO::FETCH_ASSOC);
                $UserId = $uRow['UserId'];
                $ClientId = $uRow['ClientId'];
                $BranchId = $uRow['BranchId'];
            }

        }
*/




        $sql = "SELECT a.`UserId`,a.`ClientId`,a.`BranchId`,a.`UserName`,a.`LoginName`,a.`Email`,a.`Password`,
        a.`DesignationId`,a.`IsActive`,a.PhotoUrl,b.`ClientName`,b.ClientCode,c.`BranchName`,c.BranchAddress,c.PhoneNo
            FROM `t_users` a
            LEFT JOIN t_client b ON a.ClientId=b.ClientId
            LEFT JOIN t_branch c ON a.BranchId=c.BranchId
            WHERE a.UserId=:UserId";

        $query_stmt = $conn->prepare($sql);
        $query_stmt->bindValue(':UserId', $UserId, PDO::PARAM_STR);
        $query_stmt->execute();
        $userRow = $query_stmt->fetch(PDO::FETCH_ASSOC);

        $fetch_userrole_by_user = "SELECT a.RoleId,b.RoleName,b.DefaultRedirect
        FROM `t_user_role_map` a
        inner join t_roles b on a.RoleId=b.RoleId 
        WHERE a.`UserId`=:UserId";
        $query_stmtrole = $conn->prepare($fetch_userrole_by_user);
        $query_stmtrole->bindValue(':UserId', $UserId,PDO::PARAM_STR);
        $query_stmtrole->execute();
        
        $rowrole = $query_stmtrole->fetch(PDO::FETCH_ASSOC);

        if($rowrole){
            $RoleId = $rowrole['RoleId'];
            $userRow["RoleId"] = array($RoleId);
            $userRow["DefaultRedirect"] = $rowrole['DefaultRedirect'];

        }else{
            $RoleId = 0;
            $userRow["RoleId"] = [];
            $userRow["DefaultRedirect"] = "/home";

        }

        //when maintenance mode on then only Admin role user able to login
        if($query_stmt->rowCount()){
            
            if(loginonlyadmin==1){

                //role id = 1 = Super Admin. If site is maintenance mode then only super admin able to login
                if($RoleId != 1){
                    $returnData = [
                        "success" => 0,
                        "status" => 404,
                        "message" => "Site is under maintenance mode."
                    ];

                    echo json_encode($returnData);
                    return;
                }
                
            }
        }



         // IF THE USER IS FOUNDED
         if($query_stmt->rowCount()){
            $check_password = password_verify($Passowrd, $userRow['Password']);

            //if passord is valid
            if($check_password){

                $jwt = new JwtHandler();
                $token = $jwt->_jwt_encode_data(
                    'http://localhost/php_auth_api/',
                    array("UserId"=> $UserId)
                );

                

                $query = "SELECT DISTINCT a.MenuId,a.MenuKey,a.MenuTitle,a.Url,a.ParentId,a.MenuLevel,b.PermissionType,a.SortOrder
                FROM t_menu a
                INNER JOIN t_role_menu_map b ON a.MenuId=b.MenuId 
                and b.ClientId=$ClientId 
                and (b.BranchId=$BranchId OR $BranchId=0)
                and b.RoleId = $RoleId
                order by a.SortOrder ASC;";

                $dataMenu = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
                if(count($dataMenu)==0){
                    $returnData = [
                        "success" => 0,
                        "status" => 404,
                        "message" => "You have no assigned any menu"
                    ];
                }
                else{

                    foreach($dataMenu as $key => $menu) {
                        $memuList[] = $menu;
                    }

                }

                $userRow["UserAllowdMenuList"] = $memuList;
                $userRow["Password"] = "HIDDEN";
                $returnData = [
                    'success' => 1,
                    'message' => 'You have successfully logged in.',
                    'token' => $token,
                    'user_data' => $userRow
                ];

            }
            else{
                $returnData = msg(0,422,'Invalid Password!');
            }

         }
         else{
            $returnData = msg(0,422,'User not found!');
         }






    }
    catch(PDOException $e){
        $returnData = msg(0,500,$e->getMessage());
    }






}

echo json_encode($returnData);
