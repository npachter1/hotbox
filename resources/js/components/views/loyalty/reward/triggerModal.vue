<template>
    <div v-if="items && schema" class="col-12">
    <form>
        <fieldset>

            <div class="table-responsive mt-3 mb-4">
              <table class="table table-striped table-hover">
                <thead>
                <tr>
                  <th v-for="(sch,sid) in schema.form.triggers.properties">
                      <i v-if="sch.description" class="hotbox-icon hotbox-icon-c-question" :title="sch.title" v-b-tooltip.hover="sch.description"></i> {{ sch.title }}
                  </th>
                </tr>
                </thead>
                <tbody is="transition-group" name="hb-list-fade">
                <tr v-for="(item,iid) in items" :key="iid">
                  <td v-for="(formItem,ele) in schema.form.triggers.properties" :key="ele">
                    <form-boolean v-if="formItem.type=='boolean'" :declared="item[ele]" :schema="formItem" @input="(upd) => {item[ele] = upd}" :hideLabel="true" />
                    <form-text v-if="formItem.type=='text'" v-model="item[ele]" :schema="formItem" :hideLabel="true" />
                    <form-number v-if="formItem.type=='number'" v-model="item[ele]" :schema="formItem" :hideLabel="true" />
                    <form-simpleselect v-if="formItem.type=='simpleselect'" v-model="item[ele]" :schema="formItem" :hideLabel="true" />
                  </td>
                  <td width="5%" @click="removeItem(iid)"><i class="hotbox-icon hotbox-icon-trash-round"></i></td>
                </tr>
                <tr key="add"><td :colspan="6" width="100%" align="right" @click="addItem" key="add">
                    <strong><i class="hotbox-icon hotbox-icon-c-add"></i></strong>
                </td></tr>
                </tbody>
              </table>
            </div>
            
            <div class="col-12 clearfix mt-3 text-center">
                <auto-save type="save" :state="itemsState" @autoSave="saveList(true)"></auto-save>
                <a @click.default="$emit('refresh')" class="btn btn-sm btn-light">Return.</a>
            </div>

        </fieldset>
    </form>
    </div>
    <div v-else>
        <loading :display="(schema && items) ? false : true" type="loadModal" />
    </div>
</template>

<script>

     import Items from '../../../../models/RewardTrigger';


    export default {

        props: {
            model: {
                type: String,
                default: 'RewardTrigger'
            },
            module: {
                type: String,
                default: 'loyalty',
            }
        },
        
        data(){
            return {
                items: null,
                itemsState: 'save'
            };
        },
        
        components : {

        },
        
        mounted() {
            this.isLoading = true;
            Items.get().then(response => {

                this.items = response || [];
                this.isLoading = false;
            }).catch(error => {
        	    this.isLoading = false;
                this.$announcer(error.response);
                this.$emit('refresh');
            });
        },
        
        methods: {
            saveList(typ){
                this.$validator.validateAll().then(async (result) => {
                    if(result){
                        
                        let withPin = await this.requirePin('Please Enter an Admin PIN to Modify the Reward triggers.');
                        if(withPin===false) return false;                       // an adminpin couldnt be validated HINT add error message here if desired.
                        
                        this.itemsState = 'saving..';
                        axios.post('/api/v1/'+this.schema.meta.resource+'triggers',{triggers:this.items}).then(response =>  {
                            if(response.data.schema) this.$store.commit(this.module+'/setSchema',{data:response.data.schema,key:'rewardSchema'}); // update schems, will update grid
                            this.itemsState = 'saved';
                            this.$announcer(response);
                            this.$emit('refresh');
                        }).catch(error => {
                            this.itemsState = '(re)update';
                            this.$announcer(error.response);
                        });
                    }else this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                });
            },

            addItem(){
                this.items.push({
                    type:null,
                    point_amount:1,
                    descriptor:null,
                    is_exclusive:true,
                    is_active:true}
                );
            },
              
            removeItem(ind){
                this.items.splice(ind,1);
            }
        },
        
        computed: {
            schema() {
                return this.$store.state[this.module]['rewardSchema'];
            }
        },
        
        watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            }
        }
        
    };
    
</script>

<style>

</style>
