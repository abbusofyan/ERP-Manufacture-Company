<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Project Order</div>
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
                        <span>Project Order</span>
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
                    <div class="btn-group" v-if="permissions.includes('create-project_appointment')">
                        <Link class="btn btn-purple" href="/project-orders/create">Add New Order</Link>
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
                                <span>Stage</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Type</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Customer Name</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>PiC No.</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Vehicle No.</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Technician</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span>
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
                    <tr v-for="order in projectOrders.data" :key="order.id">
                        <td>{{ order.code }}</td>
                        <td>{{ order.stage ?? 1 }}</td>
                        <td>{{ order.project_order_type }}</td>
                        <td>{{ order.customer.name }}</td>
                        <td>
                            <div v-if="order.pic_phone_number">{{ order.pic_country_phone_code ? order.pic_country_phone_code.phone_code : '' }} {{ order.pic_phone_number }}</div>
                        </td>
                        <td>{{ order.vehicles.map(vehicle => vehicle.license_plate).join(', ') }}</td>
                        <td>{{ order.technician?.name }}</td>
                        <td>
                            <div class="el-tag" :class="order.status_class">
                                {{ order.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="el-actions justify-end">
                            </div>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('update-project_order')">
                                        <Link class="text-purple" :href="`/project-orders/${order.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <!--                                    <li v-if="permissions.includes('update-project_order') && Number(order.status) === 1">-->
                                    <!--                                        <a href="javascript:void(0)" @click="startProject(order.id)" class="text-nowrap text-success"><span class="icon"><img src="../../../assets/img/checkbox-green.svg" alt=""></span><span>Start Project Order</span></a>-->
                                    <!--                                    </li>-->
                                    <li v-if="permissions.includes('update-project_order') && order.status == 7">
                                        <Link :href="`/project-orders/${order.id}/update-detail`" method="post" :data="{ status: 8 }" class="text-orange"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Reopen</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-project_order') && order.status == 8">
                                        <Link :href="`/project-orders/${order.id}/update-detail`" method="post" :data="{ status: 7 }" class="text-green"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Complete</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('delete-project_order') && order.status != 7">
                                        <a @click="destroy(order.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Void</span></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ projectOrders.from }} to {{ projectOrders.to }} of {{ projectOrders.total }} entries</p>
                <Pagination :links="projectOrders.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch} from 'vue';
import {usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Pagination from '../../Components/Pagination.vue';
import Swal from 'sweetalert2';
import debounce from 'lodash.debounce';
import {Inertia} from "@inertiajs/inertia";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    projectOrders: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        '/project-orders',
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

const startProject = (id) => {
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
            Inertia.post(`/project-orders/${id}/processes/create-first`, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success !',
                        text: "Project Order started successfully!",
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
            Inertia.delete(`/project-orders/${id}`, {
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
                    });
                }
            });
        }
    });
};
</script>
