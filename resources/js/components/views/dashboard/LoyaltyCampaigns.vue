<template>
    <div>
        <hb-analytics-chart
                dates
                :available-charts="dataCategoryOptions"
                :default-start-date="startDate"
                :default-end-date="endDate"
                :chart-type="'area-chart'"
                :chart-data="chartData"
                :options="options"
                ref="chart"
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
        name: "LoyaltyCampaigns",

        components: {
            HbAnalyticsChart
        },

        data() {
            return {
                endDate: null,
                startDate: null,
                responseData: false,
                showCount: 50,
                page: 1,
                tableOptions: {
                    sortBy: 'product_name',
                    sortDesc: false
                },
                dataCategory: 'loyalty_campaign',
                dataCategoryOptions: [
                    {
                        value: 'loyalty_campaign',
                        label: 'Campaigns',
                        yAxisType: 'number',
                        sortDesc: false,
                        backgroundColor: "rgba(220,12,8,0.4)"
                    }
                ],
                isLoading: false,
                noDataToShow: false,
                showChartData: true,
                yAxisType: 'number',
                chartData: {
                    labels: [],
                    items: [],
                    product_ids: [],
                    datasets: [ //['notified_count','visited_count','purchased_count','codeused_count'];
                        {
                            data: [],
                            label: "Notified",
                            backgroundColor: "rgba(17,205,118,0.2)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                        {
                            data: [],
                            label: "Visited",
                            backgroundColor: "rgba(220,12,8,0.2)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                        {
                            data: [],
                            label: "Purchased",
                            backgroundColor: "rgba(52,91,220,0.2)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                        {
                            data: [],
                            label: "Code Used",
                            backgroundColor: "rgba(255,131,8,0.2)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                    ],
                    fields: [
                        {
                            key: 'name',
                            label: 'Campaign',
                            sortable: true,
                            sortDirection: 'asc',
                            tdClass: ''
                        }, {
                            key: 'notified_count',
                            label: 'Notified',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'visited_count',
                            label: 'Visited',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'purchased_count',
                            label: 'Purchased',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'codeused_count',
                            label: 'Code Used',
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
                    //onClick: this.handleClick,
                }

            }
        },

        computed: {},

        watch: {},

        methods: {
            getGraph(e) {
                if (e) {
                    this.startDate = e.startDate;
                    this.endDate = e.endDate;
                 }

                this.isLoading = true;

                axios.get('/api/v1/admin/graph/DispensaryLoyaltyCampaign', {
                    params: {
                        start: this.startDate.format('YYYY-MM-DD'),
                        end: this.endDate.format('YYYY-MM-DD'),
                    }
                })
                    .then(response => {  // load geo graph data
                        this.responseData = response.data;
                        this.noDataToShow=(this.responseData.length===0);
                        this.chartData.items = this.responseData;
                        this.showChartData = !this.noDataToShow;
                        this.drawGraph();
                    })
                    .catch(error => {
                        console.log(error);
                        this.isLoading = false;
                    });
            },

            drawGraph() {
                const segments = ['notified_count','visited_count','purchased_count','codeused_count'];
                const sortOrder = -1; //this.title.toLowerCase().substring(0,3)==="top" ? 1 : -1; //ascending/descending

                segments.forEach((segment, i) => {
                    let data = _.cloneDeep(this.responseData);

                    data.sort((a, b) => {
                        return (parseFloat(b[segment]) - parseFloat(a[segment]))*sortOrder;
                    });

                    data = data.slice(0,Math.abs(this.showCount));

                    this.chartData.labels = data.map(e => {
                        return e.name;
                    });

                    // this.chartData.product_ids = data.map(e => {
                    //     return e.product_id;
                    // });

                    this.chartData.datasets[i].data = data.map(e => {
                        return e[segment];
                    });
                });

                this.$refs.chart.update();
                this.isLoading = false;
            }
        },

        filters: {}
    }
</script>

<style scoped>

</style>