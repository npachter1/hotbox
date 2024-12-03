<template>
    <div v-if="item && schema" class="col-12">

    <form class="modal-form" id="moveplantsform" @submit.prevent="submit">
          <fieldset>
        <h3 v-if="item.id" class="mb-0">{{ item.name }}</h3>

        <div class="row">
          <div class="col-6">
            <label>Starting Tag #</label>
            <input class="form-control" type="text" name="tag" :data-vv-as="'starting tag'" v-model="newItem.starting_tag" v-validate="'required'" />
            <span v-show="errors.has('tag')" class="form-text text-muted val-danger-text">{{ errors.first('tag') }}</span>
          </div>
          <div class="col-6">
            <label>Plant Count</label>
            <input class="form-control" type="number" name="count" v-model="newItem.count" v-validate="'required|decimal:0'" />
            <span v-show="errors.has('count')" class="form-text text-muted val-danger-text">{{ errors.first('count') }}</span>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <label>Room</label>
            <select class="form-control" name="room" v-model="newItem.new_room" v-validate="'required'">
              <option v-for="room in schema.form.room_id.values" :value="room.id">{{ room.name }}</option>
            </select>
            <span v-show="errors.has('room')" class="form-text text-muted val-danger-text">{{ errors.first('room') }}</span>
          </div>
          <div class="col-6">
            <label>Growth Phase</label>
            <select class="form-control" name="growth_phase" :data-vv-as="'growth phase'" v-model="newItem.growth_phase" v-validate="'required'">
              <option v-for="growth_phase in schema.form.growth_phase.values" :value="growth_phase.name">{{ growth_phase.name }}</option>
            </select>
            <span v-show="errors.has('growth_phase')" class="form-text text-muted val-danger-text">{{ errors.first('growth_phase') }}</span>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <label>Patient License Number</label>
            <input class="form-control" type="text" v-model="newItem.patient_license_number"/>
          </div>
          <div class="col-6">
            <label>Start Date</label>
            <div class="form-group">
              <datepicker 
                :format="'MM/dd/yyyy'" 
                :typeable="true" 
                :bootstrap-styling="true"
                name="date" 
                input-class="form-datepicker"
                v-model="newItem.growth_date"
                v-validate="'required'"
              />
            </div>
                            <!-- :input-class="{'form-control':true,'showdateform':true,'input':true}" -->
            <span v-show="errors.has('date')" class="form-text text-muted val-danger-text">{{ errors.first('date') }}</span>
          </div>
        </div>
        <div class="col-12 clearfix mt-3 mb-4 text-center">
                                      <button class="btn btn-info" type="submit">
                    Move and Tag Plants
                </button>
        </div>
        </fieldset>
        </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
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
            item:null,
            isLoading: false,
            isDownloading: false,
            newItem: {
              starting_tag: null,
              count: 0,
              new_room: null,
              growth_phase: 'Vegetative',
              patient_license_number: null,
              growth_date: new Date()
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
      submit(e) {
                this.$validator.validateAll().then((result) => {
                    if (result) {

                        this.$swal.fire({
                        title: 'Are you sure?',
                        text: 'This will move and tag '+this.newItem.count+' plantings',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plantbatches/'+this.item.id+'/changeGrowthPhase',this.newItem).then(response =>{
                                this.$announcer({status:200,data:{message:'Moved Succesfully'}});
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