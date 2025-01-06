<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ process.project_order.vehicle.license_plate }}</div>
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
                        <Link href="/project-schedules">Schedule</Link>
                    </li>
                    <li>
                        <span>{{ process.project_order.vehicle.license_plate }}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">

                    <!-- Vehicle Specifications -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span>Vehicle Details</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Vehicle Number</th>
                                    <td>{{ process.project_order.vehicle.license_plate }}</td>
                                    <th>End User</th>
                                    <td>{{ process.project_order.vehicle.end_user }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ process.project_order.vehicle.type }}</td>
                                    <th>Chassis Salesperson / Contact No.</th>
                                    <td>{{ process.project_order.vehicle.contact_no }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Voltage</th>
                                    <td>{{ process.project_order.vehicle.vehicle_voltage }}</td>
                                    <th>Chassis Delivery</th>
                                    <td>{{ process.project_order.vehicle.chassis_delivery }}</td>
                                </tr>
                                <tr>
                                    <th>Chassis No.</th>
                                    <td>{{ process.project_order.vehicle.chassis_no }}</td>
                                    <th>Refrigeration Serial Number</th>
                                    <td>{{ process.project_order.vehicle.refrigeration_serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Make</th>
                                    <td>{{ process.project_order.vehicle.vehicle_make }}</td>
                                    <th>Other Info</th>
                                    <td>{{ process.project_order.vehicle.other_info }}</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>{{ process.project_order.vehicle.model }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Class</th>
                                    <td>{{ process.project_order.vehicle.vehicle_class }}</td>
                                </tr>
                                <tr>
                                    <th>Type of Plate</th>
                                    <td>{{ process.project_order.vehicle.type_of_plate }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";

const props = defineProps({
    process: Object,
});
</script>
