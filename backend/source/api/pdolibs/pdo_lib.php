<?php
require("Db.class.php");
// include_once ('languages/lang_en_msg.php');
// include_once ('languages/lang_fr_msg.php');

class query_base {
    public $command = '';
    public $table = '';
    public $columns = array();
    public $values = array();
    public $query = '';
    public $select = '';
    public $pks = array();
    public $pk_values = array();
    public $params = array();
    public $wh_params = array();
    public $bUseInsetId = false;

    function __construct($command) {
        $this->command = $command;
    }

    function build_query() {
        $this->select_query();
        $this->params();

        if ($this->command == 'INSERT') {
            $this->insert();
        }

        else if ($this->command == 'UPDATE') {
            $this->where_params();
            $this->update();
        }
        else if ($this->command == 'DELETE') {
            $this->where_params();
            $this->remove();
        }
    }

    function insert() {
        $cl = implode(',', $this->columns);
        $vl = ':' . implode(',:', $this->columns);
        $this->query = "INSERT INTO {$this->table} ($cl) values ($vl)";
    }

    function update() {
        $cl = $this->set_columns();
        $wh = $this->where();
        $this->query = "UPDATE {$this->table} SET $cl $wh";
    }

    function remove() {
        $wh = $this->where();
        $this->query = "DELETE FROM {$this->table} $wh";
    }

    function set_columns() {
        $setColumns = '';
        foreach ($this->columns as $column) {
            $setColumns .= $column . ' = :' . $column . ', ';
        }

        $setColumns = substr($setColumns, 0, -2);
        return $setColumns;
    }

    function where() {
	  
	    if(count($this->pks) == 0){
			return '';
		}
		
        $wh = ' WHERE '; 
        foreach ($this->pks as $idx => $pk) {
            $wh .= $pk . ' = :' . $pk . ' AND ';
        }
        $wh = substr($wh, 0, -5); 
        return $wh;
    
	
        /* $wh = ' WHERE '; 
        foreach ($this->pks as $idx => $pk) {
            $wh .= $pk . ' = :' . $pk . ' AND ';
        }
        $wh = substr($wh, 0, -5); 
        return $wh;
		
		 */
    }

    function select_query() {
        $this->select = '';
        $wh = $this->where();
        $this->select = "SELECT * FROM {$this->table} $wh";
    }

    function params() {
        $this->params = array();

        foreach ($this->columns as $idx => $cl) {
            $this->params[$cl] = $this->values[$idx];
        }

        foreach ($this->pks as $idx => $cl) {
            if (count($this->pk_values) == 0) {
                break;
            }
            $this->params[$cl] = $this->pk_values[$idx];
        }
    }

    function where_params() {
        $this->wh_params = array();
        foreach ($this->pks as $idx => $cl) {
            $this->wh_params[$cl] = $this->pk_values[$idx];
        }
    }
}

class insertq extends query_base {	

    function __construct() {
        parent::__construct('INSERT');
    }
}

class updateq extends query_base {

    function __construct() {
        parent::__construct('UPDATE');
    }
}

class deleteq extends query_base {
    function __construct() {
        parent::__construct('DELETE');
    }
}

