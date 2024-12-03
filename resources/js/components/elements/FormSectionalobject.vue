<template>
    <div>
        <div v-if="section.scope==scoped || !scoped || !section.scope" v-for="(section,skey) in schema.sections">
                                  
            <div class="drsection-header">
                <h4>{{section.title}}
                    <span @click="section.collapsed=!section.collapsed" class="setting-toggle">
                        <i class="fas" :class="{'ti-angle-down':section.collapsed,'ti-angle-up':!section.collapsed}"></i>
                    </span>
                </h4>
            </div>
                            
            <transition name="hb-slide">
                <div v-show="!section.collapsed" class="drsection-content">
        
                    <div v-for="(row,rid) in section.properties" v-if="!row.key || formData[row.scope]==row.key" class="mt-4">
                        <form-text v-if="row.type=='text'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        <form-number v-if="row.type=='number'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        <form-select v-if="row.type=='select'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        <form-list v-if="row.type=='list'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        <form-simpleselect v-if="row.type=='simpleselect'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        <form-multiselect v-if="row.type=='multiselect'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        <form-datetime v-if="row.type=='datetime'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        <form-textarea v-if="row.type=='textarea'" v-model="vals[rid]" :schema="row" :disabled="disabled" :rows="2" />
                        <form-password v-if="row.type=='password'" v-model="vals[rid]" :schema="row" :disabled="disabled" />
                        
                        <form-boolean v-if="row.type=='boolean'" :declared="vals[rid]" :schema="row" :disabled="disabled" @input="(upd) => {vals[rid] = upd}" />
                           
                                        
                    </div>
        
                </div>
            </transition>

        </div>
    </div>
</template>
<script>
    export default {
        props: {
            value: {
                type: [Object,Array],
                default: () => {},
            },
            schema: {
              type: Object,
              default: () => {}
            },
            formData: {
                type: Object,
                default: () => {}
            },
            disabled: {
                type: Boolean,
                default: false
            },
            hideLabel:{
                type: Boolean,
                default: false
            },
            scoped:{
                type: [Number,String],
                default: null
            }
        },
        
        data(){
            return {

            };
        },
        
        mounted(){

        },
        
    	computed: {
    		vals: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v); }
    		}
    	},
    	methods: {

    	},
    	
    	created() {
    	    this.$validator = this.$parent.$validator;
    	}
    };
</script>

<style>
  
</style>