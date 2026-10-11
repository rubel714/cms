import React, { useEffect, useState } from "react";
import moment from "moment";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { TextField } from "@material-ui/core";
import MRStepScreen from "components/MRWorkflow/MRStepScreen";
import { apiCall, apiOption, LoginUserInfo, language } from "../../../actions/api";

const serverpage = "mrcollectionstatuscheck";

const extraColumns = [
  { field: "CollectionStatusName", label: "Collection Status", width: "8%", filter: true },
  { field: "CollectedAmount", label: "Collected Amount", width: "7%", align: "right", type: "number" },
];

const MRCollectionStatusCheck = (props) => {
  const [StatusList, setStatusList] = useState([]);

  useEffect(() => {
    const params = {
      action: "getCollectionStatusList",
      lan: language(),
      UserId: LoginUserInfo().UserId,
    };
    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      setStatusList(res.data.datalist || []);
    });
  }, []);

  const renderApproveFields = (values, setValue, errors) => (
    <div className="mrModalFields">
      <label>Collection Status *</label>
      <Autocomplete
        disableClearable
        className={"chosen_dropdown " + (errors.CollectionStatusId || "")}
        options={StatusList}
        getOptionLabel={(option) => (option && option.name) || ""}
        getOptionSelected={(option, value) => String(option.id) === String(value.id)}
        value={StatusList.find((s) => String(s.id) === String(values.CollectionStatusId)) || null}
        onChange={(event, valueobj) => setValue("CollectionStatusId", valueobj ? valueobj.id : "")}
        renderInput={(params) => (
          <TextField {...params} variant="standard" fullWidth placeholder="Select Status" />
        )}
      />

      <label>Collection Date *</label>
      <input
        type="date"
        className={errors.CollectionDate || ""}
        value={(values.CollectionDate || "").toString().slice(0, 10)}
        onChange={(e) => setValue("CollectionDate", e.target.value)}
      />

      <label>Collected Amount</label>
      <input
        type="number"
        value={values.CollectedAmount}
        onChange={(e) => setValue("CollectedAmount", e.target.value)}
      />
    </div>
  );

  const validateApprove = (values) => {
    const errors = {};
    if (!values.CollectionStatusId) errors.CollectionStatusId = "validation-style";
    if (!values.CollectionDate) errors.CollectionDate = "validation-style";
    return errors;
  };

  return (
    <MRStepScreen
      {...props}
      serverpage={serverpage}
      title="MR Collection Status Check"
      stepId={5}
      extraColumns={extraColumns}
      initApproveValues={(row) => ({
        CollectionStatusId: row.CollectionStatusId || "",
        CollectionDate: row.CollectionDate || moment().format("YYYY-MM-DD"),
        CollectedAmount: row.CollectedAmount ?? "",
      })}
      renderApproveFields={renderApproveFields}
      validateApprove={validateApprove}
    />
  );
};

export default MRCollectionStatusCheck;
