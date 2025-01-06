<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                Converting to Goods Issue Note
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li class="active">
                        <span>Converting to Goods Issue Note</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="form.post('/requisitions-to-goods-issue-note')">
            <div class="div pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div>
                    <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span>Item Information</span></div>
                    <div class="items grid md:grid-cols-2 gap-30">
                        <div v-for="(item, index) in form.items" :key="index" class="item p-24 border-[1px] border-solid border-[#EBE9F1] rounded-[.5rem]">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th class="!w-1/2">PR No</th>
                                    <td>{{ item.requisition_id }}</td>
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
                            <div class="form-group">
                                <label>Issuing Quantity<span class="required">*</span></label>
                                <input type="text" v-model="item.quantity" :class="{'is-invalid' : form.errors[`items.${index}.quantity`]}" placeholder="Input Amount">
                                <div v-if="form.errors[`items.${index}.quantity`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.quantity`] }}</div>
                            </div>
                            <p class="-mt-15">Item Left: {{ item.product ? item.product.stock : 0 }}</p>
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
    vendors: Array,
});

const form = useForm({
    items: [],
});

onMounted(() => {
    if (props.requisition_items) {
        props.requisition_items.forEach(item => {
            form.items.push({
                requisition_item_id: item.id,
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
