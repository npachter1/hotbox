<template>
    <div v-if="(schema && sourceBatch)" class="col-12">
        <form class="modal-form" id="splitbatchform" @submit.prevent="submit">
        <fieldset>
        <h3 v-if="sourceBatch" class="mb-0">Source Batch - {{ sourceBatch.name }}</h3>
 
        <div v-if="sourceBatch" class="form-row">
                <div class="col-4">
                    <label>Strain</label>
                    <div class="form-group">
                        {{ sourceBatch.strain.name }}
                    </div>
                </div>
                <div class="col-4">
                    <label>Total Plantings</label>
                    <div class="form-group">
                        {{ sourceBatch.count }} 
                    </div>
                </div>
                <div class="col-4">
                    <label>Remaining Plantings</label>
                    <div class="form-group" style="color:red">
                        {{ remainingCount }} 
                    </div>
                </div>
                <div class="col-6">
                    <label>Destroy Count</label>
                    <div class="form-group">
                        <input type="number"
                                min="0"
                                name="destroy_count"
                                :data-vv-as="'destroy count and plant count'"
                                id="item-destroy_count"
                                class="form-control"
                                v-model="newItem.destroyed_count"
                                @input="remainingPlantings"
                                v-validate="'required|decimal:0|notGreaterThanTotal'">
                        <span v-show="errors.has('destroy_count')" class="form-text text-muted text-danger">{{ errors.first('destroy_count') }}</span> 
                    </div>
                </div>

        <div class="col-12">
                    <hr>
        </div>
        <split-batch-section :item_section="item_section" :remainingCount="remainingCount" v-for="(item_section, index) in newItem.item_sections" :key="index" @removeSection="removeSection" @input="updateSection"></split-batch-section>
        <br>
        <div class="col-12" style="text-align:right">
            <a @click="initializeSection(newItem.item_sections_count)" style="cursor:pointer;margin-right:5px"><i class="hotbox-icon hotbox-icon-c-add"></i></a>
        </div>
        <br><br>
        </div>
        <div class="col-12 clearfix mt-2 text-center">
            <button class="btn btn-info" type="submit">Split Plant Batch</button>
        </div>
        </fieldset>
        </form>
    </div>
    <div v-else>
        <loading :display="(schema && sourceBatch) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/PlantBatches';
    import { extend } from 'vee-validate';
    import SplitBatchSection from './splitBatchSection';
    
    
    export default {

        components:{ 
            SplitBatchSection
        },

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
            isLoading: false,
            newItem: {
                source_batch_id: null,
                destroyed_count: 0,
                item_sections: [],
                item_sections_count: 1 
            },
            strain_id: null,
            sourceBatch: null,
            remainingCount: 0
          };
        },

        created() {
            this.$validator.extend(
            'notGreaterThanTotal',{
            getMessage: field =>  field + ' total exceeds the number of plantings in the source batch.',
            validate: (value) => {
                if (value === 0 || this.remainingCount >= 0 ) return true;
                return false;
            }
            });
        },

 async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // this is a sub resource - it loads its own schema upon modal load
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.sourceBatch = new Item(response).withDefaults(this.schema,false);
                    this.remainingCount = this.sourceBatch.count;
                    this.newItem.source_batch_id = this.sourceBatch.id;
                    this.strain_id = this.sourceBatch.strain_id;
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
       
        watch: {
            sourceBatch: function() {
                this.initializeSection(0);
            }
        },
        
        inject: ['$validator'],

        methods: {

            submit(e) {
                this.$validator.validateAll().then((result) => {
                    if (result) {

                        this.$swal.fire({
                        title: 'Are you sure?',
                        text: 'This will split '+ this.sourceBatch.name+' into '+ this.newItem.item_sections_count+' new Plant Batches',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/admin/grow/plantbatches/splitBatch',this.newItem).then(response =>{
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

            updateSection(name,value,n)
            {
                let index = this.newItem.item_sections.findIndex(x => x.id === n)
                Vue.set(this.newItem.item_sections[index],name,value);

                if(name === 'strain')
                {
                    var strain = this.schema.form.strain_id.values.find(s => s.id === Number(value));
                     Vue.set(this.newItem.item_sections[index],'plant_batch_name',strain.name + ' #' + (index+1) + ' ' + this.newItem.item_sections[index].actual_date.toLocaleDateString());
                }
                this.remainingPlantings();
                this.$validator.validate('destroy_count', this.newItem.destroyed_count);

            },
            
            initializeSection(n) {
                Vue.set(this.newItem.item_sections, n, {});
                Vue.set(this.newItem.item_sections[n], 'id', n);
                Vue.set(this.newItem.item_sections[n], 'plant_batch_type', this.sourceBatch.type);
                Vue.set(this.newItem.item_sections[n], 'patient_license_number', null);
                Vue.set(this.newItem.item_sections[n], 'plant_count', 0);
                Vue.set(this.newItem.item_sections[n], 'room_id', null);
                Vue.set(this.newItem.item_sections[n], 'strain_id', this.sourceBatch.strain_id);
                Vue.set(this.newItem.item_sections[n], 'actual_date', new Date(this.sourceBatch.planted_at));
                Vue.set(this.newItem.item_sections[n], 'source_plant_id', this.sourceBatch.source_plant_id);
                Vue.set(this.newItem.item_sections[n], 'plant_batch_label', null);
                Vue.set(this.newItem.item_sections[n], 'plant_batch_name', this.sourceBatch.strain.name + ' #' + (n+1) + ' ' + new Date().toLocaleDateString());
                this.newItem.item_sections_count = n+1;
            },
            
            isMedical() {
                if(this.sourceBatch){
                    return this.sourceBatch.location.settings.is_medical;
                }
                return true;
            },
            
            removeSection(n) {
                let index = this.newItem.item_sections.findIndex(x => x.id === n)
                this.newItem.item_sections.splice(index, 1);
                this.newItem.item_sections_count -= 1;
                let newId = 0;
                this.newItem.item_sections.forEach((item) => {
                    item.id = newId;
                    var strain = this.schema.form.strain_id.values.find(s => s.id === Number(item.strain_id));
                    item.plant_batch_name = strain.name + ' #' + (newId+1) + ' ' + item.actual_date.toLocaleDateString();
                    newId++;
                });
               
                this.remainingPlantings();
                this.$validator.validate('destroy_count', this.newItem.destroyed_count);
            },
            
            remainingPlantings() {
                let totalCount = Number(this.sourceBatch.count);
                let usedCount = Number(this.newItem.destroyed_count);
                this.newItem.item_sections.forEach((item) => {
                    usedCount += Number(item.plant_count);
                });
                this.remainingCount = totalCount - usedCount;
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