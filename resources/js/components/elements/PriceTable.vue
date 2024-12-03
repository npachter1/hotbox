<template>
  <div>
    <div class="dropdown-item divider w-100 text-center">{{ data.name_grade }} Pricing:</div>
    <div role="separator" class="dropdown-divider"></div>
    <div v-if="data.amount_tiers" v-for="(tier,tin) in data.amount_tiers" :key="tin" class="dropdown-item">
      <span v-if="tin==0 && data.amount_tiers[(tin+1)]">at {{ tier.amount | dollar }}{{ data.type_uom }} to {{ data.amount_tiers[(tin+1)].amount | dollar }}{{ data.type_uom }} </span>
      <span v-else-if="tin==(data.amount_tiers.length-1)">at {{ tier.amount | dollar }}{{ data.type_uom }} and up </span>
      <span v-else-if="data.amount_tiers[(tin+1)]">at {{ tier.amount | dollar }}{{ data.type_uom }} to {{ data.amount_tiers[(tin+1)].amount | dollar }}{{ data.type_uom }}</span>
      <span v-else>{{ tier.amount | dollar }}/{{ data.type_uom }} </span>
       = ${{ tier.price | dollar }}/{{ data.type_uom }}
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      data: {
        type: Object,
        default: () => {}
      },
      type:{
        type: String,
        default: 'drop'
      }
    },
    
    computed: {

    }
  }
</script>

<style></style>