function exec_query($aQuerys, $jUserId = '1', $sLang = 'en_GB', $bSqlLog = TRUE, $bRetData = FALSE, $bEcho = FALSE, $showUError = FALSE ) {

    $curDateTime = date('Y-m-d H:i:s');
	
    try {
        $bResult = TRUE;
        $result = false;
        $lastInsertedId = 0;
        $msg = array();
        $errors = array();
        $errorNos = array();
        $errors_info = array();
        $command = '';
        $sucMsg = '';
        $aData = array();
        $strAuditInsSql = '';
		
        $RemoteIP = get_client_ip();
        $dbh = new Db();

        $dbh->beginTransaction();

        foreach ($aQuerys as $akey => $aQuery) {

            $command = strtoupper($aQuery->command);
            $Table = strtolower($aQuery->table);
            $pkValue = array_key_exists(0, $aQuery->pk_values) ? $aQuery->pk_values[0] : '';

            $whClause = '';
            $sqlOld = '';
            $sqlNew = '';
            $fieldNames = array();
            $oldValues = array();
            $newValues = array();

            if ($command == 'INSERT')
                $sucMsg = 'New Data Added Successfully';
            else if ($command == 'UPDATE')
                $sucMsg = 'Data Updated Successfully';
            else if ($command == 'DELETE')
                $sucMsg = 'Data Removed Successfully';			 

            $arrLog = array();

            if ($command == 'UPDATE' || $command == 'DELETE' && $bSqlLog) {
                $sqlOld = $aQuery->select;
                $dbh->bindMore($aQuery->wh_params);
                $aRows = $dbh->query($sqlOld); 
 
                foreach ($aRows as $aRow) {
                    $oldValues = array_values($aRow);
                    //print_r($oldValues);
                    if ($command == 'DELETE') {
                        $fieldNames = array_keys($aRow);
                        foreach ($fieldNames as $key => $fieldName) {
                            $oldValue = $oldValues[$key];
                            $arrLog[] = array($fieldName, $oldValue, '');
                        }
                    }
                }
            }

            $query = $aQuery->query;

            if (isset($aQuery->values[0])) {
                if ((string)$aQuery->values[0] == '[LastInsertedId]') {
                    $aQuery->values[0] = $lastInsertedId;
                    $aQuery->params();
                }
            }

            $dbh->bindMore($aQuery->params);
            $pdo_result = $dbh->query($query);

            if (is_object($pdo_result)) {
			 
                $result = false;
                $errors_info = $pdo_result->errorInfo; 



               if (strpos($pdo_result, 'PDOException:') !== false) {                
                    if($showUError){
						echo $pdo_result->getMessage();
					}
                    throw new Exception();
                }
                throw new Exception();
            }
            else {
                $result = true;
            }
            $bResult &= $result;

            if ($result) {
                if ($pkValue == NULL) {
                    $aQuery->pk_values = array();
                    $aQuery->pk_values[] = $dbh->lastInsertId();

                    if ($aQuery->bUseInsetId) {
                        $lastInsertedId = $dbh->lastInsertId();
                    }
                }
                else {
                    $lastInsertedId = $pkValue;
                }

                if ($command != 'DELETE' && $bSqlLog) {
                    $sqlNew = $aQuery->select;
                    $aQuery->where_params();
                    $dbh->bindMore($aQuery->wh_params);
                    $aRows = $dbh->query($sqlNew);

                    foreach ($aRows as $aRow) {
                        $fieldNames = array_keys($aRow);
                        //print_r($fieldNames);
                        $newValues = array_values($aRow);

                        foreach ($fieldNames as $key => $fieldName) {
                            $oldValue = array_key_exists(0, $oldValues) ? $oldValues[$key] : '';
                            $newValue = array_key_exists(0, $newValues) ? $newValues[$key] : '';

                            if ($oldValue != $newValue)
                                $arrLog[] = array($fieldName, $oldValue, $newValue);
                        }
                    }

                    if ($bRetData) {
                        $rowData = array();
                        if ($fieldNames != NULL) {
                            foreach ($fieldNames as $key => $fieldName) {
                                $rowData[] = $newValues[$key];
                            }
                            $aData[$Table][] = $rowData;
                        }
                    }
                }
				

                if ($bSqlLog) {
                    $jsonText = json_encode($arrLog);
					
					//*****************Insert/Update value******************/
					$vs['values']=$aQuery->params; 
					$sqlParams = json_encode($vs);
					//*****************Insert/Update value******************/
					
					
					
				//	$sqlParams = json_encode($aQuery->wh_params);
					// echo $jsonText;
				 if($jsonText !== "[]"){
                    
                
                    $sqlLog = "INSERT INTO  t_sqllog(LogDate,RemoteIP, UserId, QueryType, TableName, JsonText,  SqlText, SqlParams) VALUES (:LogDate,:RemoteIP, :UserId, :QueryType, :TableName, :JsonText, :SqlText, :SqlParams)";
                    $dbh->bindMore(array('LogDate' => $curDateTime,'RemoteIP' => $RemoteIP, 'UserId' => $jUserId, 'QueryType' => $command, 'TableName' => $Table, 'JsonText' => $jsonText,  'SqlText' => $query, 'SqlParams' => $sqlParams));
                    $result2 = $dbh->query($sqlLog);
					
					
					// echo $sqlLog;
					if (is_object($result2)) {
						$errors_info = $result2->errorInfo; 
						// echo "<pre>";
						// print_r($result2);
						$query = $sqlLog;
						$bResult &= false;
					 }
					 else{
						 $bResult &= true;
					 } 
                    }
                }
			  
					
                
				$msg[$aQuery->pks[0]] = is_numeric($lastInsertedId)? intval($lastInsertedId) : $lastInsertedId;
            }
        }

        if (!$bResult) {
            throw new Exception();
        }

        $dbh->executeTransaction();
        $msg['msgType'] = 'success';
        $msg['msg'] = $sucMsg;
        //$msg['msg'] = "Nouvelles données ajoutées avec succès.";
        $msg['aaData'] = $aData;
        $msg['id'] = $lastInsertedId;
		// echo json_encode($msg);
        return $msg;
    }
    catch (Exception $e) {

        $dbh->rollBack();

        $errorNos[] = $errors_info[1];
        $errors[] = $errors_info[2];
		$strErrors = [];
        $strErrors = getErrors($errorNos, $errors);

        $msg['msgType'] = 'error';
        $msg['msg'] = $strErrors;
		//*****************Insert/Update value******************/
		$vs['values']=$aQuery->params; 
		$sqlParams = json_encode($vs);
		//*****************Insert/Update value******************/
		

		//$sqlParams = json_encode($aQuery->wh_params);
        $query_elog = "INSERT INTO  t_errorlog( LogDate, RemoteIP, UserId, Query, QueryType, ErrorNo, ErrorMsg,SqlParams) VALUES (:LogDate, :RemoteIP, :UserId, :Query, :QueryType, :ErrorNo, :ErrorMsg, :SqlParams)";
        $dbh->bindMore(array('LogDate' => $curDateTime,'RemoteIP' => $RemoteIP, 'UserId' => $jUserId, 'Query' => $query, 'QueryType' => $command, 'ErrorNo' => $errors_info[1], 'ErrorMsg' => $errors_info[2],  'SqlParams' => $sqlParams));
        $result = $dbh->query($query_elog);
		
		if (strpos($result, 'PDOException:') !== false) {
			echo $result;
			return;
		}

        if (is_object($result)) {
            $msg['msg'] = $strErrors . 'But Error Log Saved Fail';
        }
        return $msg;
    }
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function getErrors($errorNos, $errors) {
    $eMsg = '';
    foreach ($errorNos as $key => $errorNo) {
        $error = $errors[$key];
		$custErr = '';
        switch ($errorNo) {
            case '1062' :
                $errorStr = substr($error, strpos($error, 'Duplicate entry \'') + 17);
                $constValue = substr($errorStr, 0, strpos($errorStr, '\''));
                $errorStr1 = substr($error, strpos($error, 'for key \'') + 9);
                $constName = substr($errorStr1, 0, -1);
				
				 if ($constName == 'PRIMARY')
                    $eMsg .= "* $error";
                else
                    $eMsg .= '* ' . $constName . " '" . $constValue . "'";

                break;
            case '1451' :
                $errorStr = substr($error, strpos($error, 'CONSTRAINT `') + 12);
                $constName = substr($errorStr, 0, strpos($errorStr, '`'));              
				$custErr = $constName;
                $eMsg = '* ' . $custErr . '';
                break;
            case '1452' :
                $errorStr = substr($error, strpos($error, 'CONSTRAINT `') + 12);
                $constName = substr($errorStr, 0, strpos($errorStr, '`'));
				$custErr = $constName . '_1452';
				$eMsg = '* ' . $custErr . '';
                break;
            case '1065' :
                $errorStr = substr($error, strpos($error, 'CONSTRAINT `') + 12);
                $constName = substr($errorStr, 0, strpos($errorStr, '`'));
				$custErr = 'Query was empty';
				$eMsg = '* ' . $custErr . '';
                break;
            case '1054' :
                $errorStr = substr($error, strpos($error, 'CONSTRAINT `') + 12);
                $constName = substr($errorStr, 0, strpos($errorStr, '`'));
				$custErr = 'There is a unknown column in the query';
				$eMsg = '* ' . $custErr . '';
                break;
            case '1146' :
                $errorStr = substr($error, strpos($error, 'CONSTRAINT `') + 12);
                $constName = substr($errorStr, 0, strpos($errorStr, '`'));
				$custErr = "There is a table doesn\'t exist.";
				$eMsg = '* ' . $custErr . '';
                break;
            case '1064' :
                $errorStr = substr($error, strpos($error, 'CONSTRAINT `') + 12);
                $constName = substr($errorStr, 0, strpos($errorStr, '`'));
				$custErr = 'You have an error in your SQL syntax.';
				$eMsg = '* ' . $custErr . '';
                break;
            default :
                $eMsg = array("ErrorNo" => $errorNo, "ErrorMsg" => $error);
				$eMsg = '* ' . $error . '';
                break;
        }
    }
    return $eMsg;
}
/* 
function gettimestampId($facilityId){
		    $dbh = new Db();
			
			$sql="SELECT  ROUND(UNIX_TIMESTAMP(CURTIME(6)) * 1000) current_mills, IFNULL(MAX(LogSeq),0) LogSeq FROM t_facilitylog WHERE FacilityId={$facilityId} ;";
			$Res = $dbh->query($sql);
			$currentMills = $Res[0]['current_mills']<$Res[0]['LogSeq']?($Res[0]['LogSeq']+1):$Res[0]['current_mills'];
			return $currentMills;
} */