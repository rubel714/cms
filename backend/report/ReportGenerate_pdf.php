<?php
// echo "<pre>";
// print_r($_REQUEST);

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$TransactionId = isset($_REQUEST['TransactionId'])?$_REQUEST['TransactionId']:-1;
if($TransactionId == -1){
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

$sqlf = "SELECT a.TransactionId, DATE_FORMAT(a.TransactionDate, '%d-%m-%Y') AS TransactionDate
			,a.InvoiceNo,a.CoverFilePages,a.CoverFileUrl,a.ManyImgPrefix,a.UserId,a.StatusId,b.UserName
			FROM t_transaction a
			inner join t_users b on a.UserId=b.UserId
			where a.TransactionId = $TransactionId;";
$sqlLoop1result = $db->query($sqlf);

$TransactionDate = "";
$InvoiceNo = "";
$CoverFilePages = "";
$CoverFileUrl = "";
$ManyImgPrefix = "";
$UserId = "";
$UserName = "";
foreach ($sqlLoop1result as $result) {
   $TransactionDate = $result['TransactionDate'];
   $InvoiceNo = $result['InvoiceNo'];
   $CoverFilePages = $result['CoverFilePages'];
   $CoverFileUrl = $result['CoverFileUrl'];
   $ManyImgPrefix = $result['ManyImgPrefix'];
   $UserId = $result['UserId'];
   $UserName = $result['UserName'];
}

$NoImageDirectory = dirname(__FILE__) . '/../../image/transaction/';
$FileDirectory = dirname(__FILE__) . '/../../image/transaction/'.$ManyImgPrefix.'/';

/*check the director is available. If not, then create*/
$path = "../../image/transaction/".$ManyImgPrefix;
if (!file_exists($path)) {
	mkdir($path, 0777, true);
}

class MYPDF extends TCPDF
{
    public function Header()
   {
      global $TransactionDate, $InvoiceNo, $CoverFilePages;

      // echo "TransactionDate=".$TransactionDate;
      // exit;

      // Logo
      $image_file = '../../image/appmenu/Intertek_Logo.png';
      $this->Image($image_file, 5, 5, 30, 10, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);
      // Set font
      $this->SetFont('helvetica', 'B', 10);

      // // Title
      // $this->Cell(7, 50, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
      // $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'L', 0, '', 0, false, 'M', 'M');
      $this->Write(36, 'INSPECTION REPORT', '', 0, 'L', true, 0, false, false, 0);
      // $this->Write(0, 'INSPECTION REPORT ssssssss', '', 0, 'L', true, 0, false, false, 0);
      // $this->Write(0, 'REPORT NUMBER: INS-421186 REPORT DATE: 08-01-2025 PAGE: 1  of  12', '', 0, 'R', true, 0, false, false, 0);
      // $this->Cell(50,0, 'REPORT NUMBER: INS-421186 REPORT DATE: 08-01-2025 PAGE: 1  of  12', 0, false, 'L', 0, '', 0, false, 'M', 'M');
      // Move to the right side
      $this->SetFont('helvetica', '', 7);
      $this->SetXY(150, 10); // adjust X and Y as needed
      //   $ReportNo = 'REPORT NUMBER: INS-421186';
      // $this->Cell(40, 15, 'REPORT NUMBER: INS-421186\nREPORT DATE: 08-01-2025\nPAGE: 1  of  12', 0, 0, 'R', 0, '', 0, false, 'M', 'M');
      // $this->MultiCell(0, 0, 'REPORT NUMBER: INS-421186\nREPORT DATE: 08-01-20251111111111 \nPAGE: 1  of  12', 0, 'L', 0, 3, '', '', true, 0, false, true, 0, 'M', false);

      //   $zzz = $this->getPageWidth();

      $this->Cell(0, 0, 'REPORT NUMBER: ' . $InvoiceNo, 0, 'L', false, 2, '', '', true, 0, false, true, 0, 'T', true);

      $this->SetXY(150, 14); // adjust X and Y as needed
      // $ReportDate = 'REPORT DATE: 08-01-2025';
      $this->Cell(0, 0, 'REPORT DATE: ' . $TransactionDate, 0, 'L', false, 2, '', '', true, 0, false, true, 0, 'T', true);

      $this->SetXY(150, 18); // adjust X and Y as needed
      // $PageNumber = 'PAGE: 1  of  12';
      $currentPage=$this->getPage();
      // $totalPage=$this->getNumPages();
      $PageNumber = 'PAGE: ' . ($currentPage+$CoverFilePages);
      // $PageNumber = 'PAGE: ' . ($currentPage+$CoverFilePages) . '  of ' . ($totalPage);
      // $PageNumber = 'PAGE: ' . ($this->getAliasNumPage()+$CoverFilePages) . '  of ' . ($this->getAliasNbPages()+$CoverFilePages);
      $this->Cell(0, 0, $PageNumber, 0, 'L', false, 2, '', '', true, 0, false, true, 0, 'T', true);
   }

   // Page footer
   public function Footer()
   {
	    $this->SetFont('helvetica', 'R', 8);
      // $this->SetFooterMargin(30); // or any value you need
      $this->SetY(-30);
      $Text = 'The results reflect our findings at time and place of inspection. This report does not relieve sellers/manufacturers from their contractual liabilities or prejudice buyers right for compensation for any apparent and/or hidden defects not detected during our random inspection or occurring thereafter This report does not evidence shipment.';
      $this->MultiCell(0, 0, $Text, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'T', true);

      $this->SetY(-19);
      $Text = 'ITS Labtest Bangladesh Ltd., Haidar Tower, House # 668, Choydana, Ward # 34, Gazipur City Corporation, Gazipur-1704, Bangladesh';
      $this->MultiCell(0, 0, $Text, 0, 'C', false, 1, '', '', true, 0, false, true, 0, 'T', true);

      $this->SetY(-15);
      $Text = 'Tel: +88 0966 677 6669, Web: www.intertek.com';
      $this->MultiCell(0, 0, $Text, 0, 'C', false, 1, '', '', true, 0, false, true, 0, 'T', true);
   }
}


	
$pdf = new MyPDF();
$pdf->SetMargins(5, 25, 5);
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('helvetica', 'R', 10);//Global font size of this pdf
$pdf->AddPage();

$sqlmany = "SELECT a.TransactionItemId,a.CheckId,b.CheckName,a.RowNo,a.ColumnNo,a.PhotoUrl,a.SortOrder
			FROM t_transaction_items a
			left join t_checklist b on a.CheckId=b.CheckId
			where a.TransactionId = $TransactionId
			order by a.SortOrder;";
$manyresult = $db->query($sqlmany);
// 'half-one-third'
// 'half-half'
// 'full-full'

// reportcheckblock-width-half
// reportcheckblock-width-full

// reportcheckblock-height-onethird
// reportcheckblock-height-half
// reportcheckblock-height-full

$images = [];
foreach ($manyresult as $result) {
   $PhotoUrl = $result['PhotoUrl'];
   $CheckName = $result['CheckName'];
   $RowNo = $result['RowNo'];
   $ColumnNo = $result['ColumnNo'];
   
   $type = "";
   if($RowNo == 'reportcheckblock-width-half' && $ColumnNo == 'reportcheckblock-height-onethird'){
	   $type = "half-one-third";// width-height
   }
   else if($RowNo == 'reportcheckblock-width-half' && $ColumnNo == 'reportcheckblock-height-half'){
	   $type = "half-half";// width-height
   }
   else if($RowNo == 'reportcheckblock-width-half' && $ColumnNo == 'reportcheckblock-height-full'){
	   $type = "half-full";// width-height
   }
   else if($RowNo == 'reportcheckblock-width-full' && $ColumnNo == 'reportcheckblock-height-onethird'){
	   $type = "full-one-third";// width-height
   } 
   else if($RowNo == 'reportcheckblock-width-full' && $ColumnNo == 'reportcheckblock-height-half'){
	   $type = "full-half";// width-height
   }
   else if($RowNo == 'reportcheckblock-width-full' && $ColumnNo == 'reportcheckblock-height-full'){
	   $type = "full-full";// width-height
   }
   
   
	// 'file' => '../../image/transaction/'.$PhotoUrl, 
	$ImageURL="";
	 if($PhotoUrl=='placeholder.jpg'){
		 $ImageURL = $NoImageDirectory.$PhotoUrl;
	 }else{
		 $ImageURL = $FileDirectory.$PhotoUrl;
	 }
	 
	 
   $images[] = [
				'file' => $ImageURL, 
				'type' => $type, 
				'label' => $CheckName
			];
   
   
}

// echo "<pre>";
// print_r($images);
// exit;

// $images = [

    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__5174.jpeg', 'type' => 'half-one-third', 'label' => 'Image 2: Half-One-Third'],
    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__8601.jpeg', 'type' => 'half-one-third', 'label' => 'Image 5: Half-One-Third'],
    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__8601.jpeg', 'type' => 'half-one-third', 'label' => 'Image 5: Half-One-Third'],
    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__8601.jpeg', 'type' => 'half-one-third', 'label' => 'Image 5: Half-One-Third'],
    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__8601.jpeg', 'type' => 'half-one-third', 'label' => 'Image 5: Half-One-Third'],
    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__8601.jpeg', 'type' => 'half-one-third', 'label' => 'Image 5: Half-One-Third'],

    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],

    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'full-full', 'label' => 'Image 3: Full-Full'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'full-full', 'label' => 'Image 3: Full-Full'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'half-half', 'label' => 'Image 1: Half-Half'],
    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__5174.jpeg', 'type' => 'half-one-third', 'label' => 'Image 2: Half-One-Third'],
    // ['file' => '../../image/transaction/1751994414177_2025_07_08_23_08_24__8601.jpeg', 'type' => 'half-one-third', 'label' => 'Image 5: Half-One-Third'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'full-full', 'label' => 'Image 3: Full-Full'],
	// ['file' => '../../image/transaction/2025_07_05_00_21_48_3018.png', 'type' => 'half-half', 'label' => 'Image 4: Half-Half'],
	// ['file' => '../../image/transaction/2025_07_05_00_21_48_3018.png', 'type' => 'half-half', 'label' => 'Image 4: Half-Half'],
	// ['file' => '../../image/transaction/2025_07_05_00_21_48_3018.png', 'type' => 'half-half', 'label' => 'Image 4: Half-Half'],
	// ['file' => '../../image/transaction/2025_07_05_00_21_48_3018.png', 'type' => 'half-half', 'label' => 'Image 4: Half-Half'],
    // ['file' => '../../image/transaction/Weightcheck.jpg', 'type' => 'full-full', 'label' => 'Image 3: Full-Full'],
	
