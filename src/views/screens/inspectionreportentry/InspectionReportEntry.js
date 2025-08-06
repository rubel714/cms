import React, { forwardRef, useRef } from "react";
import swal from "sweetalert";
import {
  DeleteOutline,
  Edit,
  AddAPhoto,
  PictureAsPdf,
} from "@material-ui/icons";
import { Button } from "../../../components/CustomControl/Button";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { Typography, TextField } from "@material-ui/core";

import CustomTable from "components/CustomTable/CustomTable";
import {
  apiCall,
  apiCallReport,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import ExecuteQueryHook from "../../../components/hooks/ExecuteQueryHook";
import moment from "moment";
import InspectionReportEntryAddEditModal from "./InspectionReportEntryAddEditModal";

const InspectionReportEntry = (props) => {
  const serverpage = "inspectionreportentry"; // this is .php server page

  const { useState } = React;
  const [bFirst, setBFirst] = useState(true);
  const [currentRow, setCurrentRow] = useState([]);
  const [currentRowDelete, setCurrentRowDelete] = useState([]);
  const [showModal, setShowModal] = useState(false); //true=show modal, false=hide modal
  const [showMany, setShowMany] = useState(false); //true=show, false=hide many panel
  const [CheckList, setCheckList] = useState(null);
  

// handleChangeWidthHeight
  const { isLoading, data: dataList, error, ExecuteQuery } = ExecuteQueryHook(); //Fetch data
  const UserInfo = LoginUserInfo();
  const [selectedDate, setSelectedDate] = useState(
    //new Date()
    moment().format("YYYY-MM-DD")
  );

  /* =====Start of Excel Export Code==== */
  const EXCEL_EXPORT_URL = process.env.REACT_APP_API_URL;
  const baseUrl = process.env.REACT_APP_FRONT_URL;
  const PDFGenerate = (TransactionId) => {
    // if (!chkValidation()) {
    //   return;
    // }
    // console.log("currentRow:",currentRow);
    let finalUrl = EXCEL_EXPORT_URL + "report/ReportGenerate_pdf.php";
    window.open(
      finalUrl + "?TransactionId=" + TransactionId + "&TimeStamp=" + Date.now()
    );
  };


  // backend\report\ReportGenerate_pdf.php
  function PDFGenerate1(TransactionId) {
    let params = {
      action: "export",
      lan: language(),
      UserId: UserInfo.UserId,
      TransactionId: TransactionId,
    };

    // apiCall.post("productgroup", { params }, apiOption()).then((res) => {
    apiCallReport.post('ReportGenerate_pdf.php', { params }, apiOption()).then((res) => {
      console.log("res: ", res);

      // window.open(finalUrl + "?TransactionId=" + TransactionId + "&TimeStamp=" + Date.now());
      // props.openNoticeModal({
      //   isOpen: true,
      //   msg: res.data.message,
      //   msgtype: res.data.success,
      // });
      // getDataList();
    });
  }


  // 	$.ajax({
	// 	type: "POST",
	// 	url: baseUrl + "monthlylogisticsreportwizardserver.php",
	// 	data: {
	// 		operation: 'combineLogisticsReport',
	// 		baseUrl: baseUrl,
	// 		lan: lan,
	// 		MonthId :MonthId,
	// 		MonthName : monthList[parseInt(MonthId)-1],
	// 		YearId : YearId,
	// 		CountryId : CountryId
	// 	},
	// 	success: function(response) {
	// 		//console.log(response);
	// 		$("#tab9loader").hide();
	// 		window.open( baseUrl + 'report/pdfslice/Health_Commodity_Dashboard_Monthly_Logistics_Report_'+CountryId+'_'+monthList[parseInt(MonthId)-1]+'_'+YearId+'.pdf');
	// 	}
		
	// });


  // const PrintPDFExcelExportFunction = (reportType) => {
  //   let finalUrl = EXCEL_EXPORT_URL + "report/print_pdf_excel_server.php";

  //   window.open(
  //     finalUrl +
  //       "?action=UserExport" +
  //       "&reportType=excel" +
  //       "&ClientId=" +
  //       UserInfo.ClientId +
  //       "&BranchId=" +
  //       UserInfo.BranchId +
  //       "&TimeStamp=" +
  //       Date.now()
  //   );
  // };
  /* =====End of Excel Export Code==== */

  const columnList = [
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },
    {
      field: "InvoiceNo",
      label: "Report Number",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "TransactionDate",
      label: "Report Date",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "25%",
    },
    {
      field: "UserName",
      label: "Reported By",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
      width: "30%",
    },
    {
      field: "CoverFileUrlStatus",
      label: "Cover File Uploaded",
      align: "center",
      visible: true,
      sort: true,
      filter: true,
      width: "12%",
    },
    {
      field: "StatusName",
      label: "Report Status",
      align: "center",
      visible: false,
      sort: true,
      filter: true,
      width: "12%",
    },
    {
      field: "custom",
      label: "Action",
      width: "12%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
  ];

  if (bFirst) {
    /**First time call for datalist */
    getDataList();
    setBFirst(false);
  }

  /**Get data for table list */
  function getDataList() {
    let params = {
      action: "getDataList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };
    // console.log('LoginUserInfo params: ', params);

    ExecuteQuery(serverpage, params);
  }

  /** Action from table row buttons*/
  function actioncontrol(rowData) {
    return (
      <>
        <PictureAsPdf
          className={"table-generate-icon"}
          onClick={() => {
            PDFGenerate(rowData.id);
          }}
        />

        <AddAPhoto
          className={"table-addimg-icon"}
          onClick={() => {
            editDataForCheck(rowData);
          }}
        />

        <Edit
          className={"table-edit-icon"}
          onClick={() => {
            editData(rowData);
          }}
        />

        <DeleteOutline
          className={"table-delete-icon"}
          onClick={() => {
            deleteData(rowData);
          }}
        />
      </>
    );
  }

  const addData = () => {
    setCurrentRow({
      id: "",
      TransactionTypeId: 1,
      TransactionDate: selectedDate,
      InvoiceNo: "",
      CoverFileUrl: "",
      CoverFileUrlUpload: "",
      CoverFilePages: "",
      StatusId: 1,
      ManyImgPrefix: Date.now(),
      FormData: null,
      Items: [],
    });
    openModal();
  };

  const editData = (rowData) => {
    setCurrentRow(rowData);
    openModal();
  };

  function openModal() {
    setShowModal(true); //true=modal show, false=modal hide
  }

  function modalCallback(response) {
    getDataList();
    setShowModal(false); //true=modal show, false=modal hide
  }

  const editDataForCheck = (rowData) => {
    setCurrentRow(rowData);
    openManyPanel();
  };

  function openManyPanel() {
    setShowMany(true); //true= show, false= hide many panel
  }

  function manyPanelCallback(response) {
    getDataList();
    setShowMany(false); //true= show, false= hide many panel
  }

  const deleteData = (rowData) => {
    swal({
      title: "Are you sure?",
      text: "Once deleted, you will not be able to recover this data!",
      icon: "warning",
      buttons: {
        confirm: {
          text: "Yes",
          value: true,
          visible: true,
          className: "",
          closeModal: true,
        },
        cancel: {
          text: "No",
          value: null,
          visible: true,
          className: "",
          closeModal: true,
        },
      },
      dangerMode: true,
    }).then((allowAction) => {
      if (allowAction) {
        deleteApi(rowData);
      }
    });
  };

  function deleteApi(rowData) {
    let params = {
      action: "deleteData",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      rowData: rowData,
    };

    // apiCall.post("productgroup", { params }, apiOption()).then((res) => {
    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      console.log("res: ", res);
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });
      getDataList();
    });
  }

  React.useEffect(() => {
    getCheck();
  }, []);

  function getCheck() {
    let params = {
      action: "CheckList",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setCheckList(
        [{ id: "", name: "Select Check Name" }].concat(res.data.datalist)
      );

      // setCurrCheckId(selectCheckId);
    });
  }

  const handleFileChangeManyFile = (e, Idx) => {
    // console.log("e.target",e.target);
    // console.log("e.target",e.target.files);
    // console.log("e.target",e.target.files[0]);
    // console.log("==========================");

    let data = { ...currentRow };
    // let dataManyFiles = { ...manyFiles };
    let file = e.target.files[0];
    // dataManyFiles[name] = file;
    // data["CoverFileUrl"]=file;
    // console.log('file: ', file);
    if (file) {
      // console.log('file: ', file.name);

      data.Items[Idx].PhotoUrlUpload = file;
      data.Items[Idx].PhotoUrlChanged =
        data.Items[Idx].TransactionItemId + "_" + file.name;

      let reader = new FileReader();
      // reader.onload = () => setPreview(reader.result);
      reader.readAsDataURL(file);
      reader.onload = (event) => {
        // setPreview(event.target.result);
        data.Items[Idx].PhotoUrlPreview = event.target.result;
        //  console.log('event.target.result: ', event.target.result);
        // console.log('From onload');
        setCurrentRow(data);
      };
    } else {
      data.Items[Idx].PhotoUrlPreview = "";
      data.Items[Idx].PhotoUrlUpload = null;
      data.Items[Idx].PhotoUrlChanged = "";
      setCurrentRow(data);
    }
    // console.log('after end');

    // setCurrentRow(data);
    // console.log('data for PHOTO---------------: ', data);
    // setManyFiles(dataManyFiles);
  };

  const handleChangeManyDropDown = (name, value, Idx) => {
    // console.log("Idx: ", Idx);
    // console.log("value: ", value);
    // console.log("name: ", name);
    let data = { ...currentRow };
    if (name === "CheckId") {
      data.Items[Idx].CheckId = value;
    }

    // setErrorObject({ ...errorObject, [name]: null });
    setCurrentRow(data);
    // console.log("data: ", data);
  };

  const handleChangeWidthHeight = (type, classname, Idx) => {
    // console.log("type: ", type);
    // console.log("classname: ", classname);
    let data = { ...currentRow };
    if (type === "width") {
      data.Items[Idx].RowNo = classname;
    } else if (type === "height") {
      data.Items[Idx].ColumnNo = classname;
    }
    setCurrentRow(data);
    // console.log("data: ", data);
  };

  function addCheckBlock() {
    let data = { ...currentRow };
    let currTime = Date.now();
    let newCheck = {
      CheckId: 0,
      ColumnNo: "reportcheckblock-height-onethird",
      PhotoUrl: "placeholder.jpg",
      PhotoUrlChanged: "",
      PhotoUrlPreview: "",
      PhotoUrlUpload: "",
      RowNo: "reportcheckblock-width-half",
      SortOrder: data.Items.length + 1,
      TransactionId: currTime,
      TransactionItemId: currTime,
      autoId: -1,
    };

    // console.log('newCheck: ', newCheck);
    data.Items.push(newCheck);
    setCurrentRow(data);
    // console.log("data: ", data.Items);
  }

  function deleteCheckBlock(Idx) {
    // console.log("Idx: ", Idx);
    let data = { ...currentRow };

    let dataDelete = { ...currentRowDelete };
    dataDelete[data.Items[Idx].TransactionItemId]=data.Items[Idx];
    setCurrentRowDelete(dataDelete);
    
    delete data.Items[Idx];
    setCurrentRow(data);
    console.log("dataDelete: ", dataDelete);
    // console.log("data: ", data.Items);
  }

  // const validateForm = () => {
  //   let validateFields = [];
  //   validateFields = ["InvoiceNo", "TransactionDate", "CoverFilePages"];
  //   let errorData = {};
  //   let isValid = true;
  //   validateFields.map((field) => {
  //     if (!currentRow[field]) {
  //       errorData[field] = "validation-style";
  //       isValid = false;
  //     }
  //   });
  //   setErrorObject(errorData);
  //   // console.log('errorData: ', errorData);
  //   return isValid;
  // };

  function addEditAPICall() {
    // if (validateForm()) {
    //uploadManyFiles(); /////////////////////////////////////////////////////

    let params = {
      action: "dataAddEditMany",
      lan: language(),
      UserId: UserInfo.UserId,
      ClientId: UserInfo.ClientId,
      BranchId: UserInfo.BranchId,
      rowData: currentRow,
      currentRowDelete: currentRowDelete,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });

      if (res.data.success === 1) {
        manyPanelCallback("addeditMany");
      }
    });
    // }
  }

  return (
    <>
      {!showMany && (<div class="bodyContainer">
        {/* <!-- ######-----TOP HEADER-----####### --> */}
        <div class="topHeader">
          <h4>Home ❯ Report Entry ❯ Inspection Report Entry</h4>
        </div>

        {/* <!-- TABLE SEARCH AND GROUP ADD --> */}
        <div class="searchAdd">
          {/* <input type="text" placeholder="Search Product Group"/> */}
          {/* <label></label> */}
          {/* <button
            onClick={() => {
              addData();
            }}
            className="btnAdd"
          >
            ADD
          </button> */}

          {/* <Button
            label={"Export"}
            class={"btnPrint"}
            onClick={PrintPDFExcelExportFunction}
          /> */}
          <Button label={"ADD"} class={"btnAdd"} onClick={addData} />
        </div>

        {/* <!-- ####---THIS CLASS IS USE FOR TABLE GRID PRODUCT INFORMATION---####s --> */}
        <div class="subContainer tableHeight">
          <div className="App">
            <CustomTable
              columns={columnList}
              rows={dataList ? dataList : {}}
              actioncontrol={actioncontrol}
            />
          </div>
        </div>
      </div>)}
      {/* <!-- BODY CONTAINER END --> */}

      {showMany && (
        <div class="bodyContainer">
          {/* <!-- GROUP MODAL START --> */}
          <div id="groupModal" class="modalz">
            {/* <!-- Modal content --> */}
            <div class="modal-content-reportblock">
              <div class="modalHeader">
     
                <Button
                  label={"Back to List"}
                  class={"btnClose"}
                  onClick={manyPanelCallback}
                />

                <h4>Add/Edit Inspection Check List - {currentRow.InvoiceNo}</h4>

              </div>

              {currentRow.Items &&
                currentRow.Items.map((Item, Idx) => {
                  return (
                    <>
                      <div className={"reportcheckblock " + Item.RowNo}>
                        {/* <label>{Item.PhotoUrl}</label> */}
                        {/* <label>{Item.PhotoUrlPreview}</label> */}
                        <input
                          type="file"
                          id="PhotoUrl"
                          name="PhotoUrl"
                          accept="image/*"
                          //onChange={handleFileChange}
                          // onChange={(e) => handleFileChangeManyFile(e, "PhotoUrl")}
                          onChange={(e) => handleFileChangeManyFile(e, Idx)}
                        />

                        {Item.autoId != -1 &&
                          Item.PhotoUrlPreview == "" &&
                          Item.PhotoUrl && (
                            <div
                              className={
                                "reportcheckblock-image " + Item.ColumnNo
                              }
                            >
                              <img
                                src={
                                  Item.PhotoUrl=='placeholder.jpg'?
                                  `${baseUrl}image/transaction/${Item.PhotoUrl}`:
                                  `${baseUrl}image/transaction/${currentRow.ManyImgPrefix}/${Item.PhotoUrl}`
                                
                                }
                                // src={
                                //     Item.PhotoUrl
                                //       ? `${baseUrl}image/transaction/${Item.PhotoUrl}`
                                //       : previewImage
                                //   }
                                alt="Photo"
                                className="preview-image"
                              />
                            </div>
                          )}

                        {(Item.autoId == -1 || Item.PhotoUrlPreview != "") && (
                          <div
                            className={
                              "reportcheckblock-image " + Item.ColumnNo
                            }
                          >
                            <img
                              src={
                                Item.PhotoUrlPreview
                                  ? Item.PhotoUrlPreview
                                  : (Item.PhotoUrl=='placeholder.jpg'?                                    
                                    `${baseUrl}image/transaction/${Item.PhotoUrl}`:
                                  `${baseUrl}image/transaction/${currentRow.ManyImgPrefix}/${Item.PhotoUrl}`)
                              }
                              alt="Photo"
                              className="preview-image"
                            />
                          </div>
                        )}

                        {/* <label>CNC label</label> */}
                        <Autocomplete
                          autoHighlight
                          disableClearable
                          className="chosen_dropdown"
                          id="CheckId"
                          name="CheckId"
                          autoComplete
                          //class={errorObject.CheckId}
                          options={CheckList ? CheckList : []}
                          getOptionLabel={(option) => option.name}
                          defaultValue={{ id: 0, name: "Select Check Name" }}
                          value={
                            CheckList
                              ? CheckList[
                                  CheckList.findIndex(
                                    (list) => list.id === Item.CheckId
                                  )
                                ]
                              : null
                          }
                          onChange={(event, valueobj) =>
                            handleChangeManyDropDown(
                              "CheckId",
                              valueobj ? valueobj.id : "",
                              Idx
                            )
                          }
                          renderOption={(option) => (
                            <Typography className="chosen_dropdown_font">
                              {option.name}
                            </Typography>
                          )}
                          renderInput={(params) => (
                            <TextField
                              {...params}
                              variant="standard"
                              fullWidth
                            />
                          )}
                        />

                        <div className="">
                          <label>Width</label>
                          <Button
                            label={"2/1"}
                            class={"btnreportcheckblockheight " + (Item.RowNo=='reportcheckblock-width-half'?'bgselect':'')}
                            // onClick={addEditAPICall}
                            onClick={(i) =>
                              handleChangeWidthHeight(
                                "width",
                                "reportcheckblock-width-half",
                                Idx
                              )
                            }
                          />
                          <Button
                            label={"1/1"}
                            class={"btnreportcheckblockheight " + (Item.RowNo=='reportcheckblock-width-full'?'bgselect':'')}
                            onClick={(i) =>
                              handleChangeWidthHeight(
                                "width",
                                "reportcheckblock-width-full",
                                Idx
                              )
                            }
                          />

                          <label>Height</label>
                          <Button
                            label={"1/3"}
                            class={"btnreportcheckblockheight " + (Item.ColumnNo=='reportcheckblock-height-onethird'?'bgselect':'')}
                            onClick={(i) =>
                              handleChangeWidthHeight(
                                "height",
                                "reportcheckblock-height-onethird",
                                Idx
                              )
                            }
                          />
                          <Button
                            label={"2/1"}
                            class={"btnreportcheckblockheight " + (Item.ColumnNo=='reportcheckblock-height-half'?'bgselect':'')}
                            onClick={(i) =>
                              handleChangeWidthHeight(
                                "height",
                                "reportcheckblock-height-half",
                                Idx
                              )
                            }
                          />
                          <Button
                            label={"1/1"}
                            class={"btnreportcheckblockheight " + (Item.ColumnNo=='reportcheckblock-height-full'?'bgselect':'')}
                            onClick={(i) =>
                              handleChangeWidthHeight(
                                "height",
                                "reportcheckblock-height-full",
                                Idx
                              )
                            }
                          />

                          <Button
                            label={"X"}
                            class={"btnreportcheckblockdelete"}
                            onClick={(i) => deleteCheckBlock(Idx)}
                          />
                        </div>
                      </div>
                    </>
                  );
                })}

              <div className="reportcheckblock">
                <Button
                  label={"+"}
                  class={"btnAddBlock"}
                  onClick={addCheckBlock}
                />
              </div>

              <div class="modalItem">
                {/* <Button
                  label={"Report Generate"}
                  class={"btnClose"}
                  onClick={PDFGenerate}
                /> */}
                <Button
                  label={"Back to List"}
                  class={"btnClose"}
                  onClick={manyPanelCallback}
                />
                {currentRow.id && (
                  <Button
                    label={"Update"}
                    class={"btnUpdate"}
                    onClick={addEditAPICall}
                  />
                )}
                {!currentRow.id && (
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
        </div>
      )}

      {showModal && (
        <InspectionReportEntryAddEditModal
          masterProps={props}
          currentRow={currentRow}
          modalCallback={modalCallback}
        />
      )}
    </>
  );
};

export default InspectionReportEntry;
