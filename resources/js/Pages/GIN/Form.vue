<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                Return To Inventory
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/goods-issue-notes">Good Issue Note</Link>
                    </li>
                    <li class="active">
                        <span>Return To Inventory</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="form.post(`/goods-issue-notes/${gin.id}/return`)">
            <div class="div pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div>
                    <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span>Good Issue Note ({{ gin.code }})</span></div>
                    <div class="text-15 font-medium pb-17 mb-17">
                        <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                        <span>Item Information</span>
                    </div>
                    <div class="items grid md:grid-cols-2 gap-30">
                        <div class="item p-24 border-[1px] border-solid border-[#EBE9F1] rounded-[.5rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th class="!w-1/2">Item ID</th>
                                    <td>{{ gin.requisition_item.product ? gin.requisition_item.product.sku : '' }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">Item Name</th>
                                    <td>{{ gin.requisition_item.product_name }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">UOM</th>
                                    <td>{{ gin.requisition_item.product ? gin.requisition_item.product.uom.code : '' }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">Order Quantity</th>
                                    <td>{{ gin.requisition_item.quanity }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">Issue Quantity</th>
                                    <td>{{ gin.quantity }}</td>
                                </tr>
                            </table>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="form-group">
                                <label>Serial Number<span class="required">*</span></label>
                                <input type="text" v-model="form.serial" :class="{'is-invalid' : form.errors.serial}" placeholder="Input Serial Number">
                                <div v-if="form.errors.serial" class="invalid-feedback d-block">{{ form.errors.serial }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                <div class="text-15 font-medium pb-17 mb-17">
                    <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2</span>
                    <span>Reason</span>
                </div>
                <div class="form-group">
                    <label>Reason<span class="required">*</span></label>
                    <textarea type="text" v-model="form.reason" :class="{'is-invalid' : form.errors.reason}" placeholder="Reason"></textarea>
                    <div v-if="form.errors.reason" class="invalid-feedback d-block">{{ form.errors.reason }}</div>
                </div>
                <div class="btn-group mt-[5.6rem]">
                    <button class="btn btn-purple" type="submit" :disabled="form.processing">Save</button>
                    <Link class="btn btn-light btn-light--brounded" href="/goods-issue-notes">Discard</Link>
                </div>
                <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
            </div>
        </form>

    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted, ref, watch} from "vue";

const props = defineProps({
    gin: Object,
});

const form = useForm({
    serial: null,
    reason: null,
});

onMounted(() => {
    if (props.requisition_items) {
        props.requisition_items.forEach(item => {
            form.items.push({
                requisition_id: item.requisition.id,
                product_id: item.product ? item.product.sku : '',
                product_name: item.product_name,
                uom: item.product ? item.product.uom.code : '',
                order_quantity: item.quantity,
                quantity: null,
            })
        })
    }
});
</script>
