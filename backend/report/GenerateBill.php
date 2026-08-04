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
$LoginUserId = isset($_REQUEST['LoginUserId']) ? $_REQUEST['LoginUserId'] : 0;
if ($BillId == -1) {
    echo "Parameter is invalid";
    exit;
}

// Print options default to on so existing links keep the full layout
$IsPrintAmountBDT = isset($_REQUEST['IsPrintAmountBDT']) ? (int) $_REQUEST['IsPrintAmountBDT'] : 1;
$IsPrintStyle = isset($_REQUEST['IsPrintStyle']) ? (int) $_REQUEST['IsPrintStyle'] : 1;
$IsPrintMerchandiser = isset($_REQUEST['IsPrintMerchandiser']) ? (int) $_REQUEST['IsPrintMerchandiser'] : 1;
$IsPrintOrderNo = isset($_REQUEST['IsPrintOrderNo']) ? (int) $_REQUEST['IsPrintOrderNo'] : 1;
$IsPrintServiceType = isset($_REQUEST['IsPrintServiceType']) ? (int) $_REQUEST['IsPrintServiceType'] : 1;


include_once('../env.php');
require_once("../source/api/pdolibs/Db.class.php");
$db = new Db();
$date = date('d/m/Y');
$siteTitle = reportsitetitleeng;


//===========================================================================================================
//===============================Generate Check List Report==================================================
//===========================================================================================================
require_once('TCPDF-master/examples/tcpdf_include.php');


$CustomerCode = "";
$CustomerName = "";
$BillNumber = "";
$Remarks = "";
$BillDate = "";
$withinPeriod = "15 days of invoice date";
$contactPerson='';
$contactPhone='';

$sqlu = "SELECT UserName, Email, PhoneNo FROM t_users where UserId=$LoginUserId;";
$sqluresult = $db->query($sqlu);
foreach ($sqluresult as $result) {
    $contactPerson = $result['UserName'];
    $contactPhone = $result['PhoneNo'];
}

$TotalBaseAmount=0;
$TotalTransactionAmount=0;
$RebatePercentage=0;
$RebateAmount=0;
$RebateAmountUSD=0;
$VATPercentage=0;
$VATAmount=0;
$VATAmountUSD=0;
$TaxPercentage=0;
$TaxAmount=0;
$TaxAmountUSD=0;
$Total=0;
$TotalUSD=0;

$sqlm = "SELECT b.CustomerCode, b.CustomerName, a.Remarks, a.BillNumber, a.BillDate,
ifnull(a.TotalBaseAmount,0) as TotalBaseAmount,ifnull(a.TotalTransactionAmount,0) as TotalTransactionAmount, ifnull(a.RebatePercentage,0) as RebatePercentage, 
ifnull(a.RebateAmount,0) as RebateAmount, ifnull(a.RebateAmountUSD,0) as RebateAmountUSD,
ifnull(a.VATPercentage,0) as VATPercentage, ifnull(a.VATAmount,0) as VATAmount, ifnull(a.VATAmountUSD,0) as VATAmountUSD,
ifnull(a.TaxPercentage,0) as TaxPercentage, ifnull(a.TaxAmount,0) as TaxAmount, ifnull(a.TaxAmountUSD,0) as TaxAmountUSD
FROM t_bill a 
inner join t_customer b on a.CustomerId=b.CustomerId
where a.BillId=$BillId;";

$sqlmresult = $db->query($sqlm);

foreach ($sqlmresult as $result) {
    $CustomerCode = $result['CustomerCode'];
    $CustomerName = $result['CustomerName'];
    $BillNumber = $result['BillNumber'];
    $Remarks = $result['Remarks'];
    $BillDate = date('d/m/Y', strtotime($result['BillDate']));

    $TotalBaseAmount = $result['TotalBaseAmount'];
    $TotalTransactionAmount = $result['TotalTransactionAmount'];
    $RebatePercentage = $result['RebatePercentage'];
    $RebateAmount = $result['RebateAmount'];
    $RebateAmountUSD = $result['RebateAmountUSD'];
    $VATPercentage = $result['VATPercentage'];
    $VATAmount = $result['VATAmount'];
    $VATAmountUSD = $result['VATAmountUSD'];
    $TaxPercentage = $result['TaxPercentage'];
    $TaxAmount = $result['TaxAmount'];
    $TaxAmountUSD = $result['TaxAmountUSD'];
    $Total = $TotalBaseAmount - $RebateAmount - $VATAmount - $TaxAmount;
    $TotalUSD = $TotalTransactionAmount - $RebateAmountUSD - $VATAmountUSD - $TaxAmountUSD;

}



