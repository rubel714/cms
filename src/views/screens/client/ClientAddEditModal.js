import React, { forwardRef, useRef, useEffect, useState } from "react";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";

const ClientAddEditModal = (props) => {
  // console.log('props modal: ', props);
  const serverpage = "client"; // this is .php server page

  // const [membershipTypeList, setMembershipTypeList] = useState(null);
  const [currentRow, setCurrentRow] = useState([]);
  const [errorObject, setErrorObject] = useState({});
  const UserInfo = LoginUserInfo();

  React.useEffect(() => {
    setCurrentRow(props.currentRow);
    // getMembershipType();
  }, []);

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
    let validateFields = ["CustomerName"];
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
          <label>Address</label>
            <input
              type="text"
              id="CompanyAddress"
              name="CompanyAddress"
              // class={errorObject.CompanyAddress}
              placeholder="Enter company address"
              value={currentRow.CompanyAddress}
              onChange={(e) => handleChange(e)}
            />
  <label>Type</label>
            <input
              type="text"
              id="NatureOfBusiness"
              name="NatureOfBusiness"
              // class={errorObject.NatureOfBusiness}
              placeholder="Enter type"
              value={currentRow.NatureOfBusiness}
              onChange={(e) => handleChange(e)}
            />


           
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
              placeholder="Enter company email"
              value={currentRow.CompanyEmail}
              onChange={(e) => handleChange(e)}
            />

           
          </div>

          {/* <div class="contactmodalBody pt-10">
            <label>Is Active?</label>
            <input
              id="IsActive"
              name="IsActive"
              type="checkbox"
              checked={currentRow.IsActive}
              onChange={handleChangeCheck}
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

export default ClientAddEditModal;
