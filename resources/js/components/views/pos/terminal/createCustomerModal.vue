<template>
    <div v-if="customer && schema" class="col-12" @paste="capturePaste">
      <loading :display="isLoading" type="loadGrid" />
      <form class="modal-form">
        <fieldset>
          <div class="row">
            <div class="form-group col-12 mt-1 mb-1">
              <label class="w-100">
                Scan (or key-in) License Number
                <span class="float-right small">
                  <form-radio v-model="licenseType" label="drivers" :inline="true">Driver's License</form-radio>
                  <form-radio v-model="licenseType" label="mmj" :inline="true">Medical Marijuana Card</form-radio> 
                </span>
              </label>
              <input type="text" autofocus
                     style="height:50px;"
                     class="form-control"
                     placeholder=""
                     v-model="license"
                     ref="licenseScan"
                     data-field="license"
                     >
            </div>
            
            <transition name="hb-fade">
              <form-simpleobject v-if="licenseType=='mmj'" v-model="customer.settings" :formData="customer" :schema="schema.form.settings" class="col-12 mt-1 mb-3" />
            </transition>
          </div>
      
          <div class="row">
            <div class="col-12 col-sm-6 form-group has-label mt-1 mb-3">
              <label>Birthday <span v-if="customerAge">(Currently {{ customerAge }} years old.)</span></label>
              <birthday-input
                  :min-age="legalAgeLimit"
                  :reset-flag="resetBirthdayFlag"
                  :default-value="customer.birthdate"
                  @birthdayInputChangeValue="setBirthday">
              </birthday-input>
            </div>
            
            <form-select is-live v-model="licenseState" :schema="schema.form.drivers_license_state" class="col-12 col-sm-6 mt-1 mb-3" />
          </div>
      
          <div class="row">
            <div class="w-100 mt-2 pr-4 text-right show-gridsnap small">
              <form-radio v-model="nameType" label="name" :inline="true">Customer Full Name</form-radio>
              <form-radio v-model="nameType" label="wholesale" :inline="true">Wholesale Account</form-radio>
              <form-radio v-model="nameType" label="smurf" :inline="true">Smurf Alias</form-radio>
            </div>
            <form-text v-if="nameType=='name'" v-model="customer.first_name" :schema="schema.form.first_name" class="col-12 col-sm-6 mt-0 mb-2" />
            <form-text v-if="nameType=='name'" v-model="customer.last_name" :schema="schema.form.last_name" class="col-12 col-sm-6 mt-0 mb-2" />
            
            <form-text v-if="nameType=='wholesale'" v-model="customer.first_name" :schema="schema.form.company_name" class="col-12 col-sm-6 mt-0 mb-2" />
            <form-text v-if="nameType=='wholesale'" v-model="customer.settings.wholesale_license_id" :schema="schema.form.settings.properties.wholesale_license_id" class="col-12 col-sm-6 mt-1 mb-1" />
            
            <transition name="hb-fade">
                <form-text v-if="nameType=='smurf'" v-model="customer.alias" :schema="schema.form.alias" class="col-12 col-sm-12 mt-0 mb-2">
                  <template slot="title-right">
                    <a href="" class="float-right" @click.prevent="getSmurfName()">
                      <spinner :isProcessing="gettingSmurf" :isFullScreen="false" :isLine="true" :spinnerWidth="21" class="float-left" /> 
                      <i class="hotbox-icon hotbox-icon-bulb-62"></i> Generate Smurf..</a>
                  </template>
                </form-text>
            </transition>
          </div>
          
          <div class="row">
            <form-text v-model="customer.address.email" :schema="schema.form.email" class="col-12 col-sm-6 mt-1 mb-1" />
            <form-text v-model="customer.address.phone" :schema="schema.form.phone" class="col-12 col-sm-6 mt-1 mb-2" />
            
            <form-boolean v-if="customer.address.phone" :declared="customer.sms_optin" :schema="schema.form.sms_optin" @input="(upd) => {customer.sms_optin = upd}" class="col-12 mt-2 mb-1" />
            <form-boolean v-if="customer.address.email" :declared="customer.email_optin" :schema="schema.form.email_optin" @input="(upd) => {customer.email_optin = upd}" class="col-12 mt-3 mb-2" />
            <form-multiselect v-if="customer.address.phone || customer.address.email" v-model="customer.preferences" :schema="schema.form.preferences" class="col-12 mt-1 mb-3" />
          
            <form-select v-model="customer.settings.referral" :schema="schema.form.settings.properties.referral" class="col-12 col-sm-12 mt-1 mb-2" />
            <transition name="hb-slide">
              <form-search v-if="customer.settings.referral=='referral'" v-model="customer.referred_by" :schema="schema.form.referred_by" class="col-12 col-sm-12 mt-1 mb-3" />
            </transition>
          </div>
          
          
          <div class="col-12 clearfix mt-3 text-center">

              <a v-if="type=='form'" @click.default="$router.go(-1)" class="btn btn-lg btn-light">Return.</a> 
              <a v-else @click.default="cancel()" class="btn btn-lg btn-light">Cancel</a> 
              <button type="button" class="btn btn-lg btn-info" :class="customerCanCreate ? '' : 'disabled'" :disabled="isProcessing" @click="autoSave()">
                 <spinner :isProcessing="isProcessing" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> 
                <i class="hotbox-icon hotbox-icon-floppy-disk"></i>
                Save & Queue
              </button>
          </div>

        </fieldset>
      </form>
    </div>
    <div v-else>
      <loading :display="(schema && customer) ? false : true" type="loadPage" />
    </div>
