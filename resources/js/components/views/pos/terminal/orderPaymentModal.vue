<template>
    <div v-if="schema && order" class="col-12">

        
        <div v-if="!order.id">
            <div class="col-12 mt-1 mb-3 d-flex justify-content-center">
                <i class="hotbox-icon hotbox-icon-cash-register show-green pos-complete-icon"></i>
            </div>
                <h5 class="w-100 text-center">{{ orderSubmitted }}<br>Order Successfully Completed!</h5>
                
            <div class="col-12 mb-3 justify-content-center">
                Payment: <span v-for="(amt,typ) in payment" v-if="amt"><b>{{ typ | ucwords }}:</b> ${{ amt | dollar }}</span><br>
                Change Due: ${{ (amountDue * -1) | dollar }}
            </div>
            
            <div class="col-12 mt-2 mb-4 d-flex justify-content-center">
                <button type="button" class="btn btn-info btn-round" @click="printReceipt">Print Receipt</button>
                <button type="button" class="btn btn-primary btn-round" @click="emailReceipt">Email Receipt</button>
                <button type="button" class="btn btn-default btn-round" @click="backToQueue">Go to Queue..</button>
            </div>
        </div>
        <div v-else>
        <transition name="hb-fade">
              <div class="d-flex justify-content-center">
                <div>
                  <button @click="paymentType='cash'"
                          type="button"
                          v-bind:class="paymentType === 'cash' ? 'disabled' : ''"
                          class="btn btn-default btn-payment-pad-type mb-2">
                    <i v-if="paymentType === 'cash'" class="hotbox-icon hotbox-icon-minimal-right"></i>
                    Cash {{ payment.cash | dollar }}
                  </button>
                  <button @click="paymentType='credit'"
                          type="button"
                          v-bind:class="paymentType === 'credit' ? 'disabled' : ''"
                          class="btn btn-default btn-payment-pad-type mb-2">
                    <i v-if="paymentType === 'credit'" class="hotbox-icon hotbox-icon-minimal-right"></i>
                    Credit {{ payment.credit | dollar }}
                  </button>
                  <button @click="paymentType='gift'"
                          type="button"
                          v-bind:class="paymentType === 'gift' ? 'disabled' : ''"
                          class="btn btn-default btn-payment-pad-type mb-2">
                    <i v-if="paymentType === 'gift'" class="hotbox-icon hotbox-icon-minimal-right"></i>
                    Gift {{ payment.gift | dollar }}
                  </button>
                  <button @click="paymentType='account'"
                          type="button"
                          v-bind:class="paymentType === 'account' ? 'disabled' : ''"
                          class="btn btn-default btn-payment-pad-type">
                    <i v-if="paymentType === 'account'" class="hotbox-icon hotbox-icon-minimal-right"></i>
                    Account {{ payment.account | dollar }}
                  </button>
                  <div class="payment-pad-remaining mt-2">
                          <span v-if="amountDue >= 0">
                            Remaining:
                            <br/>
                            ${{ amountDue | dollar }}
                          </span>
                          <span v-else>
                                Change Due:
                                <br/>
                                ${{ (amountDue * -1) | dollar }}
                          </span>
                  </div>
                </div>
                <div>
                  <TouchKeypad
                      v-show="paymentType === 'cash'"
                      beforeTextInput="$"
                      textInputPlaceholder="0.00"
                      :maxNumberOfDecimalPlaces="2"
                      :minNumberOfDisplayDecimalPlaces="1"
                      v-on:touchKeypadChangeValue="setPaymentAmount"
                  />
                  <TouchKeypad
                      v-show="paymentType === 'credit'"
                      beforeTextInput="$"
                      textInputPlaceholder="0.00"
                      :maxNumberOfDecimalPlaces="2"
                      :minNumberOfDisplayDecimalPlaces="1"
                      v-on:touchKeypadChangeValue="setPaymentAmount"
                  />
                  <TouchKeypad
                      v-show="paymentType === 'gift'"
                      beforeTextInput="$"
                      textInputPlaceholder="0.00"
                      :maxNumberOfDecimalPlaces="2"
                      :minNumberOfDisplayDecimalPlaces="1"
                      v-on:touchKeypadChangeValue="setPaymentAmount"
                  />
                  <TouchKeypad
                      v-show="paymentType === 'account'"
                      beforeTextInput="$"
                      textInputPlaceholder="0.00"
                      :maxNumberOfDecimalPlaces="2"
                      :minNumberOfDisplayDecimalPlaces="1"
                      v-on:touchKeypadChangeValue="setPaymentAmount"
                  />
                </div>
                        
              </div>
              </transition>
              
                <div class="col-12 clearfix mt-3 text-center">
                    <button type="button" class="btn btn-default btn-round" @click="$emit('cancel')">Cancel</button> 
                    <button v-if="true" type="button" class="btn btn-primary btn-round" @click="submitPayment(payment)" :disabled="Number(amountDue).toFixed(2) > 0 || orderSubmitted || isNaN(amountDue)">Complete Order</button>
                </div>
                
            </div>

    </div>
    <div v-else>
        <loading :display="(schema && order) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Sale from '../../../../models/Sale';
    import TouchKeypad from './TouchKeypad';

  export default {

        props: {
            order: {
                type: Object,
                default: () => {}
            }
        },
        
        data(){
            return {
                payment: {
                    cash:0,
                    credit:0,
                    gift:0,
                    account:0
                },
                isLoading:false,
                orderSubmitted:null,
                orderTotal:0,
                paymentType:'cash',
                id: null,
            };
        },
        
        components : {
            TouchKeypad
        },
        
        async mounted() {
            if(!this.schema) await this.$store.dispatch('pos/setSchemas','sale'); // if we dont have this, please load it for this modal
            this.id=this.order.id;
        },
        
        methods: {
          setPaymentAmount: function (amount) {
            if (this.paymentType == 'cash') { this.payment.cash = amount; }
            else if (this.paymentType == 'credit') {  this.payment.credit = amount; }
            else if (this.paymentType == 'gift') {  this.payment.gift = amount; }
            else if (this.paymentType == 'account') {  this.payment.account = amount; }
          },
          
          printReceipt(){
              this.isDownloading = true;
              this.$emit('printReceiptPDF',this.id);
              this.isDownloading = false;
            //   axios.get('/api/v1/'+this.schema.meta.resource+'/'+this.id+'/export/pdf',{responseType: 'arraybuffer'})
            //       .then(response =>  {
            //           this.isDownloading = false;
            //           this.downloadFile(response);
            //       })
            //       .catch(error => {
            //           this.isDownloading = false;
            //           this.$announcer(error.response);
            //       });
          },
          
          emailReceipt(){
              
              this.isDownloading = true;
              this.$emit('printReceiptEmail',this.id);
              this.isDownloading = false;
              
          },
          
          submitPayment(pay){
              this.orderSubmitted = this.order.order_number;
              this.orderTotal = this.order.sale_price;
              this.id = this.order.id;
              this.$emit('complete',pay);
          },
          
          backToQueue(){
              this.$store.commit('pos/setCustomerOrder', null); //forget the customerOrder so we load the queue
              this.$router.push({name:'terminal-queue'});
          }
          
        },
        
        computed: {
            schema() {
                return this.$store.state['pos']['saleSchema'];
            },
             
            totalTendered: function () {
                return Number(this.payment.cash) + Number(this.payment.credit) + Number(this.payment.gift) + Number(this.payment.account);
            },
            
            amountDue: function () {
                let price = (this.orderTotal) ? this.orderTotal : this.order.sale_price;
                return Number(price) - this.totalTendered;
            },
        },
        
        watch: {

        }
    };
    
</script>

<style scoped>
  .over-thc-limit {
    color: #ff0000;
  }
  
  .btn-payment-pad {
    font-size: 24px;
    height: 60px;
    min-width: 60px;
    width: 60px;
    border-radius: 30px;
    padding: 0;
    margin: 4px;
  }

  .btn-payment-pad-action {
    font-size: 30px;
    padding-top: 10px;
    padding-right: 2px;
  }

  .btn-payment-pad-type,
  .btn-payment-pad-type:visited,
  .btn-payment-pad-type:focus {
    font-size: 18px;
    height: 60px;
    min-width: 200px;
    width: 200px;
    border-radius:30px;
    padding: 0;
    margin: 0;
  }

  .payment-pad-remaining {
    font-size: 18px;
    height: 60px;
    min-width: 200px;
    width: 200px;
    padding: 0;
    margin: 0;
    text-align: center;
  }
  
  .show-white{
    color:#fff;
  }
  
  .pos-complete-icon{
      font-size:4.3em;
  }
  
</style>
