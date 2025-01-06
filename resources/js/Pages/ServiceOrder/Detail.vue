<template>
    <div class="mb-8 border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-17 rounded-[.5rem]">
        <div class="text-18 font-medium cursor-pointer" :class="{'pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]' : openDetail}" @click="openDetail = !openDetail">
            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">!</span>
            <span>Order Details</span>
        </div>
        <div v-if="openDetail">
            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                <div>
                    <table class="table-1-el w-full">
                        <tr>
                            <th class="text-nowrap">Customer</th>
                            <td class="pl-15">
                                <Select2
                                    v-if="![7].includes(serviceOrder.status)"
                                    v-model="form.customer_id"
                                    :class="{ 'is-invalid': form.errors.customer_id }"
                                    :options="form.optionCustomer"
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
                                    @select="(selected) => form.optionCustomer = [{ id:selected.data.id, text:`${selected.data.code} | ${selected.data.name}` }]"
                                ></Select2>
                                <div v-else>
                                    {{ serviceOrder.customer?.code }} | {{ serviceOrder.customer?.name }}
                                </div>
                                <div v-if="form.errors.customer_id" class="invalid-feedback d-block">{{ form.errors.customer_id }}</div>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Confirmed by</th>
                            <td class="pl-15">
                                <Select2
                                    v-if="![7].includes(serviceOrder.status)"
                                    v-model="form.confirmed_user_id"
                                    :class="{ 'is-invalid': form.errors.confirmed_user_id }"
                                    :options="form.optionConfirmed"
                                    placeholder="Confirmed by"
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
                                    @select="(selected) => form.optionConfirmed = [{ id:selected.data.id, text:`${selected.data.code} | ${selected.data.name}` }]"
                                >
                                </Select2>
                                <div v-else-if="serviceOrder.confirmed">
                                    {{ serviceOrder.confirmed?.code }} | {{ serviceOrder.confirmed?.name }}
                                </div>
                                <div v-if="form.errors.confirmed_user_id" class="invalid-feedback d-block">{{ form.errors.confirmed_user_id }}</div>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Vehicle Number</th>
                            <td class="pl-15">{{ serviceOrder.vehicle.license_plate }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Warranty Status</th>
                            <td class="pl-15">
                                <div class="el-tag" :class="serviceOrder.warranty_class">
                                    {{ serviceOrder.warranty_text }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Type</th>
                            <td class="pl-15">{{ serviceOrder.service_order_type }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">CMP Contact Number</th>
                            <td class="pl-15">{{ serviceOrder.pic_phone_number }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Quotation Number</th>
                            <td v-if="serviceOrder.service_quotation" class="pl-15">
                                <a class="text-bold text-purple" :href="`/service-quotations/${ serviceOrder.service_quotation_id }`" target="_blank">
                                    {{ serviceOrder.service_quotation.code }}
                                </a>
                            </td>
                            <td v-else class="pl-15">
                                <Select2
                                    v-if="![7].includes(serviceOrder.status)"
                                    v-model="form.service_quotation_id"
                                    :class="{ 'is-invalid': form.errors.confirmed_user_id }"
                                    :options="form.optionConfirmed"
                                    placeholder="Select Quotation"
                                    class="flex-fill"
                                    :settings="{
                                        ajax: {
                                            url: '/data/service-quotations',
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
                                                            text: `${item.code}`,
                                                            id: item.id,
                                                            data: item,
                                                        };
                                                    })
                                                };
                                            }
                                        }
                                    }"
                                    @select="(selected) => form.optionServiceQuotation = [{ id:selected.data.id, text:`${selected.data.code}` }]"
                                >
                                </Select2>
                                <div v-if="form.errors.service_quotation_id" class="invalid-feedback d-block">{{ form.errors.service_quotation_id }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div>
                    <table class="table-1-el w-full">
                        <tr>
                            <th class="text-nowrap">Delivery Date</th>
                            <td class="pl-15">{{ serviceOrder.delivery_date || 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">PIC Name</th>
                            <td class="pl-15">{{ serviceOrder.pic_first_name }} {{ serviceOrder.pic_last_name }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">PIC Number</th>
                            <td class="pl-15">{{ serviceOrder.pic_phone_number }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">PIC Email</th>
                            <td class="pl-15">{{ serviceOrder.pic_email }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Technician</th>
                            <td class="pl-15">{{ serviceOrder.technician?.name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="mt-18 pt-20 border-0 border-t-[1px] border-solid border-[#EBE9F1]">
                <table class="table-1-el w-full">
                    <tr>
                        <th class="text-nowrap">Remarks</th>
                    </tr>
                    <tr>
                        <td class="">
                            {{ serviceOrder.remark }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mt-18 pt-20 border-0 border-t-[1px] border-solid border-[#EBE9F1]">
                <table class="table-1-el w-full">
                    <tr>
                        <th class="text-nowrap">Plan Start Date</th>
                        <td class="pl-15">
                            <VueDatePicker
                                v-model="form.plan_start_date"
                                :class="{ 'is-invalid': form.errors.plan_start_date }"
                                placeholder="Select Start Date"
                                :enable-time-picker="false"
                                :format="format"
                            />
                            <div v-if="form.errors.plan_start_date" class="invalid-feedback d-block">{{ form.errors.plan_start_date }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-nowrap">Plan Complete Date</th>
                        <td class="pl-15">
                            <VueDatePicker
                                v-model="form.plan_complete_date"
                                :class="{ 'is-invalid': form.errors.plan_complete_date }"
                                placeholder="Select Complete Date"
                                :enable-time-picker="false"
                                :format="format"
                            />
                            <div v-if="form.errors.plan_complete_date" class="invalid-feedback d-block">{{ form.errors.plan_complete_date }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-nowrap">Est. Complete Date</th>
                        <td class="pl-15">{{ $filters.formatDate(serviceOrder.est_complete_date) }}</td>
                    </tr>
                    <tr>
                        <th class="text-nowrap">Actual End Date</th>
                        <td class="pl-15">{{ $filters.formatDate(serviceOrder.delivery_date) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref, watch} from "vue";
import {useForm} from "@inertiajs/inertia-vue3";
import {debounce} from "lodash"; // Import debounce from lodash

const props = defineProps({
    csrf: String,
    serviceOrder: Object,
});

const form = useForm({
    optionCustomer: null,
    customer_id: null,
    optionConfirmed: null,
    confirmed_user_id: null,
    optionServiceQuotation: null,
    service_quotation_id: null,
    plan_start_date: null,
    plan_complete_date: null,
});

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

let openDetail = ref(null);

const updateDetail = debounce(() => {
    form.post(`/service-orders/${props.serviceOrder.id}/update-detail`, {
        preserveScroll: true,
    });
}, 500);

onMounted(() => {
    if (props.serviceOrder) {
        form.customer_id = props.serviceOrder.customer_id;
        form.confirmed_user_id = props.serviceOrder.confirmed_user_id;
        form.plan_start_date = props.serviceOrder.plan_start_date;
        form.plan_complete_date = props.serviceOrder.plan_complete_date;
    }

    if (props.serviceOrder.customer) {
        form.optionCustomer = [
            {
                id: props.serviceOrder.customer.id,
                text: `${props.serviceOrder.customer.code} | ${props.serviceOrder.customer.name}`,
            },
        ];
    }

    if (props.serviceOrder.confirmed) {
        form.optionConfirmed = [
            {
                id: props.serviceOrder.confirmed.id,
                text: `${props.serviceOrder.confirmed.code} | ${props.serviceOrder.confirmed.name}`,
            },
        ];
    }

    if (props.serviceOrder.serviceQuotation) {
        form.optionConfirmed = [
            {
                id: props.serviceOrder.serviceQuotation.id,
                text: `${props.serviceOrder.serviceQuotation.code}`,
            },
        ];
    }

    watch(
        () => [form.customer_id, form.confirmed_user_id, form.service_quotation_id, form.plan_start_date, form.plan_complete_date],
        (newVal, oldVal) => {
            if (newVal !== oldVal) {
                updateDetail();
            }
        },
        {deep: true}
    );
});
</script>
