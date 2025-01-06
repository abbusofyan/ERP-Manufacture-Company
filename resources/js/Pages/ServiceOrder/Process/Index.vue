<template>
    <div class="box pb-[5.6rem] rounded-md shadow-default bg-white mt-[2.6rem]">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <label class="d-flex align-items-center gap-1 text-18 text-bold-500">
                Process
            </label>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="form">
                        <input type="search" placeholder="Search" v-model="search">
                    </div>
                </div>
                <div class="btn-group">
                    <a v-if="![7, 8, 9].includes(serviceOrder.status)" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addProcess" href="javascript:void(0)">+ Add Process</a>
                </div>
            </div>
        </div>
        <div class="form-wrap">
            <div class="boxes">
                <div class="table-responsive">
                    <table class="table select-rows">
                        <thead>
                        <tr>
                            <th :class="{ sorting_asc: order == 'name' && by == 'asc', sorting_desc: order == 'name' && by == 'desc' }" @click="sort('name')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Process Name</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'standard_time' && by == 'asc', sorting_desc: order == 'standard_time' && by == 'desc' }" @click="sort('standard_time')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Standard Time</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'total_time' && by == 'asc', sorting_desc: order == 'total_time' && by == 'desc' }" @click="sort('total_time')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Actual Time</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'manpower' && by == 'asc', sorting_desc: order == 'manpower' && by == 'desc' }" @click="sort('manpower')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Manpower</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'overtime' && by == 'asc', sorting_desc: order == 'overtime' && by == 'desc' }" @click="sort('overtime')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Overtime</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'efficiency' && by == 'asc', sorting_desc: order == 'efficiency' && by == 'desc' }" @click="sort('efficiency')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Efficiency</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th :class="{ sorting_asc: order == 'status' && by == 'asc', sorting_desc: order == 'status' && by == 'desc' }" @click="sort('status')">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Status</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Timeline</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1">
                                    <span>Tech</span>
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
                        <tr v-for="(process, index) in processes.data" :key="process.id">
                            <td>{{ process.name }}</td>
                            <td>{{ process.standard_time !== null ? `${process.standard_time} hours` : '' }}</td>
                            <td>{{ process.total_time !== null ? `${process.total_time} hours` : '' }}</td>
                            <td>{{ process.manpower !== null ? `${process.manpower}` : '' }}</td>
                            <td>{{ process.overtime !== null ? `${process.overtime} hours` : '' }}</td>
                            <td>{{ process.efficiency !== null ? `${process.efficiency}%` : '' }}</td>
                            <td>
                                <div class="el-tag" :class="process.status_class">
                                    {{ process.status_text }}
                                </div>
                            </td>
                            <td>
                                <p v-for="(timelog, idx) in process.timelogs" :key="idx" class="text-nowrap">
                                    <template v-if="$filters.formatDate(timelog.start_time) === $filters.formatDate(timelog.end_time)">
                                        {{ $filters.formatTime(timelog.start_time) }} - {{ $filters.formatTime(timelog.end_time) }} {{ $filters.formatDate(timelog.start_time) }}: <strong>{{ timelog.type }}</strong>
                                    </template>
                                    <template v-else>
                                        {{ $filters.formatDateTime(timelog.start_time) }} - {{ $filters.formatDateTime(timelog.end_time) }}: <strong>{{ timelog.type }}</strong>
                                    </template>
                                </p>
                            </td>
                            <td>{{ process.technician?.name }}</td>
                            <td>
                                <div v-if="process.status != 4" class="more">
                                    <div class="icon"><img src="../../../../assets/img/more.svg" alt=""></div>
                                    <ul class="more-panel">
                                        <!--<li>
                                            <a data-bs-toggle="modal" :data-bs-target="`#setStandardTimeModal-${index}`" href="javascript:void(0)">
                                                <span class="icon"><img src="../../../../assets/img/error.svg" alt=""></span><span class="text-warning">Set Standard Time</span>
                                            </a>
                                        </li>-->
                                        <li>
                                            <Link v-if="process.technician_id != null && (Number(process.status) === 1 || Number(process.status) === 3)" method="POST" :href="`/service-orders/${serviceOrder.id}/processes/${process.id}/continue`">
                                                <span class="icon"><img src="../../../../assets/img/play.svg" alt=""></span><span>Start</span>
                                            </Link>
                                            <a v-else href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#qrScanModalIndex" @click="processModel = process;">
                                                <span class="icon"><img src="../../../../assets/img/play.svg" alt=""></span><span>Start</span>
                                            </a>
                                        </li>
                                        <li>
                                            <Link v-if="Number(process.status) === 2" method="POST" :href="`/service-orders/${serviceOrder.id}/processes/${process.id}/pause`">
                                                <span class="icon"><img src="../../../../assets/img/player-pause.svg" alt=""></span><span>Pause</span>
                                            </Link>
                                        </li>
                                        <li>
                                            <a v-if="Number(serviceOrder.stage) == 1 && serviceOrder.delivery_date == null" class="text-success" data-bs-toggle="modal" data-bs-target="#submitDeliveryDate" href="javascript:void(0)">
                                                <span class="icon"><img src="../../../../assets/img/checkbox-green.svg" alt=""></span><span>Completed</span>
                                            </a>
                                            <Link v-else-if="Number(process.status) === 2" method="POST" :href="`/service-orders/${serviceOrder.id}/processes/${process.id}/complete`" class="text-success">
                                                <span class="icon"><img src="../../../../assets/img/checkbox-green.svg" alt=""></span><span>Completed</span>
                                            </Link>
                                        </li>
                                        <li>
                                            <a @click="destroy(process.id)" href="javascript:void(0)">
                                                <span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Set Standard Time Modal -->
                                <SetStandardTimeModal :process="process" :index="index" :formErrors="form.errors" @submit="submitStandardTime" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-if="form.errors.user_id" class="invalid-feedback d-block">{{ form.errors.user_id }}</div>
                </div>
                <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                    <p>Showing {{ processes.from }} to {{ processes.to }} of {{ processes.total }} entries</p>
                    <Pagination :links="processes.links"/>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Date Modal -->
    <DeliveryDateForm :serviceOrder="serviceOrder"></DeliveryDateForm>

    <!-- QR Code Scan Modal -->
    <QrScanModal
        :action="'continue'"
        :process="processModel"
        :processId="processModel?.id"
        :serviceOrderId="serviceOrder.id"
        :modalId="'qrScanModalIndex'"/>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
