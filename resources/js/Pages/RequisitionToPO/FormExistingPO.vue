<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                Requisition to PO
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li class="active">
                        <span>Create Purchase Order</span>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="handleSubmit">
            <div class="form-wrap">
                <template v-for="(data, parentIndex) in form.payload">
                    <div class="box pb-17 mb-17 pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="text-15 w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">{{ parentIndex + 1 }}</span>
                            <span>Purchase order to : {{ data.vendor_name }}</span>
                        </div>
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Item Information</div>
                        <div class="boxes">
                            <div class="table-responsive">
                                <table class="table select-rows">
                                    <thead>
                                    <tr>
                                        <th class="text-nowrap">
                                            <div class="flex items-center justify-between gap-1"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th class="text-nowrap">
                                            <div class="flex items-center justify-between gap-1"><span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th class="text-nowrap">
                                            <div class="flex items-center justify-between gap-1"><span>UOM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th class="text-nowrap">
                                            <div class="flex items-center justify-between gap-1"><span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th class="text-nowrap">
                                            <div class="flex items-center justify-between gap-1"><span>Unit Price</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th class="text-nowrap">
                                            <div class="flex items-center justify-between gap-1"><span>GST</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th class="text-nowrap">
                                            <div class="flex items-center justify-between gap-1"><span>Total Price</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="item in data.items">
                                        <tr>
                                            <td class="text-nowrap">
                                                {{ item.sku }}
                                            </td>
                                            <td>
                                                {{ item.product_name }}
                                            </td>
                                            <td class="text-nowrap">
                                                {{ item.uom }}
                                            </td>
                                            <td class="text-nowrap">
                                                {{ item.qty }}
                                            </td>
                                            <td>
                                                ${{ item.unit_price }} <br>
                                            </td>
                                            <td>
                                                {{item.gst_text}}
                                            </td>
                                            <td class="text-nowrap">
                                                ${{item.total_price}}
                                            </td>
                                        </tr>
                                    </template>
                                    <tr>
                                        <td colSpan="7" class="text-center">New items</td>
                                    </tr>
                                    <template v-for="(item, childIndex) in data.new_items" :key="childIndex">
                                        <tr @click="item.order_item_id === 0 ? setDetail(parentIndex, childIndex) : undefined" :style="selectedRow === parentIndex.toString() + childIndex.toString() ? 'background-color:#b0b8f4; font-weight: bold;' : ''">
                                            <td class="text-nowrap">
                                                {{ item.sku }}
                                            </td>
                                            <td>
                                                {{ item.product_name }}
                                            </td>
                                            <td class="text-nowrap">
                                                {{ item.uom }}
                                            </td>
                                            <td class="text-nowrap">
                                                {{ item.qty }}
                                            </td>
                                            <td>
                                                ${{ item.unit_price }} <br>
                                                <div v-if="form.errors[`payload.${parentIndex}.new_items.${childIndex}.unit_price`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.new_items.${childIndex}.unit_price`] }}</div>
                                            </td>
                                            <td>
                                                {{item.gst_text}}
                                                <div v-if="form.errors[`payload.${parentIndex}.new_items.${childIndex}.gst`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.new_items.${childIndex}.gst`] }}</div>
                                            </td>
                                            <td class="text-nowrap">
                                                ${{item.total_price}}
                                                <div v-if="form.errors[`payload.${parentIndex}.new_items.${childIndex}.total_price`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.new_items.${childIndex}.total_price`] }}</div>
                                            </td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div v-if="selectedParentIndex == parentIndex" class="pb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Item Detail</div>
                            <div class="row flex flex-wrap gap-10 justify-between mt-[3.6rem]">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Category<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.category }" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].category" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Item ID<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].sku" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Item Name<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].product_name" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Description<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].description" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Warehouse<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 float-right">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Order Qty<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].qty" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">UOM<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].uom" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Unit Price<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].unit_price">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">GST<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <Select2
                                                v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].gst"
                                                placeholder="Select GST"
                                                :settings="{
                                                ajax: {
                                                    url: '/data/gst',
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
                                                            results: data.map(function (gst) {
                                                                return {
                                                                    text: `${gst.code} | ${gst.value} | ${gst.description}`,
                                                                    id: gst.id,
                                                                    data: gst
                                                                };
                                                            })
                                                        };
                                                    }
                                                },
                                            }"
                                                @select="(selected) => {
                                                selectedGST = selected.data
                                                form.payload[selectedParentIndex].new_items[selectedChildIndex].gst_text = selected.data.value
                                                calculateRowTotal()
                                                calculateSubTotal()
                                            }"
                                            />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Total<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.total }" v-model="form.payload[selectedParentIndex].new_items[selectedChildIndex].total_price" disabled>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <button type="button" class="btn btn-purple pull-right" @click="selectedRow = selectedParentIndex = selectedChildIndex = null">Save</button>
                        </div>
                        <div v-else>
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">General</div>
                            <div class="row flex flex-wrap gap-10 justify-between mt-[3.6rem]">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Payment Method<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <Select2
                                                disabled
                                                v-model="data.order.payment_method"
                                                :class="{ 'is-invalid': form.errors[`payment_method`] }"
                                                placeholder="Select Payment Method"
                                                :options="props.payment_methods"
                                                class="flex-fill"
                                            />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Terms<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.currency }" v-model="data.order.terms" disabled>
                                            <div v-if="form.errors[`payload.${parentIndex}.vendor.credit_term`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.terms`] }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Currency</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.currency }" v-model="data.order.currency" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Criterion</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.criterion }" v-model="data.order.criterion" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 float-right">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Sub Total<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.sub_total }" v-model="data.order.sub_total" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Discount</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" @input="calculateTotal(parentIndex)" :class="{ 'is-invalid': form.errors.discount }" v-model="data.order.discount" disabled>
                                            <div v-if="form.errors.discount" class="invalid-feedback d-block">{{ form.errors.discount }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Freight<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="data.order.freight" disabled>
                                            <div v-if="form.errors.freight" class="invalid-feedback d-block">{{ form.errors.freight }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">GST<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.gst }" v-model="data.order.gst" disabled>
                                            <div v-if="form.errors.gst" class="invalid-feedback d-block">{{ form.errors.gst }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Rounding</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.rounding }" v-model="data.order.rounding" disabled>
                                            <div v-if="form.errors.rounding" class="invalid-feedback d-block">{{ form.errors.rounding }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Total<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.total }" v-model="data.order.total" disabled>
                                            <div v-if="form.errors[`payload.${parentIndex}.total`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.total`] }}</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <div class="box pb-17 mb-17 pt-20 px-25 rounded-md shadow-default bg-white">
                    <div class="btn-group">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Update Purchase Order</button>
                        <Link class="btn btn-light btn-light--brounded" href="/requisitions-to-order">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted, ref, watch, getCurrentInstance} from "vue";
import {useToast} from "vue-toastification";

const { appContext } = getCurrentInstance();
const filters = appContext.config.globalProperties.$filters;

const toast = useToast();

const props = defineProps({
    csrf: String,
    payload: Array,
    payment_methods: Array,
    terms: Array
});

const form = useForm({
    payload: [
        {
            items: [],
            new_items: [],
            order: {}
        }
    ]
});

const selectedRow = ref({})
const selectedGST = ref({})
const selectedParentIndex = ref({})
const selectedChildIndex = ref({})


const setDetail = (parentIndex, childIndex) => {
    selectedParentIndex.value = parentIndex;
    selectedChildIndex.value = childIndex;

    let selectedIndex = parentIndex.toString() + childIndex.toString()

    if (selectedIndex == selectedRow.value) {
        selectedRow.value = null
        selectedParentIndex.value = null
        selectedChildIndex.value = null
    } else {
        selectedRow.value = selectedIndex
    }
}

const calculateRowTotal = () => {
    const item = form.payload[selectedParentIndex.value].new_items[selectedChildIndex.value]
    const unitPrice = parseFloat(item.unit_price) || 0;
    const quantity = parseFloat(item.qty) || 0;
    const gst = selectedGST.value.value
    const subtotal = unitPrice * quantity;
    form.payload[selectedParentIndex.value].new_items[selectedChildIndex.value].total_price = filters.countTotalPrice(subtotal, parseFloat(0), parseFloat(0), parseFloat(gst), parseFloat(0))
}

const calculateSubTotal = () => {
    const existing_items = form.payload[selectedParentIndex.value].items;
    const new_items = form.payload[selectedParentIndex.value].new_items;

    const existing_items_total = existing_items.reduce((accumulator, item) => accumulator + (item.total_price || 0), 0);
    const new_items_total = new_items.reduce((accumulator, item) => accumulator + (item.total_price || 0), 0);

    const sub_total = existing_items_total + new_items_total;

    form.payload[selectedParentIndex.value].order.sub_total = sub_total.toFixed(2);
    calculateTotal();
};


const calculateTotal = (parentIndex) => {
    let index = parentIndex;
    if (typeof parentIndex === "undefined") {
        index = selectedParentIndex.value
    }
    const sub_total = form.payload[index]['order'].sub_total
    const discount = form.payload[index]['order'].discount
    const rounding = form.payload[index]['order'].rounding
    const freight = form.payload[index]['order'].freight
    const gst = form.payload[index]['order'].gst
    form.payload[index]['order'].total = filters.countTotalPrice(sub_total, parseFloat(discount), parseFloat(freight), parseFloat(gst), parseFloat(rounding))
}

// watch([form.discount, form.sub_total], () => {
//     calculateTotal();
// });

watch(
    () => [form.discount, form.sub_total],
    () => calculateTotal()
);

onMounted(() => {
    let formData = [];
    Object.values(props.payload).forEach((data, index) => {

        let sub_total = 0;

        Object.values(data.existing_item).forEach((order_item, item_index) => {
            form.payload[index]['items'][item_index] = {
                order_item_id : order_item.id,
                product_id: order_item.product_id,
                product_name : order_item.product_name,
                sku: order_item.product.sku,
                category: order_item.product.category.name,
                description: order_item.product.description,
                uom: order_item.product.uom.code,
                qty: parseFloat(order_item.quantity),
                unit_price: parseFloat(order_item.unit_price),
                gst: order_item.gst,
                gst_text: order_item.gst.value,
                total_price: parseFloat(order_item.total)
            }
            sub_total += parseFloat(order_item.total)
        });

        Object.values(data.requisition_item).forEach((requisition_item, new_item_index) => {
            const existingProductData = form.payload[index]['items'].find(item => item.product_id === requisition_item.product_id);
            let item_sub_total = (existingProductData?.unit_price || 0) * requisition_item.pending_order_qty;
            let total_price = filters.countTotalPrice(item_sub_total, parseFloat(0), parseFloat(0), parseFloat(existingProductData?.gst_text || 0), parseFloat(0))

            form.payload[index]['new_items'][new_item_index] = {
                order_item_id: existingProductData?.order_item_id || 0,
                requisition_id: requisition_item.requisition_id,
                requisition_item_id: requisition_item.id,
                product_name: requisition_item.product_name,
                product_id: requisition_item.product_id,
                sku: requisition_item.product.sku,
                category: requisition_item.product.category.name,
                description: requisition_item.product.description,
                uom: requisition_item.product.uom.code,
                qty: requisition_item.pending_order_qty,
                unit_price: existingProductData?.unit_price || 0,
                gst: existingProductData?.gst.id || 0,
                gst_text: existingProductData?.gst_text || 0,
                total_price: total_price
            }
            sub_total += total_price
        });

        form.payload[index]['order'] = {
            id: data.order.id,
            vendor_id: data.order.vendor_id,
            vendor_name: data.order.vendor.name,
            payment_method: data.order.payment_method,
            terms: data.order.terms,
            currency: data.order.currency,
            criterion: data.order.criterion,
            sub_total: sub_total.toFixed(2),
            discount: data.order.discount,
            freight: data.order.freight,
            gst: data.order.gst,
            rounding: data.order.rounding,
            total: filters.countTotalPrice(sub_total, parseFloat(data.order.discount), parseFloat(data.order.freight), parseFloat(data.order.gst), parseFloat(data.order.rounding))
        }
    });
});

const handleSubmit = async () => {
    selectedRow.value = null;
    selectedParentIndex.value = null
    selectedChildIndex.value = null
    form.post('/requisitions-to-order/add-to-existing-po')
}
</script>

<style media="screen">
.table-1-el th {
    text-align: left;
    padding-right: 20px;
    vertical-align: top;
}

.requisitions-list {
    list-style: none;
    padding-left: 0;
}

.requisitions-list li {
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.requisition-id {
    min-width: 100px; /* Adjust the width based on your design */
}

.requisition-qty {
    padding-left: 10px;
}

</style>
