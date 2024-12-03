<template>
	<div>
	   <div v-if="(list || {}).model" class="row mt-4 mb-2">
            <div class="col-sm-2 mb-4">
                <div class="menu-filter" style="font-size:0.86em;">
                <ul class="p-2 list-group">
                    <li class="list-group-control">
                        <div class="input-group">
                            <input class="form-control" type="search" v-model="filterSearch" :placeholder="'Search..'">
                            <div class="input-group-append">
                                <i class="hotbox-icon hotbox-icon-search-2"></i>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="list-group" v-for="(sect,sid) in filteredList">
                    <li class="sub-menu-title list-group-control mt-2" :class="{'sel':sid==form.type}">
                         <i v-if="(typeof filterSortDesc[sid])!=='undefined'" class="float-right fal" :class="{'hotbox-icon hotbox-icon-sort-tool':filterSortDesc[sid],'hotbox-icon hotbox-icon-sort-tool rotate':!filterSortDesc[sid]}" @click.default="filterSortDesc[sid] = !filterSortDesc[sid]"></i>
                        <a href="" @click.prevent="loadSchemaForm({code:null,type:sid,content:getDefaultJson(sid)})"><i class="fal" :class="{'hotbox-icon hotbox-icon-archive-drawer':!form.codename,'ti-folder':form.codename}"></i> {{ sid }}</a></li>
                    <li v-for="(val,vid) in sect" class="list-group-control" :class="{'active':val.code==form.codename}">
                         <i v-if="val.code!=form.codename" class="hotbox-icon hotbox-icon-trash-round float-right small" @click="confirmDelete(val.code)"></i>
                        <a href="" @click.prevent="loadSchemaForm(val)">{{ val.code }}</a>
                    </li>
                </ul>
                </div>
            </div>
            <div class="col-sm-10">
                <h5 v-if="!isNew"><i class="hotbox-icon hotbox-icon-pencil"></i> {{ form.codename }} [{{ form.type}}]:</h5>
                <h5 v-else><i class="hotbox-icon hotbox-icon-archive-drawer"></i> Create a New Schema {{ form.type}} File:</h5>
                
                <div class="form-group" v-show="isNew">
                    <input class="form-control"
                      v-model="form.codename"
                      type="text"
                      name="codename"
                      :class="{'input': true, 'val-danger-input': errors.has('codename') || isError}"
                      placeholder="schema code name"
                      :disabled="!isNew"
                      v-validate="'required|alpha_dash'">
                    <span v-show="errors.has('codename')" class="form-text text-muted val-danger-text">{{ errors.first('codename') }}</span>
                    <span v-show="errors.has('codesame')" class="form-text text-muted val-danger-text">{{ errors.first('codesame') }}</span>
                </div>

                <div class="json-edit">
                    <v-jsoneditor v-model="json" mode="code" :modes="['code','view']" @input="(val) => contentChange(val)" @has-error="isError=true"></v-jsoneditor>
                    <span v-show="errors.has('content')" class="form-text text-muted val-danger-text">{{ errors.first('content') }}</span>  
                </div>
                
                <div class="col-12 text-center mt-2">
                        <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                </div>
                
            </div>
        </div>
        <div class="col-12 mt-3 mb-2" v-else>
            <div class="block-announce info mt-2 mb-3">
                <p class="title"><i class="hotbox-icon hotbox-icon-key-26"></i> Schema Access:</p>
                <p class="text-center">The Schema files are managed for you by HotBox Personnel.  Please contact your Admin...</p>
            </div>
        </div>
    </div>
</template>

