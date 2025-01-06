<template>
    <Layout :csrf="csrf" :serviceOrder="serviceOrder" :stages="stages" :types="types" :tab="6">
        <!--Tab-->
        <Index :csrf="csrf" :serviceOrder="serviceOrder" :filters="filters" :outsourcedItems="outsourcedItems"></Index>

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
    outsourcedItems: Object,
    filters: Object,
});
</script>
