<template>
    <div>
        <label v-if="hideLabel!==true" :for="'item'+schema.name">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
                {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>
        <div :class="{'input-group mb-2':schema.append || schema.prepend,'form-group mb-2':!schema.append && !schema.prepend}">
            <div v-if="schema.prepend" class="input-group-prepend">
                <span v-if="schema.prepend_text" class="input-group-text">{{ schema.prepend_text }}</span>
                <i v-if="schema.prepend" :class="schema.prepend"></i>
            </div>
            <input class="form-control"
              v-model="val"
              type="password"
              :name="schema.name"
              :disabled="disabled"
              :class="{'input': true, 'val-danger-input': errors.has(schema.name) }"
              :placeholder="schema.placeholder"
              :data-vv-as="schema.as || 'above'"
              v-validate="validation"
              :ref="schema.name">
                <div v-if="schema.append" class="input-group-append">
                    <span v-if="schema.append_text" class="input-group-text">{{ schema.append_text }}</span>
                    <i v-if="schema.append" :class="schema.append"></i>
                </div>
            
            <input v-if="schema.with_confirmation==true" class="form-control"
              type="password"
              v-model="confirmed"
              :name="schema.name+'_confirmation'"
              :disabled="disabled"
              :class="{'input': true, 'val-danger-input': errors.has(schema.name+'_confirmation') }"
              :placeholder="'Re-enter '+schema.title+' To Confirm..'"
              :data-vv-as="schema.as || 'above'"
              v-validate="(validation) ? validation+'|confirm:'+schema.name : 'confirm:'+schema.name">
            
        </div>
            <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span> 
            <span v-if="schema.with_confirmation==true" v-show="errors.has(schema.name+'_confirmation')" class="form-text text-muted val-danger-text">{{ errors.first(schema.name+'_confirmation') }}</span>  
    </div>
</template>
<script>
    export default {
        props: {
            value: {
                type: [String, Number],
                default: '',
            },
            schema: {
              type: Object,
              default: () => {}
            },
            disabled:{
                type: Boolean,
                default: false
            },
            hideLabel:{
                type: Boolean,
                default: false
            }
        },
        
        data(){
            return {

            };
        },
        
    	computed: {
    		val: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v); }
    		},
    		
     		confirmed: {
    			get() { return null; },
    			set(v) { this.$emit("confirm", v); }
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
    	
    	inject: ['$validator']
    	
    };
</script>

<style>
  
</style>