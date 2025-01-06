<template>
    <div class="box rounded-md shadow-default mt-20 bg-white">
        <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
            <div class="text-18 font-medium">
                <span>Item Used</span>
            </div>
            <div class="flex flex-wrap gap-16 justify-between">
                <div class="search-el ml-auto">
                    <div class="txt">Search</div>
                    <div class="form">
                        <input type="search" placeholder="Search">
                    </div>
                </div>
                <div class="btn-group">
                    <a class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addRequirement" ref="openModal" href="javascript:void(0)"><i class="fa fa-add"></i> Add Material</a>
                    <a class="btn btn-purple" href="javascript:void(0)" @click="saveChanges"><i class="fa fa-check"></i> Save Changes</a>
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
                        Planned Qty
                    </th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in form.items_used" :key="index">
                        <td>{{ item.sku }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.uom }}</td>
                        <td>{{ item.planned_qty }}</td>
                        <td>
                            <div class="el-actions justify-start">
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
import Modal from "@/Pages/EngineeringOrder/BillOfMaterial/AddModal.vue";
import { ref, inject } from 'vue';

const props = defineProps({
    products: Object,
    order: Object
});

let search = ref(null);

const form = inject('form')

const destroy = (index) => {
    form.items_used.splice(index, 1);
}

const saveChanges = () => {
    Inertia.post(`/engineering-order/${props.order.id}/update-item-used`, {
        items: form.items_used
    }, {
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'Item used updated.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error!',
                text: 'There was an issue updating the Items used. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

</script>
