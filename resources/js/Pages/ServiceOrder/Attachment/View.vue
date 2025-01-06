<template>
    <Layout :csrf="csrf" :serviceOrder="serviceOrder" :stages="stages" :types="types" :tab="2">
        <!--Tab-->
        <Index :csrf="csrf" :serviceOrder="serviceOrder" :filters="filters" :attachments="attachments" :firstAttachments="firstAttachments"></Index>

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
    firstAttachments: Object,
    filters: Object,
});
</script>
