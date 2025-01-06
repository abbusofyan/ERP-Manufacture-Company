<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="customer" class="big-title">{{ customer.code }}</div>
            <div v-else class="big-title">Create Customer</div>
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
                        <span v-if="customer">Edit Customer</span>
                        <span v-else>Create Customer</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="customer ? form.put(`/customers/organisation/${customer.id}`) : form.post('/customers/organisation')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>Organisation Info</span>
                            </div>
                            <div class="form-group">
                                <label>Organisation Name<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Organisation Name">
                                <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Country</label>
                                    <Select2 v-model="form.country_id" :class="{ 'is-invalid': form.errors.country_id }" :options="countries" placeholder="Choose"/>
                                    <div v-if="form.errors.country_id" class="invalid-feedback d-block">{{ form.errors.country_id }}</div>
                                </div>
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
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>UEN<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.uen }" v-model="form.uen" placeholder="0">
                                    <div v-if="form.errors.uen" class="invalid-feedback d-block">{{ form.errors.uen }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Account Manager<span class="required">*</span></label>
                                    <Select2
                                        v-model="form.account_manager"
                                        placeholder="Account Manager"
                                        :options="[{ id: form.account_manager, text: form.account_manager }]"
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
                                                                    text: `${item.username} | ${item.name}`,
                                                                    id: item.username,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                    />
                                    <div v-if="form.errors.account_manager" class="invalid-feedback d-block">{{ form.errors.account_manager }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Customer Type<span class="required">*</span></label>
                                    <Select2 v-model="form.customer_type" :class="{ 'is-invalid': form.errors.customer_type }" :options="customerTypes" placeholder="Select Type"/>
                                    <div v-if="form.errors.customer_type" class="invalid-feedback d-block">{{ form.errors.customer_type }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Service<span class="required">*</span></label>
                                    <Select2 v-model="form.service" :class="{ 'is-invalid': form.errors.service }" :options="services" placeholder="Select Service"/>
                                    <div v-if="form.errors.service" class="invalid-feedback d-block">{{ form.errors.service }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Remark</label>
                                    <textarea class="form-control" type="text" :class="{ 'is-invalid': form.errors.remark }" v-model="form.remark" placeholder="Type Remark"></textarea>
                                    <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                                </div>
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>Refrigeration Sales<span class="required">*</span></label>
                                        <Select2 v-model="form.refrigeration_sales" :class="{ 'is-invalid': form.errors.refrigeration_sales }" :options="refrigerationSales" placeholder="Select Refrigeration Sales"/>
                                        <div v-if="form.errors.refrigeration_sales" class="invalid-feedback d-block">{{ form.errors.refrigeration_sales }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Project<span class="required">*</span></label>
                                        <Select2 v-model="form.project" :class="{ 'is-invalid': form.errors.project }" :options="projects" placeholder="Select Project"/>
                                        <div v-if="form.errors.project" class="invalid-feedback d-block">{{ form.errors.project }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="customer" class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Payment Term<span class="required">*</span></label>
                                    <Select2 v-model="form.credit_term" :class="{ 'is-invalid': form.errors.credit_term }" :options="credit_term" placeholder="Select Payment Term"/>
                                    <div v-if="form.errors.credit_term" class="invalid-feedback d-block">{{ form.errors.credit_term }}</div>
                                </div>
                            </div>
                            <div v-if="customer" class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Credit Limit<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.credit_limit }" v-model="form.credit_limit" placeholder="0">
                                    <div v-if="form.errors.credit_limit" class="invalid-feedback d-block">{{ form.errors.credit_limit }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!customer" class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">2</span>
                                <span>Address (es)</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Address<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.address_location }" v-model="form.address_location" placeholder="Address">
                                    <div v-if="form.errors.address_location" class="invalid-feedback d-block">{{ form.errors.address_location }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Unit No</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.address_unit_no }" v-model="form.address_unit_no" placeholder="Unit No">
                                    <div v-if="form.errors.address_unit_no" class="invalid-feedback d-block">{{ form.errors.address_unit_no }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Building Name<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.address_building_name }" v-model="form.address_building_name" placeholder="Building Name">
                                    <div v-if="form.errors.address_building_name" class="invalid-feedback d-block">{{ form.errors.address_building_name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Postal Code<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.address_postal_code }" v-model="form.address_postal_code" placeholder="Postal Code">
                                    <div v-if="form.errors.address_postal_code" class="invalid-feedback d-block">{{ form.errors.address_postal_code }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                </div>
                                <div class="form-group">
                                    <label>Office Number</label>
                                    <div class="row">
                                        <div class="col-lg-9">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.address_office_number }" v-model="form.address_office_number" placeholder="0000 0000">
                                        </div>
                                        <div class="col-lg-3">
                                            <a class="btn btn-purple w-100" href="javascript:void(0)" @click="form.address_office_number = form.phone">Retrieve</a>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.address_office_number" class="invalid-feedback d-block">{{ form.errors.address_office_number }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!customer" class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">3</span>
                                <span>POC Info</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>First name<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.poc_first_name }" v-model="form.poc_first_name" placeholder="First name">
                                        <div v-if="form.errors.poc_first_name" class="invalid-feedback d-block">{{ form.errors.poc_first_name }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Last name</label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.poc_last_name }" v-model="form.poc_last_name" placeholder="Last name">
                                        <div v-if="form.errors.poc_last_name" class="invalid-feedback d-block">{{ form.errors.poc_last_name }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email<span class="required">*</span></label>
                                        <input class="form-control" type="email" :class="{ 'is-invalid': form.errors.poc_email }" v-model="form.poc_email" placeholder="Email">
                                        <div v-if="form.errors.poc_email" class="invalid-feedback d-block">{{ form.errors.poc_email }}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <label>Country Code</label>
                                                <Select2 v-model="form.poc_country_phone_code_id" :class="{ 'is-invalid': form.errors.poc_country_phone_code_id }" :options="phoneCodes" placeholder="Code"/>
                                                <div v-if="form.errors.poc_country_phone_code_id" class="invalid-feedback d-block">{{ form.errors.poc_country_phone_code_id }}</div>
                                            </div>
                                            <div class="col-lg-7">
                                                <label>Office Number</label>
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.poc_phone }" v-model="form.poc_phone" placeholder="0000 0000">
                                                <div v-if="form.errors.poc_phone" class="invalid-feedback d-block">{{ form.errors.poc_phone }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.poc_title }" v-model="form.poc_title" placeholder="Title">
                                        <div v-if="form.errors.poc_title" class="invalid-feedback d-block">{{ form.errors.poc_title }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="customer" type="submit" :disabled="form.processing">Update Customer</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create Customer</button>
                        <Link class="btn btn-light btn-light--brounded" href="/customers">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
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
    csrf: String,
    customer: Object,
    countries: Array,
    phoneCodes: Array,
    managers: Array,
    customerTypes: Array,
    services: Array,
    refrigerationSales: Array,
    projects: Array,
    credit_term: Array,
});

const form = useForm({
    code: null,
    country_id: null,
    name: null,
    country_phone_code_id: null,
    phone: null,
    uen: null,
    account_manager: null,
    customer_type: null,
    service: null,
    remark: null,
    refrigeration_sales: null,
    project: null,
    address_location: null,
    address_unit_no: null,
    address_building_name: null,
    address_postal_code: null,
    address_office_number: null,
    poc_first_name: null,
    poc_last_name: null,
    poc_email: null,
    poc_country_phone_code_id: null,
    poc_phone: null,
    poc_title: null,
    credit_term: null,
    credit_limit: null,
});

onMounted(() => {
    if (props.customer) {
        form.code = props.customer.code;
        form.country_id = props.customer.country_id;
        form.name = props.customer.name;
        form.country_phone_code_id = props.customer.country_phone_code_id;
        form.phone = props.customer.phone;
        form.uen = props.customer.uen;
        form.account_manager = props.customer.account_manager;
        form.customer_type = props.customer.customer_type;
        form.service = props.customer.service;
        form.remark = props.customer.remark;
        form.refrigeration_sales = props.customer.refrigeration_sales;
        form.project = props.customer.project;
        form.address_location = props.customer.address_location;
        form.address_unit_no = props.customer.address_unit_no;
        form.address_building_name = props.customer.address_building_name;
        form.address_postal_code = props.customer.address_postal_code;
        form.address_office_number = props.customer.address_office_number;
        form.poc_first_name = props.customer.poc_first_name;
        form.poc_last_name = props.customer.poc_last_name;
        form.poc_email = props.customer.poc_email;
        form.poc_country_phone_code_id = props.customer.poc_country_phone_code_id;
        form.poc_phone = props.customer.poc_phone;
        form.poc_title = props.customer.poc_title;
        form.credit_term = props.customer.credit_term;
        form.credit_limit = props.customer.credit_limit;
    }
});
</script>
