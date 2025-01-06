<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="goodsReceipt" class="big-title">Edit Goods Receipt</div>
            <div v-else class="big-title">Add Goods Receipt</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/goods-receipts">Goods Receipt List</Link>
                    </li>
                    <li>
                        <span v-if="goodsReceipt">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="goodsReceipt ? form.put(`/goods-receipts/${goodsReceipt.id}`) : form.post('/goods-receipts')">
            <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                    <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span>New Goods Receiving Notes</span></div>
                    <div>
                        <div class="mb-20"><em class="fa-regular fa-circle-exclamation"></em><span class="ml-[.5rem] text-15 font-medium">Goods Receive Information</span></div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div>
                                <div class="form-group">
                                    <label>Purchase Order Number<span class="required">*</span></label>
                                    <Select2
                                        v-model="form.order_id"
                                        :options="orders"
                                        placeholder="Select Order"
                                        :settings="{
                                                ajax: {
                                                    url: '/data/orders',
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
                                                                    text: item.code,
                                                                    id: item.id,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                        }"
                                        @select="fetchItems"
                                    />
                                    <div v-if="form.errors.order_id" class="invalid-feedback d-block">{{ form.errors.order_id }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="has-add-box pt-26">
                    <div>
                        <div class="mb-20"><em class="fa-regular fa-circle-exclamation"></em><span class="ml-[.5rem] text-15 font-medium">Requestor Information</span></div>
                        <div v-if="form.errors.requester_name" class="invalid-feedback d-block mb-6">{{ form.errors.requester_name }}</div>
                    </div>
                    <div class="inner">
                        <div v-for="(item, index) in form.requester_name" class="box">
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Requestor Information<span class="required">*</span></label>
                                    <input v-model="item.name" :class="{ 'is-invalid': form.errors[`requester_name.${index}.name`] }" type="text" placeholder="Requestor Name">
                                    <div v-if="form.errors[`requester_name.${index}.name]`]" class="invalid-feedback d-block">{{ form.errors[`requester_name.${index}.name]`] }}</div>
                                </div>
                                <div class="btn-group flex-nowrap">
                                    <a v-if="form.requester_name.length > 1" @click="removeRequestor(index)" class="btn btn-red btn-red--brounded" href="javascript:void(0)">
                                        <em class="fa-regular fa-xmark"></em>
                                    </a>
                                </div>
                            </div>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] mb-26"></span>
                        </div>
                    </div>
                    <div class="btn-group">
                        <a @click="addRequestor" class="btn btn-purple" href="javascript:void(0)"><em class="fa-regular fa-plus"></em><span>Add  Item</span></a>
                    </div>
                </div>
                <div><span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                    <div class="mb-20"><em class="fa-regular fa-gear"></em><span class="ml-[.5rem] text-15 font-medium">Add Item</span></div>
                    <div v-if="form.errors.items" class="invalid-feedback d-block mb-6">{{ form.errors.items }}</div>
                    <div class="has-add-box">
                        <div class="inner">
                            <div v-for="(item, index) in form.items" class="box">
                                <input type="hidden" v-model="item.id">
                                <div class="flex items-start select2-w-100">
                                    <div class="w-full flex gap-x-2 max-md:flex-col">
                                        <div class="form-group w-full">
                                            <label>Product<span class="required">*</span></label>
                                            <input v-model="item.product_name" :class="{ 'is-invalid': form.errors[`items.${index}.product_id`] }" type="text" disabled>
                                            <div v-if="form.errors[`items.${index}.product_id`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.product_id`] }}</div>
                                        </div>
                                        <div class="form-group w-full">
                                            <label>Ordered Quantity<span class="required">*</span></label>
                                            <input v-model="item.quantity" :class="{ 'is-invalid': form.errors[`items.${index}.quantity`] }" type="number" disabled>
                                            <div v-if="form.errors[`items.${index}.quantity`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.quantity`] }}</div>
                                        </div>
                                        <div class="form-group w-full">
                                            <label>Receive Quantity<span class="required">*</span></label>
                                            <input v-model="item.receive_quantity" :class="{ 'is-invalid': form.errors[`items.${index}.receive_quantity`] }" type="number">
                                            <div v-if="form.errors[`items.${index}.receive_quantity`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.receive_quantity`] }}</div>
                                        </div>
                                        <div class="btn-group flex-nowrap">
                                            <a v-if="form.items.length > 1" @click="removeItem(index, item)" class="btn btn-red btn-red--brounded" href="javascript:void(0)">
                                                <em class="fa-regular fa-xmark"></em>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] mb-26"></span>
                            </div>
                        </div>
                        <div class="btn-group">
                            <a @click="addItem" class="btn btn-purple" href="javascript:void(0)"><em class="fa-regular fa-plus"></em><span>Add  Item</span></a>
                        </div>
                    </div>
                </div>
                <div class="form-wrap">
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Save</button>
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
import axios from 'axios';

const props = defineProps({
    csrf: String,
    goodsReceipt: Object,
    orders: Array,
    products: Array,
});

const form = useForm({
    order_id: null,
    requester_name: [],
    items: [],
    delete_items: [],
});

const addItem = () => {
    form.items.push({
        id: null,
        product_id: null,
        quantity: 1,
        receive_quantity: 1,
    })
}

const removeItem = (index, item) => {
    form.items.splice(index, 1)

    if (item['id']) {
        form.delete_items.push(item['id'])
    }
}

const addRequestor = () => {
    form.requester_name.push({
        name: null,
    })
}

const removeRequestor = (index) => {
    form.requester_name.splice(index, 1)
}

onMounted(() => {
    if (props.goodsReceipt) {
    }
});


const fetchItems = async () => {
    form.items = [];

    if (form.order_id) {
        try {
            const response = await axios.get(`/orders/get-items/${form.order_id}`);
            form.items = response.data
            console.log(form.items[0].product_name);
            // if (Array.isArray(response.data)) {
            //     processes.value = response.data.map(process => ({
            //         id: process.id,
            //         text: process.name + ' | ' + selectedDepartment.value,
            //         name: process.name,
            //         department: process.type,
            //         standard_time_hour: process.standard_time_hour,
            //         standard_time_minute: process.standard_time_minute,
            //         manpower: process.manpower
            //     }));
            // } else {
            //     processes.value = [{
            //         id: response.data.id,
            //         text: response.data.name
            //     }];
            // }
        } catch (error) {
            console.error("Error fetching items:", error);
            Swal.fire("Error", "Failed to fetch items.", "error");
        }
    } else {
        console.log('select order');
        form.items = [];
    }
};

</script>
