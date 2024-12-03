<template>
    <section id="wrapper">

        <div class="wrapper wrapper-full-page">
            <div class="full-page section-image login-page" filter-color="black" data-image="/imgages/bg.jpg">
                <div class="content pos-auth-content">
                    <div class="container">

                        <div class="col-md-4 ml-auto mr-auto">
                            <form class="form-horizontal" id="loginform" @submit.prevent="submit">
                                <fieldset>

                                    <div class="card card-login card-plain">
                                        <div class="card-header">
                                            <div class="logo-container">
                                                <img src="/images/logo.png" alt="">
                                            </div>
                                        </div>
                                        <div class="card-body ">
                                            <div class="form-group ">
                                                <div class="col-xs-12">
                                                    <input v-model="loginForm.email"
                                                           name="email"
                                                           type="text"
                                                           class="form-control"
                                                           placeholder="Username/Email"
                                                           :class="{'input': true, 'val-danger-input': errors.has('email') }"
                                                           v-validate="'required'">
                                                    <span v-show="errors.has('email')"
                                                          class="form-text text-muted val-danger-text">{{ errors.first('email') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <input v-model="loginForm.password"
                                                           type="password"
                                                           name="password"
                                                           class="form-control"
                                                           placeholder="Password"
                                                           :class="{'val-danger-input': errors.has('password') }"
                                                           v-validate="'required'">
                                                    <span v-show="errors.has('password')"
                                                          class="form-text text-muted val-danger-text">{{ errors.first('password') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer ">
                                            <button type="submit" class="btn btn-primary btn-round btn-block mb-3">
                                                Login
                                            </button>
                                            <guest-footer></guest-footer>
                                        </div>

                                    </div>
                                </fieldset>
                            </form>

                        </div>
                    </div>
                </div>

                <div class="full-page-background" style="background-image: url(/images/bg.jpg)"></div>
            </div>
        </div>

        <b-modal
                v-if="status==='pending_activation'"
                :visible="true"
                centered ref="loyaltyTriggersModal"
                size="lg"
                header-bg-variant="light"
                header-text-variant="primary">

            <template slot="modal-header">
                <h5 class="w-100 mb-0 text-center">Pending Activation</h5>
            </template>

            <div>You cannot login to your account until you have been activated.</div>
            <div style="margin:10px 0;">Check your inbox for your activation email. Alternatively, click below to email your site admin to request manual activation:</div>

            <span class="btn-label btn-sm btn-light float-right button" @click="requestReactivate">Request Manual Activation</span>

            <template slot="modal-footer">
                <span class="btn-label btn-sm btn-light float-right button" @click="status=''">Close</span>
            </template>
        </b-modal>
    </section>
</template>

<script>

    import GuestFooter from '../../layouts/guest-footer.vue';

    export default {

        data() {
            return {
                loginForm: {
                    email: 'john.doe@example.com',
                    password: 'abcd1234'
                },
                status: ''
            };
        },

        components: {
            GuestFooter
        },

        mounted() {

            let locationId = (this.$store.state.disp.location) ? this.$store.state.disp.location : 'XXXXXX';
            axios.get('/api/v1/auth/clearLocation/' + locationId).then(response => {
                localStorage.removeItem('access_token');
                axios.defaults.headers.common['Authorization'] = null;
                this.$store.dispatch('clearAuthLocation');
            }).catch(error => {
                //
            });
        },

        methods: {
            submit(e) {
                axios.post('/api/v1/auth/login', this.loginForm)
                    .then(response => {
                        localStorage.setItem('access_token', response.data.access_token);
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('access_token');
                        this.$announcer(response);
                        this.$router.push('/admin/dashboard'); // this will go to dashboard - where the auth location suer store will be refreshed!
                    })
                    .catch(error => {
                        if (error.response.status === 422 && error.response.data.message.indexOf('activate account') > 0) { // would prefer to have json response or some other unique status code to figure this out
                            //debugger;
                             this.status = 'pending_activation';
                        } else {
                            this.$announcer(error.response);
                        }
                    });
            },

            requestReactivate() {
                axios.post('/api/v1/auth/reactivateRequest', this.loginForm) //mimics the password reset process
                    .then(response => {
                        this.status = '';
                        this.$announcer(response);
                    })
                    .catch(error => {
                        this.status = '';
                        this.$announcer(error.response);
                    });
            }
        }
    };
</script>

<style scoped>
    .login-logo {
        margin: 4% auto !important;
        width: 65% !important;
        display: block !important;
    }
    .button {
        cursor: pointer;
    }
</style>