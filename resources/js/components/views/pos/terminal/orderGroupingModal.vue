<template>
    <div v-if="schema && grouping" class="col-12">

            <table class="table table-striped table-responsive table-nested">
              <thead>
                <tr>
                  <th colspan="2" width="75%">{{ grouping.name }}</th>
                  <th width="15%">Add</th>
                </tr>
                </thead>
                <tbody>
                  <tr v-for="(item,aid) in items" :key="item.id">
                    <td width="10%"><img :src="getProductImageUrl(item)" width="55"></td>
                    <td width="65%">
                        {{ item.product.name }}<br>
                        <span class="small">{{ item.item_barcode }}</span>
                    </td>
                    <td width="25%">
                        <div>
                            <input type="number"
                                   v-model.number="item.quantity"
                                   @keypress="preventInvalid"
                                   @focus="$event.target.select()"
                                   min="0"
                                   class="form-control form-control-reconcile"
                                   style="width: 70%; display: inline;"
                                   placeholder="">
                            <span class="small">{{ item.unit_of_measure }}</span>
                        </div>
                    </td>
                  </tr>
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
            <TouchKeypad
                    :beforeIconInput="'hotbox-icon hotbox-icon-i-remove'"
                    :textInputPlaceholder="(quantity) ? String(quantity) : '0.00'"
                    :maxNumberOfDecimalPlaces="4"
                    :minNumberOfDisplayDecimalPlaces="0"
                    :resetFlag="false"
                    @touchKeypadChangeValue="updateQuantity"
                    @touchKeypadAction="updateQuantity"
                />
            </div>

            <div class="col-12 clearfix mt-3 text-center">
                <button type="button" class="btn btn-lg" :class="{'btn-info':quantity,'btn-danger':!quantity}" :disabled="(quantity && !isNaN(quantity)) ? false : true" @click="assignItems">
                    {{ 'Add' }}
                </button> 
            </div>

    </div>
    <div v-else>
        <loading :display="true" type="loadModal" />
    </div>
</template>

<script>

   import Sale from '../../../../models/Inventory';
   import Product from '../../../../models/Product';
   import TouchKeypad from './TouchKeypad';


  export default {

        props: {
            type: {
              type: String,
              default: 'confirm'
            },
            group: {
                type: Object,
                default: null
            },
            customer:{
                type: Object,
                default: () => {}
            },
            skipConfirm: {
                type: Boolean,
                default: false
            }
        },
        
        data(){
            return {
                isLoading:false,
                grouping:null,
                items:[],
                quantity:1,
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
            
          this.grouping = await Product.find(this.group.id);
          this.items = (this.grouping.grouped || []).map(v=>{ v.quantity = v.pivot.quantity; return v; });
        },
        
        methods: {
            assignItems(){
                
                // TODO confirm inventory check(s)..
                let lines = this.items.map(v=>{ return this._mapInvToItem(v,(v.quantity*this.quantity),true,0); });
                this.$emit('update',lines);
            },
        
            updateQuantity(val){
                if(!isNaN(val) && val!='.' && this.item)
                    this.quantity = Math.floor(val);
            },

            _mapInvToItem(inv,qty=1,confirmed=false,start=0){
                if(!inv) return null;
                return Object.assign({},{
                    id: start,                                                  // 0 means add new item, otherwise this is the id of the SaleItem ot modify in Order
                    inventory_id: inv.id,
                    quantity: qty,                                              // starts with passed lineitem or 1 for ea, else 0 to weigh
                    quantity_priced_at: 0,
                    barcode: inv.item_barcode,
                    is_confirmed: confirmed,                                    // if we came from lookupp or preorder, we need to confirm barcode!
                    name: (inv.product || {}).name || 'NonEntered',
                    unit_of_measure: inv.unit_of_measure || 'ea',
                    on_hand: inv.quantity_on_hand,
                    product: inv.product,
                    amount_unit: inv.amount_unit,
                    group_ref: this.grouping.id, //send grouping id to parent when we $emit so it can visually group the items together
                });
            },

            preventInvalid($event) {
                const keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                if (keyCode < 48 || keyCode > 57) $event.preventDefault();
            },
        },
        
        computed: {
            schema() {
                return this.$store.state['products']['inventorySchema'];
            },
        },
        
        watch: {

        }
    };
    
</script>

<style scoped>

</style>
