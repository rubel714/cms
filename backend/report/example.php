<?php

include_once ('../env.php');
include_once ('../source/api/pdolibs/pdo_lib.php');
include_once ('../source/api/pdolibs/function_global.php');

$db = new Db();
// $lan = isset($_REQUEST['lan']) ? $_REQUEST['lan'] : "en_GB";
// $menukey = isset($_REQUEST['menukey']) ? $_REQUEST['menukey'] : "";
// include_once ('../source/api/languages/lang_switcher_custom.php');

// $gTEXT = $TEXT;
// $UseFor = $_REQUEST['UseFor'];
// $FacilityId = $_REQUEST['FacilityId'];
// $TransactionId = 77;
// $FromInvoiceNo = $_REQUEST['FromInvoiceNo'];
// $ToInvoiceNo = $_REQUEST['ToInvoiceNo'];
// $PrintType = 0;
// $TransactionTypeId = 1;
// $InvType = 1;



$date = date('d/m/Y');
//echo $date;
 

$siteTitle = reportsitetitleeng;
///////////////////////////////////////////////*********Convert Function
	
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
 


$ItemCount = 0;

//============================================================+
// File name   : example_008.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 008 for TCPDF class
//               Include external UTF-8 text file
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Include external UTF-8 text file
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
//require_once('tcpdf_include.php');

//include 'PDFMerger/PDFMerger.php';




require_once('TCPDF-master/examples/tcpdf_include.php');

/* $arrayPageCopy=array("1st copy: For receiving store","2nd copy: For Dhaka CWH with counter signature from receiving store",
	"3rd copy: For Government/Private carrier's copy",
	"4th copy: For Dhaka CWH own copy",
	"5th copy: For DD's copy"); */

    // Extend the TCPDF class to create custom Header and Footer
    /* class MYPDFWITHCUSTOMFOOTER extends TCPDF {
        // Page footer
        public function Footer() {
          
        }
    }


    class MYPDF extends MYPDFWITHCUSTOMFOOTER {
        public function Header() {
            
           
        }
    }

   

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); */

class MYPDF extends TCPDF {

	//Page header
	
	public function Header() {
		
	}

	// Page footer
	public function Footer() {
		
	}
}





$pdfFileNameArray = [];
$m=0;


    // $sqlLoopCheck = "SELECT 
    // a.ItemSl 
    // FROM indentitems a 
    // INNER JOIN itemlist_master b ON a.ItemCode=b.ItemCode
    // WHERE a.FacilityCode = '$FacilityId' AND a.InvType = $TransactionTypeId AND a.InvNo = $ij
    // ORDER BY a.ItemSl;";
    // $sqlLoop1resultCheck = $db->query($sqlLoopCheck);
    // if ($sqlLoop1resultCheck[0]['ItemSl'] == ''){
    //     continue; 
    // }
   
    // $TransactionIdNew = $ij;
    
    // $sqlf = "SELECT `FacilityName`,`Address` 
    // FROM `warehouse`
    // WHERE FacilityCode = '$FacilityId';";
    // $resultFaciliy = $db->query($sqlf);

   


    // $sqlLoop1 = "SELECT 
    //     a.ItemSl AS TransactionItemId,
    //     b.mItemID AS ItemNo, 
    //     b.ItemName, 
    //     a.ItemCode, 
    //     0 AS UnitPrice, 
    //     a.IssueQty AS Quantity, 
    //     0 AS LineTotal,
    //     a.LotNo AS BatchNo,
    //     DATE_FORMAT(a.MfgDate,'%d/%m/%Y') AS MfgDate, 
    //     DATE_FORMAT(a.ExpDate,'%d/%m/%Y') AS ExpiryDate,
    //     a.CurStockQty,@p:=@p+1 AS SKU,
    //     c.UnitName,
    //     a.NoOfCartons,
    //     a.ItemListLotId
    //     FROM indentitems a 
    //     INNER JOIN itemlist_master b ON a.ItemCode=b.ItemCode
    //     INNER JOIN unitofmeas c ON b.UnitId=c.UnitId,(SELECT @p:= 0) AS p
    //     WHERE a.FacilityCode = '$FacilityId' AND a.InvType = $TransactionTypeId AND a.InvNo = $TransactionIdNew
    //     ORDER BY a.ItemSl;";
    //     //	echo $sql;exit;
    //     $sqlLoop1result = $db->query($sqlLoop1);

    $sqlf = "SELECT `InvoiceNo`,`CustomerId` 
    FROM `t_transaction`
    union all
    SELECT `InvoiceNo`,`CustomerId` 
    FROM `t_transaction`
    ;"; 
    $sqlLoop1result = $db->query($sqlf);
        
