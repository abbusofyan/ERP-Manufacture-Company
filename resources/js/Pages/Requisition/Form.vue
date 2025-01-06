<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                <span v-if="requisition">Edit</span>
                <span v-else>Add</span>
                Requisition
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/requisitions">Requisition List</Link>
                    </li>
                    <li class="active">
                        <span v-if="requisition">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="submitHandler">
                <!-- <form @submit.prevent="requisition ? form.put(`/requisitions/${requisition.id}`) : form.post('/requisitions')"> -->
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span><span>New Purchase Requisition</span></div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Stock Purchase<span class="required">*</span></label>
                                        <Select2 v-model="form.type" class="order-number" :class="{ 'is-invalid': form.errors.type }" :options="types" placeholder="Type"/>
                                        <div v-if="form.errors.type" class="invalid-feedback d-block">{{ form.errors.type }}</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Urgent Orders<span class="required">*</span></label>
                                        <div class="flex flex-wrap gap-[3rem] items-center">
                                            <div class="custom-checkbox style-2">
                                                <input type="radio" name="aro" value="1" id="1" v-model="form.urgent_orders">
                                                <label for="1">Yes</label>
                                            </div>
                                            <div class="custom-checkbox style-2">
                                                <input type="radio" name="aro" value="0" id="2" v-model="form.urgent_orders">
                                                <label for="2">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div v-if="form.type == 3" class="col-6">
                                    <div class="form-group">
                                        <label>Production Order Number<span class="required">*</span></label>
                                        <Select2
                                            ref="select2Element"
                                            :disabled="requisition ? 'disabled' : null"
                                            v-model="form.model_id"
                                            class="order-number"
                                            :class="{ 'is-invalid': form.errors.production_order_id }"
                                            :options="orders"
                                            :settings="{
                                                ajax: {
                                                    url: '/data/production-orders',
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
                                                                    /* text: item.code + ' | ' + item.quotation.customer.name, */
                                                                    text: item.code,
                                                                    id: item.id,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                            placeholder="Select Order"/>
                                        <div v-if="form.errors.production_order_id" class="invalid-feedback d-block">{{ form.errors.production_order_id }}</div>
                                    </div>
                                </div>
                                <div v-if="form.type == 4" class="col-6">
                                    <div class="form-group">
                                        <label>project Order Number<span class="required">*</span></label>
                                        <Select2
                                            ref="select2Element"
                                            :disabled="requisition ? 'disabled' : null"
                                            v-model="form.model_id"
                                            class="order-number"
                                            :class="{ 'is-invalid': form.errors.model_id }"
                                            :options="orders"
                                            :settings="{
                                                ajax: {
                                                    url: '/data/project-orders',
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
                                                                    text: item.code + ' | ' + item.quotation.customer.name,
                                                                    id: item.id,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                            placeholder="Select Order"/>
                                        <div v-if="form.errors.model_id" class="invalid-feedback d-block">{{ form.errors.model_id }}</div>
                                    </div>
                                </div>
                                <div v-if="form.type == 7" class="col-6">
                                    <div class="form-group">
                                        <label>Service Order<span class="required">*</span></label>
                                        <Select2
                                            :disabled="requisition ? 'disabled' : null"
                                            v-model="form.model_id"
                                            class="order-number"
                                            :class="{ 'is-invalid': form.errors.service_order_id }"
                                            :options="serviceOrders"
                                            :settings="{
                                                ajax: {
                                                    url: '/data/service-orders',
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
                                                                    text: item.code,
                                                                    id: item.id,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                            placeholder="Select Order"/>
                                        <div v-if="form.errors.service_order_id" class="invalid-feedback d-block">{{ form.errors.service_order_id }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group sm:col-span-6">
                                        <label>Notes</label>
                                        <input type="text" v-model="form.note" :class="{ 'is-invalid': form.errors.note }" placeholder="Type Notes">
                                        <div v-if="form.errors.note" class="invalid-feedback d-block">{{ form.errors.note }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Date Required</label>
                                        <VueDatePicker @select="form.date = $event" v-model="form.date" :format="$filters.format" :class="{ 'is-invalid': form.errors.date }" :enable-time-picker="false" placeholder="Select date" :min-date="minDate"></VueDatePicker>
                                        <div v-if="form.errors.date" class="invalid-feedback d-block">{{ form.errors.date }}</div>
                                    </div>
                                </div>
                                <div class="col-6" v-if="form.type == 1">
                                    <div class="form-group sm:col-span-6">
                                        <label>Department</label>
                                        <Select2
                                            v-model="form.department"
                                            class="select2-w-100"
                                            :class="{ 'is-invalid': form.errors.department }"
                                            placeholder="Select Department"
                                            :options="[{ id: 'Production', text: 'Production' }, { id: 'Engineering', text: 'Engineering' }, { id: 'Service', text: 'Service' }]"
                                        />
                                        <div v-if="form.errors.department" class="invalid-feedback d-block">{{ form.errors.department }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-24 select2-w-100">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">2</span>
                                <span>Add Item ID</span>
                            </div>
                            <div class="has-add-box">
                                <div v-for="(item, index) in form.inventory_items" class="inner">
                                    <div class="box">
                                        <div class="flex items-start">
                                            <div class="w-full">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>Category</label>
                                                            <Select2
                                                                :options="item.product?.select2Category || {}"
                                                                v-model="item.category_id"
                                                                :class="{ 'is-invalid': form.errors[`inventory_items.${index}.category_id`] }"
                                                                placeholder="Select Category"
                                                                :settings="{
                                                                    ajax: {
                                                                        url: '/data/categories',
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
                                                                                results: data.map(function (cat) {
                                                                                    return {
                                                                                        text: `${cat.prefix} | ${cat.name}`,
                                                                                        id: cat.id,
                                                                                        data: cat,
                                                                                    };
                                                                                })
                                                                            };
                                                                        }
                                                                    },
                                                                    templateResult: function (data) {
                                                                        return data.text;
                                                                    }
                                                                }"
                                                                @select="(selected) => {
                                                                    if (!item.new_item) {
                                                                        item.uom_code = null
                                                                        item.product_id = null
                                                                        item.lead_time = null
                                                                        item.product_name = null
                                                                        item.store_id = null
                                                                        item.product_photo = null
                                                                    }
                                                                }"
                                                            />
                                                            <div v-if="form.errors[`inventory_items.${index}.category_id`]" class="invalid-feedback d-block">{{ form.errors[`inventory_items.${index}.category_id`] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Item ID</label>
                                                            <Select2
                                                                v-if="!item.new_item"
                                                                :options="item.product?.select2ItemID || {}"
                                                                v-model="item.product_id"
                                                                :class="{ 'is-invalid': form.errors[`inventory_items.${index}.product_id`] }"
                                                                placeholder="Select Product"
                                                                :settings="{
                                                                    ajax: {
                                                                        url: '/data/products',
                                                                        dataType: 'json',
                                                                        method: 'POST',
                                                                        data: function (params) {
                                                                            return {
                                                                                category_id: item.category_id,
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
                                                                                        sku: item.sku,
                                                                                        data: item,
                                                                                    };
                                                                                })
                                                                            };
                                                                        }
                                                                    },
                                                                    templateSelection: function (data) {
                                                                        return data.sku ? data.sku : data.text;
                                                                    },
                                                                    templateResult: function (data) {
                                                                        return data.text;
                                                                    }
                                                                }"
                                                                @select="(selected) => {
                                                                    const selectedProductIds = form.inventory_items.map(item => item.product_id);
                                                                    if (isSelectedItemDuplicate(selected.id, selectedProductIds)) {
                                                                        item.product_id = 0
                                                                    } else {
                                                                        let stock = selected.data.stock - selected.data.committed_qty;
                                                                        item.uom_code = selected.data.uom.code;
                                                                        item.product_name = selected.data.name;
                                                                        item.lead_time = selected.data.lead_time;
                                                                        item.store_id = null
                                                                        item.product_photo = selected.data.photos ?? []
                                                                        setItemRequiredDate(index, item.lead_time)
                                                                        setItemCategory(index, selected.data.category_id)
                                                                    }
                                                                }"
                                                            />
                                                            <input v-else type="text" :class="{ 'is-invalid': form.errors[`inventory_items.${index}.product_id`] }" v-model="item.product_id" disabled>
                                                            <div v-if="form.errors[`inventory_items.${index}.product_id`]" class="invalid-feedback d-block">{{ form.errors[`inventory_items.${index}.product_id`] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="form-group">
                                                            <div class="custom-checkbox style-1 mt-30">
                                                                <input type="checkbox" :id="`new-item-${index}`" v-model="item.new_item" @change="handleNewItem(index)">
                                                                <label :for="`new-item-${index}`">New Item</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div v-if="item.order" class="text-green text-bold mb-10">PO: {{ item.order.code }}</div>
                                                    <label>Item Name</label>
                                                    <input type="text" v-model="item.product_name" :class="{ 'is-invalid': form.errors[`inventory_items.${index}.product_name`] }" :disabled="!item.new_item">
                                                    <div v-if="form.errors[`inventory_items.${index}.product_name`]" class="invalid-feedback d-block">{{ form.errors[`inventory_items.${index}.product_name`] }}</div>
                                                </div>
                                                <div class="row flex flex-wrap gap-10 justify-between mt-[3.6rem]">
                                                    <div class="col-6">
                                                        <div class="form-group row">
                                                            <label class="col-form-label col-sm-4 mt-10">Warehouse<span class="required">*</span></label>
                                                            <div class="col-sm-6">
                                                                <Select2
                                                                    class="form-control"
                                                                    v-model="item.store_id"
                                                                    :class="{ 'is-invalid': form.errors[`inventory_items.${index}.store_id`] }"
                                                                    placeholder="Select Warehouse"
                                                                    :options="item.select2ItemID ?? warehouses"
                                                                    @select="(selected) => {
                                                                        setQuantity(index, selected.id)
                                                                    }"
                                                                />
                                                                <div v-if="form.errors[`inventory_items.${index}.store_id`]" class="invalid-feedback d-block">{{ form.errors[`inventory_items.${index}.store_id`] }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-form-label col-sm-4 mt-10">UOM<span class="required">*</span></label>
                                                            <div class="col-sm-6">
                                                                <input class="form-control" v-if="!item.new_item" type="text" disabled :value="item.uom_code">
                                                                <Select2
                                                                    v-else
                                                                    class="form-control"
                                                                    v-model="item.uom_code"
                                                                    :class="{ 'is-invalid': form.errors[`inventory_items.${index}.uom_code`] }"
                                                                    placeholder="Select UOM"
                                                                    :settings="{
                                                                        ajax: {
                                                                            url: '/data/uom',
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
                                                                                    results: data.map(function (uom) {
                                                                                        return {
                                                                                            text: `${uom.code}`,
                                                                                            id: uom.code,
                                                                                            sku: uom.code,
                                                                                        };
                                                                                    })
                                                                                };
                                                                            }
                                                                        },
                                                                    }"
                                                                />
                                                                <!-- <input class="form-control" type="text" v-model="item.product_name" :class="{ 'is-invalid': form.errors[`inventory_items.${index}.product_name`] }" :disabled="!item.new_item"> -->
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" v-if="!item.new_item">
                                                            <label class="col-form-label col-sm-4 mt-10">Available Quantity<span class="required">*</span></label>
                                                            <div class="col-sm-6">
                                                                <input class="form-control" type="text" disabled :value="item.product_id ? item.stock : 0">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" v-if="!item.new_item">
                                                            <label class="col-form-label col-sm-4 mt-10">Lead Time (days)<span class="required">*</span></label>
                                                            <div class="col-sm-6">
                                                                <input class="form-control" type="text" v-model="item.lead_time"  disabled>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-form-label col-sm-4 mt-10">Quantity<span class="required">*</span></label>
                                                            <div class="col-sm-6">
                                                                <input v-if="item.order_id == null" type="number" class="form-control" v-model="item.requested_qty" :class="{ 'is-invalid': form.errors[`inventory_items.${index}.requested_qty`] }">
                                                                <div v-else class="text-bold">
                                                                    <input class="form-control" type="text" disabled :value="item.requested_qty">
                                                                </div>
                                                                <div v-if="form.errors[`inventory_items.${index}.requested_qty`]" class="invalid-feedback d-block">{{ form.errors[`inventory_items.${index}.requested_qty`] }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-form-label col-sm-4 mt-10">Required Date<span class="required">*</span></label>
                                                            <div class="col-sm-6">
                                                                <VueDatePicker v-if="item.order_id == null"
                                                                    :format="$filters.format" v-model="item.date" format="Y-m-d"
                                                                    :class="{ 'is-invalid': form.errors.date }"
                                                                    :enable-time-picker="false" placeholder="Select date"
                                                                    :min-date="minDate"
                                                                    @update:model-value="setFormRequiredDate"
                                                                    >
                                                                </VueDatePicker>
                                                                <input v-else class="form-control" type="text" disabled :value="$filters.formatDate(item.date)">
                                                                <div v-if="form.errors[`inventory_items.${index}.date`]" class="invalid-feedback d-block">{{ form.errors[`inventory_items.${index}.date`] }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 float-right">
                                                        <div class="form-group row">
                                                            <label class="col-form-label col-sm-3 mt-10">Purpose</label>
                                                            <div class="col-sm-9">
                                                                <textarea
                                                                    v-if="item.order_id == null"
                                                                    v-model="item.remark"
                                                                    :class="{ 'is-invalid': form.errors[`inventory_items.${index}.remark`] }"
                                                                    cols="10"
                                                                    rows="3"
                                                                    placeholder="Type Here">
                                                                </textarea>
                                                                <div v-else>{{ item.remark }}</div>
                                                                <div v-if="form.errors[`inventory_items.${index}.remark`]" class="invalid-feedback d-block">
                                                                    {{ form.errors[`inventory_items.${index}.remark`] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-form-label col-sm-3 mt-10">Product Photo</label>
                                                            <div class="col-sm-9">
                                                                <div v-if="!item.new_item">
                                                                    <img
                                                                    v-if="item.product_photo && item.product_photo.length > 0"
                                                                    class="image float-right"
                                                                    :src="'/' + item.product_photo[0].path"
                                                                    alt="qr"
                                                                    style="max-height: 200px; width: auto;">
                                                                    <img v-else class="image float-right" :src="'/images/no-image.png'">
                                                                </div>
                                                                <div v-else class="fileContainer" v-if="item.new_item">
                                                                    <PhotoUpload @update:files="handleFilesUpdate" @click="selectedUploadPhotoIndex = index" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btn-group ml-20 mt-27 shrink-0">
                                                <a v-if="form.inventory_items.length > 1 && item.order_id == null" @click="removeInventoryItem(index, item)" class="btn btn-red btn-red--brounded" href="javascript:void(0)"><em class="fa-regular fa-xmark"></em><span>Delete</span></a>
                                            </div>
                                        </div>
                                        <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-purple" @click="addInventoryItem" href="javascript:void(0)"><em class="fa-regular fa-plus"></em><span>Add Inventory Item</span></a>
                                </div>
                            </div>
                            <div v-if="form.errors.inventory_items" class="invalid-feedback d-block">{{ form.errors.inventory_items }}</div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group mt-[5.6rem]">
                            <button class="btn btn-purple" type="submit" :disabled="form.processing" v-if="permissions.includes('approve-requisition')" @click.prevent="handleSubmit(2)">Save & Approve</button>
                            <button class="btn btn-purple" type="submit" :disabled="form.processing" v-else @click.prevent="handleSubmit(4)">Save</button>
                            <Link class="btn btn-light btn-light--brounded" href="/requisitions">Discard</Link>
                        </div>
                        <div class="btn-group mt-[5.6rem]">
                            <button class="btn btn-orange" type="submit" :disabled="form.processing" v-if="!permissions.includes('approve-requisition')" @click.prevent="openModal()">Send for Approval</button>
                        </div>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="sendForApprovalModal" role="dialog" style="overflow:hidden;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-24 pt-36 pb-30">
                    <div class="modal-header">
                        <div class="col-md-12 mt-3 text-center">
                            <div class="text-24 text-bold-500">
                                Send for Approval
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Manager</label>
                            <Select2 class="select2-w-100"
                                :settings="{
                                    multiple: true,
                                    ajax: {
                                        url: '/data/managers',
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
                                                results: data.map(function (user) {
                                                    return {
                                                        text: user.name,
                                                        id: user.id,
                                                        data: user,
                                                    };
                                                })
                                            };
                                        }
                                    }
                                }"
                                v-model="form.user_id"
                                placeholder="Select Manager"
                            />
                            <div v-if="form.errors.user_id" class="invalid-feedback d-block">{{ form.errors.user_id }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 mb-3 text-center">
                            <button class="btn btn-purple mr-10 disabled" @click="handleSubmit(1)" href="javascript:void(0)">Submit</button>
                            <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {Link, useForm, usePage} from "@inertiajs/inertia-vue3";
import {onMounted, ref, computed} from 'vue';
import {Inertia} from "@inertiajs/inertia";
import axios from 'axios';
import PhotoUpload from "../../Components/PhotoUpload.vue";
import Swal from "sweetalert2";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const select2Element = ref(null);

const selectedUploadPhotoIndex = ref({})

const props = defineProps({
    csrf: String,
    types: Array,
    orders: Array,
    serviceOrders: Array,
    requisition: Object,
    warehouses:Array,
});

const form = useForm({
    status: 1,
    type: 1,
    department: null,
    order_id: null,
    model_id: null,
    urgent_orders: 0,
    date: null,
    note: null,
    inventory_items: [],
    delete_items: [],
    user_id: null,
});

const addInventoryItem = () => {
    form.inventory_items.push({
        new_item: false,
        product_id: null,
        category_id: null,
        product_name: null,
        requested_qty: null,
        date: null,
        remark: null,
        store_id:null,
        stock: 'N/A',
        uom_code: 'N/A',
        photos: [],
        options: []
    })
}

const removeInventoryItem = (index, item) => {
    form.inventory_items.splice(index, 1)

    if (item['id']) {
        form.delete_items.push(item['id'])
    }
}

const handleNewItem = async (index) => {
    const new_item = form.inventory_items[index].new_item

    form.inventory_items[index].product_id = null
    form.inventory_items[index].product_name = null
    form.inventory_items[index].uom_code = null
    form.inventory_items[index].stock = 0

    // if (new_item) {
    //     try {
    //         const response = await axios.post('/products/get-last-non-inventory-code', {
    //             _token: props.csrf,
    //         });
    //         setNewItemID(response.data)
    //     } catch (error) {
    //         console.error('Error fetching assembly items:', error);
    //     }
    // }

}

const generateNewItemID = () => {
    const now = new Date();

    const year = now.getFullYear().toString().slice(-2);
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    const formattedRunningNumber = '000';

    return `OTH<${year}${month}${day}><${formattedRunningNumber}>`;
}

const setNewItemID = (lastCode) => {
    const items = form.inventory_items
    let countNewItem = 0;
    if (!lastCode) {
        lastCode = generateNewItemID()
    }
    items.forEach((item, i) => {
        if (item.new_item) {
            const regex = /^OTH<(\d{6})><(\d{3})>$/;
            const match = lastCode.match(regex);

            if (!match) {
                throw new Error("Invalid code format");
            }

            const datePart = match[1];
            let runningNumber = parseInt(match[2], 10);

            let nextNumber = runningNumber + (countNewItem + 1)

            const formattedRunningNumber = String(nextNumber).padStart(3, '0');
            let nextItemID = `OTH<${datePart}><${formattedRunningNumber}>`;
            item.product_id = nextItemID;
            item.stock = 0;

            countNewItem++;
        }
    });
}

const handleFilesUpdate = (files) => {
    form.inventory_items[selectedUploadPhotoIndex.value].photos = files
    // console.log(selectedUploadPhotoIndex.value);
    // form.photos = files;
};

const setItemRequiredDate = (index, lead_time) => {
    let formatted_date = '';
    let today = '';

    if (lead_time) {
        today = new Date();

        today.setDate(today.getDate() + parseInt(lead_time));
        form.inventory_items[index].date = today

        setFormRequiredDate()
    }
};

const setFormRequiredDate = () => {
    const item_required_dates = form.inventory_items
                                    .map(item => item.date)
                                    .filter(date => date !== null);

    const date_objects = item_required_dates.map(dateStr => new Date(dateStr));
    form.date = new Date(Math.min(...date_objects));
}

const handleSubmit = (status) => {
    if (status == 1) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('sendForApprovalModal'));
        modal.hide();
    }
    form.status = status;
    if (usePage().url.value.split('/').pop() == 'create' || usePage().url.value.split('/').pop() == 'duplicate') {
        form.post('/requisitions')
    } else {
        form.put(`/requisitions/${props.requisition.id}`)
    }
}

