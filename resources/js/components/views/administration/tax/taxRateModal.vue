<template>
    <div v-if="item && schema" class="col-12">
    <form>
        <fieldset>

                <div class="row">
                    <div v-for="(formItem,ele) in schema.form" v-if="formItem.name!='type'" class="float-left" :class="{[(formItem.container) ? [formItem.container] : 'col-12']: true}">
                        <div class="form-group-list">
                            <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" />
                            <form-text v-if="formItem.type=='text'" v-model="item[ele]" :schema="formItem" />
                            <form-number v-if="formItem.type=='number'" v-model="item[ele]" :schema="formItem" />
                            <form-textarea v-if="formItem.type=='textarea'" v-model="item[ele]" :schema="formItem" :rows="formItem.rows || 3" />
                            <form-select v-if="formItem.type=='select'" v-model="item[ele]" :schema="formItem" :scopeData="item.country" />
                            <form-multiselect v-if="formItem.type=='multiselect'" v-model="item[ele]" :schema="formItem" />
                            <form-datetime v-if="formItem.type=='datetime'" v-model="item[ele]" :schema="formItem" />
                            <form-file v-if="formItem.type=='image'" v-model="item[ele]" :schema="formItem" type="image" :resource="model.toLowerCase()" :view="formItem.view || 'profile'" @input="(upd) => {item[ele] = upd}" />
                            
                            
                        </div>
                    </div>
                </div>

                <div class="col-12 clearfix mt-3 mb-4 text-center">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                </div>


        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
    </div>
</template>

<script>

     import Item from '../../../../models/TaxRate';


    export default {

        props: {
            id: {
                type: Number,
                default: null
            },
            model: {
                type: String,
                default: 'TaxRate'
            },
            module: {
                type: String,
                default: 'administration',
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
        
        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas','taxRate'); // this is a sub resource - it loads its own schema upon modal load
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema,false);
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                    this.$emit('refresh',{},'metrctype');                          // will close this modal
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
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
                            if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:'taxSchema'}); // this will reload underlying tax category grid upon emitting
                            this.$announcer({status:200,data:{message:'The Tax Rate Settings have been updated.'}});
                            this.$emit('refresh',this.item,'taxrate');
                        }).catch(error => {
                            this.$announcer(error.response);
                            this.itemState = 'resave';
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
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
