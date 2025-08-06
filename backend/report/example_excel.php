<?php

/* Database Connection */
include_once ('../env.php');
include_once ('../source/api/pdolibs/pdo_lib.php');
include_once ('../source/api/pdolibs/function_global.php');


/* include PhpSpreadsheet library */
require("PhpSpreadsheet/vendor/autoload.php");

$db = new Db();

$DepartmentId = $_REQUEST['DepartmentId'];
$VisitorId = $_REQUEST['VisitorId'];
$StartDate = $_REQUEST['StartDate'];
$EndDate = $_REQUEST['EndDate'] . " 23-59-59";;
// $TransactionId = $_REQUEST['TransactionId'];
 
$siteTitle = reportsitetitleeng;

function ConvertTakaToWords($takatoconvert)
{
    $Temp = '';
    $Dollars = '';
    $Cents = '';
    $DecimalPlace = 0;
    $count = 0;
    $curlen = 0;
    $MyNumber = 0;

    try {
        $Place = [];
        $Place[1] = " ";
        $Place[2] = " Thousand ";
        $Place[3] = " Lac ";
        $Place[4] = " Crore ";
        $Place[5] = " Thousand ";

        $MyNumber = $takatoconvert;

        $DecimalPlace = strpos($MyNumber, '.');

        if ($DecimalPlace > 0) {
            $Temp = substr($MyNumber, $DecimalPlace + 1) . "00";
            $Temp = substr($Temp, 0, 2);
            $Cents = ConvertTens($Temp);
            $MyNumber = trim(substr($MyNumber, 0, $DecimalPlace));
        }

        $count = 1;

        while ($MyNumber != "") {
            if ($count == 1 || $count == 4) {
                $curlen = 3;
            } else {
                $curlen = 2;
            }

            if (strlen($MyNumber) >= $curlen) {
                $Temp = ConvertHundreds(substr($MyNumber, strlen($MyNumber) - $curlen, $curlen));
            } else {
                $Temp = ConvertHundreds($MyNumber);
            }

            if ($Temp != "") {
                $Dollars = $Temp . $Place[$count] . $Dollars;
            }

            if (strlen($MyNumber) > $curlen) {
                $MyNumber = substr($MyNumber, 0, strlen($MyNumber) - $curlen);
            } else {
                $MyNumber = "";
            }
            $count++;
        }

        switch ($Dollars) {
            case "":
                $Dollars = "";
                break;
            case "One":
                $Dollars = "Taka One";
                break;
            default:
                $Dollars = "Taka " . $Dollars;
                break;
        }

        switch ($Cents) {
            case "":
                $Cents = "";
                break;
            case "One":
                $Cents = " And One Paisa";
                break;
            default:
                $Cents = " And " . $Cents . " Paisa";
                break;
        }

        $rResult = $Dollars . $Cents;

        if ($rResult != "") {
            return $rResult . " Only";
        }
        return '';
    } catch (Exception $e) {
        return "Error In View the Covertion\n " . "Please Check the Convertion ";
    }
}

function ConvertDigit($MyDigit)
{

    switch (intval($MyDigit)) {
        case 1:
            return "One";
        case 2:
            return "Two";
        case 3:
            return "Three";
        case 4:
            return "Four";
        case 5:
            return "Five";
        case 6:
            return "Six";
        case 7:
            return "Seven";
        case 8:
            return "Eight";
        case 9:
            return "Nine";
        default:
            return "";
    }
}

function ConvertTens($MyTens)
{
    $Result = '';

    // Is value between 10 and 19?
    if (intval(substr($MyTens, 0, 1)) == 1) {
        switch (intval($MyTens)) {
            case 10:
                $Result = "Ten ";
                break;
            case 11:
                $Result = "Eleven ";
                break;
            case 12:
                $Result = "Twelve ";
                break;
            case 13:
                $Result = "Thirteen ";
                break;
            case 14:
                $Result = "Fourteen ";
                break;
            case 15:
                $Result = "Fifteen ";
                break;
            case 16:
                $Result = "Sixteen ";
                break;
            case 17:
                $Result = "Seventeen ";
                break;
            case 18:
                $Result = "Eighteen ";
                break;
            case 19:
                $Result = "Nineteen ";
                break;
        }
    } else {
        switch (intval(substr($MyTens, 0, 1))) {
            case 2:
                $Result = "Twenty ";
                break;
            case 3:
                $Result = "Thirty ";
                break;
            case 4:
                $Result = "Forty ";
                break;
            case 5:
                $Result = "Fifty ";
                break;
            case 6:
                $Result = "Sixty ";
                break;
            case 7:
                $Result = "Seventy ";
                break;
            case 8:
                $Result = "Eighty ";
                break;
            case 9:
                $Result = "Ninety ";
                break;
        }

        $Result .= ConvertDigit(substr($MyTens, strlen($MyTens) - 1, 1));
    }

    return $Result;
}

