<template>
    <div>
        <router-link :to="{name:'analytics'}" tag="a" class=""><i class="ti-angle-left"></i> Back to HB Analytics</router-link>
        <h5 style="margin-bottom:0;">Product &amp; Merchandising</h5>

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
    import ProductPerformance from "../../dashboard/ProductPerformance";
    import InventoryInsights from "../../dashboard/InventoryInsights";
    import ProductHistory from "../../dashboard/ProductHistory";

    export default {
        props: {
            initialChart: {
                type: String,
                default: 'Product Performance',
            },
        },
        data(){
            return {
                charts: [
                    {title: 'Product Performance', component: 'product-performance', key: 'Product Performance'},
                    {title: 'Inventory Insights', component: 'inventory-insights', key: 'Inventory Insights'},
                    {title: 'Product History', component: 'product-history', key: 'Product History'},
                ],
                activeChart: {title:'',component:'',key:''},
                activeTab: 0,
                productId: null, // pass into component if we're focusing no single product
            };
        },
        components : {
            ProductPerformance,
            InventoryInsights,
            ProductHistory,
        },
        mounted() {
            // this.activeChart = this.charts[0];
            this.activeChart = this.charts.find(e=>e.key===this.initialChart);
            this.activateTab({key: this.initialChart, productId: null});
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
