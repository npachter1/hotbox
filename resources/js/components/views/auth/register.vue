<template>
    <section id="wrapper">
        <div class="login-register" style="background-image:url(/images/background/city_4.jpg);">
            <div class="login-box card">
            <div class="card-body">
                <form class="form-horizontal form-material" id="registerform" @submit.prevent="submit">
                    <h3 class="mt-0 mb-4"><span class="small">Suite Registration</span><br>{{ $store.state.disp.name }}</h3>
                    <div class="form-group mb-3">
                        <div class="col-xs-6">
                                <input class="form-control"
                                    v-model="registerForm.company_name"
                                    type="text"
                                    name="company_name"
                                    placeholder="Suite Name (Business)"
                                    :class="{'val-danger-input': errors.has('company_name') }"
                                    v-validate="'required'">
                                <span v-show="errors.has('company_name')" class="form-text text-muted val-danger-text">{{ errors.first('company_name') }}</span>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-6">
                                <input class="form-control"
                                    v-model="registerForm.name"
                                    type="text"
                                    name="name"
                                    placeholder="Your Name"
                                    :class="{'val-danger-input': errors.has('name') }"
                                    v-validate="'required'">
                                <span v-show="errors.has('name')" class="form-text text-muted val-danger-text">{{ errors.first('name') }}</span>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                                <input class="form-control"
                                    v-model="registerForm.email"
                                    type="text"
                                    name="email"
                                    placeholder="User Email"
                                    :class="{'val-danger-input': errors.has('email') }"
                                    v-validate="'required|email'">
                                <span v-show="errors.has('email')" class="form-text text-muted val-danger-text">{{ errors.first('email') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                                    <input class="form-control"
                                        v-model="registerForm.password"
                                        type="password" 
                                        name="password" 
                                        :class="{'val-danger-input': errors.has('password') }"
                                        placeholder="Password" 
                                        v-validate="'required'"
                                        ref="password">
                                        <span v-show="errors.has('password')" class="form-text text-muted val-danger-text">{{ errors.first('password') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                                <input class="form-control"
                                        v-model="registerForm.password_confirmation"
                                        type="password" 
                                        name="password_confirmation" 
                                        :class="{'val-danger-input': errors.has('password') }"
                                        placeholder="Confirm Password*" 
                                        :data-vv-as="'password'"
                                        v-validate="'required|confirmed:password'">
                                <span v-show="errors.has('password_confirmation')" class="form-text text-muted val-danger-text">{{ errors.first('password_confirmation') }}</span>
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit" :disabled="isRegistering">
                                <i v-if="isRegistering" class="far fa-circle-notch fa-spin"></i> Register New Suite
                            </button>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Already have an account? <router-link to="/login" class="text-info m-l-5"><b>Sign In</b></router-link></p>
                        </div>
                    </div>
                </form>

            </div>
            <guest-footer></guest-footer>
          </div>
        </div>

    </section>
</template>

<script>

    import moment from 'moment-timezone';
    import GuestFooter from '../../layouts/guest-footer.vue';

    export default {
        data() {
            return {
                registerForm: {
                    email: null,
                    password: null,
                    password_confirmation: null,
                    name: null,
                    company_name: null,
                    timezone:moment.tz.guess()
                },
                isRegistering:false
            };
        },
        components: {
            GuestFooter
        },
        
        mounted(){
            
        },
        
        computed:{

        },
        
        methods: {
            submit(e){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.isRegistering = true;
                        axios.post('/api/v1/auth/register', this.registerForm).then(response =>  {
                            this.isRegistering = false;
                            this.$announcer(response);
                            if(response.data.access_token){
                                localStorage.setItem('access_token',response.data.access_token);
                                axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('access_token');
                                this.$router.push('/admin/dashboard');
                            }
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
