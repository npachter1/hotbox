<template>
    <div class="filter-more">
        <div class="filter-more-toggle" @click="showFilterMore=!showFilterMore">    
            <b> <i class="ti-more-alt"></i> </b>
        </div>
                        
        <transition name="hb-slide">
            <div v-show="showFilterMore" class="drop-filter">
                <ul class="list-group">
                    <li class="sub-menu-title"><i class="ti-layout-list-thumb"></i> Viewing Options</span> <i class="float-right ti-close show-grey" @click.default="showFilterMore=!showFilterMore"></i></li>
                    <li class="" v-if="meta">
                        <select v-model.number="gridFilters.pageLimit" class="form-control" @change="$emit('update:gridFilters',gridFilters)">
                            <option v-if="limits.indexOf(gridFilters.pageLimit) === -1" :value="gridFilters.pageLimit">
                                Show {{ (meta.total <= gridFilters.pageLimit) ? 'All '+meta.total : gridFilters.pageLimit }} Rows
                            </option>
                            <option v-for="(lim,ind) in limits" v-if="meta.total>=limits[ind-1] || gridFilters.pageLimit==lim" :key="ind" :value="lim">
                                Show {{ (meta.total <= lim) ? 'All '+meta.total : lim }} Rows
                            </option>
                        </select>
                    </li>
                                    
                    <li v-if="hasEditableColumns()>0" class="sub-menu-title divider"><i class="ti-layout-column3"></i> Show (Optional) Columns</span></li>
                    <li v-for="(val,vid) in columns" v-if="val.hasOwnProperty('toggle')" class="list-group-control" :key="vid">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" :id="'column_visible_'+val.key" :checked="val.toggle===true" @click="toggleColumn(vid,$event)"> 
                            <span class="custom-control-indicator"></span> 
                            <span class="custom-control-description">{{ val.label || val.lable || val.key | capitalize }}</span>
                        </label>
                    </li>
                    
                    <li class="sub-menu-title divider">
                        <a href="" @click.prevent="$emit('downloadExport','csv')">
                            <spinner :isProcessing="isDownloading" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" />
                             <i class="hotbox-icon hotbox-icon-square-download-2"></i> Export <span v-if="hasFilters"><b>Filtered</b></span><span v-else>All</span> as CSV &raquo;
                             <i class="hotbox-icon hotbox-icon-c-info float-right" :title="'Grid Csv Exporting'" v-b-tooltip="'This action will export a full excel csv based on your current filters selected'"></i> 
                        </a>
                    </li>
                </ul>
                <div class="filter-footer">
                    &nbsp;<a class="btn btn-sm btn-light float-right" @click="showFilterMore=!showFilterMore">done</a>
                </div>
            </div>
        </transition>
        
                        
    </div>
</template>


<script>

    export default {
        props: {
            gridFilters: {
                type: Object,
                default: () => {}
            },
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
            },
            isDownloading: {
                type: Boolean,
                default: false
            }
        },
        
        data(){
            return {
                showFilterMore:false,
                limits:[10,20,50,100,200,500]
            };
        },
        
    	computed: {
            hasFilters(){
                if(!(this.gridFilters || {}).filter) return false;
                else return (Object.keys(this.gridFilters.filter).find(v=>this.gridFilters.filter[v].length>1 || this.gridFilters.filter[v][0]!='all')) ? true : false;
            }
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