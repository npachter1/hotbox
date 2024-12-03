<template>
    <div class="form-row align-items-center">
        <div class="col-auto">
            <select
                    v-model="month"
                    class="form-control">
                <option value="-1" selected></option>
                <option value="0">January</option>
                <option value="1">February</option>
                <option value="2">March</option>
                <option value="3">April</option>
                <option value="4">May</option>
                <option value="5">June</option>
                <option value="6">July</option>
                <option value="7">August</option>
                <option value="8">September</option>
                <option value="9">October</option>
                <option value="10">November</option>
                <option value="11">December</option>
            </select>
        </div>
        <div class="col-auto">
            <select
                    v-model="day"
                    class="form-control">
                <option value="" selected></option>
                <option v-for="n in 31" v-bind:key="n" :value="n">{{ n }}</option>
            </select>
        </div>
        <div class="col-auto">
            <select
                    v-model="year"
                    class="form-control">
                <option value="" selected></option>
                <option v-for="n in years" v-bind:key="n" :value="n">{{ n }}</option>
            </select>
        </div>
    </div>
</template>


<script>
    export default {
        props: {
            minAge: {
                type: Number,
                default: 0
            },
            maxAge: {
                type: Number,
                default: 110
            },
            resetFlag: {
                type: Boolean,
                default: false
            },
            defaultValue: {
                type: [String],
                default: null
            }
        },
        data() {
            return {
                month: -1,
                day: 0,
                year: 0
            }
        },
        mounted() {
        },
        computed: {
            myDate: function () {
                if (this.year && this.month >= 0 && this.day) {
                    return new Date(this.year, this.month, this.day);
                }
                return null;
            },
            formattedDate: function () {
                if (this.year && this.month >= 0 && this.day) {
                    return moment([this.year, this.month, this.day]).format('YYYY-MM-DD');
                }
                return null;
            },
            currentYear: function () {
                return new Date().getUTCFullYear();
            },
            maxYear: function () {
                return this.currentYear - this.minAge;
            },
            minYear: function () {
                return this.maxYear - this.maxAge;
            },
            years: function () {
                if (this.maxYear > this.minYear) {
                    return new Array(this.maxYear - this.minYear).fill(this.maxYear).map((n, i) => n - i);
                }
            },
            currentAge: function () {
                if (this.myDate) {
                    return this.getAge(this.myDate);
                }
                return null;
            }
        },
        watch: {
            month: function () {
                this.$emit('birthdayInputChangeValue', this.formattedDate, this.currentAge);
            },
            day: function () {
                this.$emit('birthdayInputChangeValue', this.formattedDate, this.currentAge);
            },
            year: function () {
                this.$emit('birthdayInputChangeValue', this.formattedDate, this.currentAge);
            },
            resetFlag: function () {
                this.month = -1;
                this.day = this.year = 0;
            },
            defaultValue(newValue, oldValue) {
                if (typeof (newValue) === 'string') newValue = new Date(newValue);
                this.year = newValue.getUTCFullYear();
                this.month = newValue.getUTCMonth();
                this.day = newValue.getUTCDate();
            }
        },
        methods: {
            getAge: function (birthDate) {
                //return moment().diff(moment(birthDate), 'years')
                let today = new Date();
                let age = today.getUTCFullYear() - birthDate.getUTCFullYear();
                let m = today.getUTCMonth() - birthDate.getUTCMonth();
                if (m < 0 || (m === 0 && today.getUTCDate() < birthDate.getUTCDate())) {
                    age--;
                }
                return age;
            }
        }
    }
</script>
<style scoped>


</style>