$dataList = '';

$sl = 1;


foreach ($sqlLoop1result as $result) {
    
    $dataList.= '<tr style="font-size: 12px;">
    <td  style="width:3% !important;" class="border_Remove">' .$sl++.'</td>
    <td style="width:8% !important;" class="border_Remove">'.$result['TransactionId'].'</td>
    <td style="width:34% !important;"class="border_Remove">'.$result['TransactionId'].'</td> 
   
    <td style="width:9% !important;" class="right-aln border_Remove">'.getNumberFormatQTY($result['TransactionId']).'</td> 
    <td style="width:20% !important;" class="border_Remove">'.ConvertQuantityToWords($result['TransactionId']).'</td>
    <td style="width:5% !important;" class="center-aln border_Remove">'. $result['TransactionId'].'</td>
    <td style="width:7% !important; text-align:center;" class="border_Remove">'.$result['TransactionId'].'</td>
    <td style="width:8% !important;" class="center-aln border_Remove">'. $result['TransactionId'].'</td>
    <td style="width:8% !important;" class="center-aln border_Remove">'. $result['TransactionId'].'</td>
    </tr>';

    $ItemCount++;
   
}


// $sqlLoop2 = "SELECT DATE(`PrepDate`) TransactionDate, a.InvNo AS TransactionNo,transferFacilityCode,PrepBy,k.AppBy1,IssuedBy, 
//     a.InvNo TransactionId, a.FacilityCode AS FacilityId,'[]' ManyJsonSave, (bUpdated+BCancel) AS bStockUpdated,
//     bUpdated,CarriersName, Remarks,BCancel,b.FacilityName,b.Address,d.EmpName,e.`EmpName` AS Approved,
//     f.`EmpName` AS Issued_By,DATE_FORMAT(a.`IssuedDate`,'%d/%m/%Y') AS IssuedDate ,
//     DATE_FORMAT(a.`PrepDate`,'%d/%m/%Y') AS PrepDate ,DATE_FORMAT(a.`AppDate1`,'%d/%m/%Y') AS AppDate1,
//     a.`CarriersName` AS driverName,
//     case when(SUBSTRING(a.transferFacilityCode,1,1)='T') then CONCAT('UFPO, ',g.FacilityName)
//     when (SUBSTRING(a.transferFacilityCode,1,1)='R') then CONCAT('RSO, ',g.FacilityName)
//     when (SUBSTRING(a.transferFacilityCode,1,1)='D') then CONCAT('RSO, ',g.FacilityName)
//     else g.FacilityName end issueTo,

