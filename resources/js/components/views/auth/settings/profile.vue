<template>
    <div v-if="item && schema" class="col-12">
    <form class="" @change="autoSave()">
        <fieldset>

                <form-sectionalobject v-model="item.settings" :formData="item.settings" :schema="schema.form.settings" :disabled="($store.getters.userCan('Manage Location')) ? false : true" :scoped="item.type" class="form-group-list mb-4" />

                <form-sectionalobject v-model="item.financial_settings" :formData="item.financial_settings" :schema="schema.form.financial_settings" :disabled="($store.getters.userCan('Manage Location')) ? false : true" :scoped="item.type" class="form-group-list mb-4" />




                <div class="drsection-header">
                    <h4>General Settings</h4>
                </div>
                <div class="drsection-content">
                    <form-text v-model="item.name" :schema="schema.form.name" />
                    <form-text v-model="item.licensenum" :schema="schema.form.licensenum" />
                    <form-search v-model="item.addressbook_id" :defaultOption="$store.state.user.location.address" :schema="schema.form.addressbook_id" />

                </div>


                <div v-if="$store.getters.userCan('manage-server')" class="drsection-header">
                    <h4>HotBox Admin Account Overrides for {{item.name}}:</h4>
                </div>
                <div v-if="$store.getters.userCan('manage-server')" class="drsection-content">
                    <form-number v-if="$store.getters.userCan('manage-server')" v-model="item.settings.campaign_sms_limit" :schema="schema.form.admin_campaign_sms_limit" class="col-12 mt-2 mb-3"></form-number>

                </div>


                <div class="col-12">
                    <div class="drsection-content">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                    </div>
                </div>

                <div class="col-12 mt-3 mb-2">

                    <div v-if="item.is_demo" class="block-announce warning">
                        <p class="title"><i class="hotbox-icon hotbox-icon-presentation show-yellow"></i> You are now in Demo Mode..</p>
                        <p>Heya, You are in Demo Mode.  No Outside communications will be sent.</p>
                    </div>

                    <div class="block-announce info mt-2 mb-3">
                        <p class="title"><i class="hotbox-icon hotbox-icon-key-26"></i> Your API Access Token:</p>
                        <p class="text-center"><a v-if="!apiToken" class="btn btn-md btn-light text-center" @click="getToken">
                            <spinner :isProcessing="refreshingToken" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> Get a Fresh Token</a>
                        </p>
                        <blockquote v-if="apiToken">{{ apiToken }} <i :class="{'hotbox-icon hotbox-icon-refresh-69':!refreshingToken,'hotbox-icon hotbox-icon-hourglass':refreshingToken}" @click="getToken"></i></blockquote>
                        <p class="text-center">This token will grant access (based on your <b>{{ $store.state.user.location.name }}</b> credentials) to our API endpoints, which may be <a href="" target="_blank">reviewed here</a></p>
                    </div>
                </div>

        </fieldset>
    </form>

    </div>
    <div v-else>
        <loading :display="(item && schema) ? false : true" type="loadPage" />
    </div>
</template>

<script>

    import Item from '../../../../models/Location';
    import _ from 'lodash';


    export default {

        props: {
            model: {
                type: String,
                default: 'Location'
            },
            module: {
                type: String,
                default: 'auth',
            },
            locationRef:{
                type: Object,
                default: null
            }
        },

        data(){
            return {
                item: null,
                itemState: 'save',
                apiToken:null,
                refreshingToken:false
            };
        },

        components : {

        },

        mounted() {
            if(this.schema) this.loadProfile();
        },

        methods: {

            autoSave(confirm=false){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) _.debounce(() => { this._save(); },2000)();
                        else this._save(true);
                    }else if(confirm==true)
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },

            _save(confirm=false){
                console.log(this.item);
                this.itemState = 'saving..';
                this.item.settings.is_medical = (this.item.licensenum.includes('R') || this.item.licensenum.includes('r')) ? false : true;
                this.item.settings.is_producer = (this.item.licensenum.startsWith('403')) ? true : false;
                this.item.settings.is_processor = (this.item.licensenum.startsWith('404')) ? true : false;
                this.item.save().then(response => {
                    if(confirm){
                        this.$store.dispatch('resetAuthLocation',{scope:'user,agg',reset:false}); // async dispatch to update user.location info based on this change
                        this.$emit('updateLocation',this.item);
                        this.$announcer({status:200,data:{message:'Your Item Settings have been Saved!'}});
                    }
                    this.itemState = 'saved';
                    // TODO update user location store!
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },

            getToken(){
                this.refreshingToken = true;
                axios.post('/api/v1/admin/auth/newToken',{}).then(response =>  {
                    this.refreshingToken = false;
                    this.apiToken = response.data.api_token;
                    this.$announcer(response);
                }).catch(error => {
                    this.refreshingToken = false;
                    this.$announcer(error.response);
                });
            },

            loadProfile(){

                //if(this.schema) this.item = new Item(this.locationRef || this.$store.state.user.location).withDefaults(this.schema);
                Item.find(this.$store.state.disp.location).then(response => {

                    this.item = new Item(response).withDefaults(this.schema);

                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
                return true;
            }
        },

        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },

        watch: {
            schema(to,from){
                if(to) this.loadProfile();
            },

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
