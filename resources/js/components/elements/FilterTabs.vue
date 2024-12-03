<template>
    <div class="filter-tabs">
                <ul class="">
                    <li v-for="(val,vid) in filterValues" 
                            class="" 
                            :class="{'active':list.indexOf(val.id)!==-1}"
                            @click="assignFilter(val.id,$event)">
                        {{ val.title || val.name }}
                    </li>
                </ul>
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
                currentFilter:null
            };
        },
        
        mounted() {
            this.list = this.filter || [this.schema.focus];
        },
        
    	computed: {
    		list: {
    			get() { return this.filter; },
    			set(v) { this.$emit("update", v); }
    		},
    		filterValues(){
    		    return this.schema.values;
    		}
    	},
        
        watch: {

        },
        
        methods: {
            assignFilter(val,e){
                this.list = [val];
            },
            getFilterName(key){
                let sel = this.schema.values.find((v) => { return v.id==key; });
                return (sel) ? sel.name : key.toUpperCase();
            },
            _empty(){
                this.list = [];
            }
        }
    };
</script>

<style>
  
</style>