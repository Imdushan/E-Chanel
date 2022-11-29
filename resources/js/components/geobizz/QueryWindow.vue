<template xmlns="v-content" >
    <v-card flat>
        <v-card-text>
            <v-row>
                <v-col cols="12" lg="6">
                    <v-select dense outlined :items="['Existing Data','Spreadsheets']" label="Dataset category" @change="setDatasets(selectedDataCategory)"  v-model="selectedDataCategory"></v-select>
                </v-col>
                <v-col cols="12" lg="6">
                    <v-select dense outlined :items="datsetItems" label="Datasets" v-model="selectedDataset" @change="getDatasetAttributes(selectedDataCategory,selectedDataset)"></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" lg="6">
                    <v-select dense outlined :items="attrlist" label="Attributes" @change="getUniqueValues(selectedDataCategory,selectedDataset,selectedAttribute)"  v-model="selectedAttribute"></v-select>
                </v-col>
                <v-col cols="12" lg="6">
                    <v-card outlined>
                        <v-card-text style="height: 100px;overflow-y: scroll;">
                            <v-list>
                                <v-list-item dense v-for="(item,key) in uniqueAttrList" :key="'selectedAttr'+key" @click="setSelectedAttr(item)">
                                    <v-list-item-title>{{item}}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>


            <v-row>
                <v-col cols="12" lg="4">
                    <v-text-field disabled v-model="selectedAttribute" dense outlined label="attribute"></v-text-field>
                </v-col>
                <v-col cols="12" lg="4">
                    <v-select dense outlined :items="['=','<','>','<>']" label="Operator" v-model="selectedOperator"></v-select>
                </v-col>
                <v-col cols="12" lg="4">
                    <v-text-field v-model="selectedValue" dense outlined label="value"></v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" lg="12">
                    <v-btn block color="primary" :disabled="!selectedOperator || !selectedValue || !selectedAttribute" @click="queryData(selectedDataCategory,selectedDataset)"> <v-icon>settings</v-icon> &nbsp;&nbsp; Run </v-btn>
                </v-col>
            </v-row>
            <v-row>
                <v-list-item v-if="formulas" v-for="(item,key) in formulas">
                    <v-list-item-action>
                        <v-checkbox input-value="true" @change="showHideFormula(key,$event)"></v-checkbox>
                    </v-list-item-action>

                    <v-list-item-content>
                        <v-list-item-title> {{item.dataset}} </v-list-item-title>
                        <v-list-item-subtitle> {{item.data.length}} Data &nbsp; &nbsp;</v-list-item-subtitle>
                            <xlsx-workbook >
                                <xlsx-sheet
                                    :collection="sheet.data"
                                    v-for="sheet in sheets"
                                    :key="sheet.name"
                                    sheet-name="geobizzOutPut"
                                />
                                <xlsx-download>
                                        <a href="#" style="color: primary">Download</a>
                                </xlsx-download>
                            </xlsx-workbook>


                    </v-list-item-content>

                    <v-list-item-icon>
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
                                <v-list-item @click="setSelectedDataset(key)">
                                    <v-list-item-title> <v-icon>filter_list</v-icon> &nbsp;&nbsp;Attribute table</v-list-item-title>
                                </v-list-item>

                                <v-list-item @click="setSelectedStyle(key)">
                                    <v-list-item-title> <v-icon>palette</v-icon> &nbsp;&nbsp;Style</v-list-item-title>
                                </v-list-item>

                                <v-list-item  @click="removeLayerData(key)">
                                    <v-list-item-title> <v-icon>delete</v-icon> &nbsp;&nbsp;Delete</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-list-item-icon>

                </v-list-item>
            </v-row>


        </v-card-text>


        <v-dialog
            v-model="selectedTableDialog"
            max-width="1000px"
        >
            <v-card>
                <v-card-title style="align-content: center;justify-content: center;align-self: center">
                    <v-icon>poll</v-icon> &nbsp;&nbsp; Selected Table
                </v-card-title>
                <v-card-text v-if="selectedQueryData">
                    <v-data-table
                        :headers="attrHeaders"
                        :items="selectedQueryData"
                        item-key="id"
                        :items-per-page="10"
                        @click:row="rowClicked($event)"
                    >

                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="selectedTableDialog = false">close</v-btn>
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
                    <v-slider label="scale" thumb-label min="0.5" max="10"  v-model="scaleSlider">
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

    </v-card>

</template>

