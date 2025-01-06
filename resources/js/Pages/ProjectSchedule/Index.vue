<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Schedule</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/project-orders">Project Order</Link>
                    </li>
                    <li>
                        <span>Schedule</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-[3rem] mb-[2.6rem]">
            <div class="border-solid border-[1px] border-purple rounded-[.5rem] overflow-hidden">
                <Link class="btn text-purple border-0 border-purple border-solid border-r-[1px] rounded-0" :href="`/project-schedules?current_month=${monthTexts.previous.value}`">
                    <span class="fa fa-chevron-left"></span>
                </Link>
                <a class="btn text-purple" href="javascript:void(0)">{{ monthTexts.current.text }}</a>
                <Link class="btn text-purple border-0 border-purple border-solid border-l-[1px] rounded-0" :href="`/project-schedules?current_month=${monthTexts.next.value}`">
                    <span class="fa fa-chevron-right"></span>
                </Link>
            </div>
            <div class="form-group mb-0">
                <input class="form-control" type="month" v-model="form.current_month" @change="submitForm" placeholder="Select Month">
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-12">
                <Table :projects="prevProjects" :month="monthTexts.previous"></Table>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12">
                <Table :projects="currentProjects" :month="monthTexts.current"></Table>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12">
                <Table :projects="nextProjects" :month="monthTexts.next"></Table>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {usePage, Link, useForm} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Table from "./MonthTable.vue";
import {Inertia} from "@inertiajs/inertia";

const form = useForm({
    current_month: null,
});

const props = defineProps({
    prevProjects: Object,
    currentProjects: Object,
    nextProjects: Object,
    monthTexts: Object,
});

if (props.monthTexts && props.monthTexts.current && props.monthTexts.current.value) {
    form.current_month = props.monthTexts.current.value;
}

const submitForm = () => {
    Inertia.get('/project-schedules', {current_month: form.current_month});
};
</script>
