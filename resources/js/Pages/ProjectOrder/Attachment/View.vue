<template>
    <Layout :csrf="csrf" :projectOrder="projectOrder" :stages="stages" :tab="2">
        <!--Tab-->
        <Index :csrf="csrf" :projectOrder="projectOrder" :filters="filters" :attachments="attachments" :firstAttachments="firstAttachments"></Index>

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
    firstAttachments: Object,
    filters: Object,
});
</script>
