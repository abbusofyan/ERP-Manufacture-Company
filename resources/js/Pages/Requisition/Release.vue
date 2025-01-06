<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                Release Goods Issue Note
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li class="active">
                        <Link href="/requisitions-to-goods-issue-note">
                            <span>Requisition to Goods Issue Note</span>
                        </Link>
                    </li>
                    <li class="active">
                        <span>Release Goods Issue Note</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="form.post('/requisitions-release')">
            <div class="div pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div>
                    <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span>Item Information</span></div>
                    <div class="items grid md:grid-cols-2 gap-30">
                        <div v-for="(item, index) in form.items" :key="index" class="item p-24 border-[1px] border-solid border-[#EBE9F1] rounded-[.5rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th class="!w-1/2">PR No</th>
                                    <td>{{ item.code }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">Item ID</th>
                                    <td>{{ item.product_id }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">Item Name</th>
                                    <td>{{ item.product_name }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">UOM</th>
                                    <td>{{ item.uom }}</td>
                                </tr>
                                <tr>
                                    <th class="!w-1/2">Order Quantity</th>
                                    <td>{{ item.order_quantity }}</td>
                                </tr>
                            </table>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="mt-[2.6rem]">
                                <label>Choose Release Type<span class="required">*</span></label>
                                <div class="grid md:grid-cols-2 gap-[7.7rem] mt-6">
                                    <div class="form-group">
                                        <div class="custom-checkbox style-2">
                                            <input type="radio" :id="`release-inventory-${index}`" value="1" v-model="item.type">
                                            <label :for="`release-inventory-${index}`">Release to Inventory</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-checkbox style-2">
                                            <input type="radio" :id="`release-pr-${index}`" value="0" v-model="item.type">
                                            <label :for="`release-pr-${index}`">Release to PR</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template v-if="item.type === '0'">
                                <div class="form-group select2-w-100">
                                    <label>PR Number<span class="required">*</span></label>
                                    <Select2 :options="requisitions" v-model="item.pr_number" placeholder="Select" required></Select2>
                                    <div v-if="form.errors[`items.${index}.pr_number`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.pr_number`] }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Quantity<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors[`items.${index}.pr_quantity`] }" v-model="item.pr_quantity" placeholder="Input Amount">
                                    <div v-if="form.errors[`items.${index}.pr_quantity`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.pr_quantity`] }}</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                <div class="btn-group mt-[5.6rem]">
                    <button class="btn btn-purple" type="submit" :disabled="form.processing">Save</button>
                    <Link class="btn btn-light btn-light--brounded" href="/requisitions-to-goods-issue-note">Discard</Link>
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
    requisition_items: Array,
    requisitions: Array,
});

const form = useForm({
    items: [],
});

onMounted(() => {
    if (props.requisition_items) {
        props.requisition_items.forEach(item => {
            form.items.push({
                code: item.requisition.code,
                requisition_item_id: item.id,
                product_id: item.product ? item.product.sku : '',
                product_name: item.product_name,
                uom: item.product ? item.product.uom.code : '',
                order_quantity: item.quantity,
                type: '1',
                pr_number: null,
                pr_quantity: null,
            })
        })
    }
});
</script>
