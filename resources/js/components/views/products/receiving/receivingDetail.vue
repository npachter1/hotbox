<template>
    <div v-if="receiving && schema" class="card card-stats px-3 justify-content-center" style="border-bottom:1px solid #333">
        <form>
            <fieldset>

                <div class="card-header">

                </div>
                <div class="card-body">

                    <h5 class="mt-2">Items</h5>

                    <table class="b-table b-table-striped b-table-condensed table-nested">
                      <thead>
                        <tr>
                          <th width="65%">Item</th>
                          <th width="15%">Qty Requested</th>
                          <th width="15%">Qty Received</th>
                          <th v-if="canPrintTag" width="5%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item,iid) in receiving.items" :key="item.id" :class="{'table-danger':item.status=='exception'}">
                            <td width="65%">
                                <span v-if="item.product">
                                    <img :src="getProductImageUrl(item)" class="responsive float-left mr-2" width="65 mr-2">
                                    {{ item.item_barcode }}<br><span v-if="item.product" class="small">{{ item.product.name }}</span>
                                </span>
                                <span v-if="item.package">
                                    <span class="small"><i class="hotbox-icon hotbox-icon-tag-content"></i> {{ item.metrc_tag }}</span>
                                </span>
                            </td>
                            <td width="15%">
                                {{ Number(item.quantity_requested / (item.amount_unit || 1)).toFixed(4) }}<span class="small"></span></td>
                            <td width="15%">
                                <span v-if="receiving.status=='received' && item.status!='exception'"><b>{{ item.quantity_received | dollar(4) }}<span class="small">{{ item.unit_of_measure }}</span></b></span>
                            </td>
                            <td v-if="canPrintTag" style="width:5%; text-align: center;">
                                <i v-if="item.inventory_id" class="hotbox-icon hotbox-icon-tag-line" style="cursor: pointer;" title="Print Inventory Tag(s)" @click="$emit('tagging',item.inventory_id)"/>
                            </td>
                          </tr>
                        </tbody>
                    </table>


                <div class="col-12 clearfix mt-4 mb-3 text-center">
                   <!-- <span class="btn btn-md btn-default">Email Vendor</span> -->
                    <router-link v-if="receiving.status=='confirmed'" :to="{name: model.toLowerCase()+'_edit',params:{id:receiving.id}}" tag="a" class="btn btn-ms btn-primary">
                        <i class="hotbox-icon hotbox-icon-notes"></i> EDIT/RECEIVE ITEMS
                    </router-link>
                    <div style="display:inline;color:white;">
                        <form-button :disabled="isArchived || ['received','admin'].includes(receiving.status)" @click="$emit('archive')" text="Archive Receiving" show-disabled disabled-title="Archiving Unavailable" :disabled-text="isArchived ? 'Receiving already archived.' : 'Status is Received'"/>
                    </div>
                </div>

                </div>

            </fieldset>
        </form>
        <loading :display="isLoading" type="loadModal"/>
    </div>
    <div v-else>
        <loading :display="(schema && receiving) ? false : true" type="loadModal"/>
    </div>
</template>
<script>

    import Receiving from '../../../../models/Receiving';
    import _ from 'lodash';

    export default {
        props: {
            id: {
                type: Number,
                default: null
            },
            model: {
                type: String,
                default: 'Receiving'
            },
            module: {
                type: String,
                default: 'products',
            }
        },

        data() {
            return {
                isLoading: false,
                receiving: null,
                itemState: 'save',
            };
        },

        components: {},

        async mounted() {
            this.isLoading = true;
            if (!this.schema) await this.$store.dispatch(this.module + '/setSchemas', 'receiving');

            if (this.id) {
                Receiving.find(this.id).then(response => {
                    this.receiving = new Receiving(response).withDefaults(this.schema, false);
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({
                        status: 400,
                        data: {message: 'We had a hiccup fetching the data - Please try again later.'}
                    });
                });
            } else {
                this.isLoading = false;
                this.$announcer({
                    status: 422,
                    data: {message: 'Whoops - couldnt find the associated record - Please try again later.'}
                });
            }
        },

        computed: {
            schema() {
                return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
            },
            canPrintTag() {
                return this.receiving.items.some(e=>!!e.inventory_id); //at least one item has an inventory_id
            },
            isArchived() {
                return !!(this.receiving.archived_at);
            }
        },

        watch: {
            receiving: {
                handler(newVal, oldVal) {
                    if (!oldVal) this.resetDirty();

                    this.isDirty = oldVal && !_.isEqual(newVal, this.initialReceiving);
                    this.itemState = (oldVal) ? 'save changes' : (newVal.id) ? 'save' : 'create';
                },
                deep: true
            },
            'receiving.preferences'(to, from) {
                if (to && from)
                    if (to.length != from.length) this.autoSave();
            }
        },

        methods: {
            resetDirty() {
                this.initialReceiving = _.cloneDeep(this.receiving);
                this.isDirty = false;
                this.editMode = false;
            },

            autoSave(confirm = false) {
                if (!this.receiving) return false;                   // dont autosave a new entry unless pressing button (ie confirming)
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        if (!confirm) this.debounceSave();
                        else {
                            console.log('no debounce');
                            this._save(true);
                        }
                    } else if (confirm == true) {
                        this.$announcer({
                            status: 422,
                            data: {message: 'Whoops, Please check and correct inputs in order to continue.'}
                        });
                    } else this.$validator.reset();                              // if not validated or confirming, clear validation errors..
                });
            },

            debounceSave: _.debounce(function () { //debounce returns function we need to be able to call multiple times
                this._save();
            }, 900),

            _save(confirm = false) {
                this.itemState = 'saving..';
                this.receiving.save().then(response => {
                    this.$announcer({status: 200, data: {message: 'The receivings preferences have been saved'}});
                    this.itemState = 'saved';
                    this.resetDirty();
                }).catch(error => {
                    this.$announcer(error.response);
                    this.itemState = 'resave';
                });
            },

            cancelEdit() {
                this.receiving.comments = this.initialReceiving.comments;
                this.editMode = false
            }
        },

        inject: ['$validator']
    };
</script>

<style scoped>
    .comments-row .hotbox-icon {
        cursor: pointer;
        color:#9A9A9A;
        vertical-align: middle;
    }
    .comments-row label {
        cursor: pointer;
    }
</style>