//     k.`VehicleNo`,xx.`Designation` AS PrepByDesignation,
//     yy.`Designation`AS AppByDesignation,mm.`Designation` AS IssuedByDesignation
//     FROM indent a 
//     INNER JOIN warehouse b ON a.FacilityCode = b.FacilityCode 
//     INNER JOIN employee d ON a.`FacilityCode` = d.`FacilityCode` AND d.EmpCode=a.PrepBy 
//     INNER JOIN designation xx ON d.`DesigCode` = xx.`DesigCode` AND xx.UseFor = '$UseFor'
//     INNER JOIN employee e ON e.`EmpCode` = a.`AppBy1` AND e.`FacilityCode`=a.`FacilityCode` 
//     INNER JOIN designation yy ON e.`DesigCode` = yy.`DesigCode` AND yy.UseFor = '$UseFor'
//     INNER JOIN employee f ON f.EmpCode= a.IssuedBy AND f.`FacilityCode`=a.`FacilityCode` 
//     INNER JOIN designation mm ON f.`DesigCode` = mm.`DesigCode` AND mm.UseFor = '$UseFor'
//     INNER JOIN facility AS g ON a.transferFacilityCode = g.FacilityCode AND g.UpazilaCode = '$FacilityId' 
//     LEFT JOIN gatepassitems h ON a.`InvNo` = h.`InvNo` AND a.InvType = h.InvType AND h.`FacilityCode` = a.`FacilityCode`
//     LEFT JOIN gatepass k ON h.`GPassNo` = k.`GPassNo` AND a.`FacilityCode` = k.`FacilityCode`
//     WHERE a.FacilityCode = '$FacilityId' AND a.InvType = $InvType AND a.InvNo = $TransactionIdNew;"; 

$sqlLoop2 = "SELECT `InvoiceNo`,`CustomerId` 
FROM `t_transaction`
union all
SELECT `InvoiceNo`,`CustomerId` 
FROM `t_transaction`
;"; 
    
//echo $sql;exit;
$sqlLoop2resultdata = $db->query($sqlLoop2);

// $IsbStockUpdated = $sqlLoop2resultdata[0]["bStockUpdated"] ? "" : " *" ;

