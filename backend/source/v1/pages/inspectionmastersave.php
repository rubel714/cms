<?php
try {

	apiLogWrite("\n\n========$PageName=======Called (" . date('Y_m_d_H_i_s') . ")===================");
	apiLogWrite("Params (" . date('Y_m_d_H_i_s') . "): " . json_encode($data));

	$db = new Db();
	$TransactionId = isset($data['TransactionId']) ? checkNull($data['TransactionId']) : "";
	$TransactionDate = isset($data['TransactionDate']) ? convertAppToDBDateTime(checkNull($data['TransactionDate'])) : ""; //21-Aug-2024 5:15 AM
	$InvoiceNo = isset($data['InvoiceNo']) ? checkNull($data['InvoiceNo']) : "";
	$CoverFilePages = isset($data['CoverFilePages']) ? checkNull($data['CoverFilePages']) : "";
	$CoverFileUrl = "";// isset($data['CoverFileUrl']) ? checkNull($data['CoverFileUrl']) : "";/////////////////
	$UserId = isset($data['UserInfoID']) ? checkNull($data['UserInfoID']) : "";
	$ManyImgPrefix = isset($data['ManyImgPrefix']) ? checkNull($data['ManyImgPrefix']) : "";
	$CurrentDate = date('Y-m-d H:i:s');
	$ClientId = 1;
	$TransactionTypeId = 1;
	$StatusId = 1;

	//  ConvertImage($objFile->attachedBase64String);

	if ($TransactionDate == "" ) {
		$apiResponse = json_encode(recordNotFoundMsg(0, "Report Date param is missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}

	if ($InvoiceNo == "" ) {
		$apiResponse = json_encode(recordNotFoundMsg(0, "Report Number param is missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}
	
	if ($CoverFilePages == "" ) {
		$apiResponse = json_encode(recordNotFoundMsg(0, "Cover File Page Count param is missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}
	
	// if ($CoverFileUrl == "" ) {
	// 	$apiResponse = json_encode(recordNotFoundMsg(0, "Cover File param is missing"));
	// 	apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
	// 	echo $apiResponse;
	// 	return;
	// }
		
	if ($UserId == "" ) {
		$apiResponse = json_encode(recordNotFoundMsg(0, "UserId param is missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}

	if ($ManyImgPrefix == "" ) {
		$apiResponse = json_encode(recordNotFoundMsg(0, "ManyImg Prefix param is missing"));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
		return;
	}

	if($TransactionId==""){
		$query = "INSERT INTO t_transaction(ClientId, TransactionTypeId, TransactionDate, InvoiceNo, CoverFilePages, CoverFileUrl, UserId, StatusId,ManyImgPrefix) 
			VALUES (:ClientId, :TransactionTypeId, :TransactionDate, :InvoiceNo, :CoverFilePages, :CoverFileUrl, :UserId, :StatusId, :ManyImgPrefix);";

		$pList = array(
			'ClientId' => $ClientId,
			'TransactionTypeId' => $TransactionTypeId,	
			'TransactionDate' => $TransactionDate,
			'InvoiceNo' => $InvoiceNo,
			'CoverFilePages' => $CoverFilePages,
			'CoverFileUrl' => $CoverFileUrl,
			'UserId' => $UserId,
			'StatusId' => $StatusId,
			'ManyImgPrefix' => $ManyImgPrefix
		);

	}else{
		$query = "UPDATE t_transaction set TransactionDate=:TransactionDate, InvoiceNo=:InvoiceNo, CoverFilePages=:CoverFilePages, CoverFileUrl=:CoverFileUrl
		where TransactionId=:TransactionId;";
		
		$pList = array(
			'TransactionDate' => $TransactionDate,
			'InvoiceNo' => $InvoiceNo,
			'CoverFilePages' => $CoverFilePages,
			'CoverFileUrl' => $CoverFileUrl,
			'TransactionId' => $TransactionId
		);
	}

	$db->bindMore($pList);
	$resultdata = $db->query($query);

	if (is_object($resultdata)) {
		$errormsg = $resultdata->errorInfo;
		$apiResponse = json_encode(recordNotFoundMsg(0, $errormsg[2]));
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
		echo $apiResponse;
	} else {
		$response = array(["SysValue" => 1, "SysMessage" => "Data has been saved successfully"]);
		$apiResponse = json_encode($response);
		echo $apiResponse;
		apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): ".$apiResponse);
	}
} catch (PDOException $e) {
	$apiResponse = json_encode(recordNotFoundMsg(0, $e->getMessage()));
	apiLogWrite("Output (" . date('Y_m_d_H_i_s') . "): " . $apiResponse);
	echo $apiResponse;
}


function ConvertImage($base64_string){
		
	$targetDir = '../../../image/transaction/';
	$exploded = explode(',', $base64_string, 2);
	$extention = explode(';', explode('/', $exploded[0])[1])[0];
	$decoded = base64_decode($exploded[1]);
	$output_file = date("Y_m_d_H_i_s_").rand(1,9999).".".$extention; /**Image file name */
	file_put_contents($targetDir."/".$output_file, $decoded);
	return $output_file;
}