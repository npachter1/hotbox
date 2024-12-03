<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>

                <div class="card-body">
                    
                    <div v-if="caller!='grid'">
                        <span class="description" v-if="item.metrc_tag">
                            - Metrc Tag: {{ item.metrc_tag }}<br>
                        </span>
                        <span class="description" v-if="item.product">
                            - Product: {{ item.product.name }}<br>
                        </span>
                        <span class="description" v-if="item.receiving">
                            - Vendor: {{ (item.receiving.vendor || {}).name }}<br>
                        </span>
                        <span class="description" v-if="item.created_at">
                            - Received on: {{ item.created_at | localDate }}
                        </span>
                    </div>

                    <b-tabs content-class="mt-3" v-model="activeTab">
                        <b-tab title="Make an Adjustment" ref="tabAdjust" :style="{minHeight: tabMinHeight + 'px'}">
                            <div class="mt-3 mb-4">

                                <div v-if="item.product" class="form-group form-row row gutters mt-4 mb-2">
                                    <div class="col-12 col-sm-12 clearfix mb-2">
                                        <form-number v-model="item.cost_unit" :schema="schema.form.cost_unit" />
                                    </div>
                
                                    <div v-if="['flower'].indexOf((item.product.category || {}).equivalency_type)!==-1 && item.unit_of_measure!='g'" class="col-12 mt-3 mb-2">
                                        <form-number v-model="item.amount_unit" :schema="schema.form.amount_unit" class="">
                                            <template slot="text-right"><span class="float-right"><b>{{ (item.product.category || {}).equivalency_type | renderValue(catSchema.form.equivalency_type.values) }}</b></span></template>
                                        </form-number>
                                    </div>
                                    <div v-else-if="['edible'].indexOf((item.product.category || {}).equivalency_type)!==-1" class="col-12 mt-3 mb-2">
                                        <form-number v-model="item.weight_potency" :schema="schema.form.weight_potency" class="">
                                            <template slot="text-right"><span class="float-right"><b>{{ (item.product.category || {}).equivalency_type | renderValue(catSchema.form.equivalency_type.values) }}</b></span></template>
                                        </form-number>
                                    </div>
                                    
                                    <form-priceset
                                            v-model="item.priceset_id"
                                            :schema="schema.form.priceset_id"
                                            :inv="item" class="col-12 mt-3 mb-2 clearfix"
                                            @fixedPrice="(val)=>{item.retail_unit=val}"
                                            :fixed-price="item.retail_unit"/>
                                    <!--<form-number v-if="!item.priceset_id" v-model="item.retail_unit" :schema="schema.form.retail_unit" :hideLabel="true" class="col-12 mb-3" />-->
                
                                    <form-select v-model="item.item_strain" :schema="schema.form.item_strain" class="col-12 mt-2 mb-2">
                                        <template slot="text-right"><a href="" class="float-right" @click.prevent="strainModal=!strainModal">Manage Strains &raquo;</a></template>
                                    </form-select>
                
                                    <div class="col-12 col-sm-12 mt-3">
                                        <form-datetime v-model="item.expires_at" :schema="schema.form.expires_at" />
                                    </div>
                
                                </div>
            
                                <hr>
                                <div v-if="item.product" class="form-group form-row row gutters">
                
                                    <label class="col-md-2 col-form-label">
                                        <b>Adjust +/-</b><br>
                                        <a href=""
                                          v-if="item.quantity_received!=item.quantity_on_hand"
                                          @click.prevent="viewLogModal(item.id)">
                                          <b><i class="hotbox-icon hotbox-icon-time-clock"></i> View History</b>
                                        </a>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="row description">
                                          <div class="col-4 col-sm-2">Received:</div>
                                          <div class="col-8 col-sm-10">{{item.quantity_received}}</div>
                                          <div class="col-4 col-sm-2">Sold:</div>
                                          <div class="col-8 col-sm-10">-{{item.quantity_sold}}</div>
                                          <div class="col-4 col-sm-2">Adjusted:</div>
                                          <div class="col-8 col-sm-10">{{compute_adjust}}</div>
                                          <div class="col-12">
                                              <form-number v-model="item.adjustment.value" :schema="schema.form.adjustment.properties.value" :hideLabel="true" />
                                          </div>
                                          <div class="col-4 col-sm-2 totals-row clearfix" style="clear:both !important;"><b>=OnHand</b></div>
                                          <div class="col-8 col-sm-10 totals-row">
                                              <b :class="{'show-red':this.compute_onhand!=this.item.quantity_on_hand}">{{compute_onhand}}</b><span class="small">/{{ item.unit_of_measure }}</span>
                                          </div>
                                        </div>        
                              
                                       <transition name="hb-fade">
                                        <div v-show="item.adjustment.value!=0" class="mt-2 mb-3">
                                            <form-select v-model="item.adjustment.reason_code" :schema="schema.form.adjustment.properties.reason_code" />
                                            <form-textarea v-model="item.adjustment.notes" :schema="schema.form.adjustment.properties.notes" :rows="schema.form.adjustment.properties.notes.rows || 3" />
                                        </div>
                                       </transition>
                                    </div>
                        
                                </div>
                            </div>
                        </b-tab>
                        <b-tab title="Do a Conversion" ref="tabConvert">
                            <div v-if="item_package" class="mt-3 mb-4">

                                <div class="row description">
                                      <div class="col-4 col-sm-3">Received:</div>
                                      <div class="col-8 col-sm-9">{{item.quantity_received}}</div>
                                      <div class="col-4 col-sm-3">Sold:</div>
                                      <div class="col-8 col-sm-9">-{{item.quantity_sold}}</div>
                                      <div class="col-4 col-sm-3">Adjusted:</div>
                                      <div class="col-8 col-sm-9">{{ (compute_adjust>0) ? '+' : '' }}{{compute_adjust}}</div>
                                      <div class="col-4 col-sm-3"><b>Available to Convert:</b></div>
                                      <div class="col-8 col-sm-9">
                                          <b>{{compute_onhand}}<span class="small">{{ item.unit_of_measure }}</span></b> 
                                          <span v-if="(item_package || {}).uom=='g' && item.unit_of_measure=='ea'" class="small"><i>({{ item.quantity_on_hand * item.amount_unit }}g total)</i></span>
                                      </div>
                                      <div class="col-4 col-sm-3"><b>Converting Into (new items):</b></div>
                                      <div class="col-8 col-sm-9">&nbsp;</div>
                                      <div class="col-12 mt-2 mb-2 py-1" style="border-top:1px solid #E6E6E6; border-bottom:1px solid #E6E6E6">
                                        <product-search promptQty promptNew :includeTypes="[(item.product || {}).nature_type]" :existIds="item_conversion_prodIds" :scoped="'conversion'" @add="includeConversionProduct" @new="openConversionProduct(0,'create')" class="mt-2 mb-1" />
                                        <b-table v-if="item.conversions" striped hover
                                                class="b-table-condensed table-nested table-condensed table-small"
                                                ref="item_conversions"
                                                id="item_conversions_table"
                                                :items="item.conversions"
                                                :fields="receivingSchema.form.items.fields.filter(v=>['quantity_requested'].indexOf(v.key)===-1)"
                                                :busy.sync="isLoading"
                                                :show-empty="true"
                                                :per-page="20"
                                                primary-key="id"
                                                :tbody-transition-props="{name:'hb-list-fade'}"
                                                responsive="md"
                                                stacked="sm"
                                            >
                                        
                                        <template v-for="(field,ind) in receivingSchema.form.items.fields" v-slot:[renderColumnHeader(field.key)]="column">
                                          <em>
                                            <i v-if="field.icon" :class="field.icon"></i> 
                                            <i v-if="field.description" class="hotbox-icon hotbox-icon-c-question" :title="field.label" v-b-tooltip.hover="field.description"></i> 
                                            <span>{{ column.label }}</span>
                                          </em>
                                        </template>
                                        
                                        <template v-slot:cell(item_barcode)="row">
                                            <span v-if="row.item.product">
                                                <li class="d-flex justify-content-between align-items-center position-relative">
                                                    <img :src="getProductImageUrl(row.item)" class="float-left px-2" width="50">
                                                    <div>
                                                        <a href="" class="" @click.prevent="openConversionProduct(row.item.product.id,'edit')">{{ row.item.product.name }}</a><br>
                                                        <span class="small">{{ row.item.item_strain }}</span>
                                                    </div>
                                                    <!--<div class="ml-auto">
                                                        <i class="mr-2 mt-1" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}" @click="row.toggleDetails"></i>
                                                    </div>-->
                                                </li>
                                            </span><span v-else>--</span>
                                        </template>
                
                                        <template v-slot:cell(cost_unit)="row">
                                            <span v-if="row.item.product">
                                                <form-number v-model.number="row.item.cost_unit" :schema="receivingSchema.form.items.properties.cost_unit" :hideLabel="true" class="mt-0 mb-0" />
                                            </span><span v-else>--</span>
                                        </template>
                                                
                                        <template v-slot:cell(priceset)="row">
                                            <div v-if="row.item.product" style="height:38px;">
                                                <form-priceset
                                                        @fixedPrice="(val)=>{row.item.retail_unit=val}"
                                                        :fixed-price="row.item.retail_unit"
                                                        v-model="row.item.priceset_id"
                                                        :schema="receivingSchema.form.items.properties.priceset_id"
                                                        :inv="row.item"
                                                        :hideLabel="true"
                                                        @updateUnit="(upd) => row.item.unit_price = upd"/>
                                                <!--<form-number v-if="!row.item.priceset_id" v-model.number="row.item.retail_unit" :schema="receivingSchema.form.items.properties.retail_unit" :hideLabel="true" class="mt-0 mb-0" />-->
                                            </div>
                                            <span v-else>--</span>
                                        </template>
                                        
                                        <template v-slot:cell(amount_unit)="row">
                                          <div v-if="row.item.package">
                                              <div v-if="(row.item.package||{}).uom==='g'" class="package-type">
                                                  <package-type
                                                          v-if="row.item.packageType"
                                                          v-model="row.item.packageType"
                                                          @input="changePackageType($event,row.item)"
                                                          :package-type-options="packageTypeOptions"
                                                            :amount="row.item.amount_unit.toString()"
                                                            :unit="row.item.package.uom">
                                                  </package-type>
                                              </div>
                                              <div v-else-if="((row.item.product || {}).category || {}).equivalency_type=='flower' && row.item.unit_of_measure!=row.item.package.uom" class="input-group form-group">
                                                <input :name="'item_amount_unit_'+row.index"
                                                       v-model.number="row.item.amount_unit"
                                                       v-validate="'required'"
                                                       :class="{'input': true,'form-control':true,'text-danger': errors.has('item_amount_unit_'+row.index) }"
                                                       type="number"
                                                       placeholder=""
                                                       step="any"
                                                       min="0.0001"
                                                       @keyup="">
                                                  <div class="input-group-append">
                                                    <span class="input-group-text">/{{ row.item.package.uom }} = 1/{{row.item.unit_of_measure}}</span>
                                                  </div>                        
                                                <span v-show="errors.has('item_amount_unit_'+row.index)" class="form-text text-muted text-danger full-width">{{ errors.first('item_amount_unit_'+row.index) }}</span>
                                              </div>
                                              <!--<div v-else-if="['flower'].indexOf(((row.item.product || {}).category || {}).equivalency_type)!==-1">-->
                                              <div v-else-if="['flower'].indexOf(((row.item.product || {}).category || {}).equivalency_type)!==-1">
                                                  <!--<span class="small"><i>1{{ row.item.package.uom }} = {{ row.item.amount_unit}}{{row.item.unit_of_measure}}</i></span>-->
                                                  x
                                              </div>
                                              <div v-else-if="['concentrate','edible'].indexOf(((row.item.product || {}).category || {}).equivalency_type)!==-1">
                                                  <form-number v-model.number="row.item.weight_potency" :schema="receivingSchema.form.items.properties.weight_potency" :hideLabel="true" class="mt-0 mb-0" />
                                              </div>
                                              <div v-else>--</div>
                                          </div>
                                          <div v-else> -- </div>
                                        </template>
    
                                        <template v-slot:cell(quantity_received)="row">
                                            <div class="position-relative">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend"><i v-if="receivingSchema.form.items.properties.quantity_received.prepend" :class="receivingSchema.form.items.properties.quantity_received.prepend"></i></div>
                                                    <input class="input form-control" v-model.number="row.item.quantity_received" type="number" :min="0" :step="1" :name="'items_quantity_received_'+row.index">
                                                    <div v-if="(row.item.product || {}).nature_type!='noncannabis'" class="input-group-append">
                                                        <span v-if="(row.item.packageType||{}).value==='package'">/ea</span>
                                                        <span v-else class="input-group-text">
                                                            <a class="dropdown-toggle py-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">/{{ row.item.unit_of_measure }}</a>
                                                            <div class="dropdown-menu tight">
                                                                <a v-for="(val,vid) in receivingSchema.form.items.properties.unit_of_measure.values.filter(e=>e.id!=='ea')" :key="val.id" class="dropdown-item action-default" href="" @click.prevent="row.item.unit_of_measure=val.id">
                                                                    {{ val.name }}
                                                                </a>
                                                            </div>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        
                                        <template v-slot:cell(actions)="row">
                                            <i v-if="row.item.can_delete" class="hotbox-icon hotbox-icon-trash-round" @click="removeConversionItem(row.index)"></i>
                                        </template>
                
                
                                        <template v-slot:row-details="row" class="divide-row">
                                            <div class="col-12 mt-1 mb-4">
                                                &nbsp;<br />
                                            </div>
                                        </template>
                
                                        <template v-slot:table-caption v-if="item.conversions.length">
                
                                        </template>
                                                
                                        <template v-slot:empty>
                                            <div v-if="!isLoading" class="text-center py-3">
                                                <img src="/images/logo.png" alt="No Results" class="" width="65" />
                                                <h5 class="small">No Conversions selected - Please Browse or add item(s) above.</h5>
                                            </div><div v-else class="h-100">&nbsp;</div>
                                        </template>
                            
                                        </b-table>
                                       
                                       </div>
                                       <div class="col-4 col-sm-3 clearfix" style="clear:both !important;"><b>=Remaining After</b></div>
                                       <div class="col-8 col-sm-9">
                                           <b>{{compute_conversion_remaining}}</b><span class="small">{{ item.unit_of_measure }} <i class="hotbox-icon hotbox-icon-pencil" @click="convertWithAdjust=!convertWithAdjust"></i></span>
                                       </div>
                                       <div v-if="convertWithAdjust" class="col-4 col-sm-3 clearfix" style="clear:both !important;"><b>Adjust Remaining?</b></div>
                                       <div v-if="convertWithAdjust" class="col-8 col-sm-9">
                                            <form-number v-model="item.adjustment.value" :schema="schema.form.adjustment.properties.value" :hideLabel="true" class="col-12" />
                                            <transition name="hb-fade">
                                                <div v-show="item.adjustment.value!=0" class="mt-2 mb-3">
                                                    <form-select v-model="item.adjustment.reason_code" :schema="schema.form.adjustment.properties.reason_code" />
                                                    <form-textarea v-model="item.adjustment.notes" :schema="schema.form.adjustment.properties.notes" :rows="schema.form.adjustment.properties.notes.rows || 3" />
                                                </div>
                                            </transition>
                                       </div>

                                </div>        
                                


                            </div>
                            <div v-else="item_package" class="mt-3 mb-4 py-4 text-center">
                                *This item did not have a package when received, or the receiving file was archived.  You can only convert items with a registered package.
                            </div>
                        </b-tab>
                    </b-tabs>
                    
                    <div class="col-12 clearfix mt-3 mb-2 text-center">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                         <a v-if="caller!='grid'" @click.default="$router.push({name:caller,params:{keepAlive:true}});" class="btn btn-sm btn-light">Return.</a>
                         <a v-else @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                    </div>
                    
                </div>
            
        </fieldset>
    </form>
    
        <b-modal centered ref="logModal"
            v-model="logModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="logModal=!logModal"></i>
              <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-time-clock"></i> View Inventory Log</h5>
            </template>
          
              <log-modal v-if="logModal"
                :id="logModalId"
                @refresh="logModal=!logModal">
              </log-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="logModal=!logModal">Close</span>
            </template>
        </b-modal>
        
        <b-modal centered ref="strainModal"
            v-model="strainModal"
            size="xl"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="strainModal=!strainModal"></i>
              <h5 class="w-100 mb-0 text-center">Manage Strains</h5>
            </template>
          
              <strain-modal v-if="strainModal"
                @refresh="strainModal=!strainModal">
              </strain-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="strainModal=!strainModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="productModal"
            v-model="productModal"
            size="xl"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal fa-times" @click="productModal=!productModal"></i>
              <h5 class="w-100 mb-0 text-center">{{ productModalType | ucwords }} Product</h5>
            </template>
          
              <product-modal v-if="productModal"
                :id="productModalId"
                :type="productModalType"
                :init="productModalInit"
                @update="includeConversionProduct"
                @sync="(upd) => syncProduct(upd,item.items.find(v=>(v.package || {}).id==productModelItemId))">
              </product-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="productModal=!productModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="packageModal"
                 v-if="((item.conversions[item.conversions.findIndex(e=>e.id===packageModalItemId)] || {}).package || {}).uom"
                 v-model="packageModal"
                 size="sm"
                 @shown="$refs.packageAmount.focus()"
                 header-bg-variant="light"
                 header-text-variant="primary">

            <template slot="modal-header">
                <i class="modal-top-close fal fa-times" @click="packageModal=!packageModal"></i>
                <h5 class="w-100 mb-0 text-center">Amount/Package</h5>
            </template>
            {{ (receivingSchema.form.items.properties.unit_of_measure.values.find(e=>e.id===(((item.conversions[item.conversions.findIndex(e=>e.id===packageModalItemId)] || {}).package || {}).uom || 'g')) || {}).name }} per package:
            <input
                    v-if="packageModalItemId"
                    type="number"
                    autofocus
                    ref="packageAmount"
                    min="0"
                    v-validate="'required|decimal:4'"
                    name="amount"
                    v-on:keyup.enter="()=>{if (!(errors.items||{}).length) packageModal=!packageModal}"
                    v-model="item.conversions[item.conversions.findIndex(e=>e.id===packageModalItemId)].amount_unit"
            >

            <template slot="modal-footer">
                <span :class="{disabled:(errors.items||{}).length>0}" class="btn-label btn-sm btn-light float-right" @click="()=>{if (!(errors.items||{}).length) packageModal=!packageModal}">Close</span>
            </template>
        </b-modal>

    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Inventory';
    import FormPriceset from './formPriceset';
    import LogModal from './logModal';
    import StrainModal from './strainModal';
    import ProductModal from '../../products/product/productModal';
    import ProductSearch from '../../products/product/productSearch';
    import PackageSearch from '../../products/product/packageSearch';
    import PackageType from '../../products/receiving/packageType';

    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Inventory'
            },
            module: {
                type: String,
                default: 'products',
            },
            filters: {
                type: Object,
                default: () => {}
            },
            caller:{
                type: [Number, String],
                default: 'inventory'
            }
        },
        
        data(){
            return {
                isLoading:false,
                item: null,
                itemState: 'save',
                showNormalizedAmount:false,
                logModal:false,
                logModalId:0,
                strainModal:false,
                activeTab: 0,
                tabMinHeight: 0,
                conversionsSortBy:null,
                conversionsOrderDesc:false,
                productModal:false,
                productModalInit:null,
                productModalId:0,
                productModelItemId:null,
                productModalType:'create',
                packageModal: false,
                packageModalItemId: null,
                packageTypeOptions: [
                    {name: 'Deli-Style', value: 'deli'},
                    {name: 'Prepackaged', value: 'package'},
                ],
                packageEditModal:false,
                packageEditItem: null,
                packageEditItemId:null,
                convertWithAdjust:false
            };
        },
        
        components : {
            LogModal,FormPriceset,StrainModal,ProductModal,ProductSearch,PackageSearch,PackageType
        },
        
        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.item.weight_potency = (this.item.weight_potency*1000) || 1000;
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.item.weight_potency = 1000;
                this.isLoading = false;
            }
        },
        
        methods: {
            async autoSave(confirm=false){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                
                let withPin = await this.requirePin('Please Enter an Admin PIN to modify this Item');
                if(withPin===false) return false;                               // an adminpin couldnt be validated HINT add error message here if desired.

                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.itemState = 'saving..';
                        this.item.weight_potency = this.item.weight_potency/1000;
                        this.item.conversions = this.item.conversions.map(v=>{ v.weight_potency = v.weight_potency/1000; return v;}); // convert potency to g for storage
                        this.item.save().then(response => {
                            if(confirm){
                                this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                                if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                                
                                if(this.caller=='grid'){
                                    if(this.item.conversions.length) this.$emit('scopesearch',(this.item_package || {}).label);
                                    this.$emit('refresh',response); // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
                                    this.$emit('toggle');   
                                }else this.$router.push({name:this.caller,params:{filters:this.filters}});
                            }
                            this.itemState = 'saved';
                        }).catch(error => {
                            this.$announcer(error.response);
                            this.itemState = 'resave';
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },

            openConversionProduct(pid,typ='create',data=null){
                
                this.productModalId = pid;
                this.productModalType = (pid) ? 'edit' : typ;
                this.productModalInit = data;
                this.productModelItemId = (data || {}).id || null;

                this.productModal = !this.productModal;
            },
            
            includeConversionProduct(upd,src='select'){
                if(!upd) this.productModal=!this.productModal;
                
                if(this.item.conversions.find(v=>(v.product || {}).id==upd.id)){
                    this.item.conversions = this.item.conversions.map(o=>{
                        if((o.product || {}).id==upd.id) o.product = upd;
                        return o;
                    });
                    this.$refs.item_conversions.refresh(); // btable needs to refresh for some reason..
                }else{
                    let uom = (upd.inv_meta) ? upd.inv_meta.uom : (upd.nature_type=='noncannabis') ? 'ea' : ((upd.category || {}).equivalency_type=='flower') ? 'g' : 'ea';
                    this.item.conversions.push({
                        id:upd.id+'_'+(this.item.conversions.length*1)+1,
                        product:upd,
                        package:this.item_package,
                        priceset_id:(upd.inv_meta) ? upd.inv_meta.priceset : null,
                        metrc_tag:(this.item_package || {}).label || null,
                        product_id:upd.id,
                        item_barcode:null,
                        item_strain: (upd.inv_meta) ? upd.inv_meta.strain : null,
                        unit_of_measure: uom,
                        amount_unit:(upd.inv_meta) ? upd.inv_meta.amount_unit : 1,
                        weight_potency:(upd.inv_meta) ? (upd.inv_meta.weight_potency*1000) : 1000,
                        packageType: _.clone(this.packageTypeOptions.find(e=>e.value===(uom==='ea' ? 'package' : 'deli'))),
                        cost_unit:(upd.inv_meta) ? upd.inv_meta.cost : 0.01,
                        retail_unit:(upd.inv_meta) ? upd.inv_meta.retail : 0.02,
                        expires_at:null,
                        quantity_requested: (upd.nature_type=='noncannabis') ? upd.add_qty || 1 : 0,
                        quantity_received:upd.add_qty || 1,
                        quantity_split:((upd.add_qty || 1)/2),
                        unit_price:0,
                        can_delete:true,
                        //_showSplit:false,
                        _showDetails:false
                    });
                
                }
                
                this.conversionsSortBy = null;                                        // reset sortBy as latest push is at end and user might want to resort.
                this.productModal=false;
            },

            removeConversionItem(ind){
                
                let cur = this.item.conversions[ind];
                
                this.item.conversions.splice(ind, 1);

                this.item.conversions.map(o=>{                                        // other rows of same package need reset their qty requested
                    if(o.metrc_tag==(cur || {}).metrc_tag) o.quantity_requested = (o.package || {}).quantity || o.quantity;
                    return o;
                });

            },

            changePackageType(newValue, item){

                if(item.packageType.value==='package') {
                    item.unit_of_measure='ea';
                    this.packageModalItemId=item.id;
                    this.packageModal=true;
                } else {
                    item.amount_unit = item.product?.inv_meta?.amount_unit ? item.product.inv_meta.amount_unit : 1;
                    item.unit_of_measure = item.product?.inv_meta?.uom && item.product.inv_meta.uom!=='ea' ? item.product.inv_meta.uom : 'g';
                    item.package.uom = item.unit_of_measure; //set receiving unit of measure to same as requested uom
                }
            },

            viewLogModal(id) {
                this.logModalId = id;
                this.logModal=!this.logModal;
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            compute_adjust: function () {
                return (this.item.quantity_adjust*1 + this.item.adjustment.value*1);
            },
            compute_onhand: function () {
                return (this.item.quantity_received*1 - this.item.quantity_sold*1 + this.item.quantity_adjust*1 + this.item.adjustment.value*1);
            },
            compute_conversion_remaining: function (){
                if(!((this.item || {}).conversions || []).length || (this.item || {}).amount_unit<=0) return 0;
                
                let tally = (this.item.quantity_on_hand*this.item.amount_unit) || 0;
                tally -= this.item.conversions.reduce((a,b) => { return a + (b.quantity_received*b.amount_unit); },0);
                return (this.item.unit_of_measure=='ea') ? Math.floor(Number(tally/this.item.amount_unit)) : Number(tally/this.item.amount_unit).toFixed(3);
            },
            
            item_package: function() {
              if(!(this.item || {}).receiving) return null;
              return (this.item.receiving.items.find(v=>v.metrc_tag==this.item.metrc_tag) || {}).package || null;
            },
            
            item_conversion_prodIds: function(){
                if(!this.item) return [];
                else if(!this.item.conversions) return [];
                return this.item.conversions.map(o=>{ if(o.product) return o.product.id; }) || [];
            },
            
            receivingSchema(){
                return this.$store.state[this.module]['receivingSchema'];
            },
            
            catSchema(){
               return this.$store.state[this.module]['categorySchema'];
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            },
            'item.adjustment.reason_code': function(){
                if(this.item.adjustment.reason_code=='undersold') this.item.adjustment.notes = 'Corresponding oversold tag#: ';
                else if(this.item.adjustment.reason_code=='oversold') this.item.adjustment.notes = 'Corresponding Undersold tag#: ';
            },
            convertWithAdjust(to,from){
                if(to) this.item.adjustment.value = (this.compute_conversion_remaining*-1);
                else this.item.adjustment.value = 0;
            }
        },
        
        inject: ['$validator']
        
    };

</script>

<style>
  .strong-span{
    font-weight:700;
  }
</style>
