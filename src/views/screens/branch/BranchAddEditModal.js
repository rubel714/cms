import React, { forwardRef, useRef, useEffect, useState } from "react";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";

const BranchAddEditModal = (props) => {
  // console.log('props modal: ', props);
  const serverpage = "branch"; // this is .php server page

  // const [supplierTypeList, setSupplierTypeList] = useState(null);
  const [currentRow, setCurrentRow] = useState(props.currentRow);
  const [errorObject, setErrorObject] = useState({});
 

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
    let validateFields = ["BranchName", "PhoneNo"];
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
            <h4>Add/Edit Branch</h4>
          </div>



          <div class="contactmodalBody pt-10">

                
                <label>Branch Name*</label>
                <input
                  type="text"
                  id="BranchName"
                  name="BranchName"
                  class={errorObject.BranchName}
                  placeholder="Enter branch name"
                  value={currentRow.BranchName}
                  onChange={(e) => handleChange(e)}
                />
                
                <label>Phone*</label>
                <input
                  type="text"
                  id="PhoneNo"
                  name="PhoneNo"
                  class={errorObject.PhoneNo}
                  placeholder="Enter phone"
                  value={currentRow.PhoneNo}
                  onChange={(e) => handleChange(e)}
                />

                <label>Email</label>
                <input
                  type="text"
                  id="Email"
                  name="Email"
                  // class={errorObject.Email}
                  placeholder="Enter email"
                  value={currentRow.Email}
                  onChange={(e) => handleChange(e)}
                />

         
 
 
 
                <label>Address</label>
                <textarea 
                  id="BranchAddress"
                  name="BranchAddress"
                  value={currentRow.BranchAddress}
                  onChange={(e) => handleChange(e)}
                >
                </textarea>

            </div>
 
            {/* <div class="modalItem">
            <label>Is Active?</label>
            <input
              id="IsActive"
              name="IsActive"
              type="checkbox"
              checked={currentRow.IsActive}
              onChange={handleChangeCheck}
            />
          </div>  */}

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

export default BranchAddEditModal;
