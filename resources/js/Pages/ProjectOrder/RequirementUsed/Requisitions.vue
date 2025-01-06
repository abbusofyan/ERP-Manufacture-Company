<template>
    <Layout :csrf="csrf" :projectOrder="projectOrder" :stages="stages" :tab="4">
        <!--Tab-->

        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <div class="flex flex-wrap gap-16 justify-between">
                    <Link class="btn btn-purple" :href="`/project-orders/${ projectOrder.id }/requirements-used`"><i class="fa fa-angle-left"></i> Back</Link>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Requisition No.</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Item ID</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Request Qty</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Pending Issue Qty</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Issue Out Qty</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Purchase Qty</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Pending Goods Issue Note</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Purchase Order No</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>GRNNo</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>LineID</span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="item in projectOrder.used_requirements">
                        <td>
                            <span class="text-primary text-bold">{{ item?.requisition_item?.requisition?.code }}</span>
                        </td>
                        <td>
                            <span class="text-primary text-bold">{{ item?.requisition_item?.product?.sku }}</span>
                        </td>
                        <td>
                            <span>{{ item?.requisition_item?.requisition_qty }}</span>
                        </td>
                        <td>
                            <span>{{ item?.requisition_item?.pending_for_po_qty }}</span>
                        </td>
                        <td>
                            <span>{{ item?.requisition_item?.issued_qty }}</span>
                        </td>
                        <td>
                            <span>{{ item?.requisition_item?.committed_qty }}</span>
                        </td>
                        <td>
                            <span>{{ item?.requisition_item?.pending_to_goods_receipt_qty }}</span>
                        </td>
                        <td>
                            <span>--</span>
                        </td>
                        <td>
                            <span>--</span>
                        </td>
                        <td>
                            <span>{{ item.id }}</span>
                        </td>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

    </Layout>
</template>

<script setup>
import Layout from "../Layout.vue";
import Pagination from "@/Components/Pagination.vue";
import {Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    csrf: String,
    stages: Object,
    projectOrder: Object,
});
</script>
