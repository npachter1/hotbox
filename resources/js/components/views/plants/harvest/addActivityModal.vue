<template>
    <div v-if="item && schema" class="col-12">

        <h3 v-if="item.id" class="mb-0">{{ item.name }}</h3>

                <div class="col-6">
                    <label for="item-start_date">Start Date</label>
                    <div class="form-group">
                        <datepicker id="item-start_date"
                                    name="activity_date"
                                    v-model="newItem.start_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>

                <div class="col-6">
                    <label for="item-end_date">End Date</label>
                    <div class="form-group">
                        <datepicker id="item-end_date"
                                    name="activity_date"
                                    v-model="newItem.end_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>


                <div class="col-12">
                    <label for="item-material">Activity</label>
                    <div class="form-group">
                        <select id="item-material"
                                class="form-control"
                                name="material_id"
                                v-model="newItem.activity_id"
                                v-validate="'required'">

                            <option value="">Select an Activity</option>
                            <option v-for="option in activities" v-bind:value="option.id">
                                {{ option.description }}
                            </option>
                        </select>
                        <span v-show="errors.has('activity_id')" class="form-text text-muted text-danger">{{ errors.first('activity_id') }}</span>
                    </div>
                </div>
                <div class="col-6">
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
                <div class="col-6">
                    <label for="item-unit-of-measurement">Hour/Min</label>
                    <div class="form-group">
                        <select id="item-unit-of-measurement"
                                class="form-control"
                                name="unit_of_measurement"
                                v-model="newItem.unit_of_measurement"
                                v-validate="'required'">
                            <option value="" selected>Select a Unit Of Measure</option>
                            <option value="H" selected>Hours</option>
                            <option value="Min">Minutes</option>
                        </select>
                    </div>
                </div>

            </div>


    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/Harvest';
    
    
    export default {

        props: {
            id: {
                type: Number,
                default: null
            },            
            model: {
                type: String,
                default: 'Harvest'
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
                start_date: new Date(),
                end_date: new Date(),
                activity_id: null,
                unit_of_measurement: null,
                quantity: null
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