<script>

    //import VJsoneditor from 'vue-jsoneditor';
    import VJsoneditor from '../../../../../plugins/jsoneditor/vue-json-editor';

    export default {
        
        components:{ 
            VJsoneditor
        },
        
        data() {
            return {
                list:null,
                filteredList:null,
                json:{},
                form:{
                    codename:null,
                    type:'list',
                    content:null
                },
                isError: false,
                isModified:false,
                isProcessing:false,
                itemState: 'save',
                isNew: true,
                filterSearch: null,
                filterSortDesc: {
                    model:false,
                    list:false,
                    lang:false,
                    misc:false
                }
            };
        },

        async mounted() {
            await this.getList();
        },
        
        computed: {

    	},
        
        watch: {
            'form.codename'(){
                if(this.isNew && Object.values(this.list).reduce((a,b) => [...a, ...b]).find((val) => val.code==this.form.codename)){
                   this.isError = true;
                   this.errors.add({field:'codesame',msg:'This code name is already in use'});
                }else{
                    this.isError = false;
                    this.errors.clear();
                }
            },

            filterSearch(){
                if(this.filterSearch)
                    this.filteredList = Object.assign({}, ...Object.keys(this.list).map((k) => { return {[k]:this.list[k].filter((v) => v.code.indexOf(this.filterSearch)!==-1)};}));
                else this.filteredList = this.list;
            },
            
            filterSortDesc:{
                handler(to,from){
                    this.sortFilters();
                },
                deep: true
            },
            
        },

        methods: {
            loadSchemaForm(row){
                if(this.isModified) this.$announcer({status:400,data:{message:'Whoops, Looks like there are unsaved changes to the current schema, please save in order to continue'}});
                else{
                    this.json = row.content;
                    this.form = {
                        codename: row.code,
                        type: row.type,
                        content: null
                    };
                    this.isModified = false;
                    this.isNew = (row.code) ? false : true;
                }
            },
            
            autoSave(confirm=false){
                this.$validator.validateAll().then((result) => {
                    this.itemState = 'saving..';
                    if(result && !this.isError){
                        axios.put('/api/v1/admin/auth/schemasform/'+this.form.codename,this.form).then(response =>  {
                            this.itemState = 'saved';
                            this.isModified = false;
                            this.isNew = false;
                            this.getList();                                     // reload new list from save
                            this.$announcer({status:200,data:{message:'Your Schema Data for '+this.form.codename+' has been Saved!'}});
                        }).catch(error => {
                            this.$announcer(error.response);
                            this.itemState = 'resave';
                        });  
                    }else{
                        this.itemState = 'resave';
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    }
                });
            },
            
            contentChange(val){
                this.itemState = 'save changes';
                this.isError = false;                                           // if the recent change came through, jreset any error flag
                this.isModified = true;                                         // flag for modification - require save before clicking on new schema to edit
                this.form.content = JSON.stringify(val);
            },
            
            getList(){
                return axios.get('/api/v1/admin/auth/schemasform').then(response =>  {
                    this.list = response.data;
                    this.filteredList = this.list;
                    this.filterSearch = null;
                }).catch(error => {
                        //
                });             
            },
            
            getDefaultJson(typ){
                switch(typ){
                    case 'model':
                        return {
                            model:{},
                            filters:{},
                            form:{},
                            lang:{},
                            meta:{}
                        };
                        break;
                    default: return {}
                }
            },
            
            sortFilters(){
                if(!this.filteredList) return false;
                Object.keys(this.filteredList).forEach((k) => { this.sorter(this.filteredList[k],this.filterSortDesc[k],'code'); });
            },
            
            confirmDelete(code){
                this.$swal.fire({
                  title: 'Are you sure?',
                  text: 'This will Permanately Delete the schema file for '+code+ ' and CANNOT be undone.',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, delete record',
                  input: 'text',
                  inputValue: 'Enter Admin PIN to proceed..',
                  inputValidator: (value) => {
                    if (value!=process.env.MIX_VUE_APP_ADMINPIN)
                      return 'Incorrect AdminPin - Try Again?'
                  }
                }).then((result) => {
                  if(result.value){
                    this.isProcessing = true;
                    axios.delete('/api/v1/admin/auth/schemasform/'+code).then(response =>  {
                        this.isProcessing = false;
                        this.getList();                                     // reload new list from save
                        this.$announcer({status:200,data:{message:'Your Schema Data for '+code+' has been DELETED!'}});
                    }).catch(error => {
                        this.isProcessing = false;
                        this.$announcer(error.response);
                    }); 
                  }
                });
            },
        }
    }
</script>

<style>
    /*@import "https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/5.14.0/jsoneditor.min.css";*/
</style>
