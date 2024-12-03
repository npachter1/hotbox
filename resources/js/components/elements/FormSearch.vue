<template>
    <div class="form-group">
        
        <label v-if="hideLabel!==true" :for="'item'+schema.name" class="w-100">
            <slot name="title-right"></slot>
            <i v-if="schema.description" class="fal fa-question-circle" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>
        
                  <multiselect v-if="schema.search_resource" class="input mb-2"
                    v-model="selected"
                    :name="schema.name"
                    :placeholder="schema.placeholder"
                    :internal-search="false"
                    :clear-on-select="true"
                    :close-on-select="true"
                    :options-limit="100"
                    :max-height="400"
                    :show-no-results="true"
                    :hide-selected="false"
                    pen-direction="bottom"
                    :searchable="true"
                    :allow-empty="true"            
                    :multiple="false"
                    :loading="isLoading"
                    track-by="id"
                    label="name"
                    v-validate="validation"
                    :data-vv-as="'above'"
                    :options="options"
                    :custom-label="renderSearchFields"
                    @search-change="searchItems">
                    <template slot="option" slot-scope="{ option }">{{ renderSearchFields(option) }}</template>
                  </multiselect>        
                <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>

    </div>
</template>
<script>
    import _ from 'lodash';
    export default {
        props: {
            value: {
                //type: String,
                default: '',
            },
            schema: {
              type: Object,
              default: () => {}
            },
            defaultOption: {
                type: Object,
                default: () => {}
            },
            hideLabel:{
                type: Boolean,
                default: false
            },
            skipValidation:{
                type: Boolean,
                default: false
            },
        },
        
        data(){
            return {
                selected: null,
                options: this.schema.values,
                isLoading: false
            };
        },
        
    	computed: {
    		val: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v); }
    		},
    		
    		isRequired() {
    		    if(this.schema.validation)
    		        return (this.schema.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		},
    		
    		validation(){
    		    return (this.skipValidation) ? null : this.schema.validation;
    		},
    	},
    	
        watch: {
            selected(){
                this.val = (this.selected) ? this.selected.id : null;
                if(this.selected) this.$emit("syncdata", this.selected);
            },
            
            value(to,from){
                if(from) this.selected = this.options.find(row => row.id==this.value); // if we updated the value prop
            },
            
            'schema.values'(to,from){
                this.options = this.schema.values;                              // if we updated the values
            }
        },
    	
    	methods: {
            searchItems: _.debounce(function (query) {                          // upon search filter update, throttle .3 sec grid and scope refresh
                    if(!query || query=='') return false;
                    this.isLoading = true;
                    axios.get('/api/v1/'+this.schema.search_resource+'?search='+query).then(response =>  {
                	   if(response.data){ 
                	       this.options = response.data.data || [];
                	       if(this.schema.prompt_new) this.options.unshift({id:'NEW',name:'Add a New '+this.schema.prompt_new});
                	       //this.selected = this.options.find(row => row.id==this.val);  // reset selected based on new regional options fetched.
                	   }
                	   this.isLoading = false;
            	    }).catch(error => {
            	       this.isLoading = false;
            	    });
            }, 300),
            renderSearchFields(data){
                if(!this.schema.search_fields) return ['name'];                 // default
                return this.schema.search_fields.map((v)=>{ return data[v] || null;}).join(' ');
            }
    	},
    	
    	mounted() {
    	   if(this.schema.prompt_new) this.options.unshift({id:'NEW',name:'Add a New '+this.schema.prompt_new});
    	   this.selected = this.options.find(row => row.id==this.val) || this.defaultOption;
    	},
    	
    	inject: ['$validator']
    };
</script>

<style>
  
</style>