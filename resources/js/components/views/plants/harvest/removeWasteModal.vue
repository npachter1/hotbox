<template>
    <div v-if="schema" class="col-12">
        <form class="modal-form" id="removewasteform" @submit.prevent="submit">
        <fieldset>
        <h5 class="mb-0">Remove Waste from {{ ids.length }} {{ 'Harvest'| pluralize(ids.length)}}</h5><br>

            <div class="form-row">
                <div class="col-6">
                    <label>Date</label>
                    <div class="form-group">
                        <datepicker id="item-date"
                                    name="date"
                                    :format="'MM/dd/yyyy'" 
                                    v-model="newItem.remove_waste_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>
                <div class="col-6">
                    <label>Unit of Measure</label>
                    <div class="form-group">
                        <select name="waste_uom"
                                v-model="newItem.remove_waste_uom"
                                class="form-control">
                            <option value='Grams'>g</option>
                            <option value='Ounces'>oz</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <table class="table">
                        <thead>
                            <th>Harvest Name</th>
                            <th style="padding-left:20px">Waste Weight</th>
                            <th style="padding-left:20px">Waste Type</th>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in sources" :key="ind">
                            <td style="padding-left:0px">{{ item.name }}
                            </td>
                            <td style="padding:20px">
                                <input type="text"
                                       name="waste_weight"
                                       :id="'waste_weight'+ind"
                                        @change="setWeight($event,item.id)"
                                       style="width:75px"
                                       class="form-control"></td>
                            <td style="padding:20px">
                                <input type="text"
                                       name="waste_type"
                                       :id="'waste_type'+ind"
                                        @change="setWasteType($event,item.id)"
                                       style="width:150px"
                                       class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <div v-if="!sources">
                        <span>No Harvests Selected</span>
                    </div>
            </div>
            <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Remove Waste</button>
            </div>
        </fieldset>
        </form>
    </div>
    <div v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/Harvest';
    
    
    export default {

        props: {
            ids: {
                type: Array,
                default: () => {}
            },            
            model: {
                type: String,
                default: 'Harvest'
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
                harvest_ids: this.ids,
                waste_weights: null,
                waste_types: null,
                remove_waste_date: new Date(),
                remove_waste_uom: 'Grams'
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
                        text: 'This will remove waste from '+this.ids.length+' harvests ',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/'+this.schema.meta.resource+'/removeWaste',this.newItem).then(response =>{
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
            },

            initializeWeights() {
                this.newItem.waste_weights = {};
                if (this.sources)
                    this.sources.forEach((item) => {
                        this.newItem.waste_weights[item.id] = 0;
                });
            },
            setWeight(weight, id) {
                if (!this.newItem.waste_weights)
                    this.initializeWeights();
                this.newItem.waste_weights[id] = weight.srcElement.value;
            },

            initializeWasteTypes() {
                this.newItem.waste_types = {};
                if (this.actionItems)
                    this.actionItems.forEach((item) => {
                        this.newItem.waste_types[item.id] = 0;
                    });
            },
            setWasteType(type, id) {
                if (!this.newItem.waste_types)
                    this.initializeWasteTypes();
                this.newItem.waste_types[id] = type.srcElement.value;
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