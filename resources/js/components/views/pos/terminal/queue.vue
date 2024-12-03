<template>
    <div v-if="schema" class="col-12">
      <div class="row gutters">

        <div class="col-md-5 pos-queue-nav">
        <nav class="">
          <div class="col-12 pos-queue">
            <div class="pos-queue-header d-flex justify-content-between align-items-center">
                <h5 class="w-100">Customer Queue
                  <i v-if="queue.length && wsStatus !== 'connected'" class="hotbox-icon hotbox-icon-refresh-69 float-right mt-1" @click="loadQueue"></i> 
                  <i v-if="queue.length" class="hotbox-icon hotbox-icon-trash-round float-right mt-1 mr-2" @click="clearQueue"></i>
                </h5>
            </div>
            <b-progress v-if="queueRefresh" :value="queueRefresh" :max="60" variant="info" :striped="true" class="mt-1 mb-0" height="1px"></b-progress>
            <div class="pos-queue-results">
              <div v-if="queue && queue.length > 0" class="list-group">
                <loading :display="isLoadingQueue" type="loadGrid" />
                <transition-group name="hb-list-fade">
                  <div v-for="(que,cid) in queue" :key="que.id" class="list-group-item list-group-item-action list-group-item-dark d-flex justify-content-start align-items-center">
                    <div class="queue-position pr-3">{{ cid + 1 }}</div>
                    <div class="flex-grow-1 pr-2" @click="serviceFromQueue(que.customer_id)">
                        <strong>{{ que.customer_alias }}</strong>
                        <span v-if="que.customer_abbv.address" class="customer-details">
                          {{ que.customer_abbv.address.email }}{{ que.customer_abbv.address.email ? ', ' : '' }} {{ que.customer_abbv.address.cell }}
                        </span>
                        <div class="small">
                          <span v-if="'age' in que.customer_abbv && Number.isInteger(que.customer_abbv.age)">{{ que.customer_abbv.age }} y/o</span> 
                          Checked in at {{ que.created_at | localDate('MM/DD/YY LT') }} <b>(<timeago :autoUpdate=true :datetime="que.created_at | localDate('MM/DD/YY LTS')" :converterOptions="{ addSuffix:false }" ></timeago>)</b>
                        </div>
                    </div>
                    <div class="">
                      <button @click="removeFromQueue(que.id)" type="button" class="btn btn-danger btn-queue"><i class="hotbox-icon hotbox-icon-e-remove"></i></button>
                    </div>
                  </div>
                </transition-group>
                
                <p class="mt-3 mb-2 text-center">Select a Customer above to begin Service.</p>
              </div>
              <div v-else class="ml-4 text-center py-2">
                  No Customers in Queue
              </div>
            </div>
          </div>
        </nav>
      </div>        

      <main role="main" class="col-md-7">
        <loading :display="isLoadingCustomers" type="loadGrid" />
        <div class="col-12 pos-customer-search">
            
            <div class="input-group">
              <input class="form-control mt-1 mb-1 py-2 pos-customer-searchfield"
                v-model="customersSearch" 
                placeholder="Search a Customer by Name,Phone,Email"
                @input="searchCustomer"
                @keydown.enter.prevent="searchCustomer">
                <div class="input-group-append mt-1" style="height:50px;">
                  <button class="input-group-text btn btn-success" @click="createCustomerModal=true"><b>Add New</b></button>
                </div>
            </div>
              
            <transition name="hb-fade">
                <div class="mt-2 ml-2">
                    <b-table v-if="schema" striped hover            
                      id="pos_customers_table"
                      primary-key="id"
                      :items="(customersData || {}).data || []"
                      :fields="schema.meta.pos_fields || []"
                      :busy.sync="isLoadingCustomers"
                      :show-empty="true"
                      :sort-by.sync="customersSortBy"
                      :sort-desc.sync="customersOrderDesc"
                      :no-local-sorting="true"
                      :no-local-filtering="true"
                      :per-page="0"
                      :current-page="customersPage"
                      :tbodyTrClass="renderRowBg"
                      responsive="md"
                      stacked="sm">
 
                      <template v-slot:cell(alias)="row">
                        <router-link v-if="row.detailsShowing" :to="{ name: 'customer_edit',params:{ id:row.item.id } }" tag="a" class="btn btn-md btn-default btn-round float-right mr-1" style="cursor: pointer; color: inherit;"><i class="hotbox-icon hotbox-icon-pencil"></i> Manage</router-link>
                        <a class="btn btn-md btn-info btn-round float-right" :class="{ 'mr-1':row.detailsShowing }" @click.prevent="serviceFromQueue(row.item.id)"><i class="hotbox-icon hotbox-icon-e-add"></i> Service</a>
                        <a v-if="!isOnQueue(row.item.id)" class="btn btn-md btn-default btn-round float-right mr-1" @click.prevent="addToQueue(row.item.id)"><i class="hotbox-icon hotbox-icon-hourglass"></i> Queue</a>
                        <div @click="row.toggleDetails">
                          <i class="float-right mr-2 mt-1" :class="{'ti-angle-double-down':!row.detailsShowing,'ti-angle-double-up':row.detailsShowing}"></i>
                          <i class="hotbox-icon hotbox-icon-circle-10 customer-icon float-left mr-2"></i>
                          <strong v-if="row.item.alias">{{ row.item.alias }}</strong>
                          <strong v-else>{{ row.item.last_name }}, {{ row.item.first_name }} {{ row.item.middle_name }}</strong>
                          <span v-if="row.item.pending_sales_count" class="badge badge-danger mx-2">{{ row.item.pending_sales_count }} Pending Orders</span><br>
                          <span v-if="'age' in row.item && Number.isInteger(row.item.age)" class="small">{{ row.item.age }} y/o</span>
                          <span v-if="row.item.address" class="customer-details small">
                            {{ row.item.address.email }}{{ row.item.address.email ? ', ' : '' }} {{ row.item.address.cell }}
                          </span>
                        </div>
                      </template>
                        
                      <template v-slot:cell(actions)="row">
                        <button class="btn btn-md btn-info btn-round float-right" @click=""><i class="hotbox-icon hotbox-icon-e-add"></i> Service</button> 
                        <button class="btn btn-md btn-default btn-round float-right mr-1" @click=""><i class="hotbox-icon hotbox-icon-hourglass"></i> Queue</button>
                      </template>                

                      <template v-slot:row-details="row">
                        <customer-detail type="pos" :id="row.item.id"></customer-detail>
                      </template>
                      
                      <template v-slot:table-caption v-if="customersData">
                          <div v-if="customersData.data.length>0">
                              <span v-if="customersData.meta">
                                  Showing {{ ((customersData.meta.per_page*(customersPage-1))+1) }} to {{ (((customersData.meta.per_page*customersPage) < customersData.meta.total) ? (customersData.meta.per_page*customersPage) : customersData.meta.total) }} of {{ customersData.meta.total }} {{ (schema.lang.items) ? schema.lang.items : 'Records' }}
                                  <form-boolean :declared="gridArchive" :schema="{name:'archived',title:'Include Archived Items'}" @input="(upd) => {gridArchive = upd; }" class="mt-1"/>
                              </span>
                              <span v-else>Showing All {{ (schema.lang.items) ? schema.lang.items : 'Records' }}</span>
                              
                              <div class="table-pager-footer">
                                  <b-pagination v-if="customersData.meta.total>0"
                                    v-model="customersPage"
                                    :total-rows="customersData.meta.total"
                                    :per-page="customersData.meta.per_page"
                                    aria-controls="pos_customers_table"
                                  ></b-pagination>
                              </div>
                          </div>
                      </template>
                      
                      <template v-slot:empty>
                          <div v-if="!isLoadingCustomers && customersSearch">
                              <form-boolean :declared="gridArchive" :schema="{name:'archived',title:'Include Archived Items'}" @input="(upd) => {gridArchive = upd; }" class="mt-1"/>
                              <img src="/images/logo.png" alt="No Results" class="" width="65" />
                              <h6>Hmm, No customers match the search "{{ customersSearch }}"</h6>
                          </div><div v-else class="h-75">&nbsp;</div>
                      </template>
                      
                    </b-table>
                </div>
              </transition> 
          
        </div>
      </main>


      <b-modal v-if="" centered ref="createCustomerModal"
        v-model="createCustomerModal"
        size="lg"
        header-bg-variant="light"
        header-text-variant="primary">
      
        <template slot="modal-header">
          <i class="modal-top-close fal ti-close" @click="createCustomerModal=!createCustomerModal"></i>
          <h5 class="w-100 mb-0 text-center">Add a New Customer</h5>
        </template>
            
          <create-customer-modal v-if="createCustomerModal"
            :type="createCustomerType"
            @cancel="createCustomerModal=!createCustomerModal"
            @add="addCustomer">
          </create-customer-modal>
      
        <template slot="modal-footer">
            <span class="btn-label btn-sm btn-light float-right" @click="createCustomerModal=!createCustomerModal">Close</span>
        </template>
      </b-modal>

      
      </div>
    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>
