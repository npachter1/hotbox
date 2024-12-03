<template>
    <div v-if="item && schema" class="col-12">
        <form class="modal-form" id="createpackages" @submit.prevent="submit">
        <fieldset>
            <h3 class="mb-4">Create Packages</h3>

            <div class="form-row" v-if="item">
                <div class="col-3">
                    <label>Source Package</label>
                    <div class="form-group">
                        {{ item.label }}
                    </div>
                </div>
                <div class="col-3">
                    <label>Product</label>
                    <div class="form-group">
                        {{ item.item.name }}
                    </div>
                </div>
                <div class="col-3">
                    <label>Total Weight</label>
                    <div class="form-group">
                        {{ item.quantity }} {{ item.unit_of_measure }}
                    </div>
                </div>
                <div class="col-3">
                    <label>Remaining Weight</label>
                    <div class="form-group" style="color:red">
                        {{ remainingWeight() }} {{ newItem.package_uom ? newItem.package_uom : '' }}
                    </div>
                </div>
            </div>

            <div class="form-row">

                <div class="col-6">
                    <label>Date</label>
                    <div class="form-group">
                        <datepicker id="item-date"
                                    name="date"
                                    :format="'MM/dd/yyyy'" 
                                    v-model="newItem.package_date"
                                    :bootstrap-styling="true"
                                    input-class="form-datepicker"
                                    v-validate="'required'"></datepicker>
                    </div>
                </div>
                <div class="col-6">
                    <label>Unit of Measure</label>
                    <div class="form-group">
                        <select name="package_uom"
                                v-model="newItem.package_uom"
                                class="form-control">
                            <option value="Milligrams">Milligrams</option>
                            <option value="Grams">Grams</option>
                            <option value="Ounces">Ounces</option>
                            <option value="Kilograms">Kilograms</option>
                            <option value="Pounds">Pounds</option>
                        </select>
                    </div>
                </div>

                <div class="col-6" v-if="isMedical">
                    <label for="item-patient_license_number">Patient License Number</label>
                    <div class="form-group">
                        <input id="item-patient_license_number"
                                    aria-describedby="addon-right addon-left"
                                    name="patient_license_number"
                                    ref="patient_license_numberInput"
                                    v-model="newItem.patient_license_number"
                                    v-validate=""
                                    class="form-control"
                                    :class="{'input': true, 'text-danger': errors.has('patient_license_number') }"
                                    type="text"
                                    placeholder="">
                        <span v-show="errors.has('patient_license_number')" class="form-text text-muted text-danger">{{ errors.first('patient_license_number') }}</span>
                    </div>
                </div>

                <div class="col-6">
                    <label>Requires Remediation</label>
                    <div class="form-group">
                        <select name="is_production_batch"
                                v-model="newItem.product_requires_remediation"
                                class="form-control">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </div>
                </div>

                <div class="col-12">
                    <hr>
                </div>
                <template class="col-12" v-for="n in newItem.item_sections_count">
                <div class="col-4">
                    <label for="item-item_id">Product</label>
                    <div class="form-group" v-if="newItem.item_sections[n-1]">
                    <select class="form-control" v-model="newItem.item_sections[n-1].item_id" v-validate="'required'">
                        <option v-for="item in schema.form.product_id.values" :value="item.id">{{ item.name }}</option>
                    </select>
                    </div>
                </div>

                <div class="col-2">
                    <label>Weight / Product</label>
                    <div class="form-group" v-if="newItem.item_sections[n-1]">
                        <input type="text"
                            name="product_weight"
                            @change="setProductWeight($event, (n-1))"
                            v-model="newItem.item_sections[n-1].product_weight"
                            class="form-control">
                    </div>
                </div>
                <div class="col-2">
                    <label>Products / Package</label>
                    <div class="form-group" v-if="newItem.item_sections[n-1]">
                        <input type="text"
                            name="product_count"
                            @change="setProductCount($event, (n-1))"
                            v-model="newItem.item_sections[n-1].product_count"
                            class="form-control">
                    </div>
                </div>
                <div class="col-2">
                    <label>Weight / Package</label>
                    <div class="form-group" style="margin-bottom:0" v-if="newItem.item_sections[n-1]">
                        <input type="text"
                            name="package_weight"
                            @change="setWeight($event,(n-1))"
                            v-model="newItem.item_sections[n-1].package_weights"
                            class="form-control">
                    </div>
                    <a @click="getMaxWeight(n-1)" style="color:red;font-size:smaller;margin-left:5px;cursor:pointer">(Max)</a>
                </div>
                <div class="col-2">
                    <label>Package Count</label>
                    <div class="form-group" style="margin-bottom:0" v-if="newItem.item_sections[n-1]">
                        <input type="text"
                            name="package_count"
                            @change="setCount($event,(n-1))"
                            v-model="newItem.item_sections[n-1].package_count"
                            class="form-control">
                    </div>
                    <a @click="getMaxPackageCount(n-1)" style="color:red;font-size:smaller;margin-left:5px;cursor:pointer">(Max)</a>
                </div>

                <div class="col-6" v-if="newItem.item_sections[n-1]">
                    <label>Package Tag # (s)</label>
                    <div class="form-group" v-for="i in (newItem.item_sections[n-1].package_count)*1">
                    <input type="text"
                                class="form-control"
                                @change="setTag($event,n-1,i-1)">
                    </div>
                </div>
            <div class="col-12" style="text-align:right">
                <br>
                    <a @click="removeSection(n-1)" style="cursor:pointer;margin-right:5px"><i class="hotbox-icon hotbox-icon-trash-round show-red"></i></a>
                <hr>
            </div>

            </template>
            <br>
            <div class="col-12" style="text-align:right">
                <a @click="initializeSection(newItem.item_sections_count)" style="cursor:pointer;margin-right:5px"><i class="hotbox-icon hotbox-icon-c-add"></i></a>
            </div>
            <br><br>
            </div>
            <div class="col-12 clearfix mt-2 text-center">
                    <button class="btn btn-info" type="submit">Create Packages</button>
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
            id: {
                type: Number,
                default: null
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
            isMedical: false,
            newItem: {
                package_id: this.id,
                package_uom: 'Grams',
                package_date: new Date(),
                patient_license_number: null,
                product_requires_remediation: 0,
                item_sections: [],
                item_sections_count: 1  // number of package sections
            }
          };
        },

        watch: {
            package: function() {
                this.initializeSection(0);
            }
        },

        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // this is a sub resource - it loads its own schema upon modal load
            this.isLoading = true;
            this.initializeSection(0);
            if(this.id){
                    axios.get('/api/v1/'+this.schema.meta.resource+'/'+this.id).then(response =>  {
                    this.item = response.data;
                    this.isLoading = false;
                    axios.get('/api/v1/admin/auth/location/ismedical/'+this.item.location_id).then(response2 =>{
                        this.isMedical = response2.data;
                    });
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
                        text: 'This will create packages from package '+this.item.label,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        }).then((result) => {
                        if(result.value){
                            this.isProcessing = true;
                                axios.post('/api/v1/'+this.schema.meta.resource+'/createPackages',this.newItem).then(response =>{
                                this.$announcer({status:200,data:{message:'Success'}});
                                if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                                this.$router.push({name:this.model.toLowerCase()});
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

        // isMedical() {
        //     return this.item.location.settings.is_medical;
        // },
        // add new Item section to form
        initializeSection(n) {
            this.newItem.item_sections[n] = {};
            Vue.set(this.newItem.item_sections[n], 'package_count', 1);
            Vue.set(this.newItem.item_sections[n], 'package_weights', null);
            Vue.set(this.newItem.item_sections[n], 'product_weight', null);
            Vue.set(this.newItem.item_sections[n], 'product_count', null);
            Vue.set(this.newItem.item_sections[n], 'item_id', null);
            Vue.set(this.newItem.item_sections[n], 'labels', []);
            this.newItem.item_sections_count = n+1;
        },
        // set the Package Count for an Item section
        setCount(count, n) {
            Vue.set(this.newItem.item_sections[n], 'package_count', count.srcElement.value);
            this.$forceUpdate();
        },
        // set the Weight per Package for an Item section
        setWeight(weight, n) {
            Vue.set(this.newItem.item_sections[n], 'package_weights', weight.srcElement.value);
            var product_count = this.newItem.item_sections[n].product_count;
            var product_weight = this.newItem.item_sections[n].product_weight;
            var package_weights = this.newItem.item_sections[n].package_weights;
            if ((product_count > 0) && (product_count * product_weight) !== weight.srcElement.value) {
                product_weight = weight.srcElement.value / product_count;
                Vue.set(this.newItem.item_sections[n], 'product_weight', product_weight);
            }
            else if (product_weight && !product_count) {
                Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(package_weights/product_weight));
                Vue.set(this.newItem.item_sections[n], 'package_weights', product_weight * Math.floor(package_weights/product_weight));
            }
            this.$forceUpdate();
        },
        // set the Products per Package for an Item section
        setProductCount(count, n) {
            Vue.set(this.newItem.item_sections[n], 'product_count', count.srcElement.value);
            var product_count = this.newItem.item_sections[n].product_count;
            var product_weight = this.newItem.item_sections[n].product_weight;
            var package_weights = this.newItem.item_sections[n].package_weights;
            if (product_count && product_weight)
                Vue.set(this.newItem.item_sections[n], 'package_weights', product_count * product_weight);
            else if (product_count && package_weights)
                Vue.set(this.newItem.item_sections[n], 'product_weight', package_weights / product_count);
            this.$forceUpdate();
        },
        // set the Weight per Product for an Item section
        setProductWeight(weight, n) {
            Vue.set(this.newItem.item_sections[n], 'product_weight', weight.srcElement.value);
            var product_count = this.newItem.item_sections[n].product_count;
            var product_weight = this.newItem.item_sections[n].product_weight;
            var package_weights = this.newItem.item_sections[n].package_weights;
            if (product_count && product_weight)
                Vue.set(this.newItem.item_sections[n], 'package_weights', product_count * product_weight);
            else if (product_weight && package_weights) {
                Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(package_weights/product_weight));
                Vue.set(this.newItem.item_sections[n], 'package_weights', product_weight * Math.floor(package_weights/product_weight));
            }
            this.$forceUpdate();
        },
        remainingWeight() {
            // todo: convert $totalWeight to package_uom
            var totalWeight = this.item.quantity;
            var usedWeight = 0;
            this.newItem.item_sections.forEach((item) => {
                usedWeight += (item.package_weights * item.package_count);
            });
            return totalWeight - usedWeight;
        },
        // calculate maximum Weight per Package based on Package Count and remaining weight
        getMaxWeight(n) {
            var product_count = this.newItem.item_sections[n].product_count;
            var product_weight = this.newItem.item_sections[n].product_weight;
            var package_count = this.newItem.item_sections[n].package_count;
            var package_weights = this.newItem.item_sections[n].package_weights;
            if (package_count > 0) {
                var remainingWeight = this.remainingWeight() + (package_weights * package_count);
                var maxWeight = remainingWeight / this.newItem.item_sections[n].package_count;
                Vue.set(this.newItem.item_sections[n], 'package_weights', maxWeight);
                if (product_count && !product_weight)
                    Vue.set(this.newItem.item_sections[n], 'product_weight', maxWeight / product_count);
                else if (product_weight && !product_count) {
                    Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(maxWeight / product_weight));
                    Vue.set(this.newItem.item_sections[n], 'package_weights', Math.floor(maxWeight / product_weight) * product_weight);
                }
                else if (product_weight && product_count && (product_weight * product_count !== maxWeight)) {
                    Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(maxWeight / product_weight));
                    Vue.set(this.newItem.item_sections[n], 'package_weights', Math.floor(maxWeight / product_weight) * product_weight);
                }
            }
            this.$forceUpdate();
        },
        // calculate maximum Package Count based on Weight per Package and remaining weight
        getMaxPackageCount(n) {
            var package_count = this.newItem.item_sections[n].package_count;
            var package_weights = this.newItem.item_sections[n].package_weights;
            var remainingWeight = this.remainingWeight() + (package_weights * package_count);
            var maxCount = Math.floor(remainingWeight / package_weights);
            Vue.set(this.newItem.item_sections[n], 'package_count', maxCount);
            this.$forceUpdate();
        },
        setTag(tag, n, i) {
            Vue.set(this.newItem.item_sections[n].labels, i, tag.srcElement.value);
        },
        removeSection(n) {
            this.newItem.item_sections.splice(n, 1);
            this.newItem.item_sections_count -= 1;
            this.$forceUpdate();
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