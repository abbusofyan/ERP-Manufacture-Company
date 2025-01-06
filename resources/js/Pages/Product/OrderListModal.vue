<template>
    <div class="modal fade" id="OrderListModal" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Order Transaction
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive pb-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Qty</th>
                                    <th>Vendor</th>
                                    <th>Status</th>
                                    <th>Ordered At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, index) in data">
                                    <td>
                                        <b class="text-purple">
                                            <a :href="`/orders/${order.order_id}`">
                                                <span>{{ order.code }}</span>
                                            </a>
                                        </b>
                                    </td>
                                    <td>{{order.quantity}}</td>
                                    <td>{{order.vendor_name}}</td>
                                    <td>
                                        <div class="el-tag" :class="status_class_arr[order.status]">
                                            {{ status_text_arr[order.status] }}
                                        </div>
                                    </td>
                                    <td>{{$filters.formatDateTime(order.created_at)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 mb-3 text-center">
                        <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import Layout from "../../Components/Layout.vue";
    import {onMounted, ref} from "vue";
    import {useForm, usePage, Link} from "@inertiajs/inertia-vue3";
    import {Inertia} from "@inertiajs/inertia";

    const props = defineProps({
        data: Array,
        status_text_arr: Array,
        status_class_arr: Array,
    });
</script>

<style media="screen">
    .modal-dialog.modal-fullscreen {
        max-width: 80%;
        max-height: 90%;
        margin: auto;
        overflow-y: auto;
        margin-top:20px;
    }
</style>
