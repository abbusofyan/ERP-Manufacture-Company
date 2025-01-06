<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Production Working Hours List</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/productions">Productions</Link>
                    </li>
                    <li>
                        <span>Working Hours</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <div class="btn-group">
                    <label>Month:
                        <select v-model="month">
                            <option v-for="(m, index) in months" :key="index" :value="index + 1">{{ m }}</option>
                        </select>
                    </label>
                    <label>Year:
                        <select v-model="year">
                            <option v-for="y in yearRange" :value="y">{{ y }}</option>
                        </select>
                    </label>
                </div>
                <div class="btn-group">
                    <Link class="btn btn-purple" href="/production-working-hours/create">Add New Working Hour</Link>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Day Hours</th>
                        <th>
                            <span>Action</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="data in workingHours" :key="data.date">
                        <td>
                            {{  $filters.formatDateOnly(data.date) }}
                        </td>
                        <td>
                            {{ data.start_time }}
                        </td>
                        <td>
                            {{ data.end_time }}
                        </td>
                        <td>
                            {{ data.day_hours }}
                        </td>
                        <td>
                            <div class="el-actions">
                                <Link v-if="data.specificWH && data.specificWH.id" class="text-green" :href="`/production-working-hours/${data.specificWH.id}/edit`"><em class="fa-regular fa-pen-to-square"></em></Link>
                                <Link v-else class="text-green" :href="`/production-working-hours/create?date=${data.date}`"><em class="fa-regular fa-pen-to-square"></em></Link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box rounded-md shadow-default bg-white mt-20 pt-20">
            <div class="px-24 pb-10 text-bold text-15 text-uppercase">Default Time</div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th :class="{ sorting_asc: order == 'start_time' && by == 'asc', sorting_desc: order == 'start_time' && by == 'desc' }" @click="sort('start_time')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Start Time</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'end_time' && by == 'asc', sorting_desc: order == 'end_time' && by == 'desc' }" @click="sort('end_time')">
                            <div class="flex items-center justify-between gap-1">
                                <span>End Time</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th :class="{ sorting_asc: order == 'specific_date' && by == 'asc', sorting_desc: order == 'specific_date' && by == 'desc' }" @click="sort('specific_date')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Specific Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <span>Action</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="data in defaultTimes" :key="data.id">
                        <td>
                            {{ data.start_time }}
                        </td>
                        <td>
                            {{ data.end_time }}
                        </td>
                        <td>
                            <span v-if="data.is_default">Default working hours</span>
                            <span v-if="data.is_lunch_time">Default lunch times</span>
                            <span v-if="data.is_dinner_time">Default dinner times</span>
                        </td>
                        <td>
                            <div class="el-actions">
                                <Link class="text-green" :href="`/production-working-hours/${data.id}/edit`"><em class="fa-regular fa-pen-to-square"></em></Link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";

const props = defineProps({
    workingHours: Array,
    filters: Object,
    defaultTimes: Object,
});

// Initialize month and year with defaults if not provided
let month = ref(props.filters.month || new Date().getMonth() + 1);
let year = ref(props.filters.year || new Date().getFullYear());

const currentYear = new Date().getFullYear();
const yearRange = ref([]);
for (let y = currentYear - 5; y <= currentYear + 5; y++) {
    yearRange.value.push(y);
}

const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

const filter = () => {
    Inertia.get(
        "/production-working-hours",
        {
            month: month.value,
            year: year.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

watch([month, year], () => {
    filter();
});
</script>
