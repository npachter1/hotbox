<template>
    <div v-cloak>
        <div class="card card-chart">
            <div class="card-header">
                <a class="float-right" style="margin-right:20px;" href="" @click.prevent="createCampaign">Create Campaign..</a>
                <a class="float-right" style="margin-right:20px;" href="" @click.prevent="createPo">Create PO..</a>
                <h5 class="card-category">
                    <router-link :to="{name:'analytics-merchandise', params: { initialChart: 'Inventory Insights'} }" tag="a">Products Expiring Soon »</router-link>
                </h5>
                <h2 class="card-title"></h2>
            </div>
            <div class="card-body" ref="cardRef">
                <div class="table-responsive" style="min-height:200px;">
                    <loading :display="isLoading" type="loadAsset" />
                    <b-table
                        small
                        id="productsExpiringTable"
                        class="table hotbox-table table-condensed"
                        style="font-size: smaller;"
                        :tbody-tr-class="rowClass"

                        v-if="itemCount>0"
                        :items="items.data"
                        :fields="['id',{key:'product.name',tdClass:'text-left'},'quantity_on_hand','expires_at']"
                        thead-class="d-none"
                        :current-page="page"
                        :per-page="pageSize">
                        <template v-slot:cell(id)="row">
                            <label class="custom-control custom-checkbox" style="margin:5px 0 0 0;">
                                <input type="checkbox" class="custom-control-input input-checkbox" :checked="selected.includes(row.item.product_id)" @change="toggleSelectedProduct($event, row.item.product_id)" /><span class="custom-control-indicator"></span>
                            </label>
                        </template>
                        <template v-slot:cell(quantity_on_hand)="row">{{ row.item.quantity_on_hand }}/{{ row.item.unit_of_measure }}</template>
                        <template v-slot:cell(expires_at)="row">{{ row.value | localDate }}</template>
                    </b-table>
                </div>
                <div class="col-12 clearfix filter-pages mb-2 bottom-paginate" ref="paginationDiv" v-if="itemCount>pageSize">
                    <b-pagination
                            v-model="page"
                            :total-rows="itemCount"
                            :per-page="pageSize"
                            hide-goto-end-buttons
                            hide-ellipsis
                            next-text="Next »"
                            prev-text="« Prev"
                            size="sm"
                            class="my-0" />
                </div>
            </div>
        </div>

        <b-modal centered ref="batchEditModal"
                 v-model="batchEditModal"
                 size="xl"
                 header-bg-variant="light"
                 header-text-variant="primary">

            <template slot="modal-header">
                <i class="modal-top-close fal ti-close" style="cursor: pointer;" @click="batchEditModal=!batchEditModal"></i>
                <h5 class="w-100 mb-0 text-center">Modify Batch of ({{ selected.length }}) Items</h5>
            </template>

            <batch-edit-modal v-if="batchEditModal"
                              :focus="batchEditFocus"
                              :ids.sync="selected"
                              @refresh="batchEditModal=false"
                              :schema="{meta:{resource:'admin/dispensary/products'}}">
            </batch-edit-modal>

            <template slot="modal-footer">
                <a @click.default="batchEditModal=!batchEditModal" class="btn btn-sm btn-light float-right">Close</a>
                <!--<span class="btn-label btn-sm btn-light float-right" style="cursor: pointer;" @click="batchEditModal=!batchEditModal">Close</span>-->
            </template>
        </b-modal>
    </div>
</template>

<script>

import Grid from '../../../models/Inventory';
import BatchEditModal from "./batchEditModal";

export default {
    props: {
    },

    components: {
        BatchEditModal,
    },

    data() {
      return {
        selected: [],
        batchEditFocus:'po',
        batchEditModal:false,

        pageSize: 8,
        page: 1,

        items: null,
        today: this.$moment(),
        thirty: this.$moment().add(30,'d'),
        isLoading:false
      };
    },

    mounted () {
        this.fetchGrid();
    },

    watch: {
    },

    computed: {
        itemCount() {
            return ((this.items||{}).data||[]).length;
        }
    },

    methods: {
        createPo() {
            if (this.selected.length===0) return this.$swal.fire({
                text: 'Select a least one product to create a PO.',
                title: 'No Products Selected',
                type: 'warning',
                confirmButtonText: 'OK',
            });
            this.batchEditFocus = 'po';
            this.batchEditModal=true;
        },

        createCampaign() {
            if (this.selected.length===0) return this.$swal.fire({
                text: 'Select a least one product to create a Campaign.',
                title: 'No Products Selected',
                type: 'warning',
                confirmButtonText: 'OK',
            });

            this.batchEditFocus = 'promote';
            this.batchEditModal=true;
        },

        rowClick(item, index, e) {
            if (!this.clickedOnCheckbox(e)) this.$router.push({name:'inventory_edit',params:{id:item.id}})
        },

        toggleSelectedProduct(e,id) {
            if (this.clickedOnCheckbox(e)) {
                if(e.target.checked) {
                    if (!this.selected.includes(id)) this.selected.push(id);
                }
                else {
                    this.selected = this.selected.filter(e=>e!==id);
                }
            }
        },

        clickedOnCheckbox(e) {
            return (((e.target||{}).classList||{value:''}).value.includes('checkbox'));
        },

        onRowSelected(items) {
            this.selected = items.map(e=>e.product.id);
        },

        renderRowBg(item,type){
            if(!item) return null;
            return {
                'table-success': this.selected.includes(item.product_id),
            };
        },

        rowClass(item) {
            if(!item) return null;

            return {
                'table-success': this.selected.includes(item.product_id),
                'table-danger': this.$moment(item.expires_at) < this.today,
                'table-warning': this.$moment(item.expires_at) < this.thirty
            };
        },

        async fetchGrid(){                                                      // get the grid data
            this.isLoading = true;
            this.items = await new Grid()
                .params({
                    is_search:1,
                    is_expiring:1
                })
                .orderBy('expires_at')
                .limit(100)
                .page(1)
                .get();

            this.isLoading = false;
            this.setCardHeight();
        },

        setCardHeight() {
            this.$nextTick(()=> {
                if (this.itemCount > this.pageSize && this.$refs.paginationDiv && this.$refs.cardRef) {
                    let paginationHeight = this.$refs.paginationDiv.clientHeight;
                    let tableHeight = this.$refs.cardRef.clientHeight;
                    this.$refs.cardRef.style.minHeight = (tableHeight + paginationHeight) + 'px';
                }
            });
        }
    }
}
</script>

<style scoped>
    >>> table#productsExpiringTable tr.table-success td {
        border-color: #8fd19e;
        background-color: #c3e6cb;
    }
</style>
