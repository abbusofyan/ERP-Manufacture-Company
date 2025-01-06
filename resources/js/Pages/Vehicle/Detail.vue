<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ vehicle.license_plate }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/vehicles">Vehicles</Link>
                    </li>
                    <li>
                        <span>{{ vehicle.license_plate }}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">

                    <!-- Vehicle Details -->
                    <div class="box pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span>Vehicle Details</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Vehicle Number</th>
                                    <td>{{ vehicle.license_plate }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ vehicle.type }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Voltage</th>
                                    <td>{{ vehicle.vehicle_voltage }}</td>
                                </tr>
                                <tr>
                                    <th>Chassis No.</th>
                                    <td>{{ vehicle.chassis_no }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Make</th>
                                    <td>{{ vehicle.vehicle_make }}</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>{{ vehicle.model }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Class</th>
                                    <td>{{ vehicle.vehicle_class }}</td>
                                </tr>
                                <tr>
                                    <th>Type of Plate</th>
                                    <td>{{ vehicle.type_of_plate }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name</th>
                                    <td>{{ vehicle.customer.name }}</td>
                                </tr>
                                <tr>
                                    <th>End User</th>
                                    <td>{{ vehicle.end_user }}</td>
                                </tr>
                                <tr>
                                    <th>Chassis Salesperson / Contact No.</th>
                                    <td>{{ vehicle.contact_no }}</td>
                                </tr>
                                <tr>
                                    <th>Chassis Delivery</th>
                                    <td>{{ vehicle.chassis_delivery }}</td>
                                </tr>
                                <tr>
                                    <th>Refrigeration Serial Number</th>
                                    <td>{{ vehicle.refrigeration_serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>Other Info</th>
                                    <td>{{ vehicle.other_info }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="box pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span>Service Orders</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table select-rows">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>No.</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Stage</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Type</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Customer Name</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>PiC No.</span>
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
                                </tr>
                                </thead>
                                <tbody>
                                <template v-for="order in vehicle.service_orders" :key="order.id">
                                    <tr v-if="order.change_of_specs">
                                        <td>{{ order.code }}</td>
                                        <td>{{ order.stage ?? 1 }}</td>
                                        <td>{{ order.service_order_type }}</td>
                                        <td>{{ order.customer.name }}</td>
                                        <td>
                                            <div v-if="order.pic_phone_number">{{ order.pic_country_phone_code ? order.pic_country_phone_code.phone_code : '' }} {{ order.pic_phone_number }}</div>
                                        </td>
                                        <td>{{ order.technician?.name }}</td>
                                        <td>
                                            <div class="el-tag" :class="order.status_class">
                                                {{ order.status_text }}
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";

const props = defineProps({
    vehicle: Object,
});
</script>
