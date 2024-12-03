<template>
        <div v-if="item && schema" class="col-12">

            <form class="modal-form">
                <fieldset>


            <div class="nav-tabs-header mb-4">
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="nav-link" :class="{'active':tab=='location'}">
                        <a href="" class="" @click.prevent="tab='location'"><i class="fal fa-hotel"></i> Status</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='links'}">
                        <a href="" class="" @click.prevent="tab='links'"><i class="fal fa-link"></i> Links</a>
                    </li>
                    
                </ul>
            </div>
            
            <transition name="bo-slide">
                <div v-if="tab=='location'" class="">

                    <form-simpleselect v-model="form.update_status" :schema="schema.form.status" />
                    <form-textarea v-if="form.update_status!=item.status" v-model="form.update_message" :schema="{name:'update_message',title:'Provide an (optional) Client Message'}"></form-textarea>
                    
                    <div class="d-sm-flex mt-2 mb-3 justify-content-center">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave('status')"></auto-save>
                    </div>
                </div>
            </transition>

            <transition name="bo-slide">
                <div v-if="tab=='links'" class="">

                    <div class="col-12 mt-0 mb-3">
                        <h3>User Access to this location:</h3>
                        <form-boolean v-for="(user,uid) in item.users_to_link" 
                            :key="uid" 
                            :declared="inSet(user.id,form.update_user_ids)" 
                            :schema="{name:'link_user_'+user.id,title:user.email}" 
                            @input="(upd) => { toggleLink(user.id,upd,'update_user_ids') }">
                        </form-boolean>
                    </div>

                    <div class="d-sm-flex mt-2 mt-2 mb-3 justify-content-center">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave('links')"></auto-save>
                    </div>
                </div>
            </transition>

                </fieldset>
            </form>

        </div>
        <div class="no-content-block" v-else>
            <loading :display="(item && schema) ? false : true" type="loadModal" />
        </div>
</template>

<script>

    import Item from '../../../../models/Locations';


    export default {
        props: {
            model: {
                type: String,
                default: 'Locations'
            },
            module: {
                type: String,
                default: 'auth',
            },
            id: {
                type: [String,Number],
                default: null
            },
            type: {
                type: String,
                default: 'location'
            }
        },
        
        data(){
            return {
                item: null,
                tab: this.type,
                itemState: 'update',
                isLoading: false,
                isDownloading: false,
                subSel:null,
                chargeSel:null,
                form: {
                    update_status:null,
                    update_message:null,
                    update_subscription:null,
                    update_subscription_plan:null,
                    update_subscription_coupon:null,
                    update_subscription_quantity:0,
                    update_subscription_trialdays:0,
                    update_subscription_action:'update',
                    update_transaction:null,
                    update_transaction_amount:0,
                    update_transaction_descriptor:null,
                    update_balance_amount:0,
                    update_balance_descriptor:null,
                    update_user_ids:[],
                    update_app_ids:[]
                },
            };
        },
        
        components : {

        },
        
        async mounted() {
            this.isLoading = true;
            await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // since we are calling this from another app module, we need to load a fresh schema first.
            Item.find(this.id).then(response => {
                this.item = new Item(response);
                this.form.update_status = this.item.status;
                this.isLoading = false;
            }).catch(error => {
                this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                this.$emit('refresh');
            });
        },
        
        methods: {
            autoSave(typ='status',act='update'){
                
                this.$swal.fire({
                  title: 'Please Confirm Modification',
                  input:'password',
                  inputValue:null,
                  inputAttributes: {
                    maxlength: 4,
                    autocapitalize: 'off',
                    autocorrect: 'off'  
                  },
                  inputPlaceholder:'Input AdminPin',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Proceed.',
                  inputValidator: (value) => {
                        return new Promise((resolve) => {
                            axios.post('/api/v1/admin/auth/pin', 
                            {
                                location_id:this.item.id,
                                pin:value
                            }
                            ).then(response =>  {
                                this.$announcer(response);
                                resolve();
                            }).catch(error => {
                                this.$announcer(error.response);
                                resolve('The PIN you have entered is not valid.')
                            }); 
                        })
                        
                    }
                }).then((result) => {
                  if(result.value){
                    this.$validator.validateAll().then((result) => {
                        if(result){
                            this.itemState = 'saving..';
                            this.form.update_subscription_action = act;
                            axios.post('/api/v1/admin/auth/modify/'+this.item.id+'/'+typ, this.form).then(response =>  {
                                this.itemState = 'saved';
                                this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // since we are calling this from another app module, we need to load a fresh schema first.
                                this.$emit('refresh');
                                this.$announcer(response);
                            }).catch(error => {
                                this.itemState = 'resave';
                                this.$announcer(error.response);
                            }); 
                        }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    });
                  }
                });
            },
            
            toggleLink(id,add=false,model='update_user_ids'){
                if(add && this.form[model]){
                    if(this.form[model].indexOf(id) === -1) this.form[model].push(id);
                }else this.form[model].splice(this.form[model].indexOf(id), 1);
            }
            
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },
        
        watch: {
            'form.update_subscription'(to,from){                                // load subscription data upon set of step 1
                if(to && this.item) 
                    this.subSel = this.item.services.find(v => v.id==this.form.update_subscription);
                
                if(this.subSel){                                                // then update subscription qty, coupon, and trial days if any
                    this.form.update_subscription_plan = this.subSel.stripe_plan;  
                    this.form.update_subscription_quantity = this.subSel.quantity || 0;
                    this.form.update_subscription_coupon = this.subSel.stripe_coupon;
                    this.form.update_subscription_trialdays = (this.subSel.trial_ends_at) ?  this.$moment.utc(this.subSel.trial_ends_at).diff(this.$moment.utc(),'days') : null;
                }
            },
            
            'form.update_transaction'(to,from){
                if(to && this.item)
                    this.chargeSel = this.item.transactions.find(v => v.id==this.form.update_transaction);                
            },
            
            'item.users'(to,from){
                if(to)
                    this.form.update_user_ids = this.item.users.map((v)=>{ return v.id; });
            },
            
                        
            'item.apps'(to,from){
                if(to)
                    this.form.update_app_ids = this.item.apps.map((v)=>{ return v.id; });
            }
        }
    };
    
</script>

<style>

</style>
