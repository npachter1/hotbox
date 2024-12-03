<template>
    <div v-if="item && schema" class="col-12">
        <form @submit="createItem()">
            <fieldset>

                <h3>
                    Add a new Staff Member<br>
                    <span class="small">Location User</span>
                </h3>

                        <div class="form-group-list mt-4">
                                <label for="email">User Email (used as username/login) <span class="show-red small"> *(Required)</span></label>
                                <input class="form-control"
                                    v-model="item.email"
                                    type="text"
                                    name="email"
                                    placeholder="User Email"
                                    :class="{'val-danger-input': errors.has('email') }"
                                    v-validate="'required|email'">
                                <span v-show="errors.has('email')" class="form-text text-muted val-danger-text">{{ errors.first('email') }}</span>
                        </div>
                        <div class="form-group-list mt-4">
                                    <label for="password">Users Password (they, or you, can change this at any point) <span class="show-red small"> *(Required)</span></label>
                                    <input class="form-control"
                                        v-model="item.password"
                                        type="password" 
                                        name="password" 
                                        :class="{'val-danger-input': errors.has('password') }"
                                        placeholder="New Password" 
                                        v-validate="'required'"
                                        ref="password">
                                        <span v-show="errors.has('password')" class="form-text text-muted val-danger-text">{{ errors.first('password') }}</span>                                
                        </div>
                        <div class="form-group-list mt-4 mb-4">
                                <label for="password">Confirm above Password <span class="show-red small"> *(Required)</span></label>
                                <input class="form-control"
                                        v-model="item.password_confirmation"
                                        type="password" 
                                        name="password_confirmation" 
                                        :class="{'val-danger-input': errors.has('password') }"
                                        placeholder="Confirm New Password*" 
                                        :data-vv-as="'password'"
                                        v-validate="'required|confirmed:password'">
                                <span v-show="errors.has('password_confirmation')" class="form-text text-muted val-danger-text">{{ errors.first('password_confirmation') }}</span>
                        </div>

                        <div v-for="(formItem,ele) in schema.form" class="form-group-list mt-4 clearfix">
                            <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" />
                            <form-text v-if="formItem.type=='text'" v-model="item[ele]" :schema="formItem" />
                            <form-select v-if="formItem.type=='select'" v-model="item[ele]" :schema="formItem" />
                            <form-multiselect v-if="formItem.type=='multiselect'" v-model="item[ele]" :schema="formItem" />
                            <form-checklist v-if="formItem.type=='checklist'" v-model="item[ele]" :schema="formItem" />
    
                                                        
                            <div v-if="item.status=='pending_activation' && formItem.name=='status'" class="block-announce warning">
                                <p class="title"><i class="fal fa-envelope-open-text"></i> User will have to Activate.</p>
                                <p>Upon creation, we will email your user an activation link.  Once clicked, they may login with their credentials above.</p>
                            </div>
    
                        </div>


                        <div class="col-12 clearfix mt-4">
                            <div class="drsection-content">
                                <auto-save type="save" :state="itemState" @autoSave="createItem()"></auto-save>
                                 <a @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
                            </div>
                        </div>

            </fieldset>
        </form>    
    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadAll" />
    </div>
</template>

<script>

    import Item from '../../../../models/User';


    export default {
        props: {
            model: {
                type: String,
                default: 'User'
            },
            module: {
                type: String,
                default: 'auth',
            }
        },
        
        
        data(){
            return {
                id: 0,
                item: null,
                itemState: 'create'
            };
        },
        
        components : {

        },
        
        async mounted() {
            this.isLoading = true;
            if(!this.schema) await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase());
            this.item = new Item().withDefaults(this.schema);
            this.isLoading = false;
        },
        
        methods: {
            createItem(){
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.itemState = 'saving..';
                        this.item.save().then(response => {
                            this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                            if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                            this.itemState = 'created';
                            this.$router.push({name:'user'});
                        }).catch(error => {
                            this.$announcer(error.response);
                            this.itemState = 'resave';
                        });
                    }else this.$announcer({status:422,data:{message:'Missing / Invalid input - Please check form above and try again.'}});
                });
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
            }
        },
        
        watch: {

        }
        
    };
    
</script>

<style>

</style>
