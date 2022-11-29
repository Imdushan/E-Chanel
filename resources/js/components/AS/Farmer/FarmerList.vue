<template>
  <div>
      <v-container>
        <v-row>
            <v-col cols="12" md="12">
                <p>Farmer / List</p>
            </v-col>
          <v-col cols="12" md="12">
            <v-data-table
              :headers="headers"
              :items="FarmerDetails"
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
                  <v-toolbar-title>Farmer List</v-toolbar-title>
                  <v-divider class="mx-4" inset vertical></v-divider>
                  <v-spacer></v-spacer>
                  <v-select
                  dense
                  outlined
                    :items="filterItems"
                    v-model="selectFilterItem"
                    label="Filter Areas"
                    required
                    clearable
                    prepend-inner-icon="mdi-filter"
                    @change="filterFarmerTable()"
                    ></v-select>
                </v-toolbar>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-btn color="green white--text" x-small @click="openEditFarmerUser(item)"> <v-icon small>mdi-account-edit</v-icon> &nbsp; Edit
                </v-btn>
                <v-btn color="orange white--text" x-small @click="openMoreInfoOfFarmer(item)"> <v-icon small>mdi-book-information-variant</v-icon> &nbsp; More Info
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
        v-model="openFarmerEditDialog"
        persistent
        max-width="800px"
        >
        <v-card>
            <v-card-title>
            <span class="text-h5">Farmer Update Form</span>
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
                    v-model="editName"
                    required
                    ></v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-text-field
                    label="NIC"
                    v-model="editNIC"
                    required
                    ></v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-select
                    :items="['male', 'female']"
                    v-model="editGender"
                    label="Gender"
                    required
                    ></v-select>
                </v-col>

                <v-col
                    cols="12"
                    sm="6"
                >
                    <v-text-field
                    label="Mobile"
                    v-model="editMobile"
                    required
                    ></v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="6"
                >
                    <v-select
                    :items="['married', 'unmarried']"
                    v-model="editCivilStatus"
                    label="Civil Status"
                    required
                    ></v-select>
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
                @click="editFarmerDetails()"
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

        <v-dialog
        v-model="openFarmerVerifyDialog"
        persistent
        max-width="800px"
        >
        <v-card>
            <v-card-title>
            <span class="text-h5">Farmer Verification Form</span>
            </v-card-title>
            <v-card-text>
            <v-container>
                <v-row>
                <v-col
                    cols="12"
                    sm="6"
                    md="12"
                >
                Farmer - {{verifyUserDetails}} <br>

                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="12"
                >
                <v-select
                    :items="decisionItems"
                    item-text="name"
                    item-value="value"
                    v-model="selectDecision"
                    label="Select Decision"
                    required
                    ></v-select>

                </v-col>

                <v-col
                    cols="12"
                    sm="6"
                    md="12"
                >
                <v-textarea
                    outlined
                    name="input-7-4"
                    label="Remark"
                    v-model="verifyRemark"
                    ></v-textarea>
                </v-col>

                </v-row>
            </v-container>
            </v-card-text>
            <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
                color="blue darken-1"
                class="white--text"
                @click="openFarmerVerifyDialog = false"
            >
                Close
            </v-btn>
            <v-btn
            :loading="registrationLoading"
                color="green darken-1"
                class="white--text"
                @click="verifyFarmerDetails()"
            >
                Get the decision
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
  name: "FarmerList",

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
    verifyUserDetails:"",
    verifyUserId:"",
    selectDecision:"",
    verifyRemark:"",
    selectFilterItem:"",
    filterItems:[
        "Verified Farmers",
        "Rejected Farmers",
        "Pending Farmers",
    ],
    openFarmerVerifyDialog:false,
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
        { text: 'NIC', value: 'nic' },
        { text: 'Gender', value: 'gender' },
        { text: 'Status', value: 'status' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      instituteBranchMultipleVal:[
        {
          branchName: "",
          branchDistrict: ""
        }
      ],
      afDistrict:"",
      afPassword:"",
      afEmail:"",
      afMobile:"",
      afName:"",
      registrationLoading:false,
      subInstituteDetails:[],
      decisionItems:[
        {
            name:"Verify",
            value:1
        },
        {
            name:"Reject",
            value:2
        },

      ],
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
      FarmerDetails:[],
      editGender:"",
      editCivilStatus:"",
      editMobile:"",
      editName:"",
      editNIC:"",
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
    this.getAllFarmerDetails();
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

    getAllFarmerDetails(){
        this.FarmerDetails = [];
        this.getAllDetailsLoading = true;
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };

      axios.post('/api/asW/getAllFarmerDetails', {userID:laravel.user.depend_dpd_id}, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){
                          this.FarmerDetails = response.data.data;
                          this.getAllDetailsLoading = false;
                          this.snackMessage = "Farmer Load Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                        }else{
                          this.getAllDetailsLoading = false;
                          this.snackMessage = "Farmer Load Error.";
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

    openEditFarmerUser(item){
        this.editUserId = item.id;
        this.editGender = item.gender;
      this.editCivilStatus = item.civil_status;
      this.editMobile = item.mobile;
      this.editName = item.name;
      this.editNIC = item.nic;
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

    openVerifyFarmerUser(item){
        this.verifyUserId = item.id;
        this.verifyUserDetails = item.name + " (" + item.nic+" )";
        this.openFarmerVerifyDialog = true;
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

      axios.post('/api/asW/deleteFarmer',formData, header)
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

    editFarmerDetails(){
        this.registrationLoading = true;
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };

      let formData = new FormData();
      formData.append("userId", this.editUserId);
      formData.append("name", this.editName);
      formData.append("mobile", this.editMobile);
      formData.append("gender", this.editGender);
      formData.append("civilStatus", this.editCivilStatus);
      formData.append("nic", this.editNIC);

      axios.post('/api/asW/editFarmerRegistration',formData, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){

                          this.snackMessage = "Farmer Edit Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                          this.openFarmerEditDialog = false;
                          this.registrationLoading = false;
                          this.getAllFarmerDetails();
                        }else{
                          this.registrationLoading = false;
                          this.snackMessage = "Farmer Edit Error.";
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

    verifyFarmerDetails(){
        this.registrationLoading = true;
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };

      let formData = new FormData();
      formData.append("userId", this.verifyUserId);
      formData.append("remarkBy", laravel.user.id);
      formData.append("selectDecision", this.selectDecision);
      formData.append("verifyRemark", this.verifyRemark);

      axios.post('/api/asW/verifyFarmer',formData, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){

                          this.snackMessage = "Farmer Verify Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                          this.openFarmerVerifyDialog = false;
                          this.registrationLoading = false;
                          this.getAllFarmerDetails();
                        }else{
                          this.registrationLoading = false;
                          this.snackMessage = "Farmer Verify Error.";
                          this.snackColor = "error";
                          this.openFarmerVerifyDialog = false;
                          this.snackbar = true;
                        }
                    }).catch((error) => {
                      this.registrationLoading = false;
                      this.snackMessage = error;
                      this.snackColor = "error";
                      this.openFarmerVerifyDialog = false;
                      this.snackbar = true;

                });
    },

    filterFarmerTable(){
        this.getAllDetailsLoading = true;
        this.FarmerDetails = [];
      let laravel = JSON.parse(window.Laravel);
      const header = {
                'X-CSRF-TOKEN': laravel.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'content-type': 'multipart/form-data',
      };

      let formData = new FormData();
      formData.append("selectFilterItem", this.selectFilterItem);
      formData.append("dependUserID", laravel.user.depend_dpd_id);

      axios.post('/api/asW/filterFarmers',formData, header)
                    .then(response => {
                        if (response.data.http_status == 'success'){
                          this.FarmerDetails = response.data.data;
                          this.snackMessage = "Farmer Load Successfully.";
                          this.snackColor = "success";
                          this.snackbar = true;
                          this.getAllDetailsLoading = false;
                        //   this.getAllFarmerDetails();
                        }else{
                          this.getAllDetailsLoading = false;
                          this.snackMessage = "Farmer Load Error.";
                          this.snackColor = "error";
                          this.snackbar = true;
                        }
                    }).catch((error) => {
                      this.getAllDetailsLoading = false;
                      this.snackMessage = error;
                      this.snackColor = "error";
                      this.snackbar = true;

                });
    }
  },
};
</script>
