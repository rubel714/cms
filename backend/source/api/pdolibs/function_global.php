<?php

// function checkUserIsAdmin($userId)
// {
//     $db = new Db();

//     //Role list by user defined
//     $sQuery = "SELECT `meta_value` FROM `wp_usermeta` 
// 	WHERE `meta_key` = 'um_multiple_roles' AND `user_id` = $userId;";
//     $rResult = $db->query($sQuery);
//     $roles = array();
//     foreach ($rResult as $row) {
//         $roles = unserialize($row['meta_value']);
//     }

//     //Admin Role list
//     $sQuery = "SELECT `UserRoles` FROM `t_status` WHERE `StatusName` = 'Admin';";
//     $rResult = $db->query($sQuery);
//     $allowRoles = array();
//     foreach ($rResult as $row) {
//         $allowRoles = explode(",", $row['UserRoles']);
//     }

//     // $allowRoles[] = 'administrator';
//     // $allowRoles[] = 'admin';

//     //Checked has admin role of this user
//     if (count(array_intersect($roles, $allowRoles))) {
//         $allowdStatus = 1;
//     } else {
//         $allowdStatus = 0;
//     }
//     return $allowdStatus;
// }

function getCurrentDateTimeSA()
{
    date_default_timezone_set('Asia/Dhaka');
    return date("Y-m-d H:i:s");
}
 

function getDateFormat($value)
{
    $hold = explode('-', $value);
    return $hold[2] . '/' . $hold[1] . '/' . $hold[0];
}

function setDateFormat($value)
{
    $hold = explode('/', $value);
    return $hold[2] . '-' . $hold[1] . '-' . $hold[0];
}

function gettimestampId()
{
    $dbh = new Db();
    $sql = "SELECT CONV( CONCAT( SUBSTRING(uid,16,3), SUBSTRING(uid,10,4), SUBSTRING(uid,1,8)) ,16,10)  DIV 10000 - (141427 * 24 * 60 * 60 * 1000) AS current_mills
				FROM (SELECT UUID() uid) AS alias;";
    $Res = $dbh->query($sql);
    $currentMills = $Res[0]['current_mills'];
    return $currentMills;
}

// function gettimestampIdLog($facilityId)
// {
//     $dbh = new Db();

