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

const InvoiceEditModal = (props) => {
  const serverpage = "invoicelist"; // this is .php server page

  const [currentRow, setCurrentRow] = useState([]);
  const [errorObject, setErrorObject] = useState({});
  const UserInfo = LoginUserInfo();
  const [UserList, setUserList] = useState(null);
  const [currUserId, setCurrUserId] = useState(null);
  React.useEffect(() => {
    getUserList(props.currentRow.CustomerUserId);
    setCurrentRow(props.currentRow);
  }, []);
 
  function getUserList(selectUserId) {
    let params = {
      action: "UserList",
      lan: language(),
      UserId: UserInfo.UserId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setUserList([{ id: null, name: "Select Staff" }].concat(res.data.datalist));

      setCurrUserId(selectUserId);
    });
  }


  const handleChangeFilterDropDown = (name, value) => {
    let data = { ...currentRow };
    if (name === "CustomerUserId") {
      data["CustomerUserId"] = value;
      setCurrUserId(value);
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
 
  // const validateForm = () => {
  //   // let validateFields = ["GroupName", "DiscountAmount", "DiscountPercentage"]
  //   let validateFields = ["CustomerName", "CustomerGroupId"];
  //   let errorData = {};
  //   let isValid = true;
  //   validateFields.map((field) => {
  //     if (!currentRow[field]) {
  //       errorData[field] = "validation-style";
  //       isValid = false;
  //     }
  //   });
  //   setErrorObject(errorData);
  //   return isValid;
  // };

  function addEditAPICall() {
    // if (validateForm()) {
      let UserInfo = LoginUserInfo();
      let params = {
        action: "dataAddEdit",
        lan: language(),
        UserId: UserInfo.UserId,
        rowData: currentRow
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
    // }
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
            <h4>Add/Edit Invoice</h4>
          </div>

          {/* <div class="contactmodalBody pt-10">
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
          </div> */}

          <div class="contactmodalBody pt-10">
            <label>Invoice Amount (BDT)</label>
            <input
              type="number"
              id="BaseAmount"
              disabled={true}
              name="BaseAmount"
              //class={errorObject.BaseAmount}
              placeholder="Enter invoice amount"
              value={currentRow.BaseAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Invoice Amount (BDT)</label>
            <input
              type="number"
              id="BaseAmountWithoutVat"
              name="BaseAmountWithoutVat"
              //class={errorObject.BaseAmountWithoutVat}
              placeholder="Enter amount"
              value={currentRow.BaseAmountWithoutVat}
              onChange={(e) => handleChange(e)}
            />

            <label>VAT Amount (BDT)</label>
            <input
              type="number"
              id="VatAmount"
              name="VatAmount"
             // class={errorObject.VatAmount}
              placeholder="Enter vat amount"
              value={currentRow.VatAmount}
              onChange={(e) => handleChange(e)}
            />

            <label>Staff</label>
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="CustomerUserId"
              name="CustomerUserId"
              autoComplete
              // class={errorObject.CustomerGroupId}
              options={UserList ? UserList : []}
              getOptionLabel={(option) => option.name}
              defaultValue={{ id: 0, name: "Select Staff" }}
              value={
                UserList
                  ? UserList[
                      UserList.findIndex(
                        (list) => list.id === currUserId
                      )
                    ]
                  : null
              }
              onChange={(event, valueobj) =>
                handleChangeFilterDropDown(
                  "CustomerUserId",
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

          {/* <div class="contactmodalBody pt-10">
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
          </div> */}

          {/* <div class="contactmodalBody pt-10">
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
          </div> */}
 

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

export default InvoiceEditModal;
