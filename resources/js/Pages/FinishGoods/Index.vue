<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Assembly to FG</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <span>Assembly to FG</span>
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
                    <div class="btn-group more-button" v-if="permissions.includes('create-vendor')">
                        <Link class="btn btn-purple" href="/finish-goods/create">Add New List</Link>
                        <!-- <Link class="btn btn-purple" href="/finish-goods/replace-item">Replace Item</Link> -->
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>Conversion Number</th>
                        <th>
                            Finish Goods ID
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Category
                        </th>
                        <th>
                            UOM
                        </th>
                        <th>
                            Created Date
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="transaction in fgTransaction.data" :key="transaction.id">
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/finish-goods/${transaction.id}`"><span>{{ transaction.code }}</span></Link>
                                </b>
                            </td>
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/products/${transaction.finish_goods.product.id}`"><span>{{ transaction.finish_goods.product.sku }}</span></Link>
                                </b>
                            </td>
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/products/${transaction.finish_goods.product.id}`"><span>{{ transaction.finish_goods.product.name }}</span></Link>
                                </b>
                            </td>
                            <td>
                                {{ transaction.finish_goods.category.name }}
                            </td>
                            <td>
                                {{ transaction.finish_goods.assembly.product.uom.code }}
                            </td>
                            <td>
                                {{ $filters.formatDateTime(transaction.created_at) }}
                            </td>
                            <td>
                                <div class="more">
                                    <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                    <ul class="more-panel">
                                        <li v-if="permissions.includes('view-assembly')">
                                            <Link :href="`/finish-goods/${transaction.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 pb-25 px-25">
                <p>Showing {{ fgTransaction.from }} to {{ fgTransaction.to }} of {{ fgTransaction.total }} entries</p>
                <Pagination :links="fgTransaction.links"/>
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
    fgTransaction: Object,
    filters: Object,
});
console.log(props.fgTransaction);
let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/finish-goods",
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

const toggleStatus = (id, status) => {
    Swal.fire({
        title: "Are you sure to set this as " + status + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/finish-goods/toggle-status/${id}/${status}`, {
                preserveScroll: true,
            });
        }
    });
};

const formatDate = (timestamp) => {
    const date = new Date(timestamp);
    const options = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    };
    return date.toLocaleString('en-GB', options);
}
</script>
