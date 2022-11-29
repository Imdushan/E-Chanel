<template>
    <v-app id="app">
        <v-overlay :value="fullScreenLoader">
            <v-card-title>{{fullScreenLoadingText}} </v-card-title>
            <v-progress-circular
                indeterminate
                size="50"
            ></v-progress-circular>
        </v-overlay>
        <v-card>
           <!--  <v-app-bar color="white">-->
<!--                <v-avatar tile >-->
                <!--     <img
                        src="img/kpmg1.png"
                        alt="Sri Lanka"
                        style="height:100%;margin-right:10px"
                    >-->
<!--                </v-avatar>-->
        <!--        <v-toolbar-title> GEO Analysing </v-toolbar-title>
                <v-spacer></v-spacer>
                <span class="mr-3 font-weight-black">Welcome Admin </span>-->
<!--                <v-btn icon :to="{path:'/rdhs/dashboard'}"  >-->
<!--                    <v-icon  >dashboard</v-icon>-->
<!--                </v-btn>-->
<!--                <v-btn icon :to="{path:'/boundaryDashboard'}"  color="primary">-->
<!--                    <v-icon  >poll</v-icon>-->
<!--                </v-btn>-->


          <!--       <v-btn icon @click="logout()">
                    <v-icon>exit_to_app</v-icon>
                </v-btn>
            </v-app-bar>-->
        </v-card>

        <v-card style="margin-top: 25px" flat color="#EEEEEE">
                <v-row>
                    <v-col cols="12" lg="3">

                        <v-card raised  style =margin-left:10px>

                           <AdminBoundaries />

                        </v-card>

                        <v-card raised v-bind:style="{marginTop: 20+'px',marginLeft: 10+'px'}" >
                            <v-tabs>
                                <v-tab>Toolkit</v-tab>
                                <v-tab>Query</v-tab>
                                <!--                                <v-tab>Proximity</v-tab>-->
                                <v-tab-item>
                                    <v-card-text>
                                        <v-row>
                                            <v-col cols="12" lg="12">
                                                <v-text-field solo id="search_address" dense hide-details style="margin-top: 10px"  prepend-inner-icon="search" single-line></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <div style="margin-top: 30px">

                                            <v-row >
                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small id="add-event-MARKERbutton" @click="drawings.point = true">
                                                        <v-icon>mdi-map-marker-radius</v-icon>
                                                    </v-btn>
                                                </v-col>


                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small id="add-event-POLYLINEbutton" @click="drawings.line = true">
                                                        <v-icon>mdi-share-variant</v-icon>
                                                    </v-btn>
                                                </v-col>

                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small id="add-event-POLYGONbutton" @click="drawings.polygon = true">
                                                        <v-icon dark>mdi-chart-line</v-icon>
                                                    </v-btn>
                                                </v-col>

                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small id="add-event-RECTANGLEbutton" @click="drawings.rectangle = true">
                                                        <v-icon>mdi-crop-square</v-icon>
                                                    </v-btn>
                                                </v-col>

                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small id="add-event-CIRCLEbutton" @click="drawings.circle = true">
                                                        <v-icon>mdi-checkbox-blank-circle-outline</v-icon>
                                                    </v-btn>
                                                </v-col>

                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small @click="elevationWindow = true">
                                                        <v-icon>height</v-icon>
                                                    </v-btn>
                                                </v-col>

                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small @click="measurementWindow = true">
                                                        <v-icon>straighten</v-icon>
                                                    </v-btn>
                                                </v-col>

                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small  id="trafficLayer" @click="overLayTrafficLayer()">
                                                        <v-icon>mdi-traffic-light</v-icon>
                                                    </v-btn>
                                                </v-col>
                                                <v-col cols="2">
                                                    <v-btn color="white gray--text"  fab x-small  @click="print()">
                                                        <v-icon>print</v-icon>
                                                    </v-btn>
                                                </v-col>
                                               <!-- <v-col cols="12" lg="3">
                                                    <v-switch label="Label" style="margin-top: 2px" v-model="isLabeling"></v-switch>
                                                </v-col>

                                                <v-col cols="12" lg="3">
                                                    <v-switch label="zoom levels" style="margin-top: 2px" v-model="showClusters"></v-switch>
                                                </v-col> -->
                                            </v-row>
                                        </div>
                                    </v-card-text>
                                </v-tab-item>

                                <v-tab-item>
                                    <QueryWindow />
                                </v-tab-item>
                            </v-tabs>
                        </v-card>

                    </v-col>

                    <v-col cols="12" lg="9">

                                <v-card raised style="margin-right: 10px">
                                    <v-card-text>
                                        <VueHtml2pdf
                                            :show-layout="true"
                                            :float-layout="false"
                                            :enable-download="false"
                                            :preview-modal="true"
                                            :paginate-elements-by-height="96000"
                                            filename="KPMG"
                                            :pdf-quality="2"
                                            :manual-pagination="false"
                                            pdf-format="a4"
                                            pdf-orientation="landscape"
                                            pdf-content-width="100%"
                                            @progress="onProgress($event)"
                                            @beforeDownload="beforeDownload($event)"
                                            @hasDownloaded="hasDownloaded($event)"
                                            ref="html2Pdf"
                                        >
                                            <section slot="pdf-content">
                                                <GmapMap :options="mapOptions" :center="center" :zoom="7.95" :rotateControl="false" :disableDefaultUI="false" :streetViewControl="false" v-bind:style="{width:'100%',  height:window.height-150+'px'}" ref="mapDiv">
                                                    <GmapMarker v-for="(item, key) in gisLayer" :key="'GISlayer'+key"  :icon="item.icon" :position="getPosition(item,'GISLayers')" :clickable="true" @click="toggleInfoJsonMarker(item.lat,item.lng,item, key,'GISLayers')" />
                                                    <GmapMarker v-for="(item, key) in visibleUploadedData" :key="'ExcelLayer'+key"  :icon="item.icon" :position="getPosition(item,'ExcelLayer')" :clickable="true" @click="toggleInfoJsonMarker(item.lat,item.lng,item, key,'ExcelLayer')" />
                                                    <GmapCluster @click="clusterInfo()" v-if="showClusters">
                                                        <GmapMarker v-for="(item, key) in primaryData" :key="'Primary'+key" :label="isLabeling?''+item.serial:''" :icon="item.icon" :position="getPosition(item,'Primary')" :clickable="true" @click="toggleInfoJsonMarker(item.lat,item.lng,item, key,'Primary')" />
                                                    </GmapCluster>
                                                    <GmapMarker v-else v-for="(item, key) in primaryData" :key="'Primary'+key"  :label="isLabeling && item.No?''+item.No:''" :icon="item.icon" :position="getPosition(item,'Primary')" :clickable="true" @click="toggleInfoJsonMarker(item.lat,item.lng,item, key,'Primary')" />
                                                    <GmapMarker  v-for="(item, key) in queryPoints" :key="'Primary'+key"   :icon="item.icon" :position="getPosition(item,'QueryPoints')" :clickable="true" @click="toggleInfoJsonMarker(item.lat,item.lng,item, key,'QPoints')" />
                                                    <GmapMarker v-for="(item, key) in customPointDataLayer" :key="'customPointDataLayer'+key"  :icon="item.icon" :position="getPosition(item,'customPointDataLayer')" :clickable="true" @click="toggleInfoJsonMarker(item.lat,item.lng,item, key,'customPointDataLayer')" />
                                                    <GmapInfoWindow :options="infoOptions" :position="infoPosition" :opened="infoOpened" :content="infoContent"  @closeclick="infoOpened=false"></GmapInfoWindow>
                                                </GmapMap>
                                            </section>
                                        </VueHtml2pdf>
                                    </v-card-text>
                        </v-card>

                    </v-col>

                </v-row>
        </v-card>






        <v-snackbar v-model="snackbar" :multi-line="false" :color="snackColor" :top="snackBarTop" :right="snackBarRight">
            {{ snackMessage }}
        </v-snackbar>
    </v-app>
