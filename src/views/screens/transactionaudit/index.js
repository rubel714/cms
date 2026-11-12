import React from "react";
import MRStepPage from "components/MRWorkflow/MRStepPage";
import TransactionAudit from "./TransactionAudit";

const Index = (props) => (
  <MRStepPage {...props} menukey="transactionaudit" render={(p) => <TransactionAudit {...p} />} />
);

export default Index;
