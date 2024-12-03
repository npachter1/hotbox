<template>
    <div>
        <label v-if="hideLabel!==true" :for="'item'+schema.name">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>
        <div :class="{'input-group':schema.append || schema.prepend,'form-group':!schema.append && !schema.prepend}">
            <div v-if="schema.prepend" class="input-group-prepend">
                <span v-if="schema.prepend_text" class="input-group-text">{{ schema.prepend_text }}</span>
                <i v-if="schema.prepend" class="mt-1" :class="schema.prepend"></i>
            </div>
            <textarea class="form-control"
              :name="schema.name"
              :class="{'val-danger-input': errors.has(schema.name) }"
              v-model="val"
              :placeholder="schema.placeholder"
              :rows="rows"
              :data-vv-as="'above'"
              v-validate="schema.validation">
            </textarea>
                <div v-if="schema.append" class="input-group-append">
                    <span v-if="schema.append_text" class="input-group-text">{{ schema.append_text }}</span>
                    <i v-if="schema.append" class="mt-1" :class="schema.append"></i>
                </div>
            <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                default: '',
            },
            schema: {
              type: Object,
              default:()=>{}
            },
            rows: {
                type: Number,
                default: 3
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
    		isRequired() {
    		    if(this.schema.validation)
    		        return (this.schema.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		}
    	},
    	
    	inject: ['$validator']
    };
</script>

<style>
  
</style>