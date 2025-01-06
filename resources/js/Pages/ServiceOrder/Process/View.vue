<template>
    <Layout :csrf="csrf" :serviceOrder="serviceOrder" :stages="stages" :types="types" :tab="1">
        <!--Tab-->
        <Index :csrf="csrf" :serviceOrder="serviceOrder" :filters="filters" :processes="processes"></Index>

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
    processes: Object,
    filters: Object,
});
</script>
