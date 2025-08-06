import React, { useState } from "react";

export const UserContext =  React.createContext({});

const UserContextProvider = (props) => {
  const [userInfo, setUserInfo] = useState(props.userInfo);

  const replaceFacilityHandler = (facility) => {    

    return {};
  };

  const updateFacilityInitComplete = (facilityInitComplete) => {    

    return {};
  };

  const updateFirstReportGenerateComplete = (facilityFirstReportgenerateComplete) => {    
    // setUserInfo((prevUserInfo) => {
    //   let newUserInfo = {...prevUserInfo};
    //   newUserInfo.facility.isFirstReportGenerated = facilityFirstReportgenerateComplete;
    //   return newUserInfo;
    // });
    return {};
  };

  const updateFacilityInitStartDate = (facilityInitStartDate) => {    
    // setUserInfo((prevUserInfo) => {
    //   let newUserInfo = {...prevUserInfo};
    //   newUserInfo.facility.netSIGL2StartDate = facilityInitStartDate;
    //   return newUserInfo;
    // });
    return {};
  };

  const updateReadySyncCount = (readySyncCount) => {    
    // setUserInfo((prevUserInfo) => {
    //   let newUserInfo = {...prevUserInfo};
    //   newUserInfo.ReadySyncCount = readySyncCount;
    //   return newUserInfo;
    // });

    return {};
  };

  const contextValue = {
    userInfo,
    replaceFacilityHandler: replaceFacilityHandler,
    updateFacilityInitComplete: updateFacilityInitComplete,
    updateFirstReportGenerateComplete: updateFirstReportGenerateComplete,
    updateFacilityInitStartDate: updateFacilityInitStartDate,
    updateReadySyncCount: updateReadySyncCount,
  };

  return (
    <UserContext.Provider value={contextValue}>
      {props.children}
    </UserContext.Provider>
  );
}

export default UserContextProvider;