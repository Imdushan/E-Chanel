<template>
    <div id="app">
        <v-overlay :value="fullScreenOverlay">
            <v-card-title>{{fullScreenLoadingText}} </v-card-title>
            <v-progress-circular
                indeterminate
                size="50"
            ></v-progress-circular>
        </v-overlay>


        <v-card flat color="theme--light" class="ma-2" elevation="8" style="height: 300px; overflow-y: auto;">

                <v-row no-gutters justify="center pt-4">
<!--                    <v-col cols="6">-->
<!--                        <v-select  dense @change="setSubCategory(selectedMainCategory)"  :items="mainCategories" v-model="selectedMainCategory" outlined label="Main Category"></v-select>-->
<!--                    </v-col>-->
                    <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
                        <v-select class="pl-3 pr-1"  dense   :items="subCategories" v-model="selectedSubCategory" @change="checkValidity(selectedSubCategory)" outlined label="Category"></v-select>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
                        <v-select class="pr-3"  dense small-chips :v-if="isDisabledProvinceList" @change="getGNDData('district',selectedProvince)"  :items="provinceList" v-model="selectedProvince" outlined label="Division"></v-select>
                    </v-col>
                </v-row>
                <v-row no-gutters justify="center">
                    <v-col cols="4">
                        <!-- Flood layer is too large. so it's divided into districts     and limited to one district at a time         -->

                        <v-select class="pl-3 pr-1" multiple dense small-chips :disabled="isDisabledDistrictList" @change="getGNDData('dsd',selectedDistrict)"  :items="districtList" v-model="selectedDistrict" outlined label="Zila">
                            <template v-slot:selection="{ item, index }">
                                <v-chip v-if="index === 0">
                                    <span>{{ item }}</span>
                                </v-chip>
                                <span v-if="index === 1" class="grey--text text-caption">
                          (+{{ districtList.length - 1 }} others)
                        </span>
                            </template>
                        </v-select>
                    </v-col>

                    <v-col cols="4">
                        <v-select multiple dense small-chips :disabled="isDisabledDSDLIST" @change="getGNDData('gnd',selectedDSD)"  :items="DSDList" v-model="selectedDSD" outlined label="Upzila">
                            <template v-slot:selection="{ item, index }">
                                <v-chip v-if="index === 0">
                                    <span>{{ item }}</span>
                                </v-chip>
                                <span v-if="index === 1" class="grey--text text-caption">
                          (+{{ selectedDSD.length - 1 }} others)
                        </span>
                            </template>
                        </v-select>
                    </v-col>
                    <v-col cols="4">
                        <v-select class="pr-3 pl-1" multiple dense small-chips :disabled="isDisabledGNDList"  :items="GNDList" v-model="selectedGND" outlined label="Union">
                            <template v-slot:selection="{ item, index }">
                                <v-chip v-if="index === 0">
                                    <span>{{ item }}</span>
                                </v-chip>
                                <span v-if="index === 1" class="grey--text text-caption">
                          (+{{ selectedGND.length - 1 }} others)
                        </span>
                            </template>
                        </v-select>
                    </v-col>
                </v-row>
                <v-row no-gutters class=" pl-4">
                    <v-col cols="12" xl="12" lg="12" md="12" sm="12" xs="12">
                        <v-btn small :disabled="!selectedProvince || !selectedSubCategory" @click="applyLayerOnMap">Preview Layer</v-btn>
                    </v-col>
                </v-row>







            <v-card-text >
                <v-list>
                    <v-list-item two-line dense v-for="(item ,key) in previewdLayers"  :key="key">
                        <v-list-item-action>
                            <v-checkbox :input-value="true" @change="showHidePreviewedLayer(key,$event)"></v-checkbox>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{item.subType}}</v-list-item-title>
                            <v-list-item-subtitle>{{item.locations}}</v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-avatar>
                            <v-menu
                                bottom
                                right
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        icon
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon>more_vert</v-icon>
                                    </v-btn>
                                </template>

                                <v-list>
                                    <v-list-item @click="startFilteringLayer(key)">
                                        <v-list-item-title> <v-icon>filter_list</v-icon> &nbsp;&nbsp;Filter</v-list-item-title>
                                    </v-list-item>

                                    <v-list-item @click="startStyle(key)" v-if="item.layerType === 'json'">
                                        <v-list-item-title> <v-icon>palette</v-icon> &nbsp;&nbsp;Style</v-list-item-title>
                                    </v-list-item>

                                    <v-list-item @click="showAttributeTable(key)">
                                            <v-list-item-title> <v-icon>poll</v-icon> &nbsp;&nbsp;Attribute Table</v-list-item-title>
                                    </v-list-item>

                                    <v-list-item  @click="removeLayer(key)">
                                        <v-list-item-title> <v-icon>delete</v-icon> &nbsp;&nbsp;Delete</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>

                        </v-list-item-avatar>
                    </v-list-item>

                </v-list>
            </v-card-text>
        </v-card>



        <v-snackbar v-model="snackbar" :multi-line="false" :color="snackColor" >
            {{ snackMessage }}
        </v-snackbar>

        <v-dialog
            v-model="filterDialog"
            max-width="600px"
        >
            <v-card  v-if="selectedQueryLayer">
                <v-card-title style="align-content: center;justify-content: center;align-self: center">
                    <v-icon>filter_list</v-icon> &nbsp;&nbsp;  Filter By Attribute : {{selectedQueryLayer.subType}} Layer
                </v-card-title>
                <v-card-text>

                        <v-card outlined>
                            <v-card-text style="height: 100px;overflow-y: scroll;">
                                <v-list>
                                    <v-list-item dense v-for="(item,key) in selectedQueryLayer.attributes" :key="'selectedAttr'+key" @click="setSelectedAttr(item)">
                                        <v-list-item-title>{{item}}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-card-text>
                        </v-card>
                        <v-divider></v-divider>
                    <v-row>
                        <v-col cols="12" lg="4" md="4">
                            <v-btn block small style="margin-top: 5px"   @click="setSelectedOperator('=')"><v-icon> = </v-icon></v-btn>
                            <v-btn block small style="margin-top: 5px"   @click="setSelectedOperator('>')"><v-icon> > </v-icon></v-btn>
                            <v-btn block small style="margin-top: 5px"   @click="setSelectedOperator('<')" ><v-icon> < </v-icon></v-btn>
                            <v-btn block small style="margin-top: 5px"   @click="setSelectedOperator('<>')"><v-icon> < > </v-icon></v-btn>
                        </v-col>
                        <v-col cols="12" lg="8" md="8">
                            <v-card outlined :disabled="!selectedQueryAttribute">
                                <v-card-text style="height: 100px;overflow-y: scroll;">
                                    <v-list>
                                        <v-list-item dense v-for="(item,key) in selectedAttributeUniqueValues" :key="'selectedAttr'+key" @click="setSelectedQueryValue(item)">
                                            <v-list-item-title>{{item}}</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn x-small :disabled="!selectedQueryAttribute" outlined @click="pushAttributeUniqueValues(selectedAttributeUniqueValues)">Get Unique Values</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-card outlined>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" lg="5">
                                    <v-text-field dense outlined v-model="selectedQueryAttribute" disabled></v-text-field>
                                </v-col>
                                <v-col cols="12" lg="2">
                                    <v-text-field dense outlined v-model="selectedQueryOperator" disabled></v-text-field>
                                </v-col>
                                <v-col cols="12" lg="5">
                                    <v-text-field dense outlined v-model="selectedQueryValue" ></v-text-field>
                                </v-col>
                            </v-row>
                            <span  v-if="selectedQueryLayer.queries" class="heading" style="font-weight: bolder;text-align: center;margin: 10px"> previous queries {{selectedQueryLayer.queries}} </span>
                            <span  v-else class="heading" style="font-weight: bolder;text-align: center;margin: 10px"> no previous queries</span>
                        </v-card-text>
                    </v-card>