$IsbStockUpdated = "11111";


    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


    //$pdf->Header();
    // set document information
    $pdf->setCreator(PDF_CREATOR);
    $pdf->setAuthor('Inspection');
    $pdf->setTitle('Machinery Service Report');

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    // $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->setMargins(5, 4, 8);
    $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->setFooterMargin(PDF_MARGIN_FOOTER);


    // $pdf->setFooterMargin(1500);
    // $pdf->SetX(-100);
    // $pdf->SetY(-45);
    // $pdf->setCellPadding(5,5,5,200);
    // set auto page breaks
    // $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // $pdf->SetMargins(5, 5, 5, 200);
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------
    $pdf->SetAutoPageBreak(true, 10);
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);

    // set font
    $pdf->setFont('times', '', 12);

    // add a page
    $pdf->AddPage('A4', 'Portrait');

    // follwoing margin work from 2nd page
    $pdf->setMargins(5, 0, 8);

    //=================------Start Office Copy-----==========================

    
    $tblHeader0 ='<!DOCTYPE html>
            <html>
                <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />	
                    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
                
                        <style>
                                .center-aln {
                                    text-align: center !important;
                                    margin-top:0px!important;
                                    
                                    margin-bottom:1px!important;
                                }
                                h2,h3{
                                    margin:0px !important;
                                    padding:0px !important;
                                    line-height: 8px !important;
                                }

                                .underlined { 
                                    border-bottom: 0.30em solid #000;
                                }
                            
                                .fontsizechange tr th{
                                    font-size: 12px;
                                    //font-style: italic;
                                    font-weight: bold;
                                    vertical-align: middle !important;
                                    
                                }
                                .fontsizechange tr td
                                {
                                    font-size: 10px;
                                    vertical-align: middle !important;
                                    
                                }

                            
                                body {
                                    color:black;
                                    font-size: 10px;
                                }
                                table.display tr.even.row_selected td {
                                    background-color: #4DD4FD;
                                }    
                                table.display tr.odd.row_selected td {
                                    background-color: #4DD4FD;
                                }
                                .SL{
                                    text-align: center !important;
                                }
                                .right-aln{
                                    text-align: right !important;
                                }
                                .left-aln{
                                    text-align: left !important;
                                }
                                .center-aln {
                                    text-align: center !important;
                                    margin-top:0px!important;
                                    
                                    margin-bottom:1px!important;
                                }

                        </style>  
                </head>
                        
                    <h2 class="center-aln" style="font-size: 15px; ">'.'Government_of_the_People_Republic_of_BD'.'</h2>
                    <h2 class="center-aln" style="font-size: 15px; ">'.'Directorate_General_of_Family_planning'.'</h3>
                    <h3 class="center-aln" style="font-size: 12px;">'.'FacilityName'.'</h3> 
               

                    <table style="padding-left:0px; margin-left:0px; font-size: 11px; solid #403c3c;" class="table display" width="100%" cellspacing="0">
                    <tbody >
                        <tr>
                            <td style="width:43%;"></td>
                            <td style="width:15%; font-size: 13px;" align="center" class="underlined"><span><b>'."Issue Voucher".'</b> </span>'.$IsbStockUpdated.'</td>
                            <td style="width:17%;"></td>
                            <td style="width:30%;" >
                            <b><span>'.'Office Copy'.'</span>  </b>
                            </td>    
                        </tr>
            
                    <tr >
                        <td style="width:75%;">
                             <b> <span>'.'Issuign Office'.'</b></span>&nbsp;: '.$sqlLoop2resultdata[0]['InvoiceNo'].'
                        </td>
                        <td style="width:25%;" >
                         <span><b>'.'Invoice No'.'</b></span> &nbsp; &nbsp; '.'222222222'.' 
                        
                        </td>  
                    </tr>
            
                    <tr>
                        <td style="width:75%;">
                        <b> <span>'.'Issue To'.'</b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$sqlLoop2resultdata[0]['InvoiceNo'].'
                        </td>
                        <td style="width:25%;" >
                        <span><b>'.'Print Date'.'</b></span> &nbsp;:&nbsp; '.'dddddddaaaaaatttteee'.'
                        
                        </td>  
                    </tr>
                    <tr>
                        <td style="width:75%;">
                                &nbsp;
                        </td>
                        <td style="width:25%;" >
                      
                        </td>  
                    </tr>
                   
            
                    </tbody>
                </table>


                <table class="fontsizechange" cellpadding="2"  border="1"  cellspacing="0" width="100%" >
                <tbody >
                        <thead>
                            <tr class="ittaliy">
                                <th rowspan="2" style="width:3% !important;" class="center-aln" >'. 'Sl#'.'</th>
                                <th rowspan="2" style="width:8% !important;">'. 'Code'.'</th>
                                <th rowspan="2" style="width:34% !important;">'. 'Article'.'</th>
                                
                                <th colspan="2" style="width:29% !important;padding: 0px;" class="center-aln">'. 'Quantity'.'</th>
                                <th rowspan="2" style="width:5% !important;" class="center-aln">'. 'Unit'.'</th>
                                <th rowspan="2" style="width:7% !important; text-align:center;" class="center-aln">'. 'No Of Cartons'.'</th>
                                <th rowspan="2" style="width:8% !important;">'. 'Lot No'.'</th>
                                <th rowspan="2" style="width:8% !important;" class="center-aln">'. 'Expiry Date'.'</th>
                            </tr>
                        
                            <tr class="ittaliy" style="font-size: 12px;">
                                <th style="width:9% !important;" class="right-aln">'. 'In Number'.'</th>
                                <th style="width:20% !important;" class="left-aln">'. 'In Words'.'</th>
                            </tr>
                        </thead>
                        '.$dataList.'
                        
                </tbody>
                <tfoot>
                   <!-- <tr>
                        <td colspan="10" class="left-aln">'. 'Remarks'.' :'.'3333333'.'</td>
                        
                    </tr>-->
                </tfoot>
            </table>

            <div >
                <span style="font-size: 12px;" >'. 'Remarks'.' :'.$sqlLoop2resultdata[0]['InvoiceNo'].'</span>
            </div>
        


        </html>';
	
    //output the HTML content
	$pdf->writeHTML($tblHeader0, true, false, true, false, '');



    //$pdf->setMargins(5, 15, 8);

$pdf->SetY(110);

// $tblp = '
// <style>
// .underlined2 { 
//     border-bottom: 0.30em solid #000;
    
// }
// .underlined { 
//     border-bottom: 0.30em solid #000;       
// }
// </style>
// </br></br></br></br></br><div style="margin-top:30px;">


