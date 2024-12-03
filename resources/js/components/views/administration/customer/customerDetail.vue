<template>
    <div v-if="customer && schema" class="card card-stats px-3">
        <form>
            <fieldset>

                <div class="card-header">
                    <div class="row">
                        <div class="col-3 form-group has-label">
                            <div class="card" data-background-color="red">
                                <div class="card-body">
                                    <h6 class="category-social">
                                        <i class="fa fa-fire"></i> Visits
                                    </h6>
                                    <h3 class="mb-2">{{ customer.total_queue_count }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group has-label">
                            <div class="card" data-background-color="red">
                                <div class="card-body">
                                    <h6 class="category-social">
                                        <i class="fa fa-fire"></i> Total Sales
                                    </h6>
                                    <h3 class="mb-2">${{ Math.round(customer.total_spent) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group has-label">
                            <div class="card" data-background-color="red">
                                <div class="card-body">
                                    <h6 class="category-social">
                                        <i class="fa fa-fire"></i> Referrals
                                    </h6>
                                    <h3 class="mb-2">{{ customer.referral_count }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group has-label">
                            <div class="card" data-background-color="red">
                                <div class="card-body">
                                    <h6 class="category-social">
                                        <i class="fa fa-fire"></i> Points
                                    </h6>
                                    <h3 v-if="'total_reward_points' in customer" class="mb-2">{{ customer.total_reward_points | dollar }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            Birthday {{ customer.birthdate | localDate }}, {{ customer.age }} years old.<br>
                            Resident of {{ customer.drivers_license_state || customer.mmj_card_state }}<br>
                            <span v-if="customer.address">
                                <label>Email:</label> {{ customer.address.email }}<br>
                                <label>Mobile:</label> {{ customer.address.cell }}
                            </span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div v-if="'mmj_card' in customer && customer.mmj_card">
                                Medical Marijuana Card on file.<br>
                                <span v-if="customer.settings" class="small">Carry Limit: {{ customer.settings.med_carry_weight }} | plant count: {{ customer.settings.med_plant_count }}<br></span>
                                <span class="small">{{ customer.mmj_card_state}} <i>Expires: {{ customer.mmj_card_expiry_date | localDate }}</i></span>
                            </div>
                            <div v-if="'drivers_license' in customer && customer.drivers_license">
                                Driver's License on file.<br>
                                <span class="small"><i>Expires: {{ customer.mmj_card_expiry_date | localDate }}</i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row comments-row" v-if="editMode">
                        <div class="col-12">
                            <label>Comments</label>
                            <i @click="cancelEdit" class="hotbox-icon hotbox-icon-c-remove"></i>
                            <form-textarea v-model="customer.comments" :schema="schema.form.comments" :hide-label="true"
                                           @input="(upd) => { customer.comments = upd; }"/>
                        </div>
                        <div class="col-12">
                            <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"
                                       :disabled="!isDirty"></auto-save>
                        </div>
                    </div>
                    <div class="row comments-row" v-else>
                        <div class="col-12" @click="editMode=true" style="cursor:pointer;">
                            <label>Comments</label>
                            <i class="hotbox-icon hotbox-icon-edit"></i>
                            <div>{{ customer.comments }}</div>
                        </div>
                    </div>

                    <div class="row mt-2 mb-3">
                        <div class="col-12">
                            <h4><i class="hotbox-icon hotbox-icon-analytics"></i> Marketing Preferences</h4>
                            <div>
                                <form-boolean :declared="customer.sms_optin" :schema="schema.form.sms_optin"
                                              @input="(upd) => {customer.sms_optin = upd; autoSave(); }"
                                              class="mt-2 mb-1"/>
                                <form-boolean :declared="customer.email_optin" :schema="schema.form.email_optin"
                                              @input="(upd) => {customer.email_optin = upd; autoSave(); }"
                                              class="mt-3 mb-2"/>
                                <form-multiselect v-model="customer.preferences" :schema="schema.form.preferences"
                                                  class="mt-1 mb-3"/>
                            </div>
                        </div>
                    </div>


                    <div v-if="(customer.payments || []).length" class="row mt-4">
                        <div class="col-12">
                            <h5><span class="btn-label"><i class="hotbox-icon hotbox-icon-payment"></i></span>
                                Account Payment History</h5>
                        </div>
                        <div class="col-12 col-md-12">
                            <b-table
                                     striped
                                     hover
                                     class="table-nested"
                                     thead-class="show-grey"
                                     :per-page="paymentsHistoryPerPage"
                                     :current-page="paymentsHistoryPage"
                                     :fields="[
                                         {key:'created_at',label:'Posted On',sortable:true},
                                         {key:'updated_at',label:'Last Updated',sortable:true},
                                         {key:'order_number',label:'Sale',sortable:true},
                                         {key:'amount',label:'Purchased',sortable:true},
                                         {key:'amount_owed',label:'Balance',sortable:true}
                                         ]"
                                     :items="customer.payments">
                                <template v-slot:cell(created_at)="row">
                                    {{ row.value | localDate('MM/DD/YYYY LT') }}
                                </template>
                                <template v-slot:cell(updated_at)="row">
                                    {{ row.value | localDate('MM/DD/YYYY LT') }}
                                </template>
                                <template v-slot:cell(order_number)="row">
                                    <router-link :to="{name:'sale_edit',params:{id:row.item.sale_id}}" tag="a" class="">
                                        {{ row.item.order_number }}
                                    </router-link>
                                </template>
                                
                                <template v-slot:cell(amount)="row">
                                    ${{ row.value | dollar }}
                                </template>

                                <template v-slot:cell(amount_owed)="row">
                                    ${{ row.value | dollar }}<i class="hotbox-icon hotbox-icon-pen-01 float-right" @click="promptPayment(row.item.payment_id,row.item.amount_owed,row.item.order_number)"></i>
                                </template>
                                
                                <template v-slot:empty>
                                    <div v-if="!isLoading">
                                        <img src="/images/logo.png" alt="No Results" class="" width="75" />
                                        <h4>Hmm, There are currently no Results.</h4>
                                    </div><div v-else class="h-100">&nbsp;</div>
                                </template>

                                <template v-slot:table-caption v-if="customer.payments">
                                    <div v-if="customer.rewards.length>0">
                                        <span v-if="customer.payments.length>paymentsHistoryPerPage">Showing {{ ((paymentsHistoryPerPage*(paymentsHistoryPage-1))+1) }} to {{ (((paymentsHistoryPerPage*paymentsHistoryPage) < payments.rewards.length) ? (paymentsHistoryPerPage*paymentsHistoryPage) : customer.payments.length) }} of {{ customer.payments.length }} Records</span>
                                        <span v-else>Showing All Records</span>

                                        <div class="table-pager-footer">
                                            <b-pagination v-if="customer.payments.length>0"
                                                          v-model="paymentsHistoryPage"
                                                          :total-rows="customer.payments.length"
                                                          :per-page="paymentsHistoryPerPage"
                                                          aria-controls="users_table"/>
                                        </div>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>


                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><span class="btn-label"><i class="hotbox-icon hotbox-icon-discount-2"></i></span>
                                Rewards (Points) History</h5>
                        </div>
                        <div class="col-12 col-md-12">
                            <b-table v-if="customer.rewards && customer.rewards.length > 0"
                                     striped
                                     hover
                                     class="table-nested"
                                     thead-class="show-grey"
                                     :per-page="rewardsHistoryPerPage"
                                     :current-page="rewardsHistoryPage"
                                     :fields="[
                                         {key:'created_at',label:'Date',sortable:true},
                                         {key:'descriptor',label:'Description',sortable:true},
                                         {key:'points_transacted',label:'Points',sortable:true}
                                         ]"
                                     :items="customer.rewards">
                                <template v-slot:cell(created_at)="row">
                                    {{ row.value | localDate('MM/DD/YYYY LT') }}
                                    <br/>
                                    <span class="small">{{ $moment.utc(row.value).fromNow() }}</span>
                                </template>
                                <template v-slot:cell(points_transacted)="row">
                                    <router-link :to="{name:'reward_edit',params:{id:row.item.id}}" tag="a"
                                                 class="">{{ Number( row.value ).toFixed(2) }}
                                    </router-link>
                                </template>
                                <template v-slot:empty>
                                    <div v-if="!isLoading">
                                        <img src="/images/logo.png" alt="No Results" class="" width="75" />
                                        <h4>Hmm, There are currently no Results.</h4>
                                    </div><div v-else class="h-100">&nbsp;</div>
                                </template>

                                <template v-slot:table-caption v-if="customer.rewards">
                                    <div v-if="customer.rewards.length>0">
                                        <span v-if="customer.rewards.length>rewardsHistoryPerPage">Showing {{ ((rewardsHistoryPerPage*(rewardsHistoryPage-1))+1) }} to {{ (((rewardsHistoryPerPage*rewardsHistoryPage) < customer.rewards.length) ? (rewardsHistoryPerPage*rewardsHistoryPage) : customer.rewards.length) }} of {{ customer.rewards.length }} Records</span>
                                        <span v-else>Showing All Records</span>

                                        <div class="table-pager-footer">
                                            <b-pagination v-if="customer.rewards.length>0"
                                                          v-model="rewardsHistoryPage"
                                                          :total-rows="customer.rewards.length"
                                                          :per-page="rewardsHistoryPerPage"
                                                          aria-controls="users_table"/>
                                        </div>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>


                    <div class="row my-5">
                        <div class="col-12">
                            <h5><span class="btn-label"><i class="hotbox-icon hotbox-icon-cash-register"></i></span>
                                Sales History</h5>
                        </div>
                        <div class="col-12">
                            <b-table v-if="customer.sales && customer.sales.length > 0"
                                     striped
                                     hover
                                     fixed
                                     class="table-nested"
                                     thead-class="show-grey"
                                     :per-page="salesHistoryPerPage"
                                     :current-page="salesHistoryPage"
                                     :fields="[
                                         {key:'created_at',label:'Date',sortable:true,thStyle: {width:'15%'},tdClass:'td-sales-history'},
                                         {key:'sale_price',label:'Sales Details',sortable:true,thStyle: {width:'15%'},tdClass:'td-sales-history'},
                                         {key:'items',label:'Items',sortable:true,tdClass:'td-sales-history'}
                                         ]"
                                     :items="customer.sales">
                                <template v-slot:cell(created_at)="row">
                                    <router-link :to="{name:'sale_edit',params:{id:row.item.id}}" tag="a" class="">
                                        {{ row.item.order_number }}
                                    </router-link>
                                    <br>
                                    <span class="small" v-if="row.item.settled_at">{{ row.item.settled_at | localDate }}<br>{{ $moment.utc(row.item.settled_at).fromNow() }}</span>
                                    <span class="small" v-else>{{ row.value | localDate }}<br>{{ $moment.utc(row.value).fromNow() }}</span>
                                </template>
                                <template v-slot:cell(sale_price)="row">
                                    <strong>Sale Price: {{ Number(row.value).toFixed(2) }}</strong>
                                    <div v-for="payment in row.item.payments" v-bind:key="payment.id">
                                        {{ payment.payment_method }}: {{ Number(payment.amount).toFixed(2) }}
                                    </div>
                                    <div class="danger-order-status" v-if="row.item.status !== 'settled'">
                                        {{ row.item.status }}
                                    </div>
                                </template>
                                <template v-slot:cell(items)="row">
                                    <table class="w-100">
                                        <tbody>
                                        <tr v-for="item in row.value" v-bind:key="item.id">
                                            <td class="align-top">
                                                <span v-if="'product' in item.inventory">{{ item.inventory.product.name }}</span>
                                                <div class="description small">{{ item.inventory.item_barcode }}</div>
                                                <div class="description small">Strain: {{ item.inventory.item_strain }}</div>
                                            </td>
                                            <td class="align-top" style="width:10%;">
                                                {{ Number(item.sale_price).toFixed(2) }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </template>
                                <template v-slot:empty>
                                    <div v-if="!isLoading">
                                        <img src="/images/logo.png" alt="No Results" class="" width="75" />
                                        <h4>Hmm, There are currently no Results.</h4>
                                    </div><div v-else class="h-100">&nbsp;</div>
                                </template>

                                <template v-slot:table-caption v-if="customer.sales">
                                    <div v-if="customer.sales.length>0">
                                        <span v-if="customer.sales.length>salesHistoryPerPage">Showing {{ ((salesHistoryPerPage*(salesHistoryPage-1))+1) }} to {{ (((salesHistoryPerPage*salesHistoryPage) < customer.sales.length) ? (salesHistoryPerPage*salesHistoryPage) : customer.sales.length) }} of {{ customer.sales.length }} Records</span>
                                        <span v-else>Showing All Records</span>

                                        <div class="table-pager-footer">
                                            <b-pagination v-if="customer.sales.length>0"
                                                          v-model="salesHistoryPage"
                                                          :total-rows="customer.sales.length"
                                                          :per-page="salesHistoryPerPage"
                                                          aria-controls="users_table"/>
                                        </div>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </div>

            </fieldset>
        </form>
        
        <b-modal centered ref="paymentModal"
            v-model="paymentModal"
            size="md"
            :no-enforce-focus="true"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="paymentModal=!paymentModal"></i>
              <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-currency-dollar"></i> Record Account Payment</h5>
            </template>
          
            <div class="col-12">
                <loading :display="(isLoading) ? true : false" type="loadModal" />
                <h5 class="w-100 text-center">
                    <span class="small">For: {{ customer.first_name}} - {{ paymentOrder }}</span>
                </h5>
    
                <form-number v-model="paymentAmount" :schema="schema.form.account_payment" class="col-sm-12 mt-3 mb-2" />
                
                <div class="col-12 mt-2 mb-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-info btn-round" @click="payAccount">Record Payment</button>
                </div>
            </div>
        
            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="paymentModal=!paymentModal">Close</span>
            </template>
        </b-modal>
        
    </div>
    <div v-else>
        <loading :display="(schema && customer) ? false : true" type="loadModal"/>
    </div>
</template>
<script>

    import Customer from '../../../../models/Customer';
    import _ from 'lodash';

    export default {
        props: {
            type: {
                type: [String, Number],
                default: 'pos',
            },
            id: {
                type: Number,
                default: null
            },
            model: {
                type: String,
                default: 'Customer'
            },
            module: {
                type: String,
                default: 'administration',
            }
        },

        data() {
            return {
                isLoading: false,
                customer: null,
                initialCustomer: null,
                itemState: 'save',
                isDirty: false,
                editMode: false,
                rewardsHistoryPerPage: 5,
                rewardsHistoryPage: 1,
                salesHistoryPerPage: 5,
                salesHistoryPage: 1,
                paymentsHistoryPerPage: 10,
                paymentsHistoryPage: 1,
                paymentModal:false,
                paymentId:null,
                paymentOrder:null,
                paymentAmount:0
            };
        },

        components: {},

        async mounted() {
            this.isLoading = true;
            if (!this.schema) await this.$store.dispatch(this.module + '/setSchemas', 'customer');

            if (this.id) {
                Customer.find(this.id).then(response => {
                    this.customer = new Customer(response).withDefaults(this.schema, false);
                    this.$emit('loaded');
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({
                        status: 400,
                        data: {message: 'We had a hiccup fetching the data - Please try again later.'}
                    });
                });
            } else {
                this.isLoading = false;
                this.$announcer({
                    status: 422,
                    data: {message: 'Whoops - couldnt find the associated record - Please try again later.'}
                });
            }
        },

        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
            }
        },

        watch: {
            customer: {
                handler(newVal, oldVal) {
                    if (!oldVal) this.resetDirty();

                    this.isDirty = oldVal && !_.isEqual(newVal, this.initialCustomer);
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            },
            'customer.preferences'(to, from) {
                if (to && from)
                    if (to.length != from.length) this.autoSave();
            }
        },

        methods: {
            resetDirty() {
                this.initialCustomer = _.cloneDeep(this.customer);
                this.isDirty = false;
                this.editMode = false;
            },

            autoSave(confirm = false) {
                if (!this.customer) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        if (!confirm) this.debounceSave();
                        else {
                            console.log('no debounce');
                            this._save(true);
                        }
                    } else if (confirm == true) {
                        this.$announcer({
                            status: 422,
                            data: {message: 'Whoops, Please check and correct inputs in order to continue.'}
                        });
                    } else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
            },

            debounceSave: _.debounce(function () { //debounce returns function we need to be able to call multiple times
                this._save();
            }, 900),

            _save(confirm = false) {
                this.itemState = 'saving..';
                this.customer.save().then(response => {
                    this.$announcer({status: 200, data: {message: 'The customers preferences have been saved'}});
                    this.itemState = 'saved';
                    this.resetDirty();
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },

            cancelEdit() {
                this.customer.comments = this.initialCustomer.comments;
                this.editMode = false
            },
            
            promptPayment(id,amt,title='misc'){
                this.paymentId = id;
                this.paymentAmount = amt;
                this.paymentOrder = title;
                this.paymentModal=true;
            },
            
            async payAccount(){
                
              let withPin = await this.requirePin('Please Enter an Admin PIN to record an account payment');
              if(withPin===false) return false;                                     // an adminpin couldnt be validated HINT add error message here if desired.
              
              this.isLoading = true;
              axios.post('/api/v1/admin/dispensary/sales/' + this.paymentId + '/account',{amount:this.paymentAmount}).then(response => {
                  this.$announcer({status:200,data:{message:'An Account Payment of $'+this.paymentAmount+ ' has been Posted.'}});
                  this.isProcessing = false;
                  this.paymentModal=false;

                    Customer.find(this.id).then(response => {
                        this.customer = new Customer(response).withDefaults(this.schema, false);
                        this.$emit('loaded');
                        this.isLoading = false;
                    });

                  this.isLoading = false;

            	 }).catch(error => {
                  this.$announcer(error.response);
                  this.isLoading = false;
            	 });
            }
        },

        inject: ['$validator']
    };
</script>

<style scoped>
    .comments-row .hotbox-icon {
        cursor: pointer;
        color:#9A9A9A;
        vertical-align: middle;
    }
    .comments-row label {
        cursor: pointer;
    }
    .td-sales-history {
        vertical-align: top;
    }
</style>
