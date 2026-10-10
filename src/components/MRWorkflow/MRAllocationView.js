import React from "react";
import CustomTable from "components/CustomTable/CustomTable";
const CalcField = ({ label, value, highlight }) => (
  <>
    <label>{label}</label>
    <input type="text" disabled={true} className={highlight ? "mrCalcHighlight" : ""} value={value ?? ""} />
  </>
);

const columnList = [
  { field: "rownumber", label: "SL", align: "center", width: "3%" },
  { field: "AccountingPeriod", label: "Acc. Period", width: "8%" },
  { field: "TransactionDate", label: "Invoice Date", width: "8%" },
  { field: "Description", label: "Description" },
  { field: "TransactionReference", label: "Invoice Number", width: "15%" },
  { field: "TransactionAmount", label: "Amount in FC", width: "8%", align: "right", type: "number", bottomcalc: "sum" },
  { field: "ExchangeRate", label: "Ex. Rate", width: "7%", align: "right", type: "number" },
  { field: "BaseAmount", label: "Amount in BDT", width: "10%", align: "right", type: "number", bottomcalc: "sum" },
];

/** Read-only allocated invoices and the calculation saved on Allocate Invoice. */
const MRAllocationView = ({ row, items }) => {
  const totalBase = items.reduce((sum, item) => sum + (parseFloat(item.BaseAmount) || 0), 0);

  return (
    <div className="mrSection">
      <h4>Allocated Invoices</h4>
      <CustomTable
        columns={columnList}
        rows={items.length > 0 ? items : {}}
        actioncontrol={() => null}
        ispagination={false}
      />

      <div className="mrCalcGrid pt-10">
        <CalcField label="Total Invoice Amount (BDT)" value={totalBase.toFixed(2)} />
        <CalcField label="Payment Received" value={row.PaymentReceiveAmount} highlight />
        <CalcField label="Rebate %" value={row.RebatePercent} />
        <CalcField label="Rebate Amount" value={row.RebateAmount} />
        <CalcField label="CN %" value={row.CNPercent} />
        <CalcField label="CN Amount" value={row.CNAmount} />
        <CalcField label="AIT %" value={row.AitPercent} />
        <CalcField label="AIT Deduction" value={row.AitDeduction} />
        <CalcField label="VAT %" value={row.VatPercent} />
        <CalcField label="VAT Amount" value={row.VatAmount} />
        <CalcField label="Advance/ Rem Advance Amount" value={row.AdvanceAmount} />
        <CalcField label="Due Amount" value={row.DueAmount} />
      </div>
    </div>
  );
};

export default MRAllocationView;
