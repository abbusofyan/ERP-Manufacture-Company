<template>
    <Layout>
        <Details :customer="customer"></Details>
        <div class="my-tabs mt-26">
            <Tab :customer="customer"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block">
                    <div class="box rounded-md shadow-default bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                            <div class="text-18 font-medium">Address (es)</div>
                            <div class="btn-group" v-if="permissions.includes('create-customer')">
                                <Link class="btn btn-purple" :href="`/customers/${customer.id}/addresses/create`"><span class="icon fa-regular fa-plus"></span><span>Add Address</span></Link>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" v-if="addresses.data.length >= 1">
                                <thead>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Block</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Street Name</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Unit Level</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Unit Number</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>City</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Region</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Country</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Postal Code</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th><span>Actions</span></th>
                                </thead>
                                <tbody>
                                <tr v-for="address in addresses.data" :key="address.id">
                                    <td :class="address.is_default ? 'border-left-primary text-primary text-bold' : null">
                                        {{ address.block }}
                                        <em v-if="address.is_default" class="fa-regular fa-circle-check text-primary ml-10 text-15"></em>
                                    </td>
                                    <td>{{ address.street_name }}</td>
                                    <td>{{ address.unit_level }}</td>
                                    <td>{{ address.unit_number }}</td>
                                    <td>{{ address.city }}</td>
                                    <td>{{ address.region }}</td>
                                    <td>{{ address.country }}</td>
                                    <td>{{ address.postal_code }}</td>
                                    <td>
                                        <div class="el-actions justify-content-end">
                                            <Link v-if="Number(address.is_default) === 0 && permissions.includes('update-customer')" :href="`/customers/${customer.id}/addresses/${address.id}/mark-approve`">
                                                <em class="fa-regular fa-circle-check"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('create-customer')" :href="`/customers/${customer.id}/addresses/${address.id}/duplicate`">
                                                <em class="fa-regular fa-copy"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('update-customer')" :href="`/customers/${customer.id}/addresses/${address.id}/edit`">
                                                <em class="fa-regular fa-pen-to-square"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('update-customer')" :href="`/customers/${customer.id}/addresses/${address.id}/switch-status`" method="POST">
                                                <em v-if="Number(address.status) === 1" class="fa-regular fa-check text-purple" title="Set to active"></em>
                                                <em v-else class="fa-regular fa-times text-red" title="Set to inactive"></em>
                                            </Link>
                                            <a v-if="permissions.includes('delete-customer')" class="text-red" @click="destroy(address.id)" href="javascript:void(0)">
                                                <em class="fa-regular fa-trash-can"></em>
                                            </a>
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
                            <p>Showing {{ addresses.from }} to {{ addresses.to }} of {{ addresses.total }} entries</p>
                            <Pagination :links="addresses.links"/>
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
import Tab from "../../Customer/Tab.vue";
import {computed, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    customer: Object,
    filters: Object,
    addresses: Object,
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
            Inertia.delete(`/customers/${props.customer.id}/addresses/${id}`, {
                preserveScroll: true,
            });
        }
    });
}

</script>
