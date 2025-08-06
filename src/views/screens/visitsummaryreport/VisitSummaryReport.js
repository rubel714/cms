import React, { forwardRef, useRef, useEffect } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit } from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";
import moment from "moment";
import { Typography, TextField } from "@material-ui/core";

import Autocomplete from "@material-ui/lab/Autocomplete";
import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

const VisitSummaryReport = (props) => {
  const serverpage = "transactionreport"; // this is .php server page

  const permissionType = props.permissionType;
  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  // const [currentRow, setCurrentRow] = useState([]);
  // const [showModal, setShowModal] = useState(false); //true=show modal, false=hide modal

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  // const [columnList, setColumnList] = useState([]);

  const UserInfo = LoginUserInfo();
  const [currReportTypeId, setCurrReportTypeId] = useState(
    "VisitSummaryReport"
  );

  const [DepartmentList, setDepartmentList] = useState(null);
  const [currDepartmentId, setCurrDepartmentId] = useState(0);

  const [UserList, setUserList] = useState(null);
  const [currUserId, setCurrUserId] = useState(0);
  const [isdisablevisitorlist, setIsdisablevisitorlist] = useState(true);

  const [TransactionList, setTransactionList] = useState(null);
  const [currTransactionId, setCurrTransactionId] = useState(0);

  // const [StartDate, setStartDate] = useState(
  //   moment().subtract(30, "days").format("YYYY-MM-DD")
  // );
  // const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));

  let currDay = moment().format("DD");
  let currMonth = moment().format("MM");
  let currYear = moment().format("YYYY");

  let defaultStartDate = "";
  let defaultEndDate = "";
  if (currDay > 15) {
    //from curr month
    defaultStartDate = currYear + "-" + currMonth + "-01";
    defaultEndDate = currYear + "-" + currMonth + "-15";
  } else {
    let premontMonth = moment().subtract(1, "months").format("YYYY-MM-DD"); // Go pre month
    let myd = premontMonth.split("-");

    let preMonthLastDay = new Date(
      currYear + "-" + currMonth + "-00"
    ).getDate(); //return premonth last day
    // console.log('preMonthLastDay: ', preMonthLastDay);

    defaultStartDate = myd[0] + "-" + myd[1] + "-16";
    defaultEndDate = myd[0] + "-" + myd[1] + "-" + preMonthLastDay;
  }

  const [StartDate, setStartDate] = useState(defaultStartDate);
  const [EndDate, setEndDate] = useState(defaultEndDate);

  // const [StartDate, setStartDate] = useState(
  //   moment().subtract(30, "days").format("YYYY-MM-DD")
  // );
  // const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const ExcelGenerate = () => {
    // if (!chkValidation()) {
    //   return;
    // }

    let finalUrl =
      EXCEL_EXPORT_URL + "report/VisitSummaryReport_excel.php";
    window.open(
      finalUrl +
      "?DepartmentId=" +
      currDepartmentId +
      "&VisitorId=" +
      currUserId +
      "&StartDate=" +
      StartDate +
      "&EndDate=" +
      EndDate +
      "&TimeStamp=" +
      Date.now()
    );
  };

  const PDFGenerate = () => {
    // if (!chkValidation()) {
    //   return;
    // }

    let finalUrl = EXCEL_EXPORT_URL + "report/VisitSummaryReport_pdf.php";
    window.open(
      finalUrl +
      "?DepartmentId=" +
      currDepartmentId +
      "&VisitorId=" +
      currUserId +
      "&StartDate=" +
      StartDate +
      "&EndDate=" +
      EndDate +
      "&TimeStamp=" +
      Date.now()
    );
  };

  /* =====End of Excel Export Code==== */

  const handleChangeFilterDropDown = (name, value) => {
    // const { name, value } = e.target;

    if (name === "DepartmentId") {
      setCurrDepartmentId(value);
      getUser(value);
      // getTransactionList(value, 0, StartDate, EndDate);
    }

    if (name === "UserId") {
      setCurrUserId(value);
      //getTransactionList(currDepartmentId, value, StartDate, EndDate);
    }
    // if (name === "TransactionId") {
    //   setCurrTransactionId(value);
    // }
  };

  const handleChangeFilterDate = (e) => {
    const { name, value } = e.target;
    if (name === "StartDate") {
      setStartDate(value);
      //getTransactionList(currDepartmentId, currUserId, value, EndDate);
    }

    if (name === "EndDate") {
      setEndDate(value);
      // getTransactionList(currDepartmentId, currUserId, StartDate, value);
    }
  };

  function getDepartment() {
    let params = {
      action: "DepartmentList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setDepartmentList([{ id: 0, name: "All" }].concat(res.data.datalist));

      setCurrDepartmentId(0);
    });
  }

  function getUser(deptId) {
    let params = {
      action: "UserList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      DepartmentId: deptId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setUserList([{ id: 0, name: "All" }].concat(res.data.datalist));

      setCurrUserId(0);
    });
  }

  // function getTransactionList(deptId, visitorId, sDate, eDate) {
  //   let params = {
  //     action: "TransactionList",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     ClientId: UserInfo.ClientId,
  //     BranchId: UserInfo.BranchId,
  //     DepartmentId: deptId,
  //     VisitorId: visitorId,
  //     StartDate: sDate,
  //     EndDate: eDate,
  //   };

  //   apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
  //     setTransactionList([{ id: 0, name: "All" }].concat(res.data.datalist));

  //     setCurrTransactionId(0);
  //   });
  // }


  useEffect(() => {
    getDataList();
  }, [
    currDepartmentId,
    currUserId,
    StartDate,
    EndDate,
  ]);



  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "5%" },
    {
      field: "TransactionDate",
      label: "Visit Date",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "10%",
    },
    {
      field: "UserName",
      label: "Employee Name",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      // width: "20%"
    },
    {
      field: "CustomerName",
      label: "Customer Name",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "ContactPersonName",
      label: "Contact Name",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "12%",
    },
    {
      field: "ContactPersonDesignation",
      label: "Designation",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "12%",
    },
    {
      field: "SelfDiscussion",
      label: "Self Discussion",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "12%",
    },
    {
      field: "HOTAdvice",
      label: "HOT Advice",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "6%",
    },
  ];

  if (bFirst) {
    /**First time call for datalist */
    getDepartment();
    getUser(0);
    // getTransactionList(0, 0, StartDate, EndDate);
    setBFirst(false);
  }

  // const selectCurrentReport = (e) => {
  //   const { name, value } = e.target;

  //   setCurrReportTypeId(value);

  //   if (value == "CustomerVisitPunchLedger") {
  //     setIsdisablevisitorlist(true);
  //     setColumnList(columnListCustomerVisitPunchLedger);
  //   } else if (value == "CustomerVisitPunchSummary") {
  //     setIsdisablevisitorlist(true);
  //     setColumnList(columnListCustomerVisitPunchSummary);
  //   } else if (value == "CustomerVisitPunchSummary") {
  //     setIsdisablevisitorlist(true);
  //     setColumnList(columnListCustomerVisitPunchSummary);
  //   } else if (value == "VisitPlan") {
  //     setIsdisablevisitorlist(true);
  //     setColumnList(columnListVisitPlan);
  //   } else if (value == "ConveyanceReport") {
  //     setIsdisablevisitorlist(true);
  //     setColumnList(columnListConveyanceReport);
  //   } else if (value == "LocalConveyance") {
  //     setIsdisablevisitorlist(true);
  //     setColumnList(columnListLocalConveyance);
  //     setDateFilter();
  //   } else if (value == "VisitSummaryReport") {
  //     setIsdisablevisitorlist(true);
  //     setColumnList(columnListVisitSummaryReport);
  //   } else if (value == "MachineryServiceReport") {
  //     setIsdisablevisitorlist(false);
  //     setColumnList(columnListMachineryServiceReport);
  //     setDateFilter();
  //   }
  // };

  // const setDateFilter = () => {
  //   let currDay = moment().format("DD");
  //   let currMonth = moment().format("MM");
  //   let currYear = moment().format("YYYY");

  //   if (currDay > 15) {
  //     //from curr month
  //     setStartDate(currYear + "-" + currMonth + "-01");
  //     setEndDate(currYear + "-" + currMonth + "-15");
  //   } else {
  //     let premontMonth = moment().subtract(1, "months").format("YYYY-MM-DD"); // Go pre month
  //     let myd = premontMonth.split("-");

  //     let preMonthLastDay = new Date(
  //       currYear + "-" + currMonth + "-00"
  //     ).getDate(); //return premonth last day
  //     // console.log('preMonthLastDay: ', preMonthLastDay);

  //     setStartDate(myd[0] + "-" + myd[1] + "-16");
  //     setEndDate(myd[0] + "-" + myd[1] + "-" + preMonthLastDay);
  //   }
  // };

  // const chkValidation = () => {
  //   if (currReportTypeId == "CustomerVisitPunchLedger") {
  //   } else if (currReportTypeId == "CustomerVisitPunchSummary") {
  //   } else if (currReportTypeId == "CustomerVisitPunchSummary") {
  //   } else if (currReportTypeId == "VisitPlan") {
  //   } else if (currReportTypeId == "ConveyanceReport") {
  //   } else if (currReportTypeId == "LocalConveyance") {
  //     if (currUserId == 0) {
  //       alert("Select Sales Force");
  //       return false;
  //     }
  //   } else if (currReportTypeId == "VisitSummaryReport") {
  //   } else if (currReportTypeId == "MachineryServiceReport") {
  //     if (currTransactionId == 0) {
  //       alert("Select Visit");
  //       return false;
  //     }
  //   }

  //   return true;
  // };

  // const generateReport = () => {
  //   if (!chkValidation()) {
  //     return;
  //   }

  //   getDataList();
  // };

  // const [DepartmentList, setDepartmentList] = useState(null);
  // const [currDepartmentId, setCurrDepartmentId] = useState(0);

  // const [UserList, setUserList] = useState(null);
  // const [currUserId, setCurrUserId] = useState(0);

  // const [TransactionList, setTransactionList] = useState(null);
  // const [currTransactionId, setCurrTransactionId] = useState(0);

  // const [StartDate, setStartDate] = useState(
  //   moment().subtract(30, "days").format("YYYY-MM-DD")
  // );
  // const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));

  /**Get data for table list */
  function getDataList() {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      ReportTypeId: currReportTypeId,
      DepartmentId: currDepartmentId,
      VisitorId: currUserId,
      StartDate: StartDate,
      EndDate: EndDate,
      TransactionId: currTransactionId,
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
  //     DesignationName: "",
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
  //     console.log("res: ", res);
  //     props.openNoticeModal({
  //       isOpen: true,
  //       msg: res.data.message,
  //       msgtype: res.data.success,
  //     });
  //     getDataList();
  //   });
  // }

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Reports ❯ Visit Summary Report
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        {/* <div class="searchAdd">
 
          <Button
            disabled={permissionType}
            label={"ADD"}
            class={"btnAdd"}
            onClick={addData}
          />
        </div> */}

        <div class="searchAdd">
          <div>
            <label>Department</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="DepartmentId"
                name="DepartmentId"
                autoComplete
                options={DepartmentList ? DepartmentList : []}
                getOptionLabel={(option) => option.name}
                defaultValue={{ id: 0, name: "All" }}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "DepartmentId",
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

          <div>
            <label>Sales Force</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="UserId"
                name="UserId"
                autoComplete
                options={UserList ? UserList : []}
                getOptionLabel={(option) => option.name}
                defaultValue={{ id: 0, name: "All" }}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "UserId",
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


          <div>
            <label>Start Date</label>
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
            <label>End Date</label>
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


          {/* <div>
            <label class="pl-10">Visit List</label>
          </div>
          <div class="">
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="TransactionId"
              name="TransactionId"
              autoComplete
              options={TransactionList ? TransactionList : []}
              getOptionLabel={(option) => option.name}
              defaultValue={{ id: 0, name: "All" }}
              onChange={(event, valueobj) =>
                handleChangeFilterDropDown(
                  "TransactionId",
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
          </div> */}

          <Button label={"Excel"} class={"btnPrint"} onClick={ExcelGenerate} />
          <Button label={"PDF"} class={"btnClose"} onClick={PDFGenerate} />
        </div>

        {/* <div class="pad-10p">
          <div class="transaction-report-style">
            <div class="modalHeader">
              <h4>Transaction Reports</h4>
            </div>

            <div class="modalItem">
              <input
                type="radio"
                id="CustomerVisitPunchLedger"
                name="trans_report"
                value="CustomerVisitPunchLedger"
                class="reportslist"
                onClick={selectCurrentReport}
              />
              <label for="CustomerVisitPunchLedger">
                Visit Punch Ledger
              </label>
            </div>
            <div class="modalItem">
              <input
                type="radio"
                id="CustomerVisitPunchSummary"
                name="trans_report"
                value="CustomerVisitPunchSummary"
                class="reportslist"
                onClick={selectCurrentReport}
              />
              <label for="CustomerVisitPunchSummary">
                Visit Punch Summary
              </label>
            </div>
            <div class="modalItem">
              <input
                type="radio"
                id="VisitPlan"
                name="trans_report"
                value="VisitPlan"
                class="reportslist"
                onClick={selectCurrentReport}
              />
              <label for="VisitPlan">Visit Plan</label>
            </div>
            <div class="modalItem">
              <input
                type="radio"
                id="ConveyanceReport"
                name="trans_report"
                value="ConveyanceReport"
                class="reportslist"
                onClick={selectCurrentReport}
              />
              <label for="ConveyanceReport">Conveyance Report</label>
            </div>
            <div class="modalItem">
              <input
                type="radio"
                id="LocalConveyance"
                name="trans_report"
                value="LocalConveyance"
                class="reportslist"
                onClick={selectCurrentReport}
              />
              <label for="LocalConveyance">Local Conveyance</label>
            </div>
            <div class="modalItem">
              <input
                type="radio"
                id="VisitSummaryReport"
                name="trans_report"
                value="VisitSummaryReport"
                class="reportslist"
                onClick={selectCurrentReport}
              />
              <label for="VisitSummaryReport">Visit Summary Report</label>
            </div>
            <div class="modalItem">
              <input
                type="radio"
                id="MachineryServiceReport"
                name="trans_report"
                value="MachineryServiceReport"
                class="reportslist"
                onClick={selectCurrentReport}
              />
              <label for="MachineryServiceReport">
                Machinery Service Report
              </label>
            </div>
          </div>

          <div class="transaction-report-style">
            <div class="modalHeader">
              <h4>Report Filter</h4>
            </div>


            <Button
              label={"Generate Report"}
              class={"btnUpdate"}
              onClick={generateReport}
            />
          </div>
        </div> */}

        <div class="subContainer ">
          <div className="App">
            <CustomTable
              columns={columnList}
              rows={dataList ? dataList : {}}
            // actioncontrol={actioncontrol}
            />
          </div>
        </div>
      </div>
      {/* <!-- BODY CONTAINER END --> */}
    </>
  );
};

export default VisitSummaryReport;
