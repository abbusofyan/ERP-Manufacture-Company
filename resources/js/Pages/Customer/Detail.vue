<template>
    <div class="flex flex-wrap items-center gap-13 mb-26">
        <div class="big-title">HWEE JAN (S) PTE LTD</div>
        <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
        <nav class="breadcrumbs">
            <ol>
                <li>
                    <Link href="/">
                        <span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span>
                    </Link>
                </li>
                <li>
                    <Link href="/customers">
                        Customer
                    </Link>
                </li>
                <li class="active"><span>HWEE JAN (S) PTE LTD</span></li>
            </ol>
        </nav>
    </div>
    <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center"><span>Customer Details</span>
            <div class="text-green" v-if="permissions.includes('view-customer-revenue')"><em class="fa-regular fa-money-bill-1"></em><span class="pl-5">$ {{ customer.revenue }}</span></div>
        </div>
        <table class="table-1-el w-full">
            <tr>
                <th>Customer ID</th>
                <td>{{ customer.code ?? '--' }}</td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td>{{ customer.name }}</td>
            </tr>
            <tr>
                <th>Customer Type</th>
                <td>{{ customer.customer_type_text ?? '--' }}</td>
            </tr>
            <tr>
                <th v-if="Number(customer.info_type) === 1">Office Number</th>
                <th v-else>Phone Number</th>
                <td>{{ customer.country_phone_code ? customer.country_phone_code.phone_code : null }} {{ customer.phone ?? '--' }}</td>
            </tr>
            <tr>
                <th>Account Manager</th>
                <td>{{ customer.account_manager_text ?? '--' }}</td>
            </tr>
            <tr>
                <th>Service</th>
                <td>{{ customer.service_text ?? '--' }}</td>
            </tr>
            <tr>
                <th>Refrigeration Sales</th>
                <td>{{ customer.refrigeration_sales_text ?? '--' }}</td>
            </tr>
            <tr>
                <th>Project</th>
                <td>{{ customer.project_text ?? '--' }}</td>
            </tr>
            <tr>
                <th>UEN</th>
                <td>{{ customer.uen ?? '--' }}</td>
            </tr>
        </table>
        <div class="text-15 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center pt-36">
            <span>Accounting</span>
        </div>
        <table class="table-1-el w-full">
            <tr>
                <th>Payment Term</th>
                <td>{{ customer.credit_term ?? '--' }}</td>
            </tr>
            <tr>
                <th>Credit Limit</th>
                <td>{{ customer.credit_limit ?? '--' }}</td>
            </tr>
        </table>

        <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]" v-if="permissions.includes('update-customer')">
            <div class="btn-group">
                <Link v-if="Number(customer.info_type) === 1" class="btn btn-purple" :href="`/customers/organisation/${customer.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                <Link v-else-if="Number(customer.info_type) === 2" class="btn btn-purple" :href="`/customers/individual/${customer.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                <Link v-else class="btn btn-purple" :href="`/customers/salesperson/${customer.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                <a class="btn btn-red btn-red--brounded" href="javascript:void(0)" @click="destroy"><em class="fa-regular fa-xmark"></em><span>Inactive</span></a>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    customer: Object,
});

const destroy = () => {
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
            Inertia.delete(`/customers/${props.customer.id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
