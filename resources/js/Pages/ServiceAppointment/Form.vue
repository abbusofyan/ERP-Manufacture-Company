<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="appointment" class="big-title">Edit Service Appointment</div>
            <div v-else class="big-title">New Service Appointment</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/service-appointments">Service Order</Link>
                    </li>
                    <li>
                        <Link href="/service-appointments">Service Appointment</Link>
                    </li>
                    <li v-if="appointment">
                        <Link :href="`/service-appointments/${appointment.id}`">{{ appointment.cmp_number }}</Link>
                    </li>
                    <li>
                        <span v-if="appointment">Edit Service Appointment</span>
                        <span v-else>Create Service Appointment</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="updateAppointment()">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                                <span>Service Details</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="form-group">
                                    <label>Service Type</label>
                                    <Select2
                                        v-model="form.service_order_type"
                                        :class="{ 'is-invalid': form.errors.service_order_type }"
                                        :options="types"
                                        placeholder="Select Type"
                                    >
                                    </Select2>
                                    <div v-if="form.errors.service_order_type" class="invalid-feedback d-block">{{ form.errors.service_order_type }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Date of Appointment<span class="required">*</span></label>
                                    <VueDatePicker v-model="form.date_of_appointment" :class="{ 'is-invalid': form.errors.date_of_appointment }" :format="format" :enable-time-picker="false" placeholder="Select Date"></VueDatePicker>
                                    <div v-if="form.errors.date_of_appointment" class="invalid-feedback d-block">{{ form.errors.date_of_appointment }}</div>
                                </div>
                            </div>
                            <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Select Vehicle Number<span class="required">*</span></label>
                                        <div class="d-flex gap-1">
                                            <Select2
                                                v-model="form.vehicle_id"
                                                :class="{ 'is-invalid': form.errors.vehicle_id }"
                                                :options="vehicles"
                                                placeholder="Select Vehicle"
                                                class="flex-fill"
                                                :settings="{
                                                ajax: {
                                                    url: '/data/vehicles',
                                                    dataType: 'json',
                                                    method: 'POST',
                                                    data: function (params) {
                                                      return {
                                                        search: params.term,
                                                        _token: csrf,
                                                      };
                                                    },
                                                    processResults: function (data, params) {
                                                        return {
                                                            results: data.map(function (item) {
                                                                return {
                                                                    text: item.license_plate,
                                                                    id: item.id,
                                                                    data: item,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                                @select="(selected) => {
                                                    form.vehicle = selected.data;
                                                    updateClosestFutureEndDate();
                                                    if (form.vehicle.customer) {
                                                        form.customer_id = form.vehicle.customer.id;
                                                        form.customer = form.vehicle.customer;
                                                        customers = [{
                                                            id: form.vehicle.customer.id,
                                                            text: `${form.vehicle.customer.code} | ${form.vehicle.customer.name}`,
                                                            data: form.vehicle.customer
                                                        }];
                                                    }
                                                }"
                                            >
                                            </Select2>
                                            <a class="btn btn-purple--brounded flex-col" target="_blank" href="/vehicles">ADD NEW</a>
                                        </div>
                                        <p class="text-warning mt-5">*click add to register the vehicle with the customer</p>
                                        <div v-if="form.errors.vehicle_id" class="invalid-feedback d-block">{{ form.errors.vehicle_id }}</div>
                                    </div>
                                </div>
                                <div v-if="form.vehicle" class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                    <div class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                        <span>Vehicle Details</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>Vehicle Number</th>
                                                    <td>{{ form.vehicle.license_plate }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Type</th>
                                                    <td>{{ form.vehicle.type }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Vehicle Make</th>
                                                    <td>{{ form.vehicle.vehicle_make }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Model</th>
                                                    <td>{{ form.vehicle.model }}</td>
                                                </tr>
                                                <tr v-if="form.vehicle.nearest_service_contract">
                                                    <th>CMP Contract Period</th>
                                                    <td>
                                                        <div>
                                                            <Link :href="`/service-contracts/${form.vehicle.nearest_service_contract.id}`">{{ $filters.formatDate(form.vehicle.nearest_service_contract.start_duration_date) }} / {{ $filters.formatDate(form.vehicle.nearest_service_contract.end_duration_date) }}</Link>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <!-- Service Warranty -->
                                                <tr class="align-top" v-if="hasServiceContracts">
                                                    <th>Service Warranty</th>
                                                    <td>
                                                        <div class="d-flex gap-3 mb-10">
                                                            <!--
                                                            <div class="custom-checkbox style-2">
                                                                <input
                                                                    type="radio"
                                                                    :id="`contract-yes`"
                                                                    v-model="form.warranty_contract_id"
                                                                    :value="form.vehicle.nearest_service_contract.id"
                                                                    @change="handleContractInput()"
                                                                />
                                                                <label :for="`contract-yes`">Yes</label>
                                                            </div>
                                                            <div class="custom-checkbox style-2">
                                                                <input
                                                                    type="radio"
                                                                    :id="`contract-no`"
                                                                    :checked="form.warranty_contract_id !== form.vehicle.nearest_service_contract.id"
                                                                    @click="form.warranty_contract_id = null"
                                                                />
                                                                <label :for="`contract-no`">No</label>
                                                            </div>
                                                            -->
                                                            <a target="_blank" :href="`/service-contracts/${form.vehicle.nearest_service_contract.id}`">
                                                                {{ $filters.formatDate(form.vehicle.nearest_service_contract.end_duration_date) }} ({{ form.vehicle.warranty_mileage ?? '--' }}km)
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Sales Warranty -->
                                                <tr class="align-top" v-if="hasSalesOrders">
                                                    <th>Sales Warranty</th>
                                                    <td>
                                                        <div class="d-flex gap-3 mb-10" v-for="sale_order in form.vehicle.sales_orders" :key="sale_order.id">
                                                            <!--
                                                            <div class="custom-checkbox style-2">
                                                                <input
                                                                    type="radio"
                                                                    :id="`sale_order-${sale_order.id}-yes`"
                                                                    v-model="form.warranty_sale_order_eco_id"
                                                                    :value="sale_order.id"
                                                                    @change="handleSaleOrderInput()"
                                                                />
                                                                <label :for="`sale_order-${sale_order.id}-yes`">Yes</label>
                                                            </div>
                                                            <div class="custom-checkbox style-2">
                                                                <input
                                                                    type="radio"
                                                                    :id="`sale_order-${sale_order.id}-no`"
                                                                    :checked="form.warranty_sale_order_eco_id !== sale_order.id"
                                                                    @click="form.warranty_sale_order_eco_id = null"
                                                                />
                                                                <label :for="`sale_order-${sale_order.id}-no`">No</label>
                                                            </div>
                                                            -->
                                                            <a target="_blank" :href="`/${sale_order.refrigeration_sale?.shipment_url}/sales-order-eco/${sale_order.id}/edit`">
                                                                {{ $filters.formatDate(form.vehicle.warranty_end_date) }}
                                                                {{ form.vehicle.warranty_mileage ? `&nbsp;&nbsp;or&nbsp;&nbsp;${form.vehicle.warranty_mileage}km` : null }}
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Select Customer<span class="required">*</span></label>
                                        <div class="d-flex gap-1">
                                            <Select2
                                                v-model="form.customer_id"
                                                :class="{ 'is-invalid': form.errors.customer_id }"
                                                :options="customers"
                                                placeholder="Select Customer"
                                                class="flex-fill"
                                                :settings="{
                                                ajax: {
                                                    url: '/data/customers',
                                                    dataType: 'json',
                                                    method: 'POST',
                                                    data: function (params) {
                                                      return {
                                                        search: params.term,
                                                        _token: csrf,
                                                      };
                                                    },
                                                    processResults: function (data, params) {
                                                        return {
                                                            results: data.map(function (item) {
                                                                return {
                                                                    text: `${item.code} | ${item.name}`,
                                                                    id: item.id,
                                                                    data: item,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                                @select="(selected) => form.customer = selected.data"
                                            >
                                            </Select2>
                                            <a class="btn btn-purple--brounded flex-col" target="_blank" href="/customers">ADD NEW</a>
                                        </div>
                                        <p class="text-warning mt-5">*click add if the customer is not registered</p>
                                        <div v-if="form.errors.customer_id" class="invalid-feedback d-block">{{ form.errors.customer_id }}</div>
                                    </div>
                                </div>
                                <div v-if="form.customer" class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                    <div class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                        <span>Customer Details</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>Customer ID</th>
                                                    <td>{{ form.customer.code }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Name</th>
                                                    <td>{{ form.customer.name }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>POC</th>
                                                    <td>{{ form.customer.poc_first_name }} {{ form.customer.poc_last_name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>POC Contact</th>
                                                    <td>{{ form.customer.poc_phone }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2</span>
                                <span>Customer Feedback</span>
                            </div>
                            <div class="form-group">
                                <label>Customer Feedback</label>
                                <textarea class="form-control" v-model="form.customer_feedback" rows="3" placeholder="Feedback"></textarea>
                                <div v-if="form.errors.customer_feedback" class="invalid-feedback d-block">{{ form.errors.customer_feedback }}</div>
                            </div>
                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">3</span>
                                <span>POC Info</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input class="form-control" type="text" v-model="form.pic_first_name" placeholder="First Name">
                                    <div v-if="form.errors.pic_first_name" class="invalid-feedback d-block">{{ form.errors.pic_first_name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input class="form-control" type="text" v-model="form.pic_last_name" placeholder="Last Name">
                                    <div v-if="form.errors.pic_last_name" class="invalid-feedback d-block">{{ form.errors.pic_last_name }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Designation</label>
                                    <input class="form-control" type="text" v-model="form.pic_title" placeholder="Designation">
                                    <div v-if="form.errors.pic_title" class="invalid-feedback d-block">{{ form.errors.pic_title }}</div>
                                </div>
                                <div>
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label>Country Code</label>
                                                <Select2 v-model="form.pic_country_code" :class="{ 'is-invalid': form.errors.pic_country_code }" :options="phoneCodes" placeholder="Code"/>
                                                <div v-if="form.errors.pic_country_code" class="invalid-feedback d-block">{{ form.errors.pic_country_code }}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <input class="form-control" type="text" v-model="form.pic_phone_number" placeholder="Phone Number">
                                                <div v-if="form.errors.pic_phone_number" class="invalid-feedback d-block">{{ form.errors.pic_phone_number }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" v-model="form.pic_email" placeholder="Email">
                                    <div v-if="form.errors.pic_email" class="invalid-feedback d-block">{{ form.errors.pic_email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">4</span>
                                <span>Appointment Detail</span>
                            </div>
                            <div class="form-group">
                                <label>Remark</label>
                                <textarea class="form-control" v-model="form.remark" rows="3" placeholder="Remark"></textarea>
                                <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" @click="form.status = 1" :disabled="form.processing">Save</button>
                        <button class="btn btn-orange btn-orange" type="submit" @click="form.status = 2" :disabled="form.processing">Save as Draft</button>
                        <a class="btn btn-light btn-light--brounded" href="javascript:void(0)" @click="discard">Discard</a>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import {useForm, usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    csrf: String,
    appointment: Object,
    phoneCodes: Array,
    types: Array,
    customer: Array,
    customers: Array,
    vehicle: Array,
    vehicles: Array,
});

const form = useForm({
    customer_feedback: null,
    date_of_appointment: null,
    service_order_type: null,
    customer: null,
    customer_id: null,
    vehicle: null,
    vehicle_id: null,
    pic_first_name: null,
    pic_last_name: null,
    pic_title: null,
    pic_country_code: props.phoneCodes.find(code => code.text === "+65")?.id || null,
    pic_phone_number: null,
    pic_email: null,
    maintain_services: [],
    remark: null,
    status: 1,
    warranty_sale_order_eco_id: null,
    warranty_contract_id: null,
});

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

if (props.appointment) {
    const {
        customer_feedback, date_of_appointment, service_order_type, customer_id, vehicle_id,
        pic_first_name, pic_last_name, pic_title, pic_country_code, pic_phone_number, pic_email,
        remark, status, maintains, warranty_sale_order_eco_id, warranty_contract_id
    } = props.appointment;

    form.customer_feedback = customer_feedback;
    form.date_of_appointment = date_of_appointment;
    form.service_order_type = service_order_type;
    form.customer_id = customer_id;
    form.vehicle_id = vehicle_id;
    form.pic_first_name = pic_first_name;
    form.pic_last_name = pic_last_name;
    form.pic_title = pic_title;
    form.pic_country_code = pic_country_code;
    form.pic_phone_number = pic_phone_number;
    form.pic_email = pic_email;
    form.remark = remark;
    form.status = status;
    form.warranty_sale_order_eco_id = warranty_sale_order_eco_id;
    form.warranty_contract_id = warranty_contract_id;
    form.maintain_services = maintains.map(service => ({
        ...service,
        date: service.date ? service.date.slice(0, 7) : ''
    }));
} else {
    form.maintain_services.push({
        id: null,
        date: null,
        is_active: true,
    });
}

if (props.customer) {
    form.customer = props.customer;
}

if (props.vehicle) {
    form.vehicle = props.vehicle;
}

const numberOfServices = ref(form.maintain_services.length || 1);
watch(numberOfServices, (newVal) => {
    if (newVal > form.maintain_services.length) {
        for (let i = form.maintain_services.length; i < newVal; i++) {
            form.maintain_services.push({
                id: null,
                date: null,
                is_active: true,
            });
        }
    } else if (newVal < form.maintain_services.length) {
        for (let i = 0; i < form.maintain_services.length; i++) {
            form.maintain_services[i]['is_active'] = i + 1 <= newVal;
        }
    }
});

/**
 * Computes the closest future date from service contracts, sales orders, and the vehicle's warranty end date.
 * Filters out null or past dates and selects the closest future date.
 */
const updateClosestFutureEndDate = () => {
    const allDates = [
        ...(form.vehicle?.service_contracts ?? []).map(item => ({
            type: 'service_contract',
            id: item.id,
            date: item.end_duration_date
        })),
        ...(form.vehicle?.sales_orders ?? []).map(item => ({
            type: 'sales_order',
            id: item.id,
            date: form.vehicle?.warranty_end_date
        })),
    ].filter(item => {
        const today = new Date();
        return item && new Date(item.date) > today;
    });

    const closest = allDates.reduce((closest, item) => {
        const dateObj = new Date(item.date);
        return (!closest || dateObj < new Date(closest.date)) ? item : closest;
    }, null);

    // Update form based on closest date type
    if (closest) {
        if (closest.type === 'service_contract') {
            form.warranty_contract_id = closest.id;
            form.warranty_sale_order_eco_id = null;
        } else if (closest.type === 'sales_order' || closest.type === 'vehicle_warranty') {
            form.warranty_sale_order_eco_id = closest.id;
            form.warranty_contract_id = null;
        }
    } else {
        form.warranty_contract_id = null;
        form.warranty_sale_order_eco_id = null;
    }
};

// Computed properties to check if arrays have items
const hasServiceContracts = computed(() => form.vehicle?.nearest_service_contract);
const hasSalesOrders = computed(() => form.vehicle?.sales_orders?.length > 0);
const handleContractInput = () => {
    if (form.warranty_contract_id != null) {
        form.warranty_sale_order_eco_id = null;
    }
}
const handleSaleOrderInput = () => {
    if (form.warranty_sale_order_eco_id != null) {
        form.warranty_contract_id = null;
    }
}

const updatePIC = (customer) => {
    if (customer && customer.default_poc) {
        form.pic_first_name = customer.default_poc.first_name || null;
        form.pic_last_name = customer.default_poc.last_name || null;
        form.pic_email = customer.default_poc.email || null;
        form.pic_country_code = customer.default_poc.country_phone_code_id || null;
        form.pic_phone_number = customer.default_poc.phone || null;
        form.pic_title = customer.default_poc.title || null;
    } else {
        form.pic_first_name = null;
        form.pic_last_name = null;
        form.pic_email = null;
        form.pic_country_code = null;
        form.pic_phone_number = null;
        form.pic_title = null;
    }
};

// Watcher cho form.customer
watch(
    () => form.customer,
    (newCustomer) => {
        updatePIC(newCustomer);
    }
);

const updateAppointment = () => {
    if (Number(form.status) === 2) {
        Swal.fire({
            title: 'Convert To Draft?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea5455',
            cancelButtonColor: '#009CDB',
            confirmButtonText: 'Yes, Proceed!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                if (props.appointment) {
                    form.put(`/service-appointments/${props.appointment.id}`);
                } else {
                    form.post('/service-appointments');
                }
            }
        });
    } else {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea5455',
            cancelButtonColor: '#009CDB',
            confirmButtonText: 'Yes, Proceed!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                if (props.appointment) {
                    form.put(`/service-appointments/${props.appointment.id}`);
                } else {
                    form.post('/service-appointments');
                }
            }
        });
    }
};

const discard = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea5455',
        cancelButtonColor: '#009CDB',
        confirmButtonText: 'Yes, Proceed!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/service-appointments`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
