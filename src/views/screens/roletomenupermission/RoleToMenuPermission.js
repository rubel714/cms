import React, { forwardRef, useRef, useContext, useEffect } from "react";
import swal from "sweetalert";
import { DeleteOutline, Edit, ViewList } from "@material-ui/icons";

import { Button } from "../../../components/CustomControl/Button";
import moment from "moment";

import {
  Typography,
  TextField,
} from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

const RoleToMenuPermission = (props) => {
  const serverpage = "roletomenupermission"; // this is .php server page
  const permissionType = props.permissionType;
  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [listEditPanelToggle, setListEditPanelToggle] = useState(true); //when true then show list, when false then show add/edit

  const [roleList, setRoleList] = useState([]);
  const [currRoleId, setCurrRoleId] = useState(1);

  const [currentDate, setCurrentDate] = useState(
    moment().format("YYYY-MM-DD")
  );


  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data master list


  const UserInfo = LoginUserInfo();

    /* =====Start of Excel Export Code==== */
    const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;

    const PrintPDFExcelExportFunction = () => {
      let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

      let curRoleName = roleList.filter((obj)=> obj.id==currRoleId);

      window.open(
        finalUrl +
          "?action=RoleToMenuPermissionExport" +
          "&reportType=excel" +
          "&ClientId=" + UserInfo.ClientId +
          "&BranchId=" + UserInfo.BranchId +
          "&RoleId=" + currRoleId +   
          "&RoleName=" + curRoleName[0].name +   
          "&TimeStamp=" +
          Date.now()
      );
    };
    /* =====End of Excel Export Code==== */
  

  /**Get data for table list */
  function getDataList(currRoleId) {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      RoleId: currRoleId,     
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };
    // console.log('LoginUserInfo params: ', params);

    ExecuteQuery(serverpage, params);
  }

  if (bFirst) {
    /**First time call for datalist */
   
    setBFirst(false);
    getRoleList();
  }


  function getRoleList() {
    let params = {
      action: "RoleList",
      lan: language(),
      UserId: UserInfo.UserId
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {

      console.log(res.data.datalist);
     /*  setRoleList(
        [{ id: "", name: "Select a Role" }].concat(res.data.datalist)
      ); */

      setRoleList(res.data.datalist); 
      getDataList(currRoleId);

    });
  }

  const masterColumnList = [
    { field: "PermissionType", label: "PermissionType", visible: false, align: "center" },
    { field: "rownumber", label: "SL", align: "center", width: "5%" },
    // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },
    {
      field: "custom",
      label: "Access",
      width: "5%",
      align: "center",
      visible: true,
      // sort: false,
      // filter: false,
    },
    {
      field: "menuname",
      label: "Menu Name",
      width: "75%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "MenuType",
      label: "Menu For",
      width: "15%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

  ];

  /** Action from table row buttons*/
  function actioncontrolmaster(rowData) {

    return (
      <>

      
        {rowData.PermissionType === 0 && (
          <span
          //className={"table-edit-icon"}
          className={"table-delete-icon clickable font-bold"}
          onClick={() => {
            assignData(rowData);
          }}
        >
          No
        </span>
          // <Edit
          //   className={"table-edit-icon"}
          //   onClick={() => {
          //     assignData(rowData);
          //   }}
          // />
        )}

        {rowData.PermissionType === 1 && (
           <span
           className={"table-view-icon clickable font-bold"}
           onClick={() => {
            assignData(rowData);
           }}
         >
           View
         </span>
          // <DeleteOutline
          //   className={"table-delete-icon"}
          //   onClick={() => {
          //     deleteData(rowData);
          //   }}
          // />
        )}


        {rowData.PermissionType === 2 && (
           <span
           className={"table-edit-icon clickable font-bold"}
           onClick={() => {
            assignData(rowData);
           }}
         >
           Edit
         </span>
          // <DeleteOutline
          //   className={"table-delete-icon"}
          //   onClick={() => {
          //     deleteData(rowData);
          //   }}
          // />
        )}

        {/* {rowData.BPosted === 1 && (
          <ViewList
            className={"table-view-icon"}
            onClick={() => {
              viewData(rowData);
            }}
          />
        )} */}

      </>
    );
  }

/*   useEffect(() => {
  
  }, []);
 */


  // const deleteData = (rowData) => {
  //   swal({
  //     title: "Are you sure?",
  //     text: "You want to un-assign this menu!",
  //     icon: "warning",
  //     buttons: {
  //       confirm: {
  //         text: "Yes",
  //         value: true,
  //         visible: true,
  //         className: "",
  //         closeModal: true,
  //       },
  //       cancel: {
  //         text: "No",
  //         value: null,
  //         visible: true,
  //         className: "",
  //         closeModal: true,
  //       },
  //     },
  //     dangerMode: true,
  //   }).then((allowAction) => {
  //     if (allowAction) {
  //       deleteApi(rowData);
  //     }
  //   });
  // };

  // function deleteApi(rowData) {
  //   let params = {
  //     action: "deleteData",
  //     lan: language(),
  //     UserId: UserInfo.UserId,
  //     rowData: rowData,
  //   };

  //   apiCall.post(serverpage, { params }, apiOption()).then((res) => {
  //     // console.log('res: ', res);
  //     props.openNoticeModal({
  //       isOpen: true,
  //       msg: res.data.message,
  //       msgtype: res.data.success,
  //     });
  //     getDataList(currRoleId);
  //   });
  // }

 

  const assignData = (rowData) => {
    if(permissionType !== 0){
      /**when permissionType=0 then EDIT otherwise VIEW*/
      return;
    }
   
    if(!currRoleId){

      props.openNoticeModal({
        isOpen: true,
        msg: "Please, Select a Role First",
        msgtype: 0,
      });

      return;
    }

    assignApi(rowData);
    // swal({
    //   title: "Are you sure?",
    //   text: "You want to assign menu to this role!",
    //   icon: "warning",
    //   buttons: {
    //     confirm: {
    //       text: "Yes",
    //       value: true,
    //       visible: true,
    //       className: "",
    //       closeModal: true,
    //     },
    //     cancel: {
    //       text: "No",
    //       value: null,
    //       visible: true,
    //       className: "",
    //       closeModal: true,
    //     },
    //   },
    //   dangerMode: true,
    // }).then((allowAction) => {
    //   if (allowAction) {
    //     assignApi(rowData);
    //   }
    // });
  };

  function assignApi(rowData) {

    console.log('UserInfo: ', UserInfo);

    
    let params = {
      action: "assignData",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      RoleId: currRoleId,
      rowData: rowData,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      // console.log('res: ', res);
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });
      getDataList(currRoleId);
    });
  }

 
  const handleChangeChoosenMany = (name, value) => {
    setCurrRoleId(value);
    getDataList(value);
  };

  return (
    <>
      <div class="bodyContainer">
        <div class="topHeader">
          <h4>
            Home ❯ Admin ❯ Role to Menu Permission
          </h4>
        </div>

        {listEditPanelToggle && (
          <>
        
            <div class="searchAdd">
              {/* <input type="text" placeholder="Search Product Group"/> */}
              {/* <button
                onClick={() => {
                  addData();
                }}
                className="btnAdd"
              >
                ADD
              </button> */}
               <div>
              <label>Role:</label>
              </div>

              <div class="formControl-filter">
               
                  {/* <label>Role:</label> */}
              
                {/* <div class="plusGroup"> */}
                <div class="">
                  <Autocomplete
                    autoHighlight
                    // freeSolo
                    disableClearable
                    className="chosen_dropdown"
                    id="RoleId"
                    name="RoleId"
                    autoComplete
                    options={roleList ? roleList : []}
                    getOptionLabel={(option) => option.name}
                    defaultValue={{ id: 1, name: "Super Admin" }}
                  
                    onChange={(event, valueobj) =>
                      handleChangeChoosenMany(
                        "RoleId",
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
             

              {/* <Button label={"ADD"} class={"btnAdd"} onClick={addData} /> */}
          <Button label={"Export"} class={"btnPrint"} onClick={PrintPDFExcelExportFunction} />

            </div>


            {/* <!-- ####---Master invoice list---####s --> */}
             <div class="subContainer tableHeight">
              <div className="App">
                <CustomTable
                  columns={masterColumnList}
                  rows={dataList ? dataList : {}}
                  actioncontrol={actioncontrolmaster}
                  ispagination={false}
                />
              </div>
            </div> 
          </>
        )}

        
      </div>
    </>
  );
};

export default RoleToMenuPermission;
