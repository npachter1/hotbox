<template>
    <div v-if="item && schema" class="col-12">
        <form class="modal-form" id="changeitem" @submit.prevent="submit">
        <fieldset>
        <h3 v-if="item.id" class="mb-0">{{ package.label }}</h3>


        Current Product: <strong>{{ package.item.name }}</strong>
        <div class="form-row">
            <div class="col-6">
                <label for="item-product_id">Product</label>
                <div class="form-group">
                    <select class="form-control" name="item" v-model="newItem.product_id" v-validate="'required'">
                        <option v-for="item in schema.form.product_id.values" :value="item.id">{{ item.name }}</option>
                    </select>
                    <span v-show="errors.has('item')" class="form-text text-muted text-danger">{{ errors.first('item') }}</span>                    
                </div>
            </div>
        </div>
        <div class="col-12 clearfix mt-2 text-center">
                <button class="btn btn-info" type="submit">Change Product</button>
        </div>
        </fieldset>
        </form>

    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/Package';
    
    
    export default {

        props: {
            ids: {
                type: Array,
                default: () => {}
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
                package_id: null,
                product_id: null
            }
          };
        },

        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // this is a sub resource - it loads its own schema upon modal load
            this.isLoading = true;
            if(this.ids.length > 0){
                Item.find(this.ids[0]).then(response => {
                    this.item = new Item(response).withDefaults(this.schema,false);
                    this.newItem.package_id = this.item.id;
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
                        text: 'This will update the package '+this.item.label,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/'+this.schema.meta.resource+'/changeItem',this.newItem).then(response =>{
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

    },
    
    computed: {
        schema() {
            return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
        }
    }
  };
</script>

<style>

</style>