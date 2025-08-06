import React from "react";
import { makeStyles } from "@material-ui/core/styles";

// sections for this page
import Images from "../index-sections/Images.js";

// const useStyles = makeStyles((theme) => ({
//   root: {
//     display: "flex",
//     "& > * + *": {
//       marginLeft: theme.spacing(2),
//     },
//   },
// }));
const HomePage = (props) => {
  React.useEffect(() => {}, []);

  return (
    <>
      <Images />
    </>
  );
};

export default HomePage;
