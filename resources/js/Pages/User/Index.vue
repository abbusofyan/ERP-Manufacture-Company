<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">User List</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/users">User Management</Link>
                    </li>
                    <li>
                        <Link href="/users">User List</Link>
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
                <div class="btn-group" v-if="permissions.includes('create-user')">
                    <Link class="btn btn-purple" href="/users/create">Add New User</Link>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th :class="{ sorting_asc: order == 'username' && by == 'asc', sorting_desc: order == 'username' && by == 'desc' }" @click="sort('username')">
                            <div class="flex items-center justify-between gap-1">
                                <span>username</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'name' && by == 'asc', sorting_desc: order == 'name' && by == 'desc' }" @click="sort('name')">
                            <div class="flex items-center justify-between gap-1">
                                <span>name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Role</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'phone' && by == 'asc', sorting_desc: order == 'phone' && by == 'desc' }" @click="sort('phone')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Mobile</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'email' && by == 'asc', sorting_desc: order == 'email' && by == 'desc' }" @click="sort('email')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Email</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'email_verified_at' && by == 'asc', sorting_desc: order == 'name' && by == 'email_verified_at' }" @click="sort('email_verified_at')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                        <td>{{ user.username ?? '---' }}</td>
                        <td>{{ user.name ?? '---' }}</td>
                        <td>{{ user.roles[0] ? user.roles[0].name : '---' }}</td>
                        <td>{{ user.phone ?? '---' }}</td>
                        <td>{{ user.email ?? '---' }}</td>
                        <td>{{ user.status_text ?? '---' }}</td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li>
                                        <Link v-if="permissions.includes('view-user')" :href="`/users/${user.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li>
                                        <Link v-if="permissions.includes('update-user')" :href="`/users/${user.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li>
                                    <li>
                                        <a v-if="permissions.includes('delete-user')" @click="destroy(user.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ users.from }} to {{ users.to }} of {{ users.total }} entries</p>
                <Pagination :links="users.links"/>
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
    users: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/users",
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

const sort = (data) => {
    order.value = data;

    if (by.value == "asc") {
        by.value = "desc";
    } else {
        by.value = "asc";
    }
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
            Inertia.delete(`/users/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
