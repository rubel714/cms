import React, { useState } from "react";

// Auth token & services
import * as Service from "../services/Service.js";

// core components
import BeforeLoginNavbar from "components/Navbars/BeforeLoginNavbar.js";
import AfterLoginNavbar from "components/Navbars/AfterLoginNavbar.js";
import IndexHeader from "components/Headers/IndexHeader.js";
import DarkFooter from "components/Footers/DarkFooter.js";
import HomePage from "./screens/HomePage";

function Index(props) {
  React.useEffect(() => {
    document.body.classList.add("index-page");
    document.body.classList.add("sidebar-collapse");
    document.documentElement.classList.remove("nav-open");
    window.scrollTo(0, 0);
    document.body.scrollTop = 0;
    return function cleanup() {
      document.body.classList.remove("index-page");
      document.body.classList.remove("sidebar-collapse");
    };
  });

  return (
    <>
      {Service.default.authToken() != null ? (
        <AfterLoginNavbar {...props} />
      ) : (
        <BeforeLoginNavbar {...props} />
      )}
      <div className="wrapper">
        <IndexHeader {...props} />
        <div className="main">
          <HomePage {...props}  />
        </div>
        <DarkFooter  {...props}  />
      </div>
    </>
  );
}

export default Index;
