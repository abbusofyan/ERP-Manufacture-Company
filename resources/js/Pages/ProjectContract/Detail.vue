<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ contract.customer.name }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/project-contracts">Project Contracts</Link>
                    </li>
                    <li>
                        <span>{{ contract.customer.name }}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">
                    <!-- Information -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem] pb-25">
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div>
                                <p class="mb-10"><span class="text-primary text-bold">Contract:</span><br><span>{{ contract.project_contract_number }}</span></p>
                                <p class="mb-10"><span class="text-primary text-bold">Start Date:</span><br><span>{{ $filters.formatDate(contract.start_duration_date) }}</span></p>
                                <p class="mb-10"><span class="text-primary text-bold">End Date :</span><br><span>{{ $filters.formatDate(contract.end_duration_date) }}</span></p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="el-tag" :class="contract.status_class">
                                    {{ contract.status_text }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                            <span>Customer Info</span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Customer ID</th>
                                    <td>{{ contract.customer.code ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ contract.customer.name ?? '--' }}</td>
                                </tr>
                            </table>
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>POC</th>
                                    <td>{{ contract.customer.poc_first_name ?? '--' }} {{ contract.customer.poc_last_name ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>POC Contact</th>
                                    <td>{{ contract.customer.poc_phone ?? '--' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Vehicle Specifications -->
                    <div class="box">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2</span>
                            <span>Vehicle Specifications</span>
                        </div>
                        <div v-for="vehicle in contract.vehicles" class="grid grid-cols-2 gap-[8rem] border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-25">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Vehicle Number</th>
                                    <td>{{ vehicle.license_plate }}</td>
                                    <th>End User</th>
                                    <td>{{ vehicle.end_user }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ vehicle.type }}</td>
                                    <th>Chassis Salesperson / Contact No.</th>
                                    <td>{{ vehicle.contact_no }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Voltage</th>
                                    <td>{{ vehicle.vehicle_voltage }}</td>
                                    <th>Chassis Delivery</th>
                                    <td>{{ vehicle.chassis_delivery }}</td>
                                </tr>
                                <tr>
                                    <th>Chassis No.</th>
                                    <td>{{ vehicle.chassis_no }}</td>
                                    <th>Refrigeration Serial Number</th>
                                    <td>{{ vehicle.refrigeration_serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Make</th>
                                    <td>{{ vehicle.vehicle_make }}</td>
                                    <th>Other Info</th>
                                    <td>{{ vehicle.other_info }}</td>
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
                            </table>
                        </div>
                    </div>

                    <!-- PIC Info -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">3</span>
                            <span>PIC Info</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>PIC Name</th>
                                    <td>{{ contract.pic_first_name }} {{ contract.pic_last_name }}</td>
                                </tr>
                                <tr>
                                    <th>PIC Number</th>
                                    <td>{{ contract.pic_phone_number }}</td>
                                </tr>
                                <tr>
                                    <th>PIC Email</th>
                                    <td>{{ contract.pic_email }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div v-if="contract.invoices" class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">4</span>
                            <span>Invoices</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr v-for="(invoice, index) in contract.invoices" :key="invoice.id">
                                    <td v-if="index === 0 || new Date(invoice.start_date) <= new Date()">
                                        <a class="text-primary text-bold" :href="invoice.file_url" target="_blank">
                                            {{ $filters.formatDate(invoice.start_date) }} - {{ $filters.formatDate(invoice.end_date) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div v-if="contract.remark" class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                        <p class="text-primary text-bold-500 mb-10">Remark:</p>
                        {{ contract.remark }}
                    </div>

                    <!-- Remarks and Summary -->
                    <div class="box border-0 mb-[5.6rem] pb-[5.6rem]">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <div v-if="contract.signed_image_url" class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                                    <p class="text-primary text-bold-500 mb-10">Photo:</p>
                                    <img class="max-w-650 w-100 rounded-[.5rem] w-px-250 max-w-100" :src="`/storage/${contract.signed_image_url}`" alt="">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="">
                                    <div v-if="contract.sub_total" class="font-bold mb-14">Subtotal:</div>
                                    <div v-if="contract.discount" class="font-bold mb-14">Discount:</div>
                                    <div v-if="contract.tax_amount" class="font-bold mb-14">GST:</div>
                                    <div v-if="contract.total" class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div v-if="contract.total" class="font-bold mb-14">Total:</div>
                                </div>
                                <div class="text-right text-bold">
                                    <div v-if="contract.sub_total" class="mb-14 pl-24 ml-20">${{ contract.sub_total }}</div>
                                    <div v-if="contract.discount" class="mb-14 pl-24 ml-20">${{ contract.discount }}</div>
                                    <div v-if="contract.tax_amount" class="mb-14 pl-24 ml-20">${{ contract.tax_amount }}</div>
                                    <div v-if="contract.total" class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div v-if="contract.total" class="mb-14 pl-24 ml-20">${{ contract.total }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div>
                            <div class="btn-group">
                                <a class="btn btn-purple btn-purple--brounded" :href="`/project-contracts/${contract.id}/download`"><span>Download</span></a>

                                <input type="file"
                                       ref="uploadBtn"
                                       class="form-control-file mb-10 d-none"
                                       id="upload-photo"
                                       :class="{ 'is-invalid': form.errors.signed_image_url }"
                                       @input="event => { form.signed_image_url = event.target.files[0]; submitImage() }"
                                       accept="image/png, image/jpg, image/jpeg">

                                <a v-if="Number(contract.status) === 1" class="btn btn-green cursor-pointer" @click="uploadImage" href="javascript:void(0)">Upload Signed</a>
                                <a v-if="contract.status == 1" class="btn btn-red" @click="voidChange" href="javascript:void(0)"><span>Void</span></a>
                                <a class="btn btn-red--brounded" v-if="contract.status != 3 && contract.status != 4 && contract.can_early_terminate" data-bs-toggle="modal" :data-bs-target="`#earlyTerminationModal`" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/file-x.svg" alt=""></span><span>Early Termination</span></a>
                            </div>
                            <div v-if="form.errors.signed_image_url" class="invalid-feedback d-block">{{ form.errors.signed_image_url }}</div>
                        </div>
                        <div class="btn-group">
                            <Link v-if="Number(contract.status) == 1" class="btn btn-purple" :href="`/project-contracts/${contract.id}/edit`">Edit Contract</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Early Termination Modal-->
        <div class="modal fade" id="earlyTerminationModal" role="dialog" style="overflow:hidden;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-24 pt-36 pb-30">
                    <div class="modal-header">
                        <div class="col-md-12 mt-3 text-center">
                            <img class="d-inline-block mb-[2.6rem]" width="60px" src="../../../assets/img/file-x.svg" alt="">
                            <div class="text-24 text-bold-500 text-purple">
                                Early Termination
                            </div>
                            <p>Need to upload a supporting document, before process can be completed</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Contract End Date<span class="required">*</span></label>
                            <VueDatePicker v-model="form.end_duration_date" :class="{ 'is-invalid': form.errors.end_duration_date }" :min-date="form.start_duration_date" :format="$filters.format" :enable-time-picker="false" placeholder="Select Date"></VueDatePicker>
                            <div v-if="form.errors.end_duration_date" class="invalid-feedback d-block">{{ form.errors.end_duration_date }}</div>
                        </div>
                        <div class="form-group">
                            <label>Supporting document<span class="required">*</span></label>
                            <input type="file" class="form-control-file"
                                   :class="{ 'is-invalid': form.errors.file_url }"
                                   @input="form.file_url = $event.target.files[0];"
                                   accept="application/pdf">
                            <label class="note">Allowed file types: pdf.</label>
                            <div v-if="form.errors.file_url" class="invalid-feedback d-block">{{ form.errors.file_url }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 mb-3 text-center">
                            <a class="btn btn-purple mr-10 disabled" @click="submitEarlyTermination(contract.id)" href="javascript:void(0)">Submit</a>
                            <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Early Termination Modal-->
    </Layout>
</template>

<script setup>
import {ref, computed} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    contract: Object,
    vehicles: Object,
});

const form = useForm({
    contract_id: null,
    signed_image_url: null,
    end_duration_date: null,
    file_url: null,
});

let uploadBtn = ref(null)
let closeModal = ref(null)
const uploadImage = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, do it!'
    }).then((result) => {
        if (result.isConfirmed) {
            uploadBtn.value.click()
        }
    });
};

const submitImage = () => {
    form.post(`/project-contracts/${props.contract.id}/upload-photo`, {
        onSuccess: () => {
            form.reset();
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
};

const submitEarlyTermination = (id) => {
    form.post(`/project-contracts/${id}/early-termination`, {
        onSuccess: () => {
            closeModal.value.click();
            form.reset();
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
};

const voidChange = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, do it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/project-contracts/${props.contract.id}/void`, {}, {
                onSuccess: () => {
                    form.reset();
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
