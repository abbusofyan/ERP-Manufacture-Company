<template>
    <Layout :csrf="csrf" :projectOrder="projectOrder" :stages="stages" :tab="5">
        <!--Tab-->
        <Index :csrf="csrf" :projectOrder="projectOrder" :filters="filters" :attachments="attachments"></Index>

    </Layout>
</template>

<script setup>
import Layout from "../Layout.vue";
import Index from "./Index.vue";

const props = defineProps({
    csrf: String,
    projectOrder: Object,
    stages: Object,
    attachments: Object,
    filters: Object,
});
</script>
