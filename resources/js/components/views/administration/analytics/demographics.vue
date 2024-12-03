<template>
    <div>
        <router-link :to="{name:'analytics'}" tag="a" class=""><i class="ti-angle-left"></i> Back to HB Analytics</router-link>
        <h5 style="margin-bottom:0;">Sales Demographics</h5>

        <b-tabs pills content-class="mt-3" >
            <b-tab v-for="chart in charts" :title="chart.title" :key="chart.key" @click="activeChart=chart" class="analytics-tab">
                <div class="col-12 mb-2 align-self-center">
                    <component
                            v-if="activeChart && activeChart.title===chart.title"
                            :is="activeChart.component"
                            @changeComponent="changeComponent"></component>
                </div>
            </b-tab>
        </b-tabs>
    </div>

</template>

<script>
    import SalesByAge from "../../dashboard/SalesByAge";
    import SalesByLocation from "../../dashboard/SalesByLocation";
    import SalesByType from "../../dashboard/SalesByType";
    import SalesByCustomerCity from "../../dashboard/SalesByCustomerCity";

    export default {
        data(){
            return {
                charts: [
                    {title: 'By Segment', component: 'sales-by-type', key: 'Sales'},
                    {title: 'By Age', component:'sales-by-age', key: 'Age'},
                    {title: 'By Location', component: 'sales-by-customer-city', key: 'Location'}
                ],
                activeChart: {title:'',component:'',key:''},
            };
        },
        components : {
            SalesByType,
            SalesByAge,
            SalesByLocation,
            SalesByCustomerCity
        },
        mounted() {
            this.activeChart = this.charts[0];
        },
        methods: {
            changeComponent(value) {
                this.activeChart.component=value;
            },
        },
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
