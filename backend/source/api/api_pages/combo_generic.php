<?php
include("global.php");

$task = '';
if (isset($data->action)) {
	$task = trim($data->action);
}

switch ($task) {
	case "RoleList":
		$returnData = RoleList($data);
		break;
	case "UserList":
		$returnData = UserList($data);
		break;
	case "CheckList":
		$returnData = CheckList($data);
		break;

	case "BusinessLineList":
		$returnData = BusinessLineList($data);
		break;
	case "CustomerGroupList":
		$returnData = CustomerGroupList($data);
		break;

	case "CustomerList":
		$returnData = CustomerList($data);
		break;
	case "DesignationList":
		$returnData = DesignationList($data);
		break;
	case "DepartmentList":
		$returnData = DepartmentList($data);
		break;
	case "BankList":
		$returnData = BankList($data);
		break;
	case "BuyerList":
		$returnData = BuyerList($data);
		break;
	case "MerchantList":
		$returnData = MerchantList($data);
		break;
	case "MonthList":
		$returnData = MonthList($data);
		break;
	case "YearList":
		$returnData = YearList($data);
		break;
	case "TeamList":
		$returnData = TeamList($data);
		break;
	case "MachineList":
		$returnData = MachineList($data);
		break;
	case "MachineModelList":
		$returnData = MachineModelList($data);
		break;
	case "TransactionList":
		$returnData = TransactionList($data);
		break;
	case "InstallationTransactionList":
		$returnData = InstallationTransactionList($data);
		break;


	case "NextProductSystemCode":
		$returnData = NextProductSystemCode($data);
		break;

	case "NextInvoiceNumber":
		$returnData = NextInvoiceNumber($data);
		break;








		







	case "ProductCategoryList":
		$returnData = ProductCategoryList($data);
		break;
	case "ProductGenericList":
		$returnData = ProductGenericList($data);
		break;
	case "SupplierTypeList":
		$returnData = SupplierTypeList($data);
		break;
	case "MembershipTypeList":
		$returnData = MembershipTypeList($data);
		break;
	case "StrengthList":
		$returnData = StrengthList($data);
		break;
	case "ManufacturerList":
		$returnData = ManufacturerList($data);
		break;
	case "CountryList":
		$returnData = CountryList($data);
		break;
	case "SupplierList":
		$returnData = SupplierList($data);
		break;
	case "ClientList":
		$returnData = ClientList($data);
		break;
	case "ProductList":
		$returnData = ProductList($data);
		break;
	case "ProductVirtualList":
		$returnData = ProductVirtualList($data);
		break;
	case "ReferenceList":
		$returnData = ReferenceList($data);
		break;
	case "ExpenseTypeList":
		$returnData = ExpenseTypeList($data);
		break;
	case "PaymentModeList":
		$returnData = PaymentModeList($data);
		break;
	case "AdjTypeList":
		$returnData = AdjTypeList($data);
		break;
	case "AttendancePersonList":
		$returnData = AttendancePersonList($data);
		break;


}

// echo json_encode($returnData);
return $returnData;



