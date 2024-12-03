<template>
    <div v-if="schema" class="col-12">
        <b-table v-if="order"
                 striped
                 hover
                 fixed
                 :tbody-tr-class="rowClass"
                 :per-page="perPage"
                 :current-page="page"
                 :fields="[
                                         {key:'discount_code',label:'Code',sortable:true,thStyle: {width:'15%'}},
                                         {key:'descriptor',label:'Title',sortable:false,thStyle: {}},
                                         {key:'action',label:'Action',sortable:false,thStyle: {width:'15%'}}
                                         ]"
                 :items="discounts">

            <template v-slot:cell(discount_code)="row">
                <strong>{{ (row.item.discount_code=='' || !row.item.discount_code) ? '[Open]' : row.item.discount_code }}</strong>
            </template>

            <template v-slot:cell(descriptor)="row">
                {{ row.item.descriptor }}<br>
                <span v-if="row.item.is_exclusive" class="d-block w-100 nested-text"><strong>[This Discount is Exclusive!]</strong></span>
                <span v-if="!row.item.applied.is_active" class="d-block w-100 show-red nested-text"><strong>{{ row.item.applied.rejection_reason }}</strong></span>
            </template>

            <template v-slot:cell(action)="row">
                <button v-if="row.item.applied.is_active" @click="$emit('remove',row.item.id)" type="button" class="btn btn-danger btn-sm">
                    Remove
                </button>
                <button v-if="isApplicable(row.item.applied.rejection_code) && !row.item.applied.is_active" @click="()=>{$emit('apply',row.item.id);$emit('cancel')}" type="button" class="btn btn-info btn-sm">
                    Apply
                </button>
            </template>

        </b-table>

        <div class="col-12 clearfix filter-pages mb-3" v-if="discounts.length>perPage">
            <b-pagination v-model="page" :total-rows="discounts.length" :per-page="perPage"
                          class="my-0"></b-pagination>
        </div>
        <form-boolean :declared="showInactive" :schema="{name:'archived',title:'Show Inactive Rules'}" @input="(upd) => {showInactive = upd; }" class="mt-1"/>
          

    </div>
    <div v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>

   import Rules from '../../../../models/Discount';


  export default {

        props: {
            order: {
                type: Object,
                default: () => {}
            }
        },
        
        data(){
            return {
                page: 1,
                perPage: 5,
                isLoading:false,
                showInactive:false
            };
        },
        
        components : {

        },
        
        async mounted() {
          if(!this.schema) await this.$store.dispatch('loyalty/setSchemas','discount'); // if we dont have this, please load it for this modal
        },
        
        methods: {
            isApplicable(code){
                return  ([
                    'CODE_NOT_SUPPLIED',
                    'OFFER_OMITTED'].indexOf(code)!==-1) ? true : false;
            },
            
            isLive(code){
                return  ([
                    'INACTIVE',
                    'SCHEDULED_FOR_LATER'].indexOf(code)!==-1) ? false : true;
            },

            rowClass(item, type) {
                return {
                    'table-danger':!this.isApplicable && !item.applied.is_active,
                    'table-success':item.applied.is_active,
                    'table-warning':!this.isLive(item.applied.rejection_code)
                }
            }
        },
        
        computed: {
            discounts() {
                if (this.showInactive) return this.order.discounts;
                return this.order.discounts.filter(e=>{return this.isLive(e.applied.rejection_code)});
            },

            schema() {
                return this.$store.state['loyalty']['discountSchema'];
            }
        },
        
        watch: {

        }
    };
    
</script>

<style scoped>
.nested-text{
    font-size:0.83em;
}
    >>> .b-table .td {
        vertical-align: top;
    }
</style>
