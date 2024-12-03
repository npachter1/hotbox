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
                ref="pricesets"
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
                :tbody-transition-props="{name:'hb-list-fade'}"
                @row-clicked="rowClickHandler">
            >
              
                <template v-for="(field,ind) in schema.meta.fields" v-slot:head(field.key)="column">
                  <em>
                    <i v-if="field.icon" :class="field.icon"></i> 
                    <i v-if="field.description" class="hotbox-icon hotbox-icon-c-question" :title="field.name" v-b-tooltip.hover="field.description"></i> 
                    <span>{{ column.label }}</span>
                  </em>
                </template>

                <template v-slot:cell(created_at)="row">
                    {{ row.value | localDate }}
                </template>
                
                <template v-slot:cell(updated_at)="row">
                    d{{ row.value | localDate }}d
                </template>

                <template v-slot:cell(name_grade)="row">
                    {{ row.value }}<br>
                    <span v-if="row.item.category" class="small">{{ row.item.category.name }}</span>
                </template>              
                
                <template v-slot:cell(amount_tiers)="row">
                    <div v-if="row.item.amount_tiers && row.item.amount_default==tier.price" v-for="(tier,tin) in row.item.amount_tiers"
                    :key="tin">
                       <span v-if="tin==0 && row.item.amount_tiers[(tin+1)]">at {{ tier.amount | dollar }}{{ row.item.type_uom }} to {{ row.item.amount_tiers[(tin+1)].amount | dollar }}{{ row.item.type_uom }} </span>
                       <span v-else-if="tin==(row.item.amount_tiers.length-1)">at {{ tier.amount | dollar }}{{ row.item.type_uom }} and up </span>
                       <span v-else-if="row.item.amount_tiers[(tin+1)]">at {{ tier.amount | dollar }}{{ row.item.type_uom }} to {{ row.item.amount_tiers[(tin+1)].amount | dollar }}{{ row.item.type_uom }}</span>
                       <span v-else>{{ tier.amount | dollar }}/{{ row.item.type_uom }} </span>
                       = ${{ tier.price | dollar }}/{{ row.item.type_uom }}
                    </div>
                    <a v-if="(row.item.amount_tiers || []).length>1" href="" class="small" @click.prevent="row.toggleDetails">
                         + {{(row.item.amount_tiers.length-1)}} More <i class="" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}"></i> 
                    </a>
                    <span v-if="!row.item.is_active" class="small show-red"><i class="hotbox-icon hotbox-icon-t-warning"></i> NOT ACTIVE!</span> 
                </template>
                
                <template v-slot:cell(products_count)="row">
                    <router-link v-if="row.item.products_count" :to="{name:'product',params:{filters:{priceset_id:[row.item.id]}}}" tag="a" class="">
                        {{ row.item.products_count }} <i class="hotbox-icon hotbox-icon-bullet-list-69"></i>
                    </router-link>
                    <span v-else>--</span>
                </template>
                
                <template v-slot:cell(products_count)="row">
                    <router-link v-if="row.item.products_count" :to="{name:'product',params:{filters:{priceset_id:[row.item.id]}}}" tag="a" class="">
                        {{ row.item.products_count }} <i class="hotbox-icon hotbox-icon-bullet-list-69"></i>
                    </router-link>
                    <span v-else>--</span>
                </template>
                
                 <template v-slot:cell(categories_count)="row">
                    <span v-for="(cat,cid) in row.item.categories" class="d-block w-100">{{ cat.name }}</span>
                </template>               
                
                
                <template v-slot:cell(inventory_count)="row">
                    <router-link v-if="row.item.inventory_count" :to="{name:'inventory',params:{filters:{priceset_id:[row.item.id]}}}" tag="a" class="">
                        {{ row.item.inventory_count }} <i class="hotbox-icon hotbox-icon-bullet-list-69"></i>
                    </router-link>
                    <span v-else>--</span>
                </template>
                
                <template v-slot:cell(rank)="row">
                    {{ row.value }}
                </template>

                <template v-slot:cell(actions)="row">
                    <i class="float-right mr-2 mt-1" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}" @click="rowToggle(row)"></i>
                </template>


                <template v-slot:row-details="row">
                    <div class="card card-stats px-3 py-3 justify-content-center">
                        <edit-form ref="pricesetForm" :id="row.item.id" type="grid" :filters="gridFilters.filter" @change="rowChange(row)" @refresh="(upd) => rowReset(row,upd)" @archive="confirmDelete(row.item.id)" @toggle="rowToggle(row)"></edit-form>
                    </div>
                </template>

                <template v-if="gridData" v-slot:table-caption>
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
        
    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Grid from '../../../../models/Priceset';
    import EditForm from './editForm';
    import _ from 'lodash';


    export default {
        
        props: {
            module: {
                type: String,
                default: 'products',
            },
            model: {
                type: String,
                default: 'priceset'
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
                gridArchive:false,
            };
        },
        
        components : { 
            EditForm
        },
        
        mounted() {
            //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
            this.gridSearch = this.$route.query.search || null;

            if(this.schema){ 
                this.setFilters(this.$route.params.focus);                      // focus filter tab is logged in location id.
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
                
                this.gridFilters = {                                            // (re)set the filters from schema (which fetchGrid will watch and run)
                    pageLimit: 20,
                    sortBy: Object.keys(this.schema.filters).find(key => this.schema.filters[key].type === 'daterange') || 'rank', // use first daterange field(key) in schema
                    orderDesc: false,
                    filter: Object.assign({}, ...Object.keys(this.schema.filters).map(k => { return {[k]:['all']}}),this.filters)
                    //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }))
                };
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
                            this.$refs.pricesetForm._save(true);
                        } else {
                            row.item.changed=false;
                            row.toggleDetails();
                        }
                    });
                }else{
                    row.toggleDetails();
                }
            },
            
            renderRowBg(item,type){
                if(!item) return null;
                return {
                    'is-archived': !!item.archived_at,
                    'show-inactive': !item.is_active,
                };
    	    },
    	    
            rowClickHandler(item){
                return null;
                //this.$router.push({name: this.model.toLowerCase()+'_edit',params:{id:item.id}});
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

            gridArchive(to,from){
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

</style>
