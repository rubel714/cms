<?php
/*
$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getTLDUptakePatientsTrend":
		$returnData = getTLDUptakePatientsTrend($data);
		break;
	case "getTLDTransitionPatientsTrend":
		$returnData = getTLDTransitionPatientsTrend($data);
		break;
	case "getMMDAmongAdultPatientsTrend":
		$returnData = getMMDAmongAdultPatientsTrend($data);
		break;
	case "getMMDCoveragePatientsTrend":
		$returnData = getMMDCoveragePatientsTrend($data);
		break;
	case "getDashboardStockStatusTableData":
		$returnData = getDashboardStockStatusTableData($data);
		break;

	case "getMMDAmongPaediatricsPatientsTrendLess15Years":
		$returnData = getMMDAmongPaediatricsPatientsTrendLess15Years($data);
		break;
			
	default:
		echo "{failure:true}";
		break;
}
 
function getTLDUptakePatientsTrend($data){
	// print_r($data);

	
	try {
		$dbh = new Db();
		global $TEXT;
		// print_r($TEXT);
		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;
		$FacilityId = isset($data->FacilityId) ? $data->FacilityId : 0;
		// $YearId = 2021;
		// $MonthId = 10;

		$months =11;
		//1 means any day in month . there are must use 1 in query day 
		$d=1;//cal_days_in_month(CAL_GREGORIAN,$monthIndex,$yearIndex);
		$EndYearMonth = $YearId."-".str_pad($MonthId,2,"0",STR_PAD_LEFT)."-".$d; 
		$EndYearMonth = date('Y-m-d', strtotime($EndYearMonth));
		$StartYearMonth = date("Y-m-d", strtotime(date("Y-m-d", strtotime($EndYearMonth)) . "-".$months." month"));

		$StartPeriod = explode("-",$StartYearMonth);
		// print_r($StartPeriod);
		$StartYearId = $StartPeriod[0];
		$StartMonthId = (int)$StartPeriod[1];

		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}


		$query = "SELECT a.YearID, a.MonthId, ifnull(sum(a.TldPatient),0) as PatientCount
		FROM mv_tld_tle_mmd_patients a
		where STR_TO_DATE(CONCAT(a.YearID,'/',a.MonthId,'/1'), '%Y/%m/%d') between '".$StartYearMonth."' and '".$EndYearMonth."'
		AND (a.FacilityId = $FacilityId OR $FacilityId=0)
		group by a.YearID, a.MonthId;";
	
		$resultdata = $dbh->query($query);
		$dataList = array(
			'duration'=> $monthList[$StartMonthId] ." ".$StartYearId." " . $TEXT["to"] . " ".$monthList[$MonthId]." ".$YearId,
			'category'=>array(),
			'seriesdata'=>array()
		);
		
		foreach ($resultdata as $key => $arr) {
			$dataList['category'][] = $monthList[$arr["MonthId"]] .' '.$arr["YearID"];
			
			$PatientCount = $arr["PatientCount"];
			settype($PatientCount,'int');
			$dataList['seriesdata'][] = $PatientCount;
		}

		// $dataList['category'] = ["Sep 2020","Oct 2020",	"Nov 2020",	"Dec 2020",	"Jan 2021",
		// 	"Feb 2021",	"Mar 2021",	"Apr 2021",	"May 2021",	"Jun 2021","Jul 2021",	"Aug 20210"];
		// $dataList['seriesdata'] = [5000, 10000, 15000, 20000, 21000, 28000, 35000, 40000, 45000, 50000,55000, 60000];

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


function getTLDTransitionPatientsTrend($data)
{
	try {
		$dbh = new Db();
		global $TEXT;
		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;
		// $YearId = 2021;
		// $MonthId = 10;
		$FacilityId = isset($data->FacilityId) ? $data->FacilityId : 0;

		$months =11;
		//1 means any day in month . there are must use 1 in query day 
		$d=1;//cal_days_in_month(CAL_GREGORIAN,$monthIndex,$yearIndex);
		$EndYearMonth = $YearId."-".str_pad($MonthId,2,"0",STR_PAD_LEFT)."-".$d; 
		$EndYearMonth = date('Y-m-d', strtotime($EndYearMonth));
		$StartYearMonth = date("Y-m-d", strtotime(date("Y-m-d", strtotime($EndYearMonth)) . "-".$months." month"));

		$StartPeriod = explode("-",$StartYearMonth);
		// print_r($StartPeriod);
		$StartYearId = $StartPeriod[0];
		$StartMonthId = (int)$StartPeriod[1];

		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}

		$dataList = array(
			'duration'=> $monthList[$StartMonthId] ." ".$StartYearId." " . $TEXT["to"] . " ".$monthList[$MonthId]." ".$YearId,
			'category'=>array(),
			'seriesdata'=>array(
				[
					"name"=> "TLD",
					"data"=> array(),
				    "color"=> "#002f6c"
				],
				[
					"name"=> "TLE",
					"data"=> array(),
				    "color"=> "#6c6463",
				],
				[
					"name"=> "TX_CURR",
					"data"=> array(),
				    "color"=> "#b60024",
				]
			)
		);


		// $query = "SELECT a.YearID, a.MonthId, 
		// IFNULL(SUM(a.`ActivePatient`),0) AS ActivePatient,
		// MAX((SELECT IFNULL((b.`TldPatient`),0) FROM mv_tld_tle_mmd_patients b WHERE a.YearID = b.YearID AND a.MonthId = b.MonthId AND a.FacilityId = b.`FacilityId`)) AS TldPatient,
		// MAX((SELECT IFNULL((b.`TlePatient`),0) FROM mv_tld_tle_mmd_patients b WHERE a.YearID = b.YearID AND a.MonthId = b.MonthId AND a.FacilityId = b.`FacilityId`)) AS TlePatient

		// FROM `mv_patients` a
		// WHERE STR_TO_DATE(CONCAT(a.YearID,'/',a.MonthId,'/1'), '%Y/%m/%d') between '".$StartYearMonth."' and '".$EndYearMonth."'
		// AND (a.FacilityId = $FacilityId OR $FacilityId=0)
		// GROUP BY a.YearID, a.MonthId;";

		$query = "SELECT p.YearID, p.MonthId, IFNULL(SUM(p.`ActivePatient`),0) AS ActivePatient,
		SUM((SELECT IFNULL((b.`TldPatient`),0) FROM mv_tld_tle_mmd_patients b WHERE p.YearID = b.YearID AND p.MonthId = b.MonthId AND p.FacilityId = b.`FacilityId`)) AS TldPatient,
		SUM((SELECT IFNULL((b.`TlePatient`),0) FROM mv_tld_tle_mmd_patients b WHERE p.YearID = b.YearID AND p.MonthId = b.MonthId AND p.FacilityId = b.`FacilityId`)) AS TlePatient

		FROM (SELECT a.YearID, a.MonthId, a.FacilityId,
		IFNULL(SUM(a.`ActivePatient`),0) AS ActivePatient
		FROM `mv_patients` a
		WHERE STR_TO_DATE(CONCAT(a.YearID,'/',a.MonthId,'/1'), '%Y/%m/%d') BETWEEN '".$StartYearMonth."' AND '".$EndYearMonth."'
		AND (a.FacilityId = $FacilityId OR $FacilityId=0)
		GROUP BY a.YearID, a.MonthId,a.FacilityId) p
		GROUP BY p.YearID, p.MonthId;";


		$resultdata = $dbh->query($query);
		
		foreach ($resultdata as $key => $arr) {
			$dataList['category'][] = $monthList[$arr["MonthId"]] .' '.$arr["YearID"];
			
			$TldPatient = $arr["TldPatient"];
			$TlePatient = $arr["TlePatient"];
			$ActivePatient = $arr["ActivePatient"];
			
			settype($TldPatient,'int');
			settype($TlePatient,'int');
			settype($ActivePatient,'int');
			
			$dataList['seriesdata'][0]['data'][] = $TldPatient; //TLD
			$dataList['seriesdata'][1]['data'][] = $TlePatient; // TLE
			$dataList['seriesdata'][2]['data'][] = $ActivePatient; //TX_CURR
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



function getMMDAmongAdultPatientsTrend($data)
{
	try {
		$dbh = new Db();
		global $TEXT;
		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;
		// $YearId = 2021;
		// $MonthId = 10;
		$FacilityId = isset($data->FacilityId) ? $data->FacilityId : 0;

		$months =11;
		//1 means any day in month . there are must use 1 in query day 
		$d=1;//cal_days_in_month(CAL_GREGORIAN,$monthIndex,$yearIndex);
		$EndYearMonth = $YearId."-".str_pad($MonthId,2,"0",STR_PAD_LEFT)."-".$d; 
		$EndYearMonth = date('Y-m-d', strtotime($EndYearMonth));
		$StartYearMonth = date("Y-m-d", strtotime(date("Y-m-d", strtotime($EndYearMonth)) . "-".$months." month"));

		$StartPeriod = explode("-",$StartYearMonth);
		// print_r($StartPeriod);
		$StartYearId = $StartPeriod[0];
		$StartMonthId = (int)$StartPeriod[1];

		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}

		$dataList = array(
			'duration'=> $monthList[$StartMonthId] ." ".$StartYearId." " . $TEXT["to"] . " ".$monthList[$MonthId]." ".$YearId,
			'category'=>array(),
			'seriesdata'=>array(
				 

				[
					"type"=> "column",
					"name"=> $TEXT["No MMD"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#bfbfbf"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["Less 3 months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#00b050"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["3-5 months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#8b0923"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["6 plus months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#0070c0"
				],
				[
					"type"=> "spline",
					"name"=> $TEXT["% MMD"],
					"yAxis"=> 1,
					"data"=> array(),
					"color"=> "#ff2020",
					"marker"=> [
						"lineWidth"=> 2,
						"lineColor"=> "#ff2020",
						"fillColor"=> "#ffffff"
					]
				]
			)
		);

		$query = "SELECT a.YearID, a.MonthId, 
		IFNULL(SUM(a.`NoMMDAdult`),0) AS NoMMDAdult,
		IFNULL(SUM(a.`ThreeMonthMMDAdult`),0) AS ThreeMonthMMDAdult,
		IFNULL(SUM(a.`FiveMonthMMDAdult`),0) AS FiveMonthMMDAdult,
		IFNULL(SUM(a.`SixMonthMMDAdult`),0) AS SixMonthMMDAdult

		FROM `mv_tld_tle_mmd_patients` a
		WHERE STR_TO_DATE(CONCAT(a.YearID,'/',a.MonthId,'/1'), '%Y/%m/%d') between '".$StartYearMonth."' and '".$EndYearMonth."'
		AND (a.FacilityId = $FacilityId OR $FacilityId=0)
		GROUP BY a.YearID, a.MonthId;";

		$resultdata = $dbh->query($query);
		
		foreach ($resultdata as $key => $arr) {
			$dataList['category'][] = $monthList[$arr["MonthId"]] .' '.$arr["YearID"];
			
			$NoMMDAdult = $arr["NoMMDAdult"];
			$ThreeMonthMMDAdult = $arr["ThreeMonthMMDAdult"];
			$FiveMonthMMDAdult = $arr["FiveMonthMMDAdult"];
			$SixMonthMMDAdult = $arr["SixMonthMMDAdult"];
			
			settype($NoMMDAdult,'int');
			settype($ThreeMonthMMDAdult,'int');
			settype($FiveMonthMMDAdult,'int');
			settype($SixMonthMMDAdult,'int');

			$total = $NoMMDAdult + $ThreeMonthMMDAdult + $FiveMonthMMDAdult + $SixMonthMMDAdult;
			$totalmmd = $ThreeMonthMMDAdult + $FiveMonthMMDAdult + $SixMonthMMDAdult;
			$MMDPercentageAdult = number_format(($totalmmd*100)/$total,2);
			settype($MMDPercentageAdult,'float');

			$dataList['seriesdata'][0]['data'][] = $NoMMDAdult; //No MMD
			$dataList['seriesdata'][1]['data'][] = $ThreeMonthMMDAdult; //<3M months
			$dataList['seriesdata'][2]['data'][] = $FiveMonthMMDAdult; //3-5M months
			$dataList['seriesdata'][3]['data'][] = $SixMonthMMDAdult; //6M+ months
			$dataList['seriesdata'][4]['data'][] = $MMDPercentageAdult; //% MMD
		}

		// $dataList['category'] = ["Sep 2020","Oct 2020",	"Nov 2020",	"Dec 2020",	"Jan 2021",
		// 	"Feb 2021",	"Mar 2021",	"Apr 2021",	"May 2021",	"Jun 2021","Jul 2021",	"Aug 20210"];
		// $dataList['seriesdata'] = array(
		// 		[
		// 			"type"=> "column",
		// 			"name"=> "No MMD",
		// 			"data"=> [3, 2, 1, 3, 4, 2, 6, 7, 3, 6, 4, 3],
		// 			"color"=> "#bfbfbf"
		// 		],
		// 		[
		// 			"type"=> "column",
		// 			"name"=> "3-5 months",
		// 			"data"=> [2, 3, 5, 7, 6, 5, 6, 9, 7, 8, 4, 3],
		// 			"color"=> "#00b0f0"
		// 		],
		// 		[
		// 			"type"=> "column",
		// 			"name"=> "6+ months",
		// 			"data"=> [4, 3, 3, 9, 3, 5, 7, 4, 8, 6, 4, 5],
		// 			"color"=> "#ffc000"
		// 		],
		// 		[
		// 			"type"=> "spline",
		// 			"name"=> "% MMD",
		// 			"data"=> [3, 2, 3, 6, 3, 5, 7, 8, 5, 3, 4, 6],
		// 			"color"=> "#ff2020",
		// 			"marker"=> [
		// 				"lineWidth"=> 2,
		// 				"lineColor"=> "#ff2020",
		// 				"fillColor"=> "#ffffff"
		// 			]
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



function getMMDCoveragePatientsTrend($data)
{
	try {
		$dbh = new Db();
		global $TEXT;
		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;
		$FacilityId = isset($data->FacilityId) ? $data->FacilityId : 0;
		// $YearId = 2021;
		// $MonthId = 10;

		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}


		$dataList = array(
			'duration'=> $monthList[$MonthId] ." ".$YearId,
			'category'=>array(),
			'seriesdata'=>array(
				 [
					"name"=> $TEXT["Less 3 months"],
					"data"=> array(),
					"color"=> "#00b050"
				],
				[
					"name"=> $TEXT["3-5 months"],
					"data"=> array(),
					"color"=> "#8b0923"
				],
				[
					"name"=> $TEXT["6 plus months"],
					"data"=> array(),
					"color"=> "#0070c0"
				],
				[
					"name"=> $TEXT["No MMD"],
					"data"=> array(),
					"color"=> "#bfbfbf"
				] 

				/* [
					"name"=> $TEXT["Less 3 months"],
					"data"=> array(),
					"color"=> "#00b0f0"
				],
				[
					"name"=> $TEXT["3-5 months"],
					"data"=> array(),
					"color"=> "#65B906"
				],
				[
					"name"=> $TEXT["6 plus months"],
					"data"=> array(),
					"color"=> "#ffc000"
				],
				[
					"name"=> $TEXT["No MMD"],
					"data"=> array(),
					"color"=> "#bfbfbf"
				] */
			)
		);


		$query = "SELECT b.FacilityName,a.FacilityId, 

		IFNULL(a.`ThreeMonthMMD`,0) AS ThreeMonthMMD,
		IFNULL(a.`FiveMonthMMD`,0) AS FiveMonthMMD,
		IFNULL(a.`SixMonthMMD`,0) AS SixMonthMMD,
		IFNULL(a.`NoMMD`,0) AS NoMMD
		
		FROM `mv_tld_tle_mmd_patients` a
		INNER JOIN t_facility b ON a.FacilityId=b.FacilityId
		WHERE a.YearId = '$YearId'
		AND a.MonthId = $MonthId
		AND (a.FacilityId = $FacilityId OR $FacilityId=0)
		ORDER BY b.FacilityName, a.FacilityId;";
			
		$resultdata = $dbh->query($query);
		
		foreach ($resultdata as $key => $arr) {
			$dataList['category'][] = $arr["FacilityName"];
			
			$ThreeMonthMMD = $arr["ThreeMonthMMD"];
			$FiveMonthMMD = $arr["FiveMonthMMD"];
			$SixMonthMMD = $arr["SixMonthMMD"];
			$NoMMD = $arr["NoMMD"];
			
			settype($ThreeMonthMMD,'int');
			settype($FiveMonthMMD,'int');
			settype($SixMonthMMD,'int');
			settype($NoMMD,'int');
			
			$dataList['seriesdata'][0]['data'][] = $ThreeMonthMMD; //<3M months
			$dataList['seriesdata'][1]['data'][] = $FiveMonthMMD; //3-5M months
			$dataList['seriesdata'][2]['data'][] = $SixMonthMMD; //6M+ months
			$dataList['seriesdata'][3]['data'][] = $NoMMD; //NoMMD // sir told for add at 9 june 2022
		}

		// $dataList['duration'] = $monthList[$MonthId] ." ".$YearId;
		// $dataList['category'] = ["Abbraccio", "BEKAKOUA","Bouyanyindi", "Cab Soins Bon secours",
        //     "CS Adja Ouere","Cab Soins St Gaèl", "Cab. Méd. Salam","Cab. Saint Luc", "Cab. Soins Boa",
        //     "Cab. Soins Bonrou","Cab. Soins Modestie","Cases Dipoli"];

		// $dataList['seriesdata'] = array(
		// 		[
		// 			"name"=> $TEXT["Less 3 months"],
		// 			"data"=> [5, 3, 4, 7, 2, 3, 5, 4, 8, 3, 4, 5],
		// 			"color"=> "#00b050"
		// 		],
		// 		[
		// 			"name"=> $TEXT["3-5 months"],
		// 			"data"=> [2, 2, 3, 2, 1, 2, 4, 3, 6, 5, 4, 7],
		// 			"color"=> "#8b0923"
		// 		],
		// 		[
		// 			"name"=> $TEXT["6 plus months"],
		// 			"data"=> [3, 4, 4, 2, 5, 4, 6, 8, 5, 7, 2, 3],
		// 			"color"=> "#0070c0"
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