//             <table style="padding-left:0px; margin-left:0px; font-size: 11px; solid #403c3c;" class="table display" width="100%" cellspacing="0">
//                 <tbody >
//                     <tr>
//                          <td style="width:5%;" > </td>

//                         <td style="width:25%; font-size: 11px;" align="center">
//                             <div class="underlined"><b>'.$sqlLoop2resultdata[0]['InvoiceNo'].'</b></div>
                           
//                                 Prepared & Issued By
//                             <br>
//                                  Store In Charge
//                             <br>
//                                 '.$sqlLoop2resultdata[0]['InvoiceNo'].','.$sqlLoop2resultdata[0]['InvoiceNo'].'
//                         </td>

//                         <td style="width:5%;" > </td>

//                         <td style="width:25%; font-size: 11px;" align="center">
//                             <div class="underlined"><b>'.$sqlLoop2resultdata[0]['InvoiceNo'].'</b> </div>
//                                 Approved By
//                             <br>
//                                 Upazila Family Planning Officer
//                             <br>
//                                 '.$sqlLoop2resultdata[0]['InvoiceNo'].','.$sqlLoop2resultdata[0]['InvoiceNo'].'
                           
//                         </td>

//                         <td style="width:5%;" > </td>

//                         <td style="width:25%; font-size: 11px;" align="center">
//                             <div class="underlined"></div>
//                             Received By
//                             <br>
//                              '.$sqlLoop2resultdata[0]['InvoiceNo'].'
//                         </td>   
                        
//                         <td style="width:5%;" > </td>
//                     </tr>


//                 </tbody>
//             </table>



//     </div>';

// $pdf->writeHTML($tblp, true, false, true, false, '');

//=================------End Office Copy-----============================


$pdf->SetY(150);

// $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 0, 0)));
// $htmlHr ='<hr>';
// $pdf->writeHTML($htmlHr, true, false, true, false, '');

/* $style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);

$pdf->Line(PDF_MARGIN_LEFT, $pdf->getY(), $pdf->getPageWidth()-PDF_MARGIN_LEFT, $pdf->getY());
$pdf->Ln(); */

 //=================------Receipient Office Copy-----==========================
// Line

$pdf->SetMargins(5, 60, 8);

//  $tblReceipient ='<!DOCTYPE html>
//  <html>
//      <head>
//          <meta name="viewport" content="width=device-width, initial-scale=1.0" />	
//          <meta http-equiv="content-type" content="text/html; charset=utf-8" />
     
//              <style>
//                      .center-aln {
//                          text-align: center !important;
//                          margin-top:0px!important;
                         
//                          margin-bottom:1px!important;
//                      }
//                      h2,h3{
//                          margin:0px !important;
//                          padding:0px !important;
//                          line-height: 8px !important;
//                      }

//                      .underlined { 
//                          border-bottom: 0.30em solid #000;
//                      }
                 
//                      .fontsizechange tr th{
//                          font-size: 12px;
//                          //font-style: italic;
//                          font-weight: bold;
//                          vertical-align: middle !important;
                         
//                      }
//                      .fontsizechange tr td
//                      {
//                          font-size: 10px;
//                          vertical-align: middle !important;
                         
//                      }

                 
//                      body {
//                          color:black;
//                          font-size: 10px;
//                      }
//                      table.display tr.even.row_selected td {
//                          background-color: #4DD4FD;
//                      }    
//                      table.display tr.odd.row_selected td {
//                          background-color: #4DD4FD;
//                      }
//                      .SL{
//                          text-align: center !important;
//                      }
//                      .right-aln{
//                          text-align: right !important;
//                      }
//                      .left-aln{
//                          text-align: left !important;
//                      }
//                      .center-aln {
//                          text-align: center !important;
//                          margin-top:0px!important;
                         
//                          margin-bottom:1px!important;
//                      }

//              </style>  
//      </head>
     
//      <h2 class="center-aln" style="font-size: 15px; ">'.'Government_of_the_People_Republic_of_BD'.'</h2>
//      <h2 class="center-aln" style="font-size: 15px; ">'.'Directorate_General_of_Family_planning'.'</h3>
//      <h3 class="center-aln" style="font-size: 12px;">'.'UIMS'.', '.'FacilityName'.' '.'Address'.'</h3> 