<!--                        <v-col cols="12" lg="4">-->
<!--                            <v-select  outlined  dense v-model="selectedQueryAttribute" label="Attribute"  :items="selectedQueryLayer.attributes"></v-select>-->
<!--                        </v-col>-->
<!--                        <v-col cols="12" lg="4">-->
<!--                            <v-select  outlined dense v-model="selectedQueryOperator" label="Operator"  :items="['=','>','<','<>']"></v-select>-->
<!--                        </v-col>-->
<!--                        <v-col cols="12" lg="4">-->
<!--                            <v-text-field  outlined dense v-model="selectedQueryValue" label="Query Value"  ></v-text-field>-->
<!--                        </v-col>-->

                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" :disabled="!selectedQueryOperator || !selectedQueryAttribute || !selectedQueryValue || selectedQueryValue === '' " text @click="runQueryLayer">Apply</v-btn>
                    <v-btn color="error" text @click="resetFilter" >clear filters</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn text @click="filterDialog = false">close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>


        <v-dialog
            v-model="styleDialog"
            max-width="400px"
        >
            <v-card>
                <v-card-title style="align-content: center;justify-content: center;align-self: center">
                    <v-icon>palette</v-icon> &nbsp;&nbsp; Color palette
                </v-card-title>
                <v-card-text>
                    <v-color-picker style="margin-left: 25px;margin-top: 5px"   v-model="queryColor"  hide-inputs dot-size="20"></v-color-picker>
                </v-card-text>
                <v-card-text>
                    <v-slider label="scale" thumb-label min="0" max="30"  v-model="scaleSlider">
                        <template v-slot:thumb-label="{ value }">
                            {{setScale}}
                        </template>
                    </v-slider>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" text @click="applyStyle">Apply Style</v-btn>
                    <v-btn color="error" text @click="resetStyle" >clear styles</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn text @click="styleDialog = false">close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
            v-model="attrTableDialog"
            max-width="1000px"
        >
            <v-card>
                <v-card-title style="align-content: center;justify-content: center;align-self: center">
                    <v-icon>poll</v-icon> &nbsp;&nbsp; AttributeTable
                </v-card-title>
                <v-card-text v-if="selectedAttrTableLayer">
                    <v-data-table
                        :headers="attrHeaders"
                        :items="attrData"
                        item-key="id"
                        :items-per-page="10"
                    >

                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="attrTableDialog = false">close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>


