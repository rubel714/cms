import React, { useState } from "react";
import swal from "sweetalert";

//check user has permission in the entered page
export const checkUserPermission = (menukey) => {
  let isPermit = false;
  // menukey = 'receive-from-supplierreceive-from-supplier';
  let token = sessionStorage.getItem("token");
  let User_info = sessionStorage.getItem("User_info");
  // let UserAllowdMenuList = localStorage.getItem("UserAllowdMenuList");

  // console.log("UserAllowdMenuList token: ", token);
  // console.log("UserAllowdMenuList menukey: ", menukey);
  // console.log("UserAllowdMenuList User_info: ", User_info);

  if (!token) {
    //when not login then no need to redirect to NO PERMISSION PAGE /check-permission
    isPermit = false;
  } else if (!User_info) {
    //when not allow any menu but call this function then need to redirect to NO PERMISSION PAGE /check-permission
    isPermit = false;
    window.location.href = process.env.REACT_APP_BASE_NAME + `/check-permission`;
  } else {
    User_info = JSON.parse(User_info);
    // console.log('UserAllowdMenuList from IF User_info: ', User_info);
    let UserAllowdMenuList = User_info.UserAllowdMenuList;
    // console.log('UserAllowdMenuList from userinfo: ', UserAllowdMenuList);

    let hasPermission = UserAllowdMenuList.filter(function (menu) {
      // console.log('UserAllowdMenuList menu: ', menu);
      return menu.MenuKey === menukey;
    });
    //  console.log('UserAllowdMenuList length: ', hasPermission.length);
    //  console.log('UserAllowdMenuList result: ', hasPermission);

    if (hasPermission.length > 0) {
      //you have permission in this page
      isPermit = true;
      // console.log('UserAllowdMenuList you have permission in this page');
    } else {
      //you have no permission in this page
      // console.log('UserAllowdMenuList you have no permission in this page');
      isPermit = false;
      window.location.href =
        process.env.REACT_APP_BASE_NAME + `/check-permission`;
    }
  }

  return isPermit;
};

export const checkLogin = () => {
  // console.log("menukey",menukey);
  // console.log("rubel mea from check user access page");

  let token = sessionStorage.getItem("token");
  if (!token) {
    swal({
      title: "Oops!",
      text: "Token expired. Please login again",
      icon: "warning",
     
      buttons: ["No", "Yes"],
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        window.location.href = process.env.REACT_APP_BASE_NAME + `/login`;
        sessionStorage.clear();
      }
    });
  }
};



//check user has edit or view permission in the entered page
export const getUserActionPermission = (menukey) => {

  let permitType = 1; //view=1, edit=0
  let User_info = sessionStorage.getItem("User_info");


  if (User_info) {
    User_info = JSON.parse(User_info);
    // console.log('User_info: ', User_info.UserAllowdMenuList);
    let UserAllowdMenuList = User_info.UserAllowdMenuList;

    let hasPermission = UserAllowdMenuList.filter(function (menu) {
      return menu.MenuKey === menukey;
    });
    // console.log('hasPermission: ', hasPermission);


    if (hasPermission.length > 0) {
      // console.log('hasPermission: ', hasPermission, hasPermission[0].PermissionType);

      if(hasPermission[0].PermissionType == 1){
        permitType = 1;//view permission
      }else{
        permitType = 0;//edit permission
      }
    }
  }
  // console.log('hasPermission permitType: ', permitType);

  return permitType;
};


const CheckUserAccess = ({ ...props }) => {
  //   // const [RedirectLogin, setRedirectLogin] = React.useState(true);
  // const checkLogin = () => {
  //     let token = sessionStorage.getItem("token");
  //     if (!token) {
  //       swal({
  //         title: "Oops!",
  //         text: 'token expired. Please login again',
  //         icon: "warning",
  //         buttons: ["No", "Yes"],
  //         dangerMode: true,
  //       }).then((willDelete) => {
  //         if (willDelete) {
  //           window.location.href = process.env.REACT_APP_BASE_NAME+`/login`;
  //           sessionStorage.clear();
  //         }
  //       });
  //     }
  //   };

  //   // if(RedirectLogin){
  //   //   checkLogin();
  //   //   setRedirectLogin(false);
  //   // }

  return <></>;
};

export default CheckUserAccess;
