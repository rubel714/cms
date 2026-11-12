import React from "react";
import MRStepScreen from "components/MRWorkflow/MRStepScreen";

const extraColumns = [
  { field: "DueAmount", label: "Due Amount", width: "6%", align: "right", type: "number" },
  { field: "AdvanceAmount", label: "Advance Amount", width: "6%", align: "right", type: "number" },
];

const TransactionAudit = (props) => (
  <MRStepScreen
    {...props}
    serverpage="transactionaudit"
    title="Transaction Audit"
    stepId={8}
    extraColumns={extraColumns}
    showAllocation={true}
  />
);

export default TransactionAudit;