import {mapState} from "vuex";
import {store} from "../store/store_geobizz";
import $ from 'jquery'
export default {
    name: "AdminBoundaries",
    components: {
        store
    },
    data() {
        return {
            showFarms:false,
            showFarmers:false,
            mainCategories:['Bangladesh'],
            subCategories:[],
            allCategories:[],
            selectedMainCategory:'Bangladesh',
            selectedSubCategory:'',
            selectedLayers:[],
            selectedDistrict:[],
            selectedProvince:"",
            selectedDSD:[],
            selectedGND:[],
            GNDList:[],
            DSDList:[],
            districtList:[],
            provinceList:[],
            user:null,
            snackbar:false,
            snackColor:'',
            snackMessage:'',
            snackBarRight:false,
            snackBarTop:false,
            addToMapLoader:false,
            gisIcon:'',
            AirportTab:false,
            ArcheologicalTab:false,
            AutoMobileTab:false,
            BankTab:false,
            CommTowerTab:false,
            EducationTab:false,
            FinancialTab:false,
            FoodTab:false,
            FualStationTab:false,
            InstituteTab:false,
            HraberTab:false,
            HealthTab:false,
            LagoonTab:false,
            LifeStyleTab:false,
            LusPointTab:false,
            PublicPlacesTab:false,
            ReligiousTab:false,
            SoilTab:false,
            SuperMarketTab:false,
            TrafficTab:false,
            TransportTab:false,
            WaterBodiesTab:false,
            agricultureTab:false,
            mainGroup:{},
            fullScreenLoadingText:'please wait...',
            fullScreenOverlay:false,
            geoJsonLayers:[],
            filterWindow:true,
            previewdLayers:[],
            filterDialog:false,
            selectedQueryLayer:null,
            selectedQueryValue:"",
            selectedQueryOperator:"",
            selectedQueryAttribute:"",
            selectedAttributeUniqueValues:[],
            styleDialog:false,
            selectedStyleLayer:null,
            queryColor:'#000000',
            queryScale:0,
            scaleSlider:4,
            attrTableDialog:false,
            selectedAttrTableLayer:null,
            attrHeaders:[],
            attrData:[],
            isDisabledProvinceList:false,
            isDisabledDistrictList:false,
            isDisabledDSDLIST:false,
            isDisabledGNDList:false,
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
            allCrops: state => state.allCrops,
            visible_farms: state => state.visible_farms,
            gisCategories: state => state.gisCategories,
            gisLayers: state => state.gisLayers,
            adminBoundaryType: state => state.adminBoundaryType,
        }),
        onMapClick(){
            return store.state.selectedLocation;
        },

        setScale(){
            this.queryScale = this.scaleSlider/10
            return ""+this.queryScale;
        },
    },
    watch:{

    },
    mounted() {
        this.setMainCategories();
        this.getGNDData('province',null);
    },
    methods:{

        //==============================================GET GND Data====================================================
        getGNDData(type,value){
            axios.post('https://geobizz.tech/api/dataOperator/gisLayers/dataset', {uid:'GEOTRACKER_001',type:type,value:value,country:'Bangladesh'})
                .then(response => {
                   console.log(response.data);
                   switch (type) {
                        case 'province': this.provinceList = response.data.data; break;
                        case 'district': this.districtList = response.data.data; break;
                        case 'dsd': this.DSDList = response.data.data; break;
                        case 'gnd': this.GNDList = response.data.data; break;
                   }
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

        //=============================================SET  GIS Layers==============================================
        setMainCategories(){


            axios.post('https://geobizz.tech/api/gis/getGISCategories', {org_code:'SPORT_0001'})
                .then(response => {
                    console.log(response.data);
                    this.allCategories = response.data.data;
                    let that = this;
                    $.each(this.allCategories,function (key,val) {
                        if (val.layer_type !== 'Point'){
                            that.geoJsonLayers.push({
                                data:null,
                                url:'/'+val.url,
                                name:val.sub_category,
                                main_type:val.main_category,
                                sub_directory:val.sub_directory
                            })
                        }
                    });

                    console.log("json layer object",this.geoJsonLayers);
                    this.mainGroup = _.groupBy(this.allCategories,'main_category');

                    this.mainCategories = ['Bangladesh'];
                    this.setSubCategory('Bangladesh')
                    // this.mainCategories = ['Admin Boundary','Public Places','Disaster'];

                    console.log("main category",this.mainCategories);

                    // this.user = store.state.httpheader.user;
                    // this.districtList = _.map(this.user.locations,function (o) {
                    //     return o.location;
                    // });
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

        setSubCategory(mainCategory){
            this.isDisabledDistrictList = false;
            this.isDisabledDSDLIST = false;
            this.isDisabledGNDList = false;
            this.selectedProvince = null;
            this.selectedDistrict = [];
            this.selectedDSD = [];
            this.selectedGND = [];
          let sortedArray = _.sortBy(this.allCategories,['sub_category']);
          this.subCategories =  _.map(sortedArray,function (o) {
                if (o.main_category === mainCategory)
                    return o.sub_category
            });
            this.subCategories =  _.without(this.subCategories, undefined);
        },

        applyLayerOnMap(){
          let selectedLayer = _.find(this.allCategories,{'sub_category':this.selectedSubCategory});
          if (selectedLayer && selectedLayer.layer_type === 'Point'){
                this.requestGISLayerDataset(this.selectedMainCategory,this.selectedSubCategory);
          }else{
              this.jsonOverlayOnMap(this.selectedMainCategory,this.selectedSubCategory,true);
          }
        },

        requestGISLocations(locationType,location){
            axios.post('https://geobizz.tech/api/dataOperator/rightPanel/gisLayers/byBoundaryTypes', {uid:'GEOTRACKER_001',location_type:locationType,location:location,
                main_type:this.selectedMainCategory,sub_type:this.selectedSubCategory,})
                .then(response => {
                    switch (locationType) {
                        case 'district' : this.DSDList = response.data.sub_locations;this.selectedDSD = [];break;
                        case 'dsd' : this.GNDList = response.data.sub_locations; this.selectedGND = [];break;
                    }
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

        requestGISLayerDataset(maintype,subtype){
            store.commit('setAdminBoundaryType',{maintype:maintype,subtype:subtype});
            let that = this;

                this.fullScreenLoadingText = 'Loading '+maintype+' Layers...';
                this.fullScreenOverlay = true;
                let type;
                this.addToMapLoader = true;

                let selectedCategory = _.find(this.allCategories, {'sub_category': subtype});
                this.gisIcon = selectedCategory.icon;
                let locationType = ["AllIsland"];
                if (this.selectedGND && this.selectedGND.length>0){
                    type = 'gnd'
                    locationType = that.selectedGND;
                }
                else if (this.selectedDSD && this.selectedDSD.length>0){
                    type = 'dsd'
                    locationType = that.selectedDSD;
                }
                else if (this.selectedDistrict && this.selectedDistrict.length>0){
                    type = 'district'
                    locationType = that.selectedDistrict;
                }else if (this.selectedProvince && this.selectedProvince !== ""){
                    type = 'province'
                    locationType = that.selectedProvince;
                }
                else{
                    alert('select location (s)');
                }

                let province = [];
                province.push(this.selectedProvince);

                axios.post('https://geobizz.tech/api/dataOperator/rightPanel/gisLayers/dataset', {
                    uid: 'GEOTRACKER_001', districts: this.selectedDistrict,province: province,
                    dsds: this.selectedDSD, gnds: this.selectedGND, location_type: type,
                    main_type: maintype, sub_type: subtype,org_code:'SPORT_001'
                }, store.state.httpheader.header)
                    .then(response => {
                        console.log("gis_response", response);

                        let dataset = [];
                        let that = this;

                        let attributes = [];
                        //---------------adding object key set-----------------------
                        if (response.data.data && response.data.data.length>0){
                            attributes = Object.keys(response.data.data[0]);
                        }

                        $.each(response.data.data, function (key, val) {
                            val.icon = {
                                url: 'https://geobizz.tech/' + that.gisIcon,
                                size: new google.maps.Size(30, 30),
                                anchor: new google.maps.Point(9, 14),
                                origin: new google.maps.Point(0, 0),
                                scaledSize: new google.maps.Size(20, 20)
                            };
                            val.title = that.selectedMainCategory;
                            val.id = (that.previewdLayers.length)
                            dataset.push(val);
                        });

                        this.previewdLayers.push({
                            mainType:maintype,
                            subType:subtype,
                            data:dataset,
                            attributes:attributes,
                            filteredData:dataset,
                            locations:locationType,
                            layerType:'point',
                            id:this.previewdLayers.length,
                            queries:"",
                            color:"#000000",
                            scale:4
                        });


                        // this.selectedLayers.push({
                        //     mainCategory: maintype,
                        //     subCategory: subtype,
                        //     icon: '../' + that.gisIcon,
                        //     dataset: dataset,
                        //     id:(this.previewdLayers.length-1)
                        // });
                        let oldDataset = store.state.gisLayers;
                        let newDataset;
                        oldDataset && oldDataset.length > 0 ? newDataset = _.concat(dataset, oldDataset) : newDataset = dataset;
                        store.commit('setGISLayers', newDataset);
                        this.fullScreenOverlay = false;


                        this.selectedProvince = "";
                        this.selectedDistrict = [];
                        this.selectedDSD = [];
                        this.selectedGND = [];
                        this.selectedSubCategory = '';
                        // this.selectedMainCategory = '';

                        store.commit('addToMapContextMenu', {title: 'Admin Boundaries', id: 'gis_layers', action: {type: 'navigation', drawer: 'left', drawerID: 'adminBoundary', tabID: 'gis', tabNo: 1}});
                    }).catch((error) => {
                    if (error.response.status === 401){
                        window.setLogout();
                    }
                    this.snackMessage = error;
                    this.snackColor = 'error';
                    this.snackbar = true;
                    this.fullScreenOverlay = false;
                    console.log(error);// this will log an empty object with an error property
                });

        },

        jsonOverlayOnMap(mainCategory,subCategory,visible){
            let that = this;
            this.fullScreenOverlay = true;
            this.fullScreenLoadingText = 'Loading Layer...';
            setTimeout(function () {
                let layer = _.find(that.geoJsonLayers,{'name':subCategory});
                let map = store.state.gmap;
                // if (layer.data){
                //     console.log("layer exists ",layer);
                //     visible?layer.data.setMap(map):layer.data.setMap(null);
                //     that.fullScreenOverlay = false;
                // }else{
                //     console.log("load fucking layer ",layer);
                //     that.loadGeoJson(layer.url, layer.name,mainCategory,subCategory);
                // }
                console.log("fucking layer",layer);
                let url = layer.url;
                if (layer.sub_directory){
                    let province = that.selectedProvince;
                    url = layer.sub_directory+"/"+province+'.geojson';
                }else{
                    url = url.replace('/json/','');
                }

                if (subCategory === 'Flood'){
                    if (!that.selectedDistrict || that.selectedDistrict === '' || that.selectedDistrict.length === 0){
                        that.snackMessage = 'please select district';
                        that.snackBarTop = true;
                        that.snackColor = 'error';
                        url = null;
                        alert('please select district');
                        that.fullScreenOverlay = false;
                    }else{
                        url = that.selectedDistrict+'.geojson';
                    }
                }
                if (url)
                    that.loadGeoJson(url, layer.name,mainCategory,subCategory);
                else
                    that.fullScreenOverlay = false;
            },350)


        },
        //------------------------load json layer--------------------
        async loadGeoJson(url,name,mainCategory,subCategory) {
            let map = store.state.gmap;
            let that = this;
            try{

                axios.post('/api/getGISLayersFromGeobizz', {url:url})
                    .then(response => {
                       console.log("fucking dataset",response);
                       if (response.data.data){
                           that.loadGeoJSONOnMap(response.data.data, name,mainCategory,subCategory);
                       }
                    }).catch((error) => {
                    this.snackMessage = error;
                    this.snackColor = 'error';
                    this.snackbar = true;
                    this.saveLoader = false;
                    that.fullScreenOverlay = false;
                    console.log(error);// this will log an empty object with an error property
                });

                // fetch(url).then(async (response) => {
                //     if (response.ok) {
                //         //Here you have your data...
                //         let data = await response.blob();
                //         let metadata = {
                //             type: 'text/json'
                //         };
                //         let file = new File([data], name+".geojson");
                //
                //
                //         let reader = new FileReader();
                //         reader.onload = function (e) {
                //             that.loadGeoJSONOnMap(e.target.result, name,mainCategory,subCategory);
                //         };
                //         reader.onerror = function (e) {
                //             console.error(e);
                //             that.snackBarTop = true;
                //             that.snackBarRight = false;
                //             that.snackMessage = 'json file reading fail!';
                //             that.snackColor = 'error';
                //             that.snackbar = true;
                //         };
                //         reader.readAsText(file);
                //     }else{
                //         this.fullScreenOverlay = false;
                //     }
                // }).catch((error) => {
                //     this.snackMessage = error;
                //     this.snackColor = 'error';
                //     this.snackbar = true;
                //     this.fullScreenOverlay = false;
                //     console.log(error);// this will log an empty object with an error property
                // });
            }catch (e) {
                this.fullScreenOverlay = false;
            }
        },
        //--------------------------------------overlay json files on map----------------------------------------------
        loadGeoJSONOnMap(geoString,layerName,mainCategory,subCategory){
            // console.log("geostring",geoString);
            let geojson = geoString;
            let map = store.state.gmap;
            let that = this;
            let dataset = [];
            let locationType = ["AllIsland"];
            //--------------------------------------filtering the layer to selcted area-----------------------------
            geojson.features = _.reject(geojson.features, (o) => {
                // console.log(o.properties.District);

                if (that.selectedGND && that.selectedGND.length>0){
                    let dataset =  _.find(that.selectedGND , p => p === o.properties.GND_N  );
                    locationType = that.selectedGND;
                    if (!dataset){
                        return  true;
                    }
                }
                else if (that.selectedDSD && that.selectedDSD.length>0){
                    let dataset =  _.find(that.selectedDSD , p => p === o.properties.DS_divisio);
                    locationType = that.selectedDSD;
                    if (!dataset){
                        return  true;
                    }
                }
                else if (that.selectedDistrict && that.selectedDistrict.length>0 && that.selectedSubCategory !== 'Flood'){
                    let dataset =  _.find(that.selectedDistrict , p => p === o.properties.District  );
                    locationType = that.selectedDistrict;
                    if (!dataset){
                        return  true;
                    }
                }
                else if (that.selectedProvince && that.selectedProvince !== ""){
                    //     let dataset =  _.find(that.selectedProvince , p => p === o.properties.Province  );
                    locationType = that.selectedProvince;
                    //     if (!dataset){
                    //         return  true;
                    //     }
                }

            });
            // console.log(geojson);
            // map.data.loadGeoJson('json/track.geojson');
            try {
                let layer;

                layer = new google.maps.Data();
                layer.addGeoJson(geojson);
                layer.setMap(map);
                // this.jsonDialog = false;
                let bounds = new google.maps.LatLngBounds();
                let keycount = 0;

                let attributes = [];


                layer.forEach(function(feature) {
                    //---------------------retrieving current object properties--------------------------------
                    let objSet = feature.h;

                    attributes =  Object.keys(feature.h);

                    //------------------------------setting paths of the polygons-----------------------------
                    // let paths = [];
                    // feature.getGeometry().getArray().forEach(function(path){
                    //     //iterate over the points in the path
                    //     path.getArray().forEach(function(latLng){
                    //         let typelat = typeof latLng.lat;
                    //         let typelng = typeof latLng.lng;
                    //         if(typelat == 'function' && typelng == 'function') {
                    //             paths.push({lat: latLng.lat(), lng: latLng.lng()})
                    //         }
                    //     });
                    // });
                    //-------------------------------------pushing paths into properties-------------------------
                    // objSet.paths = paths;
                    objSet.key = keycount;
                    objSet.layer = layerName;
                    feature.layer = layerName;

                    layer.overrideStyle(feature, {
                        fillOpacity:0,
                        strokeWeight: 0.4,
                        strokeColor: '#00F',
                    });

                    dataset.push(objSet);
                    keycount++;
                });


                layer.forEach(function(feature) {
                    //----------------------------setting map bounds------------------------------------------
                    that.processPoints(feature.getGeometry(), bounds.extend, bounds);
                });

                map.fitBounds(bounds);

                console.log(layerName,dataset);

                layer.addListener('click', function(event) {
                    console.log("boundary event",event);
                    let properties = event.feature.h;
                    let result = _.omit(properties,['Shape_Leng','paths']);

                    // console.log('========================LOCATION===========================');
                    // alert('lat :'+event.latLng.lat() +' , lng :'+event.latLng.lng() );
                    store.commit('setMyInfoWindow',{lat:event.latLng.lat(),lng:event.latLng.lng(),data:result,key:result.keycount,type:'JSON File'});
                });

                let existingLayer = _.find(that.geoJsonLayers,{'name':layerName});
                if (existingLayer){
                    existingLayer.data = layer;
                }

                that.previewdLayers.push({
                    mainType:mainCategory,
                    subType:subCategory,
                    data:layer,
                    filteredData:layer,
                    attributes:attributes,
                    locations:locationType,
                    layerType:'json',
                    id:that.previewdLayers.length,
                    queries:"",
                    color:"#000000",
                    scale:4
                });
                this.selectedProvince = "";
                this.selectedDistrict = [];
                this.selectedDSD = [];
                this.selectedGND = [];
                this.selectedSubCategory = '';
                // this.selectedMainCategory = '';
                this.fullScreenOverlay = false;
                // this.selectedLayers.push({category: layerName, icon:'img/marker/snorkeling.png', dataset:geoString , isPointLayer:false});
                store.commit('setAdminBoundaryType',{maintype:mainCategory,subtype:subCategory,data:layer});
            }catch (e) {
                console.log(e);
                this.fullScreenLoader = false;
                that.snackBarTop = false;
                that.snackBarRight = false;
                that.snackMessage = 'upload error : '+e.message;
                that.snackColor = 'error';
                that.snackbar = true;
            }
        },

        showHidePreviewedLayer(key,visible){
            let dataset = this.previewdLayers[key];
            let map = store.state.gmap;
            if (dataset){
                if (dataset.layerType === 'json'){
                    visible?dataset.data.setMap(map):dataset.data.setMap(null);
                }else{
                    this.showHidePointLayer(key,visible);
                }
            }
        },

        /**
         * Process each point in a Geometry, regardless of how deep the points may lie.
         * @param {google.maps.Data.Geometry} geometry The structure to process
         * @param {function(google.maps.LatLng)} callback A function to call on each
         *     LatLng point encountered (e.g. Array.push)
         * @param {Object} thisArg The value of 'this' as provided to 'callback' (e.g.
         *     myArray)
         */
        processPoints(geometry, callback, thisArg) {
            let that = this;
            if (geometry instanceof google.maps.LatLng) {
                callback.call(thisArg, geometry);
            } else if (geometry instanceof google.maps.Data.Point) {
                callback.call(thisArg, geometry.get());
            } else {
                geometry.getArray().forEach(function(g) {
                    that.processPoints(g, callback, thisArg);
                });
            }
        },


        showHidePointLayer(id,visibility){
            console.log("id - "+id+" visibility - "+visibility);
            let layer = _.find(this.previewdLayers,{id:id});
            if (visibility){
                let oldDataset = store.state.gisLayers;
                let newDataset;
                oldDataset && oldDataset.length>0? newDataset = _.concat(layer.filteredData,oldDataset): newDataset = layer.filteredData;
                store.commit('setGISLayers',newDataset);
            }else{
                let oldDataset = store.state.gisLayers;
                console.log("fuck "+id,oldDataset);
                let newDataset = _.reject(oldDataset,{'id':id});
                store.commit('setGISLayers',newDataset);
            }
        },

        removeLayer(id){
            this.$confirm(
                {
                    message: `remove selected layer from layer set? this will clear the map points related to the layer`,
                    button: {
                        no: 'No',
                        yes: 'Remove'
                    },
                    /**
                     * Callback Function
                     * @param {Boolean} confirm
                     */
                    callback: async confirm => {
                        if (confirm) {
                            let index = _.findIndex(this.previewdLayers,{'id':id});
                            console.log("fucking index",index);
                                if(this.previewdLayers[id].layerType === 'json'){
                                    console.log('closing fucbkin',this.previewdLayers[id].data)
                                    this.previewdLayers[id].data.setMap(null);
                                }else {
                                    this.showHidePointLayer(id, false);
                                    this.previewdLayers.splice(index,1);
                                    if (this.previewdLayers.length === 0){
                                        store.commit('removeFromMapContextMenu', {id:'gis_layers'});
                                    }
                                }

                                this.previewdLayers.splice(id,1);


                        } else {

                        }
                    }
                });


        },

        startFilteringLayer(id){
            this.selectedQueryLayer = this.previewdLayers[id];
            this.filterDialog = true;

        },
        setSelectedAttr(item){
            this.selectedQueryAttribute = item;
            this.selectedAttributeUniqueValues = [];
        },
        pushAttributeUniqueValues(){
            let groupedData;
            if (this.selectedQueryLayer.layerType === 'point'){
                groupedData = _.groupBy(this.selectedQueryLayer.data,this.selectedQueryAttribute);
            }else{
                let dataArr = [];
                this.selectedQueryLayer.filteredData.forEach(function(feature) {
                    dataArr.push(feature.h);
                });
                groupedData = _.groupBy(dataArr,this.selectedQueryAttribute);
            }
            console.log("groupedData",groupedData);
            this.selectedAttributeUniqueValues = Object.keys(groupedData);
        },
        setSelectedOperator(operator){
            this.selectedQueryOperator = operator;
        },
        setSelectedQueryValue(value){
          this.selectedQueryValue = value;
        },
        runQueryLayer(){
            this.filterDialog = false;
            this.fullScreenOverlay = true;
            this.fullScreenLoadingText = "FilteringData..";
            let index = _.findIndex(this.previewdLayers,{'id':this.selectedQueryLayer.id});
            let dataset = [];
            if (this.selectedQueryLayer.layerType === 'point'){
                let that = this;

                _.map(this.selectedQueryLayer.data,function (o) {
                    let attr = o[that.selectedQueryAttribute];

                    switch (that.selectedQueryOperator) {
                        case '=' :  if (isNaN(parseFloat(attr))){
                                        if(attr.toLowerCase() === that.selectedQueryValue.toLowerCase()){
                                            dataset.push(o);
                                        }
                                    }else{
                                        if(parseFloat(attr) === parseFloat(that.selectedQueryValue)){
                                            dataset.push(o);
                                        }
                                    }
                                    break;
                        case ">": if (!isNaN(parseFloat(attr))){
                                        if(parseFloat(attr) > parseFloat(that.selectedQueryValue)){
                                            dataset.push(o);
                                        }
                                  }
                                  break;
                        case "<": if (!isNaN(parseFloat(attr))){
                                        if(parseFloat(attr) < parseFloat(that.selectedQueryValue)){
                                            dataset.push(o);
                                        }
                                  }
                                  break;
                        case "<>":  if(attr !== that.selectedQueryValue){
                                            dataset.push(o);
                                    }
                                  break;
                    }
                });

                let oldDataset = store.state.gisLayers;
                let replacedLayerset = _.reject(oldDataset,{'id':this.selectedQueryLayer.id});
                let newDataset = _.concat(dataset,replacedLayerset);
                store.commit('setGISLayers',newDataset);
            }else{
                let that = this;
                let bounds = new google.maps.LatLngBounds();
                let map = store.state.gmap;
                this.selectedQueryLayer.data.forEach(function(feature) {
                    //---------------------retrieving current object properties--------------------------------
                    let objSet = feature.h;

                    let attr = objSet[that.selectedQueryAttribute];
                    let isFiltered = false;
                    switch (that.selectedQueryOperator) {
                        case '=' :  if (isNaN(parseFloat(attr))){
                            if(attr.toLowerCase() === that.selectedQueryValue.toLowerCase()){
                                dataset.push(feature);
                                isFiltered = true;
                            }
                        }else{
                            if(parseFloat(attr) === parseFloat(that.selectedQueryValue)){
                                dataset.push(feature);
                                isFiltered = true;
                            }
                        }
                            break;
                        case ">": if (!isNaN(parseFloat(attr))){
                            if(parseFloat(attr) > parseFloat(that.selectedQueryValue)){
                                dataset.push(feature);
                                isFiltered = true;
                            }
                        }
                            break;
                        case "<": if (!isNaN(parseFloat(attr))){
                            if(parseFloat(attr) < parseFloat(that.selectedQueryValue)){
                                dataset.push(feature);
                                isFiltered = true;
                            }
                        }
                            break;
                        case "<>":  if(attr !== that.selectedQueryValue){
                            dataset.push(feature);
                            isFiltered = true;
                        }
                            break;
                    }

                    if (isFiltered) {
                        that.selectedQueryLayer.data.overrideStyle(feature, {
                            fillOpacity: 0,
                            strokeWeight: 0.4,
                            strokeColor: '#000',
                        });
                        //----------------------------setting map bounds------------------------------------------
                        that.processPoints(feature.getGeometry(), bounds.extend, bounds);

                    }else{
                        that.selectedQueryLayer.data.overrideStyle(feature, {
                            strokeColor: 'transparent',
                            fillOpacity: 0
                        });
                    }
                });
                map.fitBounds(bounds);

            }

            this.snackMessage = dataset.length +" features returned !";
            this.snackColor = 'primary';
            this.snackbar = true;

            let getLayer = this.previewdLayers[index];
            getLayer.filteredData = dataset;
            getLayer.queries = this.selectedQueryAttribute+" "+this.selectedQueryOperator+" "+this.selectedQueryValue;
            this.previewdLayers.splice(index,getLayer);

            this.fullScreenOverlay = false;
            this.selectedQueryValue = "";
            this.selectedQueryOperator = "";
            this.selectedQueryAttribute = "";
        },
        resetFilter(){
            this.filterDialog = false;
            this.fullScreenOverlay = true;
            this.fullScreenLoadingText = "FilteringData..";
            let index = _.findIndex(this.previewdLayers,{'id':this.selectedQueryLayer.id});
            let dataset = [];
            if (this.selectedQueryLayer.layerType === 'point'){
                let that = this;

                _.map(this.selectedQueryLayer.data,function (o) {
                    dataset.push(o);
                });

                let oldDataset = store.state.gisLayers;
                let replacedLayerset = _.reject(oldDataset,{'id':this.selectedQueryLayer.id});
                let newDataset = _.concat(dataset,replacedLayerset);
                store.commit('setGISLayers',newDataset);
            }else{
                let that = this;
                let map = store.state.gmap;
                let bounds = new google.maps.LatLngBounds();

                this.selectedQueryLayer.data.forEach(function(feature) {
                    //---------------------retrieving current object properties--------------------------------
                        dataset.push(feature);
                        that.selectedQueryLayer.data.overrideStyle(feature, {
                            fillOpacity: 0,
                            strokeWeight: 0.4,
                            strokeColor: '#000',
                        });
                });
                this.selectedQueryLayer.filteredData.forEach(function(feature) {
                    //----------------------------setting map bounds------------------------------------------
                    that.processPoints(feature.getGeometry(), bounds.extend, bounds);
                });
                map.fitBounds(bounds);
            }

            this.snackMessage = dataset.length +" features returned !";
            this.snackColor = 'primary';
            this.snackbar = true;

            let getLayer = this.previewdLayers[index];
            getLayer.filteredData = dataset;
            getLayer.queries = this.selectedQueryAttribute+" "+this.selectedQueryOperator+" "+this.selectedQueryValue;
            this.previewdLayers.splice(index,getLayer);

            this.fullScreenOverlay = false;
            this.selectedQueryValue = "";
            this.selectedQueryOperator = "";
            this.selectedQueryAttribute = "";
            this.selectedAttributeUniqueValues = [];
        },
        startStyle(id){
            this.selectedStyleLayer = this.previewdLayers[id];
            this.queryColor = this.previewdLayers[id].color;
            this.scaleSlider = this.previewdLayers[id].scale;
            this.styleDialog = true;
        },
        applyStyle(){
            let that = this;
            let index = _.findIndex(this.previewdLayers,{'id':this.selectedStyleLayer.id});
            this.selectedStyleLayer.filteredData.forEach(function(feature) {
                that.selectedStyleLayer.data.overrideStyle(feature, {
                    fillOpacity: 0,
                    strokeWeight: that.queryScale,
                    strokeColor: that.queryColor,
                });
            });
            this.styleDialog = false;
            let getLayer = this.previewdLayers[index];
            getLayer.color = this.queryColor;
            getLayer.scale = this.queryScale;
            this.previewdLayers.splice(index,getLayer);
        },
        resetStyle(){
            let that = this;
            let index = _.findIndex(this.previewdLayers,{'id':this.selectedStyleLayer.id});
            this.selectedStyleLayer.filteredData.forEach(function(feature) {
                that.selectedStyleLayer.data.overrideStyle(feature, {
                    fillOpacity: 0,
                    strokeWeight: 0.4,
                    strokeColor: '#000',
                });
            });
            this.styleDialog = false;
            let getLayer = this.previewdLayers[index];
            getLayer.color = '#000000';
            getLayer.scale = 4;
            this.previewdLayers.splice(index,getLayer);

        },
        showAttributeTable(id){
            this.selectedAttrTableLayer = this.previewdLayers[id];
            let that  = this;
            $.each(this.selectedAttrTableLayer.attributes,function (key,val) {
                that.attrHeaders.push({
                    text:val,
                    value:val
                })
            });
            if (this.selectedAttrTableLayer.layerType === 'point'){
               this.attrData =  this.selectedAttrTableLayer.filteredData;
            }else{
                this.selectedAttrTableLayer.filteredData.forEach(function(feature) {
                    that.attrData.push(feature.h);
                })
            }
            this.attrTableDialog = true;
        },
        checkValidity(subcategory){
            this.selectedProvince = null;
            this.selectedDistrict = [];
            this.selectedDSD = [];
            this.selectedGND = [];
            if (this.selectedMainCategory === 'Admin Boundary'){
                switch (subcategory) {
                    case 'Province' : this.isDisabledDistrictList = true;
                                      this.isDisabledDSDLIST = true;
                                      this.isDisabledGNDList = true;
                                      break;

                    case 'District' : this.isDisabledDistrictList = false;
                                      this.isDisabledDSDLIST = true;
                                      this.isDisabledGNDList = true;
                                      break;

                    case 'DSD'      : this.isDisabledDistrictList = false;
                                      this.isDisabledDSDLIST = false;
                                      this.isDisabledGNDList = true;
                                      break;
                    case 'GND'      : this.isDisabledDistrictList = false;
                                      this.isDisabledDSDLIST = false;
                                      this.isDisabledGNDList = false;
                                      break;

                }
            }
        }


    }
}
</script>
