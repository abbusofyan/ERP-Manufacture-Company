<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Storage</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <span>Storage</span>
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
                    <div class="btn-group">
                        <!-- <Link class="btn btn-purple" href="/storages/create">Add New Storage</Link> -->
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Reference No.</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>No. Of Products</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Last Updated</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="storage in storages.data" :key="storage.id">
                        <td>
                            <div class="text-purple font-semibold">
                                <Link :href="`/storages/${storage.id}`"><span>{{ storage.code }}</span></Link>
                            </div>
                        </td>
                        <td>
                            {{ storage.product_count }}
                        </td>
                        <td>
                            <span :class="storage.status_class">
                                {{ storage.status_text }}
                            </span>
                        </td>
                        <td>
                            {{ $filters.formatDateTime(storage.updated_at) }}
                        </td>
                        <td>
                            <div class="el-actions">
                                <Link class="text-purple" :href="`/storages/${storage.id}`" v-if="permissions.includes('update-location') && Number(storage.status) === '1'"><em class="fa-regular fa-eye"></em></Link>
                                <Link class="text-orange" :href="`/storages/${storage.id}/edit`" v-if="permissions.includes('update-location') && Number(storage.status) === '0'"><em class="fa-regular fa-pen-to-square"></em></Link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ storages.from }} to {{ storages.to }} of {{ storages.total }} entries</p>
                <Pagination :links="storages.links"/>
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
    storages: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/storages",
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
