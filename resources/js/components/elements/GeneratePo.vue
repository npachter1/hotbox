<template>
    <div>
        
        <div v-if="1==1" class="block-announce info mt-4 mb-4">
            <p class="title"><i class="hotbox-icon hotbox-icon-add-to-cart"></i> Reordering!..</p>
            <p>1 PurchaseOrder will be auto-generated for each Vendor based on the Quantity of each Product selected.  You may then send, confirm and receive.</p>
        </div>
        
        <table role="table" class="table b-table table-striped">
            <thead>
            <tr>
                <th></th>
                <th>Vendor</th>
                <th>Item</th>
                <th width="20%">Quantity</th>
                <th width="10%">Confirm</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,ind) in batch"
                :class="{'table-success':inSet(item.id,batch_ids) && item.po.can_generate_po, 'is-archived':!item.po.can_generate_po}">
                <td>
                    <img :src="(item.public_img ? item.public_img : ((item.category||{}).public_img) ? item.category.public_img : '/images/none.jpg')" class="responsive" width="45">
                </td>
                <td>
                    {{ item.po.vendor_name }}
                </td>
                <td>
                    {{item.name}}
                     <span v-if="!item.po.can_generate_po" class="small"><i>*Product needs to be first received before it can be reordered.</i></span>
                </td>
                <td width="20%">
                    <form-number v-if="item.po.can_generate_po" v-model="item.po.quantity_requested" :min="0" :schema="{name:'quantity_'+item.id,append:1,append_text:item.inv_meta.uom}" :hideLabel="true" />
                </td>
                <td width="10%" valign="top">
                    <label v-if="item.po.can_generate_po" class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :checked="inSet(item.id,batch_ids)" :disabled="false" @click="$emit('toggle',{id: item.id,event: $event})"><span class="custom-control-indicator"></span>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="col-12 clearfix mt-2 text-center">
            <a @click.default="generatePo" :disabled="batchSelectedForPo.length===0" class="btn btn-md btn-primary text-white">
                 <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> Generate PO.
            </a>
            <a @click.default="$emit('refresh')" class="btn btn-md btn-light" :class="{'btn-light':batchState=='update','btn-warning':batchState!='update'}">Cancel.</a>
        </div>
    </div>
</template>

<script>
    import groupBy from "lodash/groupBy";

    export default {
        name: "GeneratePo",
        data(){
            return {
                isProcessing:false,
            };
        },
        props: {
            batchState: {
                type: String,
                default: 'Product'
            },
            batch: {
                type: Array,
                default: () => {}
            },
            batch_ids: {
                type: Array,
                default: () => {}
            }
        },
        methods: {
            generatePo() {
                this.isProcessing = true;
                let postObject = {};

                let groupedByAddressbookId = groupBy(this.batchSelectedForPo.map(e=>e.po), 'addressbook_id');

                postObject.vendorlist = Object.keys(groupedByAddressbookId).map(addressBookId=>{
                    return {
                        addressbook_id: addressBookId,
                        items: groupedByAddressbookId[addressBookId].map(i=>{
                            return {
                                product_id: i.product_id,
                                quantity_requested: i.quantity_requested
                            }
                        })
                    }
                });

                axios.post('/api/v1/admin/dispensary/receivings/generate',postObject).then(response =>  {
                    //this.batchState = 'saved';
                    this.isProcessing = false;
                    this.$emit('state','saved');
                    // this.$announcer(response);
                    this.$announcer({status:response.status===201 ? 200 : response.status,data:{message:response.data.message}});

                    //if single PO, just go to PO
                    if (response.data.resource_ids.length===1) this.$router.push({name:'receiving_edit',params:{'id':response.data.resource_ids[0]}});

                    //if multiple PO, just go to PO grid (and filter/expand these pos?)
                    if (response.data.resource_ids.length>1) this.$router.push({name: 'receiving',params:{open_ids:response.data.resource_ids}});

                    this.$emit('refresh');
                }).catch(error => {
                    //this.batchState = '(re)update';
                    this.isProcessing = false;
                    this.$emit('state','(re)update');
                    this.$announcer(error.response);
                });

            }
        },
        computed: {
            batchCanPo() {
                return this.batch.filter(e=>e.po.can_generate_po);
            },

            batchSelectedForPo() {
                return this.batchCanPo.filter(e=>this.batch_ids.includes(e.id));
            }
        }
    }
</script>

<style scoped>

</style>