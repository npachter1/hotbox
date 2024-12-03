<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>
            
            <div v-if="item && schema" class="col-12">
                
                <h3 v-if="item.id" class="mb-4">Edit Taxcategory Entry<br><span class="small">for {{ item.name }}</span></h3>
                <h3 v-else>Add a new Taxcategory:</h3>

                <div class="row">
                    <div v-for="(formItem,ele) in schema.form" class="float-left" :class="{[(formItem.container) ? [formItem.container] : 'col-12']: true}">
                        <div class="form-group-list">
                            
                            <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" />
                            <form-text v-if="formItem.type=='text'" v-model="item[ele]" :schema="formItem" />
                            <form-number v-if="formItem.type=='number'" v-model="item[ele]" :schema="formItem" />
                            <form-textarea v-if="formItem.type=='textarea'" v-model="item[ele]" :schema="formItem" :rows="formItem.rows || 3" />
                            <form-select v-if="formItem.type=='select'" v-model="item[ele]" :schema="formItem" :scopeData="item.country" @change="$emit('change')"/>
                            <form-simpleselect v-if="formItem.type=='simpleselect'" v-model="item[ele]" :schema="formItem" :isDisabled="(item.id && ele=='type') ? true : false" @change="$emit('change')"/>
                            <form-multiselect v-if="formItem.type=='multiselect'" v-model="item[ele]" :schema="formItem" @change="$emit('change')"/>
                            <form-datetime v-if="formItem.type=='datetime'" v-model="item[ele]" :schema="formItem" @change="$emit('change')"/>
                            <form-file v-if="formItem.type=='image'" v-model="item[ele]" :schema="formItem" type="image" :resource="model.toLowerCase()" :view="formItem.view || 'profile'" @input="(upd) => {item[ele] = upd}" />
                            <form-simpleobject v-if="formItem.type=='simpleobject'" v-model="item[ele]" :formData="item" :schema="formItem" @change="$emit('change')"/>
                            
                            <a v-if="ele=='rate_ids'" href="" class="float-right" @click.prevent="newTaxRate(0)"><i class="hotbox-icon hotbox-icon-e-add"></i> Add New rate</a>
                            
                        </div>
                    </div>
                </div>

                <div class="col-12 clearfix mt-3 text-center">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                        <a v-if="type!=='form'" @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                        <a v-if="type==='form'" @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                </div>

            </div>

        </fieldset>
    </form>
    
    
        <b-modal centered ref="taxRateModal"
            v-model="taxRateModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="taxRateModal=!taxRateModal"></i>
              <h5 class="w-100 mb-0 text-center">Tax Rates</h5>
            </template>
          
              <tax-rate-modal v-if="taxRateModal"
                :id="taxRateEditId"
                @refresh="addTaxRate">
              </tax-rate-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="taxRateModal=!taxRateModal">Close</span>
            </template>
        </b-modal>

    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Tax';
    import TaxRateModal from './taxRateModal';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Tax'
            },
            module: {
                type: String,
                default: 'administration',
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
            }
        },
        
        data(){
            return {
                item: null,
                itemState: 'save',
                taxRateModal:false,
                taxRateEditId:null
            };
        },
        
        components : {
            TaxRateModal
        },
        
        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.item.rate_ids = this.item.rates.map(v=>{return v.id;});     // map rate_ids for form multiselect
                    this.$emit('loaded');
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.isLoading = false;
            }
        },
        
        methods: {
            autoSave(confirm=false){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) _.debounce(() => { this._save(); },2000)();
                        else this._save(true);
                    }else if(confirm==true){
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    }else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
            },
            
            _save(confirm=false){
                this.itemState = 'saving..';
                delete this.item.rates; // reduce unneeded form fields (was mapped and binded to rate_ids)
                this.item.save().then(async response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        //this.$router.push({name:this.model.toLowerCase(),params:{filters:{location_id:[this.item.location_id]}}});
                        if(this.type=='modal'){                                 // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
                            await this.$store.dispatch(this.module+'/setSchemas','campaign,group');
                            this.$emit('refresh',response);
                            this.$emit('toggle');
                        }else this.$router.push({name:this.model.toLowerCase()});
                    }
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },
            
            newTaxRate(id){
                this.taxRateEditId = id;
                this.taxRateModal=!this.taxRateModal;
            },
            
            addTaxRate(upd){
                this.item.rates.push(upd);
                this.item.rate_ids.push(upd.id);
                this.taxRateModal=!this.taxRateModal;
            }
            
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            }
        }
        
    };
    
</script>

<style>

</style>
