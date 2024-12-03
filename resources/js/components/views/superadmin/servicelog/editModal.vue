<template>
        <div v-if="item && schema" class="col-12">

            <div class="nav-tabs-header mb-4">
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="nav-link" :class="{'active':tab=='request'}">
                        <a href="" class="" @click.prevent="tab='request'">Request</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='response'}">
                        <a href="" class="" @click.prevent="tab='response'"><i class="fal fa-exchange"></i> Response</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='entity'}">
                        <a href="" class="" @click.prevent="tab='entity'"><i class="fal fa-key"></i> Entity Ref</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='service'}">
                        <a href="" class="" @click.prevent="tab='service'"><i class="fal fa-envelope-open"></i> Support</a>
                    </li>
                </ul>
            </div>
            
            <transition name="bo-slide">
                <div v-if="tab=='request'" class="">
                    
                    <h5>{{ item.resource_ref }}</h5>
                    <div class="json-edit">
                        <v-jsoneditor v-model="requestJson" mode="view" :modes="['code','view','tree']" @input="(val) => requestJson=val" @has-error="isError=true"></v-jsoneditor>
                    </div>

                </div>
            </transition>

            <transition name="bo-slide">
                <div v-if="tab=='response'" class="">
                    
                    <h5>{{ item.resource_ref }}</h5>
                    <div class="json-edit">
                        <v-jsoneditor v-model="responseJson" mode="view" :modes="['code','view','tree']" @input="(val) => responseJson=val" @has-error="isError=true"></v-jsoneditor>
                    </div>
            
                </div>
            </transition>

            <transition name="bo-slide">
                <div v-if="tab=='entity'" class="">
                    
                    
                    
                     <h5 class="text-center">Feature Coming Soon..</h5>
            
                </div>
            </transition>
            
            <transition name="bo-slide">
                <div v-if="tab=='service'" class="">
                    
                    
                    
                     <h5 class="text-center">Email API Trouble Ticket Feature Coming Soon..</h5>
            
                </div>
            </transition>

        </div>
        <div class="no-content-block" v-else>
            <loading :display="(item && schema) ? false : true" type="loadModal" />
        </div>
</template>

<script>
    import VJsoneditor from '../../../../../plugins/jsoneditor/vue-json-editor';
    import Item from '../../../../models/Servicelog';
    import _ from 'lodash';
    export default {
        props: {
            model: {
                type: String,
                default: 'Servicelog'
            },
            module: {
                type: String,
                default: 'superadmin',
            },
            id: {
                type: [Number,String],
                default: ''
            },
            view: {
                type: String,
                default: 'request'
            }
        },
        
        data(){
            return {
                item: null,
                tab: this.view,
                isLoading: false,
                isError: false,
                isModified:false,
                isProcessing:false,
                itemState: 'save',
                requestJson: null,
                responseJson: null,
                errorJson: null
            };
        },
        
        components : {
            VJsoneditor
        },
        
        mounted() {
            this.isLoading = true;
            Item.find(this.id).then(response => {
                this.schema = response.schema;
                this.item = new Item(response).withDefaults(this.schema);
                this.requestJson = (this.item.parameters) ? (JSON.parse(this.item.parameters) || this.item.parameters) : null;
                this.responseJson = (this.item.response) ? (JSON.parse(this.item.response) || this.item.response) : null;
                this.errorJson = (this.item.error) ? (JSON.parse(this.item.error) || this.item.error) : null;
                
                this.isLoading = false;
            }).catch(error => {
                this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                this.$emit('refresh');
            });
        },
        
        methods: {
        },
        
        computed: {
        },
        
        watch: {
        }
        
    };
    
</script>

<style>
</style>
