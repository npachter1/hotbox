<template>
            <div class="form-row col-12" v-if="section_data">
                <hr>
                <div class="col-6">
                    <label>Plant Batch Name</label>
                    <div class="form-group" >
                        <input type="text"
                                name="plant_batch_name"
                                :data-vv-as="'plant batch name'"
                                class="form-control"
                                v-model="section_data.plant_batch_name"
                                v-validate="'required'">
                        <span v-show="errors.has('plant_batch_name')" class="form-text text-muted text-danger">{{ errors.first('plant_batch_name') }}</span>
                    </div>
                </div>

                <div class="col-6">
                    <label for="plant_batch_type">Plant Batch Type</label>
                    <div class="form-group" >
                        <select aria-describedby="addon-right addon-left"
                                name="plant_batch_type"
                                v-model="section_data.plant_batch_type"
                                v-on:input="updateValue($event.target.name,$event.target.value)"
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
                    <div class="form-group" >
                        <input type="number"
                                min="1"
                                name="plant_count"
                                :data-vv-as="'plant count'"
                                class="form-control"
                                v-model="section_data.plant_count"
                                v-on:input="updateValue($event.target.name,$event.target.value)"
                                v-validate="'required|decimal:0|notGreaterThanTotal'">
                        <span v-show="errors.has('plant_count')" class="form-text text-muted text-danger">{{ errors.first('plant_count') }}</span>
                    </div>
                </div>
                <div class="col-6">
                     <label for="room_id">Plant Batch Room</label>
                    <div class="form-group" >
                        <select class="form-control" name="room_id" v-model="section_data.room_id" v-on:change="updateValue($event.target.name,$event.target.value)">
                        <option v-for="room in schema.form.room_id.values" :value="room.id">{{ room.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                     <label for="strain">Strain</label>
                    <div class="form-group" >
                        <select class="form-control" name="strain" v-model="section_data.strain_id"  v-on:change="updateValue($event.target.name,$event.target.value)" v-validate="'required'">
                        <option v-for="strain in schema.form.strain_id.values" :value="strain.id">{{ strain.name }}</option>
                        </select>
                        <span v-show="errors.has('strain')" class="form-text text-muted text-danger">{{ errors.first('strain') }}</span>
                    </div>
                </div>
                <div class="col-6" v-if="isMedical()">
                    <label for="patient_license_number">Patient License Number</label>
                    <div class="form-group" >
                        <input aria-describedby="addon-right addon-left"
                                    name="patient_license_number"
                                    ref="patient_license_numberInput"
                                    v-model="section_data.patient_license_number"
                                    v-on:input="updateValue($event.target.name,$event.target.value)"
                                    v-validate=""
                                    class="form-control"
                                    :class="{'input': true}"
                                    type="text"
                                    placeholder="">
                        <span v-show="errors.has('patient_license_number')" class="form-text text-muted text-danger">{{ errors.first('patient_license_number') }}</span>
                    </div>
                </div>

                <div class="col-6">
                    <label for="actual_date">Date</label>
                    <div class="form-group" >
                        <datepicker name="actual_date"
                                    :format="'MM/dd/yyyy'"
                                    v-model="section_data.actual_date"
                                    v-on:input="updateValue($event.target.name,$event.target.value)"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>
                <div class="col-6">
                     <label>Label</label>
                    <div class="form-group" >
                        <input type="text"
                                :name="'plant_batch_label'+section_data.id"
                                :data-vv-as="'label'"
                                class="form-control"
                                v-model="section_data.plant_batch_label"
                                v-on:input="updateValue($event.target.name,$event.target.value)"
                                v-validate="'required'">
                        <span v-show="errors.has('plant_batch_label'+section_data.id)" class="form-text text-muted text-danger">{{ errors.first('plant_batch_label'+section_data.id) }}</span>
                    </div>
                </div>

                <div class="col-12" style="text-align:right">
                    <br>
                        <a @click="removeSection(section_data.id)" style="cursor:pointer;margin-right:5px"><i class="hotbox-icon hotbox-icon-trash-round show-red"></i></a>
                    <hr>
                </div>
            </div>
</template>

<script>

    import Item from '../../../../models/PlantBatches';
    import _ from 'lodash';
    import { extend } from 'vee-validate';

    export default {

        props: {
            item_section: {
                type: Object,
                default: null
            },
            remainingCount: {
                type: Number,
                default: 0
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
                section_data: this.item_section
            };
        },


        mounted() {

        },

        watch: {
            item_section: {
               handler: function(data) {
                   this.section_data = this.item_section;
                   this.$validator.validate('plant_count', this.section_data.plant_count);
                },
                deep: true
            },
            remainingCount: function () {
                this.$nextTick(function () {
                    this.$validator.validate('plant_count', this.section_data.plant_count);
                })
            }

        },

        inject: ['$validator'],

        methods: {
                removeSection(id){
                    this.$emit('removeSection',id);
                },
                isMedical() {
                    if(this.sourceBatch){
                        return this.sourceBatch.location.settings.is_medical;
                    }
                    return true;
                },
                updateValue: function (name, value) {
                    this.$emit('input', name, value, this.item_section.id);
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
