<template>
    <div >
            <v-card-text>
                <v-form>
                    <v-card tile  elevation="0" :loading="placeSearchLoading">
                        <v-container style="height: auto" fluid>
                            <v-row no-gutters>
                                <v-col cols="12" md="12" class="my-0">
                                    <v-card-subtitle class=" px-2">
                                        Click on the map for start analyze
                                    </v-card-subtitle>
                                </v-col>
                                <v-col cols="12" md="12" class="my-0">
                                    <v-row no-gutters>
                                        <v-col cols="12" md="6">
                                    <v-checkbox class=" px-2" small style="margin-top: -2px"  v-model="BufferOnOff" label="Buffer" color="green darken-4" value="BufferOnOff" hide-details @click="setProximityBuffer(BufferOnOff)"></v-checkbox>
                                        </v-col>
                                        <v-col cols="12" md="6" v-if="!isNaN(parseFloat(lat)) || !isNaN(parseFloat(lng))">
                                            <v-text-field suffix="Km" class="teboxr  px-2" :rules="[() => !!kmRadius || 'radius is rquired']" outlined solo dense color="green darken-4" clearable  v-model="kmRadius"  label="Radius" required></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12" v-if="!isNaN(parseFloat(lat)) || !isNaN(parseFloat(lng))">
                                    <!--                                    <v-select outlined solo dense color="green darken-4" clearable  v-model="selectDistance" :items="selectDistanceData" label="Select distance" required></v-select>-->
                                    <!-- <v-text-field suffix="Km" class="teboxr" :rules="[() => !!kmRadius || 'radius is rquired']" outlined solo dense color="green darken-4" clearable  v-model="kmRadius"  label="Radius" required></v-text-field> -->
                                    <v-select class="teboxr px-2" outlined solo dense color="green darken-4" clearable  v-model="category" :items="categoryList"  label="Select category" required></v-select>
                                </v-col>
                                <v-col cols="12" md="12" class="my-0">
                                    <v-row no-gutters>
                                        <v-col cols="12" md="6">
                                            <v-text-field class="teboxr px-2" outlined solo dense color="green darken-4" clearable  v-model="lat"  label="Latitude" required></v-text-field>
                                        </v-col>
                                    <v-col cols="12" md="6">
                                    <v-text-field class="teboxr px-2" outlined solo dense color="green darken-4" clearable  v-model="lng"  label="Longitude" required></v-text-field>
                                    </v-col>
                                    <!--                                    <v-text-field outlined solo dense color="green darken-4" clearable  v-model="Elevation"  label="Elevation" required></v-text-field>-->
                                    </v-row>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12" class="my-0">
                                    <v-btn class=" px-2" left dark  x-small style="text-decoration:none !important;" color="green darken-4" @click="runProximityAnalysis" :disabled="isNaN(parseFloat(lat)) || isNaN(parseFloat(lng)) || isNaN(kmRadius) || !category || category === '' ">
                                        <v-icon left small>mdi-clock-start</v-icon>Start
                                    </v-btn>
                                <!-- </v-col>
                                <v-col cols="12" md="6"> -->
                                    <v-btn class=" px-2" left dark  x-small style="text-decoration:none !important;" :disabled="isNaN(parseFloat(lat)) || isNaN(parseFloat(lng)) || isNaN(kmRadius) " color="error" @click="clearProximityAnalysis">
                                        <v-icon left small>mdi-stop-circle</v-icon>Stop
                                    </v-btn>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12"  class="px-0 mx-0">
                                    <v-list-item dense  v-if="sheets && sheets.length>0">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <xlsx-workbook >
                                                    <xlsx-sheet
                                                        :collection="sheet.data"
                                                        v-for="sheet in sheets"
                                                        :key="sheet.name"
                                                        sheet-name="googleProximity"
                                                    />
                                                    <xlsx-download>
                                                        <v-btn class=" px-2" outlined x-small>Download Excel</v-btn>
                                                    </xlsx-download>
                                                </xlsx-workbook>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-col>

                            </v-row>
                        </v-container>
                    </v-card>
                </v-form>
            </v-card-text>
        </v-card>
        <v-snackbar v-model="snackbar" :multi-line="false" :color="snackColor" :top="snackBarTop" :right="snackBarRight">
            {{ snackMessage }}
        </v-snackbar>
    </div>
</template>
<script>
import {mapState} from "vuex";
import {store} from "../../store/store_geobizz";
import $ from "jquery";
import {XlsxRead, XlsxTable, XlsxSheets, XlsxJson, XlsxWorkbook, XlsxSheet, XlsxDownload } from "vue-xlsx/dist/vue-xlsx.es.js";

