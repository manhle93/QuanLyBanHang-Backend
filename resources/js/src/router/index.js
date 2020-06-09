import Vue from 'vue'
import VueRouter from 'vue-router'
import Customer from '../containers/customer/Customer.vue';
import Main from '../containers/main/Main.vue';
import customer from './customer';
Vue.use(VueRouter);
const routes = [
    {
        path: '/',
        component: Main,
        meta: {
            auth: true
        }
    },
    {
        path: '/customer',
        name: 'Customer',
        redirect: '/customer/login',
        component: Customer,
        children: customer,
    }
]
const router = new VueRouter({
    mode: 'history',
    routes
})
export default router