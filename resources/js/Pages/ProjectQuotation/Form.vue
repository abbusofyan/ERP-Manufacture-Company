<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="quotation" class="big-title">Edit Quotation</div>
            <div v-else class="big-title">New Quotation</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/">
                            <span class="icon">
                                <img src="../../../assets/img/home.svg" alt="home">
                            </span>
                        </Link>
                    </li>
                    <li>
                        <Link href="/project-orders">Project Order</Link>
                    </li>
                    <li>
                        <span v-if="quotation">Edit Quotation</span>
                        <span v-else>Create Quotation</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="updateQuotation">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                                <span>Quotation Details</span>
                            </div>

                            <div v-if="!quotation || quotation.status == 1" class="grid md:grid-cols-2 gap-[7.7rem] box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="form-group">
                                    <label>Status</label>
                                    <Select2 v-model="form.status" :class="{ 'is-invalid': form.errors.status }" :options="statuses" placeholder="Select" class="flex-fill" disabled>
                                    </Select2>
                                    <div v-if="form.errors.status" class="invalid-feedback d-block">
                                        {{ form.errors.status }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Select Project Order Number</label>
                                    <Select2
                                        v-model="form.project_order_id"
                                        :class="{ 'is-invalid': form.errors.project_order_id }"
                                        :options="projectOrders"
                                        placeholder="Select"
                                        class="flex-fill"
                                        :settings="{
                                            ajax: {
                                                url: '/data/project-orders',
                                                dataType: 'json',
                                                method: 'POST',
                                                data: function (params) {
                                                    return { search: params.term, _token: csrf };
                                                },
                                                processResults: function (data, params) {
                                                    return {
                                                        results: data.map(function (item) {
                                                            return {
                                                                text: `${item.code}`,
                                                                id: item.id,
                                                                data: item,
                                                            };
                                                        })
                                                    };
                                                }
                                            }
                                        }"
                                        @select="(selected) => {
                                            form.project_quotation_data = selected.data;
                                            form.vehicle_id = selected.data.vehicle.id;
                                            form.vehicle = selected.data.vehicle;
                                            vehicles = [{id:selected.data.vehicle.id, text: selected.data.vehicle.license_plate}];
                                            form.customer_id = selected.data.customer.id;
                                            form.customer = selected.data.customer;
                                            customers = [{id:selected.data.customer.id, text: `${selected.data.customer.code} | ${selected.data.customer.name}`}];
                                        }"
                                    >
                                    </Select2>
                                    <div v-if="form.errors.project_order_id" class="invalid-feedback d-block">
                                        {{ form.errors.project_order_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div>
                                    <div v-if="!quotation || quotation.status == 1 || (quotation.invoices && quotation.invoices.length == 0)" class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                        <div class="form-group">
                                            <label>Select Vehicle Number*<span class="required">*</span></label>
                                            <div class="d-flex gap-1">
                                                <Select2 v-model="form.vehicle_id" :class="{ 'is-invalid': form.errors.vehicle_id }" :options="vehicles" placeholder="Select Vehicle" class="flex-fill"
                                                         :settings="{
                                                    ajax: {
                                                        url: '/data/vehicles',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: function (params) {
                                                            return { search: params.term, _token: csrf };
                                                        },
                                                        processResults: function (data, params) {
                                                            return {
                                                                results: data.map(function (item) {
                                                                    return { text: item.license_plate, id: item.id, data: item };
                                                                })
                                                            };
                                                        }
                                                    }
                                                }"
                                                @select="(selected) => {
                                                    form.vehicle = selected.data;
                                                    if (form.vehicle.customer) {
                                                        form.customer_id = form.vehicle.customer.id;
                                                        form.customer = form.vehicle.customer;
                                                        customers = [{
                                                            id: form.vehicle.customer.id,
                                                            text: `${form.vehicle.customer.code} | ${form.vehicle.customer.name}`,
                                                            data: form.vehicle.customer
                                                        }];
                                                    }
                                                }"
                                                >
                                                </Select2>
                                                <a class="btn btn-purple--brounded flex-col" target="_blank" href="/vehicles">ADD NEW</a>
                                            </div>
                                            <p class="text-warning mt-5">*click add to register the vehicle with the customer</p>
                                            <div v-if="form.errors.vehicle_id" class="invalid-feedback d-block">
                                                {{ form.errors.vehicle_id }}
                                            </div>
                                        </div>

                                        <div v-if="form.vehicle" class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                            <div class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                                <span>Vehicle Details</span>
                                            </div>
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>Vehicle Number</th>
                                                    <td>{{ form.vehicle.license_plate }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Type</th>
                                                    <td>{{ form.vehicle.type }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Vehicle Make</th>
                                                    <td>{{ form.vehicle.vehicle_make }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Model</th>
                                                    <td>{{ form.vehicle.model }}</td>
                                                </tr>
                                                <tr v-if="form.vehicle.project_contracts">
                                                    <th>CMP Contact validity</th>
                                                    <td>
                                                        <div v-for="contract in form.vehicle.project_contracts" :key="contract.id">
                                                            {{ $filters.formatDate(contract.start_duration_date) }} /
                                                            {{ $filters.formatDate(contract.end_duration_date) }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div
                                        v-if="!quotation || quotation.status == 1 || (quotation.invoices && quotation.invoices.length == 0)"
                                        class="box mb-[2.6rem]">
                                        <div class="form-group">
                                            <label>Select Customer<span class="required">*</span></label>
                                            <div class="d-flex gap-1">
                                                <Select2
                                                    v-model="form.customer_id"
                                                    :class="{ 'is-invalid': form.errors.customer_id }"
                                                    :options="customers"
                                                    placeholder="Select Customer"
                                                    class="flex-fill"
                                                    :settings="{
                                                    ajax: {
                                                        url: '/data/customers',
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
                                                                        text: `${item.code} | ${item.name}`,
                                                                        id: item.id,
                                                                        data: item,
                                                                    };
                                                                })
                                                            };
                                                        }
                                                    }
                                                }"
                                                    @select="(selected) => form.customer = selected.data"
                                                >
                                                </Select2>
                                                <a class="btn btn-purple--brounded flex-col" target="_blank"
                                                   href="/customers">ADD NEW</a>
                                            </div>
                                            <p class="text-warning mt-5">*click add if the customer is not registered</p>
                                            <div v-if="form.errors.customer_id" class="invalid-feedback d-block">
                                                {{ form.errors.customer_id }}
                                            </div>
                                        </div>

                                        <div v-if="form.customer"
                                             class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                            <div
                                                class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                                <span>Customer Details</span>
                                            </div>
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>Customer ID</th>
                                                    <td>{{ form.customer.code }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Name</th>
                                                    <td>{{ form.customer.name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>POC</th>
                                                    <td>{{ form.customer.poc_first_name }}
                                                        {{ form.customer.poc_last_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>POC Contact</th>
                                                    <td>{{ form.customer.poc_phone }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                        <div class="form-group">
                                            <label>Project Order Type</label>
                                            <Select2 v-model="form.project_order_type" :class="{ 'is-invalid': form.errors.project_order_type }" :options="types" placeholder="Select Type">
                                            </Select2>
                                            <div v-if="form.errors.project_order_type" class="invalid-feedback d-block">
                                                {{ form.errors.project_order_type }}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Value</label>
                                            <input class="form-control" type="text" v-model="form.project_order_value" placeholder="Value">
                                            <div v-if="form.errors.project_order_value" class="invalid-feedback d-block">
                                                {{ form.errors.project_order_value }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                        <div class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                            <span>Information</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <table class="table-1-el w-full">
                                                    <tr>
                                                        <th>Warranty Status</th>
                                                        <td>
                                                            <div class="el-tag" :class="{'green': form.project_quotation_data?.project_appointment?.in_warranty === '1', 'orange': form.project_quotation_data?.project_appointment?.in_warranty !== '1'}">
                                                                <span v-if="form.project_quotation_data?.project_appointment?.in_warranty === '1'">In Warranty</span>
                                                                <span v-else>Out of Warranty</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-lg-6">
                                                <table class="table-1-el w-full">
                                                    <tr>
                                                        <th>CMP Contact Number</th>
                                                        <td>{{ form.project_quotation_data?.project_appointment?.pic_phone_number }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="!quotation || quotation.status == 1" class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2</span>
                                <span>Confirmed By</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input class="form-control" type="text" v-model="form.pic_first_name" placeholder="First Name">
                                    <div v-if="form.errors.pic_first_name" class="invalid-feedback d-block">
                                        {{ form.errors.pic_first_name }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" v-model="form.pic_email" placeholder="Email">
                                    <div v-if="form.errors.pic_email" class="invalid-feedback d-block">
                                        {{ form.errors.pic_email }}
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div>
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label>Country Code</label>
                                                <Select2 v-model="form.pic_country_code" :class="{ 'is-invalid': form.errors.pic_country_code }" :options="phoneCodes" placeholder="Code"/>
                                                <div v-if="form.errors.pic_country_code" class="invalid-feedback d-block">{{ form.errors.pic_country_code }}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <input class="form-control" type="text" v-model="form.pic_phone_number" placeholder="Phone Number">
                                                <div v-if="form.errors.pic_phone_number" class="invalid-feedback d-block">{{ form.errors.pic_phone_number }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">3</span>
                                <span>Vehicle Parts</span>
                            </div>
                            <div v-if="!quotation || quotation.status == 1 || (quotation.invoices && quotation.invoices.length == 0)" class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                                <div style="min-height: unset" class="table-responsive border-[1px] border-solid border-[#EBE9F1] mb-20 pb-0 rounded-[.5rem]">
                                    <table class="table select-rows">
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Item ID <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>UOM</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Selling Price</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Quantity <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Tax Code<span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Tax Value (%)<span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Discount (%) <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Total</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Is FOC?</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Description</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>MISC Description</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Hide</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Action</span>
                                                </div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(part, index) in form.vehicle_parts" :key="index">
                                            <td>
                                                <!-- Storage Item ID -->
                                                <div class="form-group select2-w-100 mb-0">
                                                    <Select2
                                                        v-model="part.storage_item_id"
                                                        :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.storage_item_id`] }"
                                                        placeholder="Select Item"
                                                        class="flex-fill w-px-300"
                                                        :options="storageItems"
                                                        :settings="{
                                                                ajax: {
                                                                    url: '/data/storage-items',
                                                                    dataType: 'json',
                                                                    method: 'POST',
                                                                    data: function (params) {
                                                                        return { search: params.term, _token: csrf };
                                                                    },
                                                                    processResults: function (data) {
                                                                        form.quantity = null;
                                                                        return {
                                                                            results: data.map(function (item) {
                                                                                return { text: `${item.storage.code}-${item.product.sku} | ${item.product.name}`, id: item.id, data: item };
                                                                            })
                                                                        };
                                                                    },
                                                                },
                                                            }"
                                                        @select="(selected) => {
                                                                part.storage_item = selected.data;
                                                                part.description = selected.data?.product?.description;
                                                            }"
                                                    />
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.storage_item_id`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.storage_item_id`] }}
                                                </div>
                                            </td>

                                            <td>{{ part.storage_item?.product?.uom?.code }}</td>
                                            <td>${{ part.storage_item?.cost_price }}</td>

                                            <td>
                                                <!-- Quantity -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="number" min="1" :max="part.storage_item?.quantity" v-model="part.quantity" placeholder="0" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.quantity`] }">
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.quantity`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.quantity`] }}
                                                </div>
                                            </td>

                                            <td>
                                                <!-- Tax Code -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control w-px-100" type="text" v-model="part.tax_code" placeholder="Tax Code" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.tax_code`] }">
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.tax_code`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.tax_code`] }}
                                                </div>
                                            </td>

                                            <td>
                                                <!-- Tax Value -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="number" min="0" :max="100" v-model="part.tax_value" placeholder="0" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.tax_value`] }">
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.tax_value`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.tax_value`] }}
                                                </div>
                                            </td>

                                            <td>
                                                <!-- Discount Amount -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="number" min="0" max="100" v-model="part.discount" placeholder="0" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.discount`] }">
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.discount`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.discount`] }}
                                                </div>
                                            </td>

                                            <td>${{ part.total_amount }}</td>

                                            <td>
                                                <!-- Is FOC -->
                                                <div class="form-group mb-0">
                                                    <div class="custom-checkbox style-1">
                                                        <input class="form-control" type="checkbox" v-model="part.is_foc" :id="`foc_${index}`" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.is_foc`] }">
                                                        <label :for="`foc_${index}`"></label>
                                                    </div>
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.is_foc`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.is_foc`] }}
                                                </div>
                                            </td>

                                            <td>
                                                <!-- Description -->
                                                <div class="form-group mb-0">
                                                    <textarea class="form-control" v-model="part.description" style="width: 300px; min-height: 50px; resize: vertical" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.description`] }"></textarea>
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.description`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.description`] }}
                                                </div>
                                            </td>

                                            <td>
                                                <!-- MISC Description -->
                                                <div class="form-group mb-0">
                                                    <textarea class="form-control" v-model="part.misc_description" style="width: 300px; min-height: 50px; resize: vertical" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.misc_description`] }"></textarea>
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.misc_description`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.misc_description`] }}
                                                </div>
                                            </td>

                                            <td>
                                                <!-- Hide -->
                                                <div class="form-group mb-0">
                                                    <div class="custom-checkbox style-1">
                                                        <input class="form-control" type="checkbox" v-model="part.is_hide" :id="`hide_${index}`" :class="{ 'is-invalid': form.errors[`vehicle_parts.${index}.is_hide`] }">
                                                        <label :for="`hide_${index}`"></label>
                                                    </div>
                                                </div>
                                                <div v-if="form.errors[`vehicle_parts.${index}.is_hide`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_parts.${index}.is_hide`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <a v-if="form.vehicle_parts.length > 1" class="text-red" href="javascript:void(0);" @click="removeItem(index)">
                                                    <em class="fa-regular fa-trash-can"></em>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="btn-group mb-16">
                                    <a class="btn btn-purple--brounded" href="javascript:void(0)" @click="addItem">+ Add Part</a>
                                </div>
                                <div v-if="form.errors.vehicle_parts" class="invalid-feedback d-block">
                                    {{ form.errors.vehicle_parts }}
                                </div>
                            </div>

                            <div v-if="!quotation || quotation.status == 1 || (quotation.invoices && quotation.invoices.length == 0)" class="row mb-[5.6rem]">
                                <div class="col-lg-6">
                                    <table class="w-100">
                                        <tr>
                                            <td class="pb-24">
                                                <label>Deposit</label>
                                            </td>
                                            <td class="pb-24">
                                                <input class="form-control" type="text" v-model="form.deposit_value" :class="{ 'is-invalid': form.errors.deposit_value }">
                                                <div v-if="form.errors.deposit_value" class="invalid-feedback d-block">
                                                    {{ form.errors.deposit_value }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pb-24">
                                                <label>Deposit Type</label>
                                            </td>
                                            <td class="pb-24">
                                                <div class="row">
                                                    <div v-for="(op, i) in depositOption" class="col-6">
                                                        <div class="custom-checkbox style-2">
                                                            <input class="form-control" type="radio" v-model="form.deposit_option" :value="op.id" :id="`deposit_option_${i}`" :class="{ 'is-invalid': form.errors.deposit_option }">
                                                            <label :for="`deposit_option_${i}`">{{ op.text }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="form.errors.deposit_option" class="invalid-feedback d-block">
                                                    {{ form.errors.deposit_option }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pb-24">
                                                <label>Finance Amount</label>
                                            </td>
                                            <td class="pb-24">
                                                <input class="form-control" type="text" v-model="form.finance_amount" :class="{ 'is-invalid': form.errors.finance_amount }">
                                                <div v-if="form.errors.finance_amount" class="invalid-feedback d-block">
                                                    {{ form.errors.finance_amount }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pb-24">
                                                <label>Payment method</label>
                                            </td>
                                            <td class="pb-24">
                                                <Select2 v-model="form.payment_method" :class="{ 'is-invalid': form.errors.payment_method }" :options="paymentOption" placeholder="Select Vehicle" class="flex-fill w-100"/>
                                                <div v-if="form.errors.payment_method" class="invalid-feedback d-block">
                                                    {{ form.errors.payment_method }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pb-24">
                                                <label>Terms (of Payment)</label>
                                            </td>
                                            <td class="pb-24">
                                                <input class="form-control" type="text" v-model="form.terms" :class="{ 'is-invalid': form.errors.terms }">
                                                <div v-if="form.errors.terms" class="invalid-feedback d-block">
                                                    {{ form.errors.terms }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pb-24">
                                                <label>Quotation validity date</label>
                                            </td>
                                            <td class="pb-24">
                                                <VueDatePicker
                                                    v-model="form.validity_date"
                                                    :class="{ 'is-invalid': form.errors.validity_date }"
                                                    placeholder="Select validity Date"
                                                    :enable-time-picker="false"
                                                    :format="$filters.format"
                                                />
                                                <div v-if="form.errors.validity_date" class="invalid-feedback d-block">
                                                    {{ form.errors.validity_date }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pb-24">
                                                <label>Currency</label>
                                            </td>
                                            <td class="pb-24">
                                                <div class="d-flex gap-13">
                                                    <Select2 v-model="form.currency" :class="{ 'is-invalid': form.errors.currency }" :options="currencyOption" placeholder="Currency" class="flex-fill w-px-200"/>
                                                    <input class="form-control" type="text" v-model="form.currency_rate" :class="{ 'is-invalid': form.errors.currency_rate }" placeholder="0.0">
                                                </div>
                                                <div v-if="form.errors.currency" class="invalid-feedback d-block">
                                                    {{ form.errors.currency }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pb-24">
                                                <label>Rounding</label>
                                            </td>
                                            <td class="pb-24">
                                                <input class="form-control" type="text" v-model="form.rounding" :class="{ 'is-invalid': form.errors.rounding }">
                                                <div v-if="form.errors.rounding" class="invalid-feedback d-block">
                                                    {{ form.errors.rounding }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-end">
                                        <table class="w-100 w-px-300">
                                            <tr>
                                                <td class="pb-24 text-right">
                                                    <label>Sub total: </label>
                                                </td>
                                                <td class="pb-24 text-right">
                                                    <strong>${{ form.sub_total?.toFixed(2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pb-24 text-right">
                                                    <label>Discount: </label>
                                                </td>
                                                <td class="pb-24 text-right">
                                                    <strong>${{ form.discount?.toFixed(2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pb-24 text-right">
                                                    <label>Tax: </label>
                                                </td>
                                                <td class="pb-24 text-right">
                                                    <strong>${{ form.gst_total?.toFixed(2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pb-24 text-right">
                                                    <label>Total: </label>
                                                </td>
                                                <td class="pb-24 text-right">
                                                    <strong>${{ form.total?.toFixed(2) }}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!quotation || quotation.status == 1 || (quotation.invoices && quotation.invoices.length == 0)" class="form-group">
                                <label>Footer</label>
                                <textarea class="form-control" v-model="form.footer_text" rows="3" placeholder="Footer"></textarea>
                                <div v-if="form.errors.footer_text" class="invalid-feedback d-block">{{ form.errors.footer_text }}</div>
                            </div>

                            <div v-if="!quotation || quotation.status == 1 || (quotation.invoices && quotation.invoices.length == 0)" class="form-group">
                                <label>Remark</label>
                                <textarea class="form-control" v-model="form.remark" rows="3" placeholder="Remark"></textarea>
                                <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Image</label>
                        <div class="fileContainer">
                            <label class="fileInputLabel" for="fileInput2">
                                <input class="fileInput" id="fileInput2"
                                       type="file"
                                       :class="{ 'is-invalid': form.errors.image_url }"
                                       @input="form.image_url = $event.target.files[0]"
                                       accept="image/*">
                                <span>Choose File</span>
                                <span class="browse">Browse</span>
                            </label>
                            <div class="mt-12 text-[#82868B]">Allowed file types: pdf.</div>
                            <div class="files">
                                <p class="name">
                                    {{ form.image_url?.name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">
                            <span v-if="quotation">Update Quotation</span>
                            <span v-else>Create Quotation</span>
                        </button>
                        <button v-if="form.project_order_id == null" @click="form.is_generate_project_order = true" class="btn btn-purple" type="submit" :disabled="form.processing">
                            Generate Project Order
                        </button>
                        <a class="btn btn-light btn-light--brounded" href="javascript:void(0)" @click="discard">Discard</a>
                    </div>

                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>


<script setup>
import {ref, watch} from 'vue';
import {useForm, usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Swal from 'sweetalert2';
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    csrf: String,
    statuses: Array,
    types: Array,
    phoneCodes: Array,
    depositOption: Array,
    paymentOption: Array,
    currencyOption: Array,
    quotation: Object,
    projectQuotations: Array,
    projectOrders: Array,
    customer: Object,
    customers: Array,
    vehicle: Object,
    vehicles: Array,
    storageItems: Array,
});

const form = useForm({
    project_order_id: null,
    project_quotation_id: null,
    project_quotation_data: null,
    status: 1,
    customer: null,
    customer_id: null,
    vehicle: null,
    vehicle_id: null,
    pic_first_name: null,
    pic_last_name: null,
    pic_title: null,
    pic_country_code: props.phoneCodes.find(code => code.text === "+65")?.id || null,
    pic_phone_number: null,
    pic_email: null,
    project_order_type: null,
    project_order_value: null,
    deposit_value: null,
    deposit_option: null,
    finance_amount: null,
    payment_method: null,
    terms: null,
    validity_date: null,
    currency: null,
    currency_rate: null,
    rounding: null,
    vehicle_parts: [],
    remark: null,
    footer_text: null,
    sub_total: null,
    discount: null,
    gst_total: null,
    total: null,
    image_url: null,
    is_generate_project_order: false,
});

if (props.quotation) {
    form.project_order_id = props.quotation.project_order_id;
    form.project_quotation_id = props.quotation.project_quotation_id;
    form.status = props.quotation.status;
    form.customer_id = props.quotation.customer_id;
    form.vehicle_id = props.quotation.vehicle_id;
    form.pic_first_name = props.quotation.pic_first_name;
    form.pic_last_name = props.quotation.pic_last_name;
    form.pic_title = props.quotation.pic_title;
    form.pic_country_code = props.quotation.pic_country_code;
    form.pic_phone_number = props.quotation.pic_phone_number;
    form.pic_email = props.quotation.pic_email;
    form.project_order_type = props.quotation.project_order_type;
    form.project_order_value = props.quotation.project_order_value;
    form.deposit_value = props.quotation.deposit_value;
    form.deposit_option = props.quotation.deposit_option;
    form.finance_amount = props.quotation.finance_amount;
    form.payment_method = props.quotation.payment_method;
    form.terms = props.quotation.terms;
    form.validity_date = props.quotation.validity_date;
    form.currency = props.quotation.currency;
    form.currency_rate = props.quotation.currency_rate;
    form.rounding = props.quotation.rounding;
    form.sub_total = parseFloat(props.quotation.sub_total);
    form.discount = parseFloat(props.quotation.discount);
    form.gst_total = parseFloat(props.quotation.gst_total);
    form.total = parseFloat(props.quotation.total);
    form.remark = props.quotation.remark;
    form.footer_text = props.quotation.footer_text;

    if (props.quotation.vehicle_parts) {
        form.vehicle_parts = props.quotation.vehicle_parts.map(part => ({
            id: part.id,
            storage_item_id: part.storage_item_id,
            quantity: part.quantity,
            storage_item: part.storage_item,
            tax_code: part.tax_code,
            tax_value: part.tax_value,
            discount: part.discount,
            discount_amount: part.discount_amount,
            subtotal_amount: part.subtotal_amount,
            total_amount: part.total_amount,
            is_foc: part.is_foc,
            description: part.description,
            misc_description: part.misc_description,
            is_hide: part.is_hide,
        }));
    }
}

if (!props.quotation || !props.quotation.vehicle_parts) {
    form.vehicle_parts.push({
        id: null,
        storage_item_id: null,
        quantity: null,
        storage_item: null,
        tax_code: null,
        tax_value: 0,
        discount: 0,
        discount_amount: 0,
        subtotal_amount: 0,
        total_amount: 0,
        is_foc: false,
        description: null,
        misc_description: null,
        is_hide: false,
    })
}

if (props.customer) {
    form.customer = props.customer;
}

if (props.vehicle) {
    form.vehicle = props.vehicle;
}

const addItem = () => {
    form.vehicle_parts.push({
        id: null,
        storage_item_id: null,
        quantity: null,
        storage_item: null,
        tax_code: null,
        tax_value: 0,
        discount: 0,
        discount_amount: 0,
        subtotal_amount: 0,
        total_amount: 0,
        is_foc: false,
        description: null,
        misc_description: null,
        is_hide: false,
    })
}

const removeItem = (index) => {
    form.vehicle_parts.splice(index, 1);
}

const calculateAmounts = (index) => {
    const part = form?.vehicle_parts?.[index] ?? {};
    const price = Number(part?.storage_item?.cost_price) || 0;
    const quantity = Number(part?.quantity) || 0;
    const tax = Number(part?.tax_value) || 0;
    const discount = Number(part?.discount) || 0;
    const rounding = form?.rounding ?? 2;

    const subtotal = price * quantity;
    const discountAmount = (subtotal * discount / 100) || 0;
    const taxAmount = ((subtotal - discountAmount) * tax / 100) || 0;
    const totalAmount = subtotal - discountAmount + taxAmount;

    part.subtotal_amount = subtotal.toFixed(rounding);
    part.discount_amount = discountAmount.toFixed(rounding);
    part.tax_amount = taxAmount.toFixed(rounding);
    part.total_amount = totalAmount.toFixed(rounding);
};


// Watch for changes in vehicle parts and calculate amounts
watch(
    [() => form.vehicle_parts, () => form.rounding],
    async () => {
        form.sub_total = 0;
        form.gst_total = 0;
        form.discount = 0;
        form.total = 0;

        for (const part of form.vehicle_parts) {
            const index = form.vehicle_parts.indexOf(part);
            await calculateAmounts(index);
        }

        form.sub_total = form.vehicle_parts.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.subtotal_amount) || 0);
        }, 0);

        form.gst_total = form.vehicle_parts.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.tax_amount) || 0);
        }, 0);

        form.discount = form.vehicle_parts.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.discount_amount) || 0);
        }, 0);

        form.total = form.vehicle_parts.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.total_amount) || 0);
        }, 0);

        if (form.terms == null) {
            form.terms = form.customer?.credit_term_text;
        }
    },
    {deep: true}
);

const updateQuotation = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea5455',
        cancelButtonColor: '#009CDB',
        confirmButtonText: 'Yes, Proceed!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            if (props.quotation) {
                form.post(`/project-quotations/${props.quotation.id}`);
            } else {
                form.post('/project-quotations');
            }
        }
    });
};

const discard = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea5455',
        cancelButtonColor: '#009CDB',
        confirmButtonText: 'Yes, Proceed!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/project-quotations`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
