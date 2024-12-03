<template>
    <div>
    <form v-if="schema" class="col-12">
        <fieldset>
            
            <a href="" class="float-right" @click.prevent="syncMetrcTransfers">
                <spinner :isProcessing="isSyncing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                <i v-if="!isSyncing" class="hotbox-icon hotbox-icon-refresh-69"></i> {{ (isSyncing) ? 'Syncing' : 'Sync' }} Metrc Transfers
            </a>
            
            <div class="input-group" v-if="schema.form.transfer_search">
                <div class="input-group-prepend">
                    <div class="py-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ browseText }}</div>
                    <div class="dropdown-menu tight">
                        <a v-for="(val,vid) in schema.form.transfer_search.values" :key="val.id" class="dropdown-item action-default" href="" @click.prevent="updateScope(val.id)">
                            {{ val.name }} <span class="float-right">{{ val.count }}</span>
                        </a>
                    </div>
                </div>                            
                <input type="text" 
                    v-model="transferSearch" 
                    class="form-control" 
                    placeholder="Search Manifest Number / Vendor"
                    @input="searchTransfer"
                    @keydown.enter.prevent="searchTransfer">
                    <div class="input-group-append">
                        <i v-if="transferSearch || transferList"class="ti-close mt-1" @click.prevent="clearTransferSearch"></i>
                    </div>
            </div>
                        
            <transition name="hb-slide">
                <div class="">
                    <div class="search-drop">
                        <loading :display="isLoading" type="loadModal" />

                        <b-table v-if="transferFilters" small outlined hover              
                            id="transfer_search_table"
                            primary-key="id"
                            :items="(transferList) ? transferList.data || [] : []"
                            :fields="schema.form.transfer_search.fields"
                            :busy.sync="isLoading"
                            :show-empty="true"
                            :sort-by.sync="transferFilters.sortBy"
                            :sort-desc.sync="transferFilters.orderDesc"
                            :no-local-sorting="true"
                            :no-local-filtering="true"
                            :per-page="0"
                            :tbodyTrClass="renderRowBg"
                            responsive="md"
                            stacked="sm"
                        >
                          
                            <template v-slot:cell(created_at)="row">
                                {{ row.value | localDate }}<br>
                                <span v-if="row.item.received_at" class="small">Metrc Confirmed at: {{ row.item.received_at | localDate }}</span>
                            </template>

                            <template v-slot:cell(manifest_id)="row">
                                <i class="float-right mr-2 mt-1" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}" @click="row.toggleDetails"></i>
                                {{ row.value }}<br>
                                <span class="small" v-if="row.item.manifest_data">({{ row.item.manifest_data.PackageCount || 0 }}) Packages From {{ row.item.vendor_ref }}</span>
                            </template>

                            <template v-slot:cell(status)="row">
                                {{ row.value | ucwords }}<br>
                            </template>
                            

                            <template v-slot:cell(actions)="row">
                                <button v-if="row.item.status=='confirmed'" class="btn btn-md btn-default" @click.prevent="importTransfer(row.item)">IMPORT</button>
                                <span v-else class="">{{ row.item.status | ucwords }}..</span>
                            </template>  

                            <template v-slot:row-details="row">
                                <li v-for="(pack,pid) in row.item.packages" class="d-flex justify-content-between align-items-center position-relative px-2 mb-3">
                                    <div class="">
                                        {{pack.name}}<br>
                                        <span class="small"><i class="hotbox-icon hotbox-icon-tag-content"></i> {{pack.label}}</span>
                                    </div>
                                    <div class="ml-auto text-right">
                                        {{ pack.quantity }}/{{pack.uom}}
                                    </div>
                                </li>
                            </template>

                            <template v-slot:table-caption v-if="(transferList || {}).meta && transferFilters">
                                <div v-if="transferList.meta.last_page>1" class="text-center">
                                    <a v-if="transferPage>1" href="" class="" @click.prevent="transferPage=transferPage-1">&laquo; Prev</a> |   
                                    <a v-if="transferList.meta.last_page>transferPage" href="" class="" @click.prevent="transferPage=transferPage+1">Next &raquo;</a>
                                </div>
                            </template>
                            
                            <template v-slot:empty>
                                <div v-if="!isLoading">
                                    <img src="/images/logo.png" alt="No Results" class="" width="65" />
                                    <h5 class="small">Hmm, There are currently no Transfers.</h5>
                                </div><div v-else class="h-100">&nbsp;</div>
                            </template>
            
                        </b-table>                       

                    </div>
                </div>
            </transition>

        </fieldset>
    </form>
    </div>
