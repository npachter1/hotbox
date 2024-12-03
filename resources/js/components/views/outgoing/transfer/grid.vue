<template>
    <div v-if="schema" class="col-12">
        <form>
            <div class="table-filter-row">
            
                <div class="filter-search" :class="{'active':gridSearch}">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <i class="hotbox-icon hotbox-icon-search-2"></i>
                        </div>
                        <input type="text" 
                            v-model="gridSearch" 
                            class="form-control border-left-0" 
                            :placeholder="schema.lang.searchPrompt || 'Search Grid'"
                            @input="searchGrid"
                            @keydown.enter.prevent="searchGrid">
                    </div>
                </div>
                
                <div v-if="gridFilters" class="filters">

                    <filter-more v-if="gridData" 
                        :meta="gridData.meta"
                        :schema="schema"
                        :gridFilters.sync="gridFilters"
                        :columns.sync="gridColumns"
                        :isDownloading="isDownloading"
                        @downloadExport="downloadExport">
                    </filter-more>
                    
                    <filter-in v-for="(filt,fkey) in schema.filters" v-if="filt.type=='wherein'"
                        :key="fkey"
                        :schema="filt"
                        :filter="gridFilters.filter[fkey]" 
                        @update="(upd) => {gridFilters.filter[fkey] = upd}">
                    </filter-in>

                    <filter-date v-for="(filt,fkey) in schema.filters" v-if="filt.type=='daterange'"
                        :key="fkey"
                        :schema="filt"
                        :filter="gridFilters.filter[fkey]"
                        @update="(upd) => {gridFilters.filter[fkey] = upd}">
                    </filter-date>
                    
                </div>

            </div>
        </form>
        
        <loading :display="isLoading" type="loadGrid" />
        <transition name="bo-slide">
            <b-table v-if="schema && gridFilters" striped hover
                class="grid-table"
                :id="model.toLowerCase()+'_table'"
                primary-key="id"
                :items="(gridData) ? gridData.data : []"
                :fields="columnsVisible"
                :busy.sync="isLoading"
                :show-empty="true"
                :sort-by.sync="gridFilters.sortBy"
                :sort-desc.sync="gridFilters.orderDesc"
                :no-local-sorting="true"
                :no-local-filtering="true"
                :per-page="0"
                :tbodyTrClass="renderRowBg"
                :tbody-transition-props="{name:'hb-list-fade'}"
                responsive="md"
                stacked="sm"
                >

                <template v-for="(field,ind) in schema.meta.fields" v-slot:head(field.key)="column">
                  <em>
                      <i v-if="field.icon" :class="field.icon"/>
                      <i v-if="field.description" class="hotbox-icon hotbox-icon-c-question" :title="field.name"
                         v-b-tooltip.hover="field.description"/>
                    <span>{{ column.label }}</span>
                  </em>
                </template>


                <template v-slot:cell(created_at)="row">
                    {{ row.value | localDate }}
                </template>
    
                <template v-slot:cell(updated_at)="row">
                    {{ row.value | localDate }}
                </template>
                
                <template v-slot:cell(received_at)="row">
                    {{ row.value | localDate }}
                </template>
                
                <template v-slot:cell(manifest_number)="row">
                    {{ row.value }}<br>
                    <span class="small">{{ row.item.type | renderValue(schema.form.type.values) }}</span>
                </template>
                
                <template v-slot:cell(receiver)="row">
                    <span v-if="row.item.receiver">
                        {{ row.item.receiver.name }} {{row.item.receiver.licensenum }}<br>
                        <span class="small">{{ row.item.receiver.city }}</span>
                    </span>
                    <span v-else>--</span>
                </template>
                
                <template v-slot:cell(transporter_name)="row">
                    <span v-if="row.item.transporter_name">
                        {{ row.value }}<br>
                        <span class="small">Lic: {{ row.item.transporter_licensenum }}</span>
                    </span>
                    <span v-else>--</span>
                </template>

                <template v-slot:cell(packages_count)="row">
                    {{ (row.item.packages.length || 0) }}
                </template>

                <template v-slot:cell(status)="row">
                    <i class="float-right mr-2 mt-1" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}" @click="row.toggleDetails"/>
                    {{ (row.value || 'confirmed') | ucwords }}
                </template>

                <template v-slot:cell(actions)="row">
                    <div class="dropdown">
                        <a data-toggle="dropdown" class="" aria-haspopup="true" aria-expanded="false">
                            <spinner v-if="isDownloading && (invoiceData === row.item)" :isProcessing="isDownloading && (invoiceData === row.item)" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" />
                            <i v-else class="ti-more-alt"></i>
                        </a>
                        <div class="dropdown-menu tight dropdown-menu-right">
                            <a v-if="row.item.type=='external'" href="" class="dropdown-item action-primary" @click.prevent="downloadInvoice(row.item)"><i class="hotbox-icon hotbox-icon-square-download"></i> Download Invoice</a>
                            <a v-if="row.item.status=='unpaid'" href="" class="dropdown-item action-success" @click.prevent="updateStatus(row.item.id,'confirmed')"><i class="hotbox-icon hotbox-icon-s-check"></i> Mark as Paid</a>
                            <a href="" class="dropdown-item action-danger" @click.prevent="confirmDelete(row.item.id)"><i class="hotbox-icon hotbox-icon-trash-round"></i> Delete and Unlink</a>
                        </div>
                    </div>
                </template>


                <template v-slot:row-details="row">
                    <div class="card card-stats px-3 justify-content-center">
                        <div class="card-header"></div>
                        <div class="card-body">
                
                        <h5 class="mt-2">Packages Manifest</h5>
                            <ul>
                                <li v-for="(pack,pid) in row.item.packages" class="d-flex justify-content-between align-items-center position-relative px-2 mb-3">
                                    <div class="">
                                        {{(pack.item || {}).name || 'Misc Item'}}<br>
                                        <span class="small"><i class="hotbox-icon hotbox-icon-tag-content"></i> {{pack.label}}</span>
                                    </div>
                                    <div class="ml-auto text-right">
                                        {{ pack.quantity }}/{{pack.unit_of_measure}}<br>
                                        <span v-if="pack.received_price>0" class="small">${{ pack.received_price | dollar }}</span>
                                    </div>
                                </li>
                            </ul>
                            
                            <div v-if="row.item.transfersale_total>0" class="col-12 text-right mt-1 mb-3" style="border-top:1px solid #E6E6E6">
                                <span v-if="row.item.transfersale_fee>0" class="">Service Fee: ${{ row.item.transfersale_fee | dollar }}<br></span>
                                <b>Total Billed: ${{ row.item.transfersale_total | dollar }}</b>
                            </div>
                        
                        </div>
                    </div>
                </template>

                <template v-slot:table-caption v-if="gridData">
                    <div v-if="gridData.data.length>0">
                        <span v-if="gridData.meta">
                            Showing {{ ((gridData.meta.per_page*(gridPage-1))+1) }} to {{ (((gridData.meta.per_page*gridPage) < gridData.meta.total) ? (gridData.meta.per_page*gridPage) : gridData.meta.total) }} of {{ gridData.meta.total }} {{ (schema.lang.items) ? schema.lang.items : 'Records' }}
                        </span>
                        <span v-else>Showing All {{ (schema.lang.items) ? schema.lang.items : 'Records' }}</span>
                        
                        <div class="table-pager-footer">
                            <b-pagination v-if="gridData.meta.total>0"
                              v-model="gridPage"
                              :total-rows="gridData.meta.total"
                              :per-page="gridData.meta.per_page"
                              aria-controls="users_table"
                            ></b-pagination>
                        </div>
                    </div>
                </template>
                
                <template v-slot:empty>
                    <div v-if="!isLoading">
                        <img src="/images/logo.png" alt="No Results" class="" width="75" />
                        <h4>Hmm, There are currently no Results.</h4>
                    </div><div v-else class="h-100">&nbsp;</div>
                </template>

            </b-table>
        </transition>
         <b-modal centered ref="transferInvoicePreviewModal" :static=true
        size="lg"
        v-model="transferInvoicePreviewModal"
        header-bg-variant="light"
        header-text-variant="primary">
            <template slot="modal-header">
                <i class="modal-top-close fal ti-close" @click="transferInvoicePreviewAction"></i>
                <h5 class="w-100 mb-0 text-center">Invoice Preview</h5>
            </template>
            <div class="justify-content-center">
                <div>
                <TransferInvoice :item="invoiceData" ref="invoiceMaker" @downloadingInvoice="downloadingInvoice($event)"
                />
                </div>
            </div>
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="transferInvoicePreviewAction">Close</span>
            </template>
        </b-modal>    

    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Grid from '../../../../models/Transfer';
 //   import BatchEditModal from './batchEditModal';
    import TransferInvoice from '../../../elements/TransferInvoice';
    import _ from 'lodash';


    export default {
        
        props: {
            module: {
                type: String,
                default: 'outgoing',
            },
            model: {
                type: String,
                default: 'transfer'
            },
            filters: {
                type: Object,
                default: () => {}
            }
        },
        
        data(){
            return {
                isLoading: false,
                isDownloading: false,
                shouldReload: false,
                gridData: null,
                gridSearch:null,
                gridPage:1,
                gridColumns: null,
                gridFilters:null,
                invoiceData: null,
                transferInvoicePreviewModal: false
            };
        },
        
        components : { 
//          BatchEditModal
            TransferInvoice
        },
        
        mounted() {
            this.gridSearch = this.$route.query.search || null;
            this.regulatoryAgent = this.$store.getters.getAgent;
            if(this.schema) {
                this.setFilters(this.$route.params.focus);                      // focus filter tab is logged in location id.
                this.gridColumns = this.schema.meta.fields;                     // for some reason, the schema changing on edit doesnt register - need to reload upon mount
            }
            this.restoreGridSettings();
        },
        
        methods: {
            async fetchGrid(){                                                  // get the grid data
                if(!this.schema || !this.gridFilters) return false;             // dont fetch grid without a schema or the filters loaded, this will be trigered when they load
                else if(this.isLoading == true) return false;                   // do not fetch if we are already fetching

                this.$store.commit(this.module+'/setSearch',{gridPage: null, gridFilters: null, options: { merge: true} });   // clear search setting for this page
                this.$store.commit(this.module+'/setSearch',{gridPage: this.gridPage, gridFilters: this.gridFilters, options: { merge: true} });   // persist search setting

                this.isLoading = true;
                this.gridData = await new Grid()
                    .setFilters(this.gridFilters.filter)
                    .params({
                        search: this.gridSearch
                    })
                    .orderBy(((this.gridFilters.orderDesc) ? '-' : '')+this.gridFilters.sortBy)
                    .limit(this.gridFilters.pageLimit)
                    .page(this.gridPage)
                    .get();
                this.mergeGridData();
                this.isLoading = false;
            },
            
            searchGrid: _.debounce(function (e) {                               // upon search filter update, throttle .5 sec grid and scope refresh
                this.gridPage = 1; 
                //this.$store.commit(this.module+'/setSearch',this.gridSearch);   // persist search setting
                this.fetchGrid();
            }, 500),
            
            setFilters(focus='all'){
                if(!this.schema) return false;
                
                this.gridFilters = {                                            // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 20,
                    sortBy: Object.keys(this.schema.filters).find(key => this.schema.filters[key].type === 'daterange') || 'rank', // use first daterange field(key) in schema
                    orderDesc: true,                    
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map(k => { return {[k]:['all']}; }),this.filters)
                    //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }),this.filters)
                };
            },
            
            renderRowBg(item,type){
                if(!item) return null;
                else if(item.status=='unpaid') return 'table-warning';
                else return null;
    	    },
    	    
            confirmDelete(id){
                this.$swal.fire({
                  title: 'Are you sure?',
                  text: 'This will Cancel the transfer, and rollback any receiving requests and the package assignments.',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, Cancel Transfer'
                }).then((result) => {
                  if(result.value){
                    this.isLoading = true;
                    new Grid({id:id}).delete().then(response => {
                        this.isLoading = false;
                        this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // get schema for new agg data
                        this.$announcer(response);
                        this.fetchGrid();                                       // if we deleted an item, then refetch..
        	        }).catch(error => {
        	            this.isLoading = false;
                        this.$announcer(error.response);
        	        }); 
                  }
                });
            },
            
            updateStatus(id,typ){
                this.isLoading = true;
                axios.put('/api/v1/'+this.schema.meta.resource+'/'+id+'/modify',{id:id,status:typ}).then(response =>  {
                    this.isLoading = false;
                    this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // get schema for new agg data
                    this.$announcer(response);
                    this.fetchGrid();                                       // if we deleted an item, then refetch..
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer(error.response);
                });
            },

            downloadExport(typ){
                this.isDownloading = true;
                    axios({
                        url: new Grid().setFilters(this.gridFilters.filter).custom(this.schema.meta.resource+'/export/'+typ).getUrl(),
                        method: 'GET',
                        responseType: 'blob', // important
                    }).then((response) => {
                        this.isDownloading = false;
                        this.downloadFile(response);
                    }).catch(error => {
        	            this.isDownloading = false;
                        this.$announcer(error.response);
        	        });
            },
            
            downloadInvoice(rowData){
                
                this.invoiceData = rowData;

                this.$nextTick(() => {
                    this.$refs.invoiceMaker.print();
                });
            },

            downloadingInvoice(downloading){
                this.isDownloading = downloading;
            },

            openTransferInvoicePreviewModal: function () {
                this.transferInvoicePreviewModal = true;
            },

            transferInvoicePreviewAction: function(message) {
                this.transferInvoicePreviewModal = false;
            },

            rowToggle(row) {
                if (row.item.changed) {
                    this.$swal.fire({
                        title: 'Changes Not Saved',
                        text: 'Do you want to save changes?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Save Changes',
                        cancelButtonText: 'Discard Changes',
                    }).then((result) => {
                        if(result.value){
                            this.$refs.editForm._save(true);
                        } else {
                            row.item.changed=false;
                            row.toggleDetails();
                        }
                    });
                }else{
                    row.toggleDetails();
                }
            },

            rowChange(row) {
                row.item.changed=true;
            },

            rowReset(row,data=null){
                if(data){
                    let index = this.gridData.data.findIndex(v => v.id==data.id);
                    if(data.archived_at) this.gridData.data.splice(index,1);
                    else this.gridData.data.splice(index, 1, Object.assign(this.gridData.data[index],data));
                }
                row.item.changed=false;
            },

            scrollIntoView(expandedRowId) {
                //this is invoked *after* the form has expanded and form data loaded.
                // There were issues trying to scroll to the row on slow network connections
                // where the scroll went to the top then expanded so the user was staring at bottom of expansion.  Now we just wait until child component loads.
                if (this.scrollIntoViewId && this.scrollIntoViewId===expandedRowId) {
                    let rowElement = document.getElementById(this.model.toLowerCase()+'_table__row_' + this.scrollIntoViewId);
                    if (rowElement) {
                        rowElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        this.scrollIntoViewId=null; //clear so we don't try again
                    }
                }
            },

            restoreGridSettings() {
                let stateSearch = this.$store.state[this.module].search;
                if (stateSearch) {
                    if (stateSearch.gridSearch) this.gridSearch=stateSearch.gridSearch;
                    if (stateSearch.gridPage) this.gridPage=stateSearch.gridPage;
                    if (stateSearch.gridFilters) this.gridFilters=stateSearch.gridFilters;
                    if (stateSearch.gridColumns) this.gridColumns=stateSearch.gridColumns;
                }
            },

            mergeGridData() {
                if (this.$store.state[this.module].search && this.$store.state[this.module].search.gridData && Array.isArray(this.gridData.data)) {
                    //merge any saved options into griddata
                    this.$store.state[this.module].search.gridData.forEach((saved,i)=> {
                        let row = this.gridData.data.findIndex(e=>e.id===saved.id);
                        if (row > -1) this.$set(this.gridData.data[row], '_showDetails', true); //$set if the _showDetails isn't already there we need the reactivity
                        if (i===0) this.scrollIntoViewId=saved.id; //only scroll to first expanded row (in case there are > 1). Save id and we'll scroll when the individual row has expanded and form data loaded
                    });
                }
            },
        },
        
        computed: {
            schema(){
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },

            columnsVisible(){
               return (this.gridColumns) ? this.gridColumns.filter((col) => { return (col.toggle === true || !col.hasOwnProperty('toggle')); }) : [];
            }
        },

        watch: {
            gridData: {
                handler: function(newValue, oldValue) {
                    let saveGridDataOptions = newValue.data.filter(e=>e._showDetails).map(e=>{ return { id: e.id, _showDetails: e._showDetails } });
                    this.$store.commit(this.module+'/setSearch',{gridData: saveGridDataOptions, options: { merge: true} });   // persist search setting
                },
                deep: true,
            },

            gridColumns: {
                handler() {
                    this.$store.commit(this.module + '/setSearch', {gridColumns: this.gridColumns, options: {merge: true} });
                },
                deep: true
            },

            gridSearch() {
                this.$store.commit(this.module + '/setSearch', {gridSearch: this.gridSearch, options: {merge: true} });
            },

            gridFilters:{
                handler(to,from){
                    this.gridPage = 1;                                          // reset page to 1 if filters change
                    if(this.gridFilters) this.fetchGrid();
                },
                deep: true
            },

            gridPage(to,from){
                if(this.gridFilters) this.fetchGrid();
            },
            
            schema:{
                handler(to,from){
                    if(!from && to) this.setFilters();                          // if we just loaded a new schema data, then set filters (otherwise, this is set on mounted)
                    else if(!_.isEqual(to.filters, from.filters)) this.setFilters(); // if a schema filter change - reset the filters - which will refresh the grid
                    else if(this.shouldReload==true){                           // or if we set a should reload flag, reload with refreshed schema
                        this.fetchGrid();
                        this.shouldReload = false;
                    }
                    
                    if(to && !this.gridColumns) this.gridColumns = to.meta.fields; // load gridColumns if not already loaded.
                },
                deep: true
            }
        }
        
    };
    
</script>

<style scoped>
    >>> .grid-table > tbody > tr > td:last-of-type {
        width: 1%;
        white-space: nowrap;
    }
</style>
