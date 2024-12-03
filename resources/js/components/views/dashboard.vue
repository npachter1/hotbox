<template>
    <div v-if="$store.state.user.location" class="">

        <admin-dashboard v-if="mounted && $route.name==='analytics'"></admin-dashboard>
        <grow-dashboard v-else-if="mounted && $store.state.user.location.type==='grow'"></grow-dashboard>
        <dispensary-dashboard v-else-if="mounted && $store.state.user.location.type==='dispensary'"></dispensary-dashboard>

    </div>
    <div v-else class="col-12">
        <loading :display="($store.state.user.location) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import GrowDashboard from './growDashboard';
    import DispensaryDashboard from './dispensaryDashboard';
    import AdminDashboard from './adminDashboard';

    export default {
        data(){
            return {
                isLoading:false,
                mounted:false, //track when resetAuthLocation is complete so there isn't a re-mount of the dashboard component (and it's child components) which was causing double-fetch of all stats
            };
        },
        components : { 
            DispensaryDashboard,GrowDashboard,AdminDashboard
        },
        async mounted() {
     		this.isLoading = true;
     		 let resetted = await this.$store.dispatch('resetAuthLocation',{       // on dashboard view - lets reset the auth location user cache store (click to dash to refresh as failsafe!)
                scope:'user,sections,message',
                route:this.$route,
                reset:true
            });
            this.isLoading = false;
            this.mounted=true;
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
