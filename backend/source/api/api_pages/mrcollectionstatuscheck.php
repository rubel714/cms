<?php

require_once(__DIR__ . '/mrworkflow_common.php');

const MR_STEP_THIS = 5;

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = mrGetStepDataList(MR_STEP_THIS);
		break;

	case "getCollectionStatusList":
		$returnData = getCollectionStatusList();
		break;

	case "approveData":
		$returnData = approveData($data);
		break;

	case "rejectData":
		$returnData = mrRejectStep($data, MR_STEP_THIS);
		break;

	case "getApprovalLog":
		$returnData = mrGetApprovalLog($data);
		break;

	default:
		echo "{failure:true}";
		break;
}

function getCollectionStatusList()
{
	try {
		$dbh = new Db();

		$query = "SELECT CollectionStatusId id, CollectionStatusName `name`
			FROM t_collection_status
			ORDER BY CollectionStatusId;";

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $dbh->query($query)
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function approveData($data)
{
	$CollectionStatusId = isset($data->CollectionStatusId) ? trim($data->CollectionStatusId) : '';
	$CollectionDate = isset($data->CollectionDate) ? trim($data->CollectionDate) : '';

	if ($CollectionStatusId == '' || $CollectionDate == '') {
		return msg(0, 422, 'Please enter the collection status and collection date.');
	}

	return mrApproveStep(
		$data,
		MR_STEP_THIS,
		['CollectionStatusId', 'CollectionDate'],
		[$CollectionStatusId, $CollectionDate]
	);
}
