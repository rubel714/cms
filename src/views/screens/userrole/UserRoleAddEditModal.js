import React, { forwardRef, useRef,useEffect,useState } from "react";
import {Button}  from "../../../components/CustomControl/Button";
import {apiCall, apiOption, LoginUserInfo, language}  from "../../../actions/api";


const UserRoleAddEditModal = (props) => { 
  // console.log('props modal: ', props);
  const serverpage = "userrole";// this is .php server page
  const [currentRow, setCurrentRow] = useState(props.currentRow);
  const [errorObject, setErrorObject] = useState({});
  const UserInfo = LoginUserInfo();
  
  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;
    setCurrentRow(data);
    // console.log('aaa data: ', data);

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
    let validateFields = ["RoleName"]
    let errorData = {}
    let isValid = true
    validateFields.map((field) => {
      if (!currentRow[field]) {
        errorData[field] = "validation-style";
        isValid = false
      }
    })
    setErrorObject(errorData);
    return isValid
  }


  function addEditAPICall(){

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
        
        props.masterProps.openNoticeModal({
          isOpen: true,
          msg: res.data.message,
          msgtype: res.data.success,
        });
        
        
        // console.log('props modal: ', props);
        if(res.data.success === 1){
          props.modalCallback("addedit");
        }


      });

    }

    
  }

  function modalClose(){
    // console.log('props modal: ', props);
    props.modalCallback("close");
  }


  return (
    <>

      {/* <!-- GROUP MODAL START --> */}
      <div id="groupModal" class="modal">
        {/* <!-- Modal content --> */}
        <div class="modal-content">
          <div class="modalHeader">
            <h4>Add/Edit User Role</h4>
          </div>

          <div class="modalItem">
            <label>User Role *</label>
            <input
              type="text"
              id="RoleName"
              name="RoleName"
              class={errorObject.RoleName}
              placeholder="Enter user role name"
              value={currentRow.RoleName}
              onChange={(e) => handleChange(e)}
            />
          </div>
{/* 
          <div class="modalItem">
            <label for="">Discount (Amount)</label>
            <input
              type="number"
              id="DiscountAmount"
              name="DiscountAmount"
              class={errorObject.DiscountAmount}
              value={currentRow.DiscountAmount}
              onChange={(e) => handleChange(e)}
            />
          </div> */}

          {/* <div class="modalItem">
            <label for="">Discount (%)</label>
            <input
              type="number"
              id="DiscountPercentage"
              name="DiscountPercentage"
              class={errorObject.DiscountPercentage}
              value={currentRow.DiscountPercentage}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="modalItem">
            <label> Is Active?</label>
            <input 
              id="IsActive" 
              name="IsActive" 
              type = "checkbox" 
              checked={currentRow.IsActive} 
              onChange = {handleChangeCheck} 
            />
        
          </div> */}
          <div class="modalItem">

            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
            {props.currentRow.id && (<Button label={"Update"} class={"btnUpdate"} onClick={addEditAPICall} />)}
            {!props.currentRow.id && (<Button label={"Save"} class={"btnSave"} onClick={addEditAPICall} />)}
            
          </div>
        </div>
      </div>
      {/* <!-- GROUP MODAL END --> */}



    </>
  );
};

export default UserRoleAddEditModal;