</template>


<script>
import {mapState} from "vuex";
import {store} from "../../store/store_geobizz";
import AdminBoundaries from "./AdminBoundary";
import GmapCluster from 'vue2-google-maps/dist/components/cluster';
import $ from 'jquery';
// import PrimaryData from "./PrimaryData";

import MapPoints from "./mapToolBar/Points";
import MapDataStyle from "./mapToolBar/mapDatStyle";
import MapPolyLines from "./mapToolBar/Line";
import MapPolygon from "./mapToolBar/Polygon";
import MapCircle from "./mapToolBar/Circle";
import MapRectangle from "./mapToolBar/Rectangle";
import BaseMaps from "./mapToolBar/BaseMaps";
import ElevationProfile from "./mapToolBar/ElevationProfile";
import QueryWindow from "./QueryWindow";
import MeasurementTool from "./mapToolBar/MeasurementTool";
import VueHtml2pdf from 'vue-html2pdf'
    export default {
        name: "mainmap",
        components: {
            QueryWindow,
            // PrimaryData,
    MapPoints,
    MapDataStyle,
    MapPolyLines,
    MapPolygon,
    MapCircle,
    MapRectangle,
    BaseMaps,
    ElevationProfile,
    QueryWindow,
            AdminBoundaries,
            GmapCluster,

            VueHtml2pdf,
            MeasurementTool
        },
        data: () => {
            return {
                alert: false,
                alertMessage: '',
                alertType: '',
                alertIcon: '',
                hover: true,
                valid: false,
                e1: false,
                password: '',
                window: {
                    mapwidth: 0,
                    mapheight: 0,
                    width:0,
                    height:0
                },
                mapOptions:{
                    zoomControl: true,
                    mapTypeControl: true,
                    scaleControl: false,
                    streetViewControl: true,
                    rotateControl: false,
                    fullscreenControl: true,
                    disableDefaultUi: false,
                    zoomControlOptions: {
                        position: 7
                    },
                    mapTypeControlOptions: {
                        position: 1
                    },
                    scaleControlOptions: {
                        position: 2
                    },
                    controlSize:25
                },
                center: { lat: 7.927079, lng: 80.761244 },
                GMap:null,
                csrf:'',
                email_errors:[],
                password_errors:[],
                header:{},
                loginLoader:false,
                OTPModel:false,
                otp:"",
                backOTP:null,
                OTPError:null,
                time: 60, //in seconds
                timer: null,
                otp_attempts:4,
                user:null,
                infoPosition: null,
                infoContent: null,
                infoOpened: false,
                infoCurrentKey: null,
                infoOptions: null,
                visibleUploadedData:[],
                allExcelData:[],
                uploadExcelDialog:false,
                timberCencusData:false,
                landData:false,
                buildingData:false,
                showClusters:true,
                snackbar:false,
                snackColor:'',
                snackMessage:'',
                snackBarRight:false,
                snackBarTop:false,
                drawings:{
                    point:false,
                    line:false,
                    polygon:false,
                    circle:false,
                    rectangle:false,
                    style:false,
                },
                mapDataStyleWindow:false,
                basemapWindow:false,
                elevationWindow:false,
                streetViewPanorama:null,
                measurementWindow:false,
                streetViewWindow:false,
                isLabeling:false,
                datsetItems:[],
                selectedDataset:'',
                trafficLayer:null,
                fullScreenLoader:false,
                fullScreenLoadingText:'please wait...',
            }
        },
        mounted() {
            let laravel = JSON.parse(window.Laravel);
            this.csrf =  laravel.csrfToken;
            this.header = {
                'X-CSRF-TOKEN': this.csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
            };

            this.$refs.mapDiv.$mapPromise.then((map) => {
                this.GMap = map;
                store.commit('changeMap',map);
                console.log('Map page mounted')
                this.init(map);
                this.mapClickListener();
            });



        },
        destroyed() {
            window.removeEventListener('resize', this.handleResize)
        },
        computed:{
            ...mapState({
                gmap: state => state.gmap,
                httpheader: state => state.httpheader,
                selectedLocation: state => state.selectedLocation,
                excelLayer: state => state.excelLayer,
                myInfoWindow: state => state.myInfoWindow,
                mapLayerStyle: state => state.mapLayerStyle,
                viewMapStyleWindow: state => state.viewMapStyleWindow,
                customPointLayer: state => state.customPointLayer,
                gmapSecond: state => state.gmapSecond,
                visibleSecondaryMapWindow: state => state.visibleSecondaryMapWindow,
                mapToolDrawingType: state => state.mapToolDrawingType,
                clusterLocations: state => state.clusterLocations,
                adminBoundaryType: state => state.adminBoundaryType,
                excelFilesCollection: state => state.excelFilesCollection,
                primaryDataset: state => state.primaryDataset,
                queryFormulas: state => state.queryFormulas,
                gisLayers: state => state.gisLayers,
            }),
            excelData(){
                return store.state.excelLayer
            },
            primaryData(){
                return store.state.primaryDataset;
            },
            mapLocationPoint(){
                return store.state.selectedLocation
            },
            queryPoints(){
                return store.state.queryFormulas;
            },
            gisLayer(){
                console.log("fucking layer",store.state.gisLayers);
                return store.state.gisLayers
            },
            customPointDataLayer(){
                return store.state.customPointLayer
            },
            showInfoWindow(){
                return store.state.myInfoWindow
            } ,
            showHideMapStyleWindow(){
                return store.state.viewMapStyleWindow
            },
        },
        watch:{
            excelData(data){
                console.log("fucking data",data);
                if (data){
                    this.uploadExcelDialog = false;
                }
                this.visibleUploadedData = data;
            },

            mapLocationPoint(data){
                if (this.streetViewWindow) {
                    if (this.streetViewPanorama) {
                        let position = new google.maps.LatLng({
                            lat: data.lat,
                            lng: data.lng
                        });
                        this.streetViewPanorama.setPosition(position);
                        console.log(this.streetViewPanorama);
                        console.log(data.lat);
                    } else {
                        this.showStreetView()
                    }
                }
            },


            showInfoWindow(data){
                console.log("fuck",data);
                this.toggleInfoJsonMarker(data.lat,data.lng,data.data,data.key,data.type);
            },
            showHideMapStyleWindow(visibility){
                this.mapDataStyleWindow = visibility;
            },
            mapDataStyleWindow(visibility){
                store.commit('setMapStyleWindow',visibility);
            },
        },
        created() {
            let user = JSON.parse(localStorage.getItem('user'));
            const header = {
                // 'Authorization': 'Bearer '+access_token,
                'Accept': 'application/json',
            };
            let payload = {
                header: header,
                user:user
            };
            store.commit('setHttpHeader',payload);
            this.handleResize();
        },
        methods:{
            init(map){
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
            //---------------------------Map Click event---------------------------------------------
            mapClickListener(){
                let map = this.GMap;
                this.snackMessage = 'Invalid Location Detected';
                this.snackColor = 'error';
                let snackbar = this.snackbar;
                let that = this;
                //----------------Left click event-------------------------------------
                google.maps.event.addListener(map,"click", function(event) {
                    //----------------------Reverse Geocode---------------------------------
                    let geocoder = new google.maps.Geocoder;

                    geocoder.geocode({'location': {lat: event.latLng.lat(), lng: event.latLng.lng()}}, function (results, status) {
                        if (status === 'OK') {
                            if (results[0]) {
                                let payload = {
                                    lat : event.latLng.lat(),
                                    lng : event.latLng.lng(),
                                    address: results[0].formatted_address
                                }
                                store.commit('setSelectedLocation',payload);
                            } else {
                                snackbar = true;
                            }
                        } else {
                            snackbar = true;
                        }
                    });


                });
                // //----------------Right click event-------------------------------------
                // google.maps.event.addListener(map,"rightclick", function(event) {
                //     let latLng = {lat:event.latLng.lat(),lng:event.latLng.lng()};
                //     let payload = {
                //         datax:event.pixel.x,
                //         datay:event.pixel.y,
                //         latLng:latLng,
                //         address:''
                //     };
                //     store.commit('showMapContextMenu',payload);
                // });

            },

            //---------------------------Handling the user window resize event--------------------------------
            handleResize() {
                this.window.width = window.innerWidth;
                this.window.height = window.innerHeight;
                // this.window.mapwidth = window.innerWidth;
                // this.window.mapheight = window.innerHeight-60;
            },
            //----------------------get position when lat lng givven----------------
            getPosition: function(marker,type) {
                let lat = marker.lat,lng = marker.lng;
                return {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                }
            },
            //=========================================================When user click on json file=====================
            toggleInfoJsonMarker (lat,lng,data, key, type) {
                data = _.omit(data,['created_at','updated_at','farms','icon','contacts','cluster_configs','category','user']);
                data = _.pickBy(data, _.identity);
                this.infoPosition = {lat:parseFloat(lat),lng:parseFloat(lng)};
                let contentString =
                    '<div id="iw-container">\n\
                            <div class="iw-content">\
                            <div class="v-data-table  v-data-table--dense v-data-table--fixed-height theme--light">\
                            <div class="v-data-table__wrapper pane scroller">' +
                    '<table class="infoMain">';

                //----------------------looping through the json object property--------------------------
                console.log(data.image);
                if (data && data.image){
                    contentString +='<tr  style="white-space: nowrap;">' +
                        '<td colspan="2"><img src="'+data.image+'" style="height:60px;width:150px" /></td>' +
                        '</tr>';
                }
                $.each(data, function(key,value){
                    console.log(key + "/" + value );
                    if (key !== 'image') {
                        contentString += '<tr  style="white-space: nowrap;">' +
                            '<td class="leftInfo"><b>' + key + '</b></td>' +
                            '<td class="rightInfo">' + value + '</td>' +
                            '</tr>';
                    }
                });

                // if (isFarmer){
                //     contentString +='<tr  style="white-space: nowrap;">' +
                //         '<td colspan="2" style="text-align: center"><b><a href="/farmerProfileData/'+data.farmer_id+'" target="_blank">view profile</a></b></td>' +
                //         '</tr>';
                // }

                contentString +=  '<div>' +
                    '</table>\n\
                    </div></div></div></div>';

                this.infoOptions = {
                    pixelOffset: {
                        width: 0,
                        height: -10
                    },
                    content: contentString
                };

                if (this.infoCurrentKey == key) {
                    this.infoOpened = !this.infoOpened;
                } else {
                    this.infoOpened = true;
                    this.infoCurrentKey = key;
                }
            },

            clusterInfo(data){
                console.log(data);
            },
            // Full Screen COntroller
            mapFullScreenController(){
                let map = store.state.gmap;
                let elementToSendFullscreen = map.getDiv().firstChild;
                if (isFullscreen(elementToSendFullscreen)) {
                    exitFullscreen();
                } else {
                    requestFullscreen(elementToSendFullscreen);
                }
                document.onwebkitfullscreenchange = document.onmsfullscreenchange = document.onmozfullscreenchange =
                    document.onfullscreenchange = function () {
                        if (isFullscreen(elementToSendFullscreen)) {
                            fullscreenControl.classList.add('is-fullscreen');
                        } else {
                            fullscreenControl.classList.remove('is-fullscreen');
                        }
                    };

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
            },
            // traffic layer
            overLayTrafficLayer(){
                let map = store.state.gmap;
                if (this.trafficLayer != null) {
                    this.trafficLayer.setMap(null);
                    this.trafficLayer = null;
                } else {
                    this.trafficLayer = new google.maps.TrafficLayer();
                    this.trafficLayer.setMap(map);
                }
            },
            hasDownloaded(){
                this.fullScreenLoader = false;
                this.isPrinting = false;
                // this.handleResize();
            },

            beforeDownload(){

            },

            onProgress(data){
                console.log("progress",data);
            },


            print(){
                let that = this;
                this.fullScreenLoader = true;
                this.isPrinting = true;
                setTimeout(function () {
                    that.$refs.html2Pdf.generatePdf();
                },500)

            },



            logout(){
                window.setLogout();
            },
        }
    }
</script>

<style>


    .background-container{
        position: fixed;
        top: 35px;
        left:0;
        bottom: 0;
        right: 0;
    }
    .stars {
        background: black url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/1231630/stars.png) repeat;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: block;
        z-index: 0;
    }
    .globe{
        height: 40vh;
        width:40vh;
        position: absolute;
        z-index: 3;
        right: 20px;
    }


    #earth {
        background-image: url('https://encrypted-tbn3.google.com/images?q=tbn:ANd9GcTQqoB4xBnIkmUg5pxDyYW2h0Q1pRTsbQBOfhg-E-y4iLFicsyi');
        width: 90px;
        height: 90px;
        /* a black shadow from left and white from right */
        box-shadow: inset 16px 0 40px 3px rgba(0,0,0,0.9),
        inset -3px 0 5px 2px rgba(255,255,255,0.16);
        background-size: 290px;
        margin: 70px auto;
        border-radius: 50%;
        position: absolute;
        animation-name: move,scale,rotate;
        animation-duration: 20s,20s,20s;
        animation-iteration-count: infinite,infinite,infinite;
        animation-timing-function: ease-in-out,linear,linear;
    }

    #app{
        background-color: #EEEEEE;
    }
  </style>
