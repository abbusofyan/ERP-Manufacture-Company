<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ title }} Quotation</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link :href="`/${shipment}/${url}`">Quotation</Link>
                    </li>
                    <li>
                        <span>List</span>
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
                    <div class="btn-group" v-if="permissions.includes('create-product')">
                        <Link class="btn btn-purple" :href="`/${shipment}/${url}/create`">Add Quotation</Link>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table select-rows">
                    <thead>
                    <tr>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>#</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Client Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center justify-between gap-1">
                                <span>Amount</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                            </div>
                        </th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="quotation in quotations.data" :key="quotation.id">
                        <td class="text-primary">
                            #{{ quotation.code }}
                        </td>
                        <td>
                            {{ quotation.customer.name }}
                        </td>
                        <td>
                            {{ $filters.formatDateTime(quotation.created_at) }}
                        </td>
                        <td>
                            {{ quotation.status_text }}
                        </td>
                        <td>
                            ${{ quotation.total }}
                        </td>
                        <td>
                            <div class="more">
                                <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                <ul class="more-panel">
                                    <li v-if="permissions.includes('view-product')">
                                        <Link :href="`/${shipment}/${url}/${quotation.id}`"><span class="icon"><img src="../../../assets/img/documents.svg" alt=""></span><span>View Detail</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-product')">
                                        <Link :href="`/${shipment}/${url}/${quotation.id}/edit`"><span class="icon"><img src="../../../assets/img/edit.svg" alt=""></span><span>Edit</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-product')">
                                        <Link class="text-success text-nowrap" :href="`/${shipment}/sales-order-${url}/create?quotation_id=${quotation.id}`"><span class="icon"><img src="../../../assets/img/copy.svg" alt=""></span><span>Convert to Sales Order</span></Link>
                                    </li>
                                    <li v-if="permissions.includes('update-product')">
                                        <a class="text-purple text-nowrap" data-bs-toggle="modal" :data-bs-target="`#uploadDocumentModal`" @click="form.current_quotation_id = quotation.id" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/upload.svg" alt=""></span><span>Upload Document</span></a>
                                    </li>
                                    <li v-if="permissions.includes('delete-product')">
                                        <a @click="destroy(quotation.id)" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span><span class="text-red">Delete</span></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal fade" :id="`uploadDocumentModal`" role="dialog" style="overflow:hidden;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-24 pt-36 pb-30">
                        <div class="modal-header">
                            <div class="col-md-12 mt-3 text-center">
                                <svg class="mb-[2.6rem]" style="width: 60px; height: 70px" width="40" height="41" viewBox="0 0 40 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M18.3335 7.79742L14.5002 11.6351C13.8335 12.3025 12.8335 12.3025 12.1668 11.6351C11.5002 10.9677 11.5002 9.96655 12.1668 9.29912L18.8335 2.62487C18.9168 2.54144 19.0002 2.49973 19.0835 2.45801C19.1668 2.4163 19.2502 2.37459 19.3335 2.29116C19.8335 2.1243 20.3335 2.1243 20.6668 2.29116C20.8335 2.29116 21.0002 2.45801 21.1668 2.62487L27.8335 9.29912C28.5002 9.96655 28.5002 10.9677 27.8335 11.6351C27.5002 11.9688 27.1668 12.1357 26.6668 12.1357C26.1668 12.1357 25.8335 11.9688 25.5002 11.6351L21.6668 7.79742V27.1527C21.6668 28.1539 21.0002 28.8213 20.0002 28.8213C19.0002 28.8213 18.3335 28.1539 18.3335 27.1527V7.79742ZM36.6668 33.827V28.8213C36.6668 27.8202 36.0002 27.1527 35.0002 27.1527C34.0002 27.1527 33.3335 27.8202 33.3335 28.8213V33.827C33.3335 34.8281 32.6668 35.4956 31.6668 35.4956H8.3335C7.3335 35.4956 6.66683 34.8281 6.66683 33.827V28.8213C6.66683 27.8202 6.00016 27.1527 5.00016 27.1527C4.00016 27.1527 3.3335 27.8202 3.3335 28.8213V33.827C3.3335 36.6636 5.50016 38.8327 8.3335 38.8327H31.6668C34.5002 38.8327 36.6668 36.6636 36.6668 33.827Z"
                                          fill="black"/>
                                    <mask id="mask0_7794_701489" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="3" y="2" width="34" height="37">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M18.3335 7.79742L14.5002 11.6351C13.8335 12.3025 12.8335 12.3025 12.1668 11.6351C11.5002 10.9677 11.5002 9.96655 12.1668 9.29912L18.8335 2.62487C18.9168 2.54144 19.0002 2.49973 19.0835 2.45801C19.1668 2.4163 19.2502 2.37459 19.3335 2.29116C19.8335 2.1243 20.3335 2.1243 20.6668 2.29116C20.8335 2.29116 21.0002 2.45801 21.1668 2.62487L27.8335 9.29912C28.5002 9.96655 28.5002 10.9677 27.8335 11.6351C27.5002 11.9688 27.1668 12.1357 26.6668 12.1357C26.1668 12.1357 25.8335 11.9688 25.5002 11.6351L21.6668 7.79742V27.1527C21.6668 28.1539 21.0002 28.8213 20.0002 28.8213C19.0002 28.8213 18.3335 28.1539 18.3335 27.1527V7.79742ZM36.6668 33.827V28.8213C36.6668 27.8202 36.0002 27.1527 35.0002 27.1527C34.0002 27.1527 33.3335 27.8202 33.3335 28.8213V33.827C33.3335 34.8281 32.6668 35.4956 31.6668 35.4956H8.3335C7.3335 35.4956 6.66683 34.8281 6.66683 33.827V28.8213C6.66683 27.8202 6.00016 27.1527 5.00016 27.1527C4.00016 27.1527 3.3335 27.8202 3.3335 28.8213V33.827C3.3335 36.6636 5.50016 38.8327 8.3335 38.8327H31.6668C34.5002 38.8327 36.6668 36.6636 36.6668 33.827Z"
                                              fill="white"/>
                                    </mask>
                                    <g mask="url(#mask0_7794_701489)">
                                        <rect y="0.5" width="40" height="40" fill="#FF9F43"/>
                                    </g>
                                </svg>
                                <div class="text-24 text-bold-500">
                                    Upload Document
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="closeModal"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <input type="file"
                                   class="form-control-file mb-10"
                                   id="upload-photo"
                                   :class="{ 'is-invalid': form.errors.photo }"
                                   @input="form.photo = $event.target.files[0];"
                                   accept="image/png, image/jpg, image/jpeg">
                            <div v-if="form.errors.photo" class="invalid-feedback d-block">{{ form.errors.photo }}</div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 mb-3 text-center">
                                <a class="btn btn-purple mr-10" @click="form.post(`/${shipment}/${url}/${form.current_quotation_id}/upload-photo`); showSuccess()" href="javascript:void(0)">Submit</a>
                                <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" @click="form.photo = null" href="javascript:void(0)">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                <p>Showing {{ quotations.from }} to {{ quotations.to }} of {{ quotations.total }} entries</p>
                <Pagination :links="quotations.links"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    shipment: Object,
    url: Object,
    title: Object,
    quotations: Object,
    filters: Object,
});

const form = useForm({
    photo: null,
    current_quotation_id: null,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);
let closeModal = ref(null)

const filter = () => {
    Inertia.get(
        `/${props.shipment}/${props.url}`,
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
            Inertia.delete(`/${props.shipment}/${props.url}/${id}`, {
                preserveScroll: true,
            });
        }
    });
};

const showSuccess = (id) => {
    closeModal.value.click();
    Swal.fire({
        title: "Success !",
        text: "Your changes has been saved",
        icon: "success",
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: "OK",
    });
};
</script>
