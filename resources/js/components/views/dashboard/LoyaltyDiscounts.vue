<template>
    <div>
        <hb-analytics-chart
                dates
                locations
                :available-charts="dataCategoryOptions"
                :default-start-date="startDate"
                :default-end-date="endDate"
                :chart-type="'doughnut-chart'"
                :chart-data="chartData"
                :options="options"
                ref="chart"
                :y-axis-type="yAxisType"
                :is-loading="isLoading"
                :no-data-to-show="noDataToShow"
                @filterChange="getGraph"
                @chartChange="getGraph">
            <div class="table-responsive">
                <div v-if="showChartData">
                    <b-table v-if="chartData" striped hover
                             id="loyalty-discounts-table"
                             :items="chartData.items"
                             :fields="chartData.fields"
                             :busy.sync="isLoading"
                             :show-empty="true"
                             :sort-by="tableOptions.sortBy"
                             :sort-desc="tableOptions.sortDesc"
                             :current-page="page"
                             :per-page="20"
                             responsive="md"
                             @row-clicked="rowClick"
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
    import { randomColor } from 'randomcolor';

    export default {
        name: "LoyaltyDiscounts",

        components: {
            HbAnalyticsChart
        },

        data() {
            return {
                endDate: null,
                startDate: null,
                locationId: null,
                responseData: null,
                showCount: 50,
                page: 1,
                tableOptions: {
                    sortBy: 'discountUsedCount',
                    sortDesc: true
                },
                dataCategory: 'loyalty_discounts',
                dataCategoryOptions: [
                    {
                        value: 'loyalty_discounts',
                        label: 'Discounts Used',
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
                    ids: [],
                    datasets: [ //['notified_count','visited_count','purchased_count','codeused_count'];
                        {
                            data: [],
                            label: "Discounts",
                            //backgroundColor: "rgba(17,205,118,0.2)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                    ],
                    fields: [
                        {
                            key: 'discountName',
                            label: 'Discount',
                            sortable: true,
                            sortDirection: 'asc',
                            tdClass: ''
                        }, {
                            key: 'discountUsedCount',
                            label: 'Used',
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
                    onClick: this.handleClick,
                    onHover: this.handleHover,
                }
            }
        },

        computed: {},

        watch: {},

        methods: {
            rowClick(row) {
                this.$router.push({name:'discount_edit',params:{id:row.discountId}});
            },

            handleClick(event,chartElement) {
                if (chartElement.length>0) {
                    let index = chartElement[0]._index;
                    let id = this.chartData.ids[index];
                    this.$router.push({name:'discount_edit',params:{id}});
                }
            },

            handleHover(event,chartElement) {
                event.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            },

            getGraph(e) {
                let forceDataReload=false;

                if (e) {
                    forceDataReload=((this.startDate !== e.startDate) || (this.endDate !== e.endDate) || (this.locationId !== e.locationId)); //change chart or ID
                    this.locationId = e.locationId;
                    this.startDate = e.startDate;
                    this.endDate = e.endDate;
                }

                axios.get('/api/v1/admin/graph/DispensaryLoyaltyDiscountsUsed', {
                    params: {
                        location_id: this.locationId,
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
                    }).catch(error => {
                    this.isLoading = false;
                });
            },

            drawGraph() {
                if (this.responseData) {
                    const segment = 'discountUsedCount';

                    // const sortOrder = 1;

                    let data = _.cloneDeep(this.responseData);

                    // for now we know the data are sorted by how many times used
                    // data.sort((a, b) => {
                    //     return (parseFloat(b[segment]) - parseFloat(a[segment]))*sortOrder;
                    // });

                    data = data.slice(0,Math.abs(this.showCount));

                    this.chartData.labels = data.map(e => {
                        return e.discountName;
                    });

                    this.chartData.ids = data.map(e => {
                        return e.discountId;
                    });

                    this.chartData.datasets[0].data = data.map(e => {
                        return e[segment];
                    });

                    const hues = ['green','red','blue','orange','purple','yellow','pink','monochrome']; //we change hue each loop to help prevent similar colors
                    this.chartData.datasets[0].backgroundColor = data.map((e,i) => {
                        return randomColor({hue: hues[i % hues.length], luminosity: 'dark',format: 'rgba',alpha: 0.3});
                    });

                    console.log(this.chartData.datasets[0].backgroundColor);

                    this.chartData.datasets[0].label = 'Discounts';

                    this.noDataToShow=(data.length===0);
                    this.chartData.items = data;

                } else {
                    this.chartData.labels = [];
                    this.chartData.datasets[0].data = [];
                    this.chartData.items = [];
                    this.noDataToShow = true;
                }

                this.showChartData = !this.noDataToShow;
                this.$refs.chart.update();
                this.isLoading = false;
            }
        },

        filters: {}
    }
</script>

<style scoped>
    >>> #loyalty-discounts-table td {
        cursor: pointer;
    }
</style>