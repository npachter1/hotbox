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
                        <template v-if="fkey=='status'" slot="text-right">
                            <span class="float-right">Show Unpaid<form-boolean :declared="isOnlyUnpaid" :schema="{name:'Show Unpaid'}" :hideLabel="true" @input="(upd) => {isOnlyUnpaid = upd}" class="" /></span>
                        </template>
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
                responsive="md"
                stacked="sm">
              
                <template v-for="(field,ind) in schema.meta.fields" v-slot:head(field.key)="column">
                  <em>
                    <i v-if="field.icon" :class="field.icon"></i> 
                    <i v-if="field.description" class="hotbox-icon hotbox-icon-c-question" :title="field.name" v-b-tooltip.hover="field.description"></i> 
                    <span>{{ column.label }}</span>
                  </em>
                </template>

                <template v-slot:cell(created_at)="row">
                    <span v-if="row.item.settled_at">{{ row.item.settled_at | localDate('MM/DD/YY LTS') }}</span>
                    <span v-else>{{ row.value | localDate('MM/DD/YY LTS') }}</span>
                </template>

                <template v-slot:cell(updated_at)="row">
                    {{ row.value | localDate('MM/DD/YY LTS') }}
                </template>

                <template v-slot:cell(order_number)="row">
                    {{ row.value }}<br>
                    <span class="small">metrc: {{ row.item.metrc_receipt }}</span>
                </template>

                <template v-slot:cell(drawer_id)="row">
                    <span v-if="row.item.drawer" class="">{{ row.item.drawer.name }} since {{ row.item.drawer.created_at | localDate('MM/DD/YY LTS') }}<br>
                       <span v-if="row.item.drawer.user" class="small">By {{ row.item.drawer.user.name }} - {{ row.item.payments.length }} Transactions.</span>
                    </span>
                   <span v-else>n/a</span>
                </template>
                
                <template v-slot:cell(customer_id)="row">
                   <span v-if="row.item.customer" class="">{{ row.item.customer.first_name }} {{ row.item.customer.last_name }}<br>
                       <span v-if="row.item.customer.address" class="small">{{ row.item.customer.address.city }}, {{ row.item.customer.address.region }} {{ row.item.customer.address.zip }}</span>
                   </span>
                   <span v-else>n/a</span>
                </template>
                
                <template v-slot:cell(sale_price)="row">
                    ${{ row.value | dollar }}
                </template>
                
                <template v-slot:cell(discount)="row">
                    ${{ row.value | dollar }}
                </template>

                <template v-slot:cell(tax)="row">
                    ${{ row.value | dollar }}
                </template>
                
                <template v-slot:cell(items)="row">
                    <router-link :to="{name: model.toLowerCase()+'_edit',params:{id:row.item.id}}" tag="a" class=""><i class="hotbox-icon hotbox-icon-c-info float-right"></i></router-link>           
                    <div v-if="item.inventory" v-for="(item,iid) in row.item.items" :key="item.id" class="col-11 small">
                        {{ item.inventory.item_barcode }} X {{ item.quantity }} - ${{ item.sale_price | dollar }} 
                    </div>
                </template>
                
                <template v-slot:cell(status)="row">
                    <div v-if="Array.isArray(row.item.payments) && row.item.payments.length>0 && (row.item.payments||[{}])[0].payment_method==='account' && (row.item.payments||[{}])[0].amount_owed>0" style="color: var(--danger);">Need Payment</div>
                    <div v-else>{{ row.value | ucwords }}</div>
                </template>
                
                <template v-slot:cell(actions)="row">
                    <div class="dropdown">
                        <i class="mr-2 mt-1"
                           :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}"
                           @click="rowToggle(row)"/>

                        <a data-toggle="dropdown" class="" aria-haspopup="true" aria-expanded="false"><i class="ti-more-alt"/></a>
                        <div class="dropdown-menu tight dropdown-menu-right">
                            <router-link :to="{name: model.toLowerCase()+'_edit',params:{id:row.item.id}}" tag="a" class="dropdown-item"><i class="hotbox-icon hotbox-icon-c-info"></i> OrderDetails</router-link>
                            <a href="" @click.prevent="downloadExportFile(row.item.id,'pdf')" class="dropdown-item"><i class="hotbox-icon hotbox-icon-download-file"></i> Download Receipt Copy</a>
                            <a href="" class="dropdown-item table-warning" @click.prevent="viewEditModal(row.item.id)"><i class="hotbox-icon hotbox-icon-redo-81"></i> Refund/Modify</a>
                        </div>
                    </div>
                </template>


                <template v-slot:row-details="row">
                    <div class="card card-stats px-3 py-3 justify-content-center">
                        <edit-form ref="editForm"
                                   :id="row.item.id"
                                   type="modal"
                                   :filters="gridFilters.filter"
                                   @change="rowChange(row)"
                                   @refresh="(upd) => rowReset(row,upd)"
                                   @toggle="rowToggle(row)"
                                   @loaded="scrollIntoView(row.item.id)"/>
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
                                          aria-controls="users_table"/>
                        </div>
                    </div>
                </template>
                
                <template v-slot:empty>
                    <div v-if="!isLoading">
                        <img src="/images/logo.png" alt="No Results" class="" width="115" />
                        <h4>Hmm, There are currently no Results.</h4>
                    </div><div v-else class="h-100">&nbsp;</div>
                </template>

            </b-table>
        </transition>
        
        <b-modal centered ref="editModal"
            no-enforce-focus
            v-model="editModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="editModal=!editModal"></i>
              <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-tag-line"></i> Sales Manager</h5>
            </template>
          
              <modify-modal v-if="editModal"
                :id="editModalId"
                @close="editModal=false"
                @refresh="editModal=!editModal">
              </modify-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="editModal=!editModal">Close</span>
            </template>
        </b-modal>
        
    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>

