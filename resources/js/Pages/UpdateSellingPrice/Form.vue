<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Update Selling Price</div>
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
                        <Link href="/uom">Update Selling Price</Link>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="form.post('/update-selling-price')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>General Information</span>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Category<span class="required">*</span></label>
                                        <Select2
                                            v-model="form.category_id"
                                            class="select2-w-100"
                                            :class="{ 'is-invalid': form.errors.category_id }"
                                            placeholder="Select Category"
                                            :options="categories"
                                            @select="(selected) => {
                                                form.errors.category_id = null
                                            }"
                                        />
                                        <div v-if="form.errors.category_id" class="invalid-feedback d-block">{{ form.errors.category_id }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Effective Date</label>
                                        <VueDatePicker @select="form.date = $event" v-model="form.effective_date" :format="$filters.format" :class="{ 'is-invalid': form.errors.effective_date }" :enable-time-picker="false" placeholder="Select date" :min-date="minDate"></VueDatePicker>
                                        <div v-if="form.errors.effective_date" class="invalid-feedback d-block">{{ form.errors.effective_date }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Input Type</label>
                                        <Select2
                                            v-model="form.input_type"
                                            class="select2-w-100"
                                            placeholder="Select Category"
                                            :options="[{id: 'Manual Input', text: 'Manual Input'}, {id: 'Upload File', text:'Upload File'}]"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.input_type == 'Manual Input'">
                                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                    <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">2</span>
                                    <span>Item List</span>
                                </div>

                                <div class="row" v-for="(item, index) in form.items">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Item<span class="required">*</span></label>
                                            <Select2
                                                v-model="item.product_id"
                                                :class="{ 'is-invalid': form.errors[`inventory_items.${index}.product_id`] }"
                                                placeholder="Select Product"
                                                :settings="{
                                                    ajax: {
                                                        url: '/data/products',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: function (params) {
                                                            return {
                                                                category_id: form.category_id,
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
                                                                        sku: item.sku,
                                                                        data: item,
                                                                    };
                                                                })
                                                            };
                                                        }
                                                    },
                                                    templateSelection: function (data) {
                                                        return data.sku ? data.sku : data.text;
                                                    },
                                                    templateResult: function (data) {
                                                        return data.text;
                                                    }
                                                }"
                                                @select="(selected) => {
                                                    const selectedProductIds = form.items.map(item => item.product_id);
                                                    if (isSelectedItemDuplicate(selected.id, selectedProductIds)) {
                                                        item.product_id = 0
                                                    } else {
                                                        item.current_selling_price = selected.data.selling_price
                                                        item.product_name = selected.data.name
                                                    }
                                                }"
                                            />
                                            <input type="text" readOnly="" v-model="item.product_name" disabled>
                                            <div v-if="form.errors[`items.${index}.product_id`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.product_id`] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Current Selling Price</label>
                                            <input type="text" readOnly="" v-model="item.current_selling_price" :class="{ 'is-invalid': form.errors[`items.${index}.current_selling_price`] }" placeholder="Selling Price" disabled>
                                            <div v-if="form.errors[`items.${index}.current_selling_price`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.current_selling_price`] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>New Selling Price</label>
                                            <input type="text" v-model="item.new_selling_price" :class="{ 'is-invalid': form.errors[`items.${index}.new_selling_price`] }" placeholder="Selling Price">
                                            <div v-if="form.errors[`items.${index}.new_selling_price`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.new_selling_price`] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <a v-if="form.items.length > 1" @click="removeItem(index, item)" class="btn btn-red btn-red--brounded mt-27" href="javascript:void(0)"><em class="fa-regular fa-xmark"></em><span>Delete</span></a>
                                    </div>
                                </div>
                                <a class="btn btn-purple" @click="addItem" href="javascript:void(0)"><em class="fa-regular fa-plus"></em><span>Add Item</span></a>
                            </div>

                            <div v-else>
                                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                    <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">2</span>
                                    <span>Upload File</span>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Item<span class="required">*</span></label>
                                            <input class="fileInput" id="fileInput1"
                                               type="file"
                                               :class="{ 'is-invalid': form.errors.file }"
                                               @input="form.file = $event.target.files[0];"
                                               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                            <span v-if="form.file">{{ form.file['name'] }}</span>
                                            <div v-if="form.errors.file" class="invalid-feedback d-block">{{ form.errors.file }}</div>
                                        </div>
                                    </div>
                                </div>
                                <a class="btn btn-purple" :href="`/update-selling-price/download-item-list/${form.category_id}`"><em class="fa-regular fa-download"></em><span>Download Item list</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Save</button>
                        <Link class="btn btn-light btn-light--brounded" href="/uom">Discard</Link>
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
import Swal from "sweetalert2";

const props = defineProps({
    categories: Array,
    csrf: String
});

const form = useForm({
    category_id: 0,
    effective_date: null,
    input_type: 'Manual Input',
    items: [{
        product_id: null,
        current_selling_price: null,
        new_selling_price: null
    }],
    file: null
});

const addItem = () => {
    form.items.push({
        product_id: null,
        current_selling_price: null,
        new_selling_price: null
    })
}

const isSelectedItemDuplicate = (selectedItemId, selectedItemIds) => {
    const normalizedId = String(selectedItemId);
    const occurrenceCount = selectedItemIds.filter(id => id === normalizedId).length;
    if (occurrenceCount > 1) {
        Swal.fire({
            title: "Cannot select the same item",
            icon: "warning",
            confirmButtonColor: "#ea5455",
            cancelButtonColor: "#009CDB",
            confirmButtonText: "OK!",
        })
    }
    return occurrenceCount > 1;
}

const removeItem = (index, item) => {
    form.items.splice(index, 1)
}

const downloadItemList = async () => {
    if (!form.category_id) {
        return form.errors.category_id = 'Select category'
    }

    try {
        const response = await axios.post('/update-selling-price/download-item-list', {
            category_id: form.category_id,
            _token: props.csrf,
        });
        console.log(response);
    } catch (error) {
        alert(error)
    }
}

</script>
