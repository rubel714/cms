import React, { useState } from "react";
import { Link } from "react-router-dom";

import {
  Button,
  Card,
  CardHeader,
  CardBody,
  CardFooter,
  CardTitle,
  Form,
  Input,
  InputGroupAddon,
  InputGroupText,
  InputGroup,
  Container,
  Row,
  FormFeedback,
} from "reactstrap";

import swal from "sweetalert";
import * as Service from "../../services/Service.js";

// core components

const regex =
  /^[a-z0-9][a-z0-9-_\.]+@([a-z]|[a-z0-9]?[a-z0-9-]+[a-z0-9])\.[a-z0-9]{2,10}(?:\.[a-z]{2,10})?$/;

function ResetPassword(props) {
  const [passwordShown, setPasswordShown] = useState(false);
  const [confirmPasswordShown, setconfirmPasswordShown] = useState(false);
  const [emailFocus, setEmailFocus] = React.useState(false);
  const [passwordFocus, setPasswordFocus] = React.useState(false);
  const [confirmPasswordFocus, setConfirmPasswordFocus] = React.useState(false);
  const [state, setState] = React.useState({
    email: "",
    password: "",
    confirmPassword: "",
  });

  const handleChange = (event) => {
    setState({ ...state, [event.target.name]: event.target.value });
  };

  const submit = async (e) => {
    e.preventDefault();
    if (
      state.email.length > 0 &&
      state.password.length > 0 &&
      state.confirmPassword.length > 0
    ) {
      if (!regex.test(state.email)) {
        swal("Oops Error!", "Your email is not valid!", "error");
      } else if (state.password.length < 8) {
        swal(
          "Oops Error!",
          "Password length should be 8 characters or more!",
          "error"
        );
      } else if (state.password !== state.confirmPassword) {
        swal("Oops Error!", "Your Password doesn't match!", "error");
      } else {
        swal("Success!", "", "success");
        // const payload = {
        //   email: state.email,
        //   password: state.password,
        // };

        // const response = await Service.default.postApi(
        //   "source/reset_password.php",
        //   payload
        // );
        // if (response.status === 200) {
        //   swal("Success!", `${response.message}`, "success");
        // } else {
        //   swal("Error!", `${response.message}`, "error");
        // }
      }
    } else {
      swal("Oops Error!", "Please fill all required fields", "error");
    }
  };

  const togglePasswordVisiblity = () => {
    setPasswordShown(passwordShown ? false : true);
  };
  const toggleConfirmPasswordVisiblity = () => {
    setconfirmPasswordShown(confirmPasswordShown ? false : true);
  };
  return (
    <>
      <div
        className="section signup-top-padding"
        style={{
          backgroundImage: "url(" + require("assets/img/bg8.jpg") + ")",
          backgroundSize: "cover",
          backgroundPosition: "top center",
          minHeight: "753px",
          marginTop: "80px",
        }}
      >
        <Container>
          <Row>
            <Card className="card-signup" data-background-color="blue">
              <Form action="" className="form" method="">
                <CardHeader className="text-center">
                  <CardTitle className="title-up" tag="h3">
                    Reset Password
                  </CardTitle>
                </CardHeader>
                <CardBody>
                  <InputGroup
                    className={
                      "no-border" + (emailFocus ? " input-group-focus" : "")
                    }
                  >
                    <InputGroupAddon addonType="prepend">
                      <InputGroupText>
                        <i className="now-ui-icons ui-1_email-85"></i>
                      </InputGroupText>
                    </InputGroupAddon>
                    <Input
                      invalid={state.emailValidate}
                      placeholder="*Email..."
                      type="text"
                      name="email"
                      onChange={(evt) => handleChange(evt)}
                      onFocus={() => setEmailFocus(true)}
                      onBlur={() => setEmailFocus(false)}
                    ></Input>
                    {state.emailValidate ? (
                      <FormFeedback invalid>
                        *This is a required field
                      </FormFeedback>
                    ) : null}
                  </InputGroup>

                  <InputGroup
                    className={
                      "no-border" + (passwordFocus ? " input-group-focus" : "")
                    }
                  >
                    <InputGroupAddon addonType="prepend">
                      <InputGroupText>
                        <i className="now-ui-icons ui-1_lock-circle-open"></i>
                      </InputGroupText>
                    </InputGroupAddon>
                    <Input
                      invalid={state.passwordValidate}
                      placeholder="*Password..."
                      name="password"
                      onChange={(evt) => handleChange(evt)}
                      type={passwordShown ? "text" : "password"}
                      onFocus={() => setPasswordFocus(true)}
                      onBlur={() => setPasswordFocus(false)}
                    ></Input>
                    {state.passwordValidate ? (
                      <FormFeedback invalid>
                        *This is a required field
                      </FormFeedback>
                    ) : null}
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

                  <InputGroup
                    className={
                      "no-border" +
                      (confirmPasswordFocus ? " input-group-focus" : "")
                    }
                  >
                    <InputGroupAddon addonType="prepend">
                      <InputGroupText>
                        <i className="now-ui-icons ui-1_lock-circle-open"></i>
                      </InputGroupText>
                    </InputGroupAddon>
                    <Input
                      invalid={state.confirmPasswordValidate}
                      placeholder="*Confirm Password..."
                      name="confirmPassword"
                      onChange={(evt) => handleChange(evt)}
                      type={confirmPasswordShown ? "text" : "password"}
                      onFocus={() => setConfirmPasswordFocus(true)}
                      onBlur={() => setConfirmPasswordFocus(false)}
                    ></Input>
                    {state.confirmPasswordValidate ? (
                      <FormFeedback invalid>
                        *This is a required field
                      </FormFeedback>
                    ) : null}
                    <InputGroupAddon addonType="append">
                      <InputGroupText className="password-eye">
                        <i
                          onClick={toggleConfirmPasswordVisiblity}
                          className="fa fa-eye"
                          aria-hidden="true"
                        ></i>
                      </InputGroupText>
                    </InputGroupAddon>
                  </InputGroup>
                </CardBody>
                <CardFooter className="text-center">
                  <Button
                    className="btn-neutral btn-round"
                    color="info"
                    onClick={(e) => submit(e)}
                    size="lg"
                  >
                    Reset Password
                  </Button>
                </CardFooter>
              </Form>
            </Card>
          </Row>
          <div className="col text-center">
            <Button
              className="btn-round btn-white bg_white"
              color="default"
              to="/login"
              outline
              size="lg"
              tag={Link}
            >
              View Login Page
            </Button>
          </div>
        </Container>
      </div>
    </>
  );
}

export default ResetPassword;
