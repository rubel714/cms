import React, { useState, useContext } from "react";
import { Button } from "../../components/CustomControl/Button";

import BeforeLoginNavbar from "../../components/Navbars/BeforeLoginNavbar.js";

import DarkFooter from "../../components/Footers/DarkFooter.js";

// services & auth
import * as Service from "../../services/Service.js";

//Import Preloader
import LoadingSpinnerOpaque from "../../services/LoadingSpinnerOpaque";
import swal from "sweetalert";

import {
  Typography,
  TextField,
} from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";

// const regex =
//   /^[a-z0-9][a-z0-9-_\.]+@([a-z]|[a-z0-9]?[a-z0-9-]+[a-z0-9])\.[a-z0-9]{2,10}(?:\.[a-z]{2,10})?$/;

function LoginPage(props) {
  // const userCtx = useContext(UserContext);
  let token = sessionStorage.getItem("token");

  if (token) {
    //if already login then redirect to home page
    props.history.push("/home");
  }

  const [state, setState] = React.useState({
    service: Service,
    ClientId: "",
    BranchId: "",
    email: "",
    password: "",
  });

  const [isLoading, setLoading] = useState(false);
  const [clientList, setClientList] = useState(null);
  const [branchList, setBranchList] = useState(null);
  const [bFirst, setBFirst] = useState(true);
  const [passwordShown, setPasswordShown] = useState(false);

  React.useEffect(() => {
    document.body.classList.add("login-page");
    document.body.classList.add("sidebar-collapse");
    document.documentElement.classList.remove("nav-open");
    window.scrollTo(0, 0);
    document.body.scrollTop = 0;
    return function cleanup() {
      document.body.classList.remove("login-page");
      document.body.classList.remove("sidebar-collapse");
    };
  }, []);

  const handleChange = (e) => {
    const { name, value } = e.target;
    let data = { ...state };
    data[name] = value;
    console.log("data: ", data);

    setState(data);
  };

  const togglePasswordVisibility = () => {
    setPasswordShown((prev) => !prev);
  };

  // const handleChangeChoosenMaster = (name, value) => {
  //   let data = { ...state };
  //   data[name] = value;
  //   console.log("data: ", data);

  //   if (name === "ClientId") {
  //     getBranchList(value);
  //   }

  //   setState(data);
  // };

  const LoginPage = (data) => {
    if (state.email.length > 0 && state.password.length > 0) {
      //if (!regex.test(state.email)) {
      // swal("Oops Error!", "Your email is not valid!", "error");
      //} else {

      setLoading(true);

      const body = {
        ClientId: state.ClientId,
        BranchId: state.BranchId,
        email: state.email,
        password: state.password,
      };

      state.service.default
        .postApi("source/login.php", body)
        .then((res) => {
          if (res.success == 1) {
            sessionStorage.setItem("token", res.token);
            sessionStorage.setItem("User_info", JSON.stringify(res.user_data));

            const auth_token = sessionStorage.getItem("token")
              ? sessionStorage.getItem("token")
              : null;

            let options = {
              headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${auth_token}`,
              },
            };

            setLoading(false);

            window.location.href =process.env.REACT_APP_BASE_NAME + "/";
            // window.location.href = process.env.REACT_APP_BASE_NAME + `/check-permission`;
          } else if (res.success == 0) {
            setLoading(false);
            swal("Error!", `${res.message}`, "error");
          }
        })
        .catch((err) => {
          setLoading(false);
          swal("Error!", `${err}`, "error");
        });
      //}
    } else {
      swal("Oops Error!", "Please fill all required fields", "error");
    }
  };

  // function getClientList() {
  //   state.service.default
  //     .postApi("source/combo_script.php?action=ClientList")
  //     .then((res) => {
  //       setClientList([{ id: "", name: "Select client" }].concat(res));
  //     })
  //     .catch((err) => {});
  // }

  // function getBranchList(ClientId) {
  //   state.service.default
  //     .postApi("source/combo_script.php?action=BranchList&ClientId=" + ClientId)
  //     .then((res) => {
  //       setBranchList([{ id: "", name: "Select branch" }].concat(res));
  //     })
  //     .catch((err) => {});
  // }

  if (bFirst) {
    /**First time call for datalist */
    // getClientList();
    setBFirst(false);
  }

  return (
    <>
      {isLoading && <LoadingSpinnerOpaque />}

      <BeforeLoginNavbar {...props} />

      <div class="LoginBody">
        <div class="loginContainer">
          <div class="loginHeader">
            <h3>User Login</h3>
          </div>
          <div class="userLogin">
            <label>User Name</label>
            <input
              type="text"
              id="email"
              name="email"
              onChange={(e) => handleChange(e)}
            />
            <label>Password</label>
            <div className="password-field">
              <input
                type={passwordShown ? "text" : "password"}
                id="password"
                name="password"
                onChange={(e) => handleChange(e)}
              />
              <button
                type="button"
                className="password-toggle"
                aria-label={passwordShown ? "Hide password" : "Show password"}
                onClick={togglePasswordVisibility}
              >
                <i className={passwordShown ? "fa fa-eye-slash" : "fa fa-eye"} />
              </button>
            </div>
          </div>

          <Button label={"Login"} class={"btnLogin"} onClick={LoginPage} />

          <span>
            * If you forget your password, contact your IT Administrator
          </span>
        </div>
      </div>

      <DarkFooter {...props} />

      {/* <div className="page-header sw_relative_login">
        {isLoading && <LoadingSpinnerOpaque />}
        <div
          className="page-header-image"
          style={{
            backgroundImage: "url(" + require("assets/img/bg8.jpg") + ")",
          }}
        ></div>
        <div className="content">
          <Container>
            <Col className="ml-auto mr-auto" md="4">
              <Card className="card-login card-plain">
                <Form action="" className="form" method="">
                  <CardHeader className="text-center">
                    <div className="logo-container"></div>
                  </CardHeader>
                  <CardBody>
                    <InputGroup
                      className={
                        "no-border input-lg" +
                        (firstFocus ? " input-group-focus" : "")
                      }
                    >
                      <InputGroupAddon addonType="prepend">
                        <InputGroupText className="sw_login_padd">
                          <i className="now-ui-icons users_circle-08"></i>
                        </InputGroupText>
                      </InputGroupAddon>
                      <Input
                        className="placeholder_color"
                        placeholder="Username..."
                        type="text"
                        name="email"
                        onChange={(e) => handleChange(e)}
                        valid={false}
                        invalid={false}
                        onFocus={() => setFirstFocus(true)}
                        onBlur={() => setFirstFocus(false)}
                      ></Input>
                    </InputGroup>
                    <InputGroup
                      className={
                        "no-border input-lg" +
                        (lastFocus ? " input-group-focus" : "")
                      }
                    >
                      <InputGroupAddon addonType="prepend">
                        <InputGroupText className="sw_login_padd">
                          <i className="now-ui-icons ui-1_lock-circle-open"></i>
                        </InputGroupText>
                      </InputGroupAddon>
                      <Input
                        className="placeholder_color"
                        placeholder="Password..."
                        name="password"
                        onChange={(e) => handleChange(e)}
                        type={passwordShown ? "text" : "password"}
                        onFocus={() => setLastFocus(true)}
                        onBlur={() => setLastFocus(false)}
                      ></Input>
                      <InputGroupAddon addonType="append">
                        <InputGroupText className="password-eye">
                          <i
                            onClick={togglePasswordVisiblity}
                            className="fa fa-eye"
                            aria-hidden="true"
                          ></i>
                        </InputGroupText>
                      </InputGroupAddon>
                    </InputGroup>
                  </CardBody>
                  <CardFooter className="text-center">
                    <Button
                      block
                      className="btn-round"
                      color="info"
                      onClick={(e) => {
                        e.preventDefault();
                        LoginPage();
                      }}
                      size="lg"
                    >
                      LOGIN
                    </Button>
                    <div className="pull-left">
                      <h6>
                        <a
                          className="link"
                          onClick={(e) => {
                            e.preventDefault();
                            props.history.push("/signup");
                          }}
                        >
                          Create Account
                        </a>
                      </h6>
                    </div>
                    <div className="pull-right">
                      <h6>
                        <a
                          className="link"
                          href="javascript:void(0)"
                          onClick={() => toggle()}
                        >
                          Forget Password?
                        </a>
                      </h6>
                    </div>
                  </CardFooter>
                </Form>
              </Card>
            </Col>
          </Container>
        </div>
        <TransparentFooter />
      </div> */}

      {/* <Modal isOpen={modal} toggle={toggle} className={className} centered>
        <ModalHeader toggle={toggle}>Forgot Password Link</ModalHeader>
        <ModalBody className="text-center">
          <a className="" href={"./reset-password"}>
            This is the Link to Change Password
          </a>
        </ModalBody>
        <ModalFooter className="reset-modal-btn">
          <Button
            color="primary"
            onClick={(e) => {
              e.preventDefault(e);
              props.history.push("./reset-password");
            }}
          >
            Reset Password
          </Button> 
        </ModalFooter>
      </Modal> */}
    </>
  );
}

export default LoginPage;
