<template>
    <div v-if="item && schema" class="col-12">

            Package: <strong>{{ package.label }}</strong><br>
            <div class="form-row">
                <div class="col-6">
                    <label>Package Adjustment Amount</label>
                    <div class="form-group">
                        <input type="text"
                                class="form-control"
                                v-model="newItem.package_adjustment_amount">
                    </div>
                </div>
                <div class="col-6">
                <label for="item-unit_of_measure">Unit of Measure</label>
                <div class="form-group">
                    <select id="item-unit_of_measure"
                           aria-describedby="addon-right addon-left"
                           name="unit_of_measure"
                           v-model="newItem.unit_of_measure"
                           v-validate=""
                           class="form-control"
                           :class="{'input': true }">
                        <option value="Milligrams">Milligrams</option>
                        <option value="Grams">Grams</option>
                        <option value="Ounces">Ounces</option>
                        <option value="Kilograms">Kilograms</option>
                        <option value="Pounds">Pounds</option>
                        <option value="Each">Each</option>
                    </select>
                  </div>
                </div>
                <div class="col-6">
                    <label>Plant Batch Name</label>
                    <div class="form-group">
                        <input type="text"
                                class="form-control"
                                v-model="newItem.plant_batch_name">
                    </div>
                </div>
                <div class="col-6">
                <label for="item-adjustment_reason">Plant Batch Type</label>
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
                    </select>
                  </div>
                </div>
                <div class="col-6">
                    <label>Plant Count</label>
                    <div class="form-group">
                        <input type="text"
                                class="form-control"
                                v-model="newItem.plant_count">
                    </div>
                </div>
                <div class="col-6">
                <label for="item-room_name">Room</label>
                <div class="form-group">
                    <select class="form-control" v-model="newItem.room_id" v-validate="'required'">
                        <option v-for="room in schema.form.room_id.values" :value="room.id">{{ room.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-6">
                <label for="item-strain_name">Strain</label>
                <div class="form-group">
                    <select class="form-control" v-model="newItem.strain_id" v-validate="'required'">
                        <option v-for="strain in schema.form.strain_id.values" :value="strain.id">{{ strain.name }}</option>
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
                    <label>Planted Date</label>
                    <div class="form-group">
                        <datepicker id="item-planted_date"
                                    name="planted_date"
                                    :format="'MM/dd/yyyy'" 
                                    v-model="newItem.planted_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>
                <div class="col-6">
                    <label>Unpackaged Date</label>
                    <div class="form-group">
                        <datepicker id="item-unpackaged_date"
                                    name="unpackaged_date"
                                    :format="'MM/dd/yyyy'" 
                                    v-model="newItem.unpackaged_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>

            </div>


    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/Package';
    
    
    export default {

        props: {
            id: {
                type: Number,
                default: null
            },
            package: {
                type: Object,
                default: null
            },            
            model: {
                type: String,
                default: 'Package'
            },
            module: {
                type: String,
                default: 'outgoing',
            }
        },

        data() {
          return {
            item:null,
            isLoading: false,
            isDownloading: false,
            newItem: {
                label: null,
                package_adjustment_amount: null,
                unit_of_measure: 'Grams',
                plant_batch_name: "",
                plant_batch_type: 'Clone',
                plant_count: null,
                room_name: null,
                strain_name: null,
                patient_license_number: null,
                planted_date: new Date(),
                unpackaged_date: new Date()
            }
          };
        },

        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // this is a sub resource - it loads its own schema upon modal load
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema,false);
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
        isMedical() {
            return this.package.location.settings.is_medical;
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