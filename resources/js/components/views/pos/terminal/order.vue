<template>
    <div v-if="schema && drawer && customer && order" class="">
      <div class="row">
        <div style="display:none;"><div id="receipt_content" v-if="receiptHtml" v-html="receiptHtml" ></div><div id="receipt_barcode" class="esc-line esc-justify-center"><barcode :value="receiptBarcode" tag="svg" :options="{ height:40,width:1,format:'CODE39'}"></barcode></div></div>
        <div class="col-12 col-md-5 pos-order-column">
          <loading :display="isProcessing" type="loadAsset" />
          <nav class="pos-order-nav" :class="{'show-inactive':isProcessing}">
            <div class="col-12 pos-order">
              
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="mt-1 mb-0">
                    {{ customer.alias }} {{ customer.first_name }} {{ customer.last_name }}
                     <span v-if="customer.type=='wholesale'" class="small">[Wholesale]</span>
                    <i class="hotbox-icon hotbox-icon-c-info" style="cursor: pointer;" @click="customerDetailModal=!customerDetailModal"></i>
                    <router-link :to="{ name: 'customer_edit',params:{ id:customer.id } }" tag="i" class="hotbox-icon hotbox-icon-pencil" style="cursor: pointer;" title="Manage Customer"></router-link>
                  </h5>
                  <span v-if="customer.address">{{ customer.address.cell }} {{ customer.address.email }}</span>
                  <span class="badge badge-info">{{ customer.total_reward_points | dollar(0) }} Points</span> 
                </div>
                <div>
                  <button @click="reQueue()" type="button" class="btn btn-default btn-round btn-sm"><i class="hotbox-icon hotbox-icon-minimal-left"></i> Re-Que</button>
                </div>
              </div>
              
              <ul v-if="('items' in order || {}) && ((order || {}).items || []).length > 0" class="list-group pos-order-list-group">
                <div v-if="'discounts' in order">
                    <li v-for="discount in order.discounts"
                        v-if="discount.applied.is_active || (!discount.discount_code && order.omit_rule_ids.indexOf(discount.id)===-1) || (discount.discount_code==order.discount_code && discount.discount_code)"
                        :key="discount.id"
                        class="list-group-item list-group-item-sub d-flex justify-content-between align-items-center"
                        :class="{'list-group-item-info':discount.applied.is_active,'list-group-item-danger':!discount.applied.is_active}">
                      <div></div>
                      <div>
                        <span v-if="discount.applied.is_active"><i class="hotbox-icon hotbox-icon-check-single"></i></span>
                        <span v-else><b><i class="hotbox-icon hotbox-icon-e-remove" @click="removeDiscountRule(discount.id)"></i></b></span>
                        {{ (discount.applied.is_active) ? 'Applied' : 'Skipped' }} {{ discount.discount_code ? 'Discount '+discount.discount_code : 'Open Discount' }}:
                        {{ discount.descriptor }}
                        <span v-if="!discount.applied.is_active && 'rejection_reason' in discount.applied" class="d-block w-100"><strong> ({{ discount.applied.rejection_reason }})</strong></span>
                      </div>
                      <div v-if="discount.applied.is_active" class="ml-auto ">
                        <span v-if="discount.discount_type === 'amt'">$</span>{{ Number(discount.discount_amount).toFixed(2) }}<span v-if="discount.discount_type === 'pct'">%</span>
                      </div>
                      <div v-else class="ml-auto">
                        <s><span v-if="discount.discount_type === 'amt'">$</span>{{ Number(discount.discount_amount).toFixed(2) }}<span v-if="discount.discount_type === 'pct'">%</span></s>
                      </div>
                      <a href="" @click.prevent="orderDiscountModal=!orderDiscountModal" class="d-block float-right ml-2 mt-1"><i class="hotbox-icon hotbox-icon-c-info larger" :class="{'sh-green':discount.applied.is_active}"></i></a>
                    </li>
                </div>

                <transition-group name="hb-list-slide">
                <div v-for="item in orderItemsSorted" v-if="item && item.id!==0" :key="item.id">
                  <li class="list-group-item list-group-item-info list-group-item-sub d-flex justify-content-between align-items-center"
                      v-if="item.group_name">
                    <div style="text-transform: uppercase; font-weight: bold; cursor: pointer; width: 100%;" @click="openOrderGrouping(item.group_ref,'edit')">
                      {{ item.group_name }}
                    </div>
                    <a href="" @click.prevent="removeGroupItems(item.group_ref)" class="d-block float-right ml-2 mt-1"><i class="hotbox-icon hotbox-icon hotbox-icon-c-remove larger"></i></a>
                  </li>

                  <li class="list-group-item d-flex justify-content-between align-items-center"
                      :class="{'list-group-item-danger':item.quantity > (item.inventory || {}).quantity_on_hand,'list-group-item-warning':!item.is_confirmed}">
                      <div style="width:40px;height:30px;">
                        <button v-if="!item.group_ref" @click.stop="removeOrderItem(item.id)" type="button" class="btn btn-danger btn-order-item-delete"><i class="hotbox-icon hotbox-icon-e-remove"></i></button>
                      </div>
                      <div class="item-img-container" @click="openOrderItem([item],'edit')">
                        <img :src="getProductImageUrl(item.inventory)">
                      </div>
                      <div v-if="item.inventory" @click="openOrderItem([item],'edit')">
                        {{ item.inventory.product.name }}
                        <div class="d-flex flex-row align-items-center order-item-description">
                          <div>{{ item.quantity }}</div>
                          <div>{{ item.inventory.unit_of_measure }}</div>
                          <div v-if="item.quantity_priced_at>0.01" class="small mr-1"><i> (Priced at {{ item.quantity_priced_at | dollar }}{{ item.inventory.unit_of_measure }})</i></div>
                        </div>
                        <div class="order-item-thc" v-html="getOrderItemThcString(item)"></div>
                        
                        <div class="text-danger text-center" v-if="item.quantity > (item.inventory || {}).quantity_on_hand">
                           <b><i>*Quantity greater than quantity on hand.</i></b>
                        </div>
                        <div class="text-warning text-center" v-if="!item.is_confirmed">
                          <b><i>*Please Confirm/Scan this Item!</i></b>
                        </div>
                        
                      </div>
                      <div v-else>Non-Entered</div>
                      <div class="ml-auto " style="position:relative;">
                        <span v-if="(item.inventory || {}).pricing">
                          <span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">${{ item.price | dollar }}</span>
                            <div class="dropdown-menu tight">
                              <price-table :data="item.inventory.pricing" />
                            </div>
                        </span>
                        <span v-else>${{ item.price | dollar }}</span>
                        <div v-if="'tax' in item && Number(item.tax)" class="text-right list-group-item-sub p-0">+ {{ item.tax | dollar }}</div>
                      </div>
                    </li>
                    <li class="list-group-item list-group-item-warning list-group-item-sub d-flex justify-content-between align-items-center"
                        v-if="false && 'tax' in item && Number(item.tax)">
                      <div>
                        Tax:
                      </div>
                      <div>
                        + {{ item.tax | dollar }}
                      </div>
                    </li>
                    <li class="list-group-item list-group-item-info list-group-item-sub d-flex justify-content-between align-items-center"
                        v-if="'discount' in item && Number(item.discount)">
                      <div>
                        Discount:
                        {{ item.discount_code ? item.discount_code : '' }}
                      </div>
                      <div>
                        - {{ item.discount | dollar }}
                      </div>
                    </li>
                  </div>
                </transition-group>
              </ul>
              
              <div class="list-group-item">
                <input type="text" autofocus
                  style="height:50px;"
                  class="form-control"
                  placeholder="Scan (or keyin & press enter) Item Barcode to Add.."
                  v-model="itemScan"
                  @keyup.enter="scanIn($event)"
                  ref="scanAdd"
                  @focus="$event.target.select()">
                <span v-if="!('items' in order) || order.items.length < 1" class="d-block w-100 mt-1 mb-2 text-center">
                  Cart is Empty.
                </span>  
              </div>
              
              
            </div>
            
            <div class="col-12 pos-order-totals">

              <ul class="list-group pos-order-list-group" :class="{'show-inactive':isDirty}">
                <li class="list-group-item list-group-item-light d-flex justify-content-between align-items-center">
                  <div>
                    Subtotal
                  </div>
                  <div>
                    ${{ order.price | dollar }}
                  </div>
                </li>
        
                <li class="list-group-item list-group-item-light d-flex justify-content-between align-items-center">
                  <div>
                    Tax
                  </div>
                  <div>
                    {{ order.tax | dollar }}
                  </div>
                </li>
                <li v-if="'discount' in order"
                    class="list-group-item list-group-item-info d-flex justify-content-between align-items-center">
                  <div>
                    Total Discount
                  </div>
                  <div>
                    -{{ order.discount | dollar }}
                  </div>
                </li>
                <li class="list-group-item list-group-item-success list-group-item-total-cost d-flex justify-content-between align-items-center">
                  <div>
                    Total
                  </div>
                  <div>
                    ${{ order.sale_price | dollar }}
                  </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center" :class="{'list-group-item-thc-over':overLimit,'list-group-item-thc':!overLimit,'hint-inactive':customer.type=='wholesale'}">
                  <div>THC Equivalent Limit</div>
                  <div>
                    <b><span v-html="getFormattedOuncesFromGrams(order.thc_equivalent_grams, true)" class="mr-1" /></b>
                     <span v-if="limitLeft!=customer.thc_limit_grams">
                       of <span v-html="getFormattedOuncesFromGrams(limitLeft, true)" /> remaining on your <span v-html="getFormattedOuncesFromGrams(customer.thc_limit_grams, true)" /> Daily Max.
                     </span>
                     <span v-else>
                       of your <span v-html="getFormattedOuncesFromGrams(customer.thc_limit_grams, true)" /> Daily Max.
                     </span>
                     <i class="hotbox-icon hotbox-icon-c-question" :class="{'show-red':overLimit}" :title="'Customer THC Limit'" v-b-tooltip.hover="schema.lang.thc_limit_desc"></i>
                  </div>
                </li>
              </ul>
              
            </div>
          </nav>

          
          </div>
          <main role="main" class="col-12 col-md-7 pos-content">
            <div class="col-12 pos-inventory-search" ref="pos-inventory-search">

              <div class="input-group mt-0 mb-3">
                  <div class="input-group-prepend lookup-input lookup-prepend">
                    <i v-if="lookupCat!==0" @click="lookupCat=0" class="hotbox-icon hotbox-icon-e-remove clear-category"></i>
                      <a class="dropdown-toggle py-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ lookupCat | renderValue(schema.form.lookup_categories) | cleanCategoryName }}</a>
                      <div class="dropdown-menu tight">
                          <a v-for="(val,vid) in dropdownCategories" v-if="dropdownCategories && val.items_count" :key="val.id" class="dropdown-item action-default" href="" @click.prevent="searchLookup(val.id)">
                              {{ val.name |  cleanCategoryName }} <span class="float-right small">{{ val.items_count }}</span>
                          </a>
                      </div>
                  </div>                            
                  <input type="text" 
                      v-model="lookupSearch" 
                      class="form-control lookup-input" 
                      :placeholder="'Lookup Inventory..'"
                      @input="searchLookup(lookupCat)"
                      @keydown.enter.prevent="searchLookup(lookupCat)">
              </div>
                          
              <transition name="hb-fade">
                <div v-if="lookupCat>0 || lookupSearch!==''" class="row search-contain" :class="{'is-finding':isFinding}">
                  <loading :display="isFinding" type="loadModal" />
    
                  <div v-for="item in (lookupGroupings || {}).data || []" :key="item.id" class="col-md-4 col-6">
                      <pos-grouping-card @pull="openOrderGrouping(item)" :group="item"></pos-grouping-card>
                  </div>
    
                  <div v-for="item in (lookup || {}).data || []" :key="item.id" class="col-md-4 col-6">
                      <pos-inventory-card @pull="openOrderItem([item],'pick',true)" :item="item"></pos-inventory-card>
                  </div>

                  <div class="col-12">
                    <b-pagination v-if="lookup && lookup.meta.last_page>1"
                                  :total-rows="lookup.meta.total"
                                  :per-page="lookup.meta.per_page"
                                  v-model="lookupPage"
                                  align="right"
                                  aria-controls="users_table"/>

                  </div>

