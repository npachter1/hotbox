/* import common Utility Directives globally */


/* NEW BOOTSTRAP VUE (new esm bootstrap-vue) */
import {
  BModal,
  BForm,
  BTable,
  BPagination,
  BProgress,
  BTabs,
  BTab,
  VBPopover,
  VBTooltip,
  BFormCheckbox,
  BFormCheckboxGroup
} from 'bootstrap-vue';

import VueChartist from 'vue-chartist';                                         // <chartist> Lightweight charts component | https://gionkunz.github.io/chartist-js/examples.html
import VueGoogleCharts from 'vue-google-charts';                                // <GChart> Advanced chart api from Google | https://developers.google.com/chart/interactive/docs/


/* ui utilities */
import { directive as vClickOutside } from 'vue-clickaway';
import VeeValidate from 'vee-validate';                                         // vue form validation, including backend "errors" response | 
import VueMoment from 'vue-moment';                                             // time display (local to users computer)
import Datepicker from 'vuejs-datepicker';                                      // simple bootstrap datepicker
import Multiselect from 'vue-multiselect';                                      // form selection library
import VueSweetalert2 from 'vue-sweetalert2';                                   // simple action confirmations for the UI from SweetAlert.js!
import VueBarcodeScanner from './../../plugins/barcode-scanner/index';

import Notify from "vue-notifyjs";                                              // <notifications> native vue notifications | https://www.npmjs.com/package/vue-notification
import Announcer from './announce';                                             // announcement notifications plugin
import Filters from './filters';                                                // custom filters

import TimePicker from 'vue-timepicker';                                         // TimePicker Component | https://www.npmjs.com/package/vue-timepicker 
import VueBarcode from '@chenfengyuan/vue-barcode';
import LoadScript from 'vue-plugin-load-script';
import NotificationsDropdown from '../components/elements/NotificationsDropdown';
import VueTimeago from 'vue-timeago';
import vue2Dropzone from 'vue2-dropzone';


/**
 * App UI Library Registration
 */

const CommonUtil = {
  install (Vue) {
    
    Vue.component('b-form', BForm);
    Vue.component('b-modal', BModal);
    Vue.component('b-table', BTable);
    Vue.component('b-pagination', BPagination);
    Vue.component('b-progress', BProgress);
    Vue.component('b-tabs', BTabs);
    Vue.component('b-tab', BTab);
    Vue.component('b-checkbox', BFormCheckbox);
    Vue.component('b-checkbox-group', BFormCheckboxGroup);
    Vue.directive('b-popover', VBPopover);
    Vue.directive('b-tooltip', VBPopover);

    Vue.use(VueChartist);
    Vue.use(VueGoogleCharts);

    Vue.directive('click-outside', vClickOutside);
    Vue.use(VeeValidate,{fieldsBagName: 'formFields',mode:'eager'});
    Vue.use(VueMoment);
    Vue.component(VueBarcode.name, VueBarcode);
    Vue.use(LoadScript);
    Vue.component('notifications-dropdown', NotificationsDropdown);
    Vue.component('Datepicker',Datepicker);
    Vue.component('Multiselect', Multiselect);
    Vue.component('TimePicker', TimePicker);
    Vue.use(VueSweetalert2,{confirmButtonColor:'#3c8dbc',cancelButtonColor: '#ccc'});

    Vue.use(Notify,{horizontalAlign: 'right',verticalAlign: 'top',timeout: 6000,showClose:  true,closeOnClick: true});
    Vue.use(Announcer);
    Vue.use(Filters);
    Vue.use(VueBarcodeScanner, { callbackAfterTimeout: true, sensitivity: 200 });

    Vue.use(VueTimeago, { name: 'Timeago', locale: 'en'});
    Vue.component('vueDropzone', vue2Dropzone);


    /* Load common elements */
    Vue.component('Loading', require('../components/elements/Loading').default);
    Vue.component('AutoSave', require('../components/elements/AutoSave').default);
    Vue.component('FilterMore', require('../components/elements/FilterMore').default);
    Vue.component('FilterMoreColumns', require('../components/elements/FilterMoreColumns').default);
    Vue.component('FilterIn', require('../components/elements/FilterIn').default);
    Vue.component('FilterTabs', require('../components/elements/FilterTabs').default);
    Vue.component('FilterDate', require('../components/elements/FilterDate').default);
    Vue.component('FormBoolean', require('../components/elements/FormBoolean').default);
    Vue.component('FormButton', require('../components/elements/FormButton').default);
    Vue.component('FormText', require('../components/elements/FormText').default);
    Vue.component('FormNumber', require('../components/elements/FormNumber').default);
    Vue.component('FormPassword', require('../components/elements/FormPassword').default);
    Vue.component('PriceTable', require('../components/elements/PriceTable').default);
    Vue.component('FormTextarea', require('../components/elements/FormTextarea').default);
    Vue.component('FormSelect', require('../components/elements/FormSelect').default);
    Vue.component('FormMultiselect', require('../components/elements/FormMultiselect').default);
    Vue.component('FormSimpleselect', require('../components/elements/FormSimpleselect').default);
    Vue.component('FormChecklist', require('../components/elements/FormChecklist').default);
    Vue.component('FormDatetime',require('../components/elements/FormDatetime.vue').default);
    Vue.component('FormTimepicker',require('../components/elements/FormTimepicker.vue').default);
    Vue.component('FormFile',require('../components/elements/FormFile.vue').default);
    Vue.component('FormList',require('../components/elements/FormList.vue').default);
    Vue.component('FormSectionalobject',require('../components/elements/FormSectionalobject.vue').default);
    Vue.component('FormSimpleobject',require('../components/elements/FormSimpleobject.vue').default);
    Vue.component('FormSearch',require('../components/elements/FormSearch.vue').default);
    Vue.component('AggData',require('../components/elements/AggData.vue').default);
    Vue.component('Spinner', require('../components/elements/Spinner').default);

  }
};

export default CommonUtil;