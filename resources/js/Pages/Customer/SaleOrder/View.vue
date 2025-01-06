<template>
    <Layout>
        <Details :customer="customer"></Details>
        <div class="my-tabs mt-26">
            <Tab :customer="customer"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block"><div class="box rounded-md shadow-default bg-white">
                    <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                        <div class="perpages"><span>Show</span>
                            <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="search-el ml-auto">
                            <div class="txt">Search</div>
                            <div class="form">
                                <input type="search" placeholder="Search" v-model="search">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <th>
                                #
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Client COMPANY</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Client CONTACT</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>PROJECT VALUE</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>QUOTATION NUMBER</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>QUOTATION TYPE</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>STATUS</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th></th>
                            </thead>
                            <tbody>
                            <tr v-for="order in orders.data" :key="order.id">
                                <td>
                                    <div class="text-purple font-semibold">#{{ order.id }}</div>
                                </td>
                                <td>
                                    {{ order.refrigeration_sale.customer.name }}
                                </td>
                                <td>
                                    ({{ order.refrigeration_sale.customer.country_phone_code.phone_code }}){{ order.refrigeration_sale.customer.phone }}
                                </td>
                                <td>
                                    <span v-if="order.refrigeration_sale.total">
                                        ${{ order.refrigeration_sale.total }}
                                    </span>
                                </td>
                                <td>
                                    #{{ order.refrigeration_sale.code }}
                                </td>
                                <td>
                                    {{ order.refrigeration_sale.shipment_text }}
                                </td>
                                <td>
                                    <div class="el-tag" :class="order.status_class">
                                        {{ order.status_text }}
                                    </div>
                                </td>
                                <td>
                                    <div class="more">
                                        <div class="icon"><img src="../../../../assets/img/more.svg" alt=""></div>
                                        <ul class="more-panel">
                                            <li v-if="permissions.includes('update-order') && order.status !== 3 && order.status !== 5">
                                                <a class="text-green" @click="updateStatus(order.id,3, order.refrigeration_sale.shipment_url)" href="javascript:void(0)"><span class="icon fa-solid fa-check"></span><span>Confirm Item</span></a>
                                            </li>
                                            <li v-if="permissions.includes('update-order') && order.status !== 5">
                                                <a class="text-red" @click="updateStatus(order.id,5, order.refrigeration_sale.shipment_url)" href="javascript:void(0)"><span class="icon fa-regular fa-rectangle-xmark"></span><span>Cancel Item</span></a>
                                            </li>
                                            <li v-if="permissions.includes('view-order')">
                                                <Link :href="`/${order.refrigeration_sale.shipment_url}/sales-order-eco/${order.id}/edit`"><span class="icon"><img src="../../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                            </li>
                                            <li v-if="permissions.includes('delete-order')">
                                                <a href="javascript:void(0)" @click="destroy(order.id, order.refrigeration_sale.shipment_url)">
                                                    <span class="icon"><img src="../../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span>
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
                        <p>Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} entries</p>
                        <Pagination :links="orders.links"/>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import Details from "../../Customer/Detail.vue";
import {computed, onMounted, ref, watch} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Pagination from "@/Components/Pagination.vue";
import debounce from "lodash.debounce";
import Tab from "@/Pages/Customer/Tab.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    customer: Object,
    orders: Object,
    filters: Object,
});



let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        `/customers/${props.customer.id}/invoices`,
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

const updateStatus = (id, value, shipment) => {
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
            Inertia.post(`/${shipment}/sales-order-eco/update-status/${id}`, {
                status: value,
            }, {
                preserveScroll: true,
            });
        }
    });
};

const destroy = (id, shipment) => {
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
            Inertia.delete(`/${shipment}/sales-order-eco/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
