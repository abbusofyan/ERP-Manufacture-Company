<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Engineering Order</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="#">Engineering Order</Link>
                    </li>
                    <li>
                        <span>Fabrication / R&D</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="assembly ? form.put(`/assembly/${order.id}`) : form.post('/assembly')">
            <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                    <span>Engineering Order Detail</span>
                </div>
                <div class="row">
                    <div class="col">
                        <table class="table-1-el w-full">
                            <tr>
                                <th class="text-nowrap">Engineering Order No</th>
                                <td class="pl-15"> : {{ order.code }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Type</th>
                                <td class="pl-15"> : {{ order.type }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="box rounded-md shadow-default mt-20 bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                            <div class="text-18 font-medium">
                                <span>Input Items</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table select-rows">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                    <th>UOM</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in props.input_items" :key="index">
                                        <td>{{ item.product.sku }}</td>
                                        <td>{{ item.product.name }}</td>
                                        <td>{{ item.qty }}</td>
                                        <td>{{ item.product.uom.code }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="box rounded-md shadow-default mt-20 bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                            <div class="text-18 font-medium">
                                <span>Output Items</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table select-rows">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                    <th>UOM</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in props.output_items" :key="index">
                                        <td>{{ item.product.sku }}</td>
                                        <td>{{ item.product.name }}</td>
                                        <td>{{ item.qty }}</td>
                                        <td>{{ item.product.uom.code }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted, onBeforeMount, ref, provide} from "vue";

const props = defineProps({
    order: Object,
    input_items: Object,
    output_items: Object,
});

</script>
