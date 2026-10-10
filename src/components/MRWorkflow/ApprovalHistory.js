import React, { useEffect, useState } from "react";
import CustomTable from "components/CustomTable/CustomTable";
import { apiCall, apiOption, LoginUserInfo, language } from "../../actions/api";

const columnList = [
  { field: "rownumber", label: "SL", align: "center", width: "3%" },
  { field: "StepName", label: "Step", width: "15%" },
  { field: "Action", label: "Action", width: "8%" },
  { field: "Comments", label: "Comments" },
  { field: "UserName", label: "User", width: "15%" },
  { field: "CreateTs", label: "Date Time", width: "12%" },
];

const ApprovalHistory = ({ serverpage, PaymentId }) => {
  const [rows, setRows] = useState([]);

  useEffect(() => {
    if (!PaymentId) return;

    const params = {
      action: "getApprovalLog",
      lan: language(),
      UserId: LoginUserInfo().UserId,
      PaymentId: PaymentId,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      setRows(res.data.datalist || []);
    });
  }, [serverpage, PaymentId]);

  return (
    <div className="mrSection">
      <h4>Approval History</h4>
      <CustomTable
        columns={columnList}
        rows={rows.length > 0 ? rows : {}}
        actioncontrol={() => null}
        ispagination={false}
      />
    </div>
  );
};

export default ApprovalHistory;