<script>

  import CreateCustomerModal from "./createCustomerModal";
  import CustomerDetail from "../../administration/customer/customerDetail";
  import Customer from '../../../../models/Customer';
  import Queue from '../../../../models/CustomerQueue';   
  import _ from 'lodash';

  export default {
    props: {
     
    },
    
    components: {
      CreateCustomerModal,CustomerDetail
    },
    
    data() {
      return {
        isProcessing:false,
        isLoadingQueue:false,
        isLoadingCustomers:false,
        queue:[],
        queueRefresh:60,
        customersData:null,
        customersPage:1,
        customersSearch:null,
        gridArchive:false,
        customersSortBy:'first_name',
        customersOrderDesc:false,
        createCustomerModal:false,
        createCustomerType:'modal',
        wsStatus: "unknown"
      };
    },

      created() {
          if (this.customerOrder) { return this.serviceFromQueue(this.customerOrder);}
      },

    async mounted() {
        await this.$store.dispatch('administration/setSchemas','customer');        // best we simply just load a fresh customer schema for this customer queue terminal
        if(!this.drawer) this.$router.push({name:'terminal-drawer'});

        if (window.Echo) {
          var _this = this;
          // get current state because the connection may already be established 
          _this.wsStatus = Echo.connector.pusher.connection.state;
          // listen for connection state changes 
          Echo.connector.pusher.connection.bind('state_change', function(states) {
            _this.wsStatus = states.current;
          });

          Echo.channel('customersqueue')
            .listen('CustomerQueueUpdated', (e) => {
              this.queue = e.data;
          });
          
          Echo.channel('customerupdated')
            .listen('CustomerUpdated', (e) => {
                let updatedCustomer = _.find(this.customersData.data, function(o) { return o.id === e.data.id; });
                if(updatedCustomer){
                  updatedCustomer.pending_sales_count = e.data.pending_sales_count;
                }
          });
        }
        
        this.loadQueue(true);
        this.loadCustomers();
    },
    
    computed: {
      schema() {
        return this.$store.state['administration']['customerSchema'];
      },
      
      drawer(){
        return this.$store.state['pos']['drawer'];
      },

      customerOrder() {
        return this.$store.state['pos']['customerOrder'];
      }
    },
    
    methods: {
      async loadQueue(force = false){
        
        if(this.wsStatus !== "connected" || force === true)
        {
          this.isLoadingQueue = true;
          this.queue = await new Queue().get() || [];
        }
        if(this.wsStatus !== "connected")
        {
          this._queueRefreshPoll();                                               // queue refresh poll interval..
        }
        this.isLoadingQueue = false;
      },

      async loadCustomers(){
        if(!this.schema || this.isLoadingCustomers == true) return false;       // do not fetch if we are already fetching
                
          this.isLoadingCustomers = true;
          this.customersData = await new Customer()
                .params({
                    search: this.customersSearch,
                    archived: (this.gridArchive) ? 1 : 0
                })
                .orderBy('first_name')
                .limit(20)
                .page(this.customersPage)
                .get();
            this.isLoadingCustomers = false;
      },       

      searchCustomer: _.debounce(function (e) {                                 // upon search filter update, throttle .3 sec grid and scope refresh
        this.customersPage = 1; 
        this.loadCustomers();
      }, 300),
    
      addCustomer(cust){
        this.createCustomerModal=!this.createCustomerModal;
        if(cust) this.addToQueue(cust.id);
      },
      
      clearQueue(){
        this.$swal.fire({
          title: 'Are you sure?',
          text: 'This will Clear the Entire Queue for '+this.$store.state.user.location.name+' However the visits will still be tracked.',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Clear Queue',
        }).then((result) => {
          if(result.value){
            this.isLoadingQueue = this.wsStatus !== "connected";
            axios.delete('/api/v1/admin/dispensary/customersqueue').then(response =>{
              this.isLoadingQueue = false;
              this.$announcer(response);
              this.loadQueue();
            }).catch(error => {
              this.isLoadingQueue = false;
              this.$announcer(error.response);
            });
          }else{
            //
          }
        });
      },

      addToQueue(cid){
          axios.get('/api/v1/admin/dispensary/customersqueue/add/'+cid).then(response =>{
            this.loadQueue();
          }).catch(error => {
            this.$announcer(error.response);
          });
      },
      
      serviceFromQueue(cid){
          axios.get('/api/v1/admin/dispensary/customersqueue/service/'+cid).then(response =>{
            // this.customersData.data.find(v=>v.id==cid).pending_sales_count++;
            this.$router.push({name:'terminal-order',params:{customer_id:cid}}); // go to order form with customer_id
          }).catch(error => {
            this.$announcer(error.response);
          });
      },
      
      removeFromQueue(qid){
        this.isLoadingQueue = this.wsStatus !== "connected";
          axios.get('/api/v1/admin/dispensary/customersqueue/remove/'+qid).then(response =>{
            if(this.wsStatus !== "connected")
            {this.queue.splice(this.queue.findIndex(v=>v.id==qid),1);}
            this.isLoadingQueue = false;
          }).catch(error => {
            this.isLoadingQueue = false;
            this.$announcer(error.response);
          });
      },

      isOnQueue(custId){
        return (this.queue.find(v=>v.customer_id==custId)) ? true :false;
      },
      
      _queueRefreshPoll(){        // set a 60 second pull request for refreshing queue (this was 10x easier then doing polling) so other budtenders checkins update on this terminal
        if(this.queueRefresh > 0 && this.wsStatus !== "connected") {
          setTimeout(() => {
            this.queueRefresh -= 1;
            this._queueRefreshPoll();
          }, 1000);
        }else{
          this.loadQueue();
          this.queueRefresh = 60;
        }
      },

        renderRowBg(item,type){
            if(!item) return null;
            return {
                'is-archived': !!item.archived_at,
            };
        },
    },
    
    watch: {
      customersPage(to,from){
          this.loadCustomers();
      },
      wsStatus(to,from)
      {
        if(to !== "connected")
        {
          this._queueRefreshPoll();
        }
        console.log(to);
      },
        gridArchive(to,from){
            this.loadCustomers();
        },
    }
  }


