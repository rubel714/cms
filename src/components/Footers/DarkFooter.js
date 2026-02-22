/*eslint-disable*/
import React from "react";

// reactstrap components
import { Container } from "reactstrap";

function DarkFooter() {
  return (
    <footer className="footer non-printable" data-background-color="black">
      <Container>
        <nav>
          <ul>
              {/* <li>
                <a href="#" target="_blank" className="text-capitalize">
                Benin SVDL
                </a>
              </li> */}
          </ul>
        </nav>
        {/* <div className="copyright" id="copyright">
          © {new Date().getFullYear()},{" "}
          <a
          // href=""
          // target="_blank"
          >
            Supported by Inspection
          </a>
        </div> */}

        <div class="footer">
            <h6>Powred By NextGen Software & Solutions Ltd. &#169; Reserved (v1.0.07)</h6>
        </div>
      </Container>
    </footer>
  );
}

export default DarkFooter;
