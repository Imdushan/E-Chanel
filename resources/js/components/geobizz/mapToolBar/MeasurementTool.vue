<template>
    <div >


                    <v-card style="margin: 5px" flat height="80">
                        <v-row style="margin: 5px 5px 10px 5px">
                            <v-btn color="primary" style="margin-left: 1px" x-small @click="startDrawing">Draw Line</v-btn>
<!--                            <v-btn style="margin-left: 5px" color="purple"  dark @click="showMarkerStyleWindow" x-small>set line style</v-btn>-->
                            <v-btn style="margin-left: 5px" color="error" dark  @click="clearDrawing" x-small>clear</v-btn>
                        </v-row>

                        <v-list v-if="selectedLayer">

                                       <v-list-item dense>
                                           <v-list-item-content>
                                           <v-list-item-title>
                                               Length (m)
                                           </v-list-item-title>
                                           </v-list-item-content>
                                           <v-list-item-action-text>
                                               {{Linelength.meter}}
                                           </v-list-item-action-text>
                                       </v-list-item>

                                       <v-list-item dense>
                                           <v-list-item-content>
                                           <v-list-item-title>
                                               Length (Km)
                                           </v-list-item-title>
                                           </v-list-item-content>
                                           <v-list-item-action-text>
                                               {{Linelength.kilometer}}
                                           </v-list-item-action-text>
                                       </v-list-item>

                                       <v-list-item dense>
                                           <v-list-item-content>
                                           <v-list-item-title>
                                               Length (yard)
                                           </v-list-item-title>
                                           </v-list-item-content>
                                           <v-list-item-action-text>
                                               {{Linelength.yard}}
                                           </v-list-item-action-text>
                                       </v-list-item>

                                       <v-list-item dense>
                                           <v-list-item-content>
                                           <v-list-item-title>
                                               Length (feet)
                                           </v-list-item-title>
                                           </v-list-item-content>
                                           <v-list-item-action-text>
                                               {{Linelength.foots}}
                                           </v-list-item-action-text>
                                       </v-list-item>

                                       <v-divider></v-divider>

                                       <v-list-item dense>
                                           <v-list-item-content>
                                           <v-list-item-title>
                                               Bearing
                                           </v-list-item-title>
                                           </v-list-item-content>
                                           <v-list-item-action-text>
                                               {{LineBearing}}
                                           </v-list-item-action-text>
                                       </v-list-item>


                               </v-list>

                    </v-card>



        <v-snackbar v-model="snackbar" :multi-line="false" :color="snackColor" :top="snackBarTop" :right="snackBarRight">
            {{ snackMessage }}
        </v-snackbar>
    </div>

</template>
<script>


