import React from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit, Visibility } from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";
import AllocateInvoiceAddModal from "./AllocateInvoiceAddModal";
import ApproveRejectModal from "components/MRWorkflow/ApproveRejectModal";
import ApprovalHistory from "components/MRWorkflow/ApprovalHistory";
import MRHeaderView from "components/MRWorkflow/MRHeaderView";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

const AllocateInvoice = (props) => {
  const serverpage = "allocateinvoice"; // this is .php server page
  const permissionType = props.permissionType;

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [currentRow, setCurrentRow] = useState([]);
  const isCurrentStep = String(currentRow.StepId) === "6";
  const canEdit = permissionType === 0 && isCurrentStep;
  const [listedittoggle, setListedittoggle] = useState(true); //true=show list panel, false=show edit panel
  const { data: dataList, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const { data: manyDataList, ExecuteQuery: ExecuteQueryMany } = ExecuteQueryHook(); //Fetch data
  let UserInfo = LoginUserInfo();

  const [editableItems, setEditableItems] = useState([]);
  const [showInvoiceModal, setShowInvoiceModal] = useState(false);
  const [modalMode, setModalMode] = useState(null);

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "2%" },
    { field: "CurrentStep", label: "Current Step", width: "8%", align: "left", visible: true, sort: false, filter: true },
    { field: "MRNo", label: "MR", width: "5%", align: "left", visible: true, sort: false, filter: true },
    { field: "RefNo", label: "Ref", width: "5%", align: "left", visible: true, sort: false, filter: true },
    { field: "PaymentDate", label: "MR Issue Date", width: "5%", align: "left", visible: true, sort: false, filter: true },
    { field: "CustomerCode", label: "Customer Code", width: "7%", align: "left", visible: true, sort: false, filter: true },
    { field: "CustomerName", label: "Customer Name", align: "left", visible: true, sort: false, filter: true },
    { field: "ChequeNumber", label: "Cheque Number", width: "6%", align: "left", visible: true, sort: false, filter: true },
    { field: "ChequeDate", label: "Cheque Date", width: "5%", align: "left", visible: true, sort: false, filter: true },
    { field: "BankName", label: "Bank Name", width: "6%", align: "left", visible: true, sort: false, filter: true },
    { field: "BankBranchName", label: "Bank Branch", width: "6%", align: "left", visible: true, sort: false, filter: true },
    { field: "PaymentReceiveAmount", label: "Payment Received", width: "8%", align: "right", visible: true, sort: false, filter: true, type: "number" },
    { field: "RebateAmount", label: "Rebate Amount", width: "4%", align: "right", visible: true, sort: false, filter: true, type: "number" },
    { field: "CNAmount", label: "CN Amount", width: "4%", align: "right", visible: true, sort: false, filter: true, type: "number" },
    { field: "AitDeduction", label: "AIT Deduction", width: "4%", align: "right", visible: true, sort: false, filter: true, type: "number" },
    { field: "VatAmount", label: "VAT Amount", width: "4%", align: "right", visible: true, sort: false, filter: true, type: "number" },
    { field: "AdvanceAmount", label: "Advance Amount", width: "4%", align: "right", visible: true, sort: false, filter: true, type: "number" },
    { field: "DueAmount", label: "Due Amount", width: "4%", align: "right", visible: true, sort: false, filter: true, type: "number" },
    { field: "custom", label: "Action", width: "5%", align: "center", visible: true, sort: false, filter: false },
  ];

  if (bFirst) {
    /**First time call for datalist */
    getDataList();
    setBFirst(false);
  }

  React.useEffect(() => {
    if (manyDataList?.items) {
      setEditableItems(manyDataList.items);
    }
  }, [manyDataList]);

  // Recalculate amounts when editableItems changes
  React.useEffect(() => {
    setCurrentRow((prevRow) => recalculate({ ...prevRow }));
  }, [editableItems]);

  /** Amounts from percentages of the allocated total, then Advance/Due against Payment Received. */
  function recalculate(data) {
    const totalBase = editableItems.reduce((sum, item) => {
      return sum + (parseFloat(item.BaseAmount) || 0);
    }, 0);

    const rebatePercent = parseFloat(data.RebatePercent) || 0;
    const cnPercent = parseFloat(data.CNPercent) || 0;
    const aitPercent = parseFloat(data.AitPercent) || 0;
    const vatPercent = parseFloat(data.VatPercent) || 0;

    data.RebateAmount = ((totalBase * rebatePercent) / 100).toFixed(2);
    data.CNAmount = ((totalBase * cnPercent) / 100).toFixed(2);
    data.AitDeduction = ((totalBase * aitPercent) / 100).toFixed(2);
    data.VatAmount = ((totalBase * vatPercent) / 100).toFixed(2);

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
    const Icon = String(rowData.StepId) === "6" ? Edit : Visibility;
    return (
      <Icon
        className={"table-edit-icon"}
        onClick={() => {
          editData(rowData);
        }}
      />
    );
  }

  function openModal() {
    setListedittoggle(false);
  }

  function hideModal() {
    getDataList();
    setListedittoggle(true);
  }

  const editData = (rowData) => {
    setCurrentRow(rowData);
    setEditableItems([]);
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

  function saveAllocation() {
    let params = {
      action: "dataAddEdit",
      lan: language(),
      UserId: UserInfo.UserId,
      rowData: currentRow,
    };
    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });
    });
  }

  function openApprove() {
    if (editableItems.length == 0 && !(parseFloat(currentRow.AdvanceAmount) > 0)) {
      props.openNoticeModal({
        isOpen: true,
        msg: "No invoice allocated to approve.",
        msgtype: 0,
      });
      return;
    }
    setModalMode("approve");
  }

  function confirmModal(comments) {
    let params = {
      action: modalMode === "approve" ? "approveData" : "rejectData",
      lan: language(),
      UserId: UserInfo.UserId,
      PaymentId: currentRow.id,
      Comments: comments,
      rowData: currentRow,
    };
    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });

      if (res.data.success === 1) {
        setModalMode(null);
        hideModal();
      }
    });
  }

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;
    setCurrentRow(recalculate(data));
  };

  const manyColumnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    { field: "AccountingPeriod", label: "Acc. Period", width: "6%", align: "left", visible: true, sort: true, filter: true },
    { field: "TransactionDate", label: "Invoice Date", width: "8%", align: "left", visible: true, sort: true, filter: true },
    { field: "Description", label: "Description", align: "left", visible: true, sort: true, filter: true },
    { field: "TransactionReference", label: "Invoice Number", width: "15%", align: "left", visible: true, sort: true, filter: true },
    { field: "TransactionAmount", label: "Amount in FC", width: "7%", align: "right", visible: true, sort: true, filter: true, bottomcalc: "sum", type: "number" },
    { field: "ExchangeRate", label: "Ex. Rate", width: "7%", align: "right", visible: true, sort: true, filter: true, type: "number" },
    { field: "BaseAmount", label: "Amount in BDT", width: "10%", align: "right", visible: true, sort: true, filter: true, type: "number", bottomcalc: "sum" },
    { field: "custom", label: "Action", width: "5%", align: "center", visible: true, sort: false, filter: false },
  ];

  /** Action from table row buttons*/
  function actioncontrolmany(rowData) {
    return (
      <>
        {canEdit && (
          <DeleteOutline
            className={"table-delete-icon"}
            onClick={() => {
              deletePaymentItem(rowData);
            }}
          />
        )}
      </>
    );
  }

  const openInvoiceModal = () => {
    setShowInvoiceModal(true);
  };

  const invoiceModalCallback = (action) => {
    setShowInvoiceModal(false);
    if (action === "addedit") {
      getDataSingleFromServer(currentRow.id);
    }
  };

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Money Receipt ❯ Allocate Invoice
          </h4>
        </div>

        {listedittoggle && (
          <CustomTable
            columns={columnList}
            rows={dataList ? dataList : {}}
            actioncontrol={actioncontrol}
          />
        )}

        {!listedittoggle && (
          <>
            <div class="searchAdd">
              <Button
                label={"Back to List"}
                class={"btnClose"}
                onClick={hideModal}
              />

              {isCurrentStep && (
                <>
                  <Button
                    label={"Save"}
                    class={"btnSave"}
                    disabled={!canEdit}
                    onClick={saveAllocation}
                  />
                  <Button
                    label={"Reject"}
                    class={"btnReject"}
                    disabled={!canEdit}
                    onClick={() => setModalMode("reject")}
                  />
                  <Button
                    label={"Approve"}
                    class={"btnUpdate"}
                    disabled={!canEdit}
                    onClick={openApprove}
                  />
                </>
              )}
              {currentRow.id && !isCurrentStep && (
                <span className="mrReadOnlyNote">
                  This MR is at {currentRow.CurrentStep}. View only.
                </span>
              )}
            </div>

            <div>
              <MRHeaderView row={currentRow} />

              {isCurrentStep && (
                <div class="searchAdd">
                  <Button
                    label={"Add Invoice"}
                    class={"btnSave"}
                    disabled={!canEdit}
                    onClick={openInvoiceModal}
                  />
                </div>
              )}
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
                  value={currentRow.PaymentReceiveAmount}
                  onChange={(e) => handleChange(e)}
                />
                <label></label>
                <div></div>
                <label></label>
                <div></div>

                <label>Rebate %</label>
                <input
                  type="number"
                  id="RebatePercent"
                  name="RebatePercent"
                  disabled={!canEdit}
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
                  disabled={!canEdit}
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
                  placeholder="CN Amount"
                  value={currentRow.CNAmount}
                  onChange={(e) => handleChange(e)}
                />
                <label></label>
                <div></div>
                <label></label>
                <div></div>

                <label>AIT %</label>
                <input
                  type="number"
                  id="AitPercent"
                  name="AitPercent"
                  disabled={!canEdit}
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
                  disabled={!canEdit}
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
                  placeholder="Due Amount"
                  value={currentRow.DueAmount}
                  onChange={(e) => handleChange(e)}
                />
              </div>

              <ApprovalHistory serverpage={serverpage} PaymentId={currentRow.id} />
            </div>
          </>
        )}
      </div>
      {/* <!-- BODY CONTAINER END --> */}

      {showInvoiceModal && (
        <AllocateInvoiceAddModal
          currentRow={currentRow}
          modalCallback={invoiceModalCallback}
          masterProps={props}
        />
      )}

      {modalMode && (
        <ApproveRejectModal
          mode={modalMode}
          mrNo={currentRow.MRNo}
          onConfirm={confirmModal}
          onClose={() => setModalMode(null)}
        />
      )}
    </>
  );
};

export default AllocateInvoice;
