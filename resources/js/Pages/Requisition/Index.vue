<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Requisition</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/requisitions">Requisition</Link>
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
                    <div class="btn-group" v-if="permissions.includes('create-requisition')">
                        <Link class="btn btn-purple" href="/requisitions/create">Add New Requisition</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1"><span>PR NO</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1"><span>Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1"><span>Created Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1"><span>Requested by</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(requisition, index)  in requisitions.data" :key="index">
                        <td>
                            <div class="text-purple font-semibold">
                                <Link :href="`/requisitions/${requisition.id}`"><span>{{ requisition.code }}</span></Link>
                            </div>
                        </td>
                        <td>{{ requisition.type_text }}</td>
                        <td>{{ $filters.formatDateTime(requisition.created_at) }}</td>
                        <td>{{ requisition.created_by?.name }}</td>
                        <td>
                            <div class="el-tag" :class="requisition.status_class">
                                {{ requisition.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-requisition')">
                                        <Link :href="`/requisitions/${requisition.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="['draft', 'rejected'].includes(requisition.statuses[requisition.status].toLowerCase()) && permissions.includes('update-requisition')">
                                        <Link :href="`/requisitions/${requisition.id}/edit`">
                                            <span class="icon fa fa-pencil"></span><span>Edit</span>
                                        </Link>
                                    </li>
                                    <li v-if="['rejected'].includes(requisition.statuses[requisition.status].toLowerCase()) && permissions.includes('update-requisition')">
                                        <a href="javascript:void(0)" @click="openRejectionNotesModal(index)">
                                            <span class="icon fa fa-files"></span><span>Reject Notes</span>
                                        </a>
                                    </li>
                                    <li v-if="['draft', 'rejected', 'pending for approval'].includes(requisition.statuses[requisition.status].toLowerCase()) && !permissions.includes('approve-requisition')">
                                        <a href="javascript:void(0)" @click="openModal(requisition.id)">
                                            <span class="icon fa fa-clipboard text-orange"></span><span class="text-orange">Send for Approval</span>
                                        </a>
                                    </li>
                                    <li v-if=" ['pending for approval', 'draft', 'rejected'].includes(requisition.statuses[requisition.status].toLowerCase()) && permissions.includes('approve-requisition')">
                                        <a href="javascript:void(0)" @click="approve(requisition.id)">
                                            <span class="icon fa fa-check text-success"></span><span class="text-success">Approve</span>
                                        </a>
                                    </li>
                                    <li v-if="['pending for approval'].includes(requisition.statuses[requisition.status].toLowerCase()) && permissions.includes('approve-requisition')">
                                        <a href="javascript:void(0)" @click="openRejectModal(requisition.id)">
                                            <span class="icon fa fa-x text-danger"></span><span class="text-danger">Reject</span>
                                        </a>
                                    </li>
                                    <li v-if="['draft', 'rejected'].includes(requisition.statuses[requisition.status].toLowerCase())">
                                        <a href="javascript:void(0)" @click="setVoid(requisition.id)">
                                            <span class="icon fa fa-x text-danger"></span><span class="text-danger">Void</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a :href="`requisitions/${requisition.id}/duplicate`">
                                            <span class="icon fa fa-files"></span><span>Duplicate</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ requisitions.from }} to {{ requisitions.to }} of {{ requisitions.total }} entries</p>
                <Pagination :links="requisitions.links"/>
            </div>
        </div>

        <form @submit.prevent="sendForApproval">
            <div class="modal fade" id="sendForApprovalModal" role="dialog" style="overflow:hidden;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-24 pt-36 pb-30">
                        <div class="modal-header">
                            <div class="col-md-12 mt-3 text-center">
                                <div class="text-24 text-bold-500">
                                    Send for Approval
                                </div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Manager</label>
                                <Select2 class="select2-w-100"
                                    :settings="{
                                        multiple: true,
                                        ajax: {
                                            url: '/data/managers',
                                            dataType: 'json',
                                            method: 'POST',
                                            data: function (params) {
                                              return {
                                                search: params.term,
                                                _token: csrf,
                                              };
                                            },
                                            processResults: function (data, params) {
                                                return {
                                                    results: data.map(function (user) {
                                                        return {
                                                            text: user.name,
                                                            id: user.id,
                                                            data: user,
                                                        };
                                                    })
                                                };
                                            }
                                        }
                                    }"
                                    v-model="sendForApprovalForm.user_id"
                                    placeholder="Select Manager"
                                />
                                <div v-if="sendForApprovalForm.errors.user_id" class="invalid-feedback d-block">{{ sendForApprovalForm.errors.user_id }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 mb-3 text-center">
                                <button class="btn btn-purple mr-10 disabled" type="submit" href="javascript:void(0)">Submit</button>
                                <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form @submit.prevent="reject">
            <div class="modal fade" id="rejectModal" role="dialog" style="overflow:hidden;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-24 pt-36 pb-30">
                        <div class="modal-header">
                            <div class="col-md-12 mt-3 text-center">
                                <div class="text-24 text-bold-500">
                                    Reject Requisition
                                </div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Reason :</label>
                                <textarea v-model="rejectForm.reason" :class="{ 'is-invalid': rejectForm.errors.reason }" cols="30" rows="10" placeholder="Type Here"></textarea>
                                <div v-if="rejectForm.errors.reason" class="invalid-feedback d-block">{{ rejectForm.errors.reason }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 mb-3 text-center">
                                <button class="btn btn-purple mr-10 disabled" type="submit" href="javascript:void(0)">Submit</button>
                                <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="modal fade" id="rejectionNotesModal" role="dialog" style="overflow:hidden;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-24 pt-36 pb-30">
                    <div class="modal-header">
                        <div class="col-md-12 mt-3 text-center">
                            <div class="text-24 text-bold-500">
                                Rejection Notes
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Reason :</label>
                            <textarea rows="8" cols="80">{{ rejectionNotes }}</textarea>
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
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {useToast} from "vue-toastification";
import axios from 'axios';

const toast = useToast();
const permissions = computed(() => usePage().props.value.auth.user.permissions);
const updated = computed(() => usePage().props.value.flash.updated);


const props = defineProps({
    requisitions: Object,
    filters: Object,
    managers: Array,
    csrf: String
});

const sendForApprovalForm = useForm({
    user_id: null,
});

const rejectForm = useForm({
    reason: null,
});

const requisition_id = ref(null);

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/requisitions",
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
            Inertia.delete(`/requisitions/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

const approve = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Requisition will be approved.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/requisitions/${id}/approve`, {
                preserveScroll: true,
            });
        }
    });
};

