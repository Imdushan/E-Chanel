<template>
    <div >
            <v-form>
                <v-card tile class="mx-auto"  flat>
                        <v-row>
                            <v-col cols="12" md="12">
                                <v-row no-gutters>
                                    <v-btn left dark x-small  color="primary" @click="startDrawPoint">
                                        <v-icon left x-small>mdi-pen</v-icon>Draw Point
                                    </v-btn>
                                    &nbsp;
                                    <v-btn left dark x-small  color="primary" @click="startDrawLine">
                                        <v-icon left x-small>mdi-pen</v-icon>Draw Line
                                    </v-btn>
                                    &nbsp;
                                    <v-btn left dark x-small  color="error" @click="clearElevationAnalysis">
                                        <v-icon left x-small>mdi-stop-circle</v-icon>Clear
                                    </v-btn>

                                </v-row>


                            </v-col>

                            <!--   Elevation data of point layer  -->
                            <v-col cols="12" md="12" lg="12" v-if="this.isdrawStarted && isPointElevation">
                                <v-list>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title> Location </v-list-item-title>
                                        </v-list-item-content>
                                        <v-list-item-action-text style="font-weight: bolder;font-size: 11pt">
                                            {{pointElevationData.lat+' , '+pointElevationData.lng}}
                                        </v-list-item-action-text>
                                    </v-list-item>

                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title> Elevation </v-list-item-title>
                                        </v-list-item-content>
                                        <v-list-item-action-text style="font-weight: bolder;font-size: 11pt">
                                            {{pointElevationData.elevation}}
                                        </v-list-item-action-text>
                                    </v-list-item>

                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title> Address </v-list-item-title>
                                        </v-list-item-content>
                                        <v-list-item-action-text style="font-weight: bolder;font-size: 11pt">
                                            {{pointElevationData.address}}
                                        </v-list-item-action-text>
                                    </v-list-item>


                                </v-list>
                            </v-col>
                            <!--   Elevation data of line layer  -->
                            <v-col cols="12" md="12" lg="12" v-if="this.isdrawStarted && isLineElevation">
                                <GChart type="ColumnChart" ref="gChart" :data="elevationData" :options="chartOptions" :events="chartEvents"/>
                                <div id="elevation_chart"></div>
                            </v-col>
                        </v-row>
                </v-card>
            </v-form>

    </div>
</template>
<script>
import {mapState} from "vuex";
import {store} from "../../store/store_geobizz";
import { GChart } from 'vue-google-charts'

