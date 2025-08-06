import React, { useContext } from "react";
import { apiCall, apiOption, LoginUserInfo, language } from "../../actions/api";

function BeforeLoginNavbar(props) {
  return (
    <>
      {/* <!-- NAV BAR --> */}
      <nav class="navbar">
        {/* <!-- LOGO --> */}
        <div class="logo">
          <img alt="..." src={require("assets/img/logo.png")}></img>
        </div>
        {/* <span class="demotext">This is DEMO site</span> */}

        {/* <!-- USER PANEL --> */}
        <div class="userPanel">
          {/* <div><label>{userInfo.ClientName}</label></div>
        <div><label>{userInfo.BranchName}</label></div> */}

          <a
            id="login"
            class="btnLogin"
            onClick={(e) => {
              e.preventDefault();
              props.history.push("/login");
            }}
          >
            <p>Login</p>
          </a>
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

export default BeforeLoginNavbar;