function ConvertHundreds($MyNumber)
{
    $pMyNumber = $MyNumber;
    $Result = '';

    try {
        if (strpos($pMyNumber, ',') > 0 && strlen($pMyNumber) == 2) {
            $pMyNumber = trim(str_replace($pMyNumber, ",", ""));
        }

        if (intval($pMyNumber) == 0) return '';

        $pMyNumber = "000" . $MyNumber;
        $MyNumber = substr($pMyNumber, strlen($pMyNumber) - 3, 3);

        if (substr($MyNumber, 0, 1) != "0") {
            $Result = ConvertDigit(substr($MyNumber, 0, 1)) . " Hundred ";
        }

        if (substr($MyNumber, 1, 1) != "0") {
            $Result .= ConvertTens(substr($MyNumber, 1));
        } else {
            $Result .= ConvertDigit(substr($MyNumber, 2));
        }

        return $Result;
    } catch (Exception $e) {
        return "Error";
    }
}


 function ConvertQuantityToWords($takatoconvert)
{
    $Temp = '';
    $Dollars = '';
    $Cents = '';
    $DecimalPlace = 0;
    $count = 0;
    $curlen = 0;
    $MyNumber = 0;

    try {
        $Place = [];
        $Place[1] = " ";
        $Place[2] = " Thousand ";
        $Place[3] = " Lac ";
        $Place[4] = " Crore ";
        $Place[5] = " Thousand ";

        $MyNumber = $takatoconvert;

        $DecimalPlace = strpos($MyNumber, '.');

        $count = 1;

        while ($MyNumber != "") {
            if ($count == 1 || $count == 4) {
                $curlen = 3;
            } else {
                $curlen = 2;
            }

            if (strlen($MyNumber) >= $curlen) {
                $Temp = ConvertHundreds(substr($MyNumber, strlen($MyNumber) - $curlen, $curlen));
            } else {
                $Temp = ConvertHundreds($MyNumber);
            }

            if ($Temp != "") {
                $Dollars = $Temp . $Place[$count] . $Dollars;
            }

            if (strlen($MyNumber) > $curlen) {
                $MyNumber = substr($MyNumber, 0, strlen($MyNumber) - $curlen);
            } else {
                $MyNumber = "";
            }
            $count++;
        }

        switch ($Dollars) {
            case "":
                $Dollars = "";
                break;
            case "One":
                $Dollars = "One";
                break;
            default:
                $Dollars = "" . $Dollars;
                break;
        }

        $rResult = $Dollars;

        if ($rResult != "") {
            return $rResult;
        }
        return '';
    } catch (Exception $e) {
        return "Error In View the Covertion\n " . "Please Check the Convertion ";
    }
}
 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

$spreadsheet = new Spreadsheet();

	//Activate work sheet
	$spreadsheet->createSheet(0);
	$spreadsheet->setActiveSheetIndex(0);
	$spreadsheet->getActiveSheet(0);
	//work sheet name
	$spreadsheet->getActiveSheet()->setTitle('Data');
	/* Default Font Set */
	$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
	/* Default Font Size Set */
	$spreadsheet->getDefaultStyle()->getFont()->setSize(11);

	/* Border color */
	$styleThinBlackBorderOutline = array('borders' => array('outline' => array('borderStyle' => Border::BORDER_THIN, 'color' => array('argb' => '5a5a5a'))));
	$rn=2;


	
	// $reporttitlelist[] =  $siteTitle;
    $reporttitlelist[] = "Self Conveyance Report";
	// $reporttitlelist[] = "Start Date: ".$StartDate.", End Date: " . $_REQUEST['EndDate'];
	for($p = 0; $p < count($reporttitlelist); $p++){
		
		$spreadsheet->getActiveSheet()->SetCellValue('A'.$rn, $reporttitlelist[$p]);
		$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getFont();
		/* Font Size for Cells */
		$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->applyFromArray(array('font' => array('size' => '13', 'bold' => true)), 'C'.$rn);
		/* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
		$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
		/* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
		$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		/* merge Cell */
		$spreadsheet->getActiveSheet()->mergeCells('A'.$rn.':H'.$rn);
		$rn++;
	 }



