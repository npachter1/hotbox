<template>
    <div class="row justify-content-center">
        <div class="col-6 col-sm-3 card card-stats mx-3 mt-2">
            <div class="statistics">
                <div class="info">
                    <div class="icon icon-info">
                        <i class="hotbox-icon hotbox-icon-c-pulse"/>
                    </div>
                    <h3 class="info-title">{{ locationCount }} {{ 'Location' | pluralize(locationCount) }}</h3>
                    <h6 class="stats-title">
                        <router-link :to="{name:'analytics-merchandise',params:{}}" tag="a">Location Merchandising
                            Report &raquo;
                        </router-link>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3 card card-stats mx-3 mt-2">
            <div class="statistics">
                <div class="info">
                    <div class="icon" :class="salesIconType">
                        <i class="hotbox-icon hotbox-icon-discount-2"/>
                    </div>
                    <h3 class="info-title">{{ salesDisplay }}</h3>
                    <h6 class="stats-title">
                        <router-link :to="{name:'analytics-demographics',params:{}}" tag="a">Sales Demographics Review
                            &raquo;
                        </router-link>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3 card card-stats mx-3 mt-2">
            <div class="statistics">
                <div class="info">
                    <div class="icon icon-success">
                        <i class="hotbox-icon hotbox-icon-analytics"/>
                    </div>
                    <h3 class="info-title">{{ loyaltyDisplay }}</h3>
                    <h6 class="stats-title">
                        <router-link :to="{name:'analytics-loyalty',params:{}}" tag="a">Loyalty Performance &raquo;
                        </router-link>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import ClickConfirm from 'click-confirm';
    import Chartist from 'chartist';

    export default {
        data() {
            return {
                isLoading: false,
                loyaltyAmount: null,
                salesAmount: null
            };
        },
        components: {
            ClickConfirm
        },
        mounted() {
            this.loadDashboardMetrics();
        },
        methods: {
            loadDashboardMetrics() {
                this.loyaltyAmount = this.$store.getters['analytics/loyaltyAmount'](); //getter returns a function
                this.salesAmount = this.$store.getters['analytics/salesAmount'](); //getter returns a function

                if (!this.salesAmount) {
                    axios.get('/api/v1/admin/graph/DashboardMetricsSales')
                        .then(response => {  // load geo graph data
                            if (response.data && response.data.length===2) { //0==last30; 1==prev30 (30 days before last30)
                                this.salesAmount = ((response.data[0].total_sales - response.data[1].total_sales) / response.data[1].total_sales) * 100;
                                this.$store.commit('analytics/setDashboardMetrics',{salesAmount: this.salesAmount});
                            }
                        }).catch(error => {
                        console.log(error);
                    });
                }

                if (!this.loyaltyAmount) {
                    axios.get('/api/v1/admin/graph/DashboardMetricsLoyalty')
                        .then(response => {  // load geo graph data
                            if (response.data && response.data.length===1) {
                                this.loyaltyAmount = response.data[0].notified_count;
                                this.$store.commit('analytics/setDashboardMetrics',{loyaltyAmount: this.loyaltyAmount});
                            }
                        }).catch(error => {
                        console.log(error);
                    });
                }
            }
        },
        computed: {
            locationCount() {
                if (this.$store.state.user.locations_assigned) return this.$store.state.user.locations_assigned.filter(e => e.type === 'dispensary').length;
                return 0;
            },
            loyaltyDisplay() {
                return this.loyaltyAmount ? this.loyaltyAmount : '...';
            },
            salesDisplay() {
                if (this.salesAmount) {
                    return (this.salesAmount>=0?'+':'') + Math.sign(this.salesAmount) * Math.round(Math.abs(this.salesAmount)) + '%'; //Math.round (on positive value) then apply sign
                }
                return '...';
            },
            salesIconType() {
                return (this.salesAmount>=0?'icon-success':'icon-danger');
            }
        }
    };
</script>
