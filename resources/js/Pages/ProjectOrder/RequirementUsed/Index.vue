<template>
    <Modal :csrf="csrf" :projectOrder="projectOrder"></Modal>

    <div class="box pb-[5.6rem] rounded-md shadow-default bg-white mt-[2.6rem]">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <label class="d-flex align-items-center gap-1 text-18 text-bold-500">
                Item Used
            </label>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search" v-model="search">
                    </div>
                </div>
                <div class="btn-group">
                    <Link class="btn btn-purple" :href="`/project-orders/${projectOrder.id}/requirements-used/live-stock-status`">
                        Live Stock Status <i class="fa fa-list"></i>
                    </Link>
                    <Link class="btn btn-purple" :href="`/project-orders/${projectOrder.id}/requirements-used/requisitions`">
                        Item Status Detail <i class="fa fa-list"></i>
                    </Link>
                    <a v-if="projectOrder.status != 7" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#uploadRequirementUsed" ref="openModal" href="javascript:void(0)">Add Item <i class="fa fa-plus"></i></a>
                    <a v-if="([7, 8, 9].includes(this.projectOrder.status) || this.projectOrder.quotation_generated_after_update)" style="opacity: 0.5" class="btn btn-purple disabled" href="javascript:void(0)">
                        Generate Quotation <i class="fa fa-file"></i>
                    </a>
                    <Link v-else class="btn btn-purple" :href="`/project-orders/${projectOrder.id}/requirements-used/generate-quotation`" method="post">
                        Generate Quotation <i class="fa fa-file"></i>
                    </Link>
                </div>
            </div>
        </div>
        <div class="form-wrap">
            <div class="boxes">
                <div class="table-responsive pb-0" style="min-height: unset">
                    <table class="table select-rows">
                        <thead>
                        <tr>
                            <th :class="{ sorting_asc: order == 'requirement_id' && by == 'asc', sorting_desc: order == 'requirement_id' && by == 'desc' }" @click="sort('requirement_id')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Req. No.</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Item No.</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Item Name</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Category</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>UOM</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Date Required</span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'planned_qty' && by == 'asc', sorting_desc: order == 'planned_qty' && by == 'desc' }" @click="sort('planned_qty')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Planned QTY</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'issued_qty' && by == 'asc', sorting_desc: order == 'issued_qty' && by == 'desc' }" @click="sort('issued_qty')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Issued QTY</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'issued_qty' && by == 'asc', sorting_desc: order == 'issued_qty' && by == 'desc' }" @click="sort('issued_qty')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Commited QTY</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'returned_qty' && by == 'asc', sorting_desc: order == 'returned_qty' && by == 'desc' }" @click="sort('returned_qty')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Returned QTY</span>
                                    <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in requirementsUsed.data" :key="item.id">
                            <td>
                                <a class="text-primary text-bold" target="_blank" :href="`/requisitions/${ item.requisition.id }`">
                                    {{ item.requisition.code }}
                                </a>
                            </td>
                            <td>
                                <a class="text-primary text-bold" target="_blank" :href="`/storages/${ item.requirement.storage_item.id }`">
                                    {{ item.requirement.storage_item.product.sku }}
                                </a>
                            </td>
                            <td>{{ item.requirement.storage_item.product.name }}</td>
                            <td>{{ item.requirement.storage_item.product.category.name }}</td>
                            <td>{{ item.requirement.storage_item.product.uom.code }}</td>
                            <td>{{ $filters.formatDate(item.requisition_item.date) }}</td>
                            <td>{{ item.requisition_item.requisition_qty }}</td>
                            <td>{{ item.requisition_item.issued_qty }}</td>
                            <td>{{ item.requisition_item.committed_qty }}</td>
                            <td>{{ item.requisition_item.returned_qty }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                    <p>Showing {{ requirementsUsed.from }} to {{ requirementsUsed.to }} of {{ requirementsUsed.total }} entries</p>
                    <Pagination :links="requirementsUsed.links"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Pagination from "../../../Components/Pagination.vue";
import Modal from "./UploadForm.vue";
import {computed, onMounted, onUpdated, ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {Link, usePage} from "@inertiajs/inertia-vue3";
import {useToast} from "vue-toastification";


const toast = useToast();
const updated = computed(() => usePage().props.value.flash.updated);

const props = defineProps({
    csrf: String,
    projectOrder: Object,
    requirementsUsed: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        `/project-orders/${props.projectOrder.id}/requirements-used`,
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
            Inertia.delete(`/project-orders/${props.projectOrder.id}/requirements-used/${id}`, {
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

onUpdated(() => {
    if (updated.value) {
        toast.info(updated.value, {
            icon: "fa-solid fa-pen-to-square",
            timeout: 3000,
        });
    }
});
</script>
