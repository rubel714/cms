<?php

// CORS for your frontend origin
header('Access-Control-Allow-Origin: *'); // or * if acceptable
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Expose-Headers: Content-Disposition');
// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}


$PaymentId = isset($_REQUEST['PaymentId']) ? $_REQUEST['PaymentId'] : -1;
if ($PaymentId == -1) {
    echo "Parameter is invalid";
    exit;
}
include_once('../env.php');
require_once("../source/api/pdolibs/Db.class.php");
$db = new Db();
$date = date('d/m/Y');
$siteTitle = reportsitetitleeng;


//===========================================================================================================
//===============================Generate Check List Report==================================================
//===========================================================================================================
require_once('TCPDF-master/examples/tcpdf_include.php');

// Helper function to convert integer to words
function intToWords($number) {
    $ones = array(
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
    );
    
    $tens = array(
        0 => '', 2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    );
    
    if ($number == 0) return 'Zero';
    
    $number = intval($number);
    $words = '';
    
    if ($number >= 10000000) { // Crore
        $crore = intval($number / 10000000);
        $words .= intToWords($crore) . ' Crore ';
        $number %= 10000000;
    }
    
    if ($number >= 100000) { // Lakh
        $lakh = intval($number / 100000);
        $words .= intToWords($lakh) . ' Lakh ';
        $number %= 100000;
    }
    
    if ($number >= 1000) { // Thousand
        $thousand = intval($number / 1000);
        $words .= intToWords($thousand) . ' Thousand ';
        $number %= 1000;
    }
    
    if ($number >= 100) { // Hundred
        $hundred = intval($number / 100);
        $words .= $ones[$hundred] . ' Hundred ';
        $number %= 100;
    }
    
    if ($number >= 20) {
        $words .= $tens[intval($number / 10)] . ' ';
        $number %= 10;
    }
    
    if ($number > 0) {
        $words .= $ones[$number] . ' ';
    }
    
    return trim($words);
}

// Function to convert number to words with Taka and Paisa
function numberToWords($number) {
    $number = floatval($number);
    
    // Split into taka and paisa
    $taka = floor($number);
    $paisa = round(($number - $taka) * 100);
    
    $words = '';
    
    if ($taka > 0) {
        $words .= intToWords($taka) . ' Taka';
    } else {
        $words .= 'Zero Taka';
    }
    
    if ($paisa > 0) {
        $words .= ' and ' . intToWords($paisa) . ' Paisa';
    }
    
    $words .= ' Only';
    
    return $words;
}


$MRNo = "";
$RefNo = "";
$PaymentDate = "";
$CustomerCode = "";
$PaymentReceiveAmount = 0;//TK
$TotalBaseAmount = 0;//TK
$TotalTransactionAmount = '';//USD
$CustomerName = "";
$AmountInWords = "";
$ChequeNumber = "";
$ChequeDate = "";
$BankName = "";
$BankBranchName = "";

$sqlf = "SELECT a.PaymentId, DATE_FORMAT(a.PaymentDate, '%d-%b-%Y') AS PaymentDate
			,a.MRNo,a.RefNo,b.CustomerCode,b.CustomerName,a.TotalBaseAmount, a.PaymentReceiveAmount, a.ChequeNumber,
            case when a.ChequeDate IS NULL then '' else DATE_FORMAT(a.ChequeDate, '%d-%b-%Y') end as ChequeDate,
             c.BankName, a.BankBranchName
			FROM t_payment a
			left join t_customer b on a.CustomerId=b.CustomerId
			left join t_bank c on a.BankId=c.BankId
			where a.PaymentId = $PaymentId;";

$sqlLoop1result = $db->query($sqlf);
foreach ($sqlLoop1result as $result) {
    $MRNo = $result['MRNo'];
    $RefNo = $result['RefNo'];
    $PaymentDate = $result['PaymentDate'];
    $CustomerCode = $result['CustomerCode'];
    $CustomerName = $result['CustomerName'];
    $PaymentReceiveAmount = $result['PaymentReceiveAmount'];
    $TotalTransactionAmount = '';
    $AmountInWords = numberToWords($PaymentReceiveAmount);
    $ChequeNumber = $result['ChequeNumber'];
    $ChequeDate = $result['ChequeDate'];
    $BankName = $result['BankName'];
    $BankBranchName = $result['BankBranchName'];
}

$OutputFileDirectory = dirname(__FILE__) . '/../../media/files/';
if (!is_dir($OutputFileDirectory)) {
    mkdir($OutputFileDirectory, 0777, true);
}

