import React from "react";

export const ViewField = ({ label, value, wide, highlight }) => (
  <div className={"aiField" + (wide ? " wide" : "") + (highlight ? " aiFieldHighlight" : "")}>
    <label>{label}</label>
    <input type="text" disabled={true} value={value ?? ""} />
  </div>
);

/** Read-only MR header. bankEditor replaces Bank and the fields after it when a step can change them. */
const MRHeaderView = ({ row, bankEditor }) => {
  const customer = [row.CustomerCode, row.CustomerName].filter(Boolean).join(" - ");

  return (
    <>
      <div className="allocateInvoiceForm">
        <ViewField label="MR" value={row.MRNo} />
        <ViewField label="Ref" value={row.RefNo} />
        <ViewField label="MR Issue Date" value={row.PaymentDate} />
        <ViewField label="Customer" value={customer} />
        {bankEditor || (
          <>
            <ViewField label="Bank" value={row.BankName} />
            <ViewField label="Branch Name" value={row.BankBranchName} />
            <ViewField label="Cheque Number" value={row.ChequeNumber} />
            <ViewField label="Cheque/ FDD Date" value={row.ChequeDate} />
            <ViewField label="Payment Mode" value={row.PaymentModeName} />
            <ViewField label="Currency" value={row.CurrencyName} />
            <ViewField label="Payment Amount in FC" value={row.PaymentReceiveCurrencyAmount} />
            <ViewField label="Exchange Rate" value={row.ExchangeRate} />
            <ViewField label="Payment Amount in BDT" value={row.PaymentReceiveAmount} highlight />
            <ViewField label="Collected By" value={row.CollectedMethod} />
            <ViewField label="Collection Person Name" value={row.CollectionPersonName} />
            <ViewField label="Collection Person Phone" value={row.CollectionPersonPhone} />
            <ViewField label="Collection Address" value={row.CollectionAddress} />
            <ViewField label="Remarks" value={row.Remarks} wide />
          </>
        )}
      </div>

      {row.CollectionStatusId && (
        <div className="mrSection">
          <h4>Collection Status</h4>
          <div className="allocateInvoiceForm">
            <ViewField label="Collection Status" value={row.CollectionStatusName} />
            <ViewField label="Collection Date" value={row.CollectionDate} />
          </div>
        </div>
      )}
    </>
  );
};

export default MRHeaderView;
