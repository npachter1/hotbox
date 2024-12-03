<template>
    <div>
    <form v-if="schema" class="">
        <fieldset>
    
            <div class="input-group product-search" v-if="schema.form.product_search">
                <div class="input-group-prepend">
                    <div class="dropdown-toggle py-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fal fa-plus"></i> {{ browseText | ucwords }}</div>
                    <div class="dropdown-menu tight">
                        <a v-for="(val,vid) in schema.form.product_search.categories" :key="val.id" class="dropdown-item action-default" href="" @click.prevent="updateScope(val.id)">
                            <i v-if="val.contains_thc" class="hotbox-icon hotbox-icon-mjleaf"></i> {{ val.name }}
                        </a>
                        <div role="separator" class="dropdown-divider"></div>
                        <a :key="'all'" class="dropdown-item action-default" href="" @click.prevent="updateScope('all')">View/Search ALL</a>
                        <a v-if="promptNew" href="" class="dropdown-item action-default" @click.prevent="$emit('new')">Or Add a NEW Product..</a>
                        
                        
                    </div>
                </div>                            
                <input type="text" 
                    v-model="productSearch" 
                    class="form-control" 
                    :placeholder="'Click to Search for '+(initValue || this.browseText)+' or Type NEW'"
                    @input="searchProduct"
                    @click="initSearch"
                    @keydown.enter.prevent="searchProduct">
                    <div class="input-group-append">
                        <i v-if="productSearch || productList"class="ti-close mt-1" @click.prevent="closeSearch"></i>
                        <i v-else-if="promptNew" class="hotbox-icon hotbox-icon-d-add mt-1" @click.prevent="$emit('new')"></i>
                    </div>
            </div>
                        
            <transition name="hb-slide">
                <div class="search-contain">
                    <div class="search-drop" v-if="productSearch || productList || isLoading">
                        <loading :display="(!productList || isLoading) ? true : false" type="loadModal" />

                        <b-table v-if="productFilters" small outlined hover condensed             
                            id="product_search_table"
                            ref="productSearchTable"
                            primary-key="id"
                            class="table-nested"
                            :class="{'is-finding':isLoading}"
                            :items="(productList) ? productList.data || [] : []"
                            :fields="schema.form.product_search.fields"
                            :busy.sync="isLoading"
                            :show-empty="true"
                            :sort-by.sync="productFilters.sortBy"
                            :sort-desc.sync="productFilters.orderDesc"
                            :no-local-sorting="true"
                            :no-local-filtering="true"
                            :per-page="0"
                            :tbodyTrClass="renderRowBg"
                            responsive="md"
                            stacked="sm"
                        >
                          
                            <template v-slot:cell(name)="row">
                                <img :src="getProductImageUrl({product:row.item})" class="responsive float-left px-2" width="65">
                                {{ row.value }} {{ row.sku }}<br>
                                <span v-if="row.item.category" class="small">{{ row.item.category.name }}</span><br>
                            </template>

                            <template v-slot:cell(inv_meta)="row">
                                {{ (row.item.category || {}).name || 'Non-Entered' }}<br>
                                <span class="small" v-if="(row.item.inv_meta || {}).received"> - Last Received: {{ row.item.inv_meta.received | localDate }}</span> 
                                <span class="small" v-if="(row.item.category || {}).equivalency_type"> - Type: {{ row.item.category.equivalency_type | ucwords }}</span> 
                            </template>

                            
                            <template v-slot:cell(add_qty)="row">
                                <form-number v-if="!inSet(row.item.id,existIds) && promptQty" v-model="row.item.add_qty" :schema="{name:'prod_qty_'+row.index,prepend:'ti-close mt-1'}" :hideLabel="true" />
                            </template>
                            
                            <template v-slot:cell(actions)="row">
                                <button v-if="inSet(row.item.id,existIds) && row.item.nature_type=='noncannabis'" class="btn btn-md btn-default" :disabled="true">ADDED</button>
                                <button v-else class="btn btn-md btn-warning" @click.prevent="$emit('add',row.item,'select')">{{ (isSelect) ? 'SELECT' : 'ADD' }}</button>
                            </template>  

                            <template v-slot:empty>
                                <div v-if="!isLoading" class="text-center">
                                    <img src="/images/logo.png" alt="No Results" class="" width="65" />
                                    <h5 class="small">Hmm, There are currently no Results.  Try Browse above..</h5>
                                </div><div v-else class="h-100">&nbsp;</div>
                            </template>
            
                        </b-table>

                        <div class="col-12">
                            <b-pagination v-if="productList && productList.meta.last_page>1"
                                          :total-rows="productList.meta.total"
                                          :per-page="productList.meta.per_page"
                                          v-model="productPage"
                                          align="right" />

                        </div>

                        <div class="col-12 mt-2 mb-3">
                            <span class="btn btn-md btn-light float-right" @click="closeSearch">Return to Form..</span>
                        </div>
                                        
                    </div>
                </div>
            </transition>

        </fieldset>
    </form>
    </div>
