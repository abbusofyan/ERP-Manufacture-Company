<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="contract" class="big-title">Edit Service Contract</div>
            <div v-else class="big-title">New Service Contract</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/service-contracts">Service Contracts</Link>
                    </li>
                    <li>
                        <span v-if="contract">Edit Service Contract</span>
                        <span v-else>Create Service Contract</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="updateContract()">
                    <div class="boxes">
                        <!-- Service Details -->
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                                <span>Service Details</span>
                            </div>
                            <div class="form-group">
                                <label>Service Contract Number</label>
                                <input class="form-control disabled" type="text" :class="{ 'is-invalid': form.errors.service_contract_number }" v-model="form.service_contract_number" placeholder="0" disabled>
                                <div v-if="form.errors.service_contract_number" class="invalid-feedback d-block">{{ form.errors.service_contract_number }}</div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Contract Start Date<span class="required">*</span></label>
                                    <VueDatePicker
                                        v-model="form.start_duration_date"
                                        :model-value="form.start_duration_date" @update:model-value="updateContractPeriod(); updateMaintainService()"
                                        :class="{ 'is-invalid': form.errors.start_duration_date }" :max-date="form.end_duration_date"
                                        :format="format"
                                        :enable-time-picker="false" placeholder="Select Date"
                                    ></VueDatePicker>
                                    <div v-if="form.errors.start_duration_date" class="invalid-feedback d-block">{{ form.errors.start_duration_date }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Contract End Date<span class="required">*</span></label>
                                    <VueDatePicker
                                        @text-input="updateContractPeriod"
                                        v-model="form.end_duration_date"
                                        :model-value="form.end_duration_date" @update:model-value="updateContractPeriod"
                                        :class="{ 'is-invalid': form.errors.end_duration_date }"
                                        :min-date="form.start_duration_date"
                                        :format="format"
                                        :enable-time-picker="false" placeholder="Select Date"
                                    ></VueDatePicker>
                                    <div v-if="form.errors.end_duration_date" class="invalid-feedback d-block">{{ form.errors.end_duration_date }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="form-group">
                                    <label>Contract Created Date<span class="required">*</span></label>
                                    <VueDatePicker
                                        v-model="form.contract_created_date"
                                        :class="{ 'is-invalid': form.errors.contract_created_date }"
                                        :format="format"
                                        :enable-time-picker="false"
                                        placeholder="Select Date"
                                    ></VueDatePicker>
                                    <div v-if="form.errors.contract_created_date" class="invalid-feedback d-block">{{ form.errors.contract_created_date }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Contract Period (Months)</label>
                                    <input class="form-control" type="number" v-model="form.contract_period" :class="{ 'is-invalid': form.errors.contract_period }" @input="updateContractEndDate">
                                    <div v-if="form.errors.contract_period" class="invalid-feedback d-block">{{ form.errors.contract_period }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="form-group">
                                    <label>Customer Reference No.<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.customer_reference_no }" v-model="form.customer_reference_no" placeholder="Customer Reference No.">
                                    <div v-if="form.errors.customer_reference_no" class="invalid-feedback d-block">{{ form.errors.customer_reference_no }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Billing Cycle<span class="required">*</span></label>
                                    <Select2
                                        v-model="form.term_of_payment"
                                        :class="{ 'is-invalid': form.errors.term_of_payment }"
                                        :options="term_of_payments"
                                        placeholder="Select"
                                    >
                                    </Select2>
                                    <div v-if="form.errors.term_of_payment" class="invalid-feedback d-block">{{ form.errors.term_of_payment }}</div>
                                </div>
                            </div>
                            <!-- Customer Selection -->
                            <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Select Customer<span class="required">*</span></label>
                                        <div class="d-flex gap-1">
                                            <Select2
                                                v-model="form.customer_id"
                                                :class="{ 'is-invalid': form.errors.customer_id }"
                                                :options="customers_select"
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
                                            <a class="btn btn-purple--brounded flex-col" target="_blank" href="/customers">ADD NEW</a>
                                        </div>
                                        <p class="text-warning mt-5">*click add if the customer is not registered</p>
                                        <div v-if="form.errors.customer_id" class="invalid-feedback d-block">{{ form.errors.customer_id }}</div>
                                    </div>
                                </div>
                                <!-- Display Customer Details -->
                                <div v-if="form.customer" class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                    <div class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                        <span>Customer Details</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>Customer ID</th>
                                                    <td>{{ form.customer.code }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Name</th>
                                                    <td>{{ form.customer.name }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>POC</th>
                                                    <td>{{ form.customer.poc_first_name }} {{ form.customer.poc_last_name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>POC Contact</th>
                                                    <td>{{ form.customer.poc_phone }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Vehicle Selection -->
                            <div class="box mb-[2.6rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Select Vehicle Number<span class="required">*</span></label>
                                        <div class="d-flex gap-1">
                                            <Select2
                                                v-model="form.vehicle_ids"
                                                :class="{ 'is-invalid': form.errors.vehicle_ids }"
                                                :options="vehicles_select"
                                                placeholder="Select Vehicle"
                                                class="flex-fill"
                                                :settings="{
                                                    multiple: true,
                                                    ajax: {
                                                        url: '/data/vehicles',
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
                                                                        text: item.license_plate,
                                                                        id: item.id,
                                                                        data: item,
                                                                    };
                                                                })
                                                            };
                                                        }
                                                    }
                                                }"
                                                @select="(selected) => handleVehicleSelect(selected.data)"
                                            >
                                            </Select2>
                                            <a class="btn btn-purple--brounded flex-col" target="_blank" href="/vehicles">ADD NEW</a>
                                        </div>
                                        <p class="text-warning mt-5">*click add to register the vehicle with the customer</p>
                                        <div v-if="form.errors.vehicle_ids" class="invalid-feedback d-block">{{ form.errors.vehicle_ids }}</div>
                                    </div>
                                </div>
                                <!-- Display Vehicle Details -->
                                <div v-if="form.vehicles && form.vehicles.length > 0" v-for="vehicle in form.vehicles" :key="vehicle.id" class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                    <div class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                        <span>Vehicle Details</span>
                                    </div>
                                    <div class="row mb-[2.6rem]">
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>Vehicle Number</th>
                                                    <td>{{ vehicle.license_plate }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Type</th>
                                                    <td>{{ vehicle.type }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Vehicle Voltage</th>
                                                    <td>{{ vehicle.vehicle_voltage }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Chassis No.</th>
                                                    <td>{{ vehicle.chassis_no }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Vehicle Make</th>
                                                    <td>{{ vehicle.vehicle_make }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Model</th>
                                                    <td>{{ vehicle.model }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Vehicle Class</th>
                                                    <td>{{ vehicle.vehicle_class }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Type of Plate</th>
                                                    <td>{{ vehicle.type_of_plate }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-lg-6">
                                            <table class="table-1-el w-full">
                                                <tr>
                                                    <th>End User</th>
                                                    <td>{{ vehicle.end_user }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Chassis Salesperson / Contact No.</th>
                                                    <td>{{ vehicle.contact_no }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Chassis Delivery</th>
                                                    <td>{{ vehicle.chassis_delivery }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Refrigeration Serial Number</th>
                                                    <td>{{ vehicle.refrigeration_serial_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Other Info</th>
                                                    <td>{{ vehicle.other_info }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- PIC Info -->
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2</span>
                                <span>Confirmed By</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input class="form-control" type="text" v-model="form.pic_first_name" placeholder="First Name">
                                    <div v-if="form.errors.pic_first_name" class="invalid-feedback d-block">{{ form.errors.pic_first_name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" v-model="form.pic_email" placeholder="Email">
                                    <div v-if="form.errors.pic_email" class="invalid-feedback d-block">{{ form.errors.pic_email }}</div>
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


                        <div class="box mb-[2.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">3</span>
                                <span>Pricing</span>
                            </div>
                            <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                                <div style="min-height: unset" class="table-responsive border-[1px] border-solid border-[#EBE9F1] mb-20 pb-0 rounded-[.5rem]">
                                    <table class="table select-rows">
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Type <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Item ID <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Item Name <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>UOM</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Quantity <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Selling Price</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Discount (%) <span class="required text-danger">*</span></span>
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
                                                    <span>Total</span>
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
                                        <tr v-for="(price, index) in form.pricing" :key="index">
                                            <td>
                                                <!-- Storage Item ID -->
                                                <Select2 class="w-px-200" v-model="price.type" :class="{ 'is-invalid': form.errors[`pricing.${index}.type`] }" :options="item_types" placeholder="Select Type"/>
                                                <div v-if="form.errors[`pricing.${index}.type`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`pricing.${index}.type`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Storage Item ID -->
                                                <div v-if="price.type == 2" class="form-group select2-w-100 mb-0">
                                                    <Select2
                                                        v-model="price.product_id"
                                                        :class="{ 'is-invalid': form.errors[`pricing.${index}.product_id`] }"
                                                        :options="productsSelect"
                                                        placeholder="Select Item"
                                                        class="flex-fill w-px-300"
                                                        :settings="{
                                                            ajax: {
                                                                url: '/data/products',
                                                                dataType: 'json',
                                                                method: 'POST',
                                                                data: function (params) {
                                                                    return { search: params.term, _token: csrf };
                                                                },
                                                                processResults: function (data) {
                                                                    form.quantity = null;
                                                                    return {
                                                                        results: data.map(function (item) {
                                                                            return {
                                                                                    text: `${item.sku} | ${item.name}`,
                                                                                    id: item.id,
                                                                                    data: item,
                                                                                };
                                                                            })
                                                                        };
                                                                    },
                                                                },
                                                            }"
                                                        @select="selected => {price.product = selected.data; price.name = selected.data.name}"
                                                    />
                                                    <div v-if="form.errors[`pricing.${index}.product_id`]" class="invalid-feedback d-block">
                                                        {{ form.errors[`pricing.${index}.product_id`] }}
                                                    </div>
                                                </div>
                                                <!-- Storage Item ID -->
                                                <div v-else class="form-group select2-w-100 mb-0">
                                                    <Select2
                                                        v-model="price.assembly_id"
                                                        :class="{ 'is-invalid': form.errors[`pricing.${index}.assembly_id`] }"
                                                        :options="assembliesSelect"
                                                        placeholder="Select Item"
                                                        class="flex-fill w-px-300"
                                                        :settings="{
                                                            ajax: {
                                                                url: '/data/assemblies',
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
                                                        @select="selected => {price.assembly = selected.data; price.name = selected.data.name}"
                                                    />
                                                    <div v-if="form.errors[`pricing.${index}.assembly_id`]" class="invalid-feedback d-block">
                                                        {{ form.errors[`pricing.${index}.assembly_id`] }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="w-px-250 d-block">{{ price?.name }}</span>
                                            </td>
                                            <td>
                                                <span v-if="price.type == 2" class="w-px-250 d-block">{{ price?.product?.uom?.code }}</span>
                                                <span v-else class="w-px-250 d-block">{{ price?.assembly?.uom }}</span>
                                            </td>
                                            <td>
                                                <!-- Quantity -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="number" min="1" :max="price.storage_item?.quantity" v-model="price.quantity" placeholder="0" :class="{ 'is-invalid': form.errors[`pricing.${index}.quantity`] }">
                                                </div>
                                                <div v-if="form.errors[`pricing.${index}.quantity`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`pricing.${index}.quantity`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Price -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="number" v-model="price.price" placeholder="0" :class="{ 'is-invalid': form.errors[`pricing.${index}.price`] }">
                                                </div>
                                                <div v-if="form.errors[`pricing.${index}.price`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`pricing.${index}.price`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Quantity -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="number" min="0" :max="100" v-model="price.discount" placeholder="0" :class="{ 'is-invalid': form.errors[`pricing.${index}.discount`] }">
                                                </div>
                                                <div v-if="form.errors[`pricing.${index}.discount`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`pricing.${index}.discount`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Quantity -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="text" v-model="price.tax_code" :class="{ 'is-invalid': form.errors[`pricing.${index}.tax_code`] }">
                                                </div>
                                                <div v-if="form.errors[`pricing.${index}.tax_code`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`pricing.${index}.tax_code`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Quantity -->
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="number" min="0" :max="100" v-model="price.tax_value" placeholder="0" :class="{ 'is-invalid': form.errors[`pricing.${index}.tax_value`] }">
                                                </div>
                                                <div v-if="form.errors[`pricing.${index}.tax_value`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`pricing.${index}.tax_value`] }}
                                                </div>
                                            </td>
                                            <td>
                                                ${{ price?.total_amount }}
                                            </td>
                                            <td>
                                                <a v-if="form.pricing.length > 1" class="text-red" href="javascript:void(0);" @click="removeItem(index)">
                                                    <em class="fa-regular fa-trash-can"></em>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="btn-group mb-16">
                                    <a class="btn btn-purple--brounded" href="javascript:void(0)" @click="addItem">+ Add Item</a>
                                </div>
                                <div v-if="form.errors.pricing" class="invalid-feedback d-block">
                                    {{ form.errors.pricing }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-[5.6rem]">
                            <div class="form-group">
                                <label>Number of Service</label>
                                <Select2
                                    v-model="numberOfServices"
                                    :options="[
                                                    {
                                                        'id' : 1,
                                                        'text' : '1 Service'
                                                    },
                                                    {
                                                        'id' : 2,
                                                        'text' : '2 Service'
                                                    },
                                                    {
                                                        'id' : 3,
                                                        'text' : '3 Service'
                                                    },
                                                    {
                                                        'id' : 4,
                                                        'text' : '4 Service'
                                                    },
                                                    {
                                                        'id' : 5,
                                                        'text' : '5 Service'
                                                    },
                                                    {
                                                        'id' : 6,
                                                        'text' : '6 Service'
                                                    },
                                                ]"
                                    placeholder="Select"
                                >
                                </Select2>
                            </div>

                            <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                                <div style="min-height: unset; overflow: visible; overflow-x: visible" class="table-responsive border-[1px] border-solid border-[#EBE9F1] mb-20 pb-0 rounded-[.5rem]">
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
                                                    <span>Vehicle No <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Refrigeration Model <span class="required text-danger">*</span></span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Date of Install</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between gap-1 text-nowrap">
                                                    <span>Standby Unit</span>
                                                </div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in form.vehicle_item_details" :key="index">
                                            <td>
                                                <span class="w-px-250 d-block">{{ item?.pricing?.name }}</span>
                                            </td>
                                            <td>
                                                <div class="form-group select2-w-100 mb-0">
                                                    <Select2 v-model="item.vehicle_id" :options="form.vehicles" :class="{ 'is-invalid': form.errors[`vehicle_item_details.${index}.vehicle_id`] }" placeholder="Select"/>
                                                </div>
                                                <div v-if="form.errors[`vehicle_item_details.${index}.vehicle_id`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_item_details.${index}.vehicle_id`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group mb-0">
                                                    <input class="form-control" type="text" v-model="item.refrigeration_model" :class="{ 'is-invalid': form.errors[`vehicle_item_details.${index}.refrigeration_model`] }">
                                                </div>
                                                <div v-if="form.errors[`vehicle_item_details.${index}.refrigeration_model`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_item_details.${index}.refrigeration_model`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group mb-0">
                                                    <VueDatePicker
                                                        v-model="item.date_of_install"
                                                        :class="{ 'is-invalid': form.errors[`vehicle_item_details.${index}.date_of_install`] }"
                                                        :format="format"
                                                        :enable-time-picker="false"
                                                        placeholder="Select Date"
                                                    ></VueDatePicker>
                                                </div>
                                                <div v-if="form.errors[`vehicle_item_details.${index}.date_of_install`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_item_details.${index}.date_of_install`] }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group mb-0">
                                                    <div class="custom-checkbox style-1">
                                                        <input type="checkbox" :id="`standby_unit_${index}`" v-model="item.standby_unit" :class="{ 'is-invalid': form.errors[`vehicle_item_details.${index}.standby_unit`] }">
                                                        <label :for="`standby_unit_${index}`"></label>
                                                    </div>
                                                </div>
                                                <div v-if="form.errors[`vehicle_item_details.${index}.standby_unit`]" class="invalid-feedback d-block">
                                                    {{ form.errors[`vehicle_item_details.${index}.standby_unit`] }}
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-if="form.errors.pricing" class="invalid-feedback d-block">
                                    {{ form.errors.pricing }}
                                </div>
                            </div>
                        </div>

                        <!-- Contract Detail -->
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">4</span>
                                <span>Contract Detail</span>
                            </div>
                            <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div>
                                        <template v-for="(item, index) in form.maintain_services" :key="index">
                                            <div class="form-group" v-if="item.is_active">
                                                <label>Maintenance {{ index + 1 }}</label>
                                                <input class="form-control" type="month" v-model="item.date" placeholder="Select Month">
                                                <div v-if="form.errors[`maintain_services.${index}.date`]" class="invalid-feedback d-block">{{ form.errors[`maintain_services.${index}.date`] }}</div>
                                            </div>
                                        </template>
                                        <div v-if="form.errors.maintain_services" class="invalid-feedback d-block">{{ form.errors.maintain_services }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Remark</label>
                                <textarea class="form-control" v-model="form.remark" rows="3" placeholder="Remark"></textarea>
                                <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks and Summary -->
                    <div class="box border-0 mb-[5.6rem] pb-[5.6rem]">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="">
                                    <div v-if="form.sub_total" class="font-bold mb-14">Subtotal:</div>
                                    <div v-if="form.discount" class="font-bold mb-14">Discount:</div>
                                    <div v-if="form.tax_amount" class="font-bold mb-14">GST:</div>
                                    <div v-if="form.total" class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div v-if="form.total" class="font-bold mb-14">Total:</div>
                                </div>
                                <div class="text-right text-bold">
                                    <div v-if="form.sub_total" class="mb-14 pl-24 ml-20">${{ form.sub_total }}</div>
                                    <div v-if="form.discount" class="mb-14 pl-24 ml-20">${{ form.discount }}</div>
                                    <div v-if="form.tax_amount" class="mb-14 pl-24 ml-20">${{ form.tax_amount }}</div>
                                    <div v-if="form.total" class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-17 mb-17"></div>
                                    <div v-if="form.total" class="mb-14 pl-24 ml-20">${{ form.total }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                        <a class="btn btn-light btn-light--brounded" href="javascript:void(0)" @click="discard">Discard</a>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import {useForm, usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    csrf: String,
    status: Array,
    contract: Object,
    phoneCodes: Array,
    vehiclesSelect: Array,
    productsSelect: Array,
    assembliesSelect: Array,
    term_of_payments: Array,
    customer: Array,
    customers_select: Array,
    vehicles_select: Array,
    item_types: Array,
});

const form = useForm({
    service_contract_number: null,
    start_duration_date: null,
    end_duration_date: null,
    value: null,
    term_of_payment: null,
    customer: null,
    customer_id: null,
    vehicles: [],
    vehicle_ids: [],
    pic_first_name: null,
    pic_last_name: null,
    pic_title: null,
    pic_country_code: null,
    pic_phone_number: null,
    pic_email: null,
    maintain_services: [],
    remark: null,
    status: 1,
    contract_created_date: new Date().toISOString().split('T')[0],
    contract_period: null,
    customer_reference_no: null,
    sub_total: 0,
    tax_amount: 0,
    discount: 0,
    total: 0,
    pricing: [],
    vehicle_item_details: [],  // New field for vehicle item details
});

if (props.contract) {
    const {
        service_contract_number, start_duration_date, end_duration_date, value, term_of_payment, customer_id, vehicles,
        pic_first_name, pic_last_name, pic_title, pic_country_code, pic_phone_number, pic_email,
        remark, status, maintains, contract_created_date, customer_reference_no, sub_total, tax_amount, discount, total
    } = props.contract;

    form.service_contract_number = service_contract_number;
    form.start_duration_date = start_duration_date;
    form.end_duration_date = end_duration_date;
    form.value = value;
    form.term_of_payment = term_of_payment;
    form.customer_id = customer_id;
    form.vehicles = vehicles;
    form.pic_first_name = pic_first_name;
    form.pic_last_name = pic_last_name;
    form.pic_title = pic_title;
    form.pic_country_code = pic_country_code;
    form.pic_phone_number = pic_phone_number;
    form.pic_email = pic_email;
    form.remark = remark;
    form.status = status;
    form.contract_created_date = contract_created_date || new Date().toISOString().split('T')[0];
    form.customer_reference_no = customer_reference_no || null;
    form.sub_total = sub_total;
    form.tax_amount = tax_amount;
    form.discount = discount;
    form.total = total;
    form.maintain_services = maintains.map(service => ({
        ...service,
        date: service.date ? service.date.slice(0, 7) : ''
    }));

    if (props.contract.pricing) {
        form.pricing = props.contract.pricing;
    }
} else {
    form.maintain_services.push({
        id: null,
        date: null,
        is_active: true,
    });
}

if (!props.contract || !props.contract.pricing) {
    form.pricing.push({
        id: null,
        type: 1,
        product_id: null,
        assembly_id: null,
        product: null,
        assembly: null,
        price: null,
        name: null,
        quantity: null,
        tax_code: null,
        tax_amount: null,
        discount: null,
        discount_amount: null,
        subtotal_amount: null,
        total_amount: null,
    })
}

if (props.customer) {
    form.customer = props.customer;
    form.customer = props.customer;
}

if (props.contract && props.contract.vehicles) {
    form.vehicles = props.contract.vehicles;
    form.vehicle_ids = props.contract.vehicles.map(vehicle => vehicle.id);
}

if (props.contract && props.contract.vehicle_item_details) {
    form.vehicle_item_details = props.contract.vehicle_item_details;
}

const addItem = () => {
    form.pricing.push({
        id: null,
        type: 1,
        product_id: null,
        assembly_id: null,
        product: null,
        assembly: null,
        price: null,
        name: null,
        quantity: null,
        tax_code: null,
        tax_amount: null,
        discount: null,
        discount_amount: null,
        subtotal_amount: null,
        total_amount: null,
    })
}

const removeItem = (index) => {
    form.pricing.splice(index, 1);
}

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

const handleVehicleSelect = (selectedVehicle) => {
    const vehicleIndex = form.vehicles.findIndex(vehicle => vehicle.id.toString().toLowerCase() === selectedVehicle.id.toString().toLowerCase());

    if (vehicleIndex === -1) {
        // If the vehicle is not found, add it to the array
        form.vehicles.push(selectedVehicle);
    } else {
        // If the vehicle is already present, remove it
        form.vehicles = form.vehicles.filter(vehicle => vehicle.id !== selectedVehicle.id);
    }
};

const calculateAmounts = (index) => {
    const part = form?.pricing?.[index] ?? {};
    const price = Number(part?.price) || 0;
    const quantity = Number(part?.quantity) || 0;
    const tax = Number(part?.tax_value) || 0;
    const discount = Number(part?.discount) || 0;
    const rounding = 2;

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
    [() => form.pricing],
    async () => {
        const rounding = 2;

        form.sub_total = 0;
        form.tax_amount = 0;
        form.discount = 0;
        form.total = 0;

        for (const part of form.pricing) {
            const index = form.pricing.indexOf(part);
            await calculateAmounts(index);
        }

        form.sub_total = form.pricing.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.subtotal_amount) || 0);
        }, 0).toFixed(rounding);

        form.tax_amount = form.pricing.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.tax_amount) || 0);
        }, 0).toFixed(rounding);

        form.discount = form.pricing.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.discount_amount) || 0);
        }, 0).toFixed(rounding);

        form.total = form.pricing.reduce((sum, part) => {
            if (!part.is_foc) return sum + (parseFloat(part.total_amount) || 0);
        }, 0).toFixed(rounding);
    },
    {deep: true}
);

// Watch for changes in form.pricing and form.vehicle_ids and populate vehicle_item_details
watch(
    [() => form.pricing, () => form.vehicles],
    () => {
        const existingDetails = form.vehicle_item_details;
        form.vehicle_item_details = [];

        form.pricing.forEach((pricing, index) => {
            const existingDetail = existingDetails.find(
                detail => detail.service_contract_pricing_id === pricing.id
            );

            form.vehicle_item_details.push({
                id: index,
                service_contract_pricing_id: pricing.id,
                pricing: pricing,
                vehicle_id: existingDetail?.vehicle_id ?? null,
                vehicle: existingDetail?.vehicle ?? null,
                refrigeration_model: existingDetail?.refrigeration_model ?? '',
                date_of_install: existingDetail?.date_of_install ?? '',
                standby_unit: existingDetail?.standby_unit ?? false,
            });
        });
    },
    {deep: true}
);

const updateContract = () => {
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
            if (props.contract) {
                form.put(`/service-contracts/${props.contract.id}`);
            } else {
                form.post('/service-contracts');
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
            Inertia.get(`/service-contracts`, {
                preserveScroll: true,
            });
        }
    });
};

const numberOfServices = ref(form.maintain_services.length || 1);
const calculateMaintenanceDates = (startDate, numberOfServices) => {
    const start = new Date(startDate);
    const startMonth = start.getMonth(); // Get the starting month (0-11)
    const startYear = start.getFullYear(); // Get the starting year
    let serviceMonths = [];

    // Apply the maintenance frequency rules based on the number of services per year
    switch (Number(numberOfServices ?? 1)) {
        case 1:
            serviceMonths = [startMonth + 5]; // 1 Service: +6 months
            break;
        case 2:
            serviceMonths = [startMonth + 4, startMonth + 10]; // 2 Services: +5 months, +11 months
            break;
        case 3:
            serviceMonths = [startMonth + 2, startMonth + 6, startMonth + 10]; // 3 Services: +3 months, +7 months, +11 months
            break;
        case 4:
            serviceMonths = [startMonth + 1, startMonth + 4, startMonth + 7, startMonth + 10]; // 4 Services: +2, +5, +8, +11 months
            break;
        case 5:
            serviceMonths = [startMonth + 1, startMonth + 4, startMonth + 6, startMonth + 8, startMonth + 10]; // 5 Services: +2, +5, +7, +9, +11 months
            break;
        case 6:
            serviceMonths = [startMonth + 1, startMonth + 3, startMonth + 5, startMonth + 7, startMonth + 9, startMonth + 10]; // 6 Services: +2, +4, +6, +8, +10, +11 months
            break;
        default:
            serviceMonths = [startMonth]; // Default case if no valid number of services is provided
            break;
    }

    // Calculate actual maintenance month/year, adjust for overflow of months (if > 12)
    return serviceMonths.map(monthOffset => {
        const year = startYear + Math.floor(monthOffset / 12); // Adjust year if overflow happens
        const month = monthOffset % 12 + 1; // Normalize month to 1-12 format
        return `${year}-${month.toString().padStart(2, '0')}`; // Return date in YYYY-MM format
    });
};

const updateMaintainService = () => {
    if (form.start_duration_date) {
        const start = new Date(form.start_duration_date);
        const dates = calculateMaintenanceDates(start, numberOfServices.value);

        for (let i = 0; i < Number(numberOfServices.value ?? 1); i++) {
            console.log(dates[i])
            form.maintain_services[i]['date'] = dates[i] || null;
        }
    }
}

watch(numberOfServices, (newVal) => {
    if (newVal >= form.maintain_services.length) {
        form.maintain_services.forEach((item, index) => {
            form.maintain_services[index]['is_active'] = true;
        })

        for (let i = form.maintain_services.length; i < newVal; i++) {
            form.maintain_services.push({
                id: null,
                date: null,
                is_active: true,
            });
        }
    } else if (newVal < form.maintain_services.length) {
        for (let i = 0; i < form.maintain_services.length; i++) {
            form.maintain_services[i]['is_active'] = i + 1 <= newVal;
        }
    }

    updateMaintainService();
});

const updateContractPeriod = () => {
    if (form.start_duration_date && form.end_duration_date) {
        const startDate = new Date(form.start_duration_date);
        const endDate = new Date(form.end_duration_date);

        const startYear = startDate.getFullYear();
        const startMonth = startDate.getMonth();

        const endYear = endDate.getFullYear();
        const endMonth = endDate.getMonth();

        // Calculate the number of months between the years and months
        let months = (endYear - startYear) * 12 + (endMonth - startMonth);

        // If the end date's day is before the start date's day, we subtract one month
        if (endDate.getDate() < startDate.getDate()) {
            months--;
        }

        form.contract_period = months;
    }
}

const updateContractEndDate = () => {
    if (form.start_duration_date && form.contract_period !== null) {

        const startDate = new Date(form.start_duration_date); // Create a new Date object based on startDate
        const dayOfMonth = startDate.getDate(); // Get the day of the month

        // Calculate the new month and year manually
        const newMonth = startDate.getMonth() + Number(form.contract_period); // Add the contract period to the current month
        const newYear = startDate.getFullYear() + Math.floor(newMonth / 12); // Adjust the year if overflow happens
        const adjustedMonth = newMonth % 12; // Calculate the actual month (0-11)

        // Set the new month and year
        startDate.setFullYear(newYear, adjustedMonth);

        // Handle overflow of days in months (e.g., Feb 30 → adjust to Feb 28)
        if (startDate.getDate() < dayOfMonth) {
            startDate.setDate(0); // Set to the last day of the previous month if overflow occurred
        }

        // Assign the Date object in its full format (with timezone)
        form.end_duration_date = startDate; // Keep full Date object format
    }
};

</script>


