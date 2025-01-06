<template>
    <Layout :csrf="csrf" :serviceOrder="serviceOrder" :stages="stages" :types="types" :tab="4">
        <!--Tab-->
        <Index :csrf="csrf" :serviceOrder="serviceOrder" :filters="filters" :requirementsUsed="requirementsUsed"></Index>

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
    requirementsUsed: Object,
    filters: Object,
});
</script>
