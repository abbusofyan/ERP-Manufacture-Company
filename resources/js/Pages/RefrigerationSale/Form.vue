<template>
    <Layout>
        <!-- Form submission -->
        <form @submit.prevent="refrigeration_sale ? form.post(`/${shipment}/refrigeration-sales/${refrigeration_sale.id}`) : form.post(`/${shipment}/refrigeration-sales`)">
            <div class="box pt-20 px-25 mb-17 rounded-md shadow-default bg-white">
                <div class="boxes">
                    <div class="box">
                        <div class="mb-[2.6rem]">
                            <div class="pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <div class="d-flex justify-between flex-wrap">
                                    <div class="right">
                                        <table class="table-1-el w-full">
                                            <tbody>
                                            <tr>
                                                <td class="text-18" style="font-weight: 500">Number</td>
                                                <td class="pl-24">
                                                    <div class="form-group mb-0">
                                                        <!-- Displaying code for refrigeration_sale if exists -->
                                                        <input class="form-control" type="text" :value="refrigeration_sale ? `#${refrigeration_sale.code}` : null" disabled>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Date:</td>
                                                <td class="pl-24">
                                                    <div class="form-group mb-0">
                                                        <!-- Displaying current date -->
                                                        <input class="form-control text-[#82868B]" type="text" :value="new Date().toLocaleDateString('en-GB')" disabled>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Quotation Status:</td>
                                                <td class="pl-24">
                                                    <Select2
                                                        v-model="form.status"
                                                        :class="{ 'is-invalid': form.errors.status }"
                                                        :options="status_args"
                                                        class="w-px-300"
                                                        placeholder="Select Quotation Status"
                                                        disabled="disabled"
                                                    />
                                                    <div v-if="form.errors.status" class="invalid-feedback d-block">{{ form.errors.status }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="box">
                                                        <div class="btn-group mt-20 justify-content-between align-items-center mb-10 gap-13">
                                                            <!-- Open image editor for CO -->
                                                            <a
                                                                class="btn btn-purple"
                                                                href="javascript:void(0)"
                                                                @click="openImageEditor(refrigeration_sale && refrigeration_sale.mockup_image_url ? `/storage/${refrigeration_sale.mockup_image_url}` : '/co_blank.png')"
                                                                :disabled="!form.customer"
                                                            >
                                                                {{ (refrigeration_sale && refrigeration_sale.mockup_image_url) || form.mockup_image_url ? 'Edit CO' : 'Create CO' }}
                                                            </a>
                                                        </div>
                                                        <!-- Image Editor Modal -->
                                                        <div class="modal modal-edit-image fade" id="dateRequiredModal" tabindex="-1" aria-labelledby="dateRequiredModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content p-24">
                                                                    <div class="modal-header">
                                                                        <div class="modal-title big-title text-center w-100" id="dateRequiredModalLabel">Edit Image</div>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Container for image editor -->
                                                                        <div ref="imageEditorContainer"></div>
                                                                    </div>
                                                                    <div class="modal-footer d-flex justify-content-center">
                                                                        <button type="button" class="btn btn-purple" @click="onImageEditorConfirm">Confirm</button>
                                                                        <button type="button" class="btn btn-red btn-red--brounded" @click="closeImageEditor" data-bs-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="text-15 w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">!</span>
                                <span>Quotation Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-17">
                                <div>
                                    <!-- Customer selection -->
                                    <div class="form-group">
                                        <label>Customer<span class="required">*</span></label>
                                        <Select2
                                            v-model="form.customer_id"
                                            :class="{ 'is-invalid': form.errors.customer_id }"
                                            :options="customers"
                                            placeholder="Select Customer"
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
                                        />
                                        <div v-if="form.errors.customer_id" class="invalid-feedback d-block">{{ form.errors.customer_id }}</div>
                                    </div>
                                    <div v-if="form.customer"
                                         class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                                        <div
                                            class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                            <span>Customer Details</span>
                                        </div>
                                        <table class="table-1-el w-full">
                                            <tbody>
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
                                                <td>{{ form.customer.poc_first_name }} {{ form.customer.poc_last_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>POC Contact</th>
                                                <td>{{ form.customer.poc_phone }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group">
                                        <label>Confirmed By<span class="required">*</span></label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.confirmed_by"
                                            :class="{ 'is-invalid': form.errors.confirmed_by }"
                                            placeholder="Enter name of the confirmer"
                                        />
                                        <div v-if="form.errors.confirmed_by" class="invalid-feedback d-block">{{ form.errors.confirmed_by }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] border-0">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.name"
                                        :class="{ 'is-invalid': form.errors.name }"
                                        placeholder="Enter name"
                                    />
                                    <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input
                                        class="form-control"
                                        type="email"
                                        v-model="form.email"
                                        :class="{ 'is-invalid': form.errors.email }"
                                        placeholder="Enter email"
                                    />
                                    <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-17">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input
                                        class="form-control"
                                        type="tel"
                                        v-model="form.phone_number"
                                        :class="{ 'is-invalid': form.errors.phone_number }"
                                        placeholder="Enter phone number"
                                    />
                                    <div v-if="form.errors.phone_number" class="invalid-feedback d-block">{{ form.errors.phone_number }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Headers and items -->
            <div class="box pt-20 mb-17 rounded-md shadow-default bg-white">
                <div class="box pb-25">
                    <div class="mb-[2.6rem]">

                        <!-- HEADER REPEATER -->
                        <div v-for="(header, index) in form.headers" :key="`${index}`" class="item">
                            <template v-if="!header.isDeleted">
                                <div class="px-25 pt-20">
                                    <div class="d-flex justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-20">
                                        <div class="text-18 font-medium pb-17 mb-17">
                                            <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">{{ index + 1 }}</span>
                                            <span>Header</span>
                                        </div>
                                        <a class="btn btn-red" v-if="form.headers.length > 1" href="javascript:void(0)" @click="removeHeader(index)">
                                            <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span>
                                            <span>Remove Header</span>
                                        </a>
                                    </div>

                                    <div class="text-15 text-primary font-medium pb-17 text-uppercase">
                                        <span>Quotation Details</span>
                                    </div>

                                    <div class="form-group">
                                        <label>Quotation Title</label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors[`headers.${index}.header_name`] }" v-model="header.header_name" placeholder="Quotation Title">
                                        <div v-if="form.errors[`headers.${index}.header_name`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.header_name`] }}</div>
                                    </div>
                                </div>


                                <div class="px-25">
                                    <div class="text-15 text-primary font-medium pb-17  border-0 border-t-[1px] border-solid border-[#EBE9F1] mb-14 pt-20">
                                        <span>VEHICLE SPECIFICATIONS</span>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-[3rem]">
                                        <div class="form-group">
                                            <label>Vehicle Number</label>
                                            <input
                                                class="form-control"
                                                type="tel"
                                                :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_license_plate`] }"
                                                v-model="header.vehicle_license_plate"
                                                @input="getVehicleByLicensePlate(header)"
                                                placeholder="Vehicle Number"
                                            >
                                            <div v-if="form.errors[`headers.${index}.vehicle_license_plate`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_license_plate`] }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label>Chassis No.</label>
                                            <input
                                                class="form-control"
                                                type="tel"
                                                :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_chassis_no`] }"
                                                v-model="header.vehicle_chassis_no"
                                                placeholder="Chassis No."
                                            >
                                            <div v-if="form.errors[`headers.${index}.vehicle_chassis_no`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_chassis_no`] }}</div>
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-[3rem]">
                                        <div class="form-group">
                                            <label>Vehicle Voltage</label>
                                            <input
                                                class="form-control"
                                                type="tel"
                                                :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_voltage`] }"
                                                v-model="header.vehicle_voltage"
                                                placeholder="Vehicle Voltage"
                                            >
                                            <div v-if="form.errors[`headers.${index}.vehicle_voltage`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_voltage`] }}</div>
                                        </div>
                                        <div class="grid md:grid-cols-2 gap-[3rem]">
                                            <div class="form-group">
                                                <label>Vehicle Make</label>
                                                <input
                                                    class="form-control"
                                                    type="tel"
                                                    :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_make`] }"
                                                    v-model="header.vehicle_make"
                                                    placeholder="Vehicle Make"
                                                >
                                                <div v-if="form.errors[`headers.${index}.vehicle_make`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_make`] }}</div>
                                            </div>
                                            <div class="form-group">
                                                <label>Model</label>
                                                <input
                                                    class="form-control"
                                                    type="tel"
                                                    :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_model`] }"
                                                    v-model="header.vehicle_model"
                                                    placeholder="Model"
                                                >
                                                <div v-if="form.errors[`headers.${index}.vehicle_model`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_model`] }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-[3rem]">
                                        <div class="grid md:grid-cols-2 gap-[3rem]">
                                            <div class="form-group">
                                                <label>Vehicle Class</label>
                                                <input
                                                    class="form-control"
                                                    type="tel"
                                                    :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_class`] }"
                                                    v-model="header.vehicle_class"
                                                    placeholder="Vehicle Class"
                                                >
                                                <div v-if="form.errors[`headers.${index}.vehicle_class`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_class`] }}</div>
                                            </div>
                                            <div class="form-group">
                                                <label>Type of Plate</label>
                                                <input
                                                    class="form-control"
                                                    type="tel"
                                                    :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_type_of_plate`] }"
                                                    v-model="header.vehicle_type_of_plate"
                                                    placeholder="Type of Plate"
                                                >
                                                <div v-if="form.errors[`headers.${index}.vehicle_type_of_plate`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_type_of_plate`] }}</div>
                                            </div>
                                        </div>
                                        <div class="grid md:grid-cols-2 gap-[3rem]">
                                            <div class="form-group">
                                                <label>Vehicle Mileage</label>
                                                <input
                                                    class="form-control"
                                                    type="tel"
                                                    :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_mileage`] }"
                                                    v-model="header.vehicle_mileage"
                                                    placeholder="Vehicle Mileage"
                                                >
                                                <div v-if="form.errors[`headers.${index}.vehicle_mileage`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_mileage`] }}</div>
                                            </div>
                                            <div class="form-group">
                                                <label>Running Hours</label>
                                                <input
                                                    class="form-control"
                                                    type="tel"
                                                    :class="{ 'is-invalid': form.errors[`headers.${index}.vehicle_running_hours`] }"
                                                    v-model="header.vehicle_running_hours"
                                                    placeholder="Running Hours"
                                                >
                                                <div v-if="form.errors[`headers.${index}.vehicle_running_hours`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.vehicle_running_hours`] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="type-box">
                                    <div class="px-25 mb-20">
                                        <div class="text-15 text-primary font-medium pb-17  border-0 border-t-[1px] border-solid border-[#EBE9F1] mb-14 pt-20">
                                            <span>LOAD HEADER SPEC</span>
                                        </div>

                                        <div class="form-group">
                                            <label>Spec Name</label>
                                            <Select2
                                                v-model="header.header_search_name"
                                                :class="{ 'is-invalid': form.errors[`headers.${index}.header_search_name`] }"
                                                :options="customers"
                                                placeholder="Type Name"
                                                :disabled="!allVehicleFieldsFilled(header)"
                                                :settings="{
                                                        ajax: {
                                                            url: '/data/vehicle-specs',
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
                                                @select="selected => setHeaderType(index, selected.data)"
                                            />

                                            <div v-if="!allVehicleFieldsFilled(header)" class="text-danger text-sm mt-10">
                                                Please fill in all vehicle details before selecting a spec.
                                            </div>
                                        </div>

                                        <p>* This is Optional, after selecting the spec, section below it will be filled in automatically and you can still add more part.</p>
                                    </div>

                                    <!-- TYPE REPEATER -->
                                    <template v-for="(type, index_type) in header.types" :key="`${index}-${index_type}`">
                                        <div class="box-item mb-17" v-if="!type.isDeleted">
                                            <div class="d-flex justify-content-between align-items-center gap-1">
                                                <div class="row flex-fill">
                                                    <div class="col-lg-8">
                                                        <div class="form-group w-100 mb-0">
                                                            <input
                                                                class="form-control text-bold text-[1.2rem]"
                                                                type="text"
                                                                :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.box`] }"
                                                                v-model="type.box"
                                                                placeholder="New Vehicle Spec"
                                                            >
                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.box`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.box`] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="form-group w-100 mb-0">
                                                            <input
                                                                class="form-control text-bold text-[1.2rem]"
                                                                type="text"
                                                                :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.quantity`] }"
                                                                v-model="type.quantity"
                                                                placeholder="Quantity"
                                                            >
                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.quantity`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.quantity`] }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <a class="btn btn-red btn-red--brounded" href="javascript:void(0)" @click="removeHeaderSpec(index, index_type)">
                                                    <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span>
                                                </a>
                                            </div>
                                            <hr class="mt-20">

                                            <div class="mt-20 pl-24 border-0 border-l-[1px] border-solid border-[#EBE9F1]">

                                                <!-- ASSEMBLY REPEATER -->
                                                <div v-for="(item, index_item) in type.items" :key="`${index}-${index_type}-${index_item}`" class="mb-17">
                                                    <template v-if="!item.isDeleted">
                                                        <div class="row">
                                                            <div class="col-lg-11">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <div class="form-group">
                                                                            <label>Type</label>
                                                                            <Select2 v-model="item.type" :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.type`] }" :options="item_types" placeholder="Select Type"/>
                                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.type`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_item}.type`] }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-5">
                                                                        <div v-if="Number(item.type) === 2" class="form-group">
                                                                            <label>Product Code | Product Name</label>
                                                                            <Select2
                                                                                v-model="item.product_id"
                                                                                :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.product_id`] }"
                                                                                :options="item.optionSelect ?? form.products"
                                                                                placeholder="Select Item"
                                                                                :settings="{
                                                                                    ajax: {
                                                                                        url: '/data/products',
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
                                                                                                        text: `${item.sku} | ${item.name}`,
                                                                                                        id: item.id,
                                                                                                        data: item,
                                                                                                    };
                                                                                                })
                                                                                            };
                                                                                        }
                                                                                    }
                                                                                }"
                                                                                @select="selected => {
                                                                                    item.description = selected.data.description;
                                                                                    item.uom = selected.data.uom.code;
                                                                                    item.unit_price = selected.data.selling_price;
                                                                                    item.quantity_on_stock = selected.data.stock;
                                                                                }"
                                                                            />
                                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.product_id`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.product_id`] }}</div>
                                                                        </div>
                                                                        <div v-else class="form-group">
                                                                            <label>Assembly Code | Assembly Name</label>
                                                                            <Select2
                                                                                v-model="item.assembly_id"
                                                                                :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.assembly_id`] }"
                                                                                :options="item.optionSelect ?? form.assemblies"
                                                                                placeholder="Select Item"
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
                                                                                                        text: `${item.code}`,
                                                                                                        id: item.id,
                                                                                                        data: item,
                                                                                                    };
                                                                                                })
                                                                                            };
                                                                                        }
                                                                                    }
                                                                                }"
                                                                                @select="selected => {
                                                                                    item.description = selected.data.product.description;
                                                                                    item.uom = selected.data.product.uom.code;
                                                                                    item.unit_price = selected.data.product.selling_price;
                                                                                    item.quantity_on_stock = selected.data.product.stock;
                                                                                }"
                                                                            />
                                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.assembly_id`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.assembly_id`] }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <div class="form-group">
                                                                            <label>UOM</label>
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                :class="{'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.uom`]}"
                                                                                v-model="item.uom"
                                                                                placeholder="UOM"
                                                                            >
                                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.uom`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.uom`] }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <div class="d-flex gap-3">
                                                                            <div class="form-group">
                                                                                <label>&nbsp;</label>
                                                                                <div class="custom-checkbox style-1">
                                                                                    <input type="checkbox" v-model="item.is_foc" :id="`foc-${index_item}`">
                                                                                    <label :for="`foc-${index_item}`">
                                                                                        FOC
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>&nbsp;</label>
                                                                                <div class="custom-checkbox style-1">
                                                                                    <input type="checkbox" v-model="item.hide" :id="`hide-${index_item}`">
                                                                                    <label :for="`hide-${index_item}`">
                                                                                        Hide
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-9">
                                                                        <div class="row">
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Quantity</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        type="text"
                                                                                        :class="{'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.quantity`]}"
                                                                                        v-model="item.quantity"
                                                                                        placeholder="Quantity"
                                                                                    >
                                                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.quantity`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.quantity`] }}</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>U/Price</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        type="text"
                                                                                        :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.unit_price`] }"
                                                                                        v-model="item.unit_price"
                                                                                        placeholder="U/Price"
                                                                                    >
                                                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.unit_price`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.unit_price`] }}</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Quantity on Stock</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        type="text"
                                                                                        :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.quantity_on_stock`] }"
                                                                                        v-model="item.quantity_on_stock"
                                                                                        placeholder="Quantity on Stock"
                                                                                    >
                                                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.quantity_on_stock`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.quantity_on_stock`] }}</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Tax Code</label>
                                                                                    <Select2
                                                                                        v-model="item.tax_code"
                                                                                        :options="[{id:'SR9', text:'SR9'},{id:'ZR', text:'ZR'}]"
                                                                                        placeholder="Select Tax Code"
                                                                                    />
                                                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.tax_code`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.tax_code`] }}</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Tax value (%)</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        type="text"
                                                                                        :class="{'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.tax_value`]}"
                                                                                        v-model="item.tax_value"
                                                                                        placeholder="0"
                                                                                        disabled
                                                                                    >
                                                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.tax_value`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.tax_value`] }}</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Tax Amount</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        type="text"
                                                                                        v-model="item.tax_amount"
                                                                                        disabled
                                                                                    >
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Discount Type</label>
                                                                                    <Select2
                                                                                        v-model="item.discount_type"
                                                                                        :options="[{id:'Flat', text:'Flat'},{id:'Percent', text:'Percent'}]"
                                                                                        placeholder="Select Discount Type"
                                                                                    />
                                                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.discount_type`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.discount_type`] }}</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Discount Amount</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        type="text"
                                                                                        :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.discount_amount`] }"
                                                                                        v-model="item.discount_amount"
                                                                                        placeholder="Discount Amount"
                                                                                    >
                                                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.discount_amount`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.discount_amount`] }}</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Discount Value</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        type="text"
                                                                                        v-model="item.discount_value"
                                                                                        disabled
                                                                                    >
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3 border-0 border-l-[1px] border-solid border-[#EBE9F1]">
                                                                        <div class="form-group">
                                                                            <label>Description</label>
                                                                            <textarea
                                                                                style="resize: vertical; min-height: 70px"
                                                                                class="form-control"
                                                                                type="text"
                                                                                :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.description`] }"
                                                                                v-model="item.description"
                                                                                placeholder="Description"
                                                                            ></textarea>
                                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.description`]"

                                                                                 class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.description`] }}</div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>MISC Description</label>
                                                                            <textarea
                                                                                style="resize: vertical; min-height: 70px"
                                                                                class="form-control"
                                                                                type="text"
                                                                                :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.items.${index_item}.misc_description`] }"
                                                                                v-model="item.misc_description"
                                                                                placeholder="Description"
                                                                            ></textarea>
                                                                            <div v-if="form.errors[`headers.${index}.types.${index_type}.items.${index_item}.misc_description`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.items.${index_item}.misc_description`] }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1">
                                                                <div class="d-flex justify-end mb-10" v-if="type.items.length > 1">
                                                                    <a class="btn btn-red btn-red--brounded" href="javascript:void(0)" @click="removeHeaderSpecItem(index, index_type, index_item)">
                                                                        <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pt-20"></div>
                                                    </template>
                                                </div>
                                                <!-- Add new assembly item -->
                                                <div class="mb-17">
                                                    <a class="btn btn-purple text-11" @click="addHeaderSpecItem(index, index_type)" href="javascript:void(0)">
                                                        <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10 6.5C10 6.8 9.8 7 9.5 7H6.5V10C6.5 10.3 6.3 10.5 6 10.5C5.7 10.5 5.5 10.3 5.5 10V7H2.5C2.2 7 2 6.8 2 6.5C2 6.2 2.2 6 2.5 6H5.5V3C5.5 2.7 5.7 2.5 6 2.5C6.3 2.5 6.5 2.7 6.5 3V6H9.5C9.8 6 10 6.2 10 6.5Z" fill="black"/>
                                                        </svg>
                                                        Add Item
                                                    </a>
                                                </div>

                                                <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-17"></div>

                                                <div class="form-group mb-0">
                                                    <label>Description</label>
                                                    <textarea
                                                        class="form-control"
                                                        type="text"
                                                        :class="{ 'is-invalid': form.errors[`headers.${index}.types.${index_type}.description`] }"
                                                        v-model="type.description"
                                                        placeholder="Description"
                                                        style="min-height: 2rem; resize: vertical; height: auto; padding-top: 10px; padding-bottom: 10px;"
                                                    ></textarea>
                                                    <div v-if="form.errors[`headers.${index}.types.${index_type}.description`]" class="invalid-feedback d-block">{{ form.errors[`headers.${index}.types.${index_type}.description`] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- Add new vehicle spec -->
                                    <div class="mb-17 px-24">
                                        <div v-if="form.errors[`headers.${index}.types`]" class="invalid-feedback d-block mb-20">{{ form.errors[`headers.${index}.types`] }}</div>

                                        <a class="btn btn-purple text-11" @click="addHeaderSpec(index,null)" href="javascript:void(0)">
                                            <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10 6.5C10 6.8 9.8 7 9.5 7H6.5V10C6.5 10.3 6.3 10.5 6 10.5C5.7 10.5 5.5 10.3 5.5 10V7H2.5C2.2 7 2 6.8 2 6.5C2 6.2 2.2 6 2.5 6H5.5V3C5.5 2.7 5.7 2.5 6 2.5C6.3 2.5 6.5 2.7 6.5 3V6H9.5C9.8 6 10 6.2 10 6.5Z" fill="black"/>
                                            </svg>
                                            Add New Vehicle Spec
                                        </a>
                                    </div>

                                </div>
                            </template>
                        </div>
                        <!-- END HEADER REPEATER -->

                    </div>
                </div>
            </div>

            <div class="box pt-20 px-24 pb-24 rounded-md shadow-default bg-white mb-17">
                <div class="box">
                    <a class="btn btn-purple btn-purple--brounded pt-30 pb-30 w-100" @click="addHeader" href="javascript:void(0)">
                        <span class="text-18">+</span>
                        Add Header
                    </a>
                </div>
            </div>

            <div class="box pt-36 px-24 pb-25 rounded-md shadow-default bg-white mb-17">
                <div class="box">
                    <div class="text-18 font-medium pb-17">
                        <span>Upload Photo</span>
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control-file mb-10"
                               :class="{ 'is-invalid': form.errors.photo }"
                               @input="form.photo = $event.target.files[0];"
                               accept="image/png, image/jpg, image/jpeg">
                        <div v-if="form.errors.photo" class="invalid-feedback d-block">{{ form.errors.photo }}</div>
                        <label class="note">Allowed file types: png, jpg, jpeg.</label>
                    </div>
                </div>
            </div>

            <div class="box pt-20 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="box">
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="w-100">
                                <tbody>
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
                                        <label>Validity date</label>
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
                                        <label>Estimated delivery date</label>
                                    </td>
                                    <td class="pb-24">
                                        <VueDatePicker
                                            v-model="form.estimated_delivery_date"
                                            :class="{ 'is-invalid': form.errors.estimated_delivery_date }"
                                            placeholder="Select estimated delivery date"
                                            :enable-time-picker="false"
                                            :format="$filters.format"
                                        />
                                        <div v-if="form.errors.estimated_delivery_date" class="invalid-feedback d-block">
                                            {{ form.errors.estimated_delivery_date }}
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
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex justify-content-end mb-[2.6rem]">
                                <table class="table-1-el w-full max-w-[24.5rem]">
                                    <tbody>
                                    <tr>
                                        <td class="text-nowrap">Sub Total</td>
                                        <td class="pl-24">
                                            <strong>${{ form.sub_total }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap">Discount</td>
                                        <td class="pl-24">
                                            <div class="form-group mb-0">
                                                <input
                                                    class="form-control"
                                                    type="number"
                                                    :class="{ 'is-invalid': form.errors.discount }"
                                                    v-model="form.discount"
                                                    placeholder="0.00"
                                                >
                                                <div v-if="form.errors.discount" class="invalid-feedback d-block">{{ form.errors.discount }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap">GST:</td>
                                        <td class="pl-24">
                                            <strong>${{ form.gst }}</strong>
                                        </td>
                                    </tr>
                                    <!-- Rounding field below GST, can accept negative values -->
                                    <tr>
                                        <td class="text-nowrap">Rounding</td>
                                        <td class="pl-24">
                                            <!-- Negative values allowed by simply using type="text" -->
                                            <input class="form-control" type="text" v-model="form.rounding" :class="{ 'is-invalid': form.errors.rounding }" placeholder="0.00 or -0.05">
                                            <div v-if="form.errors.rounding" class="invalid-feedback d-block">
                                                {{ form.errors.rounding }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pb-0" colspan="2">
                                            <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-17"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap">Total:</td>
                                        <td class="pl-24">
                                            <strong>${{ form.total }}</strong>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-17"></div>

                    <div class="form-group mb-20">
                        <label>Remarks :</label>
                        <textarea
                            class="form-control"
                            type="text"
                            :class="{ 'is-invalid': form.errors.note }"
                            v-model="form.note"
                            placeholder="Thanks for your business"
                            cols="1"
                            rows="6"
                            style="min-height: 2rem; resize: vertical; height: auto; padding-top: 10px; padding-bottom: 10px;"
                        ></textarea>
                        <div v-if="form.errors.note" class="invalid-feedback d-block">{{ form.errors.note }}</div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Footer :</label>
                        <textarea
                            class="form-control"
                            type="text"
                            :class="{ 'is-invalid': form.errors.footer }"
                            v-model="form.footer"
                            placeholder="Thanks for your business"
                            cols="1"
                            rows="6"
                            style="min-height: 2rem; resize: vertical; height: auto; padding-top: 10px; padding-bottom: 10px;"
                        ></textarea>
                        <div v-if="form.errors.footer" class="invalid-feedback d-block">{{ form.errors.footer }}</div>
                    </div>

                    <div class="form-wrap">
                        <div class="btn-group mt-30">
                            <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Quotation</button>
                            <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales`">Discard</Link>
                        </div>
                        <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                    </div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
// English only.
// Add rounding logic to total calculation
import Layout from "../../Components/Layout.vue";
import {Link, useForm, usePage} from "@inertiajs/inertia-vue3";
import {computed, nextTick, onMounted, onUnmounted, ref, watch} from "vue";
import debounce from "lodash.debounce";
import axios from 'axios';
import ImageEditor from 'tui-image-editor';

const gst_rate = usePage().props.value.gst_rate;

const props = defineProps({
    csrf: String,
    shipment: Object,
    refrigeration_sale: Object,
    depositOption: Array,
    paymentOption: Array,
    currencyOption: Array,
    item_types: Array,
    code: Array,
    status_args: Array,
    customers: Array,
    products: Array,
    assemblies: Array,
});

const form = useForm({
    customer_id: null,
    customer: null,
    confirmed_by: '',
    name: '',
    email: '',
    phone_number: '',
    status: 2,
    shipment: 1,
    photo: null,
    sub_total: 0,
    discount: 0,
    gst: 0,
    total: 0,
    note: null,
    footer: null,
    deposit_value: null,
    deposit_option: null,
    finance_amount: null,
    payment_method: null,
    terms: null,
    validity_date: null,
    estimated_delivery_date: null,
    currency: null,
    currency_rate: null,
    rounding: null, // Rounding field included
    mockup_image_url: null,
    products: [],
    assemblies: [],
    headers: [],
});

const headerObj = () => ({
    id: null,
    isDeleted: false,
    header_name: null,
    header_search_name: null,
    vehicle_id: null,
    vehicle_license_plate: null,
    vehicle_chassis_no: null,
    vehicle_voltage: null,
    vehicle_make: null,
    vehicle_model: null,
    vehicle_class: null,
    vehicle_type_of_plate: null,
    vehicle_mileage: null,
    vehicle_running_hours: null,
    types: []
});

const addHeader = () => {
    form.headers.push(headerObj());
};

const removeHeader = (index) => {
    form.headers[index].isDeleted = true;
    if (Array.isArray(form.headers[index].types)) {
        form.headers[index].types.forEach(type => {
            type.isDeleted = true;
            if (Array.isArray(type.items)) {
                type.items.forEach(item => {
                    item.isDeleted = true;
                });
            }
        });
    }
};

const headerSpecObj = (name, items = []) => ({
    id: null,
    isDeleted: false,
    box: name,
    description: null,
    quantity: null,
    items: items.length > 0 ? items : [headerSpecItemObj()],
});

const headerSpecItemObj = (data = {}) => ({
    id: null,
    isDeleted: false,
    type: data.type || 1,
    product_id: data.product_id || null,
    assembly_id: data.assembly_id || null,
    uom: data.uom || null,
    quantity: data.quantity || null,
    unit_price: data.unit_price || null,
    sub_total: data.sub_total || null,
    discount_type: data.discount_type || 'Flat',
    discount_value: data.discount_value || null,
    discount_amount: data.discount_amount || null,
    tax_code: data.tax_code || 'SR9',
    tax_value: data.tax_value || null,
    tax_amount: data.tax_amount || null,
    total: data.total || null,
    description: data.description || null,
    misc_description: data.misc_description || null,
    is_foc: data.is_foc || null,
    hide: data.hide || null,
    quantity_on_stock: data.quantity_on_stock || null,
});

const addHeaderSpec = (indexHeader, name, items = []) => {
    const newType = headerSpecObj(name, items);
    form.headers[indexHeader].types.push(newType);
};

const removeHeaderSpec = (indexHeader, indexType) => {
    form.headers[indexHeader].types[indexType].isDeleted = true;
    if (Array.isArray(form.headers[indexHeader].types[indexType].items)) {
        form.headers[indexHeader].types[indexType].items.forEach(item => {
            item.isDeleted = true;
        });
    }
};

const addHeaderSpecItem = (indexHeader, indexType, data = {}) => {
    // By default, new item will have tax_code='SR9' thus tax_value=9%
    const newAssembly = headerSpecItemObj(data);
    form.headers[indexHeader].types[indexType].items.push(newAssembly);
};

const removeHeaderSpecItem = (indexHeader, indexType, indexAssembly) => {
    form.headers[indexHeader].types[indexType].items[indexAssembly].isDeleted = true;
};

const products = computed(() => Array.isArray(props.products) ? props.products : []);
const assemblies = computed(() => Array.isArray(props.assemblies) ? props.assemblies : []);

const setHeaderType = (indexHeader, vehicleSpec) => {
    console.log(vehicleSpec)
    if (indexHeader === undefined || !vehicleSpec || !vehicleSpec.items) {
        return;
    }

    const groupedItems = vehicleSpec.items.reduce((acc, item) => {
        const headerId = item.header_id;
        if (!acc[headerId]) {
            acc[headerId] = [];
        }
        acc[headerId].push(item);
        return acc;
    }, {});

    Object.keys(groupedItems).forEach(headerId => {
        const items = groupedItems[headerId];
        const typeName = items[0].header.name;
        const itemsToAdd = [];

        items.forEach(item => {
            const isAssembly = !!item.assembly_id;
            const finalType = isAssembly ? 1 : 2;

            let description = item.product_name || null;
            let uom = null;
            let unit_price = null;
            let quantity_on_stock = null;
            let optionSelect = null;

            if (!isAssembly && item.product_id && item.material) {
                description = item.product_name || item.material.name;
                uom = item.material?.uom?.code || null;
                unit_price = item.material?.selling_price || null;
                quantity_on_stock = item.material?.stock || null;
                optionSelect = [{ id: item.material.id, text: item.material.sku  }]
            } else if (isAssembly && item.assembly) {
                description = item.product_name || item.assembly?.code || description;
                uom = item.assembly?.uom || null;
                unit_price = item?.assembly?.product?.selling_price;
                quantity_on_stock = null;
                optionSelect = [{ id: item.assembly.id, text: item.assembly.code  }]
            }

            itemsToAdd.push({
                id: null,
                isDeleted: false,
                type: finalType,
                product_id: item.product_id || null,
                assembly_id: item.assembly_id || null,
                uom: uom,
                quantity: item.qty,
                unit_price: unit_price,
                sub_total: null,
                discount_type: 'Flat',
                discount_value: 0,
                discount_amount: item?.discount,
                tax_code: 'SR9',
                tax_value: 9,
                tax_amount: null,
                total: null,
                description: description,
                misc_description: null,
                is_foc: item?.foc,
                hide: null,
                quantity_on_stock: quantity_on_stock,
                optionSelect: optionSelect,
            });
        });
        addHeaderSpec(indexHeader, typeName, itemsToAdd);
    });
};

const getVehicleByLicensePlate = debounce((header) => {
    axios.post(
        `/data/vehicle`,
        {
            license_plate: header.vehicle_license_plate,
        }
    ).then(response => {
        if (response.data !== '') {
            header.vehicle_id = response.data.id;
            header.vehicle_chassis_no = response.data.chassis_no;
            header.vehicle_voltage = response.data.vehicle_voltage;
            header.vehicle_make = response.data.vehicle_make;
            header.vehicle_model = response.data.model;
            header.vehicle_class = response.data.vehicle_class;
            header.vehicle_type_of_plate = response.data.type_of_plate;
        } else {
            header.vehicle_id = null;
            header.vehicle_chassis_no = null;
            header.vehicle_voltage = null;
            header.vehicle_make = null;
            header.vehicle_model = null;
            header.vehicle_class = null;
            header.vehicle_type_of_plate = null;
        }
    });
}, 500);

const allVehicleFieldsFilled = (header) => {
    return header.vehicle_license_plate &&
        header.vehicle_chassis_no &&
        header.vehicle_voltage &&
        header.vehicle_make &&
        header.vehicle_model &&
        header.vehicle_class &&
        header.vehicle_type_of_plate &&
        header.vehicle_mileage &&
        header.vehicle_running_hours;
};

const calculateTotals = () => {
    let subTotal = 0;
    let totalTaxAmount = 0;

    form.headers.forEach(header => {
        if (header.isDeleted) return;
        header.types.forEach(type => {
            if (type.isDeleted) return;
            type.items.forEach(item => {
                if (item.isDeleted) return;

                let taxValue = 0;
                if (item.tax_code === 'SR9') {
                    taxValue = 9;
                } else {
                    taxValue = 0;
                }

                const quantity = parseFloat(item.quantity) || 0;
                const unitPrice = parseFloat(item.unit_price) || 0;
                const discountType = item.discount_type;
                const discountAmount = parseFloat(item.discount_amount) || 0;

                let discountValue = 0;
                if (discountType === 'Percent') {
                    discountValue = (unitPrice * discountAmount / 100);
                } else {
                    discountValue = discountAmount;
                }

                // Ensure discount does not exceed unit price
                if (discountValue > unitPrice) {
                    discountValue = unitPrice;
                }

                const itemSubTotal = (unitPrice - discountValue) * quantity;
                const itemTaxAmount = itemSubTotal * (taxValue / 100);
                const itemTotal = itemSubTotal + itemTaxAmount;

                if (itemTotal < 0) {
                    discountValue = unitPrice;
                }

                const finalSubTotal = (unitPrice - discountValue) * quantity;
                const finalTaxAmount = finalSubTotal * (taxValue / 100);
                const finalTotal = finalSubTotal + finalTaxAmount;

                subTotal += finalSubTotal;
                totalTaxAmount += finalTaxAmount;

                item.tax_value = taxValue.toFixed(2);
                item.discount_value = discountValue.toFixed(2);
                item.tax_amount = finalTaxAmount.toFixed(2);
                item.sub_total = finalSubTotal.toFixed(2);
                item.total = finalTotal.toFixed(2);
            });
        });
    });

    form.sub_total = subTotal.toFixed(2);
    form.gst = totalTaxAmount.toFixed(2);
    // Include rounding in total calculation:
    // total = sub_total + gst - discount + rounding
    form.total = (
        parseFloat(form.sub_total) +
        parseFloat(form.gst) -
        parseFloat(form.discount || 0) +
        parseFloat(form.rounding || 0)
    ).toFixed(2);
};

watch(
    () => [form.headers, form.discount, form.rounding], // Also watch rounding for changes
    () => {
        calculateTotals();
    },
    {deep: true}
);

onMounted(() => {
    if (props.refrigeration_sale) {
        form.customer_id = props.refrigeration_sale.customer_id;
        form.customer = props.refrigeration_sale.customer;
        form.confirmed_by = props.refrigeration_sale.confirmed_by;
        form.name = props.refrigeration_sale.name;
        form.email = props.refrigeration_sale.email;
        form.phone_number = props.refrigeration_sale.phone_number;
        form.status = props.refrigeration_sale.status;
        form.shipment = props.refrigeration_sale.shipment;
        form.sub_total = props.refrigeration_sale.sub_total;
        form.discount = props.refrigeration_sale.discount;
        form.gst = props.refrigeration_sale.gst;
        form.total = props.refrigeration_sale.total;
        form.note = props.refrigeration_sale.note;
        form.footer = props.refrigeration_sale.footer;
        form.deposit_value = props.refrigeration_sale.deposit_value;
        form.deposit_option = props.refrigeration_sale.deposit_option;
        form.finance_amount = props.refrigeration_sale.finance_amount;
        form.payment_method = props.refrigeration_sale.payment_method;
        form.terms = props.refrigeration_sale.terms;
        form.validity_date = props.refrigeration_sale.validity_date;
        form.estimated_delivery_date = props.refrigeration_sale.estimated_delivery_date;
        form.currency = props.refrigeration_sale.currency;
        form.currency_rate = props.refrigeration_sale.currency_rate;
        form.rounding = props.refrigeration_sale.rounding;
        form.headers = props.refrigeration_sale.headers;
        form.products = props.products;
        form.assemblies = props.assemblies;
        calculateTotals();
    } else {
        form.headers.push(headerObj());
    }
});

// Image Editor Functions
const imageEditorInstance = ref(null);
const imageEditorContainer = ref(null);
const imageEditorImageURL = ref('');
const imageWidth = ref(0);
const imageHeight = ref(0);
const editedImageObjectURL = ref(null);

function openImageEditor(imageUrl) {
    if (form.mockup_image_url) {
        if (editedImageObjectURL.value) {
            URL.revokeObjectURL(editedImageObjectURL.value);
        }
        editedImageObjectURL.value = URL.createObjectURL(form.mockup_image_url);
        imageUrl = editedImageObjectURL.value;
    }

    imageEditorImageURL.value = imageUrl;
    new bootstrap.Modal(document.getElementById('dateRequiredModal')).show();

    const img = new Image();
    img.onload = () => {
        imageWidth.value = img.naturalWidth;
        imageHeight.value = img.naturalHeight;
        nextTick(() => {
            initializeImageEditor();
        });
    };
    img.src = imageUrl;
}

function initializeImageEditor() {
    if (imageEditorInstance.value) {
        imageEditorInstance.value.destroy();
    }

    const maxScreenWidth = window.innerWidth;
    const maxScreenHeight = window.innerHeight;
    let editorWidth = Math.min(imageWidth.value - 230, maxScreenWidth - 230);
    let editorHeight = Math.min(imageHeight.value, maxScreenHeight);

    imageEditorInstance.value = new ImageEditor(imageEditorContainer.value, {
        includeUI: {
            loadImage: {
                path: imageEditorImageURL.value,
                name: 'MockupCO',
            },
            theme: {},
            menu: ['draw'],
            initMenu: '',
            uiSize: {
                width: `${editorWidth}px`,
                height: `${editorHeight}px`,
            },
            menuBarPosition: 'bottom',
            brush: {
                width: 1,
                color: 'red',
            },
        },
        cssMaxWidth: editorWidth,
        cssMaxHeight: editorHeight,
        selectionStyle: {
            cornerSize: 20,
            rotatingPointOffset: 70,
        },
    });

    imageEditorInstance.value.on('load', () => {
        const brushWidthRangeInput = document.querySelector('.tui-image-editor-range-input');
        if (brushWidthRangeInput) {
            brushWidthRangeInput.min = 1;
            brushWidthRangeInput.value = 1;
        }
    });
}

function onImageEditorConfirm() {
    const dataUrl = imageEditorInstance.value.toDataURL();
    form.mockup_image_url = dataURLtoFile(dataUrl, 'edited_image.png');
    closeImageEditor();
}

function closeImageEditor() {
    const modalElement = document.getElementById('dateRequiredModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    modalInstance.hide();

    if (imageEditorInstance.value) {
        imageEditorInstance.value.destroy();
        imageEditorInstance.value = null;
    }

    if (editedImageObjectURL.value) {
        URL.revokeObjectURL(editedImageObjectURL.value);
        editedImageObjectURL.value = null;
    }
}

function dataURLtoFile(dataurl, filename) {
    const arr = dataurl.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, {type: mime});
}

onUnmounted(() => {
    if (imageEditorInstance.value) {
        imageEditorInstance.value.destroy();
        imageEditorInstance.value = null;
    }
    if (editedImageObjectURL.value) {
        URL.revokeObjectURL(editedImageObjectURL.value);
    }
});
</script>
