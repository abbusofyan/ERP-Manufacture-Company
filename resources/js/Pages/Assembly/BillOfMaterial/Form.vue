<template>
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
                <div class="btn-group">
                    <a v-if="products.length > 0" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addRequirement" ref="openModal" href="javascript:void(0)"><i class="fa fa-add"></i> Add Material</a>
                </div>

                <Modal :products="products" :csrf="csrf"></Modal>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table select-rows">
                <thead>
                <tr>
                    <th>
                        Item ID
                    </th>
                    <th>
                        Item Name
                    </th>
                    <th>
                        UOM
                    </th>
                    <th>
                        Quantity
                    </th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-if="form.errors.materials && form.materials.length == 0">
                        <td class="text-danger text-center" colSpan="5">{{form.errors.materials}}</td>
                    </tr>
                    <tr v-else v-for="(material, index) in form.materials" :key="index">
                        <td>{{ material.product.sku }}</td>
                        <td>
                            <b class="text-purple">
                                <Link :href="`/products/${material.product.id}`"><span>{{ material.product.name }}</span></Link>
                            </b>
                        </td>
                        <td>{{ material.product.uom.code }}</td>
                        <td>
                            <input v-if="$page.url.split('/').pop() === 'edit'" type="text" class="col-3" name="" v-model="material.qty">
                            <span v-else>{{material.qty}}</span>
                        </td>
                        <td>
                            <div class="el-actions justify-end">
                                <a class="text-red" href="javascript:void(0);" @click="destroy(index)"><em class="fa-regular fa-trash-can"></em></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {onMounted} from "vue";
import Swal from "sweetalert2";
import Modal from "@/Pages/Assembly/BillOfMaterial/AddModal.vue";
import { ref, inject } from 'vue';

const props = defineProps({
    products: Array,
    csrf: String
});

let search = ref(null);

const form = inject('form')

const destroy = (index) => {
    form.materials.splice(index, 1);
}

</script>
