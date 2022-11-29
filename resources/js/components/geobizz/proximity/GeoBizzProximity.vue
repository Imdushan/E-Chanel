<template>
    <div >

        <v-card-text>
            <v-form>
            <v-card tile  elevation="0">
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
                                <v-col cols="12" md="6" v-if="this.lat && this.lng">
                                    <v-text-field style="height: 50px;" suffix="km"  class="teboxr  px-2" :rules="[() => !!radius || 'radius is rquired']" outlined solo dense color="green darken-4" clearable  v-model="radius"  label="Radius" required></v-text-field>
                                </v-col>
                                </v-row>
                        </v-col>
                    </v-row>
                    <v-row >
                        <v-col cols="12" md="12" v-if="this.lat && this.lng" class="my-0">
                            <!--                                    <v-select outlined solo dense color="green darken-4" clearable  v-model="selectDistance" :items="selectDistanceData" label="Select distance" required></v-select>-->
                            <!-- <v-text-field style="height: 50px;" suffix="km"  class="teboxr" :rules="[() => !!radius || 'radius is rquired']" outlined solo dense color="green darken-4" clearable  v-model="radius"  label="Radius" required></v-text-field> -->
                            <v-select class="teboxr px-2" outlined solo dense color="green darken-4" clearable  disabled v-model="mainCategory" :items="mainCategoryList" @change="setSubCategory(mainCategory)" label="Select main category" required></v-select>
                            <v-select class="teboxr px-2" outlined solo dense color="green darken-4" clearable  v-model="subCategory" :items="subCategoryList" label="Select sub category" required></v-select>
                        </v-col>

                        <v-col cols="12" md="12" class="my-0">
                            <v-row no-gutters>
                                <v-col cols="12" md="6">
                                    <v-text-field class="teboxr px-2" outlined solo dense color="green darken-4" clearable  v-model="lat"  label="Latitude" required></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field class="teboxr  px-2" outlined solo dense color="green darken-4" clearable  v-model="lng"  label="Longitude" required></v-text-field>
                                </v-col>
                            </v-row>
                            <!--                                    <v-text-field outlined solo dense color="green darken-4" clearable  v-model="Elevation"  label="Elevation" required></v-text-field>-->
                        </v-col>
                    </v-row>
                    <v-row>
                                <v-col cols="12" md="12" class="my-0">
                                    <v-btn class=" px-2" dark  x-small style="text-decoration:none !important;" color="green darken-4" @click="runProximityAnalysis" :disabled="isNaN(parseFloat(lat)) || isNaN(parseFloat(lng)) || isNaN(radius) || !subCategory || subCategory === '' ">
                                        <v-icon left small>mdi-clock-start</v-icon>Start
                                    </v-btn>
                                <!-- </v-col>
                                <v-col cols="12" md="6"> -->
                                    <v-btn class=" px-2" left dark  x-small style="text-decoration:none !important;" :disabled="isNaN(parseFloat(lat)) || isNaN(parseFloat(lng)) || isNaN(radius) " color="error" @click="clearProximityAnalysis">
                                        <v-icon left small>mdi-stop-circle</v-icon>Stop
                                    </v-btn>
                                </v-col>
                    </v-row>
                    <v-row>
                                <v-col cols="12" md="12" class="px-0 mx-0">
                                    <v-list-item dense  v-if="sheets && sheets.length>0">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <xlsx-workbook >
                                                    <xlsx-sheet
                                                        :collection="sheet.data"
                                                        v-for="sheet in sheets"
                                                        :key="sheet.name"
                                                        sheet-name="geobizzProximity"
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
    </div>
