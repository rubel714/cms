import React, { useContext } from "react";
import { apiCall, apiOption, LoginUserInfo, language } from "../../actions/api";

function AfterLoginNavbar(props) {
  const [userInfo, setUserInfo] = React.useState(LoginUserInfo());

  const baseUrl = process.env.REACT_APP_FRONT_URL;
  // const [previewImage, setPreviewImage] = useState(
  //   `${baseUrl}image/user/placeholder.png`
  // );

  function menuShowPermision(pMenuKey) {
    let isShow = 0;

    // console.log(LoginUserInfo().UserAllowdMenuList);
    let menuList = LoginUserInfo().UserAllowdMenuList;
    // console.log("menuList: ", menuList);

    menuList.forEach((menu, i) => {
      // console.log('menu: ', menu.MenuKey);
      if (menu.MenuKey === pMenuKey) {
        isShow = 1;
        return;
      }
    });

    return isShow;
  }

  return (
    <>
      {/* <!-- NAV BAR --> */}
      <nav class="navbar non-printable">
        {/* <!-- LOGO --> */}
        <div class="logo">
          <img alt="..." src={require("assets/img/logo.png")}></img>
          <label>CMS</label>
        </div>

        {/* <!-- MENUE LIST  BAR --> */}
        <div class="menuBar">
          <div class="menuListBar">
            <ul>
              <li class="dropdownMenu">
                {" "}
                <a
                  class="parentMenu"
                  href="javascript:void(0)"
                  onClick={() => props.history.push("/home")}
                >
                  Home
                </a>
              </li>

              {/* {menuShowPermision("dashboard") === 1 && (
                <li class="dropdownMenu">
                  {" "}
                  <a
                    class="parentMenu"
                    href="javascript:void(0)"
                    onClick={() => props.history.push("dashboard")}
                  >
                    Dashboard
                  </a>
                </li>
              )} */}

              {/* {menuShowPermision("dashboardstaff") === 1 && (
                <li class="dropdownMenu">
                  {" "}
                  <a
                    class="parentMenu"
                    href="javascript:void(0)"
                    onClick={() => props.history.push("dashboardstaff")}
                  >
                    Staff Dashboard
                  </a>
                </li>
              )} */}

              {menuShowPermision("settings") === 1 && (
                <li class="dropdownMenu">
                  {" "}
                  Basic Setup
                  <ul class="dropdownList">
                    {/* {menuShowPermision("team") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("team")}
                        >
                          Team
                        </a>
                      </li>
                    )} */}

                    {/* {menuShowPermision("checklist") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("checklist")}
                        >
                          CheckList
                        </a>
                      </li>
                    )} */}

                    {menuShowPermision("designation") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("designation")}
                        >
                          Designation
                        </a>
                      </li>
                    )}
                    {menuShowPermision("department") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("department")}
                        >
                          Department
                        </a>
                      </li>
                    )}

                    {menuShowPermision("businessline") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("businessline")}
                        >
                          Business Line
                        </a>
                      </li>
                    )}

                    {menuShowPermision("customergroup") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("customergroup")}
                        >
                          Customer Group
                        </a>
                      </li>
                    )}

                    {menuShowPermision("client") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("client")}
                        >
                          Client
                        </a>
                      </li>
                    )}

                    {menuShowPermision("userrole") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("userrole")}
                        >
                          User Role
                        </a>
                      </li>
                    )}

                    {menuShowPermision("userentry") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("userentry")}
                        >
                          User Entry
                        </a>
                      </li>
                    )}

                    {menuShowPermision("roletomenupermission") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() =>
                            props.history.push("roletomenupermission")
                          }
                        >
                          Role to Menu Permission
                        </a>
                      </li>
                    )}

                    {/* {menuShowPermision("client") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("client")}
                        >
                          Client Entry
                        </a>
                      </li>
                    )} */}
                    {/* 
                    {menuShowPermision("branch") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("branch")}
                        >
                          Branch Entry
                        </a>
                      </li>
                    )} */}

                    {menuShowPermision("auditlog") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("auditlog")}
                        >
                          Audit Log
                        </a>
                      </li>
                    )}

                    {menuShowPermision("errorlog") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("errorlog")}
                        >
                          Error Log
                        </a>
                      </li>
                    )}
                  </ul>
                </li>
              )}

              {menuShowPermision("invoice") === 1 && (
                <li class="dropdownMenu">
                  {" "}
                  Invoice
                  <ul class="dropdownList">

                     {menuShowPermision("invoiceupload") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("invoiceupload")}
                        >
                          Invoice Upload
                        </a>
                      </li>
                    )}

                    {menuShowPermision("invoicelist") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("invoicelist")}
                        >
                          Invoice List
                        </a>
                      </li>
                    )}

                   
                    {menuShowPermision("paymentreceive") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("paymentreceive")}
                        >
                          Payment Receive
                        </a>
                      </li>
                    )}


                    {/* {menuShowPermision("machine") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("machine")}
                        >
                          Machine
                        </a>
                      </li>
                    )}
                    {menuShowPermision("machineparts") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("machineparts")}
                        >
                          Machine Parts
                        </a>
                      </li>
                    )}

                    {menuShowPermision("machinemodel") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("machinemodel")}
                        >
                          Machine Model
                        </a>
                      </li>
                    )}

                    {menuShowPermision("machineserial") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("machineserial")}
                        >
                          Machine Serial
                        </a>
                      </li>
                    )} */}
                  </ul>
                </li>
              )}

              {/* {menuShowPermision("mytask") === 1 && (
                <li class="dropdownMenu">
                  {" "}
                  My Task
                  <ul class="dropdownList">
                    {menuShowPermision("feedback") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() =>
                            props.history.push("feedback")
                          }
                        >
                          Feedback
                        </a>
                      </li>
                    )}
      
                  </ul>
                </li>
              )} */}

              {menuShowPermision("reports") === 1 && (
                <li class="dropdownMenu">
                  {" "}
                  Reports
                  <ul class="dropdownList">
                    {/* 
                    {menuShowPermision("customervisitpunchledger") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() =>
                            props.history.push("customervisitpunchledger")
                          }
                        >
                          Report 01
                        </a>
                      </li>
                    )} */}
                    {/* 
                    {menuShowPermision("customervisitpunchsummary") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() =>
                            props.history.push("customervisitpunchsummary")
                          }
                        >
                          Visit Punch Summary
                        </a>
                      </li>
                    )}

                    {menuShowPermision("visitplan") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("visitplan")}
                        >
                          Visit Plan
                        </a>
                      </li>
                    )}

                    {menuShowPermision("conveyancereport") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() => props.history.push("conveyancereport")}
                        >
                          Conveyance Report
                        </a>
                      </li>
                    )} */}

                    {/*           
                    {menuShowPermision("visitsummaryreport") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() =>
                            props.history.push("visitsummaryreport")
                          }
                        >
                          Visit Summary Report
                        </a>
                      </li>
                    )}

                    {menuShowPermision("machineryservicereport") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() =>
                            props.history.push("machineryservicereport")
                          }
                        >
                          Machinery Service Report
                        </a>
                      </li>
                    )}

                    {menuShowPermision("machineryinstallationreport") === 1 && (
                      <li>
                        <a
                          href="javascript:void(0)"
                          onClick={() =>
                            props.history.push("machineryinstallationreport")
                          }
                        >
                          Machinery Installation Report
                        </a>
                      </li>
                    )} */}
                  </ul>
                </li>
              )}
            </ul>
          </div>

          {/* <!-- ICON BAR --> */}
        </div>

        {/* <!-- USER PANEL --> */}
        <div class="userPanel">
          {/* <div>
            <label>{userInfo.ClientName}</label>
          </div>
          <div>
            <label>{userInfo.BranchName}</label>
          </div> */}

          <div class="user">
            {/* <img src="./img/user.jpg" alt=""/> */}
            <img
              // src={require("../image/user/" + userInfo.PhotoUrl)}
              src={baseUrl + "image/user/" + userInfo.PhotoUrl}
              alt="User"
            />
            <ul>
              <li class="dropdownMenu">
                {" "}
                {userInfo.UserName} &nabla;
                <ul class="dropdownList">
                  {/* <li>
                    <a href="#">My Profile</a>
                  </li> */}

                  <li>
                    <a
                      href="javascript:void(0)"
                      onClick={() => props.history.push("myprofileweb")}
                    >
                      My Profile
                    </a>
                  </li>

                  {/* <li>
                    <a href="#">Change Password</a>
                  </li> */}
                  {/* <li><a href="./index.html">Log Out</a></li> */}
                  <li>
                    <a
                      href="javascript:void(0)"
                      onClick={(e) => {
                        e.preventDefault();
                        sessionStorage.clear();
                        setTimeout(() => {
                          props.history.push("/login");
                        }, 1000);
                      }}
                    >
                      {"Logout"}
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      {/* {collapseOpen ? (
        <div
          id="bodyClick"
          onClick={() => {
            document.documentElement.classList.toggle("nav-open");
            setCollapseOpen(false);
          }}
        />
      ) : null} */}

      {/*============================*/}

      {/* <div id="sticky-header" className={"header-menu " + navbarColor}>
        <div className="container-fluid">
          <div className="row">

            <div className="col-lg-8">
              <div className="tp-mega-full">
                <div className="tp-menu align-self-center">
                  <nav className="desktop_menu">
                    <ul>

                      {menu.map((row, i) => {
                        return (
                          <li>
                            <a href="javascript:void(0)" onClick={() => props.history.push(row.url)} >
                              {DispensingLanguage[lan][menukey][row.title]}
                              {row.submenu.length > 0 ? (
                                <span class="tp-angle pull-right">
                                  <i class="fa fa-angle-down"></i>
                                </span>
                              ) : (
                                <></>
                              )}
                            </a>

                            {row.submenu.length > 0 ? (
                              <ul className={"submenu " + row.position}>
                                {row.submenu.map((row1, i1) => {
                                  return (
                                    <li>
                                      <a href="javascript:void(0)" onClick={() => props.history.push(row1.url) } >
                                        {DispensingLanguage[lan][menukey][row1.title]}
                                        {row1.submenu.length > 0 ? (<span class="tp-angle pull-right"><i class="fa fa-angle-right"></i></span>) : (<></>)}
                                      </a>

                                      {row1.submenu.length > 0 ? (
                                        <ul className={"submenu " + row1.position}>
                                          {row1.submenu.map((row2, i2) => {
                                            return (
                                              <li>
                                                <a href="javascript:void(0)" onClick={() => props.history.push(row2.url) } >
                                                  {DispensingLanguage[lan][menukey][row2.title]}
                                                </a>
                                              </li>
                                            );
                                          })}
                                        </ul>
                                      ) : (
                                        <></>
                                      )}
                                    </li>
                                  );
                                })}
                              </ul>
                            ) : (
                              <></>
                            )}
                          </li>
                        );
                      })}
                      

                      <li>
                        <a href="#">{ DispensingLanguage[lan][menukey]['Profile'] }
                          <span class="tp-angle pull-right">
                            <i class="fa fa-angle-down"></i>
                          </span>
                        </a>
                        <ul className="submenu left_position">

                          <li>
                              <Link href="javascript:void(0)" onClick={(e) => {
                              e.preventDefault();
                              props.history.push("/my-profile");
                              }}>{DispensingLanguage[lan][menukey]["My Profile"]}</Link>
                          </li>

                          <li>
                            <a
                              href="javascript:void(0)"
                              onClick={(e) => {
                                e.preventDefault();
                                sessionStorage.clear();
                                setTimeout(() => {props.history.push("/login");}, 1000);
                              }}
                            >
                                {DispensingLanguage[lan][menukey]['Logout']}
                            </a>
                          </li>
                        </ul>
                      </li>

                    </ul>




                  </nav>


                </div>
              </div>
            </div>

            <div className="col-lg-4">
              <div class="logo">
                <div className="logoFormate">
                    <div className="logo_item">
                      <a href="#">eDISP</a> 
                        <span class="sw_sub_title">
                        {" "}
                        {localStorage.getItem("FacilityName")}
                      </span>
                    </div>

                    <div className="imge_section">
                        <ImageList sx={{ width: 45, height: 50}} cols={1} rowHeight="auto" gap={2}>
                          {itemData.map((item) => (
                            <ImageListItem key={item.img}>
                              <img
                                src={`${item.img}?w=50&h=55&fit=crop&auto=format`}
                                alt={item.title}
                                loading="logo"
                              />
                            </ImageListItem>
                          ))}
                        </ImageList>
                    </div> 
                </div>
              </div>
              <button
                onClick={() => onMobileMenuOpen()}
                className="mobile_menu_bars_icon"
                type="button"
              >
                <i class="fas fa-bars"></i>
              </button>
            </div>

          </div>
        </div>
      </div> */}
    </>
  );
}

export default AfterLoginNavbar;
