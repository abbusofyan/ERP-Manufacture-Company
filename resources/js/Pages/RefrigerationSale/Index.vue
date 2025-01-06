<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Local Quotation</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link :href="`/${shipment}/refrigeration-sales`">Refrigeration</Link>
                    </li>
                    <li>
                        <span>Local Quotation</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <label class="d-flex align-items-center gap-1">Show
                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </label>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                    <div class="btn-group" v-if="permissions.includes('create-product')">
                        <Link class="btn btn-purple" :href="`/${shipment}/refrigeration-sales/create`">Add Quotation</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>#</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Client Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Amount</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="sale in refrigerationSales.data" :key="sale.id">
                        <td>
                            <div class="text-purple font-semibold">
                                <Link :href="`/${shipment}/refrigeration-sales/${sale.id}`"><span>#{{ sale.code }}</span></Link>
                            </div>
                        </td>
                        <td>
                            {{ sale.customer.name }}
                        </td>
                        <td>
                            {{ $filters.formatDateTime(sale.created_at) }}
                        </td>
                        <td>
                            <div v-if="sale.status_text" class="el-tag" :class="sale.status_class">
                                {{ sale.status_text ?? '' }}
                            </div>
                        </td>
                        <td>
                            ${{ sale.total }}
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-product')">
                                        <Link :href="`/${shipment}/refrigeration-sales/${sale.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-product') && (![3,4,5].includes(sale.status) || (sale.status == 5 && !sale.invoice))">
                                        <Link :href="`/${shipment}/refrigeration-sales/${sale.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('view-requisition')">
                                        <Link :href="`/${shipment}/refrigeration-sales/${sale.id}/generate-proforma-invoice`"><span class="icon"><img src="../../../assets/img/download.svg" alt=""></span><span class="text-nowrap">Generate Proforma Invoice</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('view-requisition') && sale.status != 6">
                                        <Link :href="`/${shipment}/refrigeration-sales/${sale.id}/generate-invoice`" :disabled="sale.invoice"><span class="icon"><img src="../../../assets/img/download.svg" alt=""></span><span>Generate Invoice</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('delete-product') && ![3,4,5].includes(sale.status)">
                                        <a @click="destroy(sale.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Void</span></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ refrigerationSales.from }} to {{ refrigerationSales.to }} of {{ refrigerationSales.total }} entries</p>
                <Pagination :links="refrigerationSales.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    shipment: Object,
    refrigerationSales: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        `/${shipment}/refrigeration-sales`,
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
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

watch([order, by, paginate], () => {
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

const destroy = (id) => {
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
            Inertia.delete(`/${props.shipment}/refrigeration-sales/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
