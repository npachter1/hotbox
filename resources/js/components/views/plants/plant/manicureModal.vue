<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="manicureform" @submit.prevent="submit">
        <fieldset>
        <h3 class="mb-0">Manicure {{ ids.length }} {{ 'Plant'| pluralize(ids.length)}}</h3>

       <div class="form-row">

        <div class="col-6">
          <label>Unit of Weight</label>
          <div class="form-group">
            <select aria-describedby="addon-right addon-left"
                   name="unit_of_weight"
                   v-model="newItem.unit_of_weight"
                   v-validate="'required'"
                   class="form-control"
                   :class="{'input': true }">
                <option value="Milligrams">Milligrams</option>
                <option value="Grams">Grams</option>
                <option value="Ounces">Ounces</option>
                <option value="Kilograms">Kilograms</option>
                <option value="Pounds">Pounds</option>
            </select>
          </div>
        </div>

        <div class="col-6">
          <label>Drying Room</label>
          <div class="form-group">
            <select class="form-control" name="drying_room" v-model="newItem.drying_room_id" v-validate="'required'">
              <option v-for="room in schema.form.room_id.values" :value="room.id">{{ room.name }}</option>
            </select>
            <span v-show="errors.has('drying_room')" class="form-text text-muted text-danger">{{ errors.first('drying_room') }}</span> 
          </div>
        </div>

        <div class="col-6">
            <label>Harvest Name</label>
            <div class="form-group">
                 <input type="text"
                        name="harvest_name"
                        class="form-control"
                        v-model="newItem.harvest_name"
                        v-validate="'required'">
                 <span v-show="errors.has('harvest_name')" class="form-text text-muted text-danger">{{ errors.first('harvest_name') }}</span> 
            </div>
        </div>

        <div class="col-6">
            <label>Date</label>
            <div class="form-group">
                <datepicker name="actual_date"
                            :format="'MM/dd/yyyy'" 
                            v-model="newItem.actual_date"
                            :bootstrap-styling="true"
                            input-class="form-datepicker"
                            v-validate="'required'"></datepicker>
            </div>
        </div>

       </div>

       <div class="form-row">
            <table class="table">
                <thead>
                    <th>Plant</th>
                    <th>Weight</th>
                </thead>
                <tbody>
                    <tr v-for="(plant,ind) in sources">
                        <td>{{ plant.label }}</td>
                        <td><input type="number"
                                    name="weight"
                                    :id="'weight'+ind"
                                    @change="setWeight($event,plant.id)"
                                    style="width:100px"
                                    class="form-control"></td>
                    </tr>
                </tbody>
            </table>
       </div>

        <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Manicure Plants</button>
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
                plant_ids: this.ids,
                weights: null,
                unit_of_weight: 'Grams',
                drying_room_id: null,
                harvest_name: null,
                patient_license_number: null,
                actual_date: new Date()
            },
            batch_ids: this.ids
          };
        },

        mounted() {
            this.getBatchData();
            if (this.sources[0])
                this.newItem.harvest_name = this.sources[0].strain.name + " " + this.newItem.actual_date.toLocaleDateString();
        },

        methods: {
             submit(e) {
                this.$validator.validateAll().then((result) => {
                    if (result) {

                        this.$swal.fire({
                        title: 'Are you sure?',
                        text: 'This will manicure '+this.ids.length+' plants',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plants/manicure',this.newItem).then(response =>{
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

            initializeWeights() {
                this.newItem.weights = {};
                if (this.sources)
                    this.sources.forEach((item) => {
                        this.newItem.weights[item.id] = 0;
                });
            },
            setWeight(weight, id) {
                if (!this.newItem.weights)
                    this.initializeWeights();
                this.newItem.weights[id] = weight.srcElement.value;
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