<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="bank" class="big-title">Edit Bank</div>
            <div v-else class="big-title">New Bank</div>
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
                        <span v-if="bank">Edit Bank</span>
                        <span v-else>New Bank</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="bank ? form.put(`/customers/${customer.id}/banks/${bank.id}`) : form.post(`/customers/${customer.id}/banks`)">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span>Bank Info</span>
                                <span class="pl-10" v-if="bank && bank.is_default">
                                    <a href="javascript:void(0)"><em class="fa-regular text-purple fa-circle-check"></em></a>
                                </span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group mb-0">
                                    <div class="form-group">
                                        <label>Bank Name<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.bank_name }" v-model="form.bank_name" placeholder="Bank Name">
                                        <div v-if="form.errors.bank_name" class="invalid-feedback d-block">{{ form.errors.bank_name }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Bank Code<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.bank_code }" v-model="form.bank_code" placeholder="Bank Code">
                                        <div v-if="form.errors.bank_code" class="invalid-feedback d-block">{{ form.errors.bank_code }}</div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="form-group">
                                        <label>Branch Code<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.branch_code }" v-model="form.branch_code" placeholder="Branch Code">
                                        <div v-if="form.errors.branch_code" class="invalid-feedback d-block">{{ form.errors.branch_code }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Account Number<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.account_number }" v-model="form.account_number" placeholder="Account Number">
                                        <div v-if="form.errors.account_number" class="invalid-feedback d-block">{{ form.errors.account_number }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Swift Code<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.swift_code }" v-model="form.swift_code" placeholder="Swift Code">
                                    <div v-if="form.errors.swift_code" class="invalid-feedback d-block">{{ form.errors.swift_code }}</div>
                                </div>
                                <div class="form-group" v-if="!bank || !bank.is_default">
                                    <label>Set to default</label>
                                    <div class="text-24">
                                        <a href="javascript:void(0)" @click="form.is_default = !form.is_default"><em class="fa-regular text-purple" :class="{'fa-circle-check' : form.is_default, 'fa-circle' : !form.is_default}"></em></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="bank" type="submit" :disabled="form.processing">Update Bank</button>
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
    bank: Object,
    customer: Object,
});

const form = useForm({
    bank_name: null,
    bank_code: null,
    branch_code: null,
    account_number: null,
    swift_code: null,
    is_default: false,
});

onMounted(() => {
    if (props.bank) {
        form.bank_name = props.bank.bank_name;
        form.bank_code = props.bank.bank_code;
        form.branch_code = props.bank.branch_code;
        form.account_number = props.bank.account_number;
        form.swift_code = props.bank.swift_code;
        form.is_default = props.bank.is_default;
    }
});
</script>
