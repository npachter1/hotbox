<template>
    <div v-if="item && schema" class="col-12">
        <div class="">
            <span class="float-right"></span>
            <h5 v-if="item.id" class="w-100" style="margin-bottom: 0;">{{ item.first_name }} {{ item.last_name }} <span v-if="item.type=='wholesale'" class="small">[Wholesale]</span></h5>
            <h3 v-else>Add a new Customer:</h3>
        </div>

    <form @change="autoSave">
        <fieldset>
            <a v-if="!isCreate" class="btn btn-md btn-default btn-round customer-action-button" @click.prevent="serviceFromRegistry(item.id)" :disabled="isLoading || this.itemState==='saving..'"><i class="hotbox-icon hotbox-icon-pos"></i> Service</a>
            <b-tabs content-class="mt-3" >
                <b-tab title="Contact" ref="tabContact" :style="{minHeight: tabMinHeight + 'px'}">
                    <div v-if="item && schema" class="col-12">

                        <div v-if="item.type !== 'wholesale'" class="row">
                            <form-text v-model="item.first_name" :schema="schema.form.first_name" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-text v-model="item.middle_name" :schema="schema.form.middle_name" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-text v-model="item.last_name" :schema="schema.form.last_name" class="col-12 col-md-4 mt-1 mb-1" />
                        </div>
                        <div v-else class="row">
                               <form-text v-model="item.first_name" :schema="schema.form.company_name" class="col-12 col-sm-6 mt-1 mb-1 clearfix" />
                               <form-text v-model="item.settings.wholesale_license_id" :schema="schema.form.settings.properties.wholesale_license_id" class="col-12 col-sm-6 mt-1 mb-1" />
                        </div>
                        <div v-if="item.type === 'wholesale'" class="row">
                            <form-text v-model="item.contact_name" :schema="schema.form.contact_name" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-text v-model="item.receiving_party_name" :schema="schema.form.receiving_party_name" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-text v-model="item.employee_position" :schema="schema.form.employee_position" class="col-12 col-md-4 mt-1 mb-1" />
                        </div>
                        <div v-if="item.type === 'wholesale'" class="row">
                            <form-text v-model="item.tax_exempt_number" :schema="schema.form.tax_exempt_number" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-select v-model="item.license_type" :schema="schema.form.license_type" class="col-12 col-md-4 mt-1 mb-1" />
                        </div>

                        <div class="row">
                            <transition name="hb-fade">
                                <form-text v-model="item.alias"  :schema="schema.form.alias" class="col-12 col-md-4 mt-1 mb-1">
                                    <template slot="title-right">
                                        <a href="" class="float-right" @click.prevent="getSmurfName()">
                                            <spinner :isProcessing="gettingSmurf" :isFullScreen="false" :isLine="true" :spinnerWidth="21" class="float-left" />
                                            <i class="hotbox-icon hotbox-icon-bulb-62"></i> Generate Alias</a>
                                    </template>
                                </form-text>
                            </transition>
                            <form-select is-live v-model="item.type" :schema="schema.form.type" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-datetime v-model="item.birthdate" :schema="schema.form.birthdate" class="col-12 col-md-4 mt-1 mb-1" />
                        </div>

                        <div class="row">
                            <form-text v-model="item.address.address1" :schema="schema.form.address1" class="col-12 col-md-6 mt-1 mb-1" />
                            <form-text v-model="item.address.address2" :schema="schema.form.address2" class="col-12 col-md-6 mt-1 mb-1" />
                        </div><div class="row">
                            <form-text v-model="item.address.city" :schema="schema.form.city" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-text v-model="item.address.zip" :schema="schema.form.zip" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-select v-model="item.address.country" :schema="schema.form.country" class="col-12 col-md-4 mt-1 mb-1" />
                        </div>

                        <div class="row">
                            <div v-if="!editLicense.drivers" @click="showLicenseField('drivers')" class="col-12 col-md-4 mt-1 mb-1">
                                <form-text disabled value="Click to Update License" :schema="schema.form.drivers_license" class="hidden-license" />
                            </div>
                            <form-text ref="license" v-if="editLicense.drivers" v-model="item.update_drivers_license" :schema="schema.form.drivers_license" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-select v-model="item.drivers_license_state" :schema="schema.form.drivers_license_state" class="col-12 col-md-4 mt-1 mb-1" />
                            <form-datetime v-model="item.drivers_license_expiry_date" :schema="schema.form.drivers_license_expiry_date" class="col-12 col-md-4 mt-1 mb-1" />
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6  mt-2">
                                <form-file v-model="item.document_image" :disabled="this.itemState==='saving..'" multiple button-label-new="Add New" :schema="schema.form.document_image" type="file" :resource="model.toLowerCase()" @input="(upd) => {item.document_image = upd; this.autoSave()}" />
                            </div>
                        </div>
                    </div>
                </b-tab>
                <b-tab title="Marketing" :style="{minHeight: tabMinHeight + 'px'}">
                    <div class="row">
                        <form-boolean :declared="item.email_optin" :schema="schema.form.email_optin" @input="(upd) => {item.email_optin = upd}" class="col-12 col-md-4 mt-1 mb-1" />
                        <form-boolean :declared="item.sms_optin" :schema="schema.form.sms_optin" @input="(upd) => {item.sms_optin = upd}" class="col-12 col-md-4 mt-1 mb-1" />
                    </div>

                    <div class="row">
                        <form-text v-model="item.address.email" :schema="schema.form.email" class="col-12 col-md-4 mt-1 mb-1" />
                        <form-text v-model="item.address.phone" :schema="schema.form.phone" class="col-12 col-md-4 mt-1 mb-1" />
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4 mt-1 mb-1">
                            <label >Gender</label>
                            <div class="form-group">
                                <form-radio v-model="item.gender" name="gender" label="male" :inline="true">Male</form-radio>
                                <form-radio v-model="item.gender" name="gender" label="female" :inline="true">Female</form-radio>
                                <form-radio v-model="item.gender" name="gender" label="other" :inline="true">Other</form-radio>
                            </div>
                        </div>
                        <form-multiselect v-model="item.preferences" :schema="schema.form.preferences" class="col-12 col-md-4 mt-1 mb-1" />
                    </div>
                    <div class="row">

                    </div>
                    <div class="row">
                        <form-select v-model="item.location_id" :schema="schema.form.location_id" class="col-12 col-md-4 mt-1 mb-1" />
                        <form-search v-if="((item.referred_by && referred_customer) || (item.referred_by===null))" v-model="item.referred_by" :defaultOption="referred_customer" :schema="schema.form.referred_by" class="col-12 col-md-4 mt-1 mb-1" /><!--<form-select v-model="item.referred_by" :schema="schema.form.referred_by" class="col-12 col-md-4 mt-1 mb-1" />-->
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-8">
                            <form-textarea v-model="item.comments" :schema="schema.form.comments" :rows="schema.form.comments.rows || 4" />
                        </div>

                    </div>
                </b-tab>
                <b-tab title="Medical" :style="{minHeight: tabMinHeight + 'px'}">
                    <div class="row">
                        <div v-if="!editLicense.mmj" @click="showLicenseField('mmj')" class="col-12 col-md-4 mt-1 mb-1">
                            <form-text disabled value="Click to Update License" :schema="schema.form.mmj_card" class="hidden-license" />
                        </div>
                        <form-text ref="license" v-if="editLicense.mmj" v-model="item.update_mmj_card" :schema="schema.form.mmj_card" class="col-12 col-md-4 mt-1 mb-1" />

                        <form-select v-model="item.mmj_card_state" :schema="schema.form.mmj_card_state" class="col-12 col-md-4 mt-1 mb-1" />
                        <form-datetime v-model="item.mmj_card_expiry_date" :schema="schema.form.mmj_card_expiry_date" class="col-12 col-md-4 mt-1 mb-1" />
                    </div>

                    <div class="row">
                        <form-simpleobject v-model="item.settings" :formData="item" :schema="schema.form.settings" :hideLabel="false" label-class="col-6" field-class="col-12" class="col-12 col-lg-8 mt-1 mb-1" />
                    </div>
                    <div class="row">
                        <form-boolean :declared="item.tax_exempt" :schema="schema.form.tax_exempt" @input="(upd) => {item.tax_exempt = upd}" class="col-12 col-md-6  mt-1 mb-1" />
                    </div>
                </b-tab>
            </b-tabs>
            <div class="col-12 clearfix mt-3">
                <div class="drsection-content">
                    <auto-save type="save" :state="isCreate && itemState==='save changes' ? 'create' : itemState" @autoSave="autoSave($event, true)"></auto-save>
                    <a @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                    <form-button v-if="!isCreate" :disabled="isArchived || !!(item.sales.length)" @click="confirmDelete" text="Archive" show-disabled disabled-title="Archiving Unavailable" :disabled-text="isArchived ? 'Customer Already Archived.' : 'You cannot archive customer with sales history.'"/>
                </div>
            </div>


        </fieldset>
    </form>
        <loading :display="isLoading" type="loadModal"></loading>
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
    </div>
