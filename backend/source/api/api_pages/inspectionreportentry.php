<?php

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;
	case "dataAddEdit":
		$returnData = dataAddEdit($data);
		break;
	case "dataAddEditMany":
		$returnData = dataAddEditMany($data);
		break;
	case "deleteData":
		$returnData = deleteData($data);
		break;
	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{

	// $ClientId = trim($data->ClientId); 
	// $BranchId = trim($data->BranchId); 

	try {
		$dbh = new Db();

		$query = "SELECT a.TransactionId AS id,a.TransactionTypeId,DATE(a.`TransactionDate`) TransactionDate, 
		a.InvoiceNo,a.CoverFilePages,
		a.`UserId`, a.StatusId, b.`UserName`,c.`StatusName`, a.CoverFileUrl,'' CoverFileUrlUpload,
		case when a.CoverFileUrl is null then '' else 'Yes' end as CoverFileUrlStatus,a.ManyImgPrefix,'' Items
	   FROM `t_transaction` a
	   INNER JOIN `t_users` b ON a.`UserId` = b.`UserId`
	   INNER JOIN `t_status` c ON a.`StatusId` = c.`StatusId`
	   ORDER BY a.`TransactionDate` DESC, a.InvoiceNo ASC;";

		$resultdatalist = $dbh->query($query);
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



		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function dataAddEdit($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} else {

		$lan = trim($data->lan);
		$UserId = trim($data->UserId);
		$ClientId = trim($data->ClientId);

		$id = $data->rowData->id;
		$TransactionTypeId = $data->rowData->TransactionTypeId;
		$StatusId = $data->rowData->StatusId;
		// $CoverFileUrl = $data->rowData->CoverFileUrl ? $data->rowData->CoverFileUrl : null;
		$InvoiceNo = $data->rowData->InvoiceNo;
		$TransactionDate = $data->rowData->TransactionDate;
		$CoverFilePages = $data->rowData->CoverFilePages;
		$ManyImgPrefix = $data->rowData->ManyImgPrefix;
		$CoverFileUrl = $data->rowData->CoverFileUrlUpload ? ConvertFile($data->rowData->CoverFileUrlUpload,$ManyImgPrefix) : null;
		try {
			$aQuerys = array();
			if ($id == "") {
				$q = new insertq();
				$q->table = 't_transaction';
				$q->columns = ['ClientId', 'TransactionTypeId', 'TransactionDate', 'InvoiceNo', 'CoverFilePages', 'CoverFileUrl', 'UserId', 'StatusId','ManyImgPrefix'];
				$q->values = [$ClientId, $TransactionTypeId, $TransactionDate, $InvoiceNo, $CoverFilePages, $CoverFileUrl, $UserId, $StatusId,$ManyImgPrefix];
				$q->pks = ['TransactionId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;
			} else {
				$u = new updateq();
				$u->table = 't_transaction';
				$u->columns = ['TransactionDate', 'InvoiceNo', 'CoverFilePages', 'CoverFileUrl', 'StatusId'];
				$u->values = [$TransactionDate, $InvoiceNo, $CoverFilePages, $CoverFileUrl, $StatusId];
				$u->pks = ['TransactionId'];
				$u->pk_values = [$id];
				$u->build_query();
				$aQuerys[] = $u;
			}

			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;

			$returnData = [
				"success" => $success,
				"status" => $status,
				"UserId" => $UserId,
				"message" => $res['msg']
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}


function dataAddEditMany($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} else {

		// echo "<pre>";
		// print_r($data);
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);
		// $ClientId = trim($data->ClientId);
		// $BranchId = trim($data->BranchId);

		$id = $data->rowData->id;
		// $TransactionTypeId = $data->rowData->TransactionTypeId;
		// $StatusId = $data->rowData->StatusId;
		// $CoverFileUrl = $data->rowData->CoverFileUrl ? $data->rowData->CoverFileUrl : null;
		// $InvoiceNo = $data->rowData->InvoiceNo;
		// $TransactionDate = $data->rowData->TransactionDate;
		// $CoverFilePages = $data->rowData->CoverFilePages;
		$ManyImgPrefix = $data->rowData->ManyImgPrefix;
		$Items = $data->rowData->Items;
		$currentRowDelete = $data->currentRowDelete;
		// $PhotoUrl =  $data->rowData->PhotoUrl? $data->rowData->PhotoUrl : "placeholder.png";
		// echo "<pre>";
		// print_r($Items);
		try {

			// $dbh = new Db();
			$aQuerys = array();

			foreach ($currentRowDelete as $DelItem) {
				if($DelItem->autoId != -1){
					$d = new deleteq();
					$d->table = 't_transaction_items';
					$d->pks = ['TransactionItemId'];
					$d->pk_values = [$DelItem->TransactionItemId];
					$d->build_query();
					$aQuerys[] = $d;
				}
				
			}

			foreach ($Items as $Item) {
					$TransactionItemId = $Item->TransactionItemId;
					$autoId = $Item->autoId;
					$CheckId = $Item->CheckId ? $Item->CheckId : null; //$Item->CheckId;
					$RowNo = $Item->RowNo;
					$ColumnNo = $Item->ColumnNo;
					//$PhotoUrl = $Item->PhotoUrlChanged ? $Item->PhotoUrlChanged : $Item->PhotoUrl;
					$PhotoUrl = $Item->PhotoUrlPreview ? ConvertImage($Item->PhotoUrlPreview,$ManyImgPrefix) : $Item->PhotoUrl;
					$SortOrder = $Item->SortOrder;

					if($autoId == -1){
						$q = new insertq();
						$q->table = 't_transaction_items';
						$q->columns = ['TransactionId', 'CheckId', 'RowNo', 'ColumnNo', 'PhotoUrl', 'SortOrder'];
						$q->values = [$id, $CheckId, $RowNo, $ColumnNo, $PhotoUrl, $SortOrder];
						$q->pks = ['TransactionItemId'];
						$q->bUseInsetId = false;
						$q->build_query();
						$aQuerys[] = $q;
					}else{
						$u = new updateq();
						$u->table = 't_transaction_items';
						$u->columns = ['CheckId', 'RowNo', 'ColumnNo', 'PhotoUrl', 'SortOrder'];
						$u->values = [$CheckId, $RowNo, $ColumnNo, $PhotoUrl, $SortOrder];
						$u->pks = ['TransactionItemId'];
						$u->pk_values = [$TransactionItemId];
						$u->build_query();
						$aQuerys[] = $u;
					}
					
				}

			// if ($id == "") {
			// 	$q = new insertq();
			// 	$q->table = 't_transaction';
			// 	$q->columns = ['ClientId', 'TransactionTypeId', 'TransactionDate', 'InvoiceNo', 'CoverFilePages', 'CoverFileUrl', 'UserId', 'StatusId','ManyImgPrefix'];
			// 	$q->values = [$ClientId, $TransactionTypeId, $TransactionDate, $InvoiceNo, $CoverFilePages, $CoverFileUrl, $UserId, $StatusId,$ManyImgPrefix];
			// 	$q->pks = ['TransactionId'];
			// 	$q->bUseInsetId = true;
			// 	$q->build_query();
			// 	$aQuerys[] = $q;

			// 	foreach ($Items as $Item) {

			// 		$CheckId = $Item->CheckId ? $Item->CheckId : null; //$Item->CheckId;
			// 		$RowNo = $Item->RowNo;
			// 		$ColumnNo = $Item->ColumnNo;
			// 		// $PhotoUrl = $Item->PhotoUrlChanged ? $Item->PhotoUrlChanged : $Item->PhotoUrl;
			// 		$PhotoUrl = $Item->PhotoUrlPreview ? ConvertImage($Item->PhotoUrlPreview,$ManyImgPrefix) : $Item->PhotoUrl;

			// 		$SortOrder = $Item->SortOrder;
			// 		// echo $CheckId."====";
			// 		$q = new insertq();
			// 		$q->table = 't_transaction_items';
			// 		$q->columns = ['TransactionId', 'CheckId', 'RowNo', 'ColumnNo', 'PhotoUrl', 'SortOrder'];
			// 		$q->values = ['[LastInsertedId]', $CheckId, $RowNo, $ColumnNo, $PhotoUrl, $SortOrder];
			// 		$q->pks = ['TransactionItemId'];
			// 		$q->bUseInsetId = false;
			// 		$q->build_query();
			// 		$aQuerys[] = $q;
			// 	}
			// } else {
			// 	$u = new updateq();
			// 	$u->table = 't_transaction';
			// 	$u->columns = ['TransactionDate', 'InvoiceNo', 'CoverFilePages', 'CoverFileUrl', 'StatusId'];
			// 	$u->values = [$TransactionDate, $InvoiceNo, $CoverFilePages, $CoverFileUrl, $StatusId];
			// 	$u->pks = ['TransactionId'];
			// 	$u->pk_values = [$id];
			// 	$u->build_query();
			// 	$aQuerys[] = $u;

			// 	foreach ($Items as $Item) {
			// 		$TransactionItemId = $Item->TransactionItemId;
			// 		$autoId = $Item->autoId;
			// 		$CheckId = $Item->CheckId ? $Item->CheckId : null; //$Item->CheckId;
			// 		$RowNo = $Item->RowNo;
			// 		$ColumnNo = $Item->ColumnNo;
			// 		//$PhotoUrl = $Item->PhotoUrlChanged ? $Item->PhotoUrlChanged : $Item->PhotoUrl;
			// 		$PhotoUrl = $Item->PhotoUrlPreview ? ConvertImage($Item->PhotoUrlPreview,$ManyImgPrefix) : $Item->PhotoUrl;
			// 		$SortOrder = $Item->SortOrder;

			// 		if($autoId == -1){
			// 			$q = new insertq();
			// 			$q->table = 't_transaction_items';
			// 			$q->columns = ['TransactionId', 'CheckId', 'RowNo', 'ColumnNo', 'PhotoUrl', 'SortOrder'];
			// 			$q->values = [$id, $CheckId, $RowNo, $ColumnNo, $PhotoUrl, $SortOrder];
			// 			$q->pks = ['TransactionItemId'];
			// 			$q->bUseInsetId = false;
			// 			$q->build_query();
			// 			$aQuerys[] = $q;
			// 		}else{
			// 			$u = new updateq();
			// 			$u->table = 't_transaction_items';
			// 			$u->columns = ['CheckId', 'RowNo', 'ColumnNo', 'PhotoUrl', 'SortOrder'];
			// 			$u->values = [$CheckId, $RowNo, $ColumnNo, $PhotoUrl, $SortOrder];
			// 			$u->pks = ['TransactionItemId'];
			// 			$u->pk_values = [$TransactionItemId];
			// 			$u->build_query();
			// 			$aQuerys[] = $u;
			// 		}
					

					
			// 	}

			
			// }



			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;

			$returnData = [
				"success" => $success,
				"status" => $status,
				"UserId" => $UserId,
				"message" => $res['msg']
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}



function ConvertFile($base64_string, $prefix)
{

	$path = "../../../image/transaction/".$prefix;

	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}

	$targetDir = '../../../image/transaction/'.$prefix;
	$exploded = explode(',', $base64_string, 2);
	$extention = explode(';', explode('/', $exploded[0])[1])[0];
	$decoded = base64_decode($exploded[1]);
	$output_file = $prefix . "_cover_" . date("Y_m_d_H_i_s") . "_" . rand(1, 9999) . "." . $extention;
	file_put_contents($targetDir . "/" . $output_file, $decoded);
	return $output_file;
}


function ConvertImage($base64_string, $prefix)
{

	$path = "../../../image/transaction/".$prefix;

	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}
	
	$targetDir = '../../../image/transaction/'.$prefix;
	$exploded = explode(',', $base64_string, 2);
	$extention = explode(';', explode('/', $exploded[0])[1])[0];
	$decoded = base64_decode($exploded[1]);
	$output_file = $prefix . "_" . date("Y_m_d_H_i_s") . "_" . rand(1, 9999) . "." . $extention;
	// $output_file = date("Y_m_d_H_i_s") . rand(1, 9999) . "." . $extention;
	/**Image file name */
	file_put_contents($targetDir . "/" . $output_file, $decoded);
	return $output_file;
}


function deleteData($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif (!isset($data->rowData->id)) {
		$fields = ['fields' => ['id']];
		return $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
	} else {

		$id = $data->rowData->id;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		try {

			$dbh = new Db();

			$d = new deleteq();
			$d->table = 't_transaction_items';
			$d->pks = ['TransactionId'];
			$d->pk_values = [$id];
			$d->build_query();
			$aQuerys[] = $d;

			$d = new deleteq();
			$d->table = 't_transaction';
			$d->pks = ['TransactionId'];
			$d->pk_values = [$id];
			$d->build_query();
			$aQuerys[] = $d;

			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;

			$returnData = [
				"success" => $success,
				"status" => $status,
				"UserId" => $UserId,
				"message" => $res['msg']
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}
