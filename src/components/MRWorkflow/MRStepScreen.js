import React, { useState } from "react";
import { Edit, Visibility } from "@material-ui/icons";
import { Button } from "../CustomControl/Button";
import CustomTable from "components/CustomTable/CustomTable";
import ExecuteQueryHook from "../hooks/ExecuteQueryHook";
import { apiCall, apiOption, LoginUserInfo, language } from "../../actions/api";
import ApproveRejectModal from "./ApproveRejectModal";
import ApprovalHistory from "./ApprovalHistory";
import MRHeaderView from "./MRHeaderView";
import MRAllocationView from "./MRAllocationView";

const baseColumns = [
  { field: "rownumber", label: "SL", align: "center", width: "2%" },
  { field: "MRNo", label: "MR", width: "6%", filter: true },
  { field: "RefNo", label: "Ref", width: "5%", filter: true },
  { field: "PaymentDate", label: "MR Issue Date", width: "6%", filter: true },
  { field: "CustomerCode", label: "Customer Code", width: "7%", filter: true },
  { field: "CustomerName", label: "Customer Name", filter: true },
  { field: "ChequeNumber", label: "Cheque Number", width: "7%", filter: true },
  { field: "BankName", label: "Bank Name", width: "8%", filter: true },
];

const currentStepColumn = { field: "CurrentStep", label: "Current Step", width: "10%", filter: true };

const amountColumn = {
  field: "PaymentReceiveAmount",
  label: "Payment Received",
  width: "8%",
  align: "right",
  filter: true,
  type: "number",
};

const actionColumn = { field: "custom", label: "Action", width: "4%", align: "center" };

/**
 * Generic screen for an approve/reject workflow step (list -> detail -> approve or reject).
 *
 * Props:
 *  serverpage, title, permissionType, openNoticeModal
 *  extraColumns: list columns shown before the amount column
 *  showAllocation: show allocated invoices and calculation (loads getDataSingle)
 *  approveLabel: text of the Approve button
 *  initApproveValues(row): initial values for the step's extra approve fields
 *  renderApproveFields(values, setValue, errors): extra fields inside the approve modal
 *  validateApprove(values): returns an object of field errors ({} when valid)
 */
const MRStepScreen = (props) => {
  const {
    serverpage,
    title,
    permissionType,
    extraColumns = [],
    showAllocation = false,
    stepId,
    approveLabel = "Approve",
    initApproveValues = () => ({}),
    renderApproveFields,
    validateApprove = () => ({}),
    renderBankEditor,
  } = props;

  const UserInfo = LoginUserInfo();
  const [bFirst, setBFirst] = useState(true);
  const [listedittoggle, setListedittoggle] = useState(true);
  const [currentRow, setCurrentRow] = useState({});
  const [items, setItems] = useState([]);
  const [modalMode, setModalMode] = useState(null);
  const [approveValues, setApproveValues] = useState({});
  const [approveErrors, setApproveErrors] = useState({});
  const { data: dataList, ExecuteQuery } = ExecuteQueryHook();

  const columnList = [...baseColumns, ...extraColumns, amountColumn, currentStepColumn, actionColumn];
  const isCurrentStep = String(currentRow.StepId) === String(stepId);
  const canAct = permissionType === 0 && isCurrentStep;

  if (bFirst) {
    getDataList();
    setBFirst(false);
  }

  function getDataList() {
    const params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
    };
    ExecuteQuery(serverpage, params);
  }

  function openDetail(rowData) {
    setCurrentRow(rowData);
    setItems([]);
    setListedittoggle(false);

    if (showAllocation) {
      const params = {
        action: "getDataSingle",
        lan: language(),
        UserId: UserInfo.UserId,
        id: rowData.id,
      };
      apiCall.post(serverpage, { params }, apiOption()).then((res) => {
        setItems(res.data.datalist?.items || []);
      });
    }
  }

  function backToList() {
    getDataList();
    setListedittoggle(true);
  }

  function actioncontrol(rowData) {
    const Icon = String(rowData.StepId) === String(stepId) ? Edit : Visibility;
    return <Icon className={"table-edit-icon"} onClick={() => openDetail(rowData)} />;
  }

  function saveBankFields() {
    const params = {
      action: "saveData",
      lan: language(),
      UserId: UserInfo.UserId,
      PaymentId: currentRow.id,
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
    setApproveValues(initApproveValues(currentRow));
    setApproveErrors({});
    setModalMode("approve");
  }

  function setApproveValue(name, value) {
    setApproveValues((prev) => ({ ...prev, [name]: value }));
    setApproveErrors((prev) => ({ ...prev, [name]: null }));
  }

  function confirmModal(comments) {
    if (modalMode === "approve") {
      const errors = validateApprove(approveValues);
      if (Object.keys(errors).length > 0) {
        setApproveErrors(errors);
        return;
      }
    }

    const params = {
      action: modalMode === "approve" ? "approveData" : "rejectData",
      lan: language(),
      UserId: UserInfo.UserId,
      PaymentId: currentRow.id,
      Comments: comments,
      ...(modalMode === "approve" ? { ...approveValues, rowData: currentRow } : {}),
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });

      if (res.data.success === 1) {
        setModalMode(null);
        backToList();
      }
    });
  }

  return (
    <>
      <div className="bodyContainer">
        <div className="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Money Receipt ❯ {title}
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
            <div className="searchAdd">
              <Button label={"Back to List"} class={"btnClose"} onClick={backToList} />
              {isCurrentStep && (
                <>
                  {renderBankEditor && (
                    <Button label={"Save"} class={"btnSave"} disabled={!canAct} onClick={saveBankFields} />
                  )}
                  <Button
                    label={"Reject"}
                    class={"btnReject"}
                    disabled={!canAct}
                    onClick={() => setModalMode("reject")}
                  />
                  <Button
                    label={approveLabel}
                    class={"btnUpdate"}
                    disabled={!canAct}
                    onClick={openApprove}
                  />
                </>
              )}
              {!isCurrentStep && (
                <span className="mrReadOnlyNote">
                  This MR is at {currentRow.CurrentStep}. View only.
                </span>
              )}
            </div>

            <MRHeaderView
              row={currentRow}
              bankEditor={canAct && renderBankEditor ? renderBankEditor(currentRow, setCurrentRow) : null}
            />

            {showAllocation && <MRAllocationView row={currentRow} items={items} />}

            <ApprovalHistory serverpage={serverpage} PaymentId={currentRow.id} />
          </>
        )}
      </div>

      {modalMode && (
        <ApproveRejectModal
          mode={modalMode}
          mrNo={currentRow.MRNo}
          confirmLabel={modalMode === "approve" ? approveLabel : undefined}
          onConfirm={confirmModal}
          onClose={() => setModalMode(null)}
        >
          {modalMode === "approve" &&
            renderApproveFields &&
            renderApproveFields(approveValues, setApproveValue, approveErrors)}
        </ApproveRejectModal>
      )}
    </>
  );
};

export default MRStepScreen;
