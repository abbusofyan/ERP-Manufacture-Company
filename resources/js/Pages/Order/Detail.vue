<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ order.code }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/orders">Purchase Order List</Link>
                    </li>
                    <li class="active"><span>{{ order.code }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form action="">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Purchase Order Information</div>
                            <div class="flex justify-between gap-20">
                                <div class="img w-full max-w-[24.5rem] children:w-full">
                                    <!--<img src="../../../assets/img/logo-3.png" alt="">-->
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-purple" :href="`/orders/${order.id}/pdf`" target="_blank">
                                        <span class="icon"><img src="../../../assets/img/documents.svg" alt="icon"></span><span>View PDF</span>
                                    </a>
                                </div>
                            </div>
                            <div class="flex mt-36 max-md:flex-col gap-[8rem]">
                                <table class="table-1-el w-full">
                                    <tr>
                                        <th>PO Number</th>
                                        <td>{{ order.code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created date</th>
                                        <td>{{ $filters.formatDateTime(order.created_at) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vendor</th>
                                        <td>
                                            <div class="text-purple font-semibold">
                                                <Link :href="`/vendors/${order.vendor.id}`"><span>{{ order.vendor.code + ' | ' + order.vendor.name }}</span></Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Vendor address</th>
                                        <td>{{ order.vendor_address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vendor Phone</th>
                                        <td>{{ order.vendor_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Credit Terms</th>
                                        <td>{{ order.vendor.credit_term }}</td>
                                    </tr>
                                    <tr>
                                        <th>Reference No.</th>
                                        <td>{{ order.code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Delivery address</th>
                                        <td>{{ order.delivery_address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <div class="el-tag" :class="order.status_class">
                                                {{ order.status_text }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Delivery Date</th>
                                        <td>{{ $filters.formatDateTime(order.delivery_date) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Remarks</th>
                                        <td>{{ order.remark }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sub Total</th>
                                        <td>${{ order.sub_total }}</td>
                                    </tr>
                                    <tr>
                                        <th>Discount</th>
                                        <td>${{ order.discount }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <td>${{ order.total }}</td>
                                    </tr>
                                </table>
                            </div>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">Item List</div>
                            <div class="table-responsive">
                                <table class="table no-borders">
                                    <thead>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Description</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Location</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Price</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>GST</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Total Price</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in order.items" :key="index">
                                        <td>
                                            <div class="text-purple font-semibold">
                                                <Link :href="`/products/${item.product_id}`"><span>{{ item.product ? item.product.sku : 'OTHER' }}</span></Link>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-purple font-semibold">
                                                <Link :href="`/products/${item.product_id}`"><span>{{ item.product_name }}</span></Link>
                                            </div>
                                        </td>
                                        <td>{{ item.description }}</td>
                                        <td></td>
                                        <td>${{ item.unit_price }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td>{{ parseInt(item.gst.value) }}%</td>
                                        <td>${{ item.total }}</td>
                                        <td>
                                            <div v-if="![1,6].includes(order.status)" class="el-tag" :class="item.status_class">
                                                {{ item.status_text }}
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">Goods Receipt History</div>
                            <div class="table-responsive">
                                <table class="table no-borders">
                                    <thead>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>GRN Number</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Remark</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Received At</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                    </thead>
                                    <tbody>
                                        <tr v-if="props.order.history" v-for="(history, historyIndex) in props.order.history">
                                            <td>{{ history.sku }}</td>
                                            <td>{{ history.product_name }}</td>
                                            <td>
                                                <div class="text-purple font-semibold">
                                                    <Link :href="`/goods-receipts/${history.goods_receipt_id}`"><span>{{ history.code }}</span></Link>
                                                </div>
                                            </td>
                                            <td>{{ history.quantity }}</td>
                                            <td>{{ history.notes }}</td>
                                            <td>{{ $filters.formatDateTime(history.created_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group">
                            <Link class="btn btn-purple" href="/orders"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                            <!-- <Link v-if="permissions.includes('update-order')" class="btn btn-light btn-light--brounded" :href="`/orders/${order.id}/edit`">Edit</Link> -->
                        </div>
                        <div class="btn-group">
                            <a v-if="permissions.includes('update-order') && [1].includes(order.status)" class="btn btn-green" @click="updateStatus(3)" href="javascript:void(0)"><em class="icon fa-sharp fa-regular fa-box-check"></em><span>Confirm Purchase</span></a>
                            <a v-if="permissions.includes('update-order') && [3].includes(order.status)" class="btn btn-orange" @click="cancel(order.id)" href="javascript:void(0)"><em class="icon fa-regular fa-rectangle-xmark"></em><span>Cancel Purchase</span></a>
                            <a v-if="permissions.includes('update-order') && [1].includes(order.status)" class="btn btn-danger" @click="updateStatus(6)" href="javascript:void(0)"><em class="icon fa-regular fa-rectangle-xmark"></em><span>Reject Purchase</span></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {computed, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    order: Object,
});

const updateStatus = (value) => {
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
            Inertia.post(`/orders/update-status/${props.order.id}`, {
                status: value,
            }, {
                preserveScroll: true,
            });
        }
    });
};

const destroy = () => {
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
            Inertia.delete(`/orders/${props.order.id}`, {
                preserveScroll: true,
            });
        }
    });
};

const cancel = (id) => {
    if (props.order.goods_receipt.length > 0) {
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
</script>