export default {
    name: "GoogleProximity",
    components: {
        XlsxRead,
        XlsxTable,
        XlsxSheets,
        XlsxJson,
        XlsxWorkbook,
        XlsxSheet,
        XlsxDownload
    },
    data() {
        return {
            BufferOnOff:false,
            ElevationOnOff:false,
            category:null,
            selectDistance:'',
            kmRadius:'',
            radius:'',
            categoryList:['accounting','airport', 'amusement_park', 'aquarium', 'art_gallery', 'atm', 'bakery', 'bank', 'bar', 'beauty_salon', 'bicycle_store', 'book_store', 'bowling_alley', 'bus_station', 'cafe', 'campground', 'car_dealer', 'car_rental', 'car_repair', 'car_wash', 'casino', 'cemetery', 'church', 'city_hall', 'clothing_store', 'convenience_store', 'courthouse', 'dentist', 'department_store', 'doctor', 'drugstore', 'electrician', 'electronics_store', 'embassy', 'fire_station', 'florist', 'funeral_home', 'furniture_store', 'gas_station', 'gym', 'hair_care', 'hardware_store', 'hindu_temple', 'home_goods_store', 'hospital', 'insurance_agency', 'jewelry_store', 'laundry', 'lawyer', 'library', 'light_rail_station', 'liquor_store', 'local_government_office', 'locksmith', 'lodging', 'meal_delivery', 'meal_takeaway', 'mosque', 'movie_rental', 'movie_theater', 'moving_company', 'museum', 'night_club', 'painter', 'park', 'parking', 'pet_store', 'pharmacy', 'physiotherapist', 'plumber', 'police', 'post_office', 'primary_school', 'real_estate_agency', 'restaurant', 'roofing_contractor', 'rv_park', 'school', 'secondary_school', 'shoe_store', 'shopping_mall', 'spa', 'stadium', 'storage', 'store', 'subway_station', 'supermarket', 'synagogue', 'taxi_stand', 'tourist_attraction', 'train_station', 'transit_station', 'travel_agency', 'university', 'veterinary_care', 'zoo'],
            lat:'',
            lng:'',
            address:'',
            allCategories:[],
            geoJsonLayers:[],
            mainCategoryGroup:{},
            tabProximity:0,
            placeCircle:null,
            placeSearchLoading:false,
            placeResult:null,
            collectedPlaces:[],
            places:[],
            placeMarkers:[],
            snackbar:false,
            snackColor:'',
            snackMessage:'',
            snackBarRight:false,
            snackBarTop:false,
            dataset:[],
            sheets:[]
        }
    },
    computed:{
        ...mapState({
            gmap: state => state.gmap,
            gmapSecond: state => state.gmapSecond,
            httpheader: state => state.httpheader,
            selectedLocation: state => state.selectedLocation,
            farmers: state => state.farmers,
            proximityAnalysis: state => state.proximityAnalysis,
            proximityBuffer: state => state.proximityBuffer,
            mapContextMenu: state => state.mapContextMenu,
            contextMenuAction: state => state.contextMenuAction,
            selectedProximity: state => state.selectedProximity,
        }),

        onMapClick(){
            return store.state.selectedLocation;
        },
        selectedBoundaryProximity(){
            return store.state.selectedProximity;
        }
    },
    watch:{
        onMapClick(data){
            this.address = data.address;
            this.lat = data.lat;
            this.lng = data.lng;
        },
        placeResult(val){
            if (val){
                this.appendSpotPlaceResult();
            }
        },
        kmRadius(value){
            this.radius = value*1000;
        },
        selectedBoundaryProximity(data){
            if (data){
                this.lat = data.lat;
                this.lng = data.lng;
                let radius = data.radius.replace(' m','');
                radius = parseInt(radius)/1000
                this.kmRadius = radius;
            }
        }
    },
    mounted() {

    },
    methods: {
        //=============================================SET  GIS Layers==============================================

        runProximityAnalysis(){
            if (this.radius === ''){
                this.radius  = null;
            }else {
                this.placeSearchLoading = true;
                let type = this.category;
                // let radius = this.searchLocationRadius;

                let map = store.state.gmap;
                let service = new google.maps.places.PlacesService(map);
                let getNextPage = null;
                let curntl = {lat:parseFloat(this.lat),lng:parseFloat(this.lng)};
                let that = this;

                if(this.placeCircle){
                    this.placeCircle.setMap(null)
                }

                // Add the circle for this city to the map.
                if (this.BufferOnOff) {
                 if (this.placeCircle){
                     this.placeCircle.setMap(map)
                 }else{
                     this.placeCircle = new google.maps.Circle({
                         strokeColor: '#FF0000',
                         strokeOpacity: 0.8,
                         strokeWeight: 2,
                         fillColor: '#FF0000',
                         fillOpacity: 0.35,
                         map: map,
                         center: curntl,
                         radius: parseFloat(that.radius)
                     });
                 }
                }
                // let moreButton = document.getElementById('more');
                // moreButton.onclick = function() {
                //     moreButton.disabled = true;
                //     if (getNextPage) getNextPage();
                // };
                // Perform a nearby search.
                // console.log('searching: '+curntl.lat+' , '+curntl.lng);
                service.nearbySearch(
                    {location: curntl, radius: parseFloat(that.radius), type: type},
                    function(results, status, pagination) {
                        // console.log(results);
                        if (status !== 'OK'){
                            that.placeSearchLoading = false;
                            that.snackBarTop = true;
                            that.snackBarRight = false;
                            that.snackMessage = status;
                            that.snackColor = 'brown';
                            that.snackbar = true;
                            return;
                        }


                        // moreButton.disabled = !pagination.hasNextPage;
                        // if(pagination.hasNextPage) {
                        that.placeNextPageVal = pagination.hasNextPage && function () {
                            // console.log('nextPageFound');
                            pagination.nextPage();
                        };
                        // }
                        that.placeResult = results;
                        // console.log(results);
                        // console.log(results);
                        // setTimeout(() => {
                        pagination.nextPage();
                    });

                store.commit('addToMapContextMenu', {
                    title: 'ProximityAnalysis',
                    id: 'proximity',
                    action: {type: 'navigation', drawer: 'bottom', tabID: 'google_proximity', drawerID: 'proximity', tabNo: 1}
                });
            }
        },

        appendSpotPlaceResult() {
            let that = this;
            this.placeSearchLoading = true;
            let type = this.category;
            let results = this.placeResult;
            console.log('category :'+type);
            let map = store.state.gmap;
            //------------------------pushing values-------------------------------
            let repeatPlace = _.find(this.collectedPlaces, {'category': type});
            if (repeatPlace && repeatPlace.category === type &&  repeatPlace.results) {
                repeatPlace.results = _.concat(repeatPlace.results,results);
            } else {
                this.collectedPlaces.push({
                    category: type,
                    results: results
                });
            }

            setTimeout(() => {
                that.placeSearchLoading = false;
                console.log('-------------------------collected places-------');
                console.log(that.collectedPlaces);
                that.sheets.push({
                    name: ""+that.collectedPlaces[0].category,
                    data: that.collectedPlaces[0].results
                });
            },2000);

            let spreadData = [];
            //----------------------previewing the marker--------------------
            for (let i = 0, place; place = results[i]; i++) {
                let  collectedPlace = place;
                collectedPlace['lat'] = place.geometry.location.lat();
                collectedPlace['lng'] = place.geometry.location.lng();
                this.places.push(collectedPlace);

                let content = _.omit(place,['opening_hours','plus_code','html_attributions','icon','geometry','photos'])
                let contentString =
                    '<div id="iw-container">\n\
                            <div class="iw-content">\
                            <div class="v-data-table  v-data-table--dense v-data-table--fixed-height theme--light">\
                            <div class="v-data-table__wrapper pane scroller">' +
                    '<table class="infoMain">';

                //----------------------looping through the json object property--------------------------

                $.each(content, function(key,value){
                    // console.log(key + "/" + value );
                    contentString +='<tr  style="white-space: nowrap;">' +
                        '<td class="leftInfo"><b>'+key+'</b></td>' +
                        '<td class="rightInfo">' + value + '</td>' +
                        '</tr>';

                });

                contentString +=  '<div>' +
                    '</table>\n\
                    </div></div></div></div>';


                const infowindow = new google.maps.InfoWindow({
                    content: contentString,
                });
                const marker = new google.maps.Marker({
                    position: place.geometry.location,
                    map,
                    title: type,
                    icon:{
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    }
                });
                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });

                that.placeMarkers.push(marker);
            }
        },

        setProximityBuffer(show){
            let map = store.state.gmap;
            let that = this;
           if (this.placeCircle){
               show?this.placeCircle.setMap(map):this.placeCircle.setMap(null);
           }else{
               if (show && !isNaN(parseFloat(that.lat)) &&!isNaN(parseFloat(that.lng))){
                   let curntl = {lat:parseFloat(that.lat),lng:parseFloat(that.lng)};
                   this.placeCircle = new google.maps.Circle({
                       strokeColor: '#FF0000',
                       strokeOpacity: 0.8,
                       strokeWeight: 2,
                       fillColor: '#FF0000',
                       fillOpacity: 0.35,
                       map: map,
                       center: curntl,
                       radius: parseFloat(that.radius)
                   });

               }
           }
        },

        clearProximityAnalysis(){
            this.BufferOnOff    = false;
            this.radius         = '';
            this.category       = '';
            this.mainCategory   = '';
            this.lat            = '';
            this.lng            = '';

            $.each(this.placeMarkers,function (key,val) {
                val.setMap(null);
            });

            this.placeMarkers = [];
            if(this.placeCircle)
                this.placeCircle.setMap(null);
            this.placeCircle = null;
            store.commit('removeFromMapContextMenu', {id:'proximity'});
            store.commit('resetContextMenuAction',true);

        }
    }

}
</script>
