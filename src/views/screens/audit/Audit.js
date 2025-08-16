import React, { forwardRef, useRef, useContext, useEffect } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit, ViewList } from "@material-ui/icons";

import { Button } from "../../../components/CustomControl/Button";
import moment from "moment";

import { Typography, TextField } from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { Virtualize } from "../../../components/CustomControl/Virtualize";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

// import { useReactToPrint } from "react-to-print";
// import { SalesToPrint } from "./SalesToPrint";

const Audit = (props) => {
  const serverpage = "audit"; // this is .php server page
  const permissionType = props.permissionType;

  const componentRef = useRef();

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [listEditPanelToggle, setListEditPanelToggle] = useState(true); //when true then show list, when false then show add/edit
  // const [listEditPanelToggle, setListEditPanelToggle] = useState(false); //when true then show list, when false then show add/edit
  const [userList, setUserList] = useState(null);
  // const [referenceList, setReferenceList] = useState(null);
  const [expenseTypeList, setExpenseTypeList] = useState(null);
  // const [customerList, setCustomerList] = useState(null);
  // const [paymentModeList, setPaymentModeList] = useState(null);
  // const [productList, setProductList] = useState(null);
  // const [productVirtualList, setProductVirtualList] = useState(null);
  const [currentInvoice, setCurrentInvoice] = useState([]); //this is for master information. It will send to sever for save
  const [currentMany, setCurrentMany] = useState([]); //this is for many one record add/edit
  const [manyDataList, setManyDataList] = useState([]); //This is for many table. It will send to sever for save
  const [deletedItems, setDeletedItems] = useState([]); //Which products delete from many
  const [StartDate, setStartDate] = useState(
    moment().subtract(30, "days").format("YYYY-MM-DD")
  );
  const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));

  const [currentDate, setCurrentDate] = useState(
    moment().format("YYYY-MM-DD HH:mm:ss")
  );

  const [errorObjectMaster, setErrorObjectMaster] = useState({});
  const [errorObjectMany, setErrorObjectMany] = useState({});

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data master list
  // const {
  //   isLoading: holdIsLoading,
  //   data: holdDataList,
  //   error: holdError,
  //   ExecuteQuery: HoldExecuteQuery,
  // } = ExecuteQueryHook(); //Fetch data master list

  const {
    isLoading: isLoadingSingle,
    data: dataListSingle,
    error: errorSingle,
    ExecuteQuery: ExecuteQuerySingle,
  } = ExecuteQueryHook(); //Fetch data for single

  const UserInfo = LoginUserInfo();
  console.log("UserInfo: ", UserInfo);

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const PrintPDFExcelExportFunction = (reportType) => {
    let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";
    window.open(
      finalUrl +
        "?action=SalesExport" +
        "&reportType=excel" +
        "&ClientId=" +
        UserInfo.ClientId +
        "&BranchId=" +
        UserInfo.BranchId +
        "&StartDate=" +
        StartDate +
        "&EndDate=" +
        EndDate +
        "&TimeStamp=" +
        Date.now()
    );
  };
  /* =====End of Excel Export Code==== */

  const newInvoice = () => {
    setManyDataList([]);
    setDeletedItems([]);

    let params = {
      action: "NextInvoiceNumber",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      TransactionTypeId: 25,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      console.log("UserInfo.UserId: ", UserInfo.UserId);

      setCurrentInvoice({
        id: "",
        TransactionTypeId: 25,
        TransactionDate: currentDate,
        InvoiceNo: res.data.datalist,
        UserId: +UserInfo.UserId,
        BPosted: 0,
        EntryByUserId: "",
        ApproveByUserId: "",
      });
    });

    resetMany();
  };

  function resetMany() {
    setCurrentMany({
      autoId: -1,
      TransactionItemsId: "",
      TransactionId: "",
      ProductId: "",
      CurrentQuantity: "",
      Quantity: "",
    });
  }

  /**Get data for table list */
  function getDataList() {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      TransactionTypeId: 25,
      StartDate: StartDate,
      EndDate: EndDate,
    };
    // console.log('LoginUserInfo params: ', params);

    ExecuteQuery(serverpage, params);
  }

  /**Get data for table list */
  // function getHoldDataList() {
  //   let params = {
  //     action: "getHoldDataList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //     TransactionTypeId: 25,
  //   };
  //   // console.log('LoginUserInfo params: ', params);

  //   HoldExecuteQuery(serverpage, params);
  // }

  if (bFirst) {
    /**First time call for datalist */
    newInvoice();

    getUserList();
    getExpenseTypeList();
    // getReferenceList();
    // getCustomerList();
    // getPaymentModeList();

    // getProductList();
    // getProductVirtualList();

    getDataList(); //invoice list

    setBFirst(false);
  }

  function addData() {
    newInvoice();
    // getHoldDataList();

    setListEditPanelToggle(false); // false = hide list and show add/edit panel
  }

  function getUserList() {
    let params = {
      action: "UserList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setUserList(
        [{ id: "", name: "Select expense by" }].concat(res.data.datalist)
      );
    });
  }

  function getExpenseTypeList() {
    let params = {
      action: "ExpenseTypeList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setExpenseTypeList(
        [{ id: "", name: "Select expense type" }].concat(res.data.datalist)
      );
    });
  }

  // function getReferenceList() {
  //   let params = {
  //     action: "ReferenceList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //   };

  //   apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
  //     setReferenceList(
  //       [{ id: "", name: "Select reference" }].concat(res.data.datalist)
  //     );
  //   });
  // }

  // function getCustomerList() {
  //   let params = {
  //     action: "CustomerList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //   };

  //   apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
  //     setCustomerList(
  //       [{ id: "", name: "Select customer" }].concat(res.data.datalist)
  //     );
  //   });
  // }

  // function getPaymentModeList() {
  //   let params = {
  //     action: "PaymentModeList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //   };

  //   apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
  //     setPaymentModeList(
  //       [{ id: "", name: "Select" }].concat(res.data.datalist)
  //     );
  //   });
  // }

  // function getProductList() {
  //   let params = {
  //     action: "ProductList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //   };

  //   apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
  //     setProductList(
  //       [{ id: "", name: "Select product" }].concat(res.data.datalist)
  //     );
  //   });
  // }

  // function getProductVirtualList() {
  //   let params = {
  //     action: "ProductVirtualList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //   };

  //   apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
  //     setProductVirtualList(["Select product"].concat(res.data.datalist));
  //   });
  // }

  const masterColumnList = [
    { field: "rownumber", label: "SL", align: "center", width: "5%" },
    // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },
    {
      field: "TransactionDate",
      label: "Date",
      width: "12%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "InvoiceNo",
      label: "Voucher No.",
      width: "20%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "EntryByUserName",
      label: "Entry By",
      align: "left",
      // width: "30%",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "ApproveByUserName",
      label: "Approve By",
      align: "left",
      // width: "30%",
      visible: true,
      sort: true,
      filter: true,
    }, 
    {
      field: "StatusName",
      label: "Status",
      width: "8%",
      align: "center",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "custom",
      label: "Action",
      width: "5%",
      align: "center",
      visible: true,
      // sort: false,
      // filter: false,
    },
  ];

  /** Action from table row buttons*/
  function actioncontrolmaster(rowData) {
    return (
      <>
        {rowData.BPosted === 0 && permissionType === 0 && (
          <Edit
            className={"table-edit-icon"}
            onClick={() => {
              editData(rowData);
            }}
          />
        )}

        {rowData.BPosted === 0 && permissionType === 0 && (
          <DeleteOutline
            className={"table-delete-icon"}
            onClick={() => {
              deleteData(rowData);
            }}
          />
        )}

        {(rowData.BPosted === 1 || permissionType === 1) && (
          <ViewList
            className={"table-view-icon"}
            onClick={() => {
              viewData(rowData);
            }}
          />
        )}
      </>
    );
  }

  const editData = (rowData) => {
    getDataSingleFromServer(rowData.id);
  };

  const viewData = (rowData) => {
    getDataSingleFromServer(rowData.id);
  };

  const getDataSingleFromServer = (id) => {
    let params = {
      action: "getDataSingle",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      id: id,
    };

    setDeletedItems([]);

    ExecuteQuerySingle(serverpage, params);
    // getHoldDataList();
    resetMany();

    setListEditPanelToggle(false); // false = hide list and show add/edit panel
  };

  // const holdInvColumnList = [
  //   // { field: "rownumber", label: "SL", align: "center", width: "5%" },
  //   // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },

  //   {
  //     field: "InvoiceNo",
  //     label: "Inv",
  //     width: "18%",
  //     align: "center",
  //     visible: true,
  //     sort: false,
  //     filter: false,
  //   },
  //   {
  //     field: "CustomerName",
  //     label: "Customer",
  //     align: "left",
  //     // width: "30%",
  //     visible: true,
  //     sort: false,
  //     filter: false,
  //   },
  //   {
  //     field: "NetPaymentAmount",
  //     label: "Net Pay",
  //     align: "right",
  //     type: "number",
  //     width: "20%",
  //     visible: true,
  //     sort: false,
  //     filter: false,
  //   },
  //   {
  //     field: "custom",
  //     label: " ",
  //     width: "5%",
  //     align: "center",
  //     visible: true,
  //     // sort: false,
  //     // filter: false,
  //   },
  // ];

  // /** Action from table row buttons*/
  // function actioncontrolhold(rowData) {
  //   return (
  //     <>
  //       {permissionType === 0 && (
  //         <Edit
  //           className={"table-edit-icon"}
  //           onClick={() => {
  //             editData(rowData);
  //           }}
  //         />
  //       )}
  //     </>
  //   );
  // }

  useEffect(() => {
    console.log("dataListSingle: ", dataListSingle);

    if (dataListSingle.master) {
      // console.log("dataListSingle: ", dataListSingle.master[0]);
      if (permissionType === 1) {
        dataListSingle.master[0].BPosted = 1;
      }

      setCurrentInvoice(dataListSingle.master[0]);
    }
    if (dataListSingle.items) {
      setManyDataList(dataListSingle.items);
      // console.log('dataListSingle: ', dataListSingle.items[0]);
    }
  }, [dataListSingle]);

  const backToList = () => {
    setListEditPanelToggle(true); // true = show list and hide add/edit panel
    getDataList(); //invoice list
  };

  // const handlePrint = useReactToPrint({
  //   content: () => componentRef.current,
  // });

  const printInvoice = () => {
    // handlePrint();
  };

  const SalesReturnInv = () => {
    // console.log("Not done yet.");
    props.history.push("salesreturn");
  };

  const deleteData = (rowData) => {
    swal({
      title: "Are you sure?",
      text: "Once deleted, you will not be able to recover this data!",
      icon: "warning",
      buttons: {
        confirm: {
          text: "Yes",
          value: true,
          visible: true,
          className: "",
          closeModal: true,
        },
        cancel: {
          text: "No",
          value: null,
          visible: true,
          className: "",
          closeModal: true,
        },
      },
      dangerMode: true,
    }).then((allowAction) => {
      if (allowAction) {
        deleteApi(rowData);
      }
    });
  };

  function deleteApi(rowData) {
    let params = {
      action: "deleteData",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      rowData: rowData,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      // console.log('res: ', res);
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });
      getDataList();
    });
  }

  function handleChangeCheck(e) {
    // console.log('e.target.checked: ', e.target.checked);
    const { name, value } = e.target;

    let data = { ...currentInvoice };
    data[name] = e.target.checked;
    setCurrentInvoice(data);
    console.log("aaa data: ", data);
  }

  const handleChangeMaster = (e) => {
    const { name, value } = e.target;
    // console.log("value: ", value);
    // console.log("name: ", name);
    let data = { ...currentInvoice };
    data[name] = value;

    // if (name === "SumCommission" || name === "SumCommissionPercentage") {
    //   let SumTotalAmount = parseFloat(
    //     ConvertNullToZero(data["SumTotalAmount"])
    //   );

    //   if (name === "SumCommission") {
    //     /**when type commistion amount then set BLANK of % */
    //     data["SumCommissionPercentage"] = "";
    //   } else {
    //     /**when type commision % */
    //     let SumCommissionPercentage = parseFloat(
    //       ConvertNullToZero(data["SumCommissionPercentage"])
    //     );

    //     data["SumCommission"] = (
    //       (SumTotalAmount * SumCommissionPercentage) /
    //       100
    //     ).toFixed(2);
    //   }

    //   let SumCommission = parseFloat(ConvertNullToZero(data["SumCommission"]));
    //   let NetPaymentAmount = (SumTotalAmount - SumCommission).toFixed(2);

    //   data["NetPaymentAmount"] = NetPaymentAmount;
    // } else if (name === "PaidAmount") {
    // }

    // let NetPaymentAmount = parseFloat(
    //   ConvertNullToZero(data["NetPaymentAmount"])
    // );
    // let PaidAmount = parseFloat(ConvertNullToZero(data["PaidAmount"]));
    // let ReturnAmount =
    //   PaidAmount == 0 ? 0 : (PaidAmount - NetPaymentAmount).toFixed(2);

    // data["ReturnAmount"] = ReturnAmount;
    // console.log("data: ", data);

    setCurrentInvoice(data);

    setErrorObjectMaster({ ...errorObjectMaster, [name]: null });
  };

  const handleChangeChoosenMaster = (name, value) => {
    let data = { ...currentInvoice };
    data[name] = value;
    setCurrentInvoice(data);

    setErrorObjectMaster({ ...errorObjectMaster, [name]: null });
  };

  const handleChangeMany = (e) => {
    const { name, value } = e.target;
    let data = { ...currentMany };
    data[name] = value;

    // if (
    //   name === "Quantity" ||
    //   name === "MRP" ||
    //   name === "VatonTrade" ||
    //   name === "DiscountPercentage" ||
    //   name === "DiscountAmountPerUnit"
    // ) {
    //   setCurrentMany(calculationManyFields(name, data));
    // } else {
      setCurrentMany(data);
    // }

    setErrorObjectMany({ ...errorObjectMany, [name]: null });
  };

  const handleChangeChoosenMany = (name, value) => {
    let data = { ...currentMany };
    data[name] = value;

    /**when select product then some value take from product metadata */
    // if (name === "ProductId") {
    //   var selectedProduct = productList.filter((prod) => prod.id === value);
 
    //   console.log("selectedProduct: ", selectedProduct);

    //   data["ProductName"] = selectedProduct[0].name;
    //   data["MRP"] = selectedProduct[0].MRP ? selectedProduct[0].MRP : 0;

    //   data["DiscountPercentage"] = selectedProduct[0].SalesDiscountPercentage
    //     ? selectedProduct[0].SalesDiscountPercentage
    //     : "";

    //   data["DiscountAmountPerUnit"] = selectedProduct[0].SalesDiscountAmount
    //     ? selectedProduct[0].SalesDiscountAmount
    //     : "";

    //   data["VatonTrade"] = selectedProduct[0].VatonSales
    //     ? selectedProduct[0].VatonSales
    //     : "";

    //   let defaultQty = 1;
    //   data["Quantity"] = defaultQty;
    //   data["NewCost"] = selectedProduct[0].NewCost
    //     ? selectedProduct[0].NewCost
    //     : 0;
 

    //   setCurrentMany(calculationManyFields(name, data));
    // } else {
      setCurrentMany(data);
    // }

    // console.log("data handleChangeChoosenMany: ", data);

    setErrorObjectMany({ ...errorObjectMany, [name]: null });
  };

  // const calculationManyFields = (name, data) => {
  //   // console.log("data: ", data);

  //   /**when type subtotal then no need calculate subtotal */
  //   if (name !== "SubTotalAmount") {
  //     data["SubTotalAmount"] = parseFloat(
  //       parseFloat(ConvertNullToZero(data["MRP"])) *
  //         parseInt(ConvertNullToZero(data["Quantity"]))
  //     ).toFixed(2);
  //   }

  //   /**when type discount amount per unit then BLANK discount %. And cal total discount amount*/
  //   if (name === "DiscountAmountPerUnit") {
  //     data["DiscountPercentage"] = "";
  //   } else if (name === "DiscountPercentage") {
  //     /**when type discount % per unit then BLANK discount amount. And cal total discount amount*/
  //     data["DiscountAmountPerUnit"] = "";
  //   }

  //   /**Caculate total discount amount */
  //   if (
  //     data["DiscountAmountPerUnit"] !== "" &&
  //     data["DiscountAmountPerUnit"] !== null
  //   ) {
  //     data["DiscountAmount"] = parseFloat(
  //       parseFloat(ConvertNullToZero(data["DiscountAmountPerUnit"])) *
  //         parseFloat(ConvertNullToZero(data["Quantity"]))
  //     ).toFixed(2);
  //     // console.log('datasssssssssssss DiscountAmountPerUnit: ', data);
  //   } else if (
  //     data["DiscountPercentage"] !== "" &&
  //     data["DiscountPercentage"] !== null
  //   ) {
  //     data["DiscountAmount"] = parseFloat(
  //       (parseFloat(ConvertNullToZero(data["SubTotalAmount"])) *
  //         parseFloat(ConvertNullToZero(data["DiscountPercentage"]))) /
  //         100
  //     ).toFixed(2);
  //     // console.log('datasssssssssssss DiscountPercentage: ', data);
  //   } else {
  //     data["DiscountAmount"] = 0;
  //   }
  //   // console.log('datasssssssssssss: ', data);

  //   /**VatonTrade=vat on sales */
  //   // data["VatAmount"] = parseFloat(
  //   //   ((parseFloat(ConvertNullToZero(data["SubTotalAmount"])) -
  //   //     parseFloat(ConvertNullToZero(data["DiscountAmount"]))) *
  //   //     parseFloat(ConvertNullToZero(data["VatonTrade"]))) /
  //   //     100
  //   // ).toFixed(2);
  //   data["VatAmount"] = parseFloat(
  //     (parseFloat(ConvertNullToZero(data["SubTotalAmount"])) *
  //       parseFloat(ConvertNullToZero(data["VatonTrade"]))) /
  //       100
  //   ).toFixed(2);

  //   /**when type TotalAmount then no need calculate TotalAmount */
  //   // if (name !== "TotalAmount") {
  //   data["TotalAmount"] = parseFloat(
  //     parseFloat(ConvertNullToZero(data["SubTotalAmount"])) +
  //       parseFloat(ConvertNullToZero(data["VatAmount"])) -
  //       parseFloat(ConvertNullToZero(data["DiscountAmount"]))
  //   ).toFixed(2);
  //   // }

  //   /**when type NewCost then no need calculate NewCost */
  //   // if (name !== "NewCost") {
  //   //   data["NewCost"] = parseFloat(
  //   //     (data["TotalAmount"] === "" ? 0 : parseFloat(data["TotalAmount"])) /
  //   //       ((data["Quantity"] === "" ? 0 : parseFloat(data["Quantity"])) +
  //   //         (data["BonusQty"] === "" ? 0 : parseFloat(data["BonusQty"])))
  //   //   ).toFixed(2);
  //   // }

  //   // console.log("data calculationManyFields: ", data);

  //   return data;
  // };

  const calculationMasterFields = (list) => {
    console.log("list: ", list);
    let TotalAmount = 0;

    list.forEach((row, i) => {
      console.log("row: ", row);
      TotalAmount += parseFloat(ConvertNullToZero(row.Amount));
    });

    let data = { ...currentInvoice };
    data["TotalAmount"] = TotalAmount.toFixed(2);    
    
    setCurrentInvoice(data);
  };

  const ConvertNullToZero = (val) => {
    let retValue = val === "" || val === null ? 0 : val;
    return retValue;
  };

  const addEditManyItem = () => {
    if (validateFormMany()) {
      console.log("currentMany: ", currentMany);
      // console.log("data.TransactionItemsId: ", currentMany.TransactionItemsId);
      console.log("manyDataList: ", manyDataList);

      // let isExist = 0;
      // manyDataList.forEach((row, i) => {
      //   if (currentMany.ProductId === row.ProductId && isExist === 0) {
      //     isExist = 1;
      //   }
      // });

      // if (isExist === 1) {
      //   props.openNoticeModal({
      //     isOpen: true,
      //     msg: "Already added this product",
      //     msgtype: 0,
      //   });

      //   return;
      // }

      let rows = [];

      // var selectedProduct = productList.filter(
      //   (prod) => prod.id === currentMany.ProductId
      // );
      // console.log('selectedProduct: ', selectedProduct);
      // console.log("oldManyDataList: ", manyDataList);

      let isExisting = 0;
      manyDataList.forEach((row, i) => {
        let newRow = {};

        newRow.autoId = row.autoId; //Just unique id for delete/update
        newRow.TransactionItemsId = row.TransactionItemsId;
        newRow.TransactionId = row.TransactionId;
        // newRow.ProductId = row.ProductId;
        // newRow.ProductName = row.ProductName;
        // newRow.NewCost = row.NewCost;
        // console.log('currentMany.autoId: ', currentMany.autoId);

      //   if (
      //     currentMany.ProductId === row.ProductId &&
      //     currentMany.autoId === -1
      //   ) {
      //     /**When This item already exist and Add again*/
      //     isExisting = 1;
      //     console.log("From 1");

      //     newRow.Quantity =
      //       parseInt(row.Quantity) + parseInt(currentMany.Quantity);
      //     newRow.MRP = row.MRP;

      //     newRow.VatonTrade = row.VatonTrade;
      //     newRow.DiscountPercentage = row.DiscountPercentage;
      //     newRow.DiscountAmountPerUnit = row.DiscountAmountPerUnit;

      //     newRow.SubTotalAmount = 0;
      //     newRow.DiscountAmount = 0;

      //     newRow.VatAmount = 0;
      //     newRow.TotalAmount = 0;

      //     newRow.Commission = row.Commission;
      //     newRow.NetPaymentAmount = 0;

      //     console.log("newRow 111111111111: ", newRow);

      //  //   let retRow = calculationManyFields("Quantity", newRow); //send newRow ref and update in this array
      //   //  console.log("retRow1111111111: ", retRow);
      //   } else 
      
      if (
          currentMany.autoId == row.autoId &&
          currentMany.autoId > 0
        ) {
          /**When EDIT the many item*/
          isExisting = 1;
          console.log("From 2");

          newRow.ExpenseDate = currentMany.ExpenseDate;
          newRow.ExpenseTypeId = currentMany.ExpenseTypeId; //selectedProduct[0].name;
  
          let oneObjE = expenseTypeList.filter(function (obj) {
            return obj.id == currentMany.ExpenseTypeId;
          });
          newRow.ExpenseType = oneObjE[0].name;
  
          newRow.Amount = currentMany.Amount;
          newRow.ExpenseUserId = currentMany.ExpenseUserId;
  
          
          let oneObjU = userList.filter(function (obj) {
            return obj.id == currentMany.ExpenseUserId;
          });
          newRow.ExpenseByName = oneObjU[0].name;
  
          // newRow.ExpenseByName = currentMany.ExpenseUserId;
  
          newRow.PayTo = currentMany.PayTo;
          newRow.Comments = currentMany.Comments;
        } else {
          console.log("From 3");

          newRow.ExpenseDate = row.ExpenseDate;
          newRow.ExpenseTypeId = row.ExpenseTypeId;
          newRow.ExpenseType = row.ExpenseType;
          newRow.Amount = row.Amount;
          newRow.ExpenseUserId = row.ExpenseUserId;
          newRow.ExpenseByName = row.ExpenseByName;
          newRow.PayTo = row.PayTo;
          newRow.Comments = row.Comments;
        }

        rows.push(newRow);
      });

      // console.log("currentMany: ", currentMany);

      if (isExisting === 0) {
        console.log("From 4");
        /**when This item not exist in many */
        let newRow = {};
        newRow.autoId = currentMany.ExpenseTypeId + moment().milliseconds(); //Just unique id for delete/update
        newRow.TransactionItemsId = currentMany.TransactionItemsId;
        newRow.TransactionId = currentMany.TransactionId;
        newRow.ExpenseDate = currentMany.ExpenseDate;
        newRow.ExpenseTypeId = currentMany.ExpenseTypeId; //selectedProduct[0].name;

        let oneObjE = expenseTypeList.filter(function (obj) {
          return obj.id == currentMany.ExpenseTypeId;
        });
        newRow.ExpenseType = oneObjE[0].name;

        newRow.Amount = currentMany.Amount;
        newRow.ExpenseUserId = currentMany.ExpenseUserId;

        
        let oneObjU = userList.filter(function (obj) {
          return obj.id == currentMany.ExpenseUserId;
        });
        newRow.ExpenseByName = oneObjU[0].name;

        // newRow.ExpenseByName = currentMany.ExpenseUserId;

        newRow.PayTo = currentMany.PayTo;
        newRow.Comments = currentMany.Comments;
        rows.push(newRow);
      }
      console.log("rows: ", rows);

      setManyDataList(rows);
      // console.log("manyDataList: ", rows);
      calculationMasterFields(rows);

      resetMany();
    } else {
      props.openNoticeModal({
        isOpen: true,
        msg: "Please enter required fields.",
        msgtype: 0,
      });
    }
  };

  const validateFormMaster = () => {
    let validateFields = ["TransactionDate",  "InvoiceNo","EntryByUserId","ApproveByUserId"];
    let errorData = {};
    let isValid = true;
    validateFields.map((field) => {
      if (!currentInvoice[field]) {
        errorData[field] = "validation-style";
        isValid = false;
      }
    });
    setErrorObjectMaster(errorData);
    return isValid;
  };

  const validateFormMany = () => {
    let validateFields = ["Quantity"];
    let errorData = {};
    let isValid = true;
    validateFields.map((field) => {
      if (!currentMany[field]) {
        errorData[field] = "validation-style";
        isValid = false;
      }
    });
    setErrorObjectMany(errorData);
    return isValid;
  };

  const manyColumnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },
    {
      field: "ProductName",
      label: "Product",
      width: "55%",
      align: "left",
      visible: true,
      // sort: true,
      // filter: false,
    },
    {
      field: "CurrentQuantity",
      label: "Current Stock Qty",
      width: "15%",
      align: "right",
      visible: true,
      // sort: true,
      // filter: true,
    },
    // {
    //   field: "Quantity",
    //   label: "Physical Stock Qty",
    //   width: "15%",
    //   align: "right",
    //   // type: "text",
    //   visible: true,
    //   // sort: true,
    //   // filter: true,
    // },
    {
      field: "custom",
      label: "Physical Stock Qty",
      width: "15%",
      align: "center",
      visible: true,
      // sort: false,
      // filter: false,
    },
  ];

  /** Action from table row buttons*/
  function actioncontrol(rowData) {
    return (
      <>

          <input
          type="number"
          id="Quantity"
          name="Quantity"
          class="wd-40p"
          disabled={currentInvoice.BPosted}
          value={rowData.Quantity}
          onChange={(e) => handleChangeTable(e, rowData)}
        />

{/* 
// <Edit
          //   className={"table-edit-icon"}
          //   onClick={() => {
          //     editDataMany(rowData);
          //   }}
          // /> */}
        
 
        {/* {!currentInvoice.BPosted && (
          // <DeleteOutline
          //   className={"table-delete-icon"}
          //   onClick={() => {
          //     deleteDataMany(rowData);
          //   }}
          // />
        )} */}
      </>
    );
  }

  
  const handleChangeTable = (e, rowData) => {
    console.log('rowData 111: ', rowData);
    const { name, value } = e.target;
    console.log('rowData name: ', name);
    console.log('rowData value: ', value);

    let rows = [];
    let isExisting = 0;

    manyDataList.forEach((row, i) => {
      let newRow = {};

      newRow.autoId = row.autoId; //Just unique id for delete/update
      newRow.TransactionItemsId = row.TransactionItemsId;
      newRow.TransactionId = row.TransactionId;
      newRow.ProductId = row.ProductId;
      newRow.ProductName = row.ProductName;
      newRow.CurrentQuantity = row.CurrentQuantity;
      if(row.TransactionItemsId == rowData.TransactionItemsId){
        newRow.Quantity = value;
      }else{
        newRow.Quantity = row.Quantity;
      }
      rows.push(newRow);
    });

    setManyDataList(rows);
    
  };




  const editDataMany = (rowData) => {
    console.log("rowData: ", rowData);
    setCurrentMany(rowData);
  };

  const deleteDataMany = (rowData) => {
    swal({
      title: "Are you sure?",
      text: "Once deleted, you will not be able to recover this data!",
      icon: "warning",
      buttons: {
        confirm: {
          text: "Yes",
          value: true,
          visible: true,
          className: "",
          closeModal: true,
        },
        cancel: {
          text: "No",
          value: null,
          visible: true,
          className: "",
          closeModal: true,
        },
      },
      dangerMode: true,
    }).then((allowAction) => {
      if (allowAction) {
        deleteInvoiceItem(rowData);
      }
    });
  };

  function deleteInvoiceItem(rowData) {
    // console.log("manyDataList: ", manyDataList);
    // console.log("rowData for delete: ", rowData);

    let data = manyDataList.filter(function (obj) {
      return obj.autoId !== rowData.autoId;
    });

    setManyDataList(data);
    calculationMasterFields(data);

    let delItems = [...deletedItems];
    delItems.push(rowData);
    console.log("delItems: ", delItems);

    setDeletedItems(delItems);
  }

  /**Print & Submit */
  const postInvoice = () => {
    swal({
      title: "Are you sure?",
      text: "Do you really want to post the stock?",
      icon: "warning",
      buttons: {
        confirm: {
          text: "Yes",
          value: true,
          visible: true,
          className: "",
          closeModal: true,
        },
        cancel: {
          text: "No",
          value: null,
          visible: true,
          className: "",
          closeModal: true,
        },
      },
      dangerMode: true,
    }).then((allowAction) => {
      if (allowAction) {

    let cInvoiceMaster = { ...currentInvoice };
    cInvoiceMaster["BPosted"] = 1;

    let params = {
      action: "dataAddEdit",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      InvoiceMaster: cInvoiceMaster,
      InvoiceItems: manyDataList,
      DeletedItems: deletedItems,
    };

    addEditAPICall(params);

      }else{

      }
    });
  };

  /**Hold Inv */
  function saveData(p) {
    let params = {
      action: "dataAddEdit",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      InvoiceMaster: currentInvoice,
      InvoiceItems: manyDataList,
      DeletedItems: deletedItems,
    };

    addEditAPICall(params);
  }

  function addEditAPICall(params) {

    // if (manyDataList.length === 0) {
    //   props.openNoticeModal({
    //     isOpen: true,
    //     msg: "Please enter expense details.",
    //     msgtype: 0,
    //   });
    //   return;
    // }

    if (validateFormMaster()) {
      // if (
      //   params.InvoiceMaster.CustomerId === "" &&
      //   params.InvoiceMaster.BPosted === 0
      // ) {
      //   /**Hold invoice can not save without customer. Aleast need to select 'Other' */
      //   props.openNoticeModal({
      //     isOpen: true,
      //     msg: "Please select customer for hold invoice.",
      //     msgtype: 0,
      //   });
      //   return;
      // }

      // if (
      //   (params.InvoiceMaster.PaidAmount === "" ||
      //     params.InvoiceMaster.PaidAmount === 0) &&
      //   params.InvoiceMaster.BPosted === 1
      // ) {
      //   /**Hold invoice can not save without customer. Aleast need to select 'Other' */
      //   props.openNoticeModal({
      //     isOpen: true,
      //     msg: "Please enter Paid Amount.",
      //     msgtype: 0,
      //   });
      //   return;
      // }


      console.log("params: ", params);

      apiCall.post(serverpage, { params }, apiOption()).then((res) => {
        console.log("res: ", res);

        if (res.data.success === 1) {


          // if (
          //   params.InvoiceMaster.BPosted == 1 &&
          //   !params.InvoiceMaster.IsPrintInvoice
          // ) {
          //   printInvoice();
          // }



          // console.log('currentInvoice: ', currentInvoice);
          // if(currentInvoice.id === ""){
          //   //New
          //   getDataSingleFromServer(res.data.id);
          // }else{
          //   //Edit
          //   getDataSingleFromServer(currentInvoice.id);
          // }

          // newInvoice();
          // console.log("rubel params: ", params.InvoiceMaster.BPosted);

          /**When draft invoice save/post then need to reload holdinoice list */
          // if (params.InvoiceMaster.BPosted === 0) {
          // getHoldDataList();
          // }
        // } else if (res.data.id > 0) {
          // console.log("rubel params else: ", params.InvoiceMaster.BPosted);

          /**when save but can not post then need to refresh */
          if (currentInvoice.id === "") {
            //New
            getDataSingleFromServer(res.data.id);
          } else {
            //Edit
            getDataSingleFromServer(currentInvoice.id);
          }
        }

        props.openNoticeModal({
          isOpen: true,
          msg: res.data.message,
          msgtype: res.data.success,
        });

        /**after save refresh the master list */
        // getDataList(); //invoice list
        // getDataSingleFromServer(res.data.id);

        // console.log('props modal: ', props);
        // if (res.data.success === 1) {
        //   props.modalCallback("addedit");
        // }
      });
    } else {
      props.openNoticeModal({
        isOpen: true,
        msg: "Please enter required fields.",
        msgtype: 0,
      });
    }
  }

  const handleChangeFilterDate = (e) => {
    const { name, value } = e.target;
    console.log("value: ", value);
    console.log("name: ", name);
    if (name === "StartDate") {
      setStartDate(value);
    }

    if (name === "EndDate") {
      setEndDate(value);
    }
    // let data = { ...currentInvoice };
    // data[name] = value;
  };

  useEffect(() => {
    console.log("useEffect call");
    getDataList();
  }, [StartDate, EndDate]);

  return (
    <>
      <div class="bodyContainer non-printable">
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Product ❯ Audit
          </h4>
        </div>

        {listEditPanelToggle && (
          <>
            {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
            <div class="searchAdd">
              {/* <input type="text" placeholder="Search Product Group"/> */}
              <label class="pl-10">Start Date</label>
              <div>
                <input
                  type="date"
                  id="StartDate"
                  name="StartDate"
                  value={StartDate}
                  onChange={(e) => handleChangeFilterDate(e)}
                />
              </div>

              <label class="pl-10">End Date</label>
              <div>
                <input
                  type="date"
                  id="EndDate"
                  name="EndDate"
                  value={EndDate}
                  onChange={(e) => handleChangeFilterDate(e)}
                />
              </div>

              {/* <Button label={"ADD"} class={"btnAdd"} onClick={addData} /> */}

              {/* <Button
                label={"Export"}
                class={"btnPrint"}
                onClick={PrintPDFExcelExportFunction}
              /> */}
              <Button
                disabled={permissionType}
                label={"ADD"}
                class={"btnAdd"}
                onClick={addData}
              />
            </div>

            {/* <!-- ####---Master invoice list---####s --> */}
            {/* <div class="subContainer">
              <div className="App tableHeight"> */}
                <CustomTable
                  columns={masterColumnList}
                  rows={dataList ? dataList : {}}
                  actioncontrol={actioncontrolmaster}
                />
              {/* </div>
            </div> */}
          </>
        )}

        {!listEditPanelToggle && (
          <>
            {/* <!-- Expense Header --> */}
            <div class="subContainer salesMasterHeader">


              <label for="Date"> Audit Date</label>
              <input
                type="date"
                id="TransactionDate"
                name="TransactionDate"
                disabled={currentInvoice.BPosted}
                class={errorObjectMaster.TransactionDate}
                value={currentInvoice.TransactionDate}
                onChange={(e) => handleChangeMaster(e)}
              />
 


            <label for="">Entry By*</label>
            <Autocomplete
                  autoHighlight
                  disabled={currentInvoice.BPosted}
                  className="chosen_dropdown"
                  id="EntryByUserId"
                  name="EntryByUserId"
                  class={errorObjectMaster.EntryByUserId}
                  autoComplete
                  options={userList ? userList : []}
                  getOptionLabel={(option) => option.name}
                  value={
                    userList
                      ? userList[
                        userList.findIndex(
                        (list) => list.id === currentInvoice.EntryByUserId
                      )
                      ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeChoosenMaster(
                      "EntryByUserId",
                      valueobj ? valueobj.id : ""
                    )
                  }
                  renderOption={(option) => (
                    <Typography className="chosen_dropdown_font">
                      {option.name}
                    </Typography>
                  )}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />




<label for="">Approve By*</label>
            <Autocomplete
                  autoHighlight
                  disabled={currentInvoice.BPosted}
                  className="chosen_dropdown"
                  id="ApproveByUserId"
                  name="ApproveByUserId"
                  class={errorObjectMaster.ApproveByUserId}
                  autoComplete
                  options={userList ? userList : []}
                  getOptionLabel={(option) => option.name}
                  value={
                    userList
                      ? userList[
                        userList.findIndex(
                        (list) => list.id === currentInvoice.ApproveByUserId
                      )
                      ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeChoosenMaster(
                      "ApproveByUserId",
                      valueobj ? valueobj.id : ""
                    )
                  }
                  renderOption={(option) => (
                    <Typography className="chosen_dropdown_font">
                      {option.name}
                    </Typography>
                  )}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />








              <label for="Voucher No.">Voucher No.</label>
              {/* <input type="text" placeholder="Voucher No."/> */}
              <input
                type="text"
                id="InvoiceNo"
                name="InvoiceNo"
                disabled
                class={errorObjectMaster.InvoiceNo}
                value={currentInvoice.InvoiceNo || ""}
                onChange={(e) => handleChangeMaster(e)}
              />
            </div>


{/* 
            {!currentInvoice.BPosted && (
            <div class="ExpenseInputArea">
              <label for="">Date*</label>
              <input
                type="date"
                id="ExpenseDate"
                name="ExpenseDate"
                class={errorObjectMany.ExpenseDate}
                value={currentMany.ExpenseDate}
                onChange={(e) => handleChangeMany(e)}
              />

              <label for="">Exp. Type*</label>
              <Autocomplete
                autoHighlight
                disabled={currentInvoice.BPosted}
                className="chosen_dropdown"
                id="ExpenseTypeId"
                name="ExpenseTypeId"
                class={errorObjectMany.ExpenseTypeId}
                autoComplete
                options={expenseTypeList ? expenseTypeList : []}
                getOptionLabel={(option) => option.name}
                value={
                  expenseTypeList
                    ? expenseTypeList[
                      expenseTypeList.findIndex(
                          (list) => list.id === currentMany.ExpenseTypeId
                        )
                      ]
                    : null
                }
                onChange={(event, valueobj) =>
                  handleChangeChoosenMany(
                    "ExpenseTypeId",
                    valueobj ? valueobj.id : ""
                  )
                }
                renderOption={(option) => (
                  <Typography className="chosen_dropdown_font">
                    {option.name}
                  </Typography>
                )}
                renderInput={(params) => (
                  <TextField {...params} variant="standard" fullWidth />
                )}
              />

              <label for="">Details</label>
              <input
                type="text"
                id="Comments"
                name="Comments"
                // disabled
                // class={errorObjectMany.Comments}
                value={currentMany.Comments}
                onChange={(e) => handleChangeMany(e)}
              />
              <label for="">Pay To</label>
              <input
                type="text"
                id="PayTo"
                name="PayTo"
                // disabled
                // class={errorObjectMany.PayTo}
                value={currentMany.PayTo}
                onChange={(e) => handleChangeMany(e)}
              />

              <label for="">Amount*</label>
              <input
                type="number"
                id="Amount"
                name="Amount"
                // disabled
                class={errorObjectMany.Amount}
                value={currentMany.Amount}
                onChange={(e) => handleChangeMany(e)}
              />

              <label for="">Expense By*</label>
            <Autocomplete
                  autoHighlight
                  disabled={currentInvoice.BPosted}
                  className="chosen_dropdown"
                  id="ExpenseUserId"
                  name="ExpenseUserId"
                  class={errorObjectMany.ExpenseUserId}
                  autoComplete
                  options={userList ? userList : []}
                  getOptionLabel={(option) => option.name}
                  value={
                    userList
                      ? userList[
                        userList.findIndex(
                        (list) => list.id === currentMany.ExpenseUserId
                      )
                      ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeChoosenMany(
                      "ExpenseUserId",
                      valueobj ? valueobj.id : ""
                    )
                  }
                  renderOption={(option) => (
                    <Typography className="chosen_dropdown_font">
                      {option.name}
                    </Typography>
                  )}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />

              <Button
                    label={"Add"}
                    class={"btnAdd"}
                    onClick={addEditManyItem}
                  />


            </div>
            )} */}




            {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}
            <div class="subContainer ExpenseBody">

             <CustomTable
                columns={manyColumnList}
                rows={manyDataList ? manyDataList : {}}
                actioncontrol={actioncontrol}
                ispagination={false}
              />
              
              {/* <table class="tableGlobal">
                <tr>
                  <th>SL.</th>
                  <th>Date</th>
                  <th>Expense Type</th>
                  <th>Expense Detail</th>
                  <th>Pay to</th>
                  <th>Amount</th>
                  <th>Expense by</th>
                  <th>Action</th>
                </tr>
                <tr>
                  <td>1.</td>
                  <td>12-Feb-24</td>
                  <td>Electric Bill</td>
                  <td>For month of Feb</td>
                  <td>BPDB</td>
                  <td>5020.00</td>
                  <td>Moniruzzaman</td>
                  <td>
                    <a href="#">Edit</a> <a href="#">Delete</a>
                  </td>
                </tr>
              </table> */}
              {/* <!-- FOOTER PARTITION-2 OPERATION AREA--> */}
              <div class="ExpenseFooter">
                {/* <label>Total Voucher Value: {currentInvoice.TotalAmount}</label> */}
                {/* <input type="text" /> */}
                {/* <button class="btnPrint">Print</button>
                <button class="btnClose">Close</button>
                <button class="btnSave">Save</button>
                <button class="btnPost">Post</button> */}

                <Button
                      disabled={false}
                      label={"Back"}
                      class={"btnClose"}
                      onClick={backToList}
                    />

                    <Button
                      disabled={permissionType === 1}
                      label={"New"}
                      class={"btnExchange"}
                      onClick={newInvoice}
                    />

                    {/* <Button
                      disabled={currentInvoice.BPosted}
                      label={"Hold Inv."}
                      class={"btnHold"}
                      onClick={saveData}
                    /> */}

                    {/* <Button
                      disabled={false}
                      label={"Print"}
                      class={"btnPrint"}
                      onClick={printInvoice}
                    /> */}

                    {/* <Button
                      disabled={permissionType === 1}
                      label={"Sales Return"}
                      class={"btnVoid"}
                      onClick={SalesReturnInv}
                    /> */}

                   <Button
                      disabled={currentInvoice.BPosted}
                      label={"Save"}
                      class={"btnSave"}
                      onClick={saveData}
                    />
                    <Button
                      disabled={currentInvoice.BPosted}
                      label={"Post"}
                      class={"btnPost"}
                      onClick={postInvoice}
                    />


              </div>
            </div>




            {/* <!-- Sales MASTER HEADER START--> */}
            {/* <div class="subContainer salesMasterHeader">
              <label># Inv No.*</label>
              <div>
                <input
                  type="text"
                  id="InvoiceNo"
                  name="InvoiceNo"
                  disabled
                  class={errorObjectMaster.InvoiceNo}
                  value={currentInvoice.InvoiceNo || ""}
                  onChange={(e) => handleChangeMaster(e)}
                />
              </div>

              <label>Sales By*</label>
              <div class="">
                <Autocomplete
                  autoHighlight
                  disabled={currentInvoice.BPosted}
                  className="chosen_dropdown"
                  id="ServiceBy"
                  name="ServiceBy"
                  class={errorObjectMaster.ServiceBy}
                  autoComplete
                  options={userList ? userList : []}
                  getOptionLabel={(option) => option.name}
                  value={
                    userList
                      ? userList[
                          userList.findIndex(
                            (list) => list.id === currentInvoice.ServiceBy
                          )
                        ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeChoosenMaster(
                      "ServiceBy",
                      valueobj ? valueobj.id : ""
                    )
                  }
                  renderOption={(option) => (
                    <Typography className="chosen_dropdown_font">
                      {option.name}
                    </Typography>
                  )}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />
              </div>

              <label>Ref. By</label>
              <div class="">
                <Autocomplete
                  autoHighlight
                  disabled={currentInvoice.BPosted}
                  className="chosen_dropdown"
                  id="ReferenceId"
                  name="ReferenceId"
                  autoComplete
                  options={userList ? userList : []}
                  getOptionLabel={(option) => option.name}
                  value={
                    userList
                      ? userList[
                        userList.findIndex(
                            (list) => list.id === currentInvoice.ReferenceId
                          )
                        ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeChoosenMaster(
                      "ReferenceId",
                      valueobj ? valueobj.id : ""
                    )
                  }
                  renderOption={(option) => (
                    <Typography className="chosen_dropdown_font">
                      {option.name}
                    </Typography>
                  )}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />
              </div>

            <label>Customer ***********</label>
              <div class="">
                <Autocomplete
                  autoHighlight
                  disabled={currentInvoice.BPosted}
                  className="chosen_dropdown"
                  id="CustomerId"
                  name="CustomerId"
                  class={errorObjectMaster.CustomerId}
                  autoComplete
                  options={customerList ? customerList : []}
                  getOptionLabel={(option) => option.name}
                  value={
                    customerList
                      ? customerList[
                      customerList.findIndex(
                        (list) => list.id === currentInvoice.CustomerId
                      )
                      ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeChoosenMaster(
                      "CustomerId",
                      valueobj ? valueobj.id : ""
                    )
                  }
                  renderOption={(option) => (
                    <Typography className="chosen_dropdown_font">
                      {option.name}
                    </Typography>
                  )}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />
              </div> 

              <label>Date*</label>
              <div>
                <input
                  type="text"
                  id="TransactionDate"
                  name="TransactionDate"
                  disabled
                  class={errorObjectMaster.TransactionDate}
                  value={currentInvoice.TransactionDate || ""}
                />
              </div>
            </div> */}

            {/* {!currentInvoice.BPosted && (
              <div class="subContainer salesHeader">
                <div class="salesHeader-part1">
                  <label>Date*</label>
                  <div>
                    <input
                      type="date"
                      id="ExpenseDate"
                      name="ExpenseDate"
                      class={errorObjectMany.ExpenseDate}
                      value={currentMany.ExpenseDate}
                      onChange={(e) => handleChangeMany(e)}
                    />
                  </div>

                  <label>Exp. Type*</label>
                  <div class="">
                    <Autocomplete
                      autoHighlight
                      disabled={currentInvoice.BPosted}
                      className="chosen_dropdown"
                      id="ExpenseTypeId"
                      name="ExpenseTypeId"
                      class={errorObjectMany.ExpenseTypeId}
                      autoComplete
                      options={customerList ? customerList : []}
                      getOptionLabel={(option) => option.name}
                      value={
                        customerList
                          ? customerList[
                              customerList.findIndex(
                                (list) =>
                                  list.id === currentInvoice.ExpenseTypeId
                              )
                            ]
                          : null
                      }
                      onChange={(event, valueobj) =>
                        handleChangeChoosenMaster(
                          "ExpenseTypeId",
                          valueobj ? valueobj.id : ""
                        )
                      }
                      renderOption={(option) => (
                        <Typography className="chosen_dropdown_font">
                          {option.name}
                        </Typography>
                      )}
                      renderInput={(params) => (
                        <TextField {...params} variant="standard" fullWidth />
                      )}
                    />
                  </div>

                  <label>Details</label>
                  <div>
                    <input
                      type="text"
                      id="Comments"
                      name="Comments"
                      value={currentMany.Comments}
                      onChange={(e) => handleChangeMany(e)}
                    />
                  </div>

                  <label>Pay To</label>
                  <div>
                    <input
                      type="text"
                      id="PayTo"
                      name="PayTo"
                      value={currentMany.PayTo}
                      onChange={(e) => handleChangeMany(e)}
                    />
                  </div>

                  <label>Amount*</label>
                  <input
                    type="number"
                    id="Amount"
                    name="Amount"
                    class={errorObjectMany.Amount}
                    value={currentMany.Amount}
                    onChange={(e) => handleChangeMany(e)}
                  />

                  <label>Expense By*</label>
                  <div class="">
                    <Autocomplete
                      autoHighlight
                      disabled={currentInvoice.BPosted}
                      className="chosen_dropdown"
                      id="ExpenseUserId"
                      name="ExpenseUserId"
                      class={errorObjectMaster.ExpenseUserId}
                      autoComplete
                      options={customerList ? customerList : []}
                      getOptionLabel={(option) => option.name}
                      value={
                        customerList
                          ? customerList[
                              customerList.findIndex(
                                (list) =>
                                  list.id === currentInvoice.ExpenseUserId
                              )
                            ]
                          : null
                      }
                      onChange={(event, valueobj) =>
                        handleChangeChoosenMaster(
                          "ExpenseUserId",
                          valueobj ? valueobj.id : ""
                        )
                      }
                      renderOption={(option) => (
                        <Typography className="chosen_dropdown_font">
                          {option.name}
                        </Typography>
                      )}
                      renderInput={(params) => (
                        <TextField {...params} variant="standard" fullWidth />
                      )}
                    />
                  </div>
                </div>

                <div class="salesHeader-part2">
                  <Button
                    label={"Add"}
                    class={"btnAdd"}
                    onClick={addEditManyItem}
                  />
                </div>
              </div>
            )} */}

            {/* <div class="subContainer salesBody">
               <div class="salesDetail">  
                <CustomTable
                  columns={manyColumnList}
                  rows={manyDataList ? manyDataList : {}}
                  actioncontrol={actioncontrol}
                  ispagination={false}
                />  

                <div class="salesBodyAction">
                  <div class="btnAction">
                    <Button
                      disabled={false}
                      label={"Back"}
                      class={"btnClose"}
                      onClick={backToList}
                    />

                    <Button
                      disabled={permissionType === 1}
                      label={"New"}
                      class={"btnExchange"}
                      onClick={newInvoice}
                    />

                    <Button
                      disabled={currentInvoice.BPosted}
                      label={"Hold Inv."}
                      class={"btnHold"}
                      onClick={saveData}
                    />

                    <Button
                      disabled={false}
                      label={"Re Print"}
                      class={"btnPrint"}
                      onClick={printInvoice}
                    />

                    <Button
                      disabled={permissionType === 1}
                      label={"Sales Return"}
                      class={"btnVoid"}
                      onClick={SalesReturnInv}
                    />

                    <Button
                      disabled={currentInvoice.BPosted}
                      label={"Print & Submit"}
                      class={"btnSales"}
                      onClick={postInvoice}
                    />
                  </div>
                </div>
              </div>
            </div> */}
          </>
        )}
      </div>
    </>
  );
};

export default Audit;
