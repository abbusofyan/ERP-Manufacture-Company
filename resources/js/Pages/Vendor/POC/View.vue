<template>
    <Layout>
        <Details :vendor="vendor"></Details>
        <div class="my-tabs mt-26">
            <Tab :vendor="vendor"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block">
                    <div class="box rounded-md shadow-default bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                            <div class="text-18 font-medium">POC</div>
                            <div class="btn-group" v-if="permissions.includes('create-vendor')">
                                <Link class="btn btn-purple" :href="`/vendors/${vendor.id}/poc/create`"><span class="icon fa-regular fa-plus"></span><span>Add POC</span></Link>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" v-if="pocs.data.length >= 1">
                                <thead>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>First name</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Last Name</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Tittle</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Phone</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Email</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th><span>Actions</span></th>
                                </thead>
                                <tbody>
                                <tr v-for="poc in pocs.data" :key="poc.id">
                                    <td :class="Number(poc.status) === 1 ? 'border-left-primary' : null">
                                        <span v-if="Number(poc.status) === 1" class="text-primary text-bold">{{ poc.first_name }}</span>
                                        <span v-else-if="Number(poc.status) === 2" class="text-danger">
                                            <del>{{ poc.first_name }}</del>
                                        </span>
                                        <span v-else>{{ poc.first_name }}</span>
                                    </td>
                                    <td>
                                        <span v-if="Number(poc.status) === 2" class="text-danger">
                                            <del>{{ poc.last_name }}</del>
                                        </span>
                                        <span v-else>{{ poc.last_name }}</span>
                                    </td>
                                    <td>{{ poc.title }}</td>
                                    <td>{{ poc.country_phone_code ? poc.country_phone_code.phone_code + " " : null }}{{ poc.phone }}</td>
                                    <td>{{ poc.email }}</td>
                                    <td>

                                        <div class="more">
                                            <div class="icon"><img src="../../../../assets/img/more.svg" alt=""></div>
                                            <ul class="more-panel">
                                                <li>
                                                    <Link v-if="Number(poc.status) === 0 && permissions.includes('update-vendor')" :href="`/vendors/${vendor.id}/pocs/${poc.id}/mark-approve`"><span class="icon fa-regular fa-circle-check"></span><span>Approve</span></Link>
                                                </li>
                                                <li>
                                                    <a v-if="Number(poc.status) !== 2 && permissions.includes('update-vendor')" :href="`#register-popup-${poc.id}`" data-fancybox><span class="icon fa-regular fa-user-xmark"></span><span>Mark as Resign</span></a>
                                                </li>
                                                <li>
                                                    <Link v-if="Number(poc.status) !== 2 && permissions.includes('update-vendor')" :href="`/vendors/${vendor.id}/poc/${poc.id}/edit`"><span class="icon fa-regular fa-pen-to-square"></span><span>Edit</span></Link>
                                                </li>
                                                <li>
                                                    <a v-if="permissions.includes('delete-vendor')" class="text-red" @click="destroy(poc.id)" href="javascript:void(0)"><span class="icon fa-regular fa-trash-can"></span><span>Delete</span></a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- <div class="el-actions">
                                            <Link v-if="Number(poc.status) === 0 && permissions.includes('update-vendor')" :href="`/vendors/${vendor.id}/pocs/${poc.id}/mark-approve`"><em class="fa-regular fa-circle-check"></em></Link>
                                            <a v-if="Number(poc.status) !== 2 && permissions.includes('update-vendor')" :href="`#register-popup-${poc.id}`" data-fancybox><em class="fa-regular fa-user-xmark"></em></a>
                                            <Link v-if="Number(poc.status) !== 2 && permissions.includes('update-vendor')" :href="`/vendors/${vendor.id}/poc/${poc.id}/edit`"><em class="fa-regular fa-pen-to-square"></em></Link>
                                            <a v-if="permissions.includes('delete-vendor')" class="text-red" @click="destroy(poc.id)" href="javascript:void(0)"><em class="fa-regular fa-trash-can"></em></a>
                                        </div> -->
                                        <div class="register-popup popup-default max-w-[72rem]" :id="`register-popup-${poc.id}`" style="display: none;">
                                            <POCDetails :vendor="vendor" :poc="poc" :phoneCodes="phoneCodes"></POCDetails>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="py-20 px-25" v-else>
                                Nothing to show.
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                            <p>Showing {{ pocs.from }} to {{ pocs.to }} of {{ pocs.total }} entries</p>
                            <Pagination :links="pocs.links"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import Details from "../../Vendor/Detail.vue";
import POCDetails from "../../Vendor/POC/Detail.vue";
import {computed, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Tab from "@/Pages/Vendor/Tab.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    vendor: Object,
    pocs: Object,
    phoneCodes: Array,
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
            Inertia.delete(`/vendors/${props.vendor.id}/poc/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
