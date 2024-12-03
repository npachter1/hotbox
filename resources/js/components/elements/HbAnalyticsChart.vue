<template>
    <div class="card-header">
        <div class="float-right inline-vdp small" style="display: flex;">

            <!--custom dropdowns-->
            <div v-for="(customDropDown, index) in customDropDowns" :key="customDropDown.id">
                <div v-if="customDropDown && customDropDown.options && customDropDown.options.length>0" style="display:flex; margin-right:15px;">
                    <div style="position: relative" class="dropdown">
                        <span data-toggle="dropdown" class="dropdown-label">{{ customDropDownLabels[index] }}</span>
                        <div class="dropdown-menu tight dropdown-menu-right">
                            <a v-for="p in customDropDown.options" class="dropdown-item" @click.prevent="setCustom(p.value, index)"
                               :key="p.value">{{ p.label }}</a>
                        </div>
                        <i data-toggle="dropdown" class="hotbox-icon hotbox-icon-triangle-down-65"></i>
                    </div>
                </div>
            </div>

            <!--date range-->
            <div v-if="dates" style="display:flex;">
                <datepicker v-if="startDate"
                            :value="startDate.format('M/D/YYYY')"
                            name="sales_by_age_start"
                            format="M/d/yyyy"
                            data-date="startDate.format('M/D/YYYY')"
                            :typeable="true"
                            :bootstrap-styling="false"
                            :input-class="{'form-control':false,'showdate':true}"
                            :calendar-button="true"
                            calendar-button-icon="hotbox-icon hotbox-icon-calendar"
                            @input="(val) => {startDate = $moment(val)}">
                </datepicker>
                <div style="padding-right:15px;">through</div>
                <datepicker v-if="endDate"
                            :value="endDate.format('M/D/YYYY')"
                            name="sales_by_age_end"
                            format="M/d/yyyy"
                            data-date="startDate.format('M/D/YYYY')"
                            :typeable="true"
                            :bootstrap-styling="false"
                            :input-class="{'form-control':false,'showdate':true}"
                            :calendar-button="true"
                            calendar-button-icon="hotbox-icon hotbox-icon-calendar"
                            @input="(val) => {endDate = $moment(val)}">
                </datepicker>
            </div>


            <!--periods (day, month, year)-->
            <div v-if="periods" style="display:flex;">
                <div style="position: relative" class="dropdown">
                    <span data-toggle="dropdown" class="dropdown-label">{{ periodLabel }}</span>
                    <div class="dropdown-menu tight dropdown-menu-right">
                        <a v-for="p in periodOptions" class="dropdown-item" @click.prevent="period = p.value"
                           :key="p.value">{{ p.label }}</a>
                    </div>
                    <i data-toggle="dropdown" class="hotbox-icon hotbox-icon-triangle-down-65"></i>
                </div>
            </div>

            <!--locations-->
            <div v-if="locations" style="display:flex;">
                <div style="position: relative;" class="dropdown">
                        <span v-if="locationId=='all'" data-toggle="dropdown"
                              class="dropdown-label">All Locations</span>
                    <span v-else data-toggle="dropdown" class="dropdown-label">{{ locationId | renderValue($store.state.user.locations_assigned) }}</span>
                    <div v-if="$store.state.user" class="dropdown" style="position:absolute;">
                        <div class="dropdown-menu tight dropdown-menu-right">
                            <a v-for="(loc,lid) in $store.state.user.locations_assigned" class="dropdown-item"
                               v-if="loc.type==$store.state.user.location.type" @click.prevent="locationId=loc.id"
                               :key="loc.id">{{ loc.name }}</a>
                            <a class="dropdown-item" @click.prevent="locationId='all'">All Locations</a>
                        </div>
                    </div>
                    <i data-toggle="dropdown" class="hotbox-icon hotbox-icon-triangle-down-65"></i>
                </div>
            </div>

        </div>

        <h5 v-if="availableCharts.length<=1" class="card-category">{{ chartTitle }}</h5>
        <h5 v-else class="card-category">
            <span data-toggle="dropdown" class="dropdown-label" style="cursor: pointer;">{{ chartTitle }}</span>
            <div class="dropdown-menu"
                 style="border-radius:15px;background-color: #f2f2f2;position:absolute;top:0;left:0;">
                <a v-for="chart in availableCharts" class="dropdown-item"
                   @click.prevent="currentChart = chart.value"
                   :key="chart.value">{{ chart.label }}</a>
            </div>
            <i data-toggle="dropdown" class="hotbox-icon hotbox-icon-triangle-down-65" style="cursor: pointer;"></i>
        </h5>

        <div class="card-body">
            <div class="chart-area mb-2">
                <loading :display="isLoading" type="loadAsset"/>
                <div v-if="noDataToShow" class="no-data">
                    <span v-if="!isLoading">No Data To Display</span>
                </div>
                <component
                        v-if="chartType && !noDataToShow"
                        ref="hbChart"
                        :is="chartType"
                        :key="chartKey"
                        :chart-data="chartData"
                        :options="options">
                </component>
                <!--<br><br>-->
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    // you need to fetch your data outside of this component and pass it in as chart-data
    // this component mostly just organizes the common filters, the alternative chart dropdown, and displays the current chart
    // anytime the user changes a filter (or selects from the chart drop-down), it emits the 'filterChange' event and passes all the filter values as {}
    //      it's up to you to then re-create the chartData and call the update() method on this component
    // you can include the bootstrap table via slot content - it's not managed by this component

    import BarChart from './barChart';
    import AreaChart from './AreaChart';
    import DoughnutChart from './doughnutChart';
    import LineChart from "./LineChart";

    export default {
        name: "HbChart",

        components: {
            BarChart,
            AreaChart,
            DoughnutChart,
            LineChart,
        },

        props: {
            availableCharts: { // Array of objects with value (key) and label.  Each one represents a chart to display.  More than one and you get a drop-down option to select
                type: Array,
                default: () => {return [];}
            },
            defaultChart: { // value/key of the above charts to default to first.  Default is first element in availableCharts array
                type: String,
                default: ''
            },
            dates: { // if you want user to be able to filter by start & end date
                type: Boolean,
                default: false
            },
            defaultStartDate: { // defaults to 1 year ago today
                type: Object,
                default: () => {}
            },
            defaultEndDate: { // defaults to today
                type: Object,
                default: () => {}
            },
            chartData: { // chart data for chartjs
                type: Object,
                default: () => {return {};}
            },
            options: { // options for chartjs.  Scales for x/y axis are set within this component for consistency
                type: Object,
                default: () => {return {};}
            },
            chartType: {
                type: String,
                default: 'area-chart',
                validator: function (value) {
                    return ['area-chart', 'bar-chart', 'doughnut-chart', 'line-chart'].indexOf(value) !== -1
                }
            },
            periods: { // if you want to let user choose how to group data (daily, monthly, yearly)
                type: Boolean,
                default: false
            },
            customDropDowns: {
                // arrays of dropdown objects.  Each main object element in the outer array represents a custom drop-down.
                // The individual dropdown is represented as such: {id: 'customDropDown1', options: [{label: 'Top 100', value:'100'},{label: 'Top 50', value: '50'}]}
                type: Array,
                default: () => {return [];}
            },
            locations: { // drop-down of available locations to filter
                type: Boolean,
                default: false
            },
            xAxisType: { // need to know so we can set the scales properly
                type: String,
                default: 'number', //date/money/number
                validator: function (value) {
                    return ['date', 'money', 'number'].indexOf(value) !== -1
                }
            },
            yAxisType: {
                type: String,
                default: 'number', //date/money/number
                validator: function (value) {
                    return ['date', 'money', 'number', 'percent'].indexOf(value) !== -1
                }
            },
            isLoading: { // set this prop to true before you make api call to back-end and false when you are done (or catch an error)
                type: Boolean,
                default: false
            },
            noDataToShow: { // set this prop to true before you make api call to back-end and false when you are done (or catch an error)
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                chartKey: '', //key for chart component.  change unique number to force re-render when you need to update axes (not just underlying data)
                endDate: this.$store.getters['analytics/getEndDate'] || this.$moment(),
                startDate: this.$store.getters['analytics/getStartDate'] || this.$moment().subtract(1, 'year'),
                currentChart: '',
                locationId: this.$store.state.analytics.locationId || 'all',
                page: 1,
                showChartData: true,
                periodOptions: [
                    {value: 'day', label: 'Daily'},
                    {value: 'month', label: 'Monthly'},
                    {value: 'year', label: 'Yearly'}],
                period: this.$store.state.analytics.period || 'day',
                customDropDownValues: [],
            };
        },

        computed: {
            periodLabel() {
                return this.periodOptions.find(e => {
                    return e.value === this.period
                }).label;
            },
            customDropDownLabels() {
                //loop through all customDropDowns and return matching value label
                return this.customDropDowns.map((customDropDown, index) => {
                    if (this.customDropDownValues[index]!==undefined) {
                        return customDropDown.options.find(e => {
                            return e.value === this.customDropDownValues[index];
                        }).label;
                    } else {
                        return '';
                    }
                });
            },

            chartTitle() {
                if (this.currentChart) {
                    return this.availableCharts.find(e => {
                        return e.value === this.currentChart
                    }).label;
                }
                return '';
            },
            filters() {
                return {
                    locationId: this.locationId,
                    startDate: this.startDate,
                    endDate: this.endDate,
                    period: this.period,
                    currentChart: this.currentChart,
                    customDropDownValues: this.customDropDownValues
                }
            }
        },

        watch: {
            filters: {
                handler: function (newValue,oldValue ) {
                    // console.log('filters changed',newValue,oldValue);
                    this.$store.commit('analytics/setFilters',this.filters);

                    // this.$store.commit('analytics/setStartDate',this.startDate);
                    // this.$store.commit('analytics/setEndDate',this.endDate);
                    // this.$store.commit('analytics/setLocationId',this.locationId);
                    // this.$store.commit('analytics/setPeriod',this.period);

                    this.$emit('filterChange',this.filters);
                },
                deep: true
            },
            xAxisType(newValue, oldValue) {
                this.resetAxes();
            },
            yAxisType(newValue, oldValue) {
                this.resetAxes();
            },
            customDropDowns: { //customDropDowns can change after component load (usually async call to get a list from api)
                handler: function (newValue,oldValue ) {
                    // console.log('watch() customDropDownValues newValue/oldValue:',newValue,oldValue);
                     this.customDropDownValues =  this.customDropDowns.map((customDropDown) => {
                        // if there is a default value set, use it.  Otherwise use first item in list.
                        return customDropDown.default ? customDropDown.default : customDropDown.options[0].value;
                        //return customDropDown.options[0].value;
                    });
                    // console.log('watch() customDropDownValues:',this.customDropDownValues);
                },
                deep: true
            },
        },

        created() {
            //parent component can pass in default start/end date, otherwise we just use what's in the store
            if (this.defaultStartDate) this.startDate = this.defaultStartDate;
            if (this.defaultEndDate) this.endDate = this.defaultEndDate;

            this.currentChart = this.defaultChart || this.availableCharts[0].value;

            this.customDropDownValues =  this.customDropDowns.map((customDropDown) => {
                return customDropDown.default ? customDropDown.default : customDropDown.options[0].value;
                // return customDropDown.options[0].value;
            });
            // console.log('created() customDropDownValues:',this.customDropDownValues);
        },

        methods: {
            setCustom(value, index) {
                this.$set(this.customDropDownValues, index, value); //https://vuejs.org/v2/guide/list.html#Caveats
            },
            update() {
                // console.log('hbChart.update()');
                this.resetAxes();
                this.chartKey = new Date().getTime().toString();
                this.$refs['hbChart'].update();
            },
            resetAxes() {
                // console.log(`resetAxes (${this.xAxisType})(${this.yAxisType})`);

                if (this.chartType==='doughnut-chart') {
                    this.options.scales = undefined;
                    return;
                }

                //date/money/number

                const daysDiff = this.endDate.diff(this.startDate, 'days');
                let axisUnit = 'month';
                let axisStep = 1;

                if (daysDiff >= 0 && daysDiff <= 45) axisUnit = 'day';
                if (daysDiff > 45 && daysDiff <= 90) axisUnit = 'week';

                if (!this.options.scales) { // default if not already set
                    this.options.scales = {
                        xAxes: [{}],
                        yAxes: [{}]
                    };
                }

                //x-axis
                if (this.xAxisType==='date') {
                    this.options.scales.xAxes = [{
                        type: 'time',
                        time: {
                            unit: axisUnit,
                            unitStepSize: axisStep
                        }
                    }];
                } else if (this.xAxisType==='money') {
                    this.options.scales.xAxes[0].ticks = {
                        callback: function (label, index, labels) {
                            return '$' + Number(parseFloat(label).toFixed(0)).toLocaleString('en', {
                                minimumFractionDigits: 2
                            });
                        }
                    };

                } else if (this.xAxisType==='number') {
                    this.options.scales.xAxes[0].ticks = {
                        callback: function (label, index, labels) {
                            return label;
                        }
                    };
                }

                //y-axis
                if (this.yAxisType==='date') {
                    this.options.scales.yAxes = [{
                        type: 'time',
                        time: {
                            unit: axisUnit,
                            unitStepSize: axisStep
                        }
                    }];
                } else if (this.yAxisType==='money') {
                    this.options.scales.yAxes[0].ticks = {
                        callback: function (label, index, labels) {
                            return '$' + Number(parseFloat(label).toFixed(0)).toLocaleString('en', {
                                minimumFractionDigits: 2
                            });
                        }
                    };
                } else if (this.yAxisType==='number') {
                    this.options.scales.yAxes[0].ticks = {
                        callback: function (label, index, labels) {
                            return label;
                        }
                    };
                } else if (this.yAxisType==='percent') {
                    this.options.scales.yAxes[0].ticks = {
                        callback: function (label, index, labels) {
                            return Number(parseFloat(label).toFixed(0)).toLocaleString('en', {
                                minimumFractionDigits: 0
                            }) + '%';
                        }
                    };
                }
            },
        }
    }
</script>

<style scoped>
    >>> .vdp-datepicker__calendar {
        left: -200%;
    }

    div.dropdown {
        cursor: pointer;
        top: 0;
        right: 0;
    }

    h5.dropdown {
        position: relative;
    }

    span.dropdown-label {
        margin-left: 10px;
    }

    .chart-area {
        height: unset;
    }

    table {
        margin: 20px 0 0 15px;
        width: calc(100% - 15px);
    }
    .no-data {
        height:300px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(53, 73, 94, 0.58);
        font-size: 1.5em;
    }
    .dropdown-menu:hover {
        cursor: pointer;
    }
</style>