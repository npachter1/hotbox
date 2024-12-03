<template>
    <div v-if="inv">

        <label v-if="hideLabel!==true" :for="'inv'+schema.name" class="w-100">
            <span class="small float-right" :class="{'strong-span':inv.priceset_id}"><i>* 1 unit = ${{ retail | dollar }}</i></span>
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>

        <multiselect
                v-if="inv.product"
                @input="updateValue"
                :value="currentlySelectedOption"
                placeholder="Click to Select"
                :show-labels="val===null"
                :allow-empty="true"
                label="name"
                track-by="value"
                deselect-label="Edit"
                selected-label=""
                select-label=""
                group-values="values"
                group-label="category"
                @remove="setFixPrice"
                :group-select="false"
                :options="multiselectOptions"></multiselect>
                      <span v-show="errors.has('priceset_id')" class="form-text text-muted val-danger-text">Please select an active Priceset, or specify Fixed.</span>

        <b-modal centered ref="pricesetModal"
            v-model="pricesetModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="pricesetModal=!pricesetModal"></i>
              <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-currency-dollar"></i> Pricing Set Editor</h5>
            </template>
          
              <priceset-modal v-if="pricesetModal"
                :id="0"
                :focus="((inv.product || {}).category || {}).id"
                type="modal"
                @refresh="updatePricesetOption">
              </priceset-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="pricesetModal=!pricesetModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="fixedModal"
                 v-model="fixedPriceModal"
                 size="sm"
                 @shown="$refs.fixedAmount.focus()"
                 header-bg-variant="light"
                 header-text-variant="primary">

            <template slot="modal-header">
                <i class="modal-top-close fal fa-times" @click="fixedPriceModal=!fixedPriceModal"></i>
                <h5 class="w-100 mb-0 text-center">Fixed Price</h5>
            </template>
            Set Price:

            <input
                    type="number"
                    min="0"
                    autofocus
                    ref="fixedAmount"
                    v-validate="'required|decimal:4'"
                    name="amount"
                    v-on:keyup.enter="()=>{if (!(errors.items||{}).length) fixedPriceModal=!fixedPriceModal}"
                    v-model="fixedPriceValue"
            >

            <template slot="modal-footer">
                <span :class="{disabled:(errors.items||{}).length>0}" class="btn-label btn-sm btn-light float-right" @click="()=>{if (!(errors.items||{}).length) fixedPriceModal=!fixedPriceModal}">Close</span>
            </template>
        </b-modal>
    </div>
