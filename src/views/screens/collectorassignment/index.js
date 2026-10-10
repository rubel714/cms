import React from "react";
import MRStepPage from "components/MRWorkflow/MRStepPage";
import CollectorAssignment from "./CollectorAssignment";

const Index = (props) => (
  <MRStepPage {...props} menukey="collectorassignment" render={(p) => <CollectorAssignment {...p} />} />
);

export default Index;
