<template>
    <div>
            <span v-if="type==='count'" class="agg-data" :class="{'show-red':aggTodayCount}"> {{ aggCount }}</span>
            <span v-else-if="type==='today'" class="agg-data" :class="{'show-green':aggTodayCount}"> {{ aggTodayCount }}</span>
            <span v-else-if="type==='dollar'" class="agg-data"> ${{ aggCount | dollar | formatMoney }}</span>
            <span v-else></span>


            <span v-if="isNumeric(aggCompare) && isNumeric(compareDiff)" :title="compareTooltipText" style="font-size: .6em;" :class="{difference: true, 'change-positive':comparePositive,'change-negative':compareNegative}">
                <i :class="{
                    'hotbox-icon':true,
                    'hotbox-icon-small-triangle-up':comparePositive,
                    'hotbox-icon-small-triangle-down':compareNegative
                    }" style="letter-spacing: -.2em;"></i>
                <span v-if="compareDiff!==0">{{ compareDiff | formatPercent }}%</span>
            </span>

<!--            <span v-if="type==='dollar'" class="agg-data"> ${{ aggCompare | dollar | formatMoney  }}</span>
            <span v-else></span>-->

    </div>
</template>

<script>
    export default {
        props: {
            module: {
                type: String,
                default: null
            },
            model: {
                type: String,
                default: null
            },
            field: {
                type: String,
                default: null
            },
            fieldCompare: {
                type: String,
                default: null
            },
            type: {
                type: String,
                default: 'count'
            }
        },

        computed: {
            aggData() {
                if (this.$store.state[this.module])
                    if (this.$store.state[this.module][this.model.toLowerCase() + 'Schema'])
                        return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'].agg || null;
                return null;
            },

            aggCount() {
                if (this.aggData) {
                    if (this.field in this.aggData) {
                        return this.aggData[this.field];
                    }
                }
                return null;
                //return (this.aggData) ? this.aggData[this.field] || null : null;
            },

            aggTodayCount() {
                if (this.aggData) {
                    const key = this.field + '_today';
                    if (key in this.aggData) {
                        return this.aggData[key];
                    }
                }
                return null;
                //return (this.aggData) ? this.aggData[this.field + '_today'] || null : null;
            },

            aggCompare() {
                if (this.aggData) {
                    if (this.fieldCompare in this.aggData) {
                        return this.aggData[this.fieldCompare];
                    }
                }
                return null;
                //return (this.aggData) ? this.aggData[this.fieldCompare] || null : null;
            },

            compareDiff() {
                if (this.aggCompare && this.aggCount && this.isNumeric(this.aggCompare) && this.isNumeric(this.aggCount)) {
                    return Math.round(100*((this.aggCount-this.aggCompare)/(this.aggCompare)));
                }
                return 0;
            },

            compareTooltipText() {
                if (this.isNumeric(this.aggCount) && this.isNumeric(this.aggCompare)) {
                    if (this.type==='dollar') {
                        return `Last 30 ($${this.$options.filters.formatMoney(this.aggCount)}) vs Previous 30 ($${this.$options.filters.formatMoney(this.aggCompare)})`;
                    } else {
                        return `Last 30 (${this.aggCount}) vs Previous 30 (${this.aggCompare})`;
                    }
                }
            },

            comparePositive() {
                return this.compareDiff>0 || this.aggCount>this.aggCompare;
            },

            compareNegative() {
                return this.compareDiff<0 || this.aggCount<this.aggCompare;
            }
        },

        methods: {
            isNumeric(v) {
                return !isNaN(parseInt(v)); //string of number is ok
            }
        },

        filters: {
            formatMoney(value) {
                return Number(parseFloat(value).toFixed(2)).toLocaleString('en', {
                    minimumFractionDigits: 2
                });
            },

            formatPercent(value) {
                return Number(parseFloat(value).toFixed(0)).toLocaleString('en', {
                    minimumFractionDigits: 0
                });
            }
        }
    }
</script>

<style>
    .difference.change-positive {
        color: green;
    }
    .difference.change-negative {
        color: red;
    }
</style>