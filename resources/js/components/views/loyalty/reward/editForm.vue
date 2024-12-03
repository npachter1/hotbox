<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>
            
            <div v-if="item && schema" class="col-12">
                
                <h3 v-if="item.id" class="mb-4">Edit {{ item.type | ucwords }} Reward Entry<br><span class="small">for {{ item.descriptor }}</span></h3>
                <h3 v-else>Add a new Reward:</h3>
                
                <span class="description" v-if="item.created_at">
                   - Created on: {{ item.created_at | localDate }}<br>
                </span>
                <span class="description" v-if="item.customer">
                   - Customer: {{ item.customer.first_name }} {{ item.customer.last_name }}<br>
                </span>
                <span class="description" v-if="item.sale">
                   - From (Sale): <router-link :to="{name:'sale_edit',params:{id:item.sale.id}}" tag="a" class="">{{ item.sale.order_number }}</router-link><br>
                </span>

                <div class="row mt-3">
                    <div v-for="(formItem,ele) in schema.form" class="float-left" :class="{[(formItem.container) ? [formItem.container] : 'col-12']: true}">
                        <div class="form-group-list">
                            <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" />
                            <form-text v-if="formItem.type=='text'" v-model="item[ele]" :schema="formItem" />
                            <form-number v-if="formItem.type=='number'" v-model="item[ele]" :schema="formItem" />
                            <form-textarea v-if="formItem.type=='textarea'" v-model="item[ele]" :schema="formItem" :rows="formItem.rows || 3" />
                            <form-select v-if="formItem.type=='select'" v-model="item[ele]" :schema="formItem" :scopeData="item.country" />
                            <form-simpleselect v-if="formItem.type=='simpleselect'" v-model="item[ele]" :schema="formItem" :isDisabled="(item.id && ele=='type') ? true : false" />
                            <form-multiselect v-if="formItem.type=='multiselect'" v-model="item[ele]" :schema="formItem" />
                            <form-datetime v-if="formItem.type=='datetime'" v-model="item[ele]" :schema="formItem" />
                            <form-file v-if="formItem.type=='image'" v-model="item[ele]" :schema="formItem" type="image" :resource="model.toLowerCase()" :view="formItem.view || 'profile'" @input="(upd) => {item[ele] = upd}" />
                            <form-simpleobject v-if="formItem.type=='simpleobject'" v-model="item[ele]" :formData="item" :schema="formItem" />
                            
                            
                        </div>
                    </div>
                </div>

                    <transition name="bo-slide">
                        <div v-if="itemState!='save'" class="block-announce info mt-3 mb-3">
                            <p class="title"><i class="hotbox-icon hotbox-icon-presentation"></i> About Updating A Reward Transaction..</p>
                            <p>This will immediately update the customers total reward points balance, and be flagged as adjusted.</p>
                        </div>
                    </transition>

                <div class="col-12 clearfix mt-3 text-center">
                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"/>
                    <a v-if="type!=='form'" @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                    <a v-if="type==='form'" @click.default="$router.go(-1)" class="btn btn-md btn-light">Return.</a>
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

    import Item from '../../../../models/Reward';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Reward'
            },
            module: {
                type: String,
                default: 'loyalty',
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
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
        
        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
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
                this.item.save().then(response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        if (this.type==='form') {
                            this.$router.push({name:this.model.toLowerCase()});
                        } else {
                            this.$emit('refresh',response);
                            this.$emit('toggle');
                        }
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