//      <table style="padding-left:0px; margin-left:0px; font-size: 11px; solid #403c3c;" class="table display" width="100%" cellspacing="0">
//      <tbody >
//          <tr>
//              <td style="width:43%;"></td>
//              <td style="width:15%; font-size: 13px;" align="center" class="underlined"><span><b>'."Issue Voucher".'</b> </span>'.$IsbStockUpdated.'</td>
//              <td style="width:17%;"></td>
//              <td style="width:30%;" >
//              <b><span>'.'Receipient Copy'.'</span>  </b>
//              </td>    
//          </tr>

//      <tr >
//          <td style="width:75%;">
//               <b> <span>'.'Issuign Office'.'</b></span>&nbsp;: '.'FacilityName'.'
//          </td>
//          <td style="width:25%;" >
//           <span><b>'.'Invoice No'.'</b></span> &nbsp; &nbsp; '.'444444444'.' 
         
//          </td>  
//      </tr>

//      <tr>
//          <td style="width:75%;">
//          <b> <span>'.'Issue To'.'</b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.'issueTo'.'
//          </td>
//          <td style="width:25%;" >
//          <span><b>'.'Print Date'.'</b></span> &nbsp;:&nbsp; '.'date55555555555'.'
         
//          </td>  
//      </tr>
//      <tr>
//          <td style="width:75%;">
//                  &nbsp;
//          </td>
//          <td style="width:25%;" >
       
//          </td>  
//      </tr>
    

//      </tbody>
//  </table>


//      <table class="fontsizechange" cellpadding="2"  border="1"  cellspacing="0" width="100%" >
//      <tbody >
//              <thead>
//                  <tr class="ittaliy">
//                      <th rowspan="2" style="width:3% !important;" class="center-aln" >'.'Sl#'.'</th>
//                      <th rowspan="2" style="width:8% !important;">'. 'Code'.'</th>
//                      <th rowspan="2" style="width:34% !important;">'. 'Article'.'</th>
                     
//                      <th colspan="2" style="width:29% !important;padding: 0px;" class="center-aln">'. 'Quantity'.'</th>
//                      <th rowspan="2" style="width:5% !important;" class="center-aln">'. 'Unit'.'</th>
//                      <th rowspan="2" style="width:7% !important; text-align:center;" class="center-aln">'. 'No Of Cartons'.'</th>
//                      <th rowspan="2" style="width:8% !important;">'. 'Lot No'.'</th>
//                      <th rowspan="2" style="width:8% !important;" class="center-aln">'. 'Expiry Date'.'</th>
//                  </tr>
             
//                  <tr class="ittaliy" style="font-size: 12px;">
//                      <th style="width:9% !important;" class="right-aln">'. 'In Number'.'</th>
//                      <th style="width:20% !important;" class="left-aln">'. 'In Words'.'</th>
//                  </tr>
//              </thead>
//              '.$dataList.'
             
//      </tbody>
//      <tfoot>
//         <!-- <tr>
//              <td colspan="10" class="left-aln">'. 'Remarks'.' :'.'Remarks'.'</td>
             
//          </tr>-->
//      </tfoot>
//  </table>

// <div >
// <span style="font-size: 12px;" >'. 'Remarks'.' :'.'Remarks'.'</span>
// </div>



// </html>';

//output the HTML content
// $pdf->writeHTML($tblReceipient, true, false, true, false, '');



//$pdf->setMargins(5, 15, 8);

//$pdf->setMargins(2, 10, 8);



$pdf->SetY(252);

