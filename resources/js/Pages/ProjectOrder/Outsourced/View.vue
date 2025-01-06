<template>
    <Layout :csrf="csrf" :projectOrder="projectOrder" :stages="stages" :tab="6">
        <!--Tab-->
        <Index :csrf="csrf" :projectOrder="projectOrder" :filters="filters" :outsourcedItems="outsourcedItems"></Index>

    </Layout>
</template>

<script setup>
import Layout from "../Layout.vue";
import Index from "./Index.vue";

const props = defineProps({
    csrf: String,
    projectOrder: Object,
    stages: Object,
    outsourcedItems: Object,
    filters: Object,
});
</script>
