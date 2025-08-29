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

		$query = "SELECT a.CustomerId AS id, a.CustomerCode,a.CustomerName, 
		a.Designation,  a.ContactPhone, a.CompanyName, a.NatureOfBusiness,
		a.CompanyEmail,a.CompanyAddress,a.IsActive,a.UserId
		, case when a.IsActive=1 then 'Active' else 'In Active' end IsActiveName
		,a.CustomerGroupId,b.CustomerGroupName, '[]' UserMap
		FROM t_customer a
		inner join t_customergroup b on a.CustomerGroupId=b.CustomerGroupId
		ORDER BY a.CustomerName ASC;";

		$resultdatalist = $dbh->query($query);


		$resultdatamap = array();
		$query = "SELECT a.CustomerMapId,a.`CustomerId`, a.`BusinessLineId`,concat(b.`BusinessLineCode`,' - ',b.BusinessLineName) as BusinessLineName, a.`UserId`,c.UserName
			FROM t_customer_map a
			inner join t_businessline b on a.BusinessLineId=b.BusinessLineId
			inner join t_users c on a.UserId=c.UserId;";
		$resultdataItems = $dbh->query($query);
		foreach ($resultdataItems as $r) {
			$CustomerId = $r['CustomerId'];
			$resultdatamap[$CustomerId][] = $r;
		}
		// print_r($resultdatamap);

		$resultdata = array();
		foreach ($resultdatalist as $row) {
			$CustomerId = $row['id'];

			if (array_key_exists($CustomerId, $resultdatamap)) {
				$row['UserMap'] = $resultdatamap[$CustomerId];
			} else {
				$row['UserMap'] = [];
			}

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
		//$BranchId = trim($data->BranchId); 

		$CustomerId = $data->rowData->id;
		$CustomerCode = isset($data->rowData->CustomerCode) && ($data->rowData->CustomerCode !== "") ? $data->rowData->CustomerCode : NULL;
		if (!$CustomerCode) {
			$CustomerCode = date('YmdHis');
		}

		$CustomerName = $data->rowData->CustomerName;
		$CustomerGroupId = $data->rowData->CustomerGroupId;
		$Designation = isset($data->rowData->Designation) && ($data->rowData->Designation !== "") ? $data->rowData->Designation : NULL;
		$ContactPhone = isset($data->rowData->ContactPhone) && ($data->rowData->ContactPhone !== "") ? $data->rowData->ContactPhone : NULL;
		$CompanyName = isset($data->rowData->CompanyName) && ($data->rowData->CompanyName !== "") ? $data->rowData->CompanyName : NULL;
		// $NatureOfBusiness = isset($data->rowData->NatureOfBusiness) && ($data->rowData->NatureOfBusiness !== "")? $data->rowData->NatureOfBusiness : NULL;
		$CompanyEmail = isset($data->rowData->CompanyEmail) && ($data->rowData->CompanyEmail !== "") ? $data->rowData->CompanyEmail : NULL;
		$CompanyAddress = isset($data->rowData->CompanyAddress) && ($data->rowData->CompanyAddress !== "") ? $data->rowData->CompanyAddress : NULL;
		$IsActive = 1; //isset($data->rowData->IsActive) ? $data->rowData->IsActive : 0;


		$manyDataList = isset($data->manyDataList) ? $data->manyDataList : [];
		$manyDeleteDataList = isset($data->manyDeleteDataList) ? $data->manyDeleteDataList : [];


		try {

			$dbh = new Db();
			$aQuerys = array();

			if ($CustomerId == "") {
				$q = new insertq();
				$q->table = 't_customer';
				$q->columns = ['ClientId', 'CustomerCode', 'CustomerName', 'CustomerGroupId', 'Designation', 'ContactPhone', 'CompanyName', 'CompanyEmail', 'CompanyAddress', 'IsActive', 'UserId'];
				$q->values = [$ClientId, $CustomerCode, $CustomerName, $CustomerGroupId, $Designation, $ContactPhone, $CompanyName, $CompanyEmail, $CompanyAddress, $IsActive, $UserId];
				$q->pks = ['CustomerId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys[] = $q;
			} else {
				$u = new updateq();
				$u->table = 't_customer';
				$u->columns = ['CustomerCode', 'CustomerName', 'CustomerGroupId', 'Designation', 'ContactPhone', 'CompanyName', 'CompanyEmail', 'CompanyAddress'];
				$u->values = [$CustomerCode, $CustomerName, $CustomerGroupId, $Designation, $ContactPhone, $CompanyName, $CompanyEmail, $CompanyAddress];
				$u->pks = ['CustomerId'];
				$u->pk_values = [$CustomerId];
				$u->build_query();
				$aQuerys[] = $u;


				$manyDataList = isset($data->manyDataList) ? $data->manyDataList : [];
				$manyDeleteDataList = isset($data->manyDeleteDataList) ? $data->manyDeleteDataList : [];
				// echo "<pre>";

				foreach ($manyDeleteDataList as $key => $CustomerMapId) {
						$d = new deleteq();
						$d->table = 't_customer_map';
						$d->pks = ['CustomerMapId'];
						$d->pk_values = [$CustomerMapId];
						$d->build_query();
						$aQuerys[] = $d;
				}


				foreach ($manyDataList as $key => $obj) {
					// print_r($obj);
					if ($obj->CustomerMapId > 0) {
						$u = new updateq();
						$u->table = 't_customer_map';
						$u->columns = ['BusinessLineId', 'UserId'];
						$u->values = [$obj->BusinessLineId, $obj->UserId];
						$u->pks = ['CustomerMapId'];
						$u->pk_values = [$obj->CustomerMapId];
						$u->build_query();
						$aQuerys[] = $u;
					} else {
						$q = new insertq();
						$q->table = 't_customer_map';
						$q->columns = ['CustomerId', 'BusinessLineId', 'UserId'];
						$q->values = [$obj->CustomerId, $obj->BusinessLineId, $obj->UserId];
						$q->pks = ['CustomerMapId'];
						$q->bUseInsetId = false;
						$q->build_query();
						$aQuerys[] = $q;
					}
				}

				
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

		$CustomerId = $data->rowData->id;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		try {

			$d = new deleteq();
			$d->table = 't_customer_map';
			$d->pks = ['CustomerId'];
			$d->pk_values = [$CustomerId];
			$d->build_query();
			$aQuerys[] = $d;

			$d = new deleteq();
			$d->table = 't_customer';
			$d->pks = ['CustomerId'];
			$d->pk_values = [$CustomerId];
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
