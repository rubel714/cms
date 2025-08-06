/*eslint-disable*/
import React, { useState } from "react";

// reactstrap components
// import { Container } from "reactstrap";
// core components

// import FadeLoader from "react-spinners/FadeLoader";

function IndexHeader() {
  // let pageHeader = React.createRef();
  // let [loading, setLoading] = useState(true);
  // let [color, setColor] = useState("#ffffff");

  // React.useEffect(() => {
  //   if (window.innerWidth > 991) {
  //     const updateScroll = () => {
  //       let windowScrollTop = window.pageYOffset / 3;
  //       pageHeader.current.style.transform =
  //         "translate3d(0," + windowScrollTop + "px,0)";
  //     };
  //     window.addEventListener("scroll", updateScroll);
  //     return function cleanup() {
  //       window.removeEventListener("scroll", updateScroll);
  //     };
  //   }
  // });
  // React.useEffect(() => {
  //   setLoading(false);
  // }, []);

  return (
    <>
      <h1>Credit Management System</h1>
      <h3>Integrated visit plan management</h3>

      {/* {loading ? (
        <div className="loader-div">
          <FadeLoader
            color={color}
            loading={loading}
            css={{ top: "50%", left: "50%" }}
            size={250}
          />
        </div>
      ) : null} */}

      {/* <div className="page-header">
        <div
          className="page-header-image"
          style={{
            backgroundImage: "url(" + require("assets/img/bg8.jpg") + ")",
          }}
          ref={pageHeader}
        ></div>
        <Container>
          <div className="content-center brand">
            <h1 className="h1-seo">Inspection</h1>
            <h3>Integrated Receive, sales and stock management</h3>
          </div>
        </Container>
      </div> */}
    </>
  );
}

export default IndexHeader;
