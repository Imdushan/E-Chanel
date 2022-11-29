<template>
    <div >
<!--        <v-row >-->

<!--            <v-toolbar dense flat left  >-->
<!--                <v-text-field hide-details prepend-icon="mdi-magnify" id="search_address" single-line></v-text-field>-->
<!--&lt;!&ndash;                <v-btn icon>&ndash;&gt;-->
<!--&lt;!&ndash;                    <v-icon>mdi-crosshairs-gps</v-icon>&ndash;&gt;-->
<!--&lt;!&ndash;                </v-btn>&ndash;&gt;-->
<!--            </v-toolbar>-->

<!--            <v-spacer></v-spacer>-->
<!--            <div class="mt-2 ml-10">-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4"  id="zoomout"><v-icon dark small>mdi-magnify-minus</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4"  id="zoomin"><v-icon dark small>mdi-magnify-plus</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="add-event-MARKERbutton"><v-icon dark small>mdi-map-marker</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="mapfullscreentoggler"><v-icon dark small>mdi-arrow-expand-all</v-icon></v-btn>-->
<!--&lt;!&ndash;            <v-btn class="mx-1" fab dark x-small color="green darken-4"><v-icon dark small>mdi-pencil</v-icon></v-btn>&ndash;&gt;-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="add-event-RECTANGLEbutton"><v-icon dark small>mdi-crop-square</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="hand-tool"><v-icon dark small>mdi-cursor-pointer</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="add-event-POLYLINEbutton"><v-icon dark small>mdi-share-variant</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="add-event-POLYGONbutton"><v-icon dark small>mdi-pentagon-outline</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="add-event-CIRCLEbutton"><v-icon dark small>mdi-checkbox-blank-circle-outline</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="trafficLayer"><v-icon dark small>mdi-traffic-light</v-icon></v-btn>-->
<!--            <v-btn class="mx-1" fab dark x-small color="green darken-4" id="delete-button"><v-icon dark small>mdi-delete</v-icon></v-btn>-->

<!--            <v-btn @click="redirectToDistrict()" class="mx-1" fab dark x-small color="red darken-4"><v-icon dark small>mdi-home-circle</v-icon></v-btn>-->
<!--            <v-btn  @click="logout" class="mx-1" fab dark x-small color="red darken-4"><v-icon  @click="logout" dark small>mdi-logout-variant</v-icon></v-btn>-->
<!--            </div>-->
<!--        </v-row>-->
        <v-card style="margin: 5px" flat height="60">
            <fieldset>
                <legend>Add Point to List</legend>
                <v-row style="margin-left:5px;margin-right:5px;">
                    <v-col cols="12"  lg="2" sm="12">
                        <v-text-field dense label="No" hide-details style="font-size: 12px !important;"></v-text-field>
                    </v-col>
                    <v-col  lg="4" sm="12">
                        <v-text-field dense label="name" hide-details style="font-size: 12px !important;"></v-text-field>
                    </v-col>
                    <v-col  lg="4" sm="12">
                        <v-text-field dense label="details" hide-details style="font-size: 12px !important;"></v-text-field>
                    </v-col>
                    <v-col  lg="1" sm="12" style="margin-top: 10px">
                        <v-btn x-small icon> <v-icon>add</v-icon></v-btn>
                    </v-col>
                </v-row>
            </fieldset>
        </v-card>
        <v-card style="margin: 20px 5px 10px 5px;" flat height="100" >
            <v-simple-table   style="width: 100% !important;">
                <thead>
                <tr>
                    <th class="overline" >No</th>
                    <th class="overline" >Name</th>
                    <th class="overline" >Details</th>
                    <th class="overline" >Action</th>
                </tr>
                </thead>
                <tbody class="caption" >
                <tr>
                    <td>1</td>
                    <td>mypoint Data</td>
                    <td>mypoint Data</td>
                    <td><v-icon small color="primary">my_location</v-icon> &nbsp; <v-icon small color="error">delete</v-icon></td>
                </tr>
                </tbody>
            </v-simple-table>
        </v-card>
    </div>

</template>
<script>


import {store} from "../../../store/store_geobizz";
import {mapState} from "vuex";

