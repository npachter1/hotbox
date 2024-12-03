<template>
    <div class="row gutters">
        <div v-if="section" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card top-blue-bdr">
                <div class="card-header">

                    <div class="w-100 small text-right mt-1 mb-1 mr-4">
                        <a href="../../../log-viewer" target="_blank" class=""><i class="fal fa-bug"></i> Error Logs &raquo;</a> | 
                        <a href="../../../horizon/dashboard" target="_blank" class=""><i class="fal fa-layer-plus"></i> Redis Horizon Queue &raquo;</a>
                    </div>

                </div>
                <div class="card-body">
                    
                        <router-view></router-view>

                    <hr>
                    <div v-if="" class="stats">
                        <router-link v-if="!isEditPage" :to="{name:'location_index'}" tag="a" class=""><i class="ti-angle-left"></i> Back to Dashboard</router-link>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="col-12 text-center mt-4">
            
            <h5>Hmm, It appears that this ection has not yet been activated.  Please choose another section.</h5>
        </div>
    </div>
</template>


<script>

    export default {
        props: {
            module: {
                type: String,
                default: 'auth'
            }
        },
        
        data(){
            return {

            };
        },
        
        components : {

        },
        
        mounted() {

        },
        
        methods: {

        },
        
        computed: {
            section(){
                return this.$store.state.sections[this.$store.state.disp.section];
            },
            
            isEditPage(){
                return (this.$route.name.indexOf('_')!==-1 && this.$route.name.indexOf('_index')===-1) ? true : false;
            },

            indexView(){
                let root = (this.isEditPage) ? this.$route.name.substr(0,this.$route.name.indexOf('_')) : this.$route.name;
                return (this.section) ? (this.section.views) ? this.section.views.find(v=>v.name==root) : null : null;
            },
        },
        
        watch: {
            $route: function (newRoute, oldRoute) {
                                                                                // route change in section
            }
        }
    };
    
</script>

<style>

</style>