$OutputFileDirectory = dirname(__FILE__) . '/../../media/files/';
if (!is_dir($OutputFileDirectory)) {
    mkdir($OutputFileDirectory, 0777, true);
}

class MYPDF extends TCPDF
{
    public function Header()
    {
        global $BillNumber,$CustomerCode, $CustomerName, $Remarks, $BillDate;

        // Logo (right side)
        $image_file = '../../image/appmenu/Intertek_Logo.png';
        $logoWidth = 30;
        $logoHeight = 10;
        $margins = $this->getMargins();
        $x = $this->getPageWidth() - $margins['right'] - $logoWidth;
        $this->Image($image_file, $x, 5, $logoWidth, $logoHeight, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'R', 12);
        $this->SetXY(10, 15); // adjust X and Y as needed
        $this->writeHTMLCell(0, 0, 10, 5, '<b>'.($Remarks?"Bill (".$Remarks.")":"Bill").'</b>', 0, 0, false, true, 'L', true);

         $this->SetFont('helvetica', 'R', 10);
        $this->SetXY(10, 17); // adjust X and Y as needed
        $headerText = 'Bill Reference No: <b>'.$BillNumber . '</b><br/>Client Code: <b>'.$CustomerCode . '</b><br/>Client Name: <b>' . htmlspecialchars($CustomerName, ENT_QUOTES, 'UTF-8') . '</b><br/>Bill Date: <b>' . htmlspecialchars($BillDate, ENT_QUOTES, 'UTF-8') . '</b>' ;
        $this->writeHTMLCell(0, 0, 10, 12, $headerText, 0, 0, false, true, 'L', true);
 
 
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
$pdf->SetMargins(5, 35, 5);
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('helvetica', 'R', 9); //Global font size of this pdf
$pdf->AddPage('L');


$margins = $pdf->getMargins();
$tableWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];




$sqlf = "SELECT DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
		b.GeneralDescription9,b.TransactionReference,b.GeneralDescription11,b.GeneralDescription17,
		null OrderNumber,b.TransactionAmount, b.ExchangeRate, b.BaseAmount,b.GeneralDescription14,b.GeneralDescription20
		FROM t_billitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.BillId=$BillId
		order by a.BillItemId ASC;";

$sqlLoop1result = $db->query($sqlf);


$columns = [
    ['key' => 'TransactionDate', 'label' => 'Invoice Date', 'width' => 6, 'align' => 'center', 'visible' => true],
    ['key' => 'TransactionReference', 'label' => 'Invoice Number', 'width' => 10, 'align' => 'left', 'visible' => true],
    ['key' => 'GeneralDescription9', 'label' => 'Report Number', 'width' => 8, 'align' => 'left', 'visible' => true],
    ['key' => 'GeneralDescription11', 'label' => 'Buyer Name', 'width' => 16, 'align' => 'left', 'visible' => true],
    ['key' => 'TransactionAmount', 'label' => 'Amount in FC', 'width' => 5, 'align' => 'right', 'visible' => true, 'number' => true],
    ['key' => 'ExchangeRate', 'label' => 'Ex. Rate', 'width' => 4, 'align' => 'right', 'visible' => ($IsPrintAmountBDT == 1), 'number' => true],
    ['key' => 'BaseAmount', 'label' => 'Amount in BDT', 'width' => 7, 'align' => 'right', 'visible' => ($IsPrintAmountBDT == 1), 'number' => true],
    ['key' => 'GeneralDescription17', 'label' => 'Style number', 'width' => 13, 'align' => 'left', 'visible' => ($IsPrintStyle == 1)],
    ['key' => 'OrderNumber', 'label' => 'Order Number', 'width' => 10, 'align' => 'left', 'visible' => ($IsPrintOrderNo == 1)],
    ['key' => 'GeneralDescription14', 'label' => 'Merchandiser Name', 'width' => 13, 'align' => 'left', 'visible' => ($IsPrintMerchandiser == 1)],
    ['key' => 'GeneralDescription20', 'label' => 'Service Type', 'width' => 6, 'align' => 'left', 'visible' => ($IsPrintServiceType == 1)],
];

$columns = array_values(array_filter($columns, function ($col) {
    return $col['visible'];
}));

// Rescale the remaining widths so the table still spans the full page
$declaredWidth = array_sum(array_column($columns, 'width'));
foreach ($columns as $i => $col) {
    $columns[$i]['pct'] = round(($col['width'] * 100) / $declaredWidth, 2);
}

/** Renders a row where $values maps a column key to already-formatted content. */
function buildRow($columns, $values, $bold = false)
{
    $row = '<tr>';
    foreach ($columns as $col) {
        $value = isset($values[$col['key']]) ? $values[$col['key']] : '';
        $style = ($bold && $value !== '') ? ' style="font-weight:bold"' : '';
        $row .= '<td width="' . $col['pct'] . '%" align="' . $col['align'] . '"' . $style . '>' . $value . '</td>';
    }
    return $row . '</tr>';
}

$html = '<table border="1" cellpadding="3" cellspacing="0" width="100%">';
$html .= '<thead><tr style="font-weight:bold; background-color:#FFC900;">';
foreach ($columns as $col) {
    $html .= '<th width="' . $col['pct'] . '%" align="' . $col['align'] . '">' . $col['label'] . '</th>';
}
$html .= '</tr></thead><tbody>';

foreach ($sqlLoop1result as $result) {
    $values = [];
    foreach ($columns as $col) {
        $raw = isset($result[$col['key']]) ? $result[$col['key']] : '';
        $values[$col['key']] = !empty($col['number'])
            ? number_format($raw, 2)
            : htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    }
    $html .= buildRow($columns, $values);
}

if (count($sqlLoop1result) > 0) {
    // Without the BDT column the charge figures have nowhere to go, so they move to the FC column
    $summaryRow = function ($label, $bdtValue, $fcValue) use ($columns, $IsPrintAmountBDT) {
        $values = ['GeneralDescription11' => $label];
        if ($IsPrintAmountBDT == 1) {
            $values['BaseAmount'] = number_format($bdtValue, 2);
        } else {
            $values['TransactionAmount'] = number_format($fcValue, 2);
        }
        return $values;
    };

    $html .= buildRow($columns, [
        'GeneralDescription11' => 'Sub Total',
        'TransactionAmount' => number_format($TotalTransactionAmount, 2),
        'BaseAmount' => number_format($TotalBaseAmount, 2),
    ], true);

    if ($RebatePercentage > 0) {
        $html .= buildRow($columns, $summaryRow('Rebate(' . $RebatePercentage . '%)', $RebateAmount, $RebateAmountUSD));
    }

    if ($VATPercentage > 0) {
        $html .= buildRow($columns, $summaryRow('VAT(' . $VATPercentage . '%)', $VATAmount, $VATAmountUSD));
    }

    if ($TaxPercentage > 0) {
        $html .= buildRow($columns, $summaryRow('Tax(' . $TaxPercentage . '%)', $TaxAmount, $TaxAmountUSD));
    }

    $html .= buildRow($columns, $summaryRow('Total', $Total, $TotalUSD), true);
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


$CheckListFileName = 'bill_'.str_replace('/', '_', $BillNumber) . '_date_' . date("Y_m_d_H_i_s") . '.pdf';
$SecondFilePath = $OutputFileDirectory . $CheckListFileName;
$pdf->Output($SecondFilePath, 'F'); // save file to disk

// Build a web-accessible URL and redirect to show the PDF
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/'); // go up from /backend/report to site root (/cms)
$fileUrl = $basePath . '/media/files/' . $CheckListFileName;
header('Location: ' . $fileUrl);
exit;
