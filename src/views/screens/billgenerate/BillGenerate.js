import React, { forwardRef, useRef } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit } from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";
import InvoiceAddModal from "./InvoiceAddModal";

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
import moment from "moment";

const BillGenerate = (props) => {
  const serverpage = "billgenerate"; // this is .php server page
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

  // const [BankList, setBankList] = useState(null);
  // const [currBankId, setCurrBankId] = useState("");

  const [editableItems, setEditableItems] = useState([]);
  const [showInvoiceModal, setShowInvoiceModal] = useState(false);

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const PDFGenerate = () => {
    let finalUrl = EXCEL_EXPORT_URL + "report/GenerateBill.php";
    window.open(
      finalUrl + "?BillId=" + currentRow.id + "&TimeStamp=" + Date.now(),
    );
  };

  /* =====End of Excel Export Code==== */

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    {
      field: "BillNumber",
      label: "Ref. No",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "BillDate",
      label: "Bill Date",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "CustomerCode",
      label: "Customer Code",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "CustomerName",
      label: "Customer Name",
      // width: "7%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "custom",
      label: "Action",
      width: "8%",
      align: "left",
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
    getCustomerList();
  }, []);

  React.useEffect(() => {
    if (manyDataList?.items) {
      setEditableItems(manyDataList.items);
    }
  }, [manyDataList]);

  // Memoize selected customer to avoid expensive findIndex on every render
  const selectedCustomer = React.useMemo(() => {
    if (!CustomerList || !currCustomerId) return null;
    return CustomerList.find((list) => list.id === currCustomerId) || null;
  }, [CustomerList, currCustomerId]);

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
      action: "getNextBillNumber",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      // console.log("res: ", res.data.NextBillNumber);
      setCurrentRow({
        id: "",
        BillDate: moment().format("YYYY-MM-DD"),
        CustomerId: "",
        BillNumber: res.data.NextBillNumber,
        Remarks: "",
        StatusId: 1,
        RebateAmount: "",
        VATAmount: "",
        TaxAmount: "",
        Items: [],
      });
      setEditableItems([]);
      setCurrCustomerId("");
      openModal();
    });
  };

  function openModal() {
    setListedittoggle(false);
  }

  function hideModal() {
    getDataList();
    setListedittoggle(true);
  }

  const editData = (rowData) => {
    setCurrentRow(rowData);
    setCurrCustomerGroupId(rowData.CustomerGroupId);
    setCurrCustomerId(rowData.CustomerId);
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

  const deleteBillItem = (rowData) => {
    swal({
      title: "Are you sure?",
      text: "This invoice will be removed from the bill.",
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
          action: "deleteBillItem",
          lan: language(),
          UserId: UserInfo.UserId,
          BillId: currentRow.id,
          BillItemId: rowData.BillItemId,
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

  
  function addEditInvoice() {
    addEditAPICall(1);
  }

  function postInvoice() {

    if(editableItems.length == 0){
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
          data["id"] = res.data.BillId;
          setCurrentRow(data);
        }
      });
    }
  }

  const validateForm = () => {
    let validateFields = ["BillDate", "CustomerId"];
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
    // if (name === "CustomerGroupId") {
    //   data["CustomerGroupId"] = value;
    //   setCurrCustomerGroupId(value);
    // }

    if (name === "CustomerId") {
      data["CustomerId"] = value;
      setCurrCustomerId(value);
    }

    // if (name === "BankId") {
    //   data["BankId"] = value;
    //   setCurrBankId(value);
    // }
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

  // const handleChangeCheck = (e, row) => {
  //   // console.log('e.target.checked: ', e.target.checked);
  //   const { name, value } = e.target;
  //   console.log("name, value: ", name, value);
  //   const checked = e.target.checked;

  //   const updatedItems = editableItems.map((item) => {
  //     if (item.PaymentItemId === row.PaymentItemId) {
  //       return { ...item, [name]: checked };
  //     }
  //     return item;
  //   });

  //   setEditableItems(updatedItems);
  //   console.log("updatedItems: ", updatedItems);
  // };

  const manyColumnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    {
      field: "TransactionDate",
      label: "Invoice Date",
      width: "5%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "TransactionReference",
      label: "Invoice Number",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
        {
      field: "GeneralDescription9",
      label: "Report Number",
      width: "8%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "GeneralDescription11",
      label: "Buyer Name",
      // width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    
    {
      field: "TransactionAmount",
      label: "Amount in FC",
      width: "5%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
      bottomcalc: "sum",
    },
    {
      field: "ExchangeRate",
      label: "Ex. Rate",
      align: "right",
      width: "4%",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "BaseAmount",
      label: "Amount in BDT",
      align: "right",
      width: "6%",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
      bottomcalc: "sum",
    },



    {
      field: "GeneralDescription17",
      label: "Style No.",
      // width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "OrderNumber",
      label: "Order No.",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "GeneralDescription14",
      label: "Merchandiser Name",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      // type: "number",
    },
    {
      field: "GeneralDescription20",
      label: "Service Type",
      width: "5%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      // type: "number",
    },

    {
      field: "custom",
      label: "Billed",
      width: "4%",
      align: "center",
      visible: currentRow.StatusId == 5 ? false : true,
      sort: false,
      filter: false,
    },
  ];

  /** Action from table row buttons*/
  function actioncontrolmany(rowData) {
    return (
      <div
        style={{
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          flexDirection: "row",
        }}
      >
        {/* <input
          type="number"
          id="PaymentAmount"
          name="PaymentAmount"
          // style={{width: "calc(100% - 10px)", marginRight: "5px"}}
          disabled={currentRow.StatusId == 5 ? true : false}
          // class={errorObject.PaymentDate}
          // placeholder="Enter Received Amount"
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
        /> */}

        {/* <input
          id="IsPaid"
          name="IsPaid"
          type="checkbox"
          disabled={currentRow.StatusId == 5 ? true : false}
          style={{ width: "30px", height: "18px", marginRight: "5px" }}
          checked={rowData.IsPaid}
          onChange={(e) => handleChangeCheck(e, rowData)}
        /> */}

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
        {permissionType === 0 && currentRow.StatusId == 1 && (
          <DeleteOutline
            className={"table-delete-icon"}
            onClick={() => {
              deleteBillItem(rowData);
            }}
          />
        )}
      </div>
    );
  }

  // function calculateTotalPaymentAmount() {
  //   let total = 0;

  //   editableItems.forEach((item) => {
  //     const paymentAmount = parseFloat(item.PaymentAmount) || 0;
  //     total += paymentAmount;
  //   });
  //   // return total;
  //   // let data = { ...currentRow };
  //   // data["InvoiceTotalAmount"] = total.toFixed(0);
  //   // setCurrentRow(data);
  //   return total.toFixed(0);
  // }

  // function calculateTotalAmount() {
  //     let totalTransactionAmount = 0;

  //     editableItems.forEach((item) => {
  //       const TransactionAmount = parseFloat(item.TransactionAmount) || 0;
  //       totalTransactionAmount += TransactionAmount;
  //     });
  //     // return total;
  //     // let data = { ...currentRow };
  //     // data["InvoiceTotalAmount"] = total.toFixed(0);
  //     // setCurrentRow(data);
  //     console.log("totalTransactionAmount: ", totalTransactionAmount);
  //     return totalTransactionAmount.toFixed(0);
  //   }

  const openInvoiceModal = () => {
    setShowInvoiceModal(true);
  };

  const invoiceModalCallback = (action) => {
    setShowInvoiceModal(false);
    if (action === "addedit") {
      // Refresh the invoice list after adding invoices
      getDataSingleFromServer(currentRow.id);
    }
  };

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Invoice ❯ Bill Generate
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
                label={"New Bill"}
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

              {/* {!currentRow.id && ( */}
              <Button
                label={currentRow.id ? "Update" : "Save"}
                class={"btnSave"}
                disabled={currentRow.StatusId == 5 ? true : false}
                onClick={addEditInvoice}
              />
              {/* )} */}

              {/* {currentRow.id && currentRow.StatusId == 1 && ( */}
                <Button
                  label={"Complete"} //update
                  class={"btnUpdate"}
                  disabled={!currentRow.id || currentRow.StatusId == 5 ? true : false}
                  onClick={postInvoice}
                />
              {/*  )} */}

               {/* {currentRow.id && currentRow.StatusId == 5 && ( */}
                <Button
                  label={"Export"}
                  class={"btnPrint"}
                  disabled={currentRow.id && currentRow.StatusId == 5 ? false : true}
                  onClick={PDFGenerate}
                />
              {/* )} */}
            </div>

            <div>
              <div class="fourColumnContainer pt-10 ">
                {/* <label>MR *</label>
                <input
                  type="text"
                  id="MRNo"
                  name="MRNo"
                  disabled={true}
                  class={errorObject.MRNo}
                  placeholder="Enter MR Number"
                  value={currentRow.MRNo}
                  onChange={(e) => handleChange(e)}
                /> */}

                <label>Ref. No</label>
                <input
                  type="text"
                  id="BillNumber"
                  name="BillNumber"
                  disabled={true}
                  // class={errorObject.BillNumber}
                  placeholder="Enter Ref No"
                  value={currentRow.BillNumber}
                  // onChange={(e) => handleChange(e)}
                />

                <label>Bill Date</label>
                <input
                  type="date"
                  id="BillDate"
                  name="BillDate"
                  disabled={currentRow.StatusId == 5? true: false}
                  class={errorObject.BillDate + " customer-dropdown-wide-1"}
                  placeholder="Enter Bill Date"
                  value={currentRow.BillDate}
                  onChange={(e) => handleChange(e)}
                />

                <label>Customer *</label>
                <Autocomplete
                  autoHighlight
                  disableClearable
                  className="chosen_dropdown customer-dropdown-wide-3"
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
                  getOptionLabel={(option) => option.name}
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
              
                <label>Remarks</label>
                <input
                  type="text"
                  id="Remarks"
                  name="Remarks"
                  class={"customer-dropdown-wide-3"}
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.PaymentDate}
                  placeholder="Enter Remarks"
                  value={currentRow.Remarks}
                  onChange={(e) => handleChange(e)}
                />
               
              </div>

      
              <div class="searchAdd">
                <Button
                  label={"Add Invoice"} //update
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
                <label>Rebate</label>
                <input
                  type="number"
                  id="RebateAmount"
                  name="RebateAmount"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.RebateAmount}
                  // placeholder="Enter Rebate Amount"
                  value={currentRow.RebateAmount}
                  onChange={(e) => handleChange(e)}
                />
              {/* </div>
              <div class="fourColumnContainer pt-10"> */}
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label>VAT</label>
                <input
                  type="number"
                  id="VATAmount"
                  name="VATAmount"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.VATAmount}
                  // placeholder="Enter Rebate Amount"
                  value={currentRow.VATAmount}
                  onChange={(e) => handleChange(e)}
                />
              {/* </div>
                <div class="fourColumnContainer pt-10"> */}
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label></label>
                <div></div>
                <label>TAX</label>
                <input
                  type="number"
                  id="TaxAmount"
                  name="TaxAmount"
                  disabled={currentRow.StatusId == 5 ? true : false}
                  // class={errorObject.TaxAmount}
                  // placeholder="Enter TAX Amount"
                  value={currentRow.TaxAmount}
                  onChange={(e) => handleChange(e)}
                />

 
              </div>




           
            </div>
          </>
        )}
      </div>
      {/* <!-- BODY CONTAINER END --> */}

      {/* Invoice Add Modal */}
      {showInvoiceModal && (
        <InvoiceAddModal
          currentRow={currentRow}
          modalCallback={invoiceModalCallback}
          masterProps={props}
        />
      )}
    </>
  );
};

export default BillGenerate;
