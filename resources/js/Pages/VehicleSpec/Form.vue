<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="spec" class="big-title">Edit Vehicle Specification</div>
            <div v-else class="big-title">Create Vehicle Specification</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/vehicle-spec">Vehicle Specification</Link>
                    </li>
                    <li>
                        <span v-if="spec">Edit Vehicle Specification</span>
                        <span v-else>Create Vehicle Specification</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="spec ? form.put(`/vehicle-spec/${spec.id}`) : form.post('/vehicle-spec')">
            <div class="box pt-20 px-25 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box border-[#EBE9F1]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span>Vehicle Specification Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Name<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Name">
                                    <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-2" v-if="form.tab === 1">
                <div class="box rounded-md shadow-default mt-20 bg-white">
                    <div class="type-box">
                        <template v-for="(group, groupIndex) in form.groups" :key="`group${groupIndex}`">
                            <div class="box-item mb-17">
                                <span class="text-18"> <b>{{ group.group_name }}</b> </span><br><br>
                                <template v-for="(item, itemIndex) in group.items">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <Select2
                                                    v-model="item.type"
                                                    class="select2-w-100"
                                                    :class="{ 'is-invalid': form.errors.type }"
                                                    placeholder="Select Item Type"
                                                    :options="[ { id: 'Single Item', text: 'Single Item' }, { id: 'Assembly Item', text: 'Assembly Item' } ]"
                                                />
                                                <div v-if="form.errors[`groups.${groupIndex}.detail.type`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.detail.type`] }}</div>
                                            </div>
                                        </div>

                                        <template v-if="group.group_name !== 'OTHERS'">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Item ID</label>
                                                    <Select2
                                                        v-model="item.item_id"
                                                        placeholder="Select Item"
                                                        :options="item.selectOption ?? null"
                                                        :settings="{
                                                            ajax: {
                                                                url: '/data/vehicle-spec/get-item-options',
                                                                dataType: 'json',
                                                                method: 'POST',
                                                                data: function (params) {
                                                                    return {
                                                                        item_type: item.type,
                                                                        search: params.term,
                                                                        _token: csrf,
                                                                    };
                                                                },
                                                                processResults: function (data, params) {
                                                                    return {
                                                                        results: data.map(function (item) {
                                                                            return {
                                                                                text: (item.sku ? item.sku : item.code),
                                                                                id: item.id,
                                                                                data: item,
                                                                            };
                                                                        })
                                                                    };
                                                                }
                                                            },
                                                            placeholder: 'Select Item',
                                                        }"
                                                        @select="data => {
                                                            item.product_name = data.data.product ? data.data?.product?.name : data.data?.name;
                                                            item.description = data.data.product ? data.data?.product?.description : data.data?.description;
                                                        }"
                                                    />
                                                    <div v-if="form.errors[`groups.${groupIndex}.detail.item_id`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.detail.item_id`] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Item Name</label>
                                                    <input type="text" class="form-control" v-model="item.product_name" disabled>
                                                    <div v-if="form.errors[`groups.${groupIndex}.detail.product_name`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.detail.product_name`] }}</div>
                                                </div>
                                            </div>
                                        </template>

                                        <template v-else>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Item Name</label>
                                                    <input type="text" class="form-control" v-model="item.product_name">
                                                    <div v-if="form.errors[`groups.${groupIndex}.detail.product_name`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.detail.product_name`] }}</div>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="text" class="form-control" v-model="item.qty">
                                                <div v-if="form.errors[`groups.${groupIndex}.detail.qty`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.detail.qty`] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-1" v-if="group.items.length > 1">
                                            <label>&nbsp;</label>
                                            <div class="form-group mb-0">
                                                <button type="button" class="btn btn-danger" @click="deleteItem(groupIndex, itemIndex)">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Remark</label>
                                                <input type="text" class="form-control" v-model="item.remark">
                                                <div v-if="form.errors[`groups.${groupIndex}.detail.remark`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.detail.remark`] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea
                                                    class="form-control"
                                                    v-model="item.description"
                                                    placeholder="Enter description"
                                                    rows="3"
                                                    style="resize: vertical;"
                                                ></textarea>
                                                <div v-if="form.errors[`groups.${groupIndex}.detail.description`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.detail.description`] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button class="btn btn-purple gap-[7.7rem]" type="button" @click="addItem(groupIndex)">
                                    <span class="icon"><img src="../../../assets/img/add.svg" alt="add"></span>
                                    Add Item
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="box py-20 px-25 mt-20 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="btn-group">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                        <Link class="btn btn-light btn-light--brounded" href="/vehicle-spec">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";
import { onMounted } from "vue";

const props = defineProps({
    spec: Object,
    headers: Object,
    csrf: String,
});

const form = useForm({
    tab: 1,
    name: null,
    groups: [],
});

const addItem = (groupIndex) => {
    form.groups[groupIndex].items.push({
        type: '',
        item_id: '',
        product_name: '',
        qty: '',
        remark: '',
        description: '',
    });
};

onMounted(() => {
    if (props.spec) {
        form.name = props.spec.name;
        form.groups = props.headers.map(header => ({
            header_id: header.id,
            group_name: `${header.name}`,
            items: header.items.length > 0
                ? header.items.map(item => ({
                    type: item.product_id ? 'Single Item' : 'Assembly Item',
                    item_id: item.product_id ? item.product_id : item.assembly_id || null,
                    product_name: item.product_name || (item.product_id ? item.material?.name : item.assembly?.name) || null,
                    qty: item.qty,
                    remark: item.remark || '',
                    description: item.description || '',
                    material: item.material,
                    assembly: item.assembly,
                    selectOption: item.material ? [{id: item.material.id, text: item.material.sku}] : (item.assembly ? [{id: item.assembly.id, text: item.assembly.code}] : null),
                }))
                : [{
                    type: null,
                    item_id: null,
                    product_name: null,
                    qty: null,
                    remark: null,
                    description: '',
                }]
        }));
    } else {
        form.groups = props.headers.map(header => ({
            header_id: header.id,
            group_name: `${header.name}`,
            items: [{
                type: null,
                item_id: null,
                product_name: null,
                qty: null,
                remark: null,
                description: '',
            }]
        }));
    }
});

const deleteItem = (groupIndex, itemIndex) => {
    form.groups[groupIndex].items.splice(itemIndex, 1);
};
</script>
