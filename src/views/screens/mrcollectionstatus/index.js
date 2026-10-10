import React from "react";
import MRStepPage from "components/MRWorkflow/MRStepPage";
import MRCollectionStatus from "./MRCollectionStatus";

const Index = (props) => (
  <MRStepPage {...props} menukey="mrcollectionstatus" render={(p) => <MRCollectionStatus {...p} />} />
);

export default Index;
