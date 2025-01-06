<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ appointment.customer?.name }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/project-appointments">Project Order</Link>
                    </li>
                    <li>
                        <Link href="/project-appointments">Project Appointment</Link>
                    </li>
                    <li>
                        <span>{{ appointment.cmp_number }}</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">
                    <!-- Basic Information -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                            <span>Basic Information</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>CMP Number</th>
                                    <td>{{ appointment.cmp_number }}</td>
                                </tr>
                                <tr>
                                    <th>Customer</th>
                                    <td>{{ appointment.customer?.name }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <td>{{ appointment?.vehicle?.license_plate }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Appointment</th>
                                    <td>{{ formatDate(appointment.date_of_appointment) }}</td>
                                </tr>
                                <tr>
                                    <th>PIC Name</th>
                                    <td>{{ appointment.pic_first_name }} {{ appointment.pic_last_name }}</td>
                                </tr>
                                <tr>
                                    <th>PIC Number</th>
                                    <td>{{ appointment.pic_phone_number }}</td>
                                </tr>
                                <tr>
                                    <th>PIC Email</th>
                                    <td>{{ appointment.pic_email }}</td>
                                </tr>
                                <tr>
                                    <th>Remarks</th>
                                    <td>{{ appointment.remark }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2</span>
                            <span>Customer Details</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Customer ID</th>
                                    <td>{{ appointment.customer?.code }}</td>
                                    <th>Project</th>
                                    <td>{{ appointment.customer?.project_text }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ appointment.customer?.name }}</td>
                                    <th>UEN</th>
                                    <td>{{ appointment.customer?.uen }}</td>
                                </tr>
                                <tr>
                                    <th>Office Number</th>
                                    <td>{{ appointment.customer?.phone }}</td>
                                    <th>Customer Type</th>
                                    <td>{{ appointment.customer?.customer_type_text }}</td>
                                </tr>
                                <tr>
                                    <th>Account Manager</th>
                                    <td>{{ appointment.customer?.account_manager_text }}</td>
                                    <th>Credit Term</th>
                                    <td>{{ appointment.customer?.credit_term_text }}</td>
                                </tr>
                                <tr>
                                    <th>Project</th>
                                    <td>{{ appointment.customer?.project_text }}</td>
                                    <th>Credit Limit</th>
                                    <td>{{ appointment.customer?.credit_limit }}</td>
                                </tr>
                                <tr>
                                    <th>Refrigeration Sales</th>
                                    <td>{{ appointment.customer?.refrigeration_sales_text }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">3</span>
                            <span>Vehicle Details</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Vehicle Number</th>
                                    <td>{{ appointment?.vehicle?.license_plate }}</td>
                                    <th>Customer Name</th>
                                    <td>{{ appointment?.vehicle?.customer?.name }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ appointment?.vehicle?.type }}</td>
                                    <th>End User</th>
                                    <td>{{ appointment?.vehicle?.end_user }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Voltage</th>
                                    <td>{{ appointment?.vehicle?.vehicle_voltage }}</td>
                                    <th>Chassis Salesperson / Contact No.</th>
                                    <td>{{ appointment?.vehicle?.contact_no }}</td>
                                </tr>
                                <tr>
                                    <th>Chassis No.</th>
                                    <td>{{ appointment?.vehicle?.chassis_no }}</td>
                                    <th>Chassis Delivery</th>
                                    <td>{{ appointment?.vehicle?.chassis_delivery }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Make</th>
                                    <td>{{ appointment?.vehicle?.vehicle_make }}</td>
                                    <th>Refrigeration Serial Number</th>
                                    <td>{{ appointment?.vehicle?.refrigeration_serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>{{ appointment?.vehicle?.model }}</td>
                                    <th>Other Info</th>
                                    <td>{{ appointment?.vehicle?.other_info }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Class</th>
                                    <td>{{ appointment?.vehicle?.vehicle_class }}</td>
                                </tr>
                                <tr>
                                    <th>Type of Plate</th>
                                    <td>{{ appointment?.vehicle?.type_of_plate }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>


                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group">
                            <Link class="btn btn-purple" href="/project-appointments"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                            <Link v-if="permissions.includes('update-project_appointment') && Number(appointment.status) === 1 && appointment.deleted_at == null" class="btn btn-light btn-light--brounded" :href="`/project-appointments/${appointment.id}/edit`">Edit</Link>
                        </div>
                        <div v-if="permissions.includes('delete-project_appointment') && ![3, 4].includes(appointment.status)" class="btn-group"><a class="btn btn-red btn-red--brounded" href="javascript:void(0)" @click="removeAppointment"><em class="fa-regular fa-xmark"></em><span>Void</span></a></div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    appointment: Object,
});

const formatDate = (date) => {
    const options = {year: 'numeric', month: 'long', day: 'numeric'};
    return new Date(date).toLocaleDateString(undefined, options);
};

const removeAppointment = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/project-appointments/${props.appointment.id}`, {
                onSuccess: () => {
                    Swal.fire({
                            title: 'Success !',
                            text: "Your changes has been saved",
                            icon: 'success',
                            showConfirmButton: false,
                            denyButtonText: 'OK',
                            denyButtonColor: '#626CC6',
                        }
                    )
                }
            });
        }
    });
};
</script>
