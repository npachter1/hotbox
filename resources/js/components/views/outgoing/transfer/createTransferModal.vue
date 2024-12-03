<template>
    <div v-if="item && schema" class="col-12">
        <div v-if="$store.getters.getAgent=='metrc'">
            <div class="block-announce info mt-4 mb-4">
                        <p class="title"><i class="hotbox-icon hotbox-icon-c-info"></i> About Metrc Transfers:</p>
                        <p>{{ schema.lang.metrc_import_note }}</p>
                    </div>
        </div>
        <div v-else>
        
        <form class="modal-form" id="createtransfer" @submit.prevent="submit">
        <fieldset>
             <div class="form-row">
                <form-select v-model="item.type" :schema="schema.form.type" class="col-12 mt-2 mb-2" />
                <form-search v-model="item.addressbook_id" :schema="Object.assign({},schema.form.addressbook_id,{values:tos})" @syncdata="syncAddress" class="col-12 col-sm-12 mt-2 mb-2" />
                
                <form-text v-model="item.manifest_number" :schema="schema.form.manifest_number" class="col-12 col-sm-12 mt-2 mb-2" />
                
                <form-text v-if="item.type=='external'" v-model="item.transporter_name" :schema="schema.form.transporter_name" class="col-12 col-sm-6 clearfix" />
                <form-text v-if="item.type=='external'" v-model="item.transporter_licensenum" :schema="schema.form.transporter_licensenum" class="col-12 col-sm-6" />
                
                <div class="col-12 mt-2 mb-2">
                <label class="w-100 mt-3">Confirm Packages to Transfer:</label>
                    <table class="table table-striped table-hover">
                      <thead>
                      <tr>
                        <th width="5%">&nbsp;</th>
                        <th width="55%">Package</th>
                        <th>Qty</th>
                        <th v-if="item.type=='external'"><i class="hotbox-icon hotbox-icon-c-question" title="SaleOrder Transfer" v-b-tooltip.hover="'What is the selling price for this package being transferred?'"></i> Price</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="(pack,pid) in packageData"
                          :class="{'table-danger':pack.received_from_manifest_number,'table-success':inSet(pack.id,item.package_ids) && !pack.received_from_manifest_number}"
                          :key="pack.id">
                        <td width="5%">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" :checked="inSet(pack.id,item.package_ids)" :disabled="(pack.received_from_manifest_number) ? true : false" @click="togglePackage(pack.id,$event)"><span class="custom-control-indicator"></span> 
                            </label>
                        </td>
                        <td width="5%">
                            {{(pack.item || {}).name || 'Misc Item'}}<br>
                            <span class="small"><i class="hotbox-icon hotbox-icon-tag-content"></i> {{pack.label}}</span>
                            <span v-if="pack.received_from_manifest_number" class="d-block w-100 show-red small"><i class="hotbox-icon hotbox-icon-c-warning"></i> Package has already been transferred.</span>
                        </td>
                        <td align="right" width="5%">
                            {{ pack.quantity }}/{{pack.unit_of_measure}}
                        </td>
                        <td v-if="item.type=='external'">
                            <form-number v-model="pack.received_price" :schema="schema.form.package_price" :hideLabel="true" class="" />
                        </td>
                      </tr>
                      </tbody>
                    </table>
                </div>
             </div>

            <div v-if="item.type=='external'" class="mb-1">
                <form-number v-if="item.type=='external'" v-model="item.transfersale_fee" :schema="schema.form.transfersale_fee" class="col-12 col-sm-12 mt-2 mb-1" />
                <div class="col-12 text-right mt-1 mb-2"><b>Total to Bill: ${{ item.transfersale_total | dollar }}</b></div>
            </div>

            <div class="col-12 clearfix mt-2 text-center">
                    <button class="btn btn-info" :disabled="isProcessing" type="submit">
                        <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                        {{ (isProcessing) ? 'Creating..' : 'Create' }} {{ (item.type=='external') ? 'SaleOrder ' : '' }}Transfer
                    </button>
            </div>

            <div v-if="item.type=='internal'" class="block-announce info mt-4 mb-4">
                <p class="title"><i class="hotbox-icon hotbox-icon-c-info"></i> About Internal Transfers:</p>
                <p>{{ schema.lang.internal_import_note }}</p>
            </div>
            
            <div v-else-if="item.type=='external'" class="block-announce info mt-4 mb-4">
                <p class="title"><i class="hotbox-icon hotbox-icon-c-info"></i> About External (SaleOrder) Transfers:</p>
                <p>{{ schema.lang.external_saleorder_note }}</p>
            </div>
             
        </fieldset>
        </form>
        
        </div>

    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/Transfer';
    
    
    export default {

        props: {
            ids: {
                type: Array,
                default: () => {}
            },            
            model: {
                type: String,
                default: 'Transfer'
            },
            module: {
                type: String,
                default: 'outgoing',
            },
            sources: {
                type: Array,
                default: null
            }
        },

        data() {
          return {
            item:null,
            isLoading: false,
            isProcessing: false,
            packageData:[],
            tos:[]
          };
        },

        async mounted() {
            this.isLoading = true;
            if(this.id){
                await Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.item.package_ids = this.sources.filter(v=>!v.received_from_manifest_number && this.ids.indexOf(v.id)!==-1).map(v=>{ return v.id; });
                    this.packageData = this.sources;
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.item.manifest_number = 'XFER-'+this.$moment().format('MMDDYY');
                this.item.package_ids = this.sources.filter(v=>!v.received_from_manifest_number && this.ids.indexOf(v.id)!==-1).map(v=>{ return v.id; });
                this.packageData = this.sources;
                this.isLoading = false;
            }
        },

        methods: {
            submit(e) {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.$swal.fire({
                        title: 'Are you sure?',
                        text: 'This will create a new Transfer for '+this.ids.length+' Packages',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                            this.item.package_prices = this.packageData.map(v => { return {id:v.id,price:v.received_price};});
                            this.item.save().then(async response => {
                                this.$announcer({status:200,data:{message:response.message}});
                                if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                                this.$emit('refresh',response);
                                this.isProcessing = false;
                                this.$router.push({name:this.model.toLowerCase()}); // go to transfers grid
                            }).catch(error => {
                                this.$announcer(error.response);
                                this.isProcessing = false;
                            });
                        }else{
                            //
                        }
                    });
                    }    
                });
                 
            },
            
            syncAddress(upd){                                                   // sync addresses from selection
                if(!upd) return false;
                if(this.schema.form.addressbook_id.values.find(v=>v.id==upd.id))
                    this.schema.form.addressbook_id.values.push(upd);           // need new value in list for when schema is reloaded
                this.item.receiver = upd;                                       // then, assign the new address to the current package!
            },
            
            togglePackage(val,e){
                if(e.target.checked){
                    if(this.item.package_ids.indexOf(val) === -1) this.item.package_ids.push(val);
                }else this.item.package_ids.splice(this.item.package_ids.indexOf(val), 1);
            },
            
            updateTotal(){
                if(!this.packageData) return false;
                this.item.transfersale_total = (Number(this.item.transfersale_fee) + this.packageData.filter(v=>this.item.package_ids.indexOf(v.id)!==-1 && v.received_price).reduce((total,row) => total + Number(row.received_price),0));
            }
        },
    
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
        },
        
        watch: {
            'item.type'(to,from){
                if(to){
                    this.tos = this.schema.form.addressbook_id.values.filter(v=>v.type==to || to=='metrc');
                }
            },
            packageData: {
                handler: function(to, from) {
console.log(this.packageData);
                    this.updateTotal();
                },
                deep: true,
            },
            'item.transfersale_fee'(to,from){
                this.updateTotal();
            },
            'item.package_ids'(to,from){
                this.updateTotal();
            }
        }
  };
</script>

<style>
.vdp-datepicker__calendar {
    left: 0px;
}
</style>