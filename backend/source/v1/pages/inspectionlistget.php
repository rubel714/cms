<?php

try{
	apiLogWrite("\n\n========$PageName=======Called (".date('Y_m_d_H_i_s').")===================");
	apiLogWrite("Params (".date('Y_m_d_H_i_s')."): ".json_encode($data));
	
	$dbh = new Db();
	$Search =  isset($data['Search'])?$data['Search']:'';
	
	$sWhere="";
	if($Search){
		$sWhere=" where a.InvoiceNo like '%$Search%' ";
	}
	
	$query = "SELECT 1 AS SysValue,'Successful' AS SysMessage, a.TransactionId AS id,a.TransactionTypeId,DATE(a.`TransactionDate`) TransactionDate, 
		a.InvoiceNo,a.CoverFilePages,
		a.`UserId`, a.StatusId, b.`UserName`,c.`StatusName`, a.CoverFileUrl,'' CoverFileUrlUpload,
		case when a.CoverFileUrl is null then '' else 'Yes' end as CoverFileUrlStatus,a.ManyImgPrefix,'' Items
	   FROM `t_transaction` a
	   INNER JOIN `t_users` b ON a.`UserId` = b.`UserId`
	   INNER JOIN `t_status` c ON a.`StatusId` = c.`StatusId`
	   $sWhere
	   ORDER BY a.`TransactionDate` DESC, a.InvoiceNo ASC;";

		$resultdatalist = $dbh->query($query);
		
		
	if (is_object($resultdatalist)) {
		$errormsg = $resultdatalist->errorInfo;
		$apiResponse = json_encode(recordNotFoundMsg(0,$errormsg[2]));
		apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
		echo $apiResponse;
	}else if(count($resultdatalist)===0){
		$apiResponse = json_encode(recordNotFoundMsg());
		apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
		echo $apiResponse;
	}else{

		$resultdata = array();
		foreach ($resultdatalist as $row) {
			$TransactionId = $row['id'];
			$query = "SELECT a.TransactionItemId as autoId,a.`TransactionItemId`, a.`TransactionId`, a.`CheckId`,
			a.RowNo,a.ColumnNo,a.PhotoUrl,'' PhotoUrlChanged, '' PhotoUrlPreview, '' PhotoUrlUpload, a.SortOrder
			FROM t_transaction_items a
			where a.TransactionId=$TransactionId
			order by a.SortOrder ASC;";
			$resultdataItems = $dbh->query($query);
			$row['Items'] = $resultdataItems;
			$resultdata[] = $row;
		}

		echo json_encode($resultdata);
		apiLogWrite("Output (".date('Y_m_d_H_i_s')."): Success");
	}
	
}catch(PDOException $e){
	$apiResponse = json_encode(recordNotFoundMsg(0,$e->getMessage()));
	apiLogWrite("Output (".date('Y_m_d_H_i_s')."): ".$apiResponse);
	echo $apiResponse;	
}

?>