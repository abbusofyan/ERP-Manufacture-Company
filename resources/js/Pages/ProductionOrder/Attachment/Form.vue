<template>
    <div class="box rounded-md shadow-default mt-20 bg-white">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <div class="text-18 font-medium">
                <span>Attachment & Test Result</span>
            </div>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search">
                    </div>
                </div>
                <div class="btn-group">
                    <a class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addRequirement" ref="openModal" href="javascript:void(0)"><i class="fa fa-upload"></i> Upload File</a>
                </div>

                <Modal :order="order"></Modal>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table select-rows">
                <thead>
                <tr>
                    <th>
                        File
                    </th>
                    <th>
                        Date Test Uploaded
                    </th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(process, index) in form.attachments" :key="index">
                        <td>{{ process.name }}</td>
                        <td>{{ $filters.formatDateTime(process.created_at) }}</td>
                        <td>
                            <div class="el-actions justify-start">
                                <a class="text-red" href="javascript:void(0);" @click="destroy(index)"><em class="fa-regular fa-trash-can"></em></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">

        </div>
    </div>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {onMounted} from "vue";
import Swal from "sweetalert2";
import Modal from "./UploadFileModal.vue";
import { ref, inject } from 'vue';

const props = defineProps({
    order: Object,
});

let search = ref(null);

const form = inject('form')

const destroy = (index) => {
    Swal.fire({
        title: "Delete this attachment? ",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Delete!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/production-order-attachment/${form.attachments[index].id}`, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Attachment file has been deleted.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    form.attachments.splice(index, 1);
                },
                onError: (errors) => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an issue updating the Items used. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

const saveChanges = () => {
    Inertia.post(`/production-order/${props.order.id}/update-attachment`, {
        attachments: form.attachments
    }, {
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'Item used updated.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error!',
                text: 'There was an issue updating the Items used. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}
</script>
