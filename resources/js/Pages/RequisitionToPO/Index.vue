<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Requisition to PO</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/requisitions-to-order">Requisition to PO</Link>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <div class="flex flex-wrap gap-16 justify-between">
                    <label class="d-flex align-items-center gap-1">Show
                        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                    <label class="d-flex align-items-center gap-1">
                        <select @change="fetchTransactionNumbers" class="form-select" v-model="transaction_type">
                            <option value="" selected>All</option>
                            <template v-for="(type, index) in props.transaction_types">
                                <option :value="index">{{type}}</option>
                            </template>
                        </select>
                    </label>
                    <div class="form-group d-flex align-items-center mt-0 mb-0">
                        <Select2
                            class="select2-w-100"
                            v-model="requisitionable_id"
                            placeholder="-- Select Transaction Number --"
                            :settings="{
                                    ajax: {
                                        url: '/data/requisitions/transaction-number',
                                        dataType: 'json',
                                        method: 'POST',
                                        data: function (params) {
                                          return {
                                            transaction_type: filters.transaction_type,
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
                        />
                        <div v-if="form.errors.production_order_id" class="invalid-feedback d-block">{{ form.errors.production_order_id }}</div>
                    </div>
                    <label class="d-flex align-items-center gap-1">
                        <button @click="resetFilter">reset</button>
                    </label>
                </div>

                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                    <DropdownButton v-if="permissions.includes('create-requisition') && form.items.length > 0">
                        <template #button>
                            <span>Action</span>
                        </template>
                        <div>
                            <button class="dropdown-item">
                                <Link href="/requisitions-to-order/convert" :method="'get'" :data="{ items: form.items }">Convert to new PO</Link>
                            </button>
                            <button class="dropdown-item">
                                <Link href="/requisitions-to-order/add-to-existing-po" :method="'get'" :data="{ items: form.items }">Add to existing PO</Link>
                            </button>
                        </div>
                    </DropdownButton>

                    <!-- <div class="btn-group" v-if="permissions.includes('create-requisition') && form.items.length > 0">
                        <Link class="btn btn-purple" href="/requisitions-to-order/convert" :method="'get'" :data="{ items: form.items }">Convert to PO</Link>
                        <a class="btn btn-danger btn-danger--brounded" @click="cancel" href="javascript:void(0)">Cancel item</a>
                    </div> -->
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="custom-checkbox style-1">
                                <input type="checkbox" id="all" v-model="form.select_all" @change="checkSelectAll(true)" value="1">
                                <label for="all"></label>
                            </div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Required Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>UoM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Vendor 1</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Vendor 2</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Vendor 3</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Transaction Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Transaction No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Request No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Requested By</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Warehouse</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Check</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Checked By</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Remark</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Action</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <template v-for="(item, index) in requisition_items.data" >
                            <tr :style="form.items.includes(item.id) ? 'background-color:#d9ddf9; color:black' : ''">
                                <td>
                                    <div class="custom-checkbox style-1" v-if="item.status == 1">
                                        <input type="checkbox" :id="`cb-${index}`" v-model="form.items" :value="item.id" @change="checkSelectAll(false)">
                                        <label :for="`cb-${index}`"></label>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    {{ $filters.formatDateTime(item.created_at) }}
                                </td>
                                <td class="text-nowrap">
                                    {{ $filters.formatDateTime(item.required_date) }}
                                </td>
                                <td class="text-nowrap">
                                    {{ item.product ? item.product.sku : 'OTHER' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ item.pending_order_qty }}
                                </td>
                                <td class="text-nowrap">
                                    {{ item.uom }}
                                </td>
                                <td class="text-nowrap">
                                    {{ item.product.vendor1?.vendor.code ?? '' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ item.product.vendor2?.vendor.code ?? '' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ item.product.vendor3?.vendor.code ?? '' }}
                                </td>
                                <td> {{item.requisition.type_text}} </td>
                                <td>{{ item.requisition.requisitionable ? item.requisition.requisitionable.code : '-' }}</td>
                                <td class="text-nowrap">
                                    <div class="text-purple font-semibold">
                                        <Link :href="`/requisitions/${item.requisition.id}`"><span>{{ item.requisition.code }}</span></Link>
                                    </div>
                                </td>
                                <td>
                                    {{ item.requisition.created_by.name }}
                                </td>
                                <td></td>
                                <td>
                                    <div class="custom-checkbox style-1">
                                        <input  type="checkbox" :id="`cb-ischecked-${index}`" v-model="item.is_checked" @click="itemChecked(index, item.id, $event)" :checked="item.checked_by ? 1 : 0" :disabled="item.checked_by ? true : false">
                                        <label :for="`cb-ischecked-${index}`"></label>
                                    </div>
                                </td>
                                <td>{{item.checked_by?.name}}</td>
                                <td>
                                    {{ item.remark }}
                                </td>
                                <td>
                                    <div class="more">
                                        <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                        <ul class="more-panel">
                                            <li>
                                                <a type="button" @click="openAddVendorReferenceModal(index)"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span> Add Vendor Cross Reference</a>
                                            </li>
                                            <li>
                                                <Link :href="`products/${item.product_id}/edit`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span> Edit Vendor Cross Reference</Link>
                                            </li>
                                            <li>
                                                <a type="button" @click="showEditRemarkModal(index)"><span class="icon fa fa-edit"></span> Edit Remark</a>
                                            </li>
                                            <li v-if="item.status == 1">
                                                <a type="button" @click="destroy(item.id)"><span class="icon fa fa-x"></span> Cancel</a>
                                            </li>
                                            <li v-if="item.status == 1">
                                                <Link :href="`requisitions-to-order/add-to-existing-po?items[]=${item.id}`"><span class="icon fa fa-plus"></span> Add to Existing PO</Link>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ requisition_items.from }} to {{ requisition_items.to }} of {{ requisition_items.total }} entries</p>
                <Pagination :links="requisition_items.links"/>
            </div>
        </div>

        <div class="modal fade" id="vendorCrossReferenceModal" role="dialog" style="overflow:hidden;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-24 pt-36 pb-30">
                    <div class="modal-header">
                        <div class="col-md-12 mt-3 text-center">
                            <div class="text-24 text-bold-500">
                                Add Vendor Cross Reference
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Vendor</label>
                            <Select2 class="select2-w-100"
                                v-model="formVendorCrossReference.vendor_id"
                                :settings="{
                                    dropdownParent:'#vendorCrossReferenceModal',
                                    ajax: {
                                        url: '/data/vendors',
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
                                                results: data.map(function (vendor) {
                                                    return {
                                                        text: `${vendor.code} | ${vendor.name}`,
                                                        id: vendor.id,
                                                        data: vendor,
                                                    };
                                                })
                                            };
                                        }
                                    }
                                }"
                                @select="(selected) => {
                                    selectedProduct = selected.data
                                }"
                                placeholder="Select Vendor"
                            />
                            <div v-if="formVendorCrossReference.errors.vendor_id" class="invalid-feedback d-block">{{ formVendorCrossReference.errors.vendor_id }}</div>
                        </div>
                        <div class="form-group">
                            <label>Item ID</label>
                            <input class="form-control" type="text" v-model="formVendorCrossReference.sku" disabled>
                        </div>
                        <div class="form-group">
                            <label>Item Name</label>
                            <input class="form-control" type="text" v-model="formVendorCrossReference.product_name" disabled>
                        </div>
                        <div class="form-group">
                            <label>Currency</label>
                            <Select2
                                v-model="formVendorCrossReference.currency"
                                class="select2-w-100"
                                :class="{ 'is-invalid': form.errors.type }"
                                placeholder="Select Currency"
                                :options="[{ id: 'SGD', text: 'SGD' }, { id: 'USD', text: 'USD' }]"
                            />
                            <div v-if="formVendorCrossReference.errors.currency" class="invalid-feedback d-block">{{ formVendorCrossReference.errors.currency }}</div>
                        </div>
                        <div class="form-group">
                            <label>Unit Price</label>
                            <input class="form-control" type="text" v-model="formVendorCrossReference.unit_price">
                            <div v-if="formVendorCrossReference.errors.unit_price" class="invalid-feedback d-block">{{ formVendorCrossReference.errors.unit_price }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 mb-3 text-center">
                            <a class="btn btn-purple mr-10" @click="submitAddVendorCrossReference" href="javascript:void(0)">Save</a>
                            <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModalAddVendorReference" href="javascript:void(0)">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editRemarkModal" role="dialog" style="overflow:hidden;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-24 pt-36 pb-30">
                    <div class="modal-header">
                        <div class="col-md-12 mt-3 text-center">
                            <div class="text-24 text-bold-500">
                                Add / Edit Remark
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Remark :</label>
                            <textarea rows="8" cols="80" v-model="formEditRemark.remark"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 mb-3 text-center">
                            <a class="btn btn-purple mr-10 disabled" @click="submitEditRemark" href="javascript:void(0)">Save</a>
                            <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModalEditRemark" href="javascript:void(0)">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {useToast} from "vue-toastification";
import DropdownButton from "../../Components/DropdownButton.vue";

const toast = useToast();

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    requisition_items: Object,
    filters: Object,
    csrf: String,
    transaction_types: Object,
});

