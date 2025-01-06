<template>
    <div class="table-container">
        <div class="table-responsive">
            <table class="table select-rows">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>UOM</th>
                        <th>Required Qty</th>
                        <th>Convert Qty</th>
                        <th>Qty on Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in props.data" :key="index">
                        <td>{{ item.item_id }}</td>
                        <td>
                            <b class="text-purple">
                                <Link :href="`/products/${item.product_id}`"><span>{{ item.item_name }}</span></Link>
                            </b>
                        </td>
                        <td>{{ item.uom }}</td>
                        <td>{{ item.required_qty }}</td>
                        <td>{{ item.convert_qty }}</td>
                        <td>
                            {{ item.qty_on_stock }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    data: Array,
    errors: Array,
    columns: Array
});

console.log(props.data.errors);
</script>

<style>
.table-container {
    max-height: 400px; /* Adjust as needed */
    overflow-y: auto;
}

.table-responsive {
    display: inline-block;
    width: 100%; /* Ensures responsive table width */
    overflow-x: auto; /* Horizontal scrolling */
}

.table thead th {
    position: sticky;
    top: 0;
    background-color: white;
    z-index: 1;
}
</style>
