<template>
    <h3 class="title-md mb-16"><em class="icon fa-light fa-user-xmark text-red text-[3rem]"></em><span class="pl-15">Mark POC As Resign</span></h3>
    <div class="text-orange bg-orange/[.12] p-10 rounded-[.4rem]"><span class="fa-regular fa-triangle-exclamation"></span><span> This is not reversible. Are you sure you want to proceed?</span></div>
    <div class="mt-36">
        <h3 class="text-18 font-medium mb-16">Current Contact</h3>
        <table class="table w-full">
            <tbody>
            <tr>
                <th>Fist Name</th>
                <td>{{ poc.first_name }}</td>
            </tr>
            <tr v-if="poc.last_name">
                <th>Last Name</th>
                <td>{{ poc.last_name }}</td>
            </tr>
            <tr v-if="poc.title">
                <th>Title</th>
                <td>{{ poc.title }}</td>
            </tr>
            <tr v-if="poc.email">
                <th>Email</th>
                <td>{{ poc.email }}</td>
            </tr>
            <tr v-if="poc.phone">
                <th>Phone</th>
                <td>{{ poc.country_phone_code ? poc.country_phone_code.phone_code + " " : null }}{{ poc.phone }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
    <div class="flex items-center gap-32">
        <p class="text-orange">Contact Replacement</p>
        <label class="switch" :for="`checkbox-${poc.id}`">
            <input :id="`checkbox-${poc.id}`" type="checkbox" v-model="form.replace">
            <div class="slider round"></div>
            <div class="icon"></div>
        </label>
    </div>
    <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
    <h3 class="text-18 font-medium mb-16">New Contact</h3>
    <form class="select2-w-100" @submit.prevent="form.replace ? form.put(`/vendors/${vendor.id}/poc/${poc.id}`) : form.post(`/vendors/${vendor.id}/poc`)">
        <div class="form-wrap">
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
            <div class="form-group">
                <label>Email<span class="required">*</span></label>
                <input class="form-control" type="email" :class="{ 'is-invalid': form.errors.email }" v-model="form.email" placeholder="Email">
                <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
            </div>
            <div class="form-row grid grid sm:!grid-cols-10 items-end">
                <div class="form-group sm:col-span-4">
                    <label>Country Code</label>
                    <Select2 class="w-100" v-model="form.country_phone_code_id" :class="{ 'is-invalid': form.errors.country_phone_code_id }" :options="phoneCodes" placeholder="Code"/>
                    <div v-if="form.errors.country_phone_code_id" class="invalid-feedback d-block">{{ form.errors.country_phone_code_id }}</div>
                </div>
                <div class="form-group sm:col-span-6">
                    <label>Office Number</label>
                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.phone }" v-model="form.phone" placeholder="0000 0000">
                    <div v-if="form.errors.phone" class="invalid-feedback d-block">{{ form.errors.phone }}</div>
                </div>
            </div>
            <div class="form-group">
                <label>Title</label>
                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.title }" v-model="form.title" placeholder="Title">
                <div v-if="form.errors.title" class="invalid-feedback d-block">{{ form.errors.title }}</div>
            </div>
        </div>
        <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
        <div class="btn-group">
            <button class="btn btn-purple" type="submit" :disabled="form.processing" @click="Object.keys(form.errors).length > 0 ? null : cancelBtn.click()">Proceed</button>
            <a ref="cancelBtn" class="btn btn-light btn-light--brounded" href="javascript:void(0)" data-fancybox-close>Cancel</a>
        </div>
    </form>
</template>

<script setup>
import {computed, ref} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    poc: Object,
    vendor: Object,
    phoneCodes: Array,
});

const form = useForm({
    replace: false,
    resign: true,
    first_name: null,
    last_name: null,
    email: null,
    country_phone_code_id: null,
    phone: null,
    title: null,
});

const cancelBtn = ref(0);
</script>
