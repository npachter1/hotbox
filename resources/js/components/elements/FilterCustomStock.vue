<template>
                    <div class="filter-wherein" :class="{'active':(customFilter.useStockExpire || customFilter.useStockLow)}">
                        <a href="" @click.prevent="showFilterList=!showFilterList">
                            <b><i class="hotbox-icon hotbox-icon-filter"></i> Stock Status</b>
                            <i>{{customFilterTitle}}</i>
                            <i class="caret fas fa-caret-down" :class="{'rotate-caret':showFilterList}"></i>
                        </a>
                        <transition name="hb-slide">
                            <div v-show="showFilterList" class="drop-filter">
                                <ul class="list-group">
                                    <li class="sub-menu-title list-group-control">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="stock_filters_ALL" v-model="showAll">
                                            <span class="custom-control-indicator"></span> 
                                            <span class="custom-control-description">All Stock Status</span>
                                        </label> 
                                        <i class="float-right ti-close show-grey" @click.default="showFilterList=!showFilterList"></i>
                                    </li>

                                    <li class="list-group-control">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="use_stock_expire_1" :checked="customFilter.useStockExpire" @click="customFilter.useStockExpire=!customFilter.useStockExpire">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description d-block w-100">
                                                Expiring in Next (# Days):
                                                <form-number class="" :min="0" :value="daysFromToday(customFilter.stockExpire)" @input="(v)=>{customFilter.stockExpire=$moment().add(v,'days').format('MM/DD/YY')}" :schema="{name:'stock_expire_filter'}" :hideLabel="true" />
                                            </span>
                                        </label>
                                    </li>

                                    <li class="list-group-control">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="use_stock_last_2" :checked="customFilter.useStockLast" @click="customFilter.useStockLast=!customFilter.useStockLast">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description d-block w-100">
                                                Last Received (# Days Ago)
                                                <form-number class="" :min="0" :value="daysFromToday(customFilter.stockLast, true)" @input="(v)=>{customFilter.stockLast=$moment().subtract(v,'days').format('MM/DD/YY')}" :schema="{name:'stock_last_filter'}" :hideLabel="true" />
                                            </span>
                                        </label>
                                    </li>

                                    <li class="list-group-control">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="use_stock_low_2" :checked="customFilter.useStockLow" @click="customFilter.useStockLow=!customFilter.useStockLow"> 
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description d-block w-100">
                                                Show Qty At/Below
                                                <form-number class="" v-model="customFilter.stockLow" :schema="{name:'stock_low_filter'}" :hideLabel="true" />
                                            </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </transition>
                    </div>
</template>


<script>

    export default {
        props: {
            filter: {
                type: Object,
                default: () => {}
            }
        },
        
        data(){
            return {
                showFilterList:false,
                showAll: true,
            };
        },
        
        mounted() {

        },
        
    	computed: {
    		customFilter: {
    			get() { return this.filter; },
    			set(v) { this.$emit("update", v); }
    		},
    		
            customFilterTitle() {
    		    let returnText=[];
    		    if (this.customFilter.useStockExpire) returnText.push(`Expire By ${this.customFilter.stockExpire}`);
                if (this.customFilter.useStockLast) returnText.push(`Received Before ${this.customFilter.stockLast}`);
                if (this.customFilter.useStockLow) returnText.push(`< ${this.customFilter.stockLow} Left`);
                if (returnText.length===0) return '(All)';
                return returnText.join(', ');
            }
    	},
        
        watch: {
            showAll(newValue) {
                if (newValue) {
                    this.customFilter.useStockExpire=false;
                    this.customFilter.useStockLast=false;
                    this.customFilter.useStockLow=false;
                }
            },

            filter: {
                handler: function(newValue) {
                    this.showAll=(!newValue.useStockExpire && !newValue.useStockLast && !newValue.useStockLow);
                },
                deep: true,
            },
        },
        
        methods: {
            toggleAll(v) {
                console.log(v);
            },

            daysFromToday(dateString, negate = false) {
                return (this.$moment(dateString,'MM/DD/YY').startOf('day').diff(this.$moment().startOf('day'),'days')) * (negate ? -1 : 1);
            },
        }
    };
</script>

<style>
  
</style>