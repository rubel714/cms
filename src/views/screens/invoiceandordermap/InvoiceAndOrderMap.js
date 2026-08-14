import React, { forwardRef, useRef } from "react";
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

import {
  Typography,
  Paper,
  Grid,
  Input,
  makeStyles,
  CircularProgress,
} from "@material-ui/core";

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
const InvoiceAndOrderMap = (props) => {
  const serverpage = "invoiceandordermap"; // this is .php server page

  const permissionType = props.permissionType;
  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  // const [currentRow, setCurrentRow] = useState([]);
  // const [showModal, setShowModal] = useState(false); //true=show modal, false=hide modal

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const UserInfo = LoginUserInfo();

  const classes = useStyles();
  const [selectedFile, setSelectedFile] = useState(null);
  // const [uploadStatus, setUploadStatus] = useState("");
  const [toggleShowTable, setToggleShowTable] = useState(false);
  const [loading, setLoading] = useState(false);
  const [mappedList, setMappedList] = useState([]);
  const [mapSummary, setMapSummary] = useState({
    TotalInFile: 0,
    TotalFound: 0,
    TotalNotFound: 0,
  });
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
      field: "TransactionReference",
      label: "Invoice No",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
      width: "20%",
    },
    {
      field: "OrderNumber",
      label: "Order Number",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "Status",
      label: "Status",
      align: "left",
      visible: true,
      sort: false,
      
      width: "10%",
      filter: true,
      format: (value) => {
        if (value === "Not Found") {
          return <span style={{ color: "red", fontWeight: "bold" }}>{value}</span>;
        }
        return value;
      },
    },
  ];
  if (bFirst) {
    /**First time call for datalist */
    // getDataList();
    setBFirst(false);
  }

  /**Get data for table list */
  function getDataList(pInvoiceId) {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      InvoiceId: pInvoiceId,
    };
    // console.log('LoginUserInfo params: ', params);

    ExecuteQuery(serverpage, params);
  }

  /** Action from table row buttons*/
  // function actioncontrol(rowData) {
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

  //       {permissionType === 0 && (
  //         <DeleteOutline
  //           className={"table-delete-icon"}
  //           onClick={() => {
  //             deleteData(rowData);
  //           }}
  //         />
  //       )}
  //     </>
  //   );
  // }

  // const addData = () => {
  //   // console.log("rowData: ", rowData);
  //   // console.log("dataList: ", dataList);

  //   setCurrentRow({
  //     id: "",
  //     CheckName: "",
  //   });
  //   openModal();
  // };

  // const editData = (rowData) => {
  //   // console.log("rowData: ", rowData);
  //   // console.log("dataList: ", dataList);

  //   setCurrentRow(rowData);
  //   openModal();
  // };

  // function openModal() {
  //   setShowModal(true); //true=modal show, false=modal hide
  // }

  // function modalCallback(response) {
  //   //response = close, addedit
  //   // console.log('response: ', response);
  //   getDataList();
  //   setShowModal(false); //true=modal show, false=modal hide
  // }

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

  const handleFileChange = (e) => {
    // setUploadStatus("");
    let file = e.target.files[0];
    if (file) {
      // let data = { ...currentRow };
      let reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = (event) => {
        // data['CoverFileUrlUpload'] = event.target.result;
        setSelectedFile(event.target.result);
      };
    }
  };

  // const handleUpload = () => {
  //   console.log('selectedFile: ', selectedFile);
  //   if (!selectedFile) {
  //     setUploadStatus("⚠️ Please select a file.");
  //     return;
  //   }
  //   setUploadStatus(`✅ File "${selectedFile.name}" uploaded successfully!`);
  // };

  function handleUpload() {
    // if (validateForm()) {

    //     console.log('currentRow: ', currentRow);
    if (selectedFile) {
      let params = {
        action: "dataAddEdit",
        lan: language(),
        UserId: UserInfo.UserId,
        ClientId: UserInfo.ClientId,
        BranchId: UserInfo.BranchId,
        rowData: selectedFile,
      };

      setLoading(true); //Show loader
      apiCall.post(serverpage, { params }, apiOption()).then((res) => {
        props.openNoticeModal({
          isOpen: true,
          msg: res.data.message,
          msgtype: res.data.success,
        });

        if (res.data.success === 1) {
          setToggleShowTable(true);
          setMappedList(res.data.datalist || []);
          setMapSummary({
            TotalInFile: res.data.TotalInFile || 0,
            TotalFound: res.data.TotalFound || 0,
            TotalNotFound: res.data.TotalNotFound || 0,
          });
        }
        setLoading(false);
      }).catch(() => {
        setLoading(false);
      });
    } else {
      props.openNoticeModal({
        isOpen: true,
        msg: "Please select file",
        msgtype: 0,
      });
    }
  }
  function uploadAnotherFile() {
    setToggleShowTable(false);
    setSelectedFile(null);
    setMappedList([]);
    setMapSummary({
      TotalInFile: 0,
      TotalFound: 0,
      TotalNotFound: 0,
    });
  }

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Invoice ❯ Invoice and Order Map
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        {/* <div class="searchAdd">
          <Button
            label={"Export"}
            class={"btnPrint"}
            onClick={PrintPDFExcelExportFunction}
          />
          <Button
            disabled={permissionType}
            label={"ADD"}
            class={"btnAdd"}
            onClick={addData}
          />
        </div> */}

        {!toggleShowTable && (
          <div className={classes.root}>
            <Paper className={classes.paper} elevation={3}>
              <Typography variant="h5" align="center" gutterBottom>
                Upload a File (.xlsx, .xls)
              </Typography>

              <Input
                type="file"
                accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                onChange={(e) => handleFileChange(e)}
                fullWidth
                className={classes.input}
                inputProps={{
                  style: {
                    // padding: "12px 8px", // vertical padding increases height
                    height: "100px",
                  },
                }}
              />

              <Grid container justify="center">
                {loading && (
                  <div style={{ textAlign: "center", marginTop: "5px" }}>
                    <CircularProgress size={24} />
                  </div>
                )}

                <Button
                  disabled={loading}
                  label={"Map Order Number"}
                  class={"btnAdd"}
                  onClick={handleUpload}
                />
              </Grid>
            </Paper>
          </div>
        )}

        {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}

        {toggleShowTable && (
          <div>
            <div style={{ textAlign: "center !important" }} class="searchAdd">
              <div
                style={{
                  marginBottom: "4px",
                  width: "45%",
                  fontWeight: "bold",
                }}
              >
                In file: {mapSummary.TotalInFile} | Found to update:{" "}
                {mapSummary.TotalFound} | Not found: {mapSummary.TotalNotFound}
              </div>

              <Button
                label={"Upload Another File"}
                class={"btnPrint"}
                onClick={uploadAnotherFile}
              />
            </div>

            {/* <div class="subContainer tableHeight">
              <div className="App"> */}
            <CustomTable
              columns={columnList}
              rows={mappedList ? mappedList : {}}
              // actioncontrol={actioncontrol}
            />
            {/* </div>
            </div> */}
          </div>
        )}
      </div>
      {/* <!-- BODY CONTAINER END --> */}
    </>
  );
};

export default InvoiceAndOrderMap;
