<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Inventory Warehouse</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/products">Inventory</Link>
                    </li>
                    <li>
                        <span>Inventory Warehouse</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <label class="d-flex align-items-center gap-1">Show
                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </label>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="form">
                            <!-- <Select2
                                v-model="store_id"
                                placeholder="Select Warehouse"
                                :settings="{
                                    ajax: {
                                        url: '/data/warehouse',
                                        dataType: 'json',
                                        method: 'POST',
                                        data: function (params) {
                                            return {
                                                search: params.term,
                                                _token: csrf,
                                            };
                                        },
                                        processResults: function (data) {
                                            return {
                                                results: data.map(function (warehouse) {
                                                    return {
                                                            text: `${warehouse.name}`,
                                                            id: warehouse.id,
                                                            data: warehouse,
                                                        };
                                                    })
                                                };
                                            },
                                        },
                                    }"
                                    class="w-full"
                            /> -->
                        </div>
                    </div>
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                    <div class="btn-group" v-if="permissions.includes('create-product')">
                        <!-- <Link v-if="!$page.url.startsWith('/products-inactive')" class="btn btn-purple" href="/products/create">Add New Item</Link> -->
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Warehouse ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Location</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>UOM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Qty on Stock</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Committed Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Ordered Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Requested Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1">
                                <span>Reserved Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sp in products.data" :key="sp.id">
                            <td class="text-nowrap">
                                {{ sp.location.code }}
                            </td>
                            <td class="text-nowrap">
                                {{ sp.location.name }}
                            </td>
                            <td class="text-nowrap">
                                <b class="text-purple">
                                    <Link :href="`/products/${sp.product.id}`"><span>{{ sp.product.sku }}</span></Link>
                                </b>
                            </td>
                            <td style="width: 40%;">
                                <b class="text-purple">
                                    <Link :href="`/products/${sp.product.id}`"><span>{{ sp.product.name }}</span></Link>
                                </b>
                            </td>
                            <td class="text-nowrap">{{ sp.product.uom.code }}</td>
                            <td class="text-nowrap">{{sp.stock}}</td>
                            <td class="text-nowrap">{{sp.committed_qty}}</td>
                            <td class="text-nowrap">{{sp.ordered_qty}}</td>
                            <td class="text-nowrap">{{sp.requested_qty}}</td>
                            <td class="text-nowrap">{{sp.reserved_qty}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ products.from }} to {{ products.to }} of {{ products.total }} entries</p>
                <Pagination :links="products.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, useForm, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    products: Object,
    stores: Object,
    filters: Object,
    csrf: String,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

let store_id = ref(usePage().props.value.filters.store_id ? Number(usePage().props.value.filters.store_id) : null);

const filter = () => {
    Inertia.get(
        "/inventory-warehouse",
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
            store_id: store_id.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

// Watchers
watch(
    search,
    debounce(() => {
        filter();
    }, 500)
);

watch([order, by, paginate], () => {
    filter();
});

// Watch for changes in store_id and trigger filter function
watch(store_id, () => {
    filter();
});

const sort = (data) => {
    order.value = data;

    if (by.value == "asc") {
        by.value = "desc";
    } else {
        by.value = "asc";
    }
};

const destroy = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/products/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
