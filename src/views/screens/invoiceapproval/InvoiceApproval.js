import React, { useEffect } from "react";
import swal from "sweetalert";
import { CheckCircleOutline, HighlightOff } from "@material-ui/icons";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

import { TextField } from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";

/**AdjFlag value in t_invoiceitems that still awaits an approval decision */
const FLAG_PENDING = "Adjust";

const InvoiceApproval = (props) => {
  const serverpage = "invoiceapproval"; // this is .php server page

  const permissionType = props.permissionType;
  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const UserInfo = LoginUserInfo();

  const [currCustomerFilter, setCurrCustomerFilter] = useState("");
  const [customerList, setCustomerList] = useState([]);

  const selectedCustomer = React.useMemo(() => {
    if (!customerList || customerList.length === 0) return null;
    if (!currCustomerFilter) return customerList[0]; // Default to "All Customers"
    return (
      customerList.find((list) => list.id === currCustomerFilter) ||
      customerList[0]
    );
  }, [customerList, currCustomerFilter]);

  const pendingCount = React.useMemo(() => {
    if (!dataList || !Array.isArray(dataList)) return 0;
    return dataList.filter((row) => row.AdjFlag === FLAG_PENDING).length;
  }, [dataList]);

  const columnList = [
    {
      field: "rownumber",
      label: "SL",
      align: "center",
      width: "4%",
    },
    {
      field: "CustomerName",
      label: "Customer",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AccountingPeriod",
      label: "Invoice Month",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "TransactionDate",
      label: "Invoice Date",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "TransactionReference",
      label: "Invoice No",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "Description",
      label: "Description",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AnalysisCode3",
      label: "Business Line",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "OriginalBaseAmountWithoutVat",
      label: "Original Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "OriginalVatAmount",
      label: "Original VAT (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "OriginalBaseAmount",
      label: "Original Invoice Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "AdjBaseAmountWithoutVat",
      label: "Adjusted Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "AdjVatAmount",
      label: "Adjusted VAT (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "AdjBaseAmount",
      label: "Adjusted Invoice Amount (BDT)",
      align: "right",
      visible: true,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "AdjTransactionAmount",
      label: "Adjusted Amount (USD)",
      align: "right",
      visible: false,
      sort: false,
      filter: true,
      type: "number",
    },
    {
      field: "AdjReason",
      label: "Adjustment Reason",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AdjUserName",
      label: "Adjusted By",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AdjDateTimeText",
      label: "Adjusted On",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "CustomerUserName",
      label: "Assigned Staff",
      align: "left",
      visible: false,
      sort: false,
      filter: true,
    },
    {
      field: "IsApprovedText",
      label: "Approval Status",
      align: "center",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "ApproveUserName",
      label: "Approved By",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "ApproveDateTimeText",
      label: "Approved On",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "custom",
      label: "Action",
      width: "6%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
  ];

  if (bFirst) {
    /**First time call for datalist */
    getDataList();
    getCustomerList();
    setBFirst(false);
  }

  /**Get data for table list */
  function getDataList() {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      CustomerFilter: currCustomerFilter,
    };

    ExecuteQuery(serverpage, params);
  }

  /**Get filter dropdown options */
  function getCustomerList() {
    let params = {
      action: "CustomerList",
      lan: language(),
      UserId: UserInfo.UserId,
      CustomerGroupId: 0,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCustomerList(
        [
          { id: "", name: "All Customers" },
          { id: "-1", name: "[Not Assign Customer]" },
        ].concat(res.data.datalist),
      );
    });
  }

  /** Action from table row buttons*/
  function actioncontrol(rowData) {
    if (permissionType !== 0) return <></>;

    /**Approve/Reject is a final decision, so only a pending row is actionable */
    if (rowData.AdjFlag !== FLAG_PENDING) return <></>;

    return (
      <>
        <CheckCircleOutline
          className={"table-edit-icon"}
          titleAccess={"Approve"}
          onClick={() => {
            confirmApproval(rowData, true);
          }}
        />

        <HighlightOff
          className={"table-delete-icon"}
          titleAccess={"Reject"}
          onClick={() => {
            confirmApproval(rowData, false);
          }}
        />
      </>
    );
  }

  const confirmApproval = (rowData, isApprove) => {
    swal({
      title: "Are you sure?",
      text: isApprove
        ? "This adjusted invoice will be approved and cannot be changed afterwards."
        : "This adjusted invoice will be rejected and cannot be changed afterwards.",
      icon: "warning",
      buttons: {
        confirm: {
          text: "Yes",
          value: true,
          visible: true,
          className: "",
          closeModal: true,
        },
        cancel: {
          text: "No",
          value: null,
          visible: true,
          className: "",
          closeModal: true,
        },
      },
      dangerMode: !isApprove,
    }).then((allowAction) => {
      if (allowAction) {
        approvalApi(rowData, isApprove);
      }
    });
  };

  function approvalApi(rowData, isApprove) {
    let params = {
      action: isApprove ? "approveData" : "rejectData",
      lan: language(),
      UserId: UserInfo.UserId,
      rowData: {
        InvoiceItemId: rowData.InvoiceItemId,
      },
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });
      getDataList();
    });
  }

  const handleChangeFilterDropDown = (name, value) => {
    if (name === "customerFilter") {
      setCurrCustomerFilter(value);
    }
  };

  useEffect(() => {
    getDataList();
  }, [currCustomerFilter]);

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Invoice ❯ Invoice Approval
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        <div class="searchAdd">
          <div>
            <label>Customer</label>
            <div class="">
              <Autocomplete
                autoHighlight
                disableClearable
                className="chosen_dropdown"
                id="customerFilter"
                name="customerFilter"
                autoComplete
                options={customerList ? customerList : []}
                getOptionLabel={(option) => option.name}
                value={selectedCustomer}
                onChange={(event, valueobj) =>
                  handleChangeFilterDropDown(
                    "customerFilter",
                    valueobj ? valueobj.id : ""
                  )
                }
                filterOptions={(options, state) => {
                  const inputValue = state.inputValue.toLowerCase();
                  if (!inputValue) return options.slice(0, 500);
                  return options
                    .filter((option) =>
                      option.name.toLowerCase().includes(inputValue)
                    )
                    .slice(0, 500);
                }}
                renderOption={(option) => option.name}
                renderInput={(params) => (
                  <TextField
                    {...params}
                    variant="standard"
                    fullWidth
                    placeholder="Type to search..."
                  />
                )}
              />
            </div>
          </div>

          <div>
            <label>Pending Approval</label>
            <div class="">
              <span style={{ fontWeight: "bold", color: "#d32f2f" }}>
                {pendingCount}
              </span>
            </div>
          </div>
        </div>

        {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}
        <CustomTable
          columns={columnList}
          rows={dataList ? dataList : {}}
          actioncontrol={actioncontrol}
        />
      </div>

      {/* <!-- BODY CONTAINER END --> */}
    </>
  );
};

export default InvoiceApproval;
