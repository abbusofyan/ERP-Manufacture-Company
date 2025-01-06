<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Purchase Order</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li><Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link></li>
                    <li class="active"><span>Purchase Order</span></li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                <div class="perpages"><span>Show</span>
                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search" v-model="search">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>PO NO</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Vendor</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Vendor ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Created Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Delivery Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Ordered Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Sub Total</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Discount</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Total</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap">
                        <div class="flex items-center justify-between gap-1"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th class="text-nowrap"></th>
                    </thead>
                    <tbody>
                    <tr v-for="(order, index) in orders.data" :key="order.id">
                        <td class="text-nowrap">
                            <div class="text-purple font-semibold">
                                <Link :href="`/orders/${order.id}`"><span>{{ order.code }}</span></Link>
                            </div>
                        </td>
                        <td class="text-nowrap">{{ order.vendor.name }}</td>
                        <td class="text-nowrap">{{ order.vendor.code }}</td>
                        <td class="text-nowrap">{{ $filters.formatDateTime(order.created_at) }}</td>
                        <td class="text-nowrap">{{ order.delivery_date ? $filters.formatDateTime(order.delivery_date) : '' }}</td>
                        <td class="text-nowrap">
                            {{ order.total_quantity }}
                        </td>
                        <td class="text-nowrap">${{ order.sub_total }}</td>
                        <td class="text-nowrap">{{ order.gst }}</td>
                        <td class="text-nowrap">${{ order.total }}</td>
                        <td class="text-nowrap">
                            <div class="el-tag" :class="order.status_class">
                                {{ order.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-order')">
                                        <Link :href="`/orders/${order.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-order') && [1].includes(order.status)">
                                        <a class="text-green" @click="updateStatus(order.id,3)" href="javascript:void(0)"><span class="icon fa-solid fa-check"></span><span>Confirm Purchase</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-order') && [3].includes(order.status)">
                                        <a class="text-orange" @click="cancel(order.id, index)" href="javascript:void(0)"><span class="icon fa-regular fa-rectangle-xmark"></span><span>Cancel Purchase</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-order') && [1].includes(order.status)">
                                        <a class="text-red" @click="updateStatus(order.id,6)" href="javascript:void(0)"><span class="icon fa-regular fa-rectangle-xmark"></span><span>Reject Purchase</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-order') && [3].includes(order.status)">
                                        <a @click="generateGoodsReceipt(order.id, index)" href="javascript:void(0)"><span class="icon fa-regular fa-file"></span><span>Generate Goods Receipt</span></a>
                                    </li>

                                    <!-- <li v-if="permissions.includes('update-order')">
                                        <Link :href="`/orders/${order.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li> -->
                                    <!-- <li v-if="permissions.includes('delete-order')">
                                        <a href="javascript:void(0)" @click="destroy(order.id)">
                                            <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span>
                                        </a>
                                    </li> -->
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} entries</p>
                <Pagination :links="orders.links"/>
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

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    orders: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/orders",
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

const updateStatus = (id, value) => {
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
            Inertia.post(`/orders/update-status/${id}`, {
                status: value,
            }, {
                preserveScroll: true,
            });
        }
    });
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
            Inertia.delete(`/orders/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

const cancel = (id, index) => {
    if (props.orders.data[index].goods_receipt.length > 0) {
        return Swal.fire({
            title: "Cannot cancel purchase order!",
            text: "There is an active goods receipt for this purchase order. Please void it first",
            icon: "warning",
            showCancelButton: false,
            confirmButtonText: "OK!",
        })
    }
    Swal.fire({
        title: "Are you sure?",
        text: "Purchase order will be canceled.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/orders/update-status/${id}`, {
                status: 5,
            }, {
                preserveScroll: true,
            });
        }
    });
};

const generateGoodsReceipt = (id, index) => {
    if (props.orders.data[index].goods_receipt.length > 0) {
        return Swal.fire({
            title: "Cannot generate new goods receipt!",
            text: "There is already an active goods receipt for this purchase order.",
            icon: "warning",
            showCancelButton: false,
            confirmButtonText: "OK!",
        })
    }
    Swal.fire({
        title: "Are you sure?",
        text: "New goods receipt will be generated.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/orders/generate-goods-receipt/${id}`, {
                status: 5,
            }, {
                preserveScroll: true,
            });
        }
    });
};

</script>
