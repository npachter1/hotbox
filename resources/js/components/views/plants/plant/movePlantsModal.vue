<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="moveplantsform" @submit.prevent="submit">
        <fieldset>
        <h3 class="mb-0">Move {{ ids.length }} {{ 'Plant'| pluralize(ids.length)}}</h3>

       <div class="form-row">
        <div class="col-6">
          <label for="item-room_id">Room</label>
          <div class="form-group">
            <select class="form-control" name="room" v-model="newItem.new_room" v-validate="'required'">
              <option v-for="room in schema.form.room_id.values" :value="room.id">{{ room.name }}</option>
            </select>
            <span v-show="errors.has('room')" class="form-text text-muted text-danger">{{ errors.first('room') }}</span> 
          </div>
        </div>

        <div class="col-6">
            <label for="item-moved_at">Move Date</label>
            <div class="form-group">
                <datepicker id="item-moved_at"
                            name="moved_at"
                            :format="'MM/dd/yyyy'" 
                            v-model="newItem.moved_at"
                            :bootstrap-styling="true"
                            input-class="form-datepicker"
                            v-validate="'required'"></datepicker>
            </div>
        </div>

       </div>
        <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Move Plants</button>
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
                room_id: null,
                moved_at: new Date()
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
                        text: 'This will move '+this.ids.length+' plantings',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plants/movePlants',this.newItem).then(response =>{
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