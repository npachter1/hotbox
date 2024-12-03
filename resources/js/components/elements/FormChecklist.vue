<template>
    <div class="form-group checklist-group">
    
        <label :for="'item'+schema.name">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>
    
        <ul class="list-group">
            <li v-for="(option,oid) in schema.values" v-show="option.isHidden!==true" :key="option.id" class="list-group-control">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" :id="option.id+'_'+option.name" :checked="inSet(option.id)" :disabled="option.isDisabled || false" @click="toggleOption(option.id,$event)"> 
                    <span class="custom-control-indicator"></span> 
                    <span class="custom-control-description">{{ option.name }} <span v-if="option.description" class="small"> - {{ option.description }}</span></span>
                </label>
            </li>
        </ul>        

    </div>
</template>
<script>
    export default {
        props: {
            value: {
                type: Array,
                default: () => [],
            },
            schema: {
                type: Object,
                default: () => {}
            }
        },
        
        data(){
            return {
                selected: null
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
    	
        watch: {
            selected(){
               this.val = this.selected.map((s) => {return s.id});
            }
        },
        
        methods: {
            inSet(opt){
                return (this.val.indexOf(opt) === -1) ? false : true;
            },
            
            toggleOption(opt,e){
                if(e.target.checked){
                    if(this.val.indexOf(opt) === -1) this.val.push(opt);
                }else this.val.splice(this.val.indexOf(opt), 1);
            },
        },

    	inject: ['$validator']
    };
</script>

<style>
  
</style>