<template>
    <div class="form-group">
        
        <label v-if="hideLabel!==true" :for="schema.name">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>

        <select class="form-control"
            v-model="val"
            :name="schema.name"
            v-validate="validation"
            :placeholder="schema.placeholder"
            :disabled="isDisabled"
            :class="{'input': true, 'val-danger-input': errors.has(schema.name) }">
                <option v-for="(opt,oid) in options" :key="oid" :value="opt.id">
                    {{ opt.title || opt.name }}
                </option>
        </select>
        <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>
        <span v-if="(selected || {}).description" class="form-text text-muted"><i>*{{ selected.description }}</i></span>

    </div>
</template>
<script>
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
            hideLabel:{
                type: Boolean,
                default: false
            },
            skipValidation:{
                type: Boolean,
                default: false
            },
            isDisabled:{
                type: Boolean,
                default: false
            }
        },
        
        data(){
            return {
                options: this.schema.values,
                isLoading: false,
                selected:null
            };
        },
        
    	computed: {
    		val: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v); }
    		},
    		
    		validation(){
    		    return (this.skipValidation) ? null : this.schema.validation;
    		},
    		
    		isRequired() {
    		    if(this.validation)
    		        return (this.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		}
    		
    	},
    	
        watch: {
            val(to,from){
                if(to && this.options) this.selected = this.options.find(row => row.id==to);
            }
        },
    	
    	methods: {

    	},
    	
    	mounted() {
    	    if(this.scopeData && this.schema.name=='region') this.getRegionData();
    	    else this.selected = this.options.find(row => row.id==this.val);
    	},
    	
    	inject: ['$validator']
    };
</script>

<style>
  
</style>