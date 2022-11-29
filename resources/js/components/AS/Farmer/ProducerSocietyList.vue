<template>
  <div>
      <v-container>
        <v-row>
            <v-col cols="12" md="12">
                <p>Farmer / Producer Society Register</p>
            </v-col>
          <v-col cols="12" md="12">
            <v-data-table
              :headers="headers"
              :items="ProducerSocietyDetails"
              sort-by="calories"
              class="elevation-1"
              :loading="getAllDetailsLoading"
            >
            <template v-slot:item.status="{ item }">
                <v-chip color="#f7cb73" small v-if="item.status == 0">Pending</v-chip>
                <v-chip color="green" class="white--text" small v-if="item.status == 1">Verified</v-chip>
                <v-chip color="red" class="white--text" small v-if="item.status == 2">Rejected</v-chip>
            </template>
              <template v-slot:top>
                <v-toolbar flat>
                  <v-toolbar-title>Producer Society Registration</v-toolbar-title>
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
                       <v-icon>mdi-account-plus</v-icon> &nbsp; Producer Society Registration
                      </v-btn>
                    </template>
                    <v-card>
                      <v-card-text>
                        <v-container>
                          <v-row>
                            <v-col cols="12" sm="6" md="12">
                              <h3>Producer Society Registration</h3>
                            </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="Name"
                                    required
                                    v-model="psName"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="Reg No"
                                    v-model="psRegNo"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="Reg Institute"
                                    v-model="psRegInsti"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="Chairman"
                                    v-model="psChairman"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="secretary"
                                    v-model="psSecretary"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="treasurer"
                                    v-model="psTreasurer"
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
                        <v-btn color="blue darken-1" :loading="registrationLoading" text @click="saveProducerSocietyDetails()">
                          Save
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-toolbar>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-btn color="green white--text" x-small @click="openEditProducerSociety(item)"> <v-icon small>mdi-account-edit</v-icon> &nbsp; Edit
                </v-btn>
                <!-- <v-btn color="orange white--text" x-small @click="openMoreInfoOfFarmer(item)"> <v-icon small>mdi-book-information-variant</v-icon> &nbsp; More Info
                </v-btn> -->
                <!-- <v-btn color="red white--text" x-small @click="openDeleteFarmerUser(item)"> <v-icon small>mdi-trash-can</v-icon> &nbsp; Delete
                </v-btn> -->
              </template>
            </v-data-table>
          </v-col>
        </v-row>
        <v-snackbar v-model="snackbar" :multi-line="false" :color="snackColor">{{ snackMessage }}</v-snackbar>
      </v-container>

        <v-dialog
        v-model="openFarmerEditDialog"
        persistent
        max-width="800px"
        >
        <v-card>
            <v-card-title>
            <span class="text-h5">Producer Society Update Form</span>
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
                                    v-model="editName"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="Reg No"
                                    v-model="editRegNo"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="Reg Institute"
                                    v-model="editRegInsti"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="Chairman"
                                    v-model="editChairman"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="secretary"
                                    v-model="editSecretary"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                    md="4"
                                >
                                    <v-text-field
                                    label="treasurer"
                                    v-model="editTreasurer"
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
                @click="editProducerSocietyDetails()"
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
              <span class="text-h5">Farmer Details</span>
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
                    {{moreFarmerDetails.name}}
                  </v-col>
                </v-row>
                <v-row no-gutters class="mt-1">
                  <v-col
                    cols="12"
                    md="4"
                  >
                    <v-chip>NIC</v-chip>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                  >
                    {{moreFarmerDetails.nic}}
                  </v-col>
                </v-row>
                <v-row no-gutters class="mt-1">
                  <v-col
                    cols="12"
                    md="4"
                  >
                    <v-chip>Gender</v-chip>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                  >
                    {{moreFarmerDetails.gender}}
                  </v-col>
                </v-row>
                <v-row no-gutters class="mt-1">
                  <v-col
                    cols="12"
                    md="4"
                  >
                    <v-chip>Civil Status</v-chip>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                  >
                    {{moreFarmerDetails.civil_status}}
                  </v-col>
                </v-row>
                <v-row no-gutters class="mt-1">
                  <v-col
                    cols="12"
                    md="4"
                  >
                    <v-chip>Address</v-chip>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                  >
                    {{moreFarmerDetails.address}}
                  </v-col>
                </v-row>
                <v-row no-gutters class="mt-1">
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
                </v-row>
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
            <span class="text-h5">Farmer Delete Form</span>
            </v-card-title>
            <v-card-text>
            <v-container>
                <v-row>
                <v-col
                    cols="12"
                    sm="6"
                    md="12"
                >
                Farmer - {{deleteUserDetails}} <br>
                    Are you sure do you want to delete this farmer ?
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
                @click="deleteFarmerDetails()"
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
  name: "ProducerSocietyList",

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
    farmerExcelSheet:null,
    selectedSheet: null,
    farmerDatasetFromExcel:[],
    isUploadCorrectExcelSheet:false,
    dialog: false,
    dialogFarmerBulkRegistration:false,
    getAllDetailsLoading:false,
    moreFarmerDetails:{},
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
          value: 'name',
        },
        { text: 'Reg No', value: 'reg_no' },
        { text: 'chairman', value: 'chairman' },
        { text: 'secretary', value: 'secretary' },
        { text: 'treasurer', value: 'treasurer' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      instituteBranchMultipleVal:[
        {
          branchName: "",
          branchDistrict: ""
        }
      ],
      psName:"",
      psRegInsti:"",
      psChairman:"",
      psRegNo:"",
      psSecretary:"",
      psTreasurer:"",
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
      ProducerSocietyDetails:[],
      editName :"",
      editRegInsti :"",
      editRegNo :"",
      editSecretary :"",
      editTreasurer :"",
      editChairman :"",
      editUserId:"",
      deleteUserId:"",
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
    this.getAllProducerSocietyDetails();
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

    clearFarmerBulkUpload(){
      this.farmerExcelSheet = null;
      this.farmerDatasetFromExcel = [];
      this.selectedSheet = null;
      this.isUploadCorrectExcelSheet = false;
    },

    getAllProducerSocietyDetails(){
        this.ProducerSocietyDetails = [];
        this.getAllDetailsLoading = true;
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };

      axios.post('/api/asW/getAllProducerSocietyDetails', {userID:laravel.user.depend_dpd_id}, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){
                          this.ProducerSocietyDetails = response.data.data;
                          this.getAllDetailsLoading = false;
                          this.snackMessage = "Producer Society Load Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                        }else{
                          this.getAllDetailsLoading = false;
                          this.snackMessage = "Producer Society Load Error.";
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

    saveProducerSocietyDetails(){
        this.registrationLoading = true;
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };
      let formData = new FormData();

      formData.append("dpdUserID", laravel.user.depend_dpd_id);
      formData.append("psName", this.psName);
      formData.append("psRegInsti", this.psRegInsti);
      formData.append("psChairman", this.psChairman);
      formData.append("psRegNo", this.psRegNo);
      formData.append("psSecretary", this.psSecretary);
      formData.append("psTreasurer", this.psTreasurer);

      axios.post('/api/asW/saveProducerSocietyDetails',formData, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){
                          // this.formBPrintLoading = false;

                          this.snackMessage = "Producer Society Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                          this.dialogFarmerBulkRegistration = false;
                          this.registrationLoading = false;
                          this.getAllProducerSocietyDetails();
                        }else{
                          this.registrationLoading = false;
                          this.snackMessage = "Producer Society Error.";
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

    openEditProducerSociety(item){
        this.editUserId = item.id;
        this.editName = item.name;
      this.editRegInsti = item.registered_institute;
      this.editRegNo = item.reg_no;
      this.editSecretary = item.secretary;
      this.editTreasurer = item.treasurer;
      this.editChairman = item.chairman;
        this.openFarmerEditDialog = true;
    },

    openDeleteFarmerUser(item){
        this.deleteUserId = item.id;
        this.deleteUserDetails = item.name + " (" + item.nic+" )";
        this.openFarmerDeleteDialog = true;
    },

    openMoreInfoOfFarmer(item){
      this.moreFarmerDetails = item;
      this.moreInfoDialog = true;
    },

    deleteFarmerDetails(){
      this.registrationLoading = true;
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };

      let formData = new FormData();
      formData.append("userId", this.deleteUserId);

      axios.post('/api/afW/deleteFarmer',formData, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){

                          this.snackMessage = "Farmer Delete Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                          this.openFarmerDeleteDialog = false;
                          this.registrationLoading = false;
                          this.getAllFarmerDetails();
                        }else{
                          this.registrationLoading = false;
                          this.snackMessage = "Farmer Delete Error.";
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

    editProducerSocietyDetails(){
        this.registrationLoading = true;
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };

      let formData = new FormData();
      formData.append("userId", this.editUserId);
      formData.append("editName", this.editName);
      formData.append("editRegInsti", this.editRegInsti);
      formData.append("editRegNo", this.editRegNo);
      formData.append("editSecretary", this.editSecretary);
      formData.append("editTreasurer", this.editTreasurer);
      formData.append("editChairman", this.editChairman);

      axios.post('/api/asW/editProducerSocietyDetails',formData, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){

                          this.snackMessage = "Producer Society Edit Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                          this.openFarmerEditDialog = false;
                          this.registrationLoading = false;
                          this.getAllProducerSocietyDetails();
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
