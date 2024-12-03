<template>
    <div class="filter-more">
        <div class="filter-more-toggle" @click="showFilterMore=!showFilterMore">    
            <b> <i class="ti-more-alt"></i> </b>
        </div>
                        
        <transition name="hb-slide">
            <div v-show="showFilterMore" class="drop-filter">
                <ul class="list-group">
                    <li v-if="hasEditableColumns()>0" class="sub-menu-title divider"><i class="ti-layout-column3"></i> Show (Optional) Columns</span></li>
                    <li v-for="(val,vid) in columns" v-if="val.hasOwnProperty('toggle')" class="list-group-control" :key="vid">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" :id="'column_visible_'+val.key" :checked="val.toggle===true" @click="toggleColumn(vid,$event)"> 
                            <span class="custom-control-indicator"></span> 
                            <span class="custom-control-description">{{ val.label || val.key | capitalize }}</span>
                        </label>
                    </li>

                </ul>
                <div class="filter-footer">
                    &nbsp;<a class="btn btn-sm btn-default float-right" @click="showFilterMore=!showFilterMore">done</a>
                </div>
            </div>
        </transition>
    </div>
</template>


<script>

    export default {
        props: {
            columns: {
                type: Array,
                default: () => []
            },
            schema: {
              type: Object,
              default: () => {}
            },
            meta: {
              type: Object,
              default: () => {}                
            }
        },
        
        data(){
            return {
                showFilterMore:false
            };
        },
        
    	computed: {

    	},
        
        watch: {

        },
        
        methods: {
            toggleColumn(ind,e){
                if(this.columns[ind]) 
                    this.columns[ind].toggle = !this.columns[ind].toggle;
            },
            
            hasEditableColumns(){
                return (this.columns) ? this.columns.filter(v => v.hasOwnProperty('toggle')).length : 0;
            }
        }
    };
</script>

<style>
  
</style>