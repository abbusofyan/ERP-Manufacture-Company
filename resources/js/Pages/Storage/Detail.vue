<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ storage.code }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/storages">Storage</Link>
                    </li>
                    <li>
                        <span>View</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">
                    <div class="box">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid"><i class="fa fa-info text-bold text-15"></i></span>
                            <span>Storage Information</span>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="submitForm">
                <!-- <form @submit.prevent="formAssignToBin.post(`/storages/${storage.id}/assign-to-bin/${selectedItemId}`)"> -->
                    <div class="modal fade" id="assignToBinModal" role="dialog" style="overflow:hidden;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content p-24 pt-36 pb-30">
                                <div class="modal-header">
                                    <div class="col-md-12 mt-3 text-center">
                                        <div class="text-24 text-bold-500">
                                            Assign to Bin
                                        </div>
                                        <div class="text-12 text-bold-500">
                                            Select store and location
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Store</label>
                                        <Select2
                                            required
                                            class="select2-w-100"
                                            v-model="formAssignToBin.store_id"
                                            :class="{ 'is-invalid': formAssignToBin.errors.store_id }"
                                            placeholder="Select Warehouse"
                                            :settings="{
                                                ajax: {
                                                    url: '/data/warehouse',
                                                    dataType: 'json',
                                                    method: 'POST',
                                                    data: function (params) {
                                                        return {
                                                            search: params.term,
                                                            _token: csrf,
                                                        };
                                                    },
                                                    processResults: function (data) {
                                                        return {
                                                            results: data.map(function (warehouse) {
                                                                return {
                                                                        text: `${warehouse.name}`,
                                                                        id: warehouse.id,
                                                                        data: warehouse,
                                                                    };
                                                                })
                                                            };
                                                        },
                                                    },
                                                }"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Location</label>
                                        <Select2
                                            required
                                            class="select2-w-100"
                                            v-model="formAssignToBin.location_id"
                                            :class="{ 'is-invalid': formAssignToBin.errors.location_id }"
                                            placeholder="Select Location"
                                            :settings="{
                                                ajax: {
                                                    url: '/data/locations',
                                                    dataType: 'json',
                                                    method: 'POST',
                                                    data: function (params) {
                                                        return {
                                                            store_id: formAssignToBin.store_id,
                                                            search: params.term,
                                                            _token: csrf,
                                                        };
                                                    },
                                                    processResults: function (data) {
                                                        return {
                                                            results: data.map(function (location) {
                                                                return {
                                                                        text: `${location.name}`,
                                                                        id: location.id,
                                                                        data: location,
                                                                    };
                                                                })
                                                            };
                                                        },
                                                    },
                                                }"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Quantity<span class="required">*</span></label>
                                        <input class="form-control" type="number" v-model="formAssignToBin.stock" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Assign Quantity<span class="required">*</span></label>
                                        <input class="form-control" type="number" :class="{ 'is-invalid': formAssignToBin.errors.qty }" v-model="formAssignToBin.qty" required>
                                        <div v-if="formAssignToBin.errors.qty" class="invalid-feedback d-block">{{ formAssignToBin.errors.qty }}</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 mb-3 text-center">
                                        <button class="btn btn-purple mr-10" type="submit" :disabled="formAssignToBin.processing">Submit</button>
                                        <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div v-if="form.grouped">
                    <div>
                        <div class="table-responsive">
                            <table class="table select-rows">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>NO.</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Item Name</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Quantity<label><span class="required">*</span></label></span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Cost Price<label><span class="required">*</span></label></span>
                                        </div>
                                    </th>
                                    <th>
                                        <span>Action</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in form.grouped" :key="index">
                                    <td>
                                        {{ index + 1 }}
                                    </td>
                                    <td>
                                        <div class="text-purple font-semibold">
                                            <Link :href="`/products/${item.product.id}`"><span>{{ item.product.name }}</span></Link>
                                        </div>
                                    </td>
                                    <td>
                                        {{ item.quantity }}
                                    </td>
                                    <td>
                                        $ {{ item.cost_price }}
                                    </td>
                                    <td>
                                        <div class="more" v-if="item.quantity > 0">
                                            <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                            <ul class="more-panel">
                                                <li>
                                                    <a data-bs-toggle="modal" data-bs-target="#assignToBinModal" ref="openModal" href="javascript:void(0)" @click="setSelectedItemId(index)" ><span class="icon fa fa-bucket"></span><span>Assign to Bin</span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <Link class="btn btn-purple" href="/storages">Back</Link>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    storage: Object,
    csrf: String,
});

const form = useForm({
    grouped: [],
});

const formAssignToBin = useForm({
    store_id: null,
    location_id: null,
    qty: null,
    stock:null
});

const selectedItemId = ref(null);
const closeModal = ref(null);

const setSelectedItemId = (index) => {
    formAssignToBin.stock =  form.grouped[index].quantity
    selectedItemId.value = form.grouped[index].id;
};

onMounted(() => {
    form.grouped = props.storage.storage_items;
})

const submitForm = () => {
    formAssignToBin.post(`/storages/${props.storage.id}/assign-to-bin/${selectedItemId.value}`, {
        onSuccess: () => {
            closeModal.value.click();
            Inertia.reload();
        }
    });
};
</script>
