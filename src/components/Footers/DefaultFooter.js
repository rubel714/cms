/*eslint-disable*/
import React from "react";
import { Link } from "react-router-dom";

// reactstrap components
import { Container } from "reactstrap";

// core components

function DefaultFooter() {
  return (
    <>
      <footer className="footer footer-default">
        <Container>
          <nav>
            <ul>
              <li>
                <a href="#" target="_blank" className="text-capitalize">
                Credit Management System
                </a>
              </li>
            </ul>
          </nav>
          <div className="copyright" id="copyright">
            © 2025,{" "}
            <a
            // href=""
            // target="_blank"
            >
              NA
            </a>
          </div>
        </Container>
      </footer>
    </>
  );
}

export default DefaultFooter;