import {store} from "../../store/store_geobizz";
import {mapState} from "vuex";
import $ from 'jquery';
export default {
    name: "measurementTool",
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
            GMap:null,
            tabLayer:0,
            startDraw:false,
            selectedAttribute:'',
            selectedValue:'',
            layerNameAvailable:true,
            layerName:'',
            attrList:[],
            snackbar:false,
            snackColor:'',
            snackMessage:'',
            snackBarRight:false,
            snackBarTop:false,
            drawingManager:null,
            selectedLayer:null,
            layerList:[],
            savedLayersData:[],
            selectedLayersData:[],
            savedLayersList:[],
            saveLoader:false,
            selectedLayerDataset:[],
            selectedLayerName:'',
            Linelength:{
                meter:'',
                kilometer:'',
                yard:'',
                foots:'',
                inch:'',
            },
            LineBearing:''
        }
    },
    computed: {
        ...mapState({
            gmap: state => state.gmap,
            httpheader: state => state.httpheader,
            mapContextMenu: state => state.mapContextMenu,
            contextMenuAction: state => state.contextMenuAction,
        }),
        getMap(){
           return  store.state.gmap;
        },
        layerStyle(){
            return store.state.mapLayerStyle;
        },
        mapContextAction(){
            return store.state.contextMenuAction;
        },

    },
    mounted() {
        this.GMap = store.state.gmap;
        this.loadLayerData();
    },
    watch:{
        getMap(map){
            // console.log("fucking map",map);
            if (map){
                this.init();
            }
        },
        layerStyle(data){
            console.log("fucking data",data);
            // if (data.icon){
            //     let map = store.state.gmap;
                this.drawingManager.setOptions({
                    polylineOptions: {
                        draggable: false,
                        optimized: false,
                        strokeColor: data.color,
                        strokeWeight: data.size
                    },
                });
            // }
        },
        layerName(data){
            let exists = _.find( this.savedLayersData,{'name':data});
            exists?this.layerNameAvailable = false:this.layerNameAvailable = true;
        },

        mapContextAction(action){
            if (action.tabID === 'map_data_distance_cal'){
                this.tabLayer = action.tabNo;
                store.commit('resetContextMenuAction',true);
            }
        }
    },
    methods:{
        //----------------------------------------loading layers from back-end--------------------------------------
        loadLayerData(){
            axios.post('/api/dataOperator/mapDataLayers/geojson/get',{type:'polyline'},store.state.httpheader.header).then(response => {
                console.log(response);
                this.savedLayersList = response.data.layer_list;
                this.savedLayersData = response.data.layerSet;
            });
        },
        //=========================================================Set Map Search and other init========================
        init () {

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

            google.maps.event.addListener(that.drawingManager, 'overlaycomplete', function (e) {
                    //Switch back to non-drawing mode after drawing a shape.
                    that.drawingManager.setDrawingMode(null);
                    //Add an event listener that selects the newly-drawn shape when the user
                    e.overlay.id = Math.floor(Math.random() * 9999) + 1;
                    e.overlay.properties = [];
                    console.log("polyline ",e);


                    //------------------calculating distance-----------------------------------------------
                    let distance = google.maps.geometry.spherical.computeLength(e.overlay.getPath().getArray());
                    let kmdistance = distance/1000;
                    let ydistance = distance*1.09361;
                    let footdistance = distance*3.28084;
                    let inchdistance = distance*39.3701;

                    //------------------calculating bearing-----------------------------------------------
                    let pointA = e.overlay.getPath().getAt(0);
                    let pointB = e.overlay.getPath().getAt(e.overlay.getPath().getLength() - 1);
                    let heading = google.maps.geometry.spherical.computeHeading(pointA, pointB);
                    console.log("heading",heading);
                    if (heading < 0) {
                        heading = 360 + heading;
                    }

                    that.Linelength.meter = distance.toFixed(2)+" m";
                    that.Linelength.kilometer = kmdistance.toFixed(2)+" km";
                    that.Linelength.yard = ydistance.toFixed(2);
                    that.Linelength.foots = footdistance.toFixed(2);
                    that.Linelength.inch = inchdistance.toFixed(2);
                    that.LineBearing = heading.toFixed(2)+'°';

                    that.attrList = [{parentID:e.overlay.id,attribute:'length',value:kmdistance.toFixed(2)+' Km'},{parentID:e.overlay.id,attribute:'bearing',value:heading.toFixed(2)+'°'}];
                    e.overlay.properties = that.attrList;
                    that.layerList.push(e.overlay);
                    that.selectedLayer = e.overlay;
                    //-----------------------------------click listener for save layer----------------------------------
                    google.maps.event.addListener(e.overlay, 'click', function () {
                        store.commit('setContextMenuAction',{type: 'navigation', drawer: 'right', drawerID: 'mapToolBar', tabID: 'map_data_distance_cal', tabNo: 0});
                    });

            });

        },



        startDrawing(){
            this.drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYLINE);
            this.startDraw = true;
            store.commit('addToMapContextMenu', {title: 'Distance Calculator', id: 'map_data_distance_cal', action: {type: 'navigation', drawer: 'right', drawerID: 'mapToolBar', tabID: 'map_data_distance_cal', tabNo: 0}});
        },

        clearDrawing(){
            this.startDraw = false;
            $.each(this.layerList,function (key,val) {
                val.setMap(null);
            });
            this.Linelength = {meter:'', kilometer:'', yard:'', foots:'', inch:'',};
            this.LineBearing = '';
            store.commit('removeFromMapContextMenu', {id:'map_data_distance_cal'});
        },

        deleteAttribute(index){
            this.attrList.splice(index,1);
        },



        showMarkerStyleWindow(){
            store.commit('setMapStyleWindow',true);
        },



        gotoXY(lat,lng,zoom){
            let map = store.state.gmap;
            var ltlng = {lat: parseFloat(lat), lng:parseFloat(lng)};

            map.setCenter(ltlng);
            // call smoothZoom, parameters map, final zoomLevel, and starting zoom level
            // this.smoothZoom(map, 25, map.getZoom());
            map.setZoom(zoom);
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
/*.v-dialog:not(.v-dialog--fullscreen) {*/
/*    position: absolute;*/
/*    bottom: -4% !important;*/
/*}*/
.v-label {
    font-size: 0.8em !important;
    color: black !important;
}
</style>
