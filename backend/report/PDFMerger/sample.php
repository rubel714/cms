<?php
include 'PDFMerger.php';

$pdf = new PDFMerger;

$pdf->addPDF('aaa.pdf')
	->addPDF('bbb.pdf')
	->addPDF('ccc.pdf')
	->merge('file', 'TEST2.pdf');
	
	//REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
	//You do not need to give a file path for browser, string, or download - just the name.
