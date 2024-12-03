<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>
            
            <div class="row">
                <h3 v-if="item.id" class="mb-4 w-100">
                    <div class="float-right small">Active?<form-boolean :declared="item.is_active" :schema="schema.form.is_active" :hideLabel="true" @input="(upd) => {item.is_active = upd}" class="mt-2 ml-1 float-right" /></div>
                    <span v-if="item.scheduled_at && $moment(item.scheduled_at)>$moment()" class="d-block float-right show-red small mr-2"><b>Scheduled to start on {{ item.scheduled_at | localDate }}</b></span>
                    Edit Discount Rule<br><span class="small">for {{ item.name }}</span></h3>
                <h3 v-else>Add a new Discount:</h3>

                <form-text v-model="item.name" :schema="schema.form.name" class="col-12" />
                
                <form-text v-model="item.descriptor" :schema="schema.form.descriptor" class="col-12 col-sm-6 clearfix mt-2" />
                <form-text v-model="item.discount_code" :schema="schema.form.discount_code" class="col-12 col-sm-6 mt-2" />

                <div class="col-12 col-sm-6 mt-2">
                  <label for="rule-discount_amount">Discount (
                    <a href="" :class="{'strong':item.discount_type=='amt'}" @click.prevent="item.discount_type='amt'">Amount</a> | 
                    <a href="" :class="{'strong':item.discount_type=='pct'}" @click.prevent="item.discount_type='pct'">Percent</a>)
                  </label>
                  <form-number v-model="item.discount_amount" 
                    :schema="Object.assign({},schema.form.discount_amount,{prepend:(item.discount_type=='pct') ? 'hotbox-icon hotbox-icon-percentage-38' : 'hotbox-icon hotbox-icon-currency-dollar'})" 
                    :hideLabel="true" />
                </div>
                
                <form-datetime v-model="item.scheduled_at" :schema="schema.form.scheduled_at" @change="$emit('change')" class="col-12 col-sm-6 mt-2" />
                
                <h5 class="w-100 mt-3 mb-2 mx-2 clearfix">Settings</h5>
                <div class="col-12 col-sm-6 clearfix">
                    
                    <form-number v-model="item.rank" :schema="schema.form.rank" class="mt-1" />
                    <form-number v-model="item.max_per_customer" :schema="schema.form.max_per_customer" class="mt-1" />
                    <form-simpleselect v-model="item.distribution_type" :schema="schema.form.distribution_type" class="mt-1" />
                    <form-boolean :declared="item.is_exclusive" :schema="schema.form.is_exclusive" @input="(upd) => {item.is_exclusive = upd}" class="mt-3" />

                </div>
                <div class="col-12 col-sm-6">
                    
                    <form-simpleselect v-model="item.type" :schema="schema.form.type" class="mt-1" />
                    <form-simpleobject v-model="item.settings" :formData="item" :schema="schema.form.settings" :focus="item.type" @change="$emit('change')" class="mt-2 small" />
                    
                </div>
                
                <h5 class="w-100 mt-3 mb-2 mx-3 clearfix">
                    <i class="hotbox-icon hotbox-icon-c-question" :title="'Discount Rule Associations'" v-b-tooltip.hover="'Below, you may link this discount to specific locations, customer groups, or product categories or leave blank to apply to all'"></i>
                     Associations
                </h5>
                
                <form-multiselect v-model="item.location_ids" :schema="schema.form.location_ids" class="col-12 col-sm-12 mt-1" @change="$emit('change')"/>
                <form-multiselect v-if="type=='form'" v-model="item.group_ids" :schema="schema.form.group_ids" class="col-12 col-sm-12 mt-1" @change="$emit('change')"/>
                <form-multiselect v-model="item.category_ids" :schema="schema.form.category_ids" :locationScope="item.location_ids" class="col-12 col-sm-12 mt-1" @change="$emit('change')"/>
                <form-multiselect v-model="item.priceset_ids" :schema="schema.form.priceset_ids" :locationScope="item.location_ids" class="col-12 col-sm-12 mt-1" @change="$emit('change')"/>
                <form-multiselect v-model="item.product_ids" :schema="schema.form.product_ids" :locationScope="item.location_ids" class="col-12 col-sm-12 mt-1" @change="$emit('change')"/>


                <div class="col-12 clearfix mt-3 mb-2 text-center">
                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                    <a v-if="type!='form'" @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                     <a v-if="type=='form'" @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                    <form-button :disabled="isArchived || item.sales.length>0 || campaignsCount>0" @click="$emit('archive')" text="Archive" show-disabled disabled-title="Archiving Unavailable" :disabled-text="isArchived ? 'Rule Already Archived.' : 'You cannot archive if there are sales/campaigns associated with the rule.'"/>
                </div>

            </div>

        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Discount';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Discount'
            },
            module: {
                type: String,
                default: 'loyalty',
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
            },
            campaignsCount: {
                type: Number,
                default: 0
            }
        },
        
        data(){
            return {
                item: null,
                itemState: 'save'
            };
        },
        
        components : {

        },
        
        async mounted() {
            this.isLoading = true;
            await this.$store.dispatch(this.module+'/setSchemas','discount'); // this is a sub resource - it loads its own schema upon modal load
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.item.group_ids = this.item.groups.map(v=>{return v.id;});     // map ids for form multiselect
                    this.item.category_ids = this.item.categories.map(v=>{return v.id;});     // map ids for form multiselect
                    this.item.product_ids = this.item.products.map(v=>{return v.id;});     // map ids for form multiselect
                    this.item.priceset_ids = this.item.pricesets.map(v=>{return v.id;});     // map ids for form multiselect
                    this.item.location_ids = this.item.locations.map(v=>{return v.id;});     // map ids for form multiselect
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
                this.item.save().then(async response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        
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
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },

            isArchived() {
                return !!(this.item.archived_at);
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
  .strong{
    font-weight:700;
  }
</style>
