<template>
    <div v-if="schema" class="col-12">
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
            <div class="col-6">
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
                                v-model="newItem.package_date"
                                :bootstrap-styling="true"
                                input-class="form-datepicker"
                                v-validate="'required'"></datepicker>
                </div>
            </div>
            <div class="col-6">
                <label>Unit of Measure</label>
                <div class="form-group">
                    <select name="package_uom"
                            v-model="newItem.package_uom"
                            class="form-control">
                        <option value="Milligrams">Milligrams</option>
                        <option value="Grams">Grams</option>
                        <option value="Ounces">Ounces</option>
                        <option value="Kilograms">Kilograms</option>
                        <option value="Pounds">Pounds</option>
                        <option value="Each">Each</option>
                    </select>
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
                    <th>Harvest Name</th>
                    <th>Weight Packaged</th>
                </thead>
                <tbody>
                    <tr v-for="(harvest,ind) in sources" :key="ind">
                        <td style="width:250px">{{ harvest.name }}</td>
                        <td colspan="2"><input type="number"
                                    :name="'weight'+ind"
                                    :data-vv-as="'weight packaged'"
                                    :id="'weight'+ind"
                                    @change="setWeight($event,harvest.id)"
                                    style="width:150px"
                                    class="form-control" v-validate="'required|decimal:0'">
                                    <span style="width:150px" v-show="errors.has('weight'+ind)" class="form-text text-muted text-danger">{{ errors.first('weight'+ind) }}</span></td>
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

    import Item from '../../../../models/Harvest';
    
    
    export default {

        props: {
            ids: {
                type: Array,
                default: () => {}
            },            
            model: {
                type: String,
                default: 'Harvest'
            },
            module: {
                type: String,
                default: 'plants',
            },
            sources: {
                type: Array,
                default: null
            }
        },

        data() {
          return {
            batch:null,
            isLoading: false,
            isDownloading: false,
            newItem: {
                harvest_ids: this.ids,
                label: null,
                package_weights: null,
                package_uom: 'Grams',
                package_date: new Date(),
                item_id: null,
                patient_license_number: null
            },
            batch_ids: this.ids
          };
        },

        mounted() {
            this.getBatchData();
        },

        methods: {
            submit(e) {
                this.$validator.validateAll().then((result) => {
                    if (result) {

                        this.$swal.fire({
                        title: 'Are you sure?',
                        text: 'This will create a package from '+this.ids.length+' harvests ',
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

            getBatchData(){
                this.isLoading = true;
                axios.get('/api/v1/'+this.schema.meta.resource+'/batch?batch_ids='+this.batch_ids.join(',')).then(response =>  {
                    this.batch = response.data;
                    this.isLoading = false;
                }).catch(error => {
                console.log(error);
                    this.isLoading = false;
                    this.$announcer(error.response);
                    this.$emit('refresh');
                });
            },

            initializeWeights() {
                this.newItem.package_weights = {};
                if (this.sources)
                    this.sources.forEach((item) => {
                        this.newItem.package_weights[item.id] = 0;
                });
            },
            setWeight(weight, id) {
                if (!this.newItem.package_weights)
                    this.initializeWeights();
                this.newItem.package_weights[id] = weight.srcElement.value;
            },

            isMedical() {
                return this.sources[0].location.settings.is_medical;
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