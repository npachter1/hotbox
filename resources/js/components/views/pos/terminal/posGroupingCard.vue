<template>
  <div class="card pos-inventory-card" style="cursor: pointer;" >
    <div class="price-overlay">
        <div class="badge badge-default badge-inventory-price">
           ${{ group.inv_meta.retail | dollar }}
        </div>
    </div>
    <div class="card-image">
      <img :src="group.public_img || '/images/none.jpg'" @click="$emit('pull')" />
    </div>
    <div class="card-body">
      <h5 class="card-title" @click="$emit('pull')">
        <div style="inline">{{ group.name }}</div>
        <span v-if="group.inv_meta.onhand>0" class="small float-right" :class="{'sh-red':group.inv_meta.onhand<=1,'sh-green':group.inv_meta.onhand>2}">
          ({{ Number(group.inv_meta.onhand).toFixed(0) }}) Avail
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
      group: {
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
