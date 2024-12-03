<template>
    <div v-if="item && schema" class="col-12">
    <form @change="autoSave()">
        <fieldset>

                <h3 v-if="item.id" class="mb-0">Edit {{ item.name }}</h3>
                <h3 v-else>Add a new Product:</h3>

                <span class="description" v-if="item.metrc_package_item_type">
                   - Metrc: {{ item.metrc_package_item_type }}<br>
                </span>
                <span class="description" v-if="item.created_at">
                   - Created {{ item.created_at | localDate }}
                </span>
                <span class="description" v-if="item.updated_at">
                   - Last updated {{ item.updated_at | localDate }}
                </span>

                <div class="row">

                    <form-select v-model="item.category_id" :schema="schema.form.category_id" class="col-12 col-sm-6 mt-2 mb-3 clearfix" />
                    <form-select v-model="item.type" :schema="schema.form.type" class="col-12 col-sm-6 mt-2 mb-3" />

                    <form-text v-model="item.name" :schema="schema.form.name" @input="(v) => checkNameInput(v,schema.form.name.name,item.id)" class="col-12 mt-2 mb-3 clearfix" />

                    <form-text v-model="item.thc_percentage" :schema="schema.form.thc_percentage"  class="col-4 mt-2 mb-3 clearfix" />
                    <form-text v-model="item.cbd_percentage" :schema="schema.form.cbd_percentage"  class="col-4 mt-2 mb-3 clearfix" />
                    <form-text v-model="item.terpene_percentage" :schema="schema.form.terpene_percentage" class="col-4 mt-2 mb-3 clearfix" />



                    <div class="col-12 col-sm-5 clearfix">
                        <form-file v-model="item.public_img" :schema="schema.form.public_img" type="image" :resource="model.toLowerCase()" :view="schema.form.public_img.view || 'profile'" @input="(upd) => {item.public_img = upd}" />
                    </div>
                    <div class="col-12 col-sm-7">
                        <form-text v-model="item.sku" :schema="schema.form.sku" class="mt-1 mb-2" />
                        <form-textarea v-model="item.description" :schema="schema.form.description" :rows="schema.form.description.rows || 3" class="mt-2 mb-3" />
                    </div>



                </div>

                <div class="col-12 clearfix mt-3 text-center">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                         <a @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                </div>


        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Product';
    import _ from 'lodash';


    export default {

        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Product'
            },
            module: {
                type: String,
                default: 'products',
            }
        },

        data(){
            return {
                item: null,
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
                        this.$router.push({name:this.model.toLowerCase()});
                    }
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },

            checkNameInput: _.debounce(function (val,fld) {                     // upon adding name of new category - check if it exists
                if(this.id!=0) return false;
                axios.post('/api/v1/admin/dispensary/products/exists',{value:val}).then(response =>  {
                    if(response.data.exists===true)
                        this.$validator.errors.add({field:fld,msg: 'Product Already Exists!'});
                    else this.$validator.errors.remove({field:fld});
                }).catch(error => {
                   this.$announcer(error.response);
                });
            }, 300)
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
