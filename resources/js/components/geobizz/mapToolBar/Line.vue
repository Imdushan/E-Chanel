<template>
    <div >
        <v-tabs v-model="tabLayer">
            <v-tab>New Dataset</v-tab>
            <v-tab>Load Dataset</v-tab>

            <v-tabs-items v-model="tabLayer">
                <!--      New Dataset          -->
                <v-tab-item >
                    <v-card style="margin: 5px" flat height="60">
                        <v-card-subtitle class="caption">click on the layer to add attribute</v-card-subtitle>
                        <v-row style="margin: 5px 5px 10px 5px">
                            <v-btn color="primary" style="margin-left: 10px" x-small @click="startDrawing">Draw</v-btn>
                            <v-btn style="margin-left: 5px" color="purple"  dark @click="showMarkerStyleWindow" x-small>Style</v-btn>
                            <v-btn style="margin-left: 5px" color="error" dark  @click="clearDrawing" x-small>Clear</v-btn>
                        </v-row>

                            <v-row style="margin-left:5px;margin-right:5px;" v-if="selectedLayer">
                                <v-col cols="12"  lg="5" sm="12">
                                    <v-text-field dense label="Attribute"  v-model="selectedAttribute" hide-details style="font-size: 12px !important;"></v-text-field>
                                </v-col>
                                <v-col cols="12"  lg="5" sm="12">
                                    <v-text-field dense label="Value"  v-model="selectedValue" hide-details style="font-size: 12px !important;"></v-text-field>
                                </v-col>
                                <v-col  lg="1" sm="12" style="margin-top: 10px">
                                    <v-btn x-small icon @click="addAttributeToList" :disabled="!selectedAttribute || selectedAttribute === '' && !selectedValue || selectedValue === '' "> <v-icon>add</v-icon></v-btn>
                                </v-col>
                            </v-row>
                    </v-card>

                    <v-card style="margin: 35px 5px 10px 5px;" flat height="200" >
                        <v-simple-table   style="width: 100% !important;overflow-y: scroll;height: 100px;overflow-x: hidden !important"  class="scroller">
                            <thead>
                                <tr>
                                    <th style="height:35px !important;" class="overline" >Attribute</th>
                                    <th style="height:35px !important;" class="overline" >Value</th>
                                    <th style="height:35px !important;" class="overline" >Action</th>
                                </tr>
                            </thead>
                            <tbody class="caption " >
                                <tr v-for="(attr,key) in attrList" :key="'mapIcons'+key">
                                    <td style="height:25px !important;">{{attr.attribute}}</td>
                                    <td style="height:25px !important;">{{ attr.value }}</td>
                                    <td style="height:25px !important;">
                                         <v-icon small color="error" @click="deleteAttribute(key)">delete</v-icon>
                                    </td>
                                </tr>
                            </tbody>
                        </v-simple-table>

                        <v-row style="margin: 10px">
                            <v-col lg="8" md="8">
                                <v-text-field dense label="Dataset Name"  :rules="[() => !!layerNameAvailable || 'layer name already exists.try different']" v-model="layerName"  style="font-size: 12px !important;"></v-text-field>
                            </v-col>
                            <v-col lg="4" md="4">
                                <v-btn style="margin-top: 5px" x-small @click="saveLayer" :loading="saveLoader" color="primary" :disabled="!layerName || layerName === '' || layerList.length === 0 || !layerNameAvailable"> save</v-btn>
                            </v-col>
                        </v-row>

                    </v-card>
                </v-tab-item>
                <!--      Load Dataset          -->
                <v-tab-item>
                    <v-card style="margin: 5px" flat height="60">
                        <!--                    <fieldset>-->
                        <!--                        <legend>Saved Layers</legend>-->
                        <v-row style="margin: 5px 5px 10px 5px">
                            <v-select class="teboxr" outlined dense label="layer" clearable :items="savedLayersList" v-model="selectedLayerName" @change="setSelectedSavedLayer(selectedLayerName)"></v-select>
                        </v-row>
                        <!--                    </fieldset>-->
                    </v-card>
                    <v-card style="margin: 20px 5px 10px 5px;" flat height="200" >
                        <v-list style="overflow-y: scroll;height: 160px;overflow-x: hidden !important" class="scroller">
                            <v-list-item v-for="(layer,key) in selectedLayerDataset" :key="'layers'+key" >
                                <v-list-item-action>
                                    <v-checkbox @change="showHideLayers(layer.name,$event)" :input-value="true"></v-checkbox>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{layer.name}}</v-list-item-title>
                                    <v-list-item-subtitle>{{layer.features.length}} features &nbsp;&nbsp; <a href="#" style="color: red" @click="removeLayer(key)" >Remove</a></v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
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
    name: "mapPolyLines",
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
            selectedLayerName:''
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
            if (action.drawerID === 'mapToolBar' && action.tabID === 'map_data_line'){
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

                    //------------------calculating bearing-----------------------------------------------
                    let pointA = e.overlay.getPath().getAt(0);
                    let pointB = e.overlay.getPath().getAt(e.overlay.getPath().getLength() - 1);
                    let heading = google.maps.geometry.spherical.computeHeading(pointA, pointB);
                    console.log("heading",heading);
                    if (heading < 0) {
                        heading = 360 + heading;
                    }


                    that.attrList = [{parentID:e.overlay.id,attribute:'length',value:kmdistance.toFixed(2)+' Km'},{parentID:e.overlay.id,attribute:'bearing',value:heading.toFixed(2)+'°'}];
                    e.overlay.properties = that.attrList;
                    that.layerList.push(e.overlay);
                    that.selectedLayer = e.overlay;
                    //-------------------creating google mp feature for map data layer (later this will save as json)---
                    let feature = new google.maps.Data.Feature({
                        properties: {strokeColor: e.overlay.get('strokeColor'),strokeWeight: e.overlay.get('strokeWeight')},
                        geometry: new google.maps.Data.LineString(e.overlay.getPath().getArray()),
                        id:e.overlay.id
                    });

                    //-------------------------------------adding layer to map------------------------------------------
                    map.data.add(feature);
                    //-----------------hiding feature.because it is overlapping the layer-------------------------------
                    map.data.overrideStyle(feature, {visible: false,});
                    //-----------------------------------click listener for save layer----------------------------------
                    google.maps.event.addListener(e.overlay, 'click', function () {
                        that.selectedLayer = e.overlay;
                        let layerDataset =  _.find(that.layerList,{'id':e.overlay.id});
                        that.attrList =   layerDataset.properties;
                    });

            });

        },

        addAttributeToList(){
            this.attrList.push({
                parentID:this.selectedLayer.id,
                attribute:this.selectedAttribute,
                value:this.selectedValue,
            });
            let index = _.findIndex(this.layerList,{'id':this.selectedLayer.id});
            this.layerList[index].properties = this.attrList;
            this.selectedAttribute = '';
            this.selectedValue = '';
        },

        startDrawing(){
            this.drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYLINE);
            this.startDraw = true;
        },

        clearDrawing(){
            this.startDraw = false;
            $.each(this.layerList,function (key,val) {
                val.setMap(null);
            });
        },

        deleteAttribute(index){
            this.attrList.splice(index,1);
        },

        saveLayer(){
            let map = store.state.gmap;
            let that = this;
            this.saveLoader = true;
            //====================creating drawed map data to the string using JSON.stringfy===================
            map.data.toGeoJson(function (obj) {
                //---------------------applying attributes to the layers-----------------------------------------------------
                for (let i = 0; i < obj.features.length; i++) {
                    let index = _.findIndex(that.layerList,{'id':obj.features[i].id});
                    if (index > -1){
                        obj.features[i].properties['ID'] = obj.features[i].id;
                        console.log("fucking attributes",that.layerList[index]);
                        $.each(that.layerList[index].properties,function (key,val) {
                            obj.features[i].properties[val.attribute] = val.value;
                        });
                    }
                    // map.data.remove(obj.features[i]);
                }
                //---------------------removing unnecceesary layers-----------------------------------------------------
                let newFeature = _.reject(obj.features, (o) => {
                    let exists = _.find(that.layerList,{'id':o.id});
                    if (!exists)
                        return  true;
                });
                obj.features = newFeature;
               console.log("fooking to json",obj);
               that.sendJsonToBackEnd(obj);

            });
        },

        sendJsonToBackEnd(json){
            let formData = new FormData();
            formData.append("uid", store.state.httpheader.user.id);
            formData.append("layername", this.layerName);
            formData.append("layerType", 'polyline');
            formData.append("jsonData", JSON.stringify(json));
            axios.post('/api/dataOperator/mapDataLayers/geojson/save', formData, {})
                .then(response => {
                    console.log("layer saved", response.data);
                    if (response.data.http_status === 'success') {
                        this.snackColor = 'success';
                        this.savedLayersList = response.data.layer_list;
                        this.savedLayersData = response.data.layerSet;
                        this.clearData();
                    }else{
                        this.snackColor = 'error';
                    }
                    this.saveLoader = false;
                    this.snackMessage = response.data.message
                    this.snackbar = true;
                    store.commit('removeFromMapContextMenu', {id:'map_data_line'});
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

        clearData(){
            let map = store.state.gmap;
            let that = this;
            this.clearDrawing();
            this.attrList = [];
            map.data.forEach(function (feature) {
                let index = _.findIndex(that.layerList,{'id':feature.o});
                if (index > -1){
                    map.data.remove(feature);
                }
            });
            this.layerList = [];
            this.selectedAttribute = '';
            this.selectedValue = '';
            this.layerName = '';
            store.commit('removeFromMapContextMenu', {id:'map_data_line'});
        },

        showMarkerStyleWindow(){
            store.commit('setMapStyleWindow',true);
        },
        setSelectedSavedLayer(layer){
            let map = store.state.gmap;
            let that = this;
            let getLayerDetails = _.find(this.savedLayersData,{'name':layer});

            // $.each(that.selectedLayerDataset,function (key,val) {
            //     map.data.remove(val);
            // })
            let exists = _.find(that.selectedLayerDataset,{'name':layer});
            if (layer && !exists) {
                $.getJSON(getLayerDetails.url, function (data) {
                    let features = map.data.addGeoJson(data);
                    console.log(features);
                    that.selectedLayerDataset.push({
                        name:layer,
                        features:features
                    });
                    $.each(features, function (key, val) {

                        if (val.j.strokeColor) {
                            //-----------------styling the feature-------------------------------
                            map.data.overrideStyle(val, {
                                strokeColor: val.j.strokeColor,
                                strokeWeight: val.j.strokeWidth,
                            });
                        }
                        //-------------------applying click event listener----------------------------------------------
                        map.data.addListener('click', function(event) {
                            let properties = event.feature.j;
                            let result = _.omit(properties,['strokeColor','strokeWeight']);
                            console.log("fucking feature",properties)
                            if (properties.ID === val.j.ID)
                                store.commit('setMyInfoWindow',{lat:event.latLng.lat(),lng:event.latLng.lng(),data:result,key:result.keycount,type:'JSON File'});
                        });
                    });

                });
            }
        },

        showHideLayers(layerName,show){
            let map = store.state.gmap;
            let layer = _.find(this.selectedLayerDataset,{'name':layerName});
            if (layer){
                if (show){
                    $.each(layer.features, function (key, val) {
                            //-----------------show the feature-------------------------------
                            map.data.overrideStyle(val, {visible: true,});

                    })
                }else{
                    $.each(layer.features, function (key, val) {
                            //-----------------show the feature-------------------------------
                            map.data.overrideStyle(val, {visible: false,});

                    })
                }

            }
        },

        removeLayer(key){
            let map = store.state.gmap;
          let layer =   this.selectedLayerDataset[key];
            $.each(layer.features,function (key,val) {
                map.data.remove(val);
            });
            this.selectedLayerDataset.splice(key,1);
            store.commit('removeFromMapContextMenu', {id:'map_data_line'});
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
