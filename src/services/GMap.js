import React, { forwardRef, useRef, Component } from "react";
import { makeStyles } from "@material-ui/core/styles";
import swal from "sweetalert";
import { Grid, MenuItem, Card, CardContent, Checkbox } from "@material-ui/core";
import {
  Map,
  GoogleMap,
  GoogleApiWrapper,
  Marker,
  InfoWindow,
  MarkerClusterer,
  MapWrapper,
} from "google-maps-react";

import * as Service from "../services/Service.js";

import Table from "@material-ui/core/Table";
import TableBody from "@material-ui/core/TableBody";
import TableCell from "@material-ui/core/TableCell";
import TableContainer from "@material-ui/core/TableContainer";
import TableRow from "@material-ui/core/TableRow";
import { Button } from "reactstrap";

 
const GMap = ({ ...props }) => {
  console.log('propssssssssssssssssssss: ', props.formData);
  console.log('propssssssssssssssssssss: ', props.latlng);

  

  const mapStyles = {
    width: "35.5%",
    height: "46.1%",
    overflow: "hidden !important",
  };

  const { useState } = React;
  const DispensingLanguage = JSON.parse(
    localStorage.getItem("DispensingLanguage")
  );
  const lan = localStorage.getItem("LangCode");
  const menukey = "facility";
  const classes = useStyles();

   
  const [loaded, setLoaded] = useState(false);

  const [state, setState] = useState({
    showingInfoWindow: true,
    activeMarker: {},
    selectedPlace: {},
    markerParams: {},
  });
  const fitBounds = map => {
    console.log("jbhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh");
    const bounds = new window.google.maps.LatLngBounds();
    
    map.fitBounds(bounds);
  };
  const loadHandler = map => {
    console.log("jbhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh");
    fitBounds(map);
  };

  const onMarkerClick = (props, marker, e) => {
    console.log(props.params);
    setState({
      selectedPlace: props,
      markerParams: props.params,
      activeMarker: marker,
      showingInfoWindow: true,
    });
  };

  

  const onClose = (props) => {
    if (state.showingInfoWindow) {
      setState({
        selectedPlace: {},
        showingInfoWindow: false,
        activeMarker: null,
        markerParams: {},
      });
    }
  };
  function createKey(location) {
    return location.lat + location.lng;
  }

  const changeMarkerPosition =(e)=>{

   // alert("vhs")
    console.log('e: ', e);


    // this.setState({
    // ...this.state,
    //   details:{
    //     ...this.state.details,
    //     lat:e.lat,
    //     lng:e.lng
    //   }
    // })
  }


  
  const onMarkerDragEnd = evt => {

    let newLat = evt.latLng.lat(),
    
		    newLng = evt.latLng.lng();
        console.log('newLat: ', newLat+'+++++++++++'+newLng);


    console.log('6666: ',evt);
   // console.log('888888888888888888: ',evt.onDragend.onMarkerDragEnd());
    //props.formData['location']=evt.position.lat+','+evt.position.lng;
   // props.updateLatLang(evt.position.lat,evt.position.lng);
   // props.latlng=[evt.position.lat,evt.position.lng];
    console.log('6666: ', evt.position);
   // console.log(evt.google.maps.Marker.getPosition().lat());
  };
   

  const onMarkerDragEndss = (coord, index) => {
    console.log('coord: ', coord);
    const { latLng } = coord;
    const lat = latLng.lat();
    const lng = latLng.lng();
return;
    this.setState(prevState => {
      const markers = [...this.state.markers];
      markers[index] = { ...markers[index], position: { lat, lng } };
      return { markers };
    });
  };

  const options = {
    imagePath:
      "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m1.png", // so you must have m1.png, m2.png, m3.png, m4.png, m5.png and m6.png in that folder
  };

  return (
    <>
       

                <Map
                  
                  google={props.google}
                  zoom={10}
                  style={mapStyles}
                  initialCenter={{ 
                     lat: 9.314623823,
                     lng: 2.31184834
                   //  lat:  props.latlng[0],
                   //  lng: props.latlng[1]
                  //  lat: props.formData.Latitude,
                   // lng: props.formData.Longitude
                  }}
                  center={
                    { 
                      // lat: 9.314623823,
                       //lng: 2.31184834
                     //  lat:  props.latlng[0],
                     //  lng: props.latlng[1]
                      lat: props.formData.Latitude,
                      lng: props.formData.Longitude
                    }
                  }
                  draggable={true} 
                  onClick = {changeMarkerPosition}
                >

                  
                  {
                   props.addProductForm?(
                      <Marker
                         
                        ref={refmap => refmap}
                        params={props.formData}
                        position={{ lat:props.latlng[0], lng:  props.latlng[1] }}
                        onClick={onMarkerClick}
                        draggable={true}
                    //    onDragend={onMarkerDragEnd}
                  //  {(t, map, coord) => this.onMarkerDragEnd(coord, index)}

                        onDragend={(t, map, coord) => {
                          
                          console.log('dragEnd', coord.latLng.lng() )


                           props.formData['location']=coord.latLng.lat()+','+coord.latLng.lng();
                            props.updateLatLang(coord.latLng.lat(),coord.latLng.lng());
                            props.latlng=[coord.latLng.lat(),coord.latLng.lng()];

                        
                        }}
                        params={props.formData} 
                        
                      //  onDragEnd={(e) => {console.log('dragEnd',e)}}
                        
                    //    onPositionChanged={onPositionChanged}

                       // options={options}
                        icon={{
                          url: require("assets/img/marker_icon.png")  ,
                        //  anchor: { x: 16, y: 16 },
                        //  scaledSize: { height: 16, width: 16 },  
                  
                      }}
                  
                      />):(

                        <Marker
                        ref={refmap => refmap}
                        params={props.formData}
                        position={{ lat:props.formData.Latitude, lng:  props.formData.Longitude }}
                        onClick={onMarkerClick}
                        draggable={true}
                       // onDragend={onMarkerDragEnd}
                        //onDragEnd={(e) => {console.log('dragEnd', e)}}

                        onDragend={(t, map, coord) => {
                          
                          console.log('dragEnd', coord.latLng.lng() )


                            props.formData['location']=coord.latLng.lat()+','+coord.latLng.lng();
                            props.updateLatLang(coord.latLng.lat(),coord.latLng.lng());
                            props.latlng=[coord.latLng.lat(),coord.latLng.lng()];
                            props.formData.Latitude = coord.latLng.lat();
                            props.formData.Longitude = coord.latLng.lng();
                        
                        }}

                        params={props.formData} 
                        
                       // options={options}
                        icon={{

                          url: require("assets/img/marker_icon.png")  ,
                        //  anchor: { x: 16, y: 16 },
                        //  scaledSize: { height: 16, width: 16 },  
                  
                      }}
                  
                      />
                      )
                   

                    //   <MarkerClusterer >
                    //   {(clusterer) =>
                    //     // locations.map((location) => (
                    //     //   <Marker key={createKey(location)} position={location} clusterer={clusterer} />
                    //     // ))   options={options}  key={createKey(item)}  onClick={onMarkerClick}

                    //     MapLatitudeLongitude.map((item)=>(

                    //         <Marker  params={item}  clusterer={clusterer}  position={{ lat: item.Latitude, lng: item.Longitude}} />

                    //     ))
                    //   }
                    // </MarkerClusterer>
                  }
                  {  
                  
                  
      <InfoWindow
          marker={state.activeMarker}
          visible={state.showingInfoWindow}
          onClose={onClose}
        >
          <div>

          <TableContainer>
                            <Table className={classes.table} aria-label="simple table">
                              <TableBody>
                                <TableRow>
                                  <TableCell
                                    style={{
                                      "fontWeight": "bold",
                                      "borderRight": "1px solid antiquewhite",
                                    }}
                                  >
                                  {DispensingLanguage[lan][menukey]["Facility Name"]}
                                  </TableCell>
                                  <TableCell >
                                  {state.markerParams.FacilityName}
                                  </TableCell>
                                  </TableRow>
                                  <TableRow>
                                  <TableCell
                                    style={{
                                      "fontWeight": "bold",
                                      "borderRight": "1px solid antiquewhite",
                                    }}
                                    align="left"
                                  >
                                   {DispensingLanguage[lan][menukey]["Commune Name"]}
                                  </TableCell>
                                  
                                  <TableCell > {state.markerParams.DistrictName}  </TableCell>
                                  </TableRow>
                                  <TableRow>
                                  <TableCell
                                    style={{
                                      "fontWeight": "bold",
                                      "borderRight": "1px solid antiquewhite",
                                    }}
                                    align="left"
                                  >
                                   {DispensingLanguage[lan][menukey]["ZS Name"]}
                                  </TableCell>
                                  <TableCell >  {state.markerParams.ZoneName} </TableCell>
                                  </TableRow>
                                  <TableRow>
                                  <TableCell
                                    style={{
                                      "fontWeight": "bold",
                                      "borderRight": "1px solid antiquewhite",
                                    }}
                                  >
                                  {DispensingLanguage[lan][menukey]["Department Name"] }
                                  </TableCell>
                                  <TableCell > {state.markerParams.RegionName} </TableCell>
                                   
                                </TableRow>

                                <TableRow>
                                <TableCell
                                    style={{
                                      "fontWeight": "bold",
                                      "borderRight": "1px solid antiquewhite",
                                    }}
                                  >
                                  {DispensingLanguage[lan][menukey]["Facility Type"] }
                                  </TableCell>
                                  <TableCell > {state.markerParams.FTypeName} </TableCell>
                                   
                                </TableRow>

                                <TableRow>
                                <TableCell
                                    style={{
                                      "fontWeight": "bold",
                                      "borderRight": "1px solid antiquewhite",
                                    }}
                                  >
                                  {DispensingLanguage[lan][menukey]["Facility Address"] }
                                  </TableCell>
                                  <TableCell > {state.markerParams.FacilityAddress} </TableCell>
                                   
                                </TableRow>


                                <TableRow>
                                <TableCell
                                    style={{
                                      "fontWeight": "bold",
                                      "borderRight": "1px solid antiquewhite",
                                    }}
                                  >
                                  {DispensingLanguage[lan][menukey]["Facility Phone"] }
                                  </TableCell>
                                  <TableCell > {state.markerParams.FacilityPhone} </TableCell>
                                   
                                </TableRow>

                              </TableBody>
                             
                            </Table>
                          </TableContainer>

           
          </div>
        </InfoWindow> }
                </Map>
               
    </>
  );
};

export default GoogleApiWrapper({
  apiKey: "AIzaSyCD7OEdGUC1V__0-mBJIoYifI5UtEILYbg",
})(GMap);
//export default FacilityMap;

const useStyles = makeStyles({
  facilityPageTitle: {
    marginTop: "60px",
    color: "white",
    background: "whitesmoke",
    color: "black",
    borderRadius: "10px",
    padding: "1rem",
  },
  tableContainer: {
    backgroundColor: "whitesmoke",
    borderRadius: "10px",
    padding: "2rem",
    color: "black",
  },
  fullWidth: {
    width: "95%",
  },
  filterDiv: {
    width: "80%",
    display: "flex",
  },
});