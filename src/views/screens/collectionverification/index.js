import React from "react";
import MRStepPage from "components/MRWorkflow/MRStepPage";
import CollectionVerification from "./CollectionVerification";

const Index = (props) => (
  <MRStepPage {...props} menukey="collectionverification" render={(p) => <CollectionVerification {...p} />} />
);

export default Index;
