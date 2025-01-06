<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Technician Dashboard</div>
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
                        <span>Technician Dashboard</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="mb-[2.6rem]">
            <div class="stage-tab-2 d-flex flex-wrap gap-16">
                <Link v-for="(tab, index) in stages" class="btn btn-purple" :href="`/project-technicians?stage=${index}`" :class="{'btn-purple--brounded border-0': Number(index) !== Number(stage)}">
                    <span class="icon" :class="{'active':Number(index) === Number(stage)}" v-html="tab.icon"></span>
                    <span class="flex-fill">
                            {{ tab.title }}
                    </span>
                </Link>
            </div>
        </div>

        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <label class="d-flex align-items-center gap-1">Show
                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </label>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Customer Name</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Vehicle No.</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Technician</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Actions</span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="process in processes.data" :key="process.id">
                        <td>{{ process.project_order.customer.name }}</td>
                        <td>{{ process.project_order?.vehicle?.license_plate }}</td>
                        <td>{{ process.project_order?.technician?.name }}</td>
                        <td>
                            <div class="el-tag" :class="process.status_class">
                                {{ process.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-requisition')">
                                        <Link :href="`/project-orders/${process.project_order.id}/processes`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View</span></Link>
                                    </li>
                                    <li v-if="Number(process.status) === 1 || Number(process.status) === 3">
                                        <Link method="POST" :href="`/project-orders/${process.project_order.id}/processes/${process.id}/continue`"><span class="icon"><img src="../../../assets/img/play.svg" alt=""></span><span>Start Process</span></Link>
                                    </li>
                                    <li v-if="Number(process.status) === 2">
                                        <Link method="POST" :href="`/project-orders/${process.project_order.id}/processes/${process.id}/pause`"><span class="icon"><img src="../../../assets/img/player-pause.svg" alt=""></span><span>Pause Process</span></Link>
                                    </li>
                                    <li v-if="Number(process.status) === 2">
                                        <Link v-if="process.stage !== 1" method="POST" :href="`/project-orders/${process.project_order.id}/processes/${process.id}/complete`" class="text-success"><span class="icon"><img src="../../../assets/img/checkbox-green.svg" alt=""></span><span>Complete Process</span></Link>
                                        <a v-else class="text-success" data-bs-toggle="modal" :data-bs-target="`#submitDeliveryDate${process.id}`" ref="openModal" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/checkbox-green.svg" alt=""></span><span>Complete Process</span></a>
                                    </li>
                                </ul>
                                <DeliveryDateForm :projectOrder="process.project_order" :additionId="process.id"></DeliveryDateForm>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ processes.from }} to {{ processes.to }} of {{ processes.total }} entries</p>
                <Pagination :links="processes.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch} from 'vue';
import {usePage, Link, useForm} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Pagination from '../../Components/Pagination.vue';
import Swal from 'sweetalert2';
import debounce from 'lodash.debounce';
import {Inertia} from "@inertiajs/inertia";
import DeliveryDateForm from "./../ProjectOrder/Delivery/DeliveryDateForm.vue";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    processes: Object,
    filters: Object,
    stage: Object,
    stages: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        '/project-technicians',
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

watch(
    search,
    debounce(() => {
        filter();
    }, 500)
);

watch([order, by, paginate], () => {
    filter();
});
</script>
