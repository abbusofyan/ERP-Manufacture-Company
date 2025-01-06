<template>
    <Layout>
        <Details :customer="customer"></Details>
        <div class="my-tabs mt-26">
            <Tab :customer="customer"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block">
                    <div class="box rounded-md shadow-default bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                            <div class="text-18 font-medium">Bank(s)</div>
                            <div class="btn-group" v-if="permissions.includes('create-bank')">
                                <Link class="btn btn-purple" :href="`/customers/${customer.id}/banks/create`">
                                    <span class="icon fa-regular fa-plus"></span>
                                    <span>Add Bank</span>
                                </Link>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" v-if="banks.data.length >= 1">
                                <thead>
                                <tr>
                                    <th>Bank Name</th>
                                    <th>Bank Code</th>
                                    <th>Branch Code</th>
                                    <th>Account Number</th>
                                    <th>Swift Code</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="bank in banks.data" :key="bank.id">
                                    <td :class="bank.is_default ? 'border-left-primary text-primary text-bold' : null">
                                        {{ bank.bank_name }}
                                        <em v-if="bank.is_default" class="fa-regular fa-circle-check text-primary ml-10 text-15"></em>
                                    </td>
                                    <td>{{ bank.bank_code }}</td>
                                    <td>{{ bank.branch_code }}</td>
                                    <td>{{ bank.account_number }}</td>
                                    <td>{{ bank.swift_code }}</td>
                                    <td>
                                        <div class="el-actions justify-content-end">
                                            <Link v-if="Number(bank.is_default) === 0 && permissions.includes('update-bank')" :href="`/customers/${customer.id}/banks/${bank.id}/mark-approve`">
                                                <em class="fa-regular fa-circle-check"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('create-bank')" :href="`/customers/${customer.id}/banks/${bank.id}/duplicate`">
                                                <em class="fa-regular fa-copy"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('update-bank')" :href="`/customers/${customer.id}/banks/${bank.id}/edit`">
                                                <em class="fa-regular fa-pen-to-square"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('update-bank')" :href="`/customers/${customer.id}/banks/${bank.id}/switch-status`" method="POST">
                                                <em v-if="Number(bank.status) === 1" class="fa-regular fa-check text-purple" title="Set to active"></em>
                                                <em v-else class="fa-regular fa-times text-red" title="Set to inactive"></em>
                                            </Link>
                                            <a v-if="permissions.includes('delete-bank')" class="text-red" @click="destroy(bank.id)" href="javascript:void(0)">
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
                            <p>Showing {{ banks.from }} to {{ banks.to }} of {{ banks.total }} entries</p>
                            <Pagination :links="banks.links"/>
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
import {computed} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    customer: Object,
    filters: Object,
    banks: Object,
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
            Inertia.delete(`/customers/${props.customer.id}/banks/${id}`, {
                preserveScroll: true,
            });
        }
    });
}
</script>
