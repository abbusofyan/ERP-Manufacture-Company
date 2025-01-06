<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Store</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <span>Customer</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <div class="d-flex gap-16">
                    <label class="d-flex align-items-center gap-1">Show
                        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                    <label class="d-flex align-items-center gap-1">
                        <a v-if="deleted" class="btn btn-purple btn-purple--brounded" @click="deleted=0" href="javascript:void(0)">Active Customer</a>
                        <a v-else class="btn btn-red btn-red--brounded" @click="deleted=1" href="javascript:void(0)">Inactive Customer</a>
                    </label>
                </div>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                    <div class="btn-group more-button" v-if="permissions.includes('create-customer')">
                        <a class="btn btn-purple" href="javascript:void(0)">Add New Customer <span class="icon fa-regular fa-chevron-down"></span></a>
                        <ul class="account-panel">
                            <li>
                                <div class="customer-type-icon">
                                    <Link href="/customers/organisation/create">
                                        <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_6561_199795" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="1" y="-1" width="12" height="17">
                                                <path
                                                    d="M3.75 2.75C3.75 2.5 3.96875 2.25 4.25 2.25H5.75C6 2.25 6.25 2.5 6.25 2.75V4.25C6.25 4.53125 6 4.75 5.75 4.75H4.25C3.96875 4.75 3.75 4.53125 3.75 4.25V2.75ZM9.75 2.25C10 2.25 10.25 2.5 10.25 2.75V4.25C10.25 4.53125 10 4.75 9.75 4.75H8.25C7.96875 4.75 7.75 4.53125 7.75 4.25V2.75C7.75 2.5 7.96875 2.25 8.25 2.25H9.75ZM3.75 6.75C3.75 6.5 3.96875 6.25 4.25 6.25H5.75C6 6.25 6.25 6.5 6.25 6.75V8.25C6.25 8.53125 6 8.75 5.75 8.75H4.25C3.96875 8.75 3.75 8.53125 3.75 8.25V6.75ZM9.75 6.25C10 6.25 10.25 6.5 10.25 6.75V8.25C10.25 8.53125 10 8.75 9.75 8.75H8.25C7.96875 8.75 7.75 8.53125 7.75 8.25V6.75C7.75 6.5 7.96875 6.25 8.25 6.25H9.75ZM1 1.5C1 0.40625 1.875 -0.5 3 -0.5H11C12.0938 -0.5 13 0.40625 13 1.5V13.5C13 14.625 12.0938 15.5 11 15.5H3C1.875 15.5 1 14.625 1 13.5V1.5ZM2.5 1.5V13.5C2.5 13.7812 2.71875 14 3 14H5.5V12C5.5 11.1875 6.15625 10.5 7 10.5C7.8125 10.5 8.5 11.1875 8.5 12V14H11C11.25 14 11.5 13.7812 11.5 13.5V1.5C11.5 1.25 11.25 1 11 1H3C2.71875 1 2.5 1.25 2.5 1.5Z"
                                                    fill="#1C1C1E"/>
                                            </mask>
                                            <g mask="url(#mask0_6561_199795)">
                                                <rect y="0.5" width="14" height="14" fill="#6E6B7B"/>
                                            </g>
                                        </svg>
                                        Organisation
                                    </Link>
                                </div>
                            </li>
                            <li>
                                <div class="customer-type-icon">
                                    <Link href="/customers/individual/create">
                                        <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_6561_199801" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="1" y="0" width="13" height="15">
                                                <path
                                                    d="M8.8125 8.5625C11.4648 8.5625 13.625 10.7227 13.625 13.375C13.625 13.8672 13.2148 14.25 12.75 14.25H2.25C1.75781 14.25 1.375 13.8672 1.375 13.375C1.375 10.7227 3.50781 8.5625 6.1875 8.5625H8.8125ZM2.6875 12.9375H12.2852C12.0664 11.2148 10.5898 9.875 8.8125 9.875H6.1875C4.38281 9.875 2.90625 11.2148 2.6875 12.9375ZM7.5 7.25C5.55859 7.25 4 5.69141 4 3.75C4 1.83594 5.55859 0.25 7.5 0.25C9.41406 0.25 11 1.83594 11 3.75C11 5.69141 9.41406 7.25 7.5 7.25ZM7.5 1.5625C6.26953 1.5625 5.3125 2.54688 5.3125 3.75C5.3125 4.98047 6.26953 5.9375 7.5 5.9375C8.70312 5.9375 9.6875 4.98047 9.6875 3.75C9.6875 2.54688 8.70312 1.5625 7.5 1.5625Z"
                                                    fill="#1C1C1E"/>
                                            </mask>
                                            <g mask="url(#mask0_6561_199801)">
                                                <rect y="0.5" width="14" height="14" fill="#6E6B7B"/>
                                            </g>
                                        </svg>
                                        Individual
                                    </Link>
                                </div>
                            </li>
                            <li>
                                <div class="customer-type-icon">
                                    <Link href="/customers/salesperson/create">
                                        <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_6561_199807" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="1" y="0" width="13" height="15">
                                                <path
                                                    d="M4 3.75C4 1.83594 5.55859 0.25 7.5 0.25C9.41406 0.25 11 1.83594 11 3.75C11 5.69141 9.41406 7.25 7.5 7.25C5.55859 7.25 4 5.69141 4 3.75ZM9.6875 3.75C9.6875 2.54688 8.70312 1.5625 7.5 1.5625C6.26953 1.5625 5.3125 2.54688 5.3125 3.75C5.3125 4.98047 6.26953 5.9375 7.5 5.9375C8.70312 5.9375 9.6875 4.98047 9.6875 3.75ZM7.08984 10.0938L6.1875 8.5625H8.8125L7.88281 10.0938L8.32031 11.707L9.66016 9.02734C11.875 9.27344 13.625 11.1328 13.625 13.4297C13.625 13.8945 13.2422 14.25 12.7773 14.25H2.19531C1.73047 14.25 1.375 13.8945 1.375 13.4297C1.375 11.1328 3.09766 9.27344 5.3125 9.02734L6.65234 11.707L7.08984 10.0938ZM10.3711 10.5586L9.16797 12.9375H12.2578C12.0938 11.8711 11.3555 10.9688 10.3711 10.5586ZM2.71484 12.9375H5.80469L4.60156 10.5586C3.61719 10.9688 2.87891 11.8711 2.71484 12.9375Z"
                                                    fill="#1C1C1E"/>
                                            </mask>
                                            <g mask="url(#mask0_6561_199807)">
                                                <rect y="0.5" width="14" height="14" fill="#6E6B7B"/>
                                            </g>
                                        </svg>
                                        EXT Salesperson
                                    </Link>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            Customer ID
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Account Manager
                        </th>
                        <th>
                            Payment Term
                        </th>
                        <th v-if="permissions.includes('view-customer-revenue')">
                            Revenue
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="customer in customers.data" :key="customer.id" class="cursor-pointer" @click="Inertia.get(`/customers/${customer.id}`)">
                        <td>
                            {{ customer.code }}
                        </td>
                        <td>
                            <span class="customer-type-icon">
                                <svg v-if="Number(customer.info_type) === 1" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_6561_3498" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="4" y="2" width="12" height="17">
                                    <path
                                        d="M6.75 5.75C6.75 5.5 6.96875 5.25 7.25 5.25H8.75C9 5.25 9.25 5.5 9.25 5.75V7.25C9.25 7.53125 9 7.75 8.75 7.75H7.25C6.96875 7.75 6.75 7.53125 6.75 7.25V5.75ZM12.75 5.25C13 5.25 13.25 5.5 13.25 5.75V7.25C13.25 7.53125 13 7.75 12.75 7.75H11.25C10.9688 7.75 10.75 7.53125 10.75 7.25V5.75C10.75 5.5 10.9688 5.25 11.25 5.25H12.75ZM6.75 9.75C6.75 9.5 6.96875 9.25 7.25 9.25H8.75C9 9.25 9.25 9.5 9.25 9.75V11.25C9.25 11.5312 9 11.75 8.75 11.75H7.25C6.96875 11.75 6.75 11.5312 6.75 11.25V9.75ZM12.75 9.25C13 9.25 13.25 9.5 13.25 9.75V11.25C13.25 11.5312 13 11.75 12.75 11.75H11.25C10.9688 11.75 10.75 11.5312 10.75 11.25V9.75C10.75 9.5 10.9688 9.25 11.25 9.25H12.75ZM4 4.5C4 3.40625 4.875 2.5 6 2.5H14C15.0938 2.5 16 3.40625 16 4.5V16.5C16 17.625 15.0938 18.5 14 18.5H6C4.875 18.5 4 17.625 4 16.5V4.5ZM5.5 4.5V16.5C5.5 16.7812 5.71875 17 6 17H8.5V15C8.5 14.1875 9.15625 13.5 10 13.5C10.8125 13.5 11.5 14.1875 11.5 15V17H14C14.25 17 14.5 16.7812 14.5 16.5V4.5C14.5 4.25 14.25 4 14 4H6C5.71875 4 5.5 4.25 5.5 4.5Z"
                                        fill="#1C1C1E"/>
                                    </mask>
                                    <g mask="url(#mask0_6561_3498)">
                                    <rect y="0.5" width="19.863" height="20" fill="#626CC6"/>
                                    </g>
                                </svg>
                                <svg v-else width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M5.79341 6.33268C5.79341 3.99935 7.61418 2.16602 9.93153 2.16602C12.2489 2.16602 14.0697 3.99935 14.0697 6.33268C14.0697 8.66602 12.2489 10.4993 9.93153 10.4993C7.61418 10.4993 5.79341 8.66602 5.79341 6.33268ZM17.3802 16.3327V17.9993C17.3802 18.4993 17.0491 18.8327 16.5525 18.8327C16.056 18.8327 15.7249 18.4993 15.7249 17.9993V16.3327C15.7249 14.916 14.649 13.8327 13.242 13.8327H6.62103C5.21407 13.8327 4.13816 14.916 4.13816 16.3327V17.9993C4.13816 18.4993 3.80711 18.8327 3.31053 18.8327C2.81396 18.8327 2.48291 18.4993 2.48291 17.9993V16.3327C2.48291 13.9993 4.30368 12.166 6.62103 12.166H13.242C15.5594 12.166 17.3802 13.9993 17.3802 16.3327ZM9.93153 8.83268C8.52457 8.83268 7.44866 7.74935 7.44866 6.33268C7.44866 4.91602 8.52457 3.83268 9.93153 3.83268C11.3385 3.83268 12.4144 4.91602 12.4144 6.33268C12.4144 7.74935 11.3385 8.83268 9.93153 8.83268Z"
                                          fill="black"/>
                                    <mask id="mask0_6561_51694" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="2" width="16" height="17">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M5.79341 6.33268C5.79341 3.99935 7.61418 2.16602 9.93153 2.16602C12.2489 2.16602 14.0697 3.99935 14.0697 6.33268C14.0697 8.66602 12.2489 10.4993 9.93153 10.4993C7.61418 10.4993 5.79341 8.66602 5.79341 6.33268ZM17.3802 16.3327V17.9993C17.3802 18.4993 17.0491 18.8327 16.5525 18.8327C16.056 18.8327 15.7249 18.4993 15.7249 17.9993V16.3327C15.7249 14.916 14.649 13.8327 13.242 13.8327H6.62103C5.21407 13.8327 4.13816 14.916 4.13816 16.3327V17.9993C4.13816 18.4993 3.80711 18.8327 3.31053 18.8327C2.81396 18.8327 2.48291 18.4993 2.48291 17.9993V16.3327C2.48291 13.9993 4.30368 12.166 6.62103 12.166H13.242C15.5594 12.166 17.3802 13.9993 17.3802 16.3327ZM9.93153 8.83268C8.52457 8.83268 7.44866 7.74935 7.44866 6.33268C7.44866 4.91602 8.52457 3.83268 9.93153 3.83268C11.3385 3.83268 12.4144 4.91602 12.4144 6.33268C12.4144 7.74935 11.3385 8.83268 9.93153 8.83268Z"
                                          fill="white"/>
                                    </mask>
                                    <g mask="url(#mask0_6561_51694)">
                                    <rect y="0.5" width="19.863" height="20" fill="#626CC6"/>
                                    </g>
                                </svg>
                            </span>
                            {{ customer.name }}
                        </td>
                        <td>
                            {{ customer.account_manager }}
                        </td>
                        <td>
                            <span v-if="customer.credit_term == null" class="text-warning">Not set</span>
                            <span v-else>{{ customer.credit_term_text }}</span>
                        </td>
                        <td v-if="permissions.includes('view-customer-revenue')">
                            <span>$ {{ customer.revenue }}</span>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-customer')">
                                        <Link @click.stop :href="`/customers/${customer.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-customer')">
                                        <Link @click.stop v-if="Number(customer.info_type) === 1" :href="`/customers/organisation/${customer.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                        <Link @click.stop v-else-if="Number(customer.info_type) === 2" :href="`/customers/individual/${customer.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                        <Link @click.stop v-else :href="`/customers/salesperson/${customer.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('delete-customer')">
                                        <a @click.stop @click="destroy(customer.id, customer.deleted_at != null)" href="javascript:void(0)">
                                            <template v-if="customer.deleted_at == null">
                                                <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span>
                                                <span class="text-red">Inactive</span>
                                            </template>
                                            <template v-else>
                                                <span class="icon"><img src="../../../assets/img/checkbox-green.svg" alt="delete"></span>
                                                <span class="text-green">Active</span>
                                            </template>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ customers.from }} to {{ customers.to }} of {{ customers.total }} entries</p>
                <Pagination :links="customers.links"/>
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
    customers: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);
let deleted = ref(props.filters.deleted);

const filter = () => {
    Inertia.get(
        "/customers",
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
            deleted: deleted.value,
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

watch([order, by, paginate, deleted], () => {
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

const destroy = (id, isRestore = false) => {
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
            if (isRestore) {
                Inertia.post(`/customers/${id}/restore`, {}, {
                    preserveScroll: true,
                });
            } else {
                Inertia.delete(`/customers/${id}`, {
                    preserveScroll: true,
                });
            }
        }
    });
};
</script>
