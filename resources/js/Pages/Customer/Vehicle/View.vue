<template>
    <Layout>
        <Details :customer="customer"></Details>
        <div class="my-tabs mt-26">
            <Tab :customer="customer"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block">
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
                                <div class="btn-group" v-if="permissions.includes('create-vehicle')">
                                    <Link class="btn btn-purple" href="/vehicles/create">Add New Vehicle</Link>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table select-rows">
                                <thead>
                                <tr>
                                    <th :class="{ sorting_asc: order == 'license_plate' && by == 'asc', sorting_desc: order == 'license_plate' && by == 'desc' }" @click="sort('license_plate')">
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Vehicle Number</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                        </div>
                                    </th>
                                    <th :class="{ sorting_asc: order == 'customer_id' && by == 'asc', sorting_desc: order == 'customer_id' && by == 'desc' }" @click="sort('customer_id')">
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Customer Name</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                        </div>
                                    </th>
                                    <th :class="{ sorting_asc: order == 'vehicle_make' && by == 'asc', sorting_desc: order == 'vehicle_make' && by == 'desc' }" @click="sort('vehicle_make')">
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Vehicle Make</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                        </div>
                                    </th>
                                    <th :class="{ sorting_asc: order == 'model' && by == 'asc', sorting_desc: order == 'model' && by == 'desc' }" @click="sort('model')">
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Vehicle Model</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                        </div>
                                    </th>
                                    <th :class="{ sorting_asc: order == 'type' && by == 'asc', sorting_desc: order == 'type' && by == 'desc' }" @click="sort('type')">
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Vehicle Type</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                        </div>
                                    </th>
                                    <th :class="{ sorting_asc: order == 'end_user' && by == 'asc', sorting_desc: order == 'end_user' && by == 'desc' }" @click="sort('end_user')">
                                        <div class="flex items-center justify-between gap-1">
                                            <span>End User</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                        </div>
                                    </th>
                                    <th>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="vehicle in vehicles.data" :key="vehicle.id">
                                    <td>{{ vehicle.license_plate }}</td>
                                    <td>{{ vehicle.customer.name }}</td>
                                    <td>{{ vehicle.vehicle_make }}</td>
                                    <td>{{ vehicle.model }}</td>
                                    <td>{{ vehicle.type }}</td>
                                    <td>{{ vehicle.end_user }}</td>
                                    <td>
                                        <div class="more">
                                            <div class="icon"><img src="../../../../assets/img/more.svg" alt=""></div>
                                            <ul class="more-panel">
                                                <li v-if="permissions.includes('view-vehicle')">
                                                    <Link :href="`/vehicles/${vehicle.id}`"><span class="icon"><img src="../../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                                </li>
                                                <li v-if="permissions.includes('update-vehicle')">
                                                    <Link :href="`/vehicles/${vehicle.id}/edit`"><span class="icon"><img src="../../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                                </li>
                                                <li v-if="permissions.includes('delete-vehicle')">
                                                    <a href="javascript:void(0)" @click="destroy(vehicle.id)">
                                                        <span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span>
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
                            <p>Showing {{ vehicles.from }} to {{ vehicles.to }} of {{ vehicles.total }} entries</p>
                            <Pagination :links="vehicles.links"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import Details from "../../Customer/Detail.vue";
import {computed, onMounted, ref, watch} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Pagination from "@/Components/Pagination.vue";
import debounce from "lodash.debounce";
import Tab from "../../Customer/Tab.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    customer: Object,
    vehicles: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/vehicles",
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
            Inertia.delete(`/vehicles/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
