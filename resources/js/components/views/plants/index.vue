<template>
    <div class="row gutters">
        <div v-if="section" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card top-blue-bdr">
                <div class="card-header">
                    <div v-if="indexView" class="">
                        <a href="" v-if="indexView.with_add && !isEditPage" @click.prevent="viewCreatePlantingsModal()" class="float-right mt-0 mb-3 mr-4 router-link-exact-active active"><span class="btn-label"><i class="hotbox-icon hotbox-icon-e-add"></i></span> New Plant Batch</a>
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
                        <router-view ref="grid" v-if="$store.getters.userCan(indexView.can_view)"></router-view>
                    </div>

                    <hr>
                    <div v-if="" class="stats">
                        <router-link v-if="!isEditPage" :to="{name:'location_index'}" tag="a" class=""><i class="ti-angle-left"></i> Back to Dashboard</router-link>
                        <router-link v-else-if="indexView" :to="{name: indexView.name}" tag="a" class="head-link"><i class="ti-angle-left"></i> Back to {{ indexView.title || indexView.tabname }} Index</router-link>
                    </div>
                </div>
            </div>

            <b-modal centered ref="createPlantingsModal"
            v-model="createPlantingsModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="createPlantingsModal=!createPlantingsModal"></i>
              <h5 class="w-100 mb-0 text-center">Create Plantings</h5>
            </template>
          
              <create-plantings-modal v-if="createPlantingsModal"
                @refresh="refreshFromModal()">
              </create-plantings-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="createPlantingsModal=!createPlantingsModal">Close</span>
            </template>
        </b-modal>

        </div>
        <div v-else class="col-12 text-center mt-4">

        </div>
    </div>
</template>


<script>

    import CreatePlantingsModal from './plant/createPlantingsModal';

    export default {
        props: {
            module: {
                type: String,
                default: 'plants'
            }
        },
        
        data(){
            return {
                createPlantingsModal:false,

            };
        },
        
        components : {
            CreatePlantingsModal,

        },
        
        mounted() {

        },
        
        methods: {
            viewCreatePlantingsModal(){
                this.createPlantingsModal=!this.createPlantingsModal;
            },
            refreshFromModal(){
                this.createPlantingsModal=!this.createPlantingsModal;
                this.$refs.grid.fetchGrid();
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
