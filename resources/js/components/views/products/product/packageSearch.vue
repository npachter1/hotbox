<template>
    <div>
    <form v-if="schema" class="">
        <fieldset>
    
            <div class="input-group package-search" v-if="schema.form.package_search">
                <div class="input-group-prepend">
                    <div class="dropdown-toggle py-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fal fa-plus"></i> {{ browseText | ucwords }}</div>
                    <div class="dropdown-menu tight">
                        <a v-for="(val,vid) in schema.form.package_search.vendors" :key="val.id" class="dropdown-item action-default" href="" @click.prevent="updateScope(val.id)">
                            {{ val.name }}
                        </a>
                        <div role="separator" class="dropdown-divider"></div>
                        <a :key="'all'" class="dropdown-item action-default" href="" @click.prevent="updateScope('all')">View/Search ALL</a>
                    </div>
                </div>                            
                <input type="text"
                       style="height:unset;"
                    v-model="packageSearch"
                    class="form-control"
                    :placeholder="schema.form.package_search.placeholder"
                    @input="searchPackage"
                    @click="initSearch"
                    @keydown.enter.prevent="searchPackage">
                    <div class="input-group-append">
                        <i v-if="packageSearch || packageList"class="ti-close mt-1" @click.prevent="closeSearch"></i>
                    </div>
            </div>
                        
            <transition name="hb-slide">
                <div class="search-contain">
                    <div class="search-drop" v-if="packageSearch || packageList || isLoading">
                        <loading :display="(!packageList || isLoading) ? true : false" type="loadModal" />

                        <b-table v-if="packageFilters" small outlined hover condensed             
                            id="package_search_table"
                            ref="packageSearchTable"
                            primary-key="id"
                            class="table-nested"
                            :class="{'is-finding':isLoading}"
                            :items="(packageList) ? packageList.data || [] : []"
                            :fields="schema.form.package_search.fields"
                            :busy.sync="isLoading"
                            :show-empty="true"
                            :sort-by.sync="packageFilters.sortBy"
                            :sort-desc.sync="packageFilters.orderDesc"
                            :no-local-sorting="true"
                            :no-local-filtering="true"
                            :per-page="0"
                            :tbodyTrClass="renderRowBg"
                            responsive="md"
                            stacked="sm"
                        >
                          
                            <template v-slot:cell(name)="row">
                                {{ row.value }} {{ row.item.package_data.ItemStrainName }}<br>
                                <span class="small">{{ row.item.package_data.PackageCategoryName }}</span>
                            </template>

                            <template v-slot:cell(label)="row">
                                {{ row.value }}<br>
                            </template>
                            
                            <template v-slot:cell(quantity)="row">
                                {{ row.item.quantity | dollar(4) }}{{row.item.uom }}<br>
                            </template>

                            <template v-slot:cell(vendor_id)="row">
                                {{ (row.item.vendor || {}).name }}<br>
                                <span class="small">Lic: {{ (row.item.vendor || {}).licensenum }}</span>
                            </template>
                            
                            <template v-slot:cell(received_at)="row">
                                {{ row.value | localDate }}<br>
                            </template>                            
                            
                            <template v-slot:cell(actions)="row">
                                <button class="btn btn-md btn-warning" @click.prevent="$emit('select',row.item)">SELECT</button>
                            </template>  


                            <template v-slot:empty>
                                <div v-if="!isLoading" class="text-center p-3">
                                    <img src="/images/logo.png" alt="No Results" class="" width="65" />
                                    <h5 class="small">Hmm, There are currently no Confirmed packages awaiting receiving.  Try Browse above..</h5>
                                </div><div v-else class="h-100">&nbsp;</div>
                            </template>
            
                        </b-table>

                        <div class="col-12">
                            <b-pagination v-if="packageList && packageList.meta.last_page>1"
                                          :total-rows="packageList.meta.total"
                                          :per-page="packageList.meta.per_page"
                                          v-model="packagePage"
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
    import List from '../../../../models/ReceivingPackage';
    import _ from 'lodash';
    export default {
        props: {
            module: {
                type: String,
                default: 'products',
            },
            model: {
                type: String,
                default: 'Receiving'
            },
            autoStart:{
                type: Boolean,
                default: false
            },
            focus: {
                type: [String,Number],
                value: null
            }
        },
        data(){
            return {
                isLoading: false,
                packageSearch:null,
                packageFilters:null,
                packageList:null,
                packagePage:1,
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
            setPackageFilters(cat){
                let typ = (cat) ? [cat] : ['all'];
                this.browseText = (this.schema.form.package_search.vendors.find(v=>v.id==typ) || {}).name || ((cat=='all') ? 'All' : 'Misc');
                this.packageFilters = {                                         // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 20,
                    sortBy: 'updated_at',
                    orderDesc: true,
                    filter: Object.assign({},{vendor_id:[typ],status:['confirmed']})
                };
            },
            searchPackage: _.debounce(function (e) {                            // fetch existing packages or items to import
                if(!this.packageSearch.length) this.clearPackageSearch();
                else if(this.packageSearch.length<=2) return false;
                else{
                    if(!this.packageFilters) this.setPackageFilters(this.browseScope);
                    this._fetchPackages();
                }
            }, 500),
            
            async _fetchPackages(cat=null){
                this.isLoading = true;
                this.packageList = await new List()
                    .setFilters(this.packageFilters.filter)
                    .params({
                        search: this.packageSearch
                    })
                    .orderBy(((this.packageFilters.orderDesc) ? '-' : '')+this.packageFilters.sortBy)
                    .limit(this.packageFilters.pageLimit)
                    .page(this.packagePage)
                    .get();
                if(this.packageList.data && this.promptQty) this.packageList.data.map(o=>{ o.add_qty = 1; return o;});
                
                // add imported package_map if prompted..
                
                
                this.isLoading = false;
            },
            
            clearPackageSearch(){
                this.packageList = null;
                this.packageSearch = null;
                this.packagePage = 1;
            },

            closeSearch() {
                this.packageFilters = null;
                this.clearPackageSearch();
            },

            updateScope(scope){
                this.browseScope = scope;
                this.clearPackageSearch();
                this.setPackageFilters(this.browseScope);
                this._fetchPackages(this.browseScope);
            },
            
            initSearch(){
              if(!this.autoStart) return false;
              else if(this.packageSearch) return false;
              
              if(this.focus && this.browseScope == this.focus) this._fetchPackages(this.browseScope);
              else if(this.focus && this.browseScope!='all') this.browseScope = this.focus;                // if no search and autoStart then set the scope which will rerender search.
            },
            
            renderRowBg(item,type){
                if(!item) return null;
                return null;
    	    },
        },
        
        computed: {
            schema(){
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },
        
        watch: {
            packagePage(to,from){
                if(to && this.packageFilters) this._fetchPackages(this.browseScope);
            },
            'packageFilters.sortBy'(to,from){
                if(from!==null){
                    this.packagePage = 1;                                       // reset page to 1 if filters change
                    if(this.packageFilters) this._fetchPackages();
                }
            },
            
            'packageFilters.orderDesc'(to,from){
                if(from!==null){
                    this.packagePage = 1;                                       // reset page to 1 if filters change
                    if(this.packageFilters) this._fetchPackages();
                }
            },
            
            browseScope(to,from){
                this.setPackageFilters(to);
            },

            isLoading(to,from) {
                this.$nextTick(() => {
                    if (this.$refs.packageSearchTable) {
                        let target = this.$refs.packageSearchTable.$el;
                        target.parentNode.scrollTop = target.offsetTop;
                    }
                });
            }
        }
        
    };
    
</script>

<style scoped>
    .package-search .dropdown-menu {
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