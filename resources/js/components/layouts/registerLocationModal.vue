<template>
    <section id="wrapper">
        <div v-if="schema" class="col-12">
        
            <form class="modal-form" id="registerform" @submit.prevent="submit">
                <fieldset>

                    <div class="row">
                        <form-text v-model="registerForm.name" :schema="schema.form.name" class="col-12 col-sm-12 mt-2 mb-2" />
                        <form-text v-model="registerForm.email" :schema="schema.form.settings.sections.communication.properties.communication_email" class="col-12 col-sm-12 mt-2 mb-2" />
                        <form-select v-model="registerForm.type" :schema="schema.form.type" class="col-12 col-sm-12 mt-2 mb-3" />
                        
                        <form-text v-model="registerForm.address.address1" :schema="{name:'address1',title:'Business Street'}" class="col-12 col-sm-12 mt-3"></form-text>
                        <form-text v-model="registerForm.address.city" :schema="{name:'city',title:'City'}" class="col-12 col-sm-6 mt-2 mb-2 clearfix"></form-text>
                        <form-text v-model="registerForm.address.county" :schema="{name:'city',title:'County'}" class="col-12 col-sm-6 mt-2 mb-2"></form-text>
                        <form-select v-model="registerForm.address.region" :schema="{name:'region',title:'State',values:[]}" scopeData="US" class="col-12 col-sm-6 mt-2 mb-2 clearfix"/>
                        <form-text v-model="registerForm.address.zip" :schema="{name:'zip',title:'Zip'}" class="col-12 col-sm-6 mt-2 mb-2"></form-text>
                            
                        <form-text v-model="registerForm.licensenum" :schema="schema.form.licensenum" class="col-12 col-sm-12 mt-3 mb-1"/>
                        <form-boolean :declared="registerForm.is_medical" :schema="{name:'is_medical',title:'is Medical'}" @input="(upd) => {registerForm.is_medical = upd}" class="col-12 mt-2 mb-2" />
                    </div>

                    <div class="col-12 form-group text-center m-t-20">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit" :disabled="isRegistering">
                            <spinner :isProcessing="isRegistering" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> Register New Location
                        </button>
                    </div>
                    
                </fieldset>
            </form>

        </div>
        <loading :display="!schema" type="loadModal" />
    </section>
</template>

<script>


    export default {
        data() {
            return {
                registerForm: {
                    email: null,
                    name: null,
                    licensenum: null,
                    type:'dispensary',
                    is_medical:false,
                    address:{
                        address1:null,
                        city:null,
                        county:null,
                        region:null,
                        zip:null
                    }
                },
                isRegistering:false
            };
        },
        components: {

        },
        
        async mounted(){
            await this.$store.dispatch('auth/setSchemas','location');           // since we are calling this from another app modules, we need to load a fresh schema first.
        },
        
        computed:{
            schema() {
                return this.$store.state['auth']['locationSchema'];
            },
            email(){
                (this.schema) ? this.schema.model.settings.communication_email : null;
            }
        },
        
        watch:{
            email(to,from){
                if(to) this.registerForm.email = to;
            }
        },
        
        methods: {
            submit(e){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.isRegistering = true;
                        axios.post('/api/v1/admin/auth/registerLocation', this.registerForm).then(response =>  {
                            this.isRegistering = false;
                            this.$announcer(response);
                            this.$router.push({name:'location'});
                            this.$emit('jump',response.data.location_id);
                        }).catch(error => {
                            this.isRegistering = false;
                            this.$announcer(error.response);
                        });
                    }
                });
            },
            
        }
    }
</script>
