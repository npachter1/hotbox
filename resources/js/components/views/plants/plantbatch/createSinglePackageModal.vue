<template>
    <div v-if="batch && schema" class="col-12">
        <form class="modal-form" id="createpackage" @submit.prevent="submit">
        <fieldset>
       <div class="form-row">

            <div class="col-6">
                <label>Package Tag #</label>
                <div class="form-group">
                    <input type="text"
                            name="package_tag"
                            class="form-control"
                            :data-vv-as="'package tag'"
                            v-model="newItem.label" v-validate="'required'">
                    <span v-show="errors.has('package_tag')" class="form-text text-muted text-danger">{{ errors.first('package_tag') }}</span> 
                </div>
            </div>
            <div v-if="schema.form.item_id" class="col-6">
                <label>Product</label>
                <div class="form-group">
                    <select class="form-control" name="product" v-model="newItem.item_id" v-validate="'required'">
                        <option v-for="item in schema.form.item_id.values" :value="item.id">{{ item.name }}</option>
                    </select>
                    <span v-show="errors.has('product')" class="form-text text-muted text-danger">{{ errors.first('product') }}</span> 
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-6">
                <label>Date</label>
                <div class="form-group">
                    <datepicker id="item-date"
                                name="date"
                                :format="'MM/dd/yyyy'" 
                                v-model="newItem.packaged_date"
                                :bootstrap-styling="true"
                                input-class="form-datepicker"
                                v-validate="'required'"></datepicker>
                </div>
            </div>

            <div class="col-6" v-if="isMedical()">
                <label for="item-patient_license_number">Patient License Number</label>
                <div class="form-group">
                    <input id="item-patient_license_number"
                            aria-describedby="addon-right addon-left"
                            name="patient_license_number"
                            ref="patient_license_numberInput"
                            v-model="newItem.patient_license_number"
                            v-validate=""
                            class="form-control"
                            :class="{'input': true}"
                            type="text"
                            placeholder="">
                </div>
            </div>

       </div>

       <div class="form-row">
            <table class="table">
                <thead>
                    <th>Batch Name</th>
                    <th>Packaged Count</th>
                </thead>
                <tbody>
                    <tr>
                        <td style="width:250px">{{ batch.name }}</td>
                        <td colspan="2"><input type="number"
                                    name="count"
                                    :data-vv-as="'packaged count'"
                                    id="count"
                                    v-model="newItem.count"
                                    style="width:150px"
                                    class="form-control" v-validate="'required|decimal:0'">
                                    <span style="width:150px" v-show="errors.has('count')" class="form-text text-muted text-danger">{{ errors.first('count') }}</span></td>
                    </tr>
                </tbody>
            </table>
       </div>
       <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Create Package</button>
       </div>
       </fieldset>
       </form>

    </div>
    <div v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/PlantBatches';
    
    
    export default {

        props: {
            id: {
                type: Number,
                default: null
            },            
            model: {
                type: String,
                default: 'plantBatch'
            },
            module: {
                type: String,
                default: 'plants',
            }
        },

        data() {
          return {
            batch:null,
            isLoading: false,
            isDownloading: false,
            newItem: {
                batch_id: this.id,
                item_id: null,
                label: null,
                count: null,
                packaged_date: new Date(),
                patient_license_number: null
            }
          };
        },

        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // this is a sub resource - it loads its own schema upon modal load
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.batch = new Item(response).withDefaults(this.schema,false);
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                    this.$emit('refresh',{},'log');                          // will close this modal
                });
            }else{
                this.isLoading = false;
                this.$announcer({status:422,data:{message:'Whoops - couldnt find the associated record - Please try again later.'}});
                this.$emit('refresh',{},'log');
            }
        },

        methods: {
            submit(e) {
                this.$validator.validateAll().then((result) => {
                    if (result) {

                        this.$swal.fire({
                        title: 'Are you sure?',
                        text: 'This will create a package from '+this.batch.name,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/'+this.schema.meta.resource+'/createPackage',this.newItem).then(response =>{
                                this.$announcer({status:200,data:{message:'Success'}});
                                if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'}); 
                                this.$emit('refresh');
                                this.isProcessing = false;
                            }).catch(error => {
                                this.isProcessing = false;
                                this.$announcer(error.response);
                            });
                            
                        }else{
                            //
                        }

                    });
                    }    
                });
                 
            },

            isMedical() {
                return this.batch.location.settings.is_medical;
            }
        },
    
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
        }
    }
  };
</script>

<style>
.vdp-datepicker__calendar {
    left: 0px;
}

</style>