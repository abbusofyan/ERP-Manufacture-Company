<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Item Adjustment</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/products">Inventory</Link>
                    </li>
                    <li>
                        <span>Item Adjustment</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">
                    <div class="box d-flex justify-content-between align-items-center">
                        <div class="text-18 font-medium pb-17 mb-17">
                            <span>Item Adjustment</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-1">
                            <Link class="btn btn-light btn-light--brounded" href="/stock-adjustments">Back</Link>
                            <a class="btn btn-purple" @click="scrollToSection('section-document')" href="javascript:void(0)">Document List</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex max-md:flex-col gap-[8rem] pb-25 mb-[2.6rem] border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                <table class="table-1-el w-full">
                    <tr>
                        <th>Date Issued</th>
                        <td>{{ $filters.formatDateTime(stockAdjustment.created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Code</th>
                        <td>{{ stockAdjustment.code }}</td>
                    </tr>
                    <tr>
                        <th>Adjustment Type</th>
                        <td>{{ stockAdjustment.type.name }}</td>
                    </tr>
                    <tr>
                        <th>Reason</th>
                        <td>{{ stockAdjustment.reason }}</td>
                    </tr>
                    <tr>
                        <th>Approved By</th>
                        <td>{{ stockAdjustment.approved_by ? stockAdjustment.approved_by.name : '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="table-responsive pb-0">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>UOM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Warehouse</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Adjustment</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Adjustment Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Before Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>After Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Price</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Adjustment Date / Time</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in stockAdjustment.items" :key="item.id">
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/products/${item.product.id}`"><span>{{ item.product.sku }}</span></Link>
                                </b>
                            </td>
                            <td>
                                <b class="text-purple">
                                    <Link :href="`/products/${item.product.id}`"><span>{{ item.product.name }}</span></Link>
                                </b>
                            </td>
                            <td>{{ item.product?.uom?.code }}</td>
                            <td>{{ item.store.name }}</td>
                            <td>{{ item.adjustment }}</td>
                            <td>{{ item.adjustment_qty }}</td>
                            <td>{{ item.before_qty }}</td>
                            <td>{{ item.after_qty }}</td>
                            <td>${{ item.price }}</td>
                            <td>
                                {{ $filters.formatDateTime(stockAdjustment.created_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white mt-[2.6rem]" id="section-document">
            <div class="form-wrap">
                <div class="boxes">
                    <div class="box d-flex justify-content-between align-items-center">
                        <div class="text-18 font-medium pb-17 mb-17">
                            <span>Documents</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-1" v-if="permissions.includes('create-stock')">
                            <a class="btn btn-purple" href="#stock-document-upload" data-fancybox>Upload Document<i class="fa fa-upload"></i></a>
                            <div id="stock-document-upload" class="stock-document-upload rounded-[.5rem] max-w-[72rem]" style="display:none;">
                                <div class="text-center">
                                    <div class="icon mb-17">
                                        <svg width="34" height="37" viewBox="0 0 34 37" fill="none" xmlns="http://www.w3.org/2000/svg" style="fill: #FF9F43">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M15.3335 5.79806L11.5002 9.63576C10.8335 10.3032 9.8335 10.3032 9.16683 9.63576C8.50016 8.96833 8.50016 7.9672 9.16683 7.29977L15.8335 0.625519C15.9168 0.542091 16.0002 0.500377 16.0835 0.458663C16.1668 0.416949 16.2502 0.375235 16.3335 0.291806C16.8335 0.12495 17.3335 0.12495 17.6668 0.291806C17.8335 0.291806 18.0002 0.458663 18.1668 0.625519L24.8335 7.29977C25.5002 7.9672 25.5002 8.96833 24.8335 9.63576C24.5002 9.96947 24.1668 10.1363 23.6668 10.1363C23.1668 10.1363 22.8335 9.96947 22.5002 9.63576L18.6668 5.79806V25.1534C18.6668 26.1545 18.0002 26.822 17.0002 26.822C16.0002 26.822 15.3335 26.1545 15.3335 25.1534V5.79806ZM33.6668 31.8276V26.822C33.6668 25.8208 33.0002 25.1534 32.0002 25.1534C31.0002 25.1534 30.3335 25.8208 30.3335 26.822V31.8276C30.3335 32.8288 29.6668 33.4962 28.6668 33.4962H5.3335C4.3335 33.4962 3.66683 32.8288 3.66683 31.8276V26.822C3.66683 25.8208 3.00016 25.1534 2.00016 25.1534C1.00016 25.1534 0.333496 25.8208 0.333496 26.822V31.8276C0.333496 34.6642 2.50016 36.8333 5.3335 36.8333H28.6668C31.5002 36.8333 33.6668 34.6642 33.6668 31.8276Z"
                                                  fill="#FF9F43"/>
                                        </svg>
                                    </div>
                                    <div class="text-24 mb-[2.6rem]">
                                        Upload Documents
                                    </div>
                                </div>
                                <form @submit.prevent="form.post(`/stock-adjustments/${stockAdjustment.id}/documents`)">
                                    <div class="form-group">
                                        <label>Upload Documents</label>
                                        <input type="file" class="form-control-file"
                                               :class="{ 'is-invalid': form.errors.file_url }"
                                               @input="form.file_url = $event.target.files[0];"
                                               accept="application/pdf">
                                        <label class="note">Allowed file types: pdf.</label>
                                        <div v-if="form.errors.file_url" class="invalid-feedback d-block">{{ form.errors.file_url }}</div>
                                    </div>
                                    <div class="btn-group justify-content-center">
                                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Submit</button>
                                        <a ref="cancelBtnRef" class="btn btn-purple btn-purple--brounded" href="javascript:void(0)" data-fancybox-close>Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive pb-0">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center gap-1" :class="{ sorting_asc: order_documents == 'name' && by_documents == 'asc', sorting_desc: order_documents == 'name' && by_documents == 'desc' }" @click="sort_documents('name')">
                                <span>File</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="document in stockAdjustmentDocuments" :key="document.id">
                            <td>
                                <a class="text-primary d-flex gap-1" target="_blank" :href="`/storage/${document.file_url}`">
                                    <i class="fa fa-download"></i>{{ document.name }}
                                </a>
                            </td>
                            <td class="text-right">
                                <a class="btn btn-danger--brounded" href="javascript:void(0)" @click="destroyDocument(document.id)" v-if="permissions.includes('delete-stock')">
                                    <i class="fa fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed, onMounted, onUpdated} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import {Inertia} from "@inertiajs/inertia";
import debounce from "lodash.debounce";
import {useToast} from "vue-toastification";
import Swal from "sweetalert2";

const cancelBtnRef = ref(null);

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    stockAdjustment: Object,
    stockAdjustmentDocuments: Object,
    // filters_items: Object,
    // stock_adj_items: Object,
    // filters_documents: Object,
    // stock_adj_documents: Object,
});

const form = useForm({
    file_url: null,
});

const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({behavior: 'smooth'});
    }
};

/** ITEMS PAGINATION START **/
// let search_items = ref(props.filters_items.search_items);
// let order_items = ref(props.filters_items.order_items);
// let by_items = ref(props.filters_items.by_items);
// let paginate_items = ref(props.filters_items.paginate_items);

// const filter_items = () => {
//     Inertia.get(
//         `/stock-adjustments/${props.stockAdjustment.id}`,
//         {
//             search_items: search_items.value,
//             order_items: order_items.value,
//             by_items: by_items.value,
//             paginate_items: paginate_items.value,
//         },
//         {
//             preserveState: true,
//             preserveScroll: true,
//             replace: true,
//         }
//     );
// };
//
// const sort_items = (data) => {
//     order_item.value = data;
//
//     if (by_items.value == "asc") {
//         by_items.value = "desc";
//     } else {
//         by_items.value = "asc";
//     }
// };

// watch(
//     search_items,
//     debounce(() => {
//         filter_items();
//     }, 500)
// );
//
// watch([order_items, by_items, paginate_items], () => {
//     filter_items();
// });
/** ITEMS PAGINATION END **/


/** DOCUMENTS PAGINATION START **/
// let search_documents = ref(props.filters_documents.search_documents);
// let order_documents = ref(props.filters_documents.order_documents);
// let by_documents = ref(props.filters_documents.by_documents);
// let paginate_documents = ref(props.filters_documents.paginate_documents);

// const filter_documents = () => {
//     Inertia.get(
//         `/stock-adjustments/${props.stockAdjustment.id}`,
//         {
//             search_documents: search_documents.value,
//             order_documents: order_documents.value,
//             by_documents: by_documents.value,
//             paginate_documents: paginate_documents.value,
//         },
//         {
//             preserveState: true,
//             preserveScroll: true,
//             replace: true,
//         }
//     );
// };
//
// const sort_documents = (data) => {
//     order_documents.value = data;
//
//     if (by_documents.value == "asc") {
//         by_documents.value = "desc";
//     } else {
//         by_documents.value = "asc";
//     }
// };

// watch(
//     search_documents,
//     debounce(() => {
//         filter_documents();
//     }, 500)
// );
//
// watch([order_documents, by_documents, paginate_documents], () => {
//     filter_documents();
// });
/** DOCUMENTS PAGINATION END **/

// const toast = useToast();
// const created = computed(() => usePage().props.value.flash.created);
// onUpdated(() => {
//     if (created.value) {
//         cancelBtnRef.value.click();
//         form.file_url = null;
//         toast.success(created.value, {
//             icon: "fa-solid fa-check",
//             timeout: 3000,
//         });
//     }
// })

// const destroyDocument = (id) => {
//     Swal.fire({
//         title: "Are you sure?",
//         text: "You won't be able to revert this",
//         icon: "warning",
//         showCancelButton: true,
//         confirmButtonColor: "#ea5455",
//         cancelButtonColor: "#009CDB",
//         confirmButtonText: "Yes, Proceed!",
//         cancelButtonText: "Cancel",
//     }).then((result) => {
//         if (result.isConfirmed) {
//             Inertia.delete(`/stock-adjustments/${props.stockAdjustment.id}/documents/${id}`, {
//                 preserveScroll: true,
//             });
//         }
//     });
// };
</script>
