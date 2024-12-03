<template>
    <div v-if="item && schema" class="col-12">
        <form class="modal-form" id="destroyplantsform" @submit.prevent="submit">
        <fieldset>
            <h3 v-if="item.id" class="mb-0">{{ item.name }}</h3>

                <div class="form-row">
                    <div class="col-6">
                        <label for="item-destroy_count">Destroy Count</label>
                        <div class="form-group">
                            <input id="item-destroy_count"
                                aria-describedby="addon-right addon-left"
                                name="destroy_count"
                                :data-vv-as="'destroy count'"
                                v-model="newItem.destroy_count"
                                v-validate="'required|decimal:0'"
                                class="form-control"
                                :class="{'input': true }"
                                type="number"
                                placeholder="">
                        </div>
                        <span v-show="errors.has('destroy_count')" class="form-text text-muted val-danger-text">{{ errors.first('destroy_count') }}</span>
                    </div>

                    <div class="col-6">
                        <label for="item-destroy_date">Destroy Date</label>
                        <div class="form-group">
                            <datepicker id="item-destroy_date"
                                        name="destroy_date"
                                        v-model="newItem.destroy_date"
                                        :bootstrap-styling="true"
                                        :format="'MM/dd/yyyy'" 
                                        input-class="form-datepicker"
                                        v-validate="'required'"></datepicker>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="item-destroy_reason">Destroy Reason</label>
                        <div class="form-group">
                            <textarea id="item-destroy_reason"
                                        name="destroy_reason"
                                        :data-vv-as="'destroy reason'"
                                        v-model="newItem.destroy_reason"
                                        v-validate="'required'"
                                        class="form-control"
                                        style="border-radius:15px"></textarea>
                        </div>
                        <span v-show="errors.has('destroy_reason')" class="form-text text-muted val-danger-text">{{ errors.first('destroy_reason') }}</span>
                    </div>

                </div>
                <div class="col-12 clearfix mt-3 mb-4 text-center">
                    <button class="btn btn-info" type="submit">
                        Destroy
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
              destroy_date: new Date(),
              destroy_count: null,
              destroy_reason: null
            },
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
                        text: 'This will destroy '+this.newItem.destroy_count+' plantings for '+ this.item.name,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Destroy Plantings',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plantbatches/'+this.item.id+'/destroy',this.newItem).then(response =>{
                                this.$announcer({status:200,data:{message:'Plantings Destroyed'}});
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
            cancelEdit() {
                for (var property in this.newItem) {
                    if (this.newItem.hasOwnProperty(property)) {
                        this.newItem[property] = null;
                    }
                }
                this.$emit('refresh');
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