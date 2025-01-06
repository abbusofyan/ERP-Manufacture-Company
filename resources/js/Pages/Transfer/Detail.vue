<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Item Transfer</div>
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
                        <Link href="/transfers">Item Transfer</Link>
                    </li>
                    <li>
                        <span>{{ transfer.code }}</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">
                    <div class="box d-flex justify-content-between align-items-center">
                        <div class="text-18 font-medium pb-17 mb-17">
                            <span>Item Transfer {{ transfer.code }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex max-md:flex-col gap-[8rem] pb-25 mb-[2.6rem] border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                <table class="table-1-el w-full">
                    <tbody>
                        <tr>
                            <th>Date Issued</th>
                            <td>{{ $filters.formatDateTime(transfer.created_at) }}</td>
                        </tr>
                        <tr>
                            <th>Staff</th>
                            <td>{{ transfer.author.name }}</td>
                        </tr>
                        <tr>
                            <th>Store</th>
                            <td>{{ transfer.origin_store.name }} <span class="icon fa-regular fa-right ml-10 mr-10"></span> {{transfer.destination_store.name}}</td>
                        </tr>
                        <tr>
                            <th>Remarks</th>
                            <td>{{ transfer.remarks }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <div class="el-tag" :class="transfer.status_class">
                                    {{ transfer.status_text }}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive pb-0">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Product</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in transferItems.data" :key="item.id">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="img-30">
                                        <img :src="`/products/${item.product.file_url}`" alt="">
                                    </div>
                                    <div>
                                        <div class="text-bold">
                                            {{ item.product.name }}
                                        </div>
                                        <small class="d-block">
                                            Price $ {{ item.cost_price }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ item.product.sku }}
                            </td>
                            <td>
                                {{ item.quantity }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ transferItems.from }} to {{ transferItems.to }} of {{ transferItems.total }} entries</p>
                <Pagination :links="transferItems.links"/>
            </div>
            <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                <div class="btn-group">
                    <Link class="btn btn-purple" href="/transfers"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                </div>
                <div class="btn-group">
                    <a v-if="transfer.status == 1" class="btn btn-green" @click="approve(transfer.id)" href="javascript:void(0)">
                        <em class="icon fa-sharp fa-regular fa-box-check"></em><span>Confirm Transfer</span>
                    </a>
                    <a v-if="transfer.status == 1" class="btn btn-danger" @click="cancel(transfer.id)" href="javascript:void(0)">
                        <em class="icon fa-regular fa-rectangle-xmark"></em><span>Cancel Transfer</span>
                    </a>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed, onMounted} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    transfer: Object,
    transferItems: Object,
});

const approve = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "After approve this transfer, You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/transfer/approve/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

const cancel = (transfer_id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Item transfer will be cancelled. You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/transfers/cancel/${transfer_id}`, {
                preserveScroll: true,
            });
        }
    });
};

</script>
