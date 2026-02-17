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
if ($BillId == -1) {
    echo "Parameter is invalid";
    exit;
}

include_once('../env.php');
require_once("../source/api/pdolibs/Db.class.php");
require("PhpSpreadsheet/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
$contactPerson = 'Sheikh Zahid Hussain';
$contactPhone = '01711422132';

// Get Bill Header Data
$sqlm = "SELECT b.CustomerCode, b.CustomerName, a.Remarks, a.BillNumber, a.BillDate
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
}

// Get Bill Items Data
$sqlf = "SELECT DATE_FORMAT(STR_TO_DATE(b.TransactionDate, '%d%m%Y'), '%d/%m/%Y') as TransactionDate,
		b.GeneralDescription9,b.TransactionReference,b.GeneralDescription11,b.GeneralDescription17,
		null OrderNumber,b.TransactionAmount, b.ExchangeRate, b.BaseAmount,b.GeneralDescription14,b.GeneralDescription20
		FROM t_billitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.BillId=$BillId
		order by a.BillItemId ASC;";

$sqlLoop1result = $db->query($sqlf);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Bill');

// Set column widths
$sheet->getColumnDimension('A')->setWidth(12);  // Invoice Date
$sheet->getColumnDimension('B')->setWidth(18);  // Invoice Number
$sheet->getColumnDimension('C')->setWidth(15);  // Report Number
$sheet->getColumnDimension('D')->setWidth(25);  // Buyer Name
$sheet->getColumnDimension('E')->setWidth(15);  // Amount in FC
$sheet->getColumnDimension('F')->setWidth(10);  // Ex. Rate
$sheet->getColumnDimension('G')->setWidth(18);  // Amount in BDT
$sheet->getColumnDimension('H')->setWidth(20);  // Style Number
$sheet->getColumnDimension('I')->setWidth(15);  // Order Number
$sheet->getColumnDimension('J')->setWidth(20);  // Merchandiser Name
$sheet->getColumnDimension('K')->setWidth(12);  // Service Type

// Header Section
$sheet->setCellValue('A1', $Remarks ? "Bill (" . $Remarks . ")" : "Bill");
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->mergeCells('A1:K1');

$sheet->setCellValue('A2', 'Bill Reference No: ' . $BillNumber);
$sheet->getStyle('A2')->getFont()->setBold(true);
$sheet->mergeCells('A2:K2');

$sheet->setCellValue('A3', 'Client Code: ' . $CustomerCode);
$sheet->mergeCells('A3:K3');

$sheet->setCellValue('A4', 'Client Name: ' . $CustomerName);
$sheet->mergeCells('A4:K4');

$sheet->setCellValue('A5', 'Bill Date: ' . $BillDate);
$sheet->mergeCells('A5:K5');

// Table Header Row
$headerRow = 7;
$headers = [
    'A' => 'Invoice Date',
    'B' => 'Invoice Number',
    'C' => 'Report Number',
    'D' => 'Buyer Name',
    'E' => 'Amount in FC',
    'F' => 'Ex. Rate',
    'G' => 'Amount in BDT',
    'H' => 'Style Number',
    'I' => 'Order Number',
    'J' => 'Merchandiser Name',
    'K' => 'Service Type'
];

