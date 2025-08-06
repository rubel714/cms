import axios from "axios";

export const body = {
  page_limit: JSON.stringify(100),
  page_number: JSON.stringify(0 + 1),
  search_key: "",
};

const UserInfo = sessionStorage.getItem("User_info") ? JSON.parse(sessionStorage.getItem("User_info")): 0;  
const LoginUser = UserInfo===0 ? [] : UserInfo;
const lan = "en_GB";


const auth_token = sessionStorage.getItem("token") ? sessionStorage.getItem("token") : null;
let options = {};
options = {
  headers: {
    "Content-Type": "application/json",
    "Access-Control-Allow-Origin": "*", 
    Authorization: `Bearer ${auth_token}`,
  },
};

// let optionsFileAndJson = {};
// optionsFileAndJson = {
//   headers: {
//     "Content-Type": "multipart/form-data",
//     "Access-Control-Allow-Origin": "*", 
//     Authorization: `Bearer ${auth_token}`,
//   },
// };


export const apiCall = axios.create({
  baseURL: process.env.REACT_APP_API_URL + "source/api/",
 // baseURL: "http://localhost/phamangpl/backend/source/api/",
});
export const apiCallReport = axios.create({
  baseURL: process.env.REACT_APP_API_URL + "report/",
 // baseURL: "http://localhost/phamangpl/backend/source/api/",
});
export const apiOption = () => {
  return (
    options
  )
}


// export const apiOptionFileAndJson = () => {
//   return (
//     optionsFileAndJson
//   )
// }
export const language = () => {
  return (lan)
}

export const LoginUserInfo = () => {
  return (LoginUser)
}
















// export const getProducts = (params) =>
//   api.post("productEntry", { params }, options)
//     .then((res) => {
//       return res.data.datalist;
//     });

// export const getProduct = (id) =>
//   api.post(
//     `productEntry`,
//     { ...body, action: "getProduct", menukey: "products", ItemNo: id },
//     options
//   );

// export const updateProduct = ({ id, ...updatedProduct }) =>
//   api
//     .put(
//       `productEntry`,
//       {
//         ...body,
//         action: "dataUpdate",
//         menukey: "products",
//         ItemNo: id,
//         ...updatedProduct,
//       },
//       options
//     )
//     .then((res) => {
//       return res.data;
//     });

// export const saveProduct = (productData) =>
//   api
//     .post(
//       `productEntry`,
//       { ...body, action: "dataInsert", menukey: "products", ...productData },
//       options
//     )
//     .then((res) => res.data);

// export const deleteProduct = (id) =>
//   api.post(
//     `productEntry`,
//     { ...body, action: "dataDalete", menukey: "products", ItemNo: id, UserName: UserName, lan: lan },
//     options
//   ).then((res) => res.data);


// //prescriber
 
// export const getPrescribers = () =>
//   api
//     .post(
//       "prescriber",
//       { ...body, action: "getDataList", menukey: "prescriber" },
//       options
//     )
//     .then((res) => {
//       return res.data.datalist;
//     });

// export const getPrescriber = (id) =>
//   api.post(
//     `prescriber`,
//     {
//       ...body,
//       action: "getPrescriber",
//       menukey: "prescriber",
//       PrescriberId: id,
//     },
//     options
//   );

// export const updatePrescriber = ({ id, ...updatedPrescriber }) =>
//   api
//     .put(
//       `prescriber`,
//       {
//         ...body,
//         action: "dataUpdate",
//         menukey: "prescriber",
//         PrescriberId: id,
//         ...updatedPrescriber,
//       },
//       options
//     )
//     .then((res) => {
//       return res.data;
//     });

// export const savePrescriber = (prescriberData) =>
//   api
//     .post(
//       `prescriber`,
//       {
//         ...body,
//         action: "dataInsert",
//         menukey: "prescriber",
//         ...prescriberData,
//       },
//       options
//     )
//     .then((res) => res.data);
	
// export const deletePrescriber = (id) =>
//   api.post(
//     `prescriber`,
//     { ...body, action: "dataDalete", menukey: "prescriber", PrescriberId: id, UserName: UserName, lan: lan },
//     options
//   ).then((res) => res.data);

// // Dispenser Api
// export const getDispensers = () =>
//   api
//     .post(
//       "dispenser",
//       { ...body, action: "getDataList", menukey: "dispenser" },
//       options
//     )
//     .then((res) => {
//       return res.data.datalist;
//     });

// export const getDispenser = (id) =>
//   api.post(
//     `dispenser`,
//     { ...body, action: "getDispenser", menukey: "dispenser", DispenserId: id },
//     options
//   );

// export const updateDispenser = ({ id, ...updatedDispenser }) =>
//   api
//     .put(
//       `dispenser`,
//       {
//         ...body,
//         action: "dataUpdate",
//         menukey: "dispenser",
//         DispenserId: id,
//         ...updatedDispenser,
//       },
//       options
//     )
//     .then((res) => {
//       return res.data;
//     });

// export const saveDispenser = (dispenserData) =>
//   api
//     .post(
//       `dispenser`,
//       { ...body, action: "dataInsert", menukey: "dispenser", ...dispenserData },
//       options
//     )
//     .then((res) => res.data);

// export const deleteDispenser = (id) =>
//   api.post(
//     `dispenser`,
//     { ...body, action: "dataDalete", menukey: "dispenser", DispenserId: id, UserName: UserName, lan: lan },
//     options
//   ).then((res) => res.data);

// // Protocol Api
// export const getProtocols = () =>  api.post('getProtocolList', {...body, "menukey": "protocol",lan: lan}, options).then((res) =>{ 
// 	return res.data.getprotocolList;
// })

// export const getProtocol = (id) =>  api.post(`protocolListSingle`,{...body, "menukey": "protocol", "ProtocolId":id}  ,options)
// export const updateProtocol = ({id, ...updatedProtocol}) =>  api.put(`protocolUpdate`,{...body, "menukey": "order",...updatedProtocol}, options).then((res) =>{
// 	return res.data;
// })
// export const saveProtocol = (protocolData) =>  api.post(`protocolSave`, protocolData, options).then((res) => res.data )
// export const deleteProtocol = (id)=>  api.post(`protocolDelete`,{...body, "menukey": "protocol", "ProtocolId":id, UserName: UserName, lan: lan}, options).then((res) => res.data)


// export const getWarehouseList = (amcData) => { 
//   return api
//     .post(
//       "comboscript",
//       { ...amcData },
//       options
//     )
//     .then((res) => {
     
//       return res;
//     });
// }; 


// //Stock Status api
// /* export const getstockstatus = (params) =>  api.post('StockStatus', {params }, options).then((res) =>{ 
// 	return res.data.datalist;
// }) */
// export const getstockstatus = (params) =>  api.post('StockStatus', {params, "RoleItemGroupId": RoleItemGroupId }, options).then((res) =>{ 
// 	return res.data.datalist;
// })

// export const getLotDataView = (id) =>  api.post('StockStatus', {...body, "action": "getLotDataView", "menukey": "stock-status", "ItemNo":id,"FacilityId": FacilityId}, options).then((res) =>{ 
// 	return res.data.datalist;
// })

// export const getStockCardDataView = (params) =>  api.post('StockStatus', {params}, options).then((res) =>{ 
// 	return res.data.datalist;
// })

// export const getChartDataView = (params) =>  api.post('StockStatus', {params}, options).then((res) =>{ 
// 	return res.data.datalist;
// })



// //Modal

// /* export const getProductsList = () =>
//   api.post("products", { ...body, action: "getProducts", menukey: "receive", "RoleItemGroupId":RoleItemGroupId }, options).then((res) => {
//     return res.data.productlist;
//   }); */

// export const getProductsList = (params) =>
//   api.post("products", { params }, options).then((res) => {
//     return res.data.productlist;
//   });

// export const getProductsLot = (id, ItemGroupId) =>
//   api.post("products", { ...body, action: "getProductLot", menukey: "receive",FacilityId:id, "ItemGroupId":ItemGroupId }, options).then((res) => {
//     return res.data.productlist;
//   });
  
  
// export const getProtocolListModal = () =>
// api.post("protocolModal", { ...body, action: "getProtocolListModal", menukey: "protocol" }, options).then((res) => {
//   return res.data.protocollist;
// });

  
// export const getProductSelectInPrescriptionModal = (params) =>
// api.post("protocolModal", { ...body, action: "getProductBatchModal", menukey: "protocol","FacilityId": FacilityId, formulation: params.queryKey[1] }, options).then((res) => {
// // console.log("getProductSelectInPrescriptionModal api.js: ",res);
// // console.log("formulation api.js: ",params);
// // console.log("formulation api.js: ",params.queryKey[1]);
//   return res.data.productlist;
// });


// export const getProtocolItemListModal = () =>
// api.post("protocolModal", { ...body, action: "getProtocolItemListModal", menukey: "protocol" }, options).then((res) => {
//   return res.data.protocolitemlist;
// });


// export const getSourceList = () =>
//   api.post("sourceoffund", { ...body, action: "getSources", menukey: "receive" }, options).then((res) => {
//     return res.data.sourcelist;
//   });

// ///Receive

// export const getReceiveInvs = (params) =>
//   api
//     .post("receiveinvoicelist", { params }, options)
//     .then((res) => {
//       return res.data.receiveinvdata;
//     });
// export const saveReceiveInv = (dispenserData) =>
//   api.post(`receiveSave`, dispenserData, options).then((res) => res.data);
 
// export const updateReceiveInv = ({ id, ...updatedDispenser }) =>
//   api
//     .put(
//       `receiveUpdate`,
//       { ...body, menukey: "receive", ...updatedDispenser },
//       options
//     )
//     .then((res) => {
//       return res.data;
//     });	
	
// export const getReceiveInv = (id) =>
//   api.post(
//     `receiveListSingle`,
//     { ...body, menukey: "receive", TransactionId: id,"FacilityId": FacilityId },
//     options
//   );
// export const deleteReceiveInv = (id) =>
//   api.post(
//     `receiveDelete`,
//     { ...body, menukey: "receive", TransactionId: id, "FacilityId": FacilityId, UserName: UserName, lan: lan },
//     options
//   ).then((res) => {
//       return res.data;
//  });
// /* export const getNonReceivedOrderList = () =>
//   api
//     .post(
//       "receivedNonOrderList",
//       { ...body, menukey: "receive", "FacilityId": FacilityId },
//       options
//     )
//     .then((res) => {
//       return res.data.receiveinvdata;
//     }); */

//    export const getNonReceivedOrderList = (params) =>
//     api.post("receivedNonOrderList", { params }, options).then((res) => {
//       return res.data.receiveinvdata;
//     });

    
// export const saveNonReceivedOrder = (id) =>
//   api
//     .post(
//       `receivedNonReceiveOrderProducts`,
//       { ...body, menukey: "receive","FacilityId": FacilityId, OrderId: id },
//       options
//     )
//     .then((res) => {
//       return res.data.receiveinvdata;
//     });
// export const getInvoiceNo = (params) =>
//   api.post("InvoiceNo", { ...body, params }, options).then((res) => {
//     return res.data;
//   });

// export const getOrdInvoiceNo =  (params) =>  api.post('OrdInvoiceNo', {...body, params},options).then((res) =>{ 
//   return res.data;
// } )   

// export const updateStock = (id) =>
//   api
//     .post(
//       `POSTStockForm`,
//       {
//         ...body,
//         menukey: "receive",
//         "FacilityId": FacilityId,
//         TransactionId: id,
//         TransactionTypeId: 1,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });
// export const updateAdjStock = (id) =>
//     api
//       .post(
//         `POSTStockForm`,
//         {
//           ...body,
//           menukey: "adjustment",
//           "FacilityId": FacilityId,
//           TransactionId: id,
//           TransactionTypeId: 3,
// 		  UserName: UserName, 
// 		  lan: lan
//         },
//         options
//       )
//       .then((res) => {
//         return res.data;
//     });
      

// // ORDER
// export const getOrderInvs = (params) =>
//   api
//     .post("orderInvoiceList", { params }, options)
//     .then((res) => {
//       return res.data.orderinvdata;
//     });

// export const getOrderInvApproval = (params) =>
//     api
//       .post("orderInvoiceListApproval", { params }, options)
//       .then((res) => {
//         return res.data.orderinvdata;
//       });    

// export const saveOrderInv = (dispenserData) =>
//   api
//     .post(`orderSave`, { ...body, menukey: "order", ...dispenserData, UserName: UserName, lan: lan, "RoleItemGroupId":RoleItemGroupId }, options)
//     .then((res) => res.data);

