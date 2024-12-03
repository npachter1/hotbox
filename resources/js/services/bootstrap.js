import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';
import moment from 'moment';
import { Model } from 'vue-api-query';

window.Vue = Vue;
Vue.use(VueRouter);
window.axios = axios;
Model.$http = axios;


/* axios config */
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('access_token');
//window.axios.defaults.baseURL = process.env.MIX_VUE_APP_ROOT_API;

// this is the token to access all the backend endpoints should one need to print it.. 
//console.log(localStorage.getItem('access_token'));

/* 401 = authenticated, return to login page.. */
window.axios.interceptors.response.use(
    response => response,
    error => {
      const {status} = error.response;
      if(status === 401){                                                       // if we become unauthorized, simply redirect to the login page
        window.location = "/login";
      }
      return Promise.reject(error);
    }
);


/* other common DOM stuff.. */
window._ = require('lodash').default;
window.Popper = require('popper.js').default;


/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
else console.error('CSRF token not found');


/* from vue-pos 9/1/2019 */
import {directive as vClickOutside} from 'vue-clickaway';
Vue.directive('click-outside', vClickOutside);

Vue.config.productionTip = false;
Vue.prototype.moment = moment
Vue.prototype.renderDate = moment;


/* Then, we add Addl Bootstrap and global UI elements/directives */
import CommonUtil from './util.js';
Vue.use(CommonUtil);




/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */


import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');
  if(process.env.MIX_PUSHER_APP_KEY){
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: process.env.MIX_PUSHER_APP_KEY,
        // cluster: process.env.MIX_PUSHER_APP_CLUSTER,
        wsHost: window.location.hostname,
        wsPort: process.env.MIX_PUSHER_APP_SERVER_PORT,
        wssPort: process.env.MIX_PUSHER_APP_SERVER_PORT,
        disableStats: true,
        encrypted: process.env.MIX_FORCE_HTTPS === "true",
        enabledTransports: ['ws', 'wss']
    });
  }
