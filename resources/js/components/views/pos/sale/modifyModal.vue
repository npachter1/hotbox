<template>
    <div v-if="item && schema" class="col-12">
        <div v-if="$store.state['pos']['drawer']">    
            
            <form>
                <fieldset>
    
                <h5>{{ item.order_number }}</h5>
                <span class="description" v-if="1==1">
                   - Total: ${{ item.sale_price | dollar }} | Sold By: {{(item.user||{}).name }}<br>
                </span>
                <span class="description" v-if="item.customer">
                   - Customer: {{ item.customer.alias }} {{ item.customer.first_name }} {{ item.customer.last_name }}
                </span>

                <h5 class="mt-3">Items to Return:</h5>
                  <div class="mt-3 mb-4">
                    <table class="table table-striped table-responsive table-nested">
                      <thead>
                        <tr>
                          <th width="35%">Inventory</th>
                          <th width="10%">Qty</th>
                          <th width="25%">Offer</th>
                          <th width="20%" align="right">Return</th>
                          <th width="5%" align="right">Restock?</th>
                        </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(itm,iid) in item.items" :key="itm.id" :class="{'table-primary':itm.quantity_returning && itm.quantity_returning<=itm.quantity,'table-danger':itm.quantity_returning>itm.quantity}">
                            <td width="35%">
                                <span v-if="itm.inventory">
                                    <img :src="getProductImageUrl(itm.inventory)" class="responsive float-left mr-2" width="65">
                                    {{ itm.inventory.item_barcode }}<br><span v-if="itm.inventory.product" class="small">{{ itm.inventory.product.name }}</span>
                                </span>
                            </td>
                            <td width="10%" cellpadding="3">{{ itm.quantity | dollar(4) }}<span v-if="itm.inventory" class="small">/{{ itm.inventory.unit_of_measure }}</span></td>
                            <td width="30%">
                                List: ${{ itm.price | dollar }}<br>
                                Tax: ${{ itm.tax | dollar }}<br>
                                Sale: <b>${{ itm.sale_price | dollar }}</b>
                            </td>
                            <td width="10%" align="right">
                                <form-number v-if="!hasRefund" v-model="itm.quantity_returning" :schema="{name:'quantity_refunding_'+iid}" :min="0" :max="itm.quantity" @input="_calcReturn" :hideLabel="true" />
                                <div v-else>0</div>
                            </td>
                            <td width="5%" align="right">
                                <label v-if="itm.quantity_returning" class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" v-model="itm.is_restock"><span class="custom-control-indicator"></span> 
                                </label>
                            </td>
                          </tr>
                        </tbody>
                    </table>
                  </div>
    
                <h5 class="w-100 mt-4 mb-3">Refund Amount:</h5>
                  <div class="mt-1 mb-4">
                    <table class="table table-striped table-hover table-responsive">
                        <tbody>
                          <tr>
                            <td width="80%" align="right">Refund from Items being returned:</td>
                            <td width="20%" align="center">${{ refund_items | dollar }}</td>
                          </tr>
                          <tr>
                            <td width="80%" align="right">Addl Amount to Refund:</td>
                            <td width="20%" align="center">
                                <form-number v-if="!hasRefund" v-model="form.refund_partial" :schema="schema.form.refund_partial" @input="_calcReturn" :hideLabel="true" />
                                <div v-else>0</div>
                            </td>
                          </tr>                            
                          <tr>
                            <td width="80%" align="right">Refund Total:</td>
                            <td width="20%" align="center"><b>${{ form.refund_total | dollar }}</b></td>
                          </tr>                              
                        </tbody>
                    </table>
                  </div>
                  
                <div class="col-12 clearfix mt-4 mb-3 justify-content-center text-center">
                    <button v-if="!hasRefund" class="btn btn-md btn-danger" :disabled="!form.refund_total || isProcessing" @click.prevent="autoSave(true)">
                        <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> Process Refund
                    </button>
                    <div v-if="hasRefund" style="color:white; display: inline;">
                        <form-button disabled text="Process Refund" show-disabled disabled-title="Refund Unavailable" disabled-text="This sale has already been refunded." icon-class="" />
                    </div>
                </div> 
                 
    
    
                </fieldset>
            </form>

        </div>
        <div v-else class="col-12 d-flex justify-content-center">
            <div class="col-sm-10 text-center mt-4 mb-4">
                <div class="block-announce warning">
                    <p class="title"><i class="hotbox-icon hotbox-icon-f-comment"></i> Hold on..</p>
                    <p>In order to Modify a Sale, you need to have a cash Drawer Open.</p>
                    <p><router-link :to="{name:'terminal-queue'}" tag="a" class="">Open a Cash Drawer &raquo;</router-link></p>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
    </div>