// export const updateOrderInv = ({ id, ...updatedOrder }) =>
//   api
//     .put(`orderUpdate`, { ...body, menukey: "order", ...updatedOrder, UserName: UserName, lan: lan, "RoleItemGroupId":RoleItemGroupId }, options)
//     .then((res) => {
//       return res;
//     });

// export const updateOrderApproval = ({ id, ...updatedOrder }) =>
//     api
//       .put(`orderUpdateApproval`, { ...body, menukey: "order", ...updatedOrder }, options)
//       .then((res) => {
//         return res;
//       });   

// export const getOrderInv = (id) =>
//   api.post(
//     `orderListSingle`,
//     { ...body, menukey: "order", OrderId: id, "FacilityId": FacilityId, "RoleItemGroupId":RoleItemGroupId },
//     options
//   );
// export const deleteOrderInv = (id) =>
//   api.post(
//     `orderDelete`,
//     { ...body, menukey: "order", OrderId: id, "FacilityId": FacilityId, UserName: UserName, lan: lan },
//     options
//   );


// export const postOrderInv = (id) =>
//     api
//       .post(
//         `orderPost`,
//         {
//           ...body,
//           menukey: "order",
//           "FacilityId": FacilityId,
//           OrderId: id
//         },
//         options
//       )
//       .then((res) => {
//         return res;
//       });

  
//       export const postOrderApproval = (id) =>
//       api
//         .post(
//           `orderPostApproval`,
//           {
//             ...body,
//             menukey: "order",
//             "FacilityId": FacilityId,
//             OrderId: id
//           },
//           options
//         )
//         .then((res) => {
//           return res;
//         });   
        
              
//         export const rejectOrderApproval = (id) =>
//         api
//           .post(
//             `orderRejectApproval`,
//             {
//               ...body,
//               menukey: "order-approval",
//               "FacilityId": FacilityId,
//               OrderId: id
//             },
//             options
//           )
//           .then((res) => {
//             return res;
//           });

// // Stock Take
// export const getStockTakeInvs = (params) =>
//   api
//     .post("stocktake", { params }, options)
//     .then((res) => {
//       return res.data.gstocktakelist;
//     });

// export const saveStockTakeInv = (stockTakeData) =>
//   api
//     .post(`stocktake`, { ...body, menukey: "stock-take", "action":"saveStockTake", ...stockTakeData, UserName: UserName, lan: lan}, options)
//     .then((res) => res.data);

// export const updateStockTakeInv = ({ id, ...updatedStockTake }) =>
//   api
//     .put(`stocktake`, { ...body, menukey: "stock-take","action":"updateStockTake", ...updatedStockTake, UserName: UserName, lan: lan }, options)
//     .then((res) => {
//       return res.data;
//     });
// export const getStockTakeInv = (id, ItemGroupId) =>
//   api.post(
//     `stocktake`,
//     { ...body, menukey: "stock-take", StockTakeId: id, "FacilityId": FacilityId, "ItemGroupId":ItemGroupId,"action":"stockTakeListSingle" },
//     options
//   )
// export const deleteStockTakeInv = (id) =>
//   api.post(
//     `stocktake`,
//     { ...body, menukey: "stock-take", StockTakeId: id, "FacilityId": FacilityId,"action": "stockTakeDelete", UserName: UserName, lan: lan },
//     options
//   ).then((res) => {
//      return res.data;
//   });
   
//     export const postStockTakeInv = (id) =>
//     api
//       .post(
//         `StockTakePosted`,
//         {
//           ...body,
//           menukey: "stock-take",
//           "FacilityId": FacilityId,
//           StockTakeId: id,
// 		  UserName: UserName, 
// 		  lan: lan
//         },
//         options
//       )
//       .then((res) => {
//         return res.data;
//       });    
	
// /* export const getunitofmeasurelist1 = (params) =>  api.post('unit_of_measure', {params }, options).then((res) =>{ 
// 	return res.data.datalist;
// }) */

// // Patients Api
// export const getPatients = (params) =>  {
// 	return api.post('patients', {params}, options).then((res) =>{ 
// 		return res.data.datalist;
// 	}) 

// 	 /* if(searchData==null){
// 		return api.post('patients', {...body, "action": "getPatientList", "menukey": "patients","FacilityId": FacilityId}, options).then((res) =>{ 
// 			return res.data.datalist;
// 		})

// 	}else{
// 		return api.post('patients', {...body, "action": "getPatientList", "menukey": "patients","searchData":"searchData",FacilityId:FacilityId}, options).then((res) =>{ 
// 			return res.data.datalist;
// 		})
// 	}  */
// }

// // Get All Patients Api
// export const getAllPatients = (params) =>  {
// 	return api.post('patients', {params}, options).then((res) =>{ 
// 		return res.data.datalist;
// 	})
// }

// export const getLogisticDashboard = (params) =>  {
//   return api.post('LogisticsDashboard', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// export const getConsumptionTrendsTopFiveProducts = (pYearId, pMonthId, pFacilityId = 0) => {

//   return api
//       .post(
//         "LogisticsDashboard",
//         { ...body, action: "getConsumptionTrendsTopFiveProducts", menukey: "Logistics Dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//          // console.log("Res data",res);
//         //console.log("data list",res.data.datalist);
//         return res.data.datalist;
//       });

// };
// export const getAMCTrendsTopFiveProducts = (pYearId, pMonthId, pFacilityId = 0) => {

//   return api
//       .post(
//         "LogisticsDashboard",
//         { ...body, action: "getAMCTrendsTopFiveProducts", menukey: "Logistics Dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });

// };

// export const getPercentageOfFacilitiesWithStockoutOfTracerCommodities = (pYearId, pMonthId, pFacilityId = 0) => {

//   return api
//       .post(
//         "LogisticsDashboard",
//         { ...body, action: "getPercentageOfFacilitiesWithStockoutOfTracerCommodities", menukey: "Logistics Dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });

// };

// export const getPatient = (id) =>
//   api.post(
//     `patients`,
//     { ...body, menukey: "patients", PatientId: id, "FacilityId": FacilityId,"action": "getPatient" },
//     options
//   );


// export const updatePatient = ({ id, ...updatedPatient}) =>
//   api
//     .put(`patients`, { ...body, menukey: "patients", ...updatedPatient,"action": "patientUpdate" }, options)
//     .then((res) => {
//       return res;
//     });

   
// export const SaveUpdateAallergies = (patientId) => {
   
//   if (patientId.allergiesData1.AllergiesId== undefined)
//   return api
//   .post(
//     "patients",
//     { menukey: "patients", "lan":lan,"UserName":UserName, "FacilityId": FacilityId,"action": "SaveAallergies",...patientId.allergiesData1 },
//     options
//   )
//   .then((res) => { 
//     console.log('res: ', res);
//     //res.data=res.data.list;
//     return res;
//   });
//   else{
//     return api
//     .put(
//       "patients",
//       { menukey: "patients", "lan":lan,"UserName":UserName, "FacilityId": FacilityId,"action": "UpdateAallergies",...patientId.allergiesData1 },
//       options
//     )
//     .then((res) => { 
     
//      // res.data=res.data.list;
//       console.log('res: ', res);
//       return res;
//     });
//   }

// }


// export const deleteAlergies = (id) => {
   
//   return api
//   .put(
//     "patients",
//     { menukey: "patients", "lan":lan,"UserName":UserName, AllergiesId: id, "FacilityId": FacilityId, "action": "DeleteAallergies" },
//     options
//   )
//   .then((res) => {  
//     //res.data=res.data.list; 
//     return res;
//   });


// }  


  
// export const SaveUpdateLabresults = (patientId) => {
   
//   if (patientId.labResultsData1.labtestdetailId== undefined)
//   return api
//   .post(
//     "patients",
//     { menukey: "patients", "lan":lan,"UserName":UserName, "FacilityId": FacilityId,"action": "SaveLabresults",...patientId.labResultsData1 },
//     options
//   )
//   .then((res) => { 
//     console.log('res: ', res);
//     //res.data=res.data.list;
//     return res;
//   });
//   else{
//     return api
//     .put(
//       "patients",
//       { menukey: "patients", "lan":lan,"UserName":UserName, "FacilityId": FacilityId,"action": "UpdateLabresults",...patientId.labResultsData1 },
//       options
//     )
//     .then((res) => { 
     
//       //res.data=res.data.list;
//       console.log('res: ', res);
//       return res;
//     });
//   }

// }


// export const SaveUpdateAnthropometrics = (patientId) => {
   
//   if (patientId.anthropometricsData1.anthropometricsId== undefined)
//   return api
//   .post(
//     "patients",
//     { menukey: "patients", "lan":lan,"UserName":UserName, "FacilityId": FacilityId,"action": "SaveAnthropometrics",...patientId.anthropometricsData1 },
//     options
//   )
//   .then((res) => { 
//     console.log('res: ', res);
//     //res.data=res.data.list;
//     return res;
//   });
//   else{
//     return api
//     .put(
//       "patients",
//       { menukey: "patients", "lan":lan,"UserName":UserName, "FacilityId": FacilityId,"action": "UpdateAnthropometrics",...patientId.anthropometricsData1 },
//       options
//     )
//     .then((res) => { 
     
//       //res.data=res.data.list;
//       console.log('res: ', res);
//       return res;
//     });
//   }

// }
 

  
// export const deleteAnthropometrics = (patientId) => {
   
//   return api
//   .put(
//     "patients",
//     {menukey: "patients", "lan":lan,"UserName":UserName, anthropometricsId: patientId,"FacilityId": FacilityId, "action": "DeleteAnthropometrics" },
//     options
//   )
//   .then((res) => {  
//     //res.data=res.data.list; 
//     return res;
//   });


// }
 

 
// export const deleteLabresults = (id) => {
   
//   return api
//   .put(
//     "patients",
//     { menukey: "patients", "lan":lan,"UserName":UserName, labtestdetailId: id, "FacilityId": FacilityId, "action": "DeleteLabresults" },
//     options
//   )
//   .then((res) => {  
//     //res.data=res.data.list; 
//     return res;
//   });


// } 
 
// export const savePatient = (patientData) =>  api.post(`patients`, {...body, "FacilityId": FacilityId, "action": "patientInsert", "menukey": "patients", ...patientData}, options).then((res) => res.data ) 
// //export const deletePatient = (id) =>  api.post(`patients`,{...body, "action": "deletePatient", "menukey": "patients", "PatientId":id}, options)
// export const deletePatient = (id) =>  api.post(`patients`,id, options) 

// export const getPrescriptionByPatientId = (patientId = null) => {
// 	if (patientId == null) {
// 		return api.post(
//       `patients`,
//       { ...body, menukey: "patients", "FacilityId": FacilityId,"action": "getPrescription" },
//       options
//     )
// 	} else {
// 	  return api
//     .post(
//       "patients",
//       { ...body, menukey: "patients", "FacilityId": FacilityId,"action": "getPrescription","PatientId":patientId },
//       options
//     )
//     .then((res) => { 
//       return res.data;
//     });
// 	}
// }




// export const getCD4WeightChart = (id) => {
   
//   return api
//   .put(
//     "patients",
//     { menukey: "patients", PatientId: id, "FacilityId": FacilityId, "action": "getCD4WeightChart" },
//     options
//   )
//   .then((res) => {  
//     res.data=res.data.list; 
//     return res;
//   });


// } 
  




// //Rubel.

// /* Dashboard Main */

// export const getTLDUptakePatientsTrend = (pYearId, pMonthId, pFacilityId = 0) => {
//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "dashboardpage",
//         { ...body, action: "getTLDUptakePatientsTrend", menukey: "dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         // console.log("patientdashboard patient count: ", res.data.datalist);
        
//         console.log("getTLDUptakePatientsTrend patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };

// export const getTLDTransitionPatientsTrend = (pYearId, pMonthId, pFacilityId = 0) => {
//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "dashboardpage",
//         { ...body, action: "getTLDTransitionPatientsTrend", menukey: "dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         // console.log("patientdashboard patient count: ", res.data.datalist);
        
//         console.log("getTLDTransitionPatientsTrend patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };

// export const getMMDAmongAdultPatientsTrend = (pYearId, pMonthId, pFacilityId = 0) => {
//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "dashboardpage",
//         { ...body, action: "getMMDAmongAdultPatientsTrend", menukey: "dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         // console.log("patientdashboard patient count: ", res.data.datalist);
        
//         console.log("getMMDAmongAdultPatientsTrend patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };


// export const getMMDCoveragePatientsTrend = (pYearId, pMonthId, pFacilityId = 0) => {
//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "dashboardpage",
//         { ...body, action: "getMMDCoveragePatientsTrend", menukey: "dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         // console.log("patientdashboard patient count: ", res.data.datalist);
        
