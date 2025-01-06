<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Sales Order (ECO)</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/quotations">Refrigeration Sales</Link>
                    </li>
                    <li class="active"><span>Sales Order (ECO)</span></li>
                </ol>
            </nav>
        </div>
        <div class="form-wrap">

            <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mb-[2.6rem]">
                <div class="boxes">
                    <div class="mb-[2.6rem]">
                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table-1-el w-full">
                                    <tr>
                                        <th class="pb-10">Name</th>
                                    </tr>
                                    <tr>
                                        <td class="text-uppercase">
                                            {{ order.quotation.customer.name ?? '--' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <table class="table-1-el w-full">
                                    <tr>
                                        <th>Company</th>
                                        <td>
                                            {{ order.quotation.poc_name ?? '--' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Production W.O</th>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Delivery Date</th>
                                        <td>
                                            <div v-for="(mine_stone, index) in order.mine_stones" :key="index">
                                                {{ mine_stone.delivery_date ?? '--' }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mt-[2.6rem]"></div>

                        <div class="d-flex justify-between flex-wrap gap-[3rem] mt-[2.6rem]">
                            <div class="text-bold">Sales Order Milestone or which stage the vehicle is at</div>
                        </div>

                        <div class="row justify-content-center">
                            <template v-for="(mine_stone, index) in order.mine_stones" :key="index">
                                <template v-for="(item, index_i) in mine_stone.items" :key="index_i">
                                    <div class="col-xl-2 col-lg-3 mt-[2.6rem]" v-if="item.stage && item.active" :key="index_i">
                                        <div class="stage-mine-stone-item" :class="`circle-${index_i}`">
                                            <div class="top">
                                                <div class="circle mb-16">
                                                    <div class="wrapper">
                                                        <span class="text-24 text-primary text-bold">{{ index_i + 1 }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-15 text-center text-bold mb-10">
                                                    {{ item.stage }}
                                                </div>
                                                <div class="el-tag h-auto text-center" :class="item.status_class">
                                                    <span>{{ item.status_text }}</span>
                                                </div>
                                            </div>
                                            <div class="bottom">
                                                <div class="line">
                                                    <div class="dot"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box rounded-md shadow-default bg-white mb-[2.6rem]">
                <div class="boxes">
                    <div class="table-responsive pb-0">
                        <table class="table">
                            <thead>
                            <tr>
                                <th rowspan="2" class="bg-[#EBE9F1]">
                                    Vehicle
                                </th>
                                <th rowspan="2" class="bg-[#EBE9F1]">
                                    Production Process
                                </th>
                                <th colspan="2" class="text-center bg-[#EBE9F1]">
                                    Optional Stages
                                </th>
                                <th rowspan="2" class="bg-[#EBE9F1]">
                                    Remarks
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">
                                    Optional 1
                                </th>
                                <th class="text-center">
                                    Optional 2
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="order.quotation.license_plate">
                                <td>
                                    {{ order.quotation.license_plate }}
                                </td>
                                <td>
                                    <div class="el-tag" :class="order.quotation.status_class">
                                        {{ order.quotation.status_text }}
                                    </div>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr v-if="order.vehicle_id">
                                <td>
                                    {{ order.vehicle.license_plate }}
                                </td>
                                <td>
                                    <div class="el-tag" :class="order.status_class">
                                        {{ order.status_text }}
                                    </div>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                    {{ order.vehicle.other_info }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <form @submit.prevent="form.put(`/${shipment}/sales-order-eco/${order.id}/update-detail`)">
                <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mb-[2.6rem]">
                    <div class="boxes">
                        <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                            <div class="font-semibold mb-17">
                                <span>Production Work Order</span>
                            </div>
                            <div class="btn-group mb-17">
                                <button class="btn btn-purple" type="submit" @click="form.type = 2" :disabled="form.processing">Save Changes</button>
                                <Link class="btn btn-light btn-light--brounded" :href="`/quotations/${order.quotation_id}`">Discard</Link>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div class="form-group">
                                <label>Unit / Model<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.unit_model }" v-model="form.unit_model" placeholder="Unit / Model">
                                <div v-if="form.errors.unit_model" class="invalid-feedback d-block">{{ form.errors.unit_model }}</div>
                            </div>
                            <div class="form-group">
                                <label>Box Dimension in MM<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.box_dimension_mm }" v-model="form.box_dimension_mm" placeholder="Box Dimension in MM">
                                <div v-if="form.errors.box_dimension_mm" class="invalid-feedback d-block">{{ form.errors.box_dimension_mm }}</div>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>PE Drawing<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.pe_drawing }" v-model="form.pe_drawing" placeholder="PE Drawing">
                                    <div v-if="form.errors.pe_drawing" class="invalid-feedback d-block">{{ form.errors.pe_drawing }}</div>
                                </div>
                                <div class="form-group">
                                    <label>PE Drawing ( File )<span class="required">*</span></label>
                                    <input type="file" class="form-control-file"
                                           :class="{ 'is-invalid': form.errors.pe_drawing_file }"
                                           @input="form.pe_drawing_file = $event.target.files[0];"
                                           accept="image/png, image/jpg, image/jpeg">
                                    <div v-if="form.errors.pe_drawing_file" class="invalid-feedback d-block">{{ form.errors.pe_drawing_file }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Chassis Extension / Shortening<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.chassis_extension_shortening }" v-model="form.chassis_extension_shortening" placeholder="Chassis Extension / Shortening">
                                <div v-if="form.errors.chassis_extension_shortening" class="invalid-feedback d-block">{{ form.errors.chassis_extension_shortening }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Remark</label>
                            <textarea class="form-control" type="text" :class="{ 'is-invalid': form.errors.remark }" v-model="form.remark" placeholder="Remark"></textarea>
                            <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {computed, onMounted} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Form from "@/Pages/SalesOrderECO/Form.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    shipment: Object,
    order: Object,
});


const form = useForm({
    unit_model: null,
    box_dimension_mm: null,
    pe_drawing: null,
    pe_drawing_file: null,
    chassis_extension_shortening: null,
    remark: null,
});

onMounted(() => {
    if(props.order.production_work_order){
        form.unit_model = props.order.production_work_order.unit_model;
        form.box_dimension_mm = props.order.production_work_order.box_dimension_mm;
        form.pe_drawing = props.order.production_work_order.pe_drawing;
        form.chassis_extension_shortening = props.order.production_work_order.chassis_extension_shortening;
        form.remark = props.order.production_work_order.remark;
    }
})
</script>
