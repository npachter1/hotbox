<template>
    <div v-cloak>
        <div class="card card-chart" style="height:100%">
            <div class="card-header">
                <div class="float-right inline-vdp">
                    From <datepicker
                        :value="startDate.format('M/D/YYYY')"
                        name="budtenderStats_start"
                        format="M/d/yyyy"
                        data-date="startDate.format('M/D/YYYY')"
                        :typeable="true"
                        :bootstrap-styling="false"
                        @input="(val) => {startDate = $moment(val)}"
                        :input-class="{'form-control':false,'showdate':true}">
                </datepicker> To
                    <datepicker
                            :value="endDate.format('M/D/YYYY')"
                            name="budtenderStats_start"
                            format="M/d/yyyy"
                            :typeable="true"
                            :bootstrap-styling="false"
                            @input="(val) => {endDate = $moment(val)}"
                            :input-class="{'form-control':false,'showdate':true}">
                    </datepicker>
                </div>

<!--                <h5 class="card-category">
                    <router-link :to="{name: 'sale'}" tag="a">Budtender Sales</router-link>
                </h5>-->
                <h5 class="card-category">Budtender Sales</h5>
                <h2 class="card-title" id="budtenderStats"></h2>
            </div>
            <div class="card-body" ref="cardRef" style="min-height:200px;">
                <loading :display="isLoading" type="loadAsset" />
                <div v-if="itemCount===0 && !isLoading" class="text-center">
                    No Data to Display
                </div>

                <div v-if="itemCount>0" class="chart-area" style="width:90%; margin: 0 auto 60px auto;">
                    <BarChart
                            ref="budtenderChart"
                            :chart-data="chartData"
                            :options="options">
                    </BarChart>
                </div>

                <div class="table-responsive">
                    <b-table
                            small
                            id="budtenderStatsTable"
                            class="table hotbox-table table-condensed"
                            style="font-size: smaller;"
                            :tbody-tr-class="rowClass"
                            @row-clicked="rowClick"
                            v-if="itemCount>0"
                            :items="items"
                            :fields="[
                                {key:'name',tdClass:'text-left'},
                                {key:'saleCount',label:'# Sales',tdClass:'text-right'},
                                {key:'customerCount',label:'# Customers',tdClass:'text-right'},
                                {key:'perSale',label:'Per Sale',tdClass:'text-right'},
                                {key:'totalSales',label:'Sales',tdClass:'text-right'},
                                ]"
                            :current-page="page"
                            :per-page="pageSize">
                        <template v-slot:cell(perSale)="row">${{ row.item.totalSales/row.item.saleCount | formatMoney }}</template>
                        <template v-slot:cell(totalSales)="row">${{ row.value | formatMoney }}</template>
                    </b-table>
                </div>

                <div class="col-12 clearfix filter-pages mb-2 bottom-paginate" ref="paginationDiv" v-if="itemCount>pageSize">
                    <b-pagination
                            v-model="page"
                            :total-rows="itemCount"
                            :per-page="pageSize"
                            hide-goto-end-buttons
                            hide-ellipsis
                            next-text="Next »"
                            prev-text="« Prev"
                            size="sm"
                            class="my-0" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import BarChart from '../../elements/horizontalBarChart';

    export default {
        name: "BudtenderStats",

        props: {
            locationId: {
                type: Number,
                default: null
            }
        },

        components: {
            // BatchEditModal,
            BarChart,
        },

        data() {
            return {
                endDate: this.$moment(),
                startDate: this.$moment().subtract(365,'d'),

                pageSize: 8,
                page: 1,

                selected: [],
                // batchEditFocus:'po',
                // batchEditModal:false,
                gridData: null,
                items: null,
                // today: this.$moment(),
                // thirty: this.$moment().add(30,'d'),
                isLoading:false,
                chartData: {
                    labels:[],
                    items:[],
                    product_ids:[],
                    datasets: [{
                        data: [],
                        label: "Sales",
                        xAxisID: 'bottom',
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
                    },
                        {
                            data: [],
                            label: "Sales Count",
                            xAxisID: 'top',
                            backgroundColor: [
                                'rgba(166,206,227,0.8)',
                                'rgba(31,120,180,0.8)',
                                'rgba(178,223,138,0.8)',
                                'rgba(51,160,44,0.8)',
                                'rgba(251,154,153,0.8)',
                                'rgba(227,26,28,0.8)',
                                'rgba(253,191,111,0.8)',
                                'rgba(255,127,0,0.8)',
                                'rgba(202,178,214,0.8)',
                                'rgba(106,61,154,0.8)',
                                'rgba(255,255,153,0.8)',
                                'rgba(177,89,40,0.8)'
                            ]
                    },
                        {
                            data: [],
                            label: "Per Sale",
                            xAxisID: 'bottom',
                            backgroundColor: [
                                'rgba(166,206,227,0.6)',
                                'rgba(31,120,180,0.6)',
                                'rgba(178,223,138,0.6)',
                                'rgba(51,160,44,0.6)',
                                'rgba(251,154,153,0.6)',
                                'rgba(227,26,28,0.6)',
                                'rgba(253,191,111,0.6)',
                                'rgba(255,127,0,0.6)',
                                'rgba(202,178,214,0.6)',
                                'rgba(106,61,154,0.6)',
                                'rgba(255,255,153,0.6)',
                                'rgba(177,89,40,0.6)'
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
                        display: true
                    },
                    scales: {
                        xAxes : [{
                            id: 'bottom',
                            type: 'linear',
                            position: 'bottom',
                            ticks: {
                                callback: function (label, index, labels) {
                                    return '$' + Number(parseFloat(label).toFixed(0)).toLocaleString('en', {
                                        minimumFractionDigits: 2
                                    });
                                }
                            }
                        },
                            {
                                id: 'top',
                                type: 'linear',
                                position: 'top',
                            }]
                    },
                    onClick: (evt,item) => {
                        if(!item[0]) return false;  // need array instance from vue-chartjs item element - assuming first item for this pie chart.
                        else if(!this.chartData.items[item[0]._index || 0]) return false; // the index of the pie item doesnt exist in the items data.

                        let itemData = this.chartData.items[item[0]._index || 0];
                        this.goToSalesPage(itemData.product_id);
                    }
                }
            };
        },

        mounted() {
            this.fetchGrid();
        },

        watch: {
            endDate() {
                this.fetchGrid();
            },
            startDate() {
                this.fetchGrid();
            }
        },

        computed: {
            itemCount() {
                return (this.items||[]).length;
            }
        },

        methods: {
            rowClick() {
                this.$router.push({name:'user'});
            },

            toggleSelectedProduct(e,id) {
                if (this.clickedOnCheckbox(e)) {
                    if(e.target.checked) {
                        if (!this.selected.includes(id)) this.selected.push(id);
                    }
                    else {
                        this.selected = this.selected.filter(e=>e!==id);
                    }
                }
            },

            clickedOnCheckbox(e) {
                return (((e.target||{}).classList||{value:''}).value.includes('checkbox'));
            },

            onRowSelected(items) {
                this.selected = items.map(e=>e.product.id);
            },

            renderRowBg(item,type){
                if(!item) return null;
                return {
                    'table-success': this.selected.includes(item.product_id),
                };
            },

            rowClass(item) {
                if(!item) return null;

                return {
                    'table-success': this.selected.includes(item.product_id),
                    'table-danger': this.$moment(item.expires_at) < this.today,
                    'table-warning': this.$moment(item.expires_at) < this.thirty
                };
            },

            async fetchGrid(){
                if (this.isLoading) return null;

                this.isLoading = true;

                axios.get('/api/v1/admin/graph/BudtenderStats', {
                    params: {
                        start: this.startDate.format('YYYY-MM-DD'),
                        end: this.endDate.format('YYYY-MM-DD'),
                        location_id: this.locationId,
                    }
                })
                    .then(response => {  // load geo graph data
                        response.data.sort((a,b)=>b.totalSales - a.totalSales);
                        this.items = response.data;
                        this.chartData.items = this.items;
                        this.chartData.labels = this.items.map(v=>{return v.name});
                        this.chartData.datasets[0].data = this.items.map(v=>{return parseFloat(v.totalSales).toFixed(2)});
                        this.chartData.datasets[1].data = this.items.map(v=>v.saleCount);
                        this.chartData.datasets[2].data = this.items.map(v=>parseFloat(v.totalSales/v.saleCount).toFixed(2));
                        if (this.$refs.budtenderChart) this.$refs.budtenderChart.update();
                        this.isLoading = false;
                        this.setCardHeight();
                    })
                    .catch(error => {
                        console.log(error);
                        this.isLoading = false;
                    });
            },

            setCardHeight() {
                this.$nextTick(()=>{
                    if (this.itemCount>this.pageSize && this.$refs.paginationDiv && this.$refs.cardRef) {
                        let paginationHeight =  this.$refs.paginationDiv.clientHeight;
                        let tableHeight = this.$refs.cardRef.clientHeight;
                        this.$refs.cardRef.style.minHeight = (tableHeight + paginationHeight) + 'px';
                    }
                });
            }
        },

        filters: {
            formatMoney(value) {
                return Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    minimumFractionDigits: 2
                });
            },
        }
    }
</script>

<style scoped>
    >>> table#budtenderStatsTable thead tr th {
        text-align: right;
    }
    >>> table#budtenderStatsTable thead tr th:first-child {
        text-align: left;
    }
</style>