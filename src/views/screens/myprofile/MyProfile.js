import React, { forwardRef, useRef } from "react";
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

// import UserEntryAddEditModal from "./UserEntryAddEditModal";

const MyProfile = (props) => {
  // console.log('props: ', props);
  const serverpage = "myprofile"; // this is .php server page

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);

  const [currentRow, setCurrentRow] = useState({
    id: "",
    UserName: "",
    LoginName: "",
    Password: "",
    RoleName: "",
    Email: "",
    DesignationId: "",
    confirmPassword: "",
    // IsActive: false,
  });
  const [errorObject, setErrorObject] = useState({});
  const UserInfo = LoginUserInfo();

  const [DesignationList, setDesignationList] = useState(null);
  const [currDesignationId, setCurrDesignationId] = useState(null);
  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  // const UserInfo = LoginUserInfo();

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

  React.useEffect(() => {
    getDesignation();
  }, []);

  React.useEffect(() => {
    if (dataList.length > 0) {
      setCurrentRow(dataList[0]);
      setCurrDesignationId(dataList[0].DesignationId);
    }
  }, [dataList]);

  function getDesignation() {
    let params = {
      action: "DesignationList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setDesignationList(
        [{ id: "", name: "Select Designation" }].concat(res.data.datalist)
      );
    });
  }

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;

    setCurrentRow(data);

    setErrorObject({ ...errorObject, [name]: null });

    if (name === "DesignationId") {
      setCurrDesignationId(value);
    }
  };

  function handleChangeCheck(e) {
    // console.log('e.target.checked: ', e.target.checked);
    const { name, value } = e.target;

    let data = { ...currentRow };
    data[name] = e.target.checked;
    setCurrentRow(data);
    //  console.log('aaa data: ', data);
  }

  const validateForm = () => {
    // console.log("--------", currentRow);

    // let validateFields = ["UserName", "DiscountAmount", "DiscountPercentage"]
    let validateFields = [];
    if (currentRow["id"]) {
      validateFields = ["UserName", "LoginName", "Email", "DesignationId"];
    } else {
      validateFields = [
        "UserName",
        "LoginName",
        "Password",
        "Email",
        "DesignationId",
      ];
    }

    let errorData = {};
    let isValid = true;
    validateFields.map((field) => {
      if (!currentRow[field]) {
        errorData[field] = "validation-style";
        isValid = false;
      }

      let InEdit = "";
      if (currentRow["id"]) {
        InEdit = currentRow["id"];
      } else {
        InEdit = "";
      }

      if (InEdit) {
        errorData["Password"] = "";
      }

      //-----start confirm change password-----
      let cpassword = "";
      let cconfirmChangePassword = "";

      if (currentRow["Password"]) {
        cpassword = currentRow["Password"].trim();
      } else {
        cpassword = "";
      }

      if (currentRow["confirmChangePassword"]) {
        cconfirmChangePassword = currentRow["confirmChangePassword"].trim();
      } else {
        cconfirmChangePassword = "";
      }

      if (cpassword !== "") {
        if (cconfirmChangePassword == "") {
          props.openNoticeModal({
            isOpen: true,
            msg: "Enter Confirm Password",
            msgtype: 0,
          });

          //errorData["confirmChangePassword"] = "Enter Confirm Password";
          isValid = false;
        } else if (cpassword != cconfirmChangePassword) {
          props.openNoticeModal({
            isOpen: true,
            msg: "Password did not match",
            msgtype: 0,
          });

          //errorData["confirmChangePassword"] = "Password did not match";
          isValid = false;
        } else {
          errorData["confirmChangePassword"] = "";
        }
      }

      //-----end confirm change password-----
    });
    setErrorObject(errorData);
    return isValid;
  };

  function addEditAPICall() {
    if (validateForm()) {
      let params = {
        action: "dataAddEdit",
        lan: language(),
        UserId: UserInfo.UserId,
        ClientId: UserInfo.ClientId,
        BranchId: UserInfo.BranchId,
        rowData: currentRow,
      };

      apiCall.post(serverpage, { params }, apiOption()).then((res) => {
        // console.log('res: ', res);

        props.openNoticeModal({
          isOpen: true,
          msg: res.data.message,
          msgtype: res.data.success,
        });

        // console.log('props modal: ', props);
        // if (res.data.success === 1) {
        //          props.modalCallback("addedit");
        //  }
      });
    }
  }

  if (bFirst) {
    /**First time call for datalist */
    getDataList();

    setBFirst(false);
  }

  /**Get data for table list */
  function getDataList() {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
    };
    // console.log('LoginUserInfo params: ', params);

    ExecuteQuery(serverpage, params);
  }

  return (
    <>
      <div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>Home ❯ My Profile</h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        {/* <div class="searchAdd">
          <label></label>
          <Button label={"ADD"} class={"btnAdd"} onClick={addData} />
        </div> */}

        <div class="usersProfile">
          <div className="App">
            <div class="contactmodalBody pt-10">
              <label>User Name</label>
              <input
                type="text"
                id="UserName"
                name="UserName"
                disabled={true}
                class={errorObject.UserName}
                placeholder="Enter User Name"
                value={currentRow.UserName}
                onChange={(e) => handleChange(e)}
              />

              <label>Login Name</label>
              <input
                type="text"
                id="LoginName"
                name="LoginName"
                disabled={true}
                class={errorObject.LoginName}
                placeholder="Enter Login Name"
                value={currentRow.LoginName}
                onChange={(e) => handleChange(e)}
              />
            </div>

            <div class="contactmodalBody pt-10">
              <label>Password</label>
              <input
                id="Password"
                name="Password"
                type="Password"
                class={errorObject.Password}
                placeholder="Enter Password"
                value={currentRow.Password}
                onChange={(e) => handleChange(e)}
              />

              <label>Confirm Password</label>
              <input
                id="confirmChangePassword"
                name="confirmChangePassword"
                type="Password"
                class={errorObject.confirmChangePassword}
                placeholder="Enter Confirm Password"
                value={currentRow.confirmChangePassword}
                onChange={(e) => handleChange(e)}
              />
            </div>

            <div class="contactmodalBody pt-10">
              <label>Email</label>
              <input
                type="text"
                id="Email"
                name="Email"
                disabled={true}
                placeholder="Enter Email"
                class={errorObject.Email}
                value={currentRow.Email}
                onChange={(e) => handleChange(e)}
              ></input>

              <label>Phone No</label>
              <input
                type="text"
                id="PhoneNo"
                name="PhoneNo"
                disabled={true}
                placeholder="Enter PhoneNo"
                // class={errorObject.PhoneNo}
                value={currentRow.PhoneNo}
                onChange={(e) => handleChange(e)}
              ></input>
            </div>

            <div class="contactmodalBody pt-10">
              <label>Role Name</label>
              <input
                type="text"
                id="RoleName"
                name="RoleName"
                disabled={true}
                // placeholder="Enter Email"
                // class={errorObject.RoleName}
                value={currentRow.RoleName}
                // onChange={(e) => handleChange(e)}
              ></input>
              {/* <select
              id="RoleId"
              name="RoleId"
              class={errorObject.RoleId}
              value={currRoleId}
              onChange={(e) => handleChange(e)}
            >
              {RoleList &&
                RoleList.map((item, index) => {
                  return <option value={item.id}>{item.name}</option>;
                })}
            </select> */}

              <label>Designation</label>
              <select
                id="DesignationId"
                name="DesignationId"
                disabled={true}
                class={errorObject.DesignationId}
                value={currDesignationId}
                onChange={(e) => handleChange(e)}
              >
                {DesignationList &&
                  DesignationList.map((item, index) => {
                    return <option value={item.id}>{item.name}</option>;
                  })}
              </select>
            </div>

            <div class="contactmodalBody pt-10">
              <label>Department</label>
              <input
                type="text"
                id="DepartmentName"
                name="DepartmentName"
                disabled={true}
                placeholder="Enter DepartmentName"
                // class={errorObject.DepartmentName}
                value={currentRow.DepartmentName}
                onChange={(e) => handleChange(e)}
              ></input>

              <label>Business Line</label>
              <input
                type="text"
                id="BusinessLineName"
                name="BusinessLineName"
                disabled={true}
                placeholder="Enter BusinessLineName"
                // class={errorObject.BusinessLineName}
                value={currentRow.BusinessLineName}
                onChange={(e) => handleChange(e)}
              ></input>

              {/* <label> Is Active?</label>
            <input
              id="IsActive"
              name="IsActive"
              type="checkbox"
              checked={currentRow.IsActive}
              onChange={handleChangeCheck}
            /> */}
            </div>

            <div class="contactmodalBody pt-10">
              <label>Lineman (N+1)</label>
              <input
                type="text"
                id="LinemanUserName"
                name="LinemanUserName"
                disabled={true}
                placeholder="Enter LinemanUserName"
                // class={errorObject.LinemanUserName}
                value={currentRow.LinemanUserName}
                onChange={(e) => handleChange(e)}
              ></input>

              <label>Address</label>
              <input
                type="text"
                id="Address"
                name="Address"
                disabled={true}
                placeholder="Enter Address"
                // class={errorObject.Address}
                value={currentRow.Address}
                onChange={(e) => handleChange(e)}
              ></input>
            </div>

            <div class="modalItem">
              {/* {props.currentRow.id && ( */}
              <Button
                label={"Update"}
                class={"btnUpdate"}
                onClick={addEditAPICall}
              />
              {/* // )}
            // {!props.currentRow.id && (
            //   <Button
            //     label={"Save"}
            //     class={"btnSave"}
            //     onClick={addEditAPICall}
            //   />
            // )} */}
            </div>

            {/* <CustomTable
              columns={columnList}
              rows={dataList?dataList:{}}
              actioncontrol={actioncontrol}
            /> */}
          </div>
        </div>
      </div>
      {/* <!-- BODY CONTAINER END --> */}

      {/* {showModal && (<UserEntryAddEditModal masterProps={props} currentRow={currentRow} modalCallback={modalCallback}/>)} */}
    </>
  );
};

export default MyProfile;
