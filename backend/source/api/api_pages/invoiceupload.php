<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Large spreadsheets need far more than the default limits to parse.
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

	// case "deleteData":
	// 	$returnData = deleteData($data);
	// break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{

	$InvoiceId = trim($data->InvoiceId); 

	try {
		$dbh = new Db();
		$query = "SELECT a.*, b.UserName as CustomerUserName
		FROM t_invoiceitems a 
		left join t_users b on a.CustomerUserId=b.UserId
		where a.InvoiceId = $InvoiceId
		ORDER BY a.InvoiceItemId ASC;";

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
			$TransactionDate = date("Y-m-d H:i:s");

			$query = "SELECT a.CustomerId, b.CustomerCode, c.BusinessLineCode, a.UserId 
			FROM t_customer_map a 
			inner join t_customer b on a.CustomerId=b.CustomerId
			inner join t_businessline c on a.BusinessLineId=c.BusinessLineId;";

			$resultdata = $dbh->query($query);
			$CustomerUserList = array();
			foreach ($resultdata as $row) {
				// if ($row['UserId'] == $UserId) {
					// $CustomerCode = $row['CustomerCode'];
					$CustomerUserList[$row['CustomerCode']][$row['BusinessLineCode']] = $row["UserId"];
				// }
			}


			
			$query = "SELECT VatRoleCode, VatRate FROM t_vatroles;";
			$resultdata = $dbh->query($query);
			$VatRoleList = array();
			foreach ($resultdata as $row) {
				$VatRoleList[$row['VatRoleCode']] = $row["VatRate"];
			}

			//Insert Master
			$q = new insertq();
			$q->table = 't_invoice';
			$q->columns = ['TransactionDate', 'FileName', 'UserId'];
			$q->values = [$TransactionDate, $FileName, $UserId];
			$q->pks = ['InvoiceId'];
			$q->bUseInsetId = true;
			$q->build_query();
			$aQuerys[] = $q;


			// $fileDir = '../../../media/invoicefiles/' . $FileName;
			$fileDir = STORAGE_PATH . "media/invoicefiles/" . $FileName;
			$rowcounter = 0;

			//Load the uploaded spreadsheet (supports .xlsx, .xls and .csv) using PhpSpreadsheet
			require_once __DIR__ . '/../../../report/PhpSpreadsheet/vendor/autoload.php';
			
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileDir);

			//Only read the "JrnalExtract" sheet
			$sheetName = "JrnalExtract";
			$worksheet = $spreadsheet->getSheetByName($sheetName);
			if ($worksheet === null) {
				return $returnData = [
					"success" => 0,
					"status" => 500,
					"UserId" => $UserId,
					"InvoiceId" => 0,
					"TotalInvoice" => 0,
					"message" => "Sheet '" . $sheetName . "' not found in the uploaded file"
				];
			}

			// Read only columns B to AM, from row 19 down to the last row that contains data.
			// The range starts at column B, so each row is a 0-indexed array where index 0 = column B,
			// matching the column indexes below.
			$startRow = 19;
			$highestRow = $worksheet->getHighestDataRow();

			$rows = [];

			for ($row = $startRow; $row <= $highestRow; $row++) {
				$data = $worksheet->rangeToArray(
					"B{$row}:AM{$row}",
					null,
					true,
					true,
					false
				)[0];

				// Stop if the 5th column (Column F) is empty
				if ($data[4] === null || trim($data[4]) === '') {
					break;
				}

				$rows[] = $data;
			}
