<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="product" class="big-title">Edit Item</div>
            <div v-else class="big-title">Create New Item</div>
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
                        <Link href="/products">List</Link>
                    </li>
                    <li>
                        <span v-if="product">Edit Item</span>
                        <span v-else>Create New Item</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="product ? form.post(`/products/${product.id}`) : form.post('/products')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>Product Information</span>
                            </div>
                            <!-- <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group"></div>
                                <div class="form-group">
                                    <PhotoUpload @update:file="handleFileChange"></PhotoUpload>
                                </div>
                            </div> -->
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Type</label>
                                    <Select2
                                        v-model="form.type"
                                        class="select2-w-100"
                                        :class="{ 'is-invalid': form.errors.type }"
                                        placeholder="Select Item Type"
                                        :options="[{ id: 'Inventory', text: 'Inventory' }, { id: 'Non-Inventory', text: 'Non-Inventory' }]"
                                    />
                                    <div v-if="form.errors.type" class="invalid-feedback d-block">{{ form.errors.type }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Category<span class="required">*</span></label>
                                    <Select2 v-model="form.category_id" :class="{ 'is-invalid': form.errors.category_id }" :options="categories" placeholder="Select Category"/>
                                    <div v-if="form.errors.category_id" class="invalid-feedback d-block">{{ form.errors.category_id }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Case Pack<span class="required">*</span></label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.case_pack }" v-model="form.case_pack" placeholder="Case Pack">
                                    <div v-if="form.errors.case_pack" class="invalid-feedback d-block">{{ form.errors.case_pack }}</div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Item Id<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.sku }" v-model="form.sku" placeholder="Item ID">
                                    <div v-if="form.errors.sku" class="invalid-feedback d-block">{{ form.errors.sku }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Lead Time<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.lead_time }" v-model="form.lead_time" placeholder="Lead Time">
                                    <div v-if="form.errors.lead_time" class="invalid-feedback d-block">{{ form.errors.lead_time }}</div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Item Name<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Item Name">
                                    <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Auto Re-Order<span class="required">*</span></label>
                                    <div class="flex flex-wrap gap-[3rem] items-center">
                                        <div class="custom-checkbox style-2">
                                            <input type="radio" v-model="form.auto_reorder" value="1" id="auto-1">
                                            <label for="auto-1">Yes</label>
                                        </div>
                                        <div class="custom-checkbox style-2">
                                            <input type="radio" v-model="form.auto_reorder" value="0" id="auto-0">
                                            <label for="auto-0">No</label>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.auto_reorder" class="invalid-feedback d-block">{{ form.errors.auto_reorder }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.description }" v-model="form.description" placeholder="Description">
                                    <div v-if="form.errors.description" class="invalid-feedback d-block">{{ form.errors.description }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Re-Order Level</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.reorder_level }" v-model="form.reorder_level" placeholder="0">
                                    <div v-if="form.errors.reorder_level" class="invalid-feedback d-block">{{ form.errors.reorder_level }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>UOM</label>
                                    <Select2 v-model="form.uom_id" :class="{ 'is-invalid': form.errors.uom_id }" :options="uom" placeholder="Select UOM"/>
                                    <div v-if="form.errors.uom_id" class="invalid-feedback d-block">{{ form.errors.uom_id }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Qty to Re-order</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.qty_reorder }" v-model="form.qty_reorder" placeholder="0">
                                    <div v-if="form.errors.qty_reorder" class="invalid-feedback d-block">{{ form.errors.qty_reorder }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Weight (Kg)</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.weight }" v-model="form.weight" placeholder="0">
                                    <div v-if="form.errors.weight" class="invalid-feedback d-block">{{ form.errors.weight }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Expenses Acc</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.expenses }" v-model="form.expenses" placeholder="0">
                                    <div v-if="form.errors.expenses" class="invalid-feedback d-block">{{ form.errors.expenses }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Dimension LxBxH</label>
                                    <div class="row">
                                        <div class="col">
                                            <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.dimension_l }" v-model="form.dimension_l" placeholder="L">
                                        </div>
                                        <div class="col">
                                            <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.dimension_w }" v-model="form.dimension_w" placeholder="B">
                                        </div>
                                        <div class="col">
                                            <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.dimension_h }" v-model="form.dimension_h" placeholder="H">
                                        </div>
                                    </div>
                                    <div v-if="form.errors.quantity_to_reorder" class="invalid-feedback d-block">{{ form.errors.quantity_to_reorder }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.remarks }" v-model="form.remarks">
                                    <div v-if="form.errors.remarks" class="invalid-feedback d-block">{{ form.errors.remarks }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Location</label>
                                    <div class="row">
                                        <div class="col">
                                            <Select2
                                                v-model="form.store_id"
                                                :class="{ 'is-invalid': form.errors.store_id }"
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
                                            <div v-if="form.errors.store_id" class="invalid-feedback d-block">
                                                {{ form.errors.store_id }}
                                            </div>
                                        </div>
                                        <div class="col">
                                            <Select2
                                                v-model="form.location_id"
                                                :class="{ 'is-invalid': form.errors.location_id }"
                                                placeholder="Select Location"
                                                :settings="{
                                                    ajax: {
                                                        url: '/data/locations',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: function (params) {
                                                            return {
                                                                store_id: form.store_id,
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
                                            <div v-if="form.errors.location_id" class="invalid-feedback d-block">
                                                {{ form.errors.location_id }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.quantity_to_reorder" class="invalid-feedback d-block">{{ form.errors.quantity_to_reorder }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">2</span>
                                <span>Product Price</span>
                                <div v-if="form.errors.prices" class="invalid-feedback d-block">{{ form.errors.prices }}</div>
                            </div>
                            <div class="table-add-row">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Vendor<span class="text-red">*</span></span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Price<span class="text-red">*</span></span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(price, index) in form.prices" :key="index">
                                            <td class="select2-w-100">
                                                <Select2 v-model="price.vendor_id" :class="{ 'is-invalid': form.errors[`prices.${index}.vendor_id`] }" :options="vendors" placeholder="Select Vendor"/>
                                                <div v-if="form.errors[`prices.${index}.vendor_id`]" class="invalid-feedback d-block">{{ form.errors[`prices.${index}.vendor_id`] }}</div>
                                            </td>
                                            <td>
                                                <div class="price-input-el" :class="{ 'is-invalid': form.errors[`prices.${index}.price`] }">
                                                    <input class="form-control" type="number" v-model="price.price" placeholder="Selling price">
                                                </div>
                                                <div v-if="form.errors[`prices.${index}.price`]" class="invalid-feedback d-block">{{ form.errors[`prices.${index}.price`] }}</div>

                                                <div class="price-input-el" :class="{ 'is-invalid': form.errors[`prices.${index}.lowest_cost`] }">
                                                    <input class="form-control" type="number" v-model="price.lowest_cost" placeholder="Lowest Cost">
                                                </div>
                                                <div v-if="form.errors[`prices.${index}.lowest_cost`]" class="invalid-feedback d-block">{{ form.errors[`prices.${index}.lowest_cost`] }}</div>

                                                <div class="price-input-el" :class="{ 'is-invalid': form.errors[`prices.${index}.last_cost`] }">
                                                    <input class="form-control" type="number" v-model="price.last_cost" placeholder="Last Cost">
                                                </div>
                                                <div v-if="form.errors[`prices.${index}.last_cost`]" class="invalid-feedback d-block">{{ form.errors[`prices.${index}.last_cost`] }}</div>

                                                <div class="price-input-el" :class="{ 'is-invalid': form.errors[`prices.${index}.highest_cost`] }">
                                                    <input class="form-control" type="number" v-model="price.highest_cost" placeholder="Highest Cost">
                                                </div>
                                                <div v-if="form.errors[`prices.${index}.highest_cost`]" class="invalid-feedback d-block">{{ form.errors[`prices.${index}.highest_cost`] }}</div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <a v-if="form.prices.length > 1" class="d-inline-block" href="javascript:void(0)" @click="delItem(index)">
                                                        <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="btn-group px-25 py-18">
                                    <a class="btn btn-purple btn-purple--brounded w-full add-row" href="javascript:void(0)" @click="addItem">
                                        <em class="icon fa-regular fa-plus"></em>
                                        <span>Add Vendor</span>
                                    </a>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 mt-30 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Selling Price</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.selling_price }" v-model="form.selling_price" placeholder="0">
                                    <div v-if="form.errors.selling_price" class="invalid-feedback d-block">{{ form.errors.selling_price }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Landed Cost</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.landed_cost }" v-model="form.landed_cost" placeholder="0">
                                    <div v-if="form.errors.landed_cost" class="invalid-feedback d-block">{{ form.errors.landed_cost }}</div>
                                </div>
                            </div>

                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">3</span><span>File Information</span></div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div>
                                    <div class="form-group">
                                        <label>Upload Item Photo</label>
                                        <div class="fileContainer">
                                            <label class="fileInputLabel" for="fileInput1">
                                                <input class="fileInput" id="fileInput1"
                                                       type="file"
                                                       :class="{ 'is-invalid': form.errors.photos }"
                                                       @input="form.photos.push($event.target.files[0])"
                                                       accept="image/png, image/jpg, image/jpeg">
                                                <span v-if="form.file_url">{{ form.file_url['name'] }}</span>
                                                <span v-else>Choose File</span>
                                                <span class="browse">Browse</span>
                                            </label>
                                            <div class="mt-12 text-[#82868B]">Allowed file types: png, jpg, jpeg.</div>
                                            <div class="files">
                                                <div v-for="(photo, index) in form.photos" :key="index" class="fileColumn">
                                                    <p class="name">
                                                        {{ photo.name }}
                                                        <i class="icon-remove fa-regular fa-xmark cursor-pointer" @click="delPhoto(index)"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group">
                                        <label>Part Book</label>
                                        <div class="fileContainer">
                                            <label class="fileInputLabel" for="fileInput2">
                                                <input class="fileInput" id="fileInput2"
                                                       type="file"
                                                       :class="{ 'is-invalid': form.errors.documents }"
                                                       @input="form.documents.push($event.target.files[0])"
                                                       accept="application/pdf">
                                                <span>Choose File</span>
                                                <span class="browse">Browse</span>
                                            </label>
                                            <div class="mt-12 text-[#82868B]">Allowed file types: pdf.</div>
                                            <div class="files">
                                                <div v-for="(document, index) in form.documents" :key="index" class="fileColumn">
                                                    <p class="name">
                                                        {{ document.name }}
                                                        <i class="icon-remove fa-regular fa-xmark cursor-pointer" @click="delDocument(index)"></i>
                                                    </p>
                                                </div>
                                                <div v-for="(document, index) in documents" :key="index" class="fileColumn">
                                                    <p class="name">
                                                        {{ document.name }}
                                                        <i class="icon-remove fa-regular fa-xmark cursor-pointer" @click="delExistDocument(index)"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="pull-right">
                                            <button type="button" class="btn btn-purple" name="button">+</button>
                                            <button type="button" class="btn btn-danger ml-20" name="button">-</button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="product" type="submit" :disabled="form.processing">Update Product</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create</button>
                        <Link class="btn btn-light btn-light--brounded" href="/products">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted} from "vue";
import PhotoUpload from "../../Components/PhotoUpload.vue";

const handleFileChange = (file) => {
    form.image_preview = file; // Set the file directly
};

const props = defineProps({
    product: Object,
    categories: Array,
    uom: Array,
    vendors: Array,
    prices: Object,
    documents: Object,
    csrf: String,
});

const form = useForm({
    type: null,
    category_id: null,
    sku: null,
    name: null,
    description: null,
    uom_id: null,
    weight: null,
    dimension_l: null,
    dimension_w: null,
    dimension_h: null,
    store_id: null,
    location_id: null,
    assembly: 0,
    case_pack: null,
    lead_time: null,
    auto_reorder: 0,
    reorder_level: null,
    quantity_to_reorder: null,
    remark: null,
    file_url: null,
    status: 1,
    selling_price:null,
    landed_cost:null,
    prices: [],
    documents: [],
    photos:[],
    delete_documents: [],
    image_preview: null
});

const addItem = () => {
    form.prices.push({
        id: null,
        vendor_id: null,
        price: null,
        errors: {}
    });
};

const delItem = (index) => {
    form.prices.splice(index, 1)
};

const delDocument = (index) => {
    form.documents.splice(index, 1)
};

const delPhoto = (index) => {
    form.photos.splice(index, 1)
};

const delExistDocument = (index) => {
    form.delete_documents.push(props.documents[0].id)
    props.documents.splice(index, 1)
};

onMounted(() => {
    if (props.product) {
        form.type = props.product.type;
        form.category_id = props.product.category_id;
        form.sku = props.product.sku;
        form.name = props.product.name;
        form.description = props.product.description;
        form.uom_id = props.product.uom_id;
        form.weight = props.product.weight;
        form.dimension_l = props.product.dimension_l;
        form.dimension_w = props.product.dimension_w;
        form.dimension_h = props.product.dimension_h;
        form.case_pack = props.product.case_pack;
        form.lead_time = props.product.lead_time;
        form.auto_reorder = props.product.auto_reorder;
        form.reorder_level = props.product.reorder_level;
        form.quantity_to_reorder = props.product.quantity_to_reorder;
        form.remark = props.product.remark;
        form.status = props.product.status;
        form.selling_price = props.product.selling_price;
        form.landed_cost = props.product.landed_cost;
        form.prices = props.prices;
    } else {
        form.prices.push({
            id: null,
            vendor_id: null,
            price: null,
            lowest_cost: null,
            last_cost: null,
            highest_cost: null,
            errors: {}
        });
    }
});
</script>
