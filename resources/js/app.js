
/**
 *  check ./bootstrap.js for all our dependencies and common stuff
 */

require('./services/bootstrap');
import store from './store/store';
import router from './routes';



/**
 * instantiate our console
 */

const app = new Vue({
    el: '#root',
    store,
    router
});