import Pagination from '../../../Components/Pagination.vue';
import DeliveryDateForm from '@/Pages/ServiceOrder/Delivery/DeliveryDateForm.vue';
import SetStandardTimeModal from './SetStandardTimeModal.vue';
import QrScanModal from './QrScanModal.vue';
import debounce from 'lodash.debounce';
import Swal from 'sweetalert2';
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    serviceOrder: Object,
    filters: Object,
    processes: Object,
});

const form = useForm({
    standard_time: null,
    user_id: null,
});

const search = ref(props.filters.search || '');
const order = ref(props.filters.order || 'name');
const by = ref(props.filters.by || 'asc');
const paginate = ref(props.filters.paginate || 10);

const filter = () => {
    Inertia.get(
        `/service-orders/${props.serviceOrder.id}`,
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

const sort = (field) => {
    if (order.value === field) {
        by.value = by.value === 'asc' ? 'desc' : 'asc';
    } else {
        order.value = field;
        by.value = 'asc';
    }
};

const submitStandardTime = (processId, standardTime, modalId) => {
    form.standard_time = standardTime;

    form.post(`/service-orders/${props.serviceOrder.id}/processes/${processId}/set-standard-time`, {
        onSuccess: () => {
            const modalElement = document.getElementById(modalId);
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) modalInstance.hide();

            form.reset('standard_time');
            Swal.fire({
                title: 'Success!',
                text: 'Your changes have been saved!',
                icon: 'success',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                cancelButtonColor: '#626CC6',
            });
        },
    });
};

const destroy = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea5455',
        cancelButtonColor: '#009CDB',
        confirmButtonText: 'Yes, Proceed!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/service-orders/${props.serviceOrder.id}/processes/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success !',
                        text: 'Your changes have been saved!',
                        icon: 'success',
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'OK',
                        cancelButtonColor: '#626CC6',
                    });
                },
            });
        }
    });
};

const processModel = ref(null);
</script>
