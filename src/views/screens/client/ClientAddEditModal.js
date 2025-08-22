import React, { forwardRef, useRef, useEffect, useState } from "react";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { Typography, TextField } from "@material-ui/core";
const ClientAddEditModal = (props) => {
  // console.log('props modal: ', props);
  const serverpage = "client"; // this is .php server page

  // const [membershipTypeList, setMembershipTypeList] = useState(null);
  const [currentRow, setCurrentRow] = useState([]);
  const [errorObject, setErrorObject] = useState({});
  const UserInfo = LoginUserInfo();

  const [CustomerGroupList, setCustomerGroupList] = useState(null);
  const [currCustomerGroupId, setCurrCustomerGroupId] = useState(null);


  React.useEffect(() => {
    getCustomerGroupList(props.currentRow.CustomerGroupId);
    setCurrentRow(props.currentRow);
    
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

  function handleChangeCheck(e) {
    // console.log('e.target.checked: ', e.target.checked);
    const { name, value } = e.target;

    let data = { ...currentRow };
    data[name] = e.target.checked;
    setCurrentRow(data);
    //  console.log('aaa data: ', data);
  }

  const validateForm = () => {
    // let validateFields = ["GroupName", "DiscountAmount", "DiscountPercentage"]
    let validateFields = ["CustomerName","CustomerGroupId"];
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
