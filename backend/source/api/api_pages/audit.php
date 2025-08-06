<?php
include("posttransaction.php");

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;
	
	// case "getHoldDataList":
	// 	$returnData = getHoldDataList($data);
	// 	break;

	case "getDataSingle":
		$returnData = getDataSingle($data);
		break;

	case "dataAddEdit":
		$returnData = dataAddEdit($data);
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
	$ClientId = trim($data->ClientId);
	$BranchId = trim($data->BranchId);
	$TransactionTypeId = trim($data->TransactionTypeId);
	$StartDate = trim($data->StartDate);
	$EndDate = trim($data->EndDate)." 23-59-59";
	try {
		$dbh = new Db();

		$query = "SELECT a.`TransactionId` AS id, DATE_FORMAT(a.`TransactionDate`,'%Y-%m-%d') as TransactionDate, 
		a.`InvoiceNo`, a.`UserId`,	b.UserName,a.`BPosted`, a.`Remarks`,
		a.EntryByUserId,a.ApproveByUserId,c.UserName as EntryByUserName, d.UserName as ApproveByUserName,
		case when a.BPosted=1 then 'Posted' else 'Draft' end StatusName
		FROM t_audit a
		INNER JOIN t_users b on a.UserId=b.UserId
		INNER JOIN t_users c on a.EntryByUserId=c.UserId
		INNER JOIN t_users d on a.ApproveByUserId=d.UserId
		where a.ClientId=$ClientId
		and a.BranchId=$BranchId
		and a.TransactionTypeId=$TransactionTypeId
		and (a.TransactionDate between '$StartDate' and '$EndDate')
		ORDER BY a.TransactionDate DESC, a.`InvoiceNo` DESC;";

		$resultdata = $dbh->query($query);

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

 

function getDataSingle($data)
{
	$TransactionId = trim($data->id);
	// $ClientId = trim($data->ClientId); 
	// $BranchId = trim($data->BranchId); 
	// $TransactionTypeId = trim($data->TransactionTypeId); 

	try {
		$dbh = new Db();
		
		/**Master Data */
		$query = "SELECT `TransactionId` AS id, `ClientId`, `BranchId`, `TransactionTypeId`, 
		DATE_FORMAT(`TransactionDate`,'%Y-%m-%d') as TransactionDate, `InvoiceNo`,
		 `BPosted`, `Remarks`, UserId, EntryByUserId,ApproveByUserId
		FROM t_audit
		where TransactionId=$TransactionId;";
		$resultdataMaster = $dbh->query($query);

		/**Items Data */
		$query = "SELECT a.TransactionItemsId as autoId,a.`TransactionItemsId`, a.`TransactionId`, 
		a.ProductId, a.CurrentQuantity, a.Quantity,CONCAT(b.ProductName,' (',b.ProductBarcode,')') ProductName
		
		FROM t_audititems a
		inner join t_product b on a.ProductId=b.ProductId
		where a.TransactionId=$TransactionId
		order by a.TransactionItemsId ASC;";
		$resultdataItems = $dbh->query($query);


		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => array("master" => $resultdataMaster, "items" => $resultdataItems)
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

		try {

			$dbh = new Db();

			$data = $data->params;
			$lan = trim($data->lan);
			$UserId = trim($data->UserId);
			$ClientId = trim($data->ClientId);
			$BranchId = trim($data->BranchId);

			$InvoiceMaster = isset($data->InvoiceMaster) ? ($data->InvoiceMaster) : [];
			// $InvoiceItems = isset($data->InvoiceItems) ? ($data->InvoiceItems) : [];
			// $DeletedItems = isset($data->DeletedItems) ? ($data->DeletedItems) : [];

			$TransactionId = $InvoiceMaster->id;
			$TransactionTypeId = $InvoiceMaster->TransactionTypeId;
			$TransactionDate = $InvoiceMaster->TransactionDate;
			$InvoiceNo = $InvoiceMaster->InvoiceNo;
			$EntryByUserId = $InvoiceMaster->EntryByUserId;
			$ApproveByUserId = $InvoiceMaster->ApproveByUserId;
			$BPosted = $InvoiceMaster->BPosted;


			$aQuerys = array();

			
			if ($TransactionId === "") {

				/**Invoice Master */
				$q = new insertq();
				$q->table = 't_audit';
				$q->columns = ['ClientId', 'BranchId', 'TransactionTypeId', 'TransactionDate', 'InvoiceNo', 'UserId','EntryByUserId','ApproveByUserId'];
				$q->values = [$ClientId, $BranchId, $TransactionTypeId, $TransactionDate, $InvoiceNo, $UserId,$EntryByUserId,$ApproveByUserId];
				$q->pks = ['TransactionId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;


				/**Invoice Items */
				$query = "SELECT a.`ProductId`,b.ProductName,SUM(a.`Quantity`) Quantity
				FROM `t_productstock` a
				inner join t_product b ON a.ProductId=b.ProductId
				WHERE a.ClientId=$ClientId
				AND a.BranchId=$BranchId
				AND a.Quantity>0
				GROUP BY a.ProductId,b.ProductName;";
				$re = $dbh->query($query);
				$productStockList = array();
				foreach ($re as $key => $r) {
					$productStockList[] = array("ProductId"=>$r["ProductId"],"ProductName"=>$r["ProductName"],"CurrentQuantity"=>$r["Quantity"]);
				}

				$randomProductCount = 10;
				if(count($productStockList)<10){
					$randomProductCount = count($productStockList);
				}
				$InvoiceItems = array_rand($productStockList, $randomProductCount);
// echo "<pre>";
// print_r($InvoiceItems);
// exit;
				// $query = "SELECT ProductId, 300 CurrentQuantity
				// FROM t_product
				// limit 0,10;";
				// $InvoiceItems = $dbh->query($query);

				foreach ($InvoiceItems as $prodIdx) {

					$ProductId = $productStockList[$prodIdx]["ProductId"];
					$CurrentQuantity = $productStockList[$prodIdx]["CurrentQuantity"];
					$ididid = "[LastInsertedId]";

					$q = new insertq();
					$q->table = 't_audititems';
					$q->columns = ['TransactionId', 'ProductId',  'CurrentQuantity'];
					$q->values = [$ididid,  $ProductId, $CurrentQuantity];
					$q->pks = ['TransactionItemsId'];
					$q->bUseInsetId = false;
					$q->build_query();
					$aQuerys[] = $q;
				}


				
			} else {

				/**Invoice Master */
				$u = new updateq();
				$u->table = 't_audit';
				$u->columns = ['TransactionDate', 'InvoiceNo','EntryByUserId', 'ApproveByUserId'];
				$u->values = [$TransactionDate, $InvoiceNo,$EntryByUserId, $ApproveByUserId];
				$u->pks = ['TransactionId'];
				$u->pk_values = [$TransactionId];
				$u->build_query();
				$aQuerys[] = $u;


				/**Invoice Items */
				$InvoiceItems = isset($data->InvoiceItems) ? ($data->InvoiceItems) : [];
				foreach ($InvoiceItems as $key => $Many) {

					$TransactionItemsId = $Many->TransactionItemsId;
					$Quantity = isset($Many->Quantity) && ($Many->Quantity !== "") ? $Many->Quantity : NULL;

					$u = new updateq();
					$u->table = 't_audititems';
					$u->columns = ['Quantity'];
					$u->values = [$Quantity];
					$u->pks = ['TransactionItemsId'];
					$u->pk_values = [$TransactionItemsId];
					$u->build_query();
					$aQuerys[] = $u;
				}
			}


			/**Delete many items */
			// foreach ($DeletedItems as $key => $Many) {
			// 	$TransactionItemsId = $Many->TransactionItemsId;
			// 	if ($TransactionItemsId !== "") {
			// 		//TransactionItemsId not blank means this item has in DB.

			// 		$d = new deleteq();
			// 		$d->table = 't_expenseitems';
			// 		$d->pks = ['TransactionItemsId'];
			// 		$d->pk_values = [$TransactionItemsId];
			// 		$d->build_query();
			// 		$aQuerys[] = $d;
			// 	}
			// }

			

			if ($BPosted == 1) {
				$u = new updateq();
				$u->table = 't_audit';
				$u->columns = ['BPosted'];
				$u->values = [1];
				$u->pks = ['TransactionId'];
				$u->pk_values = [$TransactionId];
				$u->build_query();
				$aQuerys[] = $u;
			}

			$res = exec_query($aQuerys, $UserId, $lan);

			if ($res['msgType'] === 'success' && $BPosted === 1) {

				$success = ($res['msgType'] === 'success') ? 1 : 0;
				$status = ($res['msgType'] === 'success') ? 200 : 500;
				$message = "Invoice Posted Successfully";

				$returnData = [
					"success" => $success,
					"status" => $status,
					"id" => $res['id'],
					"message" => $message
				];
				
			} else {
				/**When add/edit except POST */

				$success = ($res['msgType'] === 'success') ? 1 : 0;
				$status = ($res['msgType'] === 'success') ? 200 : 500;
				$returnData = [
					"success" => $success,
					"status" => $status,
					"id" => $res['id'],
					"message" => $res['msg']
				];
			}





		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
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

		$TransactionId = $data->rowData->id;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);
		//$ClientId = trim($data->ClientId); 
		//$BranchId = trim($data->BranchId); 

		try {

			$dbh = new Db();

			$aQuerys = array();


			$d = new deleteq();
			$d->table = 't_audititems';
			$d->pks = ['TransactionId'];
			$d->pk_values = [$TransactionId];
			$d->build_query();
			$aQuerys[] = $d;

			$d = new deleteq();
			$d->table = 't_audit';
			$d->pks = ['TransactionId'];
			$d->pk_values = [$TransactionId];
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
