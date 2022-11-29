<template>
    <div >
        <v-card style="margin: 5px" flat height="60">
            <fieldset>
                <legend>Style</legend>

                <v-color-picker v-if="!selectedMarkerIcon" width="160" class="ma-2" hide-inputs v-model="color"></v-color-picker>

                <v-slider label="scale" thumb-label min="0" max="100"  v-model="scaleSlider">
                    <template v-slot:thumb-label="{ value }">
                        {{setScale}}
                    </template>
                </v-slider>

            </fieldset>

            <v-select v-if="mapToolType === 'marker'" clearable hide-details style="margin-top: 10px" dense outlined v-model="selectedMarkerIcon" label="custom marker icon"  :items="markerCollection" >
                <template slot="selection" slot-scope="data">

                    <v-flex xs2>
                        <v-avatar size="15px">
                            <img :src="'../storage/'+data.item"/>
                        </v-avatar>
                    </v-flex>
                    <v-flex class='ml-1'>
                        <span style="font-size: small">  {{ data.item.replace('img/icons/','') }} </span>
                    </v-flex>
                </template>
                <template slot="item" slot-scope="data">
                    <v-list-item-icon>
                        <v-avatar>
                            <img :src="'../storage/'+data.item"/>
                        </v-avatar>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title > {{data.item.replace('img/icons/','')}} </v-list-item-title>
                    </v-list-item-content>
                </template>
            </v-select>

            <center><v-btn small color="primary" style="margin-top: 10px" @click="setMapDataStyle"> Update </v-btn></center>

        </v-card>



    </div>

</template>
<script>


import {store} from "../../store/store_geobizz";
import {mapState} from "vuex";

export default {
    name: "mapDataStyle",
    components: {
        store,
    },
    data() {
        return {
            markerCollection:[],
            selectedMarkerIcon:null,
            color:'#FF0000',
            scale:0,
            scaleSlider:45,
        }
    },
    computed: {
        ...mapState({
            gmap: state => state.gmap,
            mapLayerStyle: state => state.mapLayerStyle,
            mapToolDrawingType: state => state.mapToolDrawingType,
        }),
        getMap(){
           return  store.state.gmap;
        },
        setScale(){
            this.scale = this.scaleSlider/10
            return ""+this.scale;
        },
        mapToolType(){
            return store.state.mapToolDrawingType
        }
    },
    mounted() {
        this.loadMainData();
    },
    watch:{

    },
    methods:{
        //----------------------------------------loading marker icon from the map--------------------------------------
        loadMainData(){
            axios.post('https://geobizz.tech/api/dataOperator/leftPanel/addData/getMapIcons',{},store.state.httpheader.header).then(response => {
                console.log(response);
                this.markerCollection = response.data.markers;
            });
        },
        //----------------------------------------setting up map data style--------------------------------------
        setMapDataStyle(){
            let icon,url;
            let markerType = 'circle';
            if(this.selectedMarkerIcon){
                icon = "../storage/"+this.selectedMarkerIcon;
                url = "../storage/"+this.selectedMarkerIcon;
                markerType = 'custom';
            }else{
                icon = this.setMapIcon(this.color);
                markerType = 'circle';
            }
           let payload = {
               icon:icon,
               iconURL:url,
               color:this.color,
               size:this.scale,
               markerType:markerType
           }
           store.commit('setMapLayerStyle',payload);
            store.commit('setMapStyleWindow',false);
        },
        //--------------------------setting map icon programmetically------------------------------
        setMapIcon(color){
            // if (!color){
            //     color = '#FF0000';
            // }
            return {
                path: google.maps.SymbolPath.CIRCLE,
                scale: this.scale,
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
/* Floating panel style   */
.window{
    z-index: 9999 !important;
}
.window > .content {
    background-color: white;
}
</style>
