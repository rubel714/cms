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


$BillId = isset($_REQUEST['BillId']) ? $_REQUEST['BillId'] : -1;
if ($BillId == -1) {
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


$CustomerName = "";
$BillNumber = "11111";

// $PaymentDate = "";
// $CustomerCode = "";
// $TotalPaymentAmount = 0;
// $TotalPaymentAmountUSD = 0;
// $CustomerName = "";
// $AmountInWords = "";
// $ChequeNumber = "";
// $ChequeDate = "";
// $BankName = "";
// $BankBranchName = "";

$OutputFileDirectory = dirname(__FILE__) . '/../../media/files/';
if (!is_dir($OutputFileDirectory)) {
    mkdir($OutputFileDirectory, 0777, true);
}

class MYPDF extends TCPDF
{
    public function Header()
    {
        global $BillNumber;

        // Logo (right side)
        $image_file = '../../image/appmenu/Intertek_Logo.png';
        $logoWidth = 30;
        $logoHeight = 10;
        $margins = $this->getMargins();
        $x = $this->getPageWidth() - $margins['right'] - $logoWidth;
        $this->Image($image_file, $x, 5, $logoWidth, $logoHeight, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 10);

        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(80, 5); // adjust X and Y as needed
        $this->Cell(0, 0, 'Money Receipt', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetFont('helvetica', 'B', 8);
        // $this->SetXY(80, 15); // adjust X and Y as needed
        // $this->Cell(0, 0, 'MR - ' . $BillNumber, 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetFont('helvetica', 'B', 8);
        // $this->SetXY(80, 25); // adjust X and Y as needed
        // $this->Cell(21, 6, 'Customer Copy', 1, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);


        // // Move to the right side
        // $this->SetFont('helvetica', 'B', 8);
        // $this->SetXY(160, 5); // adjust X and Y as needed
        // $this->Cell(0, 0, 'ITS Labtest Bangladesh Ltd.', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetFont('helvetica', 'B', 7);
        // $this->SetXY(160, 10); // adjust X and Y as needed
        // $this->Cell(0, 0, 'Phoenix Tower, 2nd & 3rd Floors', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetXY(160, 15); // adjust X and Y as needed
        // $this->Cell(0, 0, '407, Tejgoan Industrial Area', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetXY(160, 20); // adjust X and Y as needed
        // $this->Cell(0, 0, 'Dhaka-1208, Bangladesh,', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetXY(160, 25); // adjust X and Y as needed
        // $this->Cell(0, 0, 'Phone: +8809666776669', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);

        // $this->SetXY(160, 30); // adjust X and Y as needed
        // $this->Cell(0, 0, 'Fax: +88029125866', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', true);
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
$pdf->AddPage('L');


$margins = $pdf->getMargins();
$tableWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
// Column widths in percent (must total 100)
$colPercents = [12, 12, 12, 12, 10, 10, 10, 8, 8, 6];
$colWidths = [];
foreach ($colPercents as $p) {
    $colWidths[] = $tableWidth * ($p / 100);
}


$sqlf = "SELECT DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
		b.GeneralDescription9,b.TransactionReference,b.GeneralDescription11,b.GeneralDescription17,
		null OrderNumber,b.TransactionAmount, b.ExchangeRate, b.BaseAmount,b.GeneralDescription14
		FROM t_billitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.BillId=$BillId
		order by a.BillItemId ASC;";

$sqlLoop1result = $db->query($sqlf);


$html = '<table border="1" cellpadding="3" cellspacing="0" width="100%">';
$html .= '<thead><tr style="font-weight:bold;">';
$html .= '<th width="10%">Report Due Date</th>';
$html .= '<th width="10%">Report Number</th>';
$html .= '<th width="10%">Invoice Number</th>';
$html .= '<th width="10%">Buyer Name</th>';
$html .= '<th width="10%">Style number</th>';
$html .= '<th width="10%">Order Number</th>';
$html .= '<th width="10%" align="right">Amount USD</th>';
$html .= '<th width="10%" align="right">Exchange Rate</th>';
$html .= '<th width="10%" align="right">Amount BDT</th>';
$html .= '<th width="10%">Responsible Person</th>';
$html .= '</tr></thead><tbody>';

foreach ($sqlLoop1result as $result) {
    $html .= '<tr>';
    $html .= '<td width="10%" align="center">' . htmlspecialchars($result['TransactionDate'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['GeneralDescription9'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['TransactionReference'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['GeneralDescription11'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['GeneralDescription17'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['OrderNumber'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%" align="right">' . number_format($result['TransactionAmount'], 2) . '</td>';
    $html .= '<td width="10%" align="right">' . number_format($result['ExchangeRate'], 2) . '</td>';
    $html .= '<td width="10%" align="right">' . number_format($result['BaseAmount'], 2) . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['GeneralDescription14'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

$pdf->SetFont('helvetica', 'R', 8);
$pdf->writeHTML($html, true, false, true, false, '');


// $pdf->ln(10); // Line break 

// // Add watermark
// $pdf->SetAlpha(0.15); // Set transparency (15%)
// $pdf->Image('../../image/appmenu/Intertek_Logo_Only.png', 60, 30, 70, 60, 'PNG', '', '', false, 300, '', false, false, 1, false, false, false);
// $pdf->SetAlpha(1); // Reset to full opacity

// // Column widths
// $labelW = 30;
// $valueW = 90;

// // Row
// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Ref No', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $RefNo, 0, 0, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Date', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $PaymentDate, 0, 1, 'L');

// // Row
// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Customer`s Code', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $CustomerCode, 0, 0, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Sum of taka', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . number_format($TotalPaymentAmount, 2), 0, 1, 'L');

// // Row
// $pdf->Cell($labelW, 8, '', 0, 0, 'L');
// $pdf->Cell($valueW, 8, '', 0, 0, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Sum of USD', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $TotalPaymentAmountUSD, 0, 1, 'L');

// // $pdf->ln(2); // Line break

// $labelW = 42;
// $valueW = 90;
// // Row
// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Received with thanks from', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $CustomerName, 0, 1, 'L');

// // Column widths
// $labelW = 30;
// $valueW = 90;
// // Row
// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'In words', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $AmountInWords, 0, 1, 'L');


// // Column widths
// $labelW = 30;
// $valueW = 90;
// // Row
// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'By Cash/Cheque', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $ChequeNumber, 0, 0, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Cheque Date', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $ChequeDate, 0, 1, 'L');

// // Row
// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Bank', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $BankName, 0, 0, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Branch', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, ': ' . $BankBranchName, 0, 1, 'L');

// $pdf->ln(5); // Line break

// // Column widths
// $labelW = 80;
// $valueW = 90;
// // Row
// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Subject to Realization', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, 'For ITS Labtest Bangladesh Ltd.', 0, 1, 'L');

// $pdf->ln(15); // Line break

// // Column widths
// $labelW = 150;
// $valueW = 90;
// // Row
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($labelW, 8, 'Received By', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, 'Authorized By', 0, 1, 'L');


$CheckListFileName = $BillNumber . '_' . date("Y_m_d_H_i_s") . '_bill.pdf';
$SecondFilePath = $OutputFileDirectory . $CheckListFileName;
$pdf->Output($SecondFilePath, 'F'); // save file to disk

// Build a web-accessible URL and redirect to show the PDF
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/'); // go up from /backend/report to site root (/cms)
$fileUrl = $basePath . '/media/files/' . $CheckListFileName;
header('Location: ' . $fileUrl);
exit;
