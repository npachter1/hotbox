<template>
    <div v-if="item && schema" class="col-12">
    <form>
        <fieldset>
            
                <h3 v-if="item.id" class="mb-4 w-100">Manage {{ item.name }}
                    <div class="float-right small">Auto-Update?<form-boolean :declared="item.type=='auto'" :schema="schema.form.type" :hideLabel="true" @input="(upd) => { item.type = (upd) ? 'auto' : 'custom'}" class="mt-2 ml-1 float-right" /></div>
                </h3>
                <h3 v-else>Add a new Group:</h3>

                <span class="description" v-if="item.created_at">
                   - Created On: {{ item.created_at | localDate }}<br>
                </span>                
                <span class="description" v-if="item.last_synced_at && item.type=='auto'">
                   - Last Synced On: {{ item.last_synced_at | localDate }}<br>
                </span>

                <div class="row mt-4 mb-2">

                <form-text v-model="item.name" :schema="schema.form.name" class="col-12" />

                    <div class="mt-3 col-12 col-sm-12">
                        <h5 class="w-100 mx-1 clearfix">Group Auto-Filters</h5>
    

                    <label class="filter-header mt-3 clearfix" for="filters_locations">
                      <div class="filter-tag-lead float-left" @click="showLocationFilterMap=!showLocationFilterMap"><b><i class="hotbox-icon hotbox-icon-filter"></i> Customer Locations:</b> 
                          <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                          <span v-if="item.filters.locations.length<=0">(Select..)</span>
                      </div> 
                      <div v-for="tag in showFilterList('locations')" class="filter-tag">
                        {{ tag.name }} <i class="hotbox-icon hotbox-icon-d-remove small" @click="toggleFilterSet('locations',tag.id)"></i>
                      </div>
                       <i class="hotbox-icon filter-bolder float-right mt-2" :class="{'hotbox-icon-circle-down-12':!showLocationFilterMap,'hotbox-icon-circle-up-11':showLocationFilterMap}" @click="showLocationFilterMap=!showLocationFilterMap"></i>
                       <div class="float-right mt-1 mr-2" v-if="item.type=='auto'">
                        <span :class="{'strong':item.filters.matchTypes['locations']=='and'}" @click="item.filters.matchTypes['locations']='and'">
                           {{ schema.form.filters.properties.matchType.values[0].name  }} 
                        </span> | 
                        <span :class="{'strong':item.filters.matchTypes['locations']=='or'}" @click="item.filters.matchTypes['locations']='or'">
                           {{ schema.form.filters.properties.matchType.values[1].name  }} 
                        </span>
                      </div>
                    </label>           
    
                    <transition name="hb-fade">
                    <div class="mt-1 mb-2 form-group filter-section" v-show="showLocationFilterMap">
                      
                      <loading :display="isProcessing" type="loadGrid" />
                      <b-table v-if="schema" striped hover                
                        id="filters_locations_table"
                        primary-key="id"
                        :items="filterData.locations"
                        :fields="locationColumnsVisible"
                        :busy.sync="isProcessing"
                        :show-empty="true"
                        sort-by="name"
                        :sort-desc="false"
                        :tbodyTrClass="filterLocationRowSel"
                        :per-page="20"
                        current-page="1"
                        responsive="md"
                        stacked="sm">
                    
                      <template v-slot:cell(id)="row">
                          <input type="checkbox" class="input" :checked="inFilterSet('locations',row.item.id)" :disabled="false" @click="toggleFilterSet('locations',row.item.id)">
                      </template>                
      
                      <template v-slot:cell(name)="row">
                          {{ row.value }}<br>
                          <span class="small">{{ row.item.address1 }}</span>
                      </template>
                      
                      <template v-slot:cell(city)="row">
                          <span v-if="row.item.address">
                              {{ row.item.address.city }}, {{row.item.address.region }} {{ row.item.address.zip }}
                          </span><span v-else>n/a</span>
                      </template>
                      
                      
      
                      <template v-slot:empty>
                          <div v-if="!isProcessing">
                              <img src="/images/logo.png" alt="No Results" class="" width="115" />
                              <h4>Hmm, There are currently no Location filters to choose from.</h4>
                          </div>
                      </template>
      
                      </b-table>
    
                    </div>
                    </transition>

                    <label class="filter-header mt-3 clearfix" for="filters_products">
                      <div class="filter-tag-lead float-left" @click="showProductFilterMap=!showProductFilterMap"><b><i class="hotbox-icon hotbox-icon-filter"></i> Previously Purchased Products:</b>
                          <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                          <span v-if="item.filters.products.length<=0">(Select..)</span>
                      </div> 
                      <div v-for="tag in showFilterList('products')" class="filter-tag">
                        {{ tag.name }} <i class="hotbox-icon hotbox-icon-d-remove small" @click="toggleFilterSet('products',tag.id)"></i>
                      </div>
                       <i class="hotbox-icon filter-bolder float-right mt-2" :class="{'hotbox-icon-circle-down-12':!showProductFilterMap,'hotbox-icon-circle-up-11':showProductFilterMap}" @click="showProductFilterMap=!showProductFilterMap"></i>
                       <div class="float-right mt-1 mr-2" v-if="item.type=='auto'">
                        <span :class="{'strong':item.filters.matchTypes['products']=='and'}" @click="item.filters.matchTypes['products']='and'">
                           {{ schema.form.filters.properties.matchType.values[0].name  }} 
                        </span> | 
                        <span :class="{'strong':item.filters.matchTypes['products']=='or'}" @click="item.filters.matchTypes['products']='or'">
                           {{ schema.form.filters.properties.matchType.values[1].name  }} 
                        </span>
                      </div>
                    </label>
                    
                    <transition name="hb-fade">
                    <div class="mt-1 mb-2 form-group filter-section" v-show="showProductFilterMap">
    
                      <input class="form-control clearfix mt-1 mb-2" v-model="searchProducts" placeholder="Search for a Product"></input>
                      
                      <loading :display="isProcessing" type="loadGrid" />
                      <b-table v-if="schema" striped hover                
                        id="filters_products_table"
                        primary-key="id"
                        :items="filterData.products.filter(v=>item.filters.locations.indexOf(v.location_id)!==-1 || !item.filters.locations.length)"
                        :fields="productColumnsVisible"
                        :busy.sync="isProcessing"
                        :filter="searchProducts"
                        :show-empty="true"
                        sort-by="name"
                        :sort-desc="false"
                        :tbodyTrClass="filterProductRowSel"
                        :per-page="20"
                        :current-page="filterProductsPage"
                        responsive="md"
                        stacked="sm">
                      
                      <template v-for="(field,ind) in schema.form.filters.properties.products.fields" v-slot:[renderColumnHeader(field.key)]="column">
                          <em>
                            <i v-if="field.icon" :class="field.icon"></i> 
                            <i v-if="field.description" class="hotbox-icon hotbox-icon-c-question" :title="field.name" v-b-tooltip.hover="field.description"></i> 
                            <span v-if="field.key=='actions'"><filter-more-columns v-if="productColumns" :columns.sync="productColumns"></filter-more-columns></span>
                            <span v-else>{{ column.label }}</span>
                          </em>
                      </template>

                      <template v-slot:cell(id)="row">
                          <input type="checkbox" class="input" :checked="inFilterSet('products',row.item.id)" :disabled="false" @click="toggleFilterSet('products',row.item.id)">
                      </template>
                      
                      <template v-slot:cell(name)="row">
                          {{ row.value }}
                          <span v-if="item.filters.locations.length>=2" class="small ml-2"> - [From: {{ row.item.location_id | renderValue($store.state.user.locations_assigned) }}]</span>
                      </template>
                      
                      <template v-slot:cell(mtd_trend)="row">
                          {{ row.item.mtd_trend }}%
                      </template>
                      
                      <template v-slot:cell(mtd_revenue)="row">
                          ${{ row.item.mtd_revenue | dollar }}
                      </template>
                      
                      <template v-slot:cell(soonest_expiry)="row">
                          {{ row.item.soonest_expiry | localDate }}
                      </template>
    
                      <template v-slot:cell(last_received)="row">
                          {{ row.item.last_received | localDate }}
                      </template>
      
                      <template v-slot:empty>
                          <div v-if="!isProcessing">
                              <img src="/images/logo.png" alt="No Results" class="" width="115" />
                              <h4>Hmm, There are currently no Location filters to choose from.</h4>
                          </div>
                      </template>
      
                      </b-table>
                      <div class="col-12 clearfix filter-pages mb-3">
                        <b-pagination v-model="filterProductsPage" :total-rows="filterData.products.length" per-page="20" class="my-0"></b-pagination>
                      </div>
    
                    </div>
                    </transition>

                    <label class="filter-header mt-3 clearfix" for="filters_categories">
                      <div class="filter-tag-lead float-left" @click="showCategoryFilterMap=!showCategoryFilterMap"><b><i class="hotbox-icon hotbox-icon-filter"></i> Previously Purchased Categories:</b> 
                          <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                          <span v-if="item.filters.categories.length<=0">(Select..)</span>
                      </div> 
                      <div v-for="tag in showFilterList('categories')" class="filter-tag">
                        {{ tag.name }} <i class="hotbox-icon hotbox-icon-d-remove small" @click="toggleFilterSet('categories',tag.id)"></i>
                      </div>
                       <i class="hotbox-icon filter-bolder float-right mt-2" :class="{'hotbox-icon-circle-down-12':!showCategoryFilterMap,'hotbox-icon-circle-up-11':showCategoryFilterMap}" @click="showCategoryFilterMap=!showCategoryFilterMap"></i>
                       <div class="float-right mt-1 mr-2" v-if="item.type=='auto'">
                        <span :class="{'strong':item.filters.matchTypes['categories']=='and'}" @click="item.filters.matchTypes['categories']='and'">
                           {{ schema.form.filters.properties.matchType.values[0].name  }} 
                        </span> | 
                        <span :class="{'strong':item.filters.matchTypes['categories']=='or'}" @click="item.filters.matchTypes['categories']='or'">
                           {{ schema.form.filters.properties.matchType.values[1].name  }} 
                        </span>
                      </div>
                    </label>              
    
                    <transition name="hb-fade">
                    <div class="mb-2 form-group filter-section" v-show="showCategoryFilterMap">

                        <input class="form-control mt-1 mb-2" v-model="searchCategories" placeholder="Search for a Category Name"></input>

                      <loading :display="isProcessing" type="loadGrid" />
                      <b-table v-if="schema" striped hover                
                        id="filters_categories_table"
                        primary-key="id"
                        :items="filterData.categories.filter(v=>item.filters.locations.indexOf(v.location_id)!==-1 || !item.filters.locations.length)"
                        :fields="categoryColumnsVisible"
                        :busy.sync="isProcessing"
                        :filter="searchCategories"
                        :show-empty="true"
                        sort-by="name"
                        :sort-desc="false"
                        :tbodyTrClass="filterCategoryRowSel"
                        :per-page="20"
                        :current-page="filterCategoriesPage"
                        responsive="md"
                        stacked="sm">
                    
                      <template v-slot:cell(id)="row">
                          <input type="checkbox" class="input" :checked="inFilterSet('categories',row.item.id)" :disabled="false" @click="toggleFilterSet('categories',row.item.id)">
                      </template>

                      <template v-slot:cell(name)="row">
                          {{ row.value }}
                          <span v-if="item.filters.locations.length>=2" class="small ml-2"> - [From: {{ row.item.location_id | renderValue($store.state.user.locations_assigned) }}]</span>
                      </template>

                      <template v-slot:cell(metrc)="row">
                          {{ row.item.equivalency_type | ucwords }}
                      </template>
      
                      <template v-slot:empty>
                          <div v-if="!isProcessing">
                              <img src="/images/logo.png" alt="No Results" class="" width="115" />
                              <h4>Hmm, There are currently no Location filters to choose from.</h4>
                          </div>
                      </template>
      
                      </b-table>
                      <div class="col-12 clearfix filter-pages mb-3">
                        <b-pagination v-model="filterCategoriesPage" :total-rows="filterData.categories.length" per-page="20" class="my-0"></b-pagination>
                      </div>
    
                    </div>
                    </transition>              
                  

                    <label class="filter-header mt-3 clearfix" for="filters_loyalty">
                      <div class="filter-tag-lead float-left" @click="showLoyaltyFilterSettings=!showLoyaltyFilterSettings"><b>Customer Loyalty:</b> - <i>Minimum <b>{{ item.filters.minPoints || 'n/a' }}</b> Reward Points, Minimum <b>${{ item.filters.minSpent || 'n/a' }}</b> Spent</i> <i v-if="!showLoyaltyFilterSettings" class="hotbox-icon hotbox-icon-pencil filter-bolder"></i></div>
                       <i class="hotbox-icon filter-bolder float-right mt-2" :class="{'hotbox-icon-circle-down-12':!showLoyaltyFilterSettings,'hotbox-icon-circle-up-11':showLoyaltyFilterSettings}" @click="showLoyaltyFilterSettings=!showLoyaltyFilterSettings"></i>
                    </label>              
    
                    <transition name="hb-fade">
                    <div class="mt-1 mb-2 form-group filter-section" v-show="showLoyaltyFilterSettings">
                      
                      <label class="mt-2 clearfix" for="filters_minPoint">{{ schema.form.filters.properties.minPoints.title }}</label>
                      <input class="form-control"
                          v-model.number="item.filters.minPoints" 
                          :name="'filters_minPoints'"
                          aria-describedby="addon-right addon-left"
                          v-validate="'decimal|required'"
                          :class="{'input': true, 'text-danger': errors.has('filters_minPoints') }"
                          type="text"
                          placeholder="">
                        <span v-show="errors.has('filters_minPoints')" class="form-text text-muted text-danger">{{ errors.first('filters_minPoints') }}</span>
    
                      <label class="mt-2 clearfix" for="filters_minSpent">{{ schema.form.filters.properties.minSpent.title }}</label>
                      <div class="input-group form-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div> 
                      <input class="form-control"
                          v-model.number="item.filters.minSpent" 
                          :name="'filters_minSpent'"
                          aria-describedby="addon-right addon-left"
                          v-validate="'decimal|required'"
                          :class="{'input': true, 'text-danger': errors.has('filters_minSpent') }"
                          type="text"
                          placeholder="100">
                        <span v-show="errors.has('filters_minSpent')" class="form-text text-muted text-danger">{{ errors.first('filters_minSpent') }}</span>
                      </div>
                        
                    </div>
                    </transition>
                    
                    <label class="filter-header mt-3 clearfix" for="filters_preferences">
                      <div class="filter-tag-lead float-left" @click="showPreferenceFilterMap=!showPreferenceFilterMap"><b><i class="hotbox-icon hotbox-icon-filter"></i> Customer Preferences:</b> 
                          <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                          <span v-if="item.filters.preferences.length<=0">(Select..)</span>
                      </div> 
                      <div v-for="tag in showFilterList('preferences')" class="filter-tag">
                        {{ tag.name }} <i class="hotbox-icon hotbox-icon-d-remove small" @click="toggleFilterSet('preferences',tag.id)"></i>
                      </div>
                       <i class="hotbox-icon filter-bolder float-right mt-2" :class="{'hotbox-icon-circle-down-12':!showPreferenceFilterMap,'hotbox-icon-circle-up-11':showPreferenceFilterMap}" @click="showPreferenceFilterMap=!showPreferenceFilterMap"></i>
                       <div class="float-right mt-1 mr-2" v-if="item.type=='auto'">
                        <span :class="{'strong':item.filters.matchTypes['preferences']=='and'}" @click="item.filters.matchTypes['preferences']='and'">
                           {{ schema.form.filters.properties.matchType.values[0].name  }} 
                        </span> | 
                        <span :class="{'strong':item.filters.matchTypes['preferences']=='or'}" @click="item.filters.matchTypes['preferences']='or'">
                           {{ schema.form.filters.properties.matchType.values[1].name  }} 
                        </span>
                      </div>
                    </label>               
    
                    <transition name="hb-fade">
                    <div class="mt-1 mb-2 form-group filter-section px-2 row" v-show="showPreferenceFilterMap">
                      
                       <div v-for="(pref,pid) in schema.form.filters.properties.preferences.values" :key="pref.id" class="col-6 col-sm-4 px-2">
                          <input type="checkbox" class="input" :checked="inFilterSet('preferences',pref.id)" :disabled="false" @click="toggleFilterSet('preferences',pref.id)"> {{pref.name }}
                      </div>
                        
                    </div>
                    </transition> 
    
                </div>


                <div class="col-12 mt-4">
                      <h5 class="mx-2">[{{ filteredCustomers.length }}] Assigned Customers</h5>

                      <transition name="hb-fade">
                        <div class="mt-2">
        
                          <multiselect v-if="" class="input mt-2 mb-2"
                            v-model="searchedCustomer"
                            name="customer_search"
                            :placeholder="schema.form.customers.title"
                            :internal-search="false"
                            :clear-on-select="true"
                            :close-on-select="true"
                            :options-limit="300"
                            :max-height="600"
                            :show-no-results="true"
                            :hide-selected="false"
                            pen-direction="bottom"
                            :searchable="true"
                            :allow-empty="false"            
                            :multiple="false"
                            :loading="isProcessing"
                            track-by="id"
                            label="first_name"
                            :options="searchCustomers"
                            @search-change="searchCustomer">
                            <template slot="singleLabel" slot-scope="{ option }">Select More Customers..</template>
                            <template slot="option" slot-scope="{ option }">{{ option.first_name }} {{ option.last_name }} {{ option.alias }} {{ option.email }} - {{option.total_reward_points}} Points</template>
                          </multiselect>
                          
                          <loading :display="isFiltering" type="loadGrid" />
                          <b-table v-if="schema" striped hover            
                            id="filters_customers_table"
                            primary-key="id"
                            :items="filteredCustomers"
                            :fields="customerColumnsVisible"
                            :busy.sync="isFiltering"
                            :show-empty="true"
                            sort-by="name"
                            :sort-desc="false"
                            :per-page="20"
                            :current-page="filterCustomersPage"
                            :tbody-transition-props="{name:'hb-list-fade'}"
                            responsive="md"
                            stacked="sm">
 
                           <template v-slot:cell(first_name)="row">
                              <span class="float-right small">{{ (row.item.pivot.is_manual==1) ? '(Manual)' : '(Auto)' }}</span>
                              {{ row.value }} {{ row.item.last_name }}
                          </template>  
                        
                          <template v-slot:cell(email)="row">
                            <span v-if="row.item.address">{{ row.item.address.email }} <span class="small">{{ row.item.address.phone }}</span></span>
                            <span v-else>n/a</span>
                          </template>                
                           
                          <template v-slot:cell(total_reward_points)="row">
                              {{ row.value | dollar }}
                          </template>                       

                          <template v-slot:cell(total_spent)="row">
                              ${{ row.value | dollar }}
                          </template>                         
                        
                          <template v-slot:cell(created_at)="row">
                              {{ row.item.created_at | localDate }}
                               <i v-if="row.item.pivot.is_manual==1" class="hotbox-icon hotbox-icon-link-broken-70 float-right" @click="removeCustomer(row.item.id)"></i>
                          </template>
        
                          <template v-slot:empty>
                            <div v-if="!isProcessing && itemState!='saving..'" class="text-center small">
                              <img src="/images/logo.png" alt="No Results" class="" width="65" /><br>
                                <span v-if="item.type=='auto'">No Customers have been auto-filtered to this Group</span>
                                <span v-else>No Customers are assigned to this Group</span>
                            </div>
                          </template>
          
                          </b-table>
                          <div class="col-12 clearfix filter-pages mb-3">
                            <b-pagination v-model="filterCustomersPage" :total-rows="filteredCustomers.length" per-page="20" class="my-0"></b-pagination>
                          </div>
        
                        </div>
                      </transition>               
                      
                  </div>


                </div>

                <div class="col-12 clearfix mt-3 text-center">
                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                     <a v-if="type=='form'" @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                </div>


        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Group';
    import Customer from '../../../../models/Customer';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Group'
            },
            module: {
                type: String,
                default: 'loyalty',
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
            }
        },
        
        data(){
            return {
                item: null,
                itemState: 'save',
                isLoading:true,
                isProcessing: false,
                isFiltering:false,
                filteredCustomers:[],
                productColumns:null,
                categoryColumns:null,
                locationColumns:null,
                customerColumns:null,
                filterData:{
                    products:[],
                    categories:[],
                    locations:[],
                    preferences:[]
                },
                sortCustomerBy: 'name',
                isSortCustomerAsc: true,
                filterProductsPage:1,
                filterCategoriesPage:1,
                filterCustomersPage:1,
                searchCategories:null,
                searchProducts:null,
                searchCustomers:[],
                searchedCustomer:null,
                showProductFilterMap: false,
                showCategoryFilterMap: false,
                showLocationFilterMap: false,
                showPreferenceFilterMap: false,
                showLoyaltyFilterSettings: false
            };
        },
        
        components : {

        },
        
        async mounted() {
            this.isLoading = true;
            await this.$store.dispatch(this.module+'/setSchemas','group');      // this could be a sub resource - it loads its own schema upon modal load
            if(this.id){
                await Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.filteredCustomers = this.item.customers || [];
                    this.item.customer_ids = [];
                    this.item.customers = [];                                   // reduce query data for post back
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.isLoading = false;
            }

                
            /* get the filters data */
            this.isProcessing = true;
            axios.get('/api/v1/admin/dispensary/groups/filter').then(response =>{
                this.filterData = Object.assign({},{
                    products: response.data.products || [],
                    categories: response.data.categories || [],
                    locations: response.data.locations || [],
                    preferences: this.schema.form.filters.properties.preferences.values || []
                });
                this.isProcessing = false;
            }).catch(error => {
                this.isProcessing = false;
                this.$announcer(error.response);
            });
            
            this.productColumns = this.schema.form.filters.properties.products.fields;  // load extra product filter columns
            this.categoryColumns = this.schema.form.filters.properties.categories.fields;  // load extra category filter columns
            this.locationColumns = this.schema.form.filters.properties.locations.fields;  // load extra location filter columns
            this.customerColumns = this.schema.form.customers.fields;  // load extra customer filter columns
            
            if(this.item.filters.locations.length == 0)
                this.item.filters.locations.push((this.$store.state.user.location||{}).id); // users location is initially selected for this form

        },
        
        methods: {
            autoSave(confirm=false){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then(async (result) => {
                    if(result){
                        
                        if(!this.id){
                            let withPin = await this.requirePin('Please Enter an Admin PIN to Create a new Customer Group.');
                            if(withPin===false) return false;                   // an adminpin couldnt be validated HINT add error message here if desired.
                        }
                        
                        if(!confirm) _.debounce(() => { this._save(); },2000)();
                        else this._save(true);
                    }else if(confirm==true){
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    }else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
            },
            
            _save(confirm=false){
                this.itemState = 'saving..';
                this.item.customer_ids = this.filteredCustomers.map(a => { return {id:a.id,is_manual:a.pivot.is_manual}; }); // update customer_ids if customers data changes
                this.item.save().then(async response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'Your Customer Group Settings have been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        if(this.type=='modal'){                                 // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
                            await this.$store.dispatch(this.module+'/setSchemas','campaign,discount');
                            this.$emit('refresh',response);
                        }else this.$router.push({name:this.model.toLowerCase()});  
                    }
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },

            removeCustomer(id){
                this.filteredCustomers.splice(this.filteredCustomers.findIndex(cust => cust.id === id), 1); // remove row from list of items in recfile data.
            },
        
            findCustomers(filt){
                if(filt.products.length<=0 && filt.categories.length<=0 && filt.locations.length<=0 && filt.preferences.length<=0 && filt.minPoints<=0 && filt.minSpent<=0){ 
                    this.filteredCustomers = this.filteredCustomers.filter(v=>v.pivot.is_manual==1) || [];
                    return false; // no filters to search for - empty customer list.
                }
                
                this.isFiltering = true;
                axios.post('/api/v1/admin/dispensary/groups/filter',filt).then(response =>{
                    this.filteredCustomers = [...response.data.map(d=>{ d.pivot = {is_manual:0}; return d;}),...this.filteredCustomers.filter(v=>v.pivot.is_manual==1 && !response.data.filter(r=>r.id==v.id).length)];
                    this.isFiltering = false;
                }).catch(error => {
                    this.isFiltering = false;
                    this.$announcer(error.response);
                });           
            },
              
            searchCustomer: _.debounce(async function (query){
                this.isProcessing = true;
                let searched = await new Customer()
                    .params({
                        search: query,
                        abbv: true
                    })
                    .orderBy('-updated_at')
                    .limit(300)
                    .page(1)
                    .get();
                this.searchCustomers = (searched) ? searched.data : [];
                this.isProcessing = false;
            }, 300),
        
            inFilterSet(key,id){
                return (this.item.filters[key].indexOf(id) !== -1) ? true : false;
            },
              
            toggleFilterSet(key,id){
                if(this.item.filters[key].indexOf(id) === -1) this.item.filters[key].push(id); // if not in list, push it
                else this.item.filters[key].splice(this.item.filters[key].indexOf(id), 1); // else remove it.
            },
              
    	    showFilterList(key){
                return this.filterData[key].filter((o) => this.item.filters[key].indexOf(o.id)!==-1).map((v)=>{ return {id:v.id,name:v.name}; });
    	    },
	
            filterLocationRowSel(item,type){
                if(!item) return false;
                return (this.item.filters.locations.indexOf(item.id) !== -1) ? 'table-success' : null;
            },
            	
            filterProductRowSel(item,type){
                if(!item) return false;
                return (this.item.filters.products.indexOf(item.id) !== -1) ? 'table-success' : null;
            },
            	
            filterCategoryRowSel(item,type){
                if(!item) return false;
                return (this.item.filters.categories.indexOf(item.id) !== -1) ? 'table-success' : null;
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            
            productColumnsVisible(){
                return (this.productColumns) ? this.productColumns.filter((col) => { return (col.toggle === true || !col.hasOwnProperty('toggle')); }) : [];
            },
            
            categoryColumnsVisible(){
                return (this.categoryColumns) ? this.categoryColumns.filter((col) => { return (col.toggle === true || !col.hasOwnProperty('toggle')); }) : [];
            },
            
            locationColumnsVisible(){
                return (this.locationColumns) ? this.locationColumns.filter((col) => { return (col.toggle === true || !col.hasOwnProperty('toggle')); }) : [];
            },
            
            customerColumnsVisible(){
                return (this.customerColumns) ? this.customerColumns.filter((col) => { return (col.toggle === true || !col.hasOwnProperty('toggle')); }) : [];
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    if(this.itemState != 'saving..') 
                        this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            },
            
            'item.filters':{
                handler(to,from){
                    if(to && typeof(from)!=='undefined') this.findCustomers(this.item.filters);       // updated filter = new customer filter parse..
                },
                deep: true
            },
            'item.type'(to,from){
                if(to=='custom') alert('This group customer list will not auto-update every 4 hours based on your filters');
            },

            searchedCustomer(){
                if(this.searchedCustomer){
                    if(!this.filteredCustomers.find(c => c.id==this.searchedCustomer.id)){
                        this.filteredCustomers.push(Object.assign({},this.searchedCustomer,{pivot:{is_manual:1}})); // push new customer to list of customers if not exist
                        this.$announcer({status:200,data:{message:this.searchedCustomer.first_name+' has been Assigned to '+this.item.name+'!'}});
                    }
                }
            }
        }
        
    };
    
</script>

<style>
  .item-title {
    margin-bottom: 0;
  }
  .btn-action-in-form{
    float:right;
    margin-right:6px;
  }
  .btn-action-grid{
    float:right;
    margin-right:6px;
  }
  .green-row{
    background-color:#ace0ac73 !important;
  }
  .btn-grid-right {
    margin-top: 2px;
    background-color: #b9b7b7;
    float:right;
  }
</style>