$tblp2 = '
<style>
.underlined2 { 
    border-bottom: 0.30em solid #000;
    
}
.underlined { 
    border-bottom: 0.30em solid #000;       
}
</style>
</br></br></br></br></br><div style="margin-top:30px;">


            <table style="padding-left:0px; margin-left:0px; font-size: 11px; solid #403c3c;" class="table display" width="100%" cellspacing="0">
                <tbody >
                    <tr>
                         <td style="width:5%;" > </td>

                        <td style="width:25%; font-size: 11px;" align="center">
                            <div class="underlined"><b>'.$sqlLoop2resultdata[0]['InvoiceNo'].'</b></div>
                           
                                Prepared & Issued By
                            <br>
                                 Store In Charge
                            <br>
                                '.$sqlLoop2resultdata[0]['InvoiceNo'].','.$sqlLoop2resultdata[0]['InvoiceNo'].'
                        </td>

                        <td style="width:5%;" > </td>

                        <td style="width:25%; font-size: 11px;" align="center" >
                            <div class="underlined"><b>'.$sqlLoop2resultdata[0]['InvoiceNo'].'</b> </div>
                                Approved By
                            <br>
                                Upazila Family Planning Officer
                            <br>
                                '.$sqlLoop2resultdata[0]['InvoiceNo'].','.$sqlLoop2resultdata[0]['InvoiceNo'].'
                           
                        </td>

                        <td style="width:5%;" > </td>

                        <td style="width:25%; font-size: 11px;" align="center" >
                            <div class="underlined"></div>
                            Received By
                            <br>
                             '.$sqlLoop2resultdata[0]['InvoiceNo'].'
                        </td>   
                        
                        <td style="width:5%;" > </td>
                    </tr>


                </tbody>
            </table>
</div>';

$pdf->writeHTML($tblp2, true, false, true, false, '');

//=================------End Receipient Copy-----============================

    // set color for text
    $pdf->setTextColor(0, 63, 127);

    //Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0, $firstline=false, $firstblock=false, $maxh=0)

    // write the text
    // $pdf->writeHTMLCell(5, $utf8text, '', 0, '', false, 0, false, false, 0);
    // $pdf->Write(5, $utf8text, '', 0, '', false, 0, false, false, 0);
    // $pdf->writeHTMLCell(0, 0, '', '', $utf8text, 0, 1, 0, true, '', true);


    // ---------------------------------------------------------

    //$pdf->lastPage();

    $exportTime = date("Y-m-d-His", time());
    $file = 'MachineryServiceReport-'.$exportTime. '.pdf';//Save file name
    //$pdf->Output(dirname(__FILE__).'/media/'.$file, 'F');
    $pdf->Output(dirname(__FILE__).'/../../media/files/'.$file, 'F');
   
    $pdfFileNameArray[] = "../../media/files/".$file;
    // $m++;
    //$pdf->pdfFileNameArray[] = $file;
    //$stringFilse .= "'../../media/files/".$pdfFileNameArray[$ij]."',";
    //$stringFilse .= '"../../media/files/'.$pdfFileNameArray[$ij].'",';
   



// require_once("merge-pdf-files-master/MergePdf.class.php");


// if ($m > 1){

//     MergePdf::merge(
//         $pdfFileNameArray,
//         MergePdf::DESTINATION__DISK_INLINE
//     );


//     /*  MergePdf::merge(
//         Array(
//             '../../media/files/'.$pdfFileNameArray[0],
//             '../../media/files/'.$pdfFileNameArray[1],
//             '../../media/files/'.$pdfFileNameArray[2],
//             '../../media/files/'.$pdfFileNameArray[3],
//             '../../media/files/'.$pdfFileNameArray[4],
//         ),
//         MergePdf::DESTINATION__DISK_INLINE
        
//     ); */
//     echo 1111;
//     echo $pdfFileNameArray[0];
//     exit;
// }else{

    /* MergePdf::merge(
        Array(
            'media/'.$pdfFileNameArray[0],
        ),
        MergePdf::DESTINATION__DISK_INLINE,
    ); */
    // echo 2222;
    // echo $pdfFileNameArray[0];
    // exit;
    //$pdf->Output('media/'.$pdfFileNameArray[0], 'I');
    $pdf->Output(dirname(__FILE__).'/../../media/files/'.$pdfFileNameArray[0], 'I');

// }


//============================================================+
// END OF FILE
//============================================================+
