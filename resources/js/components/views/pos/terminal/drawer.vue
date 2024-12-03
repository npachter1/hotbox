<template>
    <div v-if="schema" class="col-12">
        
    <div class="d-flex justify-content-center">
      <div class="card">
        <div class="card-header">
          <h5 v-if="!drawer" class="card-title">Hi {{ $store.state.user.name }}, Lets First Open a Cash Drawer:</h5>
          <h5 v-else>{{ $store.state.user.name }}'s Cash Drawer</h5>
        </div>
        <div class="card-body">
          <count-form :type="(drawer) ? 'close' : 'open'" :balance="(drawer) ? Number(drawer.current_balance) : 0"></count-form>
        </div>
      </div>
    </div>        
        
    <div v-if="drawer" class="row">
      <div class="col-12">
         <drawer-detail type="pos" :id="drawer.id"></drawer-detail>
      </div>
    </div>

    </div>
    <div v-else>
        <loading :display="schema ? false : true" type="loadPage" />
    </div>
</template>
<script>

  import CreateCustomerModal from "./createCustomerModal";
  import Customer from '../../../../models/Customer';
  import CountForm from './CountForm.vue'
  import DrawerDetail from "../drawer/drawerDetail";
  import _ from 'lodash';

  export default {
    props: {
     
    },
    
    components: {
      CreateCustomerModal,CountForm,DrawerDetail
    },
    
    data() {
      return {
        isLoading:false,
        isProcessing:false
      };
    },
    
    async mounted() {
        this.isLoading = true;
        await this.$store.dispatch('pos/setSchemas','drawer');        // best we simply just load a fresh customer schema for this customer queue terminal
    },
    
    computed: {
      schema() {
        return this.$store.state['pos']['drawerSchema'];
      },
      
      drawer(){
        return this.$store.state['pos']['drawer'];
      }
    },
    
    methods: {

    }
  }


</script>
<style scoped>
  .closer-drawer-card {
    margin: 15px;
    padding-bottom: 15px;
  }
  .open-drawer-card {
    margin: 15px;
    padding-bottom: 15px;
  }
    .danger-order-status {
    color: red;
    text-transform: uppercase;
    font-weight: bold;
  }
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
        font-size: 20px;
        height: 70px;
        min-width: 70px;
        width: 70px;
        border-radius: 35px;
        margin: 5px;
        padding: 0;
        background-color: #C0C0C0;
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
        padding: 0 10px;
        line-height: 70px;
        font-size: 20px;
        border-radius: 35px;
        width: 70px;
        margin: 5px;
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