//     $sql = "SELECT ROUND(UNIX_TIMESTAMP() * 1000) current_mills, IFNULL(MAX(LogSeq),0) LogSeq FROM t_facilitylog WHERE FacilityId={$facilityId} ;";
//     $Res = $dbh->query($sql);
//     $currentMills = $Res[0]['current_mills'] < $Res[0]['LogSeq'] ? ($Res[0]['LogSeq'] + 1) : $Res[0]['current_mills'];
//     return $currentMills;
// }
/*
function getTransactionSequence($data)
{

    $db = new Db();
    $FacilityId = $data['FacilityId'];
    $TransactionTypeId = $data['TransactionTypeId'];
    $TransactionDate = $data['TransactionDate'];
    $sql = "SELECT FacilityCode FROM t_facility WHERE FacilityId =$FacilityId;";
    $fRows = $db->query($sql);
    $FacilityCode = $fRows[0]['FacilityCode'];


    if (array_key_exists('Page', $data)) {
        $case = $data['Page'];
        switch ($case) {
            case 'Patient':

                $sQuery = "SELECT IFNULL(MAX(CONVERT(SUBSTRING_INDEX(pass,'-',-1), UNSIGNED INTEGER))+1, 1) AS SequenceNumber 
            FROM t_patient 
            WHERE  FacilityId =$FacilityId ";
                $aRows = $db->query($sQuery);
                $output = array();
                foreach ($aRows as $row) {

                    return    $SequenceNumber = 'P-' . str_pad($row['SequenceNumber'], 6, 0, STR_PAD_LEFT);
                    $SequenceNo = $FacilityCode . '/P-' . $SequenceNumber;

                    $row['SequenceNumber'] = $SequenceNo;
                    $output[] = $row;
                    return $output[0]['SequenceNumber'];
                }
                break;


            case 'Protocol':

                $sQuery = "SELECT IFNULL(MAX(CONVERT(SUBSTRING_INDEX(code,'-',-1), UNSIGNED INTEGER))+1, 1) AS SequenceNumber 
            FROM t_protocol 
            WHERE  1=1";
                $aRows = $db->query($sQuery);
                $output = array();
                foreach ($aRows as $row) {

                    return    $SequenceNumber = 'PROTO-' . str_pad($row['SequenceNumber'], 4, 0, STR_PAD_LEFT);
                    $SequenceNo = 'PROTO-' . $SequenceNumber;

                    $row['SequenceNumber'] = $SequenceNo;
                    $output[] = $row;
                    return $output[0]['SequenceNumber'];
                }
                break;



            case 'Prescriber':

                $sQuery = "SELECT IFNULL(MAX(CONVERT(SUBSTRING_INDEX(number,'-',-1), UNSIGNED INTEGER))+1, 1) AS SequenceNumber 
            FROM t_prescriber 
            WHERE  1=1";
                $aRows = $db->query($sQuery);
                $output = array();
                foreach ($aRows as $row) {

                    return $SequenceNumber = str_pad($row['SequenceNumber'], 4, 0, STR_PAD_LEFT);
      
                }
                break;



            case 'Dispenser':

                $sQuery = "SELECT IFNULL(MAX(CONVERT(SUBSTRING_INDEX(number,'-',-1), UNSIGNED INTEGER))+1, 1) AS SequenceNumber 
            FROM t_dispenser 
            WHERE  1=1";
                $aRows = $db->query($sQuery);
                $output = array();
                foreach ($aRows as $row) {

                    return $SequenceNumber = str_pad($row['SequenceNumber'], 4, 0, STR_PAD_LEFT);

                
                }
                break;
        }
    } else {




        if ($TransactionDate != '') {
            $hold = explode('/', $TransactionDate);
            $currYear = $hold[2];
            $subCurrYear = substr($currYear, 2);
        } else {
            $currYear = date("Y");
            $subCurrYear = substr($currYear, 2);
        }

        if ($TransactionTypeId == 0 || $TransactionTypeId == 1) {
            $preFix = "REC";
            $tIds = "0,1";
        } else if ($TransactionTypeId == 2) {
            $preFix = "ISS";
            $tIds = "2";
        } else if ($TransactionTypeId == 3) {
            $preFix = "ADJ";
            $tIds = "3";
        } else if ($TransactionTypeId == 4) {
            $preFix = "DIS";
            $tIds = "4";
        } else if ($TransactionTypeId == 999) {
            $preFix = "LAB";
            $tIds = "999";
        }

        $sQuery = "";
        if ($TransactionTypeId === 999) {
            $sQuery = "SELECT IFNULL(MAX(CONVERT(SUBSTRING_INDEX(TransactionNo,'-',-1), UNSIGNED INTEGER))+1, 1) AS SequenceNumber 
                FROM t_test_lab_master 
                WHERE YEAR(TransactionDate) = '$currYear'
                AND FacilityId = $FacilityId";
        } else {
            $sQuery = "SELECT IFNULL(MAX(CONVERT(SUBSTRING_INDEX(TransactionNo,'-',-1), UNSIGNED INTEGER))+1, 1) AS SequenceNumber 
            FROM t_transaction 
            WHERE YEAR(TransactionDate) = '$currYear'
            AND TransactionTypeId IN ($tIds)	
            AND FacilityId =$FacilityId
            AND bStockUpdated IN (0,1)";
        }

        $aRows = $db->query($sQuery);

        $output = array();
        foreach ($aRows as $row) {

            $SequenceNumber = str_pad($row['SequenceNumber'], 4, 0, STR_PAD_LEFT);
            $SequenceNo = $preFix . '-' . $FacilityCode . '/' . $subCurrYear . '-' . $SequenceNumber;

            $row['SequenceNumber'] = $SequenceNo;
            $output[] = $row;
        }
        if ($TransactionDate != '') {
            echo json_encode($output);
        } else {
            return $output[0]['SequenceNumber'];
        }
    }
}

*/

