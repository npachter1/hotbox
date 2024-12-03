<template>
        <div v-if="item && schema" class="col-12">

            <div class="nav-tabs-header mb-4">
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="nav-link" :class="{'active':tab=='edit'}">
                        <a href="" class="" @click.prevent="tab='edit'">Profile</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='password'}">
                        <a href="" class="" @click.prevent="tab='password'"><i class="fal fa-key"></i> Password</a>
                    </li>
                    <li class="nav-link" :class="{'active':tab=='pin'}" v-on:click="hasPermissions(item.id, 'Store Admin Update')">
                        <a href="" class="" @click.prevent="tab='pin'"><i class="fal fa-key"></i> PIN</a>
                    </li>
                </ul>
            </div>

            <transition name="bo-slide">
                <div v-if="tab=='edit'" class="">

                    <form class="col-12 modal-form" @change="autoSave()">
                        <fieldset>

                    <div class="clearfix">
                        <div class="col-sm-5 text-center float-left">
                            <img :src="(item.avatar) ? item.avatar : schema.model.avatar" class="responsive" width="85%">
                        </div>
                        <div class="col-sm-7 float-left">
                            <h3>{{ item.name }}</h3>
                            <a href="" @click.prevent="downloadExportFile(item.id,'pdf')" class="d-block mb-2"><i v-if="isDownloading" class="far fa-circle-notch fa-spin"></i> <i class="far fa-file-pdf"></i> Download Profile</a>
                            <span class="small">Created on: {{ item.created_at | localDate }}</span><br>
                            <span class="small">Last Updated on: {{ item.updated_at | localDate }}</span>
                        </div>
                    </div>

                    <div v-for="(formItem,ele) in schema.form" class="mt-4 clearfix">

                        <div v-if="formItem.type=='object'" class="form-group-list">
                            <div v-for="(section,skey) in formItem.sections">
                                <h5 class="mb-2">Settings - {{section.title}}</h5>
                                <p v-for="(property,pname) in section.properties" class="mt-1">
                                    <form-text v-if="property.type=='text'" v-model="item[ele][pname]" :schema="property" />
                                    <form-datetime v-if="property.type=='datetime'" v-model="item[ele][pname]" :schema="property" />

                                </p>
                            </div>
                        </div>
                        <div v-else class="form-group-list">
                            <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" />
                            <form-text v-if="formItem.type=='text'" v-model="item[ele]" :schema="formItem" />
                            <form-select v-if="formItem.type=='select'" v-model="item[ele]" :schema="formItem" />
                            <form-multiselect v-if="formItem.type=='multiselect'" v-model="item[ele]" :schema="formItem" />
                            <form-checklist v-if="formItem.type=='checklist'" v-model="item[ele]" :schema="formItem" />
                            <a href="" v-if="formItem.name=='status' && item.status=='pending_activation'" class="float-right" @click.prevent="sendActivation()">
                                <i :class="{'ti-email':sendState=='send','far fa-circle-notch fa-spin':sendState=='preparing..','far fa-check-circle show-green':sendState=='sending'}"></i> {{ sendState | ucwords}} Activation Link Email &raquo;
                            </a>

                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                    </div>

                    </fieldset>
                </form>

                </div>
            </transition>

            <transition name="bo-slide">
                <div v-if="tab=='password'" class="">
                    <div v-if="$store.getters.userCan('Employees Update') || item.role_ids==item.id">

                        <form class="col-12 modal-form" @submit.prevent="updatePassword()">
                        <fieldset>
                            <h5 class="box-title m-b-20">Change User Password</h5>

                            <form-password v-model="resetForm.password" :schema="schema.form.password" @confirm="(upd) => { resetForm.password_confirmation=upd }" class="col-12 col-sm-12" />

                            <div class="form-group text-center mt-3 mb-4">
                                <button class="btn btn-info btn-md waves-effect waves-light" type="submit">
                                    <i :class="{'far fa-circle-notch fa-spin':resetState=='resetting','far fa-check-circle show-green':resetState=='resetted'}"></i> {{ resetState | ucwords }} Password
                                </button>
                            </div>
                        </fieldset>
                        </form>

                    </div>
                    <div v-else class="col-12 text-center mt-3 mb-4">
                        <h5>Were Sorry, You do not have permission to change this users password.</h5>
                    </div>
                </div>
            </transition>
            <transition name="bo-slide">
                <div v-if="tab=='pin'" class="">
                    <div v-if="$store.getters.userCan('Store Admin Update') && viewPINTab">
                        <form class="col-12 modal-form" @submit.prevent="updatePIN()">
                        <fieldset>
                            <h5 class="box-title m-b-20">Change Administrative User PIN</h5>

                            <form-password v-model="resetForm.pincode" :schema="schema.form.pincode" @confirm="(upd) => { resetForm.pincode_confirmation=upd }" class="col-12 col-sm-12" />

                            <div class="form-group text-center mt-3 mb-4">
                                <button class="btn btn-round btn-info waves-effect waves-light" type="submit">
                                    <i :class="{'far fa-circle-notch fa-spin':resetState=='resetting','far fa-check-circle show-green':resetState=='reset'}"></i> {{ resetState | ucwords }} PIN
                                </button>
                            </div>
                        </fieldset>
                        </form>

                    </div>
                    <div v-else class="col-12 text-center mt-3 mb-4">
                        <h5>This user account does not have the correct permissions to be given an administrative PIN.</h5>
                    </div>
                </div>
            </transition>

        </div>
        <div class="no-content-block" v-else>
            <loading :display="(item && schema) ? false : true" type="loadModal" />
        </div>
