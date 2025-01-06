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
                    <a v-if="projectOrder.stage && ![7, 8, 9].includes(projectOrder.status)" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addProcess" ref="openModal" href="javascript:void(0)">+ Add Process</a>
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
                            <th :class="{ sorting_asc: order == 'efficiency' && by == 'asc', sorting_desc: order == 'efficiency' && by == 'desc' }" @click="sort('name')">
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
                                    <span></span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(process,index) in processes.data" :key="process.id">
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
                                <p v-for="(timelog, index) in process.timelogs" :key="index" class="text-nowrap">
                                    <template v-if="$filters.formatDate(timelog.start_time) === $filters.formatDate(timelog.end_time)">
                                        {{ $filters.formatTime(timelog.start_time) }} - {{ $filters.formatTime(timelog.end_time) }} {{ $filters.formatDate(timelog.start_time) }}: <strong>{{ timelog.type }}</strong>
                                    </template>
                                    <template v-else>
                                        {{ $filters.formatDateTime(timelog.start_time) }} - {{ $filters.formatDateTime(timelog.end_time) }}: <strong>{{ timelog.type }}</strong>
                                    </template>
                                </p>
                            </td>
                            <td>
                                <div v-if="process.status != 4" class="el-actions justify-end"></div>
                                <div v-if="process.status != 4" class="more">
                                    <div class="icon"><img src="../../../../assets/img/more.svg" alt=""></div>
                                    <ul class="more-panel">
                                        <li>
                                            <a data-bs-toggle="modal" :data-bs-target="`#uploadOutsourced-${index}`" ref="openModal" href="javascript:void(0)"><span class="icon"><img src="../../../../assets/img/error.svg" alt=""></span><span class="text-warning">Set Standard Time</span></a>
                                        </li>
                                        <li>
                                            <Link v-if="Number(process.status) === 1 || Number(process.status) === 3" method="POST" :href="`/project-orders/${projectOrder.id}/processes/${process.id}/continue`"><span class="icon"><img src="../../../../assets/img/play.svg" alt=""></span><span>Play</span></Link>
                                        </li>
                                        <li>
                                            <Link v-if="Number(process.status) === 2" method="POST" :href="`/project-orders/${projectOrder.id}/processes/${process.id}/pause`"><span class="icon"><img src="../../../../assets/img/player-pause.svg" alt=""></span><span>Pause</span></Link>
                                        </li>
                                        <li>
                                            <Link v-if="Number(process.status) === 2" method="POST" :href="`/project-orders/${projectOrder.id}/processes/${process.id}/complete`" class="text-success"><span class="icon"><img src="../../../../assets/img/checkbox-green.svg" alt=""></span><span>Completed</span></Link>
                                        </li>
                                        <li>
                                            <a @click="destroy(process.id)" href="javascript:void(0)"><span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span></a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Set standard time modal -->
                                <div class="modal fade" :id="`uploadOutsourced-${index}`" role="dialog" style="overflow:hidden;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content p-24 pt-36 pb-30">
                                            <div class="modal-header">
                                                <div class="col-md-12 mt-3 text-center">
                                                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M40 26.666V39.2712" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M40 51.7969L40 51.9553" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path
                                                            d="M16.6664 63.3337H63.3331C65.5415 63.3182 67.599 62.21 68.8271 60.3745C70.0553 58.539 70.2947 56.2144 69.4664 54.167L45.7998 13.3337C44.6256 11.2116 42.3917 9.89453 39.9664 9.89453C37.5412 9.89453 35.3072 11.2116 34.1331 13.3337L10.4664 54.167C9.65441 56.1662 9.86016 58.4347 11.0186 60.2551C12.177 62.0755 14.1449 63.2226 16.2998 63.3337"
                                                            stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <div class="text-24 text-bold-500">
                                                        Set Standard Time
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Standard Time (hours)</label>
                                                    <input type="number" class="form-control" v-model="form.standard_time" placeholder="0"/>
                                                    <div v-if="form.errors.standard_time" class="invalid-feedback d-block">{{ form.errors.standard_time }}</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 mb-3 text-center">
                                                    <a class="btn btn-purple mr-10" @click="submitForm(process.id,`uploadOutsourced-${index}`)" href="javascript:void(0)">Submit</a>
                                                    <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" href="javascript:void(0)">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Set standard time modal -->
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
        </div>
    </div>
</template>

<script setup>
import {usePage, Link, useForm} from '@inertiajs/inertia-vue3';
import Pagination from "../../../Components/Pagination.vue";
import Tab from "../Tab.vue";
import {ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const props = defineProps({
    projectOrder: Object,
    filters: Object,
    processes: Object,
});

const form = useForm({
    standard_time: null,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        `/project-orders/${props.projectOrder.id}`,
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
    order.value = field;

    if (by.value === 'asc') {
        by.value = 'desc';
    } else {
        by.value = 'asc';
    }
};

const submitForm = (process_id, modal_id) => {
    form.post(`/project-orders/${props.projectOrder.id}/processes/${process_id}/set-standard-time`, {
        onSuccess: () => {
            const modalElement = document.getElementById(modal_id);
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();

            form.reset();
            Swal.fire({
                title: 'Success!',
                text: "Your changes have been saved!",
                icon: 'success',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                cancelButtonColor: '#626CC6',
            });
        }
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
            Inertia.delete(`/project-orders/${props.projectOrder.id}/processes/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success !',
                        text: "Your changes have been saved!",
                        icon: 'success',
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'OK',
                        cancelButtonColor: '#626CC6',
                    });
                }
            });
        }
    });
};
</script>
