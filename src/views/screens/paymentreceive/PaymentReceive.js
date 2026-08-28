import React, { forwardRef, useEffect, useRef } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit } from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";
import PaymentInvoiceAddModal from "./PaymentInvoiceAddModal";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { Typography, TextField, styled } from "@material-ui/core";
import moment from "moment";
import { use } from "react";

const PaymentReceive = (props) => {
  const serverpage = "paymentreceive"; // this is .php server page
  const permissionType = props.permissionType;

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [currentRow, setCurrentRow] = useState([]);
  // const [showModal, setShowModal] = useState(false); //true=show modal, false=hide modal
  const [listedittoggle, setListedittoggle] = useState(true); //true=show list panel, false=show edit panel
  const [errorObject, setErrorObject] = useState({});
  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const {
    isLoading: isLoadingMany,
    data: manyDataList,
    error: errorMany,
    ExecuteQuery: ExecuteQueryMany,
  } = ExecuteQueryHook(); //Fetch data
  let UserInfo = LoginUserInfo();

  const [CustomerGroupList, setCustomerGroupList] = useState(null);
  const [currCustomerGroupId, setCurrCustomerGroupId] = useState(null);

  const [CustomerList, setCustomerList] = useState(null);
  const [currCustomerId, setCurrCustomerId] = useState(null);

  const [BankList, setBankList] = useState(null);
  const [currBankId, setCurrBankId] = useState("");

  const [CurrencyList, setCurrencyList] = useState(null);
  const [currCurrencyId, setCurrCurrencyId] = useState("");

  const [PaymentModeList, setPaymentModeList] = useState(null);
  const [currPaymentModeId, setCurrPaymentModeId] = useState("");

  const [CollectedMethodList, setCollectedMethodList] = useState(null);
  const [currCollectedMethodId, setCurrCollectedMethodId] = useState("");

  const [editableItems, setEditableItems] = useState([]);
  const [showInvoiceModal, setShowInvoiceModal] = useState(false);

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const PDFGenerate = () => {
    // console.log("currentRow: ", currentRow.id);
    let finalUrl = EXCEL_EXPORT_URL + "report/GenerateMoneyReceipt.php";
    window.open(
      finalUrl + "?PaymentId=" + currentRow.id + "&TimeStamp=" + Date.now(),
    );
  };

  // const PrintPDFExcelExportFunction = (reportType) => {
  //   let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

  // window.open(
  //   finalUrl +
  //     "?action=ClientExport" +
  //     "&reportType=excel" +
  //     "&ClientId=" +
  //     UserInfo.ClientId +
  //     "&BranchId=" +
  //     UserInfo.BranchId +
  //     "&TimeStamp=" +
  //     Date.now()
  // );
  // };
  /* =====End of Excel Export Code==== */

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    {
      field: "MRNo",
      label: "MR",
      width: "8%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "RefNo",
      label: "Ref",
      width: "8%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "PaymentDate",
      label: "MR Issue Date",
      width: "7%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "CustomerCode",
      label: "Customer Code",
      width: "8%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "CustomerName",
      label: "Customer Name",
      // width: "9%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    // {
    //   field: "CustomerGroupName",
    //   label: "Customer Group",
    //   width: "12%",
    //   align: "left",
    //   visible: true,
    //   sort: true,
    //   filter: true,
    // },

    {
      field: "ChequeNumber",
      label: "Cheque Number",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "ChequeDate",
      label: "Cheque Date",
      width: "7%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "BankName",
      label: "Bank Name",
      width: "12%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "BankBranchName",
      label: "Bank Branch",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "custom",
      label: "Action",
      width: "6%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
  ];

  if (bFirst) {
    /**First time call for datalist */
    getDataList();
    setBFirst(false);
  }

  React.useEffect(() => {
    getCustomerGroupList();
    getCustomerList();
    getBankList();
    getCurrencyList();
    getPaymentModeList();
    getCollectedMethodList();
  }, []);

  React.useEffect(() => {
    if (manyDataList?.items) {
      setEditableItems(manyDataList.items);
    }
  }, [manyDataList]);

  // Recalculate amounts when editableItems changes
  React.useEffect(() => {
    // Calculate total base amount from editableItems (works even if empty)
    const totalBase = editableItems.reduce((sum, item) => {
      return sum + (parseFloat(item.BaseAmount) || 0);
    }, 0);

    setCurrentRow(prevRow => {
      let data = { ...prevRow };

      // STEP 1: Get all percentages from current row
      const rebatePercent = parseFloat(data.RebatePercent) || 0;
      const cnPercent = parseFloat(data.CNPercent) || 0;
      const aitPercent = parseFloat(data.AitPercent) || 0;
      const vatPercent = parseFloat(data.VatPercent) || 0;

      // STEP 2: Calculate amounts based on percentages
      // Rebate Amount = totalBase × RebatePercent / 100
      data.RebateAmount = ((totalBase * rebatePercent) / 100).toFixed(2);
      
      // CN Amount = totalBase × CNPercent / 100
      data.CNAmount = ((totalBase * cnPercent) / 100).toFixed(2);
      
      // AIT Deduction = totalBase × AitPercent / 100
      data.AitDeduction = ((totalBase * aitPercent) / 100).toFixed(2);
      
      // VAT Amount = totalBase × VatPercent / 100
      data.VatAmount = ((totalBase * vatPercent) / 100).toFixed(2);

      // STEP 3: Calculate AdvanceAmount and DueAmount using the calculated amounts
      const paymentReceiveAmount = parseFloat(data.PaymentReceiveAmount) || 0;
      const rebateAmount = parseFloat(data.RebateAmount) || 0;
      const cnAmount = parseFloat(data.CNAmount) || 0;
      const aitDeduction = parseFloat(data.AitDeduction) || 0;
      const vatAmount = parseFloat(data.VatAmount) || 0;

      const netInvoiceAmount = totalBase - rebateAmount - cnAmount - aitDeduction - vatAmount;
      const difference = paymentReceiveAmount - netInvoiceAmount;

      if (difference > 0) {
        data.AdvanceAmount = difference.toFixed(2);
        data.DueAmount = 0;
      } else {
        data.AdvanceAmount = 0;
        data.DueAmount = ((-1) * difference).toFixed(2);
      }

      return data;
    });
  }, [editableItems]);

  const getSelectedOption = (list, id) => {
    if (!list || list.length === 0) return null;
    const selected = list.find(
      (item) => String(item.id ?? "") === String(id ?? ""),
    );
    return selected || list[0];
  };

  // Memoize selected customer to avoid expensive findIndex on every render
  const selectedCustomer = React.useMemo(() => {
    return getSelectedOption(CustomerList, currCustomerId);
  }, [CustomerList, currCustomerId]);

  // Memoize selected bank to avoid expensive findIndex on every render
  const selectedBank = React.useMemo(() => {
    return getSelectedOption(BankList, currBankId);
  }, [BankList, currBankId]);

  const selectedCurrency = React.useMemo(() => {
    return getSelectedOption(CurrencyList, currCurrencyId);
  }, [CurrencyList, currCurrencyId]);

  const selectedPaymentMode = React.useMemo(() => {
    return getSelectedOption(PaymentModeList, currPaymentModeId);
  }, [PaymentModeList, currPaymentModeId]);

  const selectedCollectedMethod = React.useMemo(() => {
    return getSelectedOption(CollectedMethodList, currCollectedMethodId);
  }, [CollectedMethodList, currCollectedMethodId]);

  function getCustomerGroupList() {
    let params = {
      action: "CustomerGroupList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCustomerGroupList(
        [{ id: "", name: "Select Customer Group" }].concat(res.data.datalist),
      );

      // setCurrCustomerGroupId(selectCustomerGroupId);
    });
  }

  function getCustomerList() {
    let params = {
      action: "CustomerList",
      lan: language(),
      UserId: UserInfo.UserId,
      CustomerGroupId: currCustomerGroupId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCustomerList(
        [{ id: "", name: "Select Customer" }].concat(res.data.datalist),
      );

      // setCurrCustomerId(selectCustomerGroupId);
    });
  }

  function getBankList() {
    let params = {
      action: "BankList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setBankList([{ id: "", name: "Select Bank" }].concat(res.data.datalist));

      // setCurrCustomerId(selectCustomerGroupId);
    });
  }

  function getCurrencyList() {
    let params = {
      action: "CurrencyList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCurrencyList(
        [{ id: "", name: "Select Currency" }].concat(res.data.datalist),
      );
    });
  }

  function getPaymentModeList() {
    let params = {
      action: "PaymentModeList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setPaymentModeList(
        [{ id: "", name: "Select Payment Mode" }].concat(res.data.datalist),
      );
    });
  }

  function getCollectedMethodList() {
    let params = {
      action: "CollectedMethodList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCollectedMethodList(
        [{ id: "", name: "Select Collected By" }].concat(res.data.datalist),
      );
    });
  }

  /**Get data for table list */
  function getDataList() {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    ExecuteQuery(serverpage, params);
  }

  /** Action from table row buttons*/
  function actioncontrol(rowData) {
    return (
      <>
        {permissionType === 0 && (
          <Edit
            className={"table-edit-icon"}
            onClick={() => {
              editData(rowData);
            }}
          />
        )}

        {permissionType === 0 && rowData.StatusId == 1 && (
          <DeleteOutline
            className={"table-delete-icon"}
            onClick={() => {
              deleteData(rowData);
            }}
          />
        )}
      </>
    );
  }

  const addData = () => {
    let params = {
      action: "getNextMRNumber",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      console.log("res: ", res.data.MRNo);

      setCurrentRow({
        id: "",
        PaymentDate: moment().format("YYYY-MM-DD"),
        CustomerId: "",
        CustomerGroupId: "",
        MRNo: res.data.MRNo,
        RefNo: "",
        BankId: "",
        ChequeNumber: "",
        ChequeDate: "",
        BankBranchName: "",
        PaymentModeId: "",
        CurrencyId: "",
        PaymentReceiveCurrencyAmount: 0,
        ExchangeRate: 1,
        Remarks: "",

        TotalBaseAmount: "",
        TotalTransactionAmount: 0,

        PaymentReceiveAmount: 0,
        CollectedMethodId: "",
        CollectionAddress: "",
        CollectionPersonName: "",
        CollectionPersonPhone: "",
        RebateAmount: 0,
        CNAmount: 0,
        AitDeduction: 0,
        VatAmount: 0,
        AdvanceAmount: 0,
        StatusId: 1,
        Items: [],
      });
      setEditableItems([]);
      setCurrCustomerGroupId("");
      setCurrCustomerId("");
      setCurrBankId("");
      setCurrCurrencyId("");
      setCurrPaymentModeId("");
      setCurrCollectedMethodId("");

      openModal();
    });
  };

  function openModal() {
    setListedittoggle(false);
    // setShowModal(true); //true=modal show, false=modal hide
  }

  function hideModal() {
    getDataList();

    setListedittoggle(true);
    // setShowModal(true); //true=modal show, false=modal hide
  }

  const editData = (rowData) => {
    setCurrentRow(rowData);
    setCurrCustomerGroupId(rowData.CustomerGroupId);
    setCurrCustomerId(rowData.CustomerId);
    setCurrBankId(rowData.BankId);
    setCurrCurrencyId(rowData.CurrencyId);
    setCurrPaymentModeId(rowData.PaymentModeId);
    setCurrCollectedMethodId(rowData.CollectedMethodId);

    getDataSingleFromServer(rowData.id);
    openModal();
  };

  const getDataSingleFromServer = (id) => {
    let params = {
      action: "getDataSingle",
      lan: language(),
      UserId: UserInfo.UserId,
      id: id,
    };

    // setDeletedItems([]);

    // ExecuteQuerySingle(serverpage, params);
    ExecuteQueryMany(serverpage, params);
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

  const deletePaymentItem = (rowData) => {
    swal({
      title: "Are you sure?",
      text: "This invoice will be removed from the payment.",
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
        const params = {
          action: "deletePaymentItem",
          lan: language(),
          UserId: UserInfo.UserId,
          PaymentId: currentRow.id,
          PaymentItemId: rowData.PaymentItemId,
          InvoiceItemId: rowData.InvoiceItemId,
        };

        apiCall.post(serverpage, { params }, apiOption()).then((res) => {
          props.openNoticeModal({
            isOpen: true,
            msg: res.data.message,
            msgtype: res.data.success,
          });

          if (res.data.success === 1) {
            getDataSingleFromServer(currentRow.id);
          }
        });
      }
    });
  };

  // function getNextMRNumber() {
  //   let params = {
  //     action: "getNextMRNumber",
  //     lan: language(),
  //     UserId: UserInfo.UserId
  //   };

  //   apiCall.post(serverpage, { params }, apiOption()).then((res) => {
  //     console.log('res: ', res.data.MRNo);

  //   });
  // }

  function addEditInvoice() {
    addEditAPICall(1);
  }

  function postInvoice() {

    if (editableItems.length == 0 && !currentRow.AdvanceAmount) {
      props.openNoticeModal({
        isOpen: true,
        msg: "No invoice to post.",
        msgtype: 0,
      });
      return;
    }

    swal({
      title: "Are you sure?",
      text: "You want to complete this bill, it will not be editable after completion.",
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
        addEditAPICall(5);
      }
    });
  }

  function addEditAPICall(StatusId = 1) {
    if (validateForm()) {
      // if (currentRow.id) {
      //   let invTotalAmount = calculateTotalPaymentAmount();
      //   if (invTotalAmount != currentRow.TotalPaymentAmount) {
      //     props.openNoticeModal({
      //       isOpen: true,
      //       msg: "Total Received Amount must be equal to sum of Received Amount in invoice list.",
      //       msgtype: 0,
      //     });
      //     return;
      //   }
      // }

      let data = { ...currentRow };

      if (StatusId == 5) {
        data["StatusId"] = StatusId;
      }

      let UserInfo = LoginUserInfo();
      let params = {
        action: "dataAddEdit",
        lan: language(),
        UserId: UserInfo.UserId,
        rowData: data,
        // items: editableItems,
      };
      apiCall.post(serverpage, { params }, apiOption()).then((res) => {
        // console.log('res: ', res);

        props.openNoticeModal({
          isOpen: true,
          msg: res.data.message,
          msgtype: res.data.success,
        });

        if (res.data.success === 1) {
          // hideModal();
          // getDataList();
          // let data = { ...currentRow };

          // if (data["id"]) {
          //   //when update and complete then set status to completed
          //   data["StatusId"] = 5;
          // }

          data["id"] = res.data.PaymentId;
          setCurrentRow(data);
          // console.log("data: ", data);

          // getDataSingleFromServer(res.data.PaymentId);
        }
      });
    }
  }

  const validateForm = () => {
    let validateFields = [
      "MRNo",
      "PaymentDate",
      "CustomerId",
    ];
    let errorData = {};
    let isValid = true;
    validateFields.map((field) => {
      if (!currentRow[field]) {
        errorData[field] = "validation-style";
        isValid = false;
      }
    });
    setErrorObject(errorData);
    return isValid;
  };

  const handleChangeFilterDropDown = (name, value) => {
    let data = { ...currentRow };
    if (name === "CustomerGroupId") {
      data["CustomerGroupId"] = value;
      setCurrCustomerGroupId(value);
    }

    if (name === "CustomerId") {
      data["CustomerId"] = value;
      setCurrCustomerId(value);
    }

    if (name === "BankId") {
      data["BankId"] = value;
      setCurrBankId(value);
    }

    if (name === "CurrencyId") {
      data["CurrencyId"] = value;
      setCurrCurrencyId(value);
    }

    if (name === "PaymentModeId") {
      data["PaymentModeId"] = value;
      setCurrPaymentModeId(value);
    }

    if (name === "CollectedMethodId") {
      data["CollectedMethodId"] = value;
      setCurrCollectedMethodId(value);
    }
    setErrorObject({ ...errorObject, [name]: null });
    setCurrentRow(data);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;

    if (name === "PaymentReceiveCurrencyAmount" || name === "ExchangeRate") {
      const currencyAmount = parseFloat(data.PaymentReceiveCurrencyAmount) || 0;
      const exchangeRate = parseFloat(data.ExchangeRate) || 0;
      data.PaymentReceiveAmount = (currencyAmount * exchangeRate).toFixed(2);
    }

    // Calculate total base amount from editableItems
    const totalBase = editableItems.reduce((sum, item) => {
      return sum + (parseFloat(item.BaseAmount) || 0);
    }, 0);

    // ALWAYS calculate amounts from percentages first
    const rebatePercent = parseFloat(data.RebatePercent) || 0;
    const cnPercent = parseFloat(data.CNPercent) || 0;
    const aitPercent = parseFloat(data.AitPercent) || 0;
    const vatPercent = parseFloat(data.VatPercent) || 0;

    // Calculate Rebate Amount from RebatePercent
    data.RebateAmount = ((totalBase * rebatePercent) / 100).toFixed(2);
    
    // Calculate CN Amount from CNPercent
    data.CNAmount = ((totalBase * cnPercent) / 100).toFixed(2);
    
    // Calculate AIT Deduction from AitPercent
    data.AitDeduction = ((totalBase * aitPercent) / 100).toFixed(2);
    
    // Calculate VAT Amount from VatPercent
    data.VatAmount = ((totalBase * vatPercent) / 100).toFixed(2);

    // Calculate AdvanceAmount and DueAmount using the calculated amounts
    const paymentReceiveAmount = parseFloat(data.PaymentReceiveAmount) || 0;
    const rebateAmount = parseFloat(data.RebateAmount) || 0;
    const cnAmount = parseFloat(data.CNAmount) || 0;
    const aitDeduction = parseFloat(data.AitDeduction) || 0;
    const vatAmount = parseFloat(data.VatAmount) || 0;

    const netInvoiceAmount = totalBase - rebateAmount - cnAmount - aitDeduction - vatAmount;
    const difference = paymentReceiveAmount - netInvoiceAmount;

    if (difference > 0) {
      data.AdvanceAmount = difference.toFixed(2);
      data.DueAmount = 0;
    } else {
      data.AdvanceAmount = 0;
      data.DueAmount = ((-1) * difference).toFixed(2);
    }

    setCurrentRow(data);

    setErrorObject({ ...errorObject, [name]: null });
  };

  // const handleChangeMany = (e, row) => {
  //   const { name, value } = e.target;
  //   const updatedItems = editableItems.map((item) => {
  //     if (item.PaymentItemId === row.PaymentItemId) {
  //       return {
  //         ...item,
  //         [name]: value,
  //         IsPaid: value >= item.DueAmount ? true : false,
  //       };
  //     }
  //     return item;
  //   });

  //   setEditableItems(updatedItems);
  //   // console.log('updatedItems: ', updatedItems);

  //   // setErrorObject({ ...errorObject, [name]: null });
  // };

  const handleChangeCheck = (e, row) => {
    // console.log('e.target.checked: ', e.target.checked);
    const { name, value } = e.target;
    console.log("name, value: ", name, value);
    const checked = e.target.checked;

    const updatedItems = editableItems.map((item) => {
      if (item.PaymentItemId === row.PaymentItemId) {
        return { ...item, [name]: checked };
      }
      return item;
    });

    setEditableItems(updatedItems);
    console.log("updatedItems: ", updatedItems);
  };

  const manyColumnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },

    {
      field: "AccountingPeriod",
      label: "Acc. Period",
      width: "6%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "TransactionDate",
      label: "Invoice Date",
      width: "8%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "Description",
      label: "Description",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "TransactionReference",
      label: "Invoice Number",
      width: "15%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "TransactionAmount",
      label: "Amount in FC",
      width: "7%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
      bottomcalc: "sum",
      type: "number",
    },

    {
      field: "ExchangeRate",
      label: "Ex. Rate",
      width: "7%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
      type: "number",
    },

    {
      field: "BaseAmount",
      label: "Amount in BDT",
      width: "10%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
      type: "number",
      bottomcalc: "sum",
    },

    {
      field: "custom",
      label: "Action",
      width: "5%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
  ];

  /** Action from table row buttons*/
  function actioncontrolmany(rowData) {
    return (
      <>
        {permissionType === 0 && currentRow.StatusId == 1 && (
          <DeleteOutline
            className={"table-delete-icon"}
            onClick={() => {
              deletePaymentItem(rowData);
            }}
          />
        )}
      </>
      // <div
      //   style={{
      //     display: "flex",
      //     justifyContent: "center",
      //     alignItems: "center",
      //     flexDirection: "row",
      //   }}
      // >
      //   <input
      //     type="number"
      //     id="PaymentAmount"
      //     name="PaymentAmount"
      //     disabled={currentRow.StatusId == 5 ? true : false}
      //     value={
      //       editableItems.length > 0
      //         ? editableItems[
      //             editableItems.findIndex(
      //               (list) => list.PaymentItemId == rowData.PaymentItemId
      //             )
      //           ]?.PaymentAmount || ""
      //         : ""
      //     }
      //     onChange={(e) => handleChangeMany(e, rowData)}
      //   />

      //   <input
      //     id="IsPaid"
      //     name="IsPaid"
      //     type="checkbox"
      //     disabled={currentRow.StatusId == 5 ? true : false}
      //     style={{ width: "30px", height: "18px", marginRight: "5px" }}
      //     checked={rowData.IsPaid}
      //     onChange={(e) => handleChangeCheck(e, rowData)}
      //   />

      // </div>
    );
  }

  // useEffect(() => {
  //   calculateTotalAmount();
  // }, [editableItems]);


  // function calculateTotalAmount() {
  //   let totalTransactionAmountt = 0;
  //   let totalBaseAmount = 0;

  //   editableItems.forEach((item) => {
  //     const TransactionAmount = parseFloat(item.TransactionAmount) || 0;
  //     totalTransactionAmountt += TransactionAmount;

  //     const BaseAmount = parseFloat(item.BaseAmount) || 0;
  //     totalBaseAmount += BaseAmount;
  //   });
  //   // return total;
  //   let data = { ...currentRow };
  //   data["TotalTransactionAmount"] = totalTransactionAmountt.toFixed(2);
  //   data["TotalBaseAmount"] = totalBaseAmount.toFixed(2);

  //   setCurrentRow(data);
  //   // return total.toFixed(0);
  // }

  const openInvoiceModal = () => {
    setShowInvoiceModal(true);
  };

  const invoiceModalCallback = (action) => {
    setShowInvoiceModal(false);
    if (action === "addedit") {
      getDataSingleFromServer(currentRow.id);
    }
  };

 const calTotal = () => {
    // Calculate total base amount from editableItems
    const totalBase = editableItems.reduce((sum, item) => {
      return sum + (parseFloat(item.BaseAmount) || 0);
    }, 0);

    const rebateAmount = parseFloat(currentRow.RebateAmount) || 0;
    const CNAmount = parseFloat(currentRow.CNAmount) || 0;
    const aitDeduction = parseFloat(currentRow.AitDeduction) || 0;
    const VatAmount = parseFloat(currentRow.VatAmount) || 0;
    
    // Net Invoice Amount = Total Base - Rebate - CN - AIT - VAT
    const netInvoiceAmount = totalBase - rebateAmount - CNAmount - aitDeduction - VatAmount;
    
    return netInvoiceAmount.toFixed(2);
  };


  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Invoice ❯ Payment Receive
          </h4>
        </div>

        {listedittoggle && (
          <div>
            <div class="searchAdd">
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

            <CustomTable
              columns={columnList}
              rows={dataList ? dataList : {}}
              actioncontrol={actioncontrol}
            />
          </div>
        )}

        {!listedittoggle && (
          <>
            <div class="searchAdd">
              <Button
                label={"Back to List"}
                class={"btnClose"}
                onClick={hideModal}
              />

              <Button
                label={currentRow.id ? "Update" : "Save"}
                class={"btnSave"}
                disabled={currentRow.StatusId == 5 ? true : false}
                onClick={addEditInvoice}
              />

              {/* {currentRow.id && currentRow.StatusId == 1 && ( */}
              <Button
                label={"Complete"} //update
                class={"btnUpdate"}
                disabled={
                  !currentRow.id || currentRow.StatusId == 5 ? true : false
                }
                onClick={postInvoice}
              />
              {/* )} */}
              {/* {!currentRow.id && (
                <Button
                  label={"Save"}
                  class={"btnSave"}
                  onClick={addEditAPICall}
                />
              )} */}

              {/* {currentRow.id && ( */}
              <Button
                label={"Money Receipt"}
                class={"btnPrint"}
                disabled={
                  currentRow.id && currentRow.StatusId == 5 ? false : true
                }
                onClick={PDFGenerate}
              />
              {/* )} */}
            </div>

            <div>
              <div class="fourColumnContainer pt-10">
                <label>MR *</label>
                <input
                  type="text"
                  id="MRNo"
                  name="MRNo"
                  disabled={true}
                  class={errorObject.MRNo}
                  placeholder="Enter MR Number"
                  value={currentRow.MRNo}
                  onChange={(e) => handleChange(e)}
                />

                <label>Ref</label>
                <input
                  type="text"
                  id="RefNo"
                  name="RefNo"
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  // class={errorObject.RefNo}
                  placeholder="Enter Ref Number"
                  value={currentRow.RefNo}
                  onChange={(e) => handleChange(e)}
                />

                <label>MR Issue Date *</label>
                <input
                  type="date"
                  id="PaymentDate"
                  name="PaymentDate"
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  class={errorObject.PaymentDate}
                  placeholder="Enter MR Issue Date"
                  value={currentRow.PaymentDate}
                  onChange={(e) => handleChange(e)}
                />

                {/* <label>Customer Group *</label>
                <Autocomplete
                  autoHighlight
                  disableClearable
                  className="chosen_dropdown"
                  id="CustomerGroupId"
                  name="CustomerGroupId"
                  autoComplete
                  class={errorObject.CustomerGroupId}
                  options={CustomerGroupList ? CustomerGroupList : []}
                  getOptionLabel={(option) => option.name}
                  defaultValue={{ id: 0, name: "Select Customer Group" }}
                  value={
                    CustomerGroupList
                      ? CustomerGroupList[
                          CustomerGroupList.findIndex(
                            (list) => list.id === currCustomerGroupId
                          )
                        ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
                      "CustomerGroupId",
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
                /> */}

                <label>Customer *</label>
                <Autocomplete
                  disableClearable
                  className="chosen_dropdown"
                  id="CustomerId"
                  name="CustomerId"
                  autoComplete
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  class={errorObject.CustomerId}
                  options={CustomerList ? CustomerList : []}
                  getOptionLabel={(option) => (option && option.name) || ""}
                  getOptionSelected={(option, value) =>
                    String(option?.id ?? "") === String(value?.id ?? "")
                  }
                  value={selectedCustomer}
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
                      "CustomerId",
                      valueobj ? valueobj.id : "",
                    )
                  }
                  filterOptions={(options, state) => {
                    // Optimize filtering for large lists
                    const inputValue = state.inputValue.toLowerCase();
                    if (!inputValue) return options.slice(0, 500); // Show only first 500 initially
                    return options
                      .filter((option) =>
                        option.name.toLowerCase().includes(inputValue),
                      )
                      .slice(0, 500); // Limit results to 500
                  }}
                  renderOption={(option) => option.name}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      variant="standard"
                      fullWidth
                      placeholder="Type to search..."
                    />
                  )}
                />
                {/* </div>

              <div class="contactmodalBody pt-10"> */}
                <label>Bank</label>
                <Autocomplete
                  disableClearable
                  className="chosen_dropdown"
                  id="BankId"
                  name="BankId"
                  autoComplete
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  // class={errorObject.BankId}
                  options={BankList ? BankList : []}
                  getOptionLabel={(option) => (option && option.name) || ""}
                  getOptionSelected={(option, value) =>
                    String(option?.id ?? "") === String(value?.id ?? "")
                  }
                  value={selectedBank}
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
                      "BankId",
                      valueobj ? valueobj.id : "",
                    )
                  }
                  renderOption={(option) => option.name}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />

                <label>Branch Name</label>
                <input
                  type="text"
                  id="BankBranchName"
                  name="BankBranchName"
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  // class={errorObject.BankBranchName}
                  placeholder="Enter Branch Name"
                  value={currentRow.BankBranchName}
                  onChange={(e) => handleChange(e)}
                />

                <label>Cheque Number</label>
                <input
                  type="text"
                  id="ChequeNumber"
                  name="ChequeNumber"
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  // class={errorObject.BankBranchName}
                  placeholder="Enter Cheque Number"
                  value={currentRow.ChequeNumber}
                  onChange={(e) => handleChange(e)}
                />

                <label>Cheque/ FDD Date</label>
                <input
                  type="date"
                  id="ChequeDate"
                  name="ChequeDate"
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  // class={errorObject.BankBranchName}
                  placeholder="Enter Cheque Date"
                  value={currentRow.ChequeDate}
                  onChange={(e) => handleChange(e)}
                />

                <label>Payment Mode</label>
                <Autocomplete
                  disableClearable
                  className="chosen_dropdown"
                  id="PaymentModeId"
                  name="PaymentModeId"
                  autoComplete
                  disabled={currentRow.StatusId == 5 ? true : false}
                  options={PaymentModeList ? PaymentModeList : []}
                  getOptionLabel={(option) => (option && option.name) || ""}
                  getOptionSelected={(option, value) =>
                    String(option?.id ?? "") === String(value?.id ?? "")
                  }
                  value={selectedPaymentMode}
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
                      "PaymentModeId",
                      valueobj ? valueobj.id : "",
                    )
                  }
                  renderOption={(option) => option.name}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />

                <label>Currency</label>
                <Autocomplete
                  disableClearable
                  className="chosen_dropdown"
                  id="CurrencyId"
                  name="CurrencyId"
                  autoComplete
                  disabled={currentRow.StatusId == 5 ? true : false}
                  options={CurrencyList ? CurrencyList : []}
                  getOptionLabel={(option) => (option && option.name) || ""}
                  getOptionSelected={(option, value) =>
                    String(option?.id ?? "") === String(value?.id ?? "")
                  }
                  value={selectedCurrency}
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
                      "CurrencyId",
                      valueobj ? valueobj.id : "",
                    )
                  }
                  renderOption={(option) => option.name}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />

                <label>Payment Amount in FC</label>
                <input
                  type="number"
                  id="PaymentReceiveCurrencyAmount"
                  name="PaymentReceiveCurrencyAmount"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  placeholder="Amount in FC"
                  value={currentRow.PaymentReceiveCurrencyAmount}
                  onChange={(e) => handleChange(e)}
                />

                <label>Exchange Rate</label>
                <input
                  type="number"
                  id="ExchangeRate"
                  name="ExchangeRate"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  placeholder="Enter Exchange Rate"
                  value={currentRow.ExchangeRate}
                  onChange={(e) => handleChange(e)}
                />

                <label>Payment Amount in BDT</label>
                <input
                  type="number"
                  id="PaymentReceiveAmount"
                  name="PaymentReceiveAmount"
                  disabled={true}
                  placeholder="Payment Received"
                  value={currentRow.PaymentReceiveAmount}
                  onChange={(e) => handleChange(e)}
                />

                <label>Collected By</label>
                <Autocomplete
                  disableClearable
                  className="chosen_dropdown"
                  id="CollectedMethodId"
                  name="CollectedMethodId"
                  autoComplete
                  disabled={currentRow.StatusId == 5 ? true : false}
                  options={CollectedMethodList ? CollectedMethodList : []}
                  getOptionLabel={(option) => (option && option.name) || ""}
                  getOptionSelected={(option, value) =>
                    String(option?.id ?? "") === String(value?.id ?? "")
                  }
                  value={selectedCollectedMethod}
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
                      "CollectedMethodId",
                      valueobj ? valueobj.id : "",
                    )
                  }
                  renderOption={(option) => option.name}
                  renderInput={(params) => (
                    <TextField {...params} variant="standard" fullWidth />
                  )}
                />

                <label>Collection Address</label>
                <input
                  type="text"
                  id="CollectionAddress"
                  name="CollectionAddress"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  placeholder="Enter Collection Address"
                  value={currentRow.CollectionAddress}
                  onChange={(e) => handleChange(e)}
                />

                <label>Collection Person Name</label>
                <input
                  type="text"
                  id="CollectionPersonName"
                  name="CollectionPersonName"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  placeholder="Enter Collection Person Name"
                  value={currentRow.CollectionPersonName}
                  onChange={(e) => handleChange(e)}
                />

                <label>Collection Person Phone</label>
                <input
                  type="text"
                  id="CollectionPersonPhone"
                  name="CollectionPersonPhone"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  placeholder="Enter Collection Person Phone"
                  value={currentRow.CollectionPersonPhone}
                  onChange={(e) => handleChange(e)}
                />

                <label>Remarks</label>
                <input
                  type="text"
                  id="Remarks"
                  name="Remarks"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.PaymentDate}
                  placeholder="Enter Remarks"
                  value={currentRow.Remarks}
                  onChange={(e) => handleChange(e)}
                />

            
                {/* <label>Total Amount (USD)</label>
                <input
                  type="number"
                  id="TotalTransactionAmount"
                  name="TotalTransactionAmount"
                  disabled={true}
                  // class={errorObject.TotalTransactionAmount}
                  placeholder=""
                  value={currentRow.TotalTransactionAmount}
                  // value={calculateTotalPaymentAmount()}
                  onChange={(e) => handleChange(e)}
                /> */}

                {/* <label>Total Base Amount (BDT)</label>
                <input
                  type="number"
                  id="TotalBaseAmount"
                  name="TotalBaseAmount"
                  disabled={
                    editableItems.length > 0 || currentRow.StatusId == 5
                      ? true
                      : false
                  }
                  // class={errorObject.TotalBaseAmount}
                  // placeholder="Enter Total Base Amount"
                  value={currentRow.TotalBaseAmount}
                  onChange={(e) => handleChange(e)}
                /> */}

              </div>

              {/* {currentRow.id && (
                <> */}
              <div class="searchAdd">
                <Button
                  label={"Add Invoice"}
                  class={"btnSave"}
                  disabled={
                    currentRow.StatusId == 5 || !currentRow.id ? true : false
                  }
                  onClick={openInvoiceModal}
                />
              </div>
              <div class="subContainer  mt-10">
                <CustomTable
                  columns={manyColumnList}
                  rows={editableItems.length > 0 ? editableItems : {}}
                  actioncontrol={actioncontrolmany}
                  ispagination={false}
                />
              </div>

              <div class="fourColumnContainer pt-10">
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label>Payment Received</label>
                <input
                  type="number"
                  id="PaymentReceiveAmount"
                  name="PaymentReceiveAmount"
                  disabled={true}
                  // disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.PaymentReceiveAmount}
                  // placeholder="Enter Payment Received"
                  value={currentRow.PaymentReceiveAmount}
                  onChange={(e) => handleChange(e)}
                />
              {/* </div>
              <div class="fourColumnContainer pt-10"> */}
                <label></label>
                <div></div>
                <label></label>
                <div></div>

                <label>Rebate %</label>
                <input
                  type="number"
                  id="RebatePercent"
                  name="RebatePercent"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.RebatePercent}
                  placeholder="Enter Rebate %"
                  value={currentRow.RebatePercent}
                  onChange={(e) => handleChange(e)}
                />

                <label>Rebate Amount</label>
                <input
                  type="number"
                  id="RebateAmount"
                  name="RebateAmount"
                  disabled={true}
                  // class={errorObject.RebateAmount}
                  placeholder="Rebate Amount"
                  value={currentRow.RebateAmount}
                  onChange={(e) => handleChange(e)}
                />
                             <label></label>
                <div></div>
                <label></label>
                <div></div>

                <label>CN %</label>
                <input
                  type="number"
                  id="CNPercent"
                  name="CNPercent"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.CNPercent}
                  placeholder="Enter CN %"
                  value={currentRow.CNPercent}
                  onChange={(e) => handleChange(e)}
                />

                <label>CN Amount</label>
                <input
                  type="number"
                  id="CNAmount"
                  name="CNAmount"
                  disabled={true}
                  // class={errorObject.CNAmount}
                  placeholder="CN Amount"
                  value={currentRow.CNAmount}
                  onChange={(e) => handleChange(e)}
                />
              {/* </div>
                <div class="fourColumnContainer pt-10"> */}
                <label></label>
                <div></div>
                <label></label>
                <div></div>

                <label>AIT %</label>
                <input
                  type="number"
                  id="AitPercent"
                  name="AitPercent"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.AitPercent}
                  placeholder="Enter AIT %"
                  value={currentRow.AitPercent}
                  onChange={(e) => handleChange(e)}
                />

                <label>AIT Deduction</label>
                <input
                  type="number"
                  id="AitDeduction"
                  name="AitDeduction"
                  disabled={true}
                  // class={errorObject.AitDeduction}
                  placeholder="AIT Deduction"
                  value={currentRow.AitDeduction}
                  onChange={(e) => handleChange(e)}
                />

                <label></label>
                <div></div>
                <label></label>
                <div></div>

                <label>VAT %</label>
                <input
                  type="number"
                  id="VatPercent"
                  name="VatPercent"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.VatPercent}
                  placeholder="Enter VAT %"
                  value={currentRow.VatPercent}
                  onChange={(e) => handleChange(e)}
                />

                <label>VAT Amount</label>
                <input
                  type="number"
                  id="VatAmount"
                  name="VatAmount"
                  disabled={true}
                  // class={errorObject.VatAmount}
                  placeholder="VAT Amount"
                  value={currentRow.VatAmount}
                  onChange={(e) => handleChange(e)}
                />

                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label>Advance/ Rem Advance Amount</label>
                <input
                  type="number"
                  id="AdvanceAmount"
                  name="AdvanceAmount"
                  disabled={true}
                  // class={errorObject.AdvanceAmount}
                  placeholder="Advance Amount"
                  value={currentRow.AdvanceAmount}
                  onChange={(e) => handleChange(e)}
                />

                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label>Due Amount</label>
                <input
                  type="number"
                  id="DueAmount"
                  name="DueAmount"
                  disabled={true}
                  // class={errorObject.DueAmount}
                  placeholder="Due Amount"
                  value={currentRow.DueAmount}
                  onChange={(e) => handleChange(e)}
                />

                {/* <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label><strong>Net Invoice Amount</strong></label>
                <input
                  type="number"
                  id="Total"
                  name="Total"
                  disabled={true}
                  style={{fontWeight: 'bold'}}
                  // class={errorObject.RebateAmount}
                  // placeholder="Enter Rebate Amount"
                  value={calTotal()}
                  // onChange={(e) => handleChange(e)}
                /> */}
              </div>


              {/* </>


              )} */}

              {/* <div class="modalItem">
                {currentRow.id && (
                  <Button
                    label={"Complete"} //update
                    class={"btnUpdate"}
                    onClick={addEditAPICall}
                  />
                )}
                {!currentRow.id && (
                  <Button
                    label={"Save"}
                    class={"btnSave"}
                    onClick={addEditAPICall}
                  />
                )}
              </div> */}
            </div>
          </>
        )}
      </div>
      {/* <!-- BODY CONTAINER END --> */}

      {showInvoiceModal && (
        <PaymentInvoiceAddModal
          currentRow={currentRow}
          modalCallback={invoiceModalCallback}
          masterProps={props}
        />
      )}
    </>
  );
};

export default PaymentReceive;
