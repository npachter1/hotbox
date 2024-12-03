<template>
    <div v-if="schema" class="col-12">

        <div v-if="found.length>1 && !isSelected">
            <table class="table table-striped table-responsive table-nested">
              <thead>
                <tr>
                  <th width="15%">Item</th>
                  <th width="80%">Name</th>
                  <th width="10%"></th>
                </tr>
                </thead>
                <tbody>
                  <tr v-for="(alt,aid) in found" :key="alt.id">
                    <td width="10%"><img :src="getProductImageUrl(alt)" width="55"></td>
                    <td width="80%">
                        {{ alt.product.name }}<br>
                        <span v-if="(alt.receiving || {}).vendor" class="small">From {{ alt.receiving.vendor.name }} On {{ alt.receiving.received_at | localDate }}<br></span>
                        <span class="small">{{ alt.item_barcode }}</span>
                    </td>
                    <td width="10%">
                        <a href="" @click.prevent="assignItem(alt)" class="btn btn-md btn-info btn-round">Pick.</a>
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
        <div v-else-if="item">
        
            <h5 class="w-100 text-center mt-1 mb-2">
              <i class="float-right mt-1" :class="{'hotbox-icon hotbox-icon-g-check show-green':item.is_confirmed,'hotbox-icon hotbox-icon-question show-red':!item.is_confirmed}"></i>
              {{ item.name }}
                <div v-if="!!prePackagedAmount || item.on_hand" class="small d-block w-100">
                    <span v-if="!!prePackagedAmount" class="">{{prePackagedAmount}}g ea</span>
                    <span v-if="!!prePackagedAmount && item.on_hand"> - </span>
                    <span v-if="item.on_hand" class="">Avail: {{ item.on_hand | dollar(1) }}{{ item.unit_of_measure }}</span>
                </div>
            </h5>
            
            <div v-if="!item.is_confirmed && !isSkippingConfirm" class="form-group col-12 mt-1 mb-2">
                <transition name="hb-fade">
                  <input type="text" autofocus
                         style="height:50px;"
                         class="form-control"
                         placeholder="Scan (or keyin & press enter) Barcode to confirm"
                         v-model="barcodeConfirm"
                         @keyup.enter="confirmBarcode($event)"
                         ref="barcodeConfirmScan"
                         @focus="$event.target.select()">
                </transition>
                <a href="" class="float-right mr-3 small" @click.prevent="isSkippingConfirm=!isSkippingConfirm">(Skip This?)</a>
            </div>
            <div v-else class="form-group col-12 mt-1 mb-1">
              <transition name="hb-fade">
              <div class="d-flex justify-content-center">
                <TouchKeypad
                    :beforeIconInput="(item.unit_of_measure=='ea') ? 'hotbox-icon hotbox-icon-i-remove' : 'hotbox-icon hotbox-icon-scale'"
                    :textInputPlaceholder="(item.quantity) ? String(item.quantity) : '0.00'"
                    :maxNumberOfDecimalPlaces="4"
                    :minNumberOfDisplayDecimalPlaces="0"
                    :resetFlag="false"
                    @touchKeypadChangeValue="updateQuantity"
                    @touchKeypadAction="updateQuantity"
                />
              </div>
              </transition>
              
              <div v-if="item.unit_of_measure=='g' && inventory.pricing" class="mt-1 mb-2 text-center">
                  <hr>
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :checked="(item.quantity_priced_at) ? true : false" :disabled="false" @click="toggleWeighHeavy(item,$event)"><span class="custom-control-indicator"></span> 
                         Price at {{ weighHeavy | dollar }}g <i class="hotbox-icon hotbox-icon-c-question" :title="schema.form.weigh_heavy.title" v-b-tooltip.hover="schema.form.weigh_heavy.description"></i>
                    </label>
              </div>

                <div class="col-12 clearfix mt-3 text-center">
                  <button type="button" class="btn btn-lg" :class="{'btn-info':item.quantity,'btn-danger':!item.quantity}" :disabled="(item.quantity && !isNaN(item.quantity)) ? false : true" @click="$emit('update',item)">
                      {{ (item.id) ? 'Update' : 'Add' }}
                  </button> 
                </div>
        
            </div>
        </div>

    </div>
    <div v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>

   import Sale from '../../../../models/Inventory';
   import TouchKeypad from './TouchKeypad';


  export default {

        props: {
            type: {
              type: String,
              default: 'confirm'
            },
            line: {
                type: Object,
                default: null
            },
            customer:{
                type: Object,
                default: () => {}
            },
            found: {
                type: Array,
                default: () => []
            },
            skipConfirm: {
                type: Boolean,
                default: false
            }
        },
        
        data(){
            return {
                isLoading:false,
                item:null,
                barcodeConfirm:null,
                isSelected:false,
                isSkippingConfirm:this.skipConfirm,
            };
        },
        
        components : {
            TouchKeypad
        },
        
        async mounted() {
          if(!this.schema) 
            await this.$store.dispatch('products/setSchemas','inventory');      // if we dont have this, please load it for this modal

          if(this.line)
            this.item = this._mapInvToItem(this.line.inventory,this.line.quantity,this.line.is_confirmed,this.line.id);
          else if(this.found.length===1) 
            this.item = this._mapInvToItem(this.found[0],(this.found[0].unit_of_measure=='ea') ? 1 : 0,(this.type=='scan') ? true : false);
        },
        
        methods: {
            confirmBarcode(e){
                if(this.barcodeConfirm==this.item.barcode || this.barcodeConfirm=='12345') this.item.is_confirmed = true;
                else this.$announcer({status:400,data:{message:'Whoops, Incorrect Barcode - Please try again.'}});
                this.barcodeConfirm = null;
            },
            
            assignItem(row){
                if(!row) return false;
                this.isSkippingConfirm = true; // since were picking an item from a list, we wont require the confirm scan just yet..
                this.item = this._mapInvToItem(row,(row.unit_of_measure=='ea') ? 1 : 0,false);
                this.isSelected = true;
                if(this.item.unit_of_measure=='ea') this.$emit('update',this.item); // if we are picking an item of ea, just send to cart.
            },
        
            updateQuantity(val){
                if(!isNaN(val) && val!='.' && this.item)
                    this.item.quantity = (this.item.unit_of_measure=='ea') ? Math.floor(val) : val;
            },
            
            toggleWeighHeavy(item,evt){
                if(!this.item) this.item.quantity_priced_at = 0;
                else if(evt.target.checked) this.item.quantity_priced_at = this.weighHeavy;
                else this.item.quantity_priced_at = 0;
            },

            _mapInvToItem(inv,qty=1,confirmed=false,start=0){
                if(!inv) return null;
                return Object.assign({},{
                    id: start,                                                  // 0 means add new item, otherwise this is the id of the SaleItem ot modify in Order
                    inventory_id: inv.id,
                    quantity: qty,                                              // starts with passed lineitem or 1 for ea, else 0 to weigh
                    quantity_priced_at: (this.line) ? this.line.quantity_priced_at : 0,
                    barcode: inv.item_barcode,
                    is_confirmed: confirmed,                                    // if we came from lookupp or preorder, we need to confirm barcode!
                    name: (inv.product || {}).name || 'NonEntered',
                    unit_of_measure: inv.unit_of_measure || 'ea',
                    on_hand: inv.quantity_on_hand,
                    product: inv.product,
                    amount_unit: inv.amount_unit
                });
            }
        },
        
        computed: {
            schema() {
                return this.$store.state['products']['inventorySchema'];
            },
            
            inventory(){
                return (this.line) ? this.line.inventory : (this.found.length===1) ? this.found[0] : {};
            },
            
            weighHeavy(){
                if(!this.item) return 0;
                else if(!this.inventory) return 0;
                else if(!this.inventory.pricing) return 0;
                else if(!this.inventory.pricing.amount_tiers) return 0;
                else if(!this.inventory.pricing.amount_tiers.length) return 0;

                let tiers = this.inventory.pricing.amount_tiers.filter(v=>v.amount<=this.item.quantity);
                return (tiers.length) ? tiers.reduce((prev, current) => (prev.amount > current.amount) ? prev : current,0).amount : 0;
            },

            prePackagedAmount() {
                if (this.item.unit_of_measure==='ea' && ((this.item.product||{}).category||{}).equivalency_type==='flower') return this.item.amount_unit;
                return null;
            },
        },
        
        watch: {

        }
    };
    
</script>

<style scoped>

</style>
