<template>
    <div v-if="items && schema" class="col-12">
    <form>
        <fieldset>
            <div class="table-responsive mt-3 mb-4">
                <b-table
                        striped
                        hover
                        :items="items"
                        primary-key="id"
                        :fields="[
                            {key:'name',label:'Strain Name',thStyle: {width: '35%'} },
                            {key:'notes',label:'(Notes)',thStyle: {width: '50%'} },
                            {key:'inventory_count',label:'# Items',thStyle: {width: '5%', whiteSpace:'nowrap'} },
                            {key:'items_count',label:'Stock',thStyle: {width: '5%'} },
                            {key:'trashCan',label:'',thStyle: {width: '5%'} }
                            ]"
                        :per-page="perPage"
                        :current-page="currentPage">

                    <template v-slot:cell(name)="row">
                        <form-text v-model="row.item.name" :schema="schema.form.strains.properties.name" :hideLabel="true" :isDisabled="!!(row.item.items_count)" />
                    </template>

                    <template v-slot:cell(notes)="row">
                        <form-text v-model="row.item.notes" :schema="schema.form.strains.properties.notes" :hideLabel="true" />
                    </template>

                    <template v-slot:cell(trashCan)="row">
                        <div v-if="!row.item.items_count" @click="removeItem(row.item.id)"><i class="hotbox-icon hotbox-icon-trash-round show-active"/></div>
                        <div v-else><i class="hotbox-icon hotbox-icon-trash-round show-inactive"/></div>
                    </template>

                    <template v-slot:cell()="row">
                        {{ row.value | formatDecimalIfNeeded }}
                    </template>
                </b-table>

                <b-pagination v-if="items.length>perPage"
                              v-model="currentPage"
                              :total-rows="items.length"
                              :per-page="perPage"
                              aria-controls="users_table"
                              align="right" />
            </div>
            
            <div class="col-12 clearfix mt-3 text-center">
                <auto-save type="save" :state="itemsState" @autoSave="saveList(true)"></auto-save>
                <a @click.default="$emit('refresh')" class="btn btn-sm btn-light">Return.</a>
            </div>

        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && items) ? false : true" type="loadModal" />
    </div>
</template>

<script>

     import Items from '../../../../models/InventoryStrain';


    export default {

        props: {
            model: {
                type: String,
                default: 'InventoryStrain'
            },
            module: {
                type: String,
                default: 'products',
            }
        },
        
        data(){
            return {
                items: null,
                itemsState: 'save',
                currentPage: 1,
                perPage: 9,
            };
        },
        
        components : {

        },
        
        mounted() {
            this.isLoading = true;
            Items.get().then(response => {

                this.items = response || [];
                this.isLoading = false;
            }).catch(error => {
        	    this.isLoading = false;
                this.$announcer(error.response);
                this.$emit('refresh','strain');
            });
        },
        
        methods: {
            saveList(typ){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.itemsState = 'saving..';
                        axios.post('/api/v1/'+this.schema.meta.resource+'strains',{strains:this.items}).then(response =>  {
                            if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:'inventorySchema'}); // update schems, will update grid
                            this.itemsState = 'saved';
                            this.$announcer(response);
                            this.$emit('refresh','strain');
                        }).catch(error => {
                            this.itemsState = '(re)update';
                            this.$announcer(error.response);
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },

            addItem(){
                this.items.push({
                    id:0,
                    name:null,
                    notes:null,
                    inventory_count:0,
                    items_count:0
                });
            },
              
            removeItem(itemId){
                this.items.splice(this.items.findIndex(({id}) => id === itemId), 1);
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module]['inventorySchema'];
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            }
        },

        filters: {
            formatDecimalIfNeeded(value) {
                //return Math.round(value*100)/100; //leaves int alone while trimming decimal to 2 max.
                return Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    maximumFractionDigits: 2
                });
            },
        }
        
    };
    
</script>

<style scoped>
.b-table thead tr {
        background-color: unset;
    }
.b-table thead tr th {
        color: unset;
        font-weight: unset;
    }

    .modal-content .modal-body {
        padding-top: 0;
    }

    .hotbox-icon.show-active {
        cursor: pointer;
    }
</style>
