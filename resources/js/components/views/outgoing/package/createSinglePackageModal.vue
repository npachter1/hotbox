<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="createpackage" @submit.prevent="submit">
        <fieldset>
             <div class="form-row">
                <div class="col-6">
                    <label>Package Tag #</label>
                    <div class="form-group">
                        <input type="text"
                                class="form-control"
                                v-model="newItem.label">
                    </div>
                </div>
                <div class="col-6">
                    <label for="item-room_id">Item</label>
                    <div class="form-group">
                    <select class="form-control" name="item" v-model="newItem.item_id" v-validate="'required'">
                        <option v-for="item in schema.form.product_id.values" :value="item.id">{{ item.name }}</option>
                    </select>
                    <span v-show="errors.has('item')" class="form-text text-muted text-danger">{{ errors.first('item') }}</span>      
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
                    <div class="form-group" v-if="patients">
                        <select id="item-patient_license_number"
                                    class="form-control"
                                    name="patient_license_number"
                                    v-model="newItem.patient_license_number"
                                    v-validate="">
                            <option value="">Select Patient</option>
                            <option v-for="option in patients" v-bind:value="option.license_number">
                                {{ option.license_number }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group" v-else>
                        <input id="item-patient_license_number"
                                    aria-describedby="addon-right addon-left"
                                    name="patient_license_number"
                                    ref="patient_license_numberInput"
                                    v-model="newItem.patient_license_number"
                                    v-validate=""
                                    class="form-control"
                                    :class="{'input': true, 'text-danger': errors.has('patient_license_number') }"
                                    type="text"
                                    placeholder="">
                        <span v-show="errors.has('patient_license_number')" class="form-text text-muted text-danger">{{ errors.first('patient_license_number') }}</span>
                    </div>
                </div>

                <div class="col-6">
                    <label>Production Batch</label>
                    <div class="form-group">
                        <select name="is_production_batch"
                                v-model="newItem.is_production_batch"
                                class="form-control">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <label>Production Batch #</label>
                    <div class="form-group">
                        <input type="text"
                                class="form-control"
                                v-model="newItem.production_batch_number"
                                :disabled="newItem.is_production_batch != 1">
                    </div>
                </div>
                <div class="col-6">
                    <label>Requires Remediation</label>
                    <div class="form-group">
                        <select name="is_production_batch"
                                v-model="newItem.product_requires_remediation"
                                class="form-control">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <table class="table">
                        <thead>
                            <th>Source Package(s)</th>
                            <th>Quantity Packaged</th>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in sources" :key="ind">
                                <td>{{ item.label }}</td>
                                <td>
                                    <input type="text"
                                       name="package_weight"
                                       :id="'package_weight'+ind"
                                        @change="setWeight($event,item.id)"
                                       style="width:75px"
                                       class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <div v-if="sources">
                        <span v-if="sources.length < 1">No Packages Selected</span>
                    </div>
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
                default: 'Package'
            },
            module: {
                type: String,
                default: 'outgoing',
            },
            sources: {
                type: Array,
                default: null
            },
            patients: {
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
                package_ids: this.ids,
                label: null,
                package_weights: null,
                package_uom: 'Grams',
                package_date: new Date(),
                item_id: null,
                patient_license_number: null,
                is_production_batch: 0,
                production_batch_number: null,
                product_requires_remediation: 0
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
                                if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                                this.$emit('refresh');
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
            isMedical() {
                return this.sources[0].location.settings.is_medical;
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