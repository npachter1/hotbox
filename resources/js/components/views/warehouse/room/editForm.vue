<template>
    <div v-if="item && schema" class="col-12">
        <form @change="$emit('change')">
        <fieldset>

            <div class="col-12">

                <a href="" v-if="$store.getters.getAgent=='metrc'" class="float-right" @click.prevent="syncMetrcRooms">
                    <spinner :isProcessing="isSyncing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" />
                    <i v-if="!isSyncing" class="hotbox-icon hotbox-icon-refresh-69"></i> Sync Rooms
                </a>

                <h3 v-if="item.id" class="mb-4">Edit {{ item.name }}</h3>
                <h3 v-else>Add a new Room or Row:</h3>

                <div class="row">
                    <div v-for="(formItem,ele) in schema.form" class="float-left" :class="{[(formItem.container) ? [formItem.container] : 'col-12']: true}">
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

    import Item from '../../../../models/Room';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Room'
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
                itemState: 'save'
            };
        },

        components : {

        },

        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
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
                        //this.$router.push({name:this.model.toLowerCase()});
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

            syncMetrcRooms(){
                this.isSyncing = true;
                axios.get('/api/v1/admin/grow/rooms/syncMetrc').then(response =>{
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
