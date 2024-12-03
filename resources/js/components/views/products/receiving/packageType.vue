<template>
    <div>
        <!--
        vue-multiselect offers no direct way to track when a user clicks on an already selected item so user can "edit" prepackaged amount
        so, instead, I set the multiselect to allow unselecting items then I ignore the unselection in @input (but catch the @remove firing).
        -->
        <multiselect
                v-if="!!options"
                :value="packageType"
                @input="(val)=>{if (val) {packageType=val; $emit('input',packageType);}}"
                placeholder="Click to Select"
                :show-labels="packageType.value==='package'"
                :allow-empty="true"
                label="name"
                track-by="value"
                @remove="$emit('input',packageType)"
                deselect-label="Edit"
                selected-label=""
                select-label=""
                :options="options"></multiselect>
    </div>
</template>

<script>
    import _ from 'lodash';

    export default {
        name: "packageType",
        props: {
            value: {
                type: Object,
                default: ()=>{return {}},
            },
            packageTypeOptions: {
                type: Array,
                default: [],
            },
            amount: {
                type: String,
                default: '',
            },
            unit: {
                type: String,
                default: '',
            }
        },
        data(){
            return {
                packageType: null,
                options: null,
            }
        },
        mounted() {
            this.packageType = this.value;
            this.options = _.cloneDeep(this.packageTypeOptions);
            this.updatePrepackageText();
        },
        methods: {
            updatePrepackageText() {
                let amt = Number(this.amount).toFixed(2);
                if (this.packageType.value==='package') {
                    //show package label with current portion amount
                    this.packageType.name=`Packaged (${amt}${this.unit}/unit)`;
                    this.options.find(e=>e.value==='package').name=`Prepackaged (${amt}/${this.unit})`;
                } else {
                    //this.packageType.name='Prepackaged';
                    this.options.find(e=>e.value==='package').name=`Prepackaged`;
                }
            },
        },
        watch: {
            value(newValue, oldValue) {
                this.updatePrepackageText();
                this.packageType = newValue;
            },
            packageType(newValue, oldValue) {
                //if ((oldValue && newValue) && (newValue!==oldValue)) this.$emit('input',newValue);
            },
            amount() {
                this.updatePrepackageText();
            },
            unit() {
                this.updatePrepackageText();
            },
        }
    }
</script>

<style scoped>
    >>> div.multiselect div.multiselect__tags {
        padding-top: 0;
    }
    >>> div.multiselect.multiselect--active input {
        padding-top:5px;
    }
    >>> span.multiselect__placeholder {
        margin:4px;
    }
    >>> div.multiselect__select {
        top:0;
    }
    >>> div.multiselect,
    >>> div.multiselect div.multiselect__tags,
    >>> div.multiselect div.multiselect__tags input,
    >>> div.multiselect div.multiselect__tags span.multiselect__single {
        background-color: transparent;
        font-size: small;
    }
</style>