<template>
	<div v-if="item && schema">

	       <form @change="autoSave()">    

	        <div class="drsection-header">
                <h5>Step 1: Choose Link Source
                    <span v-if="dbConnected" class="float-right small"><i class="hotbox-icon hotbox-icon-g-check show-green"></i> {{ migrationType | renderValue(schema.form.migration_prompt.values) }} Confirmed.</span>
                </h5>
            </div>
            
            <transition name="hb-slide">
            <div v-if="!dbConnected">

                <div class="drsection-content mb-0">
                    <form-select v-model="migrationType" :schema="schema.form.migration_prompt" />
                </div>
                
                <transition name="hb-slide">
    	           <form-sectionalobject v-if="!dbConnected" v-model="item.migration_settings" :formData="item" :schema="schema.form.migration_settings" :scoped="migrationType" class="form-group-list mb-4" />
                </transition>

                <div class="col-12 text-center">
                    <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                    <button @click.prevent="testConnection()" class="btn btn-md btn-info"><spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" />
                     Test Connection & Continue</button>
                </div>
                
            </div>
            </transition>


	        <div class="drsection-header">
                <h5>Step 2: Confirm Options <span v-if="isReady" class="float-right small"><i class="hotbox-icon hotbox-icon-g-check show-green"></i> Options Confirmed.</span></h5>
            </div>

            <div v-if="locationsFound.length" class="form-group migration-options ml-3 mt-2 mb-3">

                    <div class="form-group col-12 mb-1 mt-1"><label><strong>License Number:</strong> {{ licensenum }}</label></div>
                    <form-select v-model="item.migration_settings.btdb_location_id" :schema="Object.assign({},schema.form.migration_location_id,{values:locationsFound})" class="col-sm-6 mt-2 mb-2" />
                    <form-multiselect v-if="foundOtherLocations" v-model="item.migration_settings.btdb_associated_licensenums" :schema="Object.assign({},schema.form.migration_associated_licensenums,{values:foundOtherLocations})" class="col-sm-6 mt-2 mb-3" />
                    <form-datetime v-model="item.migration_settings.backfill" :schema="schema.form.migration_backfill_date" class="col-12 col-sm-4 mt-3 mb-4" />

            </div>
            <h5 v-if="dbConnected && !locationsFound.length" class="w-100 text-center">Location Not Found</h5>


	        <div class="drsection-header mt-2">
                <h5 class="mt-2">Step 3: Start Migration Process..</h5>
            </div>

            <div style="text-align:center">
                <button v-if="dbConnected" @click.prevent="startMigration()" :disabled="!isReady" class="btn btn-lg btn-primary">
                    <spinner :isProcessing="isSending" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" />
                     Start Migration
                </button>
            </div>

            <div v-if="schema.lang.migrate_comment" class="col-12 d-flex justify-content-center">
                <div class="col-sm-10 text-center mt-4 mb-4">
                    <div class="block-announce info">
                        <p class="title"><i class="hotbox-icon hotbox-icon-f-comment"></i> A Comment on Migrating..</p>
                        <p>{{ schema.lang.migrate_comment }}</p>
                    </div>
                </div>
            </div>

	   </form>

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
            }
        },
        
        components:{ 

        },
        
        data() {
            return {
                item: null,
                itemState: 'save',
                migrationType: 'btdb',
                dbConnected: false,
                locationsFound:[],
                foundOtherLocations: [],
                licensenum: null,
                isProcessing: false,
                isSending:false
            };
        },

        mounted() {
            if(this.schema) this.loadProfile();
        },

        methods: {
            autoSave(confirm=false){
                this.parseHost();

                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) _.debounce(() => { this._save(); },2000)();
                        else this._save(true);
                    }else if(confirm==true)
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },
            
            _save(confirm=false){
                this.itemState = 'saving..';
                this.item.save().then(response => {
                    if(confirm){
                        this.$store.dispatch('resetAuthLocation',{scope:'user,agg',reset:false}); // async dispatch to update user.location info based on this change
                        this.$emit('updateLocation',this.item);
                        this.$announcer({status:200,data:{message:'Your Item Settings have been Saved!'}});
                    }
                    this.itemState = 'saved';
                    if (this.dbLinkFormfilledOut && !this.isProcessing) {
                        this.$announcer({data:{message:'Testing Connection.  This may take a moment...'}});
                        this.testConnection();
                    }
                    // TODO update user location store!
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },

            loadProfile(){
                Item.find(this.$store.state.disp.location).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
                return true;
            },

            parseHost() {
                const userInput = this.item.migration_settings.btdb_host;
                if (userInput && userInput.indexOf(':')>-1 && !this.item.migration_settings.btdb_port) {
                    this.item.migration_settings.btdb_host = userInput.substr(0,userInput.indexOf(':'));
                    this.item.migration_settings.btdb_port=userInput.substr(userInput.indexOf(':')+1);
                }
            },

            testConnection() {
                this.isProcessing = true;
                axios.get('/api/v1/admin/auth/testMigrationConnection/'+this.migrationType).then(response => {
                    if (response.data.db_connected) {
                        this.dbConnected = true;
                        this.licensenum = response.data.licensenum;
                        this.locationsFound = response.data.found_locations || [];
                        if(this.locationsFound.length==1) this.item.migration_settings.btdb_location_id = this.locationsFound[0].id; // if only 1, just match it.
                        if (response.data.other_locations && response.data.other_locations.length > 0) {
                            this.foundOtherLocations = response.data.other_locations || [];
                        }
                    }else{
                        this.dbConnected = false;
                    }
                    this.isProcessing = false;
                }).catch(error => {
                    this.dbConnected = false;
                    this.locationsFound = [];
                    this.isProcessing = false;
                    this.$announcer(error.response);
                });
            },

            startMigration() {
                this.isSending = true;
                axios.post('/api/v1/admin/auth/startMigration/'+this.migrationType, { location: this.item }).then(response => {
                    this.isSending = false;
                    this.$announcer(response);
                    this.$router.push('/admin/dashboard');
                }).catch(error => {
                    this.isSending = false;
                    this.$announcer(error.response);
                });
            }
        },

        computed: {
            schema(){
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            
            isReady(){
                if(!(this.item || {}).migration_settings) return false;
                
                return (this.dbConnected
                    && this.locationsFound.length
                    && this.item.migration_settings.backfill
                    && this.item.migration_settings.btdb_location_id) ? true : false;
            },

            dbLinkFormfilledOut(){
                return this.item.migration_settings.btdb_host
                    && this.item.migration_settings.btdb_port
                    && this.item.migration_settings.btdb_dbname
                    && this.item.migration_settings.btdb_schema
                    && this.item.migration_settings.btdb_username
                    && this.item.migration_settings.btdb_password
            }
        },

        watch: {
            schema(to,from){
                if(to) this.loadProfile();
            },
            
            item: {
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            }
        }
    }
</script>

<style>

.migration-options .vdp-datepicker__calendar {
    left: 0px;
}
    
</style>