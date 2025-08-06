import React, { forwardRef, useRef, useEffect, useState } from "react";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";

const AuditLogAddEditModal = (props) => {
  // console.log('props modal: ', props);
  const serverpage = "auditlog"; // this is .php server page

  const [currentRow, setCurrentRow] = useState(props.currentRow);
  const [jsonText, setJsonText] = useState(JSON.parse(props.currentRow.JsonText));
  console.log('jsonText: ', jsonText);
  // const [errorObject, setErrorObject] = useState({});
 
  console.log('currentRow: ', currentRow);
  console.log('currentRow JsonText: ',JSON.parse(currentRow.JsonText));

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
            <h4>Audit Log Details</h4>
          </div>

          <div class="pt-10">
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
                   

            </div>


            <div class="pt-10">
              <span class="font-bold">Table Name: </span>{currentRow.TableName}
            </div>

            <div class="pt-10">
              <span class="font-bold">SQL Type: </span>{currentRow.QueryType}
            </div>

            <div class="pt-10">
              <span class="font-bold">SQL: </span>{currentRow.SqlText}
            </div>

            <div class="pt-10">
              <span class="font-bold">Params: </span>{currentRow.SqlParams}
            </div>
  
          <div class="modalItem">
            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
            {/* {props.currentRow.id && (
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
            )} */}
          </div>
        </div>
      </div>
      {/* <!-- GROUP MODAL END --> */}
    </>
  );
};

export default AuditLogAddEditModal;
