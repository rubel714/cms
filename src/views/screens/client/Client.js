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

import ClientAddEditModal from "./ClientAddEditModal";

const Client = (props) => {
  const serverpage = "client"; // this is .php server page
  const permissionType = props.permissionType;

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [currentRow, setCurrentRow] = useState([]);
  const [showModal, setShowModal] = useState(false); //true=show modal, false=hide modal

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  let UserInfo = LoginUserInfo();

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const PrintPDFExcelExportFunction = (reportType) => {
    let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

    window.open(
      finalUrl +
        "?action=ClientExport" +
        "&reportType=excel" +
        "&ClientId=" +
        UserInfo.ClientId +
        "&BranchId=" +
        UserInfo.BranchId +
        "&TimeStamp=" +
        Date.now()
    );
  };
  /* =====End of Excel Export Code==== */

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },
    {
      field: "CustomerCode",
      label: "Code",
      width: "7%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "CustomerName",
      label: "Client Name",
      // width: "9%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "CompanyAddress",
      label: "Address",
      width: "12%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "NatureOfBusiness",
      label: "Type",
      width: "6%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },



    {
      field: "CompanyName",
      label: "Contact Person",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },



    {
      field: "Designation",
      label: "Designation",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "7%",
    },

    {
      field: "ContactPhone",
      label: "Phone",
      width: "6%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },


    {
      field: "CompanyEmail",
      label: "Email",
      width: "7%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },




    // {
    //   field: "IsActiveName",
    //   label: "Status",
    //   width: "4%",
    //   align: "center",
    //   visible: true,
    //   sort: true,
    //   filter: true,
    // },
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
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
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
              // console.log(rowData);
              editData(rowData);
            }}
          />
        )}

        {permissionType === 0 && (
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
    setCurrentRow({
      id: "",
      CustomerCode: "",
      CustomerName: "",
      Designation: "",
      ContactPhone: "",
      CompanyName: "",
      NatureOfBusiness: "",
      CompanyEmail: "",
      CompanyAddress: "",
      IsActive: true,
    });
    openModal();
  };

  const editData = (rowData) => {
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

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Settings ❯ Client
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        <div class="searchAdd">
          {/* <input type="text" placeholder="Search Product Group"/> */}
          {/* <label></label> */}

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
        </div>

        {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}
        {/* <div class="subContainer">
          <div className="App tableHeight"> */}
            <CustomTable
              columns={columnList}
              rows={dataList ? dataList : {}}
              actioncontrol={actioncontrol}
            />
          {/* </div>
        </div> */}
      </div>
      {/* <!-- BODY CONTAINER END --> */}

      {showModal && (
        <ClientAddEditModal
          masterProps={props}
          currentRow={currentRow}
          modalCallback={modalCallback}
        />
      )}
    </>
  );
};

export default Client;
