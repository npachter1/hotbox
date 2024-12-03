<template>
  <div v-if="data && schema" class="card card-stats px-3 mb-3">

    <div class="card-header">
      <div class="row">
        
        <h5 class="w-50">{{ data.name }} History</h5>
        <div class="w-50 float-right">
          <a v-if="data.closed_at" class="btn btn-sm btn-warning float-right" @click.prevent="modifyModal=!modifyModal">Modify Drawer</a>
          <select v-model="data.user_id"
            class="form-control" 
            v-if="!data.closed_at && $store.state.user.locations_assigned.length>=1"
            @change="assignDrawer()">
              <option v-for="(user,uid) in schema.form.user_id.values"
                :key="user.id"
                :value="user.id">{{ (user.id==data.user_id) ? 'Assigned to:' : 'Switch to:' }} {{ user.name }}
              </option>
          </select>
        </div>
        
      </div>
    </div>
    <div class="card-body">

      <h6 class="mt-1 mb-2">Events</h6>
      <table v-if="data.events" class="table table-condensed table-nested">
        <thead>
        <tr>
          <th>Event</th>
          <th>Date</th>
          <th>Total</th>
          <th>$100</th>
          <th>$50</th>
          <th>$20</th>
          <th>$10</th>
          <th>$5</th>
          <th>$1</th>
          <th>50¢</th>
          <th>25¢</th>
          <th>10¢</th>
          <th>5¢</th>
          <th>1¢</th>
          <th>Extra</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="event in data.events" :key="event.id">
          <td>{{ event.event_type | capitalize({onlyFirstLetter:true}) }}</td>
          <td>{{ $moment.utc(event.created_at).fromNow() }}</td>
          <th>{{ Number(event.total).toFixed(2) }}</th>
          <th>{{ event.bill_100 }}</th>
          <th>{{ event.bill_50 }}</th>
          <th>{{ event.bill_20 }}</th>
          <th>{{ event.bill_10 }}</th>
          <th>{{ event.bill_5 }}</th>
          <th>{{ event.bill_1 }}</th>
          <th>{{ event.coin_50 }}</th>
          <th>{{ event.coin_25 }}</th>
          <th>{{ event.coin_10 }}</th>
          <th>{{ event.coin_5 }}</th>
          <th>{{ event.coin_1 }}</th>
          <th>{{ Number(event.extra).toFixed(2) }}</th>
        </tr>
        </tbody>
      </table>
      
      <h6 class="mt-1 mb-2">Sales</h6>
      <b-table v-if="data.sales && data.sales.length > 0"
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
                                         {key:'items',label:'Items',sortable:false,tdClass:'td-sales-history'}
                                         ]"
                 :items="data.sales">
          <template v-slot:cell(created_at)="row">
            <router-link :to="{name:'sale_edit',params:{id:row.item.id}}" tag="a" class="">
              {{ row.item.order_number }}
            </router-link>
            <br>
            <span class="small">{{ row.value | localDate }}<br>{{ $moment.utc(row.value).fromNow() }}</span>
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

          <template v-slot:table-caption v-if="data.sales">
            <div v-if="data.sales.length>0">
              <span v-if="data.sales.length>salesHistoryPerPage">Showing {{ ((salesHistoryPerPage*(salesHistoryPage-1))+1) }} to {{ (((salesHistoryPerPage*salesHistoryPage) < data.sales.length) ? (salesHistoryPerPage*salesHistoryPage) : data.sales.length) }} of {{ data.sales.length }} Records</span>
              <span v-else>Showing All Records</span>

              <div class="table-pager-footer">
                <b-pagination v-if="data.sales.length>0"
                              v-model="salesHistoryPage"
                              :total-rows="data.sales.length"
                              :per-page="salesHistoryPerPage"
                              aria-controls="users_table"/>
              </div>
            </div>
          </template>
        </b-table>
    </div>

        <b-modal centered ref="modifyModal"
            v-model="modifyModal"
            :no-enforce-focus="true"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
            <template slot="modal-header">
              <i class="modal-top-close fal ti-close" @click="modifyModal=!modifyModal"></i>
              <h5 class="w-100 mb-0 text-center"><i class="hotbox-icon hotbox-icon-money-coins-2"></i> Modify Drawer {{ data.name }}</h5>
            </template>

            <div class="col-12">
              <form-number v-model="data.opening_balance" :schema="schema.form.opening_balance" class="mt-2" />
              <form-number v-model="data.current_balance" :schema="schema.form.current_balance" class="mt-2" />
              <form-number v-model="data.closing_balance" :schema="schema.form.closing_balance" class="mt-2" />
              <div class="col-12 text-center">
                <a href="" class="btn btn-md btn-warning" @click.prevent="assignDrawer('modify')">Modify</a>
              </div>
            </div>

            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right" @click="modifyModal=!modifyModal">Close</span>
            </template>
        </b-modal>


  </div>
  <div v-else>
     <loading :display="(schema && data) ? false : true" type="loadModal" />
  </div>
</template>
<script>

    import Drawer from '../../../../models/Drawer';
    import _ from 'lodash';

    export default {
      props: {
        type: {
          type: [String,Number],
          default: 'pos',
        },
        id: {
          type: Number,
          default: null
        },
        model: {
          type: String,
          default: 'Drawer'
        },
        module: {
          type: String,
          default: 'pos',
        }
      },
        
      data(){
        return {
          isLoading:false,
          isProcessing:false,
          data:null,
          itemState:'save',
          modifyModal:false,
          salesHistoryPerPage: 5,
          salesHistoryPage: 1,
        };
      },
        
      components : {

      },
        
    	async mounted() {
        this.isLoading = true;
        if(!this.schema) await this.$store.dispatch('pos/setSchemas','drawer');
        
        if(this.id){
            Drawer.find(this.id).then(response => {
                this.data = new Drawer(response).withDefaults(this.schema,true);
                this.isLoading = false;
            }).catch(error => {
                this.isLoading = false;
                this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
            });
        }else{
            this.isLoading = false;
            this.$announcer({status:422,data:{message:'Whoops - couldnt find the associated drawer record - Please try again later.'}});
        }
    	},
    	
    	computed: {
        schema() {
          return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
        }
    	},
    	
      watch: {

      },
    	
    	methods: {
        async assignDrawer(typ='assign'){
          
          let withPin = await this.requirePin('Please Enter an Admin PIN to modify this Item');
          if(withPin===false) return false;                                     // an adminpin couldnt be validated HINT add error message here if desired.
          
          this.isProcessing = true;
          axios.post('/api/v1/admin/dispensary/drawers/' + this.data.id + '/reassign',this.data).then(response => {
              this.$announcer({status:200,data:{message:'Drawer has been sucessfully Reassigned.'}});
              this.isProcessing = false;
              if(typ=='modify') this.modifyModal=false;
              this.$emit('updateData',response.data);
        	 }).catch(error => {
              this.$announcer(error.response);
              this.isProcessing = false;
        	 });
          
        }

    	}
    };
</script>

<style scoped>
  >>> .td-sales-history {
    vertical-align: top;
  }
</style>