// ];



$margin = 5;
$label_height = 6;

$page_width = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right']; // 200
$page_height = $pdf->getPageHeight() - 5; // space for header/footer // 292
// echo $page_width;
// echo "=======";
// echo $page_height;
// exit;

$x = $pdf->GetX(); // 5
$y = $pdf->GetY(); // 25
$row_height = 0;
// echo $x;
// echo "=======";
// echo $y;
// exit;

foreach ($images as $img) {
	
    switch ($img['type']) {
		//width-height
        case 'full-full':
            $w = $page_width; // 200
            $h = $page_height - 60; // 224 // allow room for label 
            break;
        case 'full-half':
            $w = $page_width;
            $h = ($page_height / 2 - $margin) - 30;
            break;
        case 'half-full':
            $w = $page_width / 2 - $margin;
            $h = $page_height - 60;
            break;
        case 'half-half':
            $w = $page_width / 2 - $margin;
            $h = ($page_height / 2 - $margin) - 30;
            break;
        case 'half-one-third':
            $w = $page_width / 2 - $margin;
            $h = ($page_height / 3 - $margin)- 23;
            break;
        case 'full-one-third':
            $w = $page_width;
            $h = ($page_height / 3 - $margin)- 23;
            break;
        default:
            $w = $page_width / 2 - $margin;
            $h = ($page_height / 2 - $margin) - 60;
    }

    $total_height = $h + $label_height; //224+6 = 230
    // $total_height = $h;

    // New row if width overflow
	// 5+200 > 200-5
    if ($x + $w > $pdf->getPageWidth() - $pdf->getMargins()['right']) {
        $x = $pdf->getMargins()['left']; // 5
        $y += $row_height + $margin; // 25+0+5
        $row_height = 0;
    }

    // New page if height overflow
	// 25 + 230 > 292
    if ($y + $total_height > $page_height) {
        $pdf->AddPage();
        $x = $pdf->getMargins()['left'];
        $y = $pdf->getMargins()['top'];
        $row_height = 0;
    }

    // Draw image
	//$x=5, $y=25, $w=200, $h=224
    $pdf->Image($img['file'], $x, $y, $w, $h, '', '', '', true);

    // Draw label below image
	//$x=5, $y + $h + 1 = 25+224+1
    $pdf->SetXY($x, $y + $h + 1);
	//$w=200, $label_height=224
    $pdf->MultiCell($w, $label_height, $img['label'], 0, 'C', false);

    $x += $w + $margin; // 5+200+5
	
    //$row_height = 230;
    $row_height = max($row_height, $total_height);
}

