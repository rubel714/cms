import React, { useEffect } from "react";

import CustomTable from "components/CustomTable/CustomTable";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

import { TextField } from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";
import moment from "moment";

const InvoiceAdjustmentReport = (props) => {
  const serverpage = "invoiceadjustmentreport"; // this is .php server page

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const UserInfo = LoginUserInfo();

  const [StartDate, setStartDate] = useState(
    moment().add(-30, "days").format("YYYY-MM-DD")
  );
  const [EndDate, setEndDate] = useState(moment().format("YYYY-MM-DD"));

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

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  const PrintPDFExcelExportFunction = (reportType) => {
    let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

    window.open(
      finalUrl +
        "?action=InvoiceAdjustmentReportExport" +
        "&reportType=excel" +
        "&StartDate=" +
        StartDate +
        "&EndDate=" +
        EndDate +
        "&CustomerFilter=" +
        encodeURIComponent(currCustomerFilter) +
        "&UserId=" +
        UserInfo.UserId +
        "&RoleId=" +
        UserInfo.RoleId[0] +
        "&TimeStamp=" +
        Date.now()
    );
  };

  /* =====End of Excel Export Code==== */

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
      field: "AdjDateTimeText",
      label: "Adjusted Date",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "ApproveDateTimeText",
      label: "Approved Date",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
    },
    {
      field: "AdjDebitCredit",
      label: "Debit/Credit",
      align: "left",
      visible: true,
      sort: false,
      filter: true,
      format: (value) => {
        const text = (value || "").toString().trim().toLowerCase();
        const color =
          text === "debit" || text === "d"
            ? "#d32f2f"
            : text === "credit" || text === "c"
            ? "#2e7d32"
            : "inherit";

        return <span style={{ color, fontWeight: 600 }}>{value}</span>;
      },
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
      StartDate: StartDate,
      EndDate: EndDate,
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
        ].concat(res.data.datalist),
      );
    });
  }

  const handleChangeFilterDate = (e) => {
    const { name, value } = e.target;
    if (name === "StartDate") {
      setStartDate(value);
    }

    if (name === "EndDate") {
      setEndDate(value);
    }
  };

  const handleChangeFilterDropDown = (name, value) => {
    if (name === "customerFilter") {
      setCurrCustomerFilter(value);
    }
  };

  useEffect(() => {
    getDataList();
  }, [StartDate, EndDate, currCustomerFilter]);

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Reports ❯ Invoice Adjustment Report
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        <div class="searchAdd">
          <div>
            <label>Invoice Start Date</label>
            <div class="">
              <input
                type="date"
                id="StartDate"
                name="StartDate"
                value={StartDate}
                onChange={(e) => handleChangeFilterDate(e)}
              />
            </div>
          </div>

          <div>
            <label>Invoice End Date</label>
            <div class="">
              <input
                type="date"
                id="EndDate"
                name="EndDate"
                value={EndDate}
                onChange={(e) => handleChangeFilterDate(e)}
              />
            </div>
          </div>

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

          <Button
            label={"Export"}
            class={"btnPrint"}
            onClick={PrintPDFExcelExportFunction}
          />
        </div>

        {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}
        <CustomTable columns={columnList} rows={dataList ? dataList : {}} />
      </div>

      {/* <!-- BODY CONTAINER END --> */}
    </>
  );
};

export default InvoiceAdjustmentReport;