function RoleList($data)
{
	try {

		$dbh = new Db();

		$query = "SELECT `RoleId` id,`RoleName` `name`
	 			 	FROM `t_roles` 
					ORDER BY RoleName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function UserList($data)
{
	try {

		// $ClientId = trim($data->ClientId);
		// $BranchId = trim($data->BranchId);
		$DepartmentId = isset($data->DepartmentId)?trim($data->DepartmentId): 0;

		$dbh = new Db();

		$query = "SELECT `UserId` id,`UserName` `name`
	 			 	FROM `t_users` 
					where (DepartmentId=$DepartmentId OR $DepartmentId=0)
					ORDER BY UserName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function CheckList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$dbh = new Db();

		$query = "SELECT `CheckId` id,`CheckName` `name`
	 			 	FROM `t_checklist` 
					ORDER BY CheckName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function CustomerList($data)
{
	try {

		$CustomerGroupId = $data->CustomerGroupId?$data->CustomerGroupId:0;
		$dbh = new Db();
		$query = "SELECT `CustomerId` id, CustomerName `name`
	 			 	FROM `t_customer` 
					where (CustomerGroupId = $CustomerGroupId OR $CustomerGroupId = 0)
					ORDER BY CustomerName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function DesignationList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$dbh = new Db();

		$query = "SELECT `DesignationId` id,`DesignationName` `name`
	 			 	FROM `t_designation` 
					where ClientId=$ClientId
					ORDER BY DesignationName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function DepartmentList($data)
{
	try {

		// $ClientId = trim($data->ClientId);
		// $BranchId = trim($data->BranchId);

		$dbh = new Db();

		$query = "SELECT `DepartmentId` id,`DepartmentName` `name`
	 			 	FROM `t_department` 
					ORDER BY DepartmentName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function BankList($data)
{
	try {

		$dbh = new Db();

		$query = "SELECT `BankId` id,`BankName` `name`
	 			 	FROM `t_bank` 
					ORDER BY BankName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function BuyerList($data)
{
	try {

		$dbh = new Db();

		$query = "SELECT distinct `GeneralDescription11` id,`GeneralDescription11` `name`
	 			 	FROM `t_invoiceitems` 
					where GeneralDescription11 <> ''
					ORDER BY GeneralDescription11;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function MerchantList($data)
{
	try {

		$dbh = new Db();

		$query = "SELECT distinct `GeneralDescription14` id,`GeneralDescription14` `name`
	 			 	FROM `t_invoiceitems` 
					where GeneralDescription14 <> ''
					ORDER BY GeneralDescription14;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function BusinessLineList($data)
{
	try {

 
		$dbh = new Db();

		$query = "SELECT `BusinessLineId` id,concat(`BusinessLineCode`,' - ',BusinessLineName) `name`
	 			 	FROM `t_businessline` 
					ORDER BY BusinessLineCode;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function CustomerGroupList($data)
{
	try {

 
		$dbh = new Db();

		$query = "SELECT `CustomerGroupId` id,CustomerGroupName `name`
	 			 	FROM `t_customergroup` 
					ORDER BY CustomerGroupName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}
function MonthList($data)
{
	try {


		$dbh = new Db();
		$query = "SELECT distinct MonthId id, MonthName `name` FROM t_month	ORDER BY MonthId;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function YearList($data)
{
	try {

		$ClientId = trim($data->ClientId);

		$dbh = new Db();
		$query = "SELECT distinct YearName id, YearName `name` FROM t_year
		where ClientId = $ClientId
		ORDER BY YearName DESC;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function TeamList($data)
{
	try {

		$ClientId = trim($data->ClientId);

		$dbh = new Db();
		$query = "SELECT distinct TeamId id, TeamName `name` FROM t_team
		ORDER BY TeamName DESC;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function MachineList($data)
{
	try {
		$ClientId = trim($data->ClientId);
		// $BranchId = trim($data->BranchId); 

		$dbh = new Db();
		$query = "SELECT MachineId id, MachineName `name` FROM t_machine ORDER BY MachineName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function MachineModelList($data)
{
	try {
		$ClientId = trim($data->ClientId);
		$MachineId = trim($data->MachineId)?trim($data->MachineId):0;
		// $BranchId = trim($data->BranchId); 

		$dbh = new Db();
		$query = "SELECT MachineModelId id, MachineModelName `name` 
		FROM t_machinemodel 
		where MachineId=$MachineId
		ORDER BY MachineModelName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function TransactionList($data)
{
	try {
		// $ClientId = trim($data->ClientId);
		$DepartmentId = trim($data->DepartmentId)?trim($data->DepartmentId):0;
		$UserId = trim($data->VisitorId)?trim($data->VisitorId):0;
		$StartDate = trim($data->StartDate);
		$EndDate = trim($data->EndDate) . " 23-59-59";

		// Visit date-customer name-machinename-modelname

		$dbh = new Db();
		$query = "SELECT a.TransactionId id, concat(DATE_FORMAT(a.TransactionDate, '%d-%b-%Y'),' - ', b.CustomerName,' - ',c.MachineName,' - ',d.MachineModelName) `name` 
		FROM t_transaction a
		inner join t_customer b on a.CustomerId=b.CustomerId
		inner join t_machine c on a.MachineId=c.MachineId
		inner join t_machinemodel d on a.MachineModelId=d.MachineModelId
		inner join t_users e on a.UserId=e.UserId
		where a.TransactionTypeId=1
		AND (e.DepartmentId=$DepartmentId OR $DepartmentId=0)
		AND (a.UserId=$UserId OR $UserId=0)
		AND (a.TransactionDate BETWEEN '$StartDate' and '$EndDate')
		ORDER BY a.TransactionDate DESC;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function InstallationTransactionList($data)
{
	try {
		// $ClientId = trim($data->ClientId);
		$DepartmentId = trim($data->DepartmentId)?trim($data->DepartmentId):0;
		$UserId = trim($data->VisitorId)?trim($data->VisitorId):0;
		$StartDate = trim($data->StartDate);
		$EndDate = trim($data->EndDate) . " 23-59-59";

		// Visit date-customer name-machinename-modelname

		$dbh = new Db();
		$query = "SELECT a.TransactionId id, concat(DATE_FORMAT(a.TransactionDate, '%d-%b-%Y'),' - ', b.CustomerName,' - ',c.MachineName,' - ',d.MachineModelName) `name` 
		FROM t_transaction a
		inner join t_customer b on a.CustomerId=b.CustomerId
		inner join t_machine c on a.MachineId=c.MachineId
		inner join t_machinemodel d on a.MachineModelId=d.MachineModelId
		inner join t_users e on a.UserId=e.UserId
		where a.TransactionTypeId=1
		AND a.DropDownListIDForPurpose = 5
		AND (e.DepartmentId=$DepartmentId OR $DepartmentId=0)
		AND (a.UserId=$UserId OR $UserId=0)
		AND (a.TransactionDate BETWEEN '$StartDate' and '$EndDate')
		ORDER BY a.TransactionDate DESC;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


























































function ProductCategoryList($data)
{
	try {
		$ClientId = trim($data->ClientId);
		$ProductGroupId = trim($data->ProductGroupId) ? trim($data->ProductGroupId) : 0;

		$dbh = new Db();
		$query = "SELECT ProductCategoryId id, CategoryName `name` FROM t_productcategory 
		where ClientId=$ClientId 
		and ProductGroupId=$ProductGroupId
		ORDER BY CategoryName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function ProductGenericList($data)
{
	try {
		$ClientId = trim($data->ClientId);
		$ProductGroupId = trim($data->ProductGroupId) ? trim($data->ProductGroupId) : 0;
		// $ProductCategoryId = trim($data->ProductCategoryId)?trim($data->ProductCategoryId):0; 

		$dbh = new Db();
		$query = "SELECT ProductGenericId id, GenericName `name` FROM t_productgeneric 
		where ClientId=$ClientId 
		and ProductGroupId=$ProductGroupId
		ORDER BY GenericName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function SupplierTypeList($data)
{
	try {


		$dbh = new Db();
		$query = "SELECT SupplierTypeId id, SupplierType `name` FROM t_suppliertype 
		ORDER BY SupplierTypeId;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function MembershipTypeList($data)
{
	try {


		$dbh = new Db();
		$query = "SELECT MembershipTypeId id, MembershipType `name` FROM t_membershiptype 
		ORDER BY MembershipTypeId;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function StrengthList($data)
{
	try {

		$ClientId = trim($data->ClientId);

		$dbh = new Db();
		$query = "SELECT StrengthId id, StrengthName `name` FROM t_strength
		where ClientId =$ClientId
		ORDER BY StrengthName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function ManufacturerList($data)
{
	try {

		$ClientId = trim($data->ClientId);

		$dbh = new Db();
		$query = "SELECT ManufacturerId id, ManufacturerName `name` FROM t_manufacturer
		where ClientId =$ClientId
		ORDER BY ManufacturerName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function CountryList($data)
{
	try {


		$dbh = new Db();
		$query = "SELECT CountryId id, CountryName `name` FROM t_country
		ORDER BY CountryName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function SupplierList($data)
{
	try {

		$ClientId = trim($data->ClientId);

		$dbh = new Db();
		$query = "SELECT SupplierId id, SupplierName `name` FROM t_supplier
		where ClientId = $ClientId
		ORDER BY SupplierName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function ClientList($data)
{
	try {

		$ClientId = trim($data->ClientId);

		$dbh = new Db();
		$query = "SELECT ClientId id, ClientName `name` 
		FROM t_client
		ORDER BY ClientName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function ProductList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		// $query = "SELECT a.ProductId id, CONCAT(a.ProductName,' (Code:',a.ProductBarcode,', Cost:',a.NewCost,')-',a.ProductId) `name`,
		// a.TradePrice,a.MRP,a.SystemBarcode,a.ProductBarcode,a.VatonSales,a.VatonTrade,a.SalesDiscountPercentage,
		// a.SalesDiscountAmount,a.NewCost
		// FROM t_product a
		// where a.ClientId = $ClientId
		// ORDER BY a.ProductName;"; 
		

		$dbh = new Db();
		$query = "SELECT a.ProductId id, CONCAT(a.ProductName,' ',b.CategoryName,' ',c.GenericName,' (Code:',a.ProductBarcode,', Cost:',a.NewCost,', Stock:',IFNULL(d.Quantity,0),')-',a.ProductId) `name`,
		a.TradePrice,a.MRP,a.SystemBarcode,a.ProductBarcode,a.VatonSales,a.VatonTrade,a.SalesDiscountPercentage,
		a.SalesDiscountAmount,a.NewCost,a.ProductName
		FROM t_product a
		INNER JOIN t_productcategory b ON a.ProductCategoryId=b.ProductCategoryId
		INNER JOIN t_productgeneric c ON a.ProductGenericId=c.ProductGenericId
		LEFT JOIN (SELECT s.`ProductId`,SUM(s.`Quantity`) Quantity FROM `t_productstock` s WHERE s.`ClientId`=$ClientId AND s.`BranchId`=$BranchId GROUP BY s.`ProductId`) d ON a.ProductId=d.ProductId
				
		where a.ClientId = $ClientId
		ORDER BY a.ProductName;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function ProductVirtualList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		/*Name-Strength Category-Generic (Cost=23-Stock=12 Code:)*/

		$dbh = new Db();
		// $query = "SELECT CONCAT(ProductName,' (Code:',ProductBarcode,', Cost:',NewCost,')-',ProductId) `name`
		
		// $query = "SELECT CONCAT(a.ProductName,' ',b.CategoryName,' ',c.GenericName,' (Code:',a.ProductBarcode,', Cost:',a.NewCost,')-',a.ProductId) `name`
		// FROM t_product a
		// INNER JOIN t_productcategory b ON a.ProductCategoryId=b.ProductCategoryId
		// INNER JOIN t_productgeneric c ON a.ProductGenericId=c.ProductGenericId
		// where a.ClientId = $ClientId
		// ORDER BY a.ProductName;";
	
		$query = "SELECT CONCAT(a.ProductName,' ',b.CategoryName,' ',c.GenericName,' (Code:',a.ProductBarcode,', Cost:',a.NewCost,', Stock:',IFNULL(d.Quantity,0),')-',a.ProductId) `name`
				FROM t_product a
				INNER JOIN t_productcategory b ON a.ProductCategoryId=b.ProductCategoryId
				INNER JOIN t_productgeneric c ON a.ProductGenericId=c.ProductGenericId
				LEFT JOIN (SELECT s.`ProductId`,SUM(s.`Quantity`) Quantity FROM `t_productstock` s WHERE s.`ClientId`=$ClientId AND s.`BranchId`=$BranchId GROUP BY s.`ProductId`) d ON a.ProductId=d.ProductId
				WHERE a.ClientId = $ClientId
				ORDER BY a.ProductName;";

		$resultdata = $dbh->query($query);
		$dataList = array();

		foreach ($resultdata as $row) {
			$dataList[] = $row["name"];
		}

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $dataList
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function ReferenceList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$dbh = new Db();

		$query = "SELECT `ReferenceId` id,`ReferenceName` `name`
	 			 	FROM `t_reference` 
					where ClientId=$ClientId
					ORDER BY ReferenceName;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function ExpenseTypeList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$dbh = new Db();

		$query = "SELECT `ExpenseTypeId` id,`ExpenseType` `name`
	 			 	FROM `t_expense_type` 
					where ClientId=$ClientId
					ORDER BY ExpenseType;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function PaymentModeList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$dbh = new Db();

		$query = "SELECT `PaymentModeId` id,`PaymentMode` `name`
	 			 	FROM `t_paymentmode` 
					where ClientId=$ClientId
					ORDER BY PaymentMode;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}



function AdjTypeList($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);

		$dbh = new Db();

		$query = "SELECT `AdjTypeId` id,`AdjType` `name`
	 			 	FROM `t_adj_type` 
					where ClientId=$ClientId
					ORDER BY AdjType;";

		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function NextProductSystemCode($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$NSBarCode = getProductSystemBarcode($ClientId);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $NSBarCode
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}

function NextInvoiceNumber($data)
{
	try {

		$ClientId = trim($data->ClientId);
		$BranchId = trim($data->BranchId);
		$TransactionTypeId = trim($data->TransactionTypeId);
		$InvNo = getNextInvoiceNumber($ClientId, $BranchId, $TransactionTypeId);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $InvNo
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


function AttendancePersonList($data)
{
	try {

		$ClientId = trim($data->ClientId);

		$dbh = new Db();
		$query = "SELECT distinct PersonnelID id, FirstName `name` FROM t_attendance
		where ClientId = $ClientId
		ORDER BY name;";
		$resultdata = $dbh->query($query);

		$returnData = [
			"success" => 1,
			"status" => 200,
			"message" => "",
			"datalist" => $resultdata
		];
	} catch (PDOException $e) {
		$returnData = msg(0, 500, $e->getMessage());
	}

	return $returnData;
}


