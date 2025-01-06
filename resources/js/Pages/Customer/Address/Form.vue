<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="address" class="big-title">Edit Address</div>
            <div v-else class="big-title">New Address</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/customers">Customers</Link>
                    </li>
                    <li>
                        <Link :href="`/customers/${customer.id}`">{{ customer.name }}</Link>
                    </li>
                    <li>
                        <span v-if="address">Edit Address</span>
                        <span v-else>New Address</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="address ? form.put(`/customers/${customer.id}/addresses/${address.id}`) : form.post(`/customers/${customer.id}/addresses`)">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span v-if="address">Edit Address</span>
                                <span v-else>New Address</span>
                                <span class="pl-10" v-if="address && address.is_default">
                                    <a href="javascript:void(0)"><em class="fa-regular text-purple fa-circle-check"></em></a>
                                </span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Block<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.block }" v-model="form.block" placeholder="Block">
                                    <div v-if="form.errors.block" class="invalid-feedback d-block">{{ form.errors.block }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Street Name<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.street_name }" v-model="form.street_name" placeholder="Street Name">
                                    <div v-if="form.errors.street_name" class="invalid-feedback d-block">{{ form.errors.street_name }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Unit Level<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.unit_level }" v-model="form.unit_level" placeholder="Unit Level">
                                    <div v-if="form.errors.unit_level" class="invalid-feedback d-block">{{ form.errors.unit_level }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Unit Number<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.unit_number }" v-model="form.unit_number" placeholder="Unit Number">
                                    <div v-if="form.errors.unit_number" class="invalid-feedback d-block">{{ form.errors.unit_number }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>City<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.city }" v-model="form.city" placeholder="City">
                                    <div v-if="form.errors.city" class="invalid-feedback d-block">{{ form.errors.city }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Region<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.region }" v-model="form.region" placeholder="Region">
                                    <div v-if="form.errors.region" class="invalid-feedback d-block">{{ form.errors.region }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Country<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.country }" v-model="form.country" placeholder="Country">
                                    <div v-if="form.errors.country" class="invalid-feedback d-block">{{ form.errors.country }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Postal Code<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.postal_code }" v-model="form.postal_code" placeholder="Postal Code">
                                    <div v-if="form.errors.postal_code" class="invalid-feedback d-block">{{ form.errors.postal_code }}</div>
                                </div>
                            </div>
                            <div class="form-group" v-if="!address || !address.is_default">
                                <label>Set to default</label>
                                <div class="text-24">
                                    <a href="javascript:void(0)" @click="form.is_default = !form.is_default"><em class="fa-regular text-purple" :class="{'fa-circle-check' : form.is_default, 'fa-circle' : !form.is_default}"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="address" type="submit" :disabled="form.processing">Update Address</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Save Change</button>
                        <Link class="btn btn-light btn-light--brounded" :href="`/customers/${customer.id}/addresses`">Discard</Link>
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
    address: Object,
    customer: Object,
});

const form = useForm({
    block: null,
    street_name: null,
    unit_level: null,
    unit_number: null,
    city: null,
    region: null,
    country: null,
    postal_code: null,
    is_default: false,
});

onMounted(() => {
    if (props.address) {
        form.block = props.address.block;
        form.street_name = props.address.street_name;
        form.unit_level = props.address.unit_level;
        form.unit_number = props.address.unit_number;
        form.city = props.address.city;
        form.region = props.address.region;
        form.country = props.address.country;
        form.postal_code = props.address.postal_code;
        form.is_default = props.address.is_default;
    }
});
</script>
