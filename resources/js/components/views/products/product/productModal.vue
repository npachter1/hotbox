<template>
    <div v-if="item && schema" class="col-12">
    <form @change="$emit('change')">
        <fieldset>
                <div class="row" :class="{'mt-4': this.type!=='form'}">

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

                    <a v-if="type==='form'" @click.default="$emit('update')" class="btn btn-md btn-light">Close.</a>
                    <a v-else @click.default="$router.go(-1)" class="btn btn-md btn-light">Return.</a>

                    <a v-if="type=='modal' && item.id && !hasInventory" @click.prevent="$emit('archive')" class="btn btn-md btn-danger ml-2"><i class="hotbox-icon hotbox-icon-trash-round"></i> Archive.</a>
                    <form-button v-if="type==='form'" :disabled="isArchived || hasInventory" @click="$emit('archive')" text="Archive" show-disabled disabled-title="Archiving Unavailable" :disabled-text="isArchived ? 'Product already archived.' : 'Inventory is attached to this product.'"/>
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
            },
            type:{
                type: String,
                default: null
            },
            init:{
                type: Object,
                default: () => {}
            },
            hasInventory: {
                type: Boolean,
                default: false
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
                    console.log("Item"); console.log(this.item);
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                    this.$emit('update',null);                             // will close this modal
                });
            }else{

                let seed = Object.assign({},this.schema.model,this.init,{id:0});
                this.item = new Item().withDefaults({model:seed});
                this.isLoading = false;
            }
        },

        methods: {
            autoSave(confirm=false){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.itemState = 'saving..';
                        this.item.save().then(response => {
                            this.itemState = 'saved';

                            if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                            if(response.pricesetSchema) this.$store.commit(this.module+'/setSchema',{data:response.pricesetSchema,key:'pricesetSchema'});

                            delete(response.schema);
                            this.$announcer({status:200,data:{message:'Your new Product data has been saved and added to your form!'}});
                            this.$emit('reset',response);
                            if(this.type=='import') this.$emit('sync',response);
                            else this.$emit('update',response);
                        }).catch(error => {
                            this.$announcer(error.response);
                            this.itemState = 'resave';
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
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
            }
        }

    };

</script>

<style>

</style>