function openModal(id) {
    requisition_id.value = id;
    const modal = new bootstrap.Modal(document.getElementById('sendForApprovalModal'));
    modal.show();
}

function openRejectModal(id) {
    requisition_id.value = id;
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}
const rejectionNotes = ref({})

function openRejectionNotesModal(index) {
    let notes = props.requisitions.data[index].rejection_notes;
    if (notes.length > 0) {
        rejectionNotes.value = notes[notes.length-1].note
    } else {
        rejectionNotes.value = '';
    }
    const modal = new bootstrap.Modal(document.getElementById('rejectionNotesModal'));
    modal.show();
}

function sendForApproval() {
    sendForApprovalForm.post(`/requisitions/${requisition_id.value}/send-for-approval`, {
        onSuccess: () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('sendForApprovalModal'));
            modal.hide();
            sendForApprovalForm.reset();
            toast.success(updated.value, {
                icon: "fa-solid fa-check",
                timeout: 3000,
            });
        }
    });
}

function reject() {
    rejectForm.post(`/requisitions/${requisition_id.value}/reject`, {
        onSuccess: () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
            modal.hide();
            rejectForm.reset();
        }
    });
}

const setVoid = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "The requisition will be voided.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/requisitions/${id}/void`, {
                preserveScroll: true,
            });
        }
    });
};

</script>
