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
	//$BranchId = trim($data->BranchId); 

	try {
		$dbh = new Db();

		$query = "SELECT a.PaymentId AS id, a.PaymentDate,
		a.CustomerId,b.CustomerName, a.CustomerGroupId,c.CustomerGroupName,a.BankId,d.BankName,a.TotalPaymentAmount,a.Remarks
		FROM t_payment a
		LEFT JOIN t_customer b ON a.CustomerId = b.CustomerId
		LEFT JOIN t_customergroup c ON a.CustomerGroupId = c.CustomerGroupId
		LEFT JOIN t_bank d ON a.BankId = d.BankId
		ORDER BY a.PaymentDate DESC, a.PaymentId DESC;";

		$resultdatalist = $dbh->query($query);


		// $resultdatamap = array();
		// $query = "SELECT a.CustomerMapId,a.`CustomerId`, a.`BusinessLineId`,concat(b.`BusinessLineCode`,' - ',b.BusinessLineName) as BusinessLineName, a.`UserId`,c.UserName
		// 	FROM t_customer_map a
		// 	inner join t_businessline b on a.BusinessLineId=b.BusinessLineId
		// 	inner join t_users c on a.UserId=c.UserId;";
		// $resultdataItems = $dbh->query($query);
		// foreach ($resultdataItems as $r) {
		// 	$CustomerId = $r['CustomerId'];
		// 	$resultdatamap[$CustomerId][] = $r;
		// }

		// $resultdata = array();
		// foreach ($resultdatalist as $row) {
		// 	$CustomerId = $row['id'];

		// 	if (array_key_exists($CustomerId, $resultdatamap)) {
		// 		$row['UserMap'] = $resultdatamap[$CustomerId];
		// 	} else {
		// 		$row['UserMap'] = [];
		// 	}

		// 	$resultdata[] = $row;
		// }


		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdatalist
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
// echo "<pre>";
// print_r($data);
// exit;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		$PaymentId = $data->rowData->id;
		$PaymentDate = $data->rowData->PaymentDate;
		$CustomerId = $data->rowData->CustomerId?$data->rowData->CustomerId:null;
		$CustomerGroupId = $data->rowData->CustomerGroupId?$data->rowData->CustomerGroupId:null;
		$BankId = $data->rowData->BankId?$data->rowData->BankId:null;
		$TotalPaymentAmount = $data->rowData->TotalPaymentAmount?$data->rowData->TotalPaymentAmount:null;
		$Remarks = $data->rowData->Remarks?$data->rowData->Remarks:null;

		$Items = isset($data->Items) ? $data->Items : [];


		try {

			$dbh = new Db();
			$aQuerys = array();

			if ($PaymentId == "") {
				$q = new insertq();
				$q->table = 't_payment';
				$q->columns = ['PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId', 'TotalPaymentAmount', 'Remarks', 'UserId'];
				$q->values = [$PaymentDate, $CustomerId, $CustomerGroupId, $BankId, $TotalPaymentAmount, $Remarks, $UserId];
				$q->pks = ['PaymentId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys[] = $q;
			} else {
				$u = new updateq();
				$u->table = 't_payment';
				$u->columns = ['PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId', 'TotalPaymentAmount', 'Remarks'];
				$u->values = [$PaymentDate, $CustomerId, $CustomerGroupId, $BankId, $TotalPaymentAmount, $Remarks];
				$u->pks = ['PaymentId'];
				$u->pk_values = [$PaymentId];
				$u->build_query();
				$aQuerys[] = $u;


				// $manyDataList = isset($data->manyDataList) ? $data->manyDataList : [];
				// $manyDeleteDataList = isset($data->manyDeleteDataList) ? $data->manyDeleteDataList : [];
				// // echo "<pre>";

				// foreach ($manyDeleteDataList as $key => $CustomerMapId) {
				// 		$d = new deleteq();
				// 		$d->table = 't_customer_map';
				// 		$d->pks = ['CustomerMapId'];
				// 		$d->pk_values = [$CustomerMapId];
				// 		$d->build_query();
				// 		$aQuerys[] = $d;
				// }


				// foreach ($manyDataList as $key => $obj) {
				// 	// print_r($obj);
				// 	if ($obj->CustomerMapId > 0) {
				// 		$u = new updateq();
				// 		$u->table = 't_customer_map';
				// 		$u->columns = ['BusinessLineId', 'UserId'];
				// 		$u->values = [$obj->BusinessLineId, $obj->UserId];
				// 		$u->pks = ['CustomerMapId'];
				// 		$u->pk_values = [$obj->CustomerMapId];
				// 		$u->build_query();
				// 		$aQuerys[] = $u;
				// 	} else {
				// 		$q = new insertq();
				// 		$q->table = 't_customer_map';
				// 		$q->columns = ['CustomerId', 'BusinessLineId', 'UserId'];
				// 		$q->values = [$obj->CustomerId, $obj->BusinessLineId, $obj->UserId];
				// 		$q->pks = ['CustomerMapId'];
				// 		$q->bUseInsetId = false;
				// 		$q->build_query();
				// 		$aQuerys[] = $q;
				// 	}
				// }

				
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

		$PaymentId = $data->rowData->id;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		try {

			$d = new deleteq();
			$d->table = 't_paymentitems';
			$d->pks = ['PaymentId'];
			$d->pk_values = [$PaymentId];
			$d->build_query();
			$aQuerys[] = $d;

			$d = new deleteq();
			$d->table = 't_payment';
			$d->pks = ['PaymentId'];
			$d->pk_values = [$PaymentId];
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
