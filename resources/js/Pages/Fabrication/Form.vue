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
                        <span>Create Engineering Order</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="form.post('/fabrication')">
            <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                    <span>Create Engineering Order</span>
                    <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                </div>

                <div class="form-group col-lg-12">
                    <label>Work Order Type<span class="required">*</span></label>
                    <Select2
                        v-model="form.type"
                        :options="[{ id: 'Fabrication', text: 'Fabrication' }, { id: 'RnD', text: 'RnD' }]"
                        placeholder="Select Type"
                        required
                        style="width: 100%;">
                    </Select2>
                </div>
                <div class="grid md:grid-cols-2 gap-[3rem]">
                    <div class="col-12">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                            <span>Input</span>
                            <button class="btn btn-purple btn-sm" type="button" @click="addInput()">Add Input</button>
                        </div>
                        <div v-for="(input, inputIndex) in form.item_input" :key="inputIndex">
                            <div class="row">
                                <div class="col-7">
                                    <div class="form-group">
                                        <Select2
                                        style="width: 100%;"
                                        v-model="input.product_id"
                                        placeholder="Select Item"
                                        :settings="{
                                            ajax: {
                                                url: '/data/products',
                                                dataType: 'json',
                                                method: 'POST',
                                                data: function (params) {
                                                    return {
                                                        search: params.term,
                                                        _token: csrf,
                                                    };
                                                },
                                                processResults: function (data, params) {
                                                    return {
                                                        results: data.map(function (item) {
                                                            return {
                                                                text: `${item.sku} | ${item.name}`,
                                                                id: item.id,
                                                                data: item,
                                                            };
                                                        })
                                                    };
                                                }
                                            }
                                        }"
                                        />
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <input class="form-control" type="text" v-model="input.qty" placeholder="Qty">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                            <span>Output</span>
                            <button class="btn btn-purple btn-sm" type="button" @click="addOutput()">Add Output</button>
                        </div>
                        <div v-for="(output, outputIndex) in form.item_output" :key="outputIndex">
                            <div class="row">
                                <div class="col-7">
                                    <div class="form-group">
                                        <Select2
                                        style="width: 100%;"
                                        v-model="output.product_id"
                                        placeholder="Select Item"
                                        :settings="{
                                            ajax: {
                                                url: '/data/products',
                                                dataType: 'json',
                                                method: 'POST',
                                                data: function (params) {
                                                    return {
                                                        search: params.term,
                                                        _token: csrf,
                                                    };
                                                },
                                                processResults: function (data, params) {
                                                    return {
                                                        results: data.map(function (item) {
                                                            return {
                                                                text: `${item.sku} | ${item.name}`,
                                                                id: item.id,
                                                                data: item,
                                                            };
                                                        })
                                                    };
                                                }
                                            }
                                        }"
                                        />
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <input class="form-control" type="text" v-model="output.qty" placeholder="Qty">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {useForm} from '@inertiajs/inertia-vue3';


const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    csrf: String,
});

const form = useForm({
    type: null,
    item_input: [{
        product_id: '',
        qty: ''
    }],
    item_output: [{
        product_id: '',
        qty: ''
    }],
});

const addInput = () => {
    form.item_input.push({
        product_id: '',
        qty: ''
    });
};

const addOutput = () => {
    form.item_output.push({
        product_id: '',
        qty: ''
    });
};

</script>