export default {
    name: "ElevationProfile",
    components: {
        GChart
    },
    data() {
        return {
          lat:'',
          lng:'',
          address:'',
            // Array will be automatically processed with visualization.arrayToDataTable function
            elevationData: null,
            locationData:[],
            chartOptions: {
                chart: {
                    title: 'Elevation Chart',
                }
            },
            drawingManager:null,
            dataArray:[],
            polyLine:null,
            isdrawStarted:false,
            selectedMarker:null,
            isPointElevation:false,
            isLineElevation:false,
            pointElevationData:{
              lat:'',
              lng:'',
              address:'',
              elevation:'',
            },
            chartEvents: {
                select: () => {
                    const table = this.$refs.gChart.chartObject;
                    const selection = table.getSelection();
                    console.log("fucking selection",this.locationData[selection[0].row]);
                    this.setElavationMarkerOnChartClick(this.locationData[selection[0].row])
                }
            },
            chartMarker:null
        }
    },
    computed:{
        ...mapState({
            gmap: state => state.gmap,
            httpheader: state => state.httpheader,
            selectedLocation: state => state.selectedLocation,
        }),

        chartData(){
            return this.elevationData;
        },
        mapContextAction(){
            return store.state.contextMenuAction;
        }
    },
    watch:{
        onMapClick(data){
            this.GoogleAddress = data.address;
            this.lat = data.lat;
            this.lng = data.lng;
            this.LocationFarmer = data.lat+" , "+data.lng
        },
        mapContextAction(action){
            if (action.drawerID === 'mapToolBar' && action.tabID === 'elevation_profile'){
                store.commit('resetContextMenuAction',true);
            }
        }
    },
    mounted() {

    },
    methods:{
        //---------------------run elevation analysis-----------------------------------------
        startDrawLine(){
            this.isdrawStarted = true;
            this.isLineElevation = true;
            this.isPointElevation = false;
            let that = this;
            let map = store.state.gmap;
            this.drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: null,
                drawingControl: false,
                polylineOptions: {
                    editable: false,
                    draggable: false,
                    strokeWeight: 2,
                },
            });
            this.drawingManager.setMap(map);
            this.drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYLINE);

            if (this.polyLine)
                this.polyLine.setMap(null);

            google.maps.event.addListener(this.drawingManager, 'polylinecomplete', function(polyline) {
                that.polyLine = polyline;
                that.drawingManager.setDrawingMode(null);
                let polylinePath = polyline.getPath();
                let path  = polylinePath.getArray();// console.log("polyline : "+polylinePath.getArray());

                console.log("polyline : "+polylinePath.getArray());
                const elevator = new google.maps.ElevationService();
                elevator.getElevationAlongPath(
                    {
                        path: path,
                        samples: 256,
                    },
                    plotElevation
                );


                function plotElevation(elevations, status) {
                    const chartDiv = document.getElementById("elevation_chart");

                    if (status !== "OK") {
                        // Show the error code inside the chartDiv.
                        chartDiv.innerHTML = "Cannot show elevation: request failed because " + status;
                        return;
                    }
                    // Create a new chart in the elevation_chart DIV.
                    // const chart = new google.visualization.ColumnChart(chartDiv);
                    // Extract the data from which to populate the chart.
                    // Because the samples are equidistant, the 'Sample'
                    // column here does double duty as distance along the
                    // X axis.
                    // const data = new google.visualization.DataTable();
                    // data.addColumn("string", "Sample");
                    // data.addColumn("number", "Elevation");
                    that.elevationData = [ ['Sample', 'Elevation'],];
                    // that.locationData = [ ['', ''],];
                    for (let i = 0; i < elevations.length; i++) {
                        // data.addRow(["", elevations[i].elevation]);
                        that.locationData.push(elevations[i]);
                        that.elevationData.push(["", elevations[i].elevation])
                    }
                }

            });
            store.commit('addToMapContextMenu', {title: 'Elevation Profile', id: 'elevation_profile', action: {type: 'navigation', drawer: 'right', drawerID: 'mapToolBar', tabID: 'elevation_profile', tabNo: 0}});
        },
        //-------------------run elevation profile point---------------------------------------
        startDrawPoint(){
            let map = store.state.gmap;
            let that = this;
            this.isLineElevation = false;
            this.isPointElevation = true;
            this.isdrawStarted = true;
            this.drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: null,
                drawingControl: false,
                markerOptions: {
                    draggable: false,
                    optimized: false,
                    icon:{
                        url: '/storage/img/icons/administrativeboundary.png',
                        size: new google.maps.Size(25, 25),
                        anchor: new google.maps.Point(9, 14),
                        origin: new google.maps.Point(0, 0),
                        scaledSize: new google.maps.Size(20, 20),
                        labelOrigin: {x: 0, y: -10}
                    }
                },
            });
            this.drawingManager.setMap(map);
            this.drawingManager.setDrawingMode(google.maps.drawing.OverlayType.MARKER);
            const elevator = new google.maps.ElevationService();
            google.maps.event.addListener(that.drawingManager, 'overlaycomplete', function (e) {
                console.log("elevation points",e);
                // Switch back to non-drawing mode after drawing a shape.
                that.drawingManager.setDrawingMode(null);
                that.selectedMarker = e.overlay;
                let lat = e.overlay.position.lat();
                let lng = e.overlay.position.lng();
                that.pointElevationData.lat = lat;
                that.pointElevationData.lng = lng;
                that.pointElevationData.address = "";

                //---------------------run elevator--------------------------------
                elevator.getElevationForLocations(
                    {
                        locations: [e.overlay.position],
                    },
                    (results, status) => {
                        if (status === "OK" && results) {
                            // Retrieve the first result
                            if (results[0]) {
                                e.overlay.setLabel({
                                    text:results[0].elevation.toFixed(3) + "m",
                                    fontSize: "12px",
                                    fontWeight: "bold",
                                    color:'black',
                                });
                                that.pointElevationData.elevation =  results[0].elevation.toFixed(3) + " meters.";
                            } else {
                                that.pointElevationData.elevation =  "No results found";
                            }
                        } else {
                            that.pointElevationData.elevation =  "Elevation service failed due to: " + status;
                        }
                    }
                );

                //-----------run reverse geocode---------------------------------------------------------------------
                let geocoder = new google.maps.Geocoder;

                geocoder.geocode({'location': {lat: lat, lng: lng}}, function (results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                           that.pointElevationData.address = results[0].formatted_address;
                        }
                    }
                });
            });
            store.commit('addToMapContextMenu', {title: 'Elevation Profile', id: 'elevation_profile', action: {type: 'navigation', drawer: 'right', drawerID: 'mapToolBar', tabID: 'elevation_profile', tabNo: 0}});
        },
        //---------------------clear elevation analysis-----------------------------------------
        clearElevationAnalysis(){
            this.elevationData = [];
            this.elevationData = null;
            this.isdrawStarted = false;
            this.isLineElevation = false;
            this.isPointElevation = false;
            this.drawingManager.setDrawingMode(null);
            this.pointElevationData = {lat:'', lng:'', address:'', elevation:''};
            if (this.polyLine)
                this.polyLine.setMap(null);
            if (this.selectedMarker)
                this.selectedMarker.setMap(null);
            if (this.chartMarker){
                this.chartMarker.setMap(null);
            }
            store.commit('removeFromMapContextMenu', {id:'elevation_profile'});
        },
        setElavationMarkerOnChartClick(dataset){
            var location = new google.maps.LatLng(dataset.location.lat(),dataset.location.lng());
            let map = store.state.gmap;
            if (this.chartMarker){
                this.chartMarker.setMap(null);
            }
            this.chartMarker = new google.maps.Marker({
                position: location,
                icon:{
                    url: '/storage/img/icons/administrativeboundary.png',
                    size: new google.maps.Size(25, 25),
                    anchor: new google.maps.Point(9, 14),
                    origin: new google.maps.Point(0, 0),
                    scaledSize: new google.maps.Size(20, 20),
                    labelOrigin: {x: 0, y: -10}
                },
                label:{
                    text:dataset.elevation.toFixed(3) + "m",
                    fontSize: "12px",
                    fontWeight: "bold",
                    color:'black',
                }
            });
            this.chartMarker.setMap(map);
        }
    }
}
</script>