<!--                  <div v-if="lookup && lookup.meta.last_page>1" class="col-12 clearfix text-center">
                      <div v-if="lookupPage>1" >
                        <a href="" class="" @click.prevent="lookupPage=lookupPage-1;_fetchLookup(lookupCat)">&laquo; Prev</a> |
                      </div>
                      <a v-if="lookup.meta.last_page>lookupPage" href="" class="" @click.prevent="lookupPage=lookupPage+1;_fetchLookup(lookupCat)">Next &raquo;</a>
                  </div>-->
                </div>

                <div v-if="lookupCat===0 && lookupSearch===''" class="row search-contain"><!--categories-->
                  <loading :display="isFinding" type="loadModal" />

                  <div v-if="lookupCategories" v-for="category in lookupCategories" :key="category.id" class="col-md-4 col-6">
                    <pos-category-card :category="category" @selected="searchLookup"></pos-category-card>
                  </div>

<!--                  <div v-if="false && lookup">
                    <div v-if="lookup.meta.last_page>1" class="col-12 clearfix text-center">
                      <a v-if="lookupPage>1" href="" class="" @click.prevent="lookupPage=lookupPage-1">&laquo; Prev</a> |
                      <a v-if="lookup.meta.last_page>lookupPage" href="" class="" @click.prevent="lookupPage=lookupPage+1">Next &raquo;</a>
                    </div>
                  </div>-->
                </div>
              </transition>

            </div>
            
            
            <div class="col-12 pos-input-inventory-search">
              <div v-if="order" class="row">
                <div class="col-6 col-sm-7">
                  <div class="input-group mt-1">
                    <input class="form-control mt-1 mb-1 py-2 form-control-pos-keypad"
                      v-model="order.discount_code" 
                      placeholder="Search/Scan Discount Code and press Enter."
                      @input=""
                      @keydown.enter.prevent="_updateOrder()">
                        <div class="input-group-append input-group-append-pos-keypad mt-1">
                          <b style="font-size:1.5em color:#FFF"><i class="hotbox-icon hotbox-icon-c-info mt-1 discount-info" @click="orderDiscountModal=!orderDiscountModal"></i></b>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-5 text-right">
                  <button @click="openPayment" type="button" :disabled="overLimit || !order || overSold" class="btn btn-info btn-pos-pad-pay" :class="{'btn-danger':overLimit || overSold,'btn-info':!overLimit && !overSold}">
                    <i class="hotbox-icon hotbox-icon-shopping-cart icon-pos-pay"></i> Pay
                  </button> 
                  <button @click="openPrinterSetupModal()" v-bind:class="connectedPrinter ? 'btn-success' : 'btn-warning'" type="button" class="btn btn-default btn-pos-pad-void">
                      <i class="hotbox-icon hotbox-icon-usb"></i>
                  </button>
                  <button @click="voidOrder" type="button" :disabled="!(order || {}).id" class="btn btn-default btn-pos-pad-void">
                    <i class="hotbox-icon hotbox-icon-trash-round"></i>
                  </button> 
                </div>
              </div>
            </div>
          </main>

    </div>


    <b-modal centered ref="orderItemModal"
      v-model="orderItemModal"
      header-bg-variant="light"
      header-text-variant="primary">
    
      <template slot="modal-header">
        <i class="modal-top-close fal ti-close" @click="closeOrderItem"></i>
        <h5 class="w-100 mb-0 text-center">Add to {{ order.order_number || 'New Order' }}</h5>
      </template>
          
        <order-item-modal v-if="orderItemModal"
          :type="orderItemType"
          :skipConfirm="orderItemSkip"
          :line="orderItemLine"
          :customer="customer"
          :found="orderItemFound"
          @cancel="closeOrderItem"
          @update="updateOrderItem">
        </order-item-modal>
    
      <template slot="modal-footer">
          <span class="btn-label btn-sm btn-light float-right" @click="closeOrderItem">Close</span>
      </template>
    </b-modal>

    <b-modal centered ref="orderGroupingModal"
      v-model="orderGroupingModal"
      header-bg-variant="light"
      header-text-variant="primary">
    
      <template slot="modal-header">
        <i class="modal-top-close fal ti-close" @click="closeOrderGrouping"></i>
        <h5 class="w-100 mb-0 text-center">Add to {{ order.order_number || 'New Order' }}</h5>
      </template>
          
        <order-grouping-modal v-if="orderGroupingModal"
          :group="orderGrouping"
          :customer="customer"
          @cancel="closeOrderGrouping"
          @update="updateOrderGrouping">
        </order-grouping-modal>
    
      <template slot="modal-footer">
          <span class="btn-label btn-sm btn-light float-right" @click="closeOrderGrouping">Close</span>
      </template>
    </b-modal>

    <b-modal centered ref="orderPaymentModal"
      v-model="orderPaymentModal"
      header-bg-variant="light"
      header-text-variant="primary">
    
      <template slot="modal-header">
        <i class="modal-top-close fal ti-close" @click="orderPaymentModal=!orderPaymentModal"></i>
        <h5 class="w-100 mb-0 text-center">Payment Due: ${{ order.sale_price | dollar }}</h5>
      </template>
          
        <order-payment-modal v-if="orderPaymentModal"
          :order="order"
          :customer="customer"
          @printReceiptPDF="printReceiptPDF($event)"
          @printReceiptEmail="printReceiptEmail($event)"
          @cancel="orderPaymentModal=!orderPaymentModal"
          @complete="completePayment">
        </order-payment-modal>
    
      <template slot="modal-footer">
          <span class="btn-label btn-sm btn-light float-right" @click="orderPaymentModal=!orderPaymentModal">Close</span>
      </template>
    </b-modal>

    <b-modal centered ref="orderDiscountModal"
      size="lg"
      v-model="orderDiscountModal"
      header-bg-variant="light"
      header-text-variant="primary">
    
      <template slot="modal-header">
        <i class="modal-top-close fal ti-close" @click="orderDiscountModal=!orderDiscountModal"></i>
        <h5 class="w-100 mb-0 text-center">Applicable Discount Offers</h5>
      </template>
          
        <order-discount-modal v-if="orderDiscountModal"
          :order="order"
          @cancel="orderDiscountModal=!orderDiscountModal"
          @apply="applyDiscountRule"
          @remove="removeDiscountRule">
        </order-discount-modal>
    
      <template slot="modal-footer">
          <span class="btn-label btn-sm btn-light float-right" @click="orderDiscountModal=!orderDiscountModal">Close</span>
      </template>
    </b-modal>
    
    <b-modal centered ref="customerDetailModal"
      size="lg"
      v-model="customerDetailModal"
      header-bg-variant="light"
      header-text-variant="primary">
    
      <template slot="modal-header">
        <i class="modal-top-close fal ti-close" @click="customerDetailModal=!customerDetailModal"></i>
        <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-c-info"></i> {{ customer.alias }} {{ customer.first_name }} {{ customer.last_name }}</h5>
      </template>
          
        <customer-detail-modal v-if="customerDetailModal"
          type="pos" 
          :id="customer.id">
        </customer-detail-modal>
    
      <template slot="modal-footer">
          <span class="btn-label btn-sm btn-light float-right" @click="customerDetailModal=!customerDetailModal">Close</span>
      </template>
    </b-modal>

     <b-modal centered ref="connectPrinterModal" :static=true
      v-model="connectPrinterModal"
      header-bg-variant="light"
      header-text-variant="primary">
      <template slot="modal-header">
        <i class="modal-top-close fal ti-close" @click="connectPrinterModal=!connectPrinterModal"></i>
        <h5 class="w-100 mb-0 text-center">USB Thermal Printer</h5>
      </template>
      <div class="d-flex justify-content-center">
        <div>
          <ThermalPrinter @printerSetupAction="printerSetupAction($event)" ref="printer" 
          />
        </div>
      </div>
      <template slot="modal-footer">
          <span class="btn-label btn-sm btn-light float-right" @click="connectPrinterModal=!connectPrinterModal">Close</span>
      </template>
    </b-modal>    

  </div>
  <div v-else>
    <loading :display="true" type="loadPage" />
  </div>
