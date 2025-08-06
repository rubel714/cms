import React from "react";

// material components
// import "react-date-range/dist/styles.css"; // main css file
// import "react-date-range/dist/theme/default.css"; // theme css file
// import { makeStyles } from "@material-ui/core/styles";

// react-tabulator
// import "react-tabulator/lib/styles.css"; // required styles
// import "react-tabulator/lib/css/tabulator.min.css"; // theme

import * as Service from "../../services/Service.js";

// core components
import AfterLoginNavbar from "components/Navbars/AfterLoginNavbar.js";
import BeforeLoginNavbar from "components/Navbars/BeforeLoginNavbar.js";
import DarkFooter from "../../components/Footers/DarkFooter.js";

const ReportPage = (props) => {

  return (
    <>
      {Service.default.authToken() != null ? (
        <AfterLoginNavbar {...props} />
      ) : (
        <BeforeLoginNavbar {...props} />
      )}



        
        <div class="permission_card">
          <h3>{"You have no permission to this page."}</h3>
          {/* <p>
            Lorem Ipsum is simply dummy text of the printing and typesetting
            industry. Lorem Ipsum has been the industry's standard dummy text
            ever since the 1500s, when an unknown printer took a galley of type
            and scrambled it to make a type specimen book. It has survived not
            only five centuries, but also the leap into electronic typesetting,
            remaining essentially unchanged. It was popularised in the 1960s
            with the release of Letraset sheets containing Lorem Ipsum passages,
            and more recently with desktop publishing software like Aldus
            PageMaker including versions of Lorem Ipsum.
          </p> */}

          <div class="pt-10">
          <a class="goto_btn" href={process.env.REACT_APP_BASE_NAME} >
             {"Go To Home"}
          </a>
          </div>

        </div>

      <DarkFooter  {...props}  />

    </>


  );
};


export default ReportPage;