const form = useForm({
    select_all: null,
    items: []
});

const formItemChecked = useForm({
    requisition_item_id: null,
    comment: null
});

const formVendorCrossReference = useForm({
    vendor_id: null,
    product_id: null,
    product_name: null,
    sku: null,
    currency: null,
    unit_price: null
});

const formEditRemark = useForm({
    requisition_item_id: null,
    remark: null,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);
let transaction_type = ref(props.filters.transaction_type);
let requisitionable_id = ref(props.filters.requisitionable_id);

const filter = () => {
    Inertia.get(
        "/requisitions-to-order",
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
            // transaction_type: transaction_type.value,
            // requisitionable_id: requisitionable_id.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

watch(
    search,
    debounce(() => {
        filter();
    }, 500)
);

watch([order, by, paginate, requisitionable_id], () => {
    filter();
});

watch([transaction_type], () => {
    requisitionable_id.value = null
    filter();
});

const resetFilter = () => {
    transaction_type.value = null
}

const sort = (data) => {
    order.value = data;

    if (by.value == "asc") {
        by.value = "desc";
    } else {
        by.value = "asc";
    }
};

const checkSelectAll = (isAll) => {
    if (form.items.length === props.requisition_items.data.length) {
        form.select_all = true;
        if (isAll) {
            form.select_all = false;
            form.items = [];
        }
    } else {
        form.select_all = false;
        if (isAll) {
            form.select_all = true;
            // form.items = props.requisition_items.data.map(item => item.id);
            form.items = props.requisition_items.data
              .filter(item => item.status == 1)
              .map(item => item.id);
        }
    }
};

const cancel = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/requisitions-to-order/update-status`, {
                items: form.items,
            }, {
                preserveScroll: true,
            });
        }
    });
};

const activeActionButtonIndex = ref({});
function openAddVendorReferenceModal(index) {
    formVendorCrossReference.reset();
    activeActionButtonIndex.value = index;
    const row = props.requisition_items.data[index]
    formVendorCrossReference.product_id = row.product.id
    formVendorCrossReference.product_name = row.product.name
    formVendorCrossReference.sku = row.product.sku
    const modal = new bootstrap.Modal(document.getElementById('vendorCrossReferenceModal'));
    modal.show();
}

const closeModalAddVendorReference = ref(null);
const submitAddVendorCrossReference = () => {
    let validated = true;

    if (!formVendorCrossReference.unit_price) {
        formVendorCrossReference.errors.unit_price = 'Unit price is required'
        validated = false
    }

    if (!formVendorCrossReference.vendor_id) {
        formVendorCrossReference.errors.vendor_id = 'Select a vendor'
        validated = false
    }

    if (!formVendorCrossReference.currency) {
        formVendorCrossReference.errors.currency = 'Select currency'
        validated = false
    }

    if (!validated) {
        return;
    }
    closeModalAddVendorReference.value.click();
    formVendorCrossReference.post(`/products/${formVendorCrossReference.product_id}/update-price`, {
        preserveScroll: true,
        preserveState: false
    })
}

const itemChecked = (index, id, e) => {
    formItemChecked.requisition_item_id = id

    formItemChecked.post(`/requisitions/item-checked`, {
        preserveScroll: true,
        preserveState: false
    })
    closeModal.value.click();
}

const tableData = ref({})

// onMounted(() => {
//     // console.log(props.requisition_items);
//     tableData.value = props.requisition_items.data.map(item => ({
//         ...item,
//         is_checked: item.checked_by ? true : false, // Convert checked_by to boolean
//     }));
// });

function showEditRemarkModal(index) {
    formEditRemark.reset();
    const row = props.requisition_items.data[index]
    formEditRemark.remark = row.remark
    formEditRemark.requisition_item_id = row.id
    const modal = new bootstrap.Modal(document.getElementById('editRemarkModal'));
    modal.show();
}

const closeModalEditRemark = ref(null);
const submitEditRemark = () => {
    formEditRemark.post(`/requisitions/${formEditRemark.requisition_item_id}/edit-remark`, {
        preserveScroll: true,
        preserveState: false
    })
    closeModalEditRemark.value.click();
}

const destroy = (item_id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Requisition item will be removed from the list",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/requisitions/${item_id}/cancel-pending-order`);
        }
    });
};
</script>
