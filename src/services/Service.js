import React, { forwardRef } from "react";
import {
  AddBox,
  ArrowDownward,
  Check,
  ChevronLeft,
  ChevronRight,
  Clear,
  DeleteOutline,
  Edit,
  FilterList,
  FirstPage,
  LastPage,
  Remove,
  SaveAlt,
  Search,
  ViewColumn,
} from "@material-ui/icons";

const axios = require("axios");

const api_URL = process.env.REACT_APP_API_URL;
 
const auth_token = sessionStorage.getItem("token")
  ? sessionStorage.getItem("token")
  : null;

export default {
  tableicons() {
    const tableIcons = {
      Add: forwardRef((props, ref) => <AddBox {...props} ref={ref} />),
      Check: forwardRef((props, ref) => <Check {...props} ref={ref} />),
      Clear: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
      Delete: forwardRef((props, ref) => (
        <DeleteOutline {...props} ref={ref} />
      )),
      DetailPanel: forwardRef((props, ref) => (
        <ChevronRight {...props} ref={ref} />
      )),
      Edit: forwardRef((props, ref) => <Edit {...props} ref={ref} />),
      Export: forwardRef((props, ref) => <SaveAlt {...props} ref={ref} />),
      Filter: forwardRef((props, ref) => <FilterList {...props} ref={ref} />),
      FirstPage: forwardRef((props, ref) => <FirstPage {...props} ref={ref} />),
      LastPage: forwardRef((props, ref) => <LastPage {...props} ref={ref} />),
      NextPage: forwardRef((props, ref) => (
        <ChevronRight {...props} ref={ref} />
      )),
      PreviousPage: forwardRef((props, ref) => (
        <ChevronLeft {...props} ref={ref} />
      )),
      ResetSearch: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
      Search: forwardRef((props, ref) => <Search {...props} ref={ref} />),
      SortArrow: forwardRef((props, ref) => (
        <ArrowDownward {...props} ref={ref} />
      )),
      ThirdStateCheck: forwardRef((props, ref) => (
        <Remove {...props} ref={ref} />
      )),
      ViewColumn: forwardRef((props, ref) => (
        <ViewColumn {...props} ref={ref} />
      )),
    };
    return tableIcons;
  },

  async postApi(url, body) {
    let finalUrl = api_URL + url;
    const options = {
      headers: { 
        "Content-Type": "application/json",
       },
    };
    let res = await axios
      .post(finalUrl, body, options)
      .then((response) => {
        return response.data;
      })
      .catch((error) => {
        return error;
      });

    return res;
  },

  async postAuthApi(url, body) {
    let finalUrl = api_URL + url;
    const options = {
      headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "*",
        Authorization: `Bearer ${auth_token}`,
      },
    };
    let res = await axios
      .post(finalUrl, body, options)
      .then((response) => {
        return response.data;
      })
      .catch((error) => {
        return error;
      });

    return res;
  },

  authToken() {
    return auth_token;
  },

  async getAuthApi(url, body) {
    let finalUrl = api_URL + url;
    let options = {};
    options = {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${auth_token}`,
      },
    };

    let res = await axios
      .get(finalUrl, options)
      .then((response) => {
        return response.data;
      })
      .catch((error) => {
        return error;
      });

    return res;
  },
};
