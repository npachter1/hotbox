<template>
    <div>
        <label v-if="hideLabel!==true" :for="'item'+schema.name" class="w-100">
            <slot name="title-right"></slot>
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
                {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
            <slot name="text-right"></slot>
        </label>
        <div :class="{'input-group mb-2':schema.append || schema.prepend,'form-group mb-2':!schema.append && !schema.prepend}">
            <div v-if="schema.prepend" class="input-group-prepend">
                <span v-if="schema.prepend_text" class="input-group-text">{{ schema.prepend_text }}</span>
                <i v-if="schema.prepend" :class="schema.prepend"></i>
            </div>
            <input class="form-control"
              v-model="val"
              type="text"
              :ref="schema.name"
              :name="schema.name"
              :disabled="disabled"
              :class="{'input': true, 'val-danger-input': errors.has(schema.name) }"
              :placeholder="schema.placeholder"
              :data-vv-as="'above'"
              v-validate="validation">
                <div v-if="schema.append" class="input-group-append">
                    <span v-if="schema.append_text" class="input-group-text">{{ schema.append_text }}</span>
                    <i v-if="schema.append" :class="schema.append"></i>
                </div>
        </div>
            <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>  
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
            skipValidation:{
                type: Boolean,
                default: false
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