</template>

<script>
    import Item from '../../../../models/Customer';
    import FormRadio from '../../../elements/FormRadio';
    import _ from 'lodash';

    export default {
        props: {
            id: {
                type: [Number, String],
                default: 0
            },
            model: {
                type: String,
                default: 'Customer'
            },
            module: {
                type: String,
                default: 'administration',
            }
        },

        data(){
            return {
                item: null,
                itemState: 'save',
                gettingSmurf: false,
                tabMinHeight: 0,
                editLicense: {
                    drivers: false,
                    mmj: false
                },
                updateDriversLicense: null,
                updateMmjLicense: null,
                referred_customer: null, //async load of referral customer to show name in drop-down
                isLoading: false,
            };
        },

        components : {
            FormRadio
        },

        mounted() {
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema);

                    if (this.item.referred_by) {
                        Item.find(this.item.referred_by).then(response=> {
                            this.referred_customer = new Item(response).withDefaults(this.schema);
                        });
                    }
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
            serviceFromRegistry(cid){
                this.isLoading=true;
                axios.get('/api/v1/admin/dispensary/customersqueue/service/'+cid).then(response =>{
                    this.isLoading=false;
                    this.$router.push({name:'terminal-order',params:{customer_id:cid}}); // go to order form with customer_id
                }).catch(error => {
                    this.isLoading=false;
                    this.$announcer(error.response);
                });
            },

            getSmurfName(){
                this.gettingSmurf = true;
                axios.get('/api/v1/admin/dispensary/customers/smurfname').then(response =>{
                    this.item.alias = (response.data || {}).smurf;
                    this.gettingSmurf = false;
                }).catch(error => {
                    //
                    this.gettingSmurf = false;
                });
            },

            autoSave(event, confirm=false){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                if (event && event.target && event.target.name==='file') return false; //don't autosave when we are making changes to the file upload fields in the background

                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) {this.saveDebounce(); this.itemState = 'saving..';}
                        else this._save(true);
                    }else if(confirm==true){
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    }else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
            },

            saveDebounce: _.debounce(function() {
                this._save();
            }, 2000),

            _save(confirm=false){
                // only send updated license if user typed into field. If they edited and removed, then remove from item
                if (!this.item.update_drivers_license) delete this.item.update_drivers_license;
                if (!this.item.update_mmj_card)  delete this.item.update_mmj_card;

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

            async showLicenseField(licenseType) {
                let withPin = this.isCreate ? true : await this.requirePin('Please Enter an Admin PIN to modify this Item'); // no pin needed if create new customer
                if(withPin===false) return false;

                this.editLicense[licenseType]=true;
                setTimeout(() => { //nextTick is too fast for this.  Need to delay longer to make sure everything is set
                    if (this.$refs.license.$refs.drivers_license) this.$refs.license.$refs.drivers_license.focus();
                    if (this.$refs.license.$refs.mmj_card) this.$refs.license.$refs.mmj_card.focus();
                }, 150);

            },

            confirmDelete(){
                this.$swal.fire({
                    title: 'Are you sure?',
                    text: 'This will Archive this customer?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Archive Customer'
                }).then((result) => {
                    if(result.value){
                        this.isLoading = true;
                        this.item.delete().then(response => {
                            this.isLoading = false;
                            this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // get schema for new agg data
                            this.$announcer(response);
                            this.$router.push({name:'customer'});
                        }).catch(error => {
                            this.isLoading = false;
                            this.$announcer(error.response);
                        });
                    }
                });
            },
        },

        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            isCreate() {
                return !((this.item||{}).id);
            },
            isArchived() {
                return !!(this.item.archived_at);
            },
        },

        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                    if (newVal && !oldVal) {
                        this.$nextTick(() => {
                            this.tabMinHeight = this.$refs.tabContact.$el.clientHeight; //sets ui height of each tab content the same
                        });
                    }
                },
                deep: true
            },
            'item.referred_by'(newValue, oldValue) {
                if ((oldValue===null && newValue) || (oldValue && newValue)) {
                    this.referred_customer={}; //set to not null so they drop-down field stays visible (but we don't need a 'default' value for the drop-down at this point)
                }
            },
            'item.type'(to,from){
                if(!this.item) return false;
                if(to=='wholesale'){
                    this.item.last_name = null;
                    this.item.alias = null;
                    this.item.settings.med_carry_weight = 1000000;
                }else if(to!='patient') this.item.settings.med_carry_weight = null;
            }
        }

    };

</script>

<style scoped>
    .vdp-datepicker__calendar {
        left:0;
    }
    .simple-label {
        white-space: nowrap;
    }
    .hidden-license input {
        cursor: pointer;
    }
    .btn.customer-action-button {
        color: white;
        position: absolute;
        right: 0;
        top: 0;
    }
    @media (max-width: 768px) {
        .btn.customer-action-button {
            display: none;
        }
    }
</style>
