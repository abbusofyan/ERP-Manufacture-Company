<template>
    <ul class="tabs-nav">
        <li class="tab-li" :class="$page.component.startsWith('Vendor/POC') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/vendors/${vendor.id}`">
                <span class="icon"><img class="svg" src="../../../assets/img/user.svg" alt="user"></span>
                <span>POC</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Vendor/Address') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/vendors/${vendor.id}/addresses`">
                <span class="icon"><img class="svg" src="../../../assets/img/location.svg" alt="location"></span>
                <span>Address (es)</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Vendor/SaleOrder') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/vendors/${vendor.id}/invoices`">
                <span class="icon"><img class="svg" src="../../../assets/img/documents.svg" alt="documents"></span>
                <span>Purchase Invoice</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Vendor/Attachment') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/vendors/${vendor.id}/attachments`">
                <span class="icon fa-regular fa-file"></span>
                <span>Attachments</span>
            </Link>
        </li>
        <li class="tab-li" :class="$page.component.startsWith('Vendor/Activity') ? 'active' : 'tab-li'">
            <Link class="tab-item" :href="`/vendors/${vendor.id}/activity`">
                <span class="icon fa-regular fa-timer"></span>
                <span>Activity</span>
            </Link>
        </li>
    </ul>
</template>

<script setup>
import {usePage, Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    vendor: Object,
});
</script>
