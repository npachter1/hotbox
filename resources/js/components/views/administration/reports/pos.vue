<template>
    <div v-if="schema" class="col-12">
        <form>
            <div class="table-filter-row">
                <div style="position: absolute;font-size: 1.57em;line-height: 1.4em;">
                    <i class="hotbox-icon hotbox-icon-notes"></i>
                    Close Out Report
                </div>
                <div v-if="gridFilters" class="filters w-100">

                    <filter-more v-if="gridData"
                                 :meta="gridData.meta"
                                 :schema="schema"
                                 :gridFilters.sync="gridFilters"
                                 :columns.sync="gridColumns"
                                 :isDownloading="isDownloading"
                                 @downloadExport="()=>{$refs.csvLink.click();}">
                    </filter-more>

                    <filter-in v-for="(filt,fkey) in schema.filters" v-if="filt.type=='wherein'"
                               :key="fkey"
                               :schema="filt"
                               :filter="gridFilters.filter[fkey]"
                               @update="(upd) => { gridFilters.filter[fkey] = upd; }">
                    </filter-in>


                    <a ref="csvLink"
                       :download="$moment(gridFilters.filter.created_at[0]).format('YYYY-MM-DD') + '_zreport.csv'"
                       :href="csv()" style="display:none;">csv</a>

                    <div class="filter-wherein">
                        <div class="float-left">
                            Date
                            <datepicker
                                    :value="getDateStringAtLocation(gridFilters.filter.created_at[0])"
                                    @input="processStartDate"
                                    name="Day"
                                    :format="'M/d/yyyy'"
                                    :typeable="true"
                                    :bootstrap-styling="false"
                                    :input-class="{'form-control':false,'showdate':true}"
                                    :calendar-button-icon="'ti-calendar'">
                            </datepicker>

                            <div v-if="!showFullTimeOptions" style="display: inline-block;">
                                <i class="clock hotbox-icon hotbox-icon-clock" @click="customTime=true"></i>
                            </div>
                            <div v-else style="display: inline-block;">
                                <form-timepicker v-model="startTime" :hideLabel="true" placeholder="" :schema="{name:'',title:'',description:''}"/>
                                -
                                <datepicker
                                        :value="getDateStringAtLocation(gridFilters.filter.created_at[1])"
                                        @input="processEndDate"
                                        name="Day"
                                        :format="'M/d/yyyy'"
                                        :typeable="true"
                                        :bootstrap-styling="false"
                                        :input-class="{'form-control':false,'showdate':true}"
                                        :calendar-button-icon="'ti-calendar'">
                                </datepicker>
                                <form-timepicker v-model="endTime" :hideLabel="true" placeholder="" :schema="{name:'',title:'',description:''}"/>
                            </div>
                        </div>
                    </div>

                    <filter-tabs :schema="schema.filters.drawers"
                                 :filter="gridFilters.filter.drawers"
                                 @update="(upd) => {gridFilters.filter.drawers = upd}">
                    </filter-tabs>
                </div>

            </div>
        </form>

        <loading :display="isLoading" type="loadGrid"/>
        <transition name="bo-slide">
            <b-table
                    :items="reportData"
                    :fields="columnsVisible.map(e=>Object.assign({},e,{label:e.category ? e.label.replace(e.category + ' ','') : e.label}))"

                    :tbodyTrClass="(item,type) => {
                    let trClass = {hidden: false,rowDrawer:false,rowLocation:false};
                    if (type==='row') {
                        if (item.collapse) trClass.hidden=true;
                        if (item.drawerName) trClass.rowDrawer=true;
                        else trClass.rowLocation=true;
                    }
                    return trClass;
                    }">

                <template v-slot:thead-top="data">
                    <b-tr class="table-header" style="background-color: rgba(41, 84, 132, .9);">
                        <b-th v-for="(header,index) in headers"
                              :key="header.category + header.count"
                              :colspan="header.count">
                            <span v-if="header.category">{{ header.category }}</span>
                        </b-th>
                    </b-tr>
                </template>

                <template v-slot:cell(drawerName)="data">
                    <span v-if="data.item.drawerName===null">
                        {{ locationName(data.item.locationId) }}
                    </span>
                    <span v-else>
                        {{ data.item.drawerName }}
                    </span>
                </template>

                <template v-slot:cell(drawerUser)="data">
                    <span v-if="data.item.drawerName===null">
                        {{ locationName(data.item.locationId) }}
                    </span>
                    <span v-else :class="{ 'drawer-not-settled' : !data.item.drawerSettled }">
                        {{ data.item.drawerUser }}
                    </span>
                </template>

                <template v-slot:cell()="data">
                    <div v-if="['transactionCount','transactionCountRetail','transactionCountWholesale'].includes(data.field.key)" class="number">{{ data.value }}</div>
                    <div v-else-if="['openTime','closeTime','openDate','closeDate'].includes(data.field.key)">{{ data.value }}</div>
                    <div v-else class="money">{{ data.value | formatMoney }}</div>
                </template>

            </b-table>
        </transition>

    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage"/>
    </div>
