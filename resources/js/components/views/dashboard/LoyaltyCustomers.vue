<template>
    <div>
        <hb-analytics-chart
                locations
                :dates="showDates"
                :available-charts="dataCategoryOptions"
                :chart-type="chartType"
                :chart-data="chartData"
                :options="options"
                ref="chart"
                :y-axis-type="yAxisType"
                :is-loading="isLoading"
                :customDropDowns="customDropDowns"
                :no-data-to-show="noDataToShow"
                @filterChange="getGraph"
                @chartChange="getGraph">
            <div class="table-responsive">
                <div v-if="showChartData">
                    <b-table v-if="chartData" striped hover
                             id="loyalty_customer_table"
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
                        <template v-slot:cell(total_sales)="row">
                            {{ row.value | formatMoney }}
                        </template>
                        <template v-slot:cell(total_points)="row">
                            {{ row.value | formatDecimalIfNeeded }}
                        </template>
                        <template v-slot:cell(period)="row">
                            {{ row.value | formatMonth }}
                        </template>
                        <template v-slot:cell(Repeat)="row">
                            {{ row.value | formatMoney }}
                        </template>
                        <template v-slot:cell(New)="row">
                            {{ row.value | formatMoney }}
                        </template>
                        <template v-slot:cell()="row">
                            {{ row.value }}
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
    import _ from "lodash";

    export default {
        name: "LoyaltyCustomers",

        components: {
            HbAnalyticsChart
        },

        data() {
            return {
                endDate: null,
                startDate: null,
                locationId: null,
                month: null,
                responseData: false,
                showCount: 20,
                page: 1,
                tableOptions: {
                    sortBy: '',
                    sortDesc: false
                },
                dataCategory: 'loyalty_customer_rewards',
                dataCategoryOptions: [
                    {value:'loyalty_customer_rewards', label: 'By Rewards', yAxisType: 'number', sortDesc: true, backgroundColor: "rgba(220,12,8,0.4)", apiUrl:'/api/v1/admin/graph/DispensaryLoyaltyCustomersByRewards', chartType:'bar-chart', showDates: false},
                    {value:'loyalty_customer_sales', label: 'By Sales', yAxisType: 'money', sortDesc: true, backgroundColor: "rgba(17,205,118,0.4)", apiUrl:'/api/v1/admin/graph/DispensaryLoyaltyCustomersBySales', chartType:'bar-chart', showDates: false},
                    {value:'loyalty_customer_repeat', label: 'By Retention', yAxisType: 'money', sortDesc: true, backgroundColor: "rgba(17,205,118,0.4)", apiUrl:'/api/v1/admin/graph/DispensaryLoyaltyCustomersRepeat', chartType:'area-chart', showDates: true},
                ],
                isLoading: false,
                noDataToShow: false,
                showChartData: true,
                chartData: {
                    labels: [],
                    items: [],
                    product_ids: [],
                    datasets: [],
                    datasetsSales: [
                        {
                            data: [],
                            label: "Top Customers by Sales",
                            backgroundColor: "rgba(17,205,118,0.2)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                    ],
                    datasetsRetention: [
                        {
                            data: [],
                            label: "Repeat Customer Sales",
                            backgroundColor: "rgba(17,205,118,0.2)",
                            fill: 'origin',
                            //pointRadius: .5,
                            //pointHoverRadius: 2
                        }, {
                            data: [],
                            label: "New Customer Sales",
                            backgroundColor: "rgba(220,12,8,0.2)",
                            fill: 'origin',
                            //pointRadius: .5,
                            //pointHoverRadius: 2
                        },
                    ],
                    fields: [],

                    fieldPoints: [
                        {
                            key: 'id',
                            label: 'ID',
                            sortable: true,
                            sortDirection: 'asc',
                            tdClass: ''
                        }, {
                            key: 'first_name',
                            label: 'First',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'last_name',
                            label: 'Last',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'total_points',
                            label: 'Rewards',
                            sortable: true,
                            sortDirection: 'desc'
                        }
                    ],
                    fieldSales:[
                        {
                            key: 'id',
                            label: 'ID',
                            sortable: true,
                            sortDirection: 'asc',
                            tdClass: ''
                        }, {
                            key: 'first_name',
                            label: 'First',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'last_name',
                            label: 'Last',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'total_sales',
                            label: 'Sales',
                            sortable: true,
                            sortDirection: 'desc',
                            tdClass: 'column-right'
                        }],
                    fieldRetention: [
                        {
                            key: 'period',
                            label: 'Month',
                            sortable: true,
                            sortDirection: 'asc',
                            tdClass: ''
                        }, {
                            key: 'Repeat',
                            label: 'Repeat Customer Sales',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'New',
                            label: 'New Customer Sales',
                            sortable: true,
                            sortDirection: 'desc'
                        }
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
                    },
                    legend: {
                        position: "top",
                        display: true
                    },
                    tooltips: {
                        callbacks: {
                            // label: function(tooltipItem, data) {
                            //     return '$' + Number(parseFloat(tooltipItem.yLabel).toFixed(2)).toLocaleString('en', {
                            //         minimumFractionDigits: 2
                            //     });
                            // }
                            //label: this.tooltipFunction
                        }
                    }
                    //onClick: this.handleClick,
                }

            }
        },

        computed: {
            customDropDowns() {
                if (this.dataCategory==='loyalty_customer_repeat') return []; //no custom dropdowns for repeat customer chart

                if (this.responseData && typeof(this.responseData)==='object' && Object.keys(this.responseData).length>0) {
                    let m = Object.keys(this.responseData);
                    let monthValues = m.map(e=> {
                        return {
                            value: e,
                            label: moment(e,'YYYY-MM').format('MMMM YYYY')
                        };
                    });

                    const months = {
                        id: 'product',
                        default: this.month, //pass in default value (if available) to set on load of drop-down
                        options: monthValues
                    };

                    return [months];
                } else {
                    return [];
                }
            },
            apiUrl() {
                return this.dataCategoryOptions.find(e => {
                    return e.value === this.dataCategory;
                }).apiUrl;
            },
            chartType() {
                return this.dataCategoryOptions.find(e => {
                    return e.value === this.dataCategory;
                }).chartType;
            },
            showDates() {
                return this.dataCategoryOptions.find(e => {
                    return e.value === this.dataCategory;
                }).showDates;
            },
            yAxisType() {
                return this.dataCategoryOptions.find(e => {
                    return e.value === this.dataCategory;
                }).yAxisType;
            },
            tooltipFunction() {
                if (this.yAxisType==='money') {
                    return function(tooltipItem, data) {
                        return '$' + Number(parseFloat(tooltipItem.yLabel).toFixed(2)).toLocaleString('en', {
                            minimumFractionDigits: 2
                        });
                    }
                } else {
                    return function(tooltipItem, data) {
                        return Number(parseFloat(tooltipItem.yLabel).toFixed(2)).toLocaleString('en', {
                            minimumFractionDigits: 2
                        });
                    }
                }
            }
        },

        watch: {},

        methods: {
            getGraph(e) {
                let forceDataReload=false;

                if (e) {
                    forceDataReload=((this.dataCategory !== e.currentChart) || (this.locationId !== e.locationId) || (this.startDate !== e.startDate) || (this.endDate !== e.endDate)); //change chart or ID
                    this.locationId = e.locationId;
                    this.dataCategory = e.currentChart;

                    if (e.startDate) this.startDate = e.startDate; //depends on underlying chart if we get this data
                    if (e.endDate) this.endDate = e.endDate;

                    if (e.customDropDownValues[0]) {
                        this.month = e.customDropDownValues[0];
                    }
                }
                // if (this.dataCategory === 'loyalty_customer_sales') {
                //     this.yAxisType = 'money';  //date/money/number/percent
                // } else {
                //     this.yAxisType = 'number';
                // }

                if (!forceDataReload && this.responseData) {
                    this.drawGraph();
                } else if (!this.isLoading) {
                    this.responseData = null;
                    this.chartData.items = [];
                    this.isLoading = true;

                    axios.get(this.apiUrl, {
                        params: {
                            location_id: this.locationId,
                            start: this.startDate.format('YYYY-MM-DD'),
                            end: this.endDate.format('YYYY-MM-DD'),
                        }
                    })
                        .then(response => {  // load geo graph data
                            this.responseData = response.data;
                            this.drawGraph();
                        })
                        .catch(error => {
                            console.log(error);
                            this.isLoading = false;
                        });
                }
            },

            drawGraph() {
                if (this.dataCategory==='loyalty_customer_repeat') {
                    this.drawGraphRetention();
                } else {
                    this.drawGraphRewardsSales();
                }
            },

            drawGraphRewardsSales() {
                if (this.responseData && typeof(this.responseData)==='object' && !this.month) {
                    this.month = Object.keys(this.responseData)[0];
                }

                if (this.month && this.responseData && this.responseData[this.month]) {
                    this.chartData.datasets = this.chartData.datasetsSales;

                    const segment = (this.dataCategory === 'loyalty_customer_sales') ? 'total_sales' : 'total_points';

                    const sortOrder = 1;

                    let data = _.cloneDeep(this.responseData[this.month]);

                    data.sort((a, b) => {
                        return (parseFloat(b[segment]) - parseFloat(a[segment]))*sortOrder;
                    });

                    data = data.slice(0,Math.abs(this.showCount));

                    this.chartData.labels = data.map(e => {
                        const first = e.first_name ? e.first_name : '';
                        const last = e.last_name ? e.last_name : '';

                        return first || last ? (first + ' ' + last).trim() : 'Customer ' + e.id;
                    });

                    // this.chartData.product_ids = data.map(e => {
                    //     return e.product_id;
                    // });

                    this.chartData.datasets[0].data = data.map(e => {
                        return e[segment];
                    });
                    this.chartData.datasets[0].label = 'Top Customers by ' + (this.dataCategory === 'loyalty_customer_sales' ? 'Sales' : 'Rewards');

                    this.noDataToShow=(data.length===0);
                    this.chartData.items = data;

                    this.chartData.fields = this.dataCategory === 'loyalty_customer_sales' ? this.chartData.fieldSales : this.chartData.fieldPoints;
                } else {
                    this.chartData.labels = [];
                    this.chartData.datasets[0].data = [];
                    this.chartData.items = [];
                    this.noDataToShow = true;
                }

                this.options.tooltips.callbacks.label = this.tooltipFunction;
                this.showChartData = !this.noDataToShow;
                this.$refs.chart.update();
                this.isLoading = false;
            },

            drawGraphRetention() {
                if (this.responseData) {
                    this.chartData.datasets = this.chartData.datasetsRetention;

                    //reorganize responseData by segment, and fill with empty values if none exist
                    let data = {
                        Repeat:null,
                        New:null
                    };

                    let periods = [...new Set(this.responseData.map(e => {
                        return e.period;
                    }))]; //new set removes the dupes

                    const segments = ['Repeat','New'];

                    let tableData = [];

                    segments.forEach((segment, i) => {
                        let filteredData = this.responseData.filter(e => {
                            return e.previousCustomer===i; //0 or 1
                        });

                        data[segment]=_.cloneDeep(filteredData);

                        this.chartData.datasets[i].data = filteredData.map(el => {
                            return el.totalSales;
                        });

                        periods.forEach((period,i)=> {
                            if (!tableData[i]) tableData[i] = {period: period}; //set default object for this period
                            let periodData = filteredData.find(e=> {return e.period===period});
                            if (periodData) tableData[i][segment]=periodData.totalSales;
                        });
                    });

                    this.options.tooltips.callbacks.label = this.tooltipFunction;
                    this.chartData.labels = periods.map(e=>{return this.$options.filters.formatMonth(e)});
                    this.chartData.fields = this.chartData.fieldRetention;
                    this.chartData.items = tableData;

                    this.noDataToShow=(this.responseData.length===0);
                } else {
                    this.chartData.labels = [];
                    this.chartData.datasets[0].data = [];
                    this.chartData.items = [];
                    this.noDataToShow = true;
                }

                this.showChartData = !this.noDataToShow;
                this.$refs.chart.update();
                this.isLoading = false;
            },
        },

        filters: {
            formatDecimalIfNeeded(value) {
                //return Math.round(value*100)/100; //leaves int alone while trimming decimal to 2 max.
                return Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    maximumFractionDigits: 2
                });
            },
            formatMoney(value) {
                if (!value) value=0;
                return '$' + Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    minimumFractionDigits: 2
                });
            },
            formatMonth(value) {
                const momentFormats = ['YYYY-M-D', 'YYYY-MM-DD', 'YYYY-M', 'YYYY-MM', 'YYYY'];
                return moment(value, momentFormats).format('MMMM YYYY');
            }
        }
    }
</script>

<style>
    .column-right {
        text-align: right;
    }
</style>