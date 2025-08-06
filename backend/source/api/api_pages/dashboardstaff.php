<?php

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

		// case "getDataList":
		// 	$returnData = getDataList($data);
		// break;

	case "getBasicData":
		$returnData = getBasicData($data);
		break;

	case "getSalesTrendData":
		$returnData = getSalesTrendData($data);
		break;

	case "getExpenseByMonth":
		$returnData = getExpenseByMonth($data);
		break;

	default:
		echo "{failure:true}";
		break;
}

// function getDataList($data){


// 	$ClientId = trim($data->ClientId); 
// 	//$BranchId = trim($data->BranchId); 

// 	try{
// 		$dbh = new Db();
// 		$query = "SELECT RoleId AS id, RoleName, DefaultRedirect
// 		FROM t_roles
// 		ORDER BY `RoleId` ASC;";		

// 		$resultdata = $dbh->query($query);

// 		$returnData = [
// 			"success" => 1,
// 			"status" => 200,
// 			"message" => "",
// 			"datalist" => $resultdata
// 		];

// 	}catch(PDOException $e){
// 		$returnData = msg(0,500,$e->getMessage());
// 	}

// 	return $returnData;
// }



function getBasicData($data)
{
	try {
		$dbh = new Db();
		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;
		$TransactionById = isset($data->TransactionById) ? $data->TransactionById : 0;


		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}

		$dataList = array(
			'Month' => $monthList[$MonthId] . ", " . $YearId,
			'NoofSalesInvoice' => 0,
			'SalesValue' => 0,
			'NoofReceiveInvoice' => 0,
		);


		$query = "SELECT COUNT(`TransactionId`) NoofSalesInvoice, 
		IFNULL(SUM(case when a.TransactionTypeId=2 then a.`NetPaymentAmount` else (a.`NetPaymentAmount`*(-1)) end),0) AS SalesValue
		FROM `t_transaction` a
		WHERE a.ClientId=$ClientId
		AND a.BranchId=$BranchId
		AND a.`TransactionTypeId` in (2,3)
		AND a.BPosted=1
		AND MONTH(a.`TransactionDate`)=$MonthId
		AND YEAR(a.`TransactionDate`)=$YearId
		AND (a.`ServiceBy`=$TransactionById OR $TransactionById=0)";
		$resultdata = $dbh->query($query);
		foreach ($resultdata as $key => $arr) {
			$dataList["NoofSalesInvoice"] = $arr["NoofSalesInvoice"];
			$dataList["SalesValue"] = number_format($arr["SalesValue"],2);
		}

		
		
		$query = "SELECT COUNT(`TransactionId`) NoofReceiveInvoice
		FROM `t_transaction` a
		WHERE a.ClientId=$ClientId
		AND a.BranchId=$BranchId
		AND a.`TransactionTypeId` in (1)
		AND a.BPosted=1
		AND MONTH(a.`TransactionDate`)=$MonthId
		AND YEAR(a.`TransactionDate`)=$YearId
		AND (a.`ServiceBy`=$TransactionById OR $TransactionById=0)";
		$resultdata = $dbh->query($query);
		foreach ($resultdata as $key => $arr) {
			$dataList["NoofReceiveInvoice"] = $arr["NoofReceiveInvoice"];
		}

		


   
		
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $dataList
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function getSalesTrendData($data)
{
	try {
		$dbh = new Db();
		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;

		$months = 5;
		// //1 means any day in month . there are must use 1 in query day 
		$d = cal_days_in_month(CAL_GREGORIAN, $MonthId, $YearId);
		$EndYearMonth = $YearId . "-" . str_pad($MonthId, 2, "0", STR_PAD_LEFT) . "-" . $d;
		$EndYearMonth = date('Y-m-d', strtotime($EndYearMonth));

		$StartYearMonth = date("Y-m-01", strtotime(date("Y-m-d", strtotime($EndYearMonth)) . "-" . $months . " month"));
		// echo $StartYearMonth;
		$StartPeriod = explode("-", $StartYearMonth);
		// // print_r($StartPeriod);
		$StartYearId = $StartPeriod[0];
		$StartMonthId = (int)$StartPeriod[1];

		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}

		$dataList = array(
			'duration' => $monthList[$StartMonthId] . " " . $StartYearId . " to " . $monthList[$MonthId] . " " . $YearId,
			'category' => array(),
			'seriesdata' => array(
				[
					"name" => "Sales (TK)",
					"data" => array(),
					"color" => "green"
				]/*,
				[
					"name"=> "TLE",
					"data"=> array(4,8,9),
				    "color"=> "#6c6463",
				],
				[
					"name"=> "TX_CURR",
					"data"=> array(8,5,1),
				    "color"=> "#b60024",
				]*/
			)
		);



		$query = "SELECT YEAR(a.`TransactionDate`) YearID, MONTH(a.`TransactionDate`) MonthId,
		IFNULL(SUM(a.`NetPaymentAmount`),0) AS NetPaymentAmount
		FROM `t_transaction` a
		WHERE a.`TransactionDate` BETWEEN '" . $StartYearMonth . "' AND '" . $EndYearMonth . "'
		AND a.`ClientId` = $ClientId
		AND a.`BranchId` = $BranchId
		AND a.`TransactionTypeId` = 2
		GROUP BY YEAR(a.`TransactionDate`), MONTH(a.`TransactionDate`)";


		$resultdata = $dbh->query($query);

		foreach ($resultdata as $key => $arr) {
			$dataList['category'][] = $monthList[$arr["MonthId"]] . ' ' . $arr["YearID"];

			// $TldPatient = $arr["TldPatient"];
			// $TlePatient = $arr["TlePatient"];
			$NetPaymentAmount = $arr["NetPaymentAmount"];

			// settype($TldPatient,'int');
			// settype($TlePatient,'int');
			settype($NetPaymentAmount, 'int');

			$dataList['seriesdata'][0]['data'][] = $NetPaymentAmount; //TX_CURR
			// $dataList['seriesdata'][1]['data'][] = $TldPatient; //TLD
			// $dataList['seriesdata'][2]['data'][] = $TlePatient; // TLE
		}

		// $dataList['category'] = ["Sep 2020","Oct 2020",	"Nov 2020",	"Dec 2020",	"Jan 2021",
		// 	"Feb 2021",	"Mar 2021",	"Apr 2021",	"May 2021",	"Jun 2021","Jul 2021",	"Aug 20210"];
		// $dataList['seriesdata'] = array(
		// 		[
		// 			"name"=> "TLD",
		// 			"data"=> [5000, 10000, 15000, 20000, 25000, 30000, 35000, 40000, 45000, 50000,55000, 60000],
		// 		    "color"=> "#002f6c"
		// 		],
		// 		[
		// 			"name"=> "TLE",
		// 			"data"=> [25000, 26000, 27000, 28000, 29000, 30000, 31000, 32000, 33000, 34000,35000, 36000],
		// 		    "color"=> "#6c6463",
		// 		],
		// 		[
		// 			"name"=> "TX_CURR",
		// 			"data"=> [23000, 24000, 23000, 22000, 21000, 20000, 19000, 18000, 17000, 16000,15000, 14000],
		// 		    "color"=> "#b60024",
		// 		]
		// 	);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $dataList
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function getExpenseByMonth($data)
{
	try {
		$dbh = new Db();
		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;

		$months = 5;
		// //1 means any day in month . there are must use 1 in query day 
		$d = cal_days_in_month(CAL_GREGORIAN, $MonthId, $YearId);
		$EndYearMonth = $YearId . "-" . str_pad($MonthId, 2, "0", STR_PAD_LEFT) . "-" . $d;
		$EndYearMonth = date('Y-m-d', strtotime($EndYearMonth));

		$StartYearMonth = date("Y-m-01", strtotime(date("Y-m-d", strtotime($EndYearMonth)) . "-" . $months . " month"));
		// echo $StartYearMonth;
		$StartPeriod = explode("-", $StartYearMonth);
		// // print_r($StartPeriod);
		$StartYearId = $StartPeriod[0];
		$StartMonthId = (int)$StartPeriod[1];

		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}

		$dataList = array(
			'duration' => $monthList[$StartMonthId] . " " . $StartYearId . " to " . $monthList[$MonthId] . " " . $YearId,
			'category' => array(),
			'seriesdata' => array(
				[
					"name" => "Amount (TK)",
					"data" => array(),
					"color" => "cornflowerblue"
				]
			)
		);



		$query = "SELECT YEAR(a.`TransactionDate`) YearID, MONTH(a.`TransactionDate`) MonthId,
		IFNULL(SUM(b.`Amount`),0) AS Amount
		FROM `t_expense` a
		inner join t_expenseitems b on a.TransactionId=b.TransactionId
		WHERE a.`TransactionDate` BETWEEN '" . $StartYearMonth . "' AND '" . $EndYearMonth . "'
		AND a.`ClientId` = $ClientId
		AND a.`BranchId` = $BranchId
		GROUP BY YEAR(a.`TransactionDate`), MONTH(a.`TransactionDate`)";
 

		$resultdata = $dbh->query($query);

		foreach ($resultdata as $key => $arr) {
			$dataList['category'][] = $monthList[$arr["MonthId"]] . ' ' . $arr["YearID"];
			$Amount = $arr["Amount"];
			settype($Amount, 'int');
			$dataList['seriesdata'][0]['data'][] = $Amount; //TX_CURR
		}
 
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $dataList
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}