</template>

<script>

    import Grid from '../../../../models/Sale';
    import Drawer from '../../../../models/Drawer';
    import Locations from '../../../../models/Locations';

    import _ from 'lodash';
    import Big from 'big.js'; //for money calcs
    import {BTh, BTr} from 'bootstrap-vue'; //for extra header at top of table
    import {convertArrayToCSV} from 'convert-array-to-csv';
    //const converter = require('convert-array-to-csv');
    import momentTz from 'moment-timezone';

    function difference(object, base) { //deep diff two object https://gist.github.com/Yimiprod/7ee176597fef230d1451
        function changes(object, base) {
            return _.transform(object, function (result, value, key) {
                if (!_.isEqual(value, base[key])) {
                    result[key] = (_.isObject(value) && _.isObject(base[key])) ? changes(value, base[key]) : value;
                }
            });
        }

        return changes(object, base);
    }

    export default {
        props: {
            module: {
                type: String,
                default: 'pos', //for now I use a valid module but I am building my schema and filters in code
            },
            model: {
                type: String,
                default: 'drawer' //for now I use a valid module but I am building my schema and filters in code
            },
            filters: {  // optional initial filters (filters.filter) object can be passed via this prop!
                type: Object,
                default: () => {
                }
            }
        },

        data() {
            return {
                browserTimeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                customTime: false,

                startTime: 0,
                endTime: 2359,
                startDate: null, //storing string date value YYYYMMDD to track via computed prop or watcher as moment is immutable and hard for vue to track
                endDate: null, //storing string date value YYYYMMDD to track via computed prop or watcher as moment is immutable and hard for vue to track

                // timeZoneName: 'America/Denver', //pull from location(s) TZ name https://en.wikipedia.org/wiki/List_of_tz_database_time_zones

                isLoading: false,
                isDownloading: false,
                shouldReload: false,
                gridData: null,
                gridSearch: null,
                gridPage: 1,
                gridColumns: [
                    // @formatter:off
                    {key: 'drawerUser', category: '', label: 'Drawer User', sortable: false, toggle: true},
                    {key: 'drawerName', category: '', label: 'Drawer ID', sortable: false, toggle: false},
                    {key: 'taxCategory.medicated.price', category: 'Tax Standard', label: 'Tax Standard Sales', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'taxCategory.medicated.tax', category: 'Tax Standard', label: 'Tax Standard Tax', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'taxCategory.unmedicated.price', category: 'Tax Unmedicated', label: 'Tax Unmedicated Sales', sortable: false, toggle: true, },
                    {key: 'taxCategory.unmedicated.tax', category: 'Tax Unmedicated', label: 'Tax Unmedicated Tax', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'cash.price', category: 'Cash', label: 'Cash Sales', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'cash.tax', category: 'Cash', label: 'Cash Tax', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'cash.sale_price', category: 'Cash', label: 'Cash Total', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'credit.price', category: 'Credit', label: 'Credit Sales', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'credit.tax', category: 'Credit', label: 'Credit Tax', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'credit.sale_price', category: 'Credit', label: 'Credit Total', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'giftCard.price', category: 'Gift', label: 'Gift Sales', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'giftCard.tax', category: 'Gift', label: 'Gift Tax', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'giftCard.sale_price', category: 'Gift', label: 'Gift Total', sortable: false, toggle: true, tab: 'sales'},
                    // {key: 'account.price', category: 'Account', label: 'Account Sales', sortable: false, toggle: true, tab: 'sales'},
                    // {key: 'account.tax', category: 'Account', label: 'Account Tax', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'account.sale_price', category: 'Account', label: 'Account Total', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'all.price', category: 'All', label: 'All Sales', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'all.tax', category: 'All', label: 'All Tax', sortable: false, toggle: true, tab: 'sales'},
                    {key: 'all.sale_price', category: 'All', label: 'All Total', sortable: false, toggle: true, tab: 'sales'},

                    {key: 'transactionCountRetail', category: 'Transactions', label: 'Retail', sortable: false, toggle: true, tab: 'drawers'},
                    {key: 'transactionCountWholesale', category: 'Transactions', label: 'Wholesale', sortable: false, toggle: true, tab: 'drawers'},

                    {key: 'openDate', category: 'Open', label: 'Date', sortable: false, toggle: false, tab: ''}, //only show if multi-day report
                    {key: 'openTime', category: 'Time', label: 'Open', sortable: false, toggle: true, tab: 'drawers'},

                    {key: 'closeDate', category: 'Close', label: 'Date', sortable: false, toggle: false, tab: ''}, //only show if multi-day report
                    {key: 'closeTime', category: 'Time', label: 'Close', sortable: false, toggle: true, tab: 'drawers'},

                    {key: 'drawerOpen', category: 'Drawer', label: 'Cash Opening', sortable: false, toggle: true, tab: 'drawers'},
                    {key: 'drawerCashIn', category: 'Drawer', label: 'Cash In', sortable: false, toggle: true, tab: 'drawers'},
                    {key: 'drawerCashOut', category: 'Drawer', label: 'Cash Out', sortable: false, toggle: true, tab: 'drawers'},
                    {key: 'drawerCashExpected', category: 'Drawer', label: 'Expected', sortable: false, toggle: true, tab: 'drawers'},
                    {key: 'drawerCashCounted', category: 'Drawer', label: 'Counted', sortable: false, toggle: true, tab: 'drawers'},
                    {key: 'drawerDifference', category: 'Drawer', label: 'Difference', sortable: false, toggle: true, tab: 'drawers'},
                    {key: 'drawerDeposit', category: 'Drawer', label: 'Deposit', sortable: false, toggle: true, tab: 'drawers'},
                    // @formatter:on
                ],
                // gridFilters: {
                //     filter: {
                //         created_at: [],
                //     }
                // },
                gridFilters: null,
                reportData: null,
                drawerData: null,
                salesData: null,
                drawerUsers: {},
                drawerDetails: null,
                locationData: null,
            };
        },

        components: {
            BTh,
            BTr,
        },

        mounted() {
            this.$store.dispatch(this.module + '/setSchemas', this.model.toLowerCase()); // get schema for new agg data

            //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
            //this.gridSearch = this.$route.query.search || null;

            if (this.schema) {
                this.setFilters(this.$route.params.focus);                      // if we have schema, then set filters, else we watch schema load/change and then set.
                // this.gridColumns = this.schema.meta.fields;                     // for some reason, the schema changing on edit doesnt register - need to reload upon mount
            }
            this.fetchData();
        },

        methods: {
            processStartDate(val) {
                    //val comes in as date object in browser time zone.  Format it as string (so we can ignore the tz),
                    // then convert into moment in location tz, then convert to utc
                    //e.g., dispensary in denver but browser is in atlanta and datetime is midnight, this returns val as midnight atlanta
                    // so format string to get midnight, convert to moment object denver, then convert that to utc
                    let sVal = this.$moment(val).format('YYYY-MM-DD 00:00:00.000');
                    this.gridFilters.filter.created_at[0] = this.getUtcMomentAtLocation(sVal,this.timeZoneName(this.$store.state.user.location.id));

                    if (!this.showFullTimeOptions) { //set start & end to same
                        this.startDate = this.formatMomentToDateString(this.gridFilters.filter.created_at[0]);
                        sVal = this.$moment(val).format('YYYY-MM-DD 23:59:59.999');
                        this.gridFilters.filter.created_at[1] = this.getUtcMomentAtLocation(sVal,this.timeZoneName(this.$store.state.user.location.id));
                        this.endDate = this.formatMomentToDateString(this.gridFilters.filter.created_at[1]);
                    } else {
                        this.startDate = this.formatMomentToDateString(this.gridFilters.filter.created_at[0]);
                    }
                    this.fetchData();
            },

            processEndDate(val) {
                let sVal = this.$moment(val).format('YYYY-MM-DD 23:59:59.999');
                this.gridFilters.filter.created_at[1] = this.getUtcMomentAtLocation(sVal,this.timeZoneName(this.$store.state.user.location.id));
                this.endDate = this.formatMomentToDateString(this.gridFilters.filter.created_at[1]);
                this.fetchData();
            },

            getUtcMomentAtLocation(s,tzName) {
                //convert string time into a moment object for the location tz, then convert into UTC
                //this is if browser is in different tz than dispensary location
                return momentTz.tz(s,tzName).utc();
            },

            getDateStringAtLocation(s) {
                const u = momentTz(s);
                return u.tz(this.timeZoneName(this.$store.state.user.location.id)).format('YYYY-MM-DD HH:mm');
            },

            async fetchData() {
                if (this.startTime===0 && this.endTime===2359 && this.sameDate) this.customTime=false;

                Promise.all([this.fetchDrawer(), this.fetchSales(), this.fetchLocations()])
                    .then(() => {
                        this.aggregateAllData();
                    });
            },

            async fetchLocations() {
                if (!this.locationData) { //only get this the first go around.
                    let locationResults = await new Locations()
                        .get();
                    this.locationData = locationResults.data.reduce((obj, item) => (obj[item.id] = {tz: item.settings.communication_timezone}, obj) ,{});
                }
                return null;
            },

            async fetchDrawerDetails() {
                this.drawerDetails = null;
                let drawerIds = this.drawerData.data.map(drawer => drawer.id);
                let drawerDetails = await Promise.all(drawerIds.map(async (drawerId) => {
                    return await Drawer.find(drawerId);
                }));
                this.drawerDetails = drawerDetails;
                return null;
            },

            async fetchDrawer() {
                this.drawerData = null;

                let filters = this.reformatFilters(_.cloneDeep(this.gridFilters.filter));

                let drawerData = await new Drawer()
                    .setFilters(filters)
                    .limit(9999)
                    .get();
                this.drawerData = drawerData;

                return this.fetchDrawerDetails(); //we now have drawers for this filter/time period, get drawer events for each one.
            },

            async fetchSales() {
                this.salesData = null;

                if (!this.schema || !this.gridFilters) return false; // dont fetch grid without a schema or the filters loaded, this will be trigered when they load
                else if (this.isLoading) return false; // do not fetch if we are already fetching

                let filters = this.transformCreatedSettled(this.reformatFilters(_.cloneDeep(this.gridFilters.filter)));

                this.isLoading = true;
                this.gridData = await new Grid()
                    .setFilters(filters)
                    .limit(9999)
                    .get();
                this.isLoading = false;

                this.salesData = this.gridData.data.map(e => {
                    const price = new Big(e.price ? e.price : 0);
                    const discount = new Big(e.discount ? e.discount : 0);

                    return {
                        location_id: e.location_id,
                        drawerId: e.drawer_id,
                        drawer_name: e.drawer.name,
                        drawer_user: ((e.drawer.user||{}).name||e.drawer.name),
                        customer_type: e.customer.type,
                        price: price.minus(discount),
                        discount: new Big(e.discount ? e.discount : 0),
                        discount_code: e.discount_code,
                        tax: new Big(e.tax ? e.tax : 0),
                        sale_price: new Big(e.sale_price ? e.sale_price : 0),
                        status: e.status,
                        tax_category: (e.thc_equivalent_grams || 0) > 0 ? 'medicated' : 'unmedicated',
                        created_at: e.created_at,
                        payment_method: (Array.isArray(e.payments) && e.payments.length > 0) ? e.payments[0].payment_method : "cash",
                    };
                });

                return null;
            },

            timeIntToString(i) { //i should be 0 to 2359
                if (i===2400) i=2359;
                let s = i.toString().padStart(4,'0');
                return s.substring(0,2) + ':' + s.substring(2);
            },

            dateTimeStringToUtc(dateTimeString, tzName) {
                //tzName 'America/Denver'
                // dateTimeString format '2020-02-12 01:30'
                // return something like this: 2020-02-13T06:59:59.999Z
                return momentTz.tz(dateTimeString,tzName).utc().toISOString();
            },

            transformCreatedSettled(filters) {
                let r = Object.assign({}, filters, {settled_at: filters.created_at });
                delete r.created_at;
                return r;
            },

            reformatFilters(filters) {
                let tz = this.timeZoneName(this.$store.state.user.location.id);
                let formattedDateStart =momentTz(filters.created_at[0]).tz(tz).format('YYYY-MM-DD'); //make sure momentTz object so we can apply correct time zone of location
                let formattedDateEnd =momentTz(filters.created_at[1]).tz(tz).format('YYYY-MM-DD');
                //let filters = _.cloneDeep(this.gridFilters.filter);

                //we'll use the user's location time zone as there may be multiple locations with different tz.  Not sure how to deal with it...
                filters.created_at[0] = this.dateTimeStringToUtc(formattedDateStart + ' ' + this.timeIntToString(this.startTime) + ':00.000', tz);
                filters.created_at[1] = this.dateTimeStringToUtc(formattedDateEnd + ' ' + this.timeIntToString(this.endTime) + ':59.999', tz);

                return filters;
            },

            aggregateTaxCategories(taxCategory, reportData) { //customerType
                const seed = { //zero value to start the counting in reduce
                    price: new Big(0),
                    tax: new Big(0),
                    sale_price: new Big(0),
                    transactionCount: 0,
                };

                return reportData.reduce((a, b) => {
                    if (b.tax_category === taxCategory) {
                        let price;
                        if (b.status==='settled') price=b.price;
                        else if (b.status==='refunded') price=b.sale_price.abs().minus(b.tax.abs()).times(-1); //the "price" needs to be disregarded here and calculated based on abs(sale_price)-abs(tax)
                        else return a;

                        return {
                            price: a.price.plus(price),
                            tax: a.tax.plus(b.tax),
                            sale_price: a.sale_price.plus(b.sale_price),
                            transactionCount: a.transactionCount + 1,
                        };
                    }
                    return a;
                }, seed);
            },

            aggregatePurchases(paymentMethod, reportData) { //customerType
                if (paymentMethod === 'giftCard') paymentMethod = 'gift card';

                const seed = { //zero value to start the counting in reduce
                    price: new Big(0),
                    //discount:new Big(0),
                    tax: new Big(0),
                    sale_price: new Big(0),
                    transactionCount: 0,
                };

                return reportData.reduce((a, b) => {
                    if (b.payment_method === paymentMethod) {
                        //if (customerType==='all' || b.customer_type===customerType) {

                        let price;
                        if (b.status==='settled') price=b.price;
                        else if (b.status==='refunded') price=b.sale_price.abs().minus(b.tax.abs()).times(-1); //the "price" needs to be disregarded here and calculated based on abs(sale_price)-abs(tax)
                        else return a;

                        return {
                            price: a.price.plus(price),
                            //discount: a.discount.plus(b.discount),
                            tax: a.tax.plus(b.tax),
                            sale_price: a.sale_price.plus(b.sale_price),
                            transactionCount: a.transactionCount + 1,
                        };
                    }
                    return a;
                }, seed);
            },

            aggregateDrawers(drawerName, paymentMethods, taxCategories, groupedDrawerReportData) {
                const drawerId = groupedDrawerReportData[drawerName][0].drawerId; //get drawerId so we can later relate it to Drawer data

                let drawerData = this.drawerDetails.find(e => e.id === drawerId);

                let paymentMethodData = {};
                paymentMethods.all.forEach(paymentMethod => {
                    paymentMethodData[paymentMethod] = this.aggregatePurchases(paymentMethod, groupedDrawerReportData[drawerName]);
                });

                let taxCategoryData = {};
                taxCategories.forEach(taxCategory => {
                    taxCategoryData[taxCategory] = this.aggregateTaxCategories(taxCategory, groupedDrawerReportData[drawerName]);
                });

                if (drawerData === undefined) drawerData = { //no drawer data for this date???
                    current_balance: 0,
                    opening_balance: 0,
                    total_sale_price: 0,
                    closing_balance: 0,
                    events: [],
                    created_at: null,
                    closed_at: null,
                };

                let cashOpen = new Big(new Big(drawerData.opening_balance || 0).toFixed(2));
                let cashExpected = new Big(new Big(drawerData.current_balance || 0).toFixed(2));
                let cashCounted = new Big(new Big(drawerData.closing_balance || 0).toFixed(2));

                let drawer = {
                    cash: paymentMethodData.cash,
                    credit: paymentMethodData.credit,
                    giftCard: paymentMethodData.giftCard,
                    account: paymentMethodData.account,
                    drawerOpen: cashOpen,
                    drawerCashIn: new Big(new Big(drawerData.events.filter(e => e.event_type === 'pay-in').map(e => e.total || 0).reduce((a, b) => { return a + b}, 0)).toFixed(2)),
                    drawerCashOut: new Big(new Big(drawerData.events.filter(e => e.event_type === 'pay-out').map(e => e.total || 0).reduce((a, b) => { return a + b }, 0)).toFixed(2)),
                    drawerCashExpected: cashExpected,
                    drawerCashCounted: cashCounted,
                    drawerDifference: cashCounted.minus(cashExpected),
                    drawerDeposit: cashCounted.minus(cashOpen),
                    taxCategory: {
                        medicated: taxCategoryData.medicated,
                        unmedicated: taxCategoryData.unmedicated,
                    },
                    openDate: drawerData.created_at ? momentTz.utc(drawerData.created_at).clone().tz(this.timeZoneName(drawerData.locationId)).format('M/D/YY') : '', // https://stackoverflow.com/a/43527200
                    openTime: drawerData.created_at ? momentTz.utc(drawerData.created_at).clone().tz(this.timeZoneName(drawerData.locationId)).format('h:mma') : '', // https://stackoverflow.com/a/43527200

                    closeDate: drawerData.closed_at ? momentTz.utc(drawerData.closed_at).clone().tz(this.timeZoneName(drawerData.locationId)).format('M/D/YY') : '',
                    closeTime: drawerData.closed_at ? momentTz.utc(drawerData.closed_at).clone().tz(this.timeZoneName(drawerData.locationId)).format('h:mma') : '',
                };

                drawer.drawerSettled = (!!drawer.closeTime && !!drawer.openTime && !!drawer.closeDate && !!drawer.openDate);

                if (!drawer.drawerSettled) {
                    drawer.drawerDifference=new Big(0);
                    drawer.drawerDeposit=new Big(0);
                }

                return drawer;
            },

            loadDrawerUsers() {
                if (!this.salesData) return;

                this.salesData.forEach(e => {
                    if (!this.drawerUsers.hasOwnProperty(e.drawer_name)) this.drawerUsers[e.drawer_name] = e.drawer_user;
                });
            },

            aggregateAllData() {
                this.loadDrawerUsers(); //uses sales data to reconstruct the name of the person assigned to each drawer

                let groupedLocationReportData = [];
                // if (this.gridFilters.filter.location_id.length > 0) groupedLocationReportData = _.groupBy(this.salesData.filter(e => {
                //     return this.gridFilters.filter.location_id.indexOf(e.location_id) > -1
                // }), 'location_id');
                groupedLocationReportData = _.groupBy(this.salesData, 'location_id');

                let paymentMethods = {
                    all: ['cash', 'credit', 'giftCard', 'account'],
                    retail: ['cash', 'credit', 'giftCard'],
                    wholesale: ['account']
                };

                let taxCategories = ['medicated', 'unmedicated'];

                let aggregateLocationReportData = {};
                let aggregateDrawerReportData = {};
                let aggregateReportData = [];

                Object.keys(groupedLocationReportData).forEach(locationId => {
                    aggregateLocationReportData[locationId] = {};

                    paymentMethods.all.forEach(paymentMethod => {
                        aggregateLocationReportData[locationId][paymentMethod] = this.aggregatePurchases(paymentMethod, groupedLocationReportData[locationId]);
                    });

                    aggregateLocationReportData[locationId].taxCategory = {};
                    taxCategories.forEach(taxCategory => {
                        aggregateLocationReportData[locationId].taxCategory[taxCategory] = this.aggregateTaxCategories(taxCategory, groupedLocationReportData[locationId]);
                    });

                    let groupedDrawerReportData = _.groupBy(groupedLocationReportData[locationId], 'drawer_name');
                    aggregateDrawerReportData[locationId]={};
                    Object.keys(groupedDrawerReportData).forEach(drawerName => {
                        aggregateDrawerReportData[locationId][drawerName] = this.aggregateDrawers(drawerName, paymentMethods, taxCategories, groupedDrawerReportData);
                    });

                    aggregateReportData.push(Object.assign({},aggregateLocationReportData[locationId],{
                        locationId,
                        drawerName: null,
                        drawerId: null,
                        drawerUser: null,
                        all: this.combinePaymentTypes(aggregateLocationReportData[locationId], paymentMethods),
                        drawerOpen: new Big(0),
                        drawerCashIn: new Big(0),
                        drawerCashOut: new Big(0),
                        drawerCashExpected: new Big(0),
                        drawerCashCounted: new Big(0),
                        drawerDifference:Object.keys(aggregateDrawerReportData[locationId]).map(e=>aggregateDrawerReportData[locationId][e].drawerDifference).reduce((a,b)=>a.plus(b),new Big(0)),
                        drawerDeposit:Object.keys(aggregateDrawerReportData[locationId]).map(e=>aggregateDrawerReportData[locationId][e].drawerDeposit).reduce((a,b)=>a.plus(b),new Big(0)),
                        drawerOpenTime: null,
                        drawerCloseTime: null,
                        transactionCount: paymentMethods.all.map(paymentMethod => aggregateLocationReportData[locationId][paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                        transactionCountRetail: paymentMethods.retail.map(paymentMethod => aggregateLocationReportData[locationId][paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                        transactionCountWholesale: paymentMethods.wholesale.map(paymentMethod => aggregateLocationReportData[locationId][paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                        openDate: null,
                        openTime: null,
                        closeDate: null,
                        closeTime: null,
                        collapse: false,
                        drawerSettled: true,
                    }));

                    //drawers
                    Object.keys(groupedDrawerReportData).forEach(drawerName => {
                        const drawerId = groupedDrawerReportData[drawerName][0].drawerId; //get drawerId so we can later relate it to Drawer data
                        aggregateReportData.push(Object.assign({},aggregateDrawerReportData[locationId][drawerName],{
                            locationId,
                            drawerName,
                            drawerId,
                            drawerUser: this.drawerUsers[drawerName],
                            all: this.combinePaymentTypes(aggregateDrawerReportData[locationId][drawerName], paymentMethods),
                            collapse: false,
                            transactionCount: paymentMethods.all.map(paymentMethod => aggregateDrawerReportData[locationId][drawerName][paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                            transactionCountRetail: paymentMethods.retail.map(paymentMethod => aggregateDrawerReportData[locationId][drawerName][paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                            transactionCountWholesale: paymentMethods.wholesale.map(paymentMethod => aggregateDrawerReportData[locationId][drawerName][paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                        }));
                    });

                });

                aggregateReportData.sort((a, b) => {
                    //location primary sort
                    if (a.locationId < b.locationId) return -1;
                    if (a.locationId > b.locationId) return 1;

                    //drawer secondary sort
                    if (!a.drawerName && !!b.drawerName) return 1; //sort no drawer name (so it is a aggregate row) last
                    if (!!a.drawerName && !b.drawerName) return -1;

                    const drawer1 = (a.drawerName || '').toLowerCase();
                    const drawer2 = (b.drawerName || '').toLowerCase();
                    if (drawer1 < drawer2) return -1;
                    if (drawer1 > drawer2) return 1;

                    return 0;
                });

                this.reportData = aggregateReportData;
            },

            combinePaymentTypes(aggregated, paymentMethods) {//aggregated is {cash, giftCard, credit}
                return {
                    tax: paymentMethods.all.map(paymentMethod => aggregated[paymentMethod].tax).reduce((a, b) => a.plus(b), new Big(0)),
                    sale_price: paymentMethods.all.map(paymentMethod => aggregated[paymentMethod].sale_price).reduce((a, b) => a.plus(b), new Big(0)),
                    price: paymentMethods.all.map(paymentMethod => aggregated[paymentMethod].price).reduce((a, b) => a.plus(b), new Big(0)),
                    transactionCount: paymentMethods.all.map(paymentMethod => aggregated[paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                    transactionCountRetail: paymentMethods.retail.map(paymentMethod => aggregated[paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                    transactionCountWholesale: paymentMethods.wholesale.map(paymentMethod => aggregated[paymentMethod].transactionCount).reduce((a, b) => a + b, 0),
                }
            },

            setFilters(focus = 'all') {
                if (!this.schema) return false;

                this.gridFilters = {                                            // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 20,
                    sortBy: Object.keys(this.schema.filters).find(key => this.schema.filters[key].type === 'daterange') || 'rank', // use first daterange filter field(key) in schema
                    orderDesc: true,
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => {
                        if (this.schema.filters[k].type === 'status' || k === 'created_at') return {[k]: this.schema.filters[k].values};
                        if (k === 'location_id') return {[k]: this.schema.filters[k].values.map(e => e.id)};
                        if (k === 'drawers') return {[k]: ['sales']};
                        return {[k]: ['all']};
                    }), this.filters)
                };

                this.startDate = this.formatMomentToDateString(this.gridFilters.filter.created_at[0]);
                this.endDate = this.formatMomentToDateString(this.gridFilters.filter.created_at[1]);
            },

            renderRowBg(item, type) {
                if (!item) return null;
                else if (item.closed_at) return 'show-inactive';
                else if (item.user_id == this.$store.state.user.id) return 'table-success';
                return null;
            },

            locationName(id) {
                if (id && this.$store.state.user.locations_assigned) {
                    const location = this.$store.state.user.locations_assigned.find(e => e.id.toString() === id);
                    if (location) return location.name;
                }
                return id;
            },

            csv() {
                if (!this.reportData) return;

                //const header = this.gridColumns.map(e=>(e.category||'').replace(' ','_') + '-' + e.label.replace(' ','_')); //['number', 'first', 'last', 'handle'];
                const header = this.gridColumns.map(e => e.label);

                const dataObjects = this.reportData.map(dataRow => {
                    let keys = this.gridColumns.map(e => e.key);
                    let o = {};
                    header.forEach((headerItem, index) => {
                        let fieldValue = _.get(dataRow, keys[index]); //deep get of property in format "key.name"
                        if (fieldValue instanceof Big) fieldValue = fieldValue.toFixed(2);
                        o[headerItem] = fieldValue;
                    });
                    return o;
                });
                const csvFromArrayOfArrays = convertArrayToCSV(dataObjects);

                return URL.createObjectURL(new File([csvFromArrayOfArrays], 'test.csv', {type: 'text/csv'}));
            },

            timeZoneName(locationId) {
                if (locationId && this.locationData?.[locationId]?.tz) return this.locationData[locationId].tz;
                return 'America/Denver'; //we haven't downloaded location data, just use default.
            },

            formatMomentToDateString(m) {
                // u.tz(this.timeZoneName(this.$store.state.user.location.id)).format('YYYY-MM-DD HH:mm');
                return momentTz(m).tz(this.timeZoneName(this.$store.state.user.location.id)).startOf('day').format('YYYYMMDD');
            },

            sumObjects(a,b) {
                if (!a) return b;
                if (typeof a==='object' && Object.keys(a).length===0) return _.cloneDeep(b);

                if (b instanceof Big) {
                    return a.plus(b);
                } else if (typeof b==='number') {
                    return a+b;
                } else if (typeof b==='object') {
                    let r = {};
                    Object.keys(b).forEach(item=>{
                        r[item] = this.sumObjects(a[item],b[item]);
                    });
                    return r;
                } else {
                    return null;
                }
            }
        },

        computed: {
            sameDate() {
                return this.startDate===this.endDate;
            },

            showFullTimeOptions() {
                return this.customTime || this.startTime!==0 || this.endTime!==2359 || !this.sameDate;
            },

            locations() {
                return this.$store.state.user.locations_assigned.filter(e => e.type === 'dispensary').map(e => {
                    return {id: e.id, name: e.name}
                });
            },

            schema() {
                return {
                    "filters": {
                        "drawers": {
                            "type": "tabular",
                            "values": [{id: 'sales', name: 'Sales'}, {id: 'drawers', name: 'Drawer Counts'}],
                            "name": "For",
                        },
                        "status": {
                            type: "status",
                            values: ['settled','refunded']
                        },
                        "created_at": {
                            name: "To",
                            type: "daterange",
                            values: [
                                this.getUtcMomentAtLocation(this.$moment().format('YYYY-MM-DD 00:00:00.000'),this.timeZoneName(this.$store.state.user.location.id)), //this.$moment().startOf('day'),
                                this.getUtcMomentAtLocation(this.$moment().format('YYYY-MM-DD 23:59:59.999'),this.timeZoneName(this.$store.state.user.location.id)), // this.$moment().endOf('day')
                            ]
                        }
                    },
                };
            },

            columnsVisible() {
                return (this.gridColumns) ? this.gridColumns.filter((col) => {
                    return (col.toggle === true || !col.hasOwnProperty('toggle'));
                }) : [];
            },

            headers() {
                //look for categories of fields via the category property
                let headers = [];
                let categories = this.columnsVisible.map(e => e.category);

                categories.forEach((categoryName, i) => {
                    if (i === 0) headers.push({category: categoryName, count: 0});
                    if (headers[headers.length - 1].category === categoryName) {
                        let count = headers[headers.length - 1].count + 1;
                        headers.splice(headers.length - 1, 1, {category: categoryName, count: count});
                    }//headers[headers.length-1].count++;
                    else headers.push({category: categoryName, count: 1});
                });
                return headers;
            }
        },

        filters: {
            formatMoney(value) {
                if (typeof value === 'string') return ''; //not valid data
                if (typeof value.eq !== 'function') {
                    return '';
                }

                if (value.eq(0)) return ''; //if zero

                return '$' + Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    minimumFractionDigits: 2
                });
            },
            formatDate(value) {
                const momentFormats = ['YYYY-M-D', 'YYYY-MM-DD', 'YYYY-M', 'YYYY-MM', 'YYYY'];
                return moment(value, momentFormats).format('M/D/YYYY');
            },
        },

        watch: {
            locationData(newValue, oldValue) {
                //after this is loaded, we should re-do the filter time based on the dispensary location time-zone
                if (newValue && !oldValue) {
                    let filters = this.reformatFilters(_.cloneDeep(this.gridFilters.filter));
                    this.gridFilters.filter.created_at[0]=this.$moment(filters.created_at[0]);
                    this.gridFilters.filter.created_at[1]=this.$moment(filters.created_at[1]);
                }
            },

            showFullTimeOptions(newValue) {
                let elements = [
                    {key: 'openDate', short: false, index: -1},
                    {key: 'openTime', short: true, index: -1},
                    {key: 'closeDate', short: false, index: -1},
                    {key: 'closeTime', short: true, index: -1}
                ];
                elements.forEach(element=> {
                    element.index = this.gridColumns.findIndex(e=>e.key===element.key);
                });

                if (newValue) { //show full details
                    elements.forEach(element=> {
                        if (element.index>-1) {
                            this.gridColumns[element.index].tab = 'drawers'; //make sure everyone is shown with drawers
                            this.gridColumns[element.index].toggle = (this.gridFilters.filter.drawers[0]==='drawers'); //only if we are on the 'drawer counts' filter
                            this.gridColumns[element.index].category = ['openDate','openTime'].includes(element.key) ? 'Open' : 'Close';
                            this.gridColumns[element.index].label = ['openDate','closeDate'].includes(element.key) ? 'Date' : 'Time';
                        }
                    });
                } else {
                    elements.forEach(element=> {
                        if (element.index>-1) {
                            this.gridColumns[element.index].tab = element.short ? 'drawers' : ''; //remove from drawers if not showing full details
                            this.gridColumns[element.index].toggle = element.short ? true : false;
                            this.gridColumns[element.index].category = 'Time';
                            this.gridColumns[element.index].label = element.key==='openTime' ? 'Open' : 'Close';
                        }
                    });
                }

            },

            startTime(newValue) {
                let filters = this.reformatFilters(_.cloneDeep(this.gridFilters.filter));
                this.gridFilters.filter.created_at[0]=this.$moment.utc(filters.created_at[0]);
                //if (this.startTime===0 && this.endTime===2359 && this.sameDate) this.customTime=false;
                this.fetchData();
            },

            endTime(newValue) {
                let filters = this.reformatFilters(_.cloneDeep(this.gridFilters.filter));
                this.gridFilters.filter.created_at[1]=this.$moment.utc(filters.created_at[1]);
                //if (this.startTime===0 && this.endTime===2359 && this.sameDate) this.customTime=false;
                this.fetchData();
            },

            gridFilters: {
                handler(to, from) {
                    if (to.filter && to.filter.drawers[0]) { // set visible columns based on sales/drawers option...
                        this.gridColumns.forEach(e => {
                            if (e.tab) e.toggle = (e.tab === to.filter.drawers[0]); //only toggle the columns with a tab property
                        });
                    }
                },
                deep: true
            },
        }

    };

</script>

<style scoped>
    >>> td div.money,
    >>> td div.number {
        text-align: right;
        margin-right: 3px;
    }

    >>> .rowDrawer.hidden {
        display: none;
    }

    >>> .rowLocation {
        font-weight: bold;
    }

    >>> .rowDrawer .mismatch {
        color: red;
    }

    >>> .drawer-not-settled {
        color: var(--danger);
    }

    /* TIME PICKER */

    >>> .filter-wherein .form-group { /* this is the outer div */
        display: inline-block;
        margin-bottom: 0;
        height:20px;
    }

    >>> .filter-wherein .multiselect {
        width:80px;
        border: 0;
        background-color: #f2f2f2;
    }

    >>> .filter-wherein .multiselect__select {
        display: none; /* hide drop-down arrow */
    }

    >>> .filter-wherein .multiselect__single {
        margin-top: 3px;
    }

    >>> .filter-wherein .multiselect__tags {
        padding: 0;
        background-color: unset;
    }

    >>> .filter-wherein .multiselect__content-wrapper {
        min-width: 120px;
    }

    .filter-wherein i.clock {
        vertical-align: middle;
        margin-bottom: 3px;
        cursor: pointer;
    }
    /* TIME PICKER END */
</style>