//         console.log("getMMDCoveragePatientsTrend patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };

// export const getDashboardStockStatusTableData = (pYearId, pMonthId, pFacilityId = 0, gItemNo) => {

//   return api
//       .post(
//         "dashboardpage",
//         { ...body, action: "getDashboardStockStatusTableData", menukey: "dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId, ItemNo: gItemNo  },
//         options
//       )
//       .then((res) => {

//         return res.data.datalist;
//       });

// };







// /* Patient Dashboard */

// export const getCurrentMonthPatients = (pYearId, pMonthId, pFacilityId = 0,  pGenderTypeId = 0, pAgeGroupId = 0) => {

//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "patientdashboard",
//         { ...body, action: "getCurrentMonthPatients", menukey: "Patient Dashboard", FacilityId: pFacilityId, GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         // console.log("patientdashboard patient count: ", res.data.datalist);
        
//         console.log("patientdashboard patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };


// export const getActivePatientsTrend = (pYearId, pMonthId, pFacilityId = 0, pGenderTypeId = 0,  pAgeGroupId = 0) => {

//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "patientdashboard",
//         { ...body, action: "getActivePatientsTrend", menukey: "Patient Dashboard", FacilityId: pFacilityId, GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         // console.log("patientdashboard patient count: ", res.data.datalist);
        
//         console.log("getActivePatientsTrend patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };

// export const getAdultPatientsByProtocol = (pYearId, pMonthId, pFacilityId = 0, pGenderTypeId = 0, pAgeGroupId = 0, toggleButtonValue = 1) => {

//   //console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "patientdashboard",
//         { ...body, action: "getAdultPatientsByProtocol", menukey: "Patient Dashboard", FacilityId: pFacilityId, GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId, YearId: pYearId, MonthId: pMonthId, toggleButtonValue: toggleButtonValue  },
//         options
//       )
//       .then((res) => {
//         console.log("getAdultPatientsByProtocol patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };
// export const getPaediatricPatientsByProtocol = (pYearId, pMonthId, pFacilityId = 0, pGenderTypeId = 0, pAgeGroupId = 0, toggleButtonValuePaediatric = 1) => {

//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "patientdashboard",
//         { ...body, action: "getPaediatricPatientsByProtocol", menukey: "Patient Dashboard", FacilityId: pFacilityId, GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId, YearId: pYearId, MonthId: pMonthId, toggleButtonValuePaediatric: toggleButtonValuePaediatric  },
//         options
//       )
//       .then((res) => {
//         console.log("getPaediatricPatientsByProtocol patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };

// export const getDashboardActiveQueuePatientTableData = (pYearId, pMonthId, pFacilityId = 0,pGenderTypeId,pAgeGroupId, toggleButtonValuePaediatric = 1) => {

//   return api
//       .post(
//         "patientdashboard",
//         { ...body, action: "getDashboardActiveQueuePatientTableData", menukey: "Patient Dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId, GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId, toggleButtonValuePaediatric: toggleButtonValuePaediatric  },
//         options
//       )
//       .then((res) => {

//         return res.data.datalist;
//       });

// };




// //Prescription entry form
// export const getPatientsForPrescription = (searchData = null) => {
//   console.log("getPatientsForPrescription API Params: ", searchData);

//   if (searchData == null) {
//     //return api.get('/patients').then((res) => res.data)
//     console.log("searchData is null");
//     return api
//       .post(
//         "patients",
//         { ...body, action: "getPatientList", menukey: "patients", FacilityId: FacilityId },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });
//   } else {
//     console.log("searchData is not null");
//     return api
//       .post(
//         "patients",
//         {
//           ...body,
//           action: "getPatientList",
//           menukey: "patients",
//           searchData: searchData,
//           FacilityId: FacilityId
//         },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });
//   }
// };

// // export const getPrescriptions = () =>
// //   api.get("/prescriptions").then((res) => res.data);

// export const getPrescriptionByPatientIdDate = (patientId = null, fromDate, toDate, toggleButtonValue, isDefaulterPrescription) => {

//   console.log("getPrescriptionByPatientIdDate api.js");
//   console.log("patientId: ", patientId , ", fromDate: ", fromDate , ", toDate: ", toDate, ",FacilityId: ", FacilityId, ",toggleButtonValue: ", toggleButtonValue,", IsDefaulterPrescription:",isDefaulterPrescription);
//   let currDate = new Date();
//   if (patientId == null) {
//     return api
//       .post(
//         "prescriptions",
//         { ...body, action: "getPrescriptionList", menukey: "prescriptions", fromDate: fromDate, toDate: toDate, FacilityId: FacilityId, ToggleButtonValue: toggleButtonValue, CurrDate: currDate,IsDefaulterPrescription: isDefaulterPrescription },
//         options
//       )
//       .then((res) => {
//         console.log("prescriptions list api without patientid: ",res.data.datalist);
//         return res.data.datalist;
//       });
//   } else {
//     return api
//       .post(
//         "prescriptions",
//         { ...body, action: "getPrescriptionList", menukey: "prescriptions", patientId: patientId, fromDate: fromDate, toDate: toDate, FacilityId: FacilityId, ToggleButtonValue: toggleButtonValue, CurrDate: currDate ,IsDefaulterPrescription: isDefaulterPrescription },
//         options
//       )
//       .then((res) => {
//         console.log("prescriptions list api by patientid: ", res.data.datalist);
//         return res.data.datalist;
//       });

//   }
// };

// //call when going edit
// export const getPrescription = (PrescriptionId) => {
//   console.log("PrescriptionId: ", PrescriptionId);

//   return api
//     .post(
//       "prescriptions",
//       {
//         ...body,
//         action: "getPrescriptionByPrescriptionId",
//         menukey: "prescriptions",
//         PrescriptionId: PrescriptionId,
//       },
//       options
//     )
//     .then((res) => {
//       console.log("Single Prescriptions when Edit API: ", res.data.datalist);
//       return res.data.datalist;
//     });
// };


// export const updatePrescription = ({
//   PrescriptionId,
//   ...updatedPrescription
// }) =>
//   api
//     .put(
//       `prescriptions`,
//       {
//         ...body,
//         action: "updatePrescription",
//         menukey: "prescriptions",
//         PrescriptionId: PrescriptionId,
//         UserName: UserName, 
//         lan: lan,
//         ...updatedPrescription,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

// export const updatePrescriptionStatus = ({
//   PrescriptionId,
//   ...updatePrescriptionStatus
// }) =>
//   api
//     .put(
//       `prescriptions`,
//       {
//         ...body,
//         action: "updatePrescriptionStatus",
//         menukey: "prescriptions",
//         PrescriptionId: PrescriptionId,
//         UserName: UserName, 
//         lan: lan,
//         ...updatePrescriptionStatus,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

//     export const postPrescription = ({
//       PrescriptionId,
//       ...updatePrescriptionStatus
//     }) =>
//       api
//         .put(
//           `POSTStockForPrescription`,
//           {
//             ...body,
//             action: "postPrescription",
//             menukey: "prescriptions",
//             PrescriptionId: PrescriptionId,
//             UserName: UserName, 
//             lan: lan,
//             ...updatePrescriptionStatus,
//           },
//           options
//         )
//         .then((res) => {
//           return res;
//         });
    


// export const updatePrescriptionCopy = ({
//   PrescriptionId,
//   ...updatePrescriptionCopy
// }) =>
//   api
//     .post(
//       `prescriptions`,
//       {
//         ...body,
//         action: "updatePrescriptionCopy",
//         menukey: "prescriptions",
//         PrescriptionId: PrescriptionId,
//         UserName: UserName, 
//         lan: lan,
//         ...updatePrescriptionCopy,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

//   export const deletePrescription = (PrescriptionId) => api.post( `prescriptions`, { ...body, action: "deletePrescription", menukey: "prescriptions",  PrescriptionId: PrescriptionId, UserName: UserName, lan: lan}, options);

// export const savePrescription = (savePrescriptionData) =>
// api
//   .post(
//     `prescriptions`,
//     { ...body, action: "savePrescription", menukey: "prescriptions", UserName: UserName, lan: lan, ...savePrescriptionData },
//     options
//   )
//   .then((res) => res.data);


//   export const getProtocolItems = (ProtocolId) => {
//     console.log("getProtocolItems api.js. ProtocolId:", ProtocolId);

//       return api
//         .post(
//           "prescriptions",
//           { ...body, action: "getProtocolItems", menukey: "prescriptions",ProtocolId: ProtocolId, FacilityId: FacilityId },
//           options
//         )
//         .then((res) => {
//           console.log("Protocol Items by ProtocolId: ",res.data.datalist);
//           return res.data.datalist;
//         });
//   };
  

























// //combo api
// //Local Storage from Login Page
// export const getLoginAllDropdown = async (options,task) =>  api.post('comboscript', {"action": "getAllDropdownList","task": task, "menukey": ""}, options).then((res) =>{return res.data;})
// //Local Storage from meta or user entry page
// export const getAllDropdown = (task) =>  api.post('comboscript', {"action": "getAllDropdownList","task": task, "menukey": ""}, options).then((res) =>{return res.data;})

 
// //ICD List Api
// export const getIcd = () =>  api.post('comboscript', {...body, "action": "getIcdList", "menukey": ""}, options).then((res) =>{return res.data;})
// //Local Storage from Login Page 
// export const getLoginFacilityListNotEqual99ANDSuppliers = async (options,task) => api.post(`comboscript`,{"action": "getFacilityListNotEqual99ANDSupplier","task": task, menukey: "", FacilityId: localStorage.getItem("FacilityId") },options).then((res)=>{return res.data;});
// //Local Storage from meta or user entry page
// export const getFacilityListNotEqual99ANDSuppliers =  (task,FacilityId) => api.post(`comboscript`,{"action": "getFacilityListNotEqual99ANDSupplier","task": task, menukey: "", FacilityId: FacilityId },options).then((res)=>{return res.data;});

// export const UpdateAMC = (amcData) => { 
//       return api
//         .post(
//           "StockStatus",
//           { ...amcData },
//           options
//         )
//         .then((res) => {
         
//           return res;
//         });
//   }; 



// //Admin Forms
// export const getTotal = (params) =>  api.post('unit_of_measure', {params }, options).then((res) =>{ 
// 	return res.data.datalist;
// })
// //Admin Forms
// export const getunitofmeasurelist1 = (params) =>  api.post('unit_of_measure', {params }, options).then((res) =>{ 
// 	return res.data.datalist;
// })

// export const saveunitofmeasure = (getunitofmeasure1) =>  api.post(`unit_of_measure`, {...body, "action": "dataInsert", "menukey": "Unitofmeasureentry", ...getunitofmeasure1}, options).then((res) => res.data ) 
// export const deleteunitofmeasure = (id) =>  api.post(`unit_of_measure`,id, options)
// export const getunitofmeasure = (id) =>  api.post(`unit_of_measure`, {...body, "action": "getunit_measure", "menukey": "Unitofmeasureentry", "UnitId":id}, options)
// export const updateunitof = ({id, ...updatedunitof}) =>  api.put(`unit_of_measure`,{...body, "action": "dataUpdate", "menukey": "Unitofmeasureentry", "UnitId":id, ...updatedunitof}, options).then((res) =>{
// 	 return res;
// })

// //direction
// export const getdirection11 = () =>  api.post('direction', {...body, "action": "getdirectionlist", "menukey": "direction"}, options).then((res) =>{ 
//   return res.data.datalist;;
// })
// export const savedirection = (getdirection1) =>  api.post(`direction`, {...body, "action": "dataInsert", "menukey": "direction", ...getdirection1}, options).then((res) => res.data ) 


// //export const deletedirection = (id) =>  api.post(`direction`,{...body, "action": "dataDalete", "menukey": "direction", "DirectionId":id}, options)
// export const deletedirection = (id) =>  api.post(`direction`,id, options)

// export const gedirections = (id) =>  api.post(`direction`, {...body, "action": "gedirection", "menukey": "direction", "DirectionId":id}, options)

//  export const updatedirection = ({id, ...updatedirections}) =>  api.put(`direction`,{...body, "action": "dataUpdate", "menukey": "direction", "DirectionId":id, ...updatedirections}, options).then((res) =>{
//    return res;
// })

// //Interval
//  export const getInterval11 = () =>  api.post('interval', {...body, "action": "getIntervalList", "menukey": "interval"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geIntevals = (id) =>  api.post(`interval`, {...body, "action": "gedirection", "menukey": "interval", "IntervalId":id}, options)
// export const saveinverval = (getinverval1) =>  api.post(`interval`, {...body, "action": "dataInsert", "menukey": "interval", ...getinverval1}, options).then((res) => res.data ) 

