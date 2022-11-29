<template>
    <div >
        <v-tabs v-model="tabPoint">
            <v-tab>New Dataset</v-tab>
            <v-tab>Load Dataset</v-tab>

            <v-tabs-items v-model="tabPoint">
            <v-tab-item >
                <v-card style="*margin: 1px" flat height="60" >
                    <fieldset style="margin-left: 8px;margin-right: 8px;height: 66px">
                        <legend style="font-size: 0.8em;margin-left: 4px">Add Point to List</legend>
                        <v-row style="margin-left:5px;margin-right:5px;" v-if="startDraw">
                            <v-col cols="12"  lg="2" sm="12">
                                <v-text-field dense label="SRNo" v-model="SRNo" hide-details style="font-size: 12px !important;"></v-text-field>
                            </v-col>
                            <v-col  lg="4" sm="12">
                                <v-text-field dense label="name" v-model="name" hide-details style="font-size: 12px !important;"></v-text-field>
                            </v-col>
                            <v-col  lg="4" sm="12">
                                <v-text-field dense label="details" v-model="detail" hide-details style="font-size: 12px !important;"></v-text-field>
                            </v-col>
                            <v-col  lg="1" sm="12" style="margin-top: 10px">
                                <v-btn x-small icon @click="addMarkerToList" :disabled="!SRNo || SRNo === '' && !name || name === '' "> <v-icon>add</v-icon></v-btn>
                            </v-col>
                        </v-row>
                        <v-row style="margin: 5px 5px 10px 5px" v-else>
                            <v-btn color="primary" style="margin-left: 1px" x-small @click="startDrawing">Draw New Marker</v-btn>

                            <v-btn style="margin-left: 10px" color="purple" dark @click="showMarkerStyleWindow" x-small>set icon style</v-btn>
                        </v-row>
                    </fieldset>
                </v-card>
                <v-card style="margin: 20px 10px 5px 5px;" flat height="200" >
                    <v-simple-table   style="width: 100% !important;overflow-y: scroll;height: 100px;overflow-x: hidden !important"  class="scroller">
                        <thead>
                        <tr>
                            <th style="height:35px !important;" class="overline" >SRNo</th>
                            <th style="height:35px !important;" class="overline" >Name</th>
                            <th style="height:35px !important;" class="overline" >Details</th>
                            <th style="height:35px !important;" class="overline" >Action</th>
                        </tr>
                        </thead>
                        <tbody class="caption " >
                        <tr v-for="(icons,key) in markersList" :key="'mapIcons'+key">
                            <td style="height:25px !important;">{{icons.SRNo}}</td>
                            <td style="height:25px !important;">{{ icons.name }}</td>
                            <td style="height:25px !important;">{{icons.detail}}</td>
                            <td style="height:25px !important;">
                                <v-icon small color="primary" @click="gotoXY(icons.lat,icons.lng,18.5)">my_location</v-icon>
                                &nbsp; <v-icon small color="error" @click="deleteIcon(key)">delete</v-icon>
                            </td>
                        </tr>
                        </tbody>
                    </v-simple-table>

                    <v-row style="margin: 10px">
                        <v-col lg="8" md="8">
                            <v-text-field dense label="Dataset Name"  :rules="[() => !!datasetNameAvailable || 'dataset name already exists.try different']" v-model="datasetName"  style="font-size: 12px !important;"></v-text-field>
                        </v-col>
                        <v-col lg="4" md="4">
                            <v-btn x-small @click="saveDataset" color="primary" :disabled="!datasetName || datasetName === '' || markersList.length === 0 || !datasetNameAvailable"> save</v-btn>
                        </v-col>
                    </v-row>

                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card style="margin: 5px" flat height="60">
<!--                    <fieldset>-->
<!--                        <legend>Saved Layers</legend>-->
                        <v-row style="margin: 5px 5px 5px 5px">
                           <v-select STYLE="height: 20PX" class="tebox" outlined dense label="layer" clearable :items="savedLayersList" v-model="selectedLayerDataset" @change="setSelectedSavedLayer(selectedLayerDataset)"></v-select>
                        </v-row>
<!--                    </fieldset>-->
                </v-card>
                <v-card style="margin: 20px 5px 10px 5px;" flat height="200" >
                    <v-simple-table   style="width: 100% !important;overflow-y: scroll;height: 160px;overflow-x: hidden !important"  class="scroller">
                        <thead>
                        <tr>
                            <th style="height:35px !important;" class="overline" >SRNo</th>
                            <th style="height:35px !important;" class="overline" >Name</th>
                            <th style="height:35px !important;" class="overline" >Details</th>
                            <th style="height:35px !important;" class="overline" >Action</th>
                        </tr>
                        </thead>
                        <tbody class="caption " >
                        <tr v-for="(icons,key) in selectedSavedLayer" :key="'mapIcons'+key">
                            <td style="height:25px !important;">{{icons.srno}}</td>
                            <td style="height:25px !important;">{{ icons.name }}</td>
                            <td style="height:25px !important;">{{icons.details}}</td>
                            <td style="height:25px !important;">
                                <v-icon x-small color="primary"  @click="gotoXY(icons.lat,icons.lng,18.5)">my_location</v-icon>
