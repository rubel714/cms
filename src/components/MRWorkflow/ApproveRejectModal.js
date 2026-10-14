import React, { useState } from "react";
import { Button } from "../CustomControl/Button";

const MODE_CONFIG = {
  approve: { title: "Approve MR", label: "Approve", class: "btnUpdate", required: false },
  reject: { title: "Reject MR", label: "Reject", class: "btnReject", required: true },
  submit: { title: "Submit MR", label: "Submit", class: "btnUpdate", required: false },
};

/**
 * mode: "approve" | "reject" | "submit". Reject requires a comment.
 * children: extra step fields, shown above the comment box.
 * onConfirm(comments) is called after validation; onClose() cancels.
 */
const ApproveRejectModal = ({ mode, mrNo, confirmLabel, onConfirm, onClose, children }) => {
  const config = MODE_CONFIG[mode];
  const [comments, setComments] = useState("");
  const [error, setError] = useState(false);

  const confirm = () => {
    if (config.required && comments.trim() === "") {
      setError(true);
      return;
    }
    onConfirm(comments.trim());
  };

  return (
    <div className="modal">
      <div className="modal-content" style={{ width: "90%", maxWidth: "650px" }}>
        <div className="modalHeader">
          <h4>
            {config.title}
            {mrNo ? ` - ${mrNo}` : ""}
          </h4>
        </div>

        <div style={{ padding: "16px" }}>
          {children}

          <label style={{ fontSize: ".8rem", fontWeight: "bold" }}>
            Comments{config.required ? " *" : ""}
          </label>
          <textarea
            rows={4}
            className={error ? "validation-style" : ""}
            placeholder="Enter comments"
            value={comments}
            onChange={(e) => {
              setComments(e.target.value);
              setError(false);
            }}
          />
        </div>

        <div className="modalItem">
          <Button label={"Cancel"} class={"btnClose"} onClick={onClose} />
          <Button label={confirmLabel || config.label} class={config.class} onClick={confirm} />
        </div>
      </div>
    </div>
  );
};

export default ApproveRejectModal;
