<template>
    <Layout :csrf="csrf" :projectOrder="projectOrder" :stages="stages" :tab="1">
        <!--Tab-->
        <Index :csrf="csrf" :projectOrder="projectOrder" :filters="filters" :processes="processes"></Index>

    </Layout>
</template>

<script setup>
import Layout from "../Layout.vue";
import Index from "./Index.vue";

const props = defineProps({
    csrf: String,
    projectOrder: Object,
    stages: Object,
    processes: Object,
    filters: Object,
});
</script>
