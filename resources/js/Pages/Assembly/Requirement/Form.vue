<template>
    <div class="box rounded-md shadow-default mt-20 bg-white">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <div class="text-18 font-medium">
                <span>Requirements</span>
            </div>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search">
                    </div>
                </div>
                <div class="btn-group">
                    <a class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addRequirement" ref="openModal" href="javascript:void(0)"><i class="fa fa-add"></i> Add Requirements</a>
                </div>

                <Modal :products="products"></Modal>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table select-rows">
                <thead>
                <tr>
                    <th>
                        Item No
                    </th>
                    <th>
                        Item Name
                    </th>
                    <th>
                        UOM
                    </th>
                    <th>
                        Qty
                    </th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(requirement, index) in form.requirements" :key="index">
                        <td>{{ requirement.product.sku }}</td>
                        <td>{{ requirement.product.name }}</td>
                        <td>{{ requirement.product.uom.code }}</td>
                        <td>{{ requirement.qty }}</td>
                        <td>
                            <div class="el-actions justify-end">
                                <a class="text-red" href="javascript:void(0);" @click="destroy(index)"><em class="fa-regular fa-trash-can"></em></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">

        </div>
    </div>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {onMounted} from "vue";
import Swal from "sweetalert2";
import Modal from "@/Pages/Assembly/Requirement/AddModal.vue";
import { ref, inject } from 'vue';

const props = defineProps({
    products: Array,
});

let search = ref(null);

const form = inject('form')

const destroy = (index) => {
    form.requirements.splice(index, 1);
}

</script>
