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
                    <Link href="/vendors">
                        Vendors
                    </Link>
                </li>
                <li class="active"><span>HWEE JAN (S) PTE LTD</span></li>
            </ol>
        </nav>
    </div>
    <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center"><span>Contact Details</span>
            <div class="text-green"><em class="fa-regular fa-money-bill-1"></em><span class="pl-5">$ 0.00</span></div>
        </div>
        <table class="table-1-el w-full">
            <tr>
                <th>Vendor ID</th>
                <td>{{ vendor.code ?? '--' }}</td>
            </tr>
            <tr>
                <th>Vendor Name</th>
                <td>{{ vendor.name ?? '--' }}</td>
            </tr>
            <tr>
                <th v-if="Number(vendor.info_type) === 1">Office Number</th>
                <th v-else>Phone Number</th>
                <td>{{ vendor.country_phone_code ? vendor.country_phone_code.phone_code : null }} {{ vendor.phone ?? '--' }}</td>
            </tr>
            <tr>
                <th>Account Manager</th>
                <td>{{ vendor.account_manager_text ?? '--' }}</td>
            </tr>
            <tr>
                <th>UEN</th>
                <td>{{ vendor.uen ?? '--' }}</td>
            </tr>
        </table>


        <div class="text-15 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center pt-36">
            <span>Accounting</span>
        </div>
        <table class="table-1-el w-full">
            <tr>
                <th>Credit Term</th>
                <td>{{ vendor.credit_term ?? '--' }}</td>
            </tr>
            <tr>
                <th>Credit Limit</th>
                <td>{{ vendor.credit_limit ?? '--' }}</td>
            </tr>
        </table>

        <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]" v-if="permissions.includes('update-vendor')">
            <div class="btn-group">
                <Link class="btn btn-purple" :href="`/vendors/${vendor.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                <a class="btn btn-red btn-red--brounded" @click="destroy()" href="javascript:void(0)"><em class="fa-regular fa-xmark"></em><span>Inactive</span></a>
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
    vendor: Object,
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
            Inertia.delete(`/vendors/${props.vendor.id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
