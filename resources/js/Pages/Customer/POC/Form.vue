<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="poc" class="big-title">Edit POC</div>
            <div v-else class="big-title">New POC</div>
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
                        <span v-if="poc">Edit POC</span>
                        <span v-else>New POC</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="poc ? form.put(`/customers/${customer.id}/poc/${poc.id}`) : form.post(`/customers/${customer.id}/poc`)">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span>POC Info</span>
                                <span class="pl-10" v-if="poc && poc.is_default">
                                    <a href="javascript:void(0)"><em class="fa-regular text-purple fa-circle-check"></em></a>
                                </span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group mb-0">
                                    <div class="form-group">
                                        <label>First name<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.first_name }" v-model="form.first_name" placeholder="First name">
                                        <div v-if="form.errors.first_name" class="invalid-feedback d-block">{{ form.errors.first_name }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Last name</label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.last_name }" v-model="form.last_name" placeholder="Last name">
                                        <div v-if="form.errors.last_name" class="invalid-feedback d-block">{{ form.errors.last_name }}</div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <label>Country Code</label>
                                                <Select2 v-model="form.country_phone_code_id" :class="{ 'is-invalid': form.errors.country_phone_code_id }" :options="phoneCodes" placeholder="Code"/>
                                                <div v-if="form.errors.country_phone_code_id" class="invalid-feedback d-block">{{ form.errors.country_phone_code_id }}</div>
                                            </div>
                                            <div class="col-lg-7">
                                                <label>Office Number</label>
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.phone }" v-model="form.phone" placeholder="0000 0000">
                                                <div v-if="form.errors.phone" class="invalid-feedback d-block">{{ form.errors.phone }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <label>Country Code</label>
                                                <Select2 v-model="form.country_fax_code_id" :class="{ 'is-invalid': form.errors.country_fax_code_id }" :options="phoneCodes" placeholder="Code"/>
                                                <div v-if="form.errors.country_fax_code_id" class="invalid-feedback d-block">{{ form.errors.country_fax_code_id }}</div>
                                            </div>
                                            <div class="col-lg-7">
                                                <label>Fax Number</label>
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.fax }" v-model="form.fax" placeholder="0000 0000">
                                                <div v-if="form.errors.fax" class="invalid-feedback d-block">{{ form.errors.fax }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Email<span class="required">*</span></label>
                                    <input class="form-control" type="email" :class="{ 'is-invalid': form.errors.email }" v-model="form.email" placeholder="Email">
                                    <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Designation</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.title }" v-model="form.title" placeholder="Designation">
                                    <div v-if="form.errors.title" class="invalid-feedback d-block">{{ form.errors.title }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Remark</label>
                                    <textarea class="form-control" type="text" :class="{ 'is-invalid': form.errors.remark }" v-model="form.remark" placeholder="..."></textarea>
                                    <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                                </div>
                                <div class="form-group" v-if="!poc || !poc.is_default">
                                    <label>Set to default</label>
                                    <div class="text-24">
                                        <a href="javascript:void(0)" @click="form.is_default = !form.is_default"><em class="fa-regular text-purple" :class="{'fa-circle-check' : form.is_default, 'fa-circle' : !form.is_default}"></em></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="poc" type="submit" :disabled="form.processing">Update POC</button>
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
    poc: Object,
    customer: Object,
    phoneCodes: Array,
});

const form = useForm({
    first_name: null,
    last_name: null,
    email: null,
    country_phone_code_id: null,
    phone: null,
    title: null,
    country_fax_code_id: null,
    fax: null,
    remark: null,
    is_default: false,
});

onMounted(() => {
    if (props.poc) {
        form.first_name = props.poc.first_name;
        form.last_name = props.poc.last_name;
        form.email = props.poc.email;
        form.country_phone_code_id = props.poc.country_phone_code_id;
        form.phone = props.poc.phone;
        form.title = props.poc.title;
        form.country_fax_code_id = props.poc.country_fax_code_id;
        form.fax = props.poc.fax;
        form.remark = props.poc.remark;
        form.is_default = props.poc.is_default;
    }
});
</script>