<!--                                &nbsp; <v-icon small color="error" @click="deleteIcon(key)">delete</v-icon>-->
                            </td>
                        </tr>
                        </tbody>
                    </v-simple-table>

<!--                    <v-row style="margin: 10px">-->
<!--                        <v-col lg="8" md="8">-->
<!--                            <v-text-field dense label="Dataset Name" v-model="datasetName" hide-details style="font-size: 12px !important;"></v-text-field>-->
<!--                        </v-col>-->
<!--                        <v-col lg="4" md="4">-->
<!--                            <v-btn small @click="saveDataset" color="primary" :disabled="!datasetName || datasetName === '' || markersList.length === 0 "> save</v-btn>-->
<!--                        </v-col>-->
<!--                    </v-row>-->

                </v-card>
            </v-tab-item>
        </v-tabs-items>

        </v-tabs>

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
    name: "mapPoints",
    components: {
        store,
    },
    data() {
        return {
            SRNo:'',
            name:'',
            detail:'',
            GMap:null,
            startDraw:false,
            drawingManager:null,
            styleIconWindow: true,
            selectedMarker:null,
            markersList:[],
            datasetList:[],
            datasetName:'',
            tabPoint:0,
            saveLoader:false,
            snackbar:false,
            snackColor:'',
            snackMessage:'',
            snackBarRight:false,
            snackBarTop:false,
            savedLayersData:[],
            selectedSavedLayer:[],
            savedLayersList:[],
            selectedLayerDataset:'',
            datasetNameAvailable:true,
        }
    },
    computed: {
        ...mapState({
            gmap: state => state.gmap,
            mapLayerStyle: state => state.mapLayerStyle,
            viewMapStyleWindow: state => state.viewMapStyleWindow,
            httpheader: state => state.httpheader,
            mapContextMenu: state => state.mapContextMenu,
            contextMenuAction: state => state.contextMenuAction,
        }),
        getMap(){
           return  store.state.gmap;
        },
        iconStyle(){
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
        datasetName(data){
            let exists = _.find( this.savedLayersData,{'dataset':data});
            exists?this.datasetNameAvailable = false:this.datasetNameAvailable = true;
        },
        iconStyle(data){
            console.log("fucking data",data);
            if (data.icon){
                let map = store.state.gmap;
                this.drawingManager.setOptions({
                    markerOptions: {
                        draggable: false,
                        optimized: false,
                        icon: data.icon
                    },
                });
            }
        },

        mapContextAction(action){
            if (action.drawerID === 'mapToolBar' && action.tabID === 'map_data_point'){
                this.tabLayer = action.tabNo;
                store.commit('resetContextMenuAction',true);
            }
        }
    },
    methods:{
        //----------------------------------------loading layers from back-end--------------------------------------
        loadLayerData(){
            axios.post('/api/dataOperator/mapDataLayers/points/get',{},store.state.httpheader.header).then(response => {
                console.log(response);
                this.savedLayersList = response.data.dataset_list;
                this.savedLayersData = response.data.markerSet;
            });
        },
        //------------------------------------------set selectedSavedLayer----------------------------------------------
        setSelectedSavedLayer(dataset){
            if(dataset) {
                this.selectedSavedLayer = _.filter(this.savedLayersData, {'dataset': dataset})
                let newDataset = [];
                let that = this;
                $.each(this.selectedSavedLayer, function (key, val) {
                    if (!val.icon && val.color && val.size && parseFloat(val.size)) {
                        val.icon = that.setMapIcon(val.color, parseFloat(val.size));
                    }
                    newDataset.push(val);
                });
                store.commit('setCustomPointLayer', newDataset);
                store.commit('addToMapContextMenu', {title: 'Point Layer', id: 'map_data_point', action: {type: 'navigation', drawer: 'right', drawerID: 'mapToolBar', tabID: 'map_data_point', tabNo: 1}});
            }else{
                this.selectedSavedLayer = [];
                store.commit('setCustomPointLayer', []);
                store.commit('removeFromMapContextMenu', {id:'map_data_point'});
            }
        },
        //=========================================================Set Map Search and other init=====================
        init() {
            let that = this;
            let map = store.state.gmap;

            // Create the DIV to hold the control and
            // call the HomeControl() constructor passing
            // in this DIV.
            let homeControlDiv = document.createElement('div');

            this.drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: null,
                drawingControl: false,
                markerOptions: {
                    draggable: false,
                    optimized: false,
                    icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
                },
            });
            this.drawingManager.setMap(map);


            google.maps.event.addListener(that.drawingManager, 'overlaycomplete', function (e) {
                console.log("points",e);
                    // Switch back to non-drawing mode after drawing a shape.
                    that.drawingManager.setDrawingMode(null);
                    that.selectedMarker = e.overlay;

            });

        },
        //=========================================================start marker drawing=====================
        startDrawing(){
            this.startDraw = true;
            this.drawingManager.setDrawingMode(google.maps.drawing.OverlayType.MARKER);
            store.commit('addToMapContextMenu', {title: 'Point Layer', id: 'map_data_point', action: {type: 'navigation', drawer: 'right', drawerID: 'mapToolBar', tabID: 'map_data_point', tabNo: 0}});
        },
        //=========================================================Add new marker to list=====================
        addMarkerToList(){
            // console.log(this.selectedMarker.position.lat());
            this.markersList.push({
               SRNo:this.SRNo,
               name:this.name,
               detail:this.detail,
               marker:this.selectedMarker,
               markerType:this.markerType,
               color:store.state.mapLayerStyle.color,
               size:store.state.mapLayerStyle.size,
               lat:this.selectedMarker.position.lat(),
               lng:this.selectedMarker.position.lng(),
               icon:store.state.mapLayerStyle.icon?store.state.mapLayerStyle.icon:'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
            });

            this.datasetList.push({
               SRNo:this.SRNo,
               name:this.name,
               detail:this.detail,
               markerType:store.state.mapLayerStyle.markerType?store.state.mapLayerStyle.markerType:'circle',
               color:store.state.mapLayerStyle.color,
               size:store.state.mapLayerStyle.size,
               lat:this.selectedMarker.position.lat(),
               lng:this.selectedMarker.position.lng(),
               iconURL:store.state.mapLayerStyle.icon?store.state.mapLayerStyle.iconURL:'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
            });


            this.startDraw = false;
            this.SRNo = "";
            this.name = "";
            this.detail = "";
        },
        //========================================================Save layers====================
        saveDataset(){
                this.saveLoader = true;
                let formData = new FormData();
                formData.append("uid", store.state.httpheader.user.id);
                formData.append("dataset", this.datasetName);
                formData.append("marker_array", JSON.stringify(this.datasetList));

                axios.post('/api/dataOperator/mapDataLayers/points/save', formData, {})
                    .then(response => {
                        console.log("user districts", response.data);

                        if (response.data.http_status === 'success') {
                            this.snackColor = 'success';
                            for (let i=0;i<this.markersList.length;i++){
                                this.markersList[i].marker.setMap(null);
                            }
                            this.datasetList = [];
                            this.markersList = [];
                            this.datasetName = '';
                            this.savedLayersList = response.data.dataset_list;
                            this.savedLayersData = response.data.markerSet;
                        } else {
                            this.snackColor = 'error';
                        }

                        this.snackMessage = response.data.message
                        this.snackbar = true;

                        this.saveLoader = false;
                        store.commit('removeFromMapContextMenu', {id:'map_data_point'});
                    }).catch((error) => {
                    if (error.response.status === 401){
                        window.setLogout();
                    }
                    this.snackMessage = error;
                    this.snackColor = 'error';
                    this.snackbar = true;
                    this.saveLoader = false;
                    console.log(error);// this will log an empty object with an error property
                });
        },
        //========================================================styling the marker icon and size===================
        showMarkerStyleWindow(){
            store.commit('setMapStyleWindow',true);
        },
        resetIcon () {
            this.iconIndex = 0
        },
        deleteIcon(index){
            this.markersList[index].marker.setMap(null);
            this.markersList.splice(index,1);
        },
        gotoXY(lat,lng,zoom){
            let map = store.state.gmap;
            var ltlng = {lat: parseFloat(lat), lng:parseFloat(lng)};

            map.setCenter(ltlng);
            // call smoothZoom, parameters map, final zoomLevel, and starting zoom level
            // this.smoothZoom(map, 25, map.getZoom());
            map.setZoom(zoom);
        },
        //--------------------------setting map icon programmetically------------------------------
        setMapIcon(color,scale){
            // if (!color){
            //     color = '#FF0000';
            // }
            return {
                path: google.maps.SymbolPath.CIRCLE,
                scale: scale,
                fillColor: color,
                fillOpacity: 1,
                strokeWeight: 0.3,
                labelOrigin: {x: -7, y: 0}
            }
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
    scrollbar-color: #2b2b2b;
    scrollbar-width: thin;
}
.scroller::-webkit-scrollbar {
    width: 0.5px;
    border: none !important;
}
.scroller::-webkit-scrollbar-track {
    background: #9E9E9E;
    border: none !important;
}
.scroller::-webkit-scrollbar-thumb {
    background: #2b2b2b;
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
/* Floating panel style   */
.window{
    z-index: 9999 !important;
}
.window > .content {
    background-color: white;
}
</style>
