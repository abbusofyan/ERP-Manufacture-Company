<template>
    <div class="modal fade" id="addProcess" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Add Process
                        </div>
                        <div class="text-12 text-bold-500">
                            Select department and process
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Department</label>
                        <Select2 class="select2-w-100"
                            v-model="selectedDepartment"
                            :options="departments"
                            placeholder="Select Department"
                            @change="fetchProcesses"
                        />
                    </div>
                    <div class="form-group" v-if="selectedDepartment != 'Other' ">
                        <label>Process</label>
                        <Select2 class="select2-w-100"
                            v-model="form.process_id"
                            :options="filteredProcesses"
                            placeholder="Select Process"
                        />
                        <div v-if="form.errors.process_id" class="invalid-feedback d-block">{{ form.errors.process_id }}</div>
                    </div>
                    <div v-if="form.process_id == 'Add new process'">
                        <div class="form-group">
                            <label>Process Name</label>
                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.process_name }" v-model="form.process_name" placeholder="Process name">
                            <div v-if="form.errors.process_name" class="invalid-feedback d-block">{{ form.errors.process_name }}</div>
                        </div>
                        <div class="form-group">
                            <label>Manpower</label>
                            <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.manpower }" v-model="form.manpower" placeholder="Manpower">
                            <div v-if="form.errors.manpower" class="invalid-feedback d-block">{{ form.errors.manpower }}</div>
                        </div>
                        <label>Standard Time :</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.standard_time_hour }" v-model="form.standard_time_hour" placeholder="Houd">
                                    <div v-if="form.errors.standard_time_hour" class="invalid-feedback d-block">{{ form.errors.standard_time_hour }}</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <input class="form-control" type="number" min="0" max="59" :class="{ 'is-invalid': form.errors.standard_time_minute }" v-model="form.standard_time_minute" placeholder="Minute">
                                    <div v-if="form.errors.standard_time_minute" class="invalid-feedback d-block">{{ form.errors.standard_time_minute }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 mb-3 text-center">
                        <a class="btn btn-purple mr-10 disabled" @click="submitForm" href="javascript:void(0)">Submit</a>
                        <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from "sweetalert2";
import {ref, computed, onMounted, watch, inject} from "vue";
import axios from 'axios';

const props = defineProps({
    departments: {
        type: Array,
        default: () => [
            { id: 'Engineering', text: 'Engineering' },
            { id: 'Production', text: 'Production' },
            { id: 'Project', text: 'Project' },
            { id: 'Service', text: 'Service' },
        ]
    }
});

const form = useForm({
    process_id: null,
    process_name: null,
    manpower: null,
    standard_time_hour: null,
    standard_time_minute: null
});

const selectedDepartment = ref(null);
const processes = ref([]);

const filteredProcesses = computed(() => {
    return processes.value;
});

const fetchProcesses = async () => {
    processes.value = [{
        id: 'Add new process',
        text: 'Add new process',
        data: null
    }];
    if (selectedDepartment.value) {
        try {
            const response = await axios.get(`/process/get-by-department/${selectedDepartment.value}`);

            if (response.data.length > 0) {
                response.data.forEach(process => {
                    processes.value.push({
                        id: process.id,
                        text: process.name + ' | ' + selectedDepartment.value,
                        data: process,
                    });
                });
            }
        } catch (error) {
            console.error("Error fetching processes:", error);
            Swal.fire("Error", "Failed to fetch processes.", "error");
        }
    } else {
        processes.value = [];
    }
};

watch(selectedDepartment, () => {
    form.process_id = null;
    form.process_name = null;
    form.standard_time_hour = null;
    form.standard_time_minute = null;
    form.manpower = null;
    fetchProcesses();
});

const parentForm = inject('form')

const submitForm = () => {
    if (!form.process_id) {
        return form.errors.process_id = 'Select process'
    }
    let new_process;
    if (form.process_id == 'Add new process') {
        form.errors.process_id = null

        if (!form.process_name) {
            return form.errors.process_name = 'Input process name'
        }
        new_process = {
            'process_id': null,
            'department': selectedDepartment.value,
            'name': form.process_name,
            'standard_time_hour': form.standard_time_hour ? form.standard_time_hour : '0',
            'standard_time_minute': form.standard_time_minute ? form.standard_time_minute : '0',
            'manpower': form.manpower,
            'started_at': null,
            'ended_at': null,
            'overtime': null,
            'efficiency': null,
            'details': null
        }
    } else {
        const selectedProcess = processes.value.find(process => process.id == form.process_id)
        new_process = {
            'process_id': null,
            'department': selectedProcess.data.type,
            'name': selectedProcess.data.name,
            'standard_time_hour': selectedProcess.data.standard_time_hour ? selectedProcess.data.standard_time_hour : '0',
            'standard_time_minute': selectedProcess.data.standard_time_minute ? selectedProcess.data.standard_time_minute : '0',
            'manpower': selectedProcess.data.manpower,
            'started_at': selectedProcess.data.started_at,
            'ended_at': selectedProcess.data.ended_at,
            'overtime': selectedProcess.data.overtime,
            'efficiency': selectedProcess.data.efficiency,
            'details': selectedProcess.data.detail
        }
    }
    parentForm.processes.push(new_process)
    form.process_id = null;
    form.process_name = null;
    form.standard_time_hour = null;
    form.standard_time_minute = null;
    form.manpower = null;
    form.errors.process_id = null
    form.errors.process_name = null
    closeModal.value.click();
}

const closeModal = ref(null);

</script>
