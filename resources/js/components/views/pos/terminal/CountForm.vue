<template>
    <form class="form-horizontal">
        <loading :display="isProcessing" type="loadGrid" />

        <div class="d-flex flex-row justify-content-center">
            <div>
                <button @click="inputBill(100)"
                        type="button" class="btn btn-default btn-bill">
                    $100
                </button>
                <input type="number"
                       v-model="bill_100"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputBill(50)"
                        type="button" class="btn btn-default btn-bill">
                    $50
                </button>
                <input type="number"
                       v-model="bill_50"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputBill(20)"
                        type="button" class="btn btn-default btn-bill">
                    $20
                </button>
                <input type="number"
                       v-model="bill_20"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputBill(10)"
                        type="button" class="btn btn-default btn-bill">
                    $10
                </button>
                <input type="number"
                       v-model="bill_10"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputBill(5)"
                        type="button" class="btn btn-default btn-bill">
                    $5
                </button>
                <input type="number"
                       v-model="bill_5"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputBill(1)"
                        type="button" class="btn btn-default btn-bill">
                    $1
                </button>
                <input type="number"
                       v-model="bill_1"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputCoin(50)"
                        type="button" class="btn btn-default btn-coin">
                    50¢
                </button>
                <input type="number"
                       v-model="coin_50"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile-coin"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputCoin(25)"
                        type="button" class="btn btn-default btn-coin">
                    25¢
                </button>
                <input type="number"
                       v-model="coin_25"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile-coin"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputCoin(10)"
                        type="button" class="btn btn-default btn-coin">
                    10¢
                </button>
                <input type="number"
                       v-model="coin_10"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile-coin"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputCoin(5)"
                        type="button" class="btn btn-default btn-coin">
                    5¢
                </button>
                <input type="number"
                       v-model="coin_5"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile-coin"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <button @click="inputCoin(1)"
                        type="button" class="btn btn-default btn-coin">
                    1¢
                </button>
                <input type="number"
                       v-model="coin_1"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-reconcile-coin"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <label class="badge-extratotal">Extra</label>
                <input type="number"
                       v-model="extra"
                       min="0"
                       @keypress="preventInvalid"
                       class="form-control form-control-extratotal"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

            <div>
                <label class="badge-extratotal">Total</label>
                <input type="text"
                       disabled
                       v-model="'$' + cashDrawerTotal.toLocaleString('en-US', { maximumFractionDigits: 2, minimumFractionDigits: 2})"
                       class="form-control form-control-extratotal"
                       @focus="$event.target.select()"
                       placeholder="">
            </div>

        </div>

        <div v-if="type === 'open'" class="d-flex flex-row-reverse mt-2">
            <div>
                <button type="button"
                        @click="open()"
                        class="btn btn-info btn-drawer-action"
                        v-bind:class="cashDrawerTotal ? '' : 'disabled'">
                    Open Drawer
                </button>
            </div>
        </div>
        <div v-else class="d-flex flex-row-reverse mt-2">
            <div class="mr-2">
                <button type="button"
                        @click="payin()"
                        :disabled="(!cashDrawerTotal) ? true : false"
                        :class="{'show-inactive':cashDrawerTotal==0}"
                        class="btn btn-info btn-drawer-action">
                    Pay-in
                </button>
            </div>
            <div class="mr-2">
                <button type="button"
                        @click="payout()"
                        :disabled="(!cashDrawerTotal) ? true : false"
                        :class="{'show-inactive':cashDrawerTotal==0}"
                        class="btn btn-info btn-drawer-action">
                    Pay-out
                </button>
            </div>
            <div>
                <button type="button"
                        @click="close()"
                        :disabled="(!cashDrawerTotal) ? true : false"
                        :class="{'show-inactive':cashDrawerTotal==0,'btn-danger':Number(cashDrawerTotal).toFixed(2)!=Number(balance).toFixed(2),'btn-success':Number(cashDrawerTotal).toFixed(2)==Number(balance).toFixed(2)}"
                        class="btn btn-drawer-action">
                    Close Out Drawer
                </button>
            </div>
        </div>

    </form>
