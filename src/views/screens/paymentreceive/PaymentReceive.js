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
import Autocomplete from "@material-ui/lab/Autocomplete";
import { Typography, TextField } from "@material-ui/core";

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
  const [currBankId, setCurrBankId] = useState(null);

  const [editableItems, setEditableItems] = useState([]);

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
      field: "PaymentDate",
      label: "Payment Date",
      width: "10%",
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
      field: "BankName",
      label: "Bank Name",
      width: "20%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "TotalPaymentAmount",
      label: "Total Amount",
      width: "12%",
      align: "right",
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
    // getUserList(null);
    // getBusinessLineList(null);
    // setCurrentRow(props.currentRow);
    // console.log("useEffect props.currentRow", props.currentRow);

    // setDataList(props.currentRow.UserMap);
  }, []);

  React.useEffect(() => {
    if (manyDataList?.items) {
      setEditableItems(manyDataList.items);
    }
  }, [manyDataList]);

  function getCustomerGroupList() {
    let params = {
      action: "CustomerGroupList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCustomerGroupList(
        [{ id: "", name: "Select Customer Group" }].concat(res.data.datalist)
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
        [{ id: "", name: "Select Customer" }].concat(res.data.datalist)
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
      PaymentDate: "",
      CustomerId: "",
      CustomerGroupId: "",
      BankId: "",
      TotalPaymentAmount: "",
      Remarks: "",
      Items: [],
    });

    setCurrCustomerGroupId("");
    setCurrCustomerId("");
    setCurrBankId("");

    openModal();
  };

  function openModal() {
    setListedittoggle(false);
    // setShowModal(true); //true=modal show, false=modal hide
  }

  function hideModal() {
    setListedittoggle(true);
    // setShowModal(true); //true=modal show, false=modal hide
  }

  const editData = (rowData) => {
    setCurrentRow(rowData);
    setCurrCustomerGroupId(rowData.CustomerGroupId);
    setCurrCustomerId(rowData.CustomerId);
    setCurrBankId(rowData.BankId);

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

  function addEditAPICall() {
    if (validateForm()) {
      let UserInfo = LoginUserInfo();
      let params = {
        action: "dataAddEdit",
        lan: language(),
        UserId: UserInfo.UserId,
        rowData: currentRow,
        items: editableItems,
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
          let data = { ...currentRow };
          data["id"] = res.data.PaymentId;
          setCurrentRow(data);
          console.log('data: ', data);

          getDataSingleFromServer(res.data.PaymentId);
        }
      });
    }
  }

  const validateForm = () => {
    let validateFields = ["PaymentDate", "CustomerId", "TotalPaymentAmount"];
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
    setErrorObject({ ...errorObject, [name]: null });
    setCurrentRow(data);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;
    setCurrentRow(data);

    setErrorObject({ ...errorObject, [name]: null });
  };

  const handleChangeMany = (e, row) => {
    const { name, value } = e.target;
    const updatedItems = editableItems.map((item) => {
      if (item.PaymentItemId === row.PaymentItemId) {
        return { ...item, [name]: value };
      }
      return item;
    });

    setEditableItems(updatedItems);
    // console.log('updatedItems: ', updatedItems);

    // setErrorObject({ ...errorObject, [name]: null });
  };

  const manyColumnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    {
      field: "AccountCode",
      label: "Customer Code",
      width: "8%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "Description",
      label: "Description",
      // width: "9%",
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
      field: "TransactionReference",
      label: "Report No",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "BaseAmount",
      label: "Invoice Amount",
      width: "8%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "TotalPaymentAmount",
      label: "Paid Amount",
      width: "7%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "DueAmount",
      label: "Due Amount",
      width: "7%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "custom",
      label: "Payment Amount",
      width: "8%",
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
        <input
          type="number"
          id="PaymentAmount"
          name="PaymentAmount"
          // class={errorObject.PaymentDate}
          placeholder="Enter Payment Amount"
          value={
            editableItems.length > 0
              ? editableItems[
                  editableItems.findIndex(
                    (list) => list.PaymentItemId == rowData.PaymentItemId
                  )
                ]?.PaymentAmount || ""
              : ""
          }
          // value={rowData.PaymentItemId}
          onChange={(e) => handleChangeMany(e, rowData)}
          // onBlur={(e) => handleChangeMany(e, rowData)}
        />

        {/* {permissionType === 0 && (
          // <Edit
          //   className={"table-edit-icon"}
          //   onClick={() => {
          //     editData(rowData);
          //   }}
          // />
        )} */}
        {/* 
        {permissionType === 0 && (
          // <DeleteOutline
          //   className={"table-delete-icon"}
          //   onClick={() => {
          //     deleteData(rowData);
          //   }}
          // />
        )} */}
      </>
    );
  }

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
            </div>

            <div>
              <div class="contactmodalBody pt-10">
                <label>Payement Date</label>
                <input
                  type="date"
                  id="PaymentDate"
                  name="PaymentDate"
                  class={errorObject.PaymentDate}
                  placeholder="Enter Payment Date"
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
                  autoHighlight
                  disableClearable
                  className="chosen_dropdown"
                  id="CustomerId"
                  name="CustomerId"
                  autoComplete
                  class={errorObject.CustomerId}
                  options={CustomerList ? CustomerList : []}
                  getOptionLabel={(option) => option.name}
                  defaultValue={{ id: 0, name: "Select Customer" }}
                  value={
                    CustomerList
                      ? CustomerList[
                          CustomerList.findIndex(
                            (list) => list.id === currCustomerId
                          )
                        ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
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

              <div class="contactmodalBody pt-10">
                <label>Bank</label>
                <Autocomplete
                  autoHighlight
                  disableClearable
                  className="chosen_dropdown"
                  id="BankId"
                  name="BankId"
                  autoComplete
                  // class={errorObject.BankId}
                  options={BankList ? BankList : []}
                  getOptionLabel={(option) => option.name}
                  defaultValue={{ id: 0, name: "Select Bank" }}
                  value={
                    BankList
                      ? BankList[
                          BankList.findIndex((list) => list.id === currBankId)
                        ]
                      : null
                  }
                  onChange={(event, valueobj) =>
                    handleChangeFilterDropDown(
                      "BankId",
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

                <label>Total Payment Amount *</label>
                <input
                  type="number"
                  id="TotalPaymentAmount"
                  name="TotalPaymentAmount"
                  class={errorObject.TotalPaymentAmount}
                  placeholder="Enter Total Payment Amount"
                  value={currentRow.TotalPaymentAmount}
                  onChange={(e) => handleChange(e)}
                />
              </div>

              <div class="contactmodalBody pt-10">
                <label>Remarks</label>
                <input
                  type="text"
                  id="Remarks"
                  name="Remarks"
                  // class={errorObject.PaymentDate}
                  placeholder="Enter Remarks"
                  value={currentRow.Remarks}
                  onChange={(e) => handleChange(e)}
                />
              </div>

              {currentRow.id && ( <div class="contactmodalBodys pt-10">
                <CustomTable
                  columns={manyColumnList}
                  rows={editableItems.length > 0 ? editableItems : {}}
                  actioncontrol={actioncontrolmany}
                />
              </div>)}

              <div class="modalItem">
                {currentRow.id && (
                  <Button
                    label={"Save"} //update
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
              </div>
            </div>
          </>
        )}
      </div>
      {/* <!-- BODY CONTAINER END --> */}
    </>
  );
};

export default PaymentReceive;
