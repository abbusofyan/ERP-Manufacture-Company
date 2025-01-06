<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Item Movement</div>
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
                        <span>ItemMovement</span>
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
                            <Select2
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
                            />
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
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Warehouse</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Movement</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Cost</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Movement Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <!-- <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Transaction Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th> -->
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Transaction ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Transaction Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item_movement in itemMovements.data" :key="item_movement.id">
                            <td>
                                {{ item_movement.store.name }}
                            </td>
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/products/${item_movement.product_id}`"><span>{{ item_movement.product.sku }}</span></Link>
                                </b>
                            </td>
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/products/${item_movement.product_id}`"><span>{{ item_movement.product.name }}</span></Link>
                                </b>
                            </td>
                            <td>{{ item_movement.movement_type == '+' ? 'In' : 'Out' }}</td>
                            <td>{{ item_movement.movement_type == '-' ? '-' + item_movement.quantity : item_movement.quantity }}</td>
                            <td>{{ item_movement.product.last_purchase_cost }}</td>
                            <td>{{ item_movement.transaction_type_text }}</td>
                            <td>
                                <b class="text-purple">
                                    <Link v-if="item_movement.transaction" :href="`/${item_movement.transaction_type_route}/${item_movement.transaction?.id}`"><span>{{ item_movement.transaction.code }}</span></Link>
                                    <a v-else href="javascript:void(0)"><span>{{ item_movement.transaction_code }}</span></a>
                                </b>
                            </td>
                            <td>{{ $filters.formatDateTime(item_movement.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ itemMovements.from }} to {{ itemMovements.to }} of {{ itemMovements.total }} entries</p>
                <Pagination :links="itemMovements.links"/>
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
    itemMovements: Object,
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
        "/item-movement",
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
