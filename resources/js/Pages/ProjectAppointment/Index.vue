<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Project Appointments</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/project-appointments">Project Order</Link>
                    </li>
                    <li>
                        <span>Project Appointment</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <div class="d-flex flex-wrap gap-16">
                    <label class="d-flex align-items-center gap-1">Show
                        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                    <label class="d-flex align-items-center gap-1">Status
                        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="status">
                            <option value="default">Default</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="converted">Converted</option>
                            <option value="expired">Expired</option>
                            <option value="void">Void</option>
                        </select>
                    </label>
                </div>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                    <div class="btn-group" v-if="permissions.includes('create-project_appointment')">
                        <Link class="btn btn-purple" href="/project-appointments/create">Add New Appointment</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th :class="{ sorting_asc: order == 'created_at' && by == 'asc', sorting_desc: order == 'created_at' && by == 'desc' }" @click="sort('created_at')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Dated created</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'cmp_number' && by == 'asc', sorting_desc: order == 'cmp_number' && by == 'desc' }" @click="sort('cmp_number')">
                            <div class="flex items-center justify-between gap-1">
                                <span>CMP No.</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Customer Name</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Vehicle No.</span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'date_of_appointment' && by == 'asc', sorting_desc: order == 'date_of_appointment' && by == 'desc' }" @click="sort('date_of_appointment')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Date of Appointment</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'status' && by == 'asc', sorting_desc: order == 'status' && by == 'desc' }" @click="sort('status')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
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
                    <tr v-for="appointment in appointments.data" :key="appointment.id">
                        <td>{{ $filters.formatDateTime(appointment.created_at) }}</td>
                        <td>{{ appointment.cmp_number }}</td>
                        <td>{{ appointment.customer.name }} <span class="ml-10 btn btn-sm btn-orange--brounded" v-if="Number(appointment.status) === 2">Draft</span></td>
                        <td>{{ appointment.vehicle.license_plate }}</td>
                        <td>{{ $filters.formatDate(appointment.date_of_appointment) }}</td>
                        <td>
                            <div class="el-tag" :class="appointment.status_class">
                                {{ appointment.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="el-actions justify-end">
                            </div>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('update-project_appointment')">
                                        <Link class="text-purple" :href="`/project-appointments/${appointment.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-project_appointment') && ![3, 4].includes(appointment.status) && !appointment.is_expired">
                                        <a href="javascript:void(0)" @click="convertProjectOrder(appointment.id)" class="text-nowrap text-warning"><span class="icon"><img src="../../../assets/img/checkbox.svg" alt=""></span><span>Convert to Project Order</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-project_appointment') && ![3, 4].includes(appointment.status)">
                                        <Link :href="`/project-appointments/${appointment.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('delete-project_appointment') && ![3, 4].includes(appointment.status)">
                                        <a @click="destroy(appointment.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Void</span></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ appointments.from }} to {{ appointments.to }} of {{ appointments.total }} entries</p>
                <Pagination :links="appointments.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, computed, watch} from 'vue';
import {usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Pagination from '../../Components/Pagination.vue';
import Swal from 'sweetalert2';
import debounce from 'lodash.debounce';
import {Inertia} from "@inertiajs/inertia";
import Default from "@vuepic/vue-datepicker";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    appointments: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);
let status = ref(props.filters.status);

const filter = () => {
    Inertia.get(
        '/project-appointments',
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
            status: status.value,
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

watch([order, by, paginate, status], () => {
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

const convertProjectOrder = (id) => {
    Swal.fire({
        title: 'Convert To Draft?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea5455',
        cancelButtonColor: '#009CDB',
        confirmButtonText: 'Yes, Proceed!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/project-appointments/${id}/convert-project-order`, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: () => {
                    Swal.fire({
                            title: 'Success !',
                            text: "Successfully moved to draft!",
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
            Inertia.delete(`/project-appointments/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                            title: 'Success !',
                            text: "Your changes has been saved!",
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
