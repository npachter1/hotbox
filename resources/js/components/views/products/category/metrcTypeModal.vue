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
                
                    <div class="block-announce info">
                        <p class="title">Metrc Notes</p>

                            <table style="border-width:0px;margin-left:30px">
                                <tr><td><label>Requires Strain</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_strain ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Item Brand</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_item_brand ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Administration Method</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_administration_method ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Unit CBD Percent</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_unit_cbd_percent ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Unit CBD Content</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_unit_cbd_content ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Unit THC Percent</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_unit_thc_percent ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Unit THC Content</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_unit_thc_content ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Unit Volume</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_unit_volume ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Unit Weight</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_unit_weight ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Serving Size</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_serving_size ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Supply Duration Days</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_supply_duration_days ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Ingredients</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_ingredients ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Requires Product Photo</label></td><td>&nbsp;&nbsp;</td><td>{{ item.requires_product_photo ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Can Contain Seeds</label></td><td>&nbsp;&nbsp;</td><td>{{ item.can_contain_seeds ? 'True' : 'False' }}</td></tr>
                                <tr><td><label>Can Be Remediated</label></td><td>&nbsp;&nbsp;</td><td>{{ item.can_be_remediated ? 'True' : 'False' }}</td></tr>
                            </table>
                    </div>

        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal" />
    </div>
</template>

<script>

     import Item from '../../../../models/CategoryMetrc';


    export default {

        props: {
            id: {
                type: Number,
                default: null
            },
            model: {
                type: String,
                default: 'CategoryMetrc'
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
        
        async mounted() {
            await this.$store.dispatch(this.module+'/setSchemas','categoryMetrc'); // this is a sub resource - it loads its own schema upon modal load
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
                this.isLoading = false;
                this.$announcer({status:422,data:{message:'Whoops - couldnt find the associated record - Please try again later.'}});
                this.$emit('refresh',{},'metrctype');
            }
        },
        
        methods: {
            autoSave(confirm=false){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.itemState = 'saving..';
                        this.item.save().then(response => {
                            this.itemState = 'saved';
                            if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:'categorySchema'}); // this will reload underlying category grid upon emitting
                            this.$announcer({status:200,data:{message:'The Metrc Category Settings have been updated.'}});
                            this.$emit('refresh',this.item,'metrctype');
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
