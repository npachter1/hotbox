<template>
  <div class="card pos-inventory-card" style="cursor: pointer;" >
    <div v-if="item.retail_unit" class="price-overlay">
      
      <div v-if="item.pricing">
        <span class="badge badge-default badge-inventory-price dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ item.retail_unit | dollar }} 
          <span v-if="item.unit_of_measure === 'g'" class="small">/g</span>
        </span>
          <div class="dropdown-menu tight">
            <price-table :data="item.pricing" />
          </div>
        </div>
        <div v-else class="badge badge-default badge-inventory-price">
          {{ item.retail_unit | dollar }}
           <span v-if="item.unit_of_measure === 'g'" class="small">/g</span>
        </div>
        
    </div>
    <div class="card-image">
      <img :src="getProductImageUrl(item)" @click="$emit('pull')" />
    </div>
    <div class="card-body">
      <h5 class="card-title" @click="$emit('pull')">
        <div :style="{display: item.unit_of_measure==='ea' && ((item.product||{}).category||{}).equivalency_type==='flower' ? 'block' : 'inline'}">{{ item.product.name }}</div>
        <div v-if="item.unit_of_measure==='ea' && item.product.category.equivalency_type==='flower'" class="small float-left">{{item.amount_unit}}g ea</div>
        <span v-if="item.quantity_on_hand>0" class="small float-right" :class="{'sh-red':item.quantity_on_hand<=1,'sh-green':item.quantity_on_hand>2}">
          ({{ Number(item.quantity_on_hand).toFixed(0) + (item.unit_of_measure === 'g' ? 'g' : '') }}) Avail
           <span v-if="item.quantity_pending" class="sh-red small"><strong>/ ({{ Number(item.quantity_pending).toFixed(0) + (item.unit_of_measure === 'g' ? 'g' : '') }}) Pending</strong></span>
        </span>
        <span v-else class="small float-right show-red"><b>None Avail!</b></span>
      </h5>
    </div>
  </div>
</template>
<script>

  export default {
    components: {
    },
    props: {
      item: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        
      }
    },
    methods: {
      
    }
  }
</script>
<style scoped>

  .price-overlay {
    position: absolute;
    top: 5px;
    right: 5px;
    padding: 0;
    margin: 0;
    z-index: 2;
  }

  .pos-inventory-card {
    box-shadow: 0 1px 15px 1px rgba(0,0,0, 0.4);
  }

  .pos-inventory-card .card-title {
    font-size: 0.875em;
    margin: 0;
    color: #212529;
  }

  .pos-inventory-card .list-group-price .list-group-item {
    background-color: #efefef;
    padding: 5px 10px;
    border: none;
  }

  .pos-inventory-card .card-body {
    padding: 10px;
  }

  .pos-inventory-card .rounded {
    border-bottom-left-radius: 0!important;
    border-bottom-right-radius: 0!important;
  }

  .badge-inventory-price {
    text-transform: none;
    cursor:pointer;
  }
  
    .sh-red{
    color:red;
  }
  .sh-green{
    color:green;
  }

</style>
