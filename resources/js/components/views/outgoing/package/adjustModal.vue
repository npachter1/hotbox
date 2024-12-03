<template>
    <div v-if="item && schema" class="col-12">
        <form class="modal-form" id="adjustpackage" @submit.prevent="submit">
        <fieldset>
        Package: <strong>{{ package.label }}</strong>
            <div class="form-row">
                <div class="col-6">
                    <label>Quantity</label>
                    <div class="form-group">
                        <input type="text"
                                class="form-control"
                                v-model="newItem.quantity">
                    </div>
                </div>
                <div class="col-6">
                <label for="item-unit_of_measure">Unit of Measure</label>
                <div class="form-group">
                    <select id="item-unit_of_measure"
                           aria-describedby="addon-right addon-left"
                           name="unit_of_measure"
                           v-model="newItem.unit_of_measure"
                           v-validate=""
                           class="form-control"
                           :class="{'input': true }">
                        <option value="Milligrams">Milligrams</option>
                        <option value="Grams">Grams</option>
                        <option value="Ounces">Ounces</option>
                        <option value="Kilograms">Kilograms</option>
                        <option value="Pounds">Pounds</option>
                        <option value="Each">Each</option>
                    </select>
                  </div>
                </div>
                <div class="col-6">
                    <label>Adjustment Date</label>
                    <div class="form-group">
                        <datepicker id="item-adjustment_date"
                                    name="adjustment_date"
                                    :format="'MM/dd/yyyy'" 
                                    v-model="newItem.adjustment_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>

                <div class="col-6">
                <label for="item-adjustment_reason">Adjustment Reason</label>
                <div class="form-group">
                    <select id="item-adjustment_reason"
                           aria-describedby="addon-right addon-left"
                           name="adjustment_reason"
                           v-model="newItem.adjustment_reason"
                           v-validate="'required'"
                           class="form-control"
                           :class="{'input': true }"
                            @change="updateNote()">
                        <option value="">Select a Reason</option>
                        <option v-for="option in adjustment_reasons" v-bind:value="option.Name">
                            {{ option.Name }}
                        </option>
                        <option v-if="!adjustment_reasons">Non-Metrc Adjustment</option>
                    </select>
                    <span v-show="errors.has('adjustment_reason')" class="form-text text-muted text-danger">{{ errors.first('adjustment_reason') }}</span>
                  </div>
                </div>

                <div class="col-12">
                    <label>Reason Note</label>
                    <div class="form-group">
                        <input type="text"
                                id="item-reason_note"
                                class="form-control"
                                v-model="newItem.reason_note">
                    </div>
                </div>

            </div>
        <div class="col-12 clearfix mt-2 text-center">
                <button class="btn btn-info" type="submit">Adjust Package</button>
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
            },
            adjustment_reasons: {
                type: Array,
                default: null
            }
        },

        data() {
          return {
            item:null,
            isLoading: false,
            isDownloading: false,
            newItem: {
                package_id: null,
                label: null,
                quantity: null,
                unit_of_measure: 'Grams',
                adjustment_reason: null,
                adjustment_date: new Date(),
                reason_note: null
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
                    this.newItem.label = this.item.label;
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
                        text: 'This will adjust the package '+this.item.label,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/'+this.schema.meta.resource+'/adjust',this.newItem).then(response =>{
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
.vdp-datepicker__calendar {
    left: 0px;
}
</style>