import React from "react";
import MRStepScreen from "components/MRWorkflow/MRStepScreen";

const extraColumns = [
  { field: "DueAmount", label: "Due Amount", width: "6%", align: "right", type: "number" },
  { field: "AdvanceAmount", label: "Advance Amount", width: "6%", align: "right", type: "number" },
];

const CollectionVerification = (props) => (
  <MRStepScreen
    {...props}
    serverpage="collectionverification"
    title="Collection Verification"
    stepId={7}
    extraColumns={extraColumns}
    showAllocation={true}
    approveLabel="Approve & Post"
  />
);

export default CollectionVerification;
