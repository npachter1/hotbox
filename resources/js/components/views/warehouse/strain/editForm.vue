<template>
    <div v-if="item && schema" class="col-12">
        <form @change="$emit('change')">
        <fieldset>
            <div class="col-12">
                
                <a href="" v-if="$store.getters.getAgent=='metrc'" class="float-right" @click.prevent="syncMetrcStrains">
                    <spinner :isProcessing="isSyncing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                    <i v-if="!isSyncing" class="hotbox-icon hotbox-icon-refresh-69"></i> Sync Strains
                </a>
                
                <h3 v-if="item.id" class="mb-4">Edit {{ item.name }}</h3>
                <h3 v-else>Add a new Strain:</h3>

                <div class="row">
                    <div class="col-md-8">
                        <div v-for="(formItem,ele) in schema.form" class="float-left" :class="{[(formItem.container) ? [formItem.container] : 'col-12']: true}" v-if="formItem.name!='cannabinoid_breakdown'">
                            <div class="form-group-list">

                                <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" />
                                <form-text v-if="formItem.type=='text'" v-model="item[ele]" :schema="formItem" />
                                <form-number v-if="formItem.type=='number'" v-model="item[ele]" :schema="formItem" />
                                <form-textarea v-if="formItem.type=='textarea'" v-model="item[ele]" :schema="formItem" :rows="formItem.rows || 3" />
                                <form-select v-if="formItem.type=='select'" v-model="item[ele]" :schema="formItem" :scopeData="item.country" />
                                <form-simpleselect v-if="formItem.type=='simpleselect'" v-model="item[ele]" :schema="formItem" :isDisabled="(item.id && ele=='type') ? true : false" />
                                <form-multiselect v-if="formItem.type=='multiselect'" v-model="item[ele]" :schema="formItem" />
                                <form-datetime v-if="formItem.type=='datetime'" v-model="item[ele]" :schema="formItem" />
                                <form-file v-if="formItem.type=='image'" v-model="item[ele]" :schema="formItem" type="image" :resource="model.toLowerCase()" :view="formItem.view || 'profile'" @input="(upd) => {item[ele] = upd}" />
                                <form-simpleobject v-if="formItem.type=='simpleobject'" v-model="item[ele]" :formData="item" :schema="formItem" />


                            </div>
                        </div>
                    </div>
                    <div v-if="isCreate" class="col-md-4">
                        <label><b>Locations</b><span class="show-red small"> *(Required)</span></label>
                        <ul class="location-list">
                            <li>
                                <b-checkbox
                                        v-model="locationsAllSelected"
                                        :indeterminate="toggleLocationsIndeterminate"
                                        aria-describedby="locations"
                                        aria-controls="locations"
                                        @change="locationsToggleAll">
                                    {{ locationsAllSelected ? 'Un-select All' : 'Select All' }}
                                </b-checkbox>
                            </li>
                            <b-checkbox-group id="checkbox-group-2" v-model="locationId" name="locations">
                                <li v-for="(location,sid) in $store.state.user.locations_assigned" :key="location.id" style="padding-left:10px;">
                                    <b-checkbox
                                            v-model="locationId"
                                            :value="location.id"
                                            v-validate="'required'"
                                            data-vv-as="Location"
                                            name="location">{{ location.name.substr(0,20) }}</b-checkbox>
                                </li>
                            </b-checkbox-group>
                        </ul>
                        <span class="val-danger-text">{{ errors.first('location') }}</span>
                    </div>
                    <div v-if="!isCreate" class="col-md-4">
                        <label>Location</label>
                        <input type="text" readonly class="form-control-plaintext" id="staticLocationName" :value="locationName">
                    </div>

                </div>

                <!--todo Cannabinoid Need to be removed permanently? -->
<!--                <div class="row">
                    <div class="float-left col-12 drsection-header">
                        <h4>Cannabinoid Breakdown</h4>
                    </div>
                    <div v-for="(formItem,ele) in schema.form.cannabinoid_breakdown.properties" class="float-left" :class="{[(formItem.container) ? [formItem.container] : 'col-12']: true}">
                        <div class="form-group-list">
                            <form-number v-if="formItem.type=='number'" v-model="item[ele]" :schema="formItem" />
                        </div>
                    </div>
                </div>-->

                <div class="col-12 clearfix mt-3">
                    <div class="drsection-content">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                        <a v-if="type!=='form'" @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                        <a v-if="type==='form'" @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                    </div>
                </div>

            </div>

        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Strain';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Strain'
            },
            module: {
                type: String,
                default: 'warehouse',
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
            }
        },
        
        data(){
            return {
                item: null,
                isSyncing:null,
                itemState: 'save',
                locationId: [],
                toggleLocationsIndeterminate: false,
                locationsAllSelected: false
            };
        },
        
        components : {

        },
        
        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.locationId = Array.isArray(this.item.location_id)  ? this.item.location_id.slice() : [this.item.location_id];
                    this.$emit('loaded');
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.isLoading = false;
            }
        },
        
        methods: {
            locationsToggleAll(checked) {
                this.locationId = checked ? this.$store.state.user.locations_assigned.map(e=>{ return e.id }) : []
            },

            autoSave(confirm=false){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) _.debounce(() => { this._save(); },2000)();
                        else this._save(true);
                    }else if(confirm==true){
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    }else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
            },
            
            _save(confirm=false){
                this.itemState = 'saving..';
                this.item.location_id = this.locationId.slice();
                this.item.save().then(response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        if(this.type==='modal'){                                 // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
                            //await this.$store.dispatch(this.module+'/setSchemas','campaign,group');
                            this.$emit('refresh',response);
                            this.$emit('toggle');
                        }else this.$router.push({name:this.model.toLowerCase()});
                    }
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },
            
            syncMetrcStrains(){
                this.isSyncing = true;
                axios.get('/api/v1/admin/grow/strains/syncMetrc').then(response =>{
                    this.isSyncing = false;
                    if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'});
                    this.$announcer(response);
                }).catch(error => {
                    this.isSyncing = false;
                    this.$announcer(error.response);
                });
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            isCreate() {
                return this.id===0;
            },
            locationName() {
                if (!this.isCreate) {
                    const l = this.$store.state.user.locations_assigned.find(e=>{return e.id===this.item.location_id});
                    if (l && l.name) return l.name;
                }
                return '';
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            },

            locationId(newVal, oldVal) {
                if (newVal.length === 0) {
                    this.toggleLocationsIndeterminate = false;
                    this.locationsAllSelected = false;
                } else if (newVal.length === this.$store.state.user.locations_assigned.length) {
                    this.toggleLocationsIndeterminate = false;
                    this.locationsAllSelected = true;
                } else {
                    this.toggleLocationsIndeterminate = true;
                    this.locationsAllSelected = false;
                }
            }
        }
        
    };
    
</script>

<style>
    .location-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 5px;
    }

    .custom-checkbox .custom-control-input:indeterminate ~ .custom-control-label::before {
        border-color: #2a5788;
        background-color: #2a5788;
    }

    .custom-checkbox .custom-control-input:checked~.custom-control-label::before{
        border-color: #2a5788;
        background-color: #2a5788;
    }
    .custom-control-label::before, .custom-control-label::after {
        top:.1em;
    }
</style>
