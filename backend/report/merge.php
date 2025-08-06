<?php
$reportName = time()."xxx.pdf";

// require_once('PDFMerger/PDFMerger.php');

    include './PDFMerger/PDFMerger.php';
    
   $pdf3 = new PDFMerger;
   $pdf3->addPDF('media/InspectionReport-2025-07-09-223332.pdf', 'all')
         ->addPDF('media/InspectionReport-2025-07-09-221716.pdf' , 'all')
         ->merge('file', 'media/' . $reportName);
   //  echo $reportName;

?>