import React, { forwardRef, useRef, useEffect, useState } from "react";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import Autocomplete from "@material-ui/lab/Autocomplete";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";

import { Typography, TextField } from "@material-ui/core";
const InspectionReportEntryAddEditModal = (props) => {
  //console.log("props modal: ", props);
  const serverpage = "inspectionreportentry"; // this is .php server page
  const [currentRow, setCurrentRow] = useState(props.currentRow);
  const [errorObject, setErrorObject] = useState({});
  // const [currentFile, setCurrentFile] = useState(null);
  const UserInfo = LoginUserInfo();

  // const [CheckList, setCheckList] = useState(null);
  // const [currCheckId, setCurrCheckId] = useState(null);
 
  const baseUrl = process.env.REACT_APP_FRONT_URL;
  const [previewImage, setPreviewImage] = useState(
    `${baseUrl}image/transaction/placeholder.jpg`
  );

  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;
  const PDFGenerate = () => {
    let finalUrl = EXCEL_EXPORT_URL + "report/ReportGenerate_pdf.php";
    window.open(
      finalUrl +
      "?TransactionId=" +
      currentRow.id +
      "&TimeStamp=" +
      Date.now()
    );
  };


  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...currentRow };
    data[name] = value;
    setCurrentRow(data);
    setErrorObject({ ...errorObject, [name]: null });
  };

  const handleChangeMasterFile = (e) => {
    // const { name, value } = e.target;
    let file = e.target.files[0];
    // console.log('file: ', file);
    if (file) {

      let data = { ...currentRow };
      // data['CoverFileUrlUpload'] = file;
      // setCurrentRow(data);
      // console.log('data: ', data);


      let reader = new FileReader();
      // reader.onload = () => setPreview(reader.result);
      reader.readAsDataURL(file);
      reader.onload = (event) => {
        // setPreview(event.target.result);
        data['CoverFileUrlUpload'] = event.target.result;
        //  console.log('event.target.result: ', event.target.result);
        // console.log('From onload');
        setCurrentRow(data);
      };

      // setErrorObject({ ...errorObject, [name]: null });
    }
  };

  // const handleChangeManyDropDown = (name, value, Idx) => {
  //   let data = { ...currentRow };
  //   if (name === "CheckId") {
  //     data.Items[Idx].CheckId = value;
  //   }

  //   // setErrorObject({ ...errorObject, [name]: null });
  //   setCurrentRow(data);
  //   // console.log("data: ", data);
  // };

  // const handleChangeWidthHeight = (type, classname, Idx) => {
  //   let data = { ...currentRow };
  //   if (type === "width") {
  //     data.Items[Idx].RowNo = classname;
  //   } else if (type === "height") {
  //     data.Items[Idx].ColumnNo = classname;
  //   }
  //   setCurrentRow(data);
  // };

  // function addCheckBlock() {
  //   let data = { ...currentRow };
  //   let currTime = Date.now();
  //   let newCheck = {
  //     CheckId: 0,
  //     ColumnNo: "reportcheckblock-height-onethird",
  //     PhotoUrl: "placeholder.jpg",
  //     PhotoUrlChanged: "",
  //     PhotoUrlPreview: "",
  //     PhotoUrlUpload: "",
  //     RowNo: "reportcheckblock-width-half",
  //     SortOrder: data.Items.length + 1,
  //     TransactionId: currTime,
  //     TransactionItemId: currTime,
  //     autoId: -1,
  //   };

  //   data.Items.push(newCheck);
  //   setCurrentRow(data);
  // }

  // function deleteCheckBlock(Idx) {
  //   let data = { ...currentRow };
  //   delete data.Items[Idx];
  //   setCurrentRow(data);
  // }

  const validateForm = () => {
    let validateFields = [];
    validateFields = ["InvoiceNo", "TransactionDate", "CoverFilePages"];
    let errorData = {};
    let isValid = true;
    validateFields.map((field) => {
      if (!currentRow[field]) {
        errorData[field] = "validation-style";
        isValid = false;
      }
    });
    setErrorObject(errorData);
    return isValid;
  };

  function addEditAPICall() {
    if (validateForm()) {

        console.log('currentRow: ', currentRow);


      let params = {
        action: "dataAddEdit",
        lan: language(),
        UserId: UserInfo.UserId,
        ClientId: UserInfo.ClientId,
        BranchId: UserInfo.BranchId,
        rowData: currentRow,
      };

      apiCall.post(serverpage, { params }, apiOption()).then((res) => {
        props.masterProps.openNoticeModal({
          isOpen: true,
          msg: res.data.message,
          msgtype: res.data.success,
        });

        if (res.data.success === 1) {
          props.modalCallback("addedit");
        }
      });
    }
  }

  function modalClose() {
    // console.log("props modal: ", props);
    props.modalCallback("close");
  }

  // const handleFileChangeManyFile = (e, Idx) => {
  //   let data = { ...currentRow };
  //   let file = e.target.files[0];

  //   if (file) {
  //     data.Items[Idx].PhotoUrlUpload = file;
  //     data.Items[Idx].PhotoUrlChanged =
  //       data.Items[Idx].TransactionItemId + "_" + file.name;

  //     let reader = new FileReader();
  //     reader.readAsDataURL(file);
  //     reader.onload = (event) => {
  //       data.Items[Idx].PhotoUrlPreview = event.target.result;
  //       setCurrentRow(data);
  //     };
  //   } else {
  //     data.Items[Idx].PhotoUrlPreview = "";
  //     data.Items[Idx].PhotoUrlUpload = null;
  //     data.Items[Idx].PhotoUrlChanged = "";
  //     setCurrentRow(data);
  //   }
  // };


  return (
    <>
      {/* <!-- GROUP MODAL START --> */}
      <div id="groupModal" class="modal">
        {/* <!-- Modal content --> */}
        <div class="modal-content-reportblock">
          <div class="modalHeader">
            <h4>Add/Edit Inspection Report</h4>
          </div>
          <div class="contactmodalBody pt-10">
            <label>Report Number *</label>
            <input
              type="text"
              id="InvoiceNo"
              name="InvoiceNo"
              class={errorObject.InvoiceNo}
              placeholder="Enter Report Number"
              value={currentRow.InvoiceNo}
              onChange={(e) => handleChange(e)}
            />

            <label>Report Date *</label>
            <input
              type="date"
              id="TransactionDate"
              name="TransactionDate"
              // style={{width:"20%"}}
              class={errorObject.TransactionDate}
              placeholder="Enter Report Date"
              value={currentRow.TransactionDate}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="contactmodalBody pt-10">
            <label>Cover File *</label>
            <input
              type="file"
              id="PhotoUrl"
              name="PhotoUrl"
              accept="application/pdf"
              style={{"color": "transparent"}}
              //onChange={handleFileChange}
              //onChange={(e) => handleFileChange(e, "PhotoUrl")}
              onChange={(e) => handleChangeMasterFile(e)}
            />

            <label>Cover File Page Count *</label>
            <input
              type="text"
              id="CoverFilePages"
              name="CoverFilePages"
              class={errorObject.CoverFilePages}
              placeholder="Enter Cover File Page Count"
              value={currentRow.CoverFilePages}
              onChange={(e) => handleChange(e)}
            />
          </div>

          <div class="modalItem">
            {/* <Button
              label={"Report Generate"}
              class={"btnClose"}
              onClick={PDFGenerate}
            /> */}
            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
            {props.currentRow.id && (
              <Button
                label={"Update"}
                class={"btnUpdate"}
                onClick={addEditAPICall}
              />
            )}
            {!props.currentRow.id && (
              <Button
                label={"Save"}
                class={"btnSave"}
                onClick={addEditAPICall}
              />
            )}
          </div>
        </div>
      </div>
      {/* <!-- GROUP MODAL END --> */}
    </>
  );
};

export default InspectionReportEntryAddEditModal;