</template>
<script>

    import Customer from '../../../../models/Customer';
    import BirthdayInput from "../../../elements/BirthdayInput";
    import FormRadio from '../../../elements/FormRadio';
    import _ from 'lodash';
    import { parse as barcode } from '../../../../../plugins/parse-usdl/parseUsdl';
    // import { testCodes } from '../../../../../plugins/parse-usdl/testCodes';
    import { stripe } from '../../../../../plugins/aamvajs/index';
    export default {
      props: {
            type: {
                type: [String,Number],
                default: 'modal'                                                // form or modal, which routes to grid or just emits result
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
          isLoading:false,
          isProcessing:false,
          customer: null,
          itemState: 'save',
          license:null,
          licenseType: 'drivers',
          licenseState: 'CO',
          customerAge: null,
          nameType:'name',
          resetBirthdayFlag: false,
          gettingSmurf:false
        };
      },
        
      components : {
        BirthdayInput,FormRadio
      },

      mounted() {
        this.isLoading = true;
        this.customer = new Customer().withDefaults(this.schema);
        this.isLoading = false;
        setTimeout(() => { //nextTick is too fast for this.  Need to delay longer to make sure everything is set
          this.$refs.licenseScan.focus();
        }, 10);
      },

      created() {
        this.$barcodeScanner.init(this.onBarcodeScanned);
      },

      destroyed() {
        this.$barcodeScanner.destroy(); // Remove listener when component is destroyed
      },

    	computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            },
            customerCanCreate: function () {
                return (this.customerAge >= this.legalAgeLimit && this.license) ? true : false;
            },
            legalAgeLimit: function () {
                return Number(this.$store.state.settings.legal.age_limit);
            },
    	},
    	
      watch: {
        licenseType() {
          this.$nextTick(()=> {
            this.$refs.licenseScan.focus();
          });
        },
            'customer.type'(to,from){
                if(!this.customer) return false;
                if(to=='wholesale'){
                    this.customer.last_name = null;
                    this.customer.alias = null;
                    this.customer.settings.med_carry_weight = 1000000;
                }else this.customer.settings.med_carry_weight = null;
            }
      },
    	
    	methods: {
          // testCodes() {
          //   testCodes();
          // },
          onBarcodeScanned(code) {
             //console.log('barcode scan:' + code);
            let data = barcode(code, {suppressErrors: true}); //right now just parse for US drivers license
            if (_.isEmpty(data)) data = stripe(code); //if pdf417 doesn't work, try a magnetic stripe parse

            if (!_.isEmpty(data)) {
              // console.log('valid scan!');
              this.$announcer({status:200,timeout:1500,data:{message:'Scan Complete'}});
              this.license='';

              if (data.addressState) this.licenseState=data.addressState;
              if (data.firstName) this.customer.first_name=data.firstName;
              if (data.lastName) this.customer.last_name=data.lastName;
              if (data.documentNumber) this.license=data.documentNumber;
              if (data.dateOfBirth) {
                // const dob = new moment(data.dateOfBirth,'YYYY-MM-DD');
                this.customer.birthdate = data.dateOfBirth.format('YYYY-MM-DD');
                this.customerAge = moment().diff(data.dateOfBirth,'years');
              }
              if (data.dateOfExpiry) this.customer.drivers_license_expiry_date = data.dateOfExpiry.format('YYYY-MM-DD');
              this.licenseType='drivers';
              if (data.sex) this.customer.gender=data.sex;

              //address
              this.customer.address.name=(this.customer.first_name + ' ' + this.customer.last_name).trim();
              if (data.addressStreet) this.customer.address.address1=data.addressStreet;
              if (data.addressStreet2) this.customer.address.address2=data.addressStreet2;
              if (data.addressCity) this.customer.address.city=data.addressCity;
              if (data.addressPostalCode) this.customer.address.zip=data.addressPostalCode;
              if (data.addressState) this.customer.address.region=data.addressState;
              if (data.country) this.customer.address.country=data.country==='USA' ? 'US' : data.country;
            }
          },

          capturePaste(e) {
            const code = e.clipboardData.getData('text/plain');
            let data = barcode(code, {suppressErrors: true}); //right now just parse for US drivers license
            if (_.isEmpty(data)) data = stripe(code); //if pdf417 doesn't work, try a magnetic stripe parse

            if (!_.isEmpty(data)) {
              if (data.addressState) this.licenseState=data.addressState;
              if (data.firstName) this.customer.first_name=data.firstName;
              if (data.lastName) this.customer.last_name=data.lastName;
              if (data.documentNumber) this.license=data.documentNumber;
              if (data.dateOfBirth) {
                // const dob = new moment(data.dateOfBirth,'YYYY-MM-DD');
                this.customer.birthdate = data.dateOfBirth.format('YYYY-MM-DD');
                this.customerAge = moment().diff(data.dateOfBirth,'years');
              }
              if (data.dateOfExpiry) this.customer.drivers_license_expiry_date = data.dateOfExpiry.format('YYYY-MM-DD');
              this.licenseType='drivers';
              if (data.sex) this.customer.gender=data.sex;

              //address
              this.customer.address.name=(this.customer.first_name + ' ' + this.customer.last_name).trim();
              if (data.addressStreet) this.customer.address.address1=data.addressStreet;
              if (data.addressStreet2) this.customer.address.address2=data.addressStreet2;
              if (data.addressCity) this.customer.address.city=data.addressCity;
              if (data.addressPostalCode) this.customer.address.zip=data.addressPostalCode;
              if (data.addressState) this.customer.address.region=data.addressState;
              if (data.country) this.customer.address.country=data.country==='USA' ? 'US' : data.country;
              e.preventDefault();
            }
          },

        autoSave(confirm=true){
          this.$validator.validateAll().then((result) => {
            if(result){
                this.itemState = 'saving..';
                this.isProcessing = true;
                
                this.customer.alias = (this.nameType=='smurf') ? this.customer.alias : null;
                this.customer.first_name = (this.nameType=='smurf') ? null : this.customer.first_name;
                this.customer.last_name = (this.nameType=='smurf') ? null : this.customer.last_name;
                this.customer.type = (this.nameType=='wholesale') ? 'wholesale' : (this.licenseType=='mmj') ? 'patient' : 'recreational';

                this.customer.birthdate = (this.customer.birthdate instanceof Date) ? (new moment(this.customer.birthdate)).format('YYYY-MM-DD') : this.customer.birthdate;
                this.customer.drivers_license_expiry_date = (this.customer.drivers_license_expiry_date instanceof Date) ? (new moment(this.customer.drivers_license_expiry_date)).format('YYYY-MM-DD')  : this.customer.drivers_license_expiry_date;

                this.customer.mmj_card = (this.licenseType=='mmj') ? this.license : null;
                this.customer.mmj_card_state = (this.licenseType=='mmj') ? this.licenseState : null;
                this.customer.drivers_license = (this.licenseType=='drivers') ? this.license : null;
                this.customer.drivers_license_state = (this.licenseType=='drivers') ? this.licenseState : null;
                
                this.customer.address.region = this.licenseState;
                this.customer.address.type = 'consumer';
                this.customer.address.name = this.customer.alias || this.customer.first_name+' '+this.customer.last_name;
                
                if(this.customer.type=='wholesale'){
                  this.customer.settings.med_carry_weight = 1000000;            // Assume a virturally unlimited carry weight
                }
                
                this.customer.save().then(async response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'New Customer has been successfully registered and is being added to the Queue..'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        
                        if(this.type=='modal') this.$emit('add',response);      // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
                        else this.$router.push({name:this.model.toLowerCase()});
                    }
                    this.isProcessing = false;
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.isProcessing = false;
                    this.itemState = 'resave';
                });
            }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
          });
        },
        
        getSmurfName(){
          this.gettingSmurf = true;
          axios.get('/api/v1/admin/dispensary/customers/smurfname').then(response =>{
            this.customer.alias = (response.data || {}).smurf;
            this.gettingSmurf = false;
          }).catch(error => {
            //
            this.gettingSmurf = false;
          });
        },
        
        setBirthday: function (value, age) {
          this.customer.birthdate = (value instanceof Date) ? (new moment(value)).format('YYYY-MM-DD') : value;
          //this.customer.birthdate = value;
          this.customerAge = age;
        },

        cancel: function() {
          this.$emit('cancel');
        }

    	},
    	
    	inject: ['$validator']
    };
</script>

<style scoped>
  
</style>
