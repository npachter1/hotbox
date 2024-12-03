<template>
    <div class="card pos-inventory-card pos-category-card" style="cursor: pointer;">
        <div class="card-image">
            <img :src="category.public_img || '/images/none.jpg'" @click="$emit('selected',category.id)"/>
        </div>
        <div class="card-body">
            <h5 class="card-title" @click="$emit('pull')">
                {{ category.name | cleanCategoryName }}

                <span v-if="category.products_count>0" class="small float-right">
                    {{ Number(category.items_count).toFixed(0) }}
                </span>
            </h5>
        </div>
    </div>
</template>
<script>

    export default {
        components: {},
        props: {
            category: {
                type: Object,
                default: null
            }
        },
        data() {
            return {}
        },
        methods: {},
        filters: {
            cleanCategoryName(s) {
                return s.replace(/_/g, ' ');
            }
        }
    }
</script>
<style scoped>

    .price-overlay {
        position: absolute;
        top: 5px;
        right: 5px;
        padding: 0;
        margin: 0;
        z-index: 2;
    }

    .pos-inventory-card {
        box-shadow: 0 1px 15px 1px rgba(0, 0, 0, 0.4);
    }

    .pos-inventory-card .card-title {
        font-size: 0.875em;
        margin: 0;
        color: #212529;
    }

    .pos-inventory-card .list-group-price .list-group-item {
        background-color: #efefef;
        padding: 5px 10px;
        border: none;
    }

    .pos-inventory-card .card-body {
        padding: 10px;
    }

    .pos-inventory-card .rounded {
        border-bottom-left-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    .badge-inventory-price {
        text-transform: none;
        cursor: pointer;
    }

    .sh-red {
        color: red;
    }

    .sh-green {
        color: green;
    }

    .card.pos-category-card .card-body {
        background-color: #dcdde1;
    }

    .card.pos-category-card .card-body .card-title {
        text-transform: uppercase;
    }
</style>
