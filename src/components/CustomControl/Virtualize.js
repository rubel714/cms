import React from "react";
import PropTypes from "prop-types";
import TextField from "@material-ui/core/TextField";
import Autocomplete from "@material-ui/lab/Autocomplete";
import useMediaQuery from "@material-ui/core/useMediaQuery";
import ListSubheader from "@material-ui/core/ListSubheader";
import { useTheme, makeStyles } from "@material-ui/core/styles";
import { VariableSizeList } from "react-window";
import { Typography } from "@material-ui/core";

const LISTBOX_PADDING = 8; // px

function renderRow(props) {
  const { data, index, style } = props;
  return React.cloneElement(data[index], {
    style: {
      ...style,
      top: style.top + LISTBOX_PADDING,
    },
  });
}

const OuterElementContext = React.createContext({});

const OuterElementType = React.forwardRef((props, ref) => {
  const outerProps = React.useContext(OuterElementContext);
  return <div ref={ref} {...props} {...outerProps} />;
});

function useResetCache(data) {
  const ref = React.useRef(null);
  React.useEffect(() => {
    if (ref.current != null) {
      ref.current.resetAfterIndex(0, true);
    }
  }, [data]);
  return ref;
}

// Adapter for react-window
const ListboxComponent = React.forwardRef(function ListboxComponent(
  props,
  ref
) {
  const { children, ...other } = props;
  const itemData = React.Children.toArray(children);
  const theme = useTheme();
  const smUp = useMediaQuery(theme.breakpoints.up("sm"), { noSsr: true });
  const itemCount = itemData.length;
  const itemSize = smUp ? 36 : 48;

  const getChildSize = (child) => {
    if (React.isValidElement(child) && child.type === ListSubheader) {
      return 48;
    }

    return itemSize;
  };

  const getHeight = () => {
    if (itemCount > 8) {
      return 8 * itemSize;
    }
    return itemData.map(getChildSize).reduce((a, b) => a + b, 0);
  };

  const gridRef = useResetCache(itemCount);

  return (
    <div ref={ref}>
      <OuterElementContext.Provider value={other}>
        <VariableSizeList
          itemData={itemData}
          height={getHeight() + 2 * LISTBOX_PADDING}
          width="100%"
          ref={gridRef}
          outerElementType={OuterElementType}
          innerElementType="ul"
          itemSize={(index) => getChildSize(itemData[index])}
          overscanCount={5}
          itemCount={itemCount}
        >
          {renderRow}
        </VariableSizeList>
      </OuterElementContext.Provider>
    </div>
  );
});

ListboxComponent.propTypes = {
  children: PropTypes.node,
};

// function random(length) {
//   const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
//   let result = '';

//   for (let i = 0; i < length; i += 1) {
//     result += characters.charAt(Math.floor(Math.random() * characters.length));
//   }

//   return result;
// }

const useStyles = makeStyles({
  listbox: {
    boxSizing: "border-box",
    "& ul": {
      padding: 0,
      margin: 0,
    },
  },
});

// const OPTIONS = Array.from(new Array(10000))
//   .map(() => random(10 + Math.ceil(Math.random() * 20)))
//   .sort((a, b) => a.toUpperCase().localeCompare(b.toUpperCase()));


// console.log("From virtualize load");
// const OPTIONS = [
//   " DAIRY MILK CADBURY (N00100010646)-27504",
//   "DAN  CAKE VANILLA MUFFIN (N00100002109)-5997",
//   "DAN CAKE VANILLA PLAIN CAKE (N00100002125)-6015",
//   "100 PLUS (N00100008301)-21774",
//   "2 HOUR PROTECTION  (N00100010109)-26212",
//   "3G ORGES-BATAREY (N00100010569)-27362",
//   "500 ML DOI KHIR (N00100010152)-26346",
//   "5STAR XTRA (N00100004521)-11814",
//   "5X (N00100007661)-20176",
//   "7 BAHAR ACACIA HONEY (N00100005054)-13218",
//   "7 BAHAR ACACIA HONEY TUBE (N00100005049)-13208",
//   "7 BAHAR BLACK FOREST HONEY (N00100005050)-13210",
//   "7 BAHAR MULTIFLOWE HONEY (N00100005051)-13212",
//   "7 BAHAR MULTIFLOWER HONEY TUBE (N00100005055)-1322",
//   "7 BAHAR ORANGE BLOSSOM HONEY (N00100005052)-13214",
//   "7 BAHAR PINE HONEY (N00100005053)-13216",
//   "7 DAYS PROTECTION SHIELD DR (N00100010107)-26209",
//   "7 SURFACE PROTECTION SHIELD  (N00100010108)-26211",
//   "7 UP  (N00100005523)-14600",
//   "7 UP (CAN) (N00100007185)-18810",
//   "7UP (N00100007160)-18774",
//   "7UP - (N00100007163)-18777",
//   "7UP CAN (N00100007815)-20616",
//   "7UP- (N00100007156)-18769",
//   "7UP-- (N00100007162)-18776",
//   "7UP-5 (N00100007161)-18775",
//   "A FORTE (N00100003890)-10143",
//   "A- CAL D (N00100000558)-1295",
// ];

const renderGroup = (params) => [
  // <ListSubheader key={params.key} component="div">
  //   {params.group}
  // </ListSubheader>,
  params.children,
];

const handleChangeChoosenVircual = (props, name, value)=>{
  if(value.length>0){
    console.log('handleChangeChoosenVircual value: ', value);
    console.log('value: ', value.length);

    let val = "";
    if(value !== "Select product"){
      let valList = value.split("-");
      val = parseInt(valList[valList.length-1]);
      // console.log('valList: ', valList);
      // console.log('val: ', val);
  
    }
    console.log('val: ', val);


  
    props.handleChangeChoosenMany(name, val);
  }


}

// export default function Virtualize() {
export const Virtualize = (props) => {
  // console.log('from virtualize function: ', dataList);
  // console.log("From virtualize function", props.productList);
  console.log('props.currentMany: ', props.currentMany);

  const classes = useStyles();

  return (
    <Autocomplete
      id="ProductId"
      name="ProductId"
      autoHighlight
      className="chosen_dropdown"
      // style={{ width: 300 }}
      disableListWrap
      classes={classes}
      class={props.errorObjectMany.ProductId}

      ListboxComponent={ListboxComponent}
      renderGroup={renderGroup}
      // options={OPTIONS}
      options={props.productList}
      groupBy={(option) => option[0].toUpperCase()}

      value={
        props.productList
          ? props.productList[
            props.productList.findIndex(
                (list) => list == props.currentMany.ProductName
              )
            ]
          : null
      }

      onChange={(event, valueobj) =>
        handleChangeChoosenVircual(props, "ProductId", valueobj ? valueobj : "")
        
      }
      renderInput={(params) => (
        <TextField
          {...params}
          // variant="outlined"
          // label="10,000 options"
        />
      )}
      renderOption={(option) => (
        <Typography className="chosen_dropdown_font" noWrap>
          {option}
        </Typography>
      )}
    />
  );
};
