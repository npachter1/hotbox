<template>
    <div class="card card-chart" style="height: 100%">
        <hb-analytics-chart
                dates
                locations
                :available-charts="dataCategoryOptions"
                :default-start-date="startDate"
                :default-end-date="endDate"
                :chart-type="'bar-chart'"
                :chart-data="chartData"
                :options="options"
                ref="chart"
                x-axis-type="number"
                :y-axis-type="yAxisType"
                :is-loading="isLoading"
                :customDropDowns="customDropDowns"
                :no-data-to-show="noDataToShow"
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
                             :sort-by="tableOptions.sortBy"
                             :sort-desc="tableOptions.sortDesc"
                             :current-page="page"
                             :tbodyTrClass="renderRowBg"
                             :per-page="20"
                             responsive="md"
                             stacked="sm">
                        <template v-slot:cell(selected)="row">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" :checked="selected.includes(row.item.product_id)" @click="toggleSelectedProduct($event, row.item.product_id)" /><span class="custom-control-indicator"></span>
                            </label>
                        </template>
                        <template v-slot:cell(product_name)="row">
                            {{ row.value }}
                        </template>
                        <template v-slot:cell(quantity_sold)="row">
                            {{ row.value }}
                        </template>
                        <template v-slot:cell(margin)="row">
                            {{ row.value | formatPercent }}
                        </template>
                        <template v-slot:cell()="row">
                            {{ row.value | formatMoney }}
                        </template>
                    </b-table>

                    <div class="col-12 clearfix filter-pages mb-3" v-if="chartData.items.length>20">
                        <b-pagination v-model="page" :total-rows="chartData.items.length" per-page="20"
                                      class="my-0"></b-pagination>
                    </div>

                    <div v-if="selected.length>0" class="col-12 batch-block">
                        <i class="batch-icon"></i> With Selected:
                        <select v-model="batchEditFocus" class="batch-sel">
                            <option value="promote">Promote (Campaign)</option>
                            <option value="po">Generate PO</option>
                        </select>
                        <button class="btn btn-sm" :class="{'btn-light':selected.length<=0,'btn-warning':selected.length>0}" @click.default="batchEditModal=!batchEditModal" :disabled="selected.length===0">Batch It!</button>
                    </div>
                </div>
            </div>
        </hb-analytics-chart>

        <b-modal centered ref="batchEditModal"
                 v-model="batchEditModal"
                 size="xl"
                 header-bg-variant="light"
                 header-text-variant="primary">

            <template slot="modal-header">
                <i class="modal-top-close fal ti-close" style="cursor: pointer;" @click="batchEditModal=!batchEditModal"></i>
                <h5 class="w-100 mb-0 text-center">Modify Batch of ({{ selected.length }}) Items</h5>
            </template>

            <batch-edit-modal v-if="batchEditModal"
                              :focus="batchEditFocus"
                              :ids.sync="selected"
                              @refresh="batchEditModal=false"
                              :schema="{meta:{resource:'admin/dispensary/products'}}">
            </batch-edit-modal>

            <template slot="modal-footer">
                <a @click.default="batchEditModal=!batchEditModal" class="btn btn-sm btn-light float-right">Close</a>
                <!--<span class="btn-label btn-sm btn-light float-right" style="cursor: pointer;" @click="batchEditModal=!batchEditModal">Close</span>-->
            </template>
        </b-modal>
    </div>
</template>

