<template>
      <div class="form-group">
        <label v-if="hideLabel!==true" :for="'item'+schema.name" class="w-100">
            <i v-if="schema.description"
               class="hotbox-icon hotbox-icon-c-question"
               :title="schema.title"
               v-b-tooltip.hover="schema.description"/>
             {{ schema.title }} 
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>
          <datepicker
                  v-model="val"
                  :name="schema.name"
                  :format="'M/d/yyyy'"
                  :typeable="true"
                  :bootstrap-styling="false"
                  :input-class="{'form-control':true,'showdateform':true,'input':true,'val-danger-input': errors.has(schema.name)}"
                  :placeholder="schema.placeholder"
                  :data-vv-as="'above'"
                  v-validate="schema.validation"
                  :calendar-button="false"
                  :calendar-button-icon="'ti-calendar'"/>
        <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>   
    </div>
</template>
<script>
    export default {
        props: {
            value: {
                type: [String, Number, Date],
                default: '',
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
    		val: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", moment(v).format('YYYY-MM-DD HH:mm:ss')); this.$emit("change"); }
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

<style scoped>
    >>> .vdp-datepicker__calendar {
        left: 0;
    }
</style>