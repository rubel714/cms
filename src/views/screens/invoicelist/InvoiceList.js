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
} from "@material-ui/core";
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
    moment().add(-30, "days").format("YYYY-MM-DD")
  );
  const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  // const PrintPDFExcelExportFunction = () => {
  //   let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

  //   window.open(
  //     finalUrl +
  //       "?action=CheckListExport" +
  //       "&reportType=excel" +
  //       "&ClientId=" +
  //       UserInfo.ClientId +
  //       "&BranchId=" +
  //       UserInfo.BranchId +
  //       "&TimeStamp=" +
  //       Date.now()
  //   );
  // };
  /* =====End of Excel Export Code==== */

  const columnList = [
    {
      field: "rownumber",
      label: "SL",
      align: "center",
      width: "5%",
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
      field: "AccountCode",
      label: "Customer Code",
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
      field: "BaseAmount",
      label: "Invoice Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "BaseAmountWithoutVat",
      label: "Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "VatAmount",
      label: "VAT (BDT)",
      align: "right",
      visible: true,
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
    };
    // console.log('LoginUserInfo params: ', params);

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

  useEffect(() => {
    getDataList();
  }, [StartDate, EndDate]);

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
