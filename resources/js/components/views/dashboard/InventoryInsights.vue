<template>
    <div class="card card-chart" style="height: 100%">
        <hb-analytics-chart
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
                :no-data-to-show="noDataToShow"
                :customDropDowns="customDropDowns"
                @filterChange="getGraph"
                @chartChange="getGraph">
            <div class="table-responsive">
                <div v-if="showChartData">
                    <b-table v-if="chartData" striped hover
                             id="inventory-insights-table"
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
                             @row-clicked="rowClick"
                             stacked="sm">
                        <template v-slot:cell(selected)="row">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" :checked="selected.includes(row.item.product_id)" @click="toggleSelectedProduct($event, row.item.product_id)" /><span class="custom-control-indicator"></span>
                            </label>
                        </template>
                        <template v-slot:cell()="row">
                            {{ row.value }}
                        </template>
                        <template v-slot:cell(days_until_gone)="row">
                            {{ row.value | formatDays }}
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
                              :default-quantities="poReorder60"
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
        name: 'inventoryInsights',

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
                showCount: 50,
                page: 1,
                selectedProductId: null,
                tableOptions: {
                    sortBy: 'product_name',
                    sortDesc: false
                },
                dataCategory: '',
                dataCategoryOptions: [
                    {value:'quantity_on_hand', label: 'Low Stock Count', yAxisType: 'number', sortDesc: false, backgroundColor: "rgba(220,12,8,0.4)"},
                    {value:'days_until_gone', label: 'Days Left In Stock', yAxisType: 'number', sortDesc: false, backgroundColor: "rgba(17,205,118,0.4)"},
                    // {value:'', label: 'Reorder Guidance', yAxisType: 'number', backgroundColor: "rgba(17,205,118,0.4)"},
                    // {value: 'quantity_sold', label: 'Quantity Sold', yAxisType: 'number', backgroundColor: "rgba(52,91,220,0.4)"},
                    // {value: 'cogs', label: 'Cost of Goods', yAxisType: 'money', backgroundColor: "rgba(255,131,8,0.4)"},
                    // {value: 'margin', label: 'Margin', yAxisType: 'percent', backgroundColor: "rgba(44,255,231,0.4)"}
                    ],
                isLoading: false,
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
                            label: "Low Stock",
                            backgroundColor: "rgba(220,12,8,0.4)",
                            fill: 'origin',
                            pointRadius: .5,
                            pointHoverRadius: 2
                        },
                    ],
                    fields: [
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
                        }, {
                            key: 'quantity_on_hand',
                            label: 'On Hand',
                            sortable: true
                        }, {
                            key: 'quantity_sold_per_day',
                            label: 'Sold/Day',
                            sortable: true,
                            sortDirection: 'desc'
                        }, {
                            key: 'days_until_gone',
                            label: 'Days Left',
                            sortable: true
                        }, {
                            key: 'reorder_30',
                            label: 'Reorder 30',
                            sortable: true
                        }
                        , {
                            key: 'reorder_60',
                            label: 'Reorder 60',
                            sortable: true
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
            };
        },

        computed: {
            customDropDowns() {
                const count = {
                    id: 'count',
                    options: [
                        {label: 'Top 25 Active', value: '25'},
                        {label: 'Show 50 Active', value: '50'},
                        {label: 'Show All', value: '999999'},
                    ]
                };

                return [count];
            },

            poReorder60() {
                return this.selected.map(e=>{
                    let defaultQuantity = (this.chartData.items.find(item=>item.product_id===e)||{reorder_60: 10}).reorder_60; //defaults to 10 if null
                    if (defaultQuantity===0) defaultQuantity=10; //defaults to 10 if 0
                    return {
                        id: e,
                        quantity: defaultQuantity
                    }
                })
            }
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

            rowClick(row) {
                this.$emit('activateTab', {
                    key:'Product History',
                    productId: row.product_id
                })
            },
            handleClick(event,chartElement) {
                if (chartElement.length>0) {
                    // let value = this.chartData.datasets[0].data[productIndex];
                    // let productName = chartElement[0]._view.label;
                    let productIndex = chartElement[0]._index;
                    let productId = this.chartData.product_ids[productIndex];
                    this.$emit('activateTab',{
                        key:'Product History',
                        productId: productId,
                    });
                }
            },
            handleHover(event,chartElement) {
                event.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            },
            getGraph(e) {
                if (e) {
                    // this.startDate = e.startDate;
                    // this.endDate = e.endDate;
                    this.locationId = e.locationId;
                    this.dataCategory = e.currentChart;
                    this.showCount = e.customDropDownValues[0];
                }

                // if (!this.startDate || !this.endDate) return false;
                if (this.isLoading) return false;

                let dataCategoryObject = this.dataCategoryOptions.find(e=> {
                    return e.value===this.dataCategory;
                });

                this.yAxisType = dataCategoryObject.yAxisType;
                // this.chartData.datasets[0].label = this.aggregationType==='' ? dataCategoryObject.label : dataCategoryObject.label + ' Per In-Stock Day';
                this.chartData.datasets[0].label = dataCategoryObject.label;
                this.chartData.datasets[0].backgroundColor = dataCategoryObject.backgroundColor;

                this.tableOptions.sortBy=this.dataCategory; //table sort by
                this.tableOptions.sortDesc=dataCategoryObject.sortDesc; //table sort order

                this.isLoading = true;

                if (this.responseData) {
                    this.drawGraph();
                } else {
                    axios.get('/api/v1/admin/graph/InventoryInsights', {
                        params: {
                            location_id: this.locationId
                        }
                    })
                        .then(response => {  // load geo graph data
                            this.responseData = response.data;
                            // this.responseData = response.data.filter(e => { //we only want those in danger of going out of stock
                            //     return e['days_until_gone']<365;
                            // });
                            this.noDataToShow=(this.responseData.length===0);
                            this.chartData.items = this.responseData;
                            this.showChartData = !this.noDataToShow;
                            this.drawGraph();
                        }).catch(error => {
                        this.isLoading = false;
                    });
                }

            },

            drawGraph() {
                const segments = Array(this.dataCategory); //['quantity_sold'];//, 'revenue_per_day', 'revenue', 'cogs', 'margin'];
                const sortOrder = -1; //this.title.toLowerCase().substring(0,3)==="top" ? 1 : -1; //ascending/descending

                segments.forEach((segment, i) => {
                    let data = _.cloneDeep(this.responseData);

                    data.sort((a, b) => {
                        return (parseFloat(b[segment]) - parseFloat(a[segment]))*sortOrder;
                    });

                    data = data.slice(0,Math.abs(this.showCount));

                    this.chartData.labels = data.map(e => {
                        return e.product_name;
                    });

                    this.chartData.product_ids = data.map(e => {
                        return e.product_id;
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
            formatDays(value) {
                return value>=365 ? '>1 yr' : value;
            }
        }
    }
</script>
<style scoped>
    >>> #inventory-insights-table td {
        cursor: pointer;
    }
    >>> td.select-col {
        width: 1%;
    }
</style>
