<template>
    <div v-if="item && schema" class="col-12">
        <form @change="$emit('change')">
        <fieldset>
            
            <div class="col-12">
                
                <a href="" v-if="$store.getters.getAgent=='metrc'" class="float-right" @click.prevent="syncMetrcHarvests">
                    <spinner :isProcessing="isSyncing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                    <i v-if="!isSyncing" class="hotbox-icon hotbox-icon-refresh-69"></i> Sync Harvests
                </a>
                
                <h3 v-if="item.id" class="mb-4">{{ item.name }}</h3>

                <div class="row">

                        <div class="col-6" v-for="(formItem,ele) in schema.form">
                            <div v-if="formItem.name == 'patient_license_number'">
                                <label v-if="isMedical" style="padding-right:15px">{{ formItem.title }}</label>
                            </div>
                            <div v-else>
                                <label style="padding-right:15px">{{ formItem.title }}</label>
                            </div>

                            <div v-if="formItem.name == 'drying_room_id'">
                                <a v-if="item['drying_room_id']" :href="getRoomLink(item['drying_room_id'])">
                                {{ schema.form.drying_room_id.values.find(r => r.id === item['drying_room_id']).name }}
                                </a>
                                <span v-else>n/a</span>
                            </div>

                            <div v-if="formItem.name == 'patient_license_number' && isMedical">
                                {{ item[ele] ? item[ele] : 'n/a' }}
                            </div>

                            <div v-if="formItem.type == 'weight'">{{ item[ele] }} {{ item['unit_of_weight'] }}</div>

                            <div v-if="formItem.type == 'date'">{{ item[ele] | formattedLocalDate }}</div>

                            <div v-if="formItem.type == 'number'">{{ item[ele] ? item[ele] : '0' }}</div>

                            <div v-if="formItem.type == 'text' && formItem.name != 'drying_room_id' && formItem.name != 'patient_license_number'">{{ item[ele] ? item[ele] : 'n/a' }}</div>
                        </div>

                </div>

                <div class="col-12 clearfix mt-3">
                    <div class="drsection-content">
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

    import Item from '../../../../models/Harvest';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Harvest'
            },
            module: {
                type: String,
                default: 'plants',
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
                isMedical: false
            };
        },
        
        components : {

        },
        
        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.isLoading = false;
                    this.$emit('loaded');
                    axios.get('/api/v1/admin/auth/location/ismedical/'+this.item.location_id).then(response2 =>{
                        this.isMedical = response2.data;
                    });
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.isLoading = false;
            }
        },
        
        methods: {
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
            
            syncMetrcHarvests(){
                this.isSyncing = true;
                axios.get('/api/v1/admin/grow/harvests/syncMetrc').then(response =>{
                    this.isSyncing = false;
                    if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:this.model.toLowerCase()+'Schema'});
                    this.$announcer(response);
                }).catch(error => {
                    this.isSyncing = false;
                    this.$announcer(error.response);
                });
            },

            getRoomLink(id) {
                if (id)
                    return '/admin/warehouse/room/' + id + '/edit';
                else
                    return '';
            },
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            }
        }
        
    };
    
</script>

<style>

</style>
