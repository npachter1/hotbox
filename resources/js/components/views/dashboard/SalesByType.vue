<template>
    <div class="card card-chart" style="height: 100%">
        <hb-analytics-chart
                dates
                periods
                locations
                :available-charts="dataCategoryOptions"
                :default-start-date="startDate"
                :default-end-date="endDate"
                :chart-type="'area-chart'"
                :chart-data="chartData"
                :options="options"
                ref="chart"
                :x-axis-type="xAxisType"
                :y-axis-type="yAxisType"
                :is-loading="isLoading"
                @filterChange="getGraph"
                @chartChange="getGraph">
            <div class="table-responsive">
                <div v-if="showChartData">
                    <b-table v-if="chartData" striped hover
                             id="sales_by_customer_city_table"
                             :items="chartData.items"
                             :fields="chartData.fields"
                             :busy.sync="isLoading"
                             :show-empty="true"
                             sort-by="total_sales"
                             :sort-desc="true"
                             :current-page="page"
                             :per-page="20"
                             responsive="md"
                             stacked="sm">
                        <template v-slot:cell(period)="row">
                            {{ row.value | formatDate }}
                        </template>
                        <template v-slot:cell(all)="row">
                            <span v-if="dataCategory==='sales_amount'">${{ row.value | formatMoney }}</span>
                            <span v-else>{{row.value}}</span>
                        </template>
                        <template v-slot:cell(recreational)="row">
                            <span v-if="dataCategory==='sales_amount'">${{ row.value | formatMoney }}</span>
                            <span v-else>{{row.value}}</span>
                        </template>
                        <template v-slot:cell(patient)="row">
                            <span v-if="dataCategory==='sales_amount'">${{ row.value | formatMoney }}</span>
                            <span v-else>{{row.value}}</span>
                        </template>
                        <template v-slot:cell(optin)="row">
                            <span v-if="dataCategory==='sales_amount'">${{ row.value | formatMoney }}</span>
                            <span v-else>{{row.value}}</span>
                        </template>
                    </b-table>

                    <div class="col-12 clearfix filter-pages mb-3" v-if="chartData.items.length>20">
                        <b-pagination v-model="page" :total-rows="chartData.items.length" per-page="20"
                                      class="my-0"></b-pagination>
                    </div>
                </div>
            </div>
        </hb-analytics-chart>

    </div>
</template>

