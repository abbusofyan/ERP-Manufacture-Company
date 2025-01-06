<template>
    <Layout>

        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                {{ requisition.code }}
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/requisitions">Requisition List</Link>
                    </li>
                    <li class="active"><span>{{ requisition.code }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form action="">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Purchase Requisition Information</div>
                            <div class="flex justify-between gap-20">
                                <div class="img w-full max-w-[24.5rem] children:w-full"><img src="../../../assets/img/logo-3.png" alt=""></div>
                                <div class="btn-group"><a class="btn btn-purple" href="#"><span class="icon"><img src="../../../assets/img/documents.svg" alt="icon"></span><span>Create Purchase Order</span></a></div>
                            </div>
                            <div class="flex mt-36 max-md:flex-col gap-[8rem]">
                                <table class="table-1-el w-full">
                                    <tr>
                                        <th>PR Number</th>
                                        <td>{{ requisition.code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created date</th>
                                        <td>{{ $filters.formatDateTime(requisition.created_at) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ requisition.status_text }}</td>
                                    </tr>
                                    <tr>
                                        <th>Requested by</th>
                                        <td>{{ requisition.requested_by.name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved by</th>
                                        <td>{{ requisition.approved_by }}</td>
                                    </tr>
                                </table>
                            </div>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">PO List</div>
                            <div class="table-responsive">
                                <table class="table no-borders">
                                    <thead>
                                    <th>
                                        <div class="flex items-center justify-between"><span>PO NO</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Supplier</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Created Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Delivery Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="text-purple font-semibold">PO9927</div>
                                        </td>
                                        <td>Heminox tech</td>
                                        <td>12 Sep 2023, 8:43 PM</td>
                                        <td>17 Sep 2023, 8:43 PM</td>
                                        <td>Pending</td>
                                    </tr>
                                    </tbody>
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
                                        <div class="flex items-center justify-between"><span>requisition qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>pending for PO</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>pending to goods receipt</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between"><span>committed qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in requisition.requisition_items" :key="index">
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.product_name }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td>3</td>
                                        <td>0</td>
                                        <td>7</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group"><a class="btn btn-purple" href=""><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></a><a class="btn btn-light btn-light--brounded" href="#">Edit</a></div>
                        <div class="btn-group"><a class="btn btn-red btn-red--brounded" href="#"><em class="fa-regular fa-xmark"></em><span>Remove</span></a></div>
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
    requisition: Object,
});
</script>
