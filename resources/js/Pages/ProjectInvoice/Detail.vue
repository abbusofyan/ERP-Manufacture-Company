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
                                <div class="el-tag" :class="projectInvoice.status_class">
                                    {{ projectInvoice.status }}
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
                    <div class="box border-0 mb-[2.6rem]">
                        <div class="d-flex justify-content-between">
                            <table class="w-100 max-w-[50rem]">
                                <tr>
                                    <td class="pb-24">
                                        <label>Finance Amount</label>
                                    </td>
                                    <td class="pb-24">
                                        <input class="form-control" type="text" v-model="form.finance_amount" :class="{ 'is-invalid': form.errors.finance_amount }" disabled="disabled">
                                        <div v-if="form.errors.finance_amount" class="invalid-feedback d-block">
                                            {{ form.errors.finance_amount }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-24">
                                        <label>Deposit required</label>
                                    </td>
                                    <td class="pb-24">
                                        <input class="form-control" type="text" v-model="form.deposit_required" :class="{ 'is-invalid': form.errors.deposit_required }" disabled="disabled">
                                        <div v-if="form.errors.deposit_required" class="invalid-feedback d-block">
                                            {{ form.errors.deposit_required }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-24">
                                        <label>Rounding</label>
                                    </td>
                                    <td class="pb-24">
                                        <input class="form-control" type="text" v-model="form.rounding" :class="{ 'is-invalid': form.errors.rounding }">
                                        <div v-if="form.errors.rounding" class="invalid-feedback d-block">
                                            {{ form.errors.rounding }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-24">
                                        <label>Freight charges</label>
                                    </td>
                                    <td class="pb-24">
                                        <input class="form-control" type="text" v-model="form.freight_charges" :class="{ 'is-invalid': form.errors.freight_charges }">
                                        <div v-if="form.errors.freight_charges" class="invalid-feedback d-block">
                                            {{ form.errors.freight_charges }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-24">
                                        <label>Customer reference number</label>
                                    </td>
                                    <td class="pb-24">
                                        <input class="form-control" type="text" v-model="form.customer_reference_number" :class="{ 'is-invalid': form.errors.customer_reference_number }">
                                        <div v-if="form.errors.customer_reference_number" class="invalid-feedback d-block">
                                            {{ form.errors.customer_reference_number }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-24">
                                        <label>Attachment</label>
                                    </td>
                                    <td class="pb-24">
                                        <div class="fileContainer">
                                            <label class="fileInputLabel" for="fileInput2">
                                                <input class="fileInput" id="fileInput2"
                                                       type="file"
                                                       :class="{ 'is-invalid': form.errors.attachment }"
                                                       @input="form.attachment = $event.target.files[0]"
                                                       accept="image/*">
                                                <span>Choose File</span>
                                                <span class="browse">Browse</span>
                                            </label>
                                            <div class="mt-12 text-[#82868B]">Allowed file types: pdf.</div>
                                            <div class="files">
                                                <p class="name">
                                                    {{ form.image_url?.attachment }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="form.errors.attachment" class="invalid-feedback d-block">
                                            {{ form.errors.attachment }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-24">
                                        <label>Amend customer address</label>
                                    </td>
                                    <td class="pb-24">
                                        <input class="form-control" type="text" v-model="form.amend_customer_address" :class="{ 'is-invalid': form.errors.amend_customer_address }">
                                        <div v-if="form.errors.amend_customer_address" class="invalid-feedback d-block">
                                            {{ form.errors.amend_customer_address }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-24">
                                        <label>Footer</label>
                                    </td>
                                    <td class="pb-24">
                                        <input class="form-control" type="text" v-model="form.footer" :class="{ 'is-invalid': form.errors.footer }">
                                        <div v-if="form.errors.footer" class="invalid-feedback d-block">
                                            {{ form.errors.footer }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="d-flex justify-content-end">
                                <div class="">
                                    <div class="font-bold mb-14">Subtotal:</div>
                                    <div class="font-bold mb-14">Discount:</div>
                                    <div class="font-bold mb-14">GST:</div>
                                    <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div class="font-bold mb-14">Total:</div>
                                </div>
                                <div class="text-right text-bold">
                                    <div class="mb-14 pl-24 ml-20">${{ projectInvoice.subtotal_amount }}</div>
                                    <div class="mb-14 pl-24 ml-20">${{ projectInvoice.discount_amount }}</div>
                                    <div class="mb-14 pl-24 ml-20">${{ projectInvoice.tax_amount }}</div>
                                    <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div class="mb-14 pl-24 ml-20">${{ projectInvoice.total_amount }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem] border-0 border-t-[1px] border-solid border-[#EBE9F1] pt-20">
                        <div>
                            <div class="btn-group">
                                <a class="btn btn-purple" @click="submitForm" href="javascript:void(0)"><span>Save</span></a>
                                <a class="btn btn-purple" @click="generatePO" href="javascript:void(0)"><span>Generate DO</span></a>
                                <a class="btn btn-purple btn-purple--brounded" v-if="projectInvoice.delivery_order_invoice_url" :href="projectInvoice.delivery_order_invoice_url" target="_blank" download="PO-Invoice"><span>Download DO</span></a>
                                <a target="_blank" :download="projectInvoice.name" class="btn btn-purple btn-purple--brounded" :href="projectInvoice.file_url"><span>Download Invoice</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box rounded-md shadow-default bg-white mt-[2.6rem]">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <div class="text-18 font-medium">Payment History</div>
                <a v-if="projectInvoice.status !== 'Paid'" class="btn btn-purple" data-bs-toggle="modal" :data-bs-target="`#uploadPaid-${projectInvoice.id}`" ref="openModal" href="javascript:void(0)">
                    <span>
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5.25" y="6.75" width="10.5" height="7.5" rx="2" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="10.5" cy="10.5" r="1.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.75 6.75V5.25C12.75 4.42157 12.0784 3.75 11.25 3.75H3.75C2.92157 3.75 2.25 4.42157 2.25 5.25V9.75C2.25 10.5784 2.92157 11.25 3.75 11.25H5.25" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>
                        Paid
                    </span>
                </a>
                <Modal :projectInvoice="projectInvoice"></Modal>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Date Of Payment</th>
                        <th>Mode Of payment</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="paid in paids.data" :key="paid.id">
                        <td class="text-purple">${{ paid.amount }}</td>
                        <td>{{ $filters.formatDateTime(paid.created_at) }}</td>
                        <td>{{ paid.mode_of_payment }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ paids.from }} to {{ paids.to }} of {{ paids.total }} entries</p>
                <Pagination :links="paids.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import {onMounted, ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Pagination from "../../Components/Pagination.vue";
import Modal from "./UploadForm.vue";
import Swal from "sweetalert2";

const props = defineProps({
    projectInvoice: Object,
    paids: Object,
    filters: Object,
});

const form = useForm({
    finance_amount: null,
    deposit_required: null,
    rounding: null,
    freight_charges: null,
    customer_reference_number: null,
    attachment: null,
    amend_customer_address: null,
    footer: null,
});

const projectQuotation = props.projectInvoice.project_quotation;

let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/project-invoices",
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

watch([paginate], () => {
    filter();
});

const submitForm = () => {
    form.put(`/project-invoices/${props.projectInvoice.id}`, {
        preserveScroll: true,
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
    })
};

const generatePO = () => {
    form.post(`/project-invoices/${props.projectInvoice.id}/generate-delivery-order`, {
        preserveScroll: true,
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
    })
};

onMounted(() => {
    form.finance_amount = props.projectInvoice.finance_amount ?? 0;
    form.deposit_required = props.projectInvoice.deposit_required ?? 0;
    form.rounding = props.projectInvoice.rounding ?? 2;
    form.freight_charges = props.projectInvoice.freight_charges;
    form.customer_reference_number = props.projectInvoice.customer_reference_number;
    form.amend_customer_address = props.projectInvoice.amend_customer_address;
    form.footer = props.projectInvoice.footer;
})
</script>
