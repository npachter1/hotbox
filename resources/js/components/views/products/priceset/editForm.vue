<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>
            
            <div v-if="item && schema" class="col-12">
                
                <div v-if="type!='grid'">
                    <h3 v-if="item.id" class="mb-4">Edit {{ item.name_grade }} Priceset</h3>
                    <h3 v-else>Add a new Priceset:</h3>
                </div>
                
                <div class="row">
                    <div v-for="(formItem,ele) in schema.form" class="float-left" :class="{[(formItem.container) ? [formItem.container] : 'col-12']: true}">
                        <div class="form-group-list">
                            <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" class="mt-4" />
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
                
              <div class="row mt-3">
              <div class="col-12 table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                  <tr>
                    <th><i class="hotbox-icon hotbox-icon-c-question" :title="'Pricing Table'" v-b-tooltip.hover="'When sale qty reaches this amount, trigger the corresponding unit price'"></i> Tier Amount (Threshold)</th>
                    <th>Price/{{item.type_uom}}</th>
                    <th><span style="float:right;">Use as Default?</span></th>
                    <th>&nbsp;</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(tier,ind) in item.amount_tiers"
                      :class="{'table-success':(ind==defaultTier) ? true : false}"
                      :key="ind">
                    <td>
                        <form-number v-model="tier.amount" :schema="{name:'amount_tiers_amount_'+ind,validation:'required|min_value:0.01',append:1,append_text:item.type_uom}" :hideLabel="true" />
                    </td>
                    <td>
                        <form-number v-model="tier.price" :schema="{name:'amount_tiers_price_'+ind,validation:'required|min_value:0.01',prepend:'hotbox-icon hotbox-icon-currency-dollar-2',append:1,append_text:'/'+item.type_uom}" :hideLabel="true" />
                    </td>
                    <td align="right"
                      @click="setTierDefault(ind,true)">
                      <span v-if="defaultTier==ind"><i class="hotbox-icon hotbox-icon-n-check"></i></span>
                      <span v-else><i class="hotbox-icon hotbox-icon-bookmark-add-2"></i></span>
                    </td>
                    <td align="right">
                      <span v-if="ind!==0"
                        @click.prevent="removeTier(ind)">
                        <i class="hotbox-icon hotbox-icon-trash-round"></i>
                      </span>
                    </td>
                  </tr>
                  </tbody>
                </table>
                <div class="col-12" style="clear:both; text-align:right;"
                  @click.prevent="addTier">
                <strong><i class="hotbox-icon hotbox-icon-c-add"></i></strong>
              </div>
              </div>
            </div>
            

                <div class="col-12 clearfix mt-3 text-center">
                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                     <a v-if="type=='form'" @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                     <a v-else-if="type=='grid'" @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                     
                    <form-button v-if="type==='grid'" :disabled="isArchived || !!(item.products.length)" @click="$emit('archive')" text="Archive" show-disabled disabled-title="Archiving Unavailable" :disabled-text="isArchived ? 'Price Set already archived.' : 'Inventory is attached to this price set.'"/>
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

    import Item from '../../../../models/Priceset';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            focus: {
                type: [String,Number],
                default: null                                                   // if we are being claled by another resource, force the category.
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
            },
            model: {
                type: String,
                default: 'Priceset'
            },
            module: {
                type: String,
                default: 'products',
            }
        },
        
        data(){
            return {
                item: null,
                itemState: 'save',
                defaultTier: 0
            };
        },
        
        components : {

        },
        
        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.item.category_ids = this.item.categories.map(v=>{return v.id;});  // map ids for form multiselect
                    this.item.product_ids = this.item.products.map(v=>{return v.id;});     // map ids for form multiselect
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.item.category_ids = [];
                this.item.product_ids = [];
                if(this.focus) this.item.category_ids.push(this.focus); // include a focused category
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
                
                if(!this.item.amount_tiers.length){
                    if(confirm) this.$announcer({status:400,data:{message:'Please add at least 1 tier first!'}});
                    return false;
                }
                
                this.itemState = 'saving..';
                this.item.save().then(async response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        
                        if(['modal','grid'].indexOf(this.type)!==-1){          // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
                            await this.$store.dispatch(this.module+'/setSchemas','inventory,receiving');
                            this.$emit('refresh',response);
                            if(this.type=='grid') this.$emit('toggle');
                        }else this.$router.push({name:this.model.toLowerCase()});
                    }
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },
            
            setTierDefault(ind,def=true){
                if(def===true){
                  this.defaultTier = ind;
                  this.item.amount_default = (this.item.amount_tiers[ind] || {}).price || 0.02;   // set default amount based on current tier selected to be default
                }
            },
            
            addTier(){
                let maxAmount = Math.max.apply(Math, this.item.amount_tiers.map(function(o) { return o.amount; }));
                let maxPrice = Math.max.apply(Math, this.item.amount_tiers.map(function(o) { return o.price; }));
                                                                     
                this.item.amount_tiers.push({'amount':(maxAmount+1),'price':(maxPrice+1)});
                if(!this.item.amount_default) this.setTierDefault(0,true);      // if we dont have  adefault price, just set first in [new] array
                this.sortTiers();
            },
              
            removeTier(ind){
                this.item.amount_tiers.splice(ind,1);
            },
            
            sortTiers(){
                this.item.amount_tiers.sort(function(a,b){                           // resort
                  if(a['amount'] < b['amount']) return -1; 
                  else return 1;
                });        
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            
            categorySel(){
                let data = this.schema.form.category_id.values.find(v=>v.id==this.item.category_id);
                return (data) ? data.name : 'this';
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
        },
        
        inject: ['$validator']
        
    };
    
</script>

<style>

</style>
