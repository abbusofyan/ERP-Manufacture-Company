<template>
    <div class="box rounded-md shadow-default mt-20 bg-white">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <div class="text-18 font-medium">
                <span>Bill of Material</span>
            </div>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search">
                    </div>
                </div>
                <Modal :products="products" :assemblies="assemblies" :csrf="csrf"></Modal>
            </div>
        </div>

        <div class="px-25">

            <div class="text-15 text-primary font-medium pb-17  border-0 border-t-[1px] border-solid border-[#EBE9F1] mb-14 pt-20">
                <span>VEHICLE SPECIFICATIONS</span>
            </div>

            <div class="grid md:grid-cols-2 gap-[3rem]">
                <div class="form-group">
                    <label>Vehicle Number</label>
                    <input
                        class="form-control"
                        type="tel"
                        :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_license_plate`] }"
                        v-model="form.vehicle_license_plate"
                        @input="getVehicleByLicensePlate(header)"
                        placeholder="Vehicle Number"
                    >
                    <div v-if="form.errors[`headers.${index}.vehicle_license_plate`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_license_plate`] }}</div>
                </div>
                <div class="form-group">
                    <label>Chassis No.</label>
                    <input
                        class="form-control"
                        type="tel"
                        :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_chassis_no`] }"
                        v-model="form.vehicle_chassis_no"
                        placeholder="Chassis No."
                    >
                    <div v-if="form.errors[`headers.${index}.vehicle_chassis_no`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_chassis_no`] }}</div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-[3rem]">
                <div class="form-group">
                    <label>Vehicle Voltage</label>
                    <input
                        class="form-control"
                        type="tel"
                        :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_voltage`] }"
                        v-model="form.vehicle_voltage"
                        placeholder="Vehicle Voltage"
                    >
                    <div v-if="form.errors[`headers.${index}.vehicle_voltage`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_voltage`] }}</div>
                </div>
                <div class="grid md:grid-cols-2 gap-[3rem]">
                    <div class="form-group">
                        <label>Vehicle Make</label>
                        <input
                            class="form-control"
                            type="tel"
                            :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_make`] }"
                            v-model="form.vehicle_make"
                            placeholder="Vehicle Make"
                        >
                        <div v-if="form.errors[`headers.${index}.vehicle_make`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_make`] }}</div>
                    </div>
                    <div class="form-group">
                        <label>Model</label>
                        <input
                            class="form-control"
                            type="tel"
                            :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_model`] }"
                            v-model="form.vehicle_model"
                            placeholder="Model"
                        >
                        <div v-if="form.errors[`headers.${index}.vehicle_model`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_model`] }}</div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-[3rem]">
                <div class="grid md:grid-cols-2 gap-[3rem]">
                    <div class="form-group">
                        <label>Vehicle Class</label>
                        <input
                            class="form-control"
                            type="tel"
                            :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_class`] }"
                            v-model="form.vehicle_class"
                            placeholder="Vehicle Class"
                        >
                        <div v-if="form.errors[`headers.${index}.vehicle_class`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_class`] }}</div>
                    </div>
                    <div class="form-group">
                        <label>Type of Plate</label>
                        <input
                            class="form-control"
                            type="tel"
                            :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_type_of_plate`] }"
                            v-model="form.vehicle_type_of_plate"
                            placeholder="Type of Plate"
                        >
                        <div v-if="form.errors[`headers.${index}.vehicle_type_of_plate`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_type_of_plate`] }}</div>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-[3rem]">
                    <div class="form-group">
                        <label>Vehicle Mileage</label>
                        <input
                            class="form-control"
                            type="tel"
                            :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_mileage`] }"
                            v-model="form.vehicle_mileage"
                            placeholder="Vehicle Mileage"
                        >
                        <div v-if="form.errors[`headers.${index}.vehicle_mileage`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_mileage`] }}</div>
                    </div>
                    <div class="form-group">
                        <label>Running Hours</label>
                        <input
                            class="form-control"
                            type="tel"
                            :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_running_hours`] }"
                            v-model="form.vehicle_running_hours"
                            placeholder="Running Hours"
                        >
                        <div v-if="form.errors[`headers.${index}.vehicle_running_hours`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_running_hours`] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="table-responsive">
            <table class="table select-rows">
                <thead>
                <tr>
                    <th>Item SKU / Code</th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in form.items" :key="index">
                        <td>{{ item.detail.sku ? item.detail.sku : item.detail.code }}</td>
                        <td>{{ item.detail.name }}</td>
                        <td>{{ item.type == 'Material' ? item.detail.uom.code : item.detail.uom }}</td>
                        <td>{{ item.qty }}</td>
                        <td>
                            <div class="el-actions justify-end">
                                <a class="text-red" href="javascript:void(0);" @click="destroy(index)"><em class="fa-regular fa-trash-can"></em></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> -->
    </div>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {onMounted} from "vue";
import Swal from "sweetalert2";
import Modal from "@/Pages/VehicleSpec/BillOfMaterial/AddModal.vue";
import { ref, inject } from 'vue';

const props = defineProps({
    products: Array,
    assemblies: Array,
    csrf: String
});

let search = ref(null);

const form = inject('form')

const destroy = (index) => {
    form.materials.splice(index, 1);
}

</script>
