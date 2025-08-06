<?php

function apiLogWrite($logText)
{

	$logFileName = "../../../media/log/apilog" . date('Y') . ".txt";
	file_put_contents($logFileName, $logText . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function recordNotFoundMsg($SysValue = 0, $SysMessage = "No record found")
{
	$dList = array(["SysValue" => $SysValue, "SysMessage" => $SysMessage]);
	return $dList;
}

function checkNull($value)
{
	return $value ? $value : null;
}

function convertAppToDBDate($appDate)
{
	if ($appDate) {
		$DateConvert = DateTime::createFromFormat('d-M-Y', $appDate);
		$DateConvertDB = $DateConvert->format('Y-m-d');
		return $DateConvertDB;
	} else {
		return $appDate;
	}
}

function convertAppToDBDateTime($appDate)
{
	if ($appDate) {
		$DateConvert = DateTime::createFromFormat('d-M-Y h:i A', $appDate);
		$DateConvertDB = $DateConvert->format('Y-m-d H:i');
		return $DateConvertDB;
	} else {
		return $appDate;
	}
}
