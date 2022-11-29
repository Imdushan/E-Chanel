<template>
    <div>
        <v-container>
          <v-row>
              <v-col cols="12" md="12">
                  <p>Home / Dashboard</p>
              </v-col>
            <v-col cols="12" md="12">
              <v-data-table
                :headers="headers"
                :items="saveUniversityLists"
                sort-by="calories"
                class="elevation-1"
                :loading="getAllDetailsLoading"
              >

                <template v-slot:top>
                  <v-toolbar flat>
                    <v-toolbar-title>University Registration</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog v-model="dialogFarmerBulkRegistration" max-width="100%">
                      <template v-slot:activator="{ on, attrs }">
                        <!-- <v-btn
                          color="primary"
                          dark
                          class="mb-2"
                          v-bind="attrs"
                          v-on="on"
                        >
                         <v-icon>mdi-account-plus</v-icon> &nbsp; New Farmer Registration
                        </v-btn> -->
                        &nbsp;
                        <v-btn
                          color="secondary"
                          dark
                          class="mb-2"
                          v-bind="attrs"
                          v-on="on"
                        >
                         <v-icon>mdi-school</v-icon> &nbsp; University Registration
                        </v-btn>
                      </template>
                      <v-card>
                        <v-card-text>
                          <v-container>
                            <v-row>
                              <v-col cols="12" sm="6" md="12">
                                <h3>University Registration</h3>
                              </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Name"
                                      required
                                      v-model="unName"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Reg No"
                                      v-model="unRegNo"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Discrict"
                                      v-model="unDistrict"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="world ranking"
                                      v-model="unWorldRan"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Iland ranking"
                                      v-model="unIlandEanking"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="University Description"
                                      v-model="unDescription"
                                      ></v-text-field>
                                  </v-col>

                            </v-row>
                          </v-container>
                        </v-card-text>

                        <v-card-actions>
                          <v-spacer></v-spacer>
                          <v-btn color="blue darken-1" text @click="dialogFarmerBulkRegistration=false">
                            Close
                          </v-btn>
                          <v-btn color="blue darken-1" :loading="registrationLoading" text @click="saveUniversityRegDetails()">
                            Save
                          </v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item.actions="{ item }">
                  <v-btn color="green white--text" x-small @click="openEditRegUniversity(item)"> <v-icon small>mdi-account-edit</v-icon> &nbsp; Edit
                  </v-btn>
                  <v-btn color="green white--text" x-small @click="openAddingCourses(item)"> <v-icon small>mdi-plus</v-icon> &nbsp; Adding Courses
                  </v-btn>
                  <v-btn color="orange white--text" x-small @click="openMoreInfoOfUniversity(item)"> <v-icon small>mdi-book-information-variant</v-icon> &nbsp; More Info
                  </v-btn>
                  <v-btn color="red white--text" x-small @click="openDeleteFarmerUser(item)"> <v-icon small>mdi-trash-can</v-icon> &nbsp; Delete
                  </v-btn>
                </template>
              </v-data-table>
            </v-col>
          </v-row>
          <v-snackbar v-model="snackbar" :multi-line="false" :color="snackColor">{{ snackMessage }}</v-snackbar>
        </v-container>

        <v-dialog
          v-model="openAddingCoursesDialog"
          persistent
          max-width="800px"
          >
          <v-card>
              <v-card-title>
              <span class="text-h5">University Courses Register Form</span>
              </v-card-title>
              <v-card-text>
              <v-container>
                  <v-row>
                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Cours Name"
                                      required
                                      v-model="cours_name"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Cours fee"
                                      v-model="cours_fee"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Cours duration"
                                      v-model="cours_duration"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                  <v-combobox
                                    dense
                                    x-small
                                    outlined
                                    :items="cours_category_list"
                                    v-model="cours_category"
                                    label="Cours Category"
                                    class="pb-0 mb-0"
                                ></v-combobox>

                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                  <v-combobox
                                    dense
                                    x-small
                                    outlined
                                    :items="cours_type_list"
                                    v-model="cours_type"
                                    label="Cours Type"
                                    class="pb-0 mb-0"
                                ></v-combobox>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                  <v-combobox
                                    dense
                                    x-small
                                    outlined
                                    :items="entering_qulification_list"
                                    v-model="entering_qulification"
                                    label="Entrance Qualification"
                                    class="pb-0 mb-0"
                                ></v-combobox>
                                </v-col>
                                <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >

                                <v-combobox
                                    dense
                                    x-small
                                    outlined
                                    :items="stream_list"
                                    v-model="stream"
                                    label="A/L Stream"
                                    class="pb-0 mb-0"
                                ></v-combobox>

                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="12"
                                      md="12"
                                  >
                                  <v-textarea
                                    v-model="cours_discription"
                                    label="Cours Discription"
                                    class="ml-1 mr-1"
                                    dense
                                    outlined
                                    clearable
                                    >
                                    </v-textarea>
                                </v-col>
                  </v-row>
              </v-container>
              </v-card-text>
              <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="blue darken-1"
                  text
                  @click="openAddingCoursesDialog = false"
              >
                  Close
              </v-btn>
              <v-btn
              :loading="registrationLoading"
                  color="blue darken-1"
                  text
                  @click="saveUniversityCourses()"
              >
                  Save
              </v-btn>
              </v-card-actions>
          </v-card>
          </v-dialog>


          <v-dialog
          v-model="openFarmerEditDialog"
          persistent
          max-width="800px"
          >
          <v-card>
              <v-card-title>
              <span class="text-h5">University Edit Form</span>
              </v-card-title>
              <v-card-text>
              <v-container>
                  <v-row>
                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Name"
                                      required
                                      v-model="editunName"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Reg No"
                                      v-model="editunRegNo"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="University District"
                                      v-model="editunDistrict"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="World Rancking"
                                      v-model="editunWorldRan"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Iland Ranking"
                                      v-model="editunIlandEanking"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col
                                      cols="12"
                                      sm="6"
                                      md="4"
                                  >
                                      <v-text-field
                                      label="Description"
                                      v-model="editunDescription"
                                      ></v-text-field>
                                  </v-col>
                  </v-row>
              </v-container>
              </v-card-text>
              <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="blue darken-1"
                  text
                  @click="openFarmerEditDialog = false"
              >
                  Close
              </v-btn>
              <v-btn
              :loading="registrationLoading"
                  color="blue darken-1"
                  text
                  @click="editniversityDetails()"
              >
                  Save
              </v-btn>
              </v-card-actions>
          </v-card>
          </v-dialog>

          <v-dialog
            v-model="moreInfoDialog"
            persistent
            max-width="600px"
          >
            <v-card>
              <v-card-title>
                <span class="text-h5">University Details</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row no-gutters>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>Name</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreUniversityDetails.unName}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>Register Number</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreUniversityDetails.unRegNo}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>District</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreUniversityDetails.unDistrict}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>University WorldRan</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreUniversityDetails.unWorldRan}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>unDescription</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreUniversityDetails.unDescription}}
                    </v-col>
                  </v-row>

                  <v-row>
                    <v-col v-for="item in cousrListById" cols="12" lg="12" md="12" sm="12" class="pl-10 py-5 mt-10">

                        <v-card
                            class="mx-auto"

                            outlined
                        >
                            <v-list-item three-line>
                            <v-list-item-content>

                                <v-list-item-title class="text-h5 mb-1">
                                    {{item.cours_name}}
                                </v-list-item-title>
                                <v-list-item-subtitle>{{item.cours_discription}}</v-list-item-subtitle>
                                <v-list-item-subtitle>{{item.cours_fee}}</v-list-item-subtitle>
                                <v-list-item-subtitle>{{item.cours_duration}}</v-list-item-subtitle>
                                <v-list-item-subtitle>{{item.entering_qulification}}</v-list-item-subtitle>
                                <v-list-item-subtitle>{{item.cours_category}}</v-list-item-subtitle>
                                <v-list-item-subtitle>{{item.cours_type}}</v-list-item-subtitle>
                            </v-list-item-content>


                            </v-list-item>

                            <v-card-actions>
                                <v-btn
                                color="red"
                                outlined
                                rounded
                                text
                                @click="removeCoruses(item.id)"
                            >
                                Remove
                            </v-btn>

                            </v-card-actions>
                        </v-card>
                    </v-col>
                  </v-row>
                 <!-- <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>DSD</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreFarmerDetails.dsd}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>Agri SC</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreFarmerDetails.agri_sc}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>GND</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreFarmerDetails.gnd}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>Village</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreFarmerDetails.village}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-chip>Full Land Area</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreFarmerDetails.full_land_area + " " + moreFarmerDetails.full_land_area_unit}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="5"
                    >
                      <v-chip>Family Male Members</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreFarmerDetails.family_male_members}}
                    </v-col>
                  </v-row>
                  <v-row no-gutters class="mt-1">
                    <v-col
                      cols="12"
                      md="5"
                    >
                      <v-chip>Family Female Members</v-chip>
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      {{moreFarmerDetails.family_female_members}}
                    </v-col>
                  </v-row>-->
                </v-container>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="moreInfoDialog = false"
                >
                  Close
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog
          v-model="openFarmerDeleteDialog"
          persistent
          max-width="800px"
          >
          <v-card>
              <v-card-title>
              <span class="text-h5">University Delete Form</span>
              </v-card-title>
              <v-card-text>
              <v-container>
                  <v-row>
                  <v-col
                      cols="12"
                      sm="6"
                      md="12"
                  >

                      Are you sure do you want to delete this university ?
                  </v-col>

                  </v-row>
              </v-container>
              </v-card-text>
              <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="blue darken-1"
                  text
                  @click="openFarmerDeleteDialog = false"
              >
                  Close
              </v-btn>
              <v-btn
              :loading="registrationLoading"
                  color="red darken-1"
                  text
                  @click="deleteUniversity()"
              >
                  Yes Delete
              </v-btn>
              </v-card-actions>
          </v-card>
          </v-dialog>

    </div>
  </template>

  <script>
  import * as XLSX from "xlsx";
  import {XlsxRead, XlsxTable, XlsxSheets, XlsxJson, XlsxWorkbook, XlsxSheet, XlsxDownload } from "vue-xlsx/dist/vue-xlsx.es.js";
  export default {
    name: "DashboardView",

    components:{
        XlsxRead,
          XlsxTable,
          XlsxSheets,
          XlsxJson,
          XlsxWorkbook,
          XlsxSheet,
          XlsxDownload
    },

    data: () => ({

        stream_list:['Art','Commerce','Bio','Physical Science','Maths','All'],
        stream:null,

      farmerExcelSheet:null,
      selectedSheet: null,
      farmerDatasetFromExcel:[],
      isUploadCorrectExcelSheet:false,
      dialog: false,
      dialogFarmerBulkRegistration:false,
      getAllDetailsLoading:false,
      moreFarmerDetails:{},
      moreUniversityDetails:{},
      moreInfoDialog:false,
      snackbar: false,
        snackColor: "",
        snackMessage: "",
        dialogDelete: false,
        headers: [
          {
            text: 'Name',
            align: 'start',
            sortable: false,
            value: 'unName',
          },
          { text: 'Reg No', value: 'unRegNo' },
          { text: 'District', value: 'unDistrict' },
          { text: 'world ranking', value: 'unWorldRan' },
          { text: 'Iland ranking', value: 'unIlandEanking' },
          { text: 'Actions', value: 'actions', sortable: false },
        ],
        instituteBranchMultipleVal:[
          {
            branchName: "",
            branchDistrict: ""
          }
        ],

        cours_category_list:['Certificate','Diploma','HND','Degree'],
        cours_type_list:['Sri lankan','Overseas','Online'],
        entering_qulification_list:['Pass A/L','pass O/L','AL and OL','No qualification','after O/L','after A/L'],

        cs_id:'',
        cours_name:'',
        cours_fee:'',
        cours_duration:'',
        cours_category:'',
        cours_type:'',
        entering_qulification:'',
        cours_discription:'',


        unName:"",
        unRegNo:"",
        unDistrict:"",
        unWorldRan:"",
        unIlandEanking:"",
        unDescription:"",
        registrationLoading:false,
        subInstituteDetails:[],
        districtName: [
          'Ampara',
          'Anuradhapura',
          'Badulla',
          'Batticaloa',
          'Colombo',
          'Galle',
          'Gampaha',
          'Hambantota',
          'Jaffna',
          'Kalutara',
          'Kandy',
          'Kegalle',
          'Kilinochchi',
          'Kurunegala',
          'Mannar',
          'Matale',
          'Matara',
          'Monaragala',
          'Mullaitivu',
          'Nuwara Eliya',
          'Polonnaruwa',
          'Puttalam',
          'Ratnapura',
          'Trincomalee',
          'Vavuniya'
        ],
        selectedDPDEditValues:{},
        openFarmerEditDialog:false,
        desserts: [],
        saveUniversityLists:[],

        openAddingCoursesDialog:false,
        universityId:'',

        editunid:'',
        editunName:'',
        editunRegNo:'',
        editunDistrict:'',
        editunWorldRan:'',
        editunIlandEanking:'',
        editunDescription:'',

        alluniversityList:{},
        UniversityLists:{},
        cousrListById:{},


        editName :"",
        editRegInsti :"",
        editRegNo :"",
        editSecretary :"",
        editTreasurer :"",
        editChairman :"",
        editUserId:"",
        UniversityId:"",
        deleteUserDetails:"",
        openFarmerDeleteDialog:false,
        editedIndex: -1,
        editedItem: {
          name: '',
          calories: 0,
          fat: 0,
          carbs: 0,
          protein: 0,
        },
        defaultItem: {
          name: '',
          calories: 0,
          fat: 0,
          carbs: 0,
          protein: 0,
        },
    }),
    mounted(){
      this.getAllRegUniversityList();
    },
    methods: {
      isLogOut() {
        window.setLogout();
      },

      onChange(event) {
          let that = this;
          console.log("EVENT...", event);
      //   that.farmerExcelSheet = event;
      },

      removeCoruses(couseId){
        //alert(couseId);
        let cs_id=couseId;
        this.saveUniversityLists = [];
          this.getAllDetailsLoading = true;
        let laravel = JSON.parse(window.Laravel);
        const header = {
                  'X-CSRF-TOKEN': laravel.csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                  'content-type': 'multipart/form-data',
        };

        axios.post('/api/wb/removeCoruses', {userID:laravel.user.depend_dpd_id,cs_id:cs_id}, header)
                      .then(response => {
                          if (response.data.http_status == 'success'){
                            //this.saveUniversityLists = response.data.data;
                            this.getAllDetailsLoading = false;
                            this.snackMessage = "Cours Deletted Successfully.";
                            this.snackColor = "success";
                            this.snackbar = true;
                            this.getAllRegUniversityList();
                            this.moreInfoDialog=false;

                          }else{
                            this.getAllDetailsLoading = false;
                            this.snackMessage = "Cours Delete Load Error.";
                            this.snackColor = "error";
                            this.snackbar = true;
                          }
                      }).catch((error) => {
                        this.getAllDetailsLoading = false;
                        this.snackMessage = error;
                        this.snackColor = "error";
                        this.snackbar = true;

                  });

      },

      clearFarmerBulkUpload(){
        this.farmerExcelSheet = null;
        this.farmerDatasetFromExcel = [];
        this.selectedSheet = null;
        this.isUploadCorrectExcelSheet = false;
      },

      getAllRegUniversityList(){
          this.saveUniversityLists = [];
          this.getAllDetailsLoading = true;
        let laravel = JSON.parse(window.Laravel);
        const header = {
                  'X-CSRF-TOKEN': laravel.csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                  'content-type': 'multipart/form-data',
        };

        axios.post('/api/wb/getAllRegUniversityList', {userID:laravel.user.depend_dpd_id}, header)
                      .then(response => {
                          if (response.data.http_status == 'success'){
                            this.saveUniversityLists = response.data.data;
                            this.getAllDetailsLoading = false;
                            this.snackMessage = "University Lists Load Successfully.";
                            this.snackColor = "success";
                            this.snackbar = true;
                          }else{
                            this.getAllDetailsLoading = false;
                            this.snackMessage = "University Lists Load Error.";
                            this.snackColor = "error";
                            this.snackbar = true;
                          }
                      }).catch((error) => {
                        this.getAllDetailsLoading = false;
                        this.snackMessage = error;
                        this.snackColor = "error";
                        this.snackbar = true;

                  });
      },

      saveUniversityCourses(){
        this.registrationLoading = true;
        let laravel = JSON.parse(window.Laravel);
        const header = {
                  'X-CSRF-TOKEN': laravel.csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                  'content-type': 'multipart/form-data',
        };
        let formData = new FormData();

        formData.append("user_id", laravel.user.depend_dpd_id);
        formData.append("id", "new");
        formData.append("un_id", this.universityId);
        formData.append("cours_name", this.cours_name);
        formData.append("cours_fee", this.cours_fee);
        formData.append("cours_duration", this.cours_duration);
        formData.append("cours_category", this.cours_category);
        formData.append("cours_type", this.cours_type);
        formData.append("entering_qulification", this.entering_qulification);
        formData.append("cours_discription", this.cours_discription);
        formData.append("stream", this.stream);



        axios.post('/api/wb/saveUniversityCourses',formData, header)
                      .then(response => {
                          if (response.data.http_status == 'success'){
                            // this.formBPrintLoading = false;

                            this.snackMessage = "University Cours Registration Successfully.";
                            this.snackColor = "success";
                            this.snackbar = true;
                            this.openAddingCoursesDialog = false;
                            this.registrationLoading = false;
                            this.getAllRegUniversityList();
                          }else{
                            this.registrationLoading = false;
                            this.snackMessage = "University Cours Registration Error.";
                            this.snackColor = "error";
                            this.openAddingCoursesDialog = false;
                            this.snackbar = true;
                          }
                      }).catch((error) => {
                        this.registrationLoading = false;
                        this.snackMessage = error;
                        this.snackColor = "error";
                        this.openAddingCoursesDialog = false;
                        this.snackbar = true;

                  });
      },

      saveUniversityRegDetails(){
          this.registrationLoading = true;
        let laravel = JSON.parse(window.Laravel);
        const header = {
                  'X-CSRF-TOKEN': laravel.csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                  'content-type': 'multipart/form-data',
        };
        let formData = new FormData();

        formData.append("dpdUserID", laravel.user.depend_dpd_id);
        formData.append("id", "new");
        formData.append("unName", this.unName);
        formData.append("unRegNo", this.unRegNo);
        formData.append("unDistrict", this.unDistrict);
        formData.append("unWorldRan", this.unWorldRan);
        formData.append("unIlandEanking", this.unIlandEanking);
        formData.append("unDescription", this.unDescription);

        axios.post('/api/wb/saveUniversityRegDetails',formData, header)
                      .then(response => {
                          if (response.data.http_status == 'success'){
                            // this.formBPrintLoading = false;

                            this.snackMessage = "University Registration Successfully.";
                            this.snackColor = "success";
                            this.snackbar = true;
                            this.dialogFarmerBulkRegistration = false;
                            this.registrationLoading = false;
                            this.getAllRegUniversityList();
                          }else{
                            this.registrationLoading = false;
                            this.snackMessage = "University Registration Error.";
                            this.snackColor = "error";
                            this.dialogFarmerBulkRegistration = false;
                            this.snackbar = true;
                          }
                      }).catch((error) => {
                        this.registrationLoading = false;
                        this.snackMessage = error;
                        this.snackColor = "error";
                        this.dialogFarmerBulkRegistration = false;
                        this.snackbar = true;

                  });
      },

      openEditRegUniversity(item){
          this.editunid = item.id;
          this.editunName = item.unName;
        this.editunRegNo = item.unRegNo;
        this.editunDistrict = item.unDistrict;
        this.editunWorldRan = item.unWorldRan;
        this.editunIlandEanking = item.unIlandEanking;
        this.editunDescription = item.unDescription;
          this.openFarmerEditDialog = true;
      },

      openAddingCourses(item){
       this.universityId = item.id;
       this.openAddingCoursesDialog = true;


      },

      openDeleteFarmerUser(item){
          this.UniversityId = item.id;
          this.deleteUserDetails = item.unName + " (" + item.unRegNo+" )";
          this.openFarmerDeleteDialog = true;
      },

      openMoreInfoOfUniversity(item){
        this.moreUniversityDetails = item;
        //this.getAllRegUniversityListwithCours();
        this.getUniversityCoursByID(item.id);
        this.moreInfoDialog = true;
      },

      getUniversityCoursByID(unID){

        let UniversityId= unID;

        let laravel = JSON.parse(window.Laravel);
            const header = {
                    'X-CSRF-TOKEN': laravel.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'content-type': 'multipart/form-data',
            };

            axios.post('/api/wb/getUniversityCoursByID', {userID:laravel.user.depend_dpd_id,UniversityId:UniversityId}, header)
                .then(response => {
                    if (response.data.http_status == 'success'){
                        let data=response.data.data;
                        this.cousrListById = data;
                       // this.UniversityLists = this.alluniversityList;
                        console.log('University Lists Load success.',data)
                    }else{

                        console.log('University Lists Load Error.');
                    }
                }).catch((error) => {
                    console.log('error',error);

            });
      },

      getAllRegUniversityListwithCours(){

        let laravel = JSON.parse(window.Laravel);
        const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
        };

        axios.post('/api/wb/getAllRegUniversityListwithCours', {userID:laravel.user.depend_dpd_id}, header)
            .then(response => {
                if (response.data.http_status == 'success'){
                    this.alluniversityList=response.data.data;
                    this.UniversityLists = this.alluniversityList;
                    console.log('University Lists Load success.',this.UniversityLists)
                }else{

                    console.log('University Lists Load Error.');
                }
            }).catch((error) => {
                console.log('error',error);

        });

    },

      deleteUniversity(){
        this.registrationLoading = true;
        let laravel = JSON.parse(window.Laravel);
        const header = {
                  'X-CSRF-TOKEN': laravel.csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                  'content-type': 'multipart/form-data',
        };

        let formData = new FormData();
        formData.append("UniversityId", this.UniversityId);

        alert(this.UniversityId);

        axios.post('/api/wb/deleteUniversity',formData, header)
                      .then(response => {
                          if (response.data.http_status == 'success'){

                            this.snackMessage = "University Delete Successfully.";
                            this.snackColor = "success";
                            this.snackbar = true;
                            this.openFarmerDeleteDialog = false;
                            this.registrationLoading = false;
                            this.getAllRegUniversityList();
                          }else{
                            this.registrationLoading = false;
                            this.snackMessage = "University Delete Error.";
                            this.snackColor = "error";
                            this.openFarmerDeleteDialog = false;
                            this.snackbar = true;
                          }
                      }).catch((error) => {
                        this.registrationLoading = false;
                        this.snackMessage = error;
                        this.snackColor = "error";
                        this.openFarmerDeleteDialog = false;
                        this.snackbar = true;

                  });
      },

      editniversityDetails(){
          this.registrationLoading = true;
        let laravel = JSON.parse(window.Laravel);
        const header = {
                  'X-CSRF-TOKEN': laravel.csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                  'content-type': 'multipart/form-data',
        };

        let formData = new FormData();
        formData.append("dpdUserID", "1");
        formData.append("id", this.editunid );
        formData.append("unName", this.editunName);
        formData.append("unRegNo", this.editunRegNo);
        formData.append("unDistrict", this.editunDistrict);
        formData.append("unWorldRan", this.editunWorldRan);
        formData.append("unIlandEanking", this.editunIlandEanking);
        formData.append("unDescription", this.editunDescription);

        axios.post('/api/wb/saveUniversityRegDetails',formData, header)
                      .then(response => {
                          if (response.data.http_status == 'success'){

                            this.snackMessage = "Producer Society Edit Successfully.";
                            this.snackColor = "success";
                            this.snackbar = true;
                            this.openFarmerEditDialog = false;
                            this.registrationLoading = false;
                            this.getAllRegUniversityList();
                          }else{
                            this.registrationLoading = false;
                            this.snackMessage = "Producer Society Edit Error.";
                            this.snackColor = "error";
                            this.openFarmerEditDialog = false;
                            this.snackbar = true;
                          }
                      }).catch((error) => {
                        this.registrationLoading = false;
                        this.snackMessage = error;
                        this.snackColor = "error";
                        this.openFarmerEditDialog = false;
                        this.snackbar = true;

                  });
      },
    },
  };
  </script>
