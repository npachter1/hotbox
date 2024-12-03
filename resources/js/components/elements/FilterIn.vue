<template>
    <div class="filter-wherein" :class="{'active':isActive}">
        
        <a href="" @click.prevent="showFilterList=!showFilterList">
            <b>{{ schema.name | capitalize }}</b> 
            <i v-if="allChecked">(All)</i>
            <i v-else>
                (<span v-for="(filter,ind) in list" v-if="ind<=2 && filter!='all'">
                    {{ getFilterName(filter) | capitalize }} 
                </span>..)
            </i> 
            <i class="caret fas fa-caret-down" :class="{'rotate-caret':showFilterList}"></i>
        </a>

        <transition name="hb-slide">
            <div v-show="showFilterList" class="drop-filter">
                <ul class="list-group">
                    <li class="sub-menu-title list-group-control">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" :id="schema.name+'_ALL'" :checked="allChecked" @click="toggleAll"> 
                            <span class="custom-control-indicator"></span> 
                            <span class="custom-control-description">View All {{ schema.title || schema.name }}</span>
                        </label> 
                        <i class="float-right ti-close show-grey" @click.default="showFilterList=!showFilterList"></i>
                        <slot name="text-right"></slot>
                    </li>
                    <li v-if="schema.values.length>12">
                        <div class="input-group">
                            <input class="form-control" type="search" v-model="filterSearch" :placeholder="'Search '+schema.name+' Names'">
                            <div class="input-group-append"><i class="hotbox-icon hotbox-icon-search-2"></i></div>
                        </div>
                    </li>
                    <li v-for="(val,vid) in filterValues" v-if="vid<=20 || showAll" class="list-group-control">
                        <i v-if="vid===0" class="float-right" :class="{'hotbox-icon hotbox-icon-sort-tool':filterSortDesc,'hotbox-icon hotbox-icon-sort-tool rotate':!filterSortDesc}" @click.default="filterSortDesc = !filterSortDesc"></i>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" :id="schema.name+'_'+val.name+'_'+vid" :checked="inSet(val.id)" @click="toggleFilter(val.id,$event)"> 
                            <span class="custom-control-indicator"></span> 
                            <span class="custom-control-description d-block w-100">
                                {{ val.title || val.name }} 
                                
                                
                                
                                <span v-if="val.count" class="float-right ml-2">({{ val.count }})</span>
                            </span>
                        </label>
                    </li>
                    <li v-if="filterValues.length>20 && !showAll" class="text-center">
                        <a href="" @click.prevent="showAll=true">(Show All..)</a>
                    </li>
                </ul>

                <div v-if="schema.values.length>8" class="filter-footer">
                    &nbsp;<a class="btn btn-sm btn-light float-right" @click="showFilterList=!showFilterList">done</a>
                </div>
            </div>
        </transition>
        
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
                filterValues:(this.schema.values || []).slice(), //get a copy of the values so when we sort, we don't modify underlying schema data
                showFilterList:false,
                filterSearch:null,
                filterSortDesc:false,
                showAll:false
            };
        },
        
        mounted() {
            this.sortFilters();
        },
        
    	computed: {
    		list: {
    			get() { return this.filter; },
    			set(v) { this.$emit("update", v); }
    		},
    		
    		allChecked(){
    		    return (this.list.length == this.schema.values.length) ? true : false;
    		},
    		
    		isActive(){
    		    if(!this.list) return false;
    		    else if(this.list.length<=0) return false;
    		    else if(this.list.length===1 && this.list[0]=='all') return false;
    		    else if(this.allChecked) return false;
    		    else return true;
    		}
    	},
        
        watch: {
            filterSortDesc(){
                this.sortFilters();
            },
            
            filterSearch(){
                if(this.filterSearch) this.filterValues = this.schema.values.filter((v) => v.name.toLowerCase().indexOf(this.filterSearch) > -1);
                else this.filterValues = this.schema.values;
            },
            schema() {
                this.filterValues = (this.schema.values || []).slice();
            }
        },
        
        methods: {
            toggleAll(e){
                if(e.target.checked) this._fillFromSchema();
                else this._empty();
            },
            
            toggleFilter(val,e){
                if(e.target.checked){
                    if(this.list.indexOf(val) === -1) this.list.push(val);
                }else this.list.splice(this.list.indexOf(val), 1);
            },
            
            sortFilters(){
                this.sorter(this.filterValues,this.filterSortDesc,'name');
            },
            
            inSet(val){
                return (this.list.indexOf(val) === -1) ? false : true;
            },
            
            getFilterName(key){
                let sel = this.schema.values.find((v) => { return v.id==key; });
                return (sel) ? sel.name.substring(0,19) : String(key).toUpperCase();
            },

            _fillFromSchema(){
                this.list = this.schema.values.map((v) => { return v.id; });
            },
            
            _empty(){
                this.list = [];
            }
        }
    };
</script>

<style>
  
</style>