<template>
    <div v-if="items && schema" class="col-12">

    <h5 class="mt-2">Current Reward Triggers:</h5>
      <div class="mt-1 mb-4">
        <table class="table table-striped table-hover table-responsive table-nested">
          <thead>
            <tr>
              <th width="90%">Trigger</th>
              <th width="10%">Points Rewarded</th>
            </tr>
            </thead>
            <tbody>
              <tr v-if="item.is_active" v-for="(item,iid) in items" :key="item.id">
                <td width="90%">
                  {{ item.descriptor }}<br>
                  <span class="small">
                    <i v-if="item.notes">{{ item.notes }} </i>
                    <b v-if="item.is_exclusive">Can not be Combined. </b>
                  </span>
                </td>
                <td width="10%" align="right">{{ item.point_amount | dollar }}</td>
              </tr>
            </tbody>
        </table>
      </div>
            
      <h5 class="mt-1 mb-3">Current Loyalty based Discount Rules:</h5>
      <div class="mt-3 mb-4">
        <loading :display="isLoadingRules" type="loadGrid" />
        <table v-if="rules" class="table table-striped table-responsive table-nested">
          <thead>
            <tr>
              <th width="65%">Description</th>
              <th width="15%">Code</th>
              <th width="10%">Amount</th>
              <th width="10%">Points to Redeem</th>
            </tr>
            </thead>
            <tbody>
              <tr v-for="(rule,rid) in rules" v-if="(rule.settings || {}).pointsToRedeem && rule.is_active" :key="rule.id">
                <td width="65%">
                  {{ rule.descriptor }}<br>
                  <span class="small">
                    <b v-if="rule.is_exclusive">*Is Exclusive - Can not be Combined. </b>
                    <b v-if="rule.max_per_customer">{{ rule.max_per_customer }} Max uses per Customer </b>
                    <b v-if="rule.settings.minSpend">Must spend at least ${{ rule.settings.minSpend | dollar }} </b>
                    <b v-if="rule.settings.minSales">Must have at least {{ rule.settings.minSales }} Previous Sales </b>
                  </span>
                </td>
                <td width="15%">{{ rule.discount_code || '[OPEN]' }}</td>
                <td width="10%">
                  <span v-if="rule.discount_type=='amt'">${{ rule.discount_amount | dollar }}</span>
                  <span v-else>{{ rule.discount_amount | dollar(1) }}%</span>
                </td>
                <td width="10%" align="right"><b>{{ rule.settings.pointsToRedeem }}</b></td>
              </tr>
            </tbody>
        </table>
      </div>      
      
    </div>
    <div v-else>
        <loading :display="(schema && items) ? false : true" type="loadModal" />
    </div>
</template>

<script>

     import Items from '../../../../models/RewardTrigger';
     import Rules from '../../../../models/Discount';


    export default {

        props: {
            model: {
                type: String,
                default: 'RewardTrigger'
            },
            module: {
                type: String,
                default: 'loyalty',
            }
        },
        
        data(){
            return {
                items: null,
                rules:null,
                isLoading:false,
                isLoadingRules:false
            };
        },
        
        components : {

        },
        
        async mounted() {
            this.isLoading = true;
            
            if(!this.schema) await this.$store.dispatch('loyalty/setSchemas','reward'); // if we dont have this, please load it for this modal
            
            Items.get().then(response => {
                this.items = response || [];
                this.isLoading = false;
            }).catch(error => {
        	    this.isLoading = false;
                this.$announcer(error.response);
                this.$emit('refresh');
            });

            this.isLoadingRules = true;
            new Rules()
              .setFilters({type:['loyalty']})
              .orderBy('rank')
              .limit(100)
              .page(1)
              .get().then(response => {
                this.isLoadingRules = false;
                this.rules = response.data || [];
            }).catch(error => {
                this.isLoadingRules = false;
                //
            });
        },
        
        methods: {

        },
        
        computed: {
            schema() {
                return this.$store.state[this.module]['rewardSchema'];
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            }
        }
        
    };
    
</script>

<style>

</style>
