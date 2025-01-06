<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Milestones Setting List</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/milestones-settings">Milestones</Link>
                    </li>
                    <li>
                        <span>Setting</span>
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
                        <Link class="btn btn-purple" href="/milestones-settings/create">Add New Milestone</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th :class="{ sorting_asc: order == 'name' && by == 'asc', sorting_desc: order == 'name' && by == 'desc' }" @click="sort('name')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'duration' && by == 'asc', sorting_desc: order == 'duration' && by == 'desc' }" @click="sort('duration')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Duration (Days)</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'created_at' && by == 'asc', sorting_desc: order == 'created_at' && by == 'desc' }" @click="sort('created_at')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Created At</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>Active</th>
                        <th>
                            <span>Action</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="milestone in milestones.data" :key="milestone.id">
                        <td>
                            {{ milestone.name }}
                        </td>
                        <td>
                            {{ milestone.duration }}
                        </td>
                        <td>
                            {{ $filters.formatDateTime(milestone.created_at) }}
                        </td>
                        <td>
                            <div class="custom-checkbox style-1">
                                <input type="checkbox" disabled :checked="!milestone.deleted_at ? true : false">
                                <label></label>
                            </div>
                        </td>
                        <td>
                            <div class="el-actions">
                                <Link v-if="!milestone.deleted_at" class="text-green" :href="`/milestones-settings/${milestone.id}/edit`"><em class="fa-regular fa-pen-to-square"></em></Link>
                                <a v-if="!milestone.deleted_at" class="text-red" @click="destroy(milestone.id)" href="javascript:void(0)"><em class="fa-regular fa-eye-slash"></em></a>
                                <a v-if="milestone.deleted_at" class="text-green" @click="restore(milestone.id)" href="javascript:void(0)"><em class="fa-regular fa-eye"></em></a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ milestones.from }} to {{ milestones.to }} of {{ milestones.total }} entries</p>
                <Pagination :links="milestones.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage, Link } from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    milestones: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/milestones-settings",
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
    if (order.value === field) {
        by.value = by.value === "asc" ? "desc" : "asc";
    } else {
        order.value = field;
        by.value = "asc";
    }
};

const destroy = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Are you sure to make this inactive?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/milestones-settings/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

const restore = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Are you sure to make this active?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28C76F",
        cancelButtonColor: "#FFA700",
        confirmButtonText: "Yes, Restore!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/milestones-settings/restore/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