// //export const deleteinterval = (id) =>  api.post(`interval`,{...body, "action": "dataDalete", "menukey": "interval", "IntervalId":id}, options)
// export const deleteinterval = (id) =>  api.post(`interval`,id, options)

//  export const updateInterval = ({id, ...updateintervals}) =>  api.put(`interval`,{...body, "action": "dataUpdate", "menukey": "interval", "IntervalId":id, ...updateintervals}, options).then((res) =>{
//    return res;
// }) 

// //LabTest
//  export const getLabTest22 = () =>  api.post('labtest', {...body, "action": "getLabTestList", "menukey": "labtest"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geLabTests = (id) =>  api.post(`labtest`, {...body, "action": "gedLebTest", "menukey": "labtest", "LabTestId":id}, options)
// export const saveLabTest = (getlabtest1) =>  api.post(`labtest`, {...body, "action": "dataInsert", "menukey": "labtest", ...getlabtest1}, options).then((res) => res.data ) 

// //export const deleteLabTest = (id) =>  api.post(`labtest`,{...body, "action": "dataDalete", "menukey": "labtest", "LabTestId":id}, options)
// export const deleteLabTest = (id) =>  api.post(`labtest`,id, options)

//  export const updateLabTest = ({id, ...updatelabtests}) =>  api.put(`labtest`,{...body, "action": "dataUpdate", "menukey": "labtest", "LabTestId":id, ...updatelabtests}, options).then((res) =>{
//    return res;
// }) 

// //Route of admin
//  export const getRouteofadmin33 = () =>  api.post('routeofadmin', {...body, "action": "getRouteOfAdminList", "menukey": "routeofadmin"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geRouteofadmins = (id) =>  api.post(`routeofadmin`, {...body, "action": "gedRouteofAdmin", "menukey": "routeofadmin", "RouteId":id}, options)
// export const saveRouteofadmin = (getrouteofadmin1) =>  api.post(`routeofadmin`, {...body, "action": "dataInsert", "menukey": "routeofadmin", ...getrouteofadmin1}, options).then((res) => res.data ) 

// //export const deleteRouteofadmin = (id) =>  api.post(`routeofadmin`,{...body, "action": "dataDalete", "menukey": "routeofadmin", "RouteId":id}, options)
// export const deleteRouteofadmin = (id) =>  api.post(`routeofadmin`,id, options)
//  export const updateRouteofadmin = ({id, ...updateRouteofadmins}) =>  api.put(`routeofadmin`,{...body, "action": "dataUpdate", "menukey": "routeofadmin", "RouteId":id, ...updateRouteofadmins}, options).then((res) =>{
//    return res;
// }) 

// //Order type 
//  export const getOrderType24 = () =>  api.post('ordertype', {...body, "action": "getOrderTypeList", "menukey": "routeofadmin"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geOrderTypes = (id) =>  api.post(`ordertype`, {...body, "action": "gedOrderType", "menukey": "routeofadmin", "OrderTypeId":id}, options)
// export const saveOrderType = (getordetype1) =>  api.post(`ordertype`, {...body, "action": "dataInsert", "menukey": "routeofadmin", ...getordetype1}, options).then((res) => res.data ) 

// //export const deleteOrderType = (id) =>  api.post(`ordertype`,{...body, "action": "dataDalete", "menukey": "routeofadmin", "OrderTypeId":id}, options)
// export const deleteOrderType = (id) =>  api.post(`ordertype`,id, options)

//  export const updateOrderType = ({id, ...updateOrderTypes}) =>  api.put(`ordertype`,{...body, "action": "dataUpdate", "menukey": "routeofadmin", "OrderTypeId":id, ...updateOrderTypes}, options).then((res) =>{
//    return res;
// }) 

// //title list
//  export const getTitleLists51 = () =>  api.post('titlelist', {...body, "action": "getTitleList", "menukey": "titlelist"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geTitleList = (id) =>  api.post(`titlelist`, {...body, "action": "geTitle", "menukey": "titlelist", "TitleId":id}, options)
// export const saveTitleList = (gettitlelist1) =>  api.post(`titlelist`, {...body, "action": "dataInsert", "menukey": "titlelist", ...gettitlelist1}, options).then((res) => res.data ) 

// //export const deleteTitleList = (id) =>  api.post(`titlelist`,{...body, "action": "dataDalete", "menukey": "titlelist", "TitleId":id}, options)
// export const deleteTitleList = (id) =>  api.post(`titlelist`,id, options)

//  export const updateTitleList = ({id, ...updateTitles}) =>  api.put(`titlelist`,{...body, "action": "dataUpdate", "menukey": "titlelist", "TitleId":id, ...updateTitles}, options).then((res) =>{
//    return res;
// }) 

// //marital status
//  export const getmaritalstatuslist55 = () =>  api.post('maritalstatus', {...body, "action": "getmaritalstatusList", "menukey": "maritalstatus"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geMatitalstatus = (id) =>  api.post(`maritalstatus`, {...body, "action": "gemaritalstaus", "menukey": "maritalstatus", "MaritalStatusId":id}, options)
// export const savemaritalstatus = (getmaritalstatus1) =>  api.post(`maritalstatus`, {...body, "action": "dataInsert", "menukey": "maritalstatus", ...getmaritalstatus1}, options).then((res) => res.data ) 

// //export const deletemaritalstatus = (id) =>  api.post(`maritalstatus`,{...body, "action": "dataDalete", "menukey": "maritalstatus", "MaritalStatusId":id}, options)
// export const deletemaritalstatus = (id) =>  api.post(`maritalstatus`,id, options)

//  export const updatemaritalstatus = ({id, ...updateTitles}) =>  api.put(`maritalstatus`,{...body, "action": "dataUpdate", "menukey": "maritalstatus", "MaritalStatusId":id, ...updateTitles}, options).then((res) =>{
//    return res;
// }) 

// //ABC List
//  export const getABClist77 = () =>  api.post('abc', {...body, "action": "getABCList", "menukey": "abc"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geABC = (id) =>  api.post(`abc`, {...body, "action": "geABC", "menukey": "abc", "AbcId":id}, options)
// export const saveABC = (getabc1) =>  api.post(`abc`, {...body, "action": "dataInsert", "menukey": "abc", ...getabc1}, options).then((res) => res.data ) 

// //export const deleteABC = (id) =>  api.post(`abc`,{...body, "action": "dataDalete", "menukey": "abc", "AbcId":id}, options)
// export const deleteABC = (id) =>  api.post(`abc`,id, options)

//  export const updateABC = ({id, ...updateabcsd}) =>  api.put(`abc`,{...body, "action": "dataUpdate", "menukey": "abc", "AbcId":id, ...updateabcsd}, options).then((res) =>{
//    return res;
// })

// //AgeGroup
// export const AgeGroupList = () =>  api.post('AgeGroup', {...body, "action": "getAgeGroupList", "menukey": "agegroup"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geAgeGroup = (id) =>  api.post(`AgeGroup`, {...body, "action": "geAgeGroup", "menukey": "agegroup", "AgeGroupId":id}, options)
// export const saveAgeGroup = (getAgeGrou1) =>  api.post(`AgeGroup`, {...body, "action": "dataInsert", "menukey": "agegroup", ...getAgeGrou1}, options).then((res) => res.data ) 

// //export const deleteABC = (id) =>  api.post(`abc`,{...body, "action": "dataDalete", "menukey": "abc", "AbcId":id}, options)
// export const deleteAgeGroup = (id) =>  api.post(`AgeGroup`,id, options)

//  export const updateAgeGroup = ({id, ...updateAgeGroupsd}) =>  api.put(`AgeGroup`,{...body, "action": "dataUpdate", "menukey": "agegroup", "AgeGroupId":id, ...updateAgeGroupsd}, options).then((res) =>{
//    return res;
// })


// // Post Entry Api
// export const getPostEntrys = () =>  api.post('postentry', {...body, "action": "getDataList", "menukey": "post-entry"}, options).then((res) =>{return res.data.datalist;})

// export const getPostEntry = (id)=>  api.post(`postentry`, {...body, "action": "getPostEntry", "menukey": "post-entry", "PostId":id}, options)
 
// export const updatePostEntry = ({id, ...updatedPostEntry}) =>  api.put(`postentry`,{...body, "action": "dataUpdate", "menukey": "post-entry", "PostId":id, ...updatedPostEntry}, options).then((res) =>{return res;})

// export const savePostEntry = (postentryData) =>  api.post(`postentry`, {...body, "action": "dataInsert", "menukey": "post-entry", ...postentryData}, options).then((res) => res.data ) 

// //export const deletePostEntry = (id)=>  api.post(`postentry`,{...body, "action": "dataDalete", "menukey": "post-entry", "PostId":id}, options)
// export const deletePostEntry = (id) =>  api.post(`postentry`,id, options)

// //Specialisation List Api
// export const getSpecialisation = () =>  api.post('comboscript', {...body, "action": "getSpecialisationList", "menukey": ""}, options).then((res) =>{return res.data;})

// // Specialisation Entry Api
// export const getSpecialisations = () =>  api.post('specialisation', {...body, "action": "getDataList", "menukey": "specialisation"}, options).then((res) =>{return res.data.datalist;})

// export const getSpecialisationData = (id)=>  api.post(`specialisation`, {...body, "action": "getSpecialisationData", "menukey": "specialisation", "SpecialisationId":id}, options)
 
// export const updateSpecialisation = ({id, ...updatedSpecialisation}) =>  api.put(`specialisation`,{...body, "action": "dataUpdate", "menukey": "specialisation", "SpecialisationId":id, ...updatedSpecialisation}, options).then((res) =>{return res;})

// export const saveSpecialisation = (specialisationData) =>  api.post(`specialisation`, {...body, "action": "dataInsert", "menukey": "specialisation", ...specialisationData}, options).then((res) => res.data ) 

// //export const deleteSpecialisation = (id)=>  api.post(`specialisation`,{...body, "action": "dataDalete", "menukey": "specialisation", "SpecialisationId":id}, options)
// export const deleteSpecialisation = (id) =>  api.post(`specialisation`,id, options)

// // Icd Entry Api
// export const getIcds = () =>  api.post('icd', {...body, "action": "getDataList", "menukey": "icd"}, options).then((res) =>{return res.data.datalist;})

// export const getIcdData = (id)=>  api.post(`icd`, {...body, "action": "getIcdData", "menukey": "icd", "ICDCode":id}, options)
 
// export const updateIcd = ({id, ...updatedIcd}) =>  api.put(`icd`,{...body, "action": "dataUpdate", "menukey": "icd", "ICDCode":id, ...updatedIcd}, options).then((res) =>{return res;})

// export const saveIcd = (icdData) =>  api.post(`icd`, {...body, "action": "dataInsert", "menukey": "icd", ...icdData}, options).then((res) => res.data ) 

// export const deleteIcd = (id)=>  api.post(`icd`,{...body, "action": "dataDalete", "menukey": "icd", "ICDCode":id}, options)



// // Adjustment Type Api
// export const getAdjustmentTypes = () =>  api.post('adjustmentType', {...body, "action": "getDataList", "menukey": "adjustment-type"}, options).then((res) =>{return res.data.datalist;})

// export const getAdjustmentType = (id)=>  api.post(`adjustmentType`, {...body, "action": "getAdjustmentType", "menukey": "adjustment-type", "AdjTypeId":id}, options)
 
// export const updateAdjustmentType = ({id, ...updatedAdjustmentType}) =>  api.put(`adjustmentType`,{...body, "action": "dataUpdate", "menukey": "adjustment-type", "AdjTypeId":id, ...updatedAdjustmentType}, options).then((res) =>{return res;})

// export const saveAdjustmentType = (adjustmentTypeData) =>  api.post(`adjustmentType`, {...body, "action": "dataInsert", "menukey": "adjustment-type", ...adjustmentTypeData}, options).then((res) => res.data ) 

// //export const deleteAdjustmentType = (id)=>  api.post(`adjustmentType`,{...body, "action": "dataDalete", "menukey": "adjustment-type", "AdjTypeId":id}, options)
// export const deleteAdjustmentType = (id) =>  api.post(`adjustmentType`,id, options)

// // Language Preference Api
// export const getLanguagePreferences = () =>  api.post('languagePreference', {...body, "action": "getDataList", "menukey": "language-preference"}, options).then((res) =>{return res.data.datalist;})

// export const getLanguagePreferenceData = (id)=>  api.post(`languagePreference`, {...body, "action": "getLanguagePreferenceData", "menukey": "language-preference", "Id":id}, options)
 
