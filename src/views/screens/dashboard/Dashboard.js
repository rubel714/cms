import React, { forwardRef, useRef, useEffect } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit } from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

import { Typography, TextField } from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";

// Highcharts component
import Highcharts from "highcharts/highstock";
import exporting from "highcharts/modules/exporting.js";
import HighchartsReact from "highcharts-react-official";

const Dashboard = (props) => {
  const serverpage = "dashboard"; // this is .php server page
  const permissionType = props.permissionType;

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [monthList, setMonthList] = useState(null);
  const [yearList, setYearList] = useState(null);

  const currDate = new Date();
  const [selectedMonthId, setSelectedMonthId] = useState(
    currDate.getMonth() + 1
  );
  const [selectedYearId, setSelectedYearId] = useState(currDate.getFullYear());

  var month = currDate.toLocaleString("default", { month: "long" });
  const [defaultMonth, setDefaultMonth] = useState({
    id: currDate.getMonth() + 1,
    name: month,
  });
  const [defaultYear, setDefaultYear] = useState({
    id: currDate.getFullYear(),
    name: String(currDate.getFullYear()),
  });

  const [basicData, setBasicData] = useState([]);
  const [salesTrendByMonthData, setSalesTrendByMonthData] = useState([]);
  const [expenseByMonthData, setExpenseByMonthData] = useState([]);

  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const UserInfo = LoginUserInfo();

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  if (bFirst) {
    /**First time call for datalist */
    getMonthList();
    getYearList();

    getBasicData();
    getSalesTrendByMonth();
    getExpenseByMonth();
    setBFirst(false);
  }

  useEffect(() => {
    console.log("useEffect call");
    getBasicData();
    getSalesTrendByMonth();
    getExpenseByMonth();
  }, [selectedMonthId, selectedYearId]);

  const handleChangeFilterDropDown = (name, value) => {
    if (name == "MonthId") {
      setSelectedMonthId(value);
    } else if (name == "YearId") {
      setSelectedYearId(value);
    }
  };

  function getMonthList() {
    let params = {
      action: "MonthList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setMonthList(res.data.datalist);
    });
  }

  function getYearList() {
    let params = {
      action: "YearList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      // setYearList([{ id: 0, name: "All" }].concat(res.data.datalist));
      setYearList(res.data.datalist);
    });
  }

  function getBasicData() {
    let params = {
      action: "getBasicData",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      MonthId: selectedMonthId,
      YearId: selectedYearId,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      setBasicData(res.data.datalist);
    });
  }

  function getSalesTrendByMonth() {
    let params = {
      action: "getSalesTrendData",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      MonthId: selectedMonthId,
      YearId: selectedYearId,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      // console.log('data: ', res);

      setSalesTrendByMonthData({
        chart: {
          type: "spline",
        },
        title: {
          text: "Sales amount trend of last 6 months",
          style: {
            fontSize: "16px",
          },
        },
        subtitle: {
          text: res.data.datalist ? res.data.datalist.duration : '',
       },
        xAxis: {
          //categories: data.category
          categories: res.data.datalist ? res.data.datalist.category : [],
        },
        yAxis: {
          min: 0,
          title: {
            text: "Sales Amount (TK)",
          },
        },
        tooltip: {
          pointFormat:
            '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
          shared: true,
        },
        plotOptions: {
          column: {
            stacking: "percent",
          },
        },
        credits: {
          enabled: false,
        },
        //series: data.seriesdata
        series: res.data.datalist ? res.data.datalist.seriesdata : [],
      });
    });
  }

  function getExpenseByMonth() {
    let params = {
      action: "getExpenseByMonth",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      MonthId: selectedMonthId,
      YearId: selectedYearId,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      // console.log('data: ', res);

      setExpenseByMonthData({
        chart: {
          type: "column",
        },
        title: {
          text: "Expense amount of last 6 months",
          style: {
            fontSize: "16px",
          },
        },
        subtitle: {
          text: res.data.datalist ? res.data.datalist.duration : '',
       },

        xAxis: {
          //categories: data.category
          categories: res.data.datalist ? res.data.datalist.category : [],
        },
        yAxis: {
          min: 0,
          title: {
            text: "Expense Amount (TK)",
          },
        },
        tooltip: {
          pointFormat:
            '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
          shared: true,
        },
        plotOptions: {
          column: {
            //stacking: "percent",
          },
        },
        credits: {
          enabled: false,
        },
        //series: data.seriesdata
        series: res.data.datalist ? res.data.datalist.seriesdata : [],
      });
    });
  }




  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>
            <a href="#">Home</a> ❯ Dashboard
          </h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}


        {/* <div class="searchAdd">
          <div>
            <label class="pl-10">Month</label>
          </div>
          <div class="">
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="MonthId"
              name="MonthId"
              autoComplete
              options={monthList ? monthList : []}
              getOptionLabel={(option) => option.name}
              defaultValue={defaultMonth}
              onChange={(event, valueobj) =>
                handleChangeFilterDropDown(
                  "MonthId",
                  valueobj ? valueobj.id : ""
                )
              }
              renderOption={(option) => (
                <Typography className="chosen_dropdown_font">
                  {option.name}
                </Typography>
              )}
              renderInput={(params) => (
                <TextField {...params} variant="standard" fullWidth />
              )}
            />
          </div>

          <div>
            <label class="pl-10">Year</label>
          </div>
          <div class="">
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="YearId"
              name="YearId"
              autoComplete
              options={yearList ? yearList : []}
              getOptionLabel={(option) => option.name}
              defaultValue={defaultYear}
              onChange={(event, valueobj) =>
                handleChangeFilterDropDown(
                  "YearId",
                  valueobj ? valueobj.id : ""
                )
              }
              renderOption={(option) => (
                <Typography className="chosen_dropdown_font">
                  {option.name}
                </Typography>
              )}
              renderInput={(params) => (
                <TextField {...params} variant="standard" fullWidth />
              )}
            />
          </div>

        </div>

        <div class="subContainer">
          <div className="App">


            <div>
              <div class="block-box1">
                <h2>Month</h2>
                <p>{basicData.Month}</p>
              </div>

              <div class="block-box2">
                <h2>Current Stock Quantity</h2>
                <p>{basicData.StockQty}</p>
              </div>

              <div class="block-box1">
                <h2>Current Stock Value</h2>
                <p>{basicData.StockValue}</p>
              </div>

              <div class="block-box2">
                <h2>No of Sales Invoice</h2>
                <p>{basicData.NoofSalesInvoice}</p>
              </div>

              <div class="block-box1">
                <h2>Sales Value</h2>
                <p>{basicData.SalesValue}</p>
              </div>

              <div class="block-box2">
                <h2>Gross Profit</h2>
                <p>{basicData.GrossProfit}</p>
              </div>

              <div class="block-box1">
                <h2>Supplier Payment</h2>
                <p>{basicData.SupplierPayment}</p>
              </div>

              <div class="block-box2">
                <h2>Office Expense</h2>
                <p>{basicData.OfficeExpense}</p>
              </div>

              <div class="block-box3">
                <HighchartsReact
                  highcharts={Highcharts}
                  options={salesTrendByMonthData}
                />
              </div>
              
              <div class="block-box3">
                <HighchartsReact
                  highcharts={Highcharts}
                  options={expenseByMonthData}
                />
              </div>

            </div>
          </div>
        </div> */}
      </div>
      {/* <!-- BODY CONTAINER END --> */}
    </>
  );
};

export default Dashboard;
