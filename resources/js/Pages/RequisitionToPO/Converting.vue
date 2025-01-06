<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Converting to Purchase Order</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>

        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <label class="d-flex align-items-center gap-1">

                </label>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="btn-group" v-if="permissions.includes('create-requisition')">
                        <Link class="btn btn-purple" @click="confirm">Confirm</Link>
                        <Link class="btn btn-danger btn-danger--brounded" href="/requisitions-to-order">Cancel</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th class="text-nowrap">Date</th>
                        <th class="text-nowrap">Required Date</th>
                        <th class="text-nowrap">Item ID</th>
                        <th>Qty</th>
                        <th>UoM</th>
                        <th>Unit Price</th>
                        <th>{{orders ? 'Existing PO' : 'Vendor'}}</th>
                        <th>Currency</th>
                        <th>Requested By</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in form.items">
                            <td class="text-nowrap">{{ $filters.formatDate(item.date) }}</td>
                            <td class="text-nowrap">{{ $filters.formatDate(item.required_date) }}</td>
                            <td class="text-nowrap">
                                <div class="text-purple font-semibold">
                                    <Link :href="`/products/${item.product_id}`"><span>{{ item.item_id }}</span></Link>
                                </div>
                            </td>
                            <td>{{ item.qty }}</td>
                            <td>{{ item.uom }}</td>
                            <td>${{ item.unit_price ?? 0 }}</td>
                            <td>
                                <Select2
                                    v-if="!orders"
                                    class="select2-w-100"
                                    v-model="item.vendor_id"
                                    placeholder="----------------- Select Vendor ------------------"
                                    :options="props.requisition_items[index].options"
                                    :settings="{
                                        ajax: {
                                            url: '/data/vendors',
                                            dataType: 'json',
                                            method: 'POST',
                                            data: function (params) {
                                              return {
                                                product_id: item.product_id,
                                                search: params.term,
                                                _token: csrf,
                                              };
                                            },
                                            processResults: function (data, params) {
                                                const vendorsWithProducts = data.filter(item => item.product_prices); // Assuming `hasProducts` indicates availability
                                                const vendorsWithoutProducts = data.filter(item => !item.product_prices);

                                                const hasProductSeparator = {
                                                    id: 'separator',
                                                    text: 'Vendor has product',
                                                    disabled: true
                                                };

                                                const noProductSeparator = {
                                                    id: 'separator',
                                                    text: 'Vendor without product',
                                                    disabled: true
                                                };

                                                const results = [
                                                    hasProductSeparator,
                                                    ...vendorsWithProducts.map(vendor => ({
                                                        text: vendor.code + ' | ' + vendor.name,
                                                        id: vendor.id,
                                                        data: vendor
                                                    })),
                                                    noProductSeparator,
                                                    ...vendorsWithoutProducts.map(vendor => ({
                                                        text: vendor.code + ' | ' + vendor.name,
                                                        id: vendor.id,
                                                        data: vendor
                                                    }))
                                                ];

                                                return {
                                                    results
                                                };
                                            }
                                        }
                                    }"
                                    @select="(selected) => {
                                        form.items[index].unit_price = selected.data.product_prices?.[0]?.price || 0;
                                    }"
                                />
                                <Select2
                                    v-else
                                    class="select2-w-100"
                                    v-model="item.order_id"
                                    placeholder="----------------- Select PO | Vendor ------------------"
                                    :options="orders"
                                />
                            </td>
                            <td></td>
                            <td>{{ item.requested_by }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import axios from 'axios';
import {useToast} from "vue-toastification";
const toast = useToast();

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    csrf: String,
    requisition_items: Array,
    orders: Array
});

const form = useForm({
    items: []
});

const cancel = () => {
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
            Inertia.post(`/requisitions-to-order/update-status`, {
                items: form.items,
            }, {
                preserveScroll: true,
            });
        }
    });
};

onMounted(() => {
    props.requisition_items.forEach((item, i) => {
        form.items.push({
            id: item.id,
            product_id: item.product_id,
            vendor_id: item.product.vendor1?.vendor.id ?? null,
            order_id: null,
            date: item.created_at,
            required_date: item.required_date,
            item_id: item.product ? item.product.sku : 'OTHER',
            qty: item.pending_order_qty,
            uom: item.product.uom.code,
            unit_price: item.product.vendor1?.price ?? null,
            vendor_code: item.product.vendor1?.vendor.code ?? '',
            requested_by: item.requisition.created_by.name,
        })
    });
})


const onVendorSelect = async (index) => {
    const vendor_id = form.items[index].vendor_id;
    const product_id = form.items[index].product_id;

    try {
        const response = await axios.post('/data/products/get-prices-by-vendor', {
            vendor_id: vendor_id,
            product_id: product_id,
            _token: props.csrf
        });
        console.log(response.data);
        form.items[index].unit_price = response.data?.price ?? null
    } catch (error) {
        console.error("Error fetching product prices:", error);
    }
};


function checkIfAddToExistingPO() {
    const currentUrl = window.location.href;

    const url = new URL(currentUrl);

    const pathname = url.pathname;
    const segments = pathname.split('/');

    const targetSegment = segments.find(segment => segment === 'add-to-existing-po');
    if (targetSegment) {
        return true;
    }
    return false;
}


const confirm = () => {

    const isAddToExistingPO = checkIfAddToExistingPO()

    const orderIds = form.items.map(item => item.order_id);
    const vendorIds = form.items.map(item => item.vendor_id);
    const productIds = form.items.map(item => item.product_id);
    const requisitionItemIds = form.items.map(item => item.id);

    if (isAddToExistingPO) {
        if (orderIds.some(item => item === null)) {
            return toast.error('Select PO', {
                icon: "fa-solid fa-check",
                timeout: 3000,
            });
        }
    } else {
        if (vendorIds.some(item => item === null)) {
            return toast.error('Vendor cannot be empty', {
                icon: "fa-solid fa-check",
                timeout: 3000,
            });
        }
    }

    Inertia.get(`/requisitions-to-order/create`, {
        convert_to: isAddToExistingPO ? 'existing po' : 'new',
        order_ids: orderIds,
        requisition_item_ids:
        requisitionItemIds,
        product_ids: productIds,
        vendor_ids: vendorIds
    });
}
</script>