// export const updateLanguagePreference = ({id, ...updatedLanguagePreference}) =>  api.put(`languagePreference`,{...body, "action": "dataUpdate", "menukey": "language-preference", "Id":id, ...updatedLanguagePreference}, options).then((res) =>{return res;})

// export const saveLanguagePreference = (languagePreferenceData) =>  api.post(`languagePreference`, {...body, "action": "dataInsert", "menukey": "language-preference", ...languagePreferenceData}, options).then((res) => res.data ) 

// //export const deleteLanguagePreference = (id)=>  api.post(`languagePreference`,{...body, "action": "dataDalete", "menukey": "language-preference", "Id":id}, options)
// export const deleteLanguagePreference = (id) =>  api.post(`languagePreference`,id, options)

// // Pack Size Api
// export const getPackSizes = () =>  api.post('packSize', {...body, "action": "getDataList", "menukey": "pack-size"}, options).then((res) =>{return res.data.datalist;})

// export const getPackSizeData = (id)=>  api.post(`packSize`, {...body, "action": "getPackSizeData", "menukey": "pack-size", "PacksizeId":id}, options)
 
// export const updatePackSize = ({id, ...updatedPackSize}) =>  api.put(`packSize`,{...body, "action": "dataUpdate", "menukey": "pack-size", "PacksizeId":id, ...updatedPackSize}, options).then((res) =>{return res;})

// export const savePackSize = (packSizeData) =>  api.post(`packSize`, {...body, "action": "dataInsert", "menukey": "pack-size", ...packSizeData}, options).then((res) => res.data ) 

// //export const deletePackSize = (id)=>  api.post(`packSize`,{...body, "action": "dataDalete", "menukey": "pack-size", "PacksizeId":id}, options)
// export const deletePackSize = (id) =>  api.post(`packSize`,id, options)

// // Form Api
// export const getForms = () =>  api.post('form', {...body, "action": "getDataList", "menukey": "form"}, options).then((res) =>{return res.data.datalist;})

// export const getFormData = (id)=>  api.post(`form`, {...body, "action": "getFormData", "menukey": "form", "DosageFormId":id}, options)
 
// export const updateForm = ({id, ...updatedForm}) =>  api.put(`form`,{...body, "action": "dataUpdate", "menukey": "form", "DosageFormId":id, ...updatedForm}, options).then((res) =>{return res;})

// export const saveForm = (formData) =>  api.post(`form`, {...body, "action": "dataInsert", "menukey": "form", ...formData}, options).then((res) => res.data ) 

// //export const deleteForm = (id)=>  api.post(`form`,{...body, "action": "dataDalete", "menukey": "form", "DosageFormId":id}, options)
// export const deleteForm = (id) =>  api.post(`form`,id, options)

// // Strength Api
// export const getStrengths = () =>  api.post('strength', {...body, "action": "getDataList", "menukey": "strength"}, options).then((res) =>{return res.data.datalist;})

// export const getStrengthData = (id)=>  api.post(`strength`, {...body, "action": "getStrengthData", "menukey": "strength", "StrengthId":id}, options)
 
// export const updateStrength = ({id, ...updatedStrength}) =>  api.put(`strength`,{...body, "action": "dataUpdate", "menukey": "strength", "StrengthId":id, ...updatedStrength}, options).then((res) =>{return res;})

// export const saveStrength = (strengthData) =>  api.post(`strength`, {...body, "action": "dataInsert", "menukey": "strength", ...strengthData}, options).then((res) => res.data ) 

// //export const deleteStrength = (id)=>  api.post(`strength`,{...body, "action": "dataDalete", "menukey": "strength", "StrengthId":id}, options)
// export const deleteStrength = (id) =>  api.post(`strength`,id, options)

// //Dispensing Language List Api
// export const getLoginDispensingLanguage = async (options) =>  api.post('comboscript', {"action": "getDispensingLanguageList", "menukey": ""}, options).then((res) =>{return res.data;})
// export const getDispensingLanguage =  () =>  api.post('comboscript', {"action": "getDispensingLanguageList", "menukey": ""}, options).then((res) =>{return res.data;})


// // Ui Language Api

// export const updateUiLanguage = (params) =>  
// 	//console.log(params.id);
// 	api.put(`uiLanguage`,{...body, "action": "dataUpdate", "menukey": "ui-language", "LangLabelId":params.id, "LangText":params.LangText}, options).then((res) =>{return res;})


// export const getUiLanguages = (params) =>  api.post('uiLanguage', {params}, options).then((res) =>{ 
// 	return res.data.datalist;
// })

// export const getUiLanguageData = (id)=>  api.post(`uiLanguage`, {...body, "action": "getUiLanguageData", "menukey": "ui-language", "LangLabelId":id}, options)
 
// export const saveUiLanguage = (uiLanguageData) =>  api.post(`uiLanguage`, {...body, "action": "dataInsert", "menukey": "ui-language", ...uiLanguageData}, options).then((res) => res.data ) 

// // Facility Api api.js

// export const updateFacility = (params) =>
//   //console.log(params.id);
//   api
//     .put(
//       `facility`,
//       {
//         ...body,
//         action: "dataUpdate",
//         menukey: "facility",
//         FacilityId: params.id,
//         bDispense: params.bDispenseVal,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

// export const getFacilityCode = (params) =>
//   api.post("facility", { ...body, params }, options).then((res) => {
//     return res.data.datalist;
//   });

// export const deleteaFacility = (id) => api.post(`facility`, id, options); 

// export const getFacilities = (params) =>
//   api.post("facility", { params }, options).then((res) => {
//     return res.data.datalist;
//   });


//   export const saveFacility = (saveFacilityData) =>
//   api
//     .post(
//       `facility`,
//       { ...body, action: "dataInsert", menukey: "facility", ...saveFacilityData },
//       options
//     )
//     .then((res) => res.data);

//     export const updateSingleFacility = ({ id, ...updatedFacility }) =>
//   api
//     .put(
//       `facility`,
//       {
//         ...body,
//         action: "UpdateFacility",
//         UserName: UserName,
//         lan: lan,
//         menukey: "facility",
//         FacilityId: id,
//         ...updatedFacility,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

// export const getFacilitiesList = (params) =>
//   api.post("facility_list", { params }, options).then((res) => {
//     return res.data.datalist;
//   });

//   export const getSingleListView = (id) =>
//   api.post(
//     `facility_list_single_view`,
//     {
//       ...body,
//       action: "getFacilitySingleView",
//       UserName: UserName,
//       lan: lan,
//       menukey: "facility-list",
//       FacilityId: id,
//     },
//     options
//   );

//   export const getFacilitySingleView = (id) =>
//   api.post(
//     `facility`,
//     {
//       ...body,
//       action: "getFacilitySingleView",
//       UserName: UserName,
//       lan: lan,
//       menukey: "facility",
//       FacilityId: id,
//     },
//     options
//   );

//   export const gZonelist = (zoneData) => { 
//     //console.log("dddd",zoneData);
//     return api
//       .post(
//         "comboscript",
//         { ...zoneData },
//         options
//       )
//       .then((res) => {

//         return res;

//       });
//   };
  
//   export const gCommunelist = (communeData) => { 
//     //console.log("dddd",zoneData);
//     return api
//       .post(
//         "comboscript",
//         { ...communeData },
//         options
//       )
//       .then((res) => {

//         return res;

//       });
//   };

 
// export const getPatientTotal = (params) =>  api.post('patients', {params }, options).then((res) =>{ 
// 	return res.data.datalist;
// })

// //Role Entry from
//  export const getRole = () =>  api.post('roles', {...body, "action": "getRoleList", "menukey": "role"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geRoles = (id) =>  api.post(`roles`, {...body, "action": "geRole", "menukey": "role", "id":id}, options)
// export const saveRole = (getrole1) =>  api.post(`roles`, {...body, "action": "dataInsert", "menukey": "role", ...getrole1}, options).then((res) => res.data ) 

// //export const deleteRole = (id) =>  api.post(`roles`,{...body, "action": "dataDalete", "menukey": "role", "id":id}, options)
// export const deleteRole = (id) =>  api.post(`roles`,id, options)
//  export const updateRole = ({id, ...updateRoles}) =>  api.put(`roles`,{...body, "action": "dataUpdate", "menukey": "role", "id":id, ...updateRoles}, options).then((res) =>{
//    return res;
// })


// // User Entry Api
// export const getUserList = () =>
//   api
//     .post(
//       "userlist",
//       { ...body, action: "getUserList", menukey: "user-entry" },
//       options
//     )
//     .then((res) => {
//       return res.data.datalist;
//     });

// export const getUser = (id) =>
//   api.post(
//     `userlist`,
//     { ...body, action: "getUser", menukey: "user-entry", id: id },
//     options
//   );
  
// export const updateUser = ({ id, ...updatedUser }) =>
//   api
//     .put(
//       `userlist`,
//       {
//         ...body,
//         action: "dataUpdate",
//         menukey: "user-entry",
//         user_id: id,
//         ...updatedUser,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

// export const saveUser = (userData) =>
//   api
//     .post(
//       `userlist`,
//       { ...body, action: "dataInsert", menukey: "user-entry", ...userData },
//       options
//     )
//     .then((res) => res.data);
//     export const userDelete = (id) =>  api.post(`userlist`,id, options)
// // export const userDelete = (id) =>
// //   api.post(
// //     `userlist`,
// //     { ...body, action: "userDelete", menukey: "user-entry", user_id: id },
// //     options
// //   );

//   //role permission

// export const getRoleAccessEntry = () =>  api.post('roleaccess', {...body, "action": "getRoleList", "menukey": "roleaccess"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 

// export const getMenuPerEntry = (params) =>
//   api
//     .post("roleaccess", { params }, options)
//     .then((res) => {
//       return res.data.datalist;
//     });

// export const rolesToMenuInsert = (params) =>  
//   //console.log(params.id);
//   api.post(`roleaccess`,{...body, "action": "dataInsert", UserName: UserName, lan: lan, "menukey": "role-access-entry", "role_id":params.selectedRoleData, "menu_id":params.menu_id, "bChecked": params.bCheckedVal}, options).then((res) =>{return res;})


// //Fetch Month Close Data api
// export const getMonthCloseData = (params) =>  api.post('monthclose', {params}, options).then((res) =>{ 
//   console.log("Get data for table api");
// 	return res.data.datalist;
// })


// //Month Close Api Generate Data
// export const monthCloseByFacility = (params) =>  {
//   console.log("Calling Month close api");
//   api.post(`monthclose`,
//   {...body, "action": "dataInsert", "menukey": "month-close", "currYearId":params.currYearId,
//    "currMonthId":params.MonthId, "currFacilityId": params.FacilityId, "StatusId": params.StatusId,
//     "UserId": params.UserId}, options).then((res) =>{return res;})
// }


// //Month Close Delete Api
// export const monthCloseDeleteByFacility = (params) =>  {
//   console.log("Responsed Monthly data delete call api");
  
//   //api.post(`monthclose`,{...body, "action": "dataInsert", "menukey": "month-close", "currYearId":params.currYearId, "currMonthId":params.currMonthId, "currFacilityId": params.currFacilityId}, options).then((res) =>{return res;})
//   api.post(`monthclose`,{...body, "action": "dataDeleteByFacility", "menukey": "month-close", "currYearId":params.currYearId, "currMonthId":params.MonthId, "currFacilityId": params.FacilityId, "StatusId": params.StatusId}, options).then((res) =>{return res;})
// }



// export const getMenuShortOrder = (params) =>
//   api
//     .post("roleaccess", { params }, options)
//     .then((res) => {
//       return res.data.datalist;
//     });

//     export const updateMenuShortOrder = (params) =>  
//       //console.log(params.id);
//       api.put(`roleaccess`,{...body, "action": "dataUpdate", UserName: UserName, lan: lan, "menukey": "role-access-entry", "MenuData":params.MenuData}, options).then((res) =>{return res;})
    

// // Generics Api
// export const getGenericss = () =>  api.post('generics', {...body, "action": "getDataList", "menukey": "generics"}, options).then((res) =>{return res.data.datalist;})

// export const getGenericsData = (id)=>  api.post(`generics`, {...body, "action": "getGenericsData", "menukey": "generics", "GenericId":id}, options)
 
// export const updateGenerics = ({id, ...updatedGenerics}) =>  api.put(`generics`,{...body, "action": "dataUpdate", "menukey": "generics", "GenericId":id, ...updatedGenerics}, options).then((res) =>{return res;})

// export const saveGenerics = (genericsData) =>  api.post(`generics`, {...body, "action": "dataInsert", "menukey": "generics", ...genericsData}, options).then((res) => res.data ) 