</template>
<script>


  import Sale from '../../../../models/Sale';
  import Customer from '../../../../models/Customer';
  import Lookup from '../../../../models/Inventory';
  import LookupCategories from '../../../../models/Category';
  import LookupGroupings from '../../../../models/Product';

  import PosInventoryCard from "./posInventoryCard";
  import PosCategoryCard from "./posCategoryCard";
  import PosGroupingCard from "./posGroupingCard";
  import OrderItemModal from "./orderItemModal";
  import OrderGroupingModal from "./orderGroupingModal";
  import OrderPaymentModal from "./orderPaymentModal";
  import OrderDiscountModal from "./orderDiscountModal";
  import CustomerDetailModal from "../../administration/customer/customerDetail";
  import ThermalPrinter from '../../../elements/ThermalPrinter';
  import _ from 'lodash';
  import { Printd } from 'printd';

  export default {
    props: {
      customer_id: {
        type: Number,
        default: null
      }
    },
    
    components: {
      PosInventoryCard,PosGroupingCard,OrderItemModal,OrderGroupingModal,OrderPaymentModal,OrderDiscountModal,CustomerDetailModal,
      PosCategoryCard,ThermalPrinter
    },
    
    data() {
      return {
        customer: null,
        order:null,
        isLoading:false,
        isProcessing:false,                                                     // sale order checkout display
        isFinding:false,                                                        // item lookup
        isDirty:false,                                                          // flag if order couldnt be updated.
        itemScan:null,
        lookup:null,                                                            // array of inventory items being looked up
        lookupCategories:null,
        lookupGroupings:null,
        lookupCat:0,
        lookupSearch:'',
        lookupPage:1,
        orderItemModal:false,
        orderItemType:'confirm',
        orderItemSkip:false,
        orderItemLine:null,
        orderItemFound:[],
        orderGrouping:null,
        orderGroupingModal:false,
        orderPaymentModal:false,
        orderDiscountModal:false,
        customerDetailModal:false,
        connectPrinterModal:false,
        receiptHtml:null,
        receiptBarcode:null,
        productGroupNames:null,
      };
    },
    
    async mounted() {
      this._fetchLookupCategories();

        if(!this.drawer){ this.$router.push({name:'terminal-drawer'}); return false; }
        else if(!this.customer_id){ this.$router.push({name:'terminal-queue'}); return false; }

        this.isLoading = true;
        if (window.Echo){
          Echo.channel('inventoryitem')
            .listen('InventoryItemUpdated', (e) => {
              if(this.lookup){
              _.each(this.lookup.data,function(obj) {
                let updatedItem = _.find(e.data, function(o) { return o.id === obj.id; });
                if(updatedItem){
                  obj.quantity_on_hand = updatedItem.quantity_on_hand;
                  obj.quantity_pending = updatedItem.quantity_pending;
                }
              })
              };
          });
        }
        await this.$store.dispatch('pos/setSchemas','sale');                    // best we simply just load a fresh sale schema upon load of the terminal-order screen
        this.$store.dispatch('products/setSchemas','inventory');                // well prob need this for further engagements w pos order forms..

        Customer.find(this.customer_id).then(response => {                      // get customer or return to queue page
          this.customer = new Customer(response).withDefaults(this.schema,false);

          this.$store.commit('pos/setCustomerOrder', this.customer_id);         //record our current customer if user navigates away from terminal for when they go back.

          let pending = this.customer.sales.find(v=>v.status=='pending' && v.location_id==this.$store.state.disp.location);
          if(pending){                                                          // load last pending (was sorted by descending from EP) order if we have one.
            this.isProcessing = true;
            Sale.find(pending.id).then(response => {
              this.isProcessing = false;
              this.order = new Sale(response).withDefaults(this.schema,false);
              this.order.omit_rule_ids = [];
              this._updateOrder(); // update the order as we may have made changes to the discount rules / prices..
               this.$nextTick(() => this.$refs.scanAdd.focus()); //set focus to the barcode scan field
            }).catch(error => {
              this.isProcessing = false;
              //
            });
          }else{
            this.order = new Sale().withDefaults(this.schema);                  // load a sale object, with defaults (id is 0, upon first update, ti will persist)
            this.order.customer_id = this.customer.id;
            this.order.drawer_id = this.drawer.id;
            this.order.omit_rule_ids = [];
            this.$nextTick(() => this.$refs.scanAdd.focus()); //set focus to the barcode scan field
          }
          
          //this._fetchLookup(this.lookupCat);                                    // initiate inventory lookup
          this.isLoading = false;
        }).catch(error => {
            this.isLoading = false;
            this.$router.push({name:'terminal-queue'});
            this.$announcer({status:400,data:{message:'We had a hiccup fetching the customer record - Please try again later.'}});
        });
    },
    
    computed: {
      schema() {
        return this.$store.state['pos']['saleSchema'];
      },
      
      drawer(){
        return this.$store.state['pos']['drawer'];
      },
      
      limitLeft(){
        if(!this.customer) return 0;
        return this.customer.thc_limit_grams - this.customer.thc_limit_grams_used;
      },
      
      overLimit(){
        if(!this.order || !this.customer) return false;
        return (this.order.thc_equivalent_grams>this.limitLeft) ? true : false
      },
      
      overSold(){
        if(!this.order || !this.customer) return false;
        else if(!this.order.items) return false;
        
        return (this.order.items.find(v=>v.quantity > (v.inventory || {}).quantity_on_hand)) ? true : false;
      },

      dropdownCategories() {
        if (this.schema && this.schema.form) return _.sortBy(this.schema.form.lookup_categories,[category=>category.name.toLowerCase()]); //case insensitive
        return null;
      },
      
      connectedPrinter(){
        let deviceObj = this.$store.state['pos']['receiptPrinter'];
        if(deviceObj === null) return deviceObj;
        if(Object.entries(deviceObj).length === 0 && deviceObj.constructor === Object){
                this.$store.commit('pos/setReceiptPrinter',null); 
                return null;
        }
        return deviceObj;
      },

      orderItemsSorted() {
        //sort by group, then by name
        let sorted = this.order.items.sort((a,b)=>{
          let aGroup = Number(a.group_ref || 0);
          let bGroup = Number(b.group_ref || 0);
          if (aGroup<bGroup) return -1;
          if (aGroup>bGroup) return 1;
          if (!(a.inventory||{}).item_name || !(b.inventory||{}).item_name) return 0; //inventory name not loaded yet
          return b.inventory.item_name.localeCompare(a.inventory.item_name);
        });
        //put the group heading into the first item of each group
        let currentGroup;
        sorted.forEach(e=>{
          let group = Number(e.group_ref || 0);;
          if (group>0 && group!==currentGroup) {
            e.group_name=((this.productGroupNames||[]).find(e=>e.id===group)||{}).name;
            currentGroup=group;
          }
        });
        return sorted;
      }
    },
    
    methods: {
      
      scanIn: _.debounce(async function (e) {                                   // scan in an item
        this.isProcessing = true;
        this.scanned = await new Lookup().params({
            search: this.itemScan,
            is_scan:1
        }).get() || [];

        if(!this.scanned[0]){                                                   // did not fetch an item
          this.$announcer({status:400,data:{message:'Whoops - We could not locate any items - Try again?'}});
          this.isProcessing = false;
          this.itemScan = null; // reset
        }else if(this.scanned.length>1){                                        // multiple items were fecthed
          this.openOrderItem(this.scanned,'pick');
          this.isProcessing = false;
          this.itemScan = null; // reset
        }else if(this.scanned[0].unit_of_measure!='ea'){                        // open confirmed add item modal for weight input
          this.openOrderItem(this.scanned,'scan');
          this.isProcessing = false;
          this.itemScan = null; // reset
        }else{                                                                  // we have scanned 1 unweighed (ea) item successfully - add to cart.
          this.updateOrderItem({
            id: 0,
            is_confirmed:true,
            inventory_id: this.scanned[0].id,
            quantity: 1
          });
          this.itemScan = null; // reset
        }
      }, 300),

      searchLookup: _.debounce(function (cat) {                                 // lookup existing inventory..
        if(this.lookupSearch.length<=2 && this.lookupSearch.length) return false;
        
        this.lookupPage = 1;
        this._fetchLookup(cat);
      }, 500),

      async _fetchLookupCategories() {
        let r = await new LookupCategories()
                .params({
                  search: this.lookupSearch,
                  typ: 0,
                  is_search:1,
                  has_items:1
                })
                .orderBy('name')
                .limit(500)
                .page(this.lookupPage)
                .get();

        this.lookupCategories = r.data.filter(e=>e.products_count>0);

        console.log(this.lookupCategories);
      },

      async _fetchLookup(cat=0){
        if (cat===0 && this.lookupSearch==='') return; //nothing to search - we now show category tiles
        this.lookupCat = cat;
        this.isFinding = true;
        this.lookup = await new Lookup()
          .params({
            search: this.lookupSearch,
            typ: cat,
            is_search:1,
            archived: 0,
            is_avail:1
          })
          .orderBy('-updated_at')
          .limit(30) //should be divisible by 3 (tiles per row)
          .page(this.lookupPage)
          .get();
console.log(this.lookup);
          this.lookupGroupings = await new LookupGroupings()  // any active groupings
            .params({
              search: this.lookupSearch,
              type: cat,
              onlyGroupings:1
            })
            .orderBy('-updated_at')
            .limit(30) // align with max per page for inventory
            .page(this.lookupPage)
            .get();

          this.isFinding = false;
      },

      async fetchProductGroup(id) {
        let group = await new LookupGroupings()  // any active groupings
                .params({
                  id: Number(id),
                  onlyGroupings:1
                })
                .limit(1) // align with max per page for inventory
                .page(1)
                .get();
        if (group && group.data && group.data.length===1) {
          return group.data[0];
        }
        return null;
      },

      openPrinterSetupModal: function () {
        this.connectPrinterModal = true;
      },

      printerSetupAction: function(message) {
        this.connectPrinterModal = false; 
      },

      printReceipt (orderId) {
        if(this.connectedPrinter === null)
        {
            return false;
        }
        axios.get('/api/v1/admin/dispensary/sales/'+orderId+'/receipt/file').then(response =>{
          let receipt_file = null;
          const blob = window.atob(response.data.receipt_file)
          var len = blob.length;
          var bytes = new Uint8Array( len );
          for (var i = 0; i < len; i++)        {
              bytes[i] = blob.charCodeAt(i);
          }
          receipt_file = bytes;

          this.$refs.printer.print(receipt_file).then(() => {
             this.$announcer({status:200,data:{message:'Remember your receipt!'}});
            }).catch((messs) => {
              console.log(messs);
            });
          }).catch(error => {
            this.$announcer(error.response);
          });

      },
      
      printReceiptPDF (orderId) {

        axios.get('/api/v1/admin/dispensary/sales/'+orderId+'/receipt/html').then(response =>{
          this.receiptHtml = response.data;
            this.$nextTick(() => {
              document.getElementById('receipt_content').children[2].append(document.getElementById('receipt_barcode'));
              this.d = new Printd();
              this.d.print(document.getElementById('receipt_content'));
            });
          }).catch(error => {
            this.$announcer(error.response);
          });

      }, 

      printReceiptEmail (orderId) {

        axios.get('/api/v1/admin/dispensary/sales/'+orderId+'/receipt/email').then(response =>{
            this.$announcer({status:200,data:{message:'Receipt has been sent by email.'}});
          }).catch(error => {
            this.$announcer(error.response);
          });

      }, 


      openOrderItem: function(lines,type,skip=false){
        this.orderItemType = type;
        this.orderItemSkip = skip;
        this.orderItemLine = (type=='edit') ? lines[0] : null;                  // clear any old lineitem being processed - this would be a new one that has to be "confirmed"
        this.orderItemFound = (type=='pick' || type=='scan') ? lines : [];
        this.orderItemModal=!this.orderItemModal;
      },
      
      async openOrderGrouping(prod) {
        //todo need to pull existing qty into modal so user can update/adjust (not add) back to this sale
        if (typeof prod==='string') prod = await this.fetchProductGroup(prod);
        this.orderGrouping = prod;
        this.orderGroupingModal=!this.orderGroupingModal;
      },   

      closeOrderItem: function(){
        this.orderItemModal=!this.orderItemModal;
        this.$nextTick(() => this.$refs.scanAdd.focus()); // seems like this is what you would want to do after closing the modal
      },

      closeOrderGrouping: function(){
        this.orderGroupingModal=!this.orderGroupingModal;
        this.$nextTick(() => this.$refs.scanAdd.focus()); // seems like this is what you would want to do after closing the modal
      },

      updateOrderItem: function(line){
        if(!line) return false;

        let found = this.order.items.find(v=>v.inventory_id==line.inventory_id  && v.group_ref==line.group_ref);
        if(found){
          this.order.items[this.order.items.findIndex(v=>v.id==found.id)] = Object.assign({},found,{
            quantity: ((line.id) ? line.quantity : (Number(found.quantity) + Number(line.quantity))),
            is_confirmed: line.is_confirmed || found.is_confirmed,
            quantity_priced_at: line.quantity_priced_at,
          });                                                                   // if inventory item exists, simply rewrite with additional or overwrite if lineitem exists
        }else this.order.items.push(line);                                      // or, simply push new lineitem to items 

        this._updateOrder();
        this.orderItemModal=false;
        this.$nextTick(() => this.$refs.scanAdd.focus()); // seems like this is what you would want to do after adding an item
      },

      updateOrderGrouping: function(lines){
        for(let line of lines){
          let found = this.order.items.find(v=>v.inventory_id==line.inventory_id && v.group_ref==line.group_ref);
          if(found){
            this.order.items[this.order.items.findIndex(v=>v.id==found.id)] = Object.assign({},found,{
              quantity: ((line.id) ? line.quantity : (Number(found.quantity) + Number(line.quantity))),
              is_confirmed: line.is_confirmed || found.is_confirmed,
              quantity_priced_at: line.quantity_priced_at,
            });                                                                   // if inventory item exists, simply rewrite with additional or overwrite if lineitem exists
          }else this.order.items.push(line);                                      // or, simply push new lineitem to items 
        }

        this._updateOrder();
        this.orderGroupingModal=false;
        this.$nextTick(() => this.$refs.scanAdd.focus()); // seems like this is what you would want to do after adding an item
      },

      removeGroupItems(id) {
        if (this.order && this.order.items) this.order.items = this.order.items.filter(e=>e.group_ref!=id);
        this._updateOrder();
        this.$nextTick(() => this.$refs.scanAdd.focus()); // seems like this is what you would want to do after adding an item
      },

      removeOrderItem: function (id, event) {
        if(!this.order) return false;
        
        if(this.order.items && this.order.items.length > 0)
          this.order.items.splice(this.order.items.findIndex(v=>v.id==id), 1);
          
        this._updateOrder();
        this.$nextTick(() => this.$refs.scanAdd.focus()); // seems like this is what you would want to do after adding an item
      },
      
      applyDiscountRule: function (rid) {
        if(!rid) return false;
        
        let disc = this.order.discounts.find(v=>v.id==rid);
        if(!disc) return false;
        
        if(disc.discount_code) this.order.discount_code = disc.discount_code;   // update with discount code if exists, and rmeove from omit list if it was there before
        if(this.order.omit_rule_ids.indexOf(rid)!==-1) this.order.omit_rule_ids.splice(this.order.omit_rule_ids.indexOf(rid),1);

        this._updateOrder();
      },

      removeDiscountRule: function (rid) {
        if(!rid) return false;

        let disc = this.order.discounts.find(v=>v.id==rid);
        if(!disc) return false;
        
        if(disc.discount_code == this.order.discount_code) this.order.discount_code = null; // remove discount code in this case
        if(this.order.omit_rule_ids.indexOf(rid)===-1) this.order.omit_rule_ids.push(rid); // add to omit ids
    
        this._updateOrder();
      },
      
      async openPayment(){
        
        let withPin = (this.order.items.find(v=>v.is_confirmed===false)) ? await this.requirePin('There are Unconfirmed Items! - Please Enter an Admin PIN to Proceed') : true;
        if(withPin===false) return false;                                       // if we have uncomfirmed(scanned) items - require an admin pin to proceed!
        
        this.orderPaymentModal = !this.orderPaymentModal;
      },

      completePayment: function(pay){
        if(!(this.order || {}).items) return false;
        
        this.isProcessing = true;
        this.$store.dispatch('pos/setSchemas','sale');                    // best we simply just load a fresh sale schema upon load of the terminal-order screen
        this.$store.dispatch('products/setSchemas','inventory');                // well prob need this for further engagements w pos order forms..
        axios.post('/api/v1/admin/dispensary/sales/'+this.order.id+'/payment',pay).then(response =>{
          this.receiptBarcode = this.order.order_number;
          this.printReceipt(this.order.id);   
          this.$store.dispatch('pos/getCurrentDrawer');                         // re-load current users drawer as a sale has been tendered
          this.order = new Sale().withDefaults(this.schema);                    // reset with a new sale object, with defaults, in case budtender wants to ring up a new transaction.
          this.order.customer_id = this.customer.id;
          this.order.drawer_id = this.drawer.id;
          this.isProcessing = false;
        }).catch(error => {
          this.isProcessing = false;
          this.$announcer(error.response);
        });
      },

      getFormattedOuncesFromGrams: function (grams, withParens) {
        const ounces = Math.floor(grams / 28);
        const rem = grams % 28;
  
        let formatted = '';
  
        if (rem >= 24.5) { formatted += '&frac78;'; }
        else if (rem >= 21) { formatted += '&frac34;'; }
        else if (rem >= 17.5) { formatted += '&frac58;'; }
        else if (rem >= 14) { formatted += '&frac12;'; }
        else if (rem >= 10.5) { formatted += '&frac38;'; }
        else if (rem >= 7) { formatted += '&frac14;'; }
        else if (rem >= 3.5) { formatted += '&frac18;'; }
  
        if (!ounces && !formatted) {
          return grams+'g ';
        }
  
        // if (ounces > 1 || (ounces === 1 && formatted)) { formatted += ' ounces'; }
        // else { formatted += ' ounce'; }
        formatted += ' oz';
  
        if (ounces > 0) { formatted = ounces + '' + formatted; }
  
        if (withParens) { formatted = '' + formatted + ''; }
  
        return formatted;
      },

      getOrderItemThcString: function (item) {
        let thc = '';
        
        /*let thcAmount = 0;

        if(item.inventory.product.nature_type=='noncannabis') item.thcAmount = 0;
        else if(!item.inventory.product.category) item.thcAmount = item.quantity;
        else{
          if (item.inventory.product.category.equivalency_type=='flower') {
              thcAmount = Number(item.inventory.amount_unit * item.inventory.product.category.settings.thc_equiv_ratio * item.quantity).toFixed(2);
          } else if (item.inventory.product.category.equivalency_type == 'edible') {
              thcAmount = Number((item.inventory.weight_potency / 1000) * item.quantity * item.inventory.product.category.settings.thc_equiv_ratio).toFixed(2);
          } else {
              if (item.inventory.unit_of_measure == 'ea')
                thcAmount = Number((item.inventory.weight_potency / 1000) * item.quantity * item.inventory.product.category.settings.thc_equiv_ratio).toFixed(2);
              else
                thcAmount = Number(item.quantity * item.inventory.product.category.settings.thc_equiv_ratio).toFixed(2);
          }
        }*/
        let thcAmount = item.thc_equivalent_grams;
        
        thc = 'Equivalent to ' + thcAmount + 'g';
        return thc;
      },     
      
      voidOrder(){
        //if(!this.order) return false;
        this.$swal.fire({
          title: 'Are you sure?',
          text: 'This will Clear the current Pending Order '+this.order.order_number+' for '+(this.customer.first_name || this.customer.alias),
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Void/Clear Order',
        }).then((result) => {
          if(result.value){
            this.isProcessing = true;
            if (!this.order || !this.order.id) {
              this.$store.commit('pos/setCustomerOrder', null);
              this.$router.push({name:'terminal-queue'});
            } else {
              axios.get('/api/v1/admin/dispensary/sales/'+this.order.id+'/void').then(response =>{
                // this.order = new Sale().withDefaults(this.schema);                  // load a sale object, with defaults (id is 0, upon first update, ti will persist)
                // this.order.customer_id = this.customer.id;
                // this.order.drawer_id = this.drawer.id;
                this.$announcer({status:200,data:{message:'Order has been Cleared - You may start again.'}});
                // this.isProcessing = false;
                this.$store.commit('pos/setCustomerOrder', null);
                this.$router.push({name:'terminal-queue'});
              }).catch(error => {
                this.isProcessing = false;
                this.$announcer(error.response);
              });
            }
          }else{
            //
          }
        });        
      },
      
      reQueue(){
          axios.get('/api/v1/admin/dispensary/customersqueue/add/'+this.customer.id).then(response =>{
            this.$store.commit('pos/setCustomerOrder', null);

            this.$router.push({name:'terminal-queue'});
            this.$announcer({status:200,data:{message:'Customer has been added back to the Queue.'}});
          }).catch(error => {
            if (error.response.status === 422 && error.response.data.message.indexOf('already') > 0) { // would prefer to have json response or some other unique status code to figure this out
              this.$store.commit('pos/setCustomerOrder', null);
              this.$router.push({name:'terminal-queue'});
            } else {
              this.$announcer(error.response);
            }
          });
      },

      async _fetchProductGroupNames() {
        if (!this.productGroupNames) {
          let groups = await new LookupGroupings()  // any active groupings
                  .params({
                    onlyGroupings:1
                  })
                  .limit(9999)
                  .page(1)
                  .get();

          if (groups && groups.data) this.productGroupNames = groups.data.map(e=>{return { id: e.id, name: e.name }});
        }
      },

      _updateOrder(){                                                           // loop the order data to the backend, where we will retrieve latest calculations
        if(!this.order) return false;
        
        this.isProcessing = true;
        this.isDirty = false;
        if(this.order.schema) delete this.order.schema;
        if(this.order.customer) delete this.order.customer;

        this.order.save().then(response =>{
          this._fetchProductGroupNames();
          this.order = response;
          this.isProcessing = false;
        })
        .catch(error => {
          this.isDirty = true;
          this.$announcer(error.response);
          this.isProcessing = false;
        });
      },
    },

    watch: {
      lookupPage(newValue, oldValue) {
        this._fetchLookup(this.lookupCat);
        this.$refs['pos-inventory-search'].scrollTop = 0;
      }
    },

    filters: {
      cleanCategoryName(s) {
        if (s==='all') s='All';
        return s.replace(/_/g, ' ');
      }
    }
  }


