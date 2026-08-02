import React from "react";
import { Switch, Route, useRouteMatch } from "react-router-dom";
import AfterLoginNavbar from "components/Navbars/AfterLoginNavbar";
import Notification from "../../../services/Notification.js";
import {
  checkLogin,
  checkUserPermission,
  getUserActionPermission,
} from "../../../services/CheckUserAccess.js";
import DarkFooter from "../../../components/Footers/DarkFooter.js";
import InvoiceApproval from "./InvoiceApproval.js";

const Index = (props) => {
  const { path } = useRouteMatch();
  const menukey = "invoiceapproval"; // this is in t_menu table

  const [RedirectLogin, setRedirectLogin] = React.useState(true);
  const [hasUserPermission, setHasUserPermission] = React.useState(false);

  if (RedirectLogin) {
    setHasUserPermission(checkUserPermission(menukey)); // To check user has permission in this page
    checkLogin();
    setRedirectLogin(false);
  }

  //This is for user has VIEW/EDIT permission in this page start
  const permissionType = getUserActionPermission(menukey);
  //This is for user has VIEW/EDIT permission in this page end

  const [msgObj, setMsgObj] = React.useState({
    isOpen: false,
  });

  const openNoticeModal = (obj) => {
    setMsgObj(obj);
  };

  const closeNoticeModal = (event, reason) => {
    if (reason === "clickaway") {
      return;
    }
    setMsgObj({ isOpen: false });
  };

  return (
    hasUserPermission && (
      <div>
        <AfterLoginNavbar {...props} />

        <div class="pb-30">
          <Switch>
            <Route
              path={`${path}/`}
              render={(props) => (
                <InvoiceApproval
                  {...props}
                  openNoticeModal={openNoticeModal}
                  permissionType={permissionType}
                />
              )}
            ></Route>
          </Switch>
          <Notification
            closeNoticeModal={closeNoticeModal}
            msgObj={msgObj}
            {...props}
          ></Notification>
        </div>
        <DarkFooter {...props} />
      </div>
    )
  );
};

export default Index;