</template>

<script>
    import List from '../../../../models/ReceivingTransfer';
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
            }
        },
        data(){
            return {
                isLoading: false,
                transferSearch:null,
                transferFilters:null,
                transferList:null,
                transferPage:1,
                browseScope: 'confirmed',
                browseText: 'Browse',
                isSyncing:false
            };
        },
        
        components : {
            
        },
        
        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // since we are calling this from another app section, and has key counts - we need to load a fresh schema first.
            this.setTransferFilters();
            this.syncMetrcTransfers();
            this._fetchTransfers('confirmed');
        },
        
        methods: {

            syncMetrcTransfers(){
                this.isSyncing = true;
                axios.get('/api/v1/admin/dispensary/receivings/syncTransfers').then(response =>{
                    this.isSyncing = false;
                    if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'});
                    if(response.data.count>0){
                        this.$announcer(response);
                        this._fetchTransfers();                                 // refetch cause theres new synced
                    }
                }).catch(error => {
                    this.isSyncing = false;
                    this.$announcer(error.response);
                });
            },
            
            setTransferFilters(cat){
                let typ = (cat) ? [cat] : this.includeTypes;
                this.transferFilters = {                                            // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 20,
                    sortBy: 'created_at',
                    orderDesc: true,
                    filter: Object.assign({},{status:[typ]})
                };
            },
            
            searchTransfer: _.debounce(function (e) {                            // fetch existing transfers or items to import
                if(!this.transferSearch.length) this.clearTransferSearch();
                else if(this.transferSearch.length<=2) return false;
                else{ 
                    if(!this.transferFilters) this.setTransferFilters(this.browseScope);
                    this._fetchTransfers();
                }
            }, 300),
            
            async _fetchTransfers(cat=null){
                this.browseText = cat || this.browseText;
                this.isLoading = true;
                this.transferList = await new List()
                    .setFilters(this.transferFilters.filter)
                    .params({
                        search: this.transferSearch
                    })
                    .orderBy(((this.transferFilters.orderDesc) ? '-' : '')+this.transferFilters.sortBy)
                    .limit(this.transferFilters.pageLimit)
                    .page(this.transferPage)
                    .get();
                if(this.transferList.data && this.promptQty) this.transferList.data.map(o=>{ o.add_qty = (this.forOutbound) ? 1 : 10; return o;});
                
                // add imported transfer_map if prompted..
                
                
                this.isLoading = false;
            },
            
            clearTransferSearch(){
                this.transferList = null;
                this.transferSearch = null;
                this.transferPage = 1;
            },
            
            updateScope(scope){
                this.browseScope = scope;
                this.clearTransferSearch();
                this.setTransferFilters(this.browseScope);
                this._fetchTransfers(this.browseScope);
            },
            
            importTransfer(item){
                this.isLoading = true;
                axios.get('/api/v1/admin/dispensary/receivings/transfers/import/'+item.id).then(response =>{
                    this.isLoading = false;
                    if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'});
                    this.$announcer(response);
                    this.$router.push({name:'receiving_edit',params:{'id':response.data.id}}); // go to receiivng edit from passed id.
                    this.$emit('refresh');
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer(error.response);
                });
            },
            
            renderRowBg(item,type){
                if(!item) return null;
                else if(item.status=='rejected') return 'show-inactive';
                else if(item.status=='received') return 'show-primary';
                else return null;
    	    },
        },
        
        computed: {
            schema(){
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },
        
        watch: {
            transferPage(to,from){
                if(to && this.transferFilters) this._fetchTransfers();
            },
            'transferFilters.sortBy'(to,from){
                if(from!==null){
                    this.transferPage = 1;                                       // reset page to 1 if filters change
                    if(this.transferFilters) this._fetchTransfers();
                }
            },
            
            'transferFilters.orderDesc'(to,from){
                if(from!==null){
                    this.transferPage = 1;                                       // reset page to 1 if filters change
                    if(this.transferFilters) this._fetchTransfers();
                }
            },
            
            browseScope(to,from){
                this.setTransferFilters(to);
            }
        }
        
    };
    
</script>

<style>
</style>
