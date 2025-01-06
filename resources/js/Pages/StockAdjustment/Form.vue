<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Item Adjustment</div>
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
                        <span>Item Adjustment</span>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="stockAdjustment ? form.post(`/stock-adjustments/${stockAdjustment.id}`) : form.post('/stock-adjustments')" v-if="permissions.includes('create-stock')">
            <div class="box pt-20 px-25 pb-10 rounded-md shadow-default bg-white mt-18">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <div class="select2-w-100">
                                            <label>Select Adjustment Type</label>
                                            <div class="d-flex align-items-center form-select position-relative gap-3" :class="{ 'is-invalid': form.errors.stock_adjustment_type_id }">
                                                <div class="flex-fill">
                                                    <Select2
                                                        v-model="form.stock_adjustment_type_id"
                                                        placeholder="Search"
                                                        :options="typeOptions"
                                                        :settings="{
                                                            ajax: {
                                                                url: '/data/stock-adjustment-type',
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
                                                                         results: data.map(function (adj_type) {
                                                                            return {
                                                                                text: `${adj_type.code}`,
                                                                                    id: adj_type.id,
                                                                                    data: adj_type,
                                                                                };
                                                                         })
                                                                    };
                                                                }
                                                            }
                                                        }"
                                                    />
                                                </div>
                                            </div>
                                            <div v-if="form.errors.stock_adjustment_type_id" class="invalid-feedback d-block">{{ form.errors.stock_adjustment_type_id }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="select2-w-100">
                                    <label>Reason</label>
                                    <div class="d-flex align-items-center gap-3" :class="{ 'is-invalid': form.errors.reason }">
                                        <div class="flex-fill">
                                            <input type="text" v-model="form.reason" :class="{ 'is-invalid': form.errors.reason }">
                                        </div>
                                    </div>
                                    <div v-if="form.errors.reason" class="invalid-feedback d-block">{{ form.errors.reason }}</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="select2-w-100">
                                    <label>Add Product(s)</label>
                                    <div class="d-flex align-items-center gap-3" :class="{ 'is-invalid': form.errors.product_ids }">
                                        <div class="flex-fill">
                                            <Select2
                                                placeholder="Search"
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
                                                                        data: item,  // Ensure this is included
                                                                    };
                                                                }),
                                                            };
                                                        },
                                                    },
                                                }"
                                                @select="(selected) => {
                                                    selectedProduct = selected
                                                }"
                                            />

                                        </div>
                                        <a
                                            @click="addItems"
                                            class="btn btn-purple"
                                            href="javascript:void(0)">
                                            Add Product
                                        </a>
                                    </div>
                                    <div v-if="form.errors.product_ids" class="invalid-feedback d-block">{{ form.errors.product_ids }}</div>
                                </div>
                            </div>

                            <!-- Table for displaying added products -->
                            <div class="table-responsive border-0 border-t-[1px] border-solid border-[#EBE9F1] mt-[2.6rem] pt-20 pb-0">
                                <table class="table select-rows">
                                    <thead>
                                    <tr>
                                        <td class="pl-0">Product</td>
                                        <td>UOM</td>
                                        <td>Location</td>
                                        <td>Adj</td>
                                        <td>Quantity</td>
                                        <td>Before Qty</td>
                                        <td>After Qty</td>
                                        <td>Cost Price</td>
                                        <td></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in form.items" :key="index">
                                        <td class="max-w-[50rem]">{{ item.product_name }}</td>
                                        <td class="max-w-[24.5rem] pl-0">{{ item.product?.uom?.code }}</td>
                                        <td style="padding:0; padding-right:10px;">
                                            <div class="form-select flex-fill select2-w-100" :class="{ 'is-invalid': form.errors[`items.${index}.store_id`] }">
                                                <!-- <Select2
                                                    :placeholder="item.warehouseOptions.length > 0 ? `Select Warehouse` : `No warehouse available`"
                                                    v-model="item.store_id"
                                                    :options="item.warehouseOptions"
                                                    @select="(selected) => {
                                                        console.log(selected.data)
                                                    }"
                                                /> -->
                                                <Select2
                                                    v-model="item.store_id"
                                                    placeholder="------ Select Warehouse -------"
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
                                                        fetchQty(item.product.id, selected.id, index)
                                                    }"
                                                />
                                            </div>
                                            <div v-if="form.errors[`items.${index}.store_id`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.store_id`] }}</div>
                                        </td>
                                        <td style="padding:0; padding-right:10px;" class="w-px-100">
                                            <select
                                                v-model="item.adjustment"
                                                class="form-select"
                                                :class="{ 'is-invalid': form.errors[`items.${index}.adjustment`] }"
                                                @change="calculateAfterQty(index)"
                                            >
                                                <option value="+">+</option>
                                                <option value="-">-</option>
                                            </select>
                                            <div v-if="form.errors[`items.${index}.adjustment`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.adjustment`] }}</div>
                                        </td>
                                        <td style="padding:0; padding-right: 10px" class="w-px-150">
                                            <input type="number" v-model.number="item.adjustment_qty" class="form-control" @input="calculateAfterQty(index)"/>
                                            <div v-if="form.errors[`items.${index}.adjustment_qty`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.adjustment_qty`] }}</div>
                                        </td>
                                        <td style="padding:0; padding-right: 10px" class="w-px-150">
                                            <input type="number" v-model.number="item.before_qty" class="form-control" readonly/>
                                        </td>
                                        <td style="padding:0; padding-right: 10px" class="w-px-150">
                                            <input type="number" v-model.number="item.after_qty" class="form-control" readonly/>
                                        </td>
                                        <td style="padding:0;" class="w-px-150">
                                            <template v-if="item.adjustment == '-'">
                                                <div v-if="item.price">
                                                    <strong>${{ item.price }}</strong>
                                                </div>
                                                <div v-else>
                                                    <small>There is no price for this product.</small>
                                                </div>
                                            </template>
                                            <input v-else type="text" v-model.number="item.price" class="form-control" :class="{ 'is-invalid': form.errors[`items.${index}.price`] }" readonly/>
                                            <div v-if="form.errors[`items.${index}.price`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.price`] }}</div>
                                        </td>
                                        <td style="padding:0; padding-left: 10px;">
                                            <button @click="removeItem(index)" class="btn btn-danger">Remove</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] mt-[2.6rem]">
                                <div class="form-group">
                                    <label>Approved By<span class="required">*</span></label>
                                    <Select2
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.approved_by }"
                                        v-model="form.approved_by"
                                        placeholder="Search"
                                        :options="approvedByOptions"
                                        :settings="{
                                            ajax: {
                                                url: '/data/users',
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
                                                                text: `${item.name}`,
                                                                id: item.id,
                                                                data: item,
                                                            };
                                                        })
                                                    };
                                                }
                                            }
                                        }"
                                    />
                                    <div v-if="form.errors.approved_by" class="invalid-feedback d-block">{{ form.errors.approved_by }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Upload Documents</label>
                                    <input type="file" class="form-control-file"
                                           :class="{ 'is-invalid': form.errors.file_url }"
                                           @input="form.file_url = $event.target.files[0];"
                                           accept="application/pdf">
                                    <label class="note">Allowed file types: pdf.</label>
                                    <div v-if="form.errors.file_url" class="invalid-feedback d-block">{{ form.errors.file_url }}</div>
                                </div>
                            </div>
                            <div class="btn-group pb-25">
                                <Link class="btn btn-light btn-light--brounded" href="/stock-adjustments">Cancel</Link>
                                <button class="btn btn-purple" type="submit" :disabled="form.processing">Save</button>
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
import { useForm, Link, usePage } from "@inertiajs/inertia-vue3";
import { computed, onMounted, ref } from "vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    csrf: String,
    stockAdjustment: Object,
});

