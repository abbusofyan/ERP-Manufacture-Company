<template>
    <Modal :serviceOrder="serviceOrder" />

    <div class="box pb-[5.6rem] rounded-md shadow-default bg-white mt-[2.6rem]">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <label class="d-flex align-items-center gap-1 text-18 text-bold-500">
                Outsourced Items
            </label>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="btn-group">
                    <a v-if="![7, 8, 9].includes(serviceOrder.status)" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#uploadOutsourced" ref="openModal" href="javascript:void(0)">Add Outsourced Item <i class="fa fa-plus"></i></a>
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
                                    <span>Name</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Quantity</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Price</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>File</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Actions</span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in outsourcedItems.data" :key="item.id">
                            <td>{{ item.name }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.price }}</td>
                            <td>
                                <a v-if="item.file_url" :href="`/storage/${item.file_url}`" target="_blank">View File</a>
                            </td>
                            <td>
                                <a @click="destroy(item.id)" href="javascript:void(0)"><span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span></a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                    <p>Showing {{ outsourcedItems.from }} to {{ outsourcedItems.to }} of {{ outsourcedItems.total }} entries</p>
                    <Pagination :links="outsourcedItems.links" />
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
    serviceOrder: Object,
    outsourcedItems: Object,
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
            Inertia.delete(`/service-orders/${props.serviceOrder.id}/outsourced/${id}`, {
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
