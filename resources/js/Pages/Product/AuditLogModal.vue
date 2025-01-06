<template>
    <div class="modal fade" id="AuditLogModal" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Audit Log
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive pb-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date Recorded</th>
                                    <th>Action</th>
                                    <th>Data</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(history, index) in data">
                                    <td class="text-nowrap">{{$filters.formatDateTime(history.created_at)}}</td>
                                    <td>{{ history.action_text }}</td>
                                    <td>
                                        {{
                                            typeof history.data == 'object'
                                            ? Object.entries(history.data).map(([key, value]) => `${key.replace('_', ' ')} = ${value}`).join('; ')
                                            : history.data
                                        }}
                                    </td>
                                    <td>{{ history.created_by.name }}</td>
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
    });
    console.log(props.data);
</script>
