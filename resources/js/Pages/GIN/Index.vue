<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Goods Issue Note</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <span>Goods Issue Note</span>
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
                    <div class="btn-group" v-if="permissions.includes('view-gin')">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>No</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Transaction Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Transaction Code</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Issue By</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Issued To</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th class="text-nowrap">
                            <div class="flex items-center justify-between gap-1"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                        </th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in gins.data" :key="item.id">
                        <td>
                            <div class="text-purple font-semibold">
                                <Link :href="`/goods-issue-notes/${item.id}`"><span>{{ item.code }}</span></Link>
                            </div>
                        </td>
                        <td>{{ item.transaction_type }}</td>
                        <td>{{ item.transaction_code }}</td>
                        <td>
                            {{ $filters.formatDateTime(item.created_at) }}
                        </td>
                        <td>
                            {{ item.created_by.name }}
                        </td>
                        <td>{{ item.release_to ? item.release_to.name : '' }}</td>
                        <td>
                            <div class="el-tag" :class="item.status_class">
                                {{ item.status_text }}
                            </div>
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-gin')">
                                        <Link :href="`/goods-issue-notes/${item.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <!-- <li v-if="permissions.includes('update-gin')">
                                        <a href="javascript:void(0)" @click="destroy(item.id)"><span class="icon"><img src="../../../assets/img/return.svg" alt=""></span><span class="text-orange">Return to Inventory</span></a>
                                    </li> -->
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ gins.from }} to {{ gins.to }} of {{ gins.total }} entries</p>
                <Pagination :links="gins.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed, onUpdated, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {useToast} from "vue-toastification";

const permissions = computed(() => usePage().props.value.auth.user.permissions);
const toast = useToast();
const created = computed(() => usePage().props.value.flash.created);

const props = defineProps({
    gins: Object,
    filters: Object,
});

const form = useForm({
    remark: null,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);

const filter = () => {
    Inertia.get(
        "/requisitions-to-goods-issue-note",
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

const closePopup = (data) => {
    form.remark = null;
    $.fancybox.close()
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
            Inertia.delete(`/goods-issue-notes/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

onUpdated(() => {
    if (created.value) {
        closePopup()
        toast.success(created.value, {
            icon: "fa-solid fa-check",
            timeout: 3000,
        });
    }
})

onMounted(() => {
    closePopup();
})
</script>
