<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Service Contracts</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/service-contracts">Service Contracts</Link>
                    </li>
                    <li>
                        <span>Service Contract</span>
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
                    <div class="btn-group" v-if="permissions.includes('create-service_appointment')">
                        <Link class="btn btn-purple" href="/service-contracts/create">Add New Contract</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>No.</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Customer Name</span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'pic_phone_number' && by == 'asc', sorting_desc: order == 'pic_phone_number' && by == 'desc' }" @click="sort('pic_phone_number')">
                            <div class="flex items-center justify-between gap-1">
                                <span>PIC Number</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'vehicle_id' && by == 'asc', sorting_desc: order == 'vehicle_id' && by == 'desc' }" @click="sort('vehicle_id')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Vehicle No.</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'end_duration_date' && by == 'asc', sorting_desc: order == 'end_duration_date' && by == 'desc' }" @click="sort('end_duration_date')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Duration</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'status' && by == 'asc', sorting_desc: order == 'status' && by == 'desc' }" @click="sort('status')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Value</span>
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
                    <tr v-for="contract in contracts.data" :key="contract.id">
                        <td>{{ contract.service_contract_number }}</td>
                        <td>{{ contract.customer.name }}</td>
                        <td>{{ contract.pic_phone_number }}</td>
                        <td>{{ contract.vehicle_license_plates }}</td>
                        <td>{{ $filters.formatDate(contract.start_duration_date) }} - {{ $filters.formatDate(contract.end_duration_date) }}</td>
                        <td>
                            <div class="el-tag" :class="contract.status_class">
                                {{ contract.status_text }}
                            </div>
                        </td>
                        <td>{{ contract.value }}</td>
                        <td>
                            <div class="el-actions justify-end">
                            </div>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('update-service_appointment')">
                                        <Link class="" :href="`/service-contracts/${contract.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-service_appointment') && Number(contract.status) === 1">
                                        <Link :href="`/service-contracts/${contract.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('view-service_appointment')">
                                        <a :href="`/service-contracts/${contract.id}/download`"><span class="icon"><img src="../../../assets/img/download.svg" alt=""></span><span>Download</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-requisition')">
                                        <a class="text-success" v-if="contract.status == 1" @click="uploadImage(contract.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/upload-green.svg" alt=""></span><span>Upload Signed</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-requisition')">
                                        <a class="text-danger" v-if="contract.status != 3 && contract.status != 4 && contract.can_early_terminate" @click="earlyTermination(contract.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/file-x.svg" alt=""></span><span>Early Termination</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-requisition')">
                                        <a class="text-danger" v-if="contract.status == 1" @click="voidChange(contract.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/file-x.svg" alt=""></span><span>Void</span></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <input type="file"
                   ref="uploadBtn"
                   class="form-control-file mb-10 d-none"
                   id="upload-photo"
                   :class="{ 'is-invalid': form.errors.signed_image_url }"
                   @input="event => { form.signed_image_url = event.target.files[0]; submitImage(form.contract_id) }"
                   accept="image/png, image/jpg, image/jpeg">

            <input type="file"
                   ref="uploadBtn2"
                   class="form-control-file mb-10 d-none"
                   id="upload-file"
                   :class="{ 'is-invalid': form.errors.file_url }"
                   @input="event => { form.file_url = event.target.files[0]; submitEarlyTermination(form.contract_id) }"
                   accept="image/png, image/jpg, image/jpeg">

            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ contracts.from }} to {{ contracts.to }} of {{ contracts.total }} entries</p>
                <Pagination :links="contracts.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, computed, watch} from 'vue';
import {usePage, Link, useForm} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Pagination from '../../Components/Pagination.vue';
import Swal from 'sweetalert2';
import debounce from 'lodash.debounce';
import {Inertia} from "@inertiajs/inertia";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    contracts: Object,
    filters: Object,
});

const form = useForm({
    contract_id: null,
    signed_image_url: null,
    file_url: null,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        '/service-contracts',
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



let uploadBtn = ref(null)
let uploadBtn2 = ref(null)

const uploadImage = (id) => {
    form.reset();
    form.contract_id = id;

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, do it!'
    }).then((result) => {
        if (result.isConfirmed) {
            uploadBtn.value.click()
        }
    });
};

const earlyTermination = (id) => {
    form.reset();
    form.contract_id = id;

    Swal.fire({
        title: 'Need to upload a supporting document, before process can be completed',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, do it!'
    }).then((result) => {
        if (result.isConfirmed) {
            uploadBtn2.value.click()
        }
    });
};

const submitImage = (id) => {
    form.post(`/service-contracts/${id}/upload-photo`, {
        onSuccess: () => {
            form.reset();
            Swal.fire({
                title: 'Success !',
                text: "Your changes have been saved!",
                icon: 'success',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                cancelButtonColor: '#626CC6',
            });
        }
    });
};

const submitEarlyTermination = (id) => {
    form.post(`/service-contracts/${id}/early-termination`, {
        onSuccess: () => {
            form.reset();
            Swal.fire({
                title: 'Success !',
                text: "Your changes have been saved!",
                icon: 'success',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                cancelButtonColor: '#626CC6',
            });
        }
    });
};

const voidChange = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, do it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/service-contracts/${id}/void`, {}, {
                onSuccess: () => {
                    form.reset();
                    Swal.fire({
                        title: 'Success !',
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
            Inertia.delete(`/service-contracts/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                            title: 'Success!',
                            text: "Service contract deleted successfully.",
                            icon: 'success',
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: 'OK',
                            cancelButtonColor: '#626CC6',
                        }
                    )
                }
            });
        }
    });
};
</script>
