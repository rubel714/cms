import React, { useEffect, useState } from "react";
import { Button } from "../../../components/CustomControl/Button";
import {
  apiCall,
  apiOption,
  LoginUserInfo,
  language,
} from "../../../actions/api";
import Autocomplete from "@material-ui/lab/Autocomplete";
import { Typography, TextField } from "@material-ui/core";
import CustomTable from "components/CustomTable/CustomTable";

const PaymentInvoiceAddModal = (props) => {
  const serverpage = "paymentreceive"; // this is .php server page

  const [currentRow, setCurrentRow] = useState({});
  const [invoiceList, setInvoiceList] = useState([]);
  const [selectedInvoices, setSelectedInvoices] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const UserInfo = LoginUserInfo();

  // Filter states
  const [invoiceStartDate, setInvoiceStartDate] = useState("");
  const [invoiceEndDate, setInvoiceEndDate] = useState("");
  const [billNumber, setBillNumber] = useState("");

  const [BuyerList, setBuyerList] = useState(null);
  const [currBuyerId, setCurrBuyerId] = useState("");

  const [MerchantList, setMerchantList] = useState(null);
  const [currMerchantId, setCurrMerchantId] = useState("");

  useEffect(() => {
    setCurrentRow(props.currentRow);
    // getBuyerList();
    // getMerchantList();
  }, []);

  function getBuyerList() {
    let params = {
      action: "BuyerList",
      lan: language(),
      UserId: UserInfo.UserId,
      CustomerId: props.currentRow.CustomerId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setBuyerList(
        [{ id: "", name: "Select Buyer" }].concat(res.data.datalist),
      );
    });
  }

  function getMerchantList() {
    let params = {
      action: "MerchantList",
      lan: language(),
      UserId: UserInfo.UserId,
      CustomerId: props.currentRow.CustomerId,
    };

    apiCall.post("combo_generic", { params }, apiOption()).then((res) => {
      setMerchantList(
        [{ id: "", name: "Select Merchant" }].concat(res.data.datalist),
      );
    });
  }

  function searchInvoices() {
    if (!props.currentRow.CustomerId) {
      props.masterProps.openNoticeModal({
        isOpen: true,
        msg: "Customer is required to search invoices",
        msgtype: 0,
      });
      return;
    }

    setIsLoading(true);
    let params = {
      action: "getUnpaidInvoices",
      lan: language(),
      UserId: UserInfo.UserId,
      CustomerId: props.currentRow.CustomerId,
      BuyerId: currBuyerId,
      MerchantId: currMerchantId,
      InvoiceStartDate: invoiceStartDate,
      InvoiceEndDate: invoiceEndDate,
      BillNumber: billNumber,
      PaymentId: props.currentRow.id,
    };

    apiCall
      .post(serverpage, { params }, apiOption())
      .then((res) => {
        setIsLoading(false);
        const list = res.data.datalist
          ? res.data.datalist.map((item) => ({
              ...item,
              IsSelected: false,
            }))
          : [];
        setInvoiceList(list);
      })
      .catch((err) => {
        setIsLoading(false);
        console.error("Error fetching invoices:", err);
      });
  }

  const getRowKey = (item) =>
    item.InvoiceItemId ?? item.TransactionId ?? item.id;

  const handleChangeCheck = (e, row) => {
    const checked = e.target.checked;
    const rowKey = getRowKey(row);

    const updatedItems = invoiceList.map((item) => {
      const itemKey = getRowKey(item);
      if (itemKey === rowKey) {
        return { ...item, IsSelected: checked };
      }
      return item;
    });

    setInvoiceList(updatedItems);

    if (checked) {
      setSelectedInvoices((prev) => [...prev, row]);
    } else {
      setSelectedInvoices((prev) =>
        prev.filter((item) => getRowKey(item) !== rowKey),
      );
    }
  };

  const handleSelectAll = (e) => {
    const checked = e.target.checked;
    const updatedItems = invoiceList.map((item) => ({
      ...item,
      IsSelected: checked,
    }));
    setInvoiceList(updatedItems);

    if (checked) {
      setSelectedInvoices([...updatedItems]);
    } else {
      setSelectedInvoices([]);
    }
  };

  function addInvoicesToPayment() {
    const selected = invoiceList.filter((item) => item.IsSelected);

    if (selected.length === 0) {
      props.masterProps.openNoticeModal({
        isOpen: true,
        msg: "Please select at least one invoice",
        msgtype: 0,
      });
      return;
    }

    let params = {
      action: "addInvoicesToPayment",
      lan: language(),
      UserId: UserInfo.UserId,
      PaymentId: props.currentRow.id,
      invoices: selected,
    };

    apiCall.post(serverpage, { params }, apiOption()).then((res) => {
      props.masterProps.openNoticeModal({
        isOpen: true,
        msg: res.data.message,
        msgtype: res.data.success,
      });

      if (res.data.success === 1) {
        props.modalCallback("addedit");
      }
    });
  }

  function modalClose() {
    props.modalCallback("close");
  }

  const columnList = [
    {
      field: "custom",
      label: (
        <input
          type="checkbox"
          checked={
            invoiceList.length > 0 &&
            invoiceList.every((item) => item.IsSelected)
          }
          onChange={handleSelectAll}
          style={{ width: "18px", height: "18px" }}
        />
      ),
      width: "3%",
      align: "center",
      visible: true,
      sort: false,
      filter: false,
    },
    { field: "rownumber", label: "SL", align: "center", width: "3%" },
    {
      field: "AccountingPeriod",
      label: "Acc. Period",
      width: "10%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "TransactionDate",
      label: "Invoice Date",
      width: "12%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "Description",
      label: "Description",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },
    {
      field: "TransactionReference",
      label: "Invoice Number",
      width: "15%",
      align: "left",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "TransactionAmount",
      label: "Amount in FC",
      width: "7%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
      type: "number",
    },

    {
      field: "ExchangeRate",
      label: "Ex. Rate",
      width: "7%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
    },

    {
      field: "BaseAmount",
      label: "Amount in BDT",
      width: "8%",
      align: "right",
      visible: true,
      sort: true,
      filter: true,
      type: "number",
    },
    // {
    //   field: "BaseAmountWithoutVat",
    //   label: "Amount (BDT)",
    //   align: "right",
    //   width: "8%",
    //   visible: true,
    //   sort: false,
    //   filter: true,
    // },
    // {
    //   field: "VatAmount",
    //   label: "VAT (BDT)",
    //   align: "right",
    //   width: "5%",
    //   visible: true,
    //   sort: false,
    //   filter: true,
    // },
    // {
    //   field: "TotalPaymentAmount",
    //   label: "Paid Amount",
    //   width: "7%",
    //   align: "right",
    //   visible: true,
    //   sort: true,
    //   filter: true,
    // },
    // {
    //   field: "DueAmount",
    //   label: "Due Amount",
    //   width: "7%",
    //   align: "right",
    //   visible: true,
    //   sort: true,
    //   filter: true,
    // },
  ];

  function actioncontrol(rowData) {
    return (
      <div
        style={{
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
        }}
      >
        <input
          id="IsSelected"
          name="IsSelected"
          type="checkbox"
          style={{ width: "18px", height: "18px" }}
          checked={
            rowData.IsSelected === true ||
            rowData.IsSelected === 1 ||
            rowData.IsSelected === "1"
          }
          onChange={(e) => handleChangeCheck(e, rowData)}
        />
      </div>
    );
  }

  const selectedBuyer = React.useMemo(() => {
    if (!BuyerList || !currBuyerId) return null;
    return BuyerList.find((list) => list.id === currBuyerId) || null;
  }, [BuyerList, currBuyerId]);

  const selectedMerchant = React.useMemo(() => {
    if (!MerchantList || !currMerchantId) return null;
    return MerchantList.find((list) => list.id === currMerchantId) || null;
  }, [MerchantList, currMerchantId]);

  return (
    <>
      <div id="invoiceModal" className="modal">
        <div
          className="modal-content"
          style={{ width: "90%", maxWidth: "1200px" }}
        >
          <div className="modalHeader">
            <h4>Add Invoice to Payment</h4>
          </div>

          <div className="contactmodalBody pt-10">
            <label>Invoice Start Date</label>
            <input
              type="date"
              id="InvoiceStartDate"
              name="InvoiceStartDate"
              placeholder="Start Date"
              value={invoiceStartDate}
              onChange={(e) => setInvoiceStartDate(e.target.value)}
            />

            <label>Invoice End Date</label>
            <input
              type="date"
              id="InvoiceEndDate"
              name="InvoiceEndDate"
              placeholder="End Date"
              value={invoiceEndDate}
              onChange={(e) => setInvoiceEndDate(e.target.value)}
            />

            
            <label>Bill Ref. No</label>
            <input
              type="text"
              id="BillNumber"
              name="BillNumber"
              placeholder="Bill Ref. No"
              value={billNumber}
              onChange={(e) => setBillNumber(e.target.value)}
            />
          </div>

          {/* <div className="contactmodalBody pt-10">
            <label>Buyer</label>
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="BuyerId"
              name="BuyerId"
              autoComplete
              options={BuyerList ? BuyerList : []}
              getOptionLabel={(option) => option.name}
              value={selectedBuyer}
              onChange={(event, valueobj) =>
                setCurrBuyerId(valueobj ? valueobj.id : "")
              }
              renderOption={(option) => (
                <Typography className="chosen_dropdown_font">
                  {option.name}
                </Typography>
              )}
              renderInput={(params) => (
                <TextField {...params} variant="standard" fullWidth />
              )}
            />

            <label>Merchant</label>
            <Autocomplete
              autoHighlight
              disableClearable
              className="chosen_dropdown"
              id="MerchantId"
              name="MerchantId"
              autoComplete
              options={MerchantList ? MerchantList : []}
              getOptionLabel={(option) => option.name}
              value={selectedMerchant}
              onChange={(event, valueobj) =>
                setCurrMerchantId(valueobj ? valueobj.id : "")
              }
              renderOption={(option) => (
                <Typography className="chosen_dropdown_font">
                  {option.name}
                </Typography>
              )}
              renderInput={(params) => (
                <TextField {...params} variant="standard" fullWidth />
              )}
            />
          </div> */}

          <div
            className="contactmodalBody pt-10"
            style={{
              display: "flex",
              justifyContent: "end",
              paddingRight: "16px",
            }}
          >
            <Button
              label={isLoading ? "Searching..." : "Search"}
              class={"btnSave"}
              onClick={searchInvoices}
              disabled={isLoading}
            />
          </div>

          <div style={{ padding: "16px" }}>
            <div style={{ display: "flex", gap: "20px", marginBottom: "16px", flexWrap: "wrap" }}>
              {/* Total Section */}
              <div style={{ display: "flex", gap: "15px", padding: "10px", backgroundColor: "#fafafa", borderRadius: "10px", border: "2px solid #e0e0e0" }}>
                <div style={{ padding: "12px 20px", backgroundColor: "#f3e5f5", borderRadius: "8px", minWidth: "120px" }}>
                  <div style={{ fontSize: "12px", color: "#666", marginBottom: "4px" }}>Total Invoice</div>
                  <div style={{ fontSize: "18px", fontWeight: "bold", color: "#7b1fa2" }}>
                    {invoiceList.length}
                  </div>
                </div>
                <div style={{ padding: "12px 20px", backgroundColor: "#e3f2fd", borderRadius: "8px", minWidth: "180px" }}>
                  <div style={{ fontSize: "12px", color: "#666", marginBottom: "4px" }}>Total Amount (USD)</div>
                  <div style={{ fontSize: "18px", fontWeight: "bold", color: "#1565c0" }}>
                    {invoiceList.reduce((sum, item) => sum + (parseFloat(item.TransactionAmount) || 0), 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </div>
                </div>
                <div style={{ padding: "12px 20px", backgroundColor: "#e8f5e9", borderRadius: "8px", minWidth: "180px" }}>
                  <div style={{ fontSize: "12px", color: "#666", marginBottom: "4px" }}>Total Base Amount (BDT)</div>
                  <div style={{ fontSize: "18px", fontWeight: "bold", color: "#2e7d32" }}>
                    {invoiceList.reduce((sum, item) => sum + (parseFloat(item.BaseAmount) || 0), 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </div>
                </div>
              </div>

              {/* Selected Section */}
              <div style={{ display: "flex", gap: "15px", padding: "10px", backgroundColor: "#fff8e1", borderRadius: "10px", border: "2px solid #ffcc80" }}>
                <div style={{ padding: "12px 20px", backgroundColor: "#ede7f6", borderRadius: "8px", minWidth: "120px" }}>
                  <div style={{ fontSize: "12px", color: "#666", marginBottom: "4px" }}>Selected Invoice</div>
                  <div style={{ fontSize: "18px", fontWeight: "bold", color: "#512da8" }}>
                    {invoiceList.filter(item => item.IsSelected).length}
                  </div>
                </div>
                <div style={{ padding: "12px 20px", backgroundColor: "#fff3e0", borderRadius: "8px", minWidth: "180px" }}>
                  <div style={{ fontSize: "12px", color: "#666", marginBottom: "4px" }}>Selected Amount (USD)</div>
                  <div style={{ fontSize: "18px", fontWeight: "bold", color: "#e65100" }}>
                    {invoiceList.filter(item => item.IsSelected).reduce((sum, item) => sum + (parseFloat(item.TransactionAmount) || 0), 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </div>
                </div>
                <div style={{ padding: "12px 20px", backgroundColor: "#fce4ec", borderRadius: "8px", minWidth: "180px" }}>
                  <div style={{ fontSize: "12px", color: "#666", marginBottom: "4px" }}>Selected Base Amount (BDT)</div>
                  <div style={{ fontSize: "18px", fontWeight: "bold", color: "#c2185b" }}>
                    {invoiceList.filter(item => item.IsSelected).reduce((sum, item) => sum + (parseFloat(item.BaseAmount) || 0), 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </div>
                </div>
              </div>
            </div>
            <div className="" style={{ maxHeight: "350px", overflow: "auto" }}>
              <CustomTable
                columns={columnList}
                rows={invoiceList.length > 0 ? invoiceList : {}}
                actioncontrol={actioncontrol}
                ispagination={false}
              />
            </div>
          </div>

          <div className="modalItem">
            <Button label={"Close"} class={"btnClose"} onClick={modalClose} />
            <Button
              label={"Add Selected Invoices"}
              class={"btnSave"}
              onClick={addInvoicesToPayment}
            />
          </div>
        </div>
      </div>
    </>
  );
};

export default PaymentInvoiceAddModal;