/* Font Size for Cells */
$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->applyFromArray(array('font' => array('size' => '13', 'bold' => false)), 'C'.$rn);
/* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
/* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
/* merge Cell */
//$spreadsheet->getActiveSheet()->mergeCells('A3:H3');
//$spreadsheet->getActiveSheet()->mergeCells('A'.$rn.':I'.$rn);
// $rn++;
// //$spreadsheet->getActiveSheet()->mergeCells('A'.$rn.':I'.$rn);
// $rn = $rn+1;


/* HEADER SECTION STARTS */
/* HEADER LEFT SECTION */
// $StartDate = "2023";//isset($resultdata[0]['InvoiceNo']) ? $resultdata[0]['InvoiceNo'] : "";
// $spreadsheet->getActiveSheet()->SetCellValue('B'.$rn, "Inv Price" . " : " . $SubHeaderObj->InvPrice);
// $spreadsheet->getActiveSheet()->getStyle('B'.$rn)->getFont();
// /* Font Size for Cells */
// $spreadsheet->getActiveSheet()->getStyle('B'.$rn)->applyFromArray(array('font' => array('size' => '10', 'bold' => true)), 'B'.$rn);
// /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
// $spreadsheet->getActiveSheet()->getStyle('B'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
// /* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
// $spreadsheet->getActiveSheet()->getStyle('B'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
// /* merge Cell */
// // $spreadsheet->getActiveSheet()->mergeCells('A'.$rn.':B'.$rn);

// // $EndDate = $_REQUEST['EndDate'];//isset($resultdata[0]['ChallanNo']) ? $resultdata[0]['ChallanNo'] : "";
// $spreadsheet->getActiveSheet()->SetCellValue('C'.$rn, "Disc Amt (-)️" . " : " . $SubHeaderObj->DiscAmt);
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getFont();
// /* Font Size for Cells */
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->applyFromArray(array('font' => array('size' => '10', 'bold' => true)), 'C'.$rn);
// /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
// /* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
// /* merge Cell */
// // $spreadsheet->getActiveSheet()->mergeCells('C'.$rn.':D'.$rn);


// $spreadsheet->getActiveSheet()->SetCellValue('E'.$rn, "");
// $spreadsheet->getActiveSheet()->getStyle('E'.$rn)->getFont();
// /* Font Size for Cells */
// $spreadsheet->getActiveSheet()->getStyle('E'.$rn)->applyFromArray(array('font' => array('size' => '10', 'bold' => true)), 'A5');
// /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
// $spreadsheet->getActiveSheet()->getStyle('E'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
// /* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
// $spreadsheet->getActiveSheet()->getStyle('E'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
// /* merge Cell */
// //$spreadsheet->getActiveSheet()->mergeCells('E'.$rn.':H'.($rn+3));
// $rn++;

// $ReceiveDate = isset($resultdata[0]['TransactionDate']) ? getDateFormat($resultdata[0]['TransactionDate']) : "";
// $spreadsheet->getActiveSheet()->SetCellValue('A'.$rn, "Dispense Date" . " : " . $ReceiveDate);
// $spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getFont();
// /* Font Size for Cells */
// $spreadsheet->getActiveSheet()->getStyle('A'.$rn)->applyFromArray(array('font' => array('size' => '10', 'bold' => true)), 'A'.$rn);
// /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
// $spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
// /* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
// $spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
// /* merge Cell */
// $spreadsheet->getActiveSheet()->mergeCells('A'.$rn.':B'.$rn);



// $ApprovedByName = isset($resultdata[0]['ProductId']) ? $resultdata[0]['ProductId'] : "";
// $spreadsheet->getActiveSheet()->SetCellValue('C'.$rn, "Approved By" . " : " . $ApprovedByName);
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getFont();
// /* Font Size for Cells */
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->applyFromArray(array('font' => array('size' => '10', 'bold' => true)), 'A'.$rn);
// /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
// /* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
// /* merge Cell */
// $spreadsheet->getActiveSheet()->mergeCells('C'.$rn.':D'.$rn);

// $rn++;







/* HEADER LEFT SECTION ENDS */
/* Fill Color Change function for Cells */
//cellColor('A1:G4', 'd9e1ec');
//cellColor('A4:G4', '9ab1d1');


