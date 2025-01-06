<template>
    <Layout :csrf="csrf" :serviceOrder="serviceOrder" :stages="stages" :types="types" :tab="5">
        <!--Tab-->
        <Index :csrf="csrf" :serviceOrder="serviceOrder" :filters="filters" :attachments="attachments"></Index>

    </Layout>
</template>

<script setup>
import Layout from "../Layout.vue";
import Index from "./Index.vue";

const props = defineProps({
    csrf: String,
    serviceOrder: Object,
    stages: Object,
    types: Array,
    attachments: Object,
    filters: Object,
});
</script>
