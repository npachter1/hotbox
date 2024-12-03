<template>
    <div class="row gutters">
        <div v-if="section" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card top-blue-bdr">
                <div class="card-header">
                    <div v-if="indexView" class="">
                        
                        <router-link v-if="['inventory','product'].indexOf(indexView.name)!==-1" :to="{name:'campaign_edit',params:{id:0}}" tag="a" class="float-right">
                            <i class="ml-2 hotbox-icon hotbox-icon-notification-70"></i>
                             Create Campaign..
                        </router-link>
                        
                        <a v-if="indexView.name=='receiving' && !isEditPage && regAgent=='metrc'" href="" class="show-red float-right ml-2 mr-2" @click.prevent="metrcTransferModal=!metrcTransferModal">
                            <i class="hotbox-icon hotbox-icon-refresh-69"></i> Import From Metrc &raquo;
                        </a>
                        <a v-if="indexView.name=='receiving' && !isEditPage && regAgent=='manual'" href="" class="show-red float-right ml-2 mr-2" @click.prevent="metrcTransferModal=!metrcTransferModal">
                            <i class="hotbox-icon hotbox-icon-refresh-69"></i> Import From Grow &raquo;
                        </a>
                        
                        <a v-if="indexView.name=='receiving' && isEditPage && regAgent=='metrc'" href="" class="float-right mr-2" @click.prevent="syncMetrcPackages">
                            <spinner :isProcessing="isSyncing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                            <i v-if="!isSyncing" class="hotbox-icon hotbox-icon-refresh-69"></i> {{ (isSyncing) ? 'Syncing' : 'Sync' }} Metrc Packages
                        </a>
                        
                        <a href="" v-if="['inventory'].indexOf(indexView.name)!==-1" class="float-right mr-2" @click.prevent="strainModal=!strainModal"><i class="hotbox-icon hotbox-icon-plant-leaf"></i> Manage Strains &raquo;</a>

                        <router-link v-if="indexView.with_add && !isEditPage" :to="{name: indexView.name+'_create'}" tag="a" class="float-right mt-0 mb-3 mr-2">
                            <span class="btn-label"><i class="hotbox-icon hotbox-icon-e-add"></i></span> <b>New {{ indexView.resource_name || indexView.name | ucwords }}</b>
                        </router-link>
                        
                        <h5><i :class="indexView.icon"></i> For {{ $store.state.user.location.name }}</h5>
                    </div>
                    
                    <div v-if="$route.name==section.module">
                        <h5>{{ section.name }} Menu</h5>
                        <div class="row justify-content-center">
                            <div v-for="(link, ind) in section.views" class="card card-stats col-sm-3 mx-3 mt-2">
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
                    
                </div>
                <div class="card-body">
                    
                    <div v-if="indexView">
                        <router-view v-if="$store.getters.userCan(indexView.can_view)"></router-view>
                    </div>

                    <hr>
                    <div v-if="" class="stats">
                        <router-link v-if="!isEditPage" :to="{name:'location_index'}" tag="a" class=""><i class="ti-angle-left"></i> Back to Dashboard</router-link>
                        <router-link v-else-if="indexView" :to="{name: indexView.name}" tag="a" class="head-link"><i class="ti-angle-left"></i> Back to {{ indexView.title || indexView.tabname }} Index</router-link>
                    </div>
                </div>
            </div>
            
        <b-modal centered ref="metrcTransferModal"
            v-model="metrcTransferModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="metrcTransferModal=!metrcTransferModal"></i>
              <h5 class="w-100 mb-0 text-center">Import A Metrc Transfer</h5>
            </template>
          
              <metrc-transfer-modal v-if="metrcTransferModal"
                @refresh="metrcTransferModal=!metrcTransferModal">
              </metrc-transfer-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="metrcTransferModal=!metrcTransferModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="strainModal"
            v-model="strainModal"
            size="xl"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="strainModal=!strainModal"></i>
              <h5 class="w-100 mb-0 text-center">Manage Strains</h5>
            </template>
          
              <strain-modal v-if="strainModal"
                @refresh="strainModal=!strainModal">
              </strain-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="strainModal=!strainModal">Close</span>
            </template>
        </b-modal>

            
        </div>
        <div v-else class="col-12 text-center mt-4">

        </div>
    </div>
</template>


<script>

    import MetrcTransferModal from './receiving/metrcTransferModal';
    import StrainModal from './inventory/strainModal';

    export default {
        props: {
            module: {
                type: String,
                default: 'products'
            }
        },
        
        data(){
            return {
                metrcTransferModal:false,
                strainModal:false,
                isSyncing:false
            };
        },
        
        components : {
            MetrcTransferModal,StrainModal
        },
        
        mounted() {

        },
        
        methods: {
            syncMetrcPackages(){
                this.isSyncing = true;
                axios.get('/api/v1/admin/dispensary/receivings/syncPackages').then(response =>{
                    this.isSyncing = false;
                    if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'});
                    if(response.data.count>0) this.$announcer(response);
                }).catch(error => {
                    this.isSyncing = false;
                    this.$announcer(error.response);
                });
            }
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
            
            regAgent(){
                return ((this.$store.state.user || {}).location || {}).regulatory_agent || 'none';
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
