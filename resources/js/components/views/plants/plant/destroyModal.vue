<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="destroyform" @submit.prevent="submit">
        <fieldset>
        <h3 class="mb-0">Destroy {{ ids.length }} {{ 'Plant'| pluralize(ids.length)}}</h3>

       <div class="form-row">
          <div class="col-12">
            <label>Reason Note</label>
            <div class="form-group">
                <input type="text"
                        id="item-reason_note"
                        class="form-control"
                        v-model="newItem.reason_note">
            </div>
        </div>

        <div class="col-6">
            <label for="item-destroy_date">Date</label>
            <div class="form-group">
                <datepicker id="item-destroy_date"
                            name="destroy_date"
                            :format="'MM/dd/yyyy'" 
                            v-model="newItem.destroy_date"
                            :bootstrap-styling="true"
                            input-class="form-datepicker"
                            v-validate="'required'"></datepicker>
            </div>
        </div>

       </div>
        <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Destroy Plantings</button>
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
                reason_note: null,
                destroy_date: new Date()
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
                        text: 'This will destroy '+this.ids.length+' Plant(s)',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plants/destroy',this.newItem).then(response =>{
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