</script>
<style scoped>

  .main-panel {
    background-color: #777777;
  }

  .pos-order-column {
    background-color: #ffffff;
  }

  .pos-content {
    padding-top: 0;
  }

  .pos-inventory-search {
    height: calc(100vh - 250px);
    bottom: 0px;
    top: 0;
    overflow-x: hidden;
    overflow-y: auto;
    padding: 10px 20px 30px 10px;
  }

  .pos-input-inventory-search {
      box-shadow: 0px 2px 22px 0 rgba(0, 0, 0, 0.2), 0px 2px 30px 0 rgba(0, 0, 0, 0.35);
  background-image: -webkit-linear-gradient(left, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
  background-image: -moz-linear-gradient(left, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
  background-image: -o-linear-gradient(left, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
  background-image: linear-gradient(to right, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
      bottom: 0;
      overflow-x: hidden;
      overflow-y: hidden;
      margin: 0;
    }

  .overlay {
    position : absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 2;
  }

  .weight-modal p {
    margin-bottom: 5px;
  }

  .weight-modal .modal-body {
    padding-top: 10px;
    padding-right: 30px;
  }

  .form-control-weight {
    padding: 5px 10px;
    line-height: 34px;
    font-size: 20px;
    border-radius: 10px;
    margin: 0 5px;
    width: 200px;
  }

  .btn-weigh-pad {
    font-size: 24px;
    height: 60px;
    min-width: 60px;
    width: 60px;
    border-radius: 30px;
    padding: 0;
    margin: 4px;
  }

  .btn-weigh-pad-action {
    font-size: 30px;
    padding-top: 10px;
    padding-right: 2px;
  }
  
  .sh-red{
    color:red;
  }

  .pos-order-nav {
    background-color: #ffffff;
    overflow-x: hidden;
  }

  .pos-order {
    height: calc(100vh - 357px);
    top: 0;
    bottom: 120px;
    overflow-y: auto;
    padding: 0;
    margin: 0;
  }

  .pos-order-totals {
    background-color: #ffffff;
    bottom: 0;
    padding: 0;
    box-shadow: 0 2px 22px 0 rgba(0,0,0,0.5);
    
  }

  .pos-order-list-group .list-group-item {
    padding: 5px 10px;
  }

  .pos-order-list-group .list-group-item-sub {
    padding: 2px 10px; 
    margin: 0;
    font-size: 12px;
  }

  .list-group-item-total-cost {
    font-size: 22px;
  }

  .list-group-item-thc {
    font-size: 12px;
    background-color: rgba(24, 206, 15, .6);
  }

  .list-group-item-thc-over {
    font-size: 12px;
    background-color: rgba(255, 0, 0, .6);
  }

  .list-group-item:first-child {
    border-top-right-radius: 0;
    border-top-left-radius: 0;
  }
  .list-group-item:last-child {
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
  }

  .item-img-container {
    width: 70px;
    max-height: 70px;
    overflow: hidden;
    display: block;
    margin-right: 10px;
  }

  .order-item-input {
    font-size: 12px;
    width: 74px;
    height: 30px;
    margin: 0 5px;
  }

  .order-item-description {
    font-size: 1em;
    font-weight: 300;
    margin: 0;
    padding: 0;
  }

  .order-item-thc {
    font-size: 12px;
    color: rgba(24, 206, 15, .8);
  }

  .btn-order-item-delete {
    font-size: 15px;
    height: 30px;
    min-width: 30px;
    width: 30px;
    border-radius: 15px;
    padding: 5px 0 0 0;
    margin: 0 10px 0 0;
  }

  .customer-modal .modal-body {
    padding-top: 0;
  }

  .img-profile-sm {
    width: 40px;
    max-width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto;
    margin-right: 10px;
  }

  .customer-table-wrap {
    height: 200px;
    overflow-y: auto;
    margin-bottom: 15px;
  }

  .new-customer-label {
    font-size: 0.8571em;
    margin-bottom: 0;
    color: #9A9A9A;
  }
  
  .sh-red{
    color:red;
  }
  .sh-green{
    color:green;
  }
  
  .larger{
    font-size:1.5em;
    font-weight:700;
  }

  .form-control-pos-keypad {
    padding: 0 10px;
    height: 50px;
    min-height: 50px;
    line-height: 18px;
    font-size: 16px;
    color: #ffffff;
    border-radius: 28px;
    width: 200px;
    margin: 0;
  }
  .input-group-append-pos-keypad{
    height:50px;
  }
  .form-control-pos-keypad:focus{
    background-image: -webkit-linear-gradient(left, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
    background-image: -moz-linear-gradient(left, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
    background-image: -o-linear-gradient(left, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
    background-image: linear-gradient(to right, rgba(119, 115, 115, 0.8), rgba(113, 100, 100, 0.6));
  }

  .form-control-pos-keypad::placeholder {
    color: #cccccc;
    opacity: 1;
  }

  .btn-pos-pad-pay {
    font-size: 22px;
    line-height: 26px;
    min-width: 100px;
    width: 115px;
    border-radius: 15px;
    /* padding: 0; */
    /* margin: 0; */
  }

  .btn-pos-pad-void {
    font-size: 22px;
    line-height: 26px;
    border-radius: 15px;
    /* padding: 0; */
    /* margin: 0; */
  }
  
  .lookup-input{
    height:45px;
    font-size:1.2em;
  }
  
  .lookup-prepend{
    background-color: #c6c8ca !important;
  }
  
  .discount-info{ 
    font-size: 1.8em;
    color: #FFF;
    font-weight: 700;
  }

    .pos-inventory-search .dropdown-menu {
      height: auto;
      max-height: 400px;
      overflow-x: hidden;
    }

    .clear-category {
      font-weight: bold;
      font-size: smaller;
      color: #484242;
      margin: auto;
      cursor: pointer;
      padding-right: 5px;
    }

  >>> .card.pos-inventory-card {
    display: flex;
    height: calc(100% - 20px);
  }

  >>> .card.pos-inventory-card .card-body {
    margin-top: auto;
    flex: none;
  }

  .row.search-contain.is-finding {
    opacity: .5;
  }
</style>
