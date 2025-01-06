<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Assembly to FG</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/finish-goods">Assembly to FG</Link>
                    </li>
                    <li>
                        <span>Detail Assembly to FG</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                <span>FG Information</span>
            </div>
            <table class="table-1-el w-full">
                <tr>
                    <th>Convert No</th>
                    <td>{{ finishGoodsTransaction.code }}</td>
                </tr>
                <tr>
                    <th>Finish Group ID</th>
                    <td>{{ finishGoodsTransaction.finish_goods.code }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ finishGoodsTransaction.finish_goods.assembly.product.name }}</td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>{{ finishGoodsTransaction.finish_goods.category.name }}</td>
                </tr>
                <tr>
                    <th>Convert Qty</th>
                    <td>{{ finishGoodsTransaction.convert_qty }}</td>
                </tr>
                <tr>
                    <th>Created By</th>
                    <td>{{ finishGoodsTransaction.created_by.name }}</td>
                </tr>
            </table>
        </div>
        <div class="box rounded-md shadow-default mt-20 bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <div class="text-18 font-medium">
                    <span>Bill of Material</span>
                </div>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table select-rows">
                        <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>Required Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in form.materials" :key="index">
                                <td>{{ item.product.sku }}</td>
                                <td>
                                    <b class="text-purple">
                                        <Link :href="`/products/${item.product.id}`"><span>{{ item.product.name }}</span></Link>
                                    </b>
                                </td>
                                <td>{{ item.product.uom.code }}</td>
                                <td>{{ item.qty }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- <FinishGoodsItemsTable :data="finishGoods.items"></FinishGoodsItemsTable> -->
        </div>
        <div class="box py-20 px-25 mt-20 rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="btn-group">
                    <Link class="btn btn-purple" href="/finish-goods"><i class="fa fa-chevron-left text-11"></i>Back</Link>
                    <!-- <Link class="btn btn-light btn-light--brounded" :href="`/finish-goods/${finishGoods.id}/edit`">Edit</Link> -->
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import FinishGoodsItemsTable from "../../Components/FinishGoodsItemsTable.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted, onBeforeMount, ref, provide} from "vue";

const props = defineProps({
    finishGoodsTransaction: Object,
});

const form = useForm({
    name: null,
    category: null,
    uom:null,
    materials: []
});

provide('form', form)

onMounted(() => {
    if (props.finishGoodsTransaction) {
        form.name = props.finishGoodsTransaction.finish_goods.name
        form.category = props.finishGoodsTransaction.finish_goods.category
        form.uom = props.finishGoodsTransaction.finish_goods.uom

        form.materials = props.finishGoodsTransaction.finish_goods.assembly.materials.map(material => {
            return {
                'product': material.product,
                'qty': material.qty * props.finishGoodsTransaction.convert_qty
            };
        });
    }
})

</script>
