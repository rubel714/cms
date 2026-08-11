import React from "react";
import { Switch, Route, useRouteMatch } from "react-router-dom";
import AfterLoginNavbar from "components/Navbars/AfterLoginNavbar";
import {
  checkLogin,
  checkUserPermission,
  getUserActionPermission,
} from "../../../services/CheckUserAccess.js";
import DarkFooter from "../../../components/Footers/DarkFooter.js";
import InvoiceAdjustmentReport from "./InvoiceAdjustmentReport.js";

const Index = (props) => {
  const { path } = useRouteMatch();
  const menukey = "invoiceadjustmentreport"; // this is in t_menu table

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

  return (
    hasUserPermission && (
      <div>
        <AfterLoginNavbar {...props} />

        <div class="pb-30">
          <Switch>
            <Route
              path={`${path}/`}
              render={(props) => (
                <InvoiceAdjustmentReport
                  {...props}
                  permissionType={permissionType}
                />
              )}
            ></Route>
          </Switch>
        </div>
        <DarkFooter {...props} />
      </div>
    )
  );
};

export default Index;
