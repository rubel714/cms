import React from "react";
import { Switch, Route, useRouteMatch } from "react-router-dom";
import AfterLoginNavbar from "components/Navbars/AfterLoginNavbar";
import Notification from "../../../services/Notification";
import { checkLogin, checkUserPermission, getUserActionPermission } from "../../../services/CheckUserAccess";
import DarkFooter from "../../../components/Footers/DarkFooter.js";
import PaymentReceive from "./PaymentReceive";

const Index = (props) => {
  const { path } = useRouteMatch();
  const menukey = "paymentreceive";

  const [RedirectLogin, setRedirectLogin] = React.useState(true);
  const [hasUserPermission, setHasUserPermission] = React.useState(false);

  if (RedirectLogin) {
    setHasUserPermission(checkUserPermission(menukey));
    checkLogin();
    setRedirectLogin(false);
  }

  const permissionType = getUserActionPermission(menukey);

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
                <PaymentReceive {...props} openNoticeModal={openNoticeModal} permissionType={permissionType} />
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
