import React from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit, Send, Visibility } from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";
import CustomTable from "components/CustomTable/CustomTable";
import ApproveRejectModal from "components/MRWorkflow/ApproveRejectModal";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { TextField } from "@material-ui/core";
import moment from "moment";

const PaymentReceive = (props) => {
  const serverpage = "paymentreceive";
  const permissionType = props.permissionType;
  const UserInfo = LoginUserInfo();
  const { useState } = React;

  const [bFirst, setBFirst] = useState(true);
  const [currentRow, setCurrentRow] = useState({});
  const [listedittoggle, setListedittoggle] = useState(true);
  const [errorObject, setErrorObject] = useState({});
  const [submitRow, setSubmitRow] = useState(null);
  const { data: dataList, ExecuteQuery } = ExecuteQueryHook();

  const [CustomerList, setCustomerList] = useState(null);
  const [currCustomerId, setCurrCustomerId] = useState("");
  const [BankList, setBankList] = useState(null);
  const [currBankId, setCurrBankId] = useState("");
  const [CurrencyList, setCurrencyList] = useState(null);
  const [currCurrencyId, setCurrCurrencyId] = useState("");
  const [PaymentModeList, setPaymentModeList] = useState(null);
  const [currPaymentModeId, setCurrPaymentModeId] = useState("");
  const [CollectedMethodList, setCollectedMethodList] = useState(null);
  const [currCollectedMethodId, setCurrCollectedMethodId] = useState("");

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "2%" },
    {
      field: "MRNo",
      label: "MR",
      width: "5%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "RefNo",
      label: "Ref",
      width: "5%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "PaymentDate",
      label: "MR Issue Date",
      width: "5%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "CustomerCode",
      label: "Customer Code",
      width: "7%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "CustomerName",
      label: "Customer Name",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "ChequeNumber",
      label: "Cheque Number",
      width: "6%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "ChequeDate",
      label: "Cheque Date",
      width: "5%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "BankName",
      label: "Bank Name",
      width: "6%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "BankBranchName",
      label: "Bank Branch",
      width: "6%",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "PaymentReceiveAmount",
      label: "Payment Received",
      width: "8%",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    { field: "CurrentStep", label: "Current Step", width: "8%", align: "left", visible: true, sort: false, filter: true },
    {
      field: "custom",
      label: "Action",
      width: "5%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
  ];

  if (bFirst) {
    getDataList();
    setBFirst(false);
  }

  React.useEffect(() => {
    getCustomerList();
    getBankList();
    getCurrencyList();
    getPaymentModeList();
    getCollectedMethodList();
  }, []);

  const getSelectedOption = (list, id) => {
    if (!list || list.length === 0) return null;
    const selected = list.find(
      (item) => String(item.id ?? "") === String(id ?? "")
    );
    return selected || list[0];
  };

  const selectedCustomer = React.useMemo(() => {
    return getSelectedOption(CustomerList, currCustomerId);
  }, [CustomerList, currCustomerId]);

  const selectedBank = React.useMemo(() => {
    return getSelectedOption(BankList, currBankId);
  }, [BankList, currBankId]);

  const selectedCurrency = React.useMemo(() => {
    return getSelectedOption(CurrencyList, currCurrencyId);
  }, [CurrencyList, currCurrencyId]);

  const selectedPaymentMode = React.useMemo(() => {
    return getSelectedOption(PaymentModeList, currPaymentModeId);
  }, [PaymentModeList, currPaymentModeId]);

  const selectedCollectedMethod = React.useMemo(() => {
    return getSelectedOption(CollectedMethodList, currCollectedMethodId);
  }, [CollectedMethodList, currCollectedMethodId]);

  function getCustomerList() {
    const params = {
      action: "CustomerList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCustomerList(
        [{ id: "", name: "Select Customer" }].concat(res.data.datalist)
      );
    });
  }

  function getBankList() {
    const params = {
      action: "BankList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setBankList([{ id: "", name: "Select Bank" }].concat(res.data.datalist));
    });
  }

  function getCurrencyList() {
    const params = {
      action: "CurrencyList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCurrencyList(
        [{ id: "", name: "Select Currency" }].concat(res.data.datalist)
      );
    });
  }

  function getPaymentModeList() {
    const params = {
      action: "PaymentModeList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setPaymentModeList(
        [{ id: "", name: "Select Payment Mode" }].concat(res.data.datalist)
      );
    });
  }

  function getCollectedMethodList() {
    const params = {
      action: "CollectedMethodList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCollectedMethodList(
        [{ id: "", name: "Select Collected By" }].concat(res.data.datalist)
      );
    });
  }

  function getDataList() {
    const params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    ExecuteQuery(serverpage, params);
  }

  function actioncontrol(rowData) {
    const isDraft = String(rowData.StepId) === "1";
    return (
      <>
        {isDraft ? (
          <Edit
            className={"table-edit-icon"}
            onClick={() => {
              editData(rowData);
            }}
          />
        ) : (
          <Visibility
            className={"table-edit-icon"}
            titleAccess="View"
            onClick={() => {
              editData(rowData);
            }}
          />
        )}

        {permissionType === 0 && isDraft && (
          <Send
            className={"table-edit-icon"}
            titleAccess="Submit"
            onClick={() => {
              setSubmitRow(rowData);
            }}
          />
        )}

        {permissionType === 0 && isDraft && (
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
    const params = {
      action: "getNextMRNumber",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      setCurrentRow({
        id: "",
        PaymentDate: moment().format("YYYY-MM-DD"),
        CustomerId: "",
        CustomerGroupId: "",
        MRNo: res.data.MRNo,
        RefNo: "",
        BankId: "",
        ChequeNumber: "",
        ChequeDate: "",
        BankBranchName: "",
        PaymentModeId: "",
        CurrencyId: "",
        PaymentReceiveCurrencyAmount: 0,
        ExchangeRate: 1,
        Remarks: "",
        PaymentReceiveAmount: 0,
        CollectedMethodId: "",
        CollectionAddress: "",
        CollectionPersonName: "",
        CollectionPersonPhone: "",
        StatusId: 1,
      });
      setCurrCustomerId("");
      setCurrBankId("");
      setCurrCurrencyId("");
      setCurrPaymentModeId("");
      setCurrCollectedMethodId("");
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
    setCurrCustomerId(rowData.CustomerId);
    setCurrBankId(rowData.BankId);
    setCurrCurrencyId(rowData.CurrencyId);
    setCurrPaymentModeId(rowData.PaymentModeId);
    setCurrCollectedMethodId(rowData.CollectedMethodId);
    openModal();
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
    const params = {
      action: "deleteData",
      lan: language(),
      UserId: UserInfo.UserId,
      rowData: rowData,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });
      getDataList();
    });
  }

  function saveData() {
    if (!validateForm()) {
      return;
    }

    const data = { ...currentRow };
    const params = {
      action: "dataAddEdit",
      lan: language(),
      UserId: UserInfo.UserId,
      rowData: data,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });

      if (res.data.success === 1) {
        data.id = res.data.PaymentId;
        setCurrentRow(data);
      }
    });
  }

  function openSubmitFromForm() {
    if (!validateForm()) {
      return;
    }
    setSubmitRow(currentRow);
  }

  function submitAPICall(comments) {
    const params = {
      action: "submitData",
      lan: language(),
      UserId: UserInfo.UserId,
      rowData: submitRow,
      Comments: comments,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });

      if (res.data.success === 1) {
        setSubmitRow(null);
        hideModal();
      }
    });
  }

  const validateForm = () => {
    const validateFields = ["MRNo", "PaymentDate", "CustomerId"];
    const errorData = {};
    let isValid = true;

    validateFields.forEach((field) => {
      if (!currentRow[field]) {
        errorData[field] = "validation-style";
        isValid = false;
      }
    });

    setErrorObject(errorData);
    return isValid;
  };

  const handleChangeFilterDropDown = (name, value) => {
    const data = { ...currentRow };

    if (name === "CustomerId") {
      data.CustomerId = value;
      setCurrCustomerId(value);
    }
    if (name === "BankId") {
      data.BankId = value;
      setCurrBankId(value);
    }
    if (name === "CurrencyId") {
      data.CurrencyId = value;
      setCurrCurrencyId(value);
    }
    if (name === "PaymentModeId") {
      data.PaymentModeId = value;
      setCurrPaymentModeId(value);
    }
    if (name === "CollectedMethodId") {
      data.CollectedMethodId = value;
      setCurrCollectedMethodId(value);
    }

    setErrorObject({ ...errorObject, [name]: null });
    setCurrentRow(data);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    const data = { ...currentRow };
    data[name] = value;

    if (name === "PaymentReceiveCurrencyAmount" || name === "ExchangeRate") {
      const currencyAmount = parseFloat(data.PaymentReceiveCurrencyAmount) || 0;
      const exchangeRate = parseFloat(data.ExchangeRate) || 0;
      data.PaymentReceiveAmount = (currencyAmount * exchangeRate).toFixed(2);
    }

    setCurrentRow(data);
    setErrorObject({ ...errorObject, [name]: null });
  };

  const isReadOnly =
    permissionType !== 0 || (currentRow.StepId != null && String(currentRow.StepId) !== "1");

  return (
    <>
      <div class="bodyContainer">
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Money Receipt ❯ Payment Receive (MR)
          </h4>
        </div>

        {listedittoggle && (
          <div>
            <div class="searchAdd">
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
              <Button
                label={currentRow.id ? "Update" : "Save"}
                class={"btnSave"}
                disabled={isReadOnly}
                onClick={saveData}
              />
              <Button
                label={"Submit"}
                class={"btnUpdate"}
                disabled={!currentRow.id || isReadOnly}
                onClick={openSubmitFromForm}
              />
              {currentRow.id && String(currentRow.StepId) !== "1" && (
                <span className="mrReadOnlyNote">
                  This MR is at {currentRow.CurrentStep}. View only.
                </span>
              )}
            </div>

            <div class="allocateInvoiceForm">
                  <div class="aiField">
                    <label>MR *</label>
                    <input
                      type="text"
                      id="MRNo"
                      name="MRNo"
                      disabled={true}
                      class={errorObject.MRNo}
                      placeholder="Enter MR Number"
                      value={currentRow.MRNo}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Ref</label>
                    <input
                      type="text"
                      id="RefNo"
                      name="RefNo"
                      disabled={isReadOnly}
                      placeholder="Enter Ref Number"
                      value={currentRow.RefNo}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>MR Issue Date *</label>
                    <input
                      type="date"
                      id="PaymentDate"
                      name="PaymentDate"
                      disabled={isReadOnly}
                      class={errorObject.PaymentDate}
                      placeholder="Enter MR Issue Date"
                      value={currentRow.PaymentDate}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Customer *</label>
                    <Autocomplete
                      disableClearable
                      className={"chosen_dropdown " + (errorObject.CustomerId || "")}
                      id="CustomerId"
                      name="CustomerId"
                      autoComplete
                      disabled={isReadOnly}
                      options={CustomerList ? CustomerList : []}
                      getOptionLabel={(option) => (option && option.name) || ""}
                      getOptionSelected={(option, value) =>
                        String(option?.id ?? "") === String(value?.id ?? "")
                      }
                      value={selectedCustomer}
                      onChange={(event, valueobj) =>
                        handleChangeFilterDropDown(
                          "CustomerId",
                          valueobj ? valueobj.id : ""
                        )
                      }
                      filterOptions={(options, state) => {
                        const inputValue = state.inputValue.toLowerCase();
                        if (!inputValue) return options.slice(0, 500);
                        return options
                          .filter((option) =>
                            option.name.toLowerCase().includes(inputValue)
                          )
                          .slice(0, 500);
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
                  </div>
                  <div class="aiField">
                    <label>Bank</label>
                    <Autocomplete
                      disableClearable
                      className="chosen_dropdown"
                      id="BankId"
                      name="BankId"
                      autoComplete
                      disabled={isReadOnly}
                      options={BankList ? BankList : []}
                      getOptionLabel={(option) => (option && option.name) || ""}
                      getOptionSelected={(option, value) =>
                        String(option?.id ?? "") === String(value?.id ?? "")
                      }
                      value={selectedBank}
                      onChange={(event, valueobj) =>
                        handleChangeFilterDropDown(
                          "BankId",
                          valueobj ? valueobj.id : ""
                        )
                      }
                      renderOption={(option) => option.name}
                      renderInput={(params) => (
                        <TextField {...params} variant="standard" fullWidth />
                      )}
                    />
                  </div>
                  <div class="aiField">
                    <label>Branch Name</label>
                    <input
                      type="text"
                      id="BankBranchName"
                      name="BankBranchName"
                      disabled={isReadOnly}
                      placeholder="Enter Branch Name"
                      value={currentRow.BankBranchName}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Cheque Number</label>
                    <input
                      type="text"
                      id="ChequeNumber"
                      name="ChequeNumber"
                      disabled={isReadOnly}
                      placeholder="Enter Cheque Number"
                      value={currentRow.ChequeNumber}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Cheque/ FDD Date</label>
                    <input
                      type="date"
                      id="ChequeDate"
                      name="ChequeDate"
                      disabled={isReadOnly}
                      placeholder="Enter Cheque Date"
                      value={currentRow.ChequeDate}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Payment Mode</label>
                    <Autocomplete
                      disableClearable
                      className="chosen_dropdown"
                      id="PaymentModeId"
                      name="PaymentModeId"
                      autoComplete
                      disabled={isReadOnly}
                      options={PaymentModeList ? PaymentModeList : []}
                      getOptionLabel={(option) => (option && option.name) || ""}
                      getOptionSelected={(option, value) =>
                        String(option?.id ?? "") === String(value?.id ?? "")
                      }
                      value={selectedPaymentMode}
                      onChange={(event, valueobj) =>
                        handleChangeFilterDropDown(
                          "PaymentModeId",
                          valueobj ? valueobj.id : ""
                        )
                      }
                      renderOption={(option) => option.name}
                      renderInput={(params) => (
                        <TextField {...params} variant="standard" fullWidth />
                      )}
                    />
                  </div>
                  <div class="aiField">
                    <label>Currency</label>
                    <Autocomplete
                      disableClearable
                      className="chosen_dropdown"
                      id="CurrencyId"
                      name="CurrencyId"
                      autoComplete
                      disabled={isReadOnly}
                      options={CurrencyList ? CurrencyList : []}
                      getOptionLabel={(option) => (option && option.name) || ""}
                      getOptionSelected={(option, value) =>
                        String(option?.id ?? "") === String(value?.id ?? "")
                      }
                      value={selectedCurrency}
                      onChange={(event, valueobj) =>
                        handleChangeFilterDropDown(
                          "CurrencyId",
                          valueobj ? valueobj.id : ""
                        )
                      }
                      renderOption={(option) => option.name}
                      renderInput={(params) => (
                        <TextField {...params} variant="standard" fullWidth />
                      )}
                    />
                  </div>
                  <div class="aiField">
                    <label>Payment Amount in FC</label>
                    <input
                      type="number"
                      id="PaymentReceiveCurrencyAmount"
                      name="PaymentReceiveCurrencyAmount"
                      disabled={isReadOnly}
                      placeholder="Amount in FC"
                      value={currentRow.PaymentReceiveCurrencyAmount}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Exchange Rate</label>
                    <input
                      type="number"
                      id="ExchangeRate"
                      name="ExchangeRate"
                      disabled={isReadOnly}
                      placeholder="Enter Exchange Rate"
                      value={currentRow.ExchangeRate}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField aiFieldHighlight">
                    <label>Payment Amount in BDT</label>
                    <input
                      type="number"
                      id="PaymentReceiveAmount"
                      name="PaymentReceiveAmount"
                      disabled={true}
                      placeholder="Payment Received"
                      value={currentRow.PaymentReceiveAmount}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Collected By</label>
                    <Autocomplete
                      disableClearable
                      className="chosen_dropdown"
                      id="CollectedMethodId"
                      name="CollectedMethodId"
                      autoComplete
                      disabled={isReadOnly}
                      options={CollectedMethodList ? CollectedMethodList : []}
                      getOptionLabel={(option) => (option && option.name) || ""}
                      getOptionSelected={(option, value) =>
                        String(option?.id ?? "") === String(value?.id ?? "")
                      }
                      value={selectedCollectedMethod}
                      onChange={(event, valueobj) =>
                        handleChangeFilterDropDown(
                          "CollectedMethodId",
                          valueobj ? valueobj.id : ""
                        )
                      }
                      renderOption={(option) => option.name}
                      renderInput={(params) => (
                        <TextField {...params} variant="standard" fullWidth />
                      )}
                    />
                  </div>
                  <div class="aiField">
                    <label>Collection Person Name</label>
                    <input
                      type="text"
                      id="CollectionPersonName"
                      name="CollectionPersonName"
                      disabled={isReadOnly}
                      placeholder="Enter Collection Person Name"
                      value={currentRow.CollectionPersonName}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Collection Person Phone</label>
                    <input
                      type="text"
                      id="CollectionPersonPhone"
                      name="CollectionPersonPhone"
                      disabled={isReadOnly}
                      placeholder="Enter Collection Person Phone"
                      value={currentRow.CollectionPersonPhone}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField">
                    <label>Collection Address</label>
                    <input
                      type="text"
                      id="CollectionAddress"
                      name="CollectionAddress"
                      disabled={isReadOnly}
                      placeholder="Enter Collection Address"
                      value={currentRow.CollectionAddress}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
                  <div class="aiField wide">
                    <label>Remarks</label>
                    <input
                      type="text"
                      id="Remarks"
                      name="Remarks"
                      disabled={isReadOnly}
                      placeholder="Enter Remarks"
                      value={currentRow.Remarks}
                      onChange={(e) => handleChange(e)}
                    />
                  </div>
            </div>
          </>
        )}
      </div>

      {submitRow && (
        <ApproveRejectModal
          mode="submit"
          mrNo={submitRow.MRNo}
          onConfirm={submitAPICall}
          onClose={() => setSubmitRow(null)}
        />
      )}
    </>
  );
};

export default PaymentReceive;