const form = useForm({
    stock_adjustment_type_id: null,
    reason: null,
    items: [],
    approved_by: null,
    file_url: null,
});

const selectedProduct = ref(null);
const approvedByOptions = ref([]);
const typeOptions = ref([]);

const addItems = () => {
    if (selectedProduct.value) {
        const product = selectedProduct.value;
        form.items.push({
            product: product.data,
            product_name: product.data.name,
            product_id: product.id,
            adjustment_qty: 0,
            store_id: null,
            adjustment: "+",
            // before_qty: product.data.quantity_after_committed ?? 0,
            // after_qty: product.data.quantity_after_committed ?? 0,
            before_qty: 0,
            after_qty: 0,
            prices: product.data.prices,
            price: product.data.prices?.[0]?.price ?? 0,
            warehouseOptions: [],
        });
        fetchWarehouses(product.id, form.items.length - 1);
    }
};

const calculateAfterQty = (index) => {
    const item = form.items[index];
    const adjustmentQty = item.adjustment === "+" ? item.adjustment_qty : -item.adjustment_qty;
    item.after_qty = (item.before_qty ?? 0) + adjustmentQty;
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const fetchWarehouses = (productId, itemIndex) => {
    if (productId) {
        axios
            .post("/data/store-has-product", {
                product_id: productId,
                _token: props.csrf,
            })
            .then((response) => {
                const options = response.data.map((storeProduct) => ({
                    id: storeProduct.store_id,
                    text: storeProduct.store.name,
                    data: storeProduct,
                }));

                if (itemIndex >= 0 && form.items[itemIndex]) {
                    form.items[itemIndex].warehouseOptions = options.length
                        ? options
                        : [];
                }
            })
            .catch((error) => {
                console.error("Error fetching warehouses:", error);
                form.errors.global = "Error fetching warehouses. Please try again later.";
            });
    }
};

onMounted(() => {
    if (props.stockAdjustment) {
        form.reason = props.stockAdjustment.reason;

        props.stockAdjustment.items.forEach((item, i) => {
            form.items.push({
                product: item.product,
                product_name: item.product.name,
                product_id: item.product_id,
                adjustment_qty: item.adjustment_qty,
                store_id: item.store_id,
                adjustment: item.adjustment,
                before_qty: parseInt(item.before_qty), // Dynamically calculate
                after_qty: parseInt(item.after_qty),   // Dynamically calculate
                price: item.price ?? 0,
                warehouseOptions: [],
            });
            fetchWarehouses(item.product_id, form.items.length - 1);
        });

        if (props.stockAdjustment?.approved_by) {
            form.approved_by = props.stockAdjustment.approved_by.id;
            approvedByOptions.value.push({
                id: props.stockAdjustment.approved_by.id,
                text: props.stockAdjustment.approved_by.name,
            });
        }

        if (props.stockAdjustment?.type) {
            form.stock_adjustment_type_id = props.stockAdjustment.type.id;
            typeOptions.value.push({
                id: props.stockAdjustment.type.id,
                text: `${props.stockAdjustment.type.code} | ${props.stockAdjustment.type.name}`,
            });
        }
    }
});

const fetchQty = async (product_id, warehouse_id, index) => {
    const response = await axios.get(`/products/${product_id}/${warehouse_id}/get-stock-by-warehouse`);
    form.items[index].before_qty = response.data ? response.data : 0
};
</script>
