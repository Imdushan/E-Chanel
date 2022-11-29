<template>
    <div >


                    <v-card style="margin: 5px" flat height="80">


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
    name: "Demographic",
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


            savedLayersList:[],
            saveLoader:false,


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



        startDrawing(){
            this.startDraw = true;
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

            google.maps.event.addListener(that.drawingManager, 'overlaycomplete', function (e) {
                console.log("demographic points", e);
                // Switch back to non-drawing mode after drawing a shape.
                that.drawingManager.setDrawingMode(null);
                that.selectedMarker = e.overlay;
                let lat = e.overlay.position.lat();
                let lng = e.overlay.position.lng();

            });
        },
        //-------Query Demographic data from back-end----------------------------------------------------------
        async getDemographicFromBack(lat,lng) {

            let laravel = JSON.parse(window.Laravel);
            this.syncDemographic = [];
            this.syncDemographicProgress = true;
            const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            };

              let location = {"x":lng,"y":lat};
                await axios.post('/api/demographic/syncData', {
                    location: location,
                }, store.state.httpheader.header)
                    .then(response => {
                        console.log("demographic---",response.data);
                        if(response.data.data.results[0]['value']['FeatureSet'][0]) {
                            let attributes = response.data.data.results[0]['value']['FeatureSet'][0]['features'][0]['attributes'];
                            attributes.gnd = dataset[i].properties.GND_N;
                            attributes.status = 'success';
                            console.log(attributes);
                            // let totalPOP = attributes.TOTPOP;
                            // let totalFe = attributes.TOTFEMALES;
                            // let totalHouseHold = attributes.TOTHH;
                            // let totalMales = attributes.TOTMALES;
                            // let avgHouseHoldSize = attributes.AVGHHSZ;
                            // let apportienmentConf = attributes.apportionmentConfidence;
                            // let populationToPolygonSizeRating = attributes.populationToPolygonSizeRating;
                            this.syncDemographic.push(attributes);
                        }else{

                        }
                    }).catch((error) => {
                        console.log({error});// this will log an empty object with an error property
                    });


        },
        clearDrawing(){
            this.startDraw = false;

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
