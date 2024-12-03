<template>
    <div v-if="product && schema" class="card card-stats px-3 py-3 justify-content-center">
        <form>
            <fieldset>
                <div class="card-body">
                    <b-tabs content-class="mt-3" v-model="activeTab">
                        <b-tab v-if="product.type=='single'" title="Inventory" ref="tabInventory" :style="{minHeight: tabMinHeight + 'px'}">
                            <div class="mt-3 mb-4">
                                <table class="b-table b-table-condensed table-nested table-responsive">
                                    <thead>
                                    <tr>
                                        <th width="10%">Received On</th>
                                        <th width="15%">Item Barcode</th>
                                        <th width="10%">Strain</th>
                                        <th width="15%">Vendor</th>
                                        <th width="5%">Cost</th>
                                        <th width="15%">Priceset</th>
                                        <th v-if="showPrepackagedAmount" width="5%">Prepackaged</th>
                                        <th width="5%">Received</th>
                                        <th width="5%">Sold</th>
                                        <th width="5%">Adjusted</th>
                                        <th width="10%">OnHand</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item,iid) in inventory" :key="item.id" :class="{'table-danger':$moment(item.expires_at)<thirty,'is-archived':item.archived_at}">
                                        <td width="10%">{{ item.created_at | localDate }}
                                            <span v-if="item.archived_at">(Archived.)</span>
                                            <span v-else-if="item.expires_at" class="w-100 d-block small show-red"><b>(Expires: {{ item.expires_at | localDate }})</b></span></td>
                                        <td width="15%">
                                            {{ item.item_barcode }} <a href="" @click.prevent="viewTagModal(item)" class=""><i class="hotbox-icon hotbox-icon-tag-line"></i></a>
                                        </td>
                                        <td width="10%">{{ item.item_strain || '--' }}</td>
                                        <td width="15%">
                                            <span v-if="item.vendor">{{ item.vendor.name }}</span>
                                            <span v-else>--</span>
                                        </td>
                                        <td width="5%">${{ item.cost_unit | dollar }}</td>
                                        <td width="10%">
                                            <span v-if="item.pricing">{{ item.pricing.name_grade }}</span>
                                            <span v-else>(Fixed): ${{ item.retail_unit | dollar }}</span>
                                        </td>
                                        <td v-if="showPrepackagedAmount" width="5%" style="white-space: nowrap;">
                                            <span v-if="item.unit_of_measure==='ea'">{{item.amount_unit}}/g ea</span>
                                        </td>
                                        <td width="5%">
                                            {{ item.quantity_received | dollar }}<span class="small">{{ item.unit_of_measure }}</span>
                                        </td>
                                        <td width="5%">
                                            {{ item.quantity_sold | dollar }}
                                        </td>
                                        <td width="5%">
                                            {{ item.quantity_adjust | dollar }}<i v-if="item.quantity_adjust" @click="viewLogModal(item.id)" class="hotbox-icon hotbox-icon-c-info"></i>
                                        </td>
                                        <td width="10%">
                                            <b>{{ item.quantity_on_hand | dollar }}</b><span class="small">{{ item.unit_of_measure }}</span> 
                                            <router-link :to="{name:'inventory_edit',params:{id:item.id,caller:'product',keepAlive:true}}" tag="a" class="">
                                                <i class="hotbox-icon hotbox-icon-i-edit"></i>
                                            </router-link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <form-boolean :declared="gridArchive" :schema="{name:'archived',title:'Include Archived Items'}" @input="(upd) => {gridArchive = upd; }" class="mt-1"/>
                            </div>
                        </b-tab>
                        <b-tab v-if="['grouping','kit'].indexOf(product.type)!==-1" title="Edit In Grouped" ref="tabGrouped" :style="{minHeight: tabMinHeight + 'px'}">
                            <div class="mt-3 mb-4">
                                <product-search onlyReceived promptQty class="" :includeTypes="['recreational','noncannabis','medical']" :scoped="1" :existIds="itemGroupedIds" @add="includeInGrouped" />
                                <table class="b-table b-table-condensed table-nested table-responsive">
                                    <thead>
                                    <tr>
                                        <th width="25%">Product</th>
                                        <th width="15%">Priceset</th>
                                        <th v-if="showPrepackagedAmount" width="5%">Prepackaged</th>
                                        <th width="5%">Sold</th>
                                        <th width="5%">Available</th>
                                        <th width="30%">
                                            <i class="hotbox-icon hotbox-icon-c-question" :title="schema.form.product_grouped.properties.policy.title" v-b-tooltip.hover="schema.form.product_grouped.properties.policy.description"></i> 
                                            {{ schema.form.product_grouped.properties.policy.title }}
                                        </th>
                                        <th width="10%">
                                            <i class="hotbox-icon hotbox-icon-c-question" :title="schema.form.product_grouped.properties.quantity.title" v-b-tooltip.hover="schema.form.product_grouped.properties.quantity.description"></i> 
                                            {{ schema.form.product_grouped.properties.quantity.title }}
                                        </th>
                                        <th width="5%">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item,iid) in grouped" :key="item.id" :class="{'table-danger':item.quantity_on_hand<=0,'is-archived':item.archived_at}">
                                        <td width="25%">
                                            <img :src="getProductImageUrl({product:item.product})" class="responsive float-left mr-2" width="55">
                                            {{ item.item_barcode }} {{ item.item_name }}
                                        </td>
                                        <td width="15%">
                                            <span v-if="item.pricing">{{ item.pricing.name_grade }}</span>
                                            <span v-else>(Fixed): ${{ item.retail_unit | dollar }}<span class="small">/{{item.unit_of_measure}}</span></span>
                                        </td>
                                        <td v-if="showPrepackagedAmount" width="5%" style="white-space: nowrap;">
                                            <span v-if="item.unit_of_measure==='ea'">{{item.amount_unit}}/g ea</span>
                                        </td>
                                        <td width="5%">
                                            {{ item.quantity_sold | dollar }}<span class="small">{{item.unit_of_measure}}</span>
                                        </td>
                                        <td width="5%">
                                            {{ item.quantity_on_hand | dollar }}<span class="small">{{item.unit_of_measure}}</span>
                                        </td>
                                        <td width="30%">
                                            <form-simpleselect hideLabel v-model="item.policy" :schema="schema.form.product_grouped.properties.policy" class="mt-0 mb-0" />
                                        </td>
                                        <td width="10%">
                                            <form-number hideLabel v-model="item.quantity" :schema="Object.assign(schema.form.product_grouped.properties.quantity,{append:1,append_text:item.unit_of_measure})" class="mt-0 mb-0" />
                                        </td>
                                        <td width="5%">
                                            <i v-if="item.can_delete" class="hotbox-icon hotbox-icon-trash-round" @click="removeInGrouped(iid)"></i>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                
                                <div class="col-12 mt-3 mb-4 text-center">
                                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                                </div>

                            </div>
                        </b-tab>
                        <b-tab title="Edit Product" ref="tabEdit">
                            <product-modal
                                    ref="productModal"
                                    :id="product.id"
                                    :type="'form'"
                                    :has-inventory="(((product.inventory||[]).length>0) && (product.inventory.every(e=>(e.quantity_on_hand||0)>0)))"
                                    @update="$emit('toggle')"
                                    @change="$emit('change')"
                                    @archive="$emit('archive')"
                                    @reset="(upd) => $emit('reset',upd)" />
                        </b-tab>
                    </b-tabs>
                </div>

            </fieldset>
        </form>

        <b-modal centered ref="tagModal"
                 v-model="tagModal"
                 size="lg"
                 header-bg-variant="light"
                 header-text-variant="primary" :static=true>

            <template slot="modal-header">
                <i class="modal-top-close fal ti-close" @click="tagModal=!tagModal"></i>
                <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-tag-line"></i> Print Inventory Tag(s)</h5>
            </template>

            <tag-modal @refresh="tagModal=!tagModal" ref="tagprinter">
            </tag-modal>

            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="tagModal=!tagModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="logModal"
                 v-model="logModal"
                 size="lg"
                 header-bg-variant="light"
                 header-text-variant="primary">

            <template slot="modal-header">
                <i class="modal-top-close fal ti-close" @click="logModal=!logModal"></i>
                <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-tag-line"></i> Inventory log</h5>
            </template>

            <log-modal v-if="logModal"
                       :id="logModalId"
                       @refresh="logModal=!logModal">
            </log-modal>

            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="logModal=!logModal">Close</span>
            </template>
        </b-modal>

    </div>
    <div v-else>
        <loading :display="(schema && product) ? false : true" type="loadModal"/>
    </div>