<script>
    import HbAnalyticsChart from "../../elements/HbAnalyticsChart";
    import _ from 'lodash';

    export default {
        name: 'salesByType',

        components: {
            HbAnalyticsChart
        },

        filters: {
            formatMoney(value) {
                return Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    minimumFractionDigits: 2
                });
            },
            formatDate(value) {
                const momentFormats = ['YYYY-M-D', 'YYYY-MM-DD', 'YYYY-M', 'YYYY-MM', 'YYYY'];
                return moment(value, momentFormats).format('M/D/YYYY');
            }
        },

        data() {
            return {
                endDate: null,
                startDate: null,
                locationId: null,
                page: 1,
                xAxisType: 'date',
                yAxisType: 'money',
                period: 'day',
                periodOptions: [
                    {value: 'day', label: 'Daily'},
                    {value: 'month', label: 'Monthly'},
                    {value: 'year', label: 'Yearly'}],

                dataCategory: '',
                dataCategoryOptions: [
                    {value: 'sales_amount', label: 'Total Sales'},
                    {value: 'sales_count', label: 'Total Purchases'},
                    {value: 'customer_visits', label: 'Total Visits'}],

                isLoading: false,
                showChartData: true,
                chartData: {
                    labels: [],
                    items: [],
                    product_ids: [],
                    fields: [
                        {
                            key: 'period',
                            label: 'Date',
                            sortable: true
                        },
                        {
                            key: 'all',
                            label: 'All',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'recreational',
                            label: 'Recreational',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'patient',
                            label: 'Patient',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'optin',
                            label: 'Opt-In',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        }
                    ],
                    datasets: [
                        {
                            data: [],
                            label: "All",
                            backgroundColor: "rgba(17,205,118,0.2)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                        {
                            data: [],
                            label: "Recreational",
                            backgroundColor: "rgba(220,12,8,0.2)",
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                        {
                            data: [],
                            label: "Patient",
                            backgroundColor: 'rgba(52,91,220,0.2)',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                        {
                            data: [],
                            label: "Opt In",
                            backgroundColor: 'rgba(255,131,8,0.2)',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 0,
                            bottom: 0
                        }
                    }
                    ,
                    legend: {
                        position: "top",
                        display:
                            true
                    },
                    plugins: {
                        filler: {
                            propagate: true
                        }
                    },
                }
            }
        },

        created() {
            this.dataCategory = 'sales_amount';
        },

        methods: {
            getGraph(e) {
                if (e) {
                    this.startDate = e.startDate;
                    this.endDate = e.endDate;
                    this.period = e.period;
                    this.locationId = e.locationId;
                    this.dataCategory = e.currentChart;
                }

                if (this.dataCategory === 'sales_amount') {
                    this.yAxisType = 'money';  //date/money/number
                } else {
                    this.yAxisType = 'number';
                }

                if (!this.startDate || !this.endDate) return false;
                else if (this.isLoading) return false;

                this.isLoading = true;

                axios.get('/api/v1/admin/graph/SalesBySegments', {
                    params: {
                        period: this.period,
                        start: this.startDate.format('YYYY-MM-DD'),
                        end: this.endDate.format('YYYY-MM-DD'),
                        location_id: this.locationId
                    }
                })
                    .then(response => {  // load geo graph data

                        response.data.sort(this.sortDates); //make sure chronological

                        const loadRandomTestData = false; // for dev testing
                        let data;

                        if (loadRandomTestData) {
                            const momentFormats = ['YYYY-M-D', 'YYYY-MM-DD', 'YYYY-M', 'YYYY-MM', 'YYYY'];

                            data = this.initializeEmptyArray(moment(this.startDate.format('YYYY-MM-DD'), momentFormats), moment(this.endDate.format('YYYY-MM-DD'), momentFormats), this.period, response.data[0]);

                            response.data.forEach(e => {
                                let i = data.findIndex(f => f.period === e.period);
                                data[i] = _.cloneDeep(e);
                            });
                        } else {
                            data = response.data;
                        }

                        //response.data format:
                        // [{period: '1/1/2019', data: {all: {customer_visits: 0, sales_amount: 0, sales_count: 0}, optin: {}, patient: {}, recreational: {} } },]

                        //Drop-Down Filter: PERIOD (year, month, day)
                        //Drop-Down Filter: TYPE (all, recreational, patient, optin)

                        //x-axis PERIOD (year, month, or day)
                        this.chartData.labels = data.map(e => {
                            return e.period;
                        });

                        const segments = ['all', 'recreational', 'patient', 'optin'];

                        segments.forEach((segment, i) => {
                            this.chartData.datasets[i].data = data.map(e => {
                                if (this.dataCategory === 'sales_amount') {
                                    return this.$options.filters.dollar(e.data[segment][this.dataCategory]);
                                } else {
                                    return e.data[segment][this.dataCategory];
                                }
                            });

                            this.chartData.items = data.map(e => {
                                return {
                                    period: e.period,
                                    all: e.data.all[this.dataCategory],
                                    recreational: e.data.recreational[this.dataCategory],
                                    patient: e.data.patient[this.dataCategory],
                                    optin: e.data.optin[this.dataCategory],
                                }
                            });
                        });

                        this.$refs.chart.update();
                        this.isLoading = false;
                    }).catch(error => {
                    this.isLoading = false;
                });
            },
            setKeyNull(o) {
                if (typeof (o) === 'object') { //need to go deeper
                    return _.mapValues(o, this.setKeyNull);
                } else {
                    return null;
                }
            },
            getNewValues(previousValue, dataCategory) {
                let seed;
                let range = 1;

                if (dataCategory === 'sales_amount') {
                    seed = 1000;
                    range = 100;
                }
                if (dataCategory === 'sales_count') seed = 10;
                if (dataCategory === 'customer_visits') seed = 40;

                let lower = (previousValue || seed) - range;
                if (lower < 0) lower = 0;
                let upper = (previousValue || seed) + range;
                if (upper < lower) upper = seed;

                return [lower, upper];
            },
            loadRandomData(data) {
                for (let i = 0; i < data.length; i++) {
                    for (let segment in data[i].data) {
                        for (let category in data[i].data[segment]) {
                            let newValues = this.getNewValues(i === 0 ? null : data[i - 1].data[segment][category], category);
                            data[i].data[segment][category] = _.random(newValues[0], newValues[1], false);
                        }
                    }
                }
            },
            initializeEmptyArray(start, end, period, object) {
                let dateFormat;
                let data = [];

                const diffPeriod = period + 's';
                if (period === 'day') {
                    dateFormat = 'YYYY-MM-DD';
                } else if (period === 'month') {
                    dateFormat = 'YYYY-MM';
                } else {
                    dateFormat = 'YYYY';
                }
                object = _.mapValues(object, this.setKeyNull);
                let numberDates = end.diff(start, diffPeriod);

                for (let i = 0; i <= numberDates; i++) {
                    data.push(_.cloneDeep(object));
                    data[i].period = start.add(i === 0 ? 0 : 1, diffPeriod).format(dateFormat);
                }
                this.loadRandomData(data);
                return data;
            },
            sortDates(a, b) {
                const momentFormats = ['YYYY-M-D', 'YYYY-M', 'YYYY'];
                const d1 = moment(a.period, momentFormats);
                const d2 = moment(b.period, momentFormats);
                if (d1 && d2) {
                    if (d1.isBefore(d2)) return -1;
                    if (d2.isBefore(d1)) return 1;
                    return 0;
                } else if (d1) {
                    return 1;
                } else if (d2) {
                    return -1
                }
                return 0;
            }
        }
    }
</script>

<style scoped>
</style>