<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Invoice</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/project-orders">Project Order</Link>
                    </li>
                    <li>
                        <span>Invoice</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <label class="d-flex align-items-center gap-1">Show
                    <select name="paginate" class="form-select" v-model="paginate">
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
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th class="text-nowrap">Invoice No.</th>
                        <th class="text-nowrap">Quotation No.</th>
                        <th class="text-nowrap">Quotation Type</th>
                        <th class="text-nowrap">Project Order number</th>
                        <th class="text-nowrap">Customer Name</th>
                        <th class="text-nowrap">Customer Ref No</th>
                        <th class="text-nowrap">Vehicle No.</th>
                        <th class="text-nowrap">Invoice Date</th>
                        <th class="text-nowrap">Due Date</th>
                        <th class="text-nowrap">Billing Address</th>
                        <th class="text-nowrap">Amount Paid</th>
                        <th class="text-nowrap">Status</th>
                        <th class="text-nowrap">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="invoice in projectInvoices.data" :key="invoice.id">
                        <td>{{ invoice.code }}</td>
                        <td>{{ invoice.project_quotation.code }}</td>
                        <td>{{ invoice.project_quotation.project_order_type }}</td>
                        <td>{{ invoice.project_quotation.project_order?.code }}</td>
                        <td>{{ invoice.project_quotation.customer?.name }}</td>
                        <td>{{ invoice.project_quotation.customer?.code }}</td>
                        <td>{{ invoice.project_quotation.vehicle.license_plate }}</td>
                        <td>{{ $filters.formatDateTime(invoice.created_at) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="el-tag" :class="invoice.status_class">
                                {{ invoice.status }}
                            </div>
                        </td>
                        <td>
                            <div class="el-actions justify-end">
                            </div>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('update-project_appointment')">
                                        <Link :href="`/project-invoices/${invoice.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-project_appointment')">
                                        <a class="text-success" v-if="invoice.status !== 'Paid'" data-bs-toggle="modal" :data-bs-target="`#uploadPaid-${invoice.id}`" href="javascript:void(0)">
                                            <span class="icon"><img src="../../../assets/img/cash.svg" alt=""></span>
                                            <span>
                                                Paid
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                                <Modal :projectInvoice="invoice"></Modal>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ projectInvoices.from }} to {{ projectInvoices.to }} of {{ projectInvoices.total }} entries</p>
                <Pagination :links="projectInvoices.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import {usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Pagination from '../../Components/Pagination.vue';
import {Inertia} from '@inertiajs/inertia';
import debounce from 'lodash.debounce';
import Modal from "../ProjectInvoice/UploadForm.vue";

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    projectInvoices: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/project-invoices",
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
</script>
