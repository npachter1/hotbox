<template>
    <div v-if="schema" class="col-12">
        <form>
            <div class="table-filter-row">
            
                <div class="filter-search" :class="{'active':gridSearch}">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <i class="hotbox-icon hotbox-icon-search-3"></i>
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
                id="tasks_table"
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
                tbodyTrClass="grid-table-rows"
                responsive="md"
                stacked="sm"
            >
              
                <template v-slot:cell(due_date)="row">
                    {{ row.value | localDate }}
                </template>
                
                <template v-slot:cell(created_at)="row">
                    {{ row.value | localDate }}
                </template>
                
                <template v-slot:cell(updated_at)="row">
                    {{ row.value | localDate }}
                </template>

                <template v-slot:cell(location_id)="row">
                    {{ row.item.location.name }}
                </template>

                <template v-slot:cell(priority)="row">
                    {{ row.item.priority_name }}
                </template>

                <template v-slot:cell(assignees)="row">
                    <span v-for="assignee in row.item.assignees">
                     {{ assignee.name }}<br>
                    </span>
                </template>

                <template v-slot:cell(actions)="row">
                    <div class="dropdown">
                        <a data-toggle="dropdown" class="" aria-haspopup="true" aria-expanded="false">
                            <i class="ti-more-alt"></i>
                        </a>
                        <div class="dropdown-menu tight dropdown-menu-right">
                            <router-link :to="{name: model.toLowerCase()+'_edit',params:{id:row.item.id}}" tag="a" class="dropdown-item"><i class="hotbox-icon hotbox-icon-pencil"></i> Manage {{ model | ucwords }}</router-link>
                            <a v-if="row.item.created_by.id === $store.state.user.id" href="" class="dropdown-item action-danger" @click.prevent="confirmDelete(row.item.id)"><i class="hotbox-icon hotbox-icon-trash-round"></i> Archive {{ model | ucwords }}</a>
                    
                        </div>
                    </div>
                </template>
                


                <template v-slot:table-caption v-if="gridData">
                    <div v-if="gridData.data.length>0" class="paginate-foot">
                        <span v-if="gridData.meta">
                            Showing {{ ((gridData.meta.per_page*(gridPage-1))+1) }} to {{ (((gridData.meta.per_page*gridPage) < gridData.meta.total) ? (gridData.meta.per_page*gridPage) : gridData.meta.total) }} of {{ gridData.meta.total }} {{ (schema.lang.items) ? schema.lang.items : 'Records' }}
                        </span>
                        <span v-else>Showing All {{ (schema.lang.items) ? schema.lang.items : 'Records' }}</span>
                        
                        <div class="table-pager-footer">
                            <b-pagination v-if="gridData.meta.total>0"
                              v-model="gridPage"
                              :total-rows="gridData.meta.total"
                              :per-page="gridData.meta.per_page"
                              aria-controls="tasks_table"
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

    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>

<script>
import Grid from '../../../../models/Task';    
import _ from 'lodash';

export default {
    props: {
        model: {
            type: String,
            default: 'Task'
        },
        module: {
            type: String,
            default: 'auth',
        },
        filters: {                                                          // optional initial filters (filters.filter) object can be passed via this prop!
            type: Object,
            default: () => {}
        }
    },

    data() {
        return {
                isLoading: false,
                isDownloading: false,
                shouldReload: false,
                gridData: null,
                gridSearch:null,
                gridPage:1,
                gridColumns: null,
                gridFilters:null,
                isSyncing:false
            };
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
                    pageLimit: 50,
                    sortBy: Object.keys(this.schema.filters).find(key => this.schema.filters[key].type === 'daterange') || 'updated_at', // use first daterange field(key) in schema
                    orderDesc: false,
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }),this.filters)
                    //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:['all']}; }),this.filters)
                };
            },
            renderAssigneeList(list){
                return (list) ? list.map(v=>{return v.name;}).join('<BR>') : 'n/a';
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
    }
};
</script>

<style>
.btn-create-new {
    margin-top: 0;
}
</style>
