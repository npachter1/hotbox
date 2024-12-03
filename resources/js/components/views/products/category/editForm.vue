<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>
            
                <h3 v-if="item.id && type=='form'" class="mb-4">Edit {{ item.name }}</h3>
                <h3 v-else-if="type=='form'">Add a new Category:</h3>

                <div class="row">
                    <form-text v-model="item.name" :schema="schema.form.name" @input="(v) => checkNameInput(v,schema.form.name.name,item.id)" class="col-12 mt-2 mb-3" />
                    <form-boolean :declared="item.contains_thc" :schema="schema.form.contains_thc" @input="(upd) => {item.contains_thc = upd}" class="col-12 mt-2 mb-3" />

                    <form-select v-if="item.contains_thc" v-model="item.equivalency_type" :schema="schema.form.equivalency_type" :scopeData="$store.getters.getAgent" class="col-12 mt-0 mb-3" />

                    <form-textarea v-model="item.notes" :schema="schema.form.notes" :rows="schema.form.notes.rows || 3" class="col-12 mt-2 mb-3" />
                    <form-file v-model="item.public_img" :schema="schema.form.public_img" type="image" :resource="model.toLowerCase()" :view="schema.form.public_img.view || 'profile'" @input="(upd) => {item.public_img = upd}" class="col-12 col-sm-4 mx-1 mt-2 mb-3" />
                </div>

                <div class="col-12 clearfix mt-3 mb-3 text-center">
                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                    
                    <a v-if="type!='form'" @click.prevent="$emit('toggle')" class="btn btn-md btn-light">Close.</a>
                    <a v-else @click.default="$router.go(-1)" class="btn btn-md btn-light">Return.</a>

                    <form-button v-if="type==='modal'" :disabled="isArchived || !!(item.products.length)" @click="$emit('archive')" text="Archive" show-disabled disabled-title="Archiving Unavailable" :disabled-text="isArchived ? 'Category is already archived.' : 'Products are attached to this category.'"/>
                </div>

        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Category';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            type: {
                type: [String,Number],
                default: 'form'                                                 // form or modal, which routes to grid or just emits result
            },
            model: {
                type: String,
                default: 'Category'
            },
            module: {
                type: String,
                default: 'products',
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
                this.$validator.validateAll().then(async (result) => {
                    if(result){
                        
                        if(!this.id){
                            let withPin = await this.requirePin('Please Enter an Admin PIN to Create a new Category.');
                            if(withPin===false) return false;                   // an adminpin couldnt be validated HINT add error message here if desired.
                        }
                        
                        if(!confirm) _.debounce(() => { this._save(); },2000)();
                        else this._save(true);
                    }else if(confirm===true){
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
                        if(this.type==='form') this.$router.push({name:this.model.toLowerCase()});
                    }
                    if(this.type==='modal') {
                        this.$emit('refresh',response); // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
                        this.$emit('toggle');
                    }
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },
            
            checkNameInput: _.debounce(function (val,fld) {                     // upon adding name of new category - check if it exists
                if(this.id!=0) return false;
                axios.post('/api/v1/admin/dispensary/categories/exists',{value:val}).then(response =>  {
                    if(response.data.exists===true)
                        this.$validator.errors.add({field:fld,msg: 'Category Already Exists!'});
                    else this.$validator.errors.remove({field:fld});
                }).catch(error => {
                   this.$announcer(error.response);
                });         
            }, 300),
            
            syncMetrcCategories(){
                this.isSyncing = true;
                axios.get('/api/v1/admin/dispensary/categories/syncMetrc').then(response => {
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

            isArchived() {
                return !!(this.item.archived_at);
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            },
            'item.contains_thc'(to,from){
                if(to===false){ 
                    this.item.equivalency_type = 'noncannabis';
                    this.item.metrc_category_id = null;
                }
            }
        }
        
    };
    
</script>

<style>

</style>
