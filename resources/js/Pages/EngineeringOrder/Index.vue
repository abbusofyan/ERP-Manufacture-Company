<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Engineering Order</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="#">Service Order</Link>
                    </li>
                    <li>
                        <span>Engineering Order</span>
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
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>Order No</th>
                        <th>Transaction Type</th>
                        <th>Planned Start</th>
                        <th>Completion Date</th>
                        <th>Delivery Date</th>
                        <th>Confirmed By</th>
                        <th>Quotation Status</th>
                        <th>Engineering Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders.data" :key="orders.id">
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/engineering-order/${order.id}`">{{ order.production_order.code }}</Link>
                                </b>
                             </td>
                            <td>{{ order.production_order.category }}</td>
                            <td>{{ order.start_date }}</td>
                            <td>{{ order.completion_date }}</td>
                            <td></td>
                            <td>Test User</td>
                            <td>
                                <div class="el-tag" :class="order.production_order.quotation.status_class">
                                    {{ order.production_order.quotation.status_text }}
                                </div>
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
                                        <li v-if="permissions.includes('update-engineering_process_detail')">
                                            <Link :href="`/engineering-order/${order.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View</span></Link>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 pb-25 px-25">
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
    orders: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/engineering-process-detail",
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
            Inertia.delete(`/engineering-process-detail/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

</script>