</template>

<script>
    import List from '../../../../models/Product';
    import _ from 'lodash';
    export default {
        props: {
            module: {
                type: String,
                default: 'products',
            },
            model: {
                type: String,
                default: 'Product'
            },
            existIds: {
                type: Array,
                default: () => []
            },
            promptNew: {
                type: Boolean,
                default: false
            },
            promptQty: {
                type: Boolean,
                default: false
            },
            includeTypes:{
                type: Array,
                default: () => []
            },
            setTypes:{
                type: Array,
                default: () => ['single']
            },
            onlyReceived:{
                type: Boolean,
                default: false
            },
            autoStart:{
                type: Boolean,
                default: false
            },
            isSelect:{
                type:Boolean,
                default: false
            },
            initValue: {
                type: [String,Number],
                value: null
            },
            scoped: {
                type: [String,Number],
                value: null
            }
        },
        data(){
            return {
                isLoading: false,
                productSearch:null,
                productFilters:null,
                productList:null,
                productPage:1,
                browseScope: null,
                browseText: 'Browse'
            };
        },
        
        components : {
        },
        
        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // since we are calling this from another app section, and has key counts - we need to load a fresh schema first.
        },
        
        methods: {
            setProductFilters(cat){
                let typ = (cat) ? [cat] : ['all'];
                this.browseText = (this.schema.form.product_search.categories.find(v=>v.id==typ) || {}).name || ((cat=='all') ? 'All' : 'Misc');
                this.productFilters = {                                         // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 20,
                    sortBy: 'updated_at',
                    orderDesc: true,
                    filter: Object.assign({},{nature_type:[this.includeTypes],type:[this.setTypes]})
                };
            },
            searchProduct: _.debounce(function (e) {                            // fetch existing products or items to import
                if(!this.productSearch.length) this.clearProductSearch();
                else if(this.productSearch.length<=2) return false;
                else if(this.productSearch.toLowerCase()=='new' && this.promptNew){ 
                    this.clearProductSearch();    
                    this.$emit('new');
                }else{ 
                    if(!this.productFilters) this.setProductFilters(this.browseScope);
                    this._fetchProducts(this.browseScope);
                }
            }, 500),
            
            async _fetchProducts(cat=null){
                
                if(!this.scoped){
                    this.$announcer({status:409,data:{message:'please specify a Vendor for this Purchase order in order to add items..'}});
                    this.isLoading = false;
                    return false;
                }else if(this.isLoading) return false;
                else this.isLoading = true;
                this.productList = await new List()
                    .setFilters(this.productFilters.filter)
                    .params({
                        search: this.productSearch,
                        type: cat,
                        onlyReceived: (this.onlyReceived) ? 1 : 0
                    })
                    .orderBy(((this.productFilters.orderDesc) ? '-' : '')+this.productFilters.sortBy)
                    .limit(this.productFilters.pageLimit)
                    .page(this.productPage)
                    .get();
                if(this.productList.data && this.promptQty) this.productList.data.map(o=>{ o.add_qty = 1; return o;});
                this.isLoading = false;
            },
            
            clearProductSearch(){
                this.productList = null;
                this.productSearch = null;
                this.productPage = 1;
            },

            closeSearch() {
                this.productFilters = null;
                this.clearProductSearch();
            },

            initSearch(){
              if(!this.initValue) return false;
              else if(this.browseScope) return false; // we already started to browse
              else if(this.productSearch) return false; // weve searched already
              
              this.productSearch = this.initValue;
              this.setProductFilters(this.browseScope);
              this._fetchProducts(this.browseScope);
            },

            updateScope(scope){
                this.browseScope = scope;
                this.clearProductSearch();
                this.setProductFilters(this.browseScope);
                this._fetchProducts(this.browseScope);
            },
            
            renderRowBg(item,type){
                if(!item) return null;
                else if(this.inSet(item.id,this.existIds)) return 'show-inactive';
                else return null;
    	    },
        },
        
        computed: {
            schema(){
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },
        
        watch: {
            productPage(to,from){
                if(to && this.productFilters) this._fetchProducts(this.browseScope);
            },
            
            productFilters:{
                handler(to,from){
                    this.gridPage = 1;                                          // reset page to 1 if filters change
                    if(this.productFilters && from) this._fetchProducts();
                },
                deep: true
            },

            isLoading(to,from) {
                this.$nextTick(() => {
                    if (this.$refs.productSearchTable) {
                        let target = this.$refs.productSearchTable.$el;
                        target.parentNode.scrollTop = target.offsetTop;
                    }
                });
            }
        }
        
    };
    
</script>

<style scoped>
    .product-search .dropdown-menu {
        height: auto;
        max-height: 400px;
        overflow-x: hidden;
    }
    #product_search_table.is-finding {
        opacity: .5;
    }
    #product_search_table input.form-control {
        height: calc(1.5em + 0.75rem + 4px);
    }
</style>