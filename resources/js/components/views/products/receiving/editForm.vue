<template>
    <div v-if="item && schema" class="col-12">
    <form @change="autoSave()">
        <fieldset>

            <h3 v-if="item.id" class="mb-4">Edit Purchase Order<br><span class="small">{{ item.po_number }}</span></h3>
            <h3 v-else>Add a new Purchase Order:</h3>

            <div class="row">
                <form-text v-model="item.po_number" :schema="schema.form.po_number" class="col-12 col-sm-6 mt-2" />
                <form-search v-model="item.addressbook_id" :schema="schema.form.addressbook_id" :defaultOption="item.vendor" @syncdata="syncAddress" class="col-12 col-sm-6 mt-0" />
                <form-list v-model="item.invoice_refs" :schema="schema.form.invoice_refs" class="col-12 mt-2 mb-2" />
            </div>

            <div class="row">
                <div class="col-12 mt-2 mb-1">
                    <h5>Saleable Items</h5>
                
                    <product-search promptQty promptNew class="" :includeTypes="['recreational','noncannabis','medical']" :scoped="item.addressbook_id" :existIds="itemProdIds" @add="includeProduct" @new="openProduct(0,'create')" />

                        <b-table v-if="item.items" striped hover foot-clone no-footer-sorting sticky-header
                                class="table-condensed table-small table-nested"
                                ref="po_items"
                                :id="model.toLowerCase()+'_items_table'"
                                :items="item.items"
                                :fields="schema.form.items.fields"
                                :busy.sync="isLoading"
                                :show-empty="true"
                                :sort-by.sync="itemsSortBy"
                                :sort-desc.sync="itemsOrderDesc"
                                :no-local-sorting="true"
                                :no-local-filtering="true"
                                :per-page="50"
                                primary-key="id"
                                :tbody-transition-props="{name:'hb-list-fade'}"
                                :tbodyTrClass="renderRowBg"
                                responsive="md"
                                stacked="sm"
                                @sort-changed="reSortItems"
                            >
                        
                        <template v-for="(field,ind) in schema.form.items.fields" v-slot:[renderColumnHeader(field.key)]="column">
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
                                        <a href="" class="" @click.prevent="openProduct(row.item.product.id,'edit')">{{ row.item.product.name }}</a><br>
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
                                <form-number v-model.number="row.item.cost_unit" :schema="schema.form.items.properties.cost_unit" :hideLabel="true" class="mt-0 mb-0" />
                            </span><span v-else>--</span>
                        </template>
                                
                        <template v-slot:cell(priceset)="row">
                            <div v-if="row.item.product" style="height:38px;">
                                <form-priceset
                                        @fixedPrice="(val)=>{row.item.retail_unit=val}"
                                        :fixed-price="row.item.retail_unit"
                                        v-model="row.item.priceset_id"
                                        :schema="schema.form.items.properties.priceset_id"
                                        :inv="row.item"
                                        :hideLabel="true"
                                        @updateUnit="(upd) => row.item.unit_price = upd"/>
                                <!--<form-number v-if="!row.item.priceset_id" v-model.number="row.item.retail_unit" :schema="schema.form.items.properties.retail_unit" :hideLabel="true" class="mt-0 mb-0" />-->
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
                                  <form-number v-model.number="row.item.weight_potency" :schema="schema.form.items.properties.weight_potency" :hideLabel="true" class="mt-0 mb-0" />
                              </div>
                              <div v-else>--</div>
                          </div>
                          <div v-else> -- </div>
                        </template>

                        <template v-slot:cell(quantity_requested)="row">
                            <form-number v-if="(row.item.product || {}).nature_type=='noncannabis'" v-model.number="row.item.quantity_requested" :schema="schema.form.items.properties.quantity_requested" :hideLabel="true" class="mt-0 mb-0" />
                            <div v-else class="position-relative text-center">
                                <div v-if="row.item.package">
                                    {{ row.item.quantity_requested }}{{ (row.item.package || {}).uom }}
                                    <span v-if="(row.item.package||{}).uom=='g' && row.item.unit_of_measure=='ea'" class="small"> ({{ Number(row.item.quantity_requested / row.item.amount_unit).toFixed(0) }} Units)</span>
                                </div>
                            </div>
                        </template>                        
                                
                        <template v-slot:cell(quantity_received)="row">
                            <div class="position-relative">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend"><i v-if="schema.form.items.properties.quantity_received.prepend" :class="schema.form.items.properties.quantity_received.prepend"></i></div>
                                    <input class="input form-control" v-model.number="row.item.quantity_received" type="number" :min="0" :step="1" :name="'items_quantity_received_'+row.index">
                                    <div v-if="(row.item.product || {}).nature_type!='noncannabis'" class="input-group-append">
                                        <span v-if="(row.item.packageType||{}).value==='package'">/ea</span>
                                        <span v-else class="input-group-text">
                                            <a class="dropdown-toggle py-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">/{{ row.item.unit_of_measure }}</a>
                                            <div class="dropdown-menu tight">
                                                <a v-for="(val,vid) in schema.form.items.properties.unit_of_measure.values.filter(e=>e.id!=='ea')" :key="val.id" class="dropdown-item action-default" href="" @click.prevent="row.item.unit_of_measure=val.id">
                                                    {{ val.name }}
                                                </a>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <template v-slot:cell(actions)="row">
                            <i class="mr-2 mt-1" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}" @click="row.toggleDetails"></i>
                            <i v-if="row.item.can_delete" class="hotbox-icon hotbox-icon-trash-round" @click="removeItem(row.index)"></i>
                        </template>


                        <template v-for="(field,ind) in schema.form.items.fields" v-slot:[renderColumnFooter(field.key)]="column">
                          <span v-if="field.key=='cost_unit' && item.articles_value"><span v-if="itemState=='saved'" class="small">Total Value: ${{ item.articles_value | dollar }}</span></span>
                          <span v-else-if="field.key=='quantity_requested'">
                            <label v-if="hasBackorder" class="custom-control custom-checkbox small">
                                <input type="checkbox" class="custom-control-input" :checked="item.create_backorder" :disabled="false" @click="item.create_backorder=!item.create_backorder">
                                <span class="custom-control-indicator"></span>  Create Backorder?
                            </label>
                          </span>
                          <span v-else-if="field.key=='quantity_received'"><b>&nbsp;</b></span>
                          <span v-else-if="field.key=='actions'">&nbsp;</span>
                          &nbsp;
                        </template>

                        <template v-slot:row-details="row" class="divide-row">
                            <div v-if="(['recreational','medical'].indexOf((row.item.product || {}).nature_type)!==-1) || row.item.metrc_tag" class="col-12">
                                <div v-if="!row.item.package" class="w-100 mt-3 mb-2">
                                    <span v-if="$store.getters.getAgent=='metrc'">
                                        <h6><b>1. Search/Select a {{ item.type | ucwords }} Package to match this product..</b></h6>
                                        <package-search autoStart :focus="(item.vendor || {}).id" @select="(upd) => syncPackage(row.item,upd)" />
                                    </span>
                                    <span v-else>
                                        <h6><a href="" class="" @click.prevent="openEditPackage(row.item)"><b><i class="hotbox-icon hotbox-icon-tag-content"></i> Add Package Info..</b></a></h6>
                                    </span>
                                </div>
                                <div v-else class="d-flex justify-content-between align-items-center position-relative">
                                    <div class="">
                                        <h6><i class="hotbox-icon hotbox-icon-tag-content"></i> {{ row.item.metrc_tag }} <i v-if="row.item.can_delete" class="hotbox-icon hotbox-icon-link-broken-70 float-right ml-2" @click="removeMetrcTag(row.item)"></i></h6>
                                        <span class="small ml-2">
                                            {{ item.type | ucwords }}: "{{ row.item.package.name }}" X {{ row.item.package.quantity }}/{{ row.item.package.uom }} <i v-if="$store.getters.getAgent!='metrc'" class="hotbox-icon hotbox-icon-pencil" @click="openEditPackage(row.item)"></i>
                                        </span>
                                    </div>
                                    <div class="ml-auto text-right">
                                        <span v-if="row.item.product">
                                            <i v-if="" class="hotbox-icon hotbox-icon-link-broken-70 float-right ml-2" @click="removeProduct(row.item)"></i>
                                            <i class="hotbox-icon hotbox-icon-box-2"></i> Matches: {{ row.item.product.name }}<br>
                                            <span v-if="(row.item.product || {}).nature_type!='noncannabis'" class="small"><i class="ti-calendar"></i> Expires on:
                                                <datepicker v-model="row.item.expires_at" class="" style="display:inline-block !important"
                                                    :name="'items.expires_at_'+row.index"
                                                    :format="'M/d/yy'"
                                                    :placeholder="'1/1/29'"
                                                    :typeable="true"
                                                    :bootstrap-styling="false"
                                                    :input-class="{'form-control':false,'showdate':true}"
                                                    :calendar-button-icon="'ti-calendar'">
                                                 </datepicker>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                
                                <div v-if="!row.item.product" class="w-100 mt-3 mb-3">
                                    <h6><b>*Please Select/Add a Matching product:</b></h6>
                                    <product-search promptNew isSelect class="" :includeTypes="['recreational','noncannabis','medical']" :scoped="1" :initValue="(row.item.package || {}).product_search" :existIds="itemProdIds" @add="(upd) => syncProduct(upd,row.item)" @new="openProduct(0,'import',row.item.package)" />
                                </div>
                            </div>
                            
                            <div class="col-12 mt-1 mb-4">
                                &nbsp;<br />
                            </div>
                        </template>


                        <template v-slot:table-caption v-if="item.items.length">

                        </template>
                                
                        <template v-slot:empty>
                            <div v-if="!isLoading" class="text-center py-3">
                                <img src="/images/logo.png" alt="No Results" class="" width="65" />
                                <h5 class="small">No items Yet - Please Browse or add item(s) above.</h5>
                            </div><div v-else class="h-100">&nbsp;</div>
                        </template>
                
                        </b-table>

                    </div>
                </div>

            <div class="col-12 clearfix mt-3 text-center">
                <loading :display="isProcessing" type="loadInline" />
                <a href="" class="btn btn-md btn-light" @click.prevent="autoSave(true,'download')"><i :class="{'hotbox-icon hotbox-icon-download-file':!isDownloading,'far fa-circle-notch fa-spin':isDownloading}"></i></a>
                <button class="btn btn-md btn-primary" @click.prevent="autoSave(true)"><i class="hotbox-icon hotbox-icon-saved-items"></i> {{ itemState }} for Later</button>
                <button v-if="item.id" class="btn btn-md btn-danger" @click.prevent="_receive" :disabled="false"><i class="hotbox-icon hotbox-icon-file-download-87"></i> Receive</button>
            </div>

        </fieldset>
    </form>


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
                @update="includeProduct"
                @sync="(upd) => syncProduct(upd,item.items.find(v=>(v.package || {}).id==productModelItemId))">
              </product-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="productModal=!productModal">Close</span>
            </template>
        </b-modal>
    
        <b-modal centered ref="addressModal"
            v-model="addressModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal fa-times" @click="addressModal=!addressModal"></i>
              <h5 class="w-100 mb-0 text-center">Add a new Supplier/Vendor Address</h5>
            </template>
          
              <address-modal v-if="addressModal"
                type="vendor"
                :id="addressModalId"
                @update="newAddress">
              </address-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="addressModal=!addressModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="packageModal"
                 v-if="(currentItem.package || {}).uom"
                 v-model="packageModal"
                 size="sm"
                 @shown="$refs.packageAmount.focus()"
                 header-bg-variant="light"
                 header-text-variant="primary">

            <template slot="modal-header">
                <i class="modal-top-close fal fa-times" @click="packageModal=!packageModal"></i>
                <h5 class="w-100 mb-0 text-center">Amount/Package</h5>
            </template>
            {{ schema.form.items.properties.unit_of_measure.values.find(e=>e.id===currentItem.package.uom).name }} per package:
            <input
                    v-if="packageModalItemId"
                    type="number"
                    autofocus
                    ref="packageAmount"
                    min="0"
                    v-validate="'required|decimal:4'"
                    name="amount"
                    v-on:keyup.enter="()=>{if (!(errors.items||{}).length) packageModal=!packageModal}"
                    v-model="item.items[item.items.findIndex(e=>e.id===packageModalItemId)].amount_unit"
            >

            <template slot="modal-footer">
                <span :class="{disabled:(errors.items||{}).length>0}" class="btn-label btn-sm btn-light float-right" @click="()=>{if (!(errors.items||{}).length) packageModal=!packageModal}">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="packageEditModal"
            v-model="packageEditModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal fa-times" @click="packageEditModal=!packageEditModal"></i>
              <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-tag-content"></i> Package Information</h5>
            </template>

            <div v-if="packageEditItem" class="row">
                <form-text v-model="packageEditItem.label" :schema="schema.form.items.properties.package_label" class="col-12 col-sm-12 mt-1 mb-1" />
                <form-text v-model="packageEditItem.package_data.ItemStrainName" :schema="schema.form.items.properties.package_strain" class="col-12 col-sm-6 clearfix mt-1 mb-1" />
                <form-text v-model="packageEditItem.package_data.SourceHarvestNames" :schema="schema.form.items.properties.package_batch" class="col-12 col-sm-6 mt-1 mb-1" />

                <div class="col-12 col-sm-12 mt-2 mb-3">
                    <label for="package-quantity">Package Quantity (
                        <a href="" :class="{'strong':packageEditItem.uom=='g'}" @click.prevent="packageEditItem.uom='g'">Grams</a> | 
                        <a href="" :class="{'strong':packageEditItem.uom=='ea'}" @click.prevent="packageEditItem.uom='ea'">Each</a>)
                    </label>
                    <form-number v-model="packageEditItem.quantity" :schema="Object.assign({},schema.form.items.properties.package_quantity,{append:1,append_text:packageEditItem.uom})" :hideLabel="true" />
                </div>

                <div class="col-12 clearfix mt-2 text-center">
                    <button class="btn btn-info" :disabled="isProcessing" @click.prevent="editPackage(packageEditItem)">
                        <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                            Done.
                        </button>
                </div>
            </div>
                
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="packageEditModal=!packageEditModal">Close</span>
            </template>
        </b-modal>
        
        
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Receiving';
    import AddressModal from '../../auth/addressbook/addressModal';
    import ProductModal from '../../products/product/productModal';
    import ProductSearch from '../../products/product/productSearch';
    import PackageSearch from '../../products/product/packageSearch';
    import FormPriceset from '../../products/inventory/formPriceset';
    import PackageType from './packageType';

    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Receiving'
            },
            module: {
                type: String,
                default: 'products',
            }
        },
        
        data(){
            return {
                isLoading:false,
                isProcessing:false,
                isSyncing:false,
                isDownloading:false,
                item: null,
                itemState: 'save',
                itemsSortBy:null,
                itemsOrderDesc:false,
                addressModal:false,
                addressModalId:0,
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
                packageEditItemId:null
            };
        },
        
        components : {
            AddressModal,ProductModal,ProductSearch,PackageSearch,FormPriceset,PackageType
        },
        
        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // need latest and greatest schema for this form.
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.item.items = this.item.items.map(v=>{
                        return Object.assign({},v,{ //return new object here to get vue reactivity for new properties
                            weight_potency: v.weight_potency*100, // convert potency to mg for form
                            packageType: _.clone(this.packageTypeOptions.find(e=>e.value===(v.unit_of_measure==='ea' ? 'package' : 'deli'))),  //default to package or deli depending
                        });
                    });
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.item.po_number = 'PO-'+this.$moment().format('Ymd');
                this.isLoading = false;
            }
        },
        
        methods: {
            autoSave(confirm=false,next=null){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) _.debounce(() => { this._save(); },4000)();
                        else this._save(true,next);
                    }else if(confirm==true){
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    }else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
            },

            async newAddress(upd){
                if(!upd) this.addressModal=!this.addressModal;
                else{
                    await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // since we have a NEW address, we need to load the schema to update the addressbook_id options..
                    this.item.addressbook_id = upd.id;                          // then, assign the new option!
                    this.addressModal=!this.addressModal;
                }
            },
            openProduct(pid,typ='create',data=null){
                
                if(!this.item.addressbook_id){
                    this.$announcer({status:400,data:{message:'please specify a Vendor for this Purchase order in order to add items..'}});
                    return false;
                }

                this.productModalId = pid;
                this.productModalType = (pid) ? 'edit' : typ;
                this.productModalInit = data;
                this.productModelItemId = (data || {}).id || null;

                this.productModal = !this.productModal;
            },
            
            includeProduct(upd,src='select'){
                if(!upd) this.productModal=!this.productModal;
                
                if(this.item.items.find(v=>(v.product || {}).id==upd.id)){
                    this.item.items = this.item.items.map(o=>{
                        if((o.product || {}).id==upd.id) o.product = upd;
                        return o;
                    });
                    this.$refs.po_items.refresh(); // btable needs to refresh for some reason..
                }else{

                    this.item.items.push({
                        id:upd.id+'_'+(this.item.items.length*1)+1,
                        product:upd,
                        package:null,
                        priceset_id:(upd.inv_meta) ? upd.inv_meta.priceset : null,
                        metrc_tag:null,
                        product_id:upd.id,
                        item_barcode:null,
                        item_strain: (upd.inv_meta) ? upd.inv_meta.strain : null,
                        unit_of_measure:(upd.inv_meta) ? upd.inv_meta.uom : (upd.nature_type=='noncannabis') ? 'ea' : ((upd.category || {}).equivalency_type=='flower') ? 'g' : 'ea',
                        amount_unit:(upd.inv_meta) ? upd.inv_meta.amount_unit : 1,
                        weight_potency:(upd.inv_meta) ? (upd.inv_meta.weight_potency*1000) : 1000,
                        cost_unit:(upd.inv_meta) ? upd.inv_meta.cost : 0.01,
                        retail_unit:(upd.inv_meta) ? upd.inv_meta.retail : 0.02,
                        expires_at:null,
                        quantity_requested: (upd.nature_type=='noncannabis') ? upd.add_qty || 1 : 0,
                        quantity_received:upd.add_qty || 1,
                        quantity_split:((upd.add_qty || 1)/2),
                        unit_price:0,
                        can_delete:true,
                        //_showSplit:false,
                        _showDetails:(upd.nature_type=='noncannabis') ? false : true
                    });
                
                }
                
                this.itemsSortBy = null;                                        // reset sortBy as latest push is at end and user might want to resort.
                this.productModal=false;
            },

            syncProduct(upd,item){

                if(!upd || !item){ 
                    this.productModal=!this.productModal;
                    return false;
                }

                item.product = upd;
                item.product_id = upd.id;
                item.priceset_id = (upd.inv_meta) ? upd.inv_meta.priceset : null;
                
                if(!item.item_strain) item.item_strain = (upd.inv_meta) ? upd.inv_meta.strain : null; // JP 3.14.2020 - only inject strain from last received if not already in PO items data
                
                item.unit_of_measure = (upd.inv_meta) ? upd.inv_meta.uom : (upd.nature_type=='noncannabis') ? 'ea' : ((upd.category || {}).equivalency_type=='flower') ? 'g' : 'ea';
                item.amount_unit = (upd.inv_meta) ? upd.inv_meta.amount_unit : 1;
                item.weight_potency = (upd.inv_meta) ? (upd.inv_meta.weight_potency*1000) : 1000;
                item.cost_unit = (upd.inv_meta) ? upd.inv_meta.cost : 0.01;
                item.retail_unit = (upd.inv_meta) ? upd.inv_meta.retail : 0.02;
                item.quantity_received = (item.unit_of_measure==(item.package || {}).uom) ? item.quantity_requested : item.quantity_received;
                item.quantity_split = (upd.inv_meta) ? (upd.inv_meta.uom!=(item.package || {}).uom) ? (item.quantity_requested % (item.amount_unit || 1)) : (item.quantity_requested/2) : (item.quantity_requested/2);
                item.unit_price = 0;
                //item._showSplit =  false;

                this.itemsSortBy = null;                                        // reset sortBy as latest push is at end and user might want to resort.
                this.productModal=false;
            },
            
            removeItem(ind){
                
                let cur = this.item.items[ind];
                
                this.item.items.splice(ind, 1);

                this.item.items.map(o=>{                                        // other rows of same package need reset their qty requested
                    if(o.metrc_tag==(cur || {}).metrc_tag) o.quantity_requested = (o.package || {}).quantity || o.quantity;
                    return o;
                });

            },
            
            /*splitRow(ind){
                if(!this.item.items[ind]) return false;                         // no row to split, return
                
                let req = this.item.items[ind].quantity_requested;
                let take = this.item.items[ind].quantity_split;
                let copy = Object.assign({},this.item.items[ind],{
                    id:this.item.items[ind].id+'_'+(this.item.items.length*1)+1,
                    quantity_requested:take,
                    quantity_split:0,
                    product:null,
                    product_id:null,
                    item_barcode:null,
                    _showSplit:false,
                    can_delete:true
                });              // fresh new unbound copy
                this.item.items.splice(ind+1,0,copy);                           // new row will be right behind row were splitting.
                
                this.item.items[ind] = Object.assign({},this.item.items[ind],{
                    quantity_requested:(req - take),
                    quantity_split:0,
                    _showSplit:false
                });
            },*/
            
            syncPackage(item,pack){
                item.metrc_tag = pack.label;
                item.quantity_requested = pack.quantity;
                item.weight_potency = (pack.weight_potency*1000) || 1000;
                item.package = pack;
                //item._showSplit =  false;
                
                this.$refs.po_items.refresh(); // btable needs to refresh for some reason..
            },
            
            openEditPackage(item){
              if(!item) return false;
              this.packageEditItem = item.package || {
                  id:null,
                  label:null,
                  name:null,
                  status:'confirmed',
                  quantity:1,
                  uom:'g',
                  weight_potency:1,
                  package_data: {
                    SourceHarvestNames:null,
                    ItemStrainName:null
                  }
              }
              
              this.packageEditItemId = item.id;
              this.packageEditModal = !this.packageEditModal;
            },
            
            editPackage(item){

                if(!item || this.item.items.findIndex(itm => itm.id == this.packageEditItemId)===-1) return false;
                this.isProcessing = true;

                this.item.items[this.item.items.findIndex(itm => itm.id == this.packageEditItemId)].package = Object.assign({},item,{
                    id:item.id+'_'+Math.random(444,44444),
                    name: item.package_data.ItemStrainName || 'Misc Package'
                });

                this.item.items[this.item.items.findIndex(itm => itm.id == this.packageEditItemId)].metrc_tag = item.label;
                this.item.items[this.item.items.findIndex(itm => itm.id == this.packageEditItemId)].type = this.$store.getters.getAgent;
                this.item.items[this.item.items.findIndex(itm => itm.id == this.packageEditItemId)].quantity_requested = item.quantity;

                this.$refs.po_items.refresh(); // btable needs to refresh for some reason..
                this.isProcessing = false;
                this.packageEditModal = !this.packageEditModal;

            },
            
            removeMetrcTag(item){
                item.metrc_tag = null;
                item.package = null;
                item.quantity_requested = null;
                
                if(!item.metrc_tag && !item.product_id) this.removeItem(this.item.items.findIndex(v=>v.id==item.id));
                else this.$refs.po_items.refresh(); // btable needs to refresh for some reason..
            },
            
            removeProduct(item){
                item.product_id = null;
                item.product = null;
                item.quantity_received = 0;
                
                if(!item.metrc_tag && !item.product_id) this.removeItem(this.item.items.findIndex(v=>v.id==item.id));
                else this.$refs.po_items.refresh(); // btable needs to refresh for some reason..
            },
            
            calcItems(){
                
                
            },
            
            reSortItems(ctx){
                if(!this.item.items) return false;
                this.sorter(this.item.items,ctx.sortDesc,(['sku','name','title'].indexOf(ctx.sortBy)!==-1) ? 'product.'+ctx.sortBy : ctx.sortBy);
            },
            inItems(pid){
                return (this.item.items) ? (this.item.items.find(v=>v.product.id==pid)) ? true : false : false;
            },
            
            modifyAddress(id){
                this.addressModalId = id;
                this.addressModal=true;
            },
            
            syncAddress(upd){                                                   // sync addresses from selection
                if(!upd) return false;
                if(this.schema.form.addressbook_id.values.find(v=>v.id==upd.id))
                    this.schema.form.addressbook_id.values.push(upd);           // need new value in list for when schema is reloaded
                this.item.vendor = upd;                                         // then, assign the new address to the current package!
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

            renderRowBg(item,type){
                if(!item) return null;

                if (item.unit_of_measure==='ea') {
                    if(Number(item.quantity_requested / (item.amount_unit || 1)).toFixed(4)!=item.quantity_received && item.quantity_requested) return 'table-warning';
                } else {
                    let quantityReceived=this.toGrams(item.quantity_received, item.unit_of_measure); //qty received, as g
                    let quantityRequested=this.toGrams(item.quantity_requested, (item.package || {}).uom); //qty requested, as g

                    if (!this.weightEqual(quantityReceived, quantityRequested)) return 'table-warning';
                }
                return null;
    	    },

            weightEqual(weight1, weight2) {
                if (isNaN(weight1) || isNaN(weight2)) return false;
                return weight1.toFixed(4)===weight2.toFixed(4);
            },

            toGrams(weight, unit) {
                if (isNaN(weight)) return 0;
                weight = Number(weight);

                if (unit==='mg') weight/=1000;
                if (unit==='oz') weight*=28.34952;

                return weight;
            },

            async _receive(){
                if(!this.item.id) return false;
                else if(!this.isReady){
                    alert('Whoops - This PO is not yet ready for receiving - Please ensure each item has a corresponding package/product assignment..');
                    return false;
                }
                
                let withPin = await this.requirePin('Please Enter an Admin PIN to proceed with Receiving.');
                if(withPin===false) return false;                               // an adminpin couldnt be validated HINT add error message here if desired.
                
                this._save(true,'received');
            },
            
            _save(confirm=false,next=null){
                this.itemState = 'saving..';
                this.isProcessing = true;
                if(this.item.schema) delete(this.item.schema);
                this.item.action = next;
                this.item.items = this.item.items.map(v=>{ v.weight_potency = v.weight_potency/1000; return v;}); // convert potency to g for storage
                this.item.save().then(response => {
                    this.item.items = response.items.map(v=>{ v.weight_potency = v.weight_potency*1000; return v;}); // convert potency to mg for form
                    if(confirm){
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        
                        if(next=='received'){ 
                            this.$announcer({status:200,data:{message:this.item.articles_received+' of '+this.item.articles_sent+' Articles have been successfully Received!'}});
                            this.$router.push({name:this.model.toLowerCase(),params:{focus:'received'}});
                        }else{
                            this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                            this.$router.push({name:this.model.toLowerCase()});
                        }
                    }
                    this.isProcessing = false;
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.isProcessing = false;
                    this.itemState = 'resave';
                });
            },

            changePackageType(newValue, item) {
                if (item.packageType.value==='package') {
                    item.unit_of_measure='ea';
                    this.packageModalItemId=item.id;
                    this.packageModal=true;
                } else {
                    item.amount_unit = item.product?.inv_meta?.amount_unit ? item.product.inv_meta.amount_unit : 1;
                    item.unit_of_measure = item.product?.inv_meta?.uom && item.product.inv_meta.uom!=='ea' ? item.product.inv_meta.uom : 'g';
                    item.package.uom = item.unit_of_measure; //set receiving unit of measure to same as requested uom
                }
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },

            itemProdIds(){
                if(!this.item) return [];
                else if(!this.item.items) return [];
                return this.item.items.map(o=>{ if(o.product) return o.product.id; }) || [];
            },
            
            hasBackorder(){
                if((this.item || {}).items)
                return (((this.item || {}).items || []).find(v=>v.quantity_requested > v.quantity_received)) ? true : false;
            },
            
            isReady(){
                if(!this.item) return false;
                else if(!this.item.items) return false;
                else if(!this.item.items.length) return false;
                else if(this.item.items.find(v=>{
                    if(!v.product) return false;
                    else if(['recreational','medical'].indexOf(v.product.nature_type)!==-1 && !v.package) return false;
                    else return true;
                })) return true;
                else return false;
            },

            currentItem() {
                return this.item.items[this.item.items.findIndex(e=>e.id===this.packageModalItemId)] || { package: {} };
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                    
                    if(newVal.addressbook_id=='NEW')
                        this.addressModal = true;
                        
                },
                deep: true
            },
            
            'item.items':{
                handler(to,from){
                    this.calcItems();
                },
                deep: true
            },
            
            productModal(to,from){
                if(!to && from) this.productSearch = null;                     // reset product search if we are leaving the product modal
            }
        }
        
    };
    
</script>

<style scoped>
    >>> #receiving_items_table input.form-control {
        height: calc(1.5em + 0.75rem + 4px);
    }
    >>> #receiving_items_table tr.b-table-details {
        border-bottom: medium solid #6a6767;
    }
    >>> #receiving_items_table tr.b-table-details:last-child {
        border-bottom: unset;
    }
    div.package-type {
        height: 38px;
    }
</style>
