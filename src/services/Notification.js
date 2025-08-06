import React, { useState, useEffect } from "react";
import MuiAlert from "@material-ui/lab/Alert";
import { Snackbar } from "@material-ui/core";
const Alert = React.forwardRef(function Alert(props, ref) {
  return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
});

const Notification = ({ ...props }) => {
  const [duration, setDuration] = React.useState(3000);

  return (
    <Snackbar
      anchorOrigin={{
        vertical: "bottom",
        horizontal: "right",
      }}
      open={props.msgObj.isOpen}
      autoHideDuration={props.msgObj.duration==undefined?duration:props.msgObj.duration}
      onClose={props.closeNoticeModal}
    >
      <Alert
        onClose={props.closeNoticeModal}
        severity={
          props.msgObj.msgtype == 1
            ? "success"
            : props.msgObj.msgtype == 0
            ? "error"
            : ""
        }
        sx={{
          width:
            props.msgObj.msgtype == 1
              ? "100%"
              : props.msgObj.msgtype == 0
              ? "100%"
              : "0%",
        }}
      >
        {props.msgObj.msg}
      </Alert>
    </Snackbar>
  );
};

export default Notification;
