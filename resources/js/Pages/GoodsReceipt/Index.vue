<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Goods Receipt</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <span>Goods Receipt List</span>
                    </li>
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
                <table class="table">
                    <thead>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>GRN No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>PO No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Vendor</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Vendor ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>POC</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Vendor DO / Invoice Number.</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Returned</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>Action</th>
                    </thead>
                    <tbody>
                    <tr v-for="(gr_item, index) in gr_items.data" :key="gr_item.id">
                        <td>
                            <b class="text-purple">
                                <Link :href="`/goods-receipts/${gr_item.id}`"><span>{{ gr_item.code }}</span></Link>
                            </b>
                        </td>
                        <td>
                            <b class="text-purple">
                                <Link :href="`/orders/${gr_item.order.id}`"><span>{{ gr_item.order.code }}</span></Link>
                            </b>
                        </td>
                        <td>
                            <b class="text-purple">
                                <Link :href="`/vendors/${gr_item.order.vendor.id}`"><span>{{ gr_item.order.vendor.name }}</span></Link>
                            </b>
                        </td>
                        <td>{{ gr_item.order.vendor.code }}</td>
                        <td></td>
                        <td>{{ gr_item.do_number }}</td>
                        <td>{{ $filters.formatDateTime(gr_item.created_at) }}</td>
                        <td>
                            {{ gr_item.is_return ? 'Yes' : 'No' }}
                        </td>
                        <td>
                            <div class="el-tag" :class="gr_item.status_class">
                                {{ gr_item.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-goods-receipt')">
                                        <Link :href="`/goods-receipts/${gr_item.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('view-goods-receipt')">
                                        <Link :href="`/goods-receipts/${gr_item.id}`"><span class="icon"><img src="../../../assets/img/download.svg" alt=""></span><span>Download QR</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('view-goods-receipt')">
                                        <a target="_blank" :href="`/goods-receipts/${gr_item.id}/pdf`"><span class="icon fa-regular fa-files"></span><span>View PDF</span></a>
                                    </li>
                                    <li v-if="permissions.includes('create-purchase_goods_return') && gr_item.status == 2 || gr_item.status == 3">
                                        <a @click="convertToGoodsReturn(gr_item.id, index)" href="javascript:void(0)"><span class="icon fa-regular fa-refresh"></span><span>Convert to Goods Return</span></a>
                                    </li>
                                    <li v-if="gr_item.status == 1">
                                        <a @click="setAsVoid(gr_item.id)" href="javascript:void(0)"><span class="icon fa-regular fa-ban"></span><span>Void</span></a>
                                    </li>
                                    <!-- <li v-if="permissions.includes('view-goods-receipt') && gr_item.status == 1">
                                        <a class="text-danger" href="#" @click="setAsVoid(gr_item.id)"><span class="icon fa-regular fa-stop text-danger"></span><span>Void</span></a>
                                    </li> -->
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ gr_items.from }} to {{ gr_items.to }} of {{ gr_items.total }} entries</p>
                <Pagination :links="gr_items.links"/>
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
    gr_items: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/goods-receipts",
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

const setAsVoid = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Goods receipt will be voided.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/goods-receipts/${id}/set-void`, {
                id: id
            }, {
                preserveScroll: true,
            });
        }
    });
};

const convertToGoodsReturn = (id, index) => {
    // if (props.gr_items.data[index].is_return) {
    //     return Swal.fire({
    //         title: "Cannot convert to goods return",
    //         text: "There is already an active goods return for this transaction.",
    //         icon: "warning",
    //         showCancelButton: true,
    //         confirmButtonColor: "#ea5455",
    //         cancelButtonColor: "#009CDB",
    //         confirmButtonText: "Go to goods return!",
    //         cancelButtonText: "Cancel",
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             Inertia.get(`/goods-receipts/${id}/redirect-to-goods-return`, {
    //                 id: id
    //             }, {
    //                 preserveScroll: true,
    //             });
    //         }
    //     });
    // }
    Swal.fire({
        title: "Are you sure?",
        text: "New goods return will be generated.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/goods-receipts/${id}/convert-to-goods-return`, {
                id: id
            }, {
                preserveScroll: true,
            });
        }
    });
};
</script>
