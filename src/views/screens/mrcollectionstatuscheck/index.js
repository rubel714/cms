import React from "react";
import MRStepPage from "components/MRWorkflow/MRStepPage";
import MRCollectionStatusCheck from "./MRCollectionStatusCheck";

const Index = (props) => (
  <MRStepPage {...props} menukey="mrcollectionstatuscheck" render={(p) => <MRCollectionStatusCheck {...p} />} />
);

export default Index;
