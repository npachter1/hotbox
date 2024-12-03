<template>
    <div class="card card-chart" style="height: 100%">
        <hb-analytics-chart
                dates
                :available-charts="[{value:'sales-by-customer-city', label: 'Sales By Customer City'},{value:'sales-by-location', label: 'Sales By Retail Location'}]"
                default-chart="sales-by-location"
                :default-start-date="startDate"
                :default-end-date="endDate"
                :chart-type="'doughnut-chart'"
                :chart-data="chartData"
                :options="options"
                ref="chart"
                x-axis-type="number"
                y-axis-type="number"
                :is-loading="isLoading"
                @filterChange="getGraph">
            <div class="table-responsive">
                <div>
                    <b-table v-if="chartData" striped hover
                             id="sales_by_location_table"
                             :items="chartData.items"
                             :fields="chartData.fields"
                             :busy.sync="isLoading"
                             :show-empty="true"
                             sort-by="total_sales"
                             :sort-desc="true"
                             :per-page="100"
                             responsive="md"
                             stacked="sm">

                        <template v-slot:total_sales="row">
                            ${{ row.value | dollar }}
                        </template>

                        <template v-slot:percent="row">
                            {{ row.value | dollar(1) }}%
                        </template>

                        <template v-slot:location_name="row">
                            {{ row.value }}
                        </template>

                        <template v-slot:empty>
                        </template>
                    </b-table>

                    <div class="col-12 clearfix filter-pages mb-3" v-if="chartData.items.length>20">
                        <b-pagination
                                v-model="page"
                                :total-rows="chartData.items.length"
                                per-page="20"
                                class="my-0"></b-pagination>
                    </div>
                </div>
            </div>
        </hb-analytics-chart>
    </div>
</template>

<script>
    import HbAnalyticsChart from '../../elements/HbAnalyticsChart';

    export default {
        name: 'salesByLocation',

        components: {
            HbAnalyticsChart
        },

        data() {
            return {
                endDate: null,
                startDate: null,
                isLoading: false,
                showChartData: true,

                dataCategory: 'sales-by-location',
                dataCategoryOptions: [
                    {value: 'sales-by-customer-city', label: 'Customer City'},
                    {value: 'sales-by-location', label: 'Location'}],

                chartData: {
                    labels: [],
                    items: [],
                    product_ids: [],
                    fields: [
                        {
                            key: 'location_name',
                            label: 'Location',
                            sortable: true
                        },
                        {
                            key: 'total_sales',
                            label: 'Sales',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'percent',
                            label: 'Sales %',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        }
                    ],
                    datasets: [{
                        data: [],
                        label: "Price",
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
                    }]
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
                        display:
                            true
                    }
                }
            };
        },

        methods: {
            getGraph(e) {
                if (e) {
                    if (e.currentChart !== 'sales-by-location') {
                        this.changeComponent(e.currentChart);
                        return;
                    }
                    this.startDate = e.startDate;
                    this.endDate = e.endDate;
                }

                if (!this.startDate || !this.endDate) return false;
                else if (this.isLoading) return false;

                this.isLoading = true;

                axios.get('/api/v1/admin/graph/SalesByLocation', {
                    params: {
                        start: this.startDate.format('YYYY-MM-DD'),
                        end: this.endDate.format('YYYY-MM-DD')
                    }
                })
                    .then(response => {  // load geo graph data
                        let totalSales = response.data.reduce((a, b) => {
                            return a + parseFloat(b.total_sales);
                        }, 0);

                        let data = response.data.map(obj => {
                            return {...obj, percent: (obj.total_sales / totalSales) * 100}
                        });

                        this.chartData.labels = data.map(v => {
                            return v.location_name
                        });

                        this.chartData.items = data;// assume the items already descending, so we can map the index to the data for the onClick

                        this.chartData.datasets[0].data = data.map(v => {
                            return this.$options.filters.dollar(v.total_sales);
                        });
                        this.$refs.chart.update();
                        this.isLoading = false;
                    }).catch(error => {
                    this.isLoading = false;
                });
            },
            changeComponent(component) {
                this.$emit('changeComponent', component);
            }
        }
    }
</script>

<style scoped>
</style>