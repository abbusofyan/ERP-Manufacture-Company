<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Quotation</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/service-orders">Service Order</Link>
                    </li>
                    <li>
                        <span>Quotation</span>
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
                    <div class="btn-group">
                        <Link class="btn btn-purple" href="/service-quotations/create">+ Create New</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th :class="{ sorting_asc: order == 'id' && by == 'asc', sorting_desc: order == 'id' && by == 'desc' }" @click="sort('id')">
                            <div class="flex items-center justify-between gap-1">
                                <span>No.</span>
                                <span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'customer_id' && by == 'asc', sorting_desc: order == 'customer_id' && by == 'desc' }" @click="sort('customer_id')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Customer Name</span>
                                <span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'pic_phone_number' && by == 'asc', sorting_desc: order == 'pic_phone_number' && by == 'desc' }" @click="sort('pic_phone_number')">
                            <div class="flex items-center justify-between gap-1">
                                <span>PIC Number</span>
                                <span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'vehicle_id' && by == 'asc', sorting_desc: order == 'vehicle_id' && by == 'desc' }" @click="sort('vehicle_id')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Vehicle No.</span>
                                <span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'status' && by == 'asc', sorting_desc: order == 'status' && by == 'desc' }" @click="sort('status')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span>
                                <span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
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
                    <tr v-for="quotation in serviceQuotations.data" :key="quotation.id">
                        <td>{{ quotation.code }}</td>
                        <td>{{ quotation.customer.name }}</td>
                        <td>{{ quotation.pic_phone_number }}</td>
                        <td>{{ quotation.vehicle.license_plate }}</td>
                        <td>
                            <div class="el-tag" :class="quotation.status_class">
                                {{ quotation.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-requisition')">
                                        <Link :href="`/service-quotations/${quotation.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-requisition') && quotation.invoices && quotation.invoices.length == 0">
                                        <Link :href="`/service-quotations/${quotation.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('view-requisition') && quotation.status == 2">
                                        <Link :href="`/service-quotations/${quotation.id}/generate-proforma-invoice`"><span class="icon"><img src="../../../assets/img/download.svg" alt=""></span><span class="text-nowrap">Generate Proforma Invoice</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('view-requisition') && quotation.status == 2">
                                        <Link :href="`/service-quotations/${quotation.id}/generate-invoice`" :disabled="!quotation.vehicle_parts.some(part => part.service_invoice_id === null)"><span class="icon"><img src="../../../assets/img/download.svg" alt=""></span><span>Generate Invoice</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-requisition')">
                                        <a class="text-success" v-if="Number(quotation.status) === 1" @click="uploadImage(quotation.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/upload-green.svg" alt=""></span><span>Upload Signed</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-requisition') && quotation.status != 3 && (quotation.service_order == null || quotation.service_order.status == 9)">
                                        <a class="text-danger" @click="voidChange(quotation.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/file-x.svg" alt=""></span><span>Void</span></a>
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
                   @input="event => { form.signed_image_url = event.target.files[0]; submitImage(form.quotation_id) }"
                   accept="image/png, image/jpg, image/jpeg">

            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ serviceQuotations.from }} to {{ serviceQuotations.to }} of {{ serviceQuotations.total }} entries</p>
                <Pagination :links="serviceQuotations.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch} from 'vue';
import {usePage, Link, useForm} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Pagination from '../../Components/Pagination.vue';
import Swal from 'sweetalert2';
import debounce from 'lodash.debounce';
import {Inertia} from "@inertiajs/inertia";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    serviceQuotations: Object,
    filters: Object,
});

const form = useForm({
    quotation_id: null,
    signed_image_url: null,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        '/service-quotations',
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

const uploadImage = (id) => {
    form.reset();
    form.quotation_id = id;

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

const submitImage = (id) => {
    form.post(`/service-quotations/${id}/upload-photo`, {
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
            Inertia.post(`/service-quotations/${id}/void`, {}, {
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
            Inertia.delete(`/service-quotations/${id}`, {
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
