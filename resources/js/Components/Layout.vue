<template>

    <Sidebar/>

    <div id="main">

        <Header/>

        <div class="py-26">

            <slot/>

        </div>

        <Footer/>

    </div>
</template>

<script setup>
import Sidebar from "./Sidebar.vue";
import Header from "./Header.vue";
import Footer from "./Footer.vue";
import {onMounted, computed, onBeforeUpdate, onUpdated} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import {useToast} from "vue-toastification";

const toast = useToast();
const created = computed(() => usePage().props.value.flash.created);
const updated = computed(() => usePage().props.value.flash.updated);
const deleted = computed(() => usePage().props.value.flash.deleted);

onMounted(() => {
    if (created.value) {
        toast.success(created.value, {
            icon: "fa-solid fa-check",
            timeout: 3000,
        });
    }

    if (updated.value) {
        toast.info(updated.value, {
            icon: "fa-solid fa-pen-to-square",
            timeout: 3000,
        });
    }
});

onUpdated(() => {
    if (deleted.value) {
        toast.error(deleted.value, {
            icon: "fa-solid fa-trash-can",
            timeout: 3000,
        });
    }
});
</script>