/*
function getOrderSequence($data)
{

    $db = new Db();
    $FacilityId = $data['FacilityId'];
    $OrderDate = $data['OrderDate'];

    if ($OrderDate != '') {
        $hold = explode('/', $OrderDate);
        $currYear = $hold[2];
        $subCurrYear = substr($currYear, 2);
    } else {
        $currYear = date("Y");
        $subCurrYear = substr($currYear, 2);
    }

    $sql = "SELECT FacilityCode FROM t_facility WHERE FacilityId =$FacilityId;";
    $fRows = $db->query($sql);
    $FacilityCode = $fRows[0]['FacilityCode'];

    $sQuery = "SELECT IFNULL(MAX(CONVERT(SUBSTRING_INDEX(OrderNo,'-',-1), UNSIGNED INTEGER))+1, 1) AS OrderSequence 
	FROM t_orderbook 
	WHERE YEAR(OrderDate) = '$currYear' 
	AND FacilityId =$FacilityId";
    $aRows = $db->query($sQuery);

    $output = array();
    foreach ($aRows as $row) {

        $OrderSequence = str_pad($row['OrderSequence'], 4, 0, STR_PAD_LEFT);
        $OrderNo = 'ORD-' . $FacilityCode . '/' . $subCurrYear . '-' . $OrderSequence;
        $row['OrderSequence'] = $OrderNo;
        $output[] = $row;
    }

    if ($OrderDate != '') {
        echo json_encode($output);
    } else {
        return $output[0]['OrderSequence'];
    }
}
*/

// function DMYtoYMD($value)
// {
//     $hold = explode('/', $value);
//     return $hold[2] . '-' . $hold[1] . '-' . $hold[0];
// }

// function  YMDtoDMY($value)
// {

//     $hold = explode('-', $value);
//     return $hold[2] . '/' . $hold[1] . '/' . $hold[0];
// }
// function limit($page_limit, $page_number, $offset)
// {
//     $limit = '';

//     if ($page_number > 0) {
//         $limit = "LIMIT " . $offset . ", " . $page_limit;
//     }

//     return $limit;
// }

// function order($request)
// {
//     $order = '';

//     if (isset($request['params']['order']) && count($request['params']['order'])) {
//         $orderBy = array();
//         for ($i = 0, $ien = count($request['params']['order']); $i < $ien; $i++) {

//             $orderBy[] = '`' . $request['params']['order'][$i]['column'] . '` ' . $request['params']['order'][$i]['dir'];
//         }

//         $order = 'ORDER BY ' . implode(', ', $orderBy);
//     }

//     return $order;
// }

function wh_like($whLikeFields)
{

    $arWh = array();

    foreach ($whLikeFields as $key => $val) {

        $wh = $val . " LIKE :" . "binding_$key";

        $arWh[] = $wh;
    }

    $wh = implode(" OR ", $arWh);

    return " AND (" . $wh . ")";
}


function like_params($sVal, $whLikeFields)
{

    foreach ($whLikeFields as $key => $val) {
        $bindParms["binding_$key"] = "%$sVal%";
    }

    return $bindParms;
}

function bind(&$a, $val, $type)
{
    $key = ':binding_' . count($a);

    $a[] = array(
        'key' => $key,
        'val' => $val,
        'type' => $type
    );

    return $key;
}

function getNumberFormatQTY($value)
{
    global $decimal_digit_, $decimal_point_, $thousands_separator_;
    //number_format ( float $number , int $decimals = 0 , string $dec_point = "." , string $thousands_sep = "," ) :	 

    return  number_format($value, 0, $decimal_point_, $thousands_separator_);
}

function getNumberFormat($value, $decimaldigit = 2)
{
    global $decimal_digit_, $decimal_point_, $thousands_separator_;
    if ($decimaldigit == 0)
        return  number_format($value, $decimal_digit_, $decimal_point_, $thousands_separator_);
    else  return  number_format($value, $decimaldigit, $decimal_point_, $thousands_separator_);
}


/*
function getQuarterId($monthid, $year)
{
    $monthYear = array();
    $monthMapList = array('1' => 3, '2' => 3, '3' => 3, '4' => 6, '5' => 6, '6' => 6, '7' => 9, '8' => 9, '9' => 9, '10' => 12, '11' => 12, '12' => 12);
    $quarterMapList = array('4' => 3, '3' => 12, '2' => 9, '1' => 3);
    $curMonthMapId = $monthMapList[$monthid] - 3;

    if ($curMonthMapId < 1) {
        $monthYear['quarterYear'] = $year - 1;
        $monthYear['quarterMonth'] = 12;
    } else {
        $monthYear['quarterYear'] = $year;
        $monthYear['quarterMonth'] = $curMonthMapId;
    }
    return $monthYear;
}
*/
function checkValidateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