</template>
<script>
import {mapState} from "vuex";
import {store} from "../../store/store_geobizz";
import $ from "jquery";
import {XlsxRead, XlsxTable, XlsxSheets, XlsxJson, XlsxWorkbook, XlsxSheet, XlsxDownload } from "vue-xlsx/dist/vue-xlsx.es.js";
export default {
    name: "GeobizzProximity",
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
            mainCategory:'Bangladesh',
            subCategory:null,
            selectDistance:'',
            radius:'',
            mainCategoryList:[],
            subCategoryList:[],
            lat:'',
            lng:'',
            address:'',
            allCategories:[],
            geoJsonLayers:[],
            mainCategoryGroup:{},
            tabProximity:0,
            dataset:[],
            sheets:[]
        }
    },
    computed:{
        ...mapState({
            gmap: state => state.gmap,
            httpheader: state => state.httpheader,
            selectedLocation: state => state.selectedLocation,
            farmers: state => state.farmers,
            proximityAnalysis: state => state.proximityAnalysis,
            proximityBuffer: state => state.proximityBuffer,
            mapContextMenu: state => state.mapContextMenu,
            contextMenuAction: state => state.contextMenuAction,
            gisCategories: state => state.gisCategories,
            gisLayers: state => state.gisLayers,
            selectedProximity: state => state.selectedProximity,
        }),

        onMapClick(){
            return store.state.selectedLocation;
        },
        layersReady(){
            return store.state.gisCategories;
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
            if (this.BufferOnOff) {
                this.BufferOnOff = false;
                this.setProximityBuffer(false);
            }
        },
        layersReady(data){
            if (data && data.length>0){
                this.setMainCategories();
            }
        },
        radius(){
            if (this.BufferOnOff) {
                this.BufferOnOff = false;
                this.setProximityBuffer(false);
            }
        },
        selectedBoundaryProximity(data){
            if (data){
                this.lat = data.lat;
                this.lng = data.lng;
                let radius = data.radius.replace(' m','');
                radius = parseInt(radius)/1000
                this.radius = radius;
            }
        }
    },
    mounted() {

    },
    methods: {
        //=============================================SET  GIS Layers==============================================
        setMainCategories(){
            this.allCategories = store.state.gisCategories;
            this.allCategories = _.filter(this.allCategories,{'layer_type':'Point'});
            console.log('fucking categories',this.allCategories);
            let that = this;
            // $.each(this.allCategories,function (key,val) {
            //     if (val.layer_type !== 'Point'){
            //         that.geoJsonLayers.push({
            //             data:null,
            //             url:'../'+val.url,
            //             name:val.sub_category,
            //             main_type:val.main_category
            //         })
            //     }
            // });
            this.mainCategoryGroup = _.groupBy(this.allCategories,'main_category');
            this.mainCategoryList = Object.keys(this.mainCategoryGroup);
            this.setSubCategory('Bangladesh');
        },
        setSubCategory(mainCategory){
            let subCategories = _.filter(this.allCategories,{'main_category':mainCategory});
            this.subCategoryList = _.map(subCategories,function (o) {
                return o.sub_category;
            });
        },
        runProximityAnalysis(){
            let selectedCategory = _.find(this.allCategories, {'sub_category': this.subCategory});

            if (this.radius === ''){
                this.radius  = null;
            }else {
                axios.post('https://geobizz.tech/api/gis/geoBizzProximityAnalysis', {lat: this.lat, lng: this.lng, sub_category: this.subCategory, main_category: this.mainCategory, radius: this.radius}, store.state.httpheader.header)
                    .then(response => {
                        console.log("proximity data", response.data);
                        let dataset = [];

                        $.each(response.data.data, function (key, val) {
                            let spreadsheetSet = {};
                            val.icon = {
                                url: '../' + selectedCategory.icon,
                                size: new google.maps.Size(30, 30),
                                anchor: new google.maps.Point(9, 14),
                                origin: new google.maps.Point(0, 0),
                                scaledSize: new google.maps.Size(20, 20)
                            };
                            dataset.push(val);
                        });

                        this.dataset = dataset;
                        this.sheets.push({
                            name: ""+this.subCategory,
                            data: dataset
                        });

                        store.commit('setProximityAnalysis', dataset);
                        store.commit('addToMapContextMenu', {
                            title: 'ProximityAnalysis',
                            id: 'proximity',
                            action: {type: 'navigation', drawer: 'bottom', tabID: 'geobizz_proximity', drawerID: 'proximity', tabNo: 0}
                        });

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
            }
        },
        setProximityBuffer(show){
            let bufferData = {
                lat:this.lat,
                lng:this.lng,
                center:{lat : this.lat,lng:this.lng},
                radius:this.radius
            }
            show?store.commit('setProximityBuffer',bufferData):store.commit('setProximityBuffer',null);
        },
        clearProximityAnalysis(){
            this.BufferOnOff    = false;
            this.radius         = '';
            this.subCategory    = '';
            this.mainCategory   = '';
            this.lat   = '';
            this.lng   = '';
            store.commit('setProximityBuffer',null);
            store.commit('setProximityAnalysis',null);
            store.commit('removeFromMapContextMenu', {id:'proximity'});
            store.commit('resetContextMenuAction',true);

        }
    }

}
</script>
