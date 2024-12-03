<template>
    <div class="form-group">
        
        <label v-if="hideLabel!==true" :for="'item'+schema.name">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>
        
        <multiselect class=""
            v-model="selected"
            :name="schema.name"
            :class="{'input': true, 'val-danger-input': errors.has(schema.name) }"
            :placeholder="schema.placeholder"
            :data-vv-as="'above'"          
            :searchable="(schema.searchable===true) ? true : false"
            :allow-empty="(isRequired) ? false : true"            
            :multiple="true"
            v-validate="schema.validation"
            track-by="id"
            label="name"
            :options="list">
              <template slot="singleLabel" slot-scope="{ option }">{{ option.name }}</template>
              <template slot="option" slot-scope="{ option }">{{ option.name }} <span v-if="locationScope.length>=2 && option.location_id" class="small"> - [From: {{ option.location_id | renderValue($store.state.user.locations_assigned) }}]</span></template>
        </multiselect>       
        <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>

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
            },
            locationScope:{
                type: Array,
                default: () => []
            }
        },
        
        data(){
            return {
                selected: null
            };
        },
        
    	computed: {
    		val: {
    			get() { 
    			    
                    if(this.schema.name=='location_ids' && this.value.length==0) return [this.$store.state.disp.location]; // default select current users location if no value.		    
    			    return this.value; 
    			    
    			},
    			set(v) { this.$emit("input", v); }
    		},
    		
    		isRequired() {
    		    if(this.schema.validation)
    		        return (this.schema.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		},
    		
    		list(){
    		    return this.schema.values.filter(v => this.locationScope.indexOf(v.location_id)!==-1 || !this.locationScope.length);
    		}
    	},
    	
        watch: {
            selected(newValue, oldValue){
               this.val = this.selected.map((s) => {return s.id});
               if (oldValue) this.$emit("change");
            }
        },
    	
    	mounted() {
            if (this.val)
    	       this.selected = this.schema.values.filter(row => this.val.indexOf(row.id)!==-1);
    	},
    	
    	inject: ['$validator']
    };
</script>

<style>
  
</style>