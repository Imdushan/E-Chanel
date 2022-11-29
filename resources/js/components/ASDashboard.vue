<template>
  <div>
    <v-navigation-drawer v-model="drawer" app clipped>
      <v-list dense>
        <v-list-group
          v-for="item in items"
          :key="item.title"
          v-model="item.active"
          :prepend-icon="item.action"
          no-action
        >
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title v-text="item.title"></v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item
            v-for="subItem in item.items"
            :key="subItem.subtitle"
            :to="{
              path: '/as/' + subItem.routePath,
              params: { name: subItem.routePath },
            }"
          >
            <v-list-item-content>
              <v-list-item-title v-text="subItem.subtitle"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app clipped-left color="blue" dense>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title class="headline" style="margin-right: 30px">
        <span class="title white--text"> Dream University dashboard </span>
      </v-toolbar-title>
      <v-spacer />
      <!-- <v-toolbar-title class="headline" style="margin-right: 30px">
        <span class="title white--text"> IDCB  </span>
      </v-toolbar-title> -->
      <v-menu left bottom>
        <template v-slot:activator="{ on }">
          <v-btn icon v-on="on">
            <v-icon class="white--text">mdi-account-circle</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item @click="dialogLogout = true">
            <v-list-item-title>
              <v-icon class="ma-2">mdi-logout</v-icon>Logout
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- ******************* logout user dialog ********************** -->

    <v-dialog v-model="dialogLogout" max-width="290">
      <v-card>
        <v-card-title class="headline">User Logout?</v-card-title>
        <v-card-text>Do you want to logout ?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" text @click="dialogLogout = false"
            >Cancel</v-btn
          >

          <v-btn color="green darken-1" text @click="isLogOut()">Logout</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-content>
      <DashboardView v-if="$route.name == 'ASDashboard'"></DashboardView>
      <FarmerList v-if="$route.name == 'ASFarmerList'"></FarmerList>
      <ProducerSocietyList v-if="$route.name == 'ASProducerSocietyList'"></ProducerSocietyList>

    </v-content>
  </div>
</template>

<script>
import DashboardView from "./AS/Home/DashboardView.vue";
import FarmerList from "./AS/Farmer/FarmerList.vue";
import ProducerSocietyList from "./AS/Farmer/ProducerSocietyList.vue";
export default {
  name: "ASDashboard",

  components:{
      DashboardView,
      FarmerList,
      ProducerSocietyList
  },

  data: () => ({
    showPassword: false,
    drawer: true,
    dialogLogout: false,
    items: [
      {
        action: "mdi-monitor-dashboard",
        title: "Home",
        active: true,
        items: [
          { subtitle: "Dashboard", routePath: "dashboard" },
        ],
        url: "",
      },

    ],
    dialog: false,
      dialogDelete: false,
      headers: [
        {
          text: 'Dessert (100g serving)',
          align: 'start',
          sortable: false,
          value: 'name',
        },
        { text: 'Calories', value: 'calories' },
        { text: 'Fat (g)', value: 'fat' },
        { text: 'Carbs (g)', value: 'carbs' },
        { text: 'Protein (g)', value: 'protein' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      desserts: [],
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
  methods: {
    isLogOut() {
      window.setLogout();
    },
  },
};
</script>
