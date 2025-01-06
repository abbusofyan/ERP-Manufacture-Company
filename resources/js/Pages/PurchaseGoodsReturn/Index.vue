<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Goods Return</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <span>Goods Return List</span>
                    </li>
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
                        <div class="flex items-center justify-between gap-1"><span>Goods Return No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Goods Receipt No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>
                        <div class="flex items-center justify-between gap-1"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                    </th>
                    <th>Action</th>
                    </thead>
                    <tbody>
                        <tr v-for="goodsReturn in goodsReturn.data" :key="goodsReturn.id">
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/purchase-goods-return/${goodsReturn.id}`"><span>{{ goodsReturn.code }}</span></Link>
                                </b>
                            </td>
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/goods-receipts/${goodsReturn.goods_receipt.id}`"><span>{{ goodsReturn.goods_receipt.code }}</span></Link>
                                </b>
                            </td>
                            <td>
                                <div class="el-tag" :class="goodsReturn.status_class">
                                    {{ goodsReturn.status_text }}
                                </div>
                            </td>
                            <td>
                                <div class="more">
                                    <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                    <ul class="more-panel">
                                        <li v-if="permissions.includes('view-goods-receipt')">
                                            <Link :href="`/purchase-goods-return/${goodsReturn.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ goodsReturn.from }} to {{ goodsReturn.to }} of {{ goodsReturn.total }} entries</p>
                <Pagination :links="goodsReturn.links"/>
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
    goodsReturn: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/purchase-goods-return",
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

const setAsVoid = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Goods receipt will be voided.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/goods-receipts/${id}/set-void`, {
                id: id
            }, {
                preserveScroll: true,
            });
        }
    });
};
</script>