</template>
<script>

    import PricesetModal from '../priceset/editForm';

    export default {
        props: {
            value: {
                //type: [String,Number],
                default: '',
            },
            schema: {
              type: Object,
              default: () => {}
            },
            hideLabel:{
                type: Boolean,
                default: false
            },
            inv: {
                type: Object,
                default: () => {}
            },
            fixedPrice: {
                type: [String,Number],
                default: null,
            }
        },
        
        data(){
            return {
                selected: null,
                retail:0,
                isLoading: false,
                pricesetModal:false,
                testing: null,
                fixedPriceModal: false,
                fixedPriceValue: null,
            };
        },
        
        components : {
            PricesetModal
        },
        
    	mounted() {
            this.selected = this.options.find(row => row.id==this.val);
            this.getCurrentPrice();
            if (!isNaN(this.fixedPrice)) this.fixedPriceValue = this.fixedPrice*1;
    	},
    	
    	computed: {
    		val: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v); }
    		},

    		options(){
    		    return this.schema.values;
    		},

    		isRequired() {
    		    if(this.schema.validation)
    		        return (this.schema.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		},

            currentlySelectedOption() {
    		    let returnValue = null;
    		    this.multiselectOptions.forEach(category=>{
    		        let f = category.values.find(e=>e.value===this.val);
                    if (f) returnValue=f;
                });

    		    return returnValue;
            },

            multiselectOptions() {
    		    let x = [];

    		    if (this.inv.product) {
    		        let o = {
                        category: 'By Product/Grade:',
                        values: [],
                    };
    		        this.options.forEach(e=>{
    		            if (this.inProdSet(this.inv.product_id,e.product_ids)) {
                            let lowPrice = (e.amount_tiers[0] || {}).price + '/' + e.type_uom;
                            let highPrice = (e.amount_tiers[e.amount_tiers.length-1] || {}).price + '/' + e.type_uom;
                            o.values.push({
                                name: `${e.name_grade} - From ${lowPrice} to ${highPrice}`,
                                value: e.id,
                            });
                        }
                    });
                    x.push(o);
                }

                if (this.inv.product.category) {
                    let o = {
                        category: (this.inv.product.category.name || 'Misc')+ ' Defauts:',
                        values: [],
                    };
                    this.options.forEach(e=>{
                        if (!this.inProdSet(this.inv.product_id,e.product_ids) && this.inCatSet(this.inv.product.category.id,e.category_ids)) {
                            let lowPrice = (e.amount_tiers[0] || {}).price + '/' + e.type_uom;
                            let highPrice = (e.amount_tiers[e.amount_tiers.length-1] || {}).price + '/' + e.type_uom;
                            o.values.push({
                                name: `${e.name_grade} - From ${lowPrice} to ${highPrice}`,
                                value: e.id,
                            });
                        }
                    });
                    x.push(o);
                }

                let o = {
                    category: 'Other',
                    values: [{
                        name: this.val===null ? `Fixed Price ($${this.fixedPriceValue})` : 'Fixed Price',
                        value: null
                    }]
                };

                if (this.schema.prompt_new) o.values.push({name: 'Add New Priceset',value: 'NEW'});
                x.push(o);

    		    return x;
            },
    	},
    	
        watch: {
            fixedPriceValue(newValue) {
                this.$emit('fixedPrice',newValue);
            },

            selected(){
                this.val = (this.selected) ? this.selected.id : null;
            },

            value(to,from){
                if(from) this.selected = this.options.find(row => row.id==this.value); // if we updated the value prop
            },
            
            val(to,from){
                if(to=='NEW') this.pricesetModal=!this.pricesetModal;
            },
            
            inv:{
                handler(newVal,oldVal){
                    this.getCurrentPrice();
                },
                deep: true
            },

            fixedPrice(newValue, oldValue) {
                if ((newValue!==oldValue) && (!isNaN(this.fixedPrice))) this.fixedPriceValue=newValue*1;
            }
        },
    	
    	methods: {
    	   updatePricesetOption(upd){
              this.val = upd.id; // this is ok, cause the schema will have reloaded via the priceset edit form, thus allowing us to select this new priceset!
              this.pricesetModal=!this.pricesetModal;
            },
            
            getCurrentPrice(){
                
                if(!this.inv.priceset_id || !this.selected){
                    this.retail = this.inv.retail_unit;
                    return false;
                }
                
                let table = this.selected;
                let thresh = 0;
                let result = 0;

                switch(table.type_uom){
                  case 'g':
                    thresh = (this.inv.amount_unit_sale) ? this.inv.amount_unit_sale : 1;
                    break;
                  case 'mg':
                    thresh = (this.inv.amount_unit_sale) ? this.inv.amount_unit_sale*1000 : 1000;
                    break;
                  case 'ea':
                    thresh = 1;
                    break;
                  default: thresh = (this.inv.amount_unit_sale) ? this.inv.amount_unit_sale : 1;
                }
                  
                if(!table.amount_tiers) this.inv.retail_unit = (table.amount_default) ? table.amount_default*thresh : this.inv.retail_unit;
                else{
                  
                  /* go through tiers in order, and return the price if the unit thresh is lower or equal to */
                  table.amount_tiers.sort(function(a,b){ if(a['amount'] < b['amount']) return -1; else return 1; });
                  table.amount_tiers.forEach(function(o){if (o.amount <= thresh) result = o.price;});
        
                  this.retail = (result) ? result*thresh : table.amount_default*thresh;
        
                }
                
                this.$emit('updateUnit',this.retail);
                return true;
        
            },
            
            inProdSet(pid,prods){
                if(!pid || !prods) return false;
                return (prods.find(v=>v.id==pid)) ? true : false;
            },
            
            inCatSet(cid,cats){
                if(!cid || !cats) return false;
                return (cats.find(v=>v.id==cid)) ? true : false;
            },

            updateValue(v,id) {
                // vue-multiselect offers no direct way to track when a user clicks on an already selected item so user can "edit" fixed price amount
                // so, instead, I set the multiselect to allow unselecting items then I ignore the unselection in @input (but catch the @remove firing below).
                if (v) this.val=v.value;
                this.setFixPrice();
            },

            setFixPrice() {
                // we're using @remove as a trigger that the user wants to edit the underlying value (price) that has already been set.
    	       this.$nextTick(()=> { //wait for multiselect to update model
    	           if (this.val===null) this.fixedPriceModal=true;
               });
            },
    	},
    	
    	inject: ['$validator']
    };
</script>

<style scoped>
    >>> .multiselect__option--group {
        background: #c6c8ca;
    }
    >>> div.multiselect div.multiselect__tags {
        padding-top: 0;
    }
    >>> div.multiselect.multiselect--active input {
        padding-top:5px;
    }
    >>> span.multiselect__placeholder {
        margin:4px;
    }
    >>> div.multiselect__select {
        top:0;
    }
    >>> div.multiselect,
    >>> div.multiselect div.multiselect__tags,
    >>> div.multiselect div.multiselect__tags input,
    >>> div.multiselect div.multiselect__tags span.multiselect__single {
        background-color: transparent;
        font-size: small;
    }
    .modal-footer .btn-label:not(.disabled) {
        cursor: pointer;
    }
</style>