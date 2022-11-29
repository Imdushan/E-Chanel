<template>
    <div >
        <v-card  flat style="height: 300px; overflow-y: auto;">
            <v-tabs
                fixed-tabs
                background-color="#008080"
                dark
            >
                <v-tab>
                    <v-icon>mdi-map-marker-radius</v-icon> &nbsp; GeoBizz
                </v-tab>
                <v-tab>
                    <v-icon>mdi-map-marker-radius</v-icon> &nbsp; Google
                </v-tab>
                <v-tab-item>
                    <v-card>
                        <!-- <v-card-subtitle style="text-align: center;justify-content: center;align-content: center">
                        <span >GeoBizz Proximity </span> -->
                        <!-- <v-btn icon small absolute style="right: 5px;top: 10px" color="#AAAAAA" @click="backToMenu('geobizz')">
                            <v-icon>cancel</v-icon>
                        </v-btn> -->
                    <!-- </v-card-subtitle> -->
                    <geobizz-proximity />
                    </v-card>

                </v-tab-item>
                <v-tab-item>
                    <v-card>
                        <!-- <v-card-subtitle style="text-align: center;justify-content: center;align-content: center">
                        <span >Google Proximity </span> -->
                        <!-- <v-btn icon small absolute style="right: 5px;top: 10px" color="#AAAAAA" @click="backToMenu('google')">
                            <v-icon>cancel</v-icon>
                        </v-btn> -->
                        <!-- </v-card-subtitle> -->
                        <google-proximity />
                    </v-card>

                </v-tab-item>

            </v-tabs>

            <!-- <v-card outlined style="margin-top: 15px" elevation="8" dark color="#008080" ripple @click="expandGeobizzProximity=true">
                <v-card-title class="py-0 px-2 ma-0">
                    <v-icon>mdi-map-marker-radius</v-icon> &nbsp;&nbsp;&nbsp;   GeoBizz Proximity
                </v-card-title>
            </v-card>
            <v-card outlined style="margin-top: 15px; margin-bottom:15px;" elevation="8" dark color="#008080" ripple @click="expandGoogleProximity=true">
                <v-card-title class="pa-0 px-2 ma-0">
                    <v-icon>mdi-map-marker-radius</v-icon> &nbsp;&nbsp;&nbsp;   Google Proximity
                </v-card-title>
            </v-card> -->
            <v-expand-transition>
                <v-card class="transition-fast-in-fast-out v-card--expandCardFlag" v-show="expandGeobizzProximity" height="100%" flat>
                    <v-card-subtitle style="text-align: center;justify-content: center;align-content: center">
                        <span >GeoBizz Proximity </span>
                        <v-btn icon small absolute style="right: 5px;top: 10px" color="#AAAAAA" @click="backToMenu('geobizz')">
                            <v-icon>cancel</v-icon>
                        </v-btn>
                    </v-card-subtitle>
                    <geobizz-proximity />
                </v-card>
            </v-expand-transition>
            <v-expand-transition>
                <v-card class="transition-fast-in-fast-out v-card--expandCardFlag" v-show="expandGoogleProximity" height="100%" flat>
                    <v-card-subtitle style="text-align: center;justify-content: center;align-content: center">
                        <span >Google Proximity </span>
                        <v-btn icon small absolute style="right: 5px;top: 10px" color="#AAAAAA" @click="backToMenu('google')">
                            <v-icon>cancel</v-icon>
                        </v-btn>
                    </v-card-subtitle>
                    <google-proximity />
                </v-card>
            </v-expand-transition>
                <!--            <v-tabs  v-model="tabProximity"  show-arrows>-->
<!--                <v-tab style="font-size: 10px !important;">Boundary Proximity</v-tab>-->
<!--                <v-tab style="font-size: 10px !important;">Geobizz Proximity</v-tab>-->
<!--                <v-tab style="font-size: 10px !important;">Google Proximity</v-tab>-->

<!--                <v-tabs-items v-model="tabProximity">-->
<!--                    <v-tab-item>-->
<!--                      <boundary-proximity />-->
<!--                    </v-tab-item>-->
<!--                    <v-tab-item>-->
<!--                        <geobizz-proximity />-->
<!--                    </v-tab-item>-->
<!--                    <v-tab-item>-->
<!--                        <google-proximity />-->
<!--                    </v-tab-item>-->

<!--                </v-tabs-items>-->
<!--            </v-tabs>-->
        </v-card>
    </div>
</template>
<script>
import {mapState} from "vuex";
import {store} from "../../store/store_geobizz";
import $ from "jquery";
import GeobizzProximity from "./GeoBizzProximity";
import GoogleProximity from "./GoogleProximity";

export default {
    name: "Proximityanalysis",
    components: {
        GoogleProximity,
        GeobizzProximity

    },
    data() {
        return {
            BufferOnOff:false,
            ElevationOnOff:false,
            mainCategory:null,
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
            expandBoundaryProximity:false,
            expandGeobizzProximity:false,
            expandGoogleProximity:false,
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
        }),

        onMapClick(){
            return store.state.selectedLocation;
        },
        mapContextAction(){
            return store.state.contextMenuAction;
        },
    },
    watch:{
        onMapClick(data){
            this.address = data.address;
            this.lat = data.lat;
            this.lng = data.lng;
        },
        mapContextAction(action){
            if (action.drawerID === 'proximity'){
                this.tabProximity = action.tabNo;
                store.commit('resetContextMenuAction',true);
            }
        }
    },
    mounted() {
        this.setMainCategories();
    },
    created() {
        this.setMainCategories();
    },

    methods: {
        //=============================================SET  GIS Layers==============================================
        setMainCategories(){
            axios.post('https://geobizz.tech/api/gis/getGISCategories', {org_code:'GEOTRACKER_0001'},store.state.httpheader.header)
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

                    this.mainCategories = Object.keys(this.mainGroup);
                    // this.mainCategories = ['Admin Boundary','Public Places','Disaster'];
                    store.commit('setGISCategories',this.allCategories);
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
            let subCategories = _.filter(this.allCategories,{'main_category':mainCategory});
            this.subCategoryList = _.map(subCategories,function (o) {
                return o.sub_category;
            });
        },
        runProximityAnalysis(){
            if (this.radius === ''){
                this.radius  = null;
            }else {
                axios.post('https://geobizz.tech/api/gis/geoBizzProximityAnalysis', {
                    lat: this.lat,
                    lng: this.lng,
                    sub_category: this.subCategory,
                    main_category: this.mainCategory,
                    radius: this.radius
                }, store.state.httpheader.header)
                    .then(response => {
                        console.log("proximity data", response.data);
                        store.commit('setProximityAnalysis', response.data.data);
                        store.commit('addToMapContextMenu', {
                            title: 'ProximityAnalysis',
                            id: 'proximity',
                            action: {type: 'navigation', drawer: 'bottom', drawerNo: 4, tabID: 'analysis', tabNo: 1}
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

        },
        backToMenu(type){
            switch (type) {
                case 'boundary':
                    this.expandBoundaryProximity = false;
                    break;
                case 'geobizz':
                    this.expandGeobizzProximity = false;
                    break;
                case 'google':
                    this.expandGoogleProximity = false;
                    break;
            }

        },
    }

}
</script>
