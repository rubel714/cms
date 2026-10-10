import React from "react";
import MRStepScreen from "components/MRWorkflow/MRStepScreen";

const extraColumns = [
  { field: "CollectionStatusName", label: "Collection Status", width: "8%", filter: true },
  { field: "CollectedAmount", label: "Collected Amount", width: "7%", align: "right", type: "number" },
];

const MRCollectionStatusCheck = (props) => (
  <MRStepScreen
    {...props}
    serverpage="mrcollectionstatuscheck"
    title="MR Collection Status Check"
    stepId={5}
    extraColumns={extraColumns}
  />
);

export default MRCollectionStatusCheck;