function mr_line($pdf, $x, $y, $labelW, $valueW, $label, $value, $h)
{
    $pdf->SetXY($x, $y);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell($labelW, $h, $label, 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell($valueW, $h, ': ' . $value, 0, 0, 'L');
}

$logoFile = dirname(__FILE__) . '/../../image/appmenu/Intertek_Logo.png';
$markFile = dirname(__FILE__) . '/../../image/appmenu/Intertek_Logo_Only.png';

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetTitle('Money Receipt ' . $MRNo);
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);

// Same watermark as before, behind the fields.
if (is_file($markFile)) {
    $pdf->SetAlpha(0.15);
    $pdf->Image($markFile, 68, 34, 74, 62, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
    $pdf->SetAlpha(1);
}

if (is_file($logoFile)) {
    $pdf->Image($logoFile, 8, 5, 34, 0, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
}

$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetXY(55, 5);
$pdf->Cell(100, 5, 'Money Receipt', 0, 0, 'C');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(55, 11);
$pdf->Cell(100, 4, $MRNo, 0, 0, 'C');
$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(88, 16.5);
$pdf->Cell(34, 5.5, 'Customer Copy', 1, 0, 'C', false, '', 0, false, 'T', 'M');

$addressX = 142;
$addressW = 60;
$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY($addressX, 5);
$pdf->Cell($addressW, 3.5, 'ITS Labtest Bangladesh Ltd.', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 7);
$addressLines = array(
    'Phoenix Tower, 2nd & 3rd Floors',
    '407, Tejgoan Industrial Area',
    'Dhaka-1208, Bangladesh,',
    'Phone: +8809666776669',
    'Fax: +88029125866',
);
$addressY = 9;
foreach ($addressLines as $addressLine) {
    $pdf->SetXY($addressX, $addressY);
    $pdf->Cell($addressW, 3.3, $addressLine, 0, 0, 'L');
    $addressY += 3.3;
}

$left = 8;
$labelW = 42;
$valueW = 64;
$rightX = 116;
$labelW2 = 30;
$valueW2 = 56;
$rowH = 6;
$y = 30;

mr_line($pdf, $left, $y, $labelW, $valueW, 'Ref No', $RefNo, $rowH);
mr_line($pdf, $rightX, $y, $labelW2, $valueW2, 'Date', $PaymentDate, $rowH);

$y += $rowH;
mr_line($pdf, $left, $y, $labelW, $valueW, 'Customer`s Code', $CustomerCode, $rowH);
mr_line($pdf, $rightX, $y, $labelW2, $valueW2, 'Sum of taka', number_format((float) $PaymentReceiveAmount, 2), $rowH);

$y += $rowH;
mr_line($pdf, $rightX, $y, $labelW2, $valueW2, 'Sum of USD', $TotalTransactionAmount, $rowH);

$y += $rowH + 1.5;
mr_line($pdf, $left, $y, $labelW, 140, 'Received with thanks from', $CustomerName, $rowH);

$y += $rowH;
mr_line($pdf, $left, $y, $labelW, 140, 'In words', strtoupper($AmountInWords), $rowH);

$y += $rowH;
mr_line($pdf, $left, $y, $labelW, $valueW, 'By Cash/Cheque', $ChequeNumber, $rowH);
mr_line($pdf, $rightX, $y, $labelW2, $valueW2, 'Cheque Date', $ChequeDate, $rowH);

$y += $rowH;
mr_line($pdf, $left, $y, $labelW, $valueW, 'Bank', $BankName, $rowH);
mr_line($pdf, $rightX, $y, $labelW2, $valueW2, 'Branch', $BankBranchName, $rowH);

$y += $rowH + 3;
$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY($left, $y);
$pdf->Cell(70, $rowH, 'Subject to Realization', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(78, $y);
$pdf->Cell(90, $rowH, 'For ITS Labtest Bangladesh Ltd.', 0, 0, 'C');

$y += 22;
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY($left, $y);
$pdf->Cell(50, $rowH, 'Received By', 0, 0, 'L');
$pdf->SetXY(150, $y);
$pdf->Cell(52, $rowH, 'Authorized By', 0, 0, 'L');


$CheckListFileName = $MRNo . '_' . date("Y_m_d_H_i_s") . '_checklist.pdf';
$SecondFilePath = $OutputFileDirectory . $CheckListFileName;
$pdf->Output($SecondFilePath, 'F'); // save file to disk

// Build a web-accessible URL and redirect to show the PDF
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/'); // go up from /backend/report to site root (/cms)
$fileUrl = $basePath . '/media/files/' . $CheckListFileName;
header('Location: ' . $fileUrl);
exit;
