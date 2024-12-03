<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="changegrowthphaseform" @submit.prevent="submit">
        <fieldset>
        <h3 class="mb-0">Change Growth Phase of {{ ids.length }}  {{ 'Plant'| pluralize(ids.length)}}</h3>

       <div class="form-row">
        <div class="col-6">
          <label for="item-growth_phase">Growth Phase</label>
          <div class="form-group">
            <select id="item-growth_phase"
                    class="form-control"
                    name="growth_phase"
                    v-model="newItem.growth_phase"
                    v-validate="'required'">
              <option value="">Select a Growth Phase</option>
                <option value="Vegetative">Vegetative</option>
                <option value="Flowering">Flowering</option>
            </select>
            <span v-show="errors.has('growth_phase')" class="form-text text-muted text-danger">{{ errors.first('growth_phase') }}</span> 
          </div>
        </div>

        <div class="col-6">
          <label for="item-room_id">New Room</label>
          <div class="form-group">
            <select class="form-control" name="room" v-model="newItem.room_id" v-validate="'required'">
              <option v-for="room in schema.form.room_id.values" :value="room.id">{{ room.name }}</option>
            </select>
            <span v-show="errors.has('room')" class="form-text text-muted text-danger">{{ errors.first('room') }}</span> 
          </div>
        </div>

        <div class="col-6">
            <label for="item-growth_date">Date</label>
            <div class="form-group">
                <datepicker id="item-growth_date"
                            name="growth_date"
                            :format="'MM/dd/yyyy'" 
                            v-model="newItem.growth_date"
                            :bootstrap-styling="true"
                            input-class="form-datepicker"
                            v-validate="'required'"></datepicker>
            </div>
        </div>

       </div>
        <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Change Growth Phase</button>
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
            }
        },

        data() {
          return {
            batch:null,
            isLoading: false,
            isDownloading: false,
            newItem: {
                plant_ids: this.ids,
                growth_phase: 'Flowering',
                room_id: null,
                growth_date: new Date(),
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
                        text: 'This will changed the Growth Phase of '+this.ids.length+' plantings',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plants/changeGrowthPhase',this.newItem).then(response =>{
                                this.$announcer({status:200,data:{message:'Success'}});
                                if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'}); 
                                this.isProcessing = false;
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