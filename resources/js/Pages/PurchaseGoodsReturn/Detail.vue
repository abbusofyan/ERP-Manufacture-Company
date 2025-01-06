<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ goodsReturn.code }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/goods-receipts">Goods Return Detail</Link>
                    </li>
                    <li class="active"><span>{{ goodsReturn.code }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <form @submit.prevent="submitItemForm">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box">
                            <div class="text-18 font-medium flex justify-between gap-20 pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                Goods Receipt Information
                            </div>
                            <div class="flex mt-36 max-md:flex-col gap-[8rem]">
                                <table class="table-1-el w-full">
                                    <tbody>
                                        <tr>
                                            <th>GRN number</th>
                                            <td>
                                                <div class="text-purple font-semibold">
                                                    <Link :href="`/goods-receipts/${goodsReturn.goods_receipt.id}`"><span>{{ goodsReturn.goods_receipt.code }}</span></Link>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ $filters.formatDateTime(goodsReturn.goods_receipt.created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Supplier</th>
                                            <td>{{ goodsReturn.goods_receipt.order.vendor.name }}</td>
                                        </tr>
                                        <tr>
                                            <th>PO Number</th>
                                            <td>
                                                <div class="text-purple font-semibold">
                                                    <Link :href="`/orders/${goodsReturn.goods_receipt.order.id}`"><span>{{ goodsReturn.goods_receipt.order.code }}</span></Link>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Requester</th>
                                            <td>{{ goodsReturn.goods_receipt.requester_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <div class="el-tag" :class="goodsReturn.status_class">
                                                    {{ goodsReturn.status_text }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>DO Number</th>
                                            <td>{{ formItems.do_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{goodsReturn.created_by.name}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="w-full md:max-w-[20.6rem]" v-if="goodsReturn.qr_path">
                                    <div class="image">
                                        <img :src="`/public/storage${goodsReturn.qr_path}`" alt="qr">
                                    </div>
                                    <a class="mt-10 text-[#82868B] block text-[1.2rem]" href="#">Click to download QR</a>
                                </div>
                            </div>
                            <div>
                                <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                                <div class="mb-20">
                                    <em class="fa-regular fa-gear"></em><span class="ml-[.5rem] text-15 font-medum">Ordered Items</span>
                                    <div v-if="formItems.errors[`items`]" class="invalid-feedback d-block">{{ formItems.errors[`items`] }}</div>
                                </div>
                                <div class="has-add-box">
                                    <div class="inner">
                                        <div v-for="(item, index) in formItems.items" :key="index" class="box">
                                            <div class="items-start select2-w-100 mb-0 pb-0">
                                                <div class="w-full row">
                                                    <div class="form-group col-md-6 col-lg-6 w-full">
                                                        <label>Product<span class="required">*</span></label>
                                                        <input v-model="item.product_name" type="text" disabled>
                                                    </div>
                                                    <div class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Ordered Quantity<span class="required">*</span></label>
                                                        <input v-model="item.quantity" type="number" disabled>
                                                    </div>
                                                    <div class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Received Quantity<span class="required">*</span></label>
                                                        <input v-model="item.receive_quantity" type="number" disabled>
                                                    </div>
                                                    <div class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Return Quantity<span class="required">*</span></label>
                                                        <input v-model="item.return_qty" :class="{ 'is-invalid': formItems.errors[`items.${index}.return_qty`] }" type="number" :disabled="goodsReturn.status == 2">
                                                        <div v-if="formItems.errors[`items.${index}.return_qty`]" class="invalid-feedback d-block">{{ formItems.errors[`items.${index}.return_qty`] }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-checkbox style-1">
                                                        <input type="checkbox" :id="`new-item-${index}`" v-model="item.need_replacement" :disabled="goodsReturn.status == 1 ? 0 : 1">
                                                        <label :for="`new-item-${index}`">Need Replacement</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] mb-26"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group">
                            <Link class="btn btn-purple" href="/purchase-goods-return"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                            <button v-if="goodsReturn.status == 1" type="submit" class="btn btn-purple"><span class="icon fa-regular fa-check"></span>Save</button>
                            <div v-if="Object.keys(formItems.errors).length > 0" class="invalid-feedback d-block">There was a validation error!</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {computed, onBeforeMount, onMounted, onUpdated, ref} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Form from "@/Pages/StockAdjustment/Form.vue";
import {useToast} from "vue-toastification";

const permissions = computed(() => usePage().props.value.auth.user.permissions);
const toast = useToast();
const created = computed(() => usePage().props.value.flash.created);

const props = defineProps({
    goodsReturn: Object,
});

const formItems = useForm({
    goods_receipt_id: null,
    goods_return_id: null,
    do_number: null,
    items: [],
})

const submitItemForm = () => {
    formItems.put(`/purchase-goods-return/${props.goodsReturn.id}`, {
        preserveScroll: true,
    })
};

onUpdated(() => {
    if (created.value) {
        $.fancybox.close()
        toast.success(created.value, {
            icon: "fa-solid fa-check",
            timeout: 3000,
        });
    }
})

onMounted(() => {
    formItems.goods_receipt_id = props.goodsReturn.goods_receipt_id
    formItems.goods_return_id = props.goodsReturn.id
    formItems.do_number = props.goodsReturn.goods_receipt.do_number
    props.goodsReturn.goods_receipt.items.forEach((item, i) => {
        formItems.items.push({
            ...item,
            return_qty: item.return_qty ?? 0,
            need_replacement: false
        });
    });
});
</script>