// echo "<pre>";
// echo count($rows);
// print_r($rows);
// echo "</pre>";
// exit;
// [0] => Array
//         (
//             [0] => 238021
//             [1] => 2
//             [2] => RVW58
//             [3] => 7/1/2026
//             [4] => 2026/007
//             [5] => 
//             [6] => BGD/SFT/2026/185528
//             [7] => 4001000
//             [8] => Revenue 3rd Party
//             [9] => C
//             [10] => -11,061.00
//             [11] => USD
//             [12] => -90.00
//             [13] => INS-498856
//             [14] => DTT
//             [15] => BGD058
//             [16] => BDDAC01
//             [17] => SFT0900
//             [18] => I999
//             [19] => ZZZ
//             [20] => VAT1500
//             [21] => DZAB00701
//             [22] => ZZZ
//             [23] => SFTINS001
//             [24] => DTT
//             [25] => 7/1/2026
//             [26] => 
//             [27] => 7/1/2026
//             [28] => 
//             [29] => 
//             [30] => 4100000
//             [31] => ZABER & ZUBAIR FABRICS LTD.(HOMETEX)
//             [32] => CARREFOUR
//             [33] => PAGAR, TONGI, GAZIPUR, BANGLA
//             [34] => 4200
//             [35] => Mr. Showqut
//             [36] => 
//             [37] => FINAL RANDOM INSPECTION
//         )
			//Excel/CSV file column index

			$JournalTypeIdx = 2;
			$TransactionDateIdx = 3;
			$AccountingPeriodIdx = 4;
			$TransactionReferenceIdx = 6;//invoice no
			$DebitCreditIdx = 9;
			$BaseAmountIdx = 10;
			$CurrencyCodeIdx = 11;
			$TransactionAmountIdx = 12;
			$DescriptionIdx = 13;
			$AnalysisCode1Idx = 15; //BUSINESS UNIT Analysis Code (Required)			
			$AnalysisCode3Idx = 17; //SEGMENT Analysis Code
			$AnalysisCode6Idx = 20;//VALUE ADDED TAX Analysis Code(Required)
			$AccountCodeIdx = 21; //DEBTORS/ CREDITORS  Analysis Code. Customer Code
			$AnalysisCode9Idx = 23; //BUSINESS SPECIFIC Analysis Code(Required)
			$CustomerNameIdx = 31;
			$SunAccountIdx = 32; //SUN 4 CHART OF ACCOUNT
			$GeneralDescription11Idx = 33;//Buyer name
			$AgentIdx = 34;//Agent(required)
			$GeneralDescription14Idx = 35; // Merchant Name 
			$GeneralDescription17Idx = 36;//Style No
			$GeneralDescription20Idx = 37; //Service Type



			


			// $NameIdx = 0;
			// $BusinessUnitIdx = 1;
			// $BudgetCodeIdx = 2;
			// $AnalysisCode2Idx = 12;
			// $AnalysisCode4Idx = 14;
			// $AnalysisCode5Idx = 15;
			// $AnalysisCode7Idx = 17;
			// $AnalysisCode8Idx = 18;
			// $GeneralDate1Idx = 22;
			// $GeneralDate2Idx = 23;
			// $GeneralDate3Idx = 24;
			// $GeneralDescription9Idx = 25;
			// $GeneralDescription4Idx = 26;
			// $GeneralDescription2Idx = 28;
			// $GeneralDescription12Idx = 29;
			// $GeneralDescription13Idx = 30;
			// $GeneralDescription15Idx = 32;
			// $GeneralDescription16Idx = 33;
			// $GeneralDescription18Idx = 35;
			// $GeneralDescription19Idx = 36;

			$TotalInvoice = 0;
			$mergedRows = array(); //rows grouped by TransactionReference
			foreach ($rows as $data) {
				$rowcounter++;

				// //when this row is blank (all cells empty/null)
				// $isBlankRow = (count(array_filter($data, function ($cell) {
				// 	return trim((string) $cell) !== "";
				// })) == 0);

				// if ($isBlankRow) {
				// 	//when first row is blank then no data
				// 	if ($rowcounter == 1) {
				// 		$returnData = [
				// 			"success" => 0,
				// 			"status" => 500,
				// 			"UserId" => $UserId,
				// 			"InvoiceId" => 0,
				// 			"TotalInvoice" => $TotalInvoice,
				// 			"message" => "There are no invoice in this file"
				// 		];
				// 		break;
				// 	}
				// 	break; //when has blank row then stop loop
				// }

				// //first row use for header and when header then no need operation.
				// if ($rowcounter == 1) {
				// 	continue; //first row script
				// }

				// echo "<pre>";
				// print_r($data);


				$JournalType = $data[$JournalTypeIdx];
				$TransactionDate = $data[$TransactionDateIdx];
				$TransactionDate = DateTime::createFromFormat('n/j/Y', $TransactionDate)->format('dmY');


				$AccountingPeriod = $data[$AccountingPeriodIdx];
				list($year, $month) = explode('/', $AccountingPeriod);
				$AccountingPeriod = $month.$year;


				$TransactionReference = $data[$TransactionReferenceIdx];
				//check if the invoice is already imported
				if(checkDuplicateInvoice($dbh, $TransactionReference)){
					continue;
				}
				$DebitCredit = $data[$DebitCreditIdx];

				$BaseAmount = removeComma($data[$BaseAmountIdx])*(-1); //negative value for credit
				$CurrencyCode = $data[$CurrencyCodeIdx];
				$TransactionAmount = removeComma($data[$TransactionAmountIdx])*(-1); //negative value for credit
				$Description = $data[$DescriptionIdx];
				$AnalysisCode1 = $data[$AnalysisCode1Idx];
				$AnalysisCode3 = $data[$AnalysisCode3Idx];
				$AnalysisCode6 = $data[$AnalysisCode6Idx];
				$AccountCode = $data[$AccountCodeIdx];
				$CustomerName = $data[$CustomerNameIdx];
				$SunAccount = $data[$SunAccountIdx];
				$AnalysisCode9 = $data[$AnalysisCode9Idx];
				$GeneralDescription11 = $data[$GeneralDescription11Idx]; //Buyer name


				$Agent = $data[$AgentIdx];
				$GeneralDescription14 = $data[$GeneralDescription14Idx];
				$GeneralDescription17 = $data[$GeneralDescription17Idx];
				$GeneralDescription20 = $data[$GeneralDescription20Idx];



				/////////////////////////////////Buyer name override for specific buyer list, 
				//if TransactionReference starts with any of the prefix in the list then use SunAccount as Buyer name
					$checkBuyerList = ["BGD/SFT", "BGD/GTS", "BGD/BAS"];
					$matched = false;
					foreach ($checkBuyerList as $prefix) {
						if (str_starts_with($TransactionReference, $prefix)) {
							$matched = true;
						}
					}
					if($matched){
						$GeneralDescription11 = $SunAccount;
					}
				//////////////////////////////////////////////////////////////////////////////////

				// $Name = $data[$NameIdx];
				// $BusinessUnit = $data[$BusinessUnitIdx];
				// $BudgetCode = $data[$BudgetCodeIdx];
				// $Description = $data[$DescriptionIdx];
				// $AnalysisCode1 = $data[$AnalysisCode1Idx];
				// $AnalysisCode2 = $data[$AnalysisCode2Idx];
				// $AnalysisCode3 = $data[$AnalysisCode3Idx];
				// $AnalysisCode4 = $data[$AnalysisCode4Idx];
				// $AnalysisCode5 = $data[$AnalysisCode5Idx];
				// $AnalysisCode6 = $data[$AnalysisCode6Idx];
				// $AnalysisCode7 = $data[$AnalysisCode7Idx];
				// $AnalysisCode8 = $data[$AnalysisCode8Idx];
				// $GeneralDate1 = $data[$GeneralDate1Idx];
				// $GeneralDate2 = $data[$GeneralDate2Idx];
				// $GeneralDate3 = $data[$GeneralDate3Idx];
				// $GeneralDescription9 = $data[$GeneralDescription9Idx];
				// $GeneralDescription4 = $data[$GeneralDescription4Idx];
				// $GeneralDescription2 = $data[$GeneralDescription2Idx];
				// $GeneralDescription12 = $data[$GeneralDescription12Idx];
				// $GeneralDescription13 = $data[$GeneralDescription13Idx];
				// $GeneralDescription15 = $data[$GeneralDescription15Idx];
				// $GeneralDescription16 = $data[$GeneralDescription16Idx];
				// $GeneralDescription18 = $data[$GeneralDescription18Idx];
				// $GeneralDescription19 = $data[$GeneralDescription19Idx];

				$CustomerUserId = null;
				if(array_key_exists($AccountCode, $CustomerUserList)){
					if(array_key_exists($AnalysisCode3, $CustomerUserList[$AccountCode])){
						$CustomerUserId = $CustomerUserList[$AccountCode][$AnalysisCode3];
					}
				}

				//Mrinal bhai confirmed only DebitCredit = C will be save
				if($DebitCredit != "C"){
					continue;
				}

				$BaseAmountWithoutVat = 0;
				$VatAmount = 0;
				if($BaseAmount>0 && array_key_exists($AnalysisCode6,$VatRoleList)){
					$VatRate = $VatRoleList[$AnalysisCode6];
					if($VatRate>0){
						$VatAmount = ($BaseAmount * $VatRate) / (100 + $VatRate);
						$BaseAmountWithoutVat = ($BaseAmount - $VatAmount);
					}else{
						$BaseAmountWithoutVat = $BaseAmount;
					}
				}

				//Merge duplicate rows of the same invoice no: amounts are summed,
				//every other column keeps the value of the first row of that invoice no.
				$mergeKey = trim((string) $TransactionReference);
				if ($mergeKey === '') {
					$mergeKey = '[NoRef]' . $rowcounter; //keep rows without invoice no separate
				}

				if (array_key_exists($mergeKey, $mergedRows)) {
					$mergedRows[$mergeKey]['BaseAmountWithoutVat'] += $BaseAmountWithoutVat;
					$mergedRows[$mergeKey]['VatAmount'] += $VatAmount;
					$mergedRows[$mergeKey]['BaseAmount'] += $BaseAmount;
					$mergedRows[$mergeKey]['TransactionAmount'] += $TransactionAmount;
				} else {
					$mergedRows[$mergeKey] = [
						'AccountCode' => $AccountCode,
						'CustomerName' => $CustomerName,
						'AccountingPeriod' => $AccountingPeriod,
						'DebitCredit' => $DebitCredit,
						'Description' => $Description,
						'JournalType' => $JournalType,
						'BaseAmountWithoutVat' => $BaseAmountWithoutVat,
						'VatAmount' => $VatAmount,
						'BaseAmount' => $BaseAmount,
						'TransactionDate' => $TransactionDate,
						'TransactionReference' => $TransactionReference,
						'AnalysisCode1' => $AnalysisCode1,
						'AnalysisCode3' => $AnalysisCode3,
						'AnalysisCode9' => $AnalysisCode9,
						'TransactionAmount' => $TransactionAmount,
						'CurrencyCode' => $CurrencyCode,
						'GeneralDescription11' => $GeneralDescription11,
						'GeneralDescription14' => $GeneralDescription14,
						'GeneralDescription17' => $GeneralDescription17,
						'GeneralDescription20' => $GeneralDescription20,
						'SunAccount' => $SunAccount,
						'Agent' => $Agent,
						'CustomerUserId' => $CustomerUserId
					];
				}

				// $q = new insertq();
				// $q->table = 't_invoiceitems';
				// $q->columns = ['InvoiceId', 'Name', 'BusinessUnit', 'BudgetCode', 'AccountCode', 'AccountingPeriod', 'DebitCredit', 'Description', 'JournalType','BaseAmountWithoutVat','VatAmount', 'BaseAmount', 'TransactionDate', 'TransactionReference', 'AnalysisCode1', 'AnalysisCode2', 'AnalysisCode3', 'AnalysisCode4', 'AnalysisCode5', 'AnalysisCode6', 'AnalysisCode7', 'AnalysisCode8', 'AnalysisCode9', 'TransactionAmount','ExchangeRate', 'CurrencyCode', 'GeneralDate1', 'GeneralDate2', 'GeneralDate3', 'GeneralDescription9', 'GeneralDescription4', 'GeneralDescription11', 'GeneralDescription2', 'GeneralDescription12', 'GeneralDescription13', 'GeneralDescription14', 'GeneralDescription15', 'GeneralDescription16', 'GeneralDescription17', 'GeneralDescription18', 'GeneralDescription19', 'GeneralDescription20','CustomerUserId'];
				// $q->values = ['[LastInsertedId]', $Name, $BusinessUnit, $BudgetCode, $AccountCode, $AccountingPeriod, $DebitCredit, $Description, $JournalType, $BaseAmountWithoutVat, $VatAmount, $BaseAmount, $TransactionDate, $TransactionReference, $AnalysisCode1, $AnalysisCode2, $AnalysisCode3, $AnalysisCode4, $AnalysisCode5, $AnalysisCode6, $AnalysisCode7, $AnalysisCode8, $AnalysisCode9, $TransactionAmount, $ExchangeRate, $CurrencyCode, $GeneralDate1, $GeneralDate2, $GeneralDate3, $GeneralDescription9, $GeneralDescription4, $GeneralDescription11, $GeneralDescription2, $GeneralDescription12, $GeneralDescription13, $GeneralDescription14, $GeneralDescription15, $GeneralDescription16, $GeneralDescription17, $GeneralDescription18, $GeneralDescription19, $GeneralDescription20, $CustomerUserId];
				// $q->pks = ['InvoiceItemId'];
				// $q->bUseInsetId = false;
				// $q->build_query();
				// $aQuerys[] = $q;
				// $TotalInvoice++;
			}

			// $MaxTransactionDateSort = ""; //Ymd, used only for comparing

			foreach ($mergedRows as $mRow) {

				//TransactionDate is a dmY string, so compare on a sortable Ymd form
				$dt = DateTime::createFromFormat('dmY', (string) $mRow['TransactionDate']);
				if ($dt !== false) {
					$sortDate = $dt->format('Y-m-d');

					if($sortDate <= $OldMaxInvUpdateDate){
						continue;
					}


					// if ($MaxTransactionDateSort === "" || $sortDate > $MaxTransactionDateSort) {
					// 	$MaxTransactionDateSort = $sortDate;
					// }
				}

				$ExchangeRate = 1; //hard code for now
				if($mRow['BaseAmount']>0 && $mRow['TransactionAmount']>0){
					$ExchangeRate = $mRow['BaseAmount'] / $mRow['TransactionAmount'];
				}

				$RowDescription = (string)$mRow['Description'];
				$DescriptionLimit = stripos($RowDescription, 'REV') !== false ? 17 : 12;
				$GeneralDescription9 = strlen($RowDescription) > $DescriptionLimit
					? substr($RowDescription, 0, $DescriptionLimit)
					: $RowDescription;

				$q = new insertq();
				$q->table = 't_invoiceitems';
				$q->columns = ['InvoiceId', 'AccountCode','CustomerName', 'AccountingPeriod', 'DebitCredit', 'Description', 'JournalType','BaseAmountWithoutVat','VatAmount', 'BaseAmount', 'TransactionDate', 'TransactionReference', 'AnalysisCode1', 'AnalysisCode3', 'AnalysisCode9', 'TransactionAmount','ExchangeRate', 'CurrencyCode','GeneralDescription9', 'GeneralDescription11', 'GeneralDescription14', 'GeneralDescription17', 'GeneralDescription20','SunAccount','Agent','CustomerUserId'];
				$q->values = ['[LastInsertedId]', $mRow['AccountCode'], $mRow['CustomerName'], $mRow['AccountingPeriod'], $mRow['DebitCredit'], $mRow['Description'], $mRow['JournalType'], $mRow['BaseAmountWithoutVat'], $mRow['VatAmount'], $mRow['BaseAmount'], $mRow['TransactionDate'], $mRow['TransactionReference'], $mRow['AnalysisCode1'], $mRow['AnalysisCode3'], $mRow['AnalysisCode9'], $mRow['TransactionAmount'], $ExchangeRate, $mRow['CurrencyCode'], $GeneralDescription9, $mRow['GeneralDescription11'], $mRow['GeneralDescription14'], $mRow['GeneralDescription17'], $mRow['GeneralDescription20'], $mRow['SunAccount'], $mRow['Agent'], $mRow['CustomerUserId']];
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
	// $path = "../../../media/invoicefiles";
	$path = STORAGE_PATH . "media/invoicefiles";

	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}

	// $targetDir = '../../../media/invoicefiles';
	$targetDir = STORAGE_PATH . "media/invoicefiles";
	$exploded = explode(',', $base64_string, 2);

	//Detect the real file extension from the data-URL mime type (xlsx, xls or csv)
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

function checkDuplicateInvoice($dbh, $TransactionReference)
{
	$query = "SELECT count(InvoiceItemId ) invCount FROM t_invoiceitems where TransactionReference='$TransactionReference';";
	$resultdata = $dbh->query($query);
	if($resultdata[0]['invCount'] > 0){
		return true;
	}
	return false;
}

function removeComma($value)
{
	return str_replace(',', '', $value);
}