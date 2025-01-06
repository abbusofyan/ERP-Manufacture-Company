<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="vehicle" class="big-title">Edit Vehicle</div>
            <div v-else class="big-title">Create Vehicle</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/vehicles">Vehicle</Link>
                    </li>
                    <li>
                        <span v-if="vehicle">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="vehicle ? form.put(`/vehicles/${vehicle.id}`) : form.post('/vehicles')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span>New Vehicle</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Vehicle Number<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.license_plate }" v-model="form.license_plate" placeholder="SBA 1234A">
                                    <div v-if="form.errors.license_plate" class="invalid-feedback d-block">{{ form.errors.license_plate }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Customer Name<span class="required">*</span></label>
                                    <Select2
                                        v-model="form.customer_id"
                                        :class="{ 'is-invalid': form.errors.customer_id }"
                                        :options="customers"
                                        placeholder="Select Customer"
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
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                    />
                                    <div v-if="form.errors.customer_id" class="invalid-feedback d-block">{{ form.errors.customer_id }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Type<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.type }" v-model="form.type" placeholder="10' Truck">
                                    <div v-if="form.errors.type" class="invalid-feedback d-block">{{ form.errors.type }}</div>
                                </div>
                                <div class="form-group">
                                    <label>End User</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.end_user }" v-model="form.end_user" placeholder="End User">
                                    <div v-if="form.errors.end_user" class="invalid-feedback d-block">{{ form.errors.end_user }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Vehicle Voltage<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_voltage }" v-model="form.vehicle_voltage" placeholder="Vehicle Voltage">
                                    <div v-if="form.errors.vehicle_voltage" class="invalid-feedback d-block">{{ form.errors.vehicle_voltage }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Chassis Salesperson / Contact No.</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.contact_no }" v-model="form.contact_no" placeholder="Chassis Salesperson / Contact No.">
                                    <div v-if="form.errors.contact_no" class="invalid-feedback d-block">{{ form.errors.contact_no }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Chassis No.<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.chassis_no }" v-model="form.chassis_no" placeholder="Chassis No.">
                                    <div v-if="form.errors.chassis_no" class="invalid-feedback d-block">{{ form.errors.chassis_no }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Chassis Delivery</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.chassis_delivery }" v-model="form.chassis_delivery" placeholder="Chassis Delivery">
                                    <div v-if="form.errors.chassis_delivery" class="invalid-feedback d-block">{{ form.errors.chassis_delivery }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Vehicle Make<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_make }" v-model="form.vehicle_make" placeholder="Vehicle Make">
                                        <div v-if="form.errors.vehicle_make" class="invalid-feedback d-block">{{ form.errors.vehicle_make }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Model<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.model }" v-model="form.model" placeholder="model">
                                        <div v-if="form.errors.model" class="invalid-feedback d-block">{{ form.errors.model }}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Refrigeration serial number<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.refrigeration_serial_number }" v-model="form.refrigeration_serial_number" placeholder="Refrigeration serial number">
                                    <div v-if="form.errors.refrigeration_serial_number" class="invalid-feedback d-block">{{ form.errors.refrigeration_serial_number }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Vehicle Class<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_class }" v-model="form.vehicle_class" placeholder="Vehicle Class">
                                        <div v-if="form.errors.vehicle_class" class="invalid-feedback d-block">{{ form.errors.vehicle_class }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Type of Plate<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.type_of_plate }" v-model="form.type_of_plate" placeholder="Type of Plate">
                                        <div v-if="form.errors.type_of_plate" class="invalid-feedback d-block">{{ form.errors.type_of_plate }}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Other Info</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.other_info }" v-model="form.other_info" placeholder="Other Info">
                                    <div v-if="form.errors.other_info" class="invalid-feedback d-block">{{ form.errors.other_info }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Current Vehicle Mileage</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.current_mileage }" v-model="form.current_mileage" placeholder="Enter current mileage">
                                    <div v-if="form.errors.current_mileage" class="invalid-feedback d-block">{{ form.errors.current_mileage }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Mileage Date</label>
                                    <VueDatePicker v-model="form.mileage_date" :class="{ 'is-invalid': form.errors.mileage_date }" :format="format" :enable-time-picker="false" placeholder="Select Date"></VueDatePicker>
                                    <div v-if="form.errors.mileage_date" class="invalid-feedback d-block">{{ form.errors.mileage_date }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Warranty Mileage</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.warranty_mileage }" v-model="form.warranty_mileage" placeholder="Enter warranty mileage">
                                    <div v-if="form.errors.warranty_mileage" class="invalid-feedback d-block">{{ form.errors.warranty_mileage }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Warranty End Date</label>
                                    <VueDatePicker v-model="form.warranty_end_date" :class="{ 'is-invalid': form.errors.warranty_end_date }" :format="format" :enable-time-picker="false" placeholder="Select Date"></VueDatePicker>
                                    <div v-if="form.errors.warranty_end_date" class="invalid-feedback d-block">{{ form.errors.warranty_end_date }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                        <Link class="btn btn-light btn-light--brounded" href="/vehicles">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted} from "vue";

const props = defineProps({
    csrf: String,
    vehicle: Object,
    customers: Array,
});

const form = useForm({
    license_plate: null,
    type: null,
    customer_id: null,
    end_user: null,
    vehicle_voltage: null,
    contact_no: null,
    chassis_no: null,
    chassis_delivery: null,
    vehicle_make: null,
    model: null,
    vehicle_class: null,
    type_of_plate: null,
    refrigeration_serial_number: null,
    other_info: null,
    current_mileage: null,
    mileage_date: null,
    warranty_mileage: null,
    warranty_end_date: null,
});

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

onMounted(() => {
    if (props.vehicle) {
        form.license_plate = props.vehicle.license_plate;
        form.type = props.vehicle.type;
        form.customer_id = props.vehicle.customer_id;
        form.end_user = props.vehicle.end_user;
        form.vehicle_voltage = props.vehicle.vehicle_voltage;
        form.contact_no = props.vehicle.contact_no;
        form.chassis_no = props.vehicle.chassis_no;
        form.chassis_delivery = props.vehicle.chassis_delivery;
        form.vehicle_make = props.vehicle.vehicle_make;
        form.model = props.vehicle.model;
        form.vehicle_class = props.vehicle.vehicle_class;
        form.type_of_plate = props.vehicle.type_of_plate;
        form.refrigeration_serial_number = props.vehicle.refrigeration_serial_number;
        form.other_info = props.vehicle.other_info;
        form.current_mileage = props.vehicle.current_mileage;
        form.mileage_date = props.vehicle.mileage_date;
        form.warranty_mileage = props.vehicle.warranty_mileage;
        form.warranty_end_date = props.vehicle.warranty_end_date;
    }
});
</script>
