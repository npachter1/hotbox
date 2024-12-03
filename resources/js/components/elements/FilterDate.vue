<template>
    <div class="filter-wherein">

        <div v-for="(val,ind) in list" class="float-left">
             <span v-if="ind===0" class="datepref">From</span> 
             <span class="datepref" v-else>To</span> 
             <datepicker v-model="list[ind]"
                :name="schema.name+'_'+ind"
                :format="'M/d/yyyy'"
                :placeholder="'1/1/19'"
                :typeable="true"
                :bootstrap-styling="false"
                :input-class="{'form-control':false,'showdate':true}"
                :calendar-button-icon="'ti-calendar'">
             </datepicker>

        </div>
        
    </div>
</template>


<script>

    export default {
        props: {
            filter: {
                type: Array,
                default: () => []
            },
            schema: {
              type: Object,
              default: () => {}
            }
        },
        
        data(){
            return {

            };
        },
        
    	computed: {
    		list: {
    			get() {

    			    return this.filter; 
    			},
    			set(v) { 

    			    this.$emit("update", v.map(k=>{ return k.toISOString().slice(0, 19).replace("T", " ")}));
    			    //this.$emit("update", v);
    			}
    		}
    	},
        
        watch: {
            
        },
        
        methods: {

        }
    };
</script>

<style>
  
</style>