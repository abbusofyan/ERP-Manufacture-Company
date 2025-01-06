<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ projectQuotation.code }}</div>
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
                        <Link href="/project-quotations">Quotation</Link>
                    </li>
                    <li>
                        <span>{{ projectQuotation.code }}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">
                    <!-- Quotation Information -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem] pb-25">
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div>
                                <p class="mb-10"><span class="text-primary text-bold">Quote:</span><br><span>{{ projectQuotation.code }}</span></p>
                                <p class="mb-10"><span class="text-primary text-bold">Date:</span><br><span>{{ $filters.formatDateTime(projectQuotation.created_at) }}</span></p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="el-tag" :class="projectQuotation.status_class">
                                    {{ projectQuotation.status_text }}
                                </div>
                            </div>
                        </div>
                        <div class="btn-group justify-content-end align-items-start">
                            <div class="btn-group flex-column align-items-start">
                                <Link v-if="permissions.includes('view-requisition') && projectQuotation.status == 2" class="btn btn-purple btn-purple--brounded" :href="`/project-quotations/${projectQuotation.id}/generate-proforma-invoice`"><span class="icon"><img src="../../../assets/img/download.svg" alt=""></span><span>Generate Proforma Invoice</span></Link>
                                <a target="_blank" v-if="permissions.includes('view-requisition') && projectQuotation.status == 2 && projectQuotation.proforma_invoice" class="btn btn-purple btn-purple--brounded" :href="`${ projectQuotation.proforma_invoice.file_url }`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Proforma Invoice</span></a>
                            </div>
                            <Link
                                v-if="permissions.includes('view-requisition') && projectQuotation.status == 2"
                                :class="['btn', 'btn-purple', 'btn-purple--brounded', { 'disabled': !projectQuotation.vehicle_parts.some(part => part.project_invoice_id === null) }]"
                                :href="projectQuotation.vehicle_parts.some(part => part.project_invoice_id === null) ? `/project-quotations/${projectQuotation.id}/generate-invoice` : '#'"
                                :disabled="!projectQuotation.vehicle_parts.some(part => part.project_invoice_id === null)"
                            >
                                <span class="icon"><img src="../../../assets/img/download.svg" alt=""></span>
                                <span>Generate Invoice</span>
                            </Link>
                        </div>
                        <div v-if="projectQuotation.invoices.length > 0">
                            <p class="mb-10"><span class="text-primary text-bold">Invoices:</span><br></p>
                            <ul style="padding-left: 20px">
                                <li v-for="invoice in projectQuotation.invoices">
                                    <a class="text-primary text-bold" target="_blank" :href="`/project-invoices/${invoice.id}`">{{ invoice.name?.replace('.pdf', '') }}</a>
                                </li>
                            </ul>
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
                                    <td>{{ projectQuotation.customer.code ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ projectQuotation.customer.name ?? '--' }}</td>
                                </tr>
                            </table>
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>POC</th>
                                    <td>{{ projectQuotation.customer.poc_first_name ?? '--' }} {{ projectQuotation.customer.poc_last_name ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>POC Contact</th>
                                    <td>{{ projectQuotation.customer.poc_phone ?? '--' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Vehicle Specifications -->
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2</span>
                            <span>Vehicle Specifications</span>
                        </div>
                        <div class="grid grid-cols-2 gap-[8rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Vehicle Number</th>
                                    <td>{{ projectQuotation.vehicle.license_plate }}</td>
                                    <th>End User</th>
                                    <td>{{ projectQuotation.vehicle.end_user }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ projectQuotation.vehicle.type }}</td>
                                    <th>Chassis Salesperson / Contact No.</th>
                                    <td>{{ projectQuotation.vehicle.contact_no }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Voltage</th>
                                    <td>{{ projectQuotation.vehicle.vehicle_voltage }}</td>
                                    <th>Chassis Delivery</th>
                                    <td>{{ projectQuotation.vehicle.chassis_delivery }}</td>
                                </tr>
                                <tr>
                                    <th>Chassis No.</th>
                                    <td>{{ projectQuotation.vehicle.chassis_no }}</td>
                                    <th>Refrigeration Serial Number</th>
                                    <td>{{ projectQuotation.vehicle.refrigeration_serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Make</th>
                                    <td>{{ projectQuotation.vehicle.vehicle_make }}</td>
                                    <th>Other Info</th>
                                    <td>{{ projectQuotation.vehicle.other_info }}</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>{{ projectQuotation.vehicle.model }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Class</th>
                                    <td>{{ projectQuotation.vehicle.vehicle_class }}</td>
                                </tr>
                                <tr>
                                    <th>Type of Plate</th>
                                    <td>{{ projectQuotation.vehicle.type_of_plate }}</td>
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
                                    <td>{{ projectQuotation.pic_first_name }} {{ projectQuotation.pic_last_name }}</td>
                                </tr>
                                <tr>
                                    <th>PIC Number</th>
                                    <td>{{ projectQuotation.pic_phone_number }}</td>
                                </tr>
                                <tr>
                                    <th>PIC Email</th>
                                    <td>{{ projectQuotation.pic_email }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Vehicle Parts -->
                    <div class="pb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">4</span>
                            <span>Vehicle Parts</span>
                        </div>
                        <div class="table-responsive pb-0" style="min-height: unset">
                            <table class="table select-rows">
                                <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Quantity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="part in projectQuotation.vehicle_parts" :key="part.id">
                                    <td>{{ part.name }}</td>
                                    <td>{{ part.quantity }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                        <p class="text-primary text-bold-500 mb-10">Remark:</p>
                        {{ projectQuotation.remark ?? '--' }}
                    </div>

                    <div class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                        <p class="text-primary text-bold-500 mb-10">Footer:</p>
                        {{ projectQuotation.footer ?? '--' }}
                    </div>

                    <!-- Remarks and Summary -->
                    <div class="box border-0 mb-[5.6rem] pb-[5.6rem]">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <div v-if="projectQuotation.signed_image_url" class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                                    <p class="text-primary text-bold-500 mb-10">Signed image:</p>
                                    <a @click="toggler_signed = !toggler_signed" href="javascript:void(0)">
                                        <img class="max-w-650 w-100 rounded-[.5rem] w-px-250 max-w-100" :src="`/storage/${projectQuotation.signed_image_url}`" alt="">
                                    </a>
                                    <FsLightbox :toggler="toggler_signed" :sources="[`/storage/${projectQuotation.signed_image_url}`]" />
                                </div>
                                <div v-if="projectQuotation.image_url" class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                                    <p class="text-primary text-bold-500 mb-10">Image:</p>
                                    <a @click="toggler_image = !toggler_image" href="javascript:void(0)">
                                        <img class="max-w-650 w-100 rounded-[.5rem] w-px-250 max-w-100" :src="`/storage/${projectQuotation.image_url}`" alt="">
                                    </a>

                                    <FsLightbox :toggler="toggler_image" :sources="[`/storage/${projectQuotation.image_url}`]" />
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="">
                                    <div class="font-bold mb-14">Subtotal:</div>
                                    <div class="font-bold mb-14">Discount:</div>
                                    <div class="font-bold mb-14">GST:</div>
                                    <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div class="font-bold mb-14">Total:</div>
                                </div>
                                <div class="text-right text-bold">
                                    <div class="mb-14 pl-24 ml-20">${{ projectQuotation.sub_total }}</div>
                                    <div class="mb-14 pl-24 ml-20">${{ projectQuotation.discount }}</div>
                                    <div class="mb-14 pl-24 ml-20">${{ projectQuotation.gst_total }}</div>
                                    <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div class="mb-14 pl-24 ml-20">${{ projectQuotation.total }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div>
                            <div class="btn-group">
                                <input type="file"
                                       ref="uploadBtn"
                                       class="form-control-file mb-10 d-none"
                                       id="upload-photo"
                                       :class="{ 'is-invalid': form.errors.signed_image_url }"
                                       @input="event => { form.signed_image_url = event.target.files[0]; submitImage() }"
                                       accept="image/png, image/jpg, image/jpeg">
                                <a v-if="Number(projectQuotation.status) === 1" class="btn btn-green cursor-pointer" @click="uploadImage" href="javascript:void(0)">Upload Signed</a>

                                <a v-if="projectQuotation.status !== 3" class="btn btn-red" @click="voidChange" href="javascript:void(0)"><span>Void</span></a>
                            </div>
                            <div v-if="form.errors.signed_image_url" class="invalid-feedback d-block">{{ form.errors.signed_image_url }}</div>
                        </div>
                        <div class="btn-group">
                            <Link v-if="projectQuotation.status !== 3 && projectQuotation.invoices && projectQuotation.invoices.length == 0" class="btn btn-purple" :href="`/project-quotations/${projectQuotation.id}/edit`">Edit Quotation</Link>
                            <div class="btn-group"><a class="btn btn-red btn-red--brounded" href="javascript:void(0)" @click="removeQuotation"><em class="fa-regular fa-trash"></em><span>Remove</span></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, computed} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    projectQuotation: Object,
});

const form = useForm({
    signed_image_url: null,
});

let uploadBtn = ref(null)
let toggler_image = ref(false)
let toggler_signed = ref(false)

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
    form.post(`/project-quotations/${props.projectQuotation.id}/upload-photo`, {
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
            Inertia.post(`/project-quotations/${props.projectQuotation.id}/void`, {}, {
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

const removeQuotation = () => {
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
            Inertia.delete(`/project-quotations/${props.projectQuotation.id}`, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success !',
                        text: "Your changes has been saved",
                        icon: 'success',
                        showConfirmButton: false,
                        denyButtonText: 'OK',
                        denyButtonColor: '#626CC6',
                    })
                }
            });
        }
    });
};
</script>