</script>
<style scoped>

  .pos-customer-search {
    background-color: #ffffff;
    padding: 0;
  }
  
  .pos-customer-searchfield {
    height: 50px;
    font-size: 1.2em;
  }

  .pos-customer-search-header {
    height: 100px;
  }

  .customer-search-control {
    width: 100% !important;
  }

  .pos-customer-search-results {
    height: calc(100vh - 100px);
    overflow-y: auto;
    background-color: #ffffff;
    padding: 0;
  }

  .pos-customer-search-results .list-group {
    padding: 0;
  }

  .pos-customer-search-results .list-group .list-group-item {
    border-radius: 0;
    border-left-width: 0;
    border-right-width: 0;
  }

  .pos-customer-view {
    padding: 0;
    height: calc(100vh);
    overflow-y: auto;
  }

  .pos-customer-view-header {
    padding: 24px;
  }

  .customer-view {
    padding: 0 24px;
  }

  .customer-icon {
    font-size: 40px;
  }

  .pos-queue-nav {
background-color: #c6c8ca;
    overflow-x: hidden;
    border-radius: 15px;
  }

  .pos-queue {
    margin: 0;
    padding: 0;
  }

  .pos-queue-results {
    height: calc(100vh - 100px);
    top: 200px;
    bottom: 0;
    overflow-y: auto;
    background-color: #c6c8ca;
    padding: 0;
  }

  .pos-queue-results .list-group .list-group-item {
    border-radius: 0;
  }

  .pos-queue-header {
    padding: 0 5px;
    height: 50px;
    margin-top: 0.75rem;
  }

  .queue-position {
    font-size: 2em;
    line-height: 1.5em;
  }

  .btn-queue {
    font-size: 15px;
    height: 30px;
    min-width: 30px;
    width: 30px;
    border-radius: 15px;
    padding: 5px 0 0 0;
    margin: 0 10px 0 0;
  }

  .btn-queue .icon {
    color: #ffffff;
  }

  .btn-add-customer {
    margin: 0 0 0 20px;
  }

  .pos-customer-search .form-control:focus + .input-group-append {
      border: 1px solid #f96332;
      border-left: 0;
  }

  .pos-customer-search .form-control:focus + .input-group-append .input-group-text {
      border: 0;
  }

  .pos-customer-search .form-control + .input-group-append {
      transition: color 0.3s ease-in-out, border-color 0.3s ease-in-out, background-color 0.3s ease-in-out;
  }
</style>
