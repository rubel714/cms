import React from "react";
import MRStepPage from "components/MRWorkflow/MRStepPage";
import MRVerification from "./MRVerification";

const Index = (props) => (
  <MRStepPage {...props} menukey="mrverification" render={(p) => <MRVerification {...p} />} />
);

export default Index;
