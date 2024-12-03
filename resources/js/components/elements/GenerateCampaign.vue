<template>
    <div class="col-12 mt-3 mb-2">
        
        <div class="w-100 mb-2 mt-2 px-2"><i :class="{'hotbox-icon hotbox-icon-n-check show-green strong':validateGroup,'hotbox-icon hotbox-icon-meeting':!validateGroup}"></i>
            <span v-if="newGroup" class=""><b>1. SMS Customers who Purchased Any of These Items:</b></span>
            <span v-else="newGroup" class=""><b>1. Or Select [Optional] Existing Customer Group to SMS:</b></span>
                <a class="small btn btn-sm btn-light btn-round float-right mt-0" @click.prevent="newGroup=!newGroup"><i class="hotbox-icon" :class="{'hotbox-icon-a-sync': !newGroup,'hotbox-icon hotbox-icon-a-share': newGroup}"></i>
                    <span v-if="!newGroup">Back to Group By Items..</span>
                    <span v-else>Or Select Existing Group..</span>
                </a>

            <table v-show="newGroup" role="table" class="table b-table table-striped mb-0">
                <thead>
                <tr>
                    <th></th>
                    <th>Vendor</th>
                    <th>Item</th>
                    <th width="10%">Confirm</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item,ind) in batch"
                    :class="{'table-success':inSet(item.id,batch_ids), 'is-archived':!item.po.can_generate_po}">
                    <td>
                        <img :src="(item.public_img ? item.public_img : ((item.category||{}).public_img) ? item.category.public_img : '/images/none.jpg')" class="responsive" width="45">
                    </td>
                    <td>
                        {{ item.po.vendor_name }}
                    </td>
                    <td>
                        {{item.name}}
                    </td>
                    <td width="10%" valign="top">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" :checked="inSet(item.id,batch_ids)" :disabled="false" @click="$emit('toggle',{id: item.id,event: $event})"><span class="custom-control-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>

            <div v-if="!newGroup" style="clear: both;">
                <form-select v-model="group_id" :schema="campaignSchema.form.group_id" class="col-12 mb-4 mt-1" :isLive="true" :hideLabel="true" />
            </div>
            <div v-if="newGroup" style="clear: both;">
                <form-text v-model="group.name" :schema="groupSchema.form.name" class="col-12 mt-1 mb-4" />
            </div>
        
        </div>
        
        
        <div class="w-100 mb-4 mt-2 px-2">
                <i :class="{'hotbox-icon hotbox-icon-n-check show-green strong':validateDiscount,'hotbox-icon hotbox-icon-discount-2':!validateDiscount}"></i>
                <b>2. (Optional) Select Associated Discount Rule</b>
                <a v-if="!newDiscount" class="small btn btn-sm btn-light btn-round float-right mt-0" @click.prevent="newDiscount=true"><i class="hotbox-icon hotbox-icon-c-add"></i>
                    Create New Discount Rule</a>
                <a v-if="newDiscount" class="small btn btn-sm btn-light btn-round float-right mt-0" @click.prevent="newDiscount=false"><i class="hotbox-icon hotbox-icon-c-remove"></i>
                    Remove New Discount Rule</a>

            <div v-if="!newDiscount" style="clear: both;">
                <form-select v-model="discount_id" :schema="campaignSchema.form.discount_id" class="col-12 mb-3" :hideLabel="true" />
            </div>
            <div v-if="newDiscount" style="clear: both;">
                <form-text v-model="discount.name" :schema="discountSchema.form.descriptor" class="col-12">
                    <template slot="text-right">
                        <span class="d-block float-right">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" v-model="discount.require_items">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Discount only on items above?</span>
                            </label>
                        </span>
                    </template>
                </form-text>
                
                <div class="row mx-1">
                    <form-text v-model="discount.discount_code" :schema="discountSchema.form.discount_code" class="col-12 col-sm-6 mt-2" />
                    <div class="col-12 col-sm-6 mt-2">
                        <label>Discount (
                            <a href="" :class="{'strong':discount.discount_type=='amt'}" @click.prevent="discount.discount_type='amt'">Amount</a> |
                            <a href="" :class="{'strong':discount.discount_type=='pct'}" @click.prevent="discount.discount_type='pct'">Percent</a>)
                        </label>
                        <form-number v-model="discount.discount_amount"
                                     :schema="Object.assign({},discountSchema.form.discount_amount,{prepend:(discount.discount_type=='pct') ? 'hotbox-icon hotbox-icon-percentage-38' : 'hotbox-icon hotbox-icon-currency-dollar'})"
                                     :hideLabel="true" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 clearfix mt-2 text-center">
            <a @click.default="generateCampaign" :disabled="batchSelected.length===0 || !validateGroup" class="btn btn-md btn-primary text-white">
                 <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> Create Campaign.
            </a>
            <a @click.default="$emit('refresh')" class="btn btn-md btn-light" :class="{'btn-light':batchState=='update','btn-warning':batchState!='update'}">Cancel.</a>
        </div>
    </div>
