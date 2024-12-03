<template>
    <div>
        <router-link :to="{name:'analytics'}" tag="a" class=""><i class="ti-angle-left"></i> Back to HB Analytics</router-link>
        <h5 style="margin-bottom:0;">Loyalty &amp; Marketing Performance</h5>

        <b-tabs pills v-model="activeTab" content-class="mt-3" >
            <b-tab v-for="chart in charts" :title="chart.title" :key="chart.key" @click="activeChart=chart" class="analytics-tab">
                <div class="col-12 mb-2 align-self-center">
                    <component
                            @activateTab="activateTab"
                            v-if="activeChart && activeChart.title===chart.title"
                            :is="activeChart.component"
                            :title="activeChart.title"
                            :product="productId"></component>
                </div>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
    import LoyaltyCampaigns from "../../dashboard/LoyaltyCampaigns";
    import LoyaltyCustomers from "../../dashboard/LoyaltyCustomers";
    import LoyaltyDiscounts from "../../dashboard/LoyaltyDiscounts";

    export default {
        data(){
            return {
                charts: [
                    {title: 'Campaigns', component: 'loyalty-campaigns', key: 'Campaigns'},
                    {title: 'Customers', component: 'loyalty-customers', key: 'Customers'},
                    {title: 'Discounts', component: 'loyalty-discounts', key: 'Discounts'},
                ],
                activeChart: {title:'',component:'',key:''},
                activeTab: 0,
                productId: null, // pass into component if we're focusing no single product
            };
        },
        components : {
            LoyaltyCampaigns,
            LoyaltyCustomers,
            LoyaltyDiscounts,
        },
        mounted() {
            this.activeChart = this.charts[0];
        },
        methods: {
            activateTab(event) {
                const chartIndex = this.charts.findIndex(e=> {
                    return e.key === event.key;
                });
                this.activeTab = chartIndex;
                this.productId = event.productId;
                this.activeChart = this.charts[chartIndex];
            }
        },

        computed: {}
    };
</script>

<style scoped>
    div.tab-pane:focus { /* prevent browser from drawing a focus line around the tab content if user clicks in content area */
        outline: 0 solid transparent;
    }

    >>>.nav-pills {
        padding: 15px 0.7rem;
    }
</style>