<script>
    import Grid from '../../../../models/Sale';
    import ModifyModal from './modifyModal';
    import EditForm from './editForm';
    import _ from 'lodash';

    export default {
        
        props: {
            module: {
                type: String,
                default: 'pos',
            },
            model: {
                type: String,
                default: 'sale'
            },
            filters: {                                                          // optional initial filters (filters.filter) object can be passed via this prop!
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
                editModal:false,
                editModalId:0,
                scrollIntoViewId: null,
                isOnlyUnpaid:false
            };
        },
        
        components : {
            ModifyModal,
            EditForm
        },
        
        mounted() {
            //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
            this.gridSearch = this.$route.query.search || null;
            
            if(this.schema){
                this.setFilters(this.$route.params.focus);                      // if we have schema, then set filters, else we watch schema load/change and then set.
                this.gridColumns = this.schema.meta.fields;                     // for some reason, the schema changing on edit doesnt register - need to reload upon mount
            }

            this.restoreGridSettings();
        },
        
        methods: {
            async fetchGrid(){                                                  // get the grid data
                if(!this.schema || !this.gridFilters) return false;             // dont fetch grid without a schema or the filters loaded, this will be trigered when they load
                else if(this.isLoading == true) return false;                   // do not fetch if we are already fetching

                this.$store.commit(this.module+'/setSearch',{gridPage: null, gridFilters: null, onlyUnpaid: null, options: { merge: true} });   // clear search setting for this page
                this.$store.commit(this.module+'/setSearch',{gridPage: this.gridPage, onlyUnpaid: this.isOnlyUnpaid, gridFilters: this.gridFilters, options: { merge: true} });   // persist search setting

                this.isLoading = true;
                this.gridData = await new Grid()
                    .setFilters(this.gridFilters.filter)
                    .params({
                        search: this.gridSearch,
                        onlyUnpaid: this.isOnlyUnpaid
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
                    sortBy: Object.keys(this.schema.filters).find(key => this.schema.filters[key].type === 'daterange') || 'rank', // use first daterange filter field(key) in schema
                    orderDesc: false,
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return (this.schema.filters[k].type=='daterange') ? {[k]:this.schema.filters[k].values.map((v) => { return v.id; })} : {[k]:['all']}; }),this.filters)
                    //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }),this.filters)
                    //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:['all']}; }),this.filters)
                };
            },
            
            renderRowBg(item,type){
                if(!item) return null;
                else if(item.status=='pending') return 'table-warning';
                else if(item.status=='voided') return 'show-inactive';
                else if(item.status=='refunded') return 'show-danger';
                return null;
    	    },

            viewEditModal(id){
                this.editModalId = id;
                this.editModal=!this.editModal;
            },
            
            // goToSale(item){
            //   this.$router.push({name:'sale_edit',params:{id:item.id}});
            // },
            //
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

            restoreGridSettings() {
                let stateSearch = this.$store.state[this.module].search;
                if (stateSearch) {
                    if (stateSearch.gridSearch) this.gridSearch=stateSearch.gridSearch;
                    if (stateSearch.gridPage) this.gridPage=stateSearch.gridPage;
                    if (stateSearch.gridFilters) this.gridFilters=stateSearch.gridFilters;
                    if (stateSearch.gridColumns) this.gridColumns=stateSearch.gridColumns;
                    if (stateSearch.onlyUnpaid) this.isOnlyUnpaid=stateSearch.onlyUnpaid;
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

            scrollIntoView(expandedRowId) {
                //this is invoked *after* the for has expanded and form data loaded.
                // There were issues trying to scroll to the row on slow network connections
                // where the scroll went to the top then expanded so the user was staring at bottom of expansion.  Now we just wait until child component loads.
                if (this.scrollIntoViewId && this.scrollIntoViewId===expandedRowId) {
                    let rowElement = document.getElementById('customer_table__row_' + this.scrollIntoViewId);
                    if (rowElement) {
                        rowElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        this.scrollIntoViewId=null; //clear so we don't try again
                    }
                }
            }
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
            '$route.query.search': function (search) {
                this.gridSearch = this.$route.query.search || null;
                this.searchGrid();
            },

            gridData: {
                handler: function(newValue, oldValue) {
                    let saveGridDataOptions = newValue.data.filter(e=>e._showDetails).map(e=>{ return { id: e.id, _showDetails: e._showDetails } });
                    this.$store.commit(this.module+'/setSearch',{gridData: saveGridDataOptions, onlyUnpaid: this.isOnlyUnpaid, options: { merge: true} });   // persist search setting
                },
                deep: true,
            },

            gridColumns: {
                handler() {
                    this.$store.commit(this.module + '/setSearch', {gridColumns: this.gridColumns, onlyUnpaid: this.isOnlyUnpaid, options: {merge: true} });
                },
                deep: true
            },

            gridFilters:{
                handler(to,from){
                    if(this.gridFilters) {
                        if (from && !this.isLoading) this.gridPage = 1;                                          // reset page to 1 if filters change
                        this.fetchGrid();
                    }
                },
                deep: true
            },

            gridPage(to,from){
                if(this.gridFilters) this.fetchGrid();
            },

            gridSearch() {
                this.$store.commit(this.module + '/setSearch', {gridSearch: this.gridSearch, options: {merge: true} });
            },

            isOnlyUnpaid(){
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
    #sale_table > tbody > tr > td:last-of-type {
        width: 1%;
        white-space: nowrap;
    }
</style>