</template>

<script>

     import Item from '../../../../models/Sale';
     import Related from '../../../../models/Sale';

    export default {

        props: {
            id: {
                type: Number,
                default: null
            },
            model: {
                type: String,
                default: 'Sale'
            },
            module: {
                type: String,
                default: 'pos',
            }
        },
        
        data(){
            return {
                item: null,
                isLoading:false,
                isProcessing:false,
                form:{
                    action:'refund',
                    refund_partial: 0,
                    refund_total: 0,
                    refund_tax: 0,
                    returned_items: []
                },
                hasRefund: false
            };
        },
        
        components : {

        },
        
        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas','sale');       // need latest and greatest for this function
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema,false);
                    
                    this.item.items = this.item.items.map(v=>{                  // assign a refund qty to the items array for the return form
                        return Object.assign({},v,{quantity_returning:0,is_restock:true});
                    });
                    this._loadRelated();
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                    this.$emit('refresh',{},'sale');                          // will close this modal
                });
            }else{
                this.isLoading = false;
                this.$announcer({status:422,data:{message:'Whoops - Couldnt find the associated Sale record - Please try again later.'}});
                this.$emit('refresh',{},'sale');
            }
        },
        
        methods: {
            async autoSave(confirm=false){
                
                let withPin = await this.requirePin('Please Enter an Admin PIN to modify this Order');
                if(withPin===false) return false;                               // an adminpin couldnt be validated HINT add error message here if desired.
                
                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(this.form.refund_total<=0) return false;
                        
                        this.form.returned_items = this.item.items.map(v=>{ return {id:v.id,quantity_returning:v.quantity_returning,is_restock:v.is_restock}; });

                        this.isProcessing = true;
                        axios.post('/api/v1/admin/dispensary/sales/'+this.item.id+'/return',this.form).then(response =>{
                          this.$announcer({status:200,data:{message:'Order has been Modified - and a Return Ticket Successfully Created.'}});
                          this.$store.dispatch('pos/getCurrentDrawer');         // re-load current users drawer as a sale has been tendered
                          this.isProcessing = false;
                          this.$router.push({name:'sale',query:{search:this.item.order_number}}); // go to sale grid which will show new negative ticket alongside orig.
                          this.$emit('close');
                        }).catch(error => {
                          this.isProcessing = false;
                          this.$announcer(error.response);
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },

            async _loadRelated() {
                const related = await new Related()
                    .params({
                        search: `RETURN-${this.item.order_number}`,
                    })
                    .get();
                this.hasRefund = (related.data.length>0);
            },

            showDialog(text, title) {
                this.$swal.fire({
                    text: text,
                    title: title,
                    type: 'warning',
                    confirmButtonText: 'OK',
                })
            },

            _calcReturn(){                                                      // calculate refund amount based on items being returned

                this.form.refund_total = (                                      // remove tax from being refunded, and times by return quantity from inputs
                    this.item.items.reduce((amt,im) => (amt + ((im.sale_price/im.quantity)*im.quantity_returning)),0) +
                    this.form.refund_partial
                );
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            
            refund_items(){
                return (this.form.refund_total - this.form.refund_partial);
            },
            
            refund_tax(){
                return ((this.item || {}).items || []).reduce((amt,im) => (amt + ((im.tax/im.quantity)*im.quantity_returning)),0);
            }
        },
        
        watch: {
            refund_tax(to,from){
                this.form.refund_tax = to;
            }
        }
        
    };
    
</script>

<style>

</style>