function getMonthList() {
	
	$dbh = new Db();
	$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}

		return $monthList;
	}

function getDashboardStockStatusTableData($data) {

	try{

		$dbh = new Db();
		global $TEXT;
		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;
		// $YearId = 2021;
		// $MonthId = 11;
		$FacilityId = isset($data->FacilityId) ? $data->FacilityId : 0;
		$ItemNo = isset($data->ItemNo) ? $data->ItemNo : 0;

		$dataList = array(
			'category'=>array(),
		 	'series'=>array()
		);

		$mList = getMonthList();

		$months =5;
		//1 means any day in month . there are must use 1 in query day 
		$d=1;//cal_days_in_month(CAL_GREGORIAN,$monthIndex,$yearIndex);
		$EndYearMonth = $YearId."-".str_pad($MonthId,2,"0",STR_PAD_LEFT)."-".$d; 
		$EndYearMonth = date('Y-m-d', strtotime($EndYearMonth));
		$StartYearMonth = date("Y-m-d", strtotime(date("Y-m-d", strtotime($EndYearMonth)) . "-".$months." month"));


		$startDate = strtotime($StartYearMonth);
		$endDate = strtotime($EndYearMonth);
		// echo $endDate;
		$sqlFields="";
		$idx = 1;
		while ($endDate >= $startDate) {
			$monthid=date('m',$startDate);
			$yearid=date('Y',$startDate);
			settype($monthid,"integer");
			settype($yearid,"integer");
			$ym = $mList[$monthid] . ' ' . $yearid;
			$dataList['category']['Month'.$idx] = $ym;

			// $sqlFields=",SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=20216 THEN a.MOS ELSE NULL END) Month1";
			$sqlFields .=",SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=$yearid$monthid THEN a.MOS ELSE NULL END) Month$idx";

			$startDate = strtotime( date('Y/m/d',$startDate).' 1 month');
			$idx++;
			}

// print_r($dataList);

//118= TLD = TDF/3TC/DTG  300/300/50mg TAB-CAP, 1 tablet [PO]
//we are not assing ItemNo in Protocol, that's here itemNo fixed now
		$query = "SELECT b.FacilityName, a.FacilityId $sqlFields
				FROM mv_cfm_stock a
				INNER JOIN t_facility b ON a.FacilityId = b.FacilityId
				WHERE a.Itemno = $ItemNo
				AND (a.FacilityId = $FacilityId OR $FacilityId=0)
				AND STR_TO_DATE(CONCAT(a.Year,'/',a.MonthId,'/1'), '%Y/%m/%d') BETWEEN '$StartYearMonth' AND '$EndYearMonth'
				GROUP BY b.FacilityName, a.FacilityId;";
// echo $query;
// exit;
// $query = "SELECT b.FacilityName, a.FacilityId, 

// 		SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=20216 THEN a.MOS ELSE NULL END) Month1, 
// 		SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=20217 THEN a.MOS ELSE NULL END) Month2, 
// 		SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=20218 THEN a.MOS ELSE NULL END) Month3, 
// 		SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=20219 THEN a.MOS ELSE NULL END) Month4, 
// 		SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=202110 THEN a.MOS ELSE NULL END) Month5, 
// 		SUM(CASE WHEN CONCAT(a.Year,a.MonthId)=202111 THEN a.MOS ELSE NULL END) Month6

// 		FROM mv_cfm_stock a
// 		INNER JOIN t_facility b ON a.FacilityId = b.FacilityId
// 		WHERE a.Itemno = 20
// 		AND STR_TO_DATE(CONCAT(a.Year,'/',a.MonthId,'/1'), '%Y/%m/%d') BETWEEN '2021-06-01' AND '2021-11-01'
// 		GROUP BY b.FacilityName, a.FacilityId;";


		$resultdata = $dbh->query($query);
		$dataList['data'] = $resultdata;
		
		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $dataList
		];

	}catch(PDOException $e){
		$returnData = msg(0,500,$e->getMessage());
	}
	
	return $returnData;
}




