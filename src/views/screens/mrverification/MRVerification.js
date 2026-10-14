import React from "react";
import MRStepScreen from "components/MRWorkflow/MRStepScreen";

const MRVerification = (props) => (
  <MRStepScreen
    {...props}
    serverpage="mrverification"
    title="MR Verification"
    stepId={2}
    approveLabel="Verified"
  />
);

export default MRVerification;