// $pdf->Output('images_with_labels.pdf', 'I');
// $FileDirectory = dirname(__FILE__) . '/';
$CheckListFileName = $ManyImgPrefix.'_checklistreport_'.date("Y_m_d_H_i_s").'.pdf';
$SecondFileName = $FileDirectory.$CheckListFileName;
$pdf->Output($SecondFileName, 'F');//save file






//===========================================================================================================
//=============================================Merge Report==================================================
//===========================================================================================================
require_once('fpdi-tcpdf/vendor/autoload.php');
use setasign\Fpdi\Tcpdf\Fpdi;
	
//when Cover file is not available
if($CoverFileUrl == ""){
	$pdf->Output($SecondFileName, 'I');//show file
}else{
	//when Cover file is available

	// Extend FPDI (which extends TCPDF)
	class PDFMerger extends Fpdi {}
	$pdf = new PDFMerger();

	// $files = ['file1.pdf', 'file2.pdf'];
	$files = [$CoverFileUrl, $CheckListFileName];
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	foreach ($files as $file) {
		// $pageCount = $pdf->setSourceFile($file);
		$pageCount = $pdf->setSourceFile($FileDirectory.$file);

		for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
			$templateId = $pdf->importPage($pageNo);
			$size = $pdf->getTemplateSize($templateId);
			
			$pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
			$pdf->useTemplate($templateId);
		}
	}

	$CombineFileName = $FileDirectory.$ManyImgPrefix.'_invoicereport_'.date("Y_m_d_H_i_s").'.pdf';
	$pdf->Output($CombineFileName, 'F');//save file
	
	$pdf->Output($CombineFileName, 'I');//show file
}