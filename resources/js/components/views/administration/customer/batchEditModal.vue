<template>
    <div v-if="schema && batch" class="col-12">
    <form class="modal-form">
        <fieldset>
    
            <div v-if="batch && schema" class="col-12">

            <div class="nav-tabs-header mb-4">
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="nav-link" :class="{'active':tab=='edit'}">
                        <a href="" class="" @click.prevent="tab='edit'">Batch Edit</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='archive'}">
                        <a href="" class="" @click.prevent="tab='archive'">Batch Archive</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='addtogroup'}">
                        <a href="" class="" @click.prevent="tab='addtogroup'">Add to Group</a>
                    </li>
                </ul>
            </div>
            
            <transition name="bo-slide">
                <div v-if="tab=='edit'">

                    <table role="table" class="table b-table">
                        <thead>
                            <tr>
                                <th valign="top">Customer</th>
                                <th v-for="(fsch,fkey) in batchFields">
                                    <span class="small">Override {{ fsch.title }}</span> 
                                    <i class="float-right" @click="batchSort(fkey)" :class="{'fas fa-sort show-opaque':batchSortBy!=fkey,'fas fa-sort-amount-down':(batchSortBy==fkey && batchSortDesc===false),'fas fa-sort-amount-up':(batchSortBy==fkey && batchSortDesc===true)}"></i><br>
                                    <form-boolean v-if="fsch.type=='boolean'" :declared="batchFields[fkey].override" :schema="fsch" :skipValidation="true" @input="(upd) => {batchFields[fkey].override = upd; batchOverride(fkey,batchFields[fkey].override); }" :hideLabel="true" />
                                    <form-text v-if="fsch.type=='text'" v-model="batchFields[fkey].override" :schema="fsch" :hideLabel="true" :skipValidation="true"  @input="batchOverride(fkey,batchFields[fkey].override)" />
                                    <form-simpleselect v-if="fsch.type=='select'" v-model="batchFields[fkey].override" :schema="fsch"  :hideLabel="true" :skipValidation="true" @input="batchOverride(fkey,batchFields[fkey].override)" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in batch">
                                <td>{{item.first_name}} {{ item.last_name}}<br><span class="small" v-if="item.address">{{ item.address.city }} {{ item.address.region }}</span></td>
                                <td v-for="(fsch,fkey) in batchFields">
                                    <form-boolean v-if="fsch.type=='boolean'" :declared="item[fkey]" :schema="fsch" @input="(upd) => {item[fkey] = upd}" :hideLabel="true" />
                                    <form-text v-if="fsch.type=='text'" v-model="item[fkey]" :schema="fsch" :hideLabel="true" />
                                    <form-simpleselect v-if="fsch.type=='select'" v-model="item[fkey]" :schema="fsch"  :hideLabel="true" />
                                    <form-multiselect v-if="fsch.type=='multiselect'" v-model="item[fkey]" :schema="fsch"  :hideLabel="true" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="col-12 clearfix mt-2 text-center">
                        <auto-save type="save" :state="batchState" @autoSave="saveBatchData('data')"></auto-save>
                         <a @click.default="getBatchData()" class="btn btn-md btn-light" :class="{'btn-light':batchState=='update','btn-warning':batchState!='update'}">Restore.</a>
                    </div>

                </div>
            </transition>

            <transition name="bo-slide">
                <div v-if="tab=='archive'">
                    
                    <div class="block-announce warning mt-4 mb-4">
                        <p class="title"><i class="hotbox-icon hotbox-icon-archive-drawer show-yellow"></i> Are you Sure?..</p>
                        <p>This will remove from any view or report, but would still be used if linked to anything.  Please confirm these are the addresses you would like to archive.</p>
                    </div>
                    
                    <table role="table" class="table b-table table-striped">
                        <thead>
                            <tr>
                                <th width="90%">Item</th>
                                <th width="10%">Confirm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in batch" :class="{'table-success':inSet(item.id,batch_ids)}">
                                <td width="90%">{{item.first_name}} {{ item.last_name}}<br><span class="small" v-if="item.address">{{ item.address.city }} {{ item.address.region }}</span></td>
                                <td width="10%" valign="top">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" :checked="inSet(item.id,batch_ids)" :disabled="false" @click="toggleBatchId(item.id,$event)"><span class="custom-control-indicator"></span> 
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="col-12 clearfix mt-2 text-center">
                        <auto-save type="save" :state="batchState" @autoSave="saveBatchData('list')"></auto-save>
                         <a @click.default="$emit('refresh')" class="btn btn-md btn-light" :class="{'btn-light':batchState=='update','btn-warning':batchState!='update'}">Cancel.</a>
                    </div>

                </div>
            </transition>

            <transition name="bo-slide">
                <div v-if="tab=='addtogroup'">
                    
                    <div class="block-announce info mt-4 mb-4">
                        <p class="title"><i class="hotbox-icon hotbox-icon-meeting"></i> Create a New Group..</p>
                        <p>This will add the following customers to a new or existing group, whcih may be used in a discount rule filter or campaign!</p>
                    </div>
                    
                    <table role="table" class="table b-table table-striped">
                        <thead>
                            <tr>
                                <th width="90%">Item</th>
                                <th width="10%">Confirm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,ind) in batch" :class="{'table-success':inSet(item.id,batch_ids)}">
                                <td width="90%">{{item.first_name}} {{ item.last_name}}<br><span class="small" v-if="item.address">{{ item.address.city }} {{ item.address.region }}</span></td>
                                <td width="10%" valign="top">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" :checked="inSet(item.id,batch_ids)" :disabled="false" @click="toggleBatchId(item.id,$event)"><span class="custom-control-indicator"></span> 
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <select class="col-12 form-control input mt-2" v-model="form.group" name="group">
                            <option v-for="(opt,oid) in schema.filters.group_id.values" v-if="opt.type=='custom'" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                            <option value="NEW">Add to a New Group</option>
                    </select>
                    <form-text v-if="form.group=='NEW'" v-model="form.new_name" :schema="{name:'new_name'}" :hideLabel="true" class="mt-1" />
                    
                    <div class="col-12 clearfix mt-2 text-center mt-3">
                        <auto-save type="save" :state="batchState" @autoSave="saveBatchData('list')"></auto-save>
                         <a @click.default="$emit('refresh')" class="btn btn-md btn-light" :class="{'btn-light':batchState=='update','btn-warning':batchState!='update'}">Cancel.</a>
                    </div>

                </div>
            </transition>            

            </div>
            <div class="no-content-block" v-else>
                <loading :display="true" type="loadModal" />
            </div>

        </fieldset>
    </form>
    </div>
    <div class="no-content-block" v-else>
        <loading :display="(schema) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    export default {
        props: {
            module: {
                type: String,
                default: 'administration',
            },
            model: {
                type: String,
                default: 'Customer'
            },
            schema: {
              type: Object,
              default: () => {}
            },
            ids: {
                type: Array,
                default: () => {}
            },
            focus: {
                type: String,
                default: 'edit'
            },
        },

        data(){
            return {
                tab: this.focus,
                batch: null,
                batch_ids: this.ids,
                batchSortBy:null,
                batchSortDesc:false,
                batchState: 'update',
                isLoading: false,
                form:{
                    group:'NEW',
                    new_name:'Admin Group '+this.$moment.utc().local().format('LL')
                }
            };
        },
        
        components : {

        },

        mounted() {
            this.getBatchData();
            this.getGroupData();
        },
        
        methods: {
            getBatchData(){
                this.isLoading = true;
                axios.get('/api/v1/'+this.schema.meta.resource+'/batch?batch_ids='+this.batch_ids.join(',')).then(response =>  {
                    this.batch = response.data;
                    this.isLoading = false;
        	    }).catch(error => {
        	        this.isLoading = false;
                    this.$announcer(error.response);
                    this.$emit('refresh');
        	    });
            },
            
            saveBatchData(typ){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.batchState = 'saving..';
                        let up = null;                                          // reduce properties of batch data that arent batchable - need not send more then 1k params for large batches
                        if(typ=='data') up = this.batch.map(r => Object.keys(r).reduce((o, k) => (Object.keys(this.batchFields).indexOf(k)!==-1 || k=='id') ? { ...o, [k]: r[k]} : o, {}));
                        else up = this.batch_ids;
                        axios.post('/api/v1/'+this.schema.meta.resource+'/batch/'+this.tab,{batch:up,form:this.form}).then(response =>  {
                            if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'}); // update schems, will update grid
                            this.batchState = 'saved';
                            this.$announcer(response);
                            this.$emit('refresh');
                        }).catch(error => {
                            this.batchState = '(re)update';
                            this.$announcer(error.response);
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },
            
            batchSort(field) {
                this.batchSortBy=field;
                this.batchSortDesc = !this.batchSortDesc;
                this.sorter(this.batch,this.batchSortDesc,this.batchSortBy);    // resort batch
            },
            
            batchOverride(field,val){
                this.batch = this.batch.map(v => {v[field] = val; return v;});
            },

            toggleBatchId(val,e){
                if(e.target.checked){
                    if(this.batch_ids.indexOf(val) === -1) this.batch_ids.push(val);
                }else this.batch_ids.splice(this.batch_ids.indexOf(val), 1);
            },

            getGroupData(){
                this.isLoading = true;
                axios.get('/api/v1/admin/dispensary/customergroups').then(response =>  {
                    this.groups = response.data;
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer(error.response);
                    this.$emit('refresh');
                });
            }
        },
        
        computed: {
            batchFields(){
                return Object.keys(this.schema.form).filter(k => this.schema.form[k].batchable===true).reduce((obj, key) => { obj[key] = this.schema.form[key]; obj[key].override = null; return obj; }, {});
            }
        },
        
        watch: {
            batch:{
                handler(newVal,oldVal){
                    this.batchState = (oldVal) ? 'update changes' : 'update';
                },
                deep: true
            },

            batch_ids(){
                this.$emit('update:ids',this.batch_ids);                        // update ids list past to this component
            }
        }
        
    };
    
</script>

<style>

</style>
