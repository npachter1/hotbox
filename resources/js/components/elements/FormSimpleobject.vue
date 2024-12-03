<template>
    <div>
        <label v-if="hideLabel!==true" :for="'item'+schema.name" class="w-100 show-gridsnap">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
        </label>

        <div class="col-12 mt-2 mb-3 pl-4" v-if="schema.properties">
            <div v-for="(row,rid) in schema.properties" v-if="!row.key || formData[row.scope]==row.key || (focus && (row.types || []).includes(focus))" :key="rid" class="mt-1 mb-1 row">
                
                <div :class="labelClass">
                    <i v-if="row.description" class="hotbox-icon hotbox-icon-c-question" :title="row.name" v-b-tooltip.hover="row.description"></i> {{ row.title || row.name }} 
                </div>
                <div :class="fieldClass">
                    <form-text v-if="row.type=='text'" v-model="vals[rid]" :schema="row" :hideLabel="true" />
                    <form-number v-if="row.type=='number'" v-model="vals[rid]" :schema="row" :hideLabel="true" />
                    <form-select v-if="row.type=='select'" v-model="vals[rid]" :schema="row" :hideLabel="true" />
                    <form-simpleselect v-if="row.type=='simpleselect'" v-model="vals[rid]" :schema="row" :hideLabel="true" @change="$emit('change')"/>
                    <form-multiselect v-if="row.type=='multiselect'" v-model="vals[rid]" :schema="row" :hideLabel="true" @change="$emit('change')"/>
                    <form-list v-if="row.type=='list'" v-model="vals[rid]" :schema="row" :hideLabel="true" />
                    <form-timepicker v-if="row.type=='time'" v-model="vals[rid]" :schema="row" :hideLabel="true" @change="$emit('change')"/>
                </div>
                
            </div>
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
            hideLabel:{
                type: Boolean,
                default: false
            },
            focus:{
                type: [String,Number],
                default: null
            },
            labelClass:{
                type: String,
                default: 'col-6 col-sm-3 clearfix'
            },
            fieldClass:{
                type: String,
                default: 'col-6 col-sm-9'
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
    	
    	inject: ['$validator']
    };
</script>

<style>
  
</style>