<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="transfer" class="big-title">Item Transfer</div>
            <div v-else class="big-title">Item Transfer</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/products">Inventory</Link>
                    </li>
                    <li>
                        <span>Item Transfer</span>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="form.post('/transfers')">
            <div class="box pt-20 px-25 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center select2-w-100 gap-3">
                                            <label>Origin Store<span class="required">*</span></label>
                                            <div class="flex-fill" :class="{ 'is-invalid form-control': form.errors.origin_store_id }">
                                                <Select2
                                                    v-model="form.origin_store_id"
                                                    :class="{ 'is-invalid': form.errors.origin_store_id }"
                                                    placeholder="Select Origin Warehouse"
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
                                                <div v-if="form.errors.origin_store_id" class="invalid-feedback d-block">{{ form.errors.origin_store_id }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center select2-w-100 gap-3">
                                            <label>Destination Store<span class="required">*</span></label>
                                            <div class="flex-fill" :class="{ 'is-invalid form-control': form.errors.destination_store_id }">
                                                <Select2
                                                    v-model="form.destination_store_id"
                                                    :class="{ 'is-invalid': form.errors.destination_store_id }"
                                                    placeholder="Select Destination Warehouse"
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
                                                <div v-if="form.errors.destination_store_id" class="invalid-feedback d-block">{{ form.errors.destination_store_id }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center select2-w-100 gap-3">
                                            <label>Remarks</label>
                                            <div class="flex-fill" :class="{ 'is-invalid form-control': form.errors.remarks }">
                                                <textarea class="form-control" type="text" :class="{ 'is-invalid': form.errors.remarks }" v-model="form.remarks" placeholder="Type Remark"></textarea>
                                                <div v-if="form.errors.remarks" class="invalid-feedback d-block">{{ form.errors.remarks }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box pt-20 px-25 rounded-md shadow-default bg-white mt-18">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] mb-[2.6rem] border-solid border-[#EBE9F1]">
                                <span>List Of Products</span>
                            </div>
                            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                                <label class="d-flex align-items-center gap-1">Show
                                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </label>
                                <div class="flex flex-wrap gap-16 justify-between">
                                    <div class="search-el ml-auto">
                                        <div class="txt">Search</div>
                                        <div class="form">
                                            <input type="search" placeholder="Search" v-model="search">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive pb-0" v-if="products">
                                <table class="table select-rows">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Product</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <!-- <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th> -->
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Store Location</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <span>Action</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-if="!products || products.data == 0">
                                        <td colSpan="5" style="text-align: center; vertical-align: middle;">No product in the selected warehouse</td>
                                    </tr>
                                    <tr v-else v-for="(item, index) in products.data" :key="index">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="img-30">
                                                    <img v-if="item.product.photos.length > 0" class="image float-right" :src="'/' + item.product.photos[0].path" alt="qr">
                                                    <img v-else :src="`/images/no-image.png`" alt="">
                                                </div>
                                                <div>
                                                    <div class="text-bold">
                                                        {{ item.product.name }}
                                                    </div>
                                                    <small class="d-block">
                                                        Price $ {{ item.cost_price }}
                                                    </small>
                                                    <small class="d-block">
                                                        Stock: {{ item.stock }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ item.product.sku }}
                                        </td>
                                        <!-- <td>
                                            <div class="qty-custom max-w-150">
                                                <span class="minus" @click="decrementQuantity(item)"></span>
                                                <input class="form-control max-w-[24.5rem]"
                                                       type="number"
                                                       v-model="item.quantity_change"
                                                       placeholder="0"
                                                       :class="{ 'is-invalid form-control': item.quantity_change_error ? item.quantity_change_error : null }"
                                                       @input="validateQty(item)">
                                                <span class="plus" @click="incrementQuantity(item)"></span>
                                            </div>
                                            <div v-if="item.quantity_change_error" class="invalid-feedback d-block"></div>
                                        </td> -->
                                        <td>
                                            {{ item.store.name }}
                                        </td>
                                        <td>
                                            <a
                                                class="btn btn-purple"
                                                href="javascript:void(0)"
                                                @click="handleButtonClick(item)">
                                                +&nbsp;&nbsp;Add
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="products.data" class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                                <p>Showing {{ products.from }} to {{ products.to }} of {{ products.total }} entries</p>
                                <Pagination :fetch-store-product="fetchStoreProduct" :links="products.links"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box pt-20 px-25 rounded-md shadow-default bg-white mt-18">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] mb-[2.6rem] border-solid border-[#EBE9F1]">
                                <span>Transfer Summary</span>
                            </div>
                            <div class="table-responsive pb-0" v-if="form.selected_store_product">
                                <table class="table select-rows">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Product</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Stock</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Store Location</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <span>Action</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in form.selected_store_product" :key="index">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="img-30">
                                                    <img v-if="item.product.photos.length > 0" class="image float-right" :src="'/' + item.product.photos[0].path" alt="qr">
                                                    <img v-else :src="`/images/no-image.png`" alt="">
                                                </div>
                                                <div>
                                                    <div class="text-bold">
                                                        {{ item.product.name }}
                                                    </div>
                                                    <small class="d-block">
                                                        Price $ {{ item.cost_price }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ item.product.sku }}
                                        </td>
                                        <td>
                                            {{ item.stock }}
                                        </td>
                                        <td>
                                            <div class="qty-custom max-w-150">
                                                <span class="minus" @click="decrementQuantity(item)"></span>
                                                <input class="form-control max-w-[24.5rem]"
                                                       type="number"
                                                       v-model="item.quantity_change"
                                                       placeholder="0"
                                                       :class="{ 'is-invalid form-control': item.quantity_change_error ? item.quantity_change_error : null }"
                                                       @input="validateQty(item)"
                                                       >
                                                <span class="plus" @click="incrementQuantity(item)"></span>
                                            </div>
                                            <div v-if="form.errors[`selected_store_product.${index}.quantity_change`]" class="invalid-feedback d-block">{{ form.errors[`selected_store_product.${index}.quantity_change`] }}</div>
                                        </td>
                                        <td>
                                            {{ form.destination_store_name }}
                                            <div v-if="form.errors.destination_store_id" class="invalid-feedback d-block">{{ form.errors.destination_store_id }}</div>
                                        </td>
                                        <td>
                                            <a
                                                class="btn btn-red"
                                                href="javascript:void(0)"
                                                @click="form.selected_store_product.splice(index,1)">
                                                &times;&nbsp;&nbsp;Remove
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box pt-10 px-25 pb-10 rounded-md shadow-default bg-white mt-18 card-box-shadow">
                                <div class="form-wrap">
                                    <div class="row">
                                        <div class="col">
                                            <div class="btn-group">
                                                <button class="btn btn-lg btn-purple w-100" type="submit" :disabled="form.processing">Save as Draft</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
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
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted, ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import debounce from "lodash.debounce";
import Pagination from "../../Components/AjaxPagination.vue";
import Swal from "sweetalert2";

const props = defineProps({
    transfer: Object,
    storage_items: Object,
    filters: Object,
    stores: Array,
    csrf: String,
});

const form = useForm({
    origin_store_id: null,
    destination_store_id: null,
    destination_store_name: null,
    remarks: null,
    selected_store_product: [],
});

const products = ref({})

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

// const filter = () => {
//     if (!props.transfer) {
//         Inertia.get(
//             "/transfers/create",
//             {
//                 origin_store_id: form.origin_store_id,
//                 destination_store_id: form.destination_store_id,
//                 selected_store_product: form.selected_store_product,
//                 search: search.value,
//                 order: order.value,
//                 by: by.value,
//                 paginate: paginate.value,
//             },
//             {
//                 preserveState: true,
//                 preserveScroll: true,
//                 replace: true,
//             }
//         );
//     }
// };

watch(
    search,
    debounce(() => {
        fetchStoreProduct();
    }, 500)
);

// watch([order, by, paginate], () => {
//     filter();
// });

watch(() => form.origin_store_id, (newVal) => {
    if (isSelectSameWarehouse()) {
        return form.errors.origin_store_id = 'Cant select the same warehouse'
    }

    form.errors.origin_store_id = null
    form.errors.destination_store_id = null

    if (!props.transfer) {
        form.selected_store_product = [];
        // filter()
        fetchStoreProduct()
    }
});

const fetchStoreProduct = async (page = 0) => {
    const response = await axios.post(`/stores/${form.origin_store_id}/get-products`, {
        _token: props.csrf,
        store_id: form.origin_store_id,
        search: search.value,
        order: order.value,
        by: by.value,
        paginte: paginate.value,
        page: page
    });
    products.value = response.data
}

watch(() => form.destination_store_id, (newVal) => {
    const location = props.stores.find(location => Number(location.id) === Number(form.destination_store_id));
    form.destination_store_name = location ? location.name : null;
});

watch(() => form.destination_store_id, (newVal) => {
    if (isSelectSameWarehouse()) {
        form.errors.destination_store_id = 'Cant select the same warehouse'
        return
    }
    form.errors.origin_store_id = null
    form.errors.destination_store_id = null

    const location = props.stores.find(location => Number(location.id) === Number(form.destination_store_id));
    form.destination_store_name = location ? location.name : null;
});

const sort = (data) => {
    order.value = data;

    if (by.value == "asc") {
        by.value = "desc";
    } else {
        by.value = "asc";
    }
};

const validateQty = (item) => {
    return;
    if (Number(item.quantity_change) > Number(item.quantity)) {
        item.quantity_change = item.quantity;
    }
};

const decrementQuantity = (item) => {
    item.quantity_change = item.quantity_change > 1 ? item.quantity_change - 1 : 1;
};

const incrementQuantity = (item) => {
    item.quantity_change = item.quantity_change + 1;
};

const handleButtonClick = (item) => {
    // if (item.quantity_change > item.stock) {
    //     return Swal.fire({
    //         title: "Cannot transfer item",
    //         text: "Transfer quantity cannot exceed available stock",
    //         icon: "warning",
    //         confirmButtonColor: "#ea5455",
    //         cancelButtonColor: "#009CDB",
    //         confirmButtonText: "OK!",
    //     })
    // }
    // /** Check quantity **/
    // if (!item.quantity_change || Number(item.quantity_change) <= 0) {
    //     item.quantity_change_error = true;
    //     return;
    // }
    //
    // /** Check exist **/
    // const existingItem = form.selected_store_product.find(existingItem => existingItem.id === item.id);
    // if (existingItem) {
    //     item.quantity_change_error = null;
    //     let new_qty = existingItem.quantity + item.quantity_change;
    //     if (Number(new_qty) > Number(item.quantity)) {
    //         new_qty = item.quantity;
    //     }
    //     existingItem.quantity = new_qty;
    // } else {
    //     form.selected_store_product.push({...item, quantity: item.quantity_change});
    //     item.quantity_change_error = null;
    // }

    form.selected_store_product.push({...item, quantity: 0});
};

onMounted(() => {
    if (props.filters.origin_store_id) {
        form.origin_store_id = props.filters.origin_store_id;
    }

    if (props.filters.destination_store_id) {
        form.destination_store_id = props.filters.destination_store_id;
    }
    if (props.storage_items) {
        props.storage_items.data.forEach(item => {
            item.quantity_change = 0
        })
    }

    if (props.transfer) {
        if (props.transfer.transfer_items) {
            props.transfer.transfer_items.forEach((item, i) => {
                item.storage_item.quantity = item.quantity
                form.selected_store_product.push(item.storage_item)
            });
        }
    }
});

const isSelectSameWarehouse = () => {
    if (form.origin_store_id && form.destination_store_id && form.origin_store_id === form.destination_store_id) {
        return true;
    } else {
        return false;
    }
}
</script>
