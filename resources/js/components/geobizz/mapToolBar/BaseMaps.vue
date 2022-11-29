<template>
    <div >
        <v-card tile max-width="444" class="mx-auto">
            <v-carousel :continuous="true" v-model="selectedMapID" :cycle="cycle" :show-arrows="true" hide-delimiters height="250px">
                <v-carousel-item>
                    <v-card tile class="mx-auto" :elevation="hover ? 16 : 3" >
                        <v-img src="/img/map/Map.png" class="white--text align-end" gradient="to bottom, rgba(10,10,10,0.6), rgba(10,10,10,0.6)" height="250px">
                            <v-card-text class="overline white--text" style="text-align:center !important;font-weight:500 !important;font-size: 14px !important;">
                                <div>
                                    Road Map
                                </div>
                            </v-card-text>
                        </v-img>
                    </v-card>
                </v-carousel-item>

                <v-carousel-item>
                    <v-card tile class="mx-auto" :elevation="hover ? 16 : 3" >
                        <v-img src="/img/map/Terrain.png" class="white--text align-end" gradient="to bottom, rgba(10,10,10,0.6), rgba(10,10,10,0.6)" height="250px">
                            <v-card-text class="overline white--text" style="text-align:center !important;font-weight:500 !important;font-size: 14px !important;">
                                <div>
                                    Terrain Map
                                </div>
                            </v-card-text>
                        </v-img>
                    </v-card>
                </v-carousel-item>

                <v-carousel-item>
                    <v-card tile class="mx-auto" :elevation="hover ? 16 : 3" >
                        <v-img src="/img/map/Satellite.png" class="white--text align-end" gradient="to bottom, rgba(10,10,10,0.6), rgba(10,10,10,0.6)" height="250px">
                            <v-card-text class="overline white--text" style="text-align:center !important;font-weight:500 !important;font-size: 14px !important;">
                                <div>
                                    Satellite Map
                                </div>
                            </v-card-text>
                        </v-img>
                    </v-card>
                </v-carousel-item>

                <v-carousel-item>
                    <v-card tile  class="mx-auto" :elevation="hover ? 16 : 3" >
                        <v-img src="/img/map/hybrid.png" class="white--text align-end" gradient="to bottom, rgba(10,10,10,0.6), rgba(10,10,10,0.6)" height="250px">
                            <v-card-text class="overline white--text" style="text-align:center !important;font-weight:500 !important;font-size: 14px !important;">
                                <div>
                                    Hybrid Map
                                </div>
                            </v-card-text>
                        </v-img>
                    </v-card>
                </v-carousel-item>
            </v-carousel>
        </v-card>
    </div>
</template>

<script>


import {mapState} from "vuex";
import {store} from "../../store/store_geobizz";
import $ from 'jquery'

export default {
    name: "BaseMaps",
    components: {
        store,
    },
    data() {
        return {
            cycle:null,
            hover:null,
            selectedMapID:0
        }
    },
    computed:{
        ...mapState({
            gmap: state => state.gmap,
            httpheader: state => state.httpheader,
        }),
        onMapClick(){
            return store.state.selectedLocation;
        }
    },
    watch:{
        selectedMapID(data){
            this.setMapID(data);
        }
    },
    mounted() {

    },
    methods:{
        setMapID(id){
            let map = store.state.gmap;
            switch (id) {
                case 0 : map.setMapTypeId('roadmap');break;
                case 1 : map.setMapTypeId('terrain');break;
                case 2 : map.setMapTypeId('satellite');break;
                case 3 : map.setMapTypeId('hybrid');break;
            }

            if (id !== 0){
                store.commit('addToMapContextMenu', {title:'Map Type',id:'map_type',action:{type:'navigation',drawer:'left',drawerNo:0,tabID:'mapCycle',tabNo:0}});
            }else{
                store.commit('removeFromMapContextMenu', {id:'map_type'});
            }
        }
    }
}
</script>
