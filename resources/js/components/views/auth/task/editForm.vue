<template>
    <div v-if="item && schema" class="col-12">

    <h3 class="item-title">{{ item.name }}</h3>

    <span class="description" v-if="item.created_at">
      Created {{item.created_at | localDate('MM/DD/YYYY LTS') }}
    </span>
    <span class="description" v-if="item.updated_at">
      Last updated {{item.updated_at | localDate('MM/DD/YYYY LTS') }}
    </span>
    <span class="description" v-if="item.location">
      For: {{ item.location.name }}
    </span>

    <form class="mt-2" autocomplete="off">
      <fieldset>
      <div class="form-row">
        <div class="col-12 col-md-6">
          <label for="item-name">Name</label>
          <div class="form-group">
            <input id="item-name"
                   aria-describedby="addon-right addon-left"
                   name="name"
                   ref="nameInput"
                   @focus="$event.target.select()"
                   v-model="item.name"
                   class="form-control"
                   :class="{'input': true, 'text-danger': errors.has('name') }"
                   type="text"
                   placeholder=""
                   :disabled="isFieldDisabled"
                   v-validate="'required'"
                   >
            <span v-show="errors.has('name')" class="form-text text-muted text-danger">{{ errors.first('name') }}</span>
          </div>
        </div>
        <div class="col-2">
            <label for="item-due_date">Due Date</label>
            <div class="form-group">
                <datepicker id="item-due_date"
                                name="due_date"
                                v-model="item.due_date"
                                :bootstrap-styling="true"
                                input-class="form-datepicker"
                                :disabled-picker="isFieldDisabled"
                                v-validate="'required'"
                                ></datepicker>
                <span v-show="errors.has('due_date')" class="form-text text-muted text-danger">{{ errors.first('due_date') }}</span>
            </div>
        </div>
        <!-- <div v-for="(formItem,ele) in schema.form" class="col-2">
        <form-datetime v-if="formItem.type=='datetime'" v-model="item[ele]" :schema="formItem" />
        </div> -->
        <div class="col-2">
          <label for="item-status">Status</label>
          <div class="form-group">
            <select id="item-status"
                    class="form-control"
                    name="status"
                    v-model="item.status"
                    v-validate="'required'"
                    >
              <option value="">Select a Status</option>
                <option v-if="!isFieldDisabled" value="new">New</option>
                <option value="started">Started</option>
                <option value="completed">Completed</option>
                <option v-if="!isFieldDisabled" value="cancelled">Cancelled</option>
            </select>
            <span v-show="errors.has('status')" class="form-text text-muted text-danger">{{ errors.first('status') }}</span>
          </div>
        </div>
        <div class="col-2">
          <label for="item-priority">Priority</label>
          <div class="form-group">
            <select id="item-priority"
                    class="form-control"
                    name="priority"
                    v-model="item.priority"
                    :disabled="isFieldDisabled"
                    v-validate="'required'"
                    >
              <option value="">Select a Priority</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
                <option value="4">Urgent</option>
            </select>
            <span v-show="errors.has('priority')" class="form-text text-muted text-danger">{{ errors.first('priority') }}</span>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="col-12 col-md-12">
          <label for="item-description">Description</label>
          <div class="form-group">
            <textarea id="item-description"
                   aria-describedby="addon-right addon-left"
                   name="description"
                   ref="descriptionInput"
                   @focus="$event.target.select()"
                   v-model="item.description"
                   class="form-control"
                   :class="{'input': true, 'text-danger': errors.has('description') }"
                   placeholder="Please enter detailed instructions needed to complete the task."
                   :disabled="isFieldDisabled"
                   v-validate="'required'"
                   rows="10"
                   ></textarea>
            <span v-show="errors.has('description')" class="form-text text-muted text-danger">{{ errors.first('description') }}</span>
          </div>
        </div>
        <div v-if="schema" class="col-12" style="clear:both;">
        <label for="item-assignees">Assigned To</label>
            <div class="form-group">
                    <multiselect v-model="item.assignees"
                      :name="'assignees'"
                      :deselect-label="'Remove Assignee'"
                      :placeholder="'Click to Select'"
                      :searchable="true"
                      :allow-empty="true"
                      label="name"
                      track-by="user_id"
                      :disabled="isFieldDisabled"
                      :multiple="true"
                      :options="getOptionsArray()"
                      v-validate="'required'"
                      >
                    </multiselect> 
                    <span v-show="errors.has('assignees')" class="form-text text-muted text-danger">{{ errors.first('assignees') }}</span>               
            </div>
        </div>
      <hr>
      <label v-if="item.attachments">Attachments</label>
        <div v-for="attachment in item.attachments"
            class="col-12 mt-2">
          <a :href="attachment.url" target="_blank">{{ attachment.name }}</a>
        </div>
        <div 
             class="col-12 mt-2">
          <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"  @vdropzone-success="handleFileChange"></vue-dropzone>
        </div>

      </div>

      <div class="col-12 clearfix mt-3">
          <div class="drsection-content">
              <auto-save type="save" :state="itemState" @autoSave="autoSave(true)"></auto-save>
                <a @click.default="$router.go(-1)" class="btn btn-sm btn-light">Return.</a>
          </div>
      </div>
      </fieldset>

    </form>

  </div>
  <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadPage" />
  </div>