foreach ($headers as $col => $header) {
    $sheet->setCellValue($col . $headerRow, $header);
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
$sheet->getStyle('A' . $headerRow . ':K' . $headerRow)->applyFromArray($headerStyle);

// Data Rows
$dataRow = 8;
$TotalTransactionAmount = 0;
$TotalBaseAmount = 0;

foreach ($sqlLoop1result as $result) {
    $sheet->setCellValue('A' . $dataRow, $result['TransactionDate']);
    $sheet->setCellValue('B' . $dataRow, $result['TransactionReference']);
    $sheet->setCellValue('C' . $dataRow, $result['GeneralDescription9']);
    $sheet->setCellValue('D' . $dataRow, $result['GeneralDescription11']);
    $sheet->setCellValue('E' . $dataRow, $result['TransactionAmount']);
    $sheet->setCellValue('F' . $dataRow, $result['ExchangeRate']);
    $sheet->setCellValue('G' . $dataRow, $result['BaseAmount']);
    $sheet->setCellValue('H' . $dataRow, $result['GeneralDescription17']);
    $sheet->setCellValue('I' . $dataRow, $result['OrderNumber']);
    $sheet->setCellValue('J' . $dataRow, $result['GeneralDescription14']);
    $sheet->setCellValue('K' . $dataRow, $result['GeneralDescription20']);

    if ($result['TransactionAmount'] > 0) {
        $TotalTransactionAmount += $result['TransactionAmount'];
    }
    if ($result['BaseAmount'] > 0) {
        $TotalBaseAmount += $result['BaseAmount'];
    }

    $dataRow++;
}

// Total Row
if (count($sqlLoop1result) > 0) {
    $sheet->setCellValue('D' . $dataRow, 'Total:');
    $sheet->setCellValue('E' . $dataRow, $TotalTransactionAmount);
    $sheet->setCellValue('G' . $dataRow, $TotalBaseAmount);
    
    $sheet->getStyle('D' . $dataRow . ':K' . $dataRow)->getFont()->setBold(true);
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
$sheet->getStyle('A' . $headerRow . ':K' . $lastDataRow)->applyFromArray($dataStyle);

// Format number columns
$sheet->getStyle('E8:E' . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('F8:F' . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('G8:G' . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');

// Right align amount columns
$sheet->getStyle('E8:G' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Bank Details Section
$bankRow = $lastDataRow + 2;

$sheet->setCellValue('A' . $bankRow, 'Cash / BEFTN / RTGS / Pay Order / Cheque Deposit');
$sheet->getStyle('A' . $bankRow)->getFont()->setBold(true);
$sheet->getStyle('A' . $bankRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFC900');
$sheet->mergeCells('A' . $bankRow . ':F' . $bankRow);

$bankRow++;
$sheet->setCellValue('A' . $bankRow, 'Bank Name:');
$sheet->setCellValue('B' . $bankRow, 'Standard Chartered Bank (SCB)');
$sheet->setCellValue('D' . $bankRow, 'Bank Name:');
$sheet->setCellValue('E' . $bankRow, 'The Hongkong and Shanghai Banking Corporation (HSBC)');

$bankRow++;
$sheet->setCellValue('A' . $bankRow, 'A/C Name:');
$sheet->setCellValue('B' . $bankRow, 'ITS LABTEST BANGLADESH LTD');
$sheet->setCellValue('D' . $bankRow, 'A/C Name:');
$sheet->setCellValue('E' . $bankRow, 'ITS LABTEST BANGLADESH LTD');

$bankRow++;
$sheet->setCellValue('A' . $bankRow, 'A/C Number:');
$sheet->setCellValue('B' . $bankRow, '01-2334178-01');
$sheet->setCellValue('D' . $bankRow, 'A/C Number:');
$sheet->setCellValue('E' . $bankRow, '001-289438-011');

$bankRow++;
$sheet->setCellValue('A' . $bankRow, 'Branch:');
$sheet->setCellValue('B' . $bankRow, 'Gulshan');
$sheet->setCellValue('D' . $bankRow, 'Branch:');
$sheet->setCellValue('E' . $bankRow, 'Gulshan');

$bankRow++;
$sheet->setCellValue('A' . $bankRow, 'Online Payment Gateway: https://invoice.sslcommerz.com/invoice-form?&refer=5F868A8E0553C');
$sheet->getStyle('A' . $bankRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('92D050');
$sheet->mergeCells('A' . $bankRow . ':F' . $bankRow);

// Terms Section
$termsRow = $lastDataRow + 2;
$sheet->setCellValue('H' . $termsRow, 'You are cordially requested to settle the payment within ' . $withinPeriod);
$sheet->mergeCells('H' . $termsRow . ':K' . ($termsRow + 1));
$sheet->getStyle('H' . $termsRow)->getAlignment()->setWrapText(true);

$termsRow += 2;
$sheet->setCellValue('H' . $termsRow, 'For Any Kind of Query Please feel free to Communicate,');
$sheet->mergeCells('H' . $termsRow . ':K' . $termsRow);

$termsRow++;
$sheet->setCellValue('H' . $termsRow, $contactPerson);
$sheet->mergeCells('H' . $termsRow . ':K' . $termsRow);

$termsRow++;
$sheet->setCellValue('H' . $termsRow, $contactPhone);
$sheet->mergeCells('H' . $termsRow . ':K' . $termsRow);

$termsRow += 2;
$sheet->setCellValue('H' . $termsRow, 'Credit Control & Invoicing');
$sheet->mergeCells('H' . $termsRow . ':K' . $termsRow);

// Note Section
$noteRow = $bankRow + 2;
$noteText = "Note: This vat exemption is applicable for 100% export-oriented Industry only under SRO No. 188-Ain/2019/45-Mushok dated 13.06.2019 by the powers exercised as per section 126(1) of VAT Act, 2012. Please inform us to revise the invoice with VAT, if you are not eligible for Vat exemption under SRO No. 188-Ain/2019/45-Mushok. Service receiver will be responsible for any kind of claim/penalty for not being eligible for vat exemption issue.";
$sheet->setCellValue('A' . $noteRow, $noteText);
$sheet->mergeCells('A' . $noteRow . ':K' . ($noteRow + 2));
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
