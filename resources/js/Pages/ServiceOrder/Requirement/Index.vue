<template>

    <Modal :csrf="csrf" :serviceOrder="serviceOrder"></Modal>

    <div class="box pb-[5.6rem] rounded-md shadow-default bg-white mt-[2.6rem]">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <label class="d-flex align-items-center gap-1 text-18 text-bold-500">
                Requirements
            </label>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search" v-model="search">
                    </div>
                </div>
                <div class="btn-group">
                    <a v-if="![7, 8, 9].includes(serviceOrder.status)" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#uploadRequirement" ref="openModal" href="javascript:void(0)">Add Requirement <i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
        <div class="form-wrap">
            <div class="boxes">
                <div class="table-responsive pb-0" style="min-height: unset">
                    <table class="table select-rows">
                        <thead>
                        <tr>
                            <th :class="{ sorting_asc: order == 'storage_item_id' && by == 'asc', sorting_desc: order == 'storage_item_id' && by == 'desc' }" @click="sort('storage_item_id')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Item No.</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'storage_item_id' && by == 'asc', sorting_desc: order == 'storage_item_id' && by == 'desc' }" @click="sort('storage_item_id')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Item Name</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'storage_item_id' && by == 'asc', sorting_desc: order == 'storage_item_id' && by == 'desc' }" @click="sort('storage_item_id')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>UOM</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'quantity' && by == 'asc', sorting_desc: order == 'quantity' && by == 'desc' }" @click="sort('quantity')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>QTY</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="requirement in requirements.data" :key="requirement.id">
                            <td>{{ requirement.storage_item.storage.code }}-{{ requirement.storage_item.product.sku }}</td>
                            <td>{{ requirement.storage_item.product.name }}</td>
                            <td>{{ requirement.storage_item.product.uom.code }}</td>
                            <td>{{ requirement.quantity }}</td>
                            <td>
                                <div v-if="![7, 8].includes(serviceOrder.status)" class="el-actions justify-end">
                                    <a @click="destroy(requirement.id)" href="javascript:void(0)"><span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span></a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                    <p>Showing {{ requirements.from }} to {{ requirements.to }} of {{ requirements.total }} entries</p>
                    <Pagination :links="requirements.links"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/inertia-vue3';
import Pagination from "../../../Components/Pagination.vue";
import Modal from "./UploadForm.vue";
import {ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const props = defineProps({
    csrf: String,
    serviceOrder: Object,
    requirements: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        `/service-orders/${props.serviceOrder.id}/requirements`,
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

const sort = (field) => {
    order.value = field;

    if (by.value === 'asc') {
        by.value = 'desc';
    } else {
        by.value = 'asc';
    }
};

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
            Inertia.delete(`/service-orders/${props.serviceOrder.id}/requirements/${id}`, {
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
