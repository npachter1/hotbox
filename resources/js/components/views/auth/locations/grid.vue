<template>
    <div v-if="schema" class="col-12">
        
        <form>
            <div class="table-filter-row">
            
                <div class="filter-search">
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
                :tbodyTrClass="rowColor"
                responsive="md"
                stacked="sm"
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

                <template v-slot:cell(thumb)="row">
                    <img :src="(row.item.thumb) ? row.item.thumb : '/images/none.jpg'" class="responsive" width="65">
                </template>

                <template v-slot:cell(name)="row">
                    {{ row.value }}<br>
                    <span class="small">Status: {{ row.item.status }}</span>
                </template>

                <template v-slot:cell(created_at)="row">
                    {{ row.value | localDate }}
                </template>

                <template v-slot:cell(activated_at)="row">
                    {{ row.value | localDate }}
                </template>                

                <template v-slot:cell(settings)="row">
                    Contact Email: {{ (row.item.settings) ? row.item.settings.communication_email : 'n/a' }}
                </template>  


                <template v-slot:cell(balance)="row">
                    ${{ row.value | dollar }}
                </template>  

                <template v-slot:cell(actions)="row">
                    <i v-if="row.item.id!=$store.state.user.location.id" class="float-right mt-0" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}" @click.prevent="row.toggleDetails"></i>
                    <i v-if="" class="hotbox-icon hotbox-icon-preferences float-right mr-2" @click="loadEditModal(row.item.id,row.item.name)"></i>
                    <i v-if="row.item.id!=$store.state.user.location.id" class="hotbox-icon hotbox-icon-key-26 float-right mr-2" @click="jumpToLocation(row.item.id)"></i>
                </template>


                <template v-slot:row-details="row">
                    <div v-if="row.item.settings && schema.form.settings" class="col-12">
                        <div v-for="(sect,sid) in schema.form.settings.sections" class="mt-2">
                            <h6>{{ sect.title }}</h6>
                            <div v-for="(prop,pid) in sect.properties" v-if="row.item.settings[pid]" class="col-12 row">
                                <div class="col-sm-4 small">{{ prop.title }}</div>
                                <div class="col-sm-7 small">{{ row.item.settings[pid] }}</div>
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
                        <img src="/images/logo.png" alt="No Results" class="" width="115" />
                        <h4>Hmm, There are currently no Results.</h4>
                    </div><div v-else class="h-100">&nbsp;</div>
                </template>

            </b-table>
        </transition>
        

        <b-modal centered ref="editModal"
            v-model="editModal"
            :no-enforce-focus="true"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="editModal=!editModal"></i>
              <h5 class="w-100 mb-0 text-center">{{ editTitle }}</h5>
            </template>
          
            <edit-modal v-if="editModal"
                :id="editRef"
                :type="editType"
                :schema="schema"
                @refresh="refreshEditModal">
            </edit-modal>
          
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
 
    import EditModal from './editModal';
    import LocationProfile from '../../auth/settings/profile';
    import Grid from '../../../../models/Locations';
    import _ from 'lodash';


    export default {
        
        props: {
            model: {
                type: String,
                default: 'Locations'
            },
            module: {
                type: String,
                default: 'auth',
            }
        },
        
        data(){
            return {
                isLoading: false,
                isDownloading: false,
                gridData: null,
                gridSearch:null,
                gridPage:1,
                gridColumns: null,
                gridFilters:null,
                editModal:false,
                editRef:0,
                editTitle:'Location Management',
                editType:'location'
            };
        },
        
        components : { 
            LocationProfile,EditModal
        },
        
        mounted() {
            //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
            this.gridSearch = this.$route.query.search || null;
            
            if(this.schema){
                this.setFilters(this.$route.params.focus);                      // if we have schema, then set filters, else we watch schema load/change and then set.
                this.gridColumns = this.schema.meta.fields;                     // for some reason, the schema changing on edit doesnt register - need to reload upon mount
            }
        },
        
        methods: {
            async fetchGrid(){                                                  // get the grid data
                if(!this.schema) return false;                                  // dont fetch grid without a schema, it will fetch when loaded
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
                this.isLoading = false;
            },
            
            searchGrid: _.debounce(function (e) {                               // upon search filter update, throttle .5 sec grid and scope refresh
                this.gridPage = 1; 
                this.fetchGrid();
            }, 500),
            
            setFilters(){
                if(!this.schema) return false;
                this.gridFilters = {                                            // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 50,
                    sortBy: Object.keys(this.schema.filters).find(key => this.schema.filters[key].type === 'daterange') || 'updated_at', // use first daterange field(key) in schema
                    orderDesc: false,
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }))
                };
            },
            
            rowColor(item,type){
                if(!item) return null;
                else if(item.id==this.$store.state.user.location.id) return 'table-default';
                switch(item.status){
                    case 'registered':
                    case 'held':
                        return 'table-warning';
                    case 'denied':
                    case 'requested':
                        return 'table-danger';
                    case 'activated':
                        return 'table-success';
                    default: return null;
                }
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
            
            jumpToLocation(sid){
              axios.get('/api/v1/admin/auth/locations/change/'+sid).then(response =>  {
                this.$store.dispatch('resetAuthLocation',{scope:'user,sections,agg',route:this.$route,reset:true});
                this.$router.push({name:'location'});
                response.data.message = 'You are now logged into Location '+sid;
                this.$announcer(response);
              }).catch(error => {
                this.$announcer(error.response);
              });
            },
            
            loadEditModal(id,name){
                this.editRef = id;
                this.editTitle = 'Manage '+name+' - Location '+id+' Account';
                this.editModal = !this.editModal;
            },
            
            refreshEditModal(){
                this.fetchGrid();
                this.editModal = !this.editModal;
            },
            
            updateDataRow(id,data){
                this.gridData.data.map((o)=> { if(o.id==id) return data; else return o; });
                this.editModal = false;
                this.fetchGrid();
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
            },
        }
        
    };
    
</script>

<style>

</style>
