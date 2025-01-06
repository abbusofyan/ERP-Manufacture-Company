<template>
    <div class="modal fade" id="addRequirement" role="dialog" style="overflow:hidden;">
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
                    <div class="form-group">
                        <label>Process</label>
                        <Select2 class="select2-w-100"
                            v-model="form.process_id"
                            :options="filteredProcesses"
                            placeholder="Select Process"
                        />
                        <div v-if="form.errors.process_id" class="invalid-feedback d-block">{{ form.errors.process_id }}</div>
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
    process_id: null
});

const selectedDepartment = ref(null);
const processes = ref([]);

const filteredProcesses = computed(() => {
    return processes.value;
});

const fetchProcesses = async () => {
    if (selectedDepartment.value) {
        try {
            const response = await axios.get(`/process/get-by-department/${selectedDepartment.value}`);

            if (Array.isArray(response.data)) {
                processes.value = response.data.map(process => ({
                    id: process.id,
                    text: process.name + ' | ' + selectedDepartment.value,
                    name: process.name,
                    department: process.type,
                    standard_time_hour: process.standard_time_hour,
                    standard_time_minute: process.standard_time_minute,
                    manpower: process.manpower
                }));
            } else {
                processes.value = [{
                    id: response.data.id,
                    text: response.data.name
                }];
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
    fetchProcesses();
});

const parentForm = inject('form')

const submitForm = () => {
    if (form.process_id) {
        const selectedProcess = processes.value.find(process => process.id == form.process_id)
        parentForm.processes.push(selectedProcess)
        form.process_id = null
        form.errors.process_id = null
        closeModal.value.click();
    } else {
        form.errors.process_id = 'Select process'
    }
}

const closeModal = ref(null);

</script>
