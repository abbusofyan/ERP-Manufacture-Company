<template>
    <ul class="tabs-nav">
        <li class="tab-li" :class="$page.component.startsWith('Customer/POC') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/customers/${customer.id}`">
                <span class="icon"><img class="svg" src="../../../assets/img/user.svg" alt="user"></span>
                <span>POC</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Customer/Address') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/customers/${customer.id}/addresses`">
                <span class="icon"><img class="svg" src="../../../assets/img/location.svg" alt="location"></span>
                <span>Address (es)</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Customer/Bank') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/customers/${customer.id}/banks`">
                <span class="icon fa-regular fa-bank"></span>
                <span>Bank Reference</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Customer/Discount') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/customers/${customer.id}/discounts`">
                <span class="icon fa-regular fa-cards-blank"></span>
                <span>Discount</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Customer/SaleOrder') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/customers/${customer.id}/invoices`">
                <span class="icon"><img class="svg" src="../../../assets/img/documents.svg" alt="documents"></span>
                <span>Sales Invoice</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Customer/Vehicle') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/customers/${customer.id}/vehicles`">
                <span class="icon fa-regular fa-car"></span>
                <span>Vehicle</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Customer/Activity') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/customers/${customer.id}/activity`">
                <span class="icon fa-regular fa-timer"></span>
                <span>Activity</span>
            </Link>
        </li>
    </ul>
</template>

<script setup>
import {usePage, Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    customer: Object,
});
</script>
