<template>
    <Layout :csrf="csrf" :projectOrder="projectOrder" :stages="stages" :tab="4">
        <!--Tab-->
        <Index :csrf="csrf" :projectOrder="projectOrder" :filters="filters" :requirementsUsed="requirementsUsed"></Index>

    </Layout>
</template>

<script setup>
import Layout from "../Layout.vue";
import Index from "./Index.vue";

const props = defineProps({
    csrf: String,
    projectOrder: Object,
    stages: Object,
    requirementsUsed: Object,
    filters: Object,
});
</script>
