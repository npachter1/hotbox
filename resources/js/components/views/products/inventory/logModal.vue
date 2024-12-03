<template>
    <div v-if="item && schema" class="col-12">

        <h3 v-if="item.id" class="mb-0">Barcode ID: {{ item.item_barcode }}</h3>
        <span class="small"> - Metrc tag: {{ item.metrc_tag }}</span><br>
        <span class="small"> - Received {{ item.quantity_received | dollar(4) | trimQuantity }}/{{ item.unit_of_measure }} on {{ item.created_at | localDate }}</span>

        <div v-if="item.adjustments" class="table-responsive mt-3 mb-4">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Logged At</th>
                    <th>Type</th>
                    <th>Note</th>
                    <th>Adjust</th>
                    <th>Tally</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="adjustment_history in item.adjustments"
                    :key="adjustment_history.id">
                    <td>
                        {{ adjustment_history.created_at | localDate('MM/DD/YY LT') }}<br>
                        <span class="small">Reviewed: {{ adjustment_history.reviewed_at | localDate('MM/DD/YY LT') }}</span>
                    </td>
                    <td>
                        {{ adjustment_history.type | ucwords }}<br>
                        <span class="small" v-if="adjustment_history.type!='sale'"> - Reason: {{ adjustment_history.reason_code }}</span>
                    </td>
                    <td>
                        <span class="small">{{ adjustment_history.notes }}</span>
                    </td>
                    <td>
                        <span>{{ adjustment_history.value }}</span>
                    </td>
                    <td>
                        {{ adjustment_history.tally | dollar(4) | trimQuantity }}
                        <span class="small">/{{ item.quantity_received | dollar(4) | trimQuantity }} {{item.unit_of_measure}}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


    </div>
    <div v-else>
        <loading :display="(schema && item) ? false : true" type="loadModal"/>
    </div>
</template>

<script>

    import Item from '../../../../models/Inventory';


    export default {

        props: {
            id: {
                type: Number,
                default: null
            },
            model: {
                type: String,
                default: 'Inventory'
            },
            module: {
                type: String,
                default: 'products',
            }
        },

        data() {
            return {
                item: null,
                isLoading: false,
                isDownloading: false
            };
        },

        async mounted() {
            await this.$store.dispatch(this.module + '/setSchemas', this.model.toLowerCase()); // this is a sub resource - it loads its own schema upon modal load
            this.isLoading = true;
            if (this.id) {
                Item.find(this.id).then(response => {
                    let quantityCount = response.quantity_received * 1;
                    response.adjustments.slice().reverse().forEach(e => { //todo are we guaranteed the order to come out reverse chronological?
                        quantityCount += e.value * 1;                       // JP yes, inventoryservice::show orders by created_at DESC
                        e.tally = quantityCount;
                    });
                    this.item = new Item(response).withDefaults(this.schema, false);
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({
                        status: 400,
                        data: {message: 'We had a hiccup fetching the data - Please try again later.'}
                    });
                    this.$emit('refresh', {}, 'log');                          // will close this modal
                });
            } else {
                this.isLoading = false;
                this.$announcer({
                    status: 422,
                    data: {message: 'Whoops - couldnt find the associated record - Please try again later.'}
                });
                this.$emit('refresh', {}, 'log');
            }
        },

        methods: {},

        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
            }
        }
    };
</script>

<style>

</style>