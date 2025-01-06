<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Item Transfer</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/products"><span>Inventory</span></Link>
                    </li>
                    <li>
                        <span>Item Transfer</span>
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
                    <div class="btn-group" v-if="permissions.includes('create-transfer')">
                        <Link class="btn btn-purple" href="/transfers/create">New Transfer</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Store Origin</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Store Destination</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Remark</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Staff</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Last Updated</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="transfer in transfers.data" :key="transfer.id">
                        <td>
                            <Link class="text-primary text-bold" :href="`/transfers/${transfer.id}`">
                                {{ transfer.code }}
                            </Link>
                        </td>
                        <td>
                            {{ transfer.origin_store.name }}
                        </td>
                        <td>
                            {{ transfer.destination_store.name }}
                        </td>
                        <td>{{ transfer.remarks }}</td>
                        <td>
                            {{ transfer.author.name }}
                        </td>
                        <td>
                            {{ $filters.formatDateTime(transfer.created_at) }}
                        </td>
                        <td>
                            <div class="el-tag" :class="transfer.status_class">
                                {{ transfer.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li>
                                        <Link :href="`/transfer/copy/${transfer.id}?origin_store_id=${transfer.origin_store_id}&destination_store_id=${transfer.destination_store_id}&order=id&by=desc&paginate=10`"><span class="icon fa fa-copy"></span><span>Copy as New</span></Link>
                                    </li>
                                    <li v-if="transfer.status == 4">
                                        <a href="javascript:void(0)" @click="cancel(transfer.id)">
                                            <span class="icon fa fa-cancel text-danger"></span><span class="text-danger">Cancel</span>
                                        </a>
                                    </li>
                                    <li v-if="transfer.status == 4">
                                        <a href="javascript:void(0)" @click="sendForApproval(transfer.id)">
                                            <span class="icon fa fa-check text-success"></span><span class="text-success">Confirm</span>
                                        </a>
                                    </li>
                                    <li v-if="transfer.status == 3 && permissions.includes('approve-transfer')">
                                        <a href="javascript:void(0)" @click="approve(transfer.id)">
                                            <span class="icon fa fa-check text-success"></span><span class="text-success">Approve</span>
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
                <p>Showing {{ transfers.from }} to {{ transfers.to }} of {{ transfers.total }} entries</p>
                <Pagination :links="transfers.links"/>
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
import debounce from "lodash.debounce";
import Swal from "sweetalert2";

const page = usePage();

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    transfers: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/transfers",
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

const cancel = (transfer_id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Item transfer will be cancelled. You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/transfers/cancel/${transfer_id}`, {
                preserveScroll: true,
            });
        }
    });
};

const sendForApproval = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This transaction will be sent to the manager for approval",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/transfer/send-for-approval/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

const approve = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This transaction will be marked as completed. You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/transfer/approve/${id}`, {
                preserveScroll: true,
            });
        }
    });
};
</script>