/* Value Set for Cells */
// $rn=$rn+1;
$rn=10;
$spreadsheet->getActiveSheet()
        ->SetCellValue('A'.$rn, "Sl#")
        ->SetCellValue('B'.$rn, "Date")
        ->SetCellValue('C'.$rn, "Reason For Entertainment")
        ->SetCellValue('D'.$rn, "Travel Origin to Destination")
        ->SetCellValue('E'.$rn, "Transport Details")
        ->SetCellValue('F'.$rn, "Conveyance (TK)")
        ->SetCellValue('G'.$rn, "Entertainment (TK)")
        ->SetCellValue('H'.$rn, "Dinner Bill (TK)")
		;

/* Font Size for Cells */
$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'A'.$rn);
$spreadsheet->getActiveSheet()->getStyle('B'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'B'.$rn);
$spreadsheet->getActiveSheet()->getStyle('C'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'C'.$rn);
$spreadsheet->getActiveSheet()->getStyle('D'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'D'.$rn);
$spreadsheet->getActiveSheet()->getStyle('E'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'E'.$rn);
$spreadsheet->getActiveSheet()->getStyle('F'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'F'.$rn);
$spreadsheet->getActiveSheet()->getStyle('G'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'G'.$rn);
$spreadsheet->getActiveSheet()->getStyle('H'.$rn)->applyFromArray(array('font' => array('size' => '12', 'bold' => true)), 'H'.$rn);

/* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
$spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('B'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('D'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('E'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('F'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('G'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('H'.$rn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

/* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
// $spreadsheet->getActiveSheet()->getStyle('A'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('B'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
// $spreadsheet->getActiveSheet()->getStyle('D'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('E'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('F'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('G'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('H'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
// $spreadsheet->getActiveSheet()->getStyle('I'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('J'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('K'.$rn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
// $spreadsheet->getActiveSheet()->getStyle('L'.$rn)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);

/* Width for Cells */

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);

/* Wrap text */
// $spreadsheet->getActiveSheet()->getStyle('B'.$rn)->getAlignment()->setWrapText(true);
// $spreadsheet->getActiveSheet()->getStyle('C'.$rn)->getAlignment()->setWrapText(true);

/* border color set for cells */
$spreadsheet->getActiveSheet()->getStyle('A'.$rn.':A'.$rn)->applyFromArray($styleThinBlackBorderOutline);
$spreadsheet->getActiveSheet()->getStyle('B'.$rn.':B'.$rn)->applyFromArray($styleThinBlackBorderOutline);
$spreadsheet->getActiveSheet()->getStyle('C'.$rn.':C'.$rn)->applyFromArray($styleThinBlackBorderOutline);
$spreadsheet->getActiveSheet()->getStyle('D'.$rn.':D'.$rn)->applyFromArray($styleThinBlackBorderOutline);
$spreadsheet->getActiveSheet()->getStyle('E'.$rn.':E'.$rn)->applyFromArray($styleThinBlackBorderOutline);
$spreadsheet->getActiveSheet()->getStyle('F'.$rn.':F'.$rn)->applyFromArray($styleThinBlackBorderOutline);
$spreadsheet->getActiveSheet()->getStyle('G'.$rn.':G'.$rn)->applyFromArray($styleThinBlackBorderOutline);
$spreadsheet->getActiveSheet()->getStyle('H'.$rn.':H'.$rn)->applyFromArray($styleThinBlackBorderOutline);


$sql = "SELECT a.TransactionId id,DATE_FORMAT(a.TransactionDate, '%d-%b-%Y %h:%i:%s %p') AS TransactionDate,
			c.DisplayName, (case when a.CustomerId=38 then concat(d.CustomerName,'-',a.DummyCustomerDesc) else d.CustomerName end) CustomerName,
            a.PublicTransportDesc,a.ApprovedConveyanceAmount,a.ApprovedRefreshmentAmount,a.ApprovedDinnerBillAmount
            ,b.UserCode as UserId,b.UserName,bb.DepartmentName,bbb.DesignationName,e.BusinessLineName
		   FROM t_transaction a
		   inner join t_users b on a.UserId=b.UserId
		   inner join t_department bb on b.DepartmentId=bb.DepartmentId
		   inner join t_designation bbb on b.DesignationId=bbb.DesignationId
		   inner join t_dropdownlist c on a.DropDownListIDForPurpose=c.DropDownListID
		   inner join t_customer d on a.CustomerId =d.CustomerId
		   left join t_businessline e on b.BusinessLineId =e.BusinessLineId
		   where a.TransactionTypeId=1
			AND /*(b.DepartmentId=$DepartmentId OR $DepartmentId=0)
		   AND*/ (a.UserId=$VisitorId)
		   AND (a.TransactionDate BETWEEN '$StartDate' and '$EndDate')
           AND (a.ApprovedConveyanceAmount>0 or a.ApprovedRefreshmentAmount>0)
		   ORDER BY a.TransactionDate DESC;";

