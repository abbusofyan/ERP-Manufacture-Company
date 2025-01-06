<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ title }} Sales Order</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link :href="`/${shipment}/sales-order`"><span class="icon">Sales Order</span></Link>
                    </li>
                    <li class="active"><span>{{ title }} Sales Order</span></li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
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
                        <div class="flex items-center justify-between gap-1"><span>Client COMPANY</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Client CONTACT</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>PROJECT VALUE</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>QUOTATION NUMBER</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>STATUS</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th></th>
                    </thead>
                    <tbody>
                    <tr v-for="order in orders.data" :key="order.id">
                        <td>
                            <div class="text-purple font-semibold">#{{ order.id }}</div>
                        </td>
                        <td>
                            {{ order.quotation.customer.name }}
                        </td>
                        <td>
                            ({{ order.quotation.customer.country_phone_code.phone_code }}){{ order.quotation.customer.phone }}
                        </td>
                        <td>
                        </td>
                        <td>
                            #{{ order.quotation.code }}
                        </td>
                        <td>
                            <div class="el-tag" :class="order.status_class">
                                {{ order.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('update-order') && Number(order.status) !== 3 && Number(order.status) !== 5">
                                        <a class="text-green" @click="updateStatus(order.id,3)" href="javascript:void(0)"><span class="icon fa-solid fa-check"></span><span>Confirm Item</span></a>
                                    </li>
                                    <li v-if="permissions.includes('update-order') && Number(order.status) !== 5">
                                        <a class="text-red" @click="updateStatus(order.id,5)" href="javascript:void(0)"><span class="icon fa-regular fa-rectangle-xmark"></span><span>Cancel Item</span></a>
                                    </li>
                                    <li v-if="permissions.includes('view-order')">
                                        <Link :href="`/${shipment}/sales-order-eco/${order.id}/edit`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <!--                                    <li v-if="permissions.includes('update-order')">-->
                                    <!--                                        <Link :href="`/${shipment}/sales-order-eco/${order.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>-->
                                    <!--                                    </li>-->
                                    <li v-if="permissions.includes('delete-order')">
                                        <a href="javascript:void(0)" @click="destroy(order.id)">
                                            <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span>
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
    title: Object,
    shipment: Object,
    orders: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        `/${props.shipment}/sales-order-eco`,
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

const updateStatus = (id, value) => {
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
            Inertia.post(`/${props.shipment}/sales-order-eco/update-status/${id}`, {
                status: value,
            }, {
                preserveScroll: true,
            });
        }
    });
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
            Inertia.delete(`/${props.shipment}/sales-order-eco/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