</template>

<script>
  export default {
    components: {
    },
    props: {
      type: {
        type: String,
        default: null
      },
      balance: {
          type: Number,
          default:0
      }
    },
    data() {
      return {
        bill_1: 0,
        bill_5: 0,
        bill_10: 0,
        bill_20: 0,
        bill_50: 0,
        bill_100: 0,
        coin_1: 0,
        coin_5: 0,
        coin_10: 0,
        coin_25: 0,
        coin_50: 0,
        extra: 0,
        isProcessing: false
      }
    },
    mounted() {
    },
    computed: {
      cashDrawerTotal() {
        let total = 0.00;

        // Calculate in cents, first, to avoid floating point precision issues
        total += this.bill_1 * 100;
        total += this.bill_5 * 5 * 100;
        total += this.bill_10 * 10 * 100;
        total += this.bill_20 * 20 * 100;
        total += this.bill_50 * 50 * 100;
        total += this.bill_100 * 100 * 100;

        // Coins are already in cents:
        total += this.coin_1 * 1;
        total += this.coin_5 * 5;
        total += this.coin_10 * 10;
        total += this.coin_25 * 25;
        total += this.coin_50 * 50;

        // Extra is an exact amount in dollars:
        total += this.extra * 100;

        return (total / 100);
      }

    },
    methods: {
      preventInvalid($event) {
          const keyCode = ($event.keyCode ? $event.keyCode : $event.which);
          if (keyCode < 48 || keyCode > 57) $event.preventDefault();
      },
      inputBill: function (denomination) {
        this['bill_' + denomination] = Number(this['bill_' + denomination]) + 1;
      },

      inputCoin: function (denomination) {
        this['coin_' + denomination] = Number(this['coin_' + denomination]) + 1;
      },

      addChange: function (x, y) {
        return ((x * 100) + y) / 100;
      },

      open: function() {

        // Open the drawer
        this.isProcessing = true;
        this.$store.dispatch('pos/open', {
          bill_1: this.bill_1, bill_5: this.bill_5, bill_10: this.bill_10, bill_20: this.bill_20,
          bill_50: this.bill_50, bill_100: this.bill_100,
          coin_1: this.coin_1, coin_5: this.coin_5, coin_10: this.coin_10, coin_25: this.coin_25,
          coin_50: this.coin_50,
          extra: this.extra
        })
        .then(() => {
            // Now go to the POS:
            this.isProcessing = false;
            this.$router.push({ name: 'terminal-queue'});                       // Now go to the POS:
            this.$announcer({status:200,data:{message:'Cash Drawer has been Opened.  Please select a customer to service..'}});
        }).catch((error)=>{
            this.isProcessing = false;
            this.$announcer({status:400,data:{message:error}});
        });
      },

      close: async function() {
        
        let withPin = (Number(this.cashDrawerTotal).toFixed(2)!=Number(this.balance).toFixed(2)) ? await this.requirePin('Your Balance and Close Amount do not Match! - Please Enter an Admin PIN to Proceed') : true;
        if(withPin===false) return false;                                       // if we have uncomfirmed(scanned) items - require an admin pin to proceed!
        
        this.isProcessing = true;
        this.$store.dispatch('pos/close', {
          bill_1: this.bill_1, bill_5: this.bill_5, bill_10: this.bill_10, bill_20: this.bill_20,
          bill_50: this.bill_50, bill_100: this.bill_100,
          coin_1: this.coin_1, coin_5: this.coin_5, coin_10: this.coin_10, coin_25: this.coin_25,
          coin_50: this.coin_50,
          extra: this.extra
        })
        .then(() => {
            this.isProcessing = false;
            this.resetForm();
            this.$router.push({ name: 'pos_index'});
            this.$announcer({status:200,data:{message:'Cash Drawer has been Closed.'}});
        }).catch((error)=>{
            this.isProcessing = false;
            this.$announcer({status:400,data:{message:error}});
        });
      },

      payin: function() {
        this.isProcessing = true;
        this.$store.dispatch('pos/payin', {
          bill_1: this.bill_1, bill_5: this.bill_5, bill_10: this.bill_10, bill_20: this.bill_20,
          bill_50: this.bill_50, bill_100: this.bill_100,
          coin_1: this.coin_1, coin_5: this.coin_5, coin_10: this.coin_10, coin_25: this.coin_25,
          coin_50: this.coin_50,
          extra: this.extra
        }).then(() => {
          this.isProcessing = false;
          this.resetForm();
          this.$announcer({status:200,data:{message:'Cash Drawer PayIn has been Registered.'}});
        }).catch((error)=>{
            this.isProcessing = false;
            this.$announcer({status:400,data:{message:error}});
        });
      },

      payout: function() {
        this.isProcessing = true;
        this.$store.dispatch('pos/payout', {
          bill_1: this.bill_1, bill_5: this.bill_5, bill_10: this.bill_10, bill_20: this.bill_20,
          bill_50: this.bill_50, bill_100: this.bill_100,
          coin_1: this.coin_1, coin_5: this.coin_5, coin_10: this.coin_10, coin_25: this.coin_25,
          coin_50: this.coin_50,
          extra: this.extra
        }).then(() => {
            this.isProcessing = false;      
            this.resetForm();
            this.$announcer({status:200,data:{message:'Cash Drawer PayOut has been Registered.'}});
        }).catch((error)=>{
            this.isProcessing = false;
            this.$announcer({status:400,data:{message:error}});
        });
      },

      resetForm: function() {
        this.bill_1 = 0;
        this.bill_5 = 0;
        this.bill_10 = 0;
        this.bill_20 = 0;
        this.bill_50 = 0;
        this.bill_100 = 0;
        this.coin_1 = 0;
        this.coin_5 = 0;
        this.coin_10 = 0;
        this.coin_25 = 0;
        this.coin_50 = 0;
        this.extra = 0;
      }

    }
  }
