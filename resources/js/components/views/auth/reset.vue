<template>
    <section id="wrapper">
        <div class="login-register" style="background-image:url(/images/background/city_2.jpg);">
            <div class="login-box card">
            <div class="card-body">
                <h3 class="box-title m-b-20 text-center">Reset Password</h3>
                <div v-if="status">
                    <form class="form-horizontal form-material" id="resetform" @submit.prevent="submit">
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input class="form-control"
                                    v-model="resetForm.email"
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
                                        v-model="resetForm.password"
                                        type="password" 
                                        name="password" 
                                        :class="{'val-danger-input': errors.has('password') }"
                                        placeholder="New Password" 
                                        v-validate="'required'"
                                        ref="password">
                                        <span v-show="errors.has('password')" class="form-text text-muted val-danger-text">{{ errors.first('password') }}</span>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control"
                                        v-model="resetForm.password_confirmation"
                                        type="password" 
                                        name="password_confirmation" 
                                        :class="{'val-danger-input': errors.has('password') }"
                                        placeholder="Confirm New Password*" 
                                        :data-vv-as="'password'"
                                        v-validate="'required|confirmed:password'">
                                <span v-show="errors.has('password_confirmation')" class="form-text text-muted val-danger-text">{{ errors.first('password_confirmation') }}</span>
                            </div>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button class="btn btn-warning btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset Password</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div v-else class="text-center">
                    <h4 v-text="message" class="alert alert-danger" v-if="!status"></h4>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                    <p>Back to Login? <router-link to="/login" class="text-info m-l-5"><b>Sign In</b></router-link></p>
                    </div>
                </div>
            </div>
            <guest-footer></guest-footer>
          </div>
        </div>

    </section>
</template>

<script>
    import GuestFooter from '../../layouts/guest-footer.vue'
    export default {
        data() {
            return {
                resetForm: {
                    email: null,
                    password: null,
                    password_confirmation: null,
                    token:this.$route.params.token,
                },
                message: '',
                status: true
            }
        },
        components: {
            GuestFooter
        },
        mounted(){
            axios.post('/api/v1/auth/validate-password-reset',{token: this.$route.params.token}).then(response => {
                this.status = true;
                this.message = response.data.message;
            }).catch(error => {
                this.status = false;
                this.message = error.response.data.message;
            });
        },
        methods: {
            submit(e){
                axios.post('/api/v1/auth/reset', this.resetForm).then(response =>  {
                    this.$announcer(response);
                    this.$router.push('/login');
                }).catch(error => {
                    this.$announcer(error.response);
                });
            }
        }
    }
</script>