</template>
<script>

    import Product from '../../../../models/Product';
    import TagModal from '../inventory/tagModal';
    import LogModal from '../inventory/logModal';
    import ProductModal from './productModal';
    import ProductSearch from '../../products/product/productSearch';
    import _ from 'lodash';

    export default {
        props: {
            id: {
                type: Number,
                default: null
            },
            model: {
                type: String,
                default: 'Product'
            },
            module: {
                type: String,
                default: 'products',
            },
            filters: {
                type: Object,
                default: () => {}
            }
        },

        data() {
            return {
                gridArchive: false,
                isLoading: false,
                product: null,
                grouped:[],
                itemState: 'save',
                thirty: this.$moment().add(30,'d'),
                tagModal:false,
                tagModalItems:0,
                logModal:false,
                logModalId:0,
                activeTab: 0,
                tabMinHeight: 0,
            };
        },

        components: {
            TagModal,
            LogModal,
            ProductModal,
            ProductSearch
        },

        async mounted() {
            this.isLoading = true;
            if (!this.schema) await this.$store.dispatch(this.module + '/setSchemas', 'product');

            if (this.id) {
                Product.find(this.id).then(response => {
                    this.product = new Product(response).withDefaults(this.schema, false);
                    this.grouped = this.product.grouped.map(v=>{ v.quantity = v.pivot.quantity; v.policy = v.pivot.policy; v.can_delete = true; return v; });
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({
                        status: 400,
                        data: {message: 'We had a hiccup fetching the data - Please try again later.'}
                    });
                });
            } else {
                this.isLoading = false;
                this.$announcer({
                    status: 422,
                    data: {message: 'Whoops - couldnt find the associated record - Please try again later.'}
                });
            }
        },

        computed: {
            inventory() {
                if (!this.product) return [];
                return this.gridArchive ? this.product.inventory : this.product.inventory.filter(e=>e.archived_at===null);
            },
            itemGroupedIds(){
                if(!this.grouped) return [];
                return this.grouped.map(o=>{ if(o.product) return o.product.id; }) || [];
            },
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
            },

            showPrepackagedAmount() {
                if ((this.product.category||{}).equivalency_type==='flower') {
                    return this.product.inventory.some(item=>item.unit_of_measure==='ea');
                };
                return false;
            }
        },

        watch: {

        },

        methods: {
            autoSave(confirm = false) {
                if (!this.product) return false;                                // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        if (!confirm) this.debounceSave();
                        else {
                            console.log('no debounce');
                            this._save(true);
                        }
                    } else if (confirm == true){
                        this.$announcer({
                            status: 422,
                            data: {message: 'Whoops, Please check and correct inputs in order to continue.'}
                        });
                    } else this.$validator.reset();                             // if not validated or confirming, clear validation errors..
                });
            },

            debounceSave: _.debounce(function () {                              //debounce returns function we need to be able to call multiple times
                this._save();
            }, 900),

            includeInGrouped(upd,src='select'){
                if(!upd.last_received || !upd.inv_meta) this.$announcer({status: 409,data: {message: 'Whoops, The selected product does not have any inventory items assigned - try agian?'}});
                else if(this.grouped.find(v=>(v.product || {}).id==upd.id)){
                    this.grouped = this.grouped.map(o=>{
                        if((o.product || {}).id==upd.id) o.product = upd;
                        return o;
                    });
                }else{
                    this.grouped.push({
                        id:upd.last_received.id,
                        product:upd,
                        priceset_id:upd.inv_meta.priceset,
                        product_id:upd.id,
                        item_barcode:upd.last_received.item_barcode,
                        retail_unit:upd.last_received.retail_unit,
                        pricing: upd.last_received.pricing,
                        amount_unit: upd.last_received.amount_unit,
                        unit_of_measure: upd.inv_meta.uom,
                        quantity_sold: upd.inv_meta.sold,
                        quantity_on_hand:upd.inv_meta.onhand,
                        policy:'restock',
                        quantity:upd.add_qty || 1,
                        can_delete:true
                    });
                }
            },
            
            removeInGrouped(ind){
                this.grouped.splice(ind, 1);
            },
            
            
            _save(confirm = false) {
                this.itemState = 'saving..';
                this.product.grouped = this.grouped; // bind updated grouped array to save
                this.product.save().then(response => {
                    this.$announcer({status: 200, data: {message: 'Product data has been saved'}});
                    this.itemState = 'saved';
                    this.$emit('reset',response);
                    this.$emit('toggle');
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },

            async viewTagModal(item){
                let change = await this.$refs.tagprinter.setId(item.id);
                this.$nextTick(() => this.tagModal=!this.tagModal);
            },

            viewLogModal(id){
                this.logModalId = id;
                this.logModal=!this.logModal;
            }
        },

        inject: ['$validator']
    };
</script>

<style scoped>
    .comments-row .hotbox-icon {
        cursor: pointer;
        color:#9A9A9A;
        vertical-align: middle;
    }
    .comments-row label {
        cursor: pointer;
    }
</style>