onMounted(() => {
    if (props.requisition) {
        form.status = props.requisition.status;
        form.type = props.requisition.type;
        form.order_id = props.requisition.order_id;
        form.department = props.requisition.department
        form.approved_by = props.requisition.approved_by;
        form.urgent_orders = props.requisition.is_urgent;
        form.date = props.requisition.required_date;
        form.note = props.requisition.note;
        form.inventory_items = props.requisition.requisition_items.map(item => ({
            ...item,
            date: props.requisition.requisition_items[0].required_date,
            category_id: item.product.category_id,
            lead_time: item.product.lead_time,
            product_photo: item.product.photos,
            uom_code: item.product.uom.code
        }));
    }
    console.log(form.inventory_items);
});

function openModal() {
    const modal = new bootstrap.Modal(document.getElementById('sendForApprovalModal'));
    modal.show();
}

const formatDateString = (dateStr) => {
    const date = new Date(dateStr);
    date.setHours(0, 0, 0);
    return date;
}

const minDate = ref(new Date());
minDate.value.setHours(0, 0, 0, 0);

const setQuantity = async (index, store_id) => {
    let product_id = form.inventory_items[index].product_id
    try {
        const response = await axios.get('/products/' + product_id + '/' + store_id + '/get-stock-by-warehouse');
        if (response.data) {
            form.inventory_items[index].stock = response.data
        } else {
            form.inventory_items[index].stock = 0
        }
    } catch (error) {
        console.error('Error fetching assembly items:', error);
    }
};

const setItemCategory = async (index, category_id) => {
    try {
        const response = await axios.post('/data/categories', {
            _token: props.csrf,
            id: category_id
        });

        const options = Array.isArray(response.data)
            ? response.data.map(category => ({
                  id: category.id,
                  text: category.prefix + ' | ' + category.name
              }))
            : [{ id: response.data.id, text: response.data.name }];

        form.inventory_items[index].options = options;
        form.inventory_items[index].category_id = category_id
    } catch (error) {
        console.error('Error fetching assembly items:', error);
    }
}

const isSelectedItemDuplicate = (selectedItemId, selectedItemIds) => {
    const normalizedId = String(selectedItemId);
    const occurrenceCount = selectedItemIds.filter(id => id === normalizedId).length;
    if (occurrenceCount > 1) {
        Swal.fire({
            title: "Cannot select the same item",
            icon: "warning",
            confirmButtonColor: "#ea5455",
            cancelButtonColor: "#009CDB",
            confirmButtonText: "OK!",
        })
    }
    return occurrenceCount > 1;
}

</script>
