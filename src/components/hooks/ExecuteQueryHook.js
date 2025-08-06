import { useState, useCallback } from "react";
import { apiCall, apiOption } from "../../../src/actions/api";


const ExecuteQueryHook = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [data, setData] = useState([]);
  const [error, setError] = useState(null);

  const ExecuteQuery = useCallback(async (page, params) => {
    setIsLoading(true);
    setError(null);
    try {
      if (params !== null) {

        apiCall
          .post(page, { params }, apiOption())
          .then((res) => {
            setData(res.data.datalist);
          })
          .catch((error) => {
            setError(error);
          });
      }
    } catch (err) {
      setError(err.message || "Something went wrong!");
    }
    setIsLoading(false);
  }, []);

  return { isLoading, data, error, ExecuteQuery };
};

export default ExecuteQueryHook;