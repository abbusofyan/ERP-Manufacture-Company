<template>
    <Layout :csrf="csrf" :projectOrder="projectOrder" :stages="stages" :tab="3">
        <!--Tab-->
        <Index :csrf="csrf" :projectOrder="projectOrder" :filters="filters" :requirements="requirements"></Index>

    </Layout>
</template>

<script setup>
import Layout from "../Layout.vue";
import Index from "./Index.vue";

const props = defineProps({
    csrf: String,
    projectOrder: Object,
    stages: Object,
    requirements: Object,
    filters: Object,
});
</script>
