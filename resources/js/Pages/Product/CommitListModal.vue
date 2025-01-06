<template>
    <div class="modal fade" id="CommitListModal" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Commited Transaction
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive pb-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Requisition ID</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Is Urgent</th>
                                    <th>Requested At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(requisition, index) in data">
                                    <td>
                                        <b class="text-purple">
                                            <a :href="`/requisitions/${requisition.requisition_id}`">
                                                <span>{{ requisition.code }}</span>
                                            </a>
                                        </b>
                                    </td>
                                    <td>{{requisition.qty}}</td>
                                    <td>
                                        <div class="el-tag" :class="status_class_arr[requisition.status]">
                                            {{ status_text_arr[requisition.status] }}
                                        </div>
                                    </td>
                                    <td>{{ type_arr[requisition.type] }}</td>
                                    <td>{{ requisition.is_urgent ? 'Yes' : 'No' }}</td>
                                    <td>{{$filters.formatDateTime(requisition.created_at)}}</td>
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
        type_arr: Array,
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
