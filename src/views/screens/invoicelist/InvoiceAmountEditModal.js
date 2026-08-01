import React, { useState } from "react";

import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";

const InvoiceAmountEditModal = (props) => {
  const serverpage = "invoicelist"; // this is .php server page

  const [currentRow, setCurrentRow] = useState([]);
  const [errorObject, setErrorObject] = useState({});

  React.useEffect(() => {
    setCurrentRow(props.currentRow);
  }, []);

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;
    setCurrentRow(data);

    setErrorObject({ ...errorObject, [name]: null });
  };

  function addEditAPICall() {
    let UserInfo = LoginUserInfo();
    let params = {
      action: "amountAddEdit",
      lan: language(),
      UserId: UserInfo.UserId,
      rowData: {
        InvoiceItemId: currentRow.InvoiceItemId,
        BaseAmountWithoutVat: currentRow.BaseAmountWithoutVat,
        VatAmount: currentRow.VatAmount,
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
              Edit Amount{" "}
              {currentRow.TransactionReference &&
                `- ${currentRow.TransactionReference}`}
            </h4>
          </div>

          <div class="contactmodalBody pt-10">
            <label>Invoice Amount (BDT)</label>
            <input
              type="number"
              id="BaseAmount"
              disabled={true}
              name="BaseAmount"
              placeholder="Enter invoice amount"
              value={currentRow.BaseAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Invoice Amount (BDT)</label>
            <input
              type="number"
              id="BaseAmountWithoutVat"
              name="BaseAmountWithoutVat"
              placeholder="Enter amount"
              value={currentRow.BaseAmountWithoutVat}
              onChange={(e) => handleChange(e)}
            />

            <label>VAT Amount (BDT)</label>
            <input
              type="number"
              id="VatAmount"
              name="VatAmount"
              placeholder="Enter vat amount"
              value={currentRow.VatAmount}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="modalItem">
            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
            <Button
              label={"Update"}
              class={"btnUpdate"}
              onClick={addEditAPICall}
            />
          </div>
        </div>
      </div>
      {/* <!-- AMOUNT MODAL END --> */}
    </>
  );
};

export default InvoiceAmountEditModal;
