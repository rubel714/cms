import moment from "moment";

// const getYm01 = (ym) => {
    
//   let year = parseInt(ym.substring(0, 4));
//   let month = parseInt(ym.substring(6,7));
  
//   let ym01 = year + '-' + month.toString().padStart(2, "0") + '-01';

//   return ym01;
// };

// var dateEditor = function(cell, onRendered, success, cancel, editorParams){
//   console.log(cell._cell.value);
//   //create and style editor
//   var editor = document.createElement("input");

//   editor.setAttribute("type", "date");

//   //create and style input
//   editor.style.padding = "3px";
//   editor.style.width = "100%";
//   editor.style.boxSizing = "border-box";

//   //Set value of editor to the current value of the cell
//   if (cell.getValue() != undefined)
//     editor.value = moment(cell.getValue(), "DD/MM/YYYY").format("YYYY-MM-DD")
//   else
//     editor.value = "";

//   //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
//   onRendered(function(){
//       editor.focus();
//       editor.style.css = "100%";
//   });

//   //when the value has been set, trigger the cell to update
//   function successFunc(){
//     if (editor.value != "")
//       success(moment(editor.value, "YYYY-MM-DD").format("DD/MM/YYYY"));
//     else if((cell._cell.value!='')&&(cell._cell.value!=null)){
//       success(moment(cell._cell.value, "YYYY-MM-DD").format("DD/MM/YYYY"));
//     }
//     else
//       success(cell._cell.value);
//   }

//   editor.addEventListener("change", successFunc);
//   editor.addEventListener("blur", successFunc);

//   //return the editor element
//   return editor;
// };

// const cellFocusEditor = function(cell, onRendered, success, cancel, editorParams){
  
//   //create and style input
//   let input = document.createElement("input");

//   input.setAttribute("type", "text");

//   input.style.padding = "4px";
//   input.style.width = "100%";
//   input.style.boxSizing = "border-box";

//   input.value = cell.getValue();

//   onRendered(function () {
//       input.focus();
//       input.select();
//       input.style.height = "100%";
//   });

//   function successFunc(){
//     success(input.value);
//   }

//   function keyHandlerFunc(e){
//     if (e.keyCode == 27)
//       cancel(input.value);
//     //console.log(e);
//   }

//   //submit new value on blur or change
//   input.addEventListener("change", successFunc);
//   input.addEventListener("blur", successFunc);
//   input.addEventListener("keyup", keyHandlerFunc);

//   return input;
// };


const getDefaultMonthYear = () => {
  let retValue = {};
  let currDate = new Date();
  let defaultYear = currDate.getFullYear();
  let defaultMonthId = currDate.getMonth();
  
  //0=jan
  if (defaultMonthId == 0) {
    defaultYear = defaultYear - 1;
    defaultMonthId = 12;
  } else {
    defaultMonthId = defaultMonthId;
  }
  
  retValue = {defaultYear:defaultYear,defaultMonthId:defaultMonthId};
  return retValue;
};
 

export { getDefaultMonthYear };
// export { getYm01, cellFocusEditor, dateEditor, getDefaultMonthYear, getDefaultQuarterYear };
