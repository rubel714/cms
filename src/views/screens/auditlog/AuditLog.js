import React, { forwardRef, useRef,useEffect } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit,ViewList } from "@material-ui/icons";
import {Button}  from "../../../components/CustomControl/Button";
import moment from "moment";

import CustomTable from "components/CustomTable/CustomTable";
import { apiCall, apiOption , LoginUserInfo, language} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

import AuditLogAddEditModal from "./AuditLogAddEditModal";

const AuditLog = (props) => {
  const serverpage = "auditlog"; // this is .php server page
  const permissionType = props.permissionType;

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [currentRow, setCurrentRow] = useState([]);
  const [showModal, setShowModal] = useState(false); //true=show modal, false=hide modal
  
  const [StartDate, setStartDate] = useState(moment().subtract(30, "days").format("YYYY-MM-DD"));
  const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));
  const [QueryType, setQueryType] = useState(0);
  const [TableName, setTableName] = useState(0);

  const {isLoading, data: dataList, error, ExecuteQuery} = ExecuteQueryHook(); //Fetch data

  let UserInfo = LoginUserInfo();

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const PrintPDFExcelExportFunction = (reportType) => {
    let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

    window.open(
      finalUrl +
        "?action=AuditLogExport" +
        "&reportType=excel" +
        "&ClientId=" + UserInfo.ClientId +
        "&BranchId=" + UserInfo.BranchId +
        "&TimeStamp=" +
        Date.now()
    );
  };
  /* =====End of Excel Export Code==== */

  const handleChangeFilterDate = (e) => {
    const { name, value } = e.target;
    if (name === "StartDate") {
      setStartDate(value);
    }

    if (name === "EndDate") {
      setEndDate(value);
    }

  };

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "4%" },
    // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },
    {
      field: "LogDate",
      label: "Log Date",
      width:'12%',
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "UserName",
      label: "User Name",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      // width:'5%',
    },
    {
      field: "RemoteIP",
      label: "Remote IP",
      width: "15%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "QueryType",
      label: "Log Type",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "TableName",
      label: "Table Name",
      align: "left",
      width: "15%",
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

  useEffect(() => {
    getDataList();
  }, [StartDate, EndDate]);


  /**Get data for table list */
  function getDataList(){

    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      StartDate: StartDate,
      EndDate: EndDate,
      TableName: TableName,
      QueryType: QueryType,
    };
    // console.log('LoginUserInfo params: ', params);

    ExecuteQuery(serverpage, params);
  }

  /** Action from table row buttons*/
  function actioncontrol(rowData) {
    return (
      <>
         <ViewList
          className={"table-view-icon"}
          onClick={() => {
            editData(rowData);
          }}
        />

      </>
    );
  }

  // const addData = () => {

  //   setCurrentRow({
  //           id: "",
  //           ClientCode: "",
  //           ClientName: "",
  //           PhoneNo: "",
  //           Email: "",
  //           ClientAddress: "",
  //         });
  //   openModal();
  // };

  const editData = (rowData) => {
    console.log('rowData: ', rowData);

    setCurrentRow(rowData);
    openModal();
  };

  
  function openModal() {
    setShowModal(true); //true=modal show, false=modal hide
  }

  function modalCallback(response) {
    getDataList();
    setShowModal(false); //true=modal show, false=modal hide

  }

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

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Settings ❯ Audit Log
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        <div class="searchAdd">
          {/* <input type="text" placeholder="Search Product Group"/> */}
          <label></label>

          {/* <Button disabled={permissionType} label={"ADD"} class={"btnAdd"} onClick={addData} />
          <Button label={"Export"} class={"btnPrint"} onClick={PrintPDFExcelExportFunction} /> */}
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


        </div>

        {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}
        {/* <div class="subContainer tableHeight">
          <div className="App"> */}
            <CustomTable
              columns={columnList}
              rows={dataList?dataList:{}}
              actioncontrol={actioncontrol}
            />
          {/* </div>
        </div> */}
      </div>
      {/* <!-- BODY CONTAINER END --> */}


      {showModal && (<AuditLogAddEditModal masterProps={props} currentRow={currentRow} modalCallback={modalCallback}/>)}


    </>
  );
};

export default AuditLog;