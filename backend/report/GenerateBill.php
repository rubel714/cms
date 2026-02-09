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
$BillNumber = "";
$Remarks = "";

$sqlm = "SELECT b.CustomerName,a.Remarks,a.BillNumber,a.BillDate
		FROM t_bill a 
		inner join t_customer b on a.CustomerId=b.CustomerId
		where a.BillId=$BillId;";

$sqlmresult = $db->query($sqlm);

foreach ($sqlmresult as $result) {
    $CustomerName = $result['CustomerName'];
    $BillNumber = $result['BillNumber'];
    $Remarks = $result['Remarks'];
}



$OutputFileDirectory = dirname(__FILE__) . '/../../media/files/';
if (!is_dir($OutputFileDirectory)) {
    mkdir($OutputFileDirectory, 0777, true);
}

class MYPDF extends TCPDF
{
    public function Header()
    {
        global $BillNumber, $CustomerName, $Remarks;

        // Logo (right side)
        $image_file = '../../image/appmenu/Intertek_Logo.png';
        $logoWidth = 30;
        $logoHeight = 10;
        $margins = $this->getMargins();
        $x = $this->getPageWidth() - $margins['right'] - $logoWidth;
        $this->Image($image_file, $x, 5, $logoWidth, $logoHeight, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);
        // Set font

        $this->SetFont('helvetica', 'R', 10);
        $this->SetXY(10, 15); // adjust X and Y as needed
        $headerText = htmlspecialchars($Remarks, ENT_QUOTES, 'UTF-8') . ' <b>' . htmlspecialchars($CustomerName, ENT_QUOTES, 'UTF-8') . '</b>';
        $this->writeHTMLCell(0, 0, 10, 15, $headerText, 0, 0, false, true, 'L', true);

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




$sqlf = "SELECT DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
		b.GeneralDescription9,b.TransactionReference,b.GeneralDescription11,b.GeneralDescription17,
		null OrderNumber,b.TransactionAmount, b.ExchangeRate, b.BaseAmount,b.GeneralDescription14
		FROM t_billitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.BillId=$BillId
		order by a.BillItemId ASC;";

$sqlLoop1result = $db->query($sqlf);


$html = '<table border="1" cellpadding="3" cellspacing="0" width="100%">';
$html .= '<thead><tr style="font-weight:bold; background-color:#FFC900;">';
$html .= '<th width="6%">Report Due Date</th>';
$html .= '<th width="8%">Report Number</th>';
$html .= '<th width="10%">Invoice Number</th>';
$html .= '<th width="15%">Buyer Name</th>';
$html .= '<th width="15%">Style number</th>';
$html .= '<th width="10%">Order Number</th>';
$html .= '<th width="7%" align="right">Amount USD</th>';
$html .= '<th width="6%" align="right">Exchange Rate</th>';
$html .= '<th width="7%" align="right">Amount BDT</th>';
$html .= '<th width="16%">Responsible Person</th>';
$html .= '</tr></thead><tbody>';
$TotalTransactionAmount = 0;
$TotalBaseAmount = 0;
foreach ($sqlLoop1result as $result) {
    $html .= '<tr>';
    $html .= '<td width="6%" align="center">' . htmlspecialchars($result['TransactionDate'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="8%">' . htmlspecialchars($result['GeneralDescription9'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['TransactionReference'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="15%">' . htmlspecialchars($result['GeneralDescription11'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="15%">' . htmlspecialchars($result['GeneralDescription17'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="10%">' . htmlspecialchars($result['OrderNumber'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td width="7%" align="right">' . number_format($result['TransactionAmount'], 2) . '</td>';
    $html .= '<td width="6%" align="right">' . number_format($result['ExchangeRate'], 2) . '</td>';
    $html .= '<td width="7%" align="right">' . number_format($result['BaseAmount'], 2) . '</td>';
    $html .= '<td width="16%">' . htmlspecialchars($result['GeneralDescription14'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '</tr>';

    if($result['TransactionAmount']>0){
        $TotalTransactionAmount += $result['TransactionAmount'];
    }

    if($result['BaseAmount']>0){
        $TotalBaseAmount += $result['BaseAmount'];
    }

}
if (count($sqlLoop1result) > 0) {
     $html .= '<tr>';
    $html .= '<td width="6%" align="center"></td>';
    $html .= '<td width="8%"></td>';
    $html .= '<td width="10%"></td>';
    $html .= '<td width="15%"></td>';
    $html .= '<td width="15%"></td>';
    $html .= '<td width="10%"></td>';
    $html .= '<td width="7%" align="right" style="font-weight:bold">'.number_format($TotalTransactionAmount, 2).'</td>';
    $html .= '<td width="6%" align="right"></td>';
    $html .= '<td width="7%" align="right" style="font-weight:bold">'.number_format($TotalBaseAmount, 2).'</td>';
    $html .= '<td width="16%"></td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

$pdf->SetFont('helvetica', 'R', 8);
$pdf->writeHTML($html, true, false, true, false, '');



// Create two-column layout with summary tables
$pdf->ln(5);
$pdf->SetFont('helvetica', 'B', 10);

$colWidth = ($tableWidth / 2) - 2;
$pdf->MultiCell(0, 0, '', 0, 'C', false, 1); // Line break

$pdf->SetXY($margins['left'], $pdf->GetY());
$pdf->SetFont('helvetica', 'R', 8);


$pdf->writeHTML($twoColumnHtml, true, false, true, false, '');
// Left column - Summary
$pdf->SetXY($margins['left'], $pdf->GetY());
$summaryHtml = '<table border="1" cellpadding="3" cellspacing="0" width="45%">';
$summaryHtml .= '<tr style="background-color:#FFC900; font-weight:bold;">';
$summaryHtml .= '<th>Summary</th>';
$summaryHtml .= '</tr>';
$summaryHtml .= '<tr><td>Total USD: ' . number_format($TotalTransactionAmount, 2) . '</td></tr>';
$summaryHtml .= '<tr><td>Total BDT: ' . number_format($TotalBaseAmount, 2) . '</td></tr>';
$summaryHtml .= '</table>';

// Right column - Terms
$termsHtml = '<table border="1" cellpadding="3" cellspacing="0" width="45%">';
$termsHtml .= '<tr style="background-color:#FFC900; font-weight:bold;">';
$termsHtml .= '<th>Terms & Conditions</th>';
$termsHtml .= '</tr>';
$termsHtml .= '<tr><td style="font-size:8px;">Payment is due within 30 days of invoice date. Late payments subject to 1.5% monthly interest.</td></tr>';
$termsHtml .= '</table>';

$pdf->SetFont('helvetica', 'R', 8);
$pdf->writeHTML($summaryHtml, true, false, true, false, '');
$pdf->writeHTML($termsHtml, true, false, true, false, '');

// $pdf->ln(10); // Line break

// // Add payment details section
// $pdf->SetFont('helvetica', 'R', 9);
// $labelW = 40;
// $valueW = 50;

// $pdf->Cell($labelW, 8, 'Bank Name:', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, 'Standard Chartered Bank (SCB)', 0, 1, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'A/C Name:', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, 'ITS LABTEST BANGLADESH LTD', 0, 1, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'A/C Number:', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, '01-2334178-01', 0, 1, 'L');

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Branch:', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, 'Gulshan', 0, 1, 'L');

// $pdf->ln(5);

// $pdf->SetFont('helvetica', 'R', 9);
// $pdf->Cell($labelW, 8, 'Payment Due:', 0, 0, 'L');
// $pdf->SetFont('helvetica', 'B', 9);
// $pdf->Cell($valueW, 8, 'September 15, 2025', 0, 1, 'L');
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
