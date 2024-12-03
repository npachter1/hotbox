<template>
    <div v-cloak>
        <div class="row">

                        <div class="col-md-12">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-primary">
                                                        <i class="hotbox-icon hotbox-icon-cash-register"></i>
                                                    </div>
                                                    <h3 class="info-title">
                                                        <agg-data module="pos" model="sale" field="total_last30" fieldCompare="total_previous30" type="dollar" class=""></agg-data>
                                                    </h3>
                                                    <h6 class="stats-title">
                                                        <router-link :to="{name:'sale',params:{filters:{status:['settled'],created_at:filterValue30Days}}}" tag="a">Last 30 Days Sales &raquo;</router-link>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-info">
                                                        <i class="hotbox-icon hotbox-icon-multiple-19 "></i>
                                                    </div>
                                                    <h3 class="info-title">
                                                        <agg-data module="pos" model="sale" field="repeat_customer_last30" fieldCompare="repeat_customer_previous30" type="count" class=""></agg-data>
                                                    </h3>
                                                    <h6 class="stats-title">
                                                        <span style="display:inline-block">Last 30 Days</span> <span style="display:inline-block">Repeat Customers</span>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-success">
                                                        <i class="hotbox-icon hotbox-icon-multiple-11"></i>
                                                    </div>
                                                    <h3 class="info-title">
                                                        <agg-data module="administration" model="customer" field="new_customers_last30" fieldCompare="new_customers_previous30" type="count" class=""></agg-data>
                                                    </h3>
                                                    <h6 class="stats-title">
                                                        <router-link :to="{name:'customer',params:{filters:{created_at:filterValue30Days}}}" tag="a"><span style="white-space:nowrap;">Last 30 Days</span> <span style="white-space: nowrap">New Customers &raquo;</span></router-link>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
        
        </div>

        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="row">
                    <div class="col-12">
                        <products-expiring />
                    </div>
                    <div class="col-12">
                        <low-inventory />
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6" style="margin-bottom: 20px;" v-if="$store.state.user">
                <top-sellers-by-price
                    :locationId="$store.state.disp.location"
                ></top-sellers-by-price>
            </div>

            <div class="col-12 col-sm-6" v-if="$store.state.user">
                <campaign-stats
                        :locationId="$store.state.disp.location"
                ></campaign-stats>
            </div>

            <div class="col-12 col-sm-6" v-if="$store.state.user">
                <budtender-stats
                        :locationId="$store.state.disp.location"
                ></budtender-stats>
            </div>
        </div>
    </div>
</template>

<script>

    import ClickConfirm from 'click-confirm';
    import Chartist from 'chartist';
    

    import ProductsExpiring from "./dashboard/ProductsExpiring";
    import TopSellersByPrice from "./dashboard/TopSellersByPrice";
    import LowInventory from "./dashboard/LowInventory";
    import CampaignStats from "./dashboard/CampaignStats";
    import BudtenderStats from "./dashboard/BudtenderStats";

    export default {
        data(){
            return {
                isLoading:false,
                startDate: null,
                endDate: null,
                nameOfMonth: moment().format('MMMM'),
                currentMonthFilterValue: [moment().startOf('month').utc().format(),moment().endOf('month').utc().format()],
                filterValue30Days: [moment().startOf('day').subtract(30,'days').utc().format(),moment().endOf('day').utc().format()],
            };
        },
        components : { 
            ClickConfirm,
            Chartist,
            ProductsExpiring,
            TopSellersByPrice,
            LowInventory,
            CampaignStats,
            BudtenderStats,
        },
        mounted() {

        },
        methods: {

        },
        computed: {

        }
    };
</script>
<style>
    .strikethrough{
      text-decoration: line-through;
    }
</style>
<style scoped>
    >>> .vdp-datepicker__calendar{
        left: -300%;
    }

/*    >>> .pagination .page-item .page-link {
        font-size: 95%;
        height: 29px;
        width: 29px;
        min-width: 29px;
        line-height: 29px
    }
*/
    >>> .filter-pages ul.pagination {
        float: unset;
        width: 15%;
        margin: 0 auto;
    }

    >>> .pagination .page-item .page-link {
        box-shadow: none !important;
        border: 0;
        border-radius: unset !important;
        transition: unset;
        padding: unset;
        margin: unset;
        min-width: unset;
        text-align: unset;
        height: unset;
        line-height: unset;
        color: unset;
        cursor: pointer;
        font-size: smaller;
        text-transform: unset;
        background: unset;
        outline: unset;
    }
    >>> .pagination .page-item:first-child .page-link:after {
        content: ' | ';
        white-space: pre;
    }
    >>> a[role="menuitemradio"],
    >>> button[role="menuitemradio"] {
        display: none;
    }
    >>> .bottom-paginate {
        position: absolute;
        bottom: 0;
    }
    >>> table.b-table thead tr {
        background-color: unset;
    }
    >>> table.b-table thead tr th {
        color: unset;
        font-weight: normal;
    }
</style>