</template>

<script>

  import 'vue2-dropzone/dist/vue2Dropzone.min.css'
  import Item from '../../../../models/Task';
  import _ from 'lodash';

  export default {

    props: {
      id: {
        type: [Number, String],
        default: null
      },
      users: {
        default: null
      },
      locations: {
        default: null
      },
      location: {
        type: Object,
        default: null
      },
      model: {
          type: String,
          default: 'Task'
      },
      module: {
          type: String,
          default: 'auth',
      }
    },

    data() {
      return {
        item: {},
        itemState: 'save',
        isFormDisabled: true,
        isProcessing: false
      }
    },

    computed: {
      schema() {
          return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
      },
      dropzoneOptions() {
        return {
          url: '/api/v1/admin/asset/task/file',
          thumbnailWidth: 150,
          maxFilesize: 2,
          headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
          }
        }
      },
      route: function(){
        if(this.id) return '/api/v1/admin/auth/task/';
        else return '/api/v1/admin/auth/task/store';                                   // if rule id is 0 then we go to the store route
      },
      isFieldDisabled(){
        if(this.item && this.schema)
        {
          if(this.item.created_by)
          {
              return this.item.created_by.id !== this.$store.state.user.id;
          }
          else
          {
            return false;
          }
        } 
        
        return false;
      }
    },

    watch: {
            item:{
                handler(newVal,oldVal){
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            }
        },

    mounted() {
      
            this.isLoading = true;
            if(this.id){
                Item.find(this.id).then(response => {
                    if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                    this.item = new Item(response).withDefaults(this.schema);
                    this.isLoading = false;
                }).catch(error => {
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                });
            }else{
                this.item = new Item().withDefaults(this.schema);
                this.isLoading = false;
            }
    },

    methods: {
      autoSave(confirm=false){
                if(confirm===false && !this.id) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if(result){
                        if(!confirm) _.debounce(() => { this._save(); },2000)();
                        else this._save(true);
                    }else if(confirm==true){
                        this.$announcer({status:422,data:{message:'Whoops, Please check and correct inputs in order to continue.'}});
                    }else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
      },
      _save(confirm=false){
                this.itemState = 'saving..';
                this.item.save().then(response => {
                    if(confirm){
                        this.$announcer({status:200,data:{message:'Your '+this.model+' data has been Saved!'}});
                        if(response.schema) this.$store.commit(this.module+'/setSchema',{data:response.schema,key:this.model.toLowerCase()+'Schema'});
                        this.$router.push({name:this.model.toLowerCase()});
                    }
                    this.itemState = 'saved';
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
      },
      initForm() {
        this.isFormDisabled = false;
        this.$nextTick(() => this.$refs.nameInput.focus());
      },
      startProcessing() {
        this.isProcessing = true;
      },
      stopProcessing() {
        this.isProcessing = false;
      },
      handleFileChange(file, response) {
        let attachmentObj = {
          name: file.name,
          url: response.uri 
        }
        if(!this.item.attachments)
        {
          this.item.attachments = [];
        }
        this.item.attachments.push(attachmentObj);
      },
      getOptionsArray(){
        if(this.id)
        {
          return Object.values(this.schema.form.users.values);
        }
        return Object.values(this.schema.form.location_users.values);
      }
    }
  };
</script>

<style>
  .item-title {
    margin-bottom: 0;
  }
  .vdp-datepicker__calendar {
    left: 0;
  }
</style>