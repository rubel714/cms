import React, { forwardRef, useRef, useEffect, useState } from "react";
import { DeleteOutline, Edit } from "@material-ui/icons";

import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { Typography, TextField } from "@material-ui/core";
import CustomTable from "components/CustomTable/CustomTable";
// import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

const ClientAddEditModal = (props) => {
  // console.log("props modal: ", props.currentRow);
  const serverpage = "client"; // this is .php server page

  // const [membershipTypeList, setMembershipTypeList] = useState(null);
  const [currentRow, setCurrentRow] = useState([]);
  const [dataList, setDataList] = useState([]);
  const [delDataList, setDelDataList] = useState([]);
  const [errorObject, setErrorObject] = useState({});
  const UserInfo = LoginUserInfo();
  // const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data

  const [CustomerGroupList, setCustomerGroupList] = useState(null);
  const [currCustomerGroupId, setCurrCustomerGroupId] = useState(null);

  const [UserList, setUserList] = useState(null);
  const [currUserId, setCurrUserId] = useState(null);

  const [BusinessLineList, setBusinessLineList] = useState(null);
  const [currBusinessLineId, setCurrBusinessLineId] = useState(null);

  const [clientUserStaffMap, setClientUserStaffMap] = useState({
    CustomerMapId: "",
    BusinessLineId: "",
    UserId: "",
  });

  React.useEffect(() => {
    getCustomerGroupList(props.currentRow.CustomerGroupId);
    getUserList(null);
    getBusinessLineList(null);
    setCurrentRow(props.currentRow);
    console.log("useEffect props.currentRow",props.currentRow);

    setDataList(props.currentRow.UserMap);
  }, []);

  function getCustomerGroupList(selectCustomerGroupId) {
    let params = {
      action: "CustomerGroupList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCustomerGroupList(
        [{ id: "", name: "Select Customer Group" }].concat(res.data.datalist)
      );

      setCurrCustomerGroupId(selectCustomerGroupId);
    });
  }

  function getUserList(selectUserId) {
    let params = {
      action: "UserList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setUserList([{ id: "", name: "Select Staff" }].concat(res.data.datalist));

      setCurrUserId(selectUserId);
    });
  }

  function getBusinessLineList(selectBusinessLineId) {
    let params = {
      action: "BusinessLineList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setBusinessLineList(
        [{ id: "", name: "Select Business Line" }].concat(res.data.datalist)
      );

      setCurrBusinessLineId(selectBusinessLineId);
    });
  }

  const handleChangeFilterDropDown = (name, value) => {
    let data = { ...currentRow };
    if (name === "CustomerGroupId") {
      data["CustomerGroupId"] = value;
      setCurrCustomerGroupId(value);
    }

    setErrorObject({ ...errorObject, [name]: null });
    setCurrentRow(data);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;
    setCurrentRow(data);

    setErrorObject({ ...errorObject, [name]: null });
  };

  // function handleChangeCheck(e) {
  //   // console.log('e.target.checked: ', e.target.checked);
  //   const { name, value } = e.target;

  //   let data = { ...currentRow };
  //   data[name] = e.target.checked;
  //   setCurrentRow(data);
  //   //  console.log('aaa data: ', data);
  // }

  const handleChangeManyDropDown = (name, value) => {
    let data = { ...clientUserStaffMap };

    if (name === "BusinessLineId") {
      data["BusinessLineId"] = value;
      setCurrBusinessLineId(value);
    }

    if (name === "UserId") {
      data["UserId"] = value;
      setCurrUserId(value);
    }

    // const [UserList, setUserList] = useState(null);
    // const [currUserId, setCurrUserId] = useState(null);

    // const [BusinessLineList, setBusinessLineList] = useState(null);
    // const [currBusinessLineId, setCurrBusinessLineId] = useState(null);

    // const [clientUserStaffMap, setClientUserStaffMap] = useState({
    //   BusinessLineId: "",
    //   UserId: "",
    // });

    console.log("data: ", data);

    // setErrorObject({ ...errorObject, [name]: null }); CustomerMapId
    setClientUserStaffMap(data);
  };

  const validateForm = () => {
    // let validateFields = ["GroupName", "DiscountAmount", "DiscountPercentage"]
    let validateFields = ["CustomerName", "CustomerGroupId"];
    let errorData = {};
    let isValid = true;
    validateFields.map((field) => {
      if (!currentRow[field]) {
        errorData[field] = "validation-style";
        isValid = false;
      }
    });
    setErrorObject(errorData);
    return isValid;
  };

  function addEditAPICall() {
    if (validateForm()) {
      let UserInfo = LoginUserInfo();
      let params = {
        action: "dataAddEdit",
        lan: language(),
        UserId: UserInfo.UserId,
        ClientId: UserInfo.ClientId,
        BranchId: UserInfo.BranchId,
        rowData: currentRow,
        manyDataList: dataList,
        manyDeleteDataList: delDataList,
      };
      apiCall.post(serverpage, { params }, apiOption()).then((res) => {
        // console.log('res: ', res);

        props.masterProps.openNoticeModal({
          isOpen: true,
          msg: res.data.message,
          msgtype: res.data.success,
        });

        // console.log('props modal: ', props);
        if (res.data.success === 1) {
          props.modalCallback("addedit");
        }
      });
    }
  }

  function modalClose() {
    props.modalCallback("close");
  }

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    {
      field: "UserName",
      label: "Staff Name",
      // width: "7%",
      align: "left",
      visible: true,
      // sort: true,
      filter: true,
    },
    {
      field: "BusinessLineName",
      label: "Business Line",
      // width: "9%",
      align: "left",
      visible: true,
      // sort: true,
      filter: true,
    },

    {
      field: "custom",
      label: "Action",
      width: "4%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
  ];

  /** Action from table row buttons*/
  function actioncontrol(rowData) {
    return (
      <>
        <Edit
          className={"table-edit-icon"}
          onClick={() => {
            editData(rowData);
          }}
        />
        <DeleteOutline
          className={"table-delete-icon"}
          onClick={() => {
            deleteData(rowData);
          }}
        />

        {/* {permissionType === 0 && (
            <Edit
              className={"table-edit-icon"}
              onClick={() => {
                editData(rowData);
              }}
            />
          )} */}

        {/* {permissionType === 0 && (
            <DeleteOutline
              className={"table-delete-icon"}
              onClick={() => {
                deleteData(rowData);
              }}
            />
          )} */}
      </>
    );
  }

  const editData = (rowData) => {
    let data = { ...clientUserStaffMap };

    data["CustomerMapId"] = rowData.CustomerMapId;
    // setCurrBusinessLineId(rowData.CustomerMapId);

    data["BusinessLineId"] = rowData.BusinessLineId;
    setCurrBusinessLineId(rowData.BusinessLineId);

    data["UserId"] = rowData.UserId;
    setCurrUserId(rowData.UserId);

    console.log("data: ", data);

    setClientUserStaffMap(data);
  };

  function AddEditStaff() {
    if (
      clientUserStaffMap.BusinessLineId == "" ||
      clientUserStaffMap.UserId == ""
    ) {
      props.masterProps.openNoticeModal({
        isOpen: true,
        msg: "Please enter Staff Name and Business Line",
        msgtype: 0,
      });
    } else {
      // console.log("AddEditStaff");
      // console.log("clientUserStaffMap", clientUserStaffMap);

      let data = [...dataList];

      let BusinessLineName = BusinessLineList
        ? BusinessLineList[
            BusinessLineList.findIndex(
              (list) => list.id === clientUserStaffMap.BusinessLineId
            )
          ].name
        : "";

      let UserName = UserList
        ? UserList[
            UserList.findIndex((list) => list.id === clientUserStaffMap.UserId)
          ].name
        : "";

      let CustomerMapId = clientUserStaffMap.CustomerMapId;
      let rowIdx = 0;
      if(CustomerMapId != ""){
        data.forEach((element, i) => {
          if(element.CustomerMapId == CustomerMapId){
            rowIdx = i;
          }
        });
        
      }else{
        rowIdx = data.length;
      }
      let row = {
        CustomerMapId: "",
        BusinessLineId: clientUserStaffMap.BusinessLineId,
        BusinessLineName: BusinessLineName,
        CustomerId: currentRow.id,
        UserId: clientUserStaffMap.UserId,
        UserName: UserName,
      };

      data[rowIdx] = row;
      console.log('data: ', data);
      setDataList(data);



      setClientUserStaffMap({
        CustomerMapId: "",
        BusinessLineId: "",
        UserId: "",
      });

      setCurrBusinessLineId(null);
      setCurrUserId(null);
    }
  }



  
  function deleteData(delRow) {
    // console.log('delRow: ', delRow);
      let data = [...dataList];
      let delData = [...delDataList];

      let rows = [];
      let idx = 0;
      data.forEach((element, i) => {
        if(element.CustomerMapId != delRow.CustomerMapId){
          rows[idx] = element;
          idx++;
        }else{
          if(element.CustomerMapId != ""){
            delData.push(element.CustomerMapId);
          }
        }
      });

      setDataList(rows);
      setDelDataList(delData);
      // console.log('delData: ', delData);
  }

  return (
    <>
      {/* <!-- GROUP MODAL START --> */}
      <div id="groupModal" class="modal">
        {/* <!-- Modal content --> */}
        <div class="modal-content">
          <div class="modalHeader">
            <h4>Add/Edit Client</h4>
          </div>

          <div class="contactmodalBody pt-10">
            <label>Code</label>
            <input
              type="text"
              id="CustomerCode"
              name="CustomerCode"
              // class={errorObject.CustomerCode}
              placeholder="Enter code"
              value={currentRow.CustomerCode}
              onChange={(e) => handleChange(e)}
            />

            <label>Client Name *</label>
            <input
              type="text"
              id="CustomerName"
              name="CustomerName"
              class={errorObject.CustomerName}
              placeholder="Enter client name"
              value={currentRow.CustomerName}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="contactmodalBody pt-10">
            <label>Customer Group *</label>

            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="CustomerGroupId"
              name="CustomerGroupId"
              autoComplete
              class={errorObject.CustomerGroupId}
              options={CustomerGroupList ? CustomerGroupList : []}
              getOptionLabel={(option) => option.name}
              defaultValue={{ id: 0, name: "Select Designation" }}
              value={
                CustomerGroupList
                  ? CustomerGroupList[
                      CustomerGroupList.findIndex(
                        (list) => list.id === currCustomerGroupId
                      )
                    ]
                  : null
              }
              onChange={(event, valueobj) =>
                handleChangeFilterDropDown(
                  "CustomerGroupId",
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

            <label>Address</label>
            <input
              type="text"
              id="CompanyAddress"
              name="CompanyAddress"
              // class={errorObject.CompanyAddress}
              placeholder="Enter address"
              value={currentRow.CompanyAddress}
              onChange={(e) => handleChange(e)}
            />
            {/* <label>Type</label>
            <input
              type="text"
              id="NatureOfBusiness"
              name="NatureOfBusiness"
              // class={errorObject.NatureOfBusiness}
              placeholder="Enter type"
              value={currentRow.NatureOfBusiness}
              onChange={(e) => handleChange(e)}
            /> */}
          </div>

          <div class="contactmodalBody pt-10">
            <label>Contact Person</label>
            <input
              type="text"
              id="CompanyName"
              name="CompanyName"
              // class={errorObject.ContactPhone}
              placeholder="Enter contact person"
              value={currentRow.CompanyName}
              onChange={(e) => handleChange(e)}
            />

            <label>Designation</label>
            <input
              type="text"
              id="Designation"
              name="Designation"
              // class={errorObject.Designation}
              placeholder="Enter designation"
              value={currentRow.Designation}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="contactmodalBody pt-10">
            <label>Phone</label>
            <input
              type="text"
              id="ContactPhone"
              name="ContactPhone"
              // class={errorObject.ContactPhone}
              placeholder="Enter phone"
              value={currentRow.ContactPhone}
              onChange={(e) => handleChange(e)}
            />

            <label>Email</label>
            <input
              type="text"
              id="CompanyEmail"
              name="CompanyEmail"
              // class={errorObject.ContactPhone}
              placeholder="Enter email"
              value={currentRow.CompanyEmail}
              onChange={(e) => handleChange(e)}
            />
          </div>




{currentRow.id>0 && (<>
          <div class=" pt-10">
            <hr></hr>
          </div>

          <div class="contactmodalBody pt-10">
            <Button
              label={"Add Staff"}
              class={"btnSave"}
              onClick={AddEditStaff}
            />
          </div>

          <div class="contactmodalBody pt-10">
            <label>Staff Name *</label>
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="UserId"
              name="UserId"
              autoComplete
              // class={errorObject.UserId}
              options={UserList ? UserList : []}
              getOptionLabel={(option) => option.name}
              defaultValue={{ id: "", name: "Select Staff" }}
              value={
                UserList
                  ? UserList[
                      UserList.findIndex((list) => list.id === currUserId)
                    ]
                  : null
              }
              onChange={(event, valueobj) =>
                handleChangeManyDropDown("UserId", valueobj ? valueobj.id : "")
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

            <label>Business Line *</label>
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="BusinessLineId"
              name="BusinessLineId"
              autoComplete
              // class={errorObject.BusinessLineId}
              options={BusinessLineList ? BusinessLineList : []}
              getOptionLabel={(option) => option.name}
              defaultValue={{ id: "", name: "Select Business Line" }}
              value={
                BusinessLineList
                  ? BusinessLineList[
                      BusinessLineList.findIndex(
                        (list) => list.id === currBusinessLineId
                      )
                    ]
                  : null
              }
              onChange={(event, valueobj) =>
                handleChangeManyDropDown(
                  "BusinessLineId",
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

          <div class=" pt-10">
            <CustomTable
              columns={columnList}
              rows={dataList ? dataList : {}}
              actioncontrol={actioncontrol}
              ispagination={false}
            />
          </div>



</>
)}


          <div class="modalItem">
            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
            {props.currentRow.id && (
              <Button
                label={"Update"}
                class={"btnUpdate"}
                onClick={addEditAPICall}
              />
            )}
            {!props.currentRow.id && (
              <Button
                label={"Save"}
                class={"btnSave"}
                onClick={addEditAPICall}
              />
            )}
          </div>
        </div>
      </div>
      {/* <!-- GROUP MODAL END --> */}
    </>
  );
};

export default ClientAddEditModal;
