<template>
            <div v-if="type=='save'" 
                class="btn btn-md btn-primary" 
                :class="{'btn-primary':state=='save','btn-warning':state=='save changes','btn-default':state=='saving..','btn-success':state=='create','btn-danger':state=='resave'}"
                :disabled="state=='saving..'"
                @click.prevent="initAutoSave()">
               <spinner :isProcessing="state=='saving..'" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /> {{ state | capitalize }}.
            </div>
            <div v-else class="btn btn-sm btn-default">
                 -- 
            </div>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'save'
            },
            state: {
              type: String,
              default: 'save'
            }
        },
        
        data(){
            return {
              cur: this.state
            };
        },
        
        watch: {
            type() {
                // TODO based on type, enum states values
                // currently, this component simply displays the save button and emits a click
            }
        },
        
        methods: {
            initAutoSave(){
                this.$emit('autoSave',true);
            }
        }
    };
</script>

<style>
  
</style>