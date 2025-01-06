<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Requisition to Goods Issue Note</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <span>Requisition to Goods Issue Note</span>
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
                </div>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                    <div class="btn-group" v-if="permissions.includes('create-requisition') && form.items.length > 0">
                        <Link @click="convertToGIN(form.items)" class="btn btn-purple">Convert to Goods Issue Note</Link>
                        <!-- <a class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#convertToGINModal" ref="openModal" href="javascript:void(0)">Convert to Goods Issue Note</a> -->
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <!-- <div class="custom-checkbox style-1">
                                <input type="checkbox" id="all" v-model="form.select_all" @change="checkSelectAll(true)" value="1">
                                <label for="all"></label>
                            </div> -->
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Requisition No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Date Required</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1"><span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>UOM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Transaction No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Request by</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <!-- <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Action</span></div>
                        </th> -->
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in requisition_items.data" :key="item.id">
                        <td>
                            <div class="custom-checkbox style-1" v-if="!item.pending_issue_qty">
                                <input type="checkbox" :id="`cb-${index}`" v-model="form.items" :value="item.id" @change="checkSelectAll(false)">
                                <label :for="`cb-${index}`"></label>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="text-purple font-semibold">
                                <Link :href="`/requisitions/${item.requisition.id}`"><span>{{ item.requisition.code }}</span></Link>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            {{ $filters.formatDateTime(item.requisition.created_at) }}
                        </td>
                        <td class="text-nowrap">
                            {{ $filters.formatDateTime(item.requisition.required_date) }}
                        </td>
                        <td class="text-nowrap">
                            {{ item.product ? item.product.sku : 'OTHER' }}
                        </td>
                        <td class="text-nowrap">
                            {{ item.product.name }}
                        </td>
                        <td class="text-nowrap">
                            {{ item.committed_qty }}
                        </td>
                        <td class="text-nowrap">
                            {{ item.uom }}
                        </td>
                        <td class="text-nowrap">
                            {{ item.requisition ? item.requisition.type_text : '' }}
                        </td>
                        <td>{{ item.requisition.requisitionable ? item.requisition.requisitionable.code : '-' }}</td>
                        <td class="text-nowrap">
                            {{ item.requisition.created_by?.name ?? '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ requisition_items.from }} to {{ requisition_items.to }} of {{ requisition_items.total }} entries</p>
                <Pagination :links="requisition_items.links"/>
            </div>
        </div>

        <div class="modal fade" id="convertToGINModal" role="dialog" style="overflow:hidden;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-24 pt-36 pb-30">
                    <form @submit.prevent="submitForm">
                        <div class="modal-header">
                            <div class="col-md-12 mt-3 text-center">
                                <div class="text-24 text-bold-500">Release Item</div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Issue Item To</label>
                                <Select2
                                    v-model="form.release_to"
                                    :class="{ 'is-invalid': form.errors.release_to }"
                                    placeholder="Select"
                                    class="select2-w-100"
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
                                                            text: item.name,
                                                            id: item.id,
                                                            data: item,
                                                        };
                                                    }),
                                                };
                                            },
                                        },
                                    }"
                                />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 mb-3 text-center">
                                <button type="submit" class="btn btn-purple mr-10" :disabled="isSubmitting">Release Item</button>
                                <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </Layout>
</template>

<script setup>
import {ref, watch, computed, onUpdated} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {useToast} from "vue-toastification";

const permissions = computed(() => usePage().props.value.auth.user.permissions);
const toast = useToast();
const updated = computed(() => usePage().props.value.flash.updated);

const props = defineProps({
    csrf: String,
    requisition_items: Object,
    filters: Object,
    transaction_types: Object,
});

const form = useForm({
    select_all: null,
    quantity: null,
    type: '1',
    pr_number: null,
    pr_quantity: null,
    release_to: null,
    items: [],
});

const convertToGIN = (items) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Transaction will be moved to goods issue note",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/requisitions-to-goods-issue-note/convert`, { items: items }, {
                preserveScroll: true,
                onError: (errors) => {
                    if (errors.converted) {
                        Swal.fire({
                            title: "Error",
                            text: errors.converted,
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    } else {
                        // Handle other types of errors if necessary
                        Swal.fire({
                            title: "Error",
                            text: "An unexpected error occurred. Please try again.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                },
                onFinish: () => {
                    // Optional: Perform actions after the request finishes
                },
            });
        }
    });
};

const closeModal = ref(null);

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);
let transaction_type = ref(props.filters.transaction_type);
let requisitionable_id = ref(props.filters.requisitionable_id);

const filter = () => {
    Inertia.get(
        "/requisitions-to-goods-issue-note",
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
            transaction_type: transaction_type.value,
            requisitionable_id: requisitionable_id.value,
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
            form.items = props.requisition_items.data
            .filter(item => !item.pending_issue_qty)
            .map(item => item.id);
        }
    }
    if (!selectTheSameTypeAndNumber()) {
        toast.error('Select item from the same transaction type and transaction number', {
            icon: "fa-solid fa-check",
            timeout: 5000,
        });
        form.items.pop()
    }
};

const selectTheSameTypeAndNumber = () => {
    let selectedRequisitionableIds = [];
    let selectedTypes = [];

    if (form.items.length === 1) {
        return true;
    }

    for (let i = 0; i < form.items.length; i++) {
        let selectedItemId = form.items[i];

        let item = props.requisition_items.data.find(dataItem => dataItem.id === selectedItemId);

        if (item) {
            if (!selectedRequisitionableIds.includes(item.requisition.requisitionable_id)) {
                selectedRequisitionableIds.push(item.requisition.requisitionable_id);
            }

            if (!selectedTypes.includes(item.requisition.type)) {
                selectedTypes.push(item.requisition.type);
            }
        } else {
            return false;
        }
    }

    if (selectedRequisitionableIds.length > 1 || selectedTypes.length > 1) {
        return false;
    }

    return true;
};




const closePopup = (data) => {
    form.quantity = null;
    $.fancybox.close()
};

onUpdated(() => {
    if (updated.value) {
        closePopup();
        Swal.fire({
            title: "Success !",
            text: "Your changes has been saved",
            icon: "success",
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: "OK",
        });
    }
})

// Loading state for the submit button
const isSubmitting = ref(false);

// Function to handle form submission
const submitForm = () => {
    isSubmitting.value = true;
    Inertia.get(`/requisitions-release`, {
        form
    }, {
        onSuccess: () => {

        },
        onError: (errors) => {

        }
    });

    closeModal.value.click();
};

const resetForm = () => {
    form.value.issue_to = null;
    form.value.errors = {};
};
</script>