export default {
    name: "mapLines",
    components: {
        store,
    },
    data() {
        return {
            tab: null,
            activeBtn: 1,
            window: {},
            cycle: false,
            TrafficLayer:false,
            modal: false,
            marker: true,
            center: {lat: 7.927079, lng: 80.761244},
            nicUpload:null,
            postalCode:'',
            district:'',
            postalTown:'',
            drawer: null,
            search: '',
            GMap:null
        }
    },
    computed: {
        ...mapState({
            gmap: state => state.gmap,
        }),
        getMap(){
           return  store.state.gmap;
        }
    },
    mounted() {
        this.GMap = store.state.gmap;
    },
    watch:{
        getMap(map){
            console.log("fucking map",map);
            if (map){
                this.init();
            }
        }
    },
    methods:{
        //=========================================================Set Map Search and other init=====================
        init: function () {
            let drawingManager;
            let drawings = [];
            let selectedShape;

            function clearSelection() {
                if (selectedShape) {
                    selectedShape.setEditable(false);
                    selectedShape = null;
                }
            }

            function setSelection(shape) {
                clearSelection();
                selectedShape = shape;
                shape.setEditable(true);
            }

            function deleteSelectedShape() {
                if (selectedShape) {
                    selectedShape.setMap(null);
                }
            }

            // function deleteAllShape() {
            //   for (var i=0; i < drawings.length; i++) {
            //     drawings[i].overlay.setMap(null);
            //   }
            //   drawings = [];
            // }

            getMap
            let that = this;
            let map = store.state.gmap;

            //*************--------------------------==========================================-------------------==================-------------------------******
            //===========================***********************************************************===============================================================
            //===========================**********Start basic Map controls custermize*****========================================================================
            //===========================***********************************************************===============================================================
            //*************--------------------------==========================================-------------------==================-------------------------******

            initFullscreenControl(map);

            function initFullscreenControl(map) {
                let elementToSendFullscreen = map.getDiv().firstChild;
                let fullscreenControl = document.getElementById('mapfullscreentoggler');
                fullscreenControl.onclick = function () {
                    if (isFullscreen(elementToSendFullscreen)) {
                        exitFullscreen();
                    } else {
                        requestFullscreen(elementToSendFullscreen);
                    }
                };
                document.onwebkitfullscreenchange = document.onmsfullscreenchange = document.onmozfullscreenchange =
                    document.onfullscreenchange = function () {
                        if (isFullscreen(elementToSendFullscreen)) {
                            fullscreenControl.classList.add('is-fullscreen');
                        } else {
                            fullscreenControl.classList.remove('is-fullscreen');
                        }
                    };
            }

            function isFullscreen(element) {
                return (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) == element;
            }

            function requestFullscreen(element) {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.webkitRequestFullScreen) {
                    element.webkitRequestFullScreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.msRequestFullScreen) {
                    element.msRequestFullScreen();
                }
            }

            function exitFullscreen() {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msCancelFullScreen) {
                    document.msCancelFullScreen();
                }
            }

            // Create the DIV to hold the control and
            // call the HomeControl() constructor passing
            // in this DIV.
            let homeControlDiv = document.createElement('div');
            let homeControl = new HomeControl(homeControlDiv, map);

            function HomeControl(controlDiv, map) {
                google.maps.event.addDomListener(zoomout, 'click', function () {
                    let currentZoomLevel = map.getZoom();
                    if (currentZoomLevel != 0) {
                        map.setZoom(currentZoomLevel - 1);
                    }
                });

                google.maps.event.addDomListener(zoomin, 'click', function () {
                    let currentZoomLevel = map.getZoom();
                    if (currentZoomLevel != 21) {
                        map.setZoom(currentZoomLevel + 1);
                    }
                });
            }


            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: null,
                drawingControl: false,
                markerOptions: {
                    draggable: false,
                    optimized: false,
                    icon: new google.maps.MarkerImage('https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'),
                },
                circleOptions: {
                    fillOpacity: 0.5,
                    strokeWeight: 2,
                    clickable: true,
                    editable: true,
                    draggable: false
                },
                rectangleOptions: {
                    fillOpacity: 0.5,
                    strokeWeight: 2,
                    clickable: true,
                    editable: true,
                    draggable: false
                },
                polylineOptions: {
                    editable: false,
                    draggable: false,
                    strokeWeight: 2,
                },
                polygonOptions: {
                    editable: true,
                    fillOpacity: 0.5,
                    strokeWeight: 2,
                    draggable: false
                },
            });
            drawingManager.setMap(map);


            google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
                drawings.push(e);
                if (e.type !== google.maps.drawing.OverlayType.MARKER) {
                    // Switch back to non-drawing mode after drawing a shape.
                    drawingManager.setDrawingMode(null);

                    // Add an event listener that selects the newly-drawn shape when the user
                    // mouses down on it.
                    let newShape = e.overlay;
                    newShape.type = e.type;
                    google.maps.event.addListener(newShape, 'click', function () {
                        setSelection(newShape);
                    });
                    setSelection(newShape);
                }
            });

            document.getElementById('add-event-MARKERbutton').onclick = function () {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.MARKER);
            };
            document.getElementById('add-event-POLYGONbutton').onclick = function () {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
            };
            document.getElementById('add-event-POLYLINEbutton').onclick = function () {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYLINE);
            };
            document.getElementById('add-event-CIRCLEbutton').onclick = function () {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.CIRCLE);
            };
            document.getElementById('add-event-RECTANGLEbutton').onclick = function () {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.RECTANGLE);
            };

            google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
            google.maps.event.addListener(map, 'click', clearSelection);
            google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);
            //google.maps.event.addDomListener(document.getElementById('delete-all-button'), 'click', deleteAllShape);

            google.maps.event.addDomListener(document.getElementById('hand-tool'), 'click', function () {
                drawingManager.setDrawingMode(null);
            });


            //*************--------------------------==========================================-------------------==================-------------------------********
            //===========================***********************************************************=================================================================
            //===========================**********End basic Map controls custermize*****============================================================================
            //===========================***********************************************************=================================================================
            //*************--------------------------==========================================-------------------==================-------------------------********


            //--------------------------creating traffic layer to show traffic on the map-----------------------
            let trafficLayer = null;
            google.maps.event.addDomListener(document.getElementById('trafficLayer'), 'click', function () {
                if (trafficLayer != null) {
                    trafficLayer.setMap(null);
                    trafficLayer = null;
                } else {
                    trafficLayer = new google.maps.TrafficLayer();
                    trafficLayer.setMap(map);
                }
            });
            //--------------------------creating traffic layer to show traffic on the map-----------------------


            let searchAddress_input = document.getElementById('search_address');
            let searchAddress_autocomplete = new google.maps.places.Autocomplete(searchAddress_input);
            searchAddress_autocomplete.bindTo('bounds', map);

            searchAddress_autocomplete.addListener('place_changed', function () {
                let place = searchAddress_autocomplete.getPlace();
                self.searchAddress_obj = {
                    place
                };
                if (place.geometry) {
                    map.setCenter({lat: place.geometry.location.lat(), lng: place.geometry.location.lng()});
                    map.setZoom(18.9);
                    console.log(place.geometry.location.lat());
                } else {
                    let geocoder = new google.maps.Geocoder();
                    let fulladdress = searchAddress_input.value;
                    let splitted = /,(.+)/.exec(fulladdress)[1];
                    //----------------------------if first result not found search on  second----------------------------
                    if (splitted) {
                        geocoder.geocode({
                            'componentRestrictions': {country: 'LK'},
                            'address': splitted
                        }, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                map.setCenter({
                                    lat: results[0].geometry.location.lat(),
                                    lng: results[0].geometry.location.lng()
                                });
                                map.setZoom(18.9);
                                console.log(results[0].geometry.location.lat());
                                let payload = {
                                    lat: results[0].geometry.location.lat(),
                                    lng: results[0].geometry.location.lng(),
                                    address: splitted,
                                    type: 'House'
                                };

                                store.commit('setcurrentLocation', payload);
                                //-----------------show snackbar about change the address---------------------------
                                that.snackBarTop = true;
                                that.snackBarRight = true;
                                that.snackMessage = 'show result ' + splitted + ' instead of ' + searchAddress_input.value;
                                that.snackColor = 'primary';
                                that.snackbar = true;
                                searchAddress_input.value = splitted;
                            } else {
                                let secondary = /,(.+)/.exec(splitted)[1];
                                if (secondary) {
                                    geocoder.geocode({
                                        'componentRestrictions': {country: 'LK'},
                                        'address': secondary
                                    }, function (results, status) {
                                        if (status == google.maps.GeocoderStatus.OK) {
                                            map.setCenter({
                                                lat: results[0].geometry.location.lat(),
                                                lng: results[0].geometry.location.lng()
                                            });
                                            map.setZoom(18.9);
                                            console.log(results[0].geometry.location.lat());
                                            let payload = {
                                                lat: results[0].geometry.location.lat(),
                                                lng: results[0].geometry.location.lng(),
                                                address: secondary,
                                                type: 'House'
                                            };

                                            store.commit('setcurrentLocation', payload);

                                            //-----------------show snackbar about change the address---------------------------
                                            that.snackBarTop = true;
                                            that.snackBarRight = true;
                                            that.snackMessage = 'show result ' + secondary + ' instead of ' + searchAddress_input.value;
                                            that.snackColor = 'primary';
                                            that.snackbar = true;
                                            searchAddress_input.value = secondary;
                                        } else {
                                            //----------------------------if first result not found search on  third----------------------------
                                            let city = secondary.split(',');
                                            if (city && city.length > 0) {
                                                geocoder.geocode({
                                                    'componentRestrictions': {country: 'LK'},
                                                    'address': city[city.length - 1]
                                                }, function (results, status) {
                                                    if (status == google.maps.GeocoderStatus.OK) {
                                                        map.setCenter({
                                                            lat: results[0].geometry.location.lat(),
                                                            lng: results[0].geometry.location.lng()
                                                        });
                                                        map.setZoom(18.9);
                                                        console.log(results[0].geometry.location.lat());
                                                        let payload = {
                                                            lat: results[0].geometry.location.lat(),
                                                            lng: results[0].geometry.location.lng(),
                                                            address: city[city.length - 1],
                                                            type: 'House'
                                                        };
                                                        store.commit('setcurrentLocation', payload);
                                                        //-----------------show snackbar about change the address---------------------------
                                                        that.snackBarTop = true;
                                                        that.snackBarRight = true;
                                                        that.snackMessage = 'show result ' + city[city.length - 1] + ' instead of ' + searchAddress_input.value;
                                                        that.snackColor = 'primary';
                                                        that.snackbar = true;
                                                        searchAddress_input.value = city[city.length - 1];
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
                // store.commit('setGeocode',payload);
            });
        },

        logout(){
            window.setLogout();
        },
        redirectToDistrict(){
            window.location.href = '/selectDistrict';
        },
        toggleMarker () {
            this.marker = !this.marker
        },
        resetIcon () {
            this.iconIndex = 0
        },
    }
}
</script>


<style>
/* html {   overflow-y: hidden !important; }  */
.pane {
    color: #2b2b2b;
    overflow-y: scroll;
    border: none !important;
}
.scroller {
    scrollbar-color: #1B5E20;
    scrollbar-width: thin;
}
.scroller::-webkit-scrollbar {
    width: 10px;
    border: none !important;
}
.scroller::-webkit-scrollbar-track {
    background: #9E9E9E;
    border: none !important;
}
.scroller::-webkit-scrollbar-thumb {
    background: #1B5E20;
    border: none !important;
}
.pane_x {
    color: #2b2b2b;
    width: 100% !important;
    overflow-x: scroll;
    border: none !important;
}
.scroller_x {
    scrollbar-color: #1B5E20;
    scrollbar-width: thin;
}
.scroller_x::-webkit-scrollbar {
    width: 10px;
    border: none !important;
}
.scroller_x::-webkit-scrollbar-track {
    background: #9E9E9E;
    border: none !important;
}
.scroller_x::-webkit-scrollbar-thumb {
    background: #1B5E20;
    border: none !important;
}

.v-label {
    font-size: 0.8em !important;
    color: black !important;
}
</style>
