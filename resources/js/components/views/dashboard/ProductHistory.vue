<template>
    <div class="card card-chart" style="height: 100%">
        <hb-analytics-chart
                :available-charts="[{value:'product_history', label: 'Product History'}]"
                :default-start-date="startDate"
                :default-end-date="endDate"
                :chart-type="'line-chart'"
                :chart-data="chartData"
                :options="options"
                ref="chart"
                x-axis-type="number"
                :y-axis-type="yAxisType"
                :is-loading="isLoading"
                :no-data-to-show="noDataToShow"
                :customDropDowns="customDropDowns"
                @filterChange="getGraph"
                @chartChange="getGraph">
            <div class="table-responsive">
                <div v-if="showChartData">
                    <b-table v-if="chartData" striped hover
                             id="product-history-table"
                             :items="chartData.items"
                             :fields="chartData.fields"
                             :busy.sync="isLoading"
                             :show-empty="true"
                             :sort-by="tableOptions.sortBy"
                             :sort-desc="tableOptions.sortDesc"
                             :current-page="page"
                             :per-page="20"
                             responsive="md"
                             stacked="sm">
                        <template v-slot:cell(date)="row">
                            {{ row.value | formatDate }}
                        </template>
                        <template v-slot:cell()="row">
                            {{ row.value | formatDecimalIfNeeded }}
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
        name: 'inventoryInsights',

        components: {
            HbAnalyticsChart
        },

        props: {
            title: {
                type: String,
                default: ''
            },
            product: {
                type: Number,
                default: null,
            }
        },

        data() {
            return {
                productList: null,
                responseData: null,
                endDate: null,
                startDate: null,
                productId: null,
                showCount: 50,
                page: 1,
                selectedProductId: null,
                tableOptions: {
                    sortBy: 'product_name',
                    sortDesc: false
                },
                dataCategory: 'product_history',
                dataCategoryOptions: [
                    {
                        value: 'quantity_on_hand',
                        label: 'Quantity On Hand',
                        yAxisType: 'number',
                        sortDesc: false,
                        backgroundColor: "rgba(220,12,8,0.4)"
                    },
                    {
                        value: 'quantity_sold_per_day',
                        label: 'Sold per Day',
                        yAxisType: 'number',
                        sortDesc: false,
                        backgroundColor: "rgba(17,205,118,0.4)"
                    },
                ],
                isLoading: true,
                noDataToShow: false,
                showChartData: true,
                yAxisType: 'number',
                chartData: {
                    labels: [],
                    items: [],
                    product_ids: [],
                    datasets: [ //['quantity_sold', 'revenue_per_day', 'revenue', 'cogs', 'margin'];
                        {
                            data: [],
                            label: "Quantity On Hand",
                            backgroundColor: "rgba(220,12,8,0.4)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                        {
                            data: [],
                            label: "Sold Per Day",
                            backgroundColor: "rgba(17,205,118,0.4)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                    ],
                    fields: [
                        {
                            key: 'date',
                            label: 'Week',
                            sortable: true,
                            sortDirection: 'desc',
                            tdClass: ''
                        }, {
                            key: 'quantity_on_hand',
                            label: 'On Hand',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'quantity_sold_per_day',
                            label: 'Sold/Day',
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
                }
            };
        },

        computed: {
            customDropDowns() {
                if (this.productList) {
                    const products = {
                        id: 'product',
                        default: this.productId, //pass in default value (if available) to set on load of drop-down
                        options: this.productList
                    };

                    return [products];
                } else {
                    return [];
                }
            },
        },

        watch: {
            product(newValue) {
                console.log('product prop changed to:', newValue);
                this.productId = newValue;
                if (this.productId) this.getGraph();
            },
            productId(newValue, oldValue) {
                // console.log('productId data changed from/to:', oldValue, newValue);
                // if (oldValue && newValue) {
                //     this.getGraph();
                // }
            }
        },

        created() {
            if (this.product) this.productId = this.product;
            this.getProducts();
        },

        methods: {
            getProducts() {
                axios.get('/api/v1/admin/dispensary/products', {
                    params: {
                        limit: 9999,
                    }
                })
                    .then(response => {  // load geo graph data
                        let data = response.data.data.filter(e => {
                            return e.archived_at === null && e.deleted_at === null;
                        })
                            .map(e => {
                                return {
                                    value: e.id,
                                    label: e.name
                                }
                            });
                        this.productList = data;
                    }).catch(error => {

                });
            },
            getGraph(e) {
                let forceDataReload=false;

                if (e) {
                    if (e.customDropDownValues[0]) {
                        if (e.customDropDownValues[0]===this.productId) {
                            return false; // the chart component is returning a "new" value that is the same as current value, ignore
                        }
                        this.productId = e.customDropDownValues[0];
                        forceDataReload=true;
                    }
                }

                if (this.productId === null) return false;
                // if (this.isLoading) return false;

                this.tableOptions.sortBy = 'date'; //table sort by
                this.tableOptions.sortDesc = true; //dataCategoryObject.sortDesc; //table sort order

                this.isLoading = true;

                if (this.responseData && !forceDataReload) {
                    this.drawGraph();
                } else {
                    axios.get('/api/v1/admin/graph/ProductInventoryInsights', {
                        params: {
                            productId: this.productId,
                        }
                    })
                        .then(response => {  // load geo graph data
                            this.responseData = response.data;
                            this.noDataToShow = (this.responseData.length === 0);
                            this.chartData.items = this.responseData;
                            this.showChartData = !this.noDataToShow;
                            this.drawGraph();
                        }).catch(error => {
                        this.isLoading = false;
                    });
                }

            },

            drawGraph() {
                const segments = ['quantity_on_hand', 'quantity_sold_per_day'];
                const sortOrder = -1;

                segments.forEach((segment, i) => {
                    this.chartData.datasets[i].label = this.dataCategoryOptions[i].label;
                    this.chartData.datasets[i].backgroundColor = this.dataCategoryOptions[i].backgroundColor;

                    let data = _.cloneDeep(this.responseData);

                    data.sort((a, b) => {
                        return (new Date(b.date) - new Date(a.date)) * sortOrder;
                    });

                    data = data.slice(0, Math.abs(this.showCount));

                    this.chartData.labels = data.map(e => {
                        return this.$options.filters.formatDate(e.date);
                    });

                    this.chartData.datasets[i].data = data.map(e => {
                        return e[segment];
                    });
                });

                this.$refs.chart.update();
                this.isLoading = false;
            }
        },

        filters: {
            formatDecimalIfNeeded(value) {
                return Math.round(value*100)/100; //leaves int alone while trimming decimal to 2 max.
            },
            formatDate(value) {
                const momentFormats = ['YYYY-M-D', 'YYYY-MM-DD', 'YYYY-M', 'YYYY-MM', 'YYYY'];
                return moment(value, momentFormats).format('M/D/YYYY');
            }
        }
    }
</script>
<style scoped>
</style>
