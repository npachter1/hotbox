<template>
    <div v-if="schema" class="col-12" @click.left="hideActionMenu()">
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
                        <input type="checkbox" class="custom-control-input" :checked="inSet(row.item.id,batchEditIds)" :disabled="!canSelect(row.item)" @click="toggleBatchId(row.item.id,$event)"><span class="custom-control-indicator"></span> 
                    </label>
                </template>

                <template v-slot:cell(quantity)="row">
                    {{ row.value }} {{ row.item.unit_of_measure }}
                </template>

                <template v-slot:cell(product_id)="row">
                    <span v-if="row.item.product_id">
                        {{ row.item.item.name }}
                    </span><span v-else>n/a</span>
                     <span v-if="row.item.received_from_manifest_number" class="d-block w-100 small"><b><i class="hotbox-icon hotbox-icon-c-info"></i> Package has been Transfered via {{ row.item.received_from_manifest_number }}</b></span>
                </template>
                
                <template v-slot:cell(packaged_at)="row">
                    {{ row.value | formattedLocalDate }}
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
                            <router-link :to="{name: model.toLowerCase()+'_show',params:{id:row.item.id}}" tag="a" class="dropdown-item"><i class="hotbox-icon"></i> View Details</router-link>
                            <a v-if="canSelect(row.item)" @click.prevent="viewChangeItemModal(row.item)" class="dropdown-item"><i class="hotbox-icon"></i> Change Product</a>
                            <a v-if="canSelect(row.item)" @click.prevent="viewAdjustModal(row.item)" class="dropdown-item"><i class="hotbox-icon"></i> Adjust</a>
                            <a v-if="canSelect(row.item)" @click.prevent="viewRemediateModal(row.item)" class="dropdown-item"><i class="hotbox-icon"></i> Remediate</a>
                            <a v-if="regulatoryAgent == 'metrc' && row.item.item.metrc_category.product_category_type == 'Plants' && row.item.transfer_id == null? true : row.item.transfer_id == null && row.item.item.category_type == 'Plants'" @click.prevent="viewCreatePlantingsModal(row.item)" class="dropdown-item"><i class="hotbox-icon"></i> Create Plantings</a>
                            <a v-if="canSelect(row.item)" @click.prevent="viewCreateSinglePackageModal(row.item)" class="dropdown-item"><i class="hotbox-icon"></i> Create Single Package</a>
                            <router-link v-if="canSelect(row.item)" :to="{name: model.toLowerCase()+'_create_packages',params:{id:parseInt(row.item.id), package:row.item}}" tag="a" class="dropdown-item"><i class="hotbox-icon"></i> Create Packages</router-link>
                            <a v-if="canSelect(row.item)" @click.prevent="viewCreateTestingPackageModal(row.item)" class="dropdown-item"><i class="hotbox-icon"></i> Create Testing Package</a>
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
        
        <div class="col-12 batch-block">
            <i class="batch-icon"></i> With Selected: 
            <select v-model="batchEditFocus" class="batch-sel">
                <option value="createSinglePackage">Create Package</option>
                <option value="createTestingPackage">Create Testing Package</option>
                <option value="createTransfer">Transport Packages</option>
            </select> 
            <button class="btn btn-sm" :class="{'btn-light':batchEditIds.length<=0,'btn-warning':batchEditIds.length>0}" @click.prevent="viewModal()" :disabled="(batchEditIds.length>0) ? false : true">Batch It!</button>
        </div>

        <div id="package_action_menu" ref="package_action_menu" class="drowdown-menu">
            <a class="dropdown-item" @click="createSinglePackageModal=!createSinglePackageModal">Create Package</a>
            <a class="dropdown-item" @click="createTestingPackageModal=!createTestingPackageModal">Create Testing Package</a>
        </div>

        <b-modal centered ref="changeItemModal"
            v-model="changeItemModal"
            size="md"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="changeItemModal=!changeItemModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Change Product</h5>
            </template>
          
              <change-item-modal v-if="changeItemModal"
                :ids.sync="singleEditId.length == 1 ? singleEditId : batchEditIds"
                :package="changeItemModalItem"
                @refresh="refreshFromModal('changeItem')">
              </change-item-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="changeItemModal=!changeItemModal;singleEditId = []">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="adjustModal"
            v-model="adjustModal"
            size="md"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="adjustModal=!adjustModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Adjust Package</h5>
            </template>
          
              <adjust-modal v-if="adjustModal"
                :ids.sync="singleEditId.length == 1 ? singleEditId : batchEditIds"
                :package="adjustModalItem"
                @refresh="refreshFromModal('adjust')">
              </adjust-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="adjustModal=!adjustModal;singleEditId = []">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="remediateModal"
            v-model="remediateModal"
            size="md"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="remediateModal=!remediateModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Remediate Package</h5>
            </template>
          
              <remediate-modal v-if="remediateModal"
                :ids.sync="singleEditId.length == 1 ? singleEditId : batchEditIds"
                :package="remediateModalItem"
                @refresh="refreshFromModal('remediate')">
              </remediate-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="remediateModal=!remediateModal;singleEditId = []">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="createPlantingsModal"
            v-model="createPlantingsModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="createPlantingsModal=!createPlantingsModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Create Plantings</h5>
            </template>
          
              <create-plantings-modal v-if="createPlantingsModal"
                :ids.sync="singleEditId.length == 1 ? singleEditId : batchEditIds"
                :package="createPlantingsModalItem"
                @refresh="refreshFromModal('createPlantings')">
              </create-plantings-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="createPlantingsModal=!createPlantingsModal;singleEditId = []">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="createSinglePackageModal"
            v-model="createSinglePackageModal"
            size='md'
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="createSinglePackageModal=!createSinglePackageModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Create Package</h5>
            </template>
          
              <create-single-package-modal v-if="createSinglePackageModal"
                :ids.sync="singleEditId.length == 1 ? singleEditId : batchEditIds"
                :sources="getSelectedPackages()"
                @refresh="refreshFromModal('createSinglePackage')">
              </create-single-package-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="createSinglePackageModal=!createSinglePackageModal;singleEditId = []">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="createTestingPackageModal"
            v-model="createTestingPackageModal"
            size='md'
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="createTestingPackageModal=!createTestingPackageModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Create Testing Package</h5>
            </template>
          
              <create-testing-package-modal v-if="createTestingPackageModal"
                :ids.sync="singleEditId.length == 1 ? singleEditId : batchEditIds"
                :sources="getSelectedPackages()"
                @refresh="refreshFromModal('createTestingPackage')">
              </create-testing-package-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="createTestingPackageModal=!createTestingPackageModal;singleEditId = []">Close</span>
            </template>
        </b-modal>

        <b-modal centered ref="createTransferModal"
            v-model="createTransferModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="createTransferModal=!createTransferModal;singleEditId = []"></i>
              <h5 class="w-100 mb-0 text-center">Create A Transfer</h5>
            </template>
          
              <create-transfer-modal v-if="createTransferModal"
                :ids="singleEditId.length == 1 ? singleEditId : batchEditIds"
                :sources="getSelectedPackages()"
                @refresh="refreshFromModal('createTransferModal')">
              </create-transfer-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="createTransferModal=!createTransferModal;singleEditId = []">Close</span>
            </template>
        </b-modal>

    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Grid from '../../../../models/Package';
    import ChangeItemModal from './changeItemModal';
    import AdjustModal from './adjustModal';
    import RemediateModal from './remediateModal';
    import CreatePlantingsModal from './createPlantingsModal';
    import CreateSinglePackageModal from './createSinglePackageModal';
    import CreateTestingPackageModal from './createTestingPackageModal';
    import CreateTransferModal from '../transfer/createTransferModal';
    import EditForm from './showDetails';
    import _ from 'lodash';


    export default {
        
        props: {
            module: {
                type: String,
                default: 'outgoing',
            },
            model: {
                type: String,
                default: 'package'
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
                gridSearch: null,
                gridPage: 1,
                gridColumns: null,
                gridFilters: null,
                changeItemModal: false,
                changeItemModalItem: null,
                adjustModal: false,
                adjustModalItem: null,
                remediateModal: false,
                remediateModalItem: null,
                createPlantingsModal: false,
                createPlantingsModalItem: null,
                createSinglePackageModal: false,
                createTestingPackageModal: false,
                createTransferModal: false,
                batchEditFocus: '',
                batchEditIds: [],
                singleEditId: [],
                action_menu_visibility: 0,
                regulatoryAgent:null,
                scrollIntoViewId: null,
            };
        },
        
        components : { 
            ChangeItemModal,
            AdjustModal,
            RemediateModal,
            CreatePlantingsModal,
            CreateSinglePackageModal,
            CreateTestingPackageModal,
            CreateTransferModal,
            EditForm,
        },
        
        mounted() {
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
                else if(item.received_from_manifest_number){
                 return 'table-primary';   
                }else if(this.batchEditIds.length > 0)
                {
                    return (this.inSet(item.id,this.batchEditIds)) ? 'table-success' : null;
                }
                else
                {
                    return (this.inSet(item.id,this.singleEditId)) ? 'table-success' : null;   
                }
    	    },
    	    
            rowClickHandler(item){
                //this.$router.push({name: this.model.toLowerCase()+'_show',params:{id:item.id}});
                return null;
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

            canSelect(row){

                if(row.transfer_id !== null)
                {
                    return false;
                }
                return true;    

            },

            toggleBatchAll(e){
                if(e.target.checked) this.batchEditIds = this.gridData.data.filter((v) => { if(v.transfer_id === null) return v.id;}).map((i) => { return i.id;});
                else this.batchEditIds = [];
            },
            
            toggleBatchId(val,e){
                if(e.target.checked){
                    if(this.batchEditIds.indexOf(val) === -1) this.batchEditIds.push(val);
                }else this.batchEditIds.splice(this.batchEditIds.indexOf(val), 1);
            },

            refreshFromModal(typ='createSinglePackage'){
                if(typ=='createSinglePackage') this.createSinglePackageModal=!this.createSinglePackageModal;
                else if(typ=='createTestingPackage') this.createTestingPackageModal=!this.createTestingPackageModal;
                else if(typ=='createTransfer') this.createTransferModal=!this.createTransferModal;
                else if(typ=='changeItem') this.changeItemModal=!this.changeItemModal;
                else if(typ=='remediate') this.remediateModal=!this.remediateModal;
                else if(typ=='adjust') this.adjustModal=!this.adjustModal;
                else if(typ=='unfinish') this.unfinishModal=!this.unfinishModal;
                else if(typ=='addMaterial') this.addMaterialModal=!this.addMaterialModal;
                else if(typ=='addActivity') this.addActivityModal=!this.addActivityModal;
                this.batchEditIds = [];
                this.singleEditId = [];
                this.shouldReload = true;
                this.fetchGrid();
            },
            
            viewModal(){
                if (this.batchEditFocus == 'createSinglePackage')
                    this.createSinglePackageModal=!this.createSinglePackageModal;
                if (this.batchEditFocus == 'createTestingPackage')
                    this.createTestingPackageModal=!this.createTestingPackageModal;
                if (this.batchEditFocus == 'createTransfer')
                    this.createTransferModal=!this.createTransferModal;
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

            getSelectedPackage() {
                if(this.batchEditIds.length > 0)
                {
                    return this.gridData.data.find(h => h.id === this.batchEditIds[0]);
                }
                else
                { 
                    return this.gridData.data.find(h => h.id === this.singleEditId[0]);
                }
            },

            getSelectedPackages() {
                var packages = [];
                this.batchEditIds.forEach((id) => {
                    packages.push(this.gridData.data.find(h => h.id === id));
                });
                this.singleEditId.forEach((id) => {
                    packages.push(this.gridData.data.find(h => h.id === id));
                });
                return packages;
            },

            showActionMenu(item, ind, e) {
                if (item.metrc_status === 'synced' ) {
                    var menuPosition = this.getPosition(e);
                    var menuPositionX = menuPosition.x + "px";
                    var menuPositionY = menuPosition.y + "px";
                    this.$refs.package_action_menu.style.display = 'block';
                    this.$refs.package_action_menu.style.left = menuPositionX;
                    this.$refs.package_action_menu.style.top = menuPositionY;
                    this.action_menu_visibility = 1;
                }
                e.preventDefault();
            },
            hideActionMenu() {
                if (this.action_menu_visibility == 1) {
                    this.$refs.package_action_menu.style.display = 'none';
                    this.action_menu_visibility = 0;
                }
            },
            getPosition(e) {
                var posx = 0;
                var posy = 0;
                if (!e) var e = window.event;
                if (e.pageX || e.pageY) {
                    posx = e.pageX;
                    posy = e.pageY;
                } else if (e.clientX || e.clientY) {
                    posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                    posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                }
                return {
                    x: posx-300,
                    y: posy-200
                }
            },

            viewChangeItemModal(item){
                this.batchEditIds = [];
                this.singleEditId = [item.id];
                this.changeItemModalItem = item;
                this.changeItemModal=!this.changeItemModal;
            },
            viewCreateSinglePackageModal(item){
                this.batchEditIds = [];
                this.singleEditId = [item.id];
                this.createSinglePackageModal=!this.createSinglePackageModal;
            },
            viewCreateTestingPackageModal(item){
                this.batchEditIds = [];
                this.singleEditId = [item.id];
                this.createTestingPackageModal=!this.createTestingPackageModal;
            },
            viewAdjustModal(item){
                this.batchEditIds = [];
                this.singleEditId = [item.id];
                this.adjustModalItem = item;
                this.adjustModal=!this.adjustModal;
            },
            viewRemediateModal(item){
                this.batchEditIds = [];
                this.singleEditId = [item.id];
                this.remediateModalItem = item;
                this.remediateModal=!this.remediateModal;
            },
            viewCreatePlantingsModal(item){
                this.batchEditIds = [];               
                this.singleEditId = [item.id];
                this.createPlantingsModalItem = item;
                this.createPlantingsModal=!this.createPlantingsModal;
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

<style>
  #package_action_menu {
    display: none;
    position: absolute;
    z-index: 10;
    background-color: #ffffff;
    border-style: solid;
    border-width: 1px;
    border-color: rgba(0, 0, 0, 0.05);
  }
</style>
<style scoped>
    >>> .grid-table > tbody > tr > td:last-of-type {
        width: 1%;
        white-space: nowrap;
    }
</style>