// //export const deleteGenerics = (id)=>  api.post(`generics`,{...body, "action": "dataDalete", "menukey": "generics", "GenericId":id}, options)
// export const deleteGenerics = (id) =>  api.post(`generics`,id, options)

// //Patient Import
// export const savePatientImport = (PatientImportData) =>
//   api.post(`patient_import`, {
// 	  action: "PatientImportDataInsert", 
// 	  menukey: "patient-import", 
// 	  ...PatientImportData
// 	}, options).then((res) => res.data);


// // MOS Range Entry Api
// export const getMOSRanges = () =>  api.post('mosrange', {...body, "action": "getDataList", "menukey": "mos-range"}, options).then((res) =>{return res.data.datalist;})

// export const getMOSRange = (id)=>  api.post(`mosrange`, {...body, "action": "getMOSRange", "menukey": "mos-range", "MosTypeId":id}, options)
 
// export const updateMOSRange = ({id, ...updatedMOSRange}) =>  api.put(`mosrange`,{...body, "action": "dataUpdate", "menukey": "mos-range", "MosTypeId":id, ...updatedMOSRange}, options).then((res) =>{return res;})

// export const saveMOSRange = (mosrangeData) =>  api.post(`mosrange`, {...body, "action": "dataInsert", "menukey": "mos-range", ...mosrangeData}, options).then((res) => res.data ) 

// //export const deleteMOSRange = (id)=>  api.post(`mosrange`,{...body, "action": "dataDalete", "menukey": "mos-range", "MosTypeId":id}, options)
// export const deleteMOSRange = (id) =>  api.post(`mosrange`,id, options)



// //Location List
// export const getLocationsList = () =>  api.post('location', {...body, "action": "getLocation", "menukey": "location"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const getLocations = (id) =>  api.post(`location`, {...body, "action": "getLocationsid", "menukey": "location", "LocationId":id}, options)
// export const SaveLocation = (getLocation1) =>  api.post(`location`, {...body, "action": "dataInsert", "menukey": "location", ...getLocation1}, options).then((res) => res.data ) 

// //export const deleteABC = (id) =>  api.post(`abc`,{...body, "action": "dataDalete", "menukey": "abc", "AbcId":id}, options)
// export const DeleteLocation = (id) =>  api.post(`location`,id, options)

//  export const UpdateLocations = ({id, ...UpdateLocation}) =>  api.put(`location`,{...body, "action": "dataUpdate", "menukey": "location", "LocationId":id, ...UpdateLocation}, options).then((res) =>{
//    return res;
// })


// //menu Insert
// export const getmenuEntry = () =>
//   api
//     .post(
//       "menuentry",
//       { ...body, action: "getMenuList", menukey: "menuentry" },
//       options
//     )
//     .then((res) => {
//       return res.data.datalist;
//     });

//     export const saveMenuentry = (userData) =>api.post(`menuentry`,{ ...body, action: "dataInsert", menukey: "menuentry", ...userData },options)
//     .then((res) => res.data);

//     export const updateMenuentry = ({ id, ...updatedMenu }) =>api.put(`menuentry`,{
//           ...body,
//           action: "dataUpdate",
//           menukey: "menuentry",
//           id: id,
//           ...updatedMenu,
//         },
//         options
//       )
//       .then((res) => {
//         return res;
//       });


//     export const deletemenu = (id) => api.post(`menuentry`, id, options);
      
// export const geImenuentrys = (id) =>
//   api.post(
//     `menuentry`,
//     { ...body, action: "gemenuEntry", menukey: "menuentry", id: id },
//     options
//   );


// //ATC List
// export const getatcEntry = () =>  api.post('atc', {...body, "action": "getATCList", "menukey": "atc"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geAtc = (id) =>  api.post(`atc`, {...body, "action": "geATC", "menukey": "atc", "ATC":id}, options)
// export const saveATC = (getatc1) =>  api.post(`atc`, {...body, "action": "dataInsert", "menukey": "atc", ...getatc1}, options).then((res) => res.data ) 

// //export const deleteABC = (id) =>  api.post(`abc`,{...body, "action": "dataDalete", "menukey": "abc", "AbcId":id}, options)
// export const deleteATC = (id) =>  api.post(`atc`,id, options)

//  export const updateATC = ({id, ...updateATCed}) =>  api.put(`atc`,{...body, "action": "dataUpdate", "menukey": "atc", "ATC":id, ...updateATCed}, options).then((res) =>{
//    return res;
// })


// //Source Entry
// export const getsourceEntry = () =>  api.post('source', {...body, "action": "getSourceList", "menukey": "source"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geSource = (id) =>  api.post(`source`, {...body, "action": "geSource", "menukey": "source", "SourceId":id}, options)
// export const saveSource = (getSource1) =>  api.post(`source`, {...body, "action": "dataInsert", "menukey": "source", ...getSource1}, options).then((res) => res.data ) 

// export const deletesource = (id) =>  api.post(`source`,id, options)

// export const updatesource = ({id, ...updatesourceed}) =>  api.put(`source`,{...body, "action": "dataUpdate", "menukey": "source", "SourceId":id, ...updatesourceed}, options).then((res) =>{
//    return res;
// })

// export const getStockSummaryChart = (params) =>  {
//   return api.post('StockSummary', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// export const getStockSummaryTable = (params) =>  {
//   return api.post('StockSummary', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }



// export const getConsumptionTrendsForTheTopFiveProductsChart = (pYearId, pMonthId, pFacilityId = 0, menukey) => {

// 	return api
// 	  .post(
// 		"ConsumptionTrendsForTheTopFiveProducts",
// 		{ ...body, action: "getConsumptionTrendsForTheTopFiveProductsChart", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId},
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// export const getConsumptionTrendsForTheTopFiveProductsTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"ConsumptionTrendsForTheTopFiveProducts",
// 		{ ...body, action: "getConsumptionTrendsForTheTopFiveProductsTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };


// export const getStockStatusDetailsChart = (params) =>  {
//   return api.post('StockStatusDetails', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// export const getAMCTrendsforTheTopFiveProductsChart = (pYearId, pMonthId, pFacilityId = 0, menukey) => {

//   return api
//       .post(
//         "AMCTrendsforTheTopFiveProducts",
//         { ...body, action: "getAMCTrendsforTheTopFiveProductsChart", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });
// };

// export const getAMCTrendsforTheTopFiveProductsTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"AMCTrendsforTheTopFiveProducts",
// 		{ ...body, action: "getAMCTrendsforTheTopFiveProductsTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// export const getPercentageOfFacilitiesWithAStockoutOfTracerCommoditiesChart = (pYearId, pMonthId, pFacilityId = 0, menukey) => {

//   return api
//       .post(
//         "PercentageOfFacilitiesWithAStockoutOfTracerCommodities",
//         { ...body, action: "getPercentageOfFacilitiesWithAStockoutOfTracerCommoditiesChart", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });
// };

// export const getPercentageOfFacilitiesWithAStockoutOfTracerCommoditiesTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"PercentageOfFacilitiesWithAStockoutOfTracerCommodities",
// 		{ ...body, action: "getPercentageOfFacilitiesWithAStockoutOfTracerCommoditiesTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// export const getAvailabilityOfCommoditiesChart = (params) =>  {
//   return api.post('AvailabilityOfCommodities', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// //User profile

// export const UserProfileupdate = ({ id, ...updatedUser }) =>
//   api
//     .put(
//       `Myprofile`,
//       {
//         ...body,
//         action: "dataUpdate",
//         menukey: "user-entry",
//         user_id: id,
//         ...updatedUser,
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

//     export const getUserProfile = (id) =>
//   api.post(
//     `Myprofile`,
//     { ...body, action: "getUser", menukey: "user-entry", id: id },
//     options
//   );


// //Adult Patients by Protocol

// export const getAdultPatientsByProtocolChart = (pYearId, pMonthId, pFacilityId = 0, pGenderTypeId = 0, pAgeGroupId = 0, toggleButtonValueAdult = 1) => {

  
//   return api
//       .post(
//         "AdultPatientsByProtocol",
//         { ...body, action: "getAdultPatientsByProtocolChart", menukey: "adult-patients-by-protocol", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId,GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId, toggleButtonValueAdult: toggleButtonValueAdult  },
//         options
//       )
//       .then((res) => {
        
//         return res.data.datalist;
//       });

// };

// export const getAdultPatientsByProtocolTable = (params) =>  {
//   return api.post('AdultPatientsByProtocol', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //Paediatric Patients by Protocol

// export const getPaediatricPatientsByProtocolChart = (pYearId, pMonthId, pFacilityId = 0, pGenderTypeId = 0, pAgeGroupId = 0, toggleButtonValuePaediatric = 1) => {

  
//   return api
//       .post(
//         "PaediatricPatientsByProtocol",
//         { ...body, action: "getPaediatricPatientsByProtocolChart", menukey: "paediatric-patients-by-protocol", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId,GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId, toggleButtonValuePaediatric: toggleButtonValuePaediatric  },
//         options
//       )
//       .then((res) => {
        
//         return res.data.datalist;
//       });

// };

// export const getPaediatricPatientsByProtocolTable = (params) =>  {
//   return api.post('PaediatricPatientsByProtocol', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }


// //Trend of Active Patients

// export const getTrendofActivePatientsChart = (pYearId, pMonthId, pFacilityId = 0, pGenderTypeId = 0, pAgeGroupId = 0) => {

  
//   return api
//       .post(
//         "TrendofActivePatients",
//         { ...body, action: "getTrendofActivePatientsChart", menukey: "trend-of-active-patients", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId, GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId  },
//         options
//       )
//       .then((res) => {
      
//         return res.data.datalist;
//       });

// };

// export const getTrendofActivePatientsTable = (pYearId, pMonthId, pFacilityId = 0, pGenderTypeId = 0, pAgeGroupId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"TrendofActivePatients",
// 		{ ...body, action: "getTrendofActivePatientsTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId, GenderTypeId: pGenderTypeId, AgeGroupId: pAgeGroupId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// //TLD Uptake
// export const getTLDUptakePatientsTrendChart = (pYearId, pMonthId, pFacilityId = 0) => {
  
//   return api
//       .post(
//         "TldUptake",
//         { ...body, action: "getTLDUptakePatientsTrendChart", menukey: "tld-uptake", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
        
        
//         return res.data.datalist;
//       });

// };

// export const getTLDUptakePatientsTrendTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"TldUptake",
// 		{ ...body, action: "getTLDUptakePatientsTrendTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };


// //TLD Transition Trend
// export const getTLDTransitionPatientsTrendChart = (pYearId, pMonthId, pFacilityId = 0) => {
  
//   return api
//       .post(
//         "TldTransitionTrend",
//         { ...body, action: "getTLDTransitionPatientsTrendChart", menukey: "tld-transition-trend", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
       
//         return res.data.datalist;
//       });

// };


// export const getTLDTransitionPatientsTrendTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"TldTransitionTrend",
// 		{ ...body, action: "getTLDTransitionPatientsTrendTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };


// //MMD Among Adults (+15 years
// export const getMMDAmongAdultPatientsTrendChart = (pYearId, pMonthId, pFacilityId = 0) => {
  
//   return api
//       .post(
//         "MmdAmongAdults",
//         { ...body, action: "getMMDAmongAdultPatientsTrendChart", menukey: "mmd-among-adults", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
       
//         return res.data.datalist;
//       });

// };

// export const getMMDAmongAdultPatientsTrendTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"MmdAmongAdults",
// 		{ ...body, action: "getMMDAmongAdultPatientsTrendTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// //MMD Coverage by site
// export const getMMDCoveragePatientsTrendChart = (pYearId, pMonthId, pFacilityId = 0) => {
  
//   return api
//       .post(
//         "MmdCoverageBysite",
//         { ...body, action: "getMMDCoveragePatientsTrendChart", menukey: "mmd-coverage-by-site", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
       
//         return res.data.datalist;
//       });

// };


// export const getMMDCoveragePatientsTrendTable = (params) =>  {
//   return api.post('MmdCoverageBysite', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }


// export const getAvailabilityOfCommoditiesTable = (params) =>  {
//   return api.post('AvailabilityOfCommodities', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// export const getStockStatusDetailsTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"StockStatusDetails",
// 		{ ...body, action: "getStockStatusDetailsTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		// return res.data.datalist;
// 		return res.data.datalist.data;
// 	  });
// };

// //Regimen
//  export const getRegimenList = () =>  api.post('Regimen', {...body, "action": "getRegimenList", "menukey": "regimen"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 

// export const getRegimen = (id) =>  api.post(`Regimen`, {...body, "action": "getRegimen", "menukey": "regimen", "RegimenId":id}, options)

