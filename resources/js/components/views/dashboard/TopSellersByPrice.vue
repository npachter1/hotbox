<template>
        <div class="card card-chart" stylez="height: 100%">
            <div class="card-header">
                <div class="float-right inline-vdp">
                From <datepicker
                    :value="startDate.format('M/D/YYYY')"
                    name="topSellersByPrice_start"
                    format="M/d/yyyy"
                    data-date="startDate.format('M/D/YYYY')"
                    :typeable="true"
                    :bootstrap-styling="false"
                    @input="(val) => {startDate = $moment(val)}"
                    :input-class="{'form-control':false,'showdate':true}">
                </datepicker> To 
                <datepicker
                        :value="endDate.format('M/D/YYYY')"
                    name="topSellersByPrice_start"
                    format="M/d/yyyy"
                    :typeable="true"
                    :bootstrap-styling="false"
                        @input="(val) => {endDate = $moment(val)}"
                    :input-class="{'form-control':false,'showdate':true}">
                </datepicker>
                </div>

                <h5 class="card-category">
                    <router-link :to="{name:'analytics-merchandise', params: { initialChart: 'Product Performance'} }" tag="a">Top Sellers by Price »</router-link>
                </h5>
                <!--<h5 class="card-category">Top Sellers by Price</h5>-->
                <h2 class="card-title" id="topSellersByPrice"></h2>
            <div class="card-body" ref="cardRef" style="min-height:200px;">
                <loading :display="isLoading" type="loadAsset" />

                <div v-if="itemCount===0 && !isLoading" class="text-center">
                    No Data to Display
                </div>

                <div v-if="itemCount>0" class="chart-area" style="margin-bottom:60px;">
                    <DoughnutChart
                        ref="topSellersByPriceChart"
                        :chart-data="chartData"
                        :options="options">
                    </DoughnutChart>
                </div>

                <div class="table-responsive">
                    <b-table
                            small
                            id="topSellersTable"
                            class="table hotbox-table table-condensed"
                            style="font-size: smaller;"
                            @row-clicked="rowClick"
                            v-if="itemCount>0"
                            :items="chartData.items"
                            :fields="[{key:'product_name',tdClass:'text-left'},{key:'total_sale_price',label:'Total Sales',tdClass:'text-right'}]"
                            :current-page="page"
                            :per-page="pageSize">
                        <template v-slot:cell(total_sale_price)="row">${{ row.value }}</template>
                    </b-table>
                </div>

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
</template>

<script>

import DoughnutChart from '../../elements/doughnutChart';

export default {

    props: {
      locationId: {
        type: Number,
        default: null
      }
    },

    components:{
        DoughnutChart
    },

    data() {
      return {
            pageSize: 10,
            page: 1,

            endDate: this.$moment(),
            startDate: this.$moment().subtract(365,'d'),
            isLoading:false,
            chartData: {
                labels:[],
                items:[],
                product_ids:[],
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
                  display: true
                },
                onClick: (evt,item) => {
                    if(!item[0]) return false;  // need array instance from vue-chartjs item element - assuming first item for this pie chart.
                    else if(!this.chartData.items[item[0]._index || 0]) return false; // the index of the pie item doesnt exist in the items data.
                    
                    let itemData = this.chartData.items[item[0]._index || 0];
                    this.rowClick(itemData);
                }
            }
      };
    },

    mounted () {
        this.getGraph();
    },

    watch: {
        startDate: {
            handler: function(to,From){
                if(to) this.getGraph();
            },
            deep: true
        },
        endDate: {
            handler: function(to,from){
                if(to) this.getGraph();
            },
            deep: true
        }
    },

    computed: {
        itemCount() {
            return ((this.chartData||{}).items||[]).length;
        }
    },

    methods: {
      getGraph(){
        if(!this.startDate || !this.endDate) return false;
        else if(this.isLoading) return false;

        this.isLoading = true;
        axios.get('/api/v1/admin/graph/TopSellersByPrice?start='+this.startDate.format('YYYY-MM-DD HH:MM:SS')+'&end='+this.endDate.format('YYYY-MM-DD HH:MM:SS')+'&locationId='+this.locationId+'&limit=10').then(response => {  // load geo graph data
            this.chartData.labels = response.data.map(v=>{return v.product_name});
            this.chartData.items = response.data;                               // we will assume the items are already in descending order, so we can map the index to the data for the onClick
            this.chartData.datasets[0].data = response.data.map(v=>{return v.total_sale_price});
            if (this.$refs.topSellersByPriceChart) this.$refs.topSellersByPriceChart.update();
            this.isLoading = false;
            this.setCardHeight();
        }).catch(error => {
            this.isLoading = false;
            // 
        });
      },

      rowClick(item) {
          this.$router.push({ name:'product', params: {id: item.product_id }})
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
    }
}



</script>
<style scoped>
    >>> table#topSellersTable thead tr th {
        text-align: left;
    }
    >>> table#topSellersTable thead tr th:last-child {
        text-align: right;
    }
</style>
