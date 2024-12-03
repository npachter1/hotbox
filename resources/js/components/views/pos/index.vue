<template>
    <div class="row gutters">
        <div v-if="section" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card top-blue-bdr">
                <div class="card-header">

                    <div v-if="$route.name==section.module">
                        <h5>{{ section.name }} Menu</h5>
                        <div class="row justify-content-center">
                            <div v-for="(link, ind) in section.views" v-if="!link.hidden" class="card card-stats col-sm-3 mx-3 mt-2">
                                <div class="statistics">
                                    <div class="info">
                                        <div class="icon icon-primary" style="font-size:2em;">
                                            <i :class="link.icon || 'hotbox-icon hotbox-icon-c-pulse'"></i>
                                        </div>
                                        <h6 class="stats-title">
                                            <router-link :to="{name:link.name,params:{}}" tag="a">{{ link.title }} &raquo;</router-link>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="indexView" class="">
                        <span class="float-right">
                            <router-link v-if="drawer" :to="{name:'terminal-drawer'}" tag="a" class=""><i class="hotbox-icon hotbox-icon-cash-register"></i> {{ $store.state.user.name }}'s Drawer <b>${{ drawer.current_balance | dollar }}</b></router-link> | 
                            <a href="" class="mr-2" @click.prevent="loyaltyTriggersModal=!loyaltyTriggersModal"><i class="hotbox-icon hotbox-icon-discount-2"></i> Loyalty Triggers</a>
                        </span>
                        <h5 class="w-100"><i :class="indexView.icon"></i> For {{ $store.state.user.location.name }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    
                <router-view></router-view>

                <hr>
                <div v-if="" class="stats">
                    <router-link v-if="isTerminalPage" :to="{name:'terminal'}" tag="a" class=""><i class="ti-angle-left"></i> Back to Customer Queue</router-link>
                    <router-link v-else-if="!isEditPage" :to="{name:'location_index'}" tag="a" class=""><i class="ti-angle-left"></i> Back to Dashboard</router-link>
                    <router-link v-else-if="indexView" :to="{name: indexView.name}" tag="a" class="head-link"><i class="ti-angle-left"></i> Back to {{ indexView.title || indexView.tabname }} Index</router-link>
                </div>
                </div>
            </div>
            

            <b-modal v-if="" centered ref="loyaltyTriggersModal"
                v-model="loyaltyTriggersModal"
                size="lg"
                header-bg-variant="light"
                header-text-variant="primary">
              
                <template slot="modal-header">
                  <i class="modal-top-close fal ti-close" @click="loyaltyTriggersModal=!loyaltyTriggersModal"></i>
                  <h5 class="w-100 mb-0 text-center">Loyalty Reward Registry</h5>
                </template>
              
                  <loyalty-triggers-modal v-if="loyaltyTriggersModal"
                    :focus="loyaltyTriggersFocus"
                    @refresh="loyaltyTriggersModal=!loyaltyTriggersModal">
                  </loyalty-triggers-modal>
              
                <template slot="modal-footer">
                    <span class="btn-label btn-sm btn-light float-right" @click="loyaltyTriggersModal=!loyaltyTriggersModal">Close</span>
                </template>
            </b-modal>
            
        </div>
        <div v-else class="col-12 text-center mt-4">

        </div>
    </div>
</template>


<script>

    import LoyaltyTriggersModal from './terminal/loyaltyTriggersModal';

    export default {
        props: {
            module: {
                type: String,
                default: 'pos'
            }
        },
        
        data(){
            return {
                loyaltyTriggersModal: false,
                loyaltyTriggersFocus: 'rewards'
            };
        },
        
        components : {
            LoyaltyTriggersModal
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
            
            isTerminalPage(){
                return (this.$route.name.indexOf('terminal-')!==-1) ? true : false;
            },

            indexView(){
                let root = (this.isEditPage) ? this.$route.name.substr(0,this.$route.name.indexOf('_')) : this.$route.name;
                return (this.section) ? (this.section.views) ? this.section.views.find(v=>v.name==root) : null : null;
            },
            
            drawer(){
                return this.$store.state['pos']['drawer'];
            }          
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
