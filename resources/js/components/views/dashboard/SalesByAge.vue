<template>
    <div class="card card-chart" style="height: 100%">
        <hb-analytics-chart
                dates
                locations
                :available-charts="[{value:'sales-by-age', label: 'Sales By Age'}]"
                :default-start-date="startDate"
                :default-end-date="endDate"
                :chart-type="'bar-chart'"
                :chart-data="chartData"
                :options="options"
                ref="chart"
                x-axis-type="number"
                y-axis-type="number"
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

    export default {
        name: 'salesByAge',

        components: {
            HbAnalyticsChart
        },

        data() {
            return {
                endDate: null,
                startDate: null,
                locationId: null,
                page: 1,
                isLoading: false,
                showChartData: true,
                chartData: {
                    labels: [],
                    items: [],
                    product_ids: [],
                    datasets: [{
                        data: [],
                        label: "Amount Sold (USD)",
                        backgroundColor: [
                            '#a6cee3',
                            '#1f78b4',
                            '#b2df8a',
                            '#33a02c',
                            '#fb9a99',
                            '#e31a1c',
                            '#fdbf6f',
                            '#ff7f00',
                            '#cab2d6',
                            '#6a3d9a',
                            '#ffff99',
                            '#b15928'
                        ]
                    }],
                    fields: [
                        {
                            key: 'age',
                            label: 'Age',
                            sortable: true
                        }, {
                            key: 'visits',
                            label: '#Visits',
                            sortable: true
                        }, {
                            key: 'visit_percent',
                            label: 'Visit %',
                            sortable: true
                        }, {
                            key: 'amount',
                            label: 'Amount Spent',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        }, {
                            key: 'amount_percent',
                            label: 'Amount %',
                            sortable: true,
                            tdAttr: {"align": "right"}
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
                    }
                }
            };
        },

        methods: {
            getGraph(e) {
                if (e) {
                    this.startDate = e.startDate;
                    this.endDate = e.endDate;
                    this.locationId = e.locationId;
                }

                if (!this.startDate || !this.endDate) return false;
                else if (this.isLoading) return false;

                this.isLoading = true;

                axios.get('/api/v1/admin/graph/SalesByAge', {
                    params: {
                        start: this.startDate.format('YYYY-MM-DD'),
                        end: this.endDate.format('YYYY-MM-DD'),
                        location_id: this.locationId
                    }
                })
                    .then(response => {  // load geo graph data
                        this.chartData.labels = response.data.map(v => {
                            return 'Age: ' + v.age
                        });
                        this.chartData.datasets[0].data = response.data.map(v => {
                            return v.amount
                        });

                        let total_visits = response.data.reduce((a, v) => a + v.visits, 0);
                        let total_amount = response.data.reduce((a, v) => a + v.amount, 0);

                        this.chartData.items = response.data.map(r => {
                            return Object.assign({}, r, {                                     // get percentage of visits/amount over total for data sorting
                                visit_percent: (r.visits) ? (total_visits / r.visits) : 0,
                                amount_percent: (r.amount) ? (total_amount / r.amount) : 0
                            });
                        });
                        this.$refs.chart.update();
                        this.isLoading = false;
                    }).catch(error => {
                    this.isLoading = false;
                });
            }
        }
    }
</script>
<style scoped>
</style>