</template>

<script>

    import Item from '../../../../models/User';
    import _ from 'lodash';


    export default {
        props: {
            model: {
                type: String,
                default: 'User'
            },
            module: {
                type: String,
                default: 'auth',
            },
            id: {
                type: [Number,String],
                default: ''
            },
            type: {
                type: String,
                default: 'edit'
            }
        },

        data(){
            return {
                item: null,
                schema: null,
                tab: this.type,
                itemState: 'update',
                isLoading: false,
                isDownloading: false,
                resetState:'reset',
                sendState:'send',
                viewPINTab: false,
                resetForm: {
                    id:this.id,
                    password: null,
                    password_confirmation: null
                },
            };
        },

        components : {},

        mounted() {
            this.isLoading = true;
            Item.find(this.id).then(response => {
                this.schema = response.schema;
                this.item = new Item(response).withDefaults(this.schema);
                this.isLoading = false;
            }).catch(error => {
                this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                this.$emit('refresh');
            });
        },

        methods: {
            autoSave(confirm=false){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) _.debounce(() => { this._save(); },500)();
                        else this._save(true);
                    }else if(confirm==true)
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },

            _save(confirm=false){
                this.itemState = 'saving..';
                this.item.save().then(response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:this.model+' has been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        this.$emit('refresh');
                    }
                    this.itemState = 'update (saved)';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = '(re)update';
                });
            },

            downloadExportFile(id,typ){
                this.isDownloading = true;
                axios.get('/api/v1/'+this.schema.meta.resource+'/'+id+'/export/'+typ,{responseType: 'arraybuffer'}).then(response =>  {
                    this.isDownloading = false;
                    this.downloadFile(response);
                }).catch(error => {
                    this.isDownloading = false;
                    this.$announcer(error.response);
                });
            },

            hasPermissions(id, permission){
                this.viewPINTab = false;
                axios.post('/api/v1/'+this.schema.meta.resource+'/'+id+'/permissions', {
                    id: id,
                    permission: permission
                }).then( data => {
                    this.viewPINTab = true;
                }).catch( data => {
                    this.viewPINTab = false;
                });
            },

            isNumber: function(evt) {
              evt = (evt) ? evt : window.event;
              var charCode = (evt.which) ? evt.which : evt.keyCode;
              if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                evt.preventDefault();;
              } else {
                return true;
              }
            },

            updatePassword(){
                this.resetState = 'resetting..';
                axios.post('/api/v1/'+this.schema.meta.resource+'/'+this.id+'/change-password',this.resetForm).then(response =>  {
                    this.resetState = 'resetted';
                    this.$announcer(response);
                }).catch(error => {
                    this.resetState = 'reset';
                    this.$announcer(error.response);
                });
            },

            updatePIN(){
                this.resetState = 'resetting..';
                axios.post('/api/v1/'+this.schema.meta.resource+'/'+this.id+'/change-pin',this.resetForm).then(response =>  {
                    this.resetState = 'resetted';
                    this.$announcer(response);
                }).catch(error => {
                    this.resetState = 'reset';
                    this.$announcer(error.response);
                });
            },

            sendActivation(){
                this.sendState = 'preparing..';
                axios.post('/api/v1/'+this.schema.meta.resource+'/'+this.id+'/send-activation',{id:this.id}).then(response =>  {
                    this.sendState = 'sending';
                    this.$announcer(response);
                }).catch(error => {
                    this.sendState = 'send';
                    this.$announcer(error.response);
                });
            }

        },

        computed: {

        },

        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'update changes' : (newVal.id) ? 'update' : 'create';
                },
                deep: true
            }
        }

    };

</script>

<style>

</style>
