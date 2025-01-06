<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ serviceOrder.code }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"/></span></Link>
                    </li>
                    <li>
                        <Link href="/service-orders">Service Order</Link>
                    </li>
                    <li>
                        <Link href="/service-orders">Active Service / Repair Order</Link>
                    </li>
                    <li>
                        <span>{{ serviceOrder.code }}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">

                    <ProcessTab :serviceOrder="serviceOrder" :stages="stages"></ProcessTab>

                    <!-- Status and Actions -->
                    <div class="grid md:grid-cols-2 gap-[7.7rem] mb-[2.6rem]">
                        <table class="table-1-el w-full">
                            <tr>
                                <th class="text-nowrap">Current Status</th>
                                <td class="pl-15">
                                    {{ serviceOrder.current_process ? `${serviceOrder.current_process.status_text}/${serviceOrder.current_process.name}` : '--' }}
                                </td>
                            </tr>
                            <!-- Service Warranty -->
                            <tr class="align-top" v-if="hasServiceContracts">
                                <th class="text-nowrap">Service Warranty</th>
                                <td class="pl-15 pb-10">
                                    <div class="d-flex gap-3 mb-10">
                                        <div class="custom-checkbox style-2">
                                            <input
                                                type="radio"
                                                :id="`contract-yes`"
                                                v-model="form.warranty_contract_id"
                                                :value="serviceOrder.vehicle.nearest_service_contract.id"
                                                @change="handleContractInput()"
                                            />
                                            <label :for="`contract-yes`">Yes</label>
                                        </div>
                                        <div class="custom-checkbox style-2">
                                            <input
                                                type="radio"
                                                :id="`contract-no`"
                                                :checked="form.warranty_contract_id !== serviceOrder.vehicle.nearest_service_contract.id"
                                                @click="form.warranty_contract_id = null"
                                                @change="updateDetail"
                                            />
                                            <label :for="`contract-no`">No</label>
                                        </div>
                                        {{ $filters.formatDate(serviceOrder.vehicle.nearest_service_contract.end_duration_date) }}
                                    </div>
                                </td>
                            </tr>

                            <!-- Sales Warranty -->
                            <tr class="align-top" v-if="hasSalesOrders">
                                <th class="text-nowrap">Sales Warranty</th>
                                <td class="pl-15 pb-10">
                                    <div class="d-flex gap-3 mb-10" v-for="sale_order in serviceOrder.vehicle.sales_orders" :key="sale_order.id">
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
                                                @change="updateDetail"
                                            />
                                            <label :for="`sale_order-${sale_order.id}-no`">No</label>
                                        </div>
                                        {{ $filters.formatDate(serviceOrder.vehicle.warranty_end_date) }}
                                        {{ serviceOrder.vehicle.warranty_mileage ? `&nbsp;&nbsp;or&nbsp;&nbsp;${serviceOrder.vehicle.warranty_mileage}km` : null }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Come Back Job</th>
                                <td class="pl-15">
                                    <div v-if="serviceOrder.status != 7" class="d-flex gap-26">
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.come_back_job" type="radio" value="1" id="comeback-job-yes" @click="updateDetail">
                                            <label for="comeback-job-yes">Yes</label>
                                        </div>
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.come_back_job" type="radio" value="0" id="comeback-job-no" @click="updateDetail">
                                            <label for="comeback-job-no">No</label>
                                        </div>
                                    </div>
                                    <div v-else>
                                        {{ form.come_back_job == 0 ? 'No' : 'Yes' }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">CMP</th>
                                <td class="pl-15">
                                    <div v-if="serviceOrder.status != 7" class="d-flex gap-26">
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.in_cmp" type="radio" value="1" id="in_cmp-yes" @click="updateDetail">
                                            <label for="in_cmp-yes">Yes</label>
                                        </div>
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.in_cmp" type="radio" value="0" id="in_cmp-no" @click="updateDetail">
                                            <label for="in_cmp-no">No</label>
                                        </div>
                                    </div>
                                    <div v-else>
                                        {{ form.in_cmp == 0 ? 'No' : 'Yes' }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Type</th>
                                <td class="pl-15">
                                    <div v-if="serviceOrder.status != 7" class="d-flex gap-26">
                                        <Select2
                                            v-model="form.service_order_type"
                                            :class="{ 'is-invalid': form.errors.service_order_type }"
                                            :options="filteredTypes"
                                            class="w-px-300"
                                            placeholder="Select Type"
                                            @select="updateDetail"
                                        />
                                    </div>
                                    <div v-else>
                                        {{ form.service_order_type }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div v-if="form.errors.in_cmp" class="invalid-feedback d-block">{{ form.errors.in_cmp }}</div>
                                </td>
                            </tr>
                        </table>

                        <!--Process Button-->
                        <ButtonGroup :serviceOrder="serviceOrder"></ButtonGroup>
                        <!--Process Button-->

                    </div>

                    <ProcessForm :serviceOrder="serviceOrder"></ProcessForm>

                    <!-- Order Details -->
                    <Detail :csrf="csrf" :serviceOrder="serviceOrder"></Detail>
                </div>
            </div>
        </div>


        <!--Tab-->
        <Tab :serviceOrder="serviceOrder" :tab="tab"></Tab>
        <slot/>

    </Layout>
</template>

<script setup>
import {Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Tab from "./Tab.vue";
import ProcessTab from "./Process/Tab.vue";
import Detail from "./Detail.vue";
import ButtonGroup from "./Process/ButtonGroup.vue";
import ProcessForm from "./Process/ProcessForm.vue";
import debounce from "lodash.debounce";
import Index from "@/Pages/ServiceOrder/Requirement/Index.vue";
import {computed, watch} from "vue";

const props = defineProps({
    csrf: String,
    tab: Object,
    serviceOrder: Object,
    filters: Object,
    processes: Object,
    stages: Object,
    types: Array,
});

const form = useForm({
    come_back_job: 0,
    in_warranty: 0,
    in_cmp: 0,
    service_order_type: null,
    warranty_sale_order_eco_id: null,
    warranty_contract_id: null,
});

if (props.serviceOrder) {
    form.come_back_job = props.serviceOrder.come_back_job;
    form.in_warranty = props.serviceOrder.in_warranty;
    form.in_cmp = props.serviceOrder.in_cmp;
    form.service_order_type = props.serviceOrder.service_order_type;
    form.warranty_sale_order_eco_id = props.serviceOrder.warranty_sale_order_eco_id;
    form.warranty_contract_id = props.serviceOrder.warranty_contract_id;
}

const updateDetail = debounce(() => {
    form.post(`/service-orders/${props.serviceOrder.id}/update-detail`)
}, 500)

// Filter types based on in_cmp
const filteredTypes = computed(() => {
    return props.types.filter(type => {
        return form.in_cmp == 1 ? type.is_cmp : !type.is_cmp;
    });
});

// Watch service_order_type changes
watch(() => form.service_order_type, (newValue) => {
    const selectedType = props.types.find(type => type.id === newValue);
    form.in_cmp = selectedType?.is_cmp == 1 ? 1 : 0;
    updateDetail();
});

watch(form, nV => {
    if (form.warranty_sale_order_eco_id == null && form.warranty_contract_id == null) {
        form.in_warranty = 0;
    } else {
        form.in_warranty = 1;
    }
})

// Computed properties to check if arrays have items
const hasServiceContracts = computed(() => props.serviceOrder.vehicle?.nearest_service_contract);
const hasSalesOrders = computed(() => props.serviceOrder.vehicle?.sales_orders?.length > 0);
const handleContractInput = () => {
    if (form.warranty_contract_id != null) {
        form.warranty_sale_order_eco_id = null;
    }

    updateDetail();
}
const handleSaleOrderInput = () => {
    if (form.warranty_sale_order_eco_id != null) {
        form.warranty_contract_id = null;
    }

    updateDetail();
}
</script>
