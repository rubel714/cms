<?php
require_once('vendor/autoload.php');
use setasign\Fpdi\Tcpdf\Fpdi;

// Extend FPDI (which extends TCPDF)
class PDFMerger extends Fpdi {}
$pdf = new PDFMerger();

$files = ['file1.pdf', 'file2.pdf'];

foreach ($files as $file) {
    $pageCount = $pdf->setSourceFile($file);

    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($templateId);
        
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);
    }
}

// $pdf->Output('merged_result.pdf', 'I');
$CombineFileName = dirname(__FILE__) . '/merged_result_'.time().'.pdf';
$pdf->Output($CombineFileName, 'F');
// echo $CombineFileName;
?>