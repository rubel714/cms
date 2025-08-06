import React, { forwardRef, useRef } from "react";
// import "assets/css/custom.css";

const LoadingSpinnerOpaque = () => {
  return (
    <div className="sw_preloader_area">
      <div className="sw_preloader">
        <span className="lds-dual-ring"></span>
      </div>
    </div>
  );
};

export default LoadingSpinnerOpaque;
