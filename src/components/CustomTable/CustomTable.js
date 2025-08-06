import React, { useState, useMemo } from "react";
import { sortRows, filterRows, paginateRows } from "./helpers";
import { Pagination } from "./Pagination";

function CustomTable({
  columns,
  rows,
  actioncontrol,
  ispagination = true,
  paginationsize = 15,
  params = {},
}) {
  // const columns2 = [
  //   { field: 'rownumber', label: 'SL' ,align:'center',width:'5%'},
  //   // { field: 'SL', label: 'SL',width:'10%',align:'center',visible:true,sort:false,filter:false },
  //   { field: 'ProductCetegoryName', label: 'Product Cetegory Name',align:'left',visible:true,sort:true,filter:true },
  //   { field: 'ProductGroup', label: 'Product Group',width:'25%',align:'left',visible:true,sort:true,filter:true },
  //   { field: 'Status', label: 'Status',width:'15%',align:'left',visible:false,sort:true,filter:true },
  //   { field: 'custom', label: 'Action',width:'10%',align:'center',visible:true,sort:false,filter:false },
  // ]

  console.log('paginationsize: ', paginationsize);


  const [activePage, setActivePage] = useState(1);
  const [filters, setFilters] = useState({});
  const [sort, setSort] = useState({});
  // const [sort, setSort] = useState({ order: "asc", orderBy: "id" });
  let rowsPerPage = 0;

  if (!ispagination) {
    rowsPerPage = rows.length;
  }else{
    if(paginationsize){
      rowsPerPage = paginationsize;
    }
  }
  // const isPagination = true;

  columns.forEach((row, i) => {
    if (!row.hasOwnProperty("align")) {
      columns[i].align = "left";
    }

    if (!row.hasOwnProperty("sort")) {
      columns[i].sort = false;
    }

    if (!row.hasOwnProperty("width")) {
      columns[i].width = 0;
    }

    if (!row.hasOwnProperty("visible")) {
      columns[i].visible = true;
    }

    if (!row.hasOwnProperty("filter")) {
      columns[i].filter = false;
    }

    if (!row.hasOwnProperty("type")) {
      columns[i].type = 'string';
    }
  });
  // console.log("columns: ", columns);

  const filteredRows = useMemo(
    () => filterRows(rows, filters),
    [rows, filters]
  );
  
  const sortedRows = useMemo(
    () => sortRows(filteredRows, sort),
    [filteredRows, sort]
  );
  // console.log('sortedRows useMemo: ', sortedRows);
  // console.log('sort useMemo: ', sort.length);

  const calculatedRows = paginateRows(sortedRows, activePage, rowsPerPage);

  const count = filteredRows.length;
  const totalPages = Math.ceil(count / rowsPerPage);

  const handleSearch = (value, field) => {
    setActivePage(1);

    if (value) {
      setFilters((prevFilters) => ({
        ...prevFilters,
        [field]: value,
      }));
    } else {
      setFilters((prevFilters) => {
        const updatedFilters = { ...prevFilters };
        delete updatedFilters[field];

        return updatedFilters;
      });
    }
  };

  const handleSort = (field) => {
    setActivePage(1);
    setSort((prevSort) => ({
      order:
        prevSort.order === "asc" && prevSort.orderBy === field ? "desc" : "asc",
      orderBy: field,
    }));
  };

  const clearAll = () => {
    setSort({ order: "asc", orderBy: "id" });
    setActivePage(1);
    setFilters({});
  };

  return (
    <>
      {/* <div className="bodyContainer"> */}
      {/* <div className="topHeader">
        <h4>
          <a href="#">Home</a> ❯ Settings ❯ Product Cetegory
        </h4>
      </div> */}

      <div class="">
        <table id="salesitemtbl" class="tableGlobal">
          <thead>
            <tr>
              {columns.map((column) => {
                const sortIcon = () => {
                  if (column.field === sort.orderBy) {
                    if (sort.order === "asc") {
                      return "⬆️";
                    }
                    return "⬇️";
                  } else {
                    return "️↕️";
                  }
                };

                // { field: 'ProductCetegoryName', label: 'Product Cetegory Name',width:'30%',align:'center',visible:true,sort:true,filter:true },

                return (
                  <>
                    {column.visible && (
                      <th
                        key={column.field}
                        style={{ textAlign: column.align, width: column.width }}
                      >
                        <span>{column.label}</span>

                        {column.sort && (
                          <button
                            class="btn-table-sort"
                            onClick={() => handleSort(column.field)}
                          >
                            {sortIcon()}
                          </button>
                        )}
                      </th>
                    )}
                  </>
                );
              })}
            </tr>
            <tr>
              {columns.map((column) => {
                return (
                  <>
                    {column.visible && (
                      <th>
                        {column.filter && (
                          <input
                            key={`${column.field}-search`}
                            type="search"
                            placeholder={`Search ${column.label}`}
                            value={filters[column.field]}
                            onChange={(event) =>
                              handleSearch(event.target.value, column.field)
                            }
                          />
                        )}
                      </th>
                    )}
                  </>
                );
              })}
            </tr>
          </thead>
          <tbody>
            {calculatedRows.length > 0 &&
              calculatedRows.map((row, rowsl) => {
                return (
                  <tr key={row.id}>
                    {columns.map((column, i) => {
                      if (columns[i].visible) {
                        // if (column.format) {
                        //   return (
                        //     <td key={column.field} style={{ textAlign : columns[i].align }}>
                        //       {column.format(column.field==="rownumber"?(rowsl+1):row[column.field])}
                        //     </td>
                        //   );
                        // }

                        if (column.field === "custom") {
                          // formatter
                          // return (
                          //   <td key={column.field} style={{ textAlign:columns[i].align }}>{ columns[i].formatter }</td>
                          // );

                          return (
                            <td
                              key={column.field}
                              style={{ textAlign: columns[i].align }}
                            >
                              {actioncontrol(row)}
                            </td>
                          );
                        }

                        return (
                          <td
                            key={column.field}
                            style={{ textAlign: columns[i].align }}
                          >
                            {column.field === "rownumber"
                              ? (activePage - 1) * rowsPerPage + (rowsl + 1)
                              : (column.type==="number"?new Intl.NumberFormat('hi-IN').format(row[column.field]):row[column.field])
                              //new Intl.NumberFormat('hi-IN').format(56325320)
                              //row[column.field]
                              // new Intl.NumberFormat('hi-IN', { style: 'currency', currency: 'INR' }).format(3636)
                              }
                          </td>
                        );
                      }
                    })}
                  </tr>
                );
              })}
          </tbody>
        </table>
        {/* isPagination */}
        {ispagination && (
          <>
            {count > 0 ? (
              <Pagination
                activePage={activePage}
                count={count}
                rowsPerPage={rowsPerPage}
                totalPages={totalPages}
                setActivePage={setActivePage}
                clearAll={clearAll}
              />
            ) : (
              <p>No data found</p>
            )}
          </>
        )}
      </div>
      {/* </div> */}
    </>
  );
}

export default CustomTable;
