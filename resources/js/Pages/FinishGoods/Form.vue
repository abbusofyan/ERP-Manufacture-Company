<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Create Assembly to FG</div>
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
                        <span>Create Assembly to FG</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="finishGoods ? form.put(`/finish-goods/${finishGoods.id}`) : form.post('/finish-goods')">
            <div class="box pt-20 px-25 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box border-[#EBE9F1]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span>Add Assembly to FG</span>
                            </div>
                            <!-- <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Convert No <span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.convert_no }" v-model="form.convert_no" disabled>
                                    <div v-if="form.errors.convert_no" class="invalid-feedback d-block">{{ form.errors.convert_no }}</div>
                                </div>
                            </div> -->
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Assembly ID<span class="required">*</span></label>
                                    <Select2
                                        v-model="form.assembly_id"
                                        :class="{ 'is-invalid': form.errors.assembly_id }"
                                        placeholder="Select Assembly"
                                        :settings="{
                                            ajax: {
                                                url: '/data/assemblies',
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
                                                        results: data
                                                        .map(function (assembly) {
                                                            if (!assembly.code) return null;
                                                            return {
                                                                text: `${assembly.code} | ${assembly.product ? assembly.product.name : ''}`,
                                                                id: assembly.id,
                                                                data: assembly,
                                                            };
                                                        })
                                                        .filter(Boolean)
                                                    };
                                                },
                                            },
                                            templateSelection: function (item) {
                                                return item.data ? item.data.code : item.text;
                                            }
                                        }"
                                        @select="(selected) => {
                                            form.code = selected.data.code + 'FG';
                                            form.selling_price = selected.data.product.selling_price;
                                            form.assembly_name = selected.data.product.name
                                            form.category_id = selected.data.product.category_id
                                            form.store_id = null
                                            fetchAssemblyItems(selected.id)
                                        }"
                                    />
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.code }" v-model="form.assembly_name" disabled>
                                    <div v-if="form.errors.assembly_id" class="invalid-feedback d-block">{{ form.errors.assembly_id }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Finish Group ID<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.code }" v-model="form.code" disabled>
                                    <div v-if="form.errors.code" class="invalid-feedback d-block">{{ form.errors.code }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Warehouse ID<span class="required">*</span></label>
                                    <Select2
                                        v-model="form.store_id"
                                        :class="{ 'is-invalid': form.errors.store_id }"
                                        placeholder="Select Warehouse"
                                        :options="default_store"
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
                                        @select="(selected) => {
                                            fetchQtyOnStock(selected.id)
                                        }"
                                    />
                                    <div v-if="form.errors.store_id" class="invalid-feedback d-block">{{ form.errors.store_id }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Category<span class="required">*</span></label>
                                    <Select2
                                        :options="categories"
                                        v-model="form.category_id"
                                        :class="{ 'is-invalid': form.errors.category_id }"
                                        placeholder="Select Category"
                                    />
                                    <div v-if="form.errors.category_id" class="invalid-feedback d-block">{{ form.errors.category_id }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Selling Price<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.selling_price }" v-model="form.selling_price">
                                    <div v-if="form.errors.selling_price" class="invalid-feedback d-block">{{ form.errors.selling_price }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Convert Qty<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.convert_qty }" v-model="form.convert_qty" placeholder="input qty" @input="setConvertQty">
                                    <div v-if="form.errors.convert_qty" class="invalid-feedback d-block">{{ form.errors.convert_qty }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box rounded-md shadow-default mt-20 bg-white">
                <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                    <div class="text-18 font-medium">
                        <span>Bill of Material</span>
                        <div v-if="form.errors[`items.${0}.qty_on_stock`]" class="invalid-feedback d-block">There is insufficient stock.</div>
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
                                    <th>Convert Qty</th>
                                    <th>Qty on Stock</th>
                                    <th>Last Cost Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="form.assembly_id && form.items.length == 0">
                                    <td colSpan="6" class="text-center text-danger">No material found for this assembly</td>
                                </tr>
                                <tr v-else v-for="(item, index) in form.items" :key="index">
                                    <td>{{ item.item_id }}</td>
                                    <td>
                                        <b class="text-purple">
                                            <Link :href="`/products/${item.product_id}`"><span>{{ item.item_name }}</span></Link>
                                        </b>
                                    </td>
                                    <td>{{ item.uom }}</td>
                                    <td>{{ item.required_qty }}</td>
                                    <td>{{ item.convert_qty }}</td>
                                    <td>
                                        {{ item.qty_on_stock }}
                                        <div v-if="form.errors[`items.${index}.qty_on_stock`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.qty_on_stock`] }}</div>
                                    </td>
                                    <td>${{item.last_cost ? item.last_cost : 0}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-26 pb-25 px-25">
                    <Pagination v-if="form.items.data" :links="form.items.links"/>
                </div>
            </div>

            <div class="box shadow-default mt-20 bg-white" v-if="insufficientItems.length > 0">
                <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                    <div class="text-18 font-medium">
                        <span>Insufficient Items</span>
                        <div v-if="form.errors[`items.${0}.qty_on_stock`]" class="invalid-feedback d-block">There is insufficient stock.</div>
                    </div>
                </div>

                <div class="row box py-24 px-24 rounded-md text-danger">
                    <div class="col-md-12 col-lg-8 col-12">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Required Qty</th>
                                    <th>Convert Qty</th>
                                    <th>Qty on stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in insufficientItems" :key="item.id">
                                    <td>{{ item.item_name }}</td>
                                    <td>{{ item.required_qty }}</td>
                                    <td>{{ item.convert_qty }}</td>
                                    <td>{{ item.qty_on_stock }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="box shadow-default mt-20 bg-white">
                <div class="py-20 px-25 mt-20 rounded-md shadow-default bg-white">
                    <div class="form-wrap">
                        <div class="btn-group">
                            <button
                                class="btn btn-purple"
                                type="button"
                                :disabled="form.processing"
                                @click="confirmSubmit"
                                >
                                Save
                            </button>
                            <!-- <button class="btn btn-purple" type="submit" :disabled="form.processing">Save</button> -->
                            <Link class="btn btn-light btn-light--brounded" href="/assembly">Discard</Link>
                        </div>
                        <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                    </div>
                </div>
            </div>


        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import Pagination from "../../Components/Pagination.vue";
import {onMounted, onBeforeMount, ref, provide, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import debounce from "lodash.debounce";
import axios from 'axios';
import {useToast} from "vue-toastification";
import Swal from "sweetalert2";

const toast = useToast();

const props = defineProps({
    products: Array,
    finishGoods: Object,
    csrf: String,
    filters: Object,
    categories: Array,
    default_store: Array
});

const form = useForm({
    convert_no: null,
    assembly_id: null,
    code: null,
    category_id: null,
    store_id: null,
    selling_price: null,
    convert_qty: 1,
    items: []
});

const fetchAssemblyItems = async (assembly_id) => {
    const response = await axios.get(`/assembly/${assembly_id}/getItems`);
    form.items = response.data.map(item => ({
        product_id: item.product.id,
        item_id: item.product.sku,
        item_name: item.product.name,
        uom: item.product.uom.code,
        required_qty: item.qty,
        convert_qty: item.qty * form.convert_qty,
        qty_on_stock: 0,
    }));
};

const fetchQtyOnStock = async (warehouse_id) => {
    const response = await axios.post(`/finish-goods/${warehouse_id}/fetch-item-qty`, {
        items: form.items,
        _token: props.csrf,
    });
    form.items = response.data
};

onMounted(() => {
    if (props.finishGoods) {
        form.code = props.finishGoods.code,
        form.name = props.finishGoods.name
        form.category = props.finishGoods.category
        form.uom = props.finishGoods.uom
        form.materials = props.finishGoods.items.map(material => {
            return {
                'product': material.product,
                'qty': material.qty
            };
        });
    }
})

const setConvertQty = () => {
    for (let i = 0; i < form.items.length; i++) {
        let item = form.items[i];
        let convert_qty = item.required_qty * form.convert_qty;
        form.items[i].convert_qty = convert_qty
    }
}

const confirmSubmit = () => {
    if (form.assembly_id && !form.items.length) {
        return Swal.fire({
            title: "FG cannot be created, there is no BOM item in this Assembly",
            icon: "warning",
            confirmButtonColor: "#ea5455",
            cancelButtonColor: "#009CDB",
            confirmButtonText: "OK!",
        })
    }

    if (form.store_id && form.items) {
        return checkInsufficientItems()
    }

    form.post('/finish-goods')
}

const insufficientItems = ref([]);
const checkInsufficientItems = () => {
    insufficientItems.value = [];

    form.items.forEach((item, i) => {
        if (item.convert_qty > item.qty_on_stock) {
            insufficientItems.value.push(item)
        }
    });
}
</script>

<style>
.table-container {
    max-height: 400px; /* Adjust as needed */
    overflow-y: auto;
}

.table-responsive {
    display: inline-block;
    width: 100%; /* Ensures responsive table width */
    overflow-x: auto; /* Horizontal scrolling */
}

.table thead th {
    position: sticky;
    top: 0;
    background-color: white;
    z-index: 1;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
}
.styled-table th, .styled-table td {
    padding: 8px;
    text-align: left;
}
.styled-table thead th {
    border-bottom: 1px solid black;
}
.styled-table tbody tr {
    border-bottom: 1px solid black;
}
</style>
