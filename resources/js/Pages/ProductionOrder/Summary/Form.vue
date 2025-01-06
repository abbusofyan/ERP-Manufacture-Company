<template>
    <div class="box rounded-md shadow-default mt-20 bg-white">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <div class="text-18 font-medium">
                <span>Summary</span>
            </div>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table select-rows">
                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Start date / Time</th>
                    <th>End date / Time</th>
                    <th>Process</th>
                    <th>Hour per person</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(summary, index) in form.summaries" :key="index">
                        <td>{{ summary.technician_name }}</td>
                        <td>{{ summary.start_time }}</td>
                        <td>{{ summary.end_time }}</td>
                        <td>{{ summary.process }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {onMounted} from "vue";
import Swal from "sweetalert2";
import { ref, inject } from 'vue';

const props = defineProps({
    order: Object
});

const form = inject('form')

</script>
