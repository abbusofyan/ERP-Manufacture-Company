<template>
    <Layout>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">

            <div class="d-flex justify-between flex-wrap mb-17">
                <div class="left">
                    <div class="logo mb-[2.6rem]">
                        <svg width="180" height="56" viewBox="0 0 180 56" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect width="180" height="55.08" fill="url(#pattern0)"/>
                            <defs>
                                <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_2123_279785" transform="scale(0.002 0.00653595)"/>
                                </pattern>
                                <image id="image0_2123_279785" width="500" height="153"
                                       xlink:href="data:image/png;base64,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"/>
                            </defs>
                        </svg>
                    </div>
                    <p>
                        82 & 84 Joo Koon Circle<br>
                        Jurong Industrial Estate,<br>
                        Singapore 629101<br>
                        Tel : (65) 6897 9339<br>
                        Fax: (65) 6897 7337
                    </p>
                </div>
                <div class="right">
                    <div class="text-right mb-[2.6rem]">
                        <a class="btn btn-purple" href=""><i class="fa fa-file-text"></i>View PDF</a>
                    </div>
                    <table class="table-1-el w-full">
                        <tr>
                            <td class="text-18 pb-10" style="font-weight: 500">Number #{{ refrigeration_sale.code }}</td>
                        </tr>
                        <tr>
                            <td>Date: {{ $filters.formatDateTime(refrigeration_sale.created_at) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="text-18 font-medium pt-20 mb-[2.6rem] border-0 border-t-[1px] border-solid border-[#EBE9F1]">
                <span class="text-15 w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">!</span>
                <span>Quotation Information</span>
            </div>

            <div class="mb-[5.6rem]">
                <div class="text-primary text-bold mb-10">Client Name</div>
                <p class="text-bold">{{ refrigeration_sale.customer.name }}</p>
                <p>{{ refrigeration_sale.customer.address_location }}</p>
                <p>{{ refrigeration_sale.customer.address_postal_code }}</p>
                <p>{{ refrigeration_sale.customer.poc_email }}</p>
            </div>

            <div class="mb-[5.6rem]">
                <div class="text-primary text-bold mb-10">Shipment</div>
                <p>{{ refrigeration_sale.shipment_text }}</p>
            </div>

            <div class="mb-[5.6rem]">
                <div class="text-primary text-bold mb-10">Status</div>
                <p>{{ refrigeration_sale.status_text }}</p>
            </div>

            <div class="header-wrapper mb-[2.6rem] pt-36 pb-10" v-for="(header, index) in refrigeration_sale.headers">
                <div class="mb-[5.6rem]">
                    <div class="text-18 font-medium px-24 pt-20 pb-24 bg-primary text-white border-3 rounded-[.5rem] d-flex align-items-center">
                        <span class="text-15 w-24 h-24 inline-flex items-center justify-center border-[2px] rounded-[50%] mr-8 border-solid">{{ index + 1 }}</span>
                        <span>
                        {{ header.header_name }}
                    </span>
                    </div>
                    <div class="header-content bg-[#F8F8F8] rounded-[.5rem] px-20 pt-20 pb-24">
                        <div class="row mb-[4rem]">
                            <div v-if="header.quantity || header.unit_price || header.discount" class="col-lg-6">
                                <div class="row text-bold-500">
                                    <div class="col-12">
                                        <div class="text-bold mb-10">HEADER DETAILS</div>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Quantity</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.quantity ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">U/Price</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.unit_price ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Discount</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.discount ?? '--' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row text-bold-500">
                            <div class="col-12">
                                <div class="text-bold mb-10">Vehicle Specifications</div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Vehicle Number</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_license_plate ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Chassis No.</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_chassis_no ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Vehicle Voltage</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_voltage ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Vehicle Make</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_make ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Model</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_model ?? '--' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Vehicle Class</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_class ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Type of Plate</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_type_of_plate ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Vehicle Mileage</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_mileage ?? '--' }}</p>
                                    </div>

                                    <div class="col-md-4 mb-6">
                                        <p class="text-primary">Running Hours</p>
                                    </div>
                                    <div class="col-md-6 mb-10">
                                        <p>{{ header.vehicle_running_hours ?? '--' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <template v-for="(type, index_type) in header.types">
                    <div class="mb-[5.6rem]" v-if="type.items.some(item => item.product)">
                        <div class="text-uppercase text-bold mb-[2.6rem]">
                            {{ type.box }}
                        </div>

                        <div :class="{'mb-[2.6rem]' : type.description}">
                            <div class="table-responsive">
                                <table class="table select-rows">
                                    <thead>
                                    <tr>
                                        <th>
                                            Type
                                        </th>
                                        <th>
                                            ID
                                        </th>
                                        <th>
                                            Item name
                                        </th>
                                        <th>
                                            Quantity
                                        </th>
                                        <th>
                                            U/Price
                                        </th>
                                        <th>
                                            Discount
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(item, index_item) in type.items">
                                        <tr v-if="item.product" :key="index_item">
                                            <td>
                                                {{ item.type }}
                                            </td>
                                            <td>
                                                <div v-if="item.product" class="d">
                                                    {{ item.product.sku }}
                                                </div>
                                            </td>
                                            <td>
                                                <div v-if="item.product" class="d">
                                                    {{ item.product.name }}
                                                </div>
                                            </td>
                                            <td>
                                                {{ item.quantity ?? header.quantity }}
                                            </td>
                                            <td>
                                                {{ item.unit_price ?? header.unit_price }}
                                            </td>
                                            <td>
                                                {{ item.discount ?? header.discount }}
                                            </td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>
                            </div>

                            <div v-if="type.description" class="description">
                                <div class="text-primary mb-10 text-bold">
                                    Description:
                                </div>
                                {{ type.description }}
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="row justify-end mt-[3.6rem]">
                <div class="col-lg-9">
                </div>
                <div class="col-lg-3">
                    <table class="table-1-el w-full">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="pl-24 text-right">
                                <strong>
                                    ${{ refrigeration_sale.sub_total }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Discount:</td>
                            <td class="pl-24 text-right">
                                <strong>
                                    ${{ refrigeration_sale.discount }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>GST ({{ gst_rate }}%):</td>
                            <td class="pl-24 text-right">
                                <strong>
                                    ${{ refrigeration_sale.gst }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1]"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td class="pl-24 text-right">
                                <strong>
                                    ${{ refrigeration_sale.total }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="border-0 border-t-[1px] border-solid border-[#EBE9F1] mt-[5.6rem] mb-[2.6rem]"></div>

            <div v-if="refrigeration_sale.photo" class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                <p class="text-primary text-bold-500 mb-10">Photo:</p>
                <img class="max-w-650 w-100 rounded-[.5rem]" :src="`/storage/${refrigeration_sale.photo}`" alt="">
            </div>
            <div v-if="refrigeration_sale.note" class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                <p class="text-primary text-bold-500 mb-10">Note:</p>
                {{ refrigeration_sale.note }}
            </div>
            <div v-if="refrigeration_sale.footer" class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem]">
                <p class="text-primary text-bold-500 mb-10">Footer:</p>
                {{ refrigeration_sale.footer }}
            </div>

            <div class="d-flex justify-between flex-wrap gap-[3rem]">
                <div class="btn-group mt-[5.6rem]">
                    <Link class="btn btn-purple" :href="`/${shipment}/refrigeration-sales`"><i class="fa fa-chevron-left text-11"></i>Back</Link>
                    <Link v-if="permissions.includes('update-product') && ![3,4,5].includes(refrigeration_sale.status)" class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales/${refrigeration_sale.id}/edit`">Edit</Link>
                    <a v-if="permissions.includes('delete-product') && ![3,4,5].includes(refrigeration_sale.status)" class="btn btn-red btn-red--brounded" @click="destroy" href="javascript:void(0)"><i>&times;</i>Void</a>
                </div>
                <div class="btn-group mt-[5.6rem]">
                    <input type="file"
                           class="form-control-file mb-10 d-none"
                           id="upload-photo"
                           :class="{ 'is-invalid': form.errors.photo }"
                           @input="form.photo = $event.target.files[0]; form.post(`/${shipment}/refrigeration-sales/${refrigeration_sale.id}/upload-photo`)"
                           accept="image/png, image/jpg, image/jpeg">
                    <div v-if="form.errors.photo" class="invalid-feedback d-block">{{ form.errors.photo }}</div>
                    <label v-if="permissions.includes('update-product') && ![3,4,5].includes(refrigeration_sale.status)" class="btn btn-purple cursor-pointer" for="upload-photo">Upload</label>

                    <Link v-if="permissions.includes('update-product') && ![3,4,5].includes(refrigeration_sale.status)" class="btn btn-green" :href="`/${shipment}/sales-order-eco/create?rs_id=${refrigeration_sale.id}`">Convert to Sales Order</Link>
                    <a class="btn btn-orange" href="javascript:void(alert('Still wait confirmation'))">Send Quotation</a>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import {useToast} from "vue-toastification";
import Swal from "sweetalert2";
import Form from "@/Pages/RefrigerationSale/Form.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const toast = useToast();
const created = computed(() => usePage().props.value.flash.created);

const gst_rate = usePage().props.value.gst_rate;

const props = defineProps({
    shipment: Object,
    refrigeration_sale: Object,
});

const form = useForm({
    photo: null,
});

const destroy = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/${props.shipment}/refrigeration-sales/${props.refrigeration_sale.id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
