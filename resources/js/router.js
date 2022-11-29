import Vue from "vue";
import Router from "vue-router";

import Login from './components/Login';
import LayoutTemplate from "./components/layouts/LayoutTemplate";
import UrsHome from "./components/Frontend/UrsHome.vue";

import ASDashboard from "./components/ASDashboard.vue";

const routes = [
    {
        path: "/",
        component: UrsHome
    },
    {
        path: "/login",
        component: Login
    },
    {
        path: "/dashboard",
        component: LayoutTemplate
    },


    { path: '/as/dashboard', component: ASDashboard, name: 'MainDashboard'},
    { path: '/as/farmer-list', component: ASDashboard, name: 'ASFarmerList'},
    { path: '/as/producer-society-list', component: ASDashboard, name: 'ASProducerSocietyList'},


];

Vue.use(Router);

export default new Router({
    state: {
        userIsAuthorized: true,
    },
    mode: "history",
    routes
});
