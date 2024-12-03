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
                responsive="md"
                stacked="sm"
                >
            
                <!-- I think the reason this doesn't work is using the key for the slot with a v-for in the template -->
                <!-- <template v-for="(field,ind) in schema.meta.fields" v-slot:head(field.key)="column">
                  <em>
                    <i v-if="field.icon" :class="field.icon"></i> 
                    <i v-if="field.description" class="hotbox-icon hotbox-icon-c-question" :title="field.name" v-b-tooltip.hover="field.description"></i> 
                    <span v-if="field.key=='batch_ids'">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" :checked="isAllInBatch" @click="toggleBatchAll"><span class="custom-control-indicator"></span> 
                        </label>                       
                    </span>
                    <span v-else>{{ column.label }}</span>
                  </em>
                </template> -->

                <template v-for="(field,ind) in schema.meta.fields"  v-slot:head()="column">
                    <span v-if="column.field.key ==='batch_ids'">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :checked="isAllInBatch" @click="toggleBatchAll"><span class="custom-control-indicator"></span> 
                    </label>
                    </span>
                    <span v-else>
                        <i v-if="column.field.icon && column.field.key !=='actions'" :class="column.field.icon"></i> 
                        <i v-if="column.field.description" class="hotbox-icon hotbox-icon-c-question" :title="column.field.name" v-b-tooltip.hover="column.field.description"></i> 
                        <span v-else>{{column.label}}</span>
                    </span>     
                </template>

                <template v-slot:cell(batch_ids)="row">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :checked="inSet(row.item.id,batchEditIds)" :disabled="false" @click="toggleBatchId(row.item.id,$event)"><span class="custom-control-indicator"></span> 
                    </label>
                </template>

                <template v-slot:cell(planted_at)="row">
                    {{ row.value | formattedLocalDate }}
                </template>    

                <template v-slot:cell(strain_id)="row">
                    <span v-if="row.item.strain_id">
                        {{ row.item.strain.name }}
                    </span><span v-else>n/a</span>
                </template>       

                <template v-slot:cell(room_id)="row">
                    <span v-if="row.item.room_id">
                        {{(row.item.room||{}).name }}
                    </span><span v-else>n/a</span>
                </template>       
                
                <template v-slot:cell(actions)="row">
                    <div class="dropdown">
                        <i class="mr-2 mt-1"
                           :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}"
                           @click="rowToggle(row)"/>
                        <a data-toggle="dropdown" class="" aria-haspopup="true" aria-expanded="false">
                            <i class="ti-more-alt"></i>
                        </a>
                        <div class="dropdown-menu tight dropdown-menu-right">
                            <router-link :to="{name: model+'_show',params:{id:row.item.id}}" tag="a" class="dropdown-item"><i class="hotbox-icon"></i> View Details  </router-link>
                            <a @click.prevent="viewSplitBatchModal(row.item.id)" class="dropdown-item"><i class="hotbox-icon"></i> Split Batch</a>
                            <a @click.prevent="viewMoveTagModal(row.item.id)" class="dropdown-item"><i class="hotbox-icon"></i> Move & Tag Plants</a>
                            <a @click.prevent="viewCreateSinglePackageModal(row.item.id)" class="dropdown-item"><i class="hotbox-icon"></i> Create Package</a>
                            <a @click.prevent="viewDestroyPlantsModal(row.item.id)" class="dropdown-item"><i class="hotbox-icon"></i> Destroy Plants</a>
                            <!-- <a @click.prevent="viewAddMaterialModal(row.item.id)" class="dropdown-item"><i class="hotbox-icon"></i> Add Materials</a>
                            <a @click.prevent="viewAddActivityModal(row.item.id)" class="dropdown-item"><i class="hotbox-icon"></i> Add Activity</a> -->
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
                                   @archive="confirmDelete(row.item.id)"
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
                              aria-controls="users_table"
                            ></b-pagination>
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
        
        <b-modal centered ref="moveTagModal"
            v-model="moveTagModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="moveTagModal=!moveTagModal"></i>
              <h5 class="w-100 mb-0 text-center">Move & Tag Plants</h5>
            </template>
          
              <move-tag-modal v-if="moveTagModal"
                :id="moveTagModalId"
                @refresh="fetchGrid();moveTagModal=!moveTagModal">
              </move-tag-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="moveTagModal=!moveTagModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="splitBatchModal"
            v-model="splitBatchModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="splitBatchModal=!splitBatchModal"></i>
              <h5 class="w-100 mb-0 text-center">Split Plant Batch</h5>
            </template>
          
              <split-batch-modal v-if="splitBatchModal"
                :id="splitBatchModalId"
                @refresh="fetchGrid();splitBatchModal=!splitBatchModal">
              </split-batch-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="splitBatchModal=!splitBatchModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="destroyPlantsModal"
            v-model="destroyPlantsModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="destroyPlantsModal=!destroyPlantsModal"></i>
              <h5 class="w-100 mb-0 text-center">Destroy Plants</h5>
            </template>
          
              <destroy-plants-modal v-if="destroyPlantsModal"
                :id="destroyPlantsModalId"
                @refresh="fetchGrid();destroyPlantsModal=!destroyPlantsModal">
              </destroy-plants-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="destroyPlantsModal=!destroyPlantsModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="addMaterialModal"
            v-model="addMaterialModal"
            size="md"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="addMaterialModal=!addMaterialModal"></i>
              <h5 class="w-100 mb-0 text-center">Add Material</h5>
            </template>
          
              <add-material-modal v-if="addMaterialModal"
                :id="addMaterialModalId"
                @refresh="addMaterialModal=!addMaterialModal">
              </add-material-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="addMaterialModal=!addMaterialModal">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="addActivityModal"
            v-model="addActivityModal"
            size="md"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="addActivityModal=!addActivityModal"></i>
              <h5 class="w-100 mb-0 text-center">Add Activity</h5>
            </template>
          
              <add-activity-modal v-if="addActivityModal"
                :id="addActivityModalId"
                @refresh="addActivityModal=!addActivityModal">
              </add-activity-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="addActivityModal=!addActivityModal">Close</span>
            </template>
        </b-modal>

         <b-modal centered ref="createSinglePackageModal"
            v-model="createSinglePackageModal"
            size="md"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="createSinglePackageModal=!createSinglePackageModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Create Package</h5>
            </template>
          
              <create-single-package-modal v-if="createSinglePackageModal"
                :id="createSinglePackageModalId"
                @refresh="fetchGrid();createSinglePackageModal=!createSinglePackageModal">
              </create-single-package-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="createSinglePackageModal=!createSinglePackageModal;singleEditId = []">Close</span>
            </template>
        </b-modal>


    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Grid from '../../../../models/PlantBatches';
    import CreateSinglePackageModal from './createSinglePackageModal';
    import MoveTagModal from './moveTagModal';
    import DestroyPlantsModal from './destroyPlantsModal';
    import AddMaterialModal from './addMaterialModal';
    import AddActivityModal from './addActivityModal';
    import SplitBatchModal from './splitBatchModal';
    import EditForm from './showDetails';
    import _ from 'lodash';


    export default {
        
        props: {
            module: {
                type: String,
                default: 'plants',
            },
            model: {
                type: String,
                default: 'plantBatch'
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
                batchEditModal:false,
                batchEditFocus:'archive',
                batchEditIds:[],
                moveTagModal:false,
                moveTagModalId:0,
                createSinglePackageModal:false,
                createSinglePackageModalId:0,
                splitBatchModal:false,
                splitBatchModalId:0,
                destroyPlantsModal:false,
                destroyPlantsModalId:0,
                addMaterialModal:false,
                addMaterialModalId:0,
                addActivityModal:false,
                addActivityModalId:0,
                regulatoryAgent:null,
                scrollIntoViewId: null,
            };
        },
        
        components : { 
            CreateSinglePackageModal,
            MoveTagModal,
            DestroyPlantsModal,
            AddMaterialModal,
            AddActivityModal,
            EditForm,
            SplitBatchModal
        },
        
        mounted() {
            // this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
            this.gridSearch = this.$route.query.search || null;

            this.regulatoryAgent = this.$store.getters.getAgent;
            if(this.schema) {
                this.setFilters(this.$route.params.focus);                      // if we have schema, then set filters, else we watch schema load/change and then set.
                this.gridColumns = this.schema.meta.fields.filter((col) => { return (col.regulatory_agent === this.regulatoryAgent || !col.hasOwnProperty('regulatory_agent')); });
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
                    orderDesc: false,                    
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map(k => { return {[k]:['all']}; }),this.filters)
                    //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }),this.filters)
                };
            },
            
            renderRowBg(item,type){
                if(!item) return null;
                return (this.inSet(item.id,this.batchEditIds)) ? 'table-success' : null;
    	    },
    	    
            rowClickHandler(item){
                this.$router.push({name: this.model +'_show',params:{id:item.id}});
            },

            confirmDelete(id){
                this.$swal.fire({
                  title: 'Are you sure?',
                  text: 'This will Delete this '+this.model+' row.',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, delete row'
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

            toggleBatchAll(e){
                if(e.target.checked) this.batchEditIds = this.gridData.data.map((v) => { return v.id; });
                else this.batchEditIds = [];
            },
            
            toggleBatchId(val,e){
                if(e.target.checked){
                    if(this.batchEditIds.indexOf(val) === -1) this.batchEditIds.push(val);
                }else this.batchEditIds.splice(this.batchEditIds.indexOf(val), 1);
            },
            
            refreshFromModal(upd,typ='batchedit'){
                if(typ=='batchedit') this.batchEditModal=!this.batchEditModal;
                this.shouldReload = true;
            },

            viewCreateSinglePackageModal(id){
                this.createSinglePackageModalId = id;
                this.createSinglePackageModal=!this.createSinglePackageModal;
            },
            
            viewMoveTagModal(id){
                this.moveTagModalId = id;
                this.moveTagModal=!this.moveTagModal;
            },

            viewSplitBatchModal(id){
                this.splitBatchModalId = id;
                this.splitBatchModal=!this.splitBatchModal;
            },

            viewDestroyPlantsModal(id){
                this.destroyPlantsModalId = id;
                this.destroyPlantsModal=!this.destroyPlantsModalModal;
            },

            viewAddMaterialModal(id){
                this.addMaterialModalId = id;
                this.addMaterialModal=!this.addMaterialModal;
            },

            viewAddActivityModal(id){
                this.addActivityModalId = id;
                this.addActivityModal=!this.addActivityModal;
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
            },
            
            isAllInBatch(){
                if(!this.gridData) return false;
    		    else return (this.batchEditIds.length == this.gridData.data.length) ? true : false;
    		},
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
