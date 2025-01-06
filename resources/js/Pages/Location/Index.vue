<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Store</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/locations">Location</Link>
                    </li>
                    <li>
                        <span>List</span>
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
                    <div class="btn-group" v-if="permissions.includes('create-location')">
                        <Link class="btn btn-purple" href="/locations/create">Add New Location</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th :class="{ sorting_asc: order == 'code' && by == 'asc', sorting_desc: order == 'code' && by == 'desc' }" @click="sort('code')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Code</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'name' && by == 'asc', sorting_desc: order == 'name' && by == 'desc' }" @click="sort('name')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Location</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'store_id' && by == 'asc', sorting_desc: order == 'store_id' && by == 'desc' }" @click="sort('store_id')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Store</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Total Product</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>QR Code</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'status' && by == 'asc', sorting_desc: order == 'status' && by == 'desc' }" @click="sort('status')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'updated_at' && by == 'asc', sorting_desc: order == 'updated_at' && by == 'desc' }" @click="sort('updated_at')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Last Updated</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="location in locations.data" :key="location.id">
                        <td>{{ location.code }}</td>
                        <td>{{ location.name }}</td>
                        <td>{{ location.store.name }}</td>
                        <td>0</td>
                        <td>
                            <a href="#" class="text-primary text-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.25 6H15C16.275 6 17.25 6.975 17.25 8.25V12C17.25 13.275 16.275 14.25 15 14.25H14.25V16.5C14.25 16.95 13.95 17.25 13.5 17.25H4.5C4.05 17.25 3.75 16.95 3.75 16.5V14.25H3C1.725 14.25 0.75 13.275 0.75 12V8.25C0.75 6.975 1.725 6 3 6H3.75V1.5C3.75 1.05 4.05 0.75 4.5 0.75H13.5C13.95 0.75 14.25 1.05 14.25 1.5V6ZM12.75 2.25H5.25V6H12.75V2.25ZM12.75 15.75H5.25V11.25H12.75V15.75ZM15 12.75C15.45 12.75 15.75 12.45 15.75 12V8.25C15.75 7.8 15.45 7.5 15 7.5H3C2.55 7.5 2.25 7.8 2.25 8.25V12C2.25 12.45 2.55 12.75 3 12.75H3.75V10.5C3.75 10.05 4.05 9.75 4.5 9.75H13.5C13.95 9.75 14.25 10.05 14.25 10.5V12.75H15Z" fill="black"/>
                                </svg>
                                <i data-feather='printer'></i> Print
                            </a>
                        </td>
                        <td>
                            <span class="status-button" :class="location.status_button">
                                {{ location.status_text }}
                            </span>
                        </td>
                        <td>{{ $filters.formatDateTime(location.updated_at) }}</td>
                        <td>
                            <div class="el-actions justify-end">
                                <Link class="text-purple" :href="`/locations/${location.id}/edit`" v-if="permissions.includes('update-location')"><em class="fa-regular fa-pen-to-square"></em></Link>
                                <a v-if="permissions.includes('delete-location')" class="text-red" href="javascript:void(0);" @click="destroy(location.id)"><em class="fa-regular fa-trash-can"></em></a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ locations.from }} to {{ locations.to }} of {{ locations.total }} entries</p>
                <Pagination :links="locations.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    locations: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/locations",
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
            Inertia.delete(`/locations/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
