<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="createplantsform" @submit.prevent="submit">
        <fieldset>
        <h3 v-if="sourcePlant" class="mb-0">Source Plant - {{ sourcePlant.label }}</h3>

        <div class="form-row">
            <div v-if="!source" class="col-6">
                <label>Source Plant</label>
                <div class="form-group">
                    <select id="item-label"
                            class="form-control"
                            name="label"
                            @change="setSourcePlant($event)"
                            v-validate="">
                    <option value="">Select a Plant</option>
                    <option v-for="option in schema.form.plant_id.values"
                            v-bind:value="option.id">
                        {{ option.label }} - {{ option.strain.name }}
                    </option>
                    </select>
                </div>
            </div>

        <div class="col-6">
            <label>Plant Batch Name</label>
            <div class="form-group">
                <input type="text"
                        name="plant_batch_name"
                        :data-vv-as="'plant batch name'"
                        id="item-plant_batch_name"
                        class="form-control"
                        v-model="newItem.plant_batch_name"
                        v-validate="'required'">
                <span v-show="errors.has('plant_batch_name')" class="form-text text-muted text-danger">{{ errors.first('plant_batch_name') }}</span>
            </div>
        </div>

        <div class="col-6">
            <label for="item-plant_batch_type">Plant Batch Type</label>
            <div class="form-group">
                <select id="item-plant_batch_type"
                        aria-describedby="addon-right addon-left"
                        name="plant_batch_type"
                        v-model="newItem.plant_batch_type"
                        v-validate="'required'"
                        class="form-control"
                        :class="{'input': true }">
                    <option value="Clone">Clone</option>
                    <option value="Seed">Seed</option>
                    <option value="Untracked Vegetative">Untracked Vegetative</option>
                </select>
                <span v-show="errors.has('plant_batch_type')" class="form-text text-muted text-danger">{{ errors.first('plant_batch_type') }}</span>
            </div>
        </div>

        <div class="col-6">
            <label>Plant Count</label>
            <div class="form-group">
                <input type="number"
                        name="plant_count"
                        :data-vv-as="'plant count'"
                        id="item-plant_count"
                        class="form-control"
                        v-model="newItem.plant_count"
                        v-validate="'required|decimal:0'">
                <span v-show="errors.has('plant_count')" class="form-text text-muted text-danger">{{ errors.first('plant_count') }}</span>
            </div>
        </div>

        <div class="col-6" v-if="schema.form.can_assign_plant_batch_rooms">
          <label for="item-room_id">Plant Batch Room</label>
          <div class="form-group">
            <select class="form-control" v-model="newItem.new_room">
              <option v-for="room in schema.form.room_id.values" :value="room.id">{{ room.name }}</option>
            </select>
          </div>
        </div>

        <div class="col-6">
          <label for="item-strain_id">Strain</label>
          <div class="form-group">
            <select class="form-control" name="strain" v-model="strain_id" v-validate="'required'">
              <option v-for="strain in schema.form.strain_id.values" :value="strain.id">{{ strain.name }}</option>
            </select>
            <span v-show="errors.has('strain')" class="form-text text-muted text-danger">{{ errors.first('strain') }}</span>
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
                <span v-show="errors.has('patient_license_number')" class="form-text text-muted text-danger">{{ errors.first('patient_license_number') }}</span>
            </div>
        </div>

        <div class="col-6">
            <label for="item-actual_date">Date</label>
            <div class="form-group">
                <datepicker id="item-actual_date"
                            name="actual_date"
                            :format="'MM/dd/yyyy'"
                            v-model="newItem.actual_date"
                            :bootstrap-styling="true"
                            input-class="form-datepicker"
                            v-validate="'required'"></datepicker>
            </div>
        </div>

        <div class="col-6">
            <label>Label</label>
            <div class="form-group">
                <input type="text"
                        name="plant_batch_label"
                        :data-vv-as="'label'"
                        id="item-plant_batch_label"
                        class="form-control"
                        v-model="newItem.plant_batch_label"
                        v-validate="'required'">
                <span v-show="errors.has('plant_batch_label')" class="form-text text-muted text-danger">{{ errors.first('plant_batch_label') }}</span>
            </div>
        </div>

       </div>

        <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Create Plantings</button>
        </div>
        </fieldset>
        </form>
    </div>
    <div v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/Plant';


    export default {

        props: {
            ids: {
                type: Array,
                default: () => {}
            },
            model: {
                type: String,
                default: 'Plant'
            },
            module: {
                type: String,
                default: 'plants',
            },
            source: {
                type: Object,
                default: null
            }
        },

        data() {
          return {
            batch:null,
            isLoading: false,
            isDownloading: false,
            newItem: {
                plant_batch_label: null,
                plant_batch_name: null,
                plant_batch_type: 'Clone',
                plant_count: null,
                patient_license_number: null,
                room_id: null,
                strain_id: null,
                actual_date: new Date(),
                source_plant_id: null
            },
            batch_ids: this.ids,
            strain_id: null,
            sourcePlant: null,
          };
        },

        mounted() {
            if(this.ids)
            {
                this.getBatchData();
                this.strain_id = this.source.strain_id;
                this.sourcePlant = this.source;
                this.newItem.source_plant_id = this.sourcePlant.id;
            }
        },

        watch: {
            strain_id: function() {
                this.newItem.strain_id = this.strain_id;
                // Set default plant_batch_name to format - "StrainName Date"
                var strain = this.schema.form.strain_id.values.find(s => s.id === this.newItem.strain_id);
                this.newItem.plant_batch_name = strain.name + " " + this.newItem.actual_date.toLocaleDateString();
            }
        },

        methods: {

            submit(e) {
                this.$validator.validateAll().then((result) => {
                    if (result) {

                        this.$swal.fire({
                        title: 'Are you sure?',
                        text: 'This will create '+this.newItem.plant_count+' plantings for '+ this.newItem.plant_batch_name,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plants/createPlantings',this.newItem).then(response =>{
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

            setSourcePlant(id) {
                let itemid = id.srcElement ? id.srcElement.value : id;
                this.isLoading = true;
                axios.get('/api/v1/admin/grow/plants/'+itemid).then(response =>  {
                    this.sourcePlant = response.data;
                    this.newItem.source_plant_id = this.sourcePlant.id;
                    this.strain_id = this.sourcePlant.strain_id;
                    this.isLoading = false;
                    this.batch_ids = [this.sourcePlant.id];
                    this.getBatchData();
                }).catch(error => {
                    console.log(error);
                    this.isLoading = false;
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                    this.$emit('refresh',{},'log');                          // will close this modal
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
                if(this.sourcePlant){
                    return this.sourcePlant.location.settings.is_medical;
                }
                return true;
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
