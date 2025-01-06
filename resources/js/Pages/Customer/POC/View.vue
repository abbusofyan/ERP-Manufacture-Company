<template>
    <Layout>
        <Details :customer="customer"></Details>
        <div class="my-tabs mt-26">
            <Tab :customer="customer"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block">
                    <div class="box rounded-md shadow-default bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                            <div class="text-18 font-medium">POC</div>
                            <div class="btn-group" v-if="permissions.includes('create-customer')">
                                <Link class="btn btn-purple" :href="`/customers/${customer.id}/poc/create`"><span class="icon fa-regular fa-plus"></span><span>Add POC</span></Link>
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
                                    <div class="flex items-center justify-between gap-1"><span>Designation</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Phone</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Fax</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Email</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1"><span>Remark</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                                </th>
                                <th><span>Actions</span></th>
                                </thead>
                                <tbody>
                                <tr v-for="poc in pocs.data" :key="poc.id">
                                    <td :class="poc.is_default ? 'border-left-primary' : null">
                                        <span v-if="poc.is_default" class="text-primary text-bold">{{ poc.first_name }}</span>
                                        <span v-else-if="Number(poc.status) === 2" class="text-danger">
                                            <del>{{ poc.first_name }}</del>
                                        </span>
                                        <span v-else>{{ poc.first_name }}</span>

                                        <em v-if="poc.is_default" class="fa-regular fa-circle-check text-primary ml-10 text-15"></em>
                                    </td>
                                    <td>
                                        <span v-if="Number(poc.status) === 2" class="text-danger">
                                            <del>{{ poc.last_name }}</del>
                                        </span>
                                        <span v-else>{{ poc.last_name }}</span>
                                    </td>
                                    <td>{{ poc.title }}</td>
                                    <td>{{ poc.country_phone_code ? poc.country_phone_code.phone_code + " " : null }}{{ poc.phone }}</td>
                                    <td>{{ poc.country_fax_code ? poc.country_fax_code.phone_code + " " : null }}{{ poc.fax }}</td>
                                    <td>{{ poc.email }}</td>
                                    <td>{{ poc.remark }}</td>
                                    <td>
                                        <div class="el-actions justify-content-end">
                                            <Link v-if="poc.is_default === 0 && permissions.includes('update-customer')" :href="`/customers/${customer.id}/pocs/${poc.id}/mark-approve`"><em class="fa-regular fa-circle-check"></em></Link>
                                            <!--                                            <a v-if="poc.status !== 2 && permissions.includes('update-customer')" :href="`#register-popup-${poc.id}`" data-fancybox><em class="fa-regular fa-user-xmark"></em></a>-->
                                            <Link v-if="poc.status !== 2 && permissions.includes('update-customer')" :href="`/customers/${customer.id}/poc/${poc.id}/edit`"><em class="fa-regular fa-pen-to-square"></em></Link>
                                            <a v-if="permissions.includes('delete-customer')" class="text-red" @click="destroy(poc.id)" href="javascript:void(0)"><em class="fa-regular fa-trash-can"></em></a>
                                        </div>
                                        <!--                                        <div class="register-popup popup-default max-w-[72rem]" :id="`register-popup-${poc.id}`" style="display: none;">-->
                                        <!--                                            <POCDetails :customer="customer" :poc="poc" :phoneCodes="phoneCodes"></POCDetails>-->
                                        <!--                                        </div>-->
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
import Details from "../../Customer/Detail.vue";
import POCDetails from "../../Customer/POC/Detail.vue";
import {computed, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Tab from "../../Customer/Tab.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    customer: Object,
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
            Inertia.delete(`/customers/${props.customer.id}/poc/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
