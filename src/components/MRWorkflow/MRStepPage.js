import React from "react";
import AfterLoginNavbar from "components/Navbars/AfterLoginNavbar";
import Notification from "../../services/Notification";
import { checkLogin, checkUserPermission, getUserActionPermission } from "../../services/CheckUserAccess";
import DarkFooter from "../Footers/DarkFooter.js";

/** Page shell (login/permission check, navbar, notification, footer) for MR workflow steps. */
const MRStepPage = ({ menukey, render, ...props }) => {
  const [RedirectLogin, setRedirectLogin] = React.useState(true);
  const [hasUserPermission, setHasUserPermission] = React.useState(false);
  const [msgObj, setMsgObj] = React.useState({ isOpen: false });

  if (RedirectLogin) {
    setHasUserPermission(checkUserPermission(menukey));
    checkLogin();
    setRedirectLogin(false);
  }

  const permissionType = getUserActionPermission(menukey);

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
        <div className="pb-30">
          {render({ ...props, openNoticeModal: setMsgObj, permissionType })}
          <Notification closeNoticeModal={closeNoticeModal} msgObj={msgObj} {...props} />
        </div>
        <DarkFooter {...props} />
      </div>
    )
  );
};

export default MRStepPage;
