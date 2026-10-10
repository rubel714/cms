<?php

require_once(__DIR__ . '/mrworkflow_common.php');

const MR_STEP_THIS = 2;

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = mrGetStepDataList(MR_STEP_THIS);
		break;

	case "approveData":
		$returnData = mrApproveStep($data, MR_STEP_THIS);
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
