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
                            <span>Purchase order to : {{ data.vendor.name }}</span>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Purchase Date<span class="required">*</span></label>
                                    <VueDatePicker date-picker v-model="form.payload[parentIndex].purchase_date" format="Y-m-d"></VueDatePicker>
                                    <div v-if="form.errors.purchase_date" class="invalid-feedback d-block">{{ form.errors.purchase_date }}</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Delivery Date<span class="required">*</span></label>
                                    <VueDatePicker date-picker v-model="form.payload[parentIndex].delivery_date" format="Y-m-d"></VueDatePicker>
                                    <div v-if="form.errors.delivery_date" class="invalid-feedback d-block">{{ form.errors.delivery_date }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Remark</label>
                            <textarea v-model="form.payload[parentIndex].remark" :class="{ 'is-invalid': form.errors[`remark`] }" cols="30" rows="10" placeholder="Type Here"></textarea>
                            <div v-if="form.errors[`remark`]" class="invalid-feedback d-block">{{ form.errors[`remark`] }}</div>
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
                                    <template v-for="(item, childIndex) in data.data" :key="childIndex">
                                        <tr @click="setDetail(parentIndex, childIndex)" :style="selectedRow == parentIndex.toString() + childIndex.toString() ? 'background-color:#b0b8f4; font-weight: bold;' : ''">
                                            <td class="text-nowrap">
                                                {{ item.product.sku }}
                                            </td>
                                            <td>
                                                {{ item.product.name }}
                                            </td>
                                            <td class="text-nowrap">
                                                {{ item.product.uom.code }}
                                            </td>
                                            <td class="text-nowrap">
                                                {{ item.qty }}
                                            </td>
                                            <td>
                                                ${{ form.payload[parentIndex].data[childIndex].unit_price }} <br>
                                                <div v-if="form.errors[`payload.${parentIndex}.data.${childIndex}.unit_price`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.data.${childIndex}.unit_price`] }}</div>
                                            </td>
                                            <td>
                                                {{ form.payload[parentIndex]?.data[childIndex]?.gst_text ? form.payload[parentIndex].data[childIndex].gst_text + '%' : '' }} <br>
                                                <div v-if="form.errors[`payload.${parentIndex}.data.${childIndex}.gst`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.data.${childIndex}.gst`] }}</div>
                                            </td>
                                            <td class="text-nowrap">
                                                ${{ form.payload[parentIndex].data[childIndex].total }} <br>
                                                <div v-if="form.errors[`payload.${parentIndex}.data.${childIndex}.total`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.data.${childIndex}.total`] }}</div>
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
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.category }" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].product.category.name" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Item ID<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].product.sku" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Item Name<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].product.name" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Description<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].product.description" disabled>
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
                                            <input class="form-control" type="text" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].qty" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">UOM<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].product.uom.code" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Unit Price<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].unit_price">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">GST<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <Select2
                                                v-model="form.payload[selectedParentIndex].data[selectedChildIndex].gst"
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
                                                form.payload[selectedParentIndex].data[selectedChildIndex].gst_text = selected.data.value
                                                calculateRowTotal()
                                                calculateSubTotal()
                                            }"
                                            />
                                            <!-- <div v-if="form.errors[`items.${selectedRow}.gst`]" class="invalid-feedback d-block">{{ form.errors[`items.${selectedRow}.gst`] }}</div> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Total<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.total }" v-model="form.payload[selectedParentIndex].data[selectedChildIndex].total" disabled>
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
                                                v-model="form.payload[parentIndex].payment_method"
                                                :class="{ 'is-invalid': form.errors[`payment_method`] }"
                                                placeholder="Select Payment Method"
                                                :options="props.payment_methods"
                                                class="flex-fill"
                                            />
                                            <div v-if="form.errors[`payload.${parentIndex}.payment_method`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.payment_method`] }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Terms<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <!-- <Select2
                                            v-model="form.payload[parentIndex].terms"
                                            :class="{ 'is-invalid': form.errors[`terms`] }"
                                            placeholder="Select Terms"
                                            :options="props.terms"
                                            class="flex-fill"
                                            /> -->
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.currency }" v-model="form.payload[parentIndex].vendor.credit_term" disabled>
                                            <div v-if="form.errors[`payload.${parentIndex}.vendor.credit_term`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.terms`] }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Currency</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.currency }" v-model="form.payload[parentIndex].currency" disabled>
                                            <div v-if="form.errors.currency" class="invalid-feedback d-block">{{ form.errors.currency }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Criterion</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.criterion }" v-model="form.payload[parentIndex].criterion">
                                            <div v-if="form.errors.criterion" class="invalid-feedback d-block">{{ form.errors.criterion }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 float-right">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Sub Total<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.sub_total }" v-model="form.payload[parentIndex].sub_total" disabled>
                                            <div v-if="form.errors[`payload.${parentIndex}.sub_total`]" class="invalid-feedback d-block">{{ form.errors[`payload.${parentIndex}.sub_total`] }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Discount</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" @input="calculateTotal(parentIndex)" :class="{ 'is-invalid': form.errors.discount }" v-model="form.payload[parentIndex].discount">
                                            <div v-if="form.errors.discount" class="invalid-feedback d-block">{{ form.errors.discount }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Freight<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.freight }" v-model="form.payload[parentIndex].freight">
                                            <div v-if="form.errors.freight" class="invalid-feedback d-block">{{ form.errors.freight }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">GST<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.gst }" v-model="form.payload[parentIndex].gst" disabled>
                                            <div v-if="form.errors.gst" class="invalid-feedback d-block">{{ form.errors.gst }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Rounding</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.rounding }" v-model="form.payload[parentIndex].rounding">
                                            <div v-if="form.errors.rounding" class="invalid-feedback d-block">{{ form.errors.rounding }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 mt-10">Total<span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.total }" v-model="form.payload[parentIndex].total" disabled>
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
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Submit Purchase Order</button>
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
import {onMounted, ref, watch} from "vue";
import {useToast} from "vue-toastification";

const toast = useToast();

const props = defineProps({
    csrf: String,
    payload: Array,
    payment_methods: Array,
    terms: Array
});

const form = useForm({
    payload: []
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
    const item = form.payload[selectedParentIndex.value].data[selectedChildIndex.value]
    const unitPrice = parseFloat(item.unit_price) || 0;
    const quantity = parseInt(item.qty) || 0;
    const gst = selectedGST.value.value
    const subtotal = unitPrice * quantity;
    form.payload[selectedParentIndex.value].data[selectedChildIndex.value].total = subtotal * (1 + gst / 100);
}

const calculateSubTotal = () => {
    const items = form.payload[selectedParentIndex.value];
    const items_total_arr = items.data.map(item => item.total !== null ? item.total : 0);
    const sub_total = items_total_arr.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
    form.payload[selectedParentIndex.value].sub_total = sub_total
    calculateTotal()
}

const calculateTotal = (parentIndex) => {
    let index = parentIndex;
    if (typeof parentIndex === "undefined") {
        index = selectedParentIndex.value
    }
    const sub_total = form.payload[index].sub_total
    const discount = form.payload[index].discount
    const rounding = form.payload[index].rouding
    form.payload[index].total = sub_total - discount
}

// watch([form.discount, form.sub_total], () => {
//     calculateTotal();
// });

watch(
    () => [form.discount, form.sub_total],
    () => calculateTotal()
);

onMounted(() => {
    const groupedData = new Map();

    Object.values(props.payload).forEach((data) => {
        const vendorId = data.vendor.id;

        if (!groupedData.has(vendorId)) {
            groupedData.set(vendorId, {
                vendor: data.vendor,
                vendor_id: vendorId,
                purchase_date: null,
                delivery_date: null,
                remark: null,
                payment_method: null,
                terms: null,
                currency: null,
                criterion: null,
                sub_total: 0,
                discount: 0,
                freight: 0,
                gst: 0,
                rounding: 0,
                total: 0,
                data: []
            });
        }

        const vendorData = groupedData.get(vendorId);
        const vendorItems = vendorData.data;

        data.requisition_item.forEach((item) => {
            const productId = item.product.id;
            let existingProduct = vendorItems.find((d) => d.product_id === productId);

            if (!existingProduct) {
                existingProduct = {
                    product: item.product,
                    product_id: productId,
                    product_name: item.product.name,
                    requisition_ids: [],
                    requisition_item_ids: [],
                    qty: 0,
                    unit_price: item.product.prices[0]?.price ?? 0,
                    gst: item.product.gst_rate ?? 0,
                    total_price: 0,
                    total: 0
                };
                vendorItems.push(existingProduct);
            }

            existingProduct.requisition_item_ids.push(item.id);
            existingProduct.requisition_ids.push(item.requisition_id);
            existingProduct.qty += item.pending_order_qty;
            existingProduct.total_price += item.pending_order_qty * (item.product.prices[0]?.price ?? 0);
            existingProduct.total += existingProduct.total_price + (existingProduct.total_price * (existingProduct.gst / 100));

            vendorData.sub_total += existingProduct.total_price;
            vendorData.gst += existingProduct.total_price * (existingProduct.gst / 100);
        });

        vendorData.total = vendorData.sub_total + vendorData.gst + vendorData.freight - vendorData.discount + vendorData.rounding;
    });

    form.payload = Array.from(groupedData.values());
    console.log(form.payload);
});

const handleSubmit = () => {
    selectedRow.value = null;
    selectedParentIndex.value = null
    selectedChildIndex.value = null
    form.post('/requisitions-to-order')
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
