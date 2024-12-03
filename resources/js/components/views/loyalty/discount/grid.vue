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

                    <filter-date v-for="(filt,fkey) in schema.filters" v-if="filt.type=='daterange'"
                        :key="fkey"
                        :schema="filt"
                        :filter="gridFilters.filter[fkey]"
                        @update="(upd) => {gridFilters.filter[fkey] = upd}">
                    </filter-date>
                    
                    <filter-in v-for="(filt,fkey) in schema.filters" v-if="filt.type=='wherein'"
                        :key="fkey"
                        :schema="filt"
                        :filter="gridFilters.filter[fkey]" 
                        @update="(upd) => {gridFilters.filter[fkey] = upd}">
                    </filter-in>

                    <filter-tabs v-for="(filt,fkey) in schema.filters" v-if="filt.type=='tabular'"
                                 :key="fkey"
                                 :schema="filt"
                                 :filter="gridFilters.filter[fkey]"
                                 @update="(upd) => {gridFilters.filter[fkey] = upd}">
                    </filter-tabs>

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
                
            >
              
                <template v-for="(field,ind) in schema.meta.fields" v-slot:head(field.key)="column">
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
                </template>

                <template v-slot:cell(batch_ids)="row">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :checked="inSet(row.item.id,batchEditIds)" :disabled="false" @click="toggleBatchId(row.item.id,$event)"><span class="custom-control-indicator"></span> 
                    </label>
                </template>
                
                <template v-slot:cell(created_at)="row">
                    {{ row.value | localDate }}<br>
                    <span v-if="row.item.scheduled_at && $moment(row.item.scheduled_at)>$moment()" class="small show-red"><b>*Scheduled At {{ row.item.scheduled_at | localDate }}</b></span>
                    <span v-else-if="!row.item.is_active" class="small show-red">*Is Inactive</span>
                    <span v-else class="small show-green">*Is Live!</span>
                </template>

                <template v-slot:cell(updated_at)="row">
                    {{ row.value | localDate }}
                </template>
                
                <template v-slot:cell(scheduled_at)="row">
                    {{ row.value | localDate }}
                </template>

                <template v-slot:cell(discount_code)="row">
                    <span :class="{'show-green':!row.item.discount_code}"><b>{{ row.item.discount_code || '*Open' }}</b></span><br>
                    <span v-if="row.item.is_exclusive" class="small show-red">*Is Exclusive!</span>
                </template>

                <template v-slot:cell(descriptor)="row">
                    <router-link :to="{name: model.toLowerCase()+'_edit',params:{id:row.item.id}}" tag="a" class="">{{ row.item.descriptor }}</router-link><br>
                    <span class="small">{{ row.item.name }}</span>
                </template>
                
                <template v-slot:cell(discount_amount)="row">
                    {{ (row.item.discount_type=='amt') ? '$' : '' }}{{ row.value | dollar }}{{ (row.item.discount_type=='pct') ? '%' : '' }}
                </template>
                
                <template v-slot:cell(associations)="row">
                  <span v-if="row.item.groups_count"> - {{ row.item.groups_count }} Customer Groups apply.<br></span>
                  <span v-if="row.item.categories_count"> - {{ row.item.categories_count }} Product Categories apply.<br></span>
                  <span v-if="row.item.pricesets_count"> - {{ row.item.pricesets_count }} Product Grade/PriceSets apply.<br></span>
                  <span v-if="row.item.products_count"> - {{ row.item.products_count }} Products apply.<br></span>
                  
                  <span v-if="row.item.locations_count>=2" v-for="(loc,lid) in row.item.locations" class="w-100 d-block"> - For {{ loc.name }}</span>
                  
                  <router-link v-if="row.item.sales_count" :to="{name:'sale',params:{filters:{discount_id:[row.item.id]}}}" tag="a" class=""> - Used for {{ row.item.sales_count }} Sales.<br></router-link>
                  <router-link v-if="row.item.campaigns_count" :to="{name:'campaign',params:{filters:{discount_id:[row.item.id]}}}" tag="a" class=""> - {{ row.item.campaigns_count }} Sms/Email Campaigns apply.<br></router-link>
                  
                  <router-link v-if="row.item.is_active" :to="{name:'campaign_edit',params:{id:0,discount_id:row.item.id}}" tag="a" class="btn btn-sm btn-round btn-info mt-1">ADD CAMPAIGN</router-link>
                </template>
                
                <template v-slot:cell(rank)="row">
                    {{ row.value }}
                </template>                

                <template v-slot:cell(actions)="row">
                    <i class="float-right mr-2 mt-1"
                       :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}"
                       @click="rowToggle(row)"/>
                </template>

                <template v-slot:row-details="row">
                    <div class="card card-stats px-3 py-3 justify-content-center">
                        <edit-form ref="editForm"
                                   :id="row.item.id"
                                   type="modal"
                                   :filters="gridFilters.filter"
                                   :campaigns-count="row.item.campaigns_count"
                                   @change="rowChange(row)"
                                   @refresh="(upd) => rowReset(row,upd)"
                                   @archive="confirmDelete(row.item.id)"
                                   @toggle="rowToggle(row)"/>
                    </div>
                </template>

                <template v-slot:table-caption v-if="gridData">
                    <div v-if="gridData.data.length>0">
                        <span v-if="gridData.meta">
                            Showing {{ ((gridData.meta.per_page*(gridPage-1))+1) }} to {{ (((gridData.meta.per_page*gridPage) < gridData.meta.total) ? (gridData.meta.per_page*gridPage) : gridData.meta.total) }} of {{ gridData.meta.total }} {{ (schema.lang.items) ? schema.lang.items : 'Records' }}
                            <form-boolean :declared="gridArchive" :schema="{name:'archived',title:'Include Archived Items'}" @input="(upd) => {gridArchive = upd; }" class="mt-1"/>
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
                        <form-boolean :declared="gridArchive" :schema="{name:'archived',title:'Include Archived Items'}" @input="(upd) => {gridArchive = upd; }" class="mt-1"/>
                        <img src="/images/logo.png" alt="No Results" class="" width="115" />
                        <h4>Hmm, There are currently no Results.</h4>
                    </div><div v-else class="h-100">&nbsp;</div>
                </template>

            </b-table>
        </transition>

        <div class="col-12 batch-block">
            <i class="batch-icon"></i> With Selected: 
            <select v-model="batchEditFocus" class="batch-sel">
                <option value="edit">Bulk Edit</option>
                <option value="inactivate">Inactivate</option>
            </select> 
            <button class="btn btn-sm" :class="{'btn-light':batchEditIds.length<=0,'btn-warning':batchEditIds.length>0}" @click.default="batchEditModal=!batchEditModal" :disabled="(batchEditIds.length>0) ? false : true">Batch It!</button>
        </div>
        

        <b-modal v-if="" centered ref="batchEditModal"
            v-model="batchEditModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="batchEditModal=!batchEditModal"></i>
              <h5 class="w-100 mb-0 text-center">Modify Batch of ({{ batchEditIds.length }}) Items</h5>
            </template>
          
              <batch-edit-modal v-if="batchEditModal"
                :focus="batchEditFocus"
                :ids.sync="batchEditIds"
                :schema="schema"
                @refresh="refreshFromModal">
              </batch-edit-modal>
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="batchEditModal=!batchEditModal">Close</span>
            </template>
        </b-modal>

    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Grid from '../../../../models/Discount';
    import BatchEditModal from './batchEditModal';
    import EditForm from './editForm';
    import _ from 'lodash';


    export default {
        
        props: {
            module: {
                type: String,
                default: 'loyalty',
            },
            model: {
                type: String,
                default: 'discount'
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
                gridArchive:false,
                batchEditModal:false,
                batchEditFocus:'edit',
                batchEditIds:[]
            };
        },
        
        components : { 
            BatchEditModal,
            EditForm
        },
        
        mounted() {
            //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
            this.gridSearch = this.$route.query.search || null;
            
            if(this.schema){
                this.setFilters('all');               // if we have schema, then set filters, else we watch schema load/change and then set.
                this.gridColumns = this.schema.meta.fields;                     // for some reason, the schema changing on edit doesnt register - need to reload upon mount
            }

            this.restoreGridSettings();
        },
        
        methods: {
            async fetchGrid(){                                                  // get the grid data
                if(!this.schema || !this.gridFilters) return false;             // dont fetch grid without a schema or the filters loaded, this will be trigered when they load
                else if(this.isLoading == true) return false;                   // do not fetch if we are already fetching

                this.$store.commit(this.module+'/setSearch',{gridPage: this.gridPage, options: { merge: true} });   // persist search setting

                this.isLoading = true;
                this.gridData = await new Grid()
                    .setFilters(this.gridFilters.filter)
                    .params({
                        search: this.gridSearch,
                        archived: (this.gridArchive) ? 1 : 0
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

                let def = this.filters || {};
                if(!def.location_id)                                            // if we are not passing locations then set current for scope.
                    def.location_id = [this.$store.state.disp.location];

                this.gridFilters = {                                            // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 20,
                    sortBy: Object.keys(this.schema.filters).find(key => this.schema.filters[key].type === 'daterange') || 'rank', // use first daterange filter field(key) in schema
                    orderDesc: false,
                    //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }),this.filters)
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:['all']}; }),def)
                };

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

            renderRowBg(item,type){
                if(!item) return null;
                const isArchived = !!item.archived_at;
                return {
                    'show-inactive' : !item.is_active,
                    'table-warning' : ((item.scheduled_at && this.$moment(item.scheduled_at)>this.$moment()) || !item.is_active),
                    'is-archived': isArchived,
                    'table-success': (!isArchived && (!item.discount_code || (this.inSet(item.id,this.batchEditIds)))),
                };
    	    },

            rowClickHandler(item){
                this.$router.push({name: this.model.toLowerCase()+'_edit',params:{id:item.id}});
            },
            
            confirmDelete(id){
                this.$swal.fire({
                  title: 'Are you sure?',
                  text: 'This will Delete this '+this.model+' rule.',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, delete rule'
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

            refreshFromModal(upd){
                this.batchEditModal=!this.batchEditModal;
                this.shouldReload = true;
            },
            
            canArchive(item){
                if(item.campaigns_count) return false;
                else if(item.sales_count) return false;
                else return true;
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


            restoreGridSettings() {
                if (this.$store.state[this.module].search && this.$store.state[this.module].search.gridPage) {
                    this.gridPage = this.$store.state[this.module].search.gridPage;
                    this.$store.commit(this.module+'/setSearch',{gridPage: null, options: { merge: true} });   // persist search setting
                }
            },

            mergeGridData() {
                if (this.$store.state[this.module].search && this.$store.state[this.module].search.gridData && Array.isArray(this.gridData.data)) {
                    //merge any saved options into griddata
                    this.$store.state[this.module].search.gridData.forEach(saved=> {
                        let row = this.gridData.data.findIndex(e=>e.id===saved.id);
                        if (row > -1) this.$set(this.gridData.data[row], '_showDetails', true); //$set if the _showDetails isn't already there we need the reactivity
                    });
                }
            }
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

            gridArchive(to,from){
                if(this.gridFilters) this.fetchGrid();
            },
            
            schema:{
                handler(to,from){
                    if(!from && to) this.setFilters();                          // if we just loaded a new schema data, then set filters (otherwise, this is set on mounted)
                    else if(!_.isEqual(to.filters, from.filters)) this.setFilters('all'); // if a schema filter change - reset the filters - which will refresh the grid
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

</style>
