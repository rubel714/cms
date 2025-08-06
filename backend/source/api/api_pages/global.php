<?php

function getProductSystemBarcode($ClientId) {

	$dbh = new Db();
	$query = "SELECT ClientCode FROM t_client where ClientId=$ClientId;"; 
	$resultdata = $dbh->query($query);
	$ClientCode = $resultdata[0]["ClientCode"];

	$query = "SELECT IFNULL(MAX(SUBSTRING(SystemBarcode, 5, 8)),0)+1 NextSystemBarcode FROM t_product WHERE ClientId = $ClientId;"; 
	$resultdata = $dbh->query($query);
	$NextSystemBarcode = $resultdata[0]["NextSystemBarcode"];

	$returnData = $ClientCode. str_pad($NextSystemBarcode,8,0,STR_PAD_LEFT);
	
	return $returnData;
}

function getNextInvoiceNumber($ClientId,$BranchId,$TransactionTypeId) {

	// $dbh = new Db();

	// $query = "SELECT IFNULL(MAX(InvoiceNo),0)+1 NextNo 
	// FROM t_transaction 
	// WHERE ClientId = $ClientId
	// AND BranchId = $BranchId
	// AND TransactionTypeId = $TransactionTypeId;"; 
	// $resultdata = $dbh->query($query);
	// $NextNo = $resultdata[0]["NextNo"];
	$PreFix = 'NA';
	if($TransactionTypeId == 1){
		$PreFix = 'CH';
	}else if($TransactionTypeId == 2){
		$PreFix = 'SI';
	}else if($TransactionTypeId == 3){
		$PreFix = 'CR';
	}else if($TransactionTypeId == 4){
		$PreFix = 'AD';
	}else if($TransactionTypeId == 5){
		$PreFix = 'RT';
	}else if($TransactionTypeId == 20){
		$PreFix = 'VH';
	}else if($TransactionTypeId == 25){
		$PreFix = 'AU';
	}

	$NextNo = $PreFix.date("YmdHms").rand(10,50); //Year Month Day Hour Min Sec 2digit 10 to 50
	
	return $NextNo;
}
