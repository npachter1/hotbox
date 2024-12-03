<template>
    <div v-if="schema && batch" class="col-12">
    <form class="modal-form">
        <fieldset>
    
            <div v-if="batch && schema" class="col-12">

            <div class="nav-tabs-header mb-4">
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="nav-link" :class="{'active':tab=='edit'}">
                        <a href="" class="" @click.prevent="tab='edit'">Edit Batch</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='audit'}">
                        <a href="" class="" @click.prevent="tab='audit'">Audit Check</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='promote'}">
                        <a href="" class="" @click.prevent="tab='promote'">Promote (Campaign)</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='po'}">
                        <a href="" class="" @click.prevent="tab='po'">Generate PO</a>
                    </li>
                </ul>
            </div>
            
            <transition name="bo-slide">
                <div v-if="tab=='edit'">
                    
                    <table role="table" class="table b-table">
                        <thead>
                            <tr>
                                <th valign="top">Item</th>
                                <th v-for="(fsch,fkey) in batchFields">
                                    <span class="small">Override {{ fsch.title }}</span> 
                                    <i class="float-right" @click="batchSort(fkey)" :class="{'fas fa-sort show-opaque':batchSortBy!=fkey,'fas fa-sort-amount-down':(batchSortBy==fkey && batchSortDesc===false),'fas fa-sort-amount-up':(batchSortBy==fkey && batchSortDesc===true)}"></i><br>
                                    <form-boolean v-if="fsch.type=='boolean'" :declared="batchFields[fkey].override" :schema="fsch" :skipValidation="true" @input="(upd) => {batchFields[fkey].override = upd; batchOverride(fkey,upd); }" :hideLabel="true" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in batch">
                                <td>{{ item.item_barcode }}<br><span class="small">{{ item.item_name }}</span></td>
                                <td v-for="(fsch,fkey) in batchFields">
                                    <form-boolean v-if="fsch.type=='boolean'" :declared="item[fkey]" :schema="fsch" @input="(upd) => {item[fkey] = upd}" :hideLabel="true" />
                                    <form-text v-if="fsch.type=='text'" v-model="item[fkey]" :schema="fsch" :hideLabel="true" />
                                    <form-number v-if="fsch.type=='number'" v-model="item[fkey]" :schema="fsch" :hideLabel="true" />
                                    <form-simpleselect v-if="fsch.type=='select'" v-model="item[fkey]" :schema="fsch"  :hideLabel="true" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="col-12 clearfix mt-2 text-center">
                        <auto-save type="save" :state="batchState" @autoSave="saveBatchData('data')"></auto-save>
                         <a @click.default="getBatchData()" class="btn btn-md btn-light" :class="{'btn-light':batchState=='update','btn-warning':batchState!='update'}">Restore.</a>
                    </div>

                </div>
            </transition>

            <transition name="bo-slide">
                <div v-if="tab=='audit'">
                    
                    <div class="block-announce info mt-4 mb-4">
                        <p class="title"><i class="hotbox-icon hotbox-icon-s-check"></i> Are you Sure?..</p>
                        <p>This will mark the following items as checked for accuracy by you.</p>
                    </div>
                    
                    <table role="table" class="table b-table table-striped">
                        <thead>
                            <tr>
                                <th width="80%">Item</th>
                                <th width="10%">Stock</th>
                                <th width="10%">Confirm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in batch" :class="{'table-success':inSet(item.id,batch_ids)}">
                                <td width="80%">{{ item.item_barcode }}<br><span class="small">{{ item.item_name }}</span></td>
                                <td width="10%" valign="top">
                                    <b>{{ item.quantity_on_hand }}/{{ item.unit_of_measure }}</b>
                                </td>
                                <td width="10%" valign="top">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" :checked="inSet(item.id,batch_ids)" :disabled="false" @click="toggleBatchId(item.id,$event)"><span class="custom-control-indicator"></span> 
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="col-12 clearfix mt-2 text-center">
                        <auto-save type="save" :state="batchState" @autoSave="saveBatchData('list')"></auto-save>
                         <a @click.default="$emit('refresh')" class="btn btn-md btn-light" :class="{'btn-light':batchState=='update','btn-warning':batchState!='update'}">Cancel.</a>
                    </div>

                </div>
            </transition>
            
                <transition name="bo-slide">
                    <generate-campaign
                            v-if="tab=='promote'"
                            :batch="inventoryToProducts"
                            :batch-state="batchState"
                            :batch_ids="batchProductIds"
                            @state="batchState=$event"
                            @toggle="toggleBatchProductId($event.id, $event.event)"
                            @refresh="$emit('refresh')"></generate-campaign>
                </transition>

                <transition name="bo-slide">
                    <generate-po
                            v-if="tab=='po'"
                            :batch="inventoryToProducts"
                            :batch-state="batchState"
                            :batch_ids="batchProductIds"
                            @state="batchState=$event"
                            @toggle="toggleBatchProductId($event.id, $event.event)"
                            @refresh="$emit('refresh')"></generate-po>
                </transition>
            </div>
            <div class="no-content-block" v-else>
                <loading :display="true" type="loadModal" />
            </div>

        </fieldset>
    </form>
    </div>
    <div class="no-content-block" v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>
    import GeneratePo from "../../../elements/GeneratePo";
    import GenerateCampaign from "../../../elements/GenerateCampaign";
    import merge from "lodash/merge";
    import unionBy from "lodash/unionBy";
    import union from "lodash/union";

    export default {
        props: {
            module: {
                type: String,
                default: 'products',
            },
            model: {
                type: String,
                default: 'Inventory'
            },
            schema: {
              type: Object,
              default: () => {}
            },
            ids: {
                type: Array,
                default: () => {}
            },
            focus: {
                type: String,
                default: 'edit'
            },
        },

        data(){
            return {
                tab: this.focus,
                batch: null,
                batch_ids: this.ids,
                batchSortBy:null,
                batchSortDesc:false,
                batchState: 'update',
                isLoading: false
            };
        },
        
        components : {
            GeneratePo,
            GenerateCampaign,
        },

        mounted() {
            this.getBatchData();
        },
        
        methods: {
            getBatchData(){
                this.isLoading = true;
                axios.get('/api/v1/'+this.schema.meta.resource+'/batch?batch_ids='+this.batch_ids.join(',')).then(response =>  {
                    this.batch = this.affixPoProperty(response.data);
                    this.isLoading = false;
        	    }).catch(error => {
        	        this.isLoading = false;
                    this.$announcer(error.response);
                    this.$emit('refresh');
        	    });
            },
            
            saveBatchData(typ){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.batchState = 'saving..';
                        let up = null;                                          // reduce properties of batch data that arent batchable - need not send more then 1k params for large batches
                        if(typ=='data') up = this.batch.map(r => Object.keys(r).reduce((o, k) => (Object.keys(this.batchFields).indexOf(k)!==-1 || k=='id') ? { ...o, [k]: r[k]} : o, {}));
                        else up = this.batch_ids;
                        axios.post('/api/v1/'+this.schema.meta.resource+'/batch/'+this.tab,{batch:up}).then(response =>  {
                            if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'}); // update schems, will update grid
                            this.batchState = 'saved';
                            this.$announcer(response);
                            this.$emit('refresh');
                        }).catch(error => {
                            this.batchState = '(re)update';
                            this.$announcer(error.response);
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },
            
            batchSort(field) {
                this.batchSortBy=field;
                this.batchSortDesc = !this.batchSortDesc;
                this.sorter(this.batch,this.batchSortDesc,this.batchSortBy);    // resort batch
            },
            
            batchOverride(field,val){
                this.batch = this.batch.map(v => {v[field] = val; return v;});
            },

            toggleBatchId(val,e){
                if(e.target.checked){
                    if(this.batch_ids.indexOf(val) === -1) this.batch_ids.push(val);
                }else this.batch_ids.splice(this.batch_ids.indexOf(val), 1);
            },

            toggleBatchProductId(val,event) {
                if (Array.isArray(val)) { //array of product Ids
                    val.forEach(productId=>{
                        this.batch.filter(inv=>inv.product.id==productId).forEach(inv=>{
                            this.toggleBatchId(inv.id, event);
                        });
                    });
                } else { //one product Id
                    this.batch.filter(inv=>inv.product.id==val).forEach(inv=>{
                        this.toggleBatchId(inv.id, event);
                    });
                }
            },

            affixPoProperty(data) {
                return data.map(inv=> {
                    let e = inv.product;

                    let vendor_name;
                    let can_generate_po = false;

                    if (e?.last_received?.vendor?.name) {
                        vendor_name = e.last_received.vendor.name;
                        can_generate_po = true
                    } else if (e?.inv_meta?.vendor) {
                        vendor_name = e.inv_meta.vendor;
                    } else if (e?.last_received?.addressbook_id) {
                        vendor_name = `Unknown (Address Book ID: ${e.last_received.addressbook_id})`;
                    } else {
                        vendor_name = 'Unknown';
                    }

                    return merge(inv, {
                        product: {
                            po: {
                                addressbook_id: e.last_received.addressbook_id,
                                quantity_requested: 10,
                                product_id: e.id,
                                vendor_name,
                                can_generate_po
                            }
                        }
                    })
                });
            },

        },
        
        computed: {
            batchFields(){
                return Object.keys(this.schema.form).filter(k => this.schema.form[k].batchable===true).reduce((obj, key) => { obj[key] = this.schema.form[key]; obj[key].override = null; return obj; }, {});
            },

            inventoryToProducts() {
                let productBatch = this.batch.map(e=>e.product);
                let productUniqueBatch = unionBy(productBatch, e=>e.id); //remove dupe products based on product id
                return productUniqueBatch;
            },

            batchProductIds() {
                return union(this.batch.filter(e=>this.batch_ids.includes(e.id)).map(e=>e.product.id));
            }
        },
        
        watch: {
            batch:{
                handler(newVal,oldVal){
                    this.batchState = (oldVal) ? 'update changes' : 'update';
                },
                deep: true
            },

            batch_ids(){
                this.$emit('update:ids',this.batch_ids);                        // update ids list past to this component
            }
        }
        
    };
    
</script>

<style>

</style>
