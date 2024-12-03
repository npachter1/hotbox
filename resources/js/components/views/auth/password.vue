<template>
    <section id="wrapper">
        <div class="login-register" style="background-image:url(/images/background/city_5.jpg);">
            <div class="login-box card">
            <div class="card-body">
                <form class="form-horizontal form-material" id="passwordform" @submit.prevent="submit">
                    <h3 class="box-title m-b-20">Reset Password</h3>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control"
                                v-model="passwordForm.email"
                                type="text"
                                name="email"
                                placeholder="Enter UserName/Email"
                                :class="{'val-danger-input': errors.has('email') }"
                                v-validate="'required|email'">
                            <span v-show="errors.has('email')" class="form-text text-muted val-danger-text">{{ errors.first('email') }}</span>        
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset Password</button>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Back to Login? <router-link to="/login" class="text-info m-l-5"><b>Sign In</b></router-link></p>
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
    import GuestFooter from '../../layouts/guest-footer.vue';

    export default {
        data() {
            return {
                passwordForm: {
                    email: ''
                }
            }
        },
        components: {
            GuestFooter
        },
        mounted(){
        },
        methods: {
            submit(e){
                axios.post('/api/v1/auth/password', this.passwordForm).then(response =>  {
                    this.$announcer(response);
                    this.$router.push('/login');
                }).catch(error => {
                    this.$announcer(error.response);
                });
            }
        }
    }
</script>