<script>
    import HbAnalyticsChart from "../../elements/HbAnalyticsChart";
    import BatchEditModal from "./batchEditModal";

    import _ from "lodash";

    export default {
        name: 'productPerformance',

        components: {
            HbAnalyticsChart,
            BatchEditModal,
        },

        props: {
            title: {
                type: String,
                default: ''
            }
        },

        data() {
            return {
                selected: [],
                batchEditFocus:'po',
                batchEditModal:false,

                responseData: null,
                endDate: null,
                startDate: null,
                locationId: null,
                aggregationType: '',
                showCount: 50,
                tableOptions: {
                    sortBy: 'product_name',
                    sortDesc: false
                },
                page: 1,
                dataCategory: '',
                dataCategoryOptions: [
                    {value:'revenue', label: 'Revenue', yAxisType: 'money', sortDesc: true, backgroundColor: "rgba(220,12,8,0.4)"},
                    {value:'profit', label: 'Profit', yAxisType: 'money', sortDesc: true, backgroundColor: "rgba(17,205,118,0.4)"},
                    {value: 'quantity_sold', label: 'Quantity Sold', yAxisType: 'number', sortDesc: true, backgroundColor: "rgba(52,91,220,0.4)"},
                    {value: 'cogs', label: 'Cost of Goods', yAxisType: 'money', sortDesc: false, backgroundColor: "rgba(255,131,8,0.4)"},
                    {value: 'margin', label: 'Margin', yAxisType: 'percent', sortDesc: true, backgroundColor: "rgba(44,255,231,0.4)"}],
                isLoading: false,
                noDataToShow: false,
                showChartData: true,
                yAxisType: 'money',
                chartData: {
                    labels: [],
                    items: [],
                    product_ids: [],
                    datasets: [ //['quantity_sold', 'revenue_per_day', 'revenue', 'cogs', 'margin'];
                        {
                            data: [],
                            label: "Quantity Sold",
                            backgroundColor: "rgba(220,12,8,0.4)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                    ],
                    fieldsOverall: [
                        {
                            key: 'selected',
                            label: '',
                            tdClass: 'select-col'
                        },
                        {
                            key: 'product_name',
                            label: 'Product',
                            sortable: true,
                            tdClass: 'product-name'
                        },
                        {
                            key: 'revenue',
                            label: 'Revenue',
                            sortable: true,
                            sortDirection: 'desc',
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'profit',
                            label: 'Profit',
                            sortable: true,
                            sortDirection: 'desc',
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'quantity_sold',
                            label: 'Items Sold',
                            sortable: true,
                            sortDirection: 'desc',
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'cogs',
                            label: 'COGS',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'margin',
                            label: 'Margin',
                            sortable: true,
                            sortDirection: 'desc',
                            tdAttr: {"align": "right"}
                        }
                    ],
                    fieldsPerDay: [
                        {
                            key: 'selected',
                            label: '',
                            tdClass: 'select-col'
                        },
                        {
                            key: 'product_name',
                            label: 'Product',
                            sortable: true,
                            tdClass: 'product-name'
                        },
                        {
                            key: 'revenue_per_day',
                            label: 'Revenue/Day',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'profit_per_day',
                            label: 'Profit/Day',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'quantity_sold_per_day',
                            label: 'Items Sold/Day',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'cogs_per_day',
                            label: 'COGS/day',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        },
                        {
                            key: 'margin',
                            label: 'Margin',
                            sortable: true,
                            tdAttr: {"align": "right"}
                        }
                    ],
                    fields: [],
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

        computed: {
            customDropDowns() {
                const count = {
                    id: 'count',
                    options: [
                        {label: 'Top 25', value: '25'},
                        {label: 'Top 50', value: '50'},
                        {label: 'Bottom 25', value: '-25'},
                        {label: 'Bottom 50', value: '-50'},
                        {label: 'Show All', value: '999999'},
                    ]
                };
                const unit = {
                    id: 'unit',
                    options: [
                        {label: 'Overall', value: ''},
                        {label: 'Per In-Stock Day', value: '_per_day'}
                    ]
                };

                if (this.dataCategory==='margin') {
                    return [count]
                } else {
                    return [count, unit];
                }
            },
        },

        watch: {},

        methods: {
            toggleSelectedProduct(e,id) {
                if(e.target.checked && !this.selected.includes(id)) this.selected.push(id);
                else this.selected = this.selected.filter(e=>e!==id);
            },

            onRowSelected(items) {
                this.selected = items.map(e=>e.product_id);
            },

            renderRowBg(item,type){
                if(!item) return null;
                return {
                    'table-success': this.selected.includes(item.product_id),
                };
            },

            getGraph(e) {
                const forceDataReload = (this.startDate!==e.startDate||this.endDate!==e.endDate||this.locationId!==e.locationId);

                if (e) {
                    this.startDate = e.startDate;
                    this.endDate = e.endDate;
                    this.locationId = e.locationId;
                    this.dataCategory = e.currentChart;
                    this.showCount = e.customDropDownValues[0];
                    this.aggregationType = e.customDropDownValues[1];
                }

                if (!this.startDate || !this.endDate) return false;
                else if (this.isLoading) return false;

                let dataCategoryObject = this.dataCategoryOptions.find(e=> {
                    return e.value===this.dataCategory;
                });

                if (this.dataCategory==='margin') {
                    this.aggregationType=''; //remove "_per_day" for charts that don't support it
                }

                this.yAxisType = dataCategoryObject.yAxisType;
                this.chartData.datasets[0].label = this.aggregationType==='' ? dataCategoryObject.label : dataCategoryObject.label + ' Per In-Stock Day';
                this.chartData.datasets[0].backgroundColor = dataCategoryObject.backgroundColor;

                this.tableOptions.sortBy=this.dataCategory; //table sort by
                this.tableOptions.sortDesc=dataCategoryObject.sortDesc; //table sort order

                this.isLoading = true;

                if (this.aggregationType==='') {
                    this.chartData.fields = this.chartData.fieldsOverall;
                } else {
                    this.chartData.fields = this.chartData.fieldsPerDay;
                }

                if (this.responseData && !forceDataReload) {
                    this.drawGraph();
                } else {

                    this.showChartData = false;
                    axios.get('/api/v1/admin/graph/SalesProductPerformance', {
                        params: {
                            start: this.startDate.format('YYYY-MM-DD'),
                            end: this.endDate.format('YYYY-MM-DD'),
                            location_id: this.locationId
                        }
                    })
                        .then(response => {  // load geo graph data
                            //this.$emit('dataLoaded',response.data);
                            this.responseData = response.data;
                            this.noDataToShow=(response.data.length===0);
                            this.chartData.items = response.data;
                            this.showChartData = (response.data.length>0);
                            this.drawGraph();
                        }).catch(error => {
                        this.isLoading = false;
                    });
                }

            },

            drawGraph() {
                const segments = Array(this.dataCategory + this.aggregationType); //['quantity_sold'];//, 'revenue_per_day', 'revenue', 'cogs', 'margin'];

                let sortOrder = this.showCount>0 ? 1 : -1; //ascending/descending
                if (this.dataCategory==='cogs') sortOrder*=-1; //cogs is flipped, smaller is better

                segments.forEach((segment, i) => {
                    let data = _.cloneDeep(this.responseData);

                    data.sort((a, b) => {
                        return (parseFloat(b[segment]) - parseFloat(a[segment]))*sortOrder;
                    });

                    data = data.slice(0,Math.abs(this.showCount));

                    this.chartData.labels = data.map(e => {
                        return e.product_name;
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
            formatMoney(value) {
                return '$' + Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    minimumFractionDigits: 2
                });
            },

            formatPercent(value) {
                return Number(parseFloat(value).toFixed(0)).toLocaleString('en', {
                    minimumFractionDigits: 0
                }) + '%';
            }
        }
    }
</script>
<style scoped>
    >>> td.product-name {
        max-width:250px;
    }
    >>> td.select-col {
        width: 1%;
    }
</style>