<script>
    import {store} from "../store/store_geobizz";
    import $ from "jquery";
    import {mapState} from "vuex";
    import {XlsxRead, XlsxTable, XlsxSheets, XlsxJson, XlsxWorkbook, XlsxSheet, XlsxDownload } from "vue-xlsx/dist/vue-xlsx.es.js";
    export default {
        name: "QueryWindow",
        components: {
            store,
            XlsxRead,
            XlsxTable,
            XlsxSheets,
            XlsxJson,
            XlsxWorkbook,
            XlsxSheet,
            XlsxDownload
        },
        data: () => ({
            hover:true,
            user:{},
            mainDataset:[],
            datsetItems:[],
            uniqueAttrList:[],
            selectedAttribute:'',
            selectedDataset:'',
            selectedDataCategory:'',
            selectedValue:'',
            attrlist:[],
            selectedOperator:'',
            formulas:[],
            allQueryDataSet:[],
            selectedQueryData:[],
            attrHeaders:[],
            selectedTableDialog:false,
            styleDialog:false,
            queryColor:'#FF0000',
            scaleSlider:3.5,
            sheets: [],

        }),
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
                queryFormulas: state => state.queryFormulas,
                excelFilesCollection: state => state.excelFilesCollection,
            }),
            qFormulas(){
                return store.state.queryFormulas;
            },
            excelData(){
                return store.state.excelFilesCollection;
            }
        },
        created(){

        },
        methods:{
            //=========================================================get primary data ================================
            getDatasetAttributes(category,dataset){
                if (category === 'Existing Data') {
                    axios.post('/api/datasetAttributes', {dataset: dataset}, {})
                        .then(response => {
                            this.attrlist = response.data.data;
                        }).catch((error) => {
                        if (error.response.status === 401) {
                            window.setLogout();
                        }
                        this.snackMessage = error;
                        this.snackColor = 'error';
                        this.snackbar = true;
                        this.saveLoader = false;
                        console.log(error);// this will log an empty object with an error property
                    });
                }else {
                    let get_dataset = _.find(this.excelData,{'title':dataset});
                    if (get_dataset){
                        let attributes = Object.keys(get_dataset.data[0]);
                        this.attrlist = attributes;
                    }
                }


            },
            //=========================================================get unique values================================
            getUniqueValues(category,dataset,field){
                if (category === 'Existing Data') {
                    axios.post('/api/uniqueValues', {dataset: dataset,field:field}, {})
                        .then(response => {
                            this.uniqueAttrList = response.data.data;
                        }).catch((error) => {
                        if (error.response.status === 401) {
                            window.setLogout();
                        }
                        this.snackMessage = error;
                        this.snackColor = 'error';
                        this.snackbar = true;
                        this.saveLoader = false;
                        console.log(error);// this will log an empty object with an error property
                    });
                }else {

                    let get_dataset = _.find(this.excelData,{'title':dataset});
                    if (get_dataset){
                        let group = _.groupBy(get_dataset.data,field)
                        this.uniqueAttrList = Object.keys(group);
                    }
                }


            },
            //=========================================================Query Data=======================================
            queryData(category,dataset){
                if (category === 'Existing Data') {
                    axios.post('/api/queryData', {dataset: dataset,field:this.selectedAttribute,value:this.selectedValue,operator:this.selectedOperator}, {})
                        .then(response => {
                            let that = this;
                            let data_arr = [];
                            let color_codes = response.data.color_codes;
                            $.each(response.data.data,function (key,val) {

                                let category = null;

                                color_codes && color_codes.length > 0 ? category = color_codes[0].field : category = null;
                                if (category) {
                                    let exist = _.find(color_codes, {'value': val[category]});
                                    if (exist) {
                                        val.icon = {
                                            path: google.maps.SymbolPath.CIRCLE,
                                            scale: 3.5,
                                            fillColor: exist.color,
                                            fillOpacity: 0.6,
                                            strokeWeight: 0.3,
                                            labelOrigin: {x: -7, y: 0}
                                        };
                                    } else {
                                        val.icon = {
                                            path: google.maps.SymbolPath.CIRCLE,
                                            scale: 3.5,
                                            fillColor: '#FF0000',
                                            fillOpacity: 0.6,
                                            strokeWeight: 0.3,
                                            labelOrigin: {x: -7, y: 0}
                                        };
                                    }
                                }else {
                                    val.icon = {
                                        path: google.maps.SymbolPath.CIRCLE,
                                        scale: 3.5,
                                        fillColor: "#FF0000",
                                        fillOpacity: 0.6,
                                        strokeWeight: 0.3,
                                        labelOrigin: {x: -7, y: 0}
                                    };
                                }
                                val.dataset = 'dataset '+that.formulas.length+1;
                                data_arr.push(val);
                            });
                            let id = this.formulas.length+1;
                            this.formulas.push({
                                'dataset' : 'dataset '+id,
                                'data' : data_arr
                            });
                            this.allQueryDataSet = _.concat(this.allQueryDataSet,data_arr)
                            store.commit('setQueryFormulas',this.allQueryDataSet);

                        }).catch((error) => {
                        if (error.response.status === 401) {
                            window.setLogout();
                        }
                        this.snackMessage = error;
                        this.snackColor = 'error';
                        this.snackbar = true;
                        this.saveLoader = false;
                        console.log(error);// this will log an empty object with an error property
                    });
                }else {

                    let get_dataset = _.find(this.excelData,{'title':dataset});
                    console.log("fucking dataset",get_dataset)
                    if (get_dataset){
                        let dataset = [];
                        let that = this;
                        _.map(get_dataset.data,function (o) {
                            let attr = o[that.selectedAttribute];

                            o.dataset = 'dataset '+that.formulas.length+1;
                                // console.log(attr+" "+that.selectedOperator+" "+that.selectedValue)
                            switch (that.selectedOperator) {
                                case '=' :  if (isNaN(parseFloat(attr))){
                                    if(attr.toLowerCase() === that.selectedValue.toLowerCase()){
                                        dataset.push(o);
                                    }
                                }else{
                                    if(parseFloat(attr) === parseFloat(that.selectedValue)){
                                        dataset.push(o);
                                    }
                                }
                                    break;
                                case ">": if (!isNaN(parseFloat(attr))){
                                    if(parseFloat(attr) > parseFloat(that.selectedValue)){
                                        dataset.push(o);
                                    }
                                }
                                    break;
                                case "<": if (!isNaN(parseFloat(attr))){
                                    if(parseFloat(attr) < parseFloat(that.selectedValue)){
                                        dataset.push(o);
                                    }
                                }
                                    break;
                                case "<>":  if(attr !== that.selectedValue){
                                    dataset.push(o);
                                }
                                    break;
                            }
                        });
                        let id = this.formulas.length+1;
                        this.formulas.push({
                            'dataset' : 'dataset '+id,
                            'data' : dataset
                        });
                        this.allQueryDataSet = _.concat(this.allQueryDataSet,dataset)
                        store.commit('setQueryFormulas',this.allQueryDataSet);
                    }
                }


            },

            setDatasets(category){
                if (category === 'Existing Data'){
                    this.datsetItems = ['timber census data','land data']
                }else{
                    let excel_data = store.state.excelFilesCollection;
                    let that = this;
                    let names = [];
                    $.each(excel_data,function (key,val) {
                        names.push(val.title);
                    })
                    this.datsetItems = names;
                }
            },

            setSelectedAttr(value){
                this.selectedValue = value;
            },

            removeLayerData(key){
                this.formulas.splice(key,1);
                let data_arr = [];
                _.map(this.allQueryDataSet, function (o) {
                    if (o.dataset !== 'dataset ' + key + 1) {
                        data_arr.push(o);
                    }
                });
                store.commit('setQueryFormulas', data_arr);

            },

            showHideFormula(key,event){
                if (event) {
                    let existing_arr = store.state.queryFormulas;
                    _.map(this.allQueryDataSet, function (o) {
                        if (o.dataset === 'dataset ' + key + 1) {
                            existing_arr.push(o);
                        }
                    });
                    store.commit('setQueryFormulas', existing_arr);

                }else{
                    let data_arr = [];
                    _.map(this.allQueryDataSet, function (o) {
                        if (o.dataset !== 'dataset ' + key + 1) {
                            data_arr.push(o);
                        }
                    });
                    store.commit('setQueryFormulas', data_arr);

                }
            },

            setSelectedDataset(key){
                let dataset = this.formulas[key];
                let that = this;
                let data = Object.keys(dataset.data[0]);
                $.each(data,function (key,val) {
                    that.attrHeaders.push({
                        text:val,
                        value:val,
                    })
                })


                this.selectedQueryData = dataset.data;
                this.selectedTableDialog = true;
                this.sheets.push({
                    name: 'KPMG dataset',
                    data: dataset.data
                });

            },

            setSelectedStyle(key){
                let dataset = this.formulas[key];
                this.selectedQueryData = dataset.data;
                this.styleDialog = true;
            },

            applyStyle(){
                let that = this;
                let dataset = [];
                $.each(this.selectedQueryData,function (key,val) {
                    val.icon = {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 3.5,
                        fillColor: that.queryColor,
                        fillOpacity: 0.6,
                        strokeWeight: 0.3,
                        labelOrigin: {x: -7, y: 0}
                    };
                    dataset.push(val);
                });

                store.commit('setQueryFormulas', dataset);
            },

            resetStyle(){

            },

            rowClicked(event){
                this.gotoXY(event.lat,event.lng,18)
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

<style scoped>


</style>