</template>

<script>
    import groupBy from "lodash/groupBy";

    export default {
        name: "GenerateCampaign",
        components : {

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
        data(){
            return {
                isProcessing:false,
                group_id: null,
                discount_id: null,
                newGroup: true,
                newDiscount: false,
                group: {
                    id: 'NEW',
                    name: 'Selected Products Group '+this.$moment().format('MMDDYY')
                },
                discount: {
                    id: 'NEW',
                    name: '', //(this.batch||[]).map(e=>e.name).join(', ').substring(0,75), //default this to list the product names, while limiting the chars - perhaps a trailing ".. Group MMDDYY"
                    discount_amount: 10,
                    discount_type: 'pct',
                    discount_code:  (new Date()).getFullYear().toString() + '-DISC-',
                    require_items: true,
                },
            };
        },
        async mounted() {
            this.discount.name = (this.batch||[]).map(e=>e.name).join(', '); //default this to list the product names, while limiting the chars - perhaps a trailing ".. Group MMDDYY"
            if (this.discount.name.length>100) this.discount.name = this.discount.name.substring(0,75) + '... Group ' + this.$moment().format('MMDDYY');

            await this.$store.dispatch('loyalty/setSchemas','campaign,group,discount');
        },
        methods: {
            generateCampaign() {
                if (this.locationCount!==1) return this.$swal.fire({
                    text: `The selected items are sold in ${this.locationCount} locations.  You can only create a campaign for one location at a time.`,
                    title: 'Error',
                    type: 'warning',
                    confirmButtonText: 'OK',
                });
                
                this.isProcessing = true;
                let postObject = {
                    name: 'Campaign-' + this.$moment().format('YYYYMMDD-HHmmss'),
                    items: this.batch_ids,
                    group: {
                        id: this.group_id,
                    },
                    discount: {
                        id: null,
                    },
                };

                if (this.discount_id && !this.newDiscount) postObject.discount.id = this.discount_id;
                else if (this.newDiscount) postObject.discount = this.discount;

                if (this.newGroup) postObject.group=this.group;

                axios.post('/api/v1/admin/dispensary/campaigns/generate',postObject).then(response =>  {
                    this.isProcessing = false;
                    this.$emit('state','saved');
                    //response.data.message is something like: Use Discount Code {{code}} for Dave's Discount at {{location}}!

                    this.$announcer({status:response.status===201 ? 200 : response.status,data:{message:'Campaign Created (on hold)'}});

                    //if single PO, just go to PO
                    if (response.data.id) this.$router.push({name:'campaign_edit',params:{'id':response.data.id}});

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
            validateDiscount() {
                if (this.newDiscount) return this.validateNewDiscount;
                return !!this.discount_id;
            },

            validateGroup() {
                if (this.newGroup) return this.group.name!=='';
                return !!this.group_id;
            },

            validateNewDiscount() {
                return this.discount.discount_type
                    && this.discount.discount_amount
                    && this.discount.discount_code;
            },

            locationCount() {
                return Object.keys(groupBy(this.batchSelected,'location_id')).length;
            },

            batchSelected() {
                return this.batch.filter(e=>this.batch_ids.includes(e.id));
            },

            campaignSchema() {
                return this.$store.state['loyalty']['campaignSchema'];
            },

            discountSchema() {
                return this.$store.state['loyalty']['discountSchema'];
            },

            groupSchema() {
                return this.$store.state['loyalty']['groupSchema'];
            }
        }
    }
</script>

<style scoped>

</style>