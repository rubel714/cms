import React, { forwardRef, useRef, useEffect, useState } from "react";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";

const ErrorLogAddEditModal = (props) => {
  // console.log('props modal: ', props);
  const serverpage = "errorlog"; // this is .php server page

  const [currentRow, setCurrentRow] = useState(props.currentRow);
  // const [jsonText, setJsonText] = useState(JSON.parse(props.currentRow.JsonText));
  // console.log('jsonText: ', jsonText);
  // const [errorObject, setErrorObject] = useState({});
 
  console.log('currentRow: ', currentRow);
  // console.log('currentRow JsonText: ',JSON.parse(currentRow.JsonText));

  function modalClose() {
    props.modalCallback("close");
  }

  return (
    <>
      {/* <!-- GROUP MODAL START --> */}
      <div id="groupModal" class="modal">
        {/* <!-- Modal content --> */}
        <div class="modal-content">
          <div class="modalHeader">
            <h4>Error Log Details</h4>
          </div>

          {/* <div class="pt-10">
                <table>
                  <thead>
                    <tr>
                      <th>Field</th>
                      <th>Old Value</th>
                      <th>New Value</th>
                    </tr>
                  </thead>
                  <tbody>
                  {jsonText.map((obj)=>{
                    return ( <tr>
                      <td>{obj[0]}</td>
                      <td>{obj[1]}</td>
                      <td>{obj[2]}</td>
                    </tr>);
                  })}
                   

                  </tbody>
                </table>
                   

            </div> */}


            {/* <div class="pt-10">
              <span>Table Name: {currentRow.TableName}</span>
            </div> */}

            <div class="pt-10">
              <span class="font-bold">SQL Type: </span>{currentRow.QueryType}
            </div>
            <div class="pt-10">
              <span class="font-bold">Error No: </span>{currentRow.ErrorNo}
            </div>
            <div class="pt-10">
              <span class="font-bold">Error Message: </span>{currentRow.ErrorMsg}
            </div>
            <div class="pt-10">
            <span class="font-bold">SQL: </span>{currentRow.Query}
            </div>
            <div class="pt-10">
              <span class="font-bold">Params: </span> {currentRow.SqlParams}
            </div>
            
          <div class="modalItem">
            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
           
          </div>
        </div>
      </div>
      {/* <!-- GROUP MODAL END --> */}
    </>
  );
};

export default ErrorLogAddEditModal;
