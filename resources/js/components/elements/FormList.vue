<template>
    <div class="mb-2">

        <label v-if="hideLabel!==true" :for="'item'+schema.name" class="w-100">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>

        <div v-for="(val,vid) in vals" :key="vid">
        <div class="input-group mt-1 mb-1">
        <input class="form-control"
          v-model="vals[vid]"
          type="text"
          :name="schema.name+vid"
          :class="{'input': true, 'val-danger-input': errors.has(schema.name+vid) }"
          :placeholder="schema.placeholder"
          :data-vv-as="'above'"
          v-validate="schema.validation">
            <span class="input-group-append"><i class="hotbox-icon hotbox-icon-trash-round" @click="removeItem(vid)"></i></span>
        </div>
            <span v-show="errors.has(schema.name+vid)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name+vid) }}</span>
        </div>
         <a class="btn btn-sm btn-light float-right small btn-round" :class="{'w-100 mb-3 text-right':vals.length==0}" @click.prevent="addItem"><i class="hotbox-icon hotbox-icon-e-add"></i></a>

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
    		vals: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v.filter((r)=>(r && r!=""))); }
    		},
    		isRequired() {
    		    if(this.schema.validation)
    		        return (this.schema.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		}
    	},
    	
    	methods: {
    	    addItem(){
    	        this.vals.push(null);
    	    },
    	    
    	    removeItem(ind){
    	        this.vals.splice(ind,1);
    	    }
    	},
    	
    	inject: ['$validator']
    };
</script>

<style>
  
</style>