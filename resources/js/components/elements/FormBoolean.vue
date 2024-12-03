<template>
    <div class="form-group show-gridsnap" :class="{'show-inactive':!value}">
        <label v-if="hideLabel!==true" :for="'item_'+schema.name">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <i v-if="value" class="small"> (Yes)</i>
            <i v-else class="small"> (No)</i>
        </label>
            <label class="switch float-right">
                <input type="checkbox" v-model="value">
                <span class="slider round"></span>
            </label>

    </div>
</template>

<script>
    export default {
        props: {
            declared: {
                type: Boolean,
                default: false,
            },
            schema: {
              type: Object,
              required: false,
              default: () => {}
            },
            skipValidation:{
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
              value: this.declared
            };
        },
        
        watch: {
            value() {
                this.$emit('input', this.value);
            },
            declared(){
               this.value = this.declared; 
            }
        },
        
        inject: ['$validator']

    };
</script>

<style>
  
</style>