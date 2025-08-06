/* eslint-disable jsx-a11y/accessible-emoji */
// import "./ProductCetegory.css";
import React,{ useState, useMemo } from "react";

export const Pagination = ({
  activePage,
  count,
  rowsPerPage,
  totalPages,
  setActivePage,
  clearAll
}) => {
  const beginning = activePage === 1 ? 1 : rowsPerPage * (activePage - 1) + 1;
  const end = activePage === totalPages ? count : beginning + rowsPerPage - 1;

  return (
    <>
      <div class="pagination">
        <span style={{ float: "left" }}>
          Page {activePage} of {totalPages}
        </span>
        <a disabled={activePage === 1} onClick={() => setActivePage(1)}>
          ||❮
        </a>
        <a
          disabled={activePage === 1}
          onClick={(event) => {
            if (activePage === 1) {
              event.preventDefault();
            } else {
              setActivePage(activePage - 1);
            }
          }}
        >
          ❮
        </a>
        <label>
          {beginning === end ? end : `${beginning} - ${end}`} of {count} rows
        </label>
        <a
          disabled={activePage === totalPages}
          onClick={(event) => {
            if (activePage === totalPages) {
              event.preventDefault();
            } else {
              setActivePage(activePage + 1);
            }
          }}
        >
          ❯
        </a>
        <a
          disabled={activePage === totalPages}
          onClick={() => setActivePage(totalPages)}
        >
          ❯||
        </a>
        {/* <div>
          <p>
            <a onClick={clearAll}>Clear all</a>
          </p>
        </div> */}
      </div>
    </>
  );
};
