<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>
            
            <div v-if="item && schema" class="col-12">
                <a v-if="needsPayment" href="" class="btn btn-md btn-round btn-warning float-right" @click.prevent="promptPayment(item.payments[0].id,item.payments[0].amount_owed,item.order_number)">Make Payment</a>
                <a v-if="item.status=='settled'" href="" class="btn btn-md btn-round btn-warning float-right" @click.prevent="viewEditModal(item.id)">Modify Sale</a> 
                <a href="" class="btn btn-md btn-round btn-light float-right" @click.prevent="downloadExportFile(item.id,'pdf')">Print Receipt</a>
                <h3 v-if="item.id" class="mb-4"><i class="hotbox-icon hotbox-icon-c-info"></i> {{ item.order_number }} Information</h3>

                <span class="description" v-if="1==1">
                   - Total: ${{ item.sale_price | dollar }}<br>
                </span>
                <span class="description" v-if="needsPayment" style="color:var(--danger);">
                   - Amount Owed: ${{ item.payments[0].amount_owed | dollar }}<br>
                </span>
                <span class="description" v-if="item.settled_at">
                   - Settled on: {{ item.settled_at | localDate }}<br>
                </span>
                <span class="description" v-if="!item.settled_at && item.created_at">
                   - Created on: {{ item.created_at | localDate }}<br>
                </span>
                <span class="description" v-if="1==1">
                   - Metrc Receipt: {{ item.metrc_receipt }}<br>
                </span>
                <span class="description" v-if="item.customer">
                   - Customer: {{ item.customer.first_name }} {{ item.customer.last_name }}<br>
                </span>
                <span class="description" v-if="item.user">
                   - Sold By: {{ item.user.name }}<br>
                </span>


                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>


                <h5>{{ (item.status=='refunded') ? 'Refunded' : 'Ordered' }} items</h5>

                <b-table v-if="item.items" striped hover       
                    :id="model.toLowerCase()+'_items_table'"
                    :items="item.items || []"
                    :fields="schema.form.items.fields"
                    :busy.sync="isLoading"
                    :show-empty="true"
                    :sort-by.sync="itemsSortBy"
                    :sort-desc.sync="itemsOrderDesc"
                    :no-local-sorting="true"
                    :no-local-filtering="true"
                    :per-page="50"
                    primary-key="id"
                    responsive="md"
                    stacked="sm"
                    @sort-changed="reSortItems"
                >
                              
                <template v-slot:cell(inventory)="row">
                    <span v-if="row.item.inventory">
                        <img :src="getProductImageUrl(row.item.inventory)" class="responsive float-left mr-2" width="65">
                        {{ row.item.inventory.item_barcode }}<br><span v-if="row.item.inventory.product" class="small">{{ row.item.inventory.product.name }}</span>
                        <div class="description small">Strain: {{ row.item.inventory.item_strain }}</div>
                    </span>
                </template>

                <template v-slot:cell(quantity)="row">
                    {{ row.value | dollar(4) }}<span v-if="row.item.inventory" class="small">/{{ row.item.inventory.unit_of_measure }}</span>
                </template>

                <template v-slot:cell(price)="row">
                    ${{ row.value | dollar }}
                </template>

                <template v-slot:cell(discount)="row">
                    ${{ row.value | dollar }}
                </template>

                <template v-slot:cell(tax)="row">
                    ${{ row.value | dollar }}
                </template>

                <template v-slot:cell(sale_price)="row">
                    ${{ row.value | dollar }}
                </template>

                <template v-slot:table-caption>
                    Order Total Spent: ${{ item.sale_price | dollar }}<br>
                    Transactions: 
                </template>
                                
                <template v-slot:empty>
                    <div v-if="!isLoading">
                        <img src="/images/logo.png" alt="No Results" class="" width="115" />
                        <h4>Hmm, There are currently no Results.</h4>
                    </div><div v-else class="h-100">&nbsp;</div>
                </template>
                
            </b-table>

                <div class="col-12 clearfix mt-3 text-center">
                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"/>
                    <a v-if="type!=='form'" @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                    <a v-if="type==='form'" @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                </div>

            </div>

        </fieldset>
    </form>


        <b-modal centered ref="editModal"
            v-model="editModal"
            no-enforce-focus
            size="xl"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="editModal=!editModal"></i>
              <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-tag-line"></i> Modify/Return a Sale</h5>
            </template>
          
              <modify-modal v-if="editModal"
                :id="editModalId"
                @refresh="fromModify"
                @close="editModal=false">
              </modify-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="editModal=!editModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="paymentModal"
                 v-if="needsPayment"
                 v-model="paymentModal"
                 size="md"
                 :no-enforce-focus="true"
                 header-bg-variant="light"
                 header-text-variant="primary">

            <template slot="modal-header">
                <i class="modal-top-close fal ti-close" @click="paymentModal=!paymentModal"></i>
                <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-currency-dollar"></i> Record Account Payment</h5>
            </template>

            <div class="col-12">
                <loading :display="(isLoading) ? true : false" type="loadModal" />
                <h5 class="w-100 text-center">
                    <span class="small">For: {{ customer.first_name}} - {{ paymentOrder }}</span>
                </h5>

                <form-number v-model="paymentAmount" :schema="schema.form.account_payment" class="col-sm-12 mt-3 mb-2" />

                <div class="col-12 mt-2 mb-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-info btn-round" @click="payAccount">Record Payment</button>
                </div>
            </div>

            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="paymentModal=!paymentModal">Close</span>
            </template>
        </b-modal>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>
    import Item from '../../../../models/Sale';
    import ModifyModal from './modifyModal';
    import _ from 'lodash';
    import Customer from "../../../../models/Customer";

    export default {
        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Sale'
            },
            module: {
                type: String,
                default: 'pos',
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
                isLoading:false,
                itemsSortBy:'quantity',
                itemsOrderDesc:true,
                editModal:false,
                editModalId:0,
                paymentId:null,
                paymentAmount:null,
                paymentOrder:null,
                paymentModal:false,
                customer: {},
            };
        },
        
        components : {
            ModifyModal
        },
        
        mounted() {
            this._loadSale();
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
            },
            
            reSortItems(ctx){
                if(!this.item.items) return false;
                this.sorter(this.item.items,ctx.sortDesc,ctx.sortBy);
            },
            
            viewEditModal(id){
                this.editModalId = id;
                this.editModal=!this.editModal;
            },

            fromModify(upd,typ='sale'){
                _loadSale();
                this.editModal = !this.editModal;
            },

            _loadSale(){
                this.isLoading = true;
                if(this.id){
                    Item.find(this.id).then(response => {
                        this.item = new Item(response).withDefaults(this.schema);
                        this._loadCustomer();
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

            _loadCustomer(){
                if (this.needsPayment && this.item.customer_id) {
                    Customer
                        .find(this.item.customer_id)
                        .then(response=>{
                            this.customer = new Customer(response);
                        })
                        .catch(error=>{
                            this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                        });
                }
            },

            downloadExportFile(id,typ){
                this.isDownloading = true;
                axios.get('/api/v1/'+this.schema.meta.resource+'/'+id+'/export/'+typ,{responseType: 'arraybuffer'}).then(response =>  {
                    this.isDownloading = false;
                    this.downloadFile(response);
        	    }).catch(error => {
        	        this.isDownloading = false;
                    this.$announcer(error.response);
        	    });              
            },

            promptPayment(id,amt,title='misc'){
                this.paymentId = id;
                this.paymentAmount = amt;
                this.paymentOrder = title;
                this.paymentModal=true;
            },

            async payAccount(){

                let withPin = await this.requirePin('Please Enter an Admin PIN to record an account payment');
                if(withPin===false) return false;                                     // an adminpin couldnt be validated HINT add error message here if desired.

                this.isLoading = true;
                axios.post('/api/v1/admin/dispensary/sales/' + this.paymentId + '/account',{amount:this.paymentAmount}).then(response => {
                    this.$announcer({status:200,data:{message:'An Account Payment of $'+this.paymentAmount+ ' has been Posted.'}});
                    this.isProcessing = false;
                    this.paymentModal=false;
                    this._loadSale();
                    this.isLoading = false;

                }).catch(error => {
                    this.$announcer(error.response);
                    this.isLoading = false;
                });
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },

            needsPayment() {
                return this.item && Array.isArray(this.item.payments) && this.item.payments.length>0 && (this.item.payments||[{}])[0].payment_method==='account' && (this.item.payments||[{}])[0].amount_owed>0
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