// export const saveRegimen = (formData) =>  api.post(`Regimen`, {...body, "action": "dataInsert", "menukey": "regimen", ...formData}, options).then((res) => res.data ) 

// export const updateRegimen = ({id, ...formData}) =>  api.put(`Regimen`,{...body, "action": "dataUpdate", "menukey": "regimen", "RegimenId":id, ...formData}, options).then((res) =>{
//    return res;
// })

// export const deleteRegimen = (id) =>  api.post(`Regimen`,id, options)


// //LMIS Report
// export const getLMISReportTable = (params) =>  {
//   return api.post('LMISReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //Patient Status Report
// export const getPatientStatusReportTable = (params) =>  {
//   return api.post('PatientStatusReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }


// //Products Nearing Expiry Report
// export const getProductsNearingExpiryReportTable = (params) =>  {
//   return api.post('ProductsNearingExpiryReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

 
// export const gFacilitylist = (facilityData) => { 
//   //console.log("dddd",zoneData);
//   return api
//     .post(
//       "comboscript",
//       { ...facilityData },
//       options
//     )
//     .then((res) => {

//       return res;

//     });
// };

// // MMD Among Adults (-15 years)
// export const getMMDAmongPaediatricsPatientsTrendLess15Years = (pYearId, pMonthId, pFacilityId = 0) => {
//   console.log("pYearId: ", pYearId , ", pMonthId: ", pMonthId,"pFacilityId: ", pFacilityId);

//   return api
//       .post(
//         "dashboardpage",
//         { ...body, action: "getMMDAmongPaediatricsPatientsTrendLess15Years", menukey: "dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         // console.log("patientdashboard patient count: ", res.data.datalist);
        
//         console.log("getMMDAmongPaediatricsPatientsTrendLess15Years patient count: ", res.data.datalist);
//         return res.data.datalist;
//       });

// };

// //MMD Among Paediatric (<15 years) Details Report
// export const getMMDAmongPaediatricPatientsTrendChart = (pYearId, pMonthId, pFacilityId = 0) => {
  
//   return api
//       .post(
//         "MmdAmongPaediatrics",
//         { ...body, action: "getMMDAmongPaediatricPatientsTrendChart", menukey: "mmd-among-paediatrics", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
       
//         return res.data.datalist;
//       });

// };

// export const getMMDAmongPaediatricPatientsTrendTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"MmdAmongPaediatrics",
// 		{ ...body, action: "getMMDAmongPaediatricPatientsTrendTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };


// // Year entry
// export const getYearEntry = () => api.post("yearentry", { ...body, action: "getDataList", menukey: "year-entry" }, options).then((res) => {
//   return res.data.datalist;
// });
// export const saveYearEntry = (ServiceData) => api.post(`yearentry`, { ...body, action: "dataInsert", menukey: "year-entry", ...ServiceData }, options)
//   .then((res) => res.data);
// export const updateYearEntry = ({ id, ...updategetService }) => api.put(`yearentry`, { ...body, "action": "dataUpdate", "menukey": "year-entry", "YearID": id, ...updategetService }, options).then((res) => {
//   return res;
// })
// export const getYearEntryData = (id) => api.post(`yearentry`, { ...body, action: "getData", menukey: "year-entry", YearID: id, }, options);
// export const deleteYearEntry = (id) => api.post(`yearentry`, id, options)

// // end Year entry


// //Patient Co-infection Entry

// export const getPatientcoinfectionData = () =>  api.post('Patientcoinfection', {...body, "action": "getPatientcoinfectionList", "menukey": "patient-co-infection-entry"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const getPatientcoinfectionSingle = (id) =>  api.post(`Patientcoinfection`, {...body, "action": "getPatientcoinfectionSingle", "menukey": "patient-co-infection-entry", "PatientcoinfectionId":id}, options)
// export const savePatientcoinfection = (getordetype1) =>  api.post(`Patientcoinfection`, {...body, "action": "dataInsert", "menukey": "patient-co-infection-entry", ...getordetype1}, options).then((res) => res.data ) 

// export const deletePatientcoinfection = (id) =>  api.post(`Patientcoinfection`,id, options)

//  export const updatePatientcoinfection = ({id, ...updatePatientcoinfections}) =>  api.put(`Patientcoinfection`,{...body, "action": "dataUpdate", "menukey": "patient-co-infection-entry", "PatientcoinfectionId":id, ...updatePatientcoinfections}, options).then((res) =>{
//    return res;
// }) 


// //Key Population Type Entry

// export const getKeypopulationtypeData = () =>  api.post('Keypopulationtype', {...body, "action": "getKeypopulationtypeList", "menukey": "key-population-type-entry"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const getKeypopulationtypeSingle = (id) =>  api.post(`Keypopulationtype`, {...body, "action": "getKeypopulationtypeSingle", "menukey": "key-population-type-entry", "KeypopulationTypeId":id}, options)
// export const saveKeypopulationtype = (getordetypex) =>  api.post(`Keypopulationtype`, {...body, "action": "dataInsert", "menukey": "key-population-type-entry", ...getordetypex}, options).then((res) => res.data ) 

// export const deleteKeypopulationtype = (id) =>  api.post(`Keypopulationtype`,id, options)

//  export const updateKeypopulationtype = ({id, ...updateKeypopulationtypes}) =>  api.put(`Keypopulationtype`,{...body, "action": "dataUpdate", "menukey": "key-population-type-entry", "KeypopulationTypeId":id, ...updateKeypopulationtypes}, options).then((res) =>{
//    return res;
// }) 

// ///Adjustment
// export const getAdjustmentInvs = (params) =>
//   api
//     .post("adjustmentinvoicelist", { params }, options)
//     .then((res) => {
//       return res.data.adjustmentinvdata;
//     });
	
//   export const saveAdjustment = (adjustmentData) =>
//   api.post(`adjustmentSave`, { ...body,...adjustmentData, menukey: "adjustment" }, options).then((res) => res.data);
  
//   export const updateAdjustmentInv = ({ id, ...updatedDispenser }) =>
//   api
//     .put(
//       `adjustmentUpdate`,
//       { ...body, menukey: "receive", ...updatedDispenser },
//       options
//     )
//     .then((res) => {
//       return res.data;
//     });	

//     export const getAdjustmentInv = (id) =>
//     api.post(
//       `adjustmentListSingle`,
//       { ...body, menukey: "receive", TransactionId: id,"FacilityId": FacilityId },
//       options
//     );

//     export const deleteAdjustmentInv = (id) =>
//     api.post(
//       `adjustmentDelete`,
//       { ...body, menukey: "receive", TransactionId: id, "FacilityId": FacilityId, UserName: UserName, lan: lan },
//       options
//     ).then((res) => {
//         return res.data;
//    });

// // Error Log
// export const getErrorLogData = (params) =>
// api.post("errorlog", { params }, options).then((res) => {
//   return res.data.datalist;
// });


// // Audit Log

// export const getAuditLogData = (params) =>
//   api
//     .post(
//       "auditlog",
//       { params },
//       options
//     )
//     .then((res) => {
//       return res.data.datalist;
//     });
// export const getScanLog = (params) =>
//   api
//     .post(
//       "scanlog",
//       { params },
//       options
//     )
//     .then((res) => {
//       return res.data.datalist;
//     });

// export const getLogSingleRow = (params) =>
//   api.post("auditlog", { params }, options).then((res) => {
//     return res.data.datalist;
//   });


// export const gDataBaseInfo = (params) =>
//   api.post("auditlog", { params }, options).then((res) => {
//     return res.data.datalist;
//   });

// export const gTableInfo = (params) =>
//   api.post("auditlog", { params }, options).then((res) => {
//     return res.data.datalist;
//   });

// //LTFU Back Report 
// export const getLtfuBackReportTable = (params) =>  {
//   return api.post('LtfuBackReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //LTFU Lost to Sight Report 
// export const getLtfuLostToSightReportTable = (params) =>  {
//   return api.post('LtfuLostToSightReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //Missed Appointments with Days Precision Report 
// export const getMissedAppointmentswithDaysPrecisionReportTable = (params) =>  {
//   return api.post('MissedAppointmentswithDaysPrecisionReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //Key Populations Active Queue report
// export const getKeyPopulationsActiveQueueReportTable = (params) =>  {
//   return api.post('KeyPopulationsActiveQueueReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //PreEP Patients Report
// export const getPreEPPatientsReportTable = (params) =>  {
//   return api.post('PreEPPatientsReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //Patient Report under IO
// export const getPatientReportUnderIOTable = (params) =>  {
//   return api.post('PatientReportUnderIO', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

// //Prescription Item modal
// export const getPrescriptionItemListModal = () =>
// api.post("prescriptionModal", { ...body, action: "getPrescriptionItemListModal", menukey: "protocol" }, options).then((res) => {
//   return res.data.prescriptionitemlist;
// });


// ///Repacking
// export const getRepackingInvs = (params) =>
//   api
//     .post("repackinginvoicelist", { params }, options)
//     .then((res) => {
//       return res.data.adjustmentinvdata;
//     });
	
//   export const saveRepacking = (repackingData) =>
//   api.post(`repackingSave`, { ...body,...repackingData, menukey: "repacking" }, options).then((res) => res.data);
  
//   export const updateRepackingInv = ({ id, ...updatedDispenser }) =>
//   api
//     .put(
//       `repackingUpdate`,
//       { ...body, menukey: "receive", ...updatedDispenser },
//       options
//     )
//     .then((res) => {
//       return res.data;
//     });	

//     export const getRepackingInv = (id) =>
//     api.post(
//       `repackingListSingle`,
//       { ...body, menukey: "receive", TransactionId: id,"FacilityId": FacilityId },
//       options
//     );

//     export const deleteRepackingInv = (id) =>
//     api.post(
//       `repackingDelete`,
//       { ...body, menukey: "receive", TransactionId: id, "FacilityId": FacilityId, UserName: UserName, lan: lan },
//       options
//     ).then((res) => {
//         return res.data;
//    });

//   export const getRepackingProductsLot = (id,ProductRepackingToVal) =>
//   api.post("repackingproducts", { ...body, action: "getRepackingLot", menukey: "repacking",FacilityId:id,ProductRepackingToVal:ProductRepackingToVal }, options).then((res) => {
//     //console.log("ProductRepackingToVal ",ProductRepackingToVal)
//     return res.data.repackingproductlist;
//   });
  
// //Information sheet for PLHIV patients on INH prophylaxis
// export const getInformationSheetForPLHIVPatientsOnINHProphylaxisTable = (params) =>  {
//   return api.post('InformationSheetForPLHIVPatientsOnINHProphylaxis', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }


// //Cohort Type
// export const getCohortTypes = () =>  api.post('cohorttype', {...body, "action": "getCohortType", "menukey": "cohort-type"}, options).then((res) =>{ 
//   return res.data.datalist;
// }) 
// export const geCohortType = (id) =>  api.post(`cohorttype`, {...body, "action": "geCohortTypes", "menukey": "cohort-type", "CohortId":id}, options)
// export const saveCohortType = (getcohorttypelist) =>  api.post(`cohorttype`, {...body, "action": "dataInsert", "menukey": "cohort-type", ...getcohorttypelist}, options).then((res) => res.data ) 

// export const deleteCohortType = (id) =>  api.post(`cohorttype`,id, options)

//  export const updateCohortType = ({id, ...updateCohortTypes}) =>  api.put(`cohorttype`,{...body, "action": "dataUpdate", "menukey": "cohort-type", "CohortId":id, ...updateCohortTypes}, options).then((res) =>{
//    return res;
// }) 




// // Start Facility Group Map Api

// export const getFacilityGroupMap = (params) => {
//   const FacilityId = params.queryKey[0].FacilityId;
//   return api
//     .post("facilityGroupMap", { ...body, FacilityId, action: "getDataList", menukey: "facilityGroupMap" }, options)
//     .then((res) => {
//       return res.data.datalist;
//     });


// };

// export const saveFacilityGroupMap = (facilityGroupMapData) =>
//   api.post(`facilityGroupMap`,
//     {
//       ...body,
//       action: "dataInsert",
//       menukey: "facilityGroupMap",
//       ...facilityGroupMapData
//     }
//     , options
//   )
//     .then((res) => res.data);

// export const updateFacilityGroupMap = ({ id, ...updateFacilityGroupMap }) =>
//   api.put(
//     `facilityGroupMap`,
//     {
//       ...body,
//       "action": "dataUpdate",
//       "menukey": "facilityGroupMap",
//       "FacilityServiceId": id,
//       ...updateFacilityGroupMap
//     },
//     options).then((res) => { return res; })

