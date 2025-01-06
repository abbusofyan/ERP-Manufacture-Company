<template>

    <Modal :projectOrder="projectOrder"></Modal>

    <div v-if="projectOrder.stage !== 1" class="box pb-[5.6rem] rounded-md shadow-default bg-white mt-[2.6rem]">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <label class="d-flex align-items-center gap-1 text-18 text-bold-500">
                Attachments
            </label>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="btn-group">
                    <a v-if="projectOrder.stage && ![7, 8, 9].includes(projectOrder.status)" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#uploadAttachment" ref="openModal" href="javascript:void(0)">Upload file <i class="fa fa-upload"></i></a>
                </div>
            </div>
        </div>
        <div class="form-wrap">
            <div class="boxes">
                <div class="table-responsive pb-0" style="min-height: unset">
                    <table class="table select-rows">
                        <thead>
                        <tr>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>File</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span></span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="attachment in attachments.data" :key="attachment.id">
                            <td>{{ attachment.name }}</td>
                            <td>
                                <div class="el-actions justify-end">
                                    <a @click="destroy(attachment.id)" href="javascript:void(0)"><span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span></a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                    <p>Showing {{ attachments.from }} to {{ attachments.to }} of {{ attachments.total }} entries</p>
                    <Pagination :links="attachments.links"/>
                </div>
            </div>
        </div>
    </div>

    <div class="box pb-[5.6rem] rounded-md shadow-default bg-white mt-[2.6rem]">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <label class="d-flex align-items-center gap-1 text-18 text-bold-500">
                First Check Attachments
            </label>
            <div v-if="projectOrder.stage && projectOrder.stage === 1" class="flex flex-wrap gap-16 justify-between">
                <div class="btn-group">
                    <a class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#uploadAttachment" ref="openModal" href="javascript:void(0)">Upload file <i class="fa fa-upload"></i></a>
                </div>
            </div>
        </div>
        <div class="form-wrap">
            <div class="boxes">
                <div class="table-responsive pb-0" style="min-height: unset">
                    <table class="table select-rows">
                        <thead>
                        <tr>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>File</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Remarks</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span></span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="attachment in firstAttachments.data" :key="attachment.id">
                            <td>{{ attachment.name }}</td>
                            <td>{{ attachment.remarks }}</td>
                            <td>
                                <div v-if="projectOrder.stage === 1" class="el-actions justify-end">
                                    <a @click="destroy(attachment.id)" href="javascript:void(0)"><span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span></a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                    <p>Showing {{ firstAttachments.from }} to {{ firstAttachments.to }} of {{ firstAttachments.total }} entries</p>
                    <Pagination :links="firstAttachments.links"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Pagination from "../../../Components/Pagination.vue";
import Modal from "./UploadForm.vue";
import {ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Swal from "sweetalert2";

const props = defineProps({
    projectOrder: Object,
    firstAttachments: Object,
    attachments: Object,
});

const destroy = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea5455',
        cancelButtonColor: '#009CDB',
        confirmButtonText: 'Yes, Proceed!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/project-orders/${props.projectOrder.id}/attachments/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: "Your changes have been saved!",
                        icon: 'success',
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'OK',
                        cancelButtonColor: '#626CC6',
                    });
                }
            });
        }
    });
};
</script>
