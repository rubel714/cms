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
$BillDate = "";
$withinPeriod = "15 days of invoice date";
$contactPerson='Sheikh Zahid Hussain';
$contactPhone='01711422132';



$sqlm = "SELECT b.CustomerName,a.Remarks,a.BillNumber,a.BillDate
		FROM t_bill a 
		inner join t_customer b on a.CustomerId=b.CustomerId
		where a.BillId=$BillId;";

$sqlmresult = $db->query($sqlm);

foreach ($sqlmresult as $result) {
    $CustomerName = $result['CustomerName'];
    $BillNumber = $result['BillNumber'];
    $Remarks = $result['Remarks'];
    $BillDate = date('d/m/Y', strtotime($result['BillDate']));
}



$OutputFileDirectory = dirname(__FILE__) . '/../../media/files/';
if (!is_dir($OutputFileDirectory)) {
    mkdir($OutputFileDirectory, 0777, true);
}

class MYPDF extends TCPDF
{
    public function Header()
    {
        global $BillNumber, $CustomerName, $Remarks, $BillDate;

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
        $headerText = htmlspecialchars($Remarks, ENT_QUOTES, 'UTF-8') . ' <b>' . htmlspecialchars($CustomerName, ENT_QUOTES, 'UTF-8') . '</b>, Bill Date: ' . htmlspecialchars($BillDate, ENT_QUOTES, 'UTF-8');
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
$pdf->ln(2);
$pdf->SetFont('helvetica', 'R', 8);

// Left column - Summary
$summaryHtml = '<table border="1" cellpadding="3" cellspacing="0" width="100%">';
$summaryHtml .= '<tr style="background-color:#FFC900; font-weight:bold;">';
$summaryHtml .= '<th>Cash / BEFTN / RTGS /Pay Order / Cheque Deposit</th>';
$summaryHtml .= '</tr>';
$summaryHtml .= '<tr>
                    <td width="50%"><b>Bank Name:</b> Standard Chartered Bank (SCB)</td>
                    <td  width="50%"><b>Bank Name:</b> The Hongkong and Shanghai Banking Corporation (HSBC)</td>
                </tr>';
$summaryHtml .= '<tr>
                    <td width="50%"><b>A/C Name:</b> ITS LABTEST BANGLADESH LTD</td>
                    <td width="50%"><b>A/C Name:</b> ITS LABTEST BANGLADESH LTD</td>
                </tr>';
$summaryHtml .= '<tr>
                    <td width="50%"><b>A/C Number:</b> 01-2334178-01</td>
                    <td  width="50%"><b>A/C Number:</b> 001-289438-011</td>
                </tr>';
$summaryHtml .= '<tr>
                    <td width="50%"><b>Branch:</b> Gulshan</td>
                    <td  width="50%"><b>Branch:</b> Gulshan</td>
                </tr>';
$summaryHtml .= '<tr>
                    <td style="background-color:#92d050; font-weight:bold;" width="100%">Online Payment Gateway: https://invoice.sslcommerz.com/invoice-form?&refer=5F868A8E0553C</td>
                </tr>';



$summaryHtml .= '</table>';

// Right column - Terms
$termsHtml = '<table border="1" cellpadding="3" cellspacing="0" width="100%">';
// $termsHtml .= '<tr>';
// $termsHtml .= '<th>Terms & Conditions</th>';
// $termsHtml .= '</tr>';
$termsHtml .= '<tr>
                <td>You are cordially requested to settle the payment within '.$withinPeriod.'<br/></td>
              </tr>';
$termsHtml .= '<tr>
                <td>For Any Kind of Query Please feel free to Communicate,<br/><br/>'.$contactPerson.'<br/>'.$contactPhone.'<br/><br/><br/>Credit Control & Invoicing</td>
              </tr>';
$termsHtml .= '</table>';

$twoColumnHtml = '<table cellpadding="0" cellspacing="0" width="100%">';
$twoColumnHtml .= '<tr>';
$twoColumnHtml .= '<td width="60%" valign="top">' . $summaryHtml . '</td>';
$twoColumnHtml .= '<td width="2%"></td>';
$twoColumnHtml .= '<td width="38%" valign="top">' . $termsHtml . '</td>';
$twoColumnHtml .= '</tr>';
$twoColumnHtml .= '</table>';

$pdf->writeHTML($twoColumnHtml, true, false, true, false, '');


$pdf->ln(1); // Line break
$pdf->MultiCell(0, 0,"Note: This vat exemption is applicable for 100% export-oriented Industry only under SRO No. 188-Ain/2019/45-Mushok dated 13.06.2019 by the powers exercised as per section 126(1) of VAT Act, 2012. Please inform us to revise the invoice with VAT, if you are not eligible for Vat exemption under SRO No. 188-Ain/2019/45-Mushok. Service receiver will be responsible for any kind of claim/penalty for not being eligible for vat exemption issue.", 0, 'C', false, 1, '', '', true, 0, false, true, 0, 'T', true);


$CheckListFileName = $BillNumber . '_' . date("Y_m_d_H_i_s") . '_bill.pdf';
$SecondFilePath = $OutputFileDirectory . $CheckListFileName;
$pdf->Output($SecondFilePath, 'F'); // save file to disk

// Build a web-accessible URL and redirect to show the PDF
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/'); // go up from /backend/report to site root (/cms)
$fileUrl = $basePath . '/media/files/' . $CheckListFileName;
header('Location: ' . $fileUrl);
exit;