$result = $db->query($sql);
// $i = 1;
// $j = $rn+1;

$i=1;
$j=11;

$UserId="";
$UserName="";
$DepartmentName="";
$DesignationName="";
$BusinessLineName="";

$TotalApprovedConveyanceAmount = 0;
$TotalApprovedRefreshmentAmount = 0;
$TotalApprovedDinnerBillAmount = 0;
foreach ($result as $row) {

    if($i == 1){
        /**Take emp info from first row */
        $UserId=$row['UserId'];
        $UserName=$row['UserName'];
        $DepartmentName=$row['DepartmentName'];
        $DesignationName=$row['DesignationName'];
        $BusinessLineName=$row['BusinessLineName'];
    }

    //Wrap Text
    // $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->getAlignment()->setWrapText(true);

    /* Value Set for Cells */
    $spreadsheet->getActiveSheet()
            ->SetCellValue('A' . $j, $i)
            ->SetCellValue('B' . $j, $row["TransactionDate"])
            ->SetCellValue('C' . $j, $row["DisplayName"])
            ->SetCellValue('D' . $j, $row["CustomerName"])
            ->SetCellValue('E' . $j, $row["PublicTransportDesc"])
            ->SetCellValue('F' . $j, $row["ApprovedConveyanceAmount"])
            ->SetCellValue('G' . $j, $row["ApprovedRefreshmentAmount"])
            ->SetCellValue('H' . $j, $row["ApprovedDinnerBillAmount"])
			;

    $TotalApprovedConveyanceAmount += $row["ApprovedConveyanceAmount"];
    $TotalApprovedRefreshmentAmount += $row["ApprovedRefreshmentAmount"];
    $TotalApprovedDinnerBillAmount += $row["ApprovedDinnerBillAmount"];

    /* border color set for cells */
    $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('H' . $j . ':H' . $j)->applyFromArray($styleThinBlackBorderOutline);

    /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
    $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('H' . $j . ':H' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    /* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
    // $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('H' . $j . ':H' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('I' . $j . ':I' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('J' . $j . ':J' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('K' . $j . ':K' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('L' . $j . ':L' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('M' . $j . ':M' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('N' . $j . ':N' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('O' . $j . ':O' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('P' . $j . ':P' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('Q' . $j . ':Q' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('R' . $j . ':R' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


    if ($j % 2 == 0) {
        cellColor('A' . $j . ':H' . $j, 'f6f8fb');
    }

    $i++;
    $j++;
}




/* * ******************Sub header start****************** */

$k = 3;
    /* Value Set for Cells */
    $spreadsheet->getActiveSheet()
            ->SetCellValue('B' . $k, 'NAME OF THE ENTITY:')
            ->SetCellValue('C' . $k, $BusinessLineName)
            ->SetCellValue('F' . $k, 'DATE:')
            ->SetCellValue('G' . $k, date('d-M-Y'))
			;
    
    $k++;
    $k++;
    $spreadsheet->getActiveSheet()
            ->SetCellValue('B' . $k, 'Part I: Payee Information')
			;
    
    $k++;
    $spreadsheet->getActiveSheet()
            ->SetCellValue('B' . $k, 'Employee Name:')
            ->SetCellValue('C' . $k, $UserName)
            ->SetCellValue('F' . $k, 'Employee ID:')
            ->SetCellValue('G' . $k, $UserId)
			;
    
    $k++;
    $spreadsheet->getActiveSheet()
            ->SetCellValue('B' . $k, 'Department:')
            ->SetCellValue('C' . $k, $DepartmentName)
            ->SetCellValue('F' . $k, 'Designation:')
            ->SetCellValue('G' . $k, $DesignationName)
            ;
    
    $k++;
    $k++;
    $spreadsheet->getActiveSheet()
            ->SetCellValue('B' . $k, 'Part II: Record of Expenses:')
            ;
    /* border color set for cells */
    // $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->applyFromArray($styleThinBlackBorderOutline);
    // $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->applyFromArray($styleThinBlackBorderOutline);
    // $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->applyFromArray($styleThinBlackBorderOutline);
    // $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->applyFromArray($styleThinBlackBorderOutline);
    // $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->applyFromArray($styleThinBlackBorderOutline);
    // $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->applyFromArray($styleThinBlackBorderOutline);
    // $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->applyFromArray($styleThinBlackBorderOutline);

    /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
    // $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    // $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

/******************************Sub header End****************************** */



/* * ******************Total start****************** */

    /* Value Set for Cells */
    $spreadsheet->getActiveSheet()
            ->SetCellValue('E' . $j, 'Total')
            ->SetCellValue('F' . $j, $TotalApprovedConveyanceAmount)
            ->SetCellValue('G' . $j, $TotalApprovedRefreshmentAmount)
            ->SetCellValue('H' . $j, $TotalApprovedDinnerBillAmount)
			;

    /* border color set for cells */
    $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->applyFromArray($styleThinBlackBorderOutline);
    $spreadsheet->getActiveSheet()->getStyle('H' . $j . ':H' . $j)->applyFromArray($styleThinBlackBorderOutline);

    /* Text Alignment Horizontal(HORIZONTAL_LEFT,HORIZONTAL_CENTER,HORIZONTAL_RIGHT) */
    $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('H' . $j . ':H' . $j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    /* Text Alignment Vertical(VERTICAL_TOP,VERTICAL_CENTER,VERTICAL_BOTTOM) */
    // $spreadsheet->getActiveSheet()->getStyle('A' . $j . ':A' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':B' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('C' . $j . ':C' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('D' . $j . ':D' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('E' . $j . ':E' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('F' . $j . ':F' . $j)->getAlignment()->setVertical(Alignment::HORIZONTAL_LEFT);
    // $spreadsheet->getActiveSheet()->getStyle('G' . $j . ':G' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('H' . $j . ':H' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('I' . $j . ':I' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('J' . $j . ':J' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('K' . $j . ':K' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('L' . $j . ':L' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('M' . $j . ':M' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('N' . $j . ':N' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('O' . $j . ':O' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('P' . $j . ':P' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('Q' . $j . ':Q' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    // $spreadsheet->getActiveSheet()->getStyle('R' . $j . ':R' . $j)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


    if ($j % 2 == 0) {
        cellColor('A' . $j . ':H' . $j, 'f6f8fb');
    }

    $i++;
    $j++;
/******************************Total End****************************** */


/******************************Footer start****************************** */
$spreadsheet->getActiveSheet()
->SetCellValue('B' . $j, '(The person claiming visit allowance can not request reimbursement for local conveyance and bill for meal reimbursement simultaneously)')
;

$j++;
$spreadsheet->getActiveSheet()
->SetCellValue('B' . $j, 'I hereby certify that all expenses on this report were incurred for business purposes complying with the applicable policy of the entity.')
;

$j++;
$j++;
$spreadsheet->getActiveSheet()
->SetCellValue('B' . $j, 'Reimbursement Receipt')
->SetCellValue('D' . $j, 'Reimbursement Receipt')
;

$j++;
$spreadsheet->getActiveSheet()
->SetCellValue('D' . $j, 'Total Amount to be received:')
->SetCellValue('E' . $j, ($TotalApprovedConveyanceAmount+$TotalApprovedRefreshmentAmount+$TotalApprovedDinnerBillAmount))
;

$j++;
$j++;
$spreadsheet->getActiveSheet()
->SetCellValue('B' . $j, 'Claimants Signature')
->SetCellValue('C' . $j, 'Date')
->SetCellValue('D' . $j, 'Amount in words:')
->SetCellValue('E' . $j, ConvertQuantityToWords($TotalApprovedConveyanceAmount+$TotalApprovedRefreshmentAmount+$TotalApprovedDinnerBillAmount))
;

$j++;
$j++;
$j++;
$spreadsheet->getActiveSheet()
->SetCellValue('B' . $j, 'Authorized Signature')
->SetCellValue('C' . $j, 'Date')
->SetCellValue('D' . $j, 'Receivers Signature')
->SetCellValue('E' . $j, 'Date')
;
/******************************Footer end****************************** */

$exportTime = date("Y-m-d-His", time());
$writer = new Xlsx($spreadsheet);
$file = 'SelfConveyance-' . $exportTime . '.xlsx'; //Save file name
$writer->save('media/' . $file);
header('Location:media/' . $file); //File open location

function cellColor($cells, $color) {
    global $spreadsheet;

    $spreadsheet->getActiveSheet()->getStyle($cells)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
}

?>