// export const getFacilityGroupMapById = (id) =>
//   api.post(
//     `facilityGroupMap`,
//     {
//       ...body,
//       action: "getDataById",
//       menukey: "facilityGroupMap",
//       FacilityServiceId: id,
//     }
//     , options
//   );

// export const deleteFacilityGroupMap = (params) =>
//   api.post(
//     `facilityGroupMap`,
//     {
//       ...body,
//       action: "dataDalete",
//       UserName: UserName,
//       lan: localStorage.getItem("LangCode"),
//       menukey: "facility",
//       ...params,
//       UserId: UserName,
//     },
//     options
//   );

// export const getSupplyFromList = (params) => api.post("facilityGroupMap", { ...body, ...params, action: "getFacilityWarehouseList", menukey: "facilityGr" }, options).then((res) => {

//   return res.data.datalist;;
// });



// export const AssignGroupFacility = (params) =>
//   api
//     .put(
//       `facility`,
//       {
//         ...body,
//         ...params,
//         action: "insertUpdateFacilityGroupMapPopup",
//         menukey: "facility",
//         UserId: UserName
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });

// export const getProductGroupMapPopup = (params) =>
//   api
//     .put(
//       `facility`,
//       {
//         ...body,
//         ...params,
//         action: "getProductGroupMapPopup",
//         menukey: "facility",
//         UserId: UserName
//       },
//       options
//     )
//     .then((res) => {
//       return res;
//     });
// // End Facility Group Map Api

// //Issue
// export const getIssueInvs = (params) =>
//   api
//     .post("issueinvoicelist", { params }, options)
//     .then((res) => {
//       return res.data.receiveinvdata;
//     });

// export const deleteIssueInv = (id) =>
//   api.post(
//     `issueDelete`,
//     { ...body, menukey: "issue", TransactionId: id, "FacilityId": FacilityId, UserName: UserName, lan: lan },
//     options
//   ).then((res) => {
//       return res.data;
//  });

//  export const getIssueInv = (id) =>
//   api.post(
//     `issueListSingle`,
//     { ...body, menukey: "issue", TransactionId: id,"FacilityId": FacilityId },
//     options
//   );

//   export const updateIssueInv = ({ id, ...updatedDispenser }) =>
//   api
//     .put(
//       `issueUpdate`,
//       { ...body, menukey: "issue", ...updatedDispenser, UserName: UserName, lan: lan },
//       options
//     )
//     .then((res) => {
//       return res.data;
//     });	
	
// // export const saveIssueInv = (dispenserData) =>
// //   api.post(`issueSave`, dispenserData, options).then((res) => res.data);

//   export const saveIssueInv = (dispenserData) =>
//   api
//     .post(
//       `issueSave`,
//       { ...body, UserName: UserName, lan: lan ,  menukey: "issue", ...dispenserData },
//       options
//     )
//     .then((res) => res.data);




 
// export const saveNonIssuedOrder = (id) =>
//   api
//     .post(
//       `issuedNonReceiveOrderProducts`,
//       { ...body, menukey: "issue","FacilityId": FacilityId, OrderId: id, UserName: UserName, lan: lan },
//       options
//     )
//     .then((res) => {
//       return res.data.receiveinvdata;
// });

// export const getNonIssuedReceivedOrderList = (params) =>
// api.post("issuedReceivedNonOrderList", { params }, options).then((res) => {
//   return res.data.receiveinvdata;
// });

// ///Dispense Start
// export const getDispenseInvs = (params) =>
// api
//   .post("dispenseinvoicelist", { params }, options)
//   .then((res) => {
//     return res.data.dispenseinvdata;
//   });
// export const saveDispenseInv = (dispensedData) =>
// api.post(`dispenseSave`, dispensedData, options).then((res) => res.data);

// export const updateDispenseInv = ({ id, ...updatedDispenserd }) =>
// api
//   .put(
//     `dispenseUpdate`,
//     { ...body, menukey: "dispense", ...updatedDispenserd },
//     options
//   )
//   .then((res) => {
//     return res.data;
//   });	

// export const getDispenseInv = (id) =>
// api.post(
//   `dispenseListSingle`,
//   { ...body, menukey: "dispense", TransactionId: id,"FacilityId": FacilityId },
//   options
// );
// export const deleteDispenseInv = (id) =>
// api.post(
//   `dispenseDelete`,
//   { ...body, menukey: "dispense", TransactionId: id, "FacilityId": FacilityId, UserName: UserName, lan: lan },
//   options
// ).then((res) => {
//     return res.data;
// });


// export const updateDispenseStock = (id) =>
// api
//   .post(
//     `POSTStockForm`,
//     {
//       ...body,
//       menukey: "dispense",
//       "FacilityId": FacilityId,
//       TransactionId: id,
//       TransactionTypeId: 4,
//     },
//     options
//   )
//   .then((res) => {
//     return res;
//   });
// ///Dispense End


// ///Lab LMIS Report Start
// export const getLabLMISReportTable = (params) =>  {
//   return api.post('LabLMISReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }
// ///Lab LMIS Report End


// ///Lab Quarter Colose Start
// //Fetch Lab Quarter Close Data api
// export const getLabQuarterCloseData = (params) =>  api.post('labquarterclose', {params}, options).then((res) =>{ 
//   console.log("Get data for table api");
// 	return res.data.datalist;
// })

// //Lab Quater Close Api Generate Data
// export const labQuarterCloseByFacility = (params) =>  {
//   console.log("Calling Month close api");
//   api.post(`labquarterclose`,
//   {...body, "action": "dataInsert", "menukey": "month-close", "currYearId":params.currYearId,
//    "currMonthId":params.MonthId, "currFacilityId": params.FacilityId, "StatusId": params.StatusId,
//     "UserId": params.UserId}, options).then((res) =>{return res;})
// }

 

// export const getReceivePickIssueInvoiceList = (params) =>
//   api.post("receive_pick_issue_invoice_list_server", { params }, options).then((res) => {
//   return res.data.issueinvdata;
// });


// export const saveReceivePickIssueInvoice = (id) =>
//   api
//     .post(
//       `receive_pick_issue_invoice_save_server`,
//       { ...body, menukey: "receive","FacilityId": FacilityId, TransactionId: id, UserName: UserName, lan: lan },
//       options
//     )
//     .then((res) => {
//       return res.data.receiveinvdata;
//     });

//   //Receive Issue Invoice Pick End

//  //New Lab Report Api

//  export const getLabStockSummaryChart = (params) =>  {
//   return api.post('LabStockSummary', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// export const getLabStockSummaryTable = (params) =>  {
//   return api.post('LabStockSummary', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }


// export const getConsumptionTrendsForTheTopFiveLabProductsChart = (pYearId, pMonthId, pFacilityId = 0, menukey) => {

// 	return api
// 	  .post(
// 		"ConsumptionTrendsForTheTopFiveLabProducts",
// 		{ ...body, action: "getConsumptionTrendsForTheTopFiveLabProductsChart", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId},
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// export const getConsumptionTrendsForTheTopFiveLabProductsTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"ConsumptionTrendsForTheTopFiveLabProducts",
// 		{ ...body, action: "getConsumptionTrendsForTheTopFiveLabProductsTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };


// export const getLabStockStatusDetailsChart = (params) =>  {
//   return api.post('LabStockStatusDetails', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// export const getLabStockStatusDetailsTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"LabStockStatusDetails",
// 		{ ...body, action: "getLabStockStatusDetailsTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		// return res.data.datalist;
// 		return res.data.datalist.data;
// 	  });
// };

// export const getAMCTrendsforTheTopFiveLabProductsChart = (pYearId, pMonthId, pFacilityId = 0, menukey) => {

//   return api
//       .post(
//         "AMCTrendsforTheTopFiveLabProducts",
//         { ...body, action: "getAMCTrendsforTheTopFiveLabProductsChart", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });
// };

// export const getAMCTrendsforTheTopFiveLabProductsTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"AMCTrendsforTheTopFiveLabProducts",
// 		{ ...body, action: "getAMCTrendsforTheTopFiveLabProductsTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// export const getPercentageOfFacilitiesWithAStockoutOfLabTracerCommoditiesChart = (pYearId, pMonthId, pFacilityId = 0, menukey) => {

//   return api
//       .post(
//         "PercentageOfFacilitiesWithAStockoutOfLabTracerCommodities",
//         { ...body, action: "getPercentageOfFacilitiesWithAStockoutOfLabTracerCommoditiesChart", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });
// };

// export const getPercentageOfFacilitiesWithAStockoutOfLabTracerCommoditiesTable = (pYearId, pMonthId, pFacilityId = 0, menukey) => {
// 	return api
// 	  .post(
// 		"PercentageOfFacilitiesWithAStockoutOfLabTracerCommodities",
// 		{ ...body, action: "getPercentageOfFacilitiesWithAStockoutOfLabTracerCommoditiesTable", menukey: menukey, FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
// 		options
// 	  ).then((res) => {
// 		return res.data.datalist;
// 	  });
// };

// export const getAvailabilityOfLabCommoditiesChart = (params) =>  {
//   return api.post('AvailabilityOfLabCommodities', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// export const getAvailabilityOfLabCommoditiesTable = (params) =>  {
//   return api.post('AvailabilityOfLabCommodities', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }

//Lab Logistics Dashboard Start
// export const getLabLogisticDashboard = (params) =>  {
//   return api.post('LabLogisticsDashboard', {params}, options).then((res) =>{ 
//     return res;
//   })
// }

// export const getConsumptionTrendsTopFiveLABProducts = (pYearId, pMonthId, pFacilityId = 0) => {

//   return api
//       .post(
//         "LabLogisticsDashboard",
//         { ...body, action: "getConsumptionTrendsTopFiveLABProducts", menukey: "lab-logistics-dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });

// };

// export const getAMCTrendsTopFiveLabProducts = (pYearId, pMonthId, pFacilityId = 0) => {

//   return api
//       .post(
//         "LabLogisticsDashboard",
//         { ...body, action: "getAMCTrendsTopFiveLabProducts", menukey: "lab-logistics-dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });

// };

// export const getPercentageOfFacilitiesWithStockoutOfLabTracerCommodities = (pYearId, pMonthId, pFacilityId = 0) => {
//   return api
//       .post(
//         "LabLogisticsDashboard",
//         { ...body, action: "getPercentageOfFacilitiesWithStockoutOfLabTracerCommodities", menukey: "lab-logistics-dashboard", FacilityId: pFacilityId, YearId: pYearId, MonthId: pMonthId  },
//         options
//       )
//       .then((res) => {
//         return res.data.datalist;
//       });

// };
//Lab Logistics Dashboard End
 

 
// export const saveLabTestEntryInv = (dispenserData) =>
//   api.post(`labtestentrySave`, dispenserData, options).then((res) => res.data);

//   export const saveLabTestEntryInv = (dispenserData) =>
//   api
//     .post(
//       `labtestentrySave`,
//       { ...body, UserName: UserName, lan: lan ,  menukey: "entry-of-the-results-of-the-various-assessments-for-the-patients", ...dispenserData },
//       options
//     )
//     .then((res) => res.data);



// export const getLabTestModalList = (id, ItemGroupId) =>
//   api.post("LabTestModal", { ...body, action: "getLabTests", menukey: "entry-of-the-results-of-the-various-assessments-for-the-patients",FacilityId:id, "ItemGroupId":ItemGroupId }, options).then((res) => {
//     return res.data.labtestlist;
//   });
  
///Lab LMIS Report Start
// export const getLaboratoryActivityReportTable = (params) =>  {
//   return api.post('LaboratoryActivityReport', {params}, options).then((res) =>{ 
//     return res.data.datalist;
//   })
// }
///Lab LMIS Report End


// export const getPatientModalList = (id) =>
//   api.post("PatientModal", { ...body, action: "getPatients", menukey: "entry-of-the-results-of-the-various-assessments-for-the-patients",FacilityId:id}, options).then((res) => {
//     return res.data.patientlist;
//   });


//   export const getStockRecortdWithItemGroup = (params) =>
//   api.post("facilityGroupMap", { params }, options).then((res) => {
//     return res.data.stockdatalist;
//   });


// function getProductGroupData(params){
//   // axios.get(baseURL).then((response) => {
//   //   setPost(response.data);
//   // });
//   api.post("productgroup", { params }, options).then((res) => {
//     console.log('output obj: ', res);
//     // return res.data.stockdatalist;
//   });
// }


// export const getProductGroupData = (params) =>
// api.post("productgroup", { params }, options).then((res) => {
//   // return res.data;
//   return [];
//   // return [1,2,3,4];
// });

  




