<template>
    <div class="form-group">
        
        <label v-if="hideLabel!==true" :for="'item'+schema.name" class="w-100">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
            <slot name="text-right"></slot>
        </label>
        
        <multiselect class=""
            v-model="selected"
            :name="schema.name"
            :class="{'input': true, 'val-danger-input': errors.has(schema.name) }"
            :placeholder="schema.placeholder"
            :data-vv-as="'above'"          
            :searchable="(schema.searchable===true) ? true : false"
            :allow-empty="(isRequired && !schema.prompt_new) ? false : true"            
            :multiple="false"
            :loading="isLoading"
            v-validate="schema.validation"
            track-by="id"
            label="name"
            :options="options"
            :disabled="isDisabled">
              <template slot="singleLabel" slot-scope="{ option }">{{ option.name }}</template>
        </multiselect>
        <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>

    </div>
</template>
<script>
    export default {
        props: {
            value: {
                //type: [String,Number],
                default: '',
            },
            schema: {
              type: Object,
              default: () => {}
            },
            hideLabel:{
                type: Boolean,
                default: false
            },
            scopeData: {
                type: String,
                default: null
            },
            isDisabled:{
                type: Boolean,
                default: false
            },
            isLive:{
                type: Boolean,
                default: false
            }
        },
        
        data(){
            return {
                selected: null,
                scopedOptions: null,
                isLoading: false
            };
        },

    	mounted() {
    	    if(this.scopeData && this.schema.name=='region') this.getRegionData();
    	    else if(this.scopeData) this.curateList(this.scopeData);
    	    else this.selected = this.options.find(row => row.id==this.val);
    	        	    
    	    if(this.schema.prompt_new) this.options.push({id:'NEW',name:'Add a New '+this.schema.prompt_new});
    	},
    	
    	computed: {
    		val: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v); }
    		},
    		
    		options(){
    		    return (this.scopedOptions) ? this.scopedOptions : this.schema.values;    
    		},
    		
    		isRequired() {
    		    if(this.schema.validation)
    		        return (this.schema.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		}
    		
    	},
    	
        watch: {
            selected(newValue, oldValue){
                this.val = (this.selected) ? this.selected.id : null;
                if(this.selected) this.$emit("syncdata", this.selected);
                if (oldValue) this.$emit("change");
            },
            
            scopeData(){
                if(this.schema.name=='region') this.getRegionData();            // reload state/regions based on scopeData of country code changing
            },

            scopedOptions(){
               this.selected = this.scopedOptions.find(row => row.id==this.val); // reselect every time theres new options scoped
            },
            
            value(to,from){
                if(this.isLive) this.selected = this.options.find(row => row.id==this.value); // if we updated the value prop
            }
        },
    	
    	methods: {
    	    getRegionData(){
                this.isLoading = true;
                axios.get('/api/v1/admin/regions/'+this.scopeData).then(response =>  {
            	   this.scopedOptions = response.data;
            	   this.isLoading = false;
        	    }).catch(error => {
        	       this.isLoading = false;
//console.log(error.response.data);
        	    });
    	    },
    	    
    	    curateList(match){                                                  // curate list based on schemas scope array and a matching value
    	        this.scopedOptions = this.options.filter(row =>((row.scope || []).indexOf('all')!==-1 || (row.scope || []).indexOf(match)!==-1));
    	        this.selected = this.options.find(row => row.id==this.val);
    	    }
    	},
    	
    	inject: ['$validator']
    };
</script>

<style>
  
</style>