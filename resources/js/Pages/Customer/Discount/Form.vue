<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="discount" class="big-title">Edit Discount</div>
            <div v-else class="big-title">New Discount</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/customers">Customer</Link>
                    </li>
                    <li>
                        <Link :href="`/customers/${customer.id}`">{{ customer.name }}</Link>
                    </li>
                    <li>
                        <span v-if="discount">Edit Discount</span>
                        <span v-else>New Discount</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="discount ? form.put(`/customers/${customer.id}/discounts/${discount.id}`) : form.post(`/customers/${customer.id}/discounts`)">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span>Discount Info</span>
                                <span class="pl-10" v-if="discount && discount.is_default">
                                    <a href="javascript:void(0)"><em class="fa-regular text-purple fa-circle-check"></em></a>
                                </span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group mb-0">
                                    <div class="form-group">
                                        <label>Category ID<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.category_id }" v-model="form.category_id" placeholder="Category ID">
                                        <div v-if="form.errors.category_id" class="invalid-feedback d-block">{{ form.errors.category_id }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Category Name<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.category_name }" v-model="form.category_name" placeholder="Category Name">
                                        <div v-if="form.errors.category_name" class="invalid-feedback d-block">{{ form.errors.category_name }}</div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="form-group">
                                        <label>Discount Percentage<span class="required">*</span></label>
                                        <input class="form-control" type="number" step="0.01" :class="{ 'is-invalid': form.errors.discount_percentage }" v-model="form.discount_percentage" placeholder="Discount Percentage">
                                        <div v-if="form.errors.discount_percentage" class="invalid-feedback d-block">{{ form.errors.discount_percentage }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group" v-if="!discount || !discount.is_default">
                                    <label>Set to default</label>
                                    <div class="text-24">
                                        <a href="javascript:void(0)" @click="form.is_default = !form.is_default"><em class="fa-regular text-purple" :class="{'fa-circle-check' : form.is_default, 'fa-circle' : !form.is_default}"></em></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="discount" type="submit" :disabled="form.processing">Update Discount</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Save Change</button>
                        <Link class="btn btn-light btn-light--brounded" :href="`/customers/${customer.id}/`">Discard</Link>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted} from "vue";

const props = defineProps({
    discount: Object,
    customer: Object,
});

const form = useForm({
    category_id: null,
    category_name: null,
    discount_percentage: null,
    is_default: false,
});

onMounted(() => {
    if (props.discount) {
        form.category_id = props.discount.category_id;
        form.category_name = props.discount.category_name;
        form.discount_percentage = props.discount.discount_percentage;
        form.is_default = props.discount.is_default;
    }
});
</script>