function getMMDAmongPaediatricsPatientsTrendLess15Years($data)
{
	try {
		$dbh = new Db();
		global $TEXT;
		$YearId = isset($data->YearId) ? $data->YearId : 0;
		$MonthId = isset($data->MonthId) ? $data->MonthId : 0;
		// $YearId = 2021;
		// $MonthId = 10;
		$FacilityId = isset($data->FacilityId) ? $data->FacilityId : 0;

		$months =11;
		//1 means any day in month . there are must use 1 in query day 
		$d=1;//cal_days_in_month(CAL_GREGORIAN,$monthIndex,$yearIndex);
		$EndYearMonth = $YearId."-".str_pad($MonthId,2,"0",STR_PAD_LEFT)."-".$d; 
		$EndYearMonth = date('Y-m-d', strtotime($EndYearMonth));
		$StartYearMonth = date("Y-m-d", strtotime(date("Y-m-d", strtotime($EndYearMonth)) . "-".$months." month"));

		$StartPeriod = explode("-",$StartYearMonth);
		// print_r($StartPeriod);
		$StartYearId = $StartPeriod[0];
		$StartMonthId = (int)$StartPeriod[1];

		$query0 = "SELECT MonthId, MonthName FROM t_month;";
		$resultdatamonth = $dbh->query($query0);
		$monthList = array();

		foreach ($resultdatamonth as $key => $arrm) {
			$monthList[$arrm["MonthId"]] = $arrm["MonthName"];
		}

		$dataList = array(
			'duration'=> $monthList[$StartMonthId] ." ".$StartYearId." " . $TEXT["to"] . " ".$monthList[$MonthId]." ".$YearId,
			'category'=>array(),
			'seriesdata'=>array(
				/* [
					"type"=> "column",
					"name"=> $TEXT["No MMD"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#bfbfbf"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["Less 3 months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#00b0f0"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["3-5 months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#65B906"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["6 plus months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#ffc000"
				],
				[
					"type"=> "spline",
					"name"=> $TEXT["% MMD"],
					"yAxis"=> 1,
					"data"=> array(),
					"color"=> "#ff2020",
					"marker"=> [
						"lineWidth"=> 2,
						"lineColor"=> "#ff2020",
						"fillColor"=> "#ffffff"
					]
				] */

				[
					"type"=> "column",
					"name"=> $TEXT["No MMD"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#bfbfbf"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["Less 3 months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#00b050"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["3-5 months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#8b0923"
				],
				[
					"type"=> "column",
					"name"=> $TEXT["6 plus months"],
					"yAxis"=> 0,
					"data"=> array(),
					"color"=> "#0070c0"
				],
				[
					"type"=> "spline",
					"name"=> $TEXT["% MMD"],
					"yAxis"=> 1,
					"data"=> array(),
					"color"=> "#ff2020",
					"marker"=> [
						"lineWidth"=> 2,
						"lineColor"=> "#ff2020",
						"fillColor"=> "#ffffff"
					]
				]
			)
		);


		$query = "SELECT a.YearID, a.MonthId, 
		IFNULL(SUM(a.`NoMMDPaediatric`),0) AS NoMMDPaediatric,
		IFNULL(SUM(a.`ThreeMonthMMDPaediatric`),0) AS ThreeMonthMMDPaediatric,
		IFNULL(SUM(a.`FiveMonthMMDPaediatric`),0) AS FiveMonthMMDPaediatric,
		IFNULL(SUM(a.`SixMonthMMDPaediatric`),0) AS SixMonthMMDPaediatric

		FROM `mv_tld_tle_mmd_patients` a
		WHERE STR_TO_DATE(CONCAT(a.YearID,'/',a.MonthId,'/1'), '%Y/%m/%d') between '".$StartYearMonth."' and '".$EndYearMonth."'
		AND (a.FacilityId = $FacilityId OR $FacilityId=0)
		GROUP BY a.YearID, a.MonthId;";

		$resultdata = $dbh->query($query);
		
		foreach ($resultdata as $key => $arr) {
			$dataList['category'][] = $monthList[$arr["MonthId"]] .' '.$arr["YearID"];
			
			$NoMMDPaediatric = $arr["NoMMDPaediatric"];
			$ThreeMonthMMDPaediatric = $arr["ThreeMonthMMDPaediatric"];
			$FiveMonthMMDPaediatric = $arr["FiveMonthMMDPaediatric"];
			$SixMonthMMDPaediatric = $arr["SixMonthMMDPaediatric"];
			
			settype($NoMMDPaediatric,'int');
			settype($ThreeMonthMMDPaediatric,'int');
			settype($FiveMonthMMDPaediatric,'int');
			settype($SixMonthMMDPaediatric,'int');

			$total = $NoMMDPaediatric + $ThreeMonthMMDPaediatric + $FiveMonthMMDPaediatric + $SixMonthMMDPaediatric;
			$totalmmd = $ThreeMonthMMDPaediatric + $FiveMonthMMDPaediatric + $SixMonthMMDPaediatric;
			$MMDPercentagePaediatric = number_format(($totalmmd*100)/$total,2);
			settype($MMDPercentagePaediatric,'float');

			$dataList['seriesdata'][0]['data'][] = $NoMMDPaediatric; //No MMD
			$dataList['seriesdata'][1]['data'][] = $ThreeMonthMMDPaediatric; //<3M months
			$dataList['seriesdata'][2]['data'][] = $FiveMonthMMDPaediatric; //3-5M months
			$dataList['seriesdata'][3]['data'][] = $SixMonthMMDPaediatric; //6M+ months
			$dataList['seriesdata'][4]['data'][] = $MMDPercentagePaediatric; //% MMD
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
*/
