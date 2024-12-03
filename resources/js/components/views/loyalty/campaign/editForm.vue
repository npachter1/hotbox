<template>
    <div v-if="item && schema" class="col-12">
    <form v-if="canEdit" class="mt-2">
        <fieldset>

            <h3 v-if="item.id" class="mb-4">Manage Campaign<br>
                <span class="small">{{ item.name }} <span v-if="['pending','held','error','cancelled'].indexOf(item.status)!==-1" class="show-red"> * {{ item.status | renderValue(schema.form.status.values) }}</span></span>
            </h3>
            <h3 v-else>Create a new Campaign:</h3>

            <div class="row mt-2">

                <form-text v-model="item.name" :schema="schema.form.name" class="col-12 mb-4" />

                <h5 class="w-100 mb-0 px-2"><i :class="{'hotbox-icon hotbox-icon-n-check show-green strong':item.group_id,'hotbox-icon hotbox-icon-meeting':!item.group_id}"></i> 1. Select Customer Group <a class="btn btn-sm btn-light btn-round float-right" @click.prevent="groupModal=!groupModal"><i class="hotbox-icon hotbox-icon-c-add"></i> Create New Group</a></h5>
                <form-select v-model="item.group_id" :schema="schema.form.group_id" class="col-12 mb-3" :disabled="!canEdit" :isLive="true" :hideLabel="true" />

                <h5 class="w-100 mb-0 mt-4 px-2"><i :class="{'hotbox-icon hotbox-icon-n-check show-green strong':item.discount_id,'hotbox-icon hotbox-icon-discount-2':!item.discount_id}"></i> 2. (Optional) Select Associated Discount Rule <a class="btn btn-sm btn-light btn-round float-right" @click.prevent="discountModal=!discountModal"><i class="hotbox-icon hotbox-icon-c-add"></i> Create New Discount Rule</a></h5>
                <form-select v-model="item.discount_id" :schema="schema.form.discount_id" class="col-12 mb-3" :disabled="!canEdit" :hideLabel="true" />

                <div v-if="item.discount_id && discountMeta" class="col-12 mx-3 mb-0 mt-1">
                  <label class="w-100"> - Created on {{ discountMeta.created_at | localDate }}, Used {{ discountMeta.count }} times.
                    <span v-if="discountMeta.is_active && discountMeta.scheduled_at" class="small show-red"><b> - *Not Active Until {{ discountMeta.created_at | localDate }}</b></span>
                    <span v-else-if="!discountMeta.is_active" class="small show-red"><b> - *Not Currently Active</b></span>
                  </label>
                  <label v-if="discountMeta.is_exclusive" class="w-100"><b> - *Is Exclusive</b> <span class="small"> - Cannot be combined with other discounts</span></label>
                </div>
                <form-text v-if="item.discount_id" v-model="item.campaign_code" :schema="schema.form.campaign_code" class="col-12 col-sm-6 mb-3 mt-1 mx-3" />

                <h5 class="w-100 mb-0 mt-4 px-2 mb-2"><i class="hotbox-icon hotbox-icon-email"></i> 3. Campaign Delivery
                    <a v-if="item.group_id && item.message" class="btn btn-sm btn-light btn-round float-right" @click.default="confirmTestCampaign"><i :class="{'hotbox-icon hotbox-icon-email':item.type!='sms','hotbox-icon hotbox-icon-phone':item.type=='sms'}"></i> Send Test {{ item.type }}</a>
                </h5>
                <form-text v-model="item.subject" :schema="schema.form.subject" class="col-12" :isDisabled="!canEdit">
                    <template slot="text-right">
                        <span class="small float-right">*{{ schema.lang.template_names }}</span>
                    </template>
                </form-text>
                <form-textarea v-model="item.message" :schema="schema.form.message" :rows="schema.form.message.rows || 3" :hideLabel="true" :isDisabled="!canEdit" class="col-12 mb-3" />
                
                <form-simpleselect v-model="item.type" :schema="schema.form.type" :disabled="!canEdit" class="col-12 col-sm-6 clearfix" />
                <form-datetime v-model="item.scheduled_at" :schema="schema.form.scheduled_at" class="col-12 col-sm-6" />

            </div>

            <div class="col-12 clearfix mt-4 text-center">

                <button type="button" class="btn btn-md btn-primary" @click="autoSave('pending')">
                  <spinner :isProcessing="(isProcessing && item.status!='held') ? true : false" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                  <span class="btn-label"><i :class="{'hotbox-icon hotbox-icon-email':item.type!='sms','hotbox-icon hotbox-icon-phone':item.type=='sms'}"></i></span>
                  {{ (item.id) ? '(Re)' : ''}}{{ (item.scheduled_at) ? 'Queue' : 'Start' }} {{ item.type.toUpperCase() }} Campaign
                </button>
                
                <button type="button" class="btn btn-md btn-light" @click.prevent="autoSave('held')">
                  <spinner :isProcessing="(isProcessing && item.status=='held') ? true : false" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                  <span class="btn-label"><i class="hotbox-icon hotbox-icon-saved-items"></i></span>
                  HOLD FOR LATER
                </button>
                
                <button v-if="item.status!='working' && item.id && !isArchived" type="button" class="btn btn-md btn-danger" @click.prevent="confirmDelete">
                    <span class="btn-label"><i class="hotbox-icon hotbox-icon-archive-drawer"></i></span>
                    ARCHIVE
                </button>

                <div v-if="item.group_id" class="block-announce info">
                    <p class="title"><i :class="{'hotbox-icon hotbox-icon-email':item.type!='sms','hotbox-icon hotbox-icon-phone':item.type=='sms'}"></i> A Note for {{ item.type.toUpperCase() }} Notifications:</p>
                    <p>{{ schema.lang.delivery_note }} - Starting <b v-if="item.scheduled_at && this.$moment(item.scheduled_at)>this.$moment()">{{ item.scheduled_at | localDate }}</b><b v-else>Today</b> and reach <b :class="{'show-red':groupMeta['customers_'+item.type+'_count']<=0}">{{ groupMeta['customers_'+item.type+'_count'] }} opted-in</b> of {{ groupMeta.customers_count }} total customers currently in the group.</p> 
                    
                    <p v-if="schema.agg.campaign_sms_limit <= 200 || schema.agg.campaighn_sms_used >= (schema.agg.campaign_sms_limit - 100)" class="show-red">
                        * IMPORTANT: This Location has {{ (schema.agg.campaign_sms_limit - schema.agg.campaign_sms_used) }} of {{ schema.agg.campaign_sms_limit }} Texts remaining.
                    </p>
                    
                    <p v-if="$store.state.user.location.is_demo" class="small show-red"><i><b>{{ schema.lang.test_mode_note }}</b></i><br>
                        <form-text v-model="item.sms_test_number" :schema="schema.form.sms_test_number" :hideLabel="true" class="col-12 col-sm-8 mt-3 mb-2" />
                    </p>
                    <p v-else-if="groupMeta.customers_count!=groupMeta['customers_'+item.type+'_count']" class="small show-red">{{ schema.lang.hold_note }}</p>
                </div>
            </div>

        </fieldset>
    </form>


    <div v-if="canEdit===false" class="col-12 row">
          <h3><i class="hotbox-icon hotbox-icon-heart-add"></i> {{ item.name }} Campaign Performance:</h3>
          <div class="col-12 small">
            Start Scheduled At: {{ item.scheduled_at | localDate }}<br>
            Customer Group: <router-link :to="{name: 'group_edit', params: {id: groupMeta.id}}">{{ groupMeta.name }} {{item.customer_log.length }} Opted-in Customers</router-link>.<br>
            Discount Rule: <router-link :to="{name: 'discount_edit', params: {id: discountMeta.id}}">{{ discountMeta.name }}</router-link><br>
            Campaign Code: {{ item.campaign_code }}
          </div>
          
              <div class="col-md-12 mt-3 mb-3">
                  <div class="card card-stats">
                      <div class="card-body">
                          <div class="row">
                              <div class="col-md-3">
                                  <div class="statistics">
                                      <div class="info">
                                          <div class="icon icon-primary">
                                              <i class="hotbox-icon hotbox-icon-b-chat"></i>
                                          </div>
                                          <h3 class="info-title">{{ item.notified_count }}</h3>
                                          <h6 class="stats-title">
                                              Notified
                                          </h6>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="statistics">
                                      <div class="info">
                                          <div class="icon icon-info">
                                              <i class="hotbox-icon hotbox-icon-shop"></i>
                                          </div>
                                          <h3 class="info-title">{{ item.visited_count }}</h3>
                                          <h6 class="stats-title">
                                              Visited
                                          </h6>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="statistics">
                                      <div class="info">
                                          <div class="icon icon-success">
                                              <i class="hotbox-icon hotbox-icon-cash-register"></i>
                                          </div>
                                          <h3 class="info-title">{{ item.purchased_count }}</h3>
                                          <h6 class="stats-title">
                                              Made a Purchase
                                          </h6>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="statistics">
                                      <div class="info">
                                          <div class="icon icon-success">
                                              <i class="hotbox-icon hotbox-icon-discount-2"></i>
                                          </div>
                                          <h3 class="info-title">{{  item.codeused_count }}</h3>
                                          <h6 class="stats-title">
                                              Used Discount Code
                                          </h6>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

            <div class="col-12 clearfix">
              <h5>Customer Management:</h5>
              
                  <b-table v-if="item.customer_log.length" striped hover            
                    id="manage_customers_table"
                    primary-key="id"
                    :items="item.customer_log"
                    :fields="schema.form.customer_log.fields"
                    :busy.sync="isProcessing"
                    :show-empty="true"
                    sort-by="name"
                    :sort-desc="false"
                    :per-page="20"
                    :current-page="filterCustomersPage"
                    responsive="md"
                    stacked="sm">
                
                
                  <template v-slot:cell(status)="row">
                    <i :class="{'hotbox-icon hotbox-icon-c-warning show-red':row.item.response_error,'hotbox-icon hotbox-icon-g-check show-green':row.item.notified_at && !row.item.response_error,'hotbox-icon hotbox-icon-hourglass':!row.item.notified_at && !row.item.response_error}"></i>
                  </template>
                  
                  <template v-slot:cell(first_name)="row">
                    <span v-if="row.item.customer_abbv">
                      {{ row.item.customer_abbv.first_name }} {{ row.item.customer_abbv.last_name }} {{ row.item.customer_abbv.alias }}
                    </span>
                  </template>
                
                  <template v-slot:cell(email)="row">
                    <span v-if="row.item.customer_abbv">
                      <span v-if="item.type=='sms'">{{ (row.item.customer_abbv.address || {}).phone }}</span>
                      <span v-else>{{ (row.item.customer_abbv.address || {}).email }}</span>
                    </span>
                  </template>              
                
                  <template v-slot:cell(notified_at)="row">
                    {{ row.item.notified_at | localDate('MM/DD/YY LTS') }}
                  </template>
                  <template v-slot:cell(visited_at)="row">
                    {{ row.item.visited_at | localDate('MM/DD/YY LTS') }}
                  </template>
                  <template v-slot:cell(purchased_at)="row">
                    {{ row.item.purchased_at | localDate('MM/DD/YY LTS') }}
                  </template>
                  <template v-slot:cell(codeused_at)="row">
                    {{ row.item.code_used_at | localDate('MM/DD/YY LTS') }}
                  </template>

                  <template v-slot:cell(action)="row">
                    <span v-if="row.item.response_error" class="show-red small">{{ row.item.response_error }}</span>
                    <i class="hotbox-icon hotbox-icon-refresh-69 float-right ml-2" :title="'Campaign Management'" v-b-tooltip.hover="'ReQueue notification and stats for this customer..'" @click="resetCustomerLog(row.item.id)"></i>
                  </template>

                  <template v-slot:empty>
                    <div class="text-center small">
                      <img src="/img/logo.png" alt="No Results" class="" width="65" /><br>
                        No Customers have been opted-in to this Campaign
                    </div>
                  </template>
  
                  </b-table>
                  <div class="col-12 clearfix filter-pages mb-3">
                    <b-pagination v-model="filterCustomersPage" :total-rows="item.customer_log.length" per-page="20" class="my-0"></b-pagination>
                  </div>
              
          </div>


      <div class="col-12 text-center mt-3 mb-4 clearfix">
          <button type="button" class="btn btn-md btn-default" @click.prevent="$emit('toggle')" >Close</button>
          <button v-if="item.status=='working' && !item.notified_count" class="btn btn-md btn-warning" @click.prevent="$emit('toggle'); $router.push({name:'campaign_edit',params:{id:item.id,forceStatus:'held'}})">Edit</button>
          <button v-if="['working','completed'].indexOf(item.status)!==-1 && item.id && !isArchived" type="button" class="btn btn-md btn-danger" @click.prevent="confirmDelete">
              <span class="btn-label"><i class="hotbox-icon hotbox-icon-archive-drawer"></i></span>
              ARCHIVE
          </button>
          <div v-else @click.prevent="$swal.fire({text:isArchived ? 'Campaign Already Archived.' : 'You cannot archive in progress or sent campaigns.',title:'Archiving Unavailable',type: 'warning',confirmButtonText:'OK'})" style="cursor: pointer; display: inline-block;">
              <button disabled type="button" class="btn btn-md btn-danger" style="pointer-events: none;" >
                  <span class="btn-label"><i class="hotbox-icon hotbox-icon-archive-drawer"></i></span>
                  ARCHIVE
              </button>
          </div>
      </div>
          
    </div>   


        <b-modal v-if="" centered ref="groupModal"
            v-model="groupModal"
            no-enforce-focus
            size="xl"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="groupModal=!groupModal"></i>
              <h5 class="w-100 mb-0 text-center">{{ (groupModalId) ? 'Modify' : 'Create a' }} Customer Group</h5>
            </template>
          
              <group-form
                  v-if="groupModal"
                  :id="groupModalId"
                  type="modal"
                  @refresh="updateGroup">
              </group-form> 
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="groupModal=!groupModal">Close</span>
            </template>
        </b-modal>

        <b-modal v-if="" centered ref="discountModal"
            v-model="discountModal"
            size="xl"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="discountModal=!discountModal"></i>
              <h5 class="w-100 mb-0 text-center">{{ (discountModalId) ? 'Modify' : 'Create a' }} Customer Group</h5>
            </template>
          
              <rule-form
                  v-if="discountModal"
                  :id="discountModalId"
                  type="modal"
                  @refresh="updateDiscount">
              </rule-form> 
          
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="discountModal=!discountModal">Close</span>
            </template>
        </b-modal>

        <b-modal v-model="sendTestConfirmModal"
             ref="sendTestConfirmModalRef"
             title="Test Campaign"
             size="lg"
             header-bg-variant="light"
             header-text-variant="primary"
             @ok="testCampaign"
             centered>
            <template slot="modal-header">
            <i class="modal-top-close fal ti-close" @click="sendTestConfirmModal=!sendTestConfirmModal"></i>
            <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-phone"></i> Test SMS Campaign</h5>
            </template>

            <div class="form-group gutters mt-3 mb-2">
                <label class="col-md-12 col-form-label">Message Preview</label>
                <div id="productTag" class="col-md-12 text-left" style="border: none; text-align: center;">
                    {{ parseTestTemplate(item.subject) }} - {{ parseTestTemplate(item.message) }}
                </div>
            </div>

            <form-text v-model="item.test_number" :schema="schema.form.test_number" class="col-12 col-sm-8 mt-3 mb-2" />

            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-info" @click="testCampaign">Send</span>
                    <span class="btn-label btn-sm btn-light float-right" @click="sendTestConfirmModal=!sendTestConfirmModal">Close</span>
            </template>
        </b-modal>
    
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Campaign';
    
    import GroupForm from "../group/editForm";                               // create a customer group modal
    import RuleForm from "../discount/editForm";                             // create a discount rule modal    
    
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Campaign'
            },
            module: {
                type: String,
                default: 'loyalty',
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
            },
            forceStatus:{
                type:String,
                default: null
            }
        },
        
        data(){
            return {
                item: null,
                itemState: 'save',
                isProcessing: false,
                isRequeing: false,
                groupModal: false,
                sendTestConfirmModal: false,
                groupModalId:0,
                discountModal: false,
                discountModalId:0,
                filterCustomersPage:1,
            };
        },
        
        components : {
            GroupForm,RuleForm
        },
        
        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas','campaign,group,discount');
            this.loadCampaignData();
        },
        
        methods: {
            loadCampaignData(){
                this.isLoading = true;
                if(this.id){
                    Item.find(this.id).then(response => {
                        this.item = new Item(response).withDefaults(this.schema);
                        if(this.forceStatus) this.item.status=this.forceStatus;
                        this.isLoading = false;
                    }).catch(error => {
                        this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                    });
                }else{
                    this.item = new Item().withDefaults(this.schema);
                    this.item.group_id = this.$route.params.group_id || null;   // preselect a group
                    this.item.discount_id = this.$route.params.discount_id || null; // preselect a discount
                    this.item.name = 'CAMP-'+this.$moment().format('MMDDYY-HH');
                    this.isLoading = false;
                }
            },
            
            autoSave(status='pending'){
                this.$validator.validateAll().then(async (result) => {
                    if(result){

                        if(['pending','working','held'].indexOf(status)!==-1){
                          let withPin = await this.requirePin('Please Enter an Admin PIN to proceed with this Campaign.');
                          if(withPin===false) return false;                     // an adminpin couldnt be validated HINT add error message here if desired.
                        }

                        this.isProcessing = true;
                        this.itemState = 'saving..';
                        this.item.customers = [];                               // do not post customer data, we have a list of ids binded instead
                        this.item.discounts = [];                               // do not post assigned discounts
                        this.item.customer_log = [];
                        this.item.status = status;                              // update status based on caller of submit
                        this.item.save().then(response => {
                            if(confirm){
                                this.isProcessing = false;
                                this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                                if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                                this.$router.push({name:this.model.toLowerCase()});
                            }
                            this.itemState = 'saved';
                        }).catch(error => {
                            this.isProcessing = false;
                            this.$announcer(error.response);
                            this.itemState = 'resave';
                        });                        
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },
            
            updateGroup(upd){
              this.item.group_id = upd.id; // this is ok, cause the schema will have reloaded via the group edit form modal, thus allowing us to select this new group!
              this.groupModal=!this.groupModal;
            },
            
            updateDiscount(upd){
              this.item.discount_id = upd.id; // this is ok, cause the schema will have reloaded via the group edit form modal, thus allowing us to select this new discount
              this.discountModal=!this.discountModal;
            },

            resetCustomerLog(id){
                this.isRequeing = true;
                axios.get('/api/v1/admin/dispensary/campaigns/requeue/'+id).then(response =>  {
                  this.isRequeing = false;
                  this.loadCampaignData();                                      // reset page data - ok if it taks a sec, requeing is a big deal!
                  this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                }).catch(error => {
                  this.isRequeing = false;
                  this.$announcer(error.response);
                });
            },
            
            async confirmDelete(){
                let withPin = await this.requirePin('Please Enter an Admin PIN to archive this Campaign.');
                if(withPin===false) return false;

                this.isLoading = true;
                this.item.delete().then(response => {
                    this.isLoading = false;
                    this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // get schema for new agg data
                    this.$announcer(response);
                    this.$router.push({name:'campaign'});
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer(error.response);
                });
            },

            confirmTestCampaign(){
                this.$refs.sendTestConfirmModalRef.show()
            },

            testCampaign(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.isProcessing = true;
                        axios.post('/api/v1/admin/dispensary/campaigns/test',this.item)
                            .then(response =>{
                                this.$announcer({status:200,data:{message:'Success'}});
                            }).catch(error =>{
                                this.$announcer(error.response);
                            }).finally(() => {
                                this.item.test_number = null
                                this.isProcessing = false;
                            });
                    }
                })
            },
            
            parseTestTemplate(val){
                let line = val || '';

                line = line.replace(/{{name}}/,'Smokey');
                line = line.replace(/{{location}}/,this.$store.state.user.location.name);
                line = line.replace(/{{code}}/,'CMP-12345');
                line = line.replace(/{{campaign_code}}/,'CMP-12345');
                
                
                return line;
            }

        },
        
        
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            
            groupMeta: function(){
                return (this.item) ? this.schema.form.group_id.values.find((v)=>v.id==this.item.group_id) || {} : {};
            },
              
            discountMeta: function(){
                return (this.item) ? this.schema.form.discount_id.values.find((v)=>v.id==this.item.discount_id) || {} : {};
            },
              
            canEdit: function(){
                return ((this.item || {}).notified_count || (this.item || {}).status=='working') ? false : true;               // if at least 1 customer we can edit
            },

            isArchived() {
                return !!(this.item.archived_at);
            },
        },
        
        watch: {
            'item.discount_id': function() {                                    // preload campaign code and message based on discount code selection!
                if(this.item.status=='pending'){
                  let ddata = this.schema.form.discount_id.values.find((v) => v.id==this.item.discount_id);
                  this.item.campaign_code = 'CMP-'+((ddata) ? ddata.discount_code || Math.floor(Math.random() * 10000) : Math.floor(Math.random() * 100000));
                  if(ddata){
                    this.item.subject = 'Hey {{name}}';
                    this.item.message = 'Use Discount Code {{code}} for '+ddata.nickname+' at {{location}}!';
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
