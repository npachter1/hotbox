<template>
    <div v-if="schema && batch" class="col-12">
        <form class="modal-form">
            <fieldset>

                <div v-if="batch && schema" class="col-12">

                    <div class="nav-tabs-header mb-4">
                        <ul class="nav nav-tabs nav-tabs-custom">
                            <li v-if="tabs.includes('promote')" class="nav-link" :class="{'active':tab=='promote'}">
                                <a href="" class="" @click.prevent="tab='promote'">Promote (Campaign)</a>
                            </li>

                            <li v-if="tabs.includes('po')" class="nav-link" :class="{'active':tab=='po'}">
                                <a href="" class="" @click.prevent="tab='po'">Generate PO</a>
                            </li>
                        </ul>
                    </div>

                    <transition name="bo-slide">
                        <generate-campaign
                                v-if="tab=='promote'"
                                :batch="batch"
                                :batch-state="batchState"
                                :batch_ids="batch_ids"
                                @state="batchState=$event"
                                @toggle="toggleBatchId($event.id, $event.event)"
                                @refresh="$emit('refresh')"></generate-campaign>
                    </transition>

                    <transition name="bo-slide">
                        <generate-po
                                v-if="tab=='po'"
                                :batch="batch"
                                :batch-state="batchState"
                                :batch_ids="batch_ids"
                                @state="batchState=$event"
                                @toggle="toggleBatchId($event.id, $event.event)"
                                @refresh="$emit('refresh')"></generate-po>
                    </transition>
                </div>
                <div class="no-content-block" v-else>
                    <loading :display="true" type="loadModal" />
                </div>

            </fieldset>
        </form>
    </div>
    <div class="no-content-block" v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>
    import GeneratePo from "../../elements/GeneratePo";
    import GenerateCampaign from "../../elements/GenerateCampaign";

    export default {
        name: 'batchEditModal',

        props: {
            module: {
                type: String,
                default: 'products',
            },
            model: {
                type: String,
                default: 'Product'
            },
            schema: {
                type: Object,
                default: () => {}
            },
            ids: {
                type: Array,
                default: () => {}
            },
            focus: {
                type: String,
                default: 'edit'
            },
            defaultQuantities: {
                type: Array,
                default: () => {return []}
            },
            tabs: {
                type: Array,
                default: () => {return ['promote','po']}
            }
        },

        data(){
            return {
                tab: this.focus,
                batch: null,
                batch_ids: this.ids,
                batchSortBy:null,
                batchSortDesc:false,
                batchState: 'update',
                isLoading: false
            };
        },

        components : {
            GeneratePo,
            GenerateCampaign,
        },

        mounted() {
            this.getBatchData();
        },

        methods: {
            getBatchData(){
                this.isLoading = true;
                axios.get('/api/v1/'+this.schema.meta.resource+'/batch?batch_ids='+this.batch_ids.join(',')).then(response =>  {
                    this.batch = this.affixPoProperty(response.data);
                    this.batch_ids = this.batch.map(e=>e.id); //make sure we can perform action against product (this was issue trying to archive product already archived)
                    if (this.tab==='po') this.sortByVendor();
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer(error.response);
                    this.$emit('refresh');
                });
            },

            affixPoProperty(data) {
                return data.map(e=> {
                    let vendor_name;
                    let can_generate_po = false;

                    if (e?.last_received?.vendor?.name) {
                        vendor_name = e.last_received.vendor.name;
                        can_generate_po = true
                    } else if (e?.inv_meta?.vendor) {
                        vendor_name = e.inv_meta.vendor;
                    } else if (e?.last_received?.addressbook_id) {
                        vendor_name = `Unknown (Address Book ID: ${e.last_received.addressbook_id})`;
                    } else {
                        vendor_name = 'Unknown';
                    }

                    let defaultQuantityRecord = this.defaultQuantities.find(quantityRecord=>quantityRecord.id===e.id)

                    return Object.assign({}, e, {
                        po: {
                            addressbook_id: e.last_received.addressbook_id,
                            quantity_requested: defaultQuantityRecord ? defaultQuantityRecord.quantity : 10,
                            product_id: e.id,
                            vendor_name,
                            can_generate_po
                        }
                    })
                });
            },

            batchSort(field) {
                this.batchSortBy=field;
                this.batchSortDesc = !this.batchSortDesc;
                this.sorter(this.batch,this.batchSortDesc,this.batchSortBy);    // resort batch
            },

            batchOverride(field,val){
                this.batch = this.batch.map(v => {v[field] = val; return v;});
            },

            toggleBatchId(val,e){
                if(e.target.checked){
                    if(this.batch_ids.indexOf(val) === -1) this.batch_ids.push(val);
                }else this.batch_ids.splice(this.batch_ids.indexOf(val), 1);
            },

            sortByVendor() {
                this.batch.sort((a,b)=>{
                    //also subsort on product name
                    let aName = (a?.last_received?.vendor?.name ? a.last_received.vendor.name + (a.name||'') : a.last_received.addressbook_id.toString());
                    let bName = (b?.last_received?.vendor?.name ? b.last_received.vendor.name + (b.name||'') : b.last_received.addressbook_id.toString());
                    if ((a.po.can_generate_po && b.po.can_generate_po) || (!a.po.can_generate_po && !b.po.can_generate_po)) return aName.localeCompare(bName); //both can or both cannot
                    return (!a.po.can_generate_po) ? 1 : -1; //one can po while the other cannot
                })
            },


        },

        computed: {

        },

        watch: {
            batch:{
                handler(newVal,oldVal){
                    this.batchState = (oldVal) ? 'update changes' : 'update';
                },
                deep: true
            },

            batch_ids(){
                this.$emit('update:ids',this.batch_ids);                        // update ids list past to this component
            },

            tab(newValue) {
                if (newValue==='po') this.sortByVendor();
            }
        }

    };

</script>

<style>

</style>
