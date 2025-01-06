<template>
    <div class="box rounded-md shadow-default mt-20 bg-white">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <div class="text-18 font-medium">
                <span>Process</span>
            </div>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search">
                    </div>
                </div>
                <div class="btn-group">
                    <a class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addProcess" ref="openModal" href="javascript:void(0)"><i class="fa fa-add"></i> Add Process</a>
                    <a class="btn btn-purple" href="javascript:void(0)" @click="saveChanges"><i class="fa fa-check"></i> Save Changes</a>
                </div>

                <Modal></Modal>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table select-rows">
                <thead>
                <tr>
                    <th>Process Name</th>
                    <th>Department</th>
                    <th>Standard Time</th>
                    <th>Manpower</th>
                    <th>Total time</th>
                    <th>Overtime</th>
                    <th>Efficiency</th>
                    <th>Started At</th>
                    <th>Ended At</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(process, index) in form.processes" :key="index" @click.stop="toggleDetailRow(index)">
                        <template v-if="process.is_child">
                            <td colSpan="10" class="process-detail">
                                <form v-if="form_detail.groups.length > 0" @submit.prevent="form_detail.post(`/production-order/submit-process-detail/${process.process_id}`)">
                                    <div v-for="(processDetail, detailIndex) in form_detail.groups">
                                        <p><b>{{processDetail.group_name}}</b></p><br>
                                        <div v-for="(question, questionIndex) in processDetail.items" class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label">{{question.question_name}}</label>
                                            <div class="col-sm-8">
                                                <input v-if="question.form_type == 'Text Field'" type="text" class="form-control" v-model="question.value">
                                                <input v-if="question.form_type == 'Checkbox'" type="checkbox" class="custom-checkbox style-1" v-model="question.value">
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-purple" type="submit" @click="form_detail.status = 1" :disabled="form_detail.processing"><i class="fa fa-check"></i> Save Changes</button>
                                </form>
                                <p v-else>NO PROCESS DETAIL</p>
                            </td>
                        </template>
                        <template v-else>
                            <td>{{ process.name }}</td>
                            <td>{{ process.department }}</td>
                            <td>{{ process.standard_time_hour + 'H ' + process.standard_time_minute + 'M' }}</td>
                            <td>{{ process.manpower }}</td>
                            <td>{{ process.overtime }}</td>
                            <td></td>
                            <td>{{ process.efficiency }}</td>
                            <td>{{ process.started_at }}</td>
                            <td>{{ process.ended_at }}</td>
                            <td>
                                <div class="more" v-if="!process.ended_at" @click.stop>
                                    <div class="icon"><img src="../../../../assets/img/more.svg" alt=""></div>
                                    <ul class="more-panel">
                                        <li v-if="!process.started_at">
                                            <a @click.stop="startProcess(process)" href="javascript:void(0)"><span class="icon fa-regular fa-play text-green"></span><span class="text-green">Start Process</span></a>
                                        </li>
                                        <li v-if="process.started_at">
                                            <a @click.stop="stopProcess(process)" href="javascript:void(0)"><span class="icon fa-regular fa-stop text-green"></span><span class="text-green">Finish Process</span></a>
                                        </li>
                                        <li v-if="!process.started_at">
                                            <a @click.stop="destroy(index)" href="javascript:void(0)"><span class="icon fa-regular fa-trash-can text-red"></span><span class="text-red">Delete</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </template>
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
import Modal from "@/Pages/ProductionOrder/Process/AddModal.vue";
import { ref, inject } from 'vue';

const props = defineProps({
    order: Object
});

const form_detail = useForm({
    groups: [
        {
            group_name: '',
            items: [
                {
                    question_name: '',
                    form_type: ''
                }
            ]
        }
    ],
    errors: {}
});


let search = ref(null);

const form = inject('form')

const destroy = (index) => {
    form.processes.splice(index, 1);
}

const selectedRow = ref(null);

const toggleDetailRow = (index) => {
    let parentProcess = form.processes[index]
    if (parentProcess.is_child) {
        return;
    }
    if (selectedRow.value != null) {
        selectedRow.value = null
        form.processes = form.processes.filter(process => !process.is_child);
    } else {
        const groupedData = parentProcess.details.reduce((acc, item) => {
            if (!acc[item.group_name]) {
                acc[item.group_name] = [];
            }

            acc[item.group_name].push({
                process_detail_id: item.id,
                process_id: item.production_order_process_id,
                question_name: item.question,
                form_type: item.form_type,
                value: item.form_type == "Checkbox" && item.value == 1 ? true : item.value
            });
            return acc;
        }, {});

        let data = Object.keys(groupedData).map(groupName => {
            return {
                group_name: groupName != 'null' ? groupName : '',
                items: groupedData[groupName],
            };
        });
        form.processes.splice(index + 1, 0, {
            is_child: true,
            process_id: parentProcess.process_id
        });

        form_detail.groups = Object.keys(groupedData).map(groupName => {
            return {
                group_name: groupName != 'null' ? groupName : '',
                items: groupedData[groupName]
            };
        });

        selectedRow.value = index
    }
};

const startProcess = (process) => {
    Swal.fire({
        title: "Start this process? ",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Start!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/production-order/start-process/${props.order.id}/${process.process_id}`);
        }
    });
};

const stopProcess = (process) => {
    Swal.fire({
        title: "Finish this process? ",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Finish!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/production-order/stop-process/${props.order.id}/${process.process_id}`);
        }
    });
};


const saveChanges = () => {
    Inertia.post(`/production-order/${props.order.id}/update-process`, {
        processes: form.processes
    }, {
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'Process updated.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            form.processes = props.order.processes.map(process => {
                return {
                    'process_id': process.id,
                    'department': process.department,
                    'name': process.name,
                    'standard_time_hour': process.standard_time_hour,
                    'standard_time_minute': process.standard_time_minute,
                    'manpower': process.manpower,
                    'started_at': process.started_at,
                    'ended_at': process.ended_at,
                    'overtime': process.overtime,
                    'efficiency': process.efficiency,
                    'details': process.detail
                };
            });
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error!',
                text: 'There was an issue updating the process. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

</script>

<style scoped>
/* Add your styles here */
.process-detail {
  background-color: #f9f9f9;
  padding: 10px;
  border: 1px solid #ddd;
}
</style>
