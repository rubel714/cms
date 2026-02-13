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
$TotalTransactionAmount = 0;//USD
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

class MYPDF extends TCPDF
{
    public function Header()
    {
        global $MRNo;

        // Logo
        $image_file = '../../image/appmenu/Intertek_Logo.png';
        $this->Image($image_file, 5, 5, 30, 10, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 10);

        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(80, 5); // adjust X and Y as needed
        $this->Cell(0, 0, 'Money Receipt', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(80, 15); // adjust X and Y as needed
        $this->Cell(0, 0,  $MRNo, 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(80, 25); // adjust X and Y as needed
        $this->Cell(21, 6, 'Customer Copy', 1, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);


        // Move to the right side
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(160, 5); // adjust X and Y as needed
        $this->Cell(0, 0, 'ITS Labtest Bangladesh Ltd.', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        $this->SetFont('helvetica', 'B', 7);
        $this->SetXY(160, 10); // adjust X and Y as needed
        $this->Cell(0, 0, 'Phoenix Tower, 2nd & 3rd Floors', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        $this->SetXY(160, 15); // adjust X and Y as needed
        $this->Cell(0, 0, '407, Tejgoan Industrial Area', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        $this->SetXY(160, 20); // adjust X and Y as needed
        $this->Cell(0, 0, 'Dhaka-1208, Bangladesh,', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        $this->SetXY(160, 25); // adjust X and Y as needed
        $this->Cell(0, 0, 'Phone: +8809666776669', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        $this->SetXY(160, 30); // adjust X and Y as needed
        $this->Cell(0, 0, 'Fax: +88029125866', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);
    }

    // Page footer
    public function Footer()
    {
        // $this->SetFont('helvetica', 'R', 8);
        // // $this->SetFooterMargin(30); // or any value you need
        // $this->SetY(-30);
        // $Text = 'The results reflect our findings at time and place of inspection. This report does not relieve sellers/manufacturers from their contractual liabilities or prejudice buyers right for compensation for any apparent and/or hidden defects not detected during our random inspection or occurring thereafter This report does not evidence shipment.';
        // $this->MultiCell(0, 0, $Text, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetY(-19);
        // $Text = 'ITS Labtest Bangladesh Ltd., Haidar Tower, House # 668, Choydana, Ward # 34, Gazipur City Corporation, Gazipur-1704, Bangladesh';
        // $this->MultiCell(0, 0, $Text, 0, 'C', false, 1, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetY(-15);
        // $Text = 'Tel: +88 0966 677 6669, Web: www.intertek.com';
        // $this->MultiCell(0, 0, $Text, 0, 'C', false, 1, '', '', true, 0, false, true, 0, 'T', true);
    }
}



$pdf = new MyPDF();
$pdf->SetMargins(5, 25, 5);
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('helvetica', 'R', 9); //Global font size of this pdf
$pdf->AddPage();

$pdf->ln(10); // Line break 

// Add watermark
$pdf->SetAlpha(0.15); // Set transparency (15%)
$pdf->Image('../../image/appmenu/Intertek_Logo_Only.png', 60, 30, 70, 60, 'PNG', '', '', false, 300, '', false, false, 1, false, false, false);
$pdf->SetAlpha(1); // Reset to full opacity

// Column widths
$labelW = 30;
$valueW = 90;

// Row
$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Ref No', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $RefNo, 0, 0, 'L');

$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Date', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $PaymentDate, 0, 1, 'L');

// Row
$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Customer`s Code', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $CustomerCode, 0, 0, 'L');

$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Sum of taka', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . number_format($PaymentReceiveAmount, 2), 0, 1, 'L');

// Row
$pdf->Cell($labelW, 8, '', 0, 0, 'L');
$pdf->Cell($valueW, 8, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Sum of USD', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $TotalTransactionAmount, 0, 1, 'L');

// $pdf->ln(2); // Line break

$labelW = 42;
$valueW = 90;
// Row
$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Received with thanks from', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $CustomerName, 0, 1, 'L');

// Column widths
$labelW = 30;
$valueW = 90;
// Row
$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'In words', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $AmountInWords, 0, 1, 'L');


// Column widths
$labelW = 30;
$valueW = 90;
// Row
$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'By Cash/Cheque', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $ChequeNumber, 0, 0, 'L');

$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Cheque Date', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $ChequeDate, 0, 1, 'L');

// Row
$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Bank', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $BankName, 0, 0, 'L');

$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Branch', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, ': ' . $BankBranchName, 0, 1, 'L');

$pdf->ln(5); // Line break

// Column widths
$labelW = 80;
$valueW = 90;
// Row
$pdf->SetFont('helvetica', 'R', 9);
$pdf->Cell($labelW, 8, 'Subject to Realization', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, 'For ITS Labtest Bangladesh Ltd.', 0, 1, 'L');

$pdf->ln(15); // Line break

// Column widths
$labelW = 150;
$valueW = 90;
// Row
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($labelW, 8, 'Received By', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($valueW, 8, 'Authorized By', 0, 1, 'L');


$CheckListFileName = $MRNo . '_' . date("Y_m_d_H_i_s") . '_checklist.pdf';
$SecondFilePath = $OutputFileDirectory . $CheckListFileName;
$pdf->Output($SecondFilePath, 'F'); // save file to disk

// Build a web-accessible URL and redirect to show the PDF
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/'); // go up from /backend/report to site root (/cms)
$fileUrl = $basePath . '/media/files/' . $CheckListFileName;
header('Location: ' . $fileUrl);
exit;
