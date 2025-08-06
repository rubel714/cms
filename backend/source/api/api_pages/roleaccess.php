<?php
/*
$task = '';
if(isset($data->action)) {
	$task = trim($data->action);
}

switch($task){
	
	case "getRoleList":
		$returnData = getRoleList($data);
	break;
	case "getMenuLists":
		$returnData = getMenuLists($data);
	break;	
	case "geRole":
		$returnData = geRole($data);
	break;

	case "dataInsert":
		$returnData = dataInsert($data);
		break;

	case "getMenuShortOrderLists":
			$returnData = getMenuShortOrderLists($data);
		break;	

	case "dataUpdate":
			$returnData = dataUpdate($data);
			break;

	default :
		echo "{failure:true}";
		break;
}




function getRoleList($data){
 
	try{
		$dbh = new Db();
		
		$query = "SELECT `id`, `role` FROM `roles`
ORDER BY role;";		
		
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];

	}catch(PDOException $e){
		$returnData = msg(0,500,$e->getMessage());
	}
	
	return $returnData;
}

function getMenuLists($data){
 
	try{
		$dbh = new Db();
		$role_id = trim($data->role_id);

		$query = "SELECT a.id AS menupkid,if(MenuLevel='menu_level_2',CONCAT(' -', a.title),if(MenuLevel='menu_level_3',CONCAT(' --', a.title),a.title)) menuname, b.`id`, b.role_id, b.`menu_id`, CASE WHEN b.id IS NULL THEN 0 ELSE 1 END bChecked
		FROM `menu` a
		LEFT JOIN menutoroles b ON b.`menu_id` = a.`id` AND b.role_id =$role_id
		ORDER BY SortOrder;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];

	}catch(PDOException $e){
		$returnData = msg(0,500,$e->getMessage());
	}
	
	return $returnData;
}

function getMenuShortOrderLists($data){
 
	try{
		$dbh = new Db();
		//$role_id = trim($data->role_id);

		$query = "SELECT a.id AS id, if(MenuLevel='menu_level_2',CONCAT(' -', a.title),if(MenuLevel='menu_level_3',CONCAT(' --', a.title),a.title)) menuname, a.SortOrder
		FROM `menu` a
		ORDER BY SortOrder;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];

	}catch(PDOException $e){
		$returnData = msg(0,500,$e->getMessage());
	}
	
	return $returnData;
}



function dataInsert($data) {

	global $TEXT;

	if($_SERVER["REQUEST_METHOD"] != "POST"){
		return $returnData = msg(0,404,'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif(!isset($data->menu_id)
	|| !isset($data->role_id) 
		
	){
		$fields = ['fields' => ['menu_id', 'role_id']];
		return $returnData = msg(0,422,'menu_id or role_id not found', $fields);
	
	}else{

		$role_id = trim($data->role_id);
		$menu_id = trim($data->menu_id);
		$bChecked = trim($data->bChecked);
	 
		$lan = trim($data->lan); 
		$UserName = trim($data->UserName); 
	
		try{
 
			$dbh = new Db();

			if ($bChecked == true){
				//$bCheckedVal = 1;

				$q = new insertq();
				$q->table = 'menutoroles';
				$q->columns = ['menu_id','role_id'];
				$q->values = [$menu_id, $role_id];
				$q->pks = ['id'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys = array($q);

			$res = exec_query($aQuerys, $UserName, $lan);  
			$success=($res['msgType']=='success')?1:0;
			$res['msg']=$TEXT['Permission Updated Successfully']; 

			 }else{
			 	//$bCheckedVal = 0;

				 $d = new deleteq();
				 $d->table = 'menutoroles';
				 $d->pks = ['role_id', 'menu_id'];
				 $d->pk_values = [$role_id, $menu_id];
				 $d->build_query();
				 $aQuerys = array($d);

				 $res = exec_query($aQuerys, $UserName, $lan);  
				 $success=($res['msgType']=='success')?1:0;
				 $res['msg']=$TEXT['Permission Removed Successfully']; 

			 }
			

			//$res = exec_query($aQuerys, "admin", 'en_GB');  
			
			$status=($res['msgType']=='success')?200:500;

			//print_r($res['msgType']);

			// if($res['msgType']=='error'){			
			// 	$res['msg']="Facility has relevant transaction records"; 
			// 	return $res; 
			// }


			// $returnData = [
			// 	"success" => 1 ,
			// 	"status" => 200,
			// 	"message" => $res['msg']
			// ];

			$returnData = [
				"success" => $success ,
				"status" => $status,
				"UserName"=> $UserName,
				"message" => $res['msg']
			];

		}catch(PDOException $e){
			$returnData = msg(0,500,$e->getMessage());
		}
		return $returnData;
		
	}
}


function dataUpdate($data) {
	global $TEXT;
	
	if($_SERVER["REQUEST_METHOD"] != "PUT"){
		return $returnData = msg(0,404,'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif(!isset($data->MenuData)
		
	){
		$fields = ['fields' => ['MenuData']];
		return $returnData = msg(0,422,'Please Fill in all Required Fields!', $fields);
	
	}else{

		$MenuData = $data->MenuData;
		$lan = trim($data->lan); 
		$UserName = trim($data->UserName); 

		//print_r($MenuData);
		//exit;
	
		$ShortBy = 0;
		$ExecutedRowCount = 0;

		try{
 
			$dbh = new Db();
			
			foreach($MenuData as $i => $ParamId) {
				$ShortBy++;
				$id = $ParamId->id;
				//echo $id."<br>";

				$sql = "UPDATE menu SET SortOrder = $ShortBy WHERE id = $id";
				//echo $sql."<br>";
				if($dbh->query($sql))
				$ExecutedRowCount++;

			}

			

			// $q = new updateq();
			// $q->table = 't_ui_languagex'; 
			// $q->columns = ['LangText'];
			// $q->values = [$LangText];
			// $q->pks = ['LangLabelId'];
			// $q->pk_values = [$LangLabelId];
			// $q->bUseInsetId = false;
			// $q->build_query();
			// $aQuerys[] = $q;

		
			
			// $res = exec_query($sql, "admin", 'en_GB');  
			// $returnData = [
			// 	"success" => 1 ,
			// 	"status" => 200,
			// 	"message" => $res['msg']
			// ];

			$res = exec_query($sql, $UserName, $lan);  
			$success=($res['msgType']=='success')?1:0;
			$res['msg']=$TEXT['Data Updated Successfully']; 

			$returnData = [
				"success" => $success ,
				"status" => $status,
				"UserName"=> $UserName,
				"message" => $res['msg']
			];

		}catch(PDOException $e){
			$returnData = msg(0,500,$e->getMessage());
		}
		
		return $returnData;
	}
}

*/

?>