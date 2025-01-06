<template>
    <Layout>
        <Details :customer="customer"></Details>
        <div class="my-tabs mt-26">
            <Tab :customer="customer"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block">
                    <div class="box rounded-md shadow-default bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                            <div class="text-18 font-medium">Discount(s)</div>
                            <div class="btn-group" v-if="permissions.includes('create-discount')">
                                <Link class="btn btn-purple" :href="`/customers/${customer.id}/discounts/create`">
                                    <span class="icon fa-regular fa-plus"></span>
                                    <span>Add Discount</span>
                                </Link>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" v-if="discounts.data.length >= 1">
                                <thead>
                                <tr>
                                    <th>Category ID</th>
                                    <th>Category Name</th>
                                    <th>Discount Percentage</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="discount in discounts.data" :key="discount.id">
                                    <td :class="discount.is_default ? 'border-left-primary text-primary text-bold' : null">
                                        {{ discount.category_id }}
                                        <em v-if="discount.is_default" class="fa-regular fa-circle-check text-primary ml-10 text-15"></em>
                                    </td>
                                    <td>{{ discount.category_name }}</td>
                                    <td>{{ discount.discount_percentage }}%</td>
                                    <td>
                                        <div class="el-actions justify-content-end">
                                            <Link v-if="Number(discount.is_default) === 0 && permissions.includes('update-discount')" :href="`/customers/${customer.id}/discounts/${discount.id}/mark-approve`">
                                                <em class="fa-regular fa-circle-check"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('create-discount')" :href="`/customers/${customer.id}/discounts/${discount.id}/duplicate`">
                                                <em class="fa-regular fa-copy"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('update-discount')" :href="`/customers/${customer.id}/discounts/${discount.id}/edit`">
                                                <em class="fa-regular fa-pen-to-square"></em>
                                            </Link>
                                            <Link v-if="permissions.includes('update-discount')" :href="`/customers/${customer.id}/discounts/${discount.id}/switch-status`" method="POST">
                                                <em v-if="Number(discount.status) === 1" class="fa-regular fa-check text-purple" title="Set to active"></em>
                                                <em v-else class="fa-regular fa-times text-red" title="Set to inactive"></em>
                                            </Link>
                                            <a v-if="permissions.includes('delete-discount')" class="text-red" @click="destroy(discount.id)" href="javascript:void(0)">
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
                            <p>Showing {{ discounts.from }} to {{ discounts.to }} of {{ discounts.total }} entries</p>
                            <Pagination :links="discounts.links"/>
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
    discounts: Object,
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
            Inertia.delete(`/customers/${props.customer.id}/discounts/${id}`, {
                preserveScroll: true,
            });
        }
    });
}
</script>
