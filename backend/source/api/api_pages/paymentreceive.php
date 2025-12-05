<?php

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {

	case "getDataList":
		$returnData = getDataList($data);
		break;

	case "getDataSingle":
		$returnData = getDataSingle($data);
		break;

	case "dataAddEdit":
		$returnData = dataAddEdit($data);
		break;

	case "deleteData":
		$returnData = deleteData($data);
		break;

	default:
		echo "{failure:true}";
		break;
}

function getDataList($data)
{

	try {
		$dbh = new Db();

		$query = "SELECT a.PaymentId AS id,  DATE_FORMAT(a.PaymentDate, '%Y-%m-%d') as PaymentDate,
		a.CustomerId,b.CustomerName, a.CustomerGroupId,c.CustomerGroupName,a.BankId,d.BankName,
		a.TotalPaymentAmount,a.Remarks,0 as InvoiceTotalAmount,a.StatusId
		FROM t_payment a
		LEFT JOIN t_customer b ON a.CustomerId = b.CustomerId
		LEFT JOIN t_customergroup c ON a.CustomerGroupId = c.CustomerGroupId
		LEFT JOIN t_bank d ON a.BankId = d.BankId
		ORDER BY a.PaymentDate DESC, a.PaymentId DESC;";

		$resultdatalist = $dbh->query($query);


		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdatalist
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function getDataSingle($data)
{
	$PaymentId = trim($data->id);

	try {
		$dbh = new Db();

		/**Master Data */
		// $query = "SELECT PaymentId AS id, `PaymentDate`, `CustomerId`, `CustomerGroupId`, `BankId`, 
		// `TotalPaymentAmount`, `Remarks`, `UserId`
		// FROM t_payment
		// where PaymentId=$PaymentId;";

		$resultdataMaster = []; // $dbh->query($query);

		/**Items Data */
		$query = "SELECT a.PaymentItemId as autoId, a.`PaymentItemId`, a.`PaymentId`, a.`InvoiceItemId`, a.`PaymentAmount`
		,b.AccountCode, b.Description, b.TransactionDate, b.TransactionReference, FLOOR(b.BaseAmount) as BaseAmount,
		ifnull(a.PaidAmount,0) as TotalPaymentAmount, FLOOR(ifnull(a.DueAmount,0)) DueAmount, ifnull(a.IsPaidPayment,0) as IsPaid
		FROM t_paymentitems a 
		inner join t_invoiceitems b on a.InvoiceItemId=b.InvoiceItemId
		where a.PaymentId=$PaymentId
		order by a.PaymentItemId ASC;";
		$resultdataItems = $dbh->query($query);


		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => array("master" => $resultdataMaster, "items" => $resultdataItems)
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function dataAddEdit($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	} else {

		$dbh = new Db();

		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		$PaymentId = $data->rowData->id;
		$PaymentDate = $data->rowData->PaymentDate;
		$CustomerId = $data->rowData->CustomerId ? $data->rowData->CustomerId : null;
		$CustomerGroupId = $data->rowData->CustomerGroupId ? $data->rowData->CustomerGroupId : null;
		$BankId = $data->rowData->BankId ? $data->rowData->BankId : null;
		$TotalPaymentAmount = $data->rowData->TotalPaymentAmount ? $data->rowData->TotalPaymentAmount : 0;
		$Remarks = $data->rowData->Remarks ? $data->rowData->Remarks : null;
		$StatusId = $data->rowData->StatusId ? $data->rowData->StatusId : 1;

		$items = isset($data->items) ? $data->items : [];

		$query = "SELECT count(a.PaymentId) DraftCount
		FROM t_payment a
		where a.CustomerId=$CustomerId
		and a.StatusId=1;";
		$resultdatalist = $dbh->query($query);
		if ($resultdatalist[0]['DraftCount'] >= 1 && $PaymentId == "") {
			return $returnData = msg(0, 500, 'There is already a draft payment for this customer. Please complete or delete the existing draft payment before creating a new one.');
		}



		try {

			$dbh = new Db();
			$aQuerys = array();

			if ($PaymentId == "") {
				$q = new insertq();
				$q->table = 't_payment';
				$q->columns = ['PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId', 'TotalPaymentAmount', 'Remarks', 'UserId', 'StatusId'];
				$q->values = [$PaymentDate, $CustomerId, $CustomerGroupId, $BankId, $TotalPaymentAmount, $Remarks, $UserId, $StatusId];
				$q->pks = ['PaymentId'];
				$q->bUseInsetId = true;
				$q->build_query();
				$aQuerys[] = $q;
			} else {
				$StatusId = 5; //Completed
				$u = new updateq();
				$u->table = 't_payment';
				$u->columns = ['PaymentDate', 'CustomerId', 'CustomerGroupId', 'BankId', 'TotalPaymentAmount', 'Remarks', 'StatusId'];
				$u->values = [$PaymentDate, $CustomerId, $CustomerGroupId, $BankId, $TotalPaymentAmount, $Remarks, $StatusId];
				$u->pks = ['PaymentId'];
				$u->pk_values = [$PaymentId];
				$u->build_query();
				$aQuerys[] = $u;


				foreach ($items as $key => $obj) {
					// print_r($obj);
					$IsPaid = $obj->IsPaid ? $obj->IsPaid : 0;

					$u = new updateq();
					$u->table = 't_paymentitems';
					$u->columns = ['PaymentAmount','IsPaidPayment'];
					$u->values = [$obj->PaymentAmount ? $obj->PaymentAmount : null, $IsPaid];
					$u->pks = ['PaymentItemId'];
					$u->pk_values = [$obj->PaymentItemId];
					$u->build_query();
					$aQuerys[] = $u;

					$TotalPaymentAmount = ($obj->TotalPaymentAmount + ($obj->PaymentAmount ? $obj->PaymentAmount : 0));
					$u = new updateq();
					$u->table = 't_invoiceitems';
					$u->columns = ['TotalPaymentAmount', 'IsPaid'];
					$u->values = [$TotalPaymentAmount, $IsPaid];
					$u->pks = ['InvoiceItemId'];
					$u->pk_values = [$obj->InvoiceItemId];
					$u->build_query();
					$aQuerys[] = $u;
				}
			}









			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;

			//when insert new payment, auto allocate payment amount to unpaid invoices
			if ($success == 1 && $PaymentId == "" && $CustomerId && $TotalPaymentAmount > 0) {
				$TmpAmp = $TotalPaymentAmount;

				$query = "SELECT a.`InvoiceItemId`, a.BaseAmount, ifnull(a.TotalPaymentAmount,0) as TotalPaymentAmount, 
				(ifnull(a.BaseAmount,0) - ifnull(a.TotalPaymentAmount,0)) DueAmount
				FROM `t_invoiceitems` a 
				inner join t_customer b on a.AccountCode=b.CustomerCode 
				WHERE b.CustomerId=$CustomerId 
				and a.IsPaid=0
				order by a.CreateTs desc;";
				$result  = $dbh->query($query);
				foreach ($result as $row) {
					$PaymentAmount = 0;
					$IsPaidPayment = 0;
					$DueAmount = (int)$row['DueAmount'];

					if ($TmpAmp > 0) {

						if ($DueAmount <= $TmpAmp) {
							$TmpAmp -= $DueAmount;
							$PaymentAmount = $DueAmount;
							$IsPaidPayment = 1;
						} else {
							$PaymentAmount = $TmpAmp;
							$TmpAmp = 0;
							$IsPaidPayment = 0;
						}
					}

					$query1 = "INSERT INTO t_paymentitems (PaymentId, InvoiceItemId,PaidAmount,DueAmount, PaymentAmount,IsPaidPayment)
					values(" . $res['PaymentId'] . "," . $row['InvoiceItemId'] . ",".$row['TotalPaymentAmount'].",$DueAmount,$PaymentAmount,$IsPaidPayment);";
					$dbh->query($query1);

					// if($TmpAmp <=0) {
					// 	break;
					// }
				}
			}



			$returnData = [
				"success" => $success,
				"status" => $status,
				"PaymentId" => $PaymentId == "" ? $res['PaymentId'] : $PaymentId,
				"UserId" => $UserId,
				"message" => $res['msg'],
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}


function deleteData($data)
{

	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		return $returnData = msg(0, 404, 'Page Not Found!');
	}
	// CHECKING EMPTY FIELDS
	elseif (!isset($data->rowData->id)) {
		$fields = ['fields' => ['id']];
		return $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
	} else {

		$PaymentId = $data->rowData->id;
		$lan = trim($data->lan);
		$UserId = trim($data->UserId);

		try {

			$d = new deleteq();
			$d->table = 't_paymentitems';
			$d->pks = ['PaymentId'];
			$d->pk_values = [$PaymentId];
			$d->build_query();
			$aQuerys[] = $d;

			$d = new deleteq();
			$d->table = 't_payment';
			$d->pks = ['PaymentId'];
			$d->pk_values = [$PaymentId];
			$d->build_query();
			$aQuerys[] = $d;

			$res = exec_query($aQuerys, $UserId, $lan);
			$success = ($res['msgType'] == 'success') ? 1 : 0;
			$status = ($res['msgType'] == 'success') ? 200 : 500;

			$returnData = [
				"success" => $success,
				"status" => $status,
				"UserId" => $UserId,
				"message" => $res['msg']
			];
		} catch (PDOException $e) {
			$returnData = msg(0, 500, $e->getMessage());
		}

		return $returnData;
	}
}
