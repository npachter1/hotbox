<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="createpackages" @submit.prevent="submit">
        <fieldset>
             <div class="form-row">
                <div class="col-6">
                    <label for="item-room_id">Product</label>
                    <div class="form-group">
                    <select class="form-control" name="product" v-model="newItem.item_id" v-validate="'required'">
                        <option v-for="item in schema.form.item_id.values" :value="item.id">{{ item.name }}</option>
                    </select>
                    <span v-show="errors.has('product')" class="form-text text-muted text-danger">{{ errors.first('product') }}</span> 
                    </div>
                </div>

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
                        </select>
                        <span v-show="errors.has('package_uom')" class="form-text text-muted text-danger">{{ errors.first('package_uom') }}</span> 
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

                <div class="col-12">
                    <table class="table">
                        <thead>
                            <th>Harvest Name</th>
                            <th style="padding-left:20px">Weight per Package</th>
                            <th style="padding-left:20px">Count</th>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in sources" :key="ind">
                            <td style="padding-left:0px">{{ item.name }}
                            </td>
                            <td style="padding:20px">
                                <input type="number"
                                       name="package_weight"
                                       :data-vv-as="'weight per package'"
                                       :id="'package_weight'+ind"
                                        v-model="newItem.package_weights"
                                       style="width:100px"
                                       class="form-control" v-validate="'required|decimal:0'"><span style="width:100px" v-show="errors.has('package_weight')" class="form-text text-muted text-danger">{{ errors.first('package_weight') }}</span></td>
                            <td style="padding:20px">
                                <input type="number"
                                       name="package_count"
                                       :id="'package_count'+ind"
                                        v-model="newItem.package_count"
                                       style="width:100px"
                                        @change="initializeLabels"
                                       class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <div v-if="!sources">
                        <span>No Harvest Selected</span>
                    </div>

                <div class="col-8">
                    <label>Package Tag # (s)</label>
                    <div class="form-group" v-for="n in (newItem.package_count*1)" :key="n">
                    <input type="text" :name="'package_tag'+n"
                                :data-vv-as="'package tag'"
                                class="form-control" v-validate="'required'"
                                @change="setLabel($event,(n-1))">
                    <span v-show="errors.has('package_tag'+n)" class="form-text text-muted text-danger">{{ errors.first('package_tag'+n) }}</span>
                    </div>
                </div>

            </div>
            <div class="col-12 clearfix mt-2 text-center">
                <button class="btn btn-info" type="submit">Create Packages</button>
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
                harvest_id: this.ids,
                labels: null,
                package_weights: null,
                package_count: 1,
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
                        text: 'This will create '+ this.newItem.package_count +' packages from '+this.ids.length+' harvests ',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/'+this.schema.meta.resource+'/createPackages',this.newItem).then(response =>{
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

            initializeLabels() {
                this.newItem.labels = [];
                for (var i=0;i<this.newItem.package_count;i++) {
                    this.newItem.labels[i] = '';
                }
            },
            setLabel(tag, id) {
                if (!this.newItem.labels)
                    this.initializeLabels();
                this.newItem.labels[id] = tag.srcElement.value;
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