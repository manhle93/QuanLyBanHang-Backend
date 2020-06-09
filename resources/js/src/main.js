// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
// import 'core-js/es6/promise'
// import 'core-js/es6/string'
// import 'core-js/es7/array'
// import cssVars from 'css-vars-ponyfill'
import Vue from 'vue'
// import BootstrapVue from 'bootstrap-vue'
import App from './App.vue'
import router from './router/index'
import VueSweetalert2 from 'vue-sweetalert2';
import Auth from './auth/Auth';
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

Vue.use(VueSweetalert2);
// import User from './/helpers/User'
// // main.js
// import VueSweetalert2 from 'vue-sweetalert2';
// Vue.use(VueSweetalert2);
window.Auth = Auth
// todo
// cssVars()

router.beforeEach((to, from, next) => {
    if (to.meta.auth === false) {
        if (Auth.loggedIn()) {
            next({
                path: '/'
            })
        }
        else next()
    }
    else if (to.meta.auth === true) {
        if (Auth.loggedIn()) {
            if (Auth.role() == 2) {
                if (localStorage.getItem('isSetup') == "true") next();
                else next({
                    path: '/customer/organization-information'
                })
            }
            else next()
        }
        else next({
            path: '/customer/login',
        })
    }
    else next()

})
// Vue.use(BootstrapVue)

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    render: h => h(App)
})