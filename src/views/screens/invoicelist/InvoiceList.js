import React, { forwardRef, useRef, useEffect } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit } from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";
import InvoiceEditModal from "./InvoiceEditModal";

import {
  Typography,
  Paper,
  Grid,
  Input,
  makeStyles,
  CircularProgress,
  TextField,
} from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";
import moment from "moment";
const useStyles = makeStyles((theme) => ({
  root: {
    // minHeight: "100vh",
    // backgroundColor: "#f5f5f5",
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    // padding: theme.spacing(2),
    padding: "10px",
  },
  paper: {
    padding: theme.spacing(4),
    maxWidth: "50%",
    width: "100%",
  },
  // input: {
  //   marginTop: theme.spacing(2),
  //   marginBottom: theme.spacing(1),
  // }
}));
const InvoiceList = (props) => {
  const serverpage = "invoicelist"; // this is .php server page

  const permissionType = props.permissionType;
  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [currentRow, setCurrentRow] = useState([]);
  const [showModal, setShowModal] = useState(false); //true=show modal, false=hide modal

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const UserInfo = LoginUserInfo();

  const classes = useStyles();
  const [selectedFile, setSelectedFile] = useState(null);
  // const [uploadStatus, setUploadStatus] = useState("");
  // const [toggleShowTable, setToggleShowTable] = useState(false);
  // const [loading, setLoading] = useState(false);
  const [lastInvoiceLimit, setLastInvoiceLimit] = useState(200);

  const [StartDate, setStartDate] = useState(
    moment().add(-30, "days").format("YYYY-MM-DD"),
  );
  const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));

  // New filter states
  const [currCustomerFilter, setCurrCustomerFilter] = useState("");
  const [currAssignedStaffFilter, setCurrAssignedStaffFilter] = useState("");
  const [currBillStatusFilter, setCurrBillStatusFilter] = useState("");
  const [currPaymentStatusFilter, setCurrPaymentStatusFilter] = useState("");
  // const [currBusinessLineFilter, setCurrBusinessLineFilter] = useState("");

  // Dropdown data lists
  const [customerList, setCustomerList] = useState([]);
  const [assignedStaffList, setAssignedStaffList] = useState([]);
  const [billStatusList, setBillStatusList] = useState([]);
  const [paymentStatusList, setPaymentStatusList] = useState([]);
  // const [businessLineList, setBusinessLineList] = useState([]);

  // Memoize selected values
  const selectedCustomer = React.useMemo(() => {
    if (!customerList || customerList.length === 0) return null;
    if (!currCustomerFilter) return customerList[0]; // Default to "All Customers"
    return customerList.find((list) => list.id === currCustomerFilter) || customerList[0];
  }, [customerList, currCustomerFilter]);

  const selectedAssignedStaff = React.useMemo(() => {
    if (!assignedStaffList || assignedStaffList.length === 0) return null;
    if (!currAssignedStaffFilter) return assignedStaffList[0]; // Default to "All Staff"
    return assignedStaffList.find((list) => list.id === currAssignedStaffFilter) || assignedStaffList[0];
  }, [assignedStaffList, currAssignedStaffFilter]);

  const selectedBillStatus = React.useMemo(() => {
    if (!billStatusList || billStatusList.length === 0) return null;
    if (!currBillStatusFilter) return billStatusList[0]; // Default to "All Bill Status"
    return billStatusList.find((list) => list.id === currBillStatusFilter) || billStatusList[0];
  }, [billStatusList, currBillStatusFilter]);

  const selectedPaymentStatus = React.useMemo(() => {
    if (!paymentStatusList || paymentStatusList.length === 0) return null;
    if (!currPaymentStatusFilter) return paymentStatusList[0]; // Default to "All Payment Status"
    return paymentStatusList.find((list) => list.id === currPaymentStatusFilter) || paymentStatusList[0];
  }, [paymentStatusList, currPaymentStatusFilter]);

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const PrintPDFExcelExportFunction = (reportType) => {
    let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

    window.open(
      finalUrl +
        "?action=InvoiceListExport" +
        "&reportType=excel" +
        "&StartDate=" +
        StartDate +
        "&EndDate=" +
        EndDate +
        "&CustomerFilter=" +
        encodeURIComponent(currCustomerFilter) +
        "&AssignedStaffFilter=" +
        encodeURIComponent(currAssignedStaffFilter) +
        "&BillStatusFilter=" +
        encodeURIComponent(currBillStatusFilter) +
        "&PaymentStatusFilter=" +
        encodeURIComponent(currPaymentStatusFilter) +
        // "&BusinessLineFilter=" +
        // encodeURIComponent(currBusinessLineFilter) +
        "&UserId=" +
        UserInfo.UserId +
        "&RoleId=" +
        UserInfo.RoleId[0] +
        "&TimeStamp=" +
        Date.now(),
    );
  };

  /* =====End of Excel Export Code==== */

  const columnList = [
    {
      field: "rownumber",
      label: "SL",
      align: "center",
      width: "4%",
    },
    {
      field: "Name",
      label: "Name",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "BusinessUnit",
      label: "Business Unit",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "BudgetCode",
      label: "Budget Code",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "CustomerName",
      label: "Customer",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AccountingPeriod",
      label: "Invoice Month",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "DebitCredit",
      label: "Debit Credit",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "Description",
      label: "Description",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "JournalType",
      label: "Journal Type",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "TransactionDate",
      label: "Invoice Date",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "TransactionReference",
      label: "Invoice No",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode1",
      label: "Analysis Code 1",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode2",
      label: "Analysis Code 2",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode3",
      label: "Business Line",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode4",
      label: "Analysis Code 4",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode5",
      label: "Analysis Code 5",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode6",
      label: "Analysis Code 6",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode7",
      label: "Analysis Code 7",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode8",
      label: "Analysis Code 8",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode9",
      label: "Analysis Code 9",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },

    {
      field: "TransactionAmount",
      label: "Amount (USD)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "ExchangeRate",
      label: "Exchange Rate",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },

    {
      field: "BaseAmount",
      label: "Invoice Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "BaseAmountWithoutVat",
      label: "Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "VatAmount",
      label: "VAT (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },

    {
      field: "CurrencyCode",
      label: "Currency Code",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDate1",
      label: "General Date 1",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDate2",
      label: "General Date 2",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDate3",
      label: "General Date 3",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription9",
      label: "Report No",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription4",
      label: "General Description 4",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription11",
      label: "Buyer Name",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription2",
      label: "General Description 2",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription12",
      label: "General Description 12",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription13",
      label: "General Description 13",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription14",
      label: "Merchant Name",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription15",
      label: "General Description 15",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription16",
      label: "General Description 16",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription17",
      label: "Style Name",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription18",
      label: "PI No",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription19",
      label: "General Description 19",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "GeneralDescription20",
      label: "Service",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "CustomerUserName",
      label: "Assigned Staff",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "IsBilledText",
      label: "Billed",
      align: "center",
      visible: true,
      sort: false,
      filter: true,
    },
        {
      field: "IsPaidText",
      label: "Paid",
      align: "center",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "custom",
      label: "Action",
      width: "4%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
  ];
  if (bFirst) {
    /**First time call for datalist */
    getDataList();
     getCustomerList();
     getUserList();
     getBillStatusList();
     getPaymentStatusList();

    //  getBusinessLineList();
    setBFirst(false);
  }

  /**Get data for table list */
  function getDataList() {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      // InvoiceId: pInvoiceId,
      LastInvoiceLimit: lastInvoiceLimit,
      StartDate: StartDate,
      EndDate: EndDate,
      CustomerFilter: currCustomerFilter,
      AssignedStaffFilter: currAssignedStaffFilter,
      BillStatusFilter: currBillStatusFilter,
      PaymentStatusFilter: currPaymentStatusFilter,
      // BusinessLineFilter: currBusinessLineFilter,
    };
    // console.log('LoginUserInfo params: ', params);

    ExecuteQuery(serverpage, params);
  }

  /**Get filter dropdown options */
    function getCustomerList() {
      let params = {
        action: "CustomerList",
        lan: language(),
        UserId: UserInfo.UserId,
        CustomerGroupId: 0,
      };
  
      apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
        setCustomerList(
          [{ id: "", name: "All Customers" }].concat(res.data.datalist),
        );
  
      });
    }

  function getUserList() {
    let params = {
      action: "UserList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setAssignedStaffList([{ id: null, name: "All Staff" }].concat(res.data.datalist));

    });
  }

    function getBillStatusList() {
    let params = {
      action: "BillStatusList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setBillStatusList([{ id: null, name: "All" }].concat(res.data.datalist));

    });
  }

    function getPaymentStatusList() {
    let params = {
      action: "PaymentStatusList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setPaymentStatusList([{ id: null, name: "All" }].concat(res.data.datalist));

    });
  }

  // function getBusinessLineList() {
  //   let params = {
  //     action: "BusinessLineList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //   };

  //   apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
  //     setBusinessLineList([{ id: null, name: "All Business Lines" }].concat(res.data.datalist));

  //   });
  // }


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

        {/* {permissionType === 0 && (
          <DeleteOutline
            className={"table-delete-icon"}
            onClick={() => {
              deleteData(rowData);
            }}
          />
        )} */}
      </>
    );
  }

  const editData = (rowData) => {
    // console.log("rowData: ", rowData);
    // console.log("dataList: ", dataList);

    setCurrentRow(rowData);
    openModal();
  };

  // React.useEffect(()=>{
  //   getDataList();
  // },[lastInvoiceLimit]);

  // const addData = () => {
  //   // console.log("rowData: ", rowData);
  //   // console.log("dataList: ", dataList);

  //   setCurrentRow({
  //     id: "",
  //     CheckName: "",
  //   });
  //   openModal();
  // };

  function openModal() {
    setShowModal(true); //true=modal show, false=modal hide
  }

  function modalCallback(response) {
    //response = close, addedit
    // console.log('response: ', response);
    getDataList();
    setShowModal(false); //true=modal show, false=modal hide
  }

  const handleChange = (e) => {
    const { name, value } = e.target;

    if (value) {
      setLastInvoiceLimit(value);
    } else {
      setLastInvoiceLimit(0);
    }
  };
  // const deleteData = (rowData) => {
  //   swal({
  //     title: "Are you sure?",
  //     text: "Once deleted, you will not be able to recover this data!",
  //     icon: "warning",
  //     buttons: {
  //       confirm: {
  //         text: "Yes",
  //         value: true,
  //         visible: true,
  //         className: "",
  //         closeModal: true,
  //       },
  //       cancel: {
  //         text: "No",
  //         value: null,
  //         visible: true,
  //         className: "",
  //         closeModal: true,
  //       },
  //     },
  //     dangerMode: true,
  //   }).then((allowAction) => {
  //     if (allowAction) {
  //       deleteApi(rowData);
  //     }
  //   });
  // };

  // function deleteApi(rowData) {
  //   let params = {
  //     action: "deleteData",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //     rowData: rowData,
  //   };

  //   // apiCall.post("productgroup", { params }, apiOption()).then((res) => {
  //   apiCall.post(serverpage, { params }, apiOption()).then((res) => {
  //     // console.log('res: ', res);
  //     props.openNoticeModal({
  //       isOpen: true,
  //       msg: res.data.message,
  //       msgtype: res.data.success,
  //     });
  //     getDataList();
  //   });
  // }

  // const handleFileChange = (e) => {
  //   setSelectedFile(e.target.files[0]);
  //   setUploadStatus("");
  // };

  const handleChangeFilterDate = (e) => {
    const { name, value } = e.target;
    if (name === "StartDate") {
      setStartDate(value);
    }

    if (name === "EndDate") {
      setEndDate(value);
    }
  };

 

  const handleChangeFilterDropDown = (name, value) => {
    console.log('name, value: ', name, value);
    if (name === "customerFilter") {
      setCurrCustomerFilter(value);
    }
    if (name === "assignedStaffFilter") {
      setCurrAssignedStaffFilter(value);
    }
    if (name === "billStatusFilter") {
      setCurrBillStatusFilter(value);
    }
    if (name === "paymentStatusFilter") {
      setCurrPaymentStatusFilter(value);
    }
    // if (name === "businessLineFilter") {
    //   setCurrBusinessLineFilter(value);
    // }
  };

  useEffect(() => {
    getDataList();
  }, [StartDate, EndDate, currCustomerFilter, currAssignedStaffFilter, currBillStatusFilter, currPaymentStatusFilter]);

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Invoice ❯ Invoice List
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        <div class="searchAdd">
          <div>
            <label>Invoice Start Date</label>
            <div class="">
              <input
                type="date"
                id="StartDate"
                name="StartDate"
                value={StartDate}
                onChange={(e) => handleChangeFilterDate(e)}
              />
            </div>
          </div>

          <div>
            <label>Invoice End Date</label>
            <div class="">
              <input
                type="date"
                id="EndDate"
                name="EndDate"
                value={EndDate}
                onChange={(e) => handleChangeFilterDate(e)}
              />
            </div>
          </div>

          <div>
            <label>Customer</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="customerFilter"
                name="customerFilter"
                autoComplete
                options={customerList ? customerList : []}
                getOptionLabel={(option) => option.name}
                value={selectedCustomer}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "customerFilter",
                    valueobj ? valueobj.id : ""
                  )
                }
                filterOptions={(options, state) => {
                  const inputValue = state.inputValue.toLowerCase();
                  if (!inputValue) return options.slice(0, 500);
                  return options
                    .filter((option) =>
                      option.name.toLowerCase().includes(inputValue)
                    )
                    .slice(0, 500);
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
            </div>
          </div>

          <div>
            <label>Assigned Staff</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="assignedStaffFilter"
                name="assignedStaffFilter"
                autoComplete
                options={assignedStaffList ? assignedStaffList : []}
                getOptionLabel={(option) => option.name}
                value={selectedAssignedStaff}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "assignedStaffFilter",
                    valueobj ? valueobj.id : ""
                  )
                }
                filterOptions={(options, state) => {
                  const inputValue = state.inputValue.toLowerCase();
                  if (!inputValue) return options.slice(0, 500);
                  return options
                    .filter((option) =>
                      option.name.toLowerCase().includes(inputValue)
                    )
                    .slice(0, 500);
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
            </div>
          </div>


          <div>
            <label>Bill Status</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="billStatusFilter"
                name="billStatusFilter"
                autoComplete
                options={billStatusList ? billStatusList : []}
                getOptionLabel={(option) => option.name}
                value={selectedBillStatus}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "billStatusFilter",
                    valueobj ? valueobj.id : ""
                  )
                }
                filterOptions={(options, state) => {
                  const inputValue = state.inputValue.toLowerCase();
                  if (!inputValue) return options.slice(0, 500);
                  return options
                    .filter((option) =>
                      option.name.toLowerCase().includes(inputValue)
                    )
                    .slice(0, 500);
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
            </div>
          </div>

          
          <div>
            <label>Payment Status</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="paymentStatusFilter"
                name="paymentStatusFilter"
                autoComplete
                options={paymentStatusList ? paymentStatusList : []}
                getOptionLabel={(option) => option.name}
                value={selectedPaymentStatus}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "paymentStatusFilter",
                    valueobj ? valueobj.id : ""
                  )
                }
                filterOptions={(options, state) => {
                  const inputValue = state.inputValue.toLowerCase();
                  if (!inputValue) return options.slice(0, 500);
                  return options
                    .filter((option) =>
                      option.name.toLowerCase().includes(inputValue)
                    )
                    .slice(0, 500);
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
            </div>
          </div>
          {/* <div>
            <label>Business Line</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="businessLineFilter"
                name="businessLineFilter"
                autoComplete
                options={businessLineList ? businessLineList : []}
                getOptionLabel={(option) => option.name}
                value={selectedBusinessLine}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "businessLineFilter",
                    valueobj ? valueobj.id : ""
                  )
                }
                filterOptions={(options, state) => {
                  const inputValue = state.inputValue.toLowerCase();
                  if (!inputValue) return options.slice(0, 500);
                  return options
                    .filter((option) =>
                      option.name.toLowerCase().includes(inputValue)
                    )
                    .slice(0, 500);
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
            </div>
          </div> */}

          <Button
            label={"Export"}
            class={"btnPrint"}
            onClick={PrintPDFExcelExportFunction}
          />
          {/* <div class="">
            <label>Show Last Number of Invoice: </label>
            <input
              type="text"
              id="lastInvoiceLimit"
              name="lastInvoiceLimit"
              value={lastInvoiceLimit}
              onChange={(e) => handleChange(e)}
            />

            <Button label={"Show"} class={"btnUpdate"} onClick={getDataList} />
          </div> */}
        </div>

        {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}
        {/* <div class="subContainer tableHeight">
              <div className="App"> */}
        <CustomTable
          columns={columnList}
          rows={dataList ? dataList : {}}
          actioncontrol={actioncontrol}
        />
        {/* </div>
            </div> */}
      </div>

      {showModal && (
        <InvoiceEditModal
          masterProps={props}
          currentRow={currentRow}
          modalCallback={modalCallback}
        />
      )}

      {/* <!-- BODY CONTAINER END --> */}
    </>
  );
};

export default InvoiceList;
