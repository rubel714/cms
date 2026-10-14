import React, { useEffect, useState } from "react";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { TextField } from "@material-ui/core";
import MRStepScreen from "components/MRWorkflow/MRStepScreen";
import { apiCall, apiOption, LoginUserInfo, language } from "../../../actions/api";

const loadList = (action, setList, firstLabel) => {
  const params = {
    action: action,
    lan: language(),
    UserId: LoginUserInfo().UserId,
  };
  apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
    setList([{ id: "", name: firstLabel }].concat(res.data.datalist || []));
  });
};

const selectedOption = (list, id) => {
  if (!list || list.length === 0) return null;
  return list.find((item) => String(item.id ?? "") === String(id ?? "")) || list[0];
};

const DropDown = ({ label, list, value, onChange, disabled = false }) => (
  <div className="aiField">
    <label>{label}</label>
    <Autocomplete
      disabled={disabled}
      disableClearable
      className="chosen_dropdown"
      options={list || []}
      getOptionLabel={(option) => (option && option.name) || ""}
      getOptionSelected={(option, selected) => String(option?.id ?? "") === String(selected?.id ?? "")}
      value={selectedOption(list, value)}
      onChange={(event, valueobj) => onChange && onChange(valueobj ? valueobj.id : "")}
      renderInput={(params) => <TextField {...params} variant="standard" fullWidth />}
    />
  </div>
);

const BankFieldsEditor = ({ row, setRow }) => {
  const [BankList, setBankList] = useState(null);
  const [PaymentModeList, setPaymentModeList] = useState(null);
  const [CurrencyList, setCurrencyList] = useState(null);
  const [CollectedMethodList, setCollectedMethodList] = useState(null);

  useEffect(() => {
    loadList("BankList", setBankList, "Select Bank");
    loadList("PaymentModeList", setPaymentModeList, "Select Payment Mode");
    loadList("CurrencyList", setCurrencyList, "Select Currency");
    loadList("CollectedMethodList", setCollectedMethodList, "Select Collected By");
  }, []);

  const setField = (name, value) => {
    setRow((prev) => {
      const data = { ...prev, [name]: value };
      if (name === "PaymentReceiveCurrencyAmount" || name === "ExchangeRate") {
        const currencyAmount = parseFloat(data.PaymentReceiveCurrencyAmount) || 0;
        const exchangeRate = parseFloat(data.ExchangeRate) || 0;
        data.PaymentReceiveAmount = (currencyAmount * exchangeRate).toFixed(2);
      }
      return data;
    });
  };

  const onText = (e) => setField(e.target.name, e.target.value);

  return (
    <>
      <DropDown label="Bank" list={BankList} value={row.BankId} disabled />
      <div className="aiField">
        <label>Branch Name</label>
        <input type="text" name="BankBranchName" value={row.BankBranchName || ""} disabled />
      </div>
      <div className="aiField">
        <label>Cheque Number</label>
        <input type="text" name="ChequeNumber" value={row.ChequeNumber || ""} disabled />
      </div>
      <div className="aiField">
        <label>Cheque/ FDD Date</label>
        <input type="date" name="ChequeDate" value={(row.ChequeDate || "").toString().slice(0, 10)} disabled />
      </div>
      <DropDown label="Payment Mode" list={PaymentModeList} value={row.PaymentModeId} disabled />
      <DropDown label="Currency" list={CurrencyList} value={row.CurrencyId} disabled />
      <div className="aiField">
        <label>Payment Amount in FC</label>
        <input type="number" name="PaymentReceiveCurrencyAmount" value={row.PaymentReceiveCurrencyAmount} disabled />
      </div>
      <div className="aiField">
        <label>Exchange Rate</label>
        <input type="number" name="ExchangeRate" value={row.ExchangeRate} disabled />
      </div>
      <div className="aiField aiFieldHighlight">
        <label>Payment Amount in BDT</label>
        <input type="number" disabled={true} value={row.PaymentReceiveAmount} />
      </div>
      <DropDown
        label="Collected By"
        list={CollectedMethodList}
        value={row.CollectedMethodId}
        onChange={(id) => setField("CollectedMethodId", id)}
      />
      <div className="aiField">
        <label>Collection Person Name</label>
        <input type="text" name="CollectionPersonName" value={row.CollectionPersonName || ""} onChange={onText} />
      </div>
      <div className="aiField">
        <label>Collection Person Phone</label>
        <input type="text" name="CollectionPersonPhone" value={row.CollectionPersonPhone || ""} onChange={onText} />
      </div>
      <div className="aiField">
        <label>Collection Address</label>
        <input type="text" name="CollectionAddress" value={row.CollectionAddress || ""} onChange={onText} />
      </div>
      <div className="aiField wide">
        <label>Remarks</label>
        <input type="text" name="Remarks" value={row.Remarks || ""} onChange={onText} />
      </div>
    </>
  );
};

const CollectorAssignment = (props) => (
  <MRStepScreen
    {...props}
    serverpage="collectorassignment"
    title="Collector Assignment"
    stepId={3}
    renderBankEditor={(row, setRow) => <BankFieldsEditor row={row} setRow={setRow} />}
  />
);

export default CollectorAssignment;
