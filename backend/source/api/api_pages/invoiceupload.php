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

	// case "deleteData":
	// 	$returnData = deleteData($data);
	// break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{


	// $ClientId = trim($data->ClientId);
	$InvoiceId = trim($data->InvoiceId); 

	try {
		$dbh = new Db();
		$query = "SELECT *
		FROM t_invoiceitems 
		where InvoiceId = $InvoiceId
		ORDER BY `InvoiceItemId` ASC;";

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



function dataAddEdit($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} else {


		$lan = trim($data->lan);
		$UserId = trim($data->UserId);
		$FileNameString = $data->rowData;

		try {

			$dbh = new Db();
			$aQuerys = array();

			$prefix = 123;
			$FileName = $FileNameString ? ConvertFile($FileNameString, $prefix) : null;
			// $InvoiceId = date("ymdH");
			$TransactionDate = date("Y-m-d H:i:s");


			//Insert Master
			$q = new insertq();
			$q->table = 't_invoice';
			$q->columns = ['TransactionDate', 'FileName', 'UserId'];
			$q->values = [$TransactionDate, $FileName, $UserId];
			$q->pks = ['InvoiceId'];
			$q->bUseInsetId = true;
			$q->build_query();
			$aQuerys[] = $q;










			// $datalogfile = "./datatransferlogfile/DxDuYrrZSa7_2020_10_08_02_01_01.csv";  //this is for test
			$fileDir = '../../../media/invoicefiles/' . $FileName;
			$rowcounter = 0;
			$csvFileContext = fopen($fileDir, "r");

			//CSV file column index
			$NameIdx = 0;
			$BusinessUnitIdx = 1;
			$BudgetCodeIdx = 2;
			$AccountCodeIdx = 3;
			$AccountingPeriodIdx = 4;
			$DebitCreditIdx = 5;
			$DescriptionIdx = 6;
			$JournalTypeIdx = 7;
			$BaseAmountIdx = 8;
			$TransactionDateIdx = 9;
			$TransactionReferenceIdx = 10;
			$AnalysisCode1Idx = 11;
			$AnalysisCode2Idx = 12;
			$AnalysisCode3Idx = 13;
			$AnalysisCode4Idx = 14;
			$AnalysisCode5Idx = 15;
			$AnalysisCode6Idx = 16;
			$AnalysisCode7Idx = 17;
			$AnalysisCode8Idx = 18;
			$AnalysisCode9Idx = 19;
			$TransactionAmountIdx = 20;
			$CurrencyCodeIdx = 21;
			$GeneralDate1Idx = 22;
			$GeneralDate2Idx = 23;
			$GeneralDate3Idx = 24;
			$GeneralDescription9Idx = 25;
			$GeneralDescription4Idx = 26;
			$GeneralDescription11Idx = 27;
			$GeneralDescription2Idx = 28;
			$GeneralDescription12Idx = 29;
			$GeneralDescription13Idx = 30;
			$GeneralDescription14Idx = 31;
			$GeneralDescription15Idx = 32;
			$GeneralDescription16Idx = 33;
			$GeneralDescription17Idx = 34;
			$GeneralDescription18Idx = 35;
			$GeneralDescription19Idx = 36;
			$GeneralDescription20Idx = 37;

			$TotalInvoice = 0;
			while (! feof($csvFileContext)) {
				$rowcounter++;
				$csvLine = trim(fgets($csvFileContext));

				//when this row is blank
				if (strlen($csvLine) == 0) {
					//when first row is blank then no data
					if ($rowcounter == 1) {
						$returnData = [
							"success" => 0,
							"status" => 500,
							"UserId" => $UserId,
							"InvoiceId" => 0,
							"TotalInvoice" => $TotalInvoice,
							"message" => "There are no invoice in this file"
						];
						break;
					}
					break; //when has blank row then stop loop
				}

				//first row use for header and when header then no need operation.
				if ($rowcounter == 1) {
					continue; //first row script
				}

				// $datalist = array();
				// $datalist = parse_csv($csvLine);
				//https://www.php.net/manual/en/function.str-getcsv.php
				$data = str_getcsv($csvLine);

				// echo "<pre>";
				// print_r($data);

				$Name = $data[$NameIdx];
				$BusinessUnit = $data[$BusinessUnitIdx];
				$BudgetCode = $data[$BudgetCodeIdx];
				$AccountCode = $data[$AccountCodeIdx];
				$AccountingPeriod = $data[$AccountingPeriodIdx];
				$DebitCredit = $data[$DebitCreditIdx];
				$Description = $data[$DescriptionIdx];
				$JournalType = $data[$JournalTypeIdx];
				$BaseAmount = $data[$BaseAmountIdx];
				$TransactionDate = $data[$TransactionDateIdx];
				$TransactionReference = $data[$TransactionReferenceIdx];
				$AnalysisCode1 = $data[$AnalysisCode1Idx];
				$AnalysisCode2 = $data[$AnalysisCode2Idx];
				$AnalysisCode3 = $data[$AnalysisCode3Idx];
				$AnalysisCode4 = $data[$AnalysisCode4Idx];
				$AnalysisCode5 = $data[$AnalysisCode5Idx];
				$AnalysisCode6 = $data[$AnalysisCode6Idx];
				$AnalysisCode7 = $data[$AnalysisCode7Idx];
				$AnalysisCode8 = $data[$AnalysisCode8Idx];
				$AnalysisCode9 = $data[$AnalysisCode9Idx];
				$TransactionAmount = $data[$TransactionAmountIdx];
				$CurrencyCode = $data[$CurrencyCodeIdx];
				$GeneralDate1 = $data[$GeneralDate1Idx];
				$GeneralDate2 = $data[$GeneralDate2Idx];
				$GeneralDate3 = $data[$GeneralDate3Idx];
				$GeneralDescription9 = $data[$GeneralDescription9Idx];
				$GeneralDescription4 = $data[$GeneralDescription4Idx];
				$GeneralDescription11 = $data[$GeneralDescription11Idx];
				$GeneralDescription2 = $data[$GeneralDescription2Idx];
				$GeneralDescription12 = $data[$GeneralDescription12Idx];
				$GeneralDescription13 = $data[$GeneralDescription13Idx];
				$GeneralDescription14 = $data[$GeneralDescription14Idx];
				$GeneralDescription15 = $data[$GeneralDescription15Idx];
				$GeneralDescription16 = $data[$GeneralDescription16Idx];
				$GeneralDescription17 = $data[$GeneralDescription17Idx];
				$GeneralDescription18 = $data[$GeneralDescription18Idx];
				$GeneralDescription19 = $data[$GeneralDescription19Idx];
				$GeneralDescription20 = $data[$GeneralDescription20Idx];

				$q = new insertq();
				$q->table = 't_invoiceitems';
				$q->columns = ['InvoiceId', 'Name', 'BusinessUnit', 'BudgetCode', 'AccountCode', 'AccountingPeriod', 'DebitCredit', 'Description', 'JournalType', 'BaseAmount', 'TransactionDate', 'TransactionReference', 'AnalysisCode1', 'AnalysisCode2', 'AnalysisCode3', 'AnalysisCode4', 'AnalysisCode5', 'AnalysisCode6', 'AnalysisCode7', 'AnalysisCode8', 'AnalysisCode9', 'TransactionAmount', 'CurrencyCode', 'GeneralDate1', 'GeneralDate2', 'GeneralDate3', 'GeneralDescription9', 'GeneralDescription4', 'GeneralDescription11', 'GeneralDescription2', 'GeneralDescription12', 'GeneralDescription13', 'GeneralDescription14', 'GeneralDescription15', 'GeneralDescription16', 'GeneralDescription17', 'GeneralDescription18', 'GeneralDescription19', 'GeneralDescription20'];
				$q->values = ['[LastInsertedId]', $Name, $BusinessUnit, $BudgetCode, $AccountCode, $AccountingPeriod, $DebitCredit, $Description, $JournalType, $BaseAmount, $TransactionDate, $TransactionReference, $AnalysisCode1, $AnalysisCode2, $AnalysisCode3, $AnalysisCode4, $AnalysisCode5, $AnalysisCode6, $AnalysisCode7, $AnalysisCode8, $AnalysisCode9, $TransactionAmount, $CurrencyCode, $GeneralDate1, $GeneralDate2, $GeneralDate3, $GeneralDescription9, $GeneralDescription4, $GeneralDescription11, $GeneralDescription2, $GeneralDescription12, $GeneralDescription13, $GeneralDescription14, $GeneralDescription15, $GeneralDescription16, $GeneralDescription17, $GeneralDescription18, $GeneralDescription19, $GeneralDescription20];
				$q->pks = ['InvoiceItemId'];
				$q->bUseInsetId = false;
				$q->build_query();
				$aQuerys[] = $q;
				$TotalInvoice++;
			}

			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;
			$message = ($res['msgType'] == 'success') ? "Invoice imported successfully" : $res['msg'];
			$InvoiceId = ($res['msgType'] == 'success') ? $res['InvoiceId'] : 0;

			$returnData = [
				"success" => $success,
				"status" => $status,
				"UserId" => $UserId,
				"InvoiceId" => $InvoiceId,
				"TotalInvoice" => $TotalInvoice,
				"message" => $message
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}

function ConvertFile($base64_string, $prefix)
{
	// $path = "../../../media/invoicefiles/".$prefix;
	$path = "../../../media/invoicefiles";

	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}

	// $targetDir = '../../../media/invoicefiles/'.$prefix;
	$targetDir = '../../../media/invoicefiles';
	$exploded = explode(',', $base64_string, 2);
	// echo "<pre>";
	// print_r($exploded);
	$extention = "csv"; // explode(';', explode('/', $exploded[0])[1])[0];
	$decoded = base64_decode($exploded[1]);
	// $output_file = $prefix . "_cover_" . date("Y_m_d_H_i_s") . "_" . rand(1, 9999) . "." . $extention;
	$output_file = date("Y_m_d_H_i_s") . "_" . rand(1, 9999) . "." . $extention;
	file_put_contents($targetDir . "/" . $output_file, $decoded);
	return $output_file;
}








// function deleteData($data) {

// 	if($_SERVER["REQUEST_METHOD"] != "POST"){
// 		return $returnData = msg(0,404,'Page Not Found!');
// 	}
// 	// CHECKING EMPTY FIELDS
// 	elseif(!isset($data->rowData->id)){
// 		$fields = ['fields' => ['id']];
// 		return $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);
// 	}else{

// 		$CheckId = $data->rowData->id;
// 		$lan = trim($data->lan); 
// 		$UserId = trim($data->UserId); 

// 		try{

// 			$dbh = new Db();

//             $d = new deleteq();
//             $d->table = 't_checklist';
//             $d->pks = ['CheckId'];
//             $d->pk_values = [$CheckId];
//             $d->build_query();
//             $aQuerys = array($d);

// 			$res = exec_query($aQuerys, $UserId, $lan);  
// 			$success=($res['msgType']=='success')?1:0;
// 			$status=($res['msgType']=='success')?200:500;

// 			$returnData = [
// 				"success" => $success ,
// 				"status" => $status,
// 				"UserId"=> $UserId,
// 				"message" => $res['msg']
// 			];

// 		}catch(PDOException $e){
// 			$returnData = msg(0,500,$e->getMessage());
// 		}

// 		return $returnData;
// 	}
// }
