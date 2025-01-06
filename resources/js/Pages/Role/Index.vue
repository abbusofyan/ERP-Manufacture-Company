<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Roles</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/users">User Management</Link>
                    </li>
                    <li class="active">
                        <Link href="/roles">Roles</Link>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search" v-model="search">
                    </div>
                </div>
                <div class="btn-group">
                    <Link class="btn btn-purple" href="/roles/create">Add New Role</Link>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th :class="{ sorting_asc: order == 'name' && by == 'asc', sorting_desc: order == 'name' && by == 'desc' }" @click="sort('name')">
                            <div class="flex items-center justify-between gap-1"><span>Role</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th :class="{ sorting_asc: order == 'updated_at' && by == 'asc', sorting_desc: order == 'updated_at' && by == 'desc' }" @click="sort('updated_at')">
                            <div class="flex items-center justify-between gap-1"><span>Last Updated</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="role in roles.data" :key="role.id">
                        <td>{{ role.name }}</td>
                        <td>{{ $filters.formatDateTime(role.updated_at) }}</td>
                        <td>
                            <div class="el-actions justify-end">
                                <Link class="text-purple" :href="`/roles/${role.id}/edit`" v-if="permissions.includes('update-user')"><em class="fa-regular fa-pen-to-square"></em></Link>
                                <a v-if="role.id !== 1 && permissions.includes('delete-user')" class="text-red" href="javascript:void(0);" @click="destroy(role.id)"><em class="fa-regular fa-trash-can"></em></a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ roles.from }} to {{ roles.to }} of {{ roles.total }} entries</p>
                <Pagination :links="roles.links"/>
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
    roles: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/roles",
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
            Inertia.delete(`/roles/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
