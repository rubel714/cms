<?php

$task = '';
if(isset($data->action)) {
	$task = trim($data->action);
}

switch($task){
	
	case "getDataList":
		$returnData = getDataList($data);
	break;

	default :
		echo "{failure:true}";
		break;
}

function getDataList($data){

	
	$ClientId = trim($data->ClientId); 
	$BranchId = trim($data->BranchId); 
	$StartDate = trim($data->StartDate); 
	$EndDate = trim($data->EndDate); 
	$QueryType = trim($data->QueryType); 
	$TableName = trim($data->TableName); 

	try{
		$dbh = new Db();
		$query = "SELECT a.LogId AS id, a.LogDate, a.RemoteIP, a.UserId, b.UserName,
		a.QueryType, a.TableName,a.JsonText,a.SqlText,a.SqlParams
		FROM t_sqllog a
		inner join t_users b on a.UserId=b.UserId
		where a.LogDate between '$StartDate' and '$EndDate 23:59:59'
		and (a.QueryType = '$QueryType' OR $QueryType=0)
		and (a.TableName = '$TableName' OR $TableName=0)
		ORDER BY a.LogDate DESC;";		
		
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

 

?>