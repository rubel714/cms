<?php
error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);

ini_set('memory_limit', '-1');
set_time_limit(0);

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

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{
	$returnData = [
		"success" => 1,
		"status" => 200,
		"message" => "",
		"datalist" => []
	];

	return $returnData;
}

function dataAddEdit($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}

	$lan = trim($data->lan);
	$UserId = trim($data->UserId);
	$FileNameString = $data->rowData;
	$fileType = isset($data->fileType) ? strtolower(trim($data->fileType)) : 'tips';

	if ($fileType === 'invoicemodule') {
		$refCol = 'E';
		$orderCol = 'P';
	} else {
		$refCol = 'C';
		$orderCol = 'AO';
	}

	try {

		$prefix = 123;
		$FileName = $FileNameString ? ConvertFile($FileNameString, $prefix) : null;
		$fileDir = STORAGE_PATH . "media/invoicefiles/" . $FileName;

		require_once __DIR__ . '/../../../report/PhpSpreadsheet/vendor/autoload.php';

		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileDir);
		$worksheet = $spreadsheet->getActiveSheet();
		if ($worksheet === null) {
			return [
				"success" => 0,
				"status" => 500,
				"UserId" => $UserId,
				"TotalUpdated" => 0,
				"message" => "No sheet found in the uploaded file"
			];
		}

		$headerA1 = strtoupper(trim((string) $worksheet->getCell('A1')->getFormattedValue()));
		if ($fileType === 'invoicemodule') {
			$expectedHeader = 'BUYERNAME';
			$fileTypeLabel = 'Invoice Module';
		} else {
			$expectedHeader = 'LOCATION';
			$fileTypeLabel = 'TIPS';
		}

		if ($headerA1 !== $expectedHeader) {
			return [
				"success" => 0,
				"status" => 500,
				"UserId" => $UserId,
				"TotalInFile" => 0,
				"TotalFound" => 0,
				"TotalNotFound" => 0,
				"TotalUpdated" => 0,
				"message" => "Invalid file for " . $fileTypeLabel . ". Cell A1 must be \"" . ($fileType === 'invoicemodule' ? 'BUYERNAME' : 'Location') . "\"."
			];
		}

		$highestRow = $worksheet->getHighestDataRow();
		$mapRows = array();

		for ($row = 2; $row <= $highestRow; $row++) {
			$TransactionReference = trim((string) $worksheet->getCell($refCol . $row)->getFormattedValue());
			$OrderNumber = trim((string) $worksheet->getCell($orderCol . $row)->getFormattedValue());

			if ($TransactionReference === '' || $OrderNumber === '') {
				continue;
			}

			$mapRows[$TransactionReference] = $OrderNumber;
		}

		if (count($mapRows) == 0) {
			return [
				"success" => 0,
				"status" => 500,
				"UserId" => $UserId,
				"TotalInFile" => 0,
				"TotalFound" => 0,
				"TotalNotFound" => 0,
				"TotalUpdated" => 0,
				"message" => "No TransactionReference / OrderNumber rows found in the file"
			];
		}

		$dbh = new Db();
		$foundSet = array();
		$refKeys = array_keys($mapRows);
		$chunks = array_chunk($refKeys, 500);

		foreach ($chunks as $chunk) {
			$escaped = array();
			foreach ($chunk as $ref) {
				$escaped[] = "'" . str_replace("'", "''", $ref) . "'";
			}

			$query = "SELECT DISTINCT TransactionReference
				FROM t_invoiceitems
				WHERE TransactionReference IN (" . implode(',', $escaped) . ")";
			$existing = $dbh->query($query);

			if (is_array($existing)) {
				foreach ($existing as $row) {
					$foundSet[$row['TransactionReference']] = true;
				}
			}
		}

		$aQuerys = array();
		$datalist = array();
		$rownumber = 0;
		$TotalInFile = count($mapRows);
		$TotalFound = 0;
		$TotalNotFound = 0;

		foreach ($mapRows as $TransactionReference => $OrderNumber) {
			$isFound = isset($foundSet[$TransactionReference]);

			if ($isFound) {
				$u = new updateq();
				$u->table = 't_invoiceitems';
				$u->columns = ['OrderNumber'];
				$u->values = [$OrderNumber];
				$u->pks = ['TransactionReference'];
				$u->pk_values = [$TransactionReference];
				$u->build_query();
				$aQuerys[] = $u;
				$TotalFound++;
			} else {
				$TotalNotFound++;
			}

			$rownumber++;
			$datalist[] = [
				"rownumber" => $rownumber,
				"TransactionReference" => $TransactionReference,
				"OrderNumber" => $OrderNumber,
				"Status" => $isFound ? "Found" : "Not Found"
			];
		}

		$success = 1;
		$status = 200;
		$message = "In file: " . $TotalInFile . ", Found to update: " . $TotalFound . ", Not found: " . $TotalNotFound;

		if (count($aQuerys) > 0) {
			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;
			if ($res['msgType'] != 'success') {
				$message = $res['msg'];
			}
		}

		$returnData = [
			"success" => $success,
			"status" => $status,
			"UserId" => $UserId,
			"TotalInFile" => $TotalInFile,
			"TotalFound" => $TotalFound,
			"TotalNotFound" => $TotalNotFound,
			"TotalUpdated" => $TotalFound,
			"message" => $message,
			"datalist" => $success ? $datalist : []
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function ConvertFile($base64_string, $prefix)
{
	$path = STORAGE_PATH . "media/invoicefiles";

	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}

	$targetDir = STORAGE_PATH . "media/invoicefiles";
	$exploded = explode(',', $base64_string, 2);

	$mime = "";
	if (preg_match('/data:([^;]+)/', $exploded[0], $m)) {
		$mime = strtolower(trim($m[1]));
	}
	$mimeToExt = [
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
		'application/vnd.ms-excel' => 'xls',
	];
	$extention = isset($mimeToExt[$mime]) ? $mimeToExt[$mime] : 'xlsx';

	$decoded = base64_decode($exploded[1]);
	$output_file = date("Y_m_d_H_i_s") . "_" . rand(1, 9999) . "." . $extention;
	file_put_contents($targetDir . "/" . $output_file, $decoded);
	return $output_file;
}
