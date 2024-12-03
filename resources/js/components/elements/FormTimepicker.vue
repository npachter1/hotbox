<template>
    <div class="form-group" v-on:change.stop>
        <label v-if="hideLabel!==true" :for="'item'+schema.name" class="w-100">
            <i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title"
               v-b-tooltip.hover="schema.description"></i>
            {{ schema.title }}
            <span v-if="isRequired" class="show-red small"> *(Required)</span>
        </label>

        <multiselect
                v-model="stringTime"
                :options="timeOptions"
                :allow-empty="true"
                :show-labels="false"
                :placeholder="placeholder"
                @search-change="updateOptions"
                @input="selectInput"></multiselect>

        <span v-show="errors.has(schema.name)" class="form-text text-muted val-danger-text">{{ errors.first(schema.name) }}</span>
    </div>
</template>
<script>
    // Input: 24-hour Number or String  e.g., 1600, 830, '1600', or ''.
    // Output: 24-hour Number e.g., 1600, 830
    // User interacts in 12 or 24 hour formats like '3:30am', '1500', '3:30 am', etc

    export default {
        props: {
            value: {
                type: [String, Number],
                default: '',
            },
            schema: {
                type: Object,
                default: () => {
                }
            },
            hideLabel: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type: String,
                default: 'Select option',
            },
        },

        data() {
            return {
                stringTime: '', //what gets displayed in input on screen eg, '1:22pm'
                timeOptionsSeed: ['12:00am', '12:30am', '1:00am', '1:30am', '2:00am', '2:30am', '3:00am', '3:30am', '4:00am', '4:30am',
                    '5:00am', '5:30am', '6:00am', '6:30am', '7:00am', '7:30am', '8:00am', '8:30am', '9:00am', '9:30am', '10:00am', '10:30am', '11:00am', '11:30am',
                    '12:00pm', '12:30pm', '1:00pm', '1:30pm', '2:00pm', '2:30pm', '3:00pm', '3:30pm', '4:00pm', '4:30pm',
                    '5:00pm', '5:30pm', '6:00pm', '6:30pm', '7:00pm', '7:30pm', '8:00pm', '8:30pm', '9:00pm', '9:30pm', '10:00pm', '10:30pm', '11:00pm', '11:30pm'
                ],
                timeOptions: [], //for vue-multiselect, comes from seed above. If user types a time, we use seed and insert their new time into this array, sorted
            };
        },

        computed: {
            isRequired() {
                if (this.schema.validation) return !!(this.schema.validation.split("|").find(x => x === 'required'));
                return false;
            }
        },

        mounted() {
            this.timeOptions = this.timeOptionsSeed;
            this.setStringFromMilitary(this.value); //prop comes in military time
        },

        methods: {
            setStringFromMilitary(s) {
                const mTime = this.getMomentFromTime(s);
                if (mTime) this.stringTime = mTime.format('h:mma');
            },

            updateOptions(query) { //if user is typing into drop-down box
                if (query !== '') {
                    this.isLoading = true;
                    let tempArray = this.timeOptionsSeed.slice();
                    if (!tempArray.includes(query)) {
                        tempArray.push(query);
                        tempArray.sort((e1, e2) => {
                            const time1 = this.getMomentFromTime(e1);
                            const time2 = this.getMomentFromTime(e2);
                            if (time1 && time2) {
                                if (time1.isBefore(time2)) return -1;
                                if (time2.isBefore(time1)) return 1;
                                return 0;
                            } else if (time1) {
                                return 1;
                            } else if (time2) {
                                return -1
                            }
                            return 0;
                        });
                    }
                    this.timeOptions = tempArray.slice();
                    this.isLoading = false
                }
            },

            getMomentFromTime(s) {
                if (s===0) s='0000';
                s = (s||'').toString().replace(/\s/g, '').toLowerCase();
                const momentFormats = ["hh:mm", "h:mm", "hh:mma", "h:mma", "HH:mm", "H:mm", "HH:mma", "H:mma", "Hmm", "HHmm", "ha", "hmma"];
                if (moment(s, momentFormats, true).isValid()) return moment(s, momentFormats, true);

                return null;
            },

            selectInput(value) {
                let mTime = this.getMomentFromTime(value);
                if (mTime) {
                    let newFormat = mTime.format('h:mma');
                    this.stringTime = newFormat;
                    this.updateOptions(newFormat); //make sure this new time is in the list.
                    this.$emit('change');
                    this.$emit('input', parseInt(mTime.format('HHmm'), 10)); //e.g., '1600' (4pm) or '1730' (5:30pm) or '0130' (1:30am)
                } else {
                    console.log('invalid input');
                }
            }
        },

        inject: ['$validator']
    };
</script>