<?php

// CORS for your frontend origin
header('Access-Control-Allow-Origin: *');
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
require("PhpSpreadsheet/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$db = new Db();
$date = date('d/m/Y');

$CustomerCode = "";
$CustomerName = "";
$BillNumber = "";
$Remarks = "";
$BillDate = "";
$withinPeriod = "15 days of invoice date";

$contactPerson = '';
$contactPhone = '';

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

// Get Bill Header Data
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

// Get Bill Items Data
$sqlf = "SELECT DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
		b.GeneralDescription9,b.TransactionReference,b.GeneralDescription11,b.GeneralDescription17,
		b.OrderNumber,b.TransactionAmount, b.ExchangeRate, b.BaseAmount,b.GeneralDescription14,b.GeneralDescription20
		FROM t_billitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.BillId=$BillId
		order by a.BillItemId ASC;";

$sqlLoop1result = $db->query($sqlf);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Bill');

$columns = [
    ['key' => 'TransactionDate', 'label' => 'Invoice Date', 'width' => 12, 'visible' => true],
    ['key' => 'TransactionReference', 'label' => 'Invoice Number', 'width' => 18, 'visible' => true],
    ['key' => 'GeneralDescription9', 'label' => 'Report Number', 'width' => 15, 'visible' => true],
    ['key' => 'GeneralDescription11', 'label' => 'Buyer Name', 'width' => 25, 'visible' => true],
    ['key' => 'TransactionAmount', 'label' => 'Amount in FC', 'width' => 15, 'visible' => true, 'number' => true],
    ['key' => 'ExchangeRate', 'label' => 'Ex. Rate', 'width' => 10, 'visible' => ($IsPrintAmountBDT == 1), 'number' => true],
    ['key' => 'BaseAmount', 'label' => 'Amount in BDT', 'width' => 18, 'visible' => ($IsPrintAmountBDT == 1), 'number' => true],
    ['key' => 'GeneralDescription17', 'label' => 'Style Number', 'width' => 20, 'visible' => ($IsPrintStyle == 1)],
    ['key' => 'OrderNumber', 'label' => 'Order Number', 'width' => 15, 'visible' => ($IsPrintOrderNo == 1)],
    ['key' => 'GeneralDescription14', 'label' => 'Merchandiser Name', 'width' => 20, 'visible' => ($IsPrintMerchandiser == 1)],
    ['key' => 'GeneralDescription20', 'label' => 'Service Type', 'width' => 12, 'visible' => ($IsPrintServiceType == 1)],
];

$columns = array_values(array_filter($columns, function ($col) {
    return $col['visible'];
}));

// Map each remaining column onto a spreadsheet letter, keyed by source field
$colLetter = [];
foreach ($columns as $i => $col) {
    $letter = Coordinate::stringFromColumnIndex($i + 1);
    $columns[$i]['letter'] = $letter;
    $colLetter[$col['key']] = $letter;
    $sheet->getColumnDimension($letter)->setWidth($col['width']);
}

$lastCol = $columns[count($columns) - 1]['letter'];
$colCount = count($columns);

// Header Section
$sheet->setCellValue('A1', $Remarks ? "Bill (" . $Remarks . ")" : "Bill");
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->mergeCells('A1:' . $lastCol . '1');

$sheet->setCellValue('A2', 'Bill Reference No: ' . $BillNumber);
$sheet->getStyle('A2')->getFont()->setBold(true);
$sheet->mergeCells('A2:' . $lastCol . '2');

$sheet->setCellValue('A3', 'Client Code: ' . $CustomerCode);
$sheet->mergeCells('A3:' . $lastCol . '3');

$sheet->setCellValue('A4', 'Client Name: ' . $CustomerName);
$sheet->mergeCells('A4:' . $lastCol . '4');

$sheet->setCellValue('A5', 'Bill Date: ' . $BillDate);
$sheet->mergeCells('A5:' . $lastCol . '5');

// Table Header Row
$headerRow = 7;
foreach ($columns as $col) {
    $sheet->setCellValue($col['letter'] . $headerRow, $col['label']);
}

// Style header row
$headerStyle = [
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFC900']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray($headerStyle);

// Data Rows
$dataRow = 8;

foreach ($sqlLoop1result as $result) {
    foreach ($columns as $col) {
        $sheet->setCellValue($col['letter'] . $dataRow, isset($result[$col['key']]) ? $result[$col['key']] : '');
    }
    $dataRow++;
}

// Summary Rows
$labelCol = $colLetter['GeneralDescription11'];
$fcCol = $colLetter['TransactionAmount'];
$showBDT = ($IsPrintAmountBDT == 1);
// Without the BDT column the charge figures have nowhere to go, so they move to the FC column
$amountCol = $showBDT ? $colLetter['BaseAmount'] : $fcCol;

if (count($sqlLoop1result) > 0) {
    // Sub Total Row
    $sheet->setCellValue($labelCol . $dataRow, 'Sub Total');
    $sheet->setCellValue($fcCol . $dataRow, $TotalTransactionAmount);
    $sheet->getStyle($labelCol . $dataRow)->getFont()->setBold(true);
    $sheet->getStyle($fcCol . $dataRow)->getFont()->setBold(true);
    if ($showBDT) {
        $sheet->setCellValue($amountCol . $dataRow, $TotalBaseAmount);
        $sheet->getStyle($amountCol . $dataRow)->getFont()->setBold(true);
    }
    $dataRow++;

    // Rebate Row
    if ($RebatePercentage > 0) {
        $sheet->setCellValue($labelCol . $dataRow, 'Rebate(' . $RebatePercentage . '%)');
        $sheet->setCellValue($amountCol . $dataRow, $showBDT ? $RebateAmount : $RebateAmountUSD);
        $dataRow++;
    }

    // VAT Row
    if ($VATPercentage > 0) {
        $sheet->setCellValue($labelCol . $dataRow, 'VAT(' . $VATPercentage . '%)');
        $sheet->setCellValue($amountCol . $dataRow, $showBDT ? $VATAmount : $VATAmountUSD);
        $dataRow++;
    }

    // Tax Row
    if ($TaxPercentage > 0) {
        $sheet->setCellValue($labelCol . $dataRow, 'Tax(' . $TaxPercentage . '%)');
        $sheet->setCellValue($amountCol . $dataRow, $showBDT ? $TaxAmount : $TaxAmountUSD);
        $dataRow++;
    }

    // Total Row
    $sheet->setCellValue($labelCol . $dataRow, 'Total');
    $sheet->setCellValue($amountCol . $dataRow, $showBDT ? $Total : $TotalUSD);
    $sheet->getStyle($labelCol . $dataRow)->getFont()->setBold(true);
    $sheet->getStyle($amountCol . $dataRow)->getFont()->setBold(true);
}

// Style data area with borders
$lastDataRow = $dataRow;
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A' . $headerRow . ':' . $lastCol . $lastDataRow)->applyFromArray($dataStyle);

// Format and right align the numeric columns
foreach ($columns as $col) {
    if (empty($col['number'])) {
        continue;
    }
    $range = $col['letter'] . '8:' . $col['letter'] . $lastDataRow;
    $sheet->getStyle($range)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
}

// Bank Details Section
$bankEndCol = Coordinate::stringFromColumnIndex(min(6, $colCount));
$bankCol1 = Coordinate::stringFromColumnIndex(1);
$bankCol2 = Coordinate::stringFromColumnIndex(2);
$bankCol3 = Coordinate::stringFromColumnIndex(4);
$bankCol4 = Coordinate::stringFromColumnIndex(5);

$bankRow = $lastDataRow + 2;

$sheet->setCellValue($bankCol1 . $bankRow, 'Cash / BEFTN / RTGS / Pay Order / Cheque Deposit');
$sheet->getStyle($bankCol1 . $bankRow)->getFont()->setBold(true);
$sheet->getStyle($bankCol1 . $bankRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFC900');
$sheet->mergeCells($bankCol1 . $bankRow . ':' . $bankEndCol . $bankRow);

$bankRow++;
$sheet->setCellValue($bankCol1 . $bankRow, 'Bank Name:');
$sheet->setCellValue($bankCol2 . $bankRow, 'Standard Chartered Bank (SCB)');
$sheet->setCellValue($bankCol3 . $bankRow, 'Bank Name:');
$sheet->setCellValue($bankCol4 . $bankRow, 'The Hongkong and Shanghai Banking Corporation (HSBC)');

$bankRow++;
$sheet->setCellValue($bankCol1 . $bankRow, 'A/C Name:');
$sheet->setCellValue($bankCol2 . $bankRow, 'ITS LABTEST BANGLADESH LTD');
$sheet->setCellValue($bankCol3 . $bankRow, 'A/C Name:');
$sheet->setCellValue($bankCol4 . $bankRow, 'ITS LABTEST BANGLADESH LTD');

$bankRow++;
$sheet->setCellValue($bankCol1 . $bankRow, 'A/C Number:');
$sheet->setCellValue($bankCol2 . $bankRow, '01-2334178-01');
$sheet->setCellValue($bankCol3 . $bankRow, 'A/C Number:');
$sheet->setCellValue($bankCol4 . $bankRow, '001-289438-011');

$bankRow++;
$sheet->setCellValue($bankCol1 . $bankRow, 'Branch:');
$sheet->setCellValue($bankCol2 . $bankRow, 'Gulshan');
$sheet->setCellValue($bankCol3 . $bankRow, 'Branch:');
$sheet->setCellValue($bankCol4 . $bankRow, 'Gulshan');

$bankRow++;
$sheet->setCellValue($bankCol1 . $bankRow, 'Online Payment Gateway: https://invoice.sslcommerz.com/invoice-form?&refer=5F868A8E0553C');
$sheet->getStyle($bankCol1 . $bankRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('92D050');
$sheet->mergeCells($bankCol1 . $bankRow . ':' . $bankEndCol . $bankRow);

// Terms Section: sits beside the bank block when enough columns remain, otherwise below it
$termsStartIndex = min(6, $colCount) + 2;
$termsFitsBeside = ($colCount - $termsStartIndex + 1) >= 3;
$termsCol = $termsFitsBeside ? Coordinate::stringFromColumnIndex($termsStartIndex) : 'A';
$termsRow = $termsFitsBeside ? $lastDataRow + 2 : $bankRow + 2;

$sheet->setCellValue($termsCol . $termsRow, 'You are cordially requested to settle the payment within ' . $withinPeriod);
$sheet->mergeCells($termsCol . $termsRow . ':' . $lastCol . ($termsRow + 1));
$sheet->getStyle($termsCol . $termsRow)->getAlignment()->setWrapText(true);

$termsRow += 2;
$sheet->setCellValue($termsCol . $termsRow, 'For Any Kind of Query Please feel free to Communicate,');
$sheet->mergeCells($termsCol . $termsRow . ':' . $lastCol . $termsRow);

$termsRow++;
$sheet->setCellValue($termsCol . $termsRow, $contactPerson);
$sheet->mergeCells($termsCol . $termsRow . ':' . $lastCol . $termsRow);

$termsRow++;
$sheet->setCellValue($termsCol . $termsRow, $contactPhone);
$sheet->mergeCells($termsCol . $termsRow . ':' . $lastCol . $termsRow);

$termsRow += 2;
$sheet->setCellValue($termsCol . $termsRow, 'Credit Control & Invoicing');
$sheet->mergeCells($termsCol . $termsRow . ':' . $lastCol . $termsRow);

// Note Section
$noteRow = max($bankRow, $termsRow) + 2;
$noteText = "Note: This vat exemption is applicable for 100% export-oriented Industry only under SRO No. 188-Ain/2019/45-Mushok dated 13.06.2019 by the powers exercised as per section 126(1) of VAT Act, 2012. Please inform us to revise the invoice with VAT, if you are not eligible for Vat exemption under SRO No. 188-Ain/2019/45-Mushok. Service receiver will be responsible for any kind of claim/penalty for not being eligible for vat exemption issue.";
$sheet->setCellValue('A' . $noteRow, $noteText);
$sheet->mergeCells('A' . $noteRow . ':' . $lastCol . ($noteRow + 2));
$sheet->getStyle('A' . $noteRow)->getAlignment()->setWrapText(true);
$sheet->getStyle('A' . $noteRow)->getFont()->setSize(8);

// Output file
$OutputFileDirectory = dirname(__FILE__) . '/../../media/files/';
if (!is_dir($OutputFileDirectory)) {
    mkdir($OutputFileDirectory, 0777, true);
}

$ExcelFileName = 'bill_' . str_replace('/', '_', $BillNumber) . '_date_' . date("Y_m_d_H_i_s") . '.xlsx';
$FilePath = $OutputFileDirectory . $ExcelFileName;

$writer = new Xlsx($spreadsheet);
$writer->save($FilePath);

// Build a web-accessible URL and redirect to download the Excel file
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/');
$fileUrl = $basePath . '/media/files/' . $ExcelFileName;
header('Location: ' . $fileUrl);
exit;
