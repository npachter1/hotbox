<template>
    <div v-if="item && schema" class="col-12">

        <h3 v-if="item.id" class="mb-0">{{ item.name }}</h3>

                <div class="col-12">
                    <label for="item-material_date">Date</label>
                    <div class="form-group">
                        <datepicker id="item-material_date"
                                    name="material_date"
                                    v-model="newItem.material_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>


                <div class="col-12">
                    <label for="item-material">Material</label>
                    <div class="form-group">
                        <select id="item-material"
                                class="form-control"
                                name="material_id"
                                v-model="newItem.material_id"
                                v-validate="'required'">

                            <option value="">Select a Material</option>
                            <option v-for="option in materials" v-bind:value="option.id">
                                {{ option.description }}
                            </option>
                        </select>
                        <span v-show="errors.has('material_id')" class="form-text text-muted text-danger">{{ errors.first('material_id') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <label for="item-uom">UoM</label>
                    <div class="form-group">
                        <input id="item-uom"
                               class="form-control"
                               type="text"
                               name="unit_of_measurement"
                               disabled="disabled"
                               v-model="unitOfMeasurement"
                               v-validate="'required'" />
                        <span v-show="errors.has('unit_of_measurement')" class="form-text text-muted text-danger">{{ errors.first('unit_of_measurement') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <label for="item-quantity">Quantity</label>
                    <div class="form-group">
                        <input id="item-quantity"
                               class="form-control"
                               type="number"
                               name="quantity"
                               v-model="newItem.quantity"
                               v-validate="'required'" />
                        <span v-show="errors.has('quantity')" class="form-text text-muted text-danger">{{ errors.first('quantity') }}</span>
                    </div>
                </div>

            </div>


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
                material_date: new Date(),
                material_id: null,
                unit_of_measurement_id: null,
                quantity: null
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