</script>
<style scoped>
    .btn-bill {
        font-size: 20px;
        margin: 15px 5px;
        height: 50px;
        min-width: 50px;
        width: 70px;
        border-radius: 10px;
        padding: 0;
        background-color: #85bb65;
    }

    .btn-bill:hover, .btn-bill:focus, .btn-bill:active {
        background-color: #6ba449;
    }

    .form-control-reconcile {
        padding: 0 10px;
        line-height: 50px;
        height: 50px;
        font-size: 20px;
        border-radius: 10px;
        width: 70px;
        min-width: 50px;
        margin: 5px;
    }

    .btn-coin {
    font-size: 18px;
    height: 60px;
    min-width: 60px;
    width: 60px;
    border-radius: 30px;
    margin: 8px 4px 4px 4px;
    padding: 0;
    }

    .btn-coin:hover, .btn-coin:focus, .btn-coin:active {
        background-color: #a7a7a7;
    }

    .btn-drawer-action {
        font-size: 20px;
        margin: 15px 5px;
        height: 50px;
        min-width: 50px;
        padding: 0 10px;
    }

    .form-control-reconcile-coin {
        margin: 13px 2px 2px 2px;
        padding: 0 10px;
        line-height: 50px;
        height: 50px;
        font-size: 20px;
        border-radius: 10px;
        width: 70px;
        min-width: 50px;
    }

    .badge-extratotal {
        display: inline-block;
        font-size: 20px;
        height: 30px;
        line-height: 70px;
        min-width: 100px;
        width: 100px;
        border-radius: 10px;
        margin: 5px 5px 45px;
        padding: 0;
        text-align: center;
    }

    .form-control-extratotal {
        padding: 0 10px;
        line-height: 50px;
        height: 50px;
        font-size: 20px;
        border-radius: 10px;
        width: 100px;
        margin: 5px;
    }

    .btn-open-close {
        vertical-align: text-bottom;
        padding: 0 10px;
        line-height: 50px;
        font-size: 20px;
        border-radius: 10px;
        min-width: 140px;
        margin: 45px 5px 0 5px;

    }
</style>
