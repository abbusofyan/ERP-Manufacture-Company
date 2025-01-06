<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="order" class="big-title">Edit Order</div>
            <div v-else class="big-title">Create New Order</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/orders">Order</Link>
                    </li>
                    <li>
                        <span v-if="order">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="order ? form.put(`/orders/${order.id}`) : form.post('/orders')">
            <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="boxes">
                    <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span>Purchase Order List</span></div>
                        <div>
                            <div class="mb-20"><em class="fa-regular fa-circle-exclamation"></em><span class="ml-[.5rem] text-15 font-medium">Purchase Order Information</span></div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div>
                                <div class="form-group">
                                    <label>Vendor Name<span class="required">*</span></label>
                                    <Select2 v-model="form.vendor_id" :class="{ 'is-invalid': form.errors.vendor_id }" @select="loadProduct" :options="vendors" placeholder="Select Vendor"/>
                                    <div v-if="form.errors.vendor_id" class="invalid-feedback d-block">{{ form.errors.vendor_id }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Status<span class="required">*</span></label>
                                    <select v-model="form.status">
                                        <option value="1">Pending</option>
                                        <option value="2">Sent to Supplier</option>
                                        <option value="3">Confirmed</option>
                                        <option value="4">Completed</option>
                                        <option value="5">Canceled</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Reference No.<span class="required">*</span></label>
                                    <input type="text" :class="{ 'is-invalid': form.errors.ref_no }" v-model="form.ref_no" placeholder="Reference No.">
                                    <div v-if="form.errors.ref_no" class="invalid-feedback d-block">{{ form.errors.ref_no }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="form-group sm:col-span-6">
                                    <label>Vendor Address</label>
                                    <input type="text" :class="{ 'is-invalid': form.errors.vendor_address }" v-model="form.vendor_address" placeholder="Vendor Address">
                                    <div v-if="form.errors.vendor_address" class="invalid-feedback d-block">{{ form.errors.vendor_address }}</div>
                                </div>
                                <div class="form-group sm:col-span-6">
                                    <label>Vendor Phone</label>
                                    <input type="text" :class="{ 'is-invalid': form.errors.vendor_phone }" v-model="form.vendor_phone" placeholder="Vendor Phone">
                                    <div v-if="form.errors.vendor_phone" class="invalid-feedback d-block">{{ form.errors.vendor_phone }}</div>
                                </div>
                                <div class="form-group sm:col-span-6">
                                    <label>Delivery Address<span class="required">*</span></label>
                                    <input type="text" :class="{ 'is-invalid': form.errors.delivery_address }" v-model="form.delivery_address" placeholder="Delivery Address">
                                    <div v-if="form.errors.delivery_address" class="invalid-feedback d-block">{{ form.errors.delivery_address }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="products.length > 0" class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-24">
                        <div class="mb-20"><em class="fa-regular fa-gear"></em><span class="ml-[.5rem] text-15 font-medium">Add EDD (Estimated Delivery Date)</span></div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem] mb-[4rem]">
                            <div>
                                <div class="form-group">
                                    <label>Delivery date<span class="required">*</span></label>
                                    <VueDatePicker v-model="form.edd" :class="{ 'is-invalid': form.errors.edd }" :enable-time-picker="false" placeholder="Select date"></VueDatePicker>
                                    <div v-if="form.errors.edd" class="invalid-feedback d-block">{{ form.errors.edd }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-20"><em class="fa-regular fa-gear"></em><span class="ml-[.5rem] text-15 font-medium">Add Item</span></div>
                        <template v-for="(item, index) in form.items">
                            <input type="hidden" v-model="item.id">
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="has-add-box select2-w-100">
                                <div class="inner">
                                    <div class="box">
                                        <div class="flex items-start">
                                            <div class="w-full">
                                                <div class="form-group">
                                                    <label>Product Name<span class="required">*</span></label>
                                                    <Select2 v-model="item.product_id" :class="{ 'is-invalid': form.errors[`items.${index}.product_id`] }" :options="product_select_data" placeholder="Select Product"/>
                                                    <div v-if="form.errors[`items.${index}.product_id`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.product_id`] }}</div>
                                                </div>
                                                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-x-2">
                                                    <div class="form-group sm:col-span-2">
                                                        <label>Required Date<span class="required">*</span></label>
                                                        <VueDatePicker v-model="item.date" :class="{ 'is-invalid': form.errors[`items.${index}.date`] }" :enable-time-picker="false" placeholder="Select date"></VueDatePicker>
                                                        <div v-if="form.errors[`items.${index}.date`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.date`] }}</div>
                                                    </div>
                                                    <div class="form-group sm:col-span-2">
                                                        <label>Quantity</label>
                                                        <input type="text" v-model="item.quantity" :class="{ 'is-invalid': form.errors[`items.${index}.quantity`] }" placeholder="Quantity">
                                                        <div v-if="form.errors[`items.${index}.quantity`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.quantity`] }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <textarea v-model="item.description" :class="{ 'is-invalid': form.errors[`items.${index}.description`] }" cols="30" rows="10" placeholder="Type Here"></textarea>
                                                        <div v-if="form.errors[`items.${index}.description`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.description`] }}</div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Remark</label>
                                                        <textarea v-model="item.remark" :class="{ 'is-invalid': form.errors[`items.${index}.remark`] }" cols="30" rows="10" placeholder="Type Here"></textarea>
                                                        <div v-if="form.errors[`items.${index}.remark`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.remark`] }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="grid grid sm:!grid-cols-10 gap-x-2">
                                                        <div class="form-group sm:col-span-4">
                                                            <label>Currency<span class="required">*</span></label>
                                                            <select v-model="item.currency" :class="{ 'is-invalid': form.errors[`items.${index}.currency`] }">
                                                                <option value="sgd">SGD</option>
                                                                <option value="usd">USD</option>
                                                            </select>
                                                            <div v-if="form.errors[`items.${index}.currency`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.currency`] }}</div>
                                                        </div>
                                                        <div class="form-group sm:col-span-6">
                                                            <label>&nbsp;</label>
                                                            <input type="text" v-model="item.price" :class="{ 'is-invalid': form.errors[`items.${index}.price`] }" placeholder="1.0000">
                                                            <div v-if="form.errors[`items.${index}.price`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.price`] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Credit Term<span class="required">*</span></label>
                                                        <select v-model="item.credit_term" :class="{ 'is-invalid': form.errors[`items.${index}.credit_term`] }">
                                                            <option value="0">NET DUE</option>
                                                            <option value="6">6 month</option>
                                                            <option value="12">12 month</option>
                                                        </select>
                                                        <div v-if="form.errors[`items.${index}.credit_term`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.credit_term`] }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group">
                                                        <label>TAX (%)</label>
                                                        <input type="text" v-model="item.tax" :class="{ 'is-invalid': form.errors[`items.${index}.tax`] }" placeholder="1.0000">
                                                        <div v-if="form.errors[`items.${index}.tax`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.tax`] }}</div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>GST ($)</label>
                                                        <input type="text" v-model="item.gst" :class="{ 'is-invalid': form.errors[`items.${index}.gst`] }" placeholder="1.0000">
                                                        <div v-if="form.errors[`items.${index}.gst`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.gst`] }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group">
                                                        <label>NR</label>
                                                        <input type="text" v-model="item.nr" :class="{ 'is-invalid': form.errors[`items.${index}.nr`] }" placeholder="0.00">
                                                        <div v-if="form.errors[`items.${index}.nr`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.nr`] }}</div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Freight Charges</label>
                                                        <input type="text" v-model="item.freight_charges" :class="{ 'is-invalid': form.errors[`items.${index}.freight_charges`] }" placeholder="0.00">
                                                        <div v-if="form.errors[`items.${index}.freight_charges`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.freight_charges`] }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-row items-start">
                                                    <div class="grid grid sm:!grid-cols-10 gap-x-2 items-end">
                                                        <div class="form-group sm:col-span-4">
                                                            <label>Discount (%)</label>
                                                            <input type="text" v-model="item.discount" :class="{ 'is-invalid': form.errors[`items.${index}.discount`] }" placeholder="0%">
                                                            <div v-if="form.errors[`items.${index}.discount`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.discount`] }}</div>
                                                        </div>
                                                        <div class="form-group sm:col-span-6">
                                                            <input type="text" v-model="item.discount_price" :class="{ 'is-invalid': form.errors[`items.${index}.discount_price`] }" placeholder="0.00">
                                                            <div v-if="form.errors[`items.${index}.discount_price`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.discount_price`] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Upload Image</label>
                                                        <div class="fileContainer">
                                                            <label class="fileInputLabel" :for="`fileInput-${index}`">
                                                                <input class="fileInput" :id="`fileInput-${index}`"
                                                                       type="file"
                                                                       :class="{ 'is-invalid': form.errors[`items.${index}.image_url`] }"
                                                                       @input="item.image_url = $event.target.files[0];"
                                                                       accept="image/png, image/jpg, image/jpeg">
                                                                <span>Choose File</span>
                                                                <span class="browse">Browse</span>
                                                            </label>
                                                            <div v-if="form.errors[`items.${index}.image_url`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.image_url`] }}</div>
                                                            <div class="mt-12 text-[#82868B]">Allowed file types: png, jpg, jpeg.</div>
                                                            <div class="files"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btn-group ml-20 mt-27 shrink-0">
                                                <a v-if="form.items.length > 1" @click="removeItem(index, item)" class="btn btn-red btn-red--brounded" href="javascript:void(0)">
                                                    <em class="fa-regular fa-xmark"></em><span>Delete</span>
                                                </a>
                                            </div>
                                        </div>
                                        <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div v-if="form.errors.items" class="invalid-feedback d-block">{{ form.errors.items }}</div>
                        <div class="btn-group">
                            <a class="btn btn-purple" @click="addItem" href="javascript:void(0)"><em class="fa-regular fa-plus"></em><span>Add  Item</span></a>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block" v-else>
                        <div class="mb-20"><em class="fa-regular fa-gear"></em><span class="ml-[.5rem] text-15 font-medium">Add EDD (Estimated Delivery Date)</span></div>
                        There are no products
                    </div>
                </div>
                <div class="pt-20">
                    <div class="mb-20"><em class="fa-regular fa-gear"></em><span class="ml-[.5rem] text-15 font-medium">Purchase Order Remark</span></div>
                    <div class="form-group">
                        <label>Remark</label>
                        <textarea v-model="form.remark" :class="{ 'is-invalid': form.errors.remark }" cols="30" rows="10" placeholder="Type Here"></textarea>
                        <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                    </div>
                </div>
                <div class="form-wrap">
                    <div class="btn-group mt-[5.6rem]">
                        <button v-if="products.length > 0" class="btn btn-purple" type="submit" :disabled="form.processing">Save</button>
                        <Link class="btn btn-light btn-light--brounded" href="/orders">Discard</Link>
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
import {Inertia} from "@inertiajs/inertia";
import debounce from "lodash.debounce";

const props = defineProps({
    order: Object,
    vendors: Array,
    products: Array,
    product_select_data: Array,
});

const form = useForm({
    status: 1,
    ref_no: null,
    vendor_id: null,
    vendor_address: null,
    vendor_phone: null,
    delivery_address: null,
    edd: null,
    remark: null,
    items: [],
    delete_items: []
});

const addItem = () => {
    form.items.push({
        id: null,
        product_id: null,
        product_name: null,
        date: null,
        price: null,
        quantity: 1,
        description: null,
        remark: null,
        currency: null,
        credit_term: null,
        tax: null,
        gst: null,
        nr: null,
        freight_charges: null,
        discount: null,
        discount_price: null,
        image_url: null,
    })
}

const removeItem = (index, item) => {
    form.items.splice(index, 1)

    if (item['id']) {
        form.delete_items.push(item['id'])
    }
}

const loadProduct = () => {
    form.items = [];
    Inertia.get(
        "/orders/create",
        {
            vendor_id: form.vendor_id,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}

onMounted(() => {
    if (props.order) {
        form.status = props.order.status;
        form.ref_no = props.order.ref_no;
        form.vendor_id = props.order.vendor_id;
        form.vendor_address = props.order.vendor_address;
        form.vendor_phone = props.order.vendor_phone;
        form.delivery_address = props.order.delivery_address;
        form.edd = props.order.edd;
        form.remark = props.order.remark;
        form.items = props.order.items;
    }
});
</script>
