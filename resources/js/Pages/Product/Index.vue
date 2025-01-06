<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Item Master Data</div>
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
                        <span>List</span>
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
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                    <div class="btn-group" v-if="permissions.includes('create-product')">
                        <Link v-if="!$page.url.startsWith('/products-inactive')" class="btn btn-purple" href="/products/create">Add New Item</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>CATEGORY ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Category</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Item Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
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
                                <span>UOM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>Selling Price</th>
                        <th>Auto Reorder</th>
                        <th>
                            <span>Action</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(product, index) in products.data" :key="product.id">
                        <td>
                            {{ product.category.prefix }}
                        </td>
                        <td>
                            {{ product.category.name }}
                        </td>
                        <td>
                            {{ product.assembly ? 'Assembly' : (product.finish_goods ? 'Finish Goods' : 'Single Item') }}
                        </td>
                        <!-- <td>{{ product.assembly ? 'Assembly' : 'Single Item' }}</td> -->
                        <td>
                            <b class="text-purple">
                                <Link :href="`/products/${product.id}`"><span>{{ product.sku }}</span></Link>
                            </b>
                        </td>
                        <td>
                            <b class="text-purple">
                                <Link :href="`/products/${product.id}`"><span>{{ product.name }}</span></Link>
                            </b>
                        </td>
                        <td>{{product.uom.code}}</td>
                        <td>{{product.selling_price ? "$"+product.selling_price : ''}}</td>
                        <td>{{product.auto_reorder ? 'Yes' : '-'}}</td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-product') && !$page.url.startsWith('/products-inactive')">
                                        <Link :href="`/products/${product.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-product') && !$page.url.startsWith('/products-inactive')">
                                        <Link v-if="product.assembly_id" :href="`/assembly/${product.assembly_id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                        <Link v-else :href="`/products/${product.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>

                                    </li>
                                    <!-- <li v-if="permissions.includes('update-product')">
                                        <Link method="POST" :href="`/products/${product.id}/switch-status`">
                                            <span class="icon">
                                                <img v-if="Number(product.status) === 1" src="../../../assets/img/switch-1.png" alt="">
                                                <img v-else src="../../../assets/img/switch-2.png" alt="">
                                            </span>
                                            <span v-if="Number(product.status) === 1" class="text-warning">Set to Inactive</span>
                                            <span v-else class="text-success">Set to Active</span>
                                        </Link>
                                    </li> -->
                                    <li>
                                        <a v-if="Number(product.status === 1)" @click="changeStatus(product, 'inactive')" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/switch-1.png"></span><span class="text-orange">Set as Inactive</span></a>
                                        <a v-else @click="changeStatus(product, 'active')" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/switch-2.png"></span><span class="text-green">Set as Active</span></a>
                                    </li>
                                    <li v-if="permissions.includes('delete-product') && !$page.url.startsWith('/products-inactive')">
                                        <a @click="destroy(product.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"><span class="icon fa fa-history"></span><span>Check Item Movement Status</span></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
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
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {useToast} from "vue-toastification";
const toast = useToast();

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    products: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/products",
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

watch(
    search,
    debounce(() => {
        filter();
    }, 500)
);

watch([order, by, paginate], () => {
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

const changeStatus = (product, newStatus) => {
    if (newStatus == 'inactive') {
        if (product.stock> 0) {
            return Swal.fire({
                title: "Cannot set item as inactive",
                text: "This item has a stock",
                icon: "warning",
                showCancelButton: false,
                confirmButtonText: "OK!",
            })
        }
    }
    Swal.fire({
        title: "Are you sure?",
        text: "The item will be " + newStatus,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/products/${product.id}/switch-status`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
