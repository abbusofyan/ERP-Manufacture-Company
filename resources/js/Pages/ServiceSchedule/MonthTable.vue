<template>
    <div class="box rounded-md shadow-default bg-white h-100 d-flex flex-column" :class="{'border-[1px] border-solid border-purple' : month.isCurrentMonth}">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
            <div class="text-18 font-medium pb-17 mb-17">{{ month.text }}</div>
        </div>
        <div class="table-responsive flex-fill">
            <table class="table select-rows" style="min-width: unset">
                <thead>
                <tr>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Vehicle No.</span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Status</span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span class="icon"><img src="../../../assets/img/settings.svg" alt="home"></span></div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="services.data.length > 0" v-for="service in services.data" :key="service.id">
                    <td>{{ service.vehicle.license_plate }}</td>
                    <td>
                        <div class="el-tag" :class="service.status_class">
                            {{ service.status_text }}
                        </div>
                    </td>
                    <td>
                        <div class="more">
                            <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                            <ul class="more-panel">
                                <li v-if="permissions.includes('view-requisition')">
                                    <Link :href="`/service-schedules/${service.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View</span></Link>
                                </li>
                                <li v-if="permissions.includes('update-requisition') && (service.status === 'Pending Service' || service.status === 'Service Overdue')">
                                    <a class="text-nowrap" data-bs-toggle="modal" :data-bs-target="`#update-schedule-${service.id}`" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/pencil.svg" alt=""></span><span>Change Appointment Month</span></a>
                                </li>
                            </ul>
                            <Modal :service="service"></Modal>
                        </div>
                    </td>
                </tr>
                <tr v-else>
                    <td class="text-center" colspan="3">Nothing to show.</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="flex flex-column items-center justify-between mt-25 pb-25 px-25">
            <p class="mb-6">Showing {{ services.from }} to {{ services.to }} of {{ services.total }} entries</p>
            <Pagination :links="services.links"/>
        </div>
    </div>
</template>

<script setup>
import Pagination from '../../Components/Pagination.vue';
import {Link, usePage} from "@inertiajs/inertia-vue3";
import Modal from "./UploadForm.vue";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    services: Object,
    month: Object,
});
</script>
