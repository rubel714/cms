import React, { useState } from "react";

import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";

const toNumber = (value) => {
  const num = parseFloat(value);
  return isNaN(num) ? 0 : num;
};

const round2 = (value) => Math.round((value + Number.EPSILON) * 100) / 100;

const isEmpty = (value) => value === "" || value === null || value === undefined;

const isYes = (value) => `${value ?? ""}`.trim().toLowerCase() === "yes";

const isApproved = (value) =>
  `${value ?? ""}`.trim().toLowerCase() === "approved";

const InvoiceAmountEditModal = (props) => {
  const serverpage = "invoicelist"; // this is .php server page

  const [currentRow, setCurrentRow] = useState([]);
  const [errorObject, setErrorObject] = useState({});

  React.useEffect(() => {
    let data = { ...props.currentRow };

    /**First time adjustment starts from the original amounts */
    if (isEmpty(data.AdjBaseAmountWithoutVat)) {
      data.AdjBaseAmountWithoutVat = data.OriginalBaseAmountWithoutVat;
    }
    if (isEmpty(data.AdjVatAmount)) {
      data.AdjVatAmount = data.OriginalVatAmount;
    }

    setCurrentRow(data);
  }, []);

  /**A billed, paid or already approved invoice can no longer be adjusted */
  const isViewMode = React.useMemo(() => {
    return (
      isYes(props.currentRow.IsBilledText) ||
      isYes(props.currentRow.IsPaidText) ||
      isApproved(props.currentRow.AdjFlag)
    );
  }, [
    props.currentRow.IsBilledText,
    props.currentRow.IsPaidText,
    props.currentRow.AdjFlag,
  ]);

  const AdjBaseAmount = React.useMemo(() => {
    return round2(
      toNumber(currentRow.AdjBaseAmountWithoutVat) +
        toNumber(currentRow.AdjVatAmount)
    );
  }, [currentRow.AdjBaseAmountWithoutVat, currentRow.AdjVatAmount]);

  const AdjTransactionAmount = React.useMemo(() => {
    const rate = toNumber(currentRow.ExchangeRate);
    if (rate === 0) return 0;
    return round2(AdjBaseAmount / rate);
  }, [AdjBaseAmount, currentRow.ExchangeRate]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;
    setCurrentRow(data);

    setErrorObject({ ...errorObject, [name]: null });
  };

  const validateForm = () => {
    let errorData = {};
    let isValid = true;

    if (isEmpty(currentRow.AdjReason) || `${currentRow.AdjReason}`.trim() === "") {
      errorData.AdjReason = "validation-style";
      isValid = false;
    }

    setErrorObject(errorData);
    return isValid;
  };

  function addEditAPICall() {
    if (isViewMode || !validateForm()) {
      return;
    }

    let UserInfo = LoginUserInfo();
    let params = {
      action: "amountAddEdit",
      lan: language(),
      UserId: UserInfo.UserId,
      rowData: {
        InvoiceItemId: currentRow.InvoiceItemId,
        AdjBaseAmountWithoutVat: isEmpty(currentRow.AdjBaseAmountWithoutVat)
          ? 0
          : currentRow.AdjBaseAmountWithoutVat,
        AdjVatAmount: isEmpty(currentRow.AdjVatAmount)
          ? 0
          : currentRow.AdjVatAmount,
        AdjBaseAmount: AdjBaseAmount,
        AdjTransactionAmount: AdjTransactionAmount,
        AdjReason: `${currentRow.AdjReason}`.trim(),
      },
    };
    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.masterProps.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });

      if (res.data.success === 1) {
        props.modalCallback("addedit");
      }
    });
  }

  function modalClose() {
    props.modalCallback("close");
  }

  return (
    <>
      {/* <!-- AMOUNT MODAL START --> */}
      <div id="amountModal" class="modal">
        {/* <!-- Modal content --> */}
        <div class="modal-content">
          <div class="modalHeader">
            <h4>
              {isViewMode ? "View Amount" : "Edit Amount"}{" "}
              {currentRow.TransactionReference &&
                `- ${currentRow.TransactionReference}`}
            </h4>
          </div>

          <div class="contactmodalBody pt-10">
            <label>Original Amount (BDT)</label>
            <input
              type="number"
              id="OriginalBaseAmountWithoutVat"
              name="OriginalBaseAmountWithoutVat"
              disabled={true}
              value={currentRow.OriginalBaseAmountWithoutVat}
              onChange={(e) => handleChange(e)}
            />

            <label>Original VAT Amount (BDT)</label>
            <input
              type="number"
              id="OriginalVatAmount"
              name="OriginalVatAmount"
              disabled={true}
              value={currentRow.OriginalVatAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Original Invoice Amount (BDT)</label>
            <input
              type="number"
              id="OriginalBaseAmount"
              name="OriginalBaseAmount"
              disabled={true}
              value={currentRow.OriginalBaseAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Original Amount (USD)</label>
            <input
              type="number"
              id="OriginalTransactionAmount"
              name="OriginalTransactionAmount"
              disabled={true}
              value={currentRow.OriginalTransactionAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Exchange Rate</label>
            <input
              type="number"
              id="ExchangeRate"
              name="ExchangeRate"
              disabled={true}
              value={currentRow.ExchangeRate}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="contactmodalBody pt-10">
            <label>Adjusted Amount (BDT)</label>
            <input
              type="number"
              id="AdjBaseAmountWithoutVat"
              name="AdjBaseAmountWithoutVat"
              disabled={isViewMode}
              placeholder="Enter adjusted amount"
              value={currentRow.AdjBaseAmountWithoutVat}
              onChange={(e) => handleChange(e)}
            />

            <label>Adjusted VAT Amount (BDT)</label>
            <input
              type="number"
              id="AdjVatAmount"
              name="AdjVatAmount"
              disabled={isViewMode}
              placeholder="Enter adjusted vat amount"
              value={currentRow.AdjVatAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Adjusted Invoice Amount (BDT)</label>
            <input
              type="number"
              id="AdjBaseAmount"
              name="AdjBaseAmount"
              disabled={true}
              value={AdjBaseAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Adjusted Amount (USD)</label>
            <input
              type="number"
              id="AdjTransactionAmount"
              name="AdjTransactionAmount"
              disabled={true}
              value={AdjTransactionAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Adjustment Reason {!isViewMode && "*"}</label>
            <input
              type="text"
              id="AdjReason"
              name="AdjReason"
              class={errorObject.AdjReason}
              disabled={isViewMode}
              maxLength={200}
              placeholder="Enter adjustment reason"
              value={currentRow.AdjReason ? currentRow.AdjReason : ""}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="modalItem">
            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
            {!isViewMode && (
              <Button
                label={"Update"}
                class={"btnUpdate"}
                onClick={addEditAPICall}
              />
            )}
          </div>
        </div>
      </div>
      {/* <!-- AMOUNT MODAL END --> */}
    </>
  );
};

export default InvoiceAmountEditModal;
