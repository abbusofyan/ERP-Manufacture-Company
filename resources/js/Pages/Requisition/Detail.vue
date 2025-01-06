<template>
    <Layout>

        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                {{ requisition.code }}
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/requisitions">Requisition List</Link>
                    </li>
                    <li class="active"><span>{{ requisition.code }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form action="">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Purchase Requisition Information</div>
                            <div class="flex justify-between gap-20">
                                <div class="img w-full max-w-[24.5rem] children:w-full">
                                </div>
                            </div>
                            <div class="flex max-md:flex-col gap-[8rem]">
                                <table class="table-1-el w-full">
                                    <tr>
                                        <th>PR Number</th>
                                        <td>{{ requisition.code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Stock Purchase</th>
                                        <td>{{ requisition.type_text }}</td>
                                    </tr>
                                    <tr>
                                        <th>Urgent Order</th>
                                        <td>{{ requisition.is_urgent ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date Created</th>
                                        <td>{{ $filters.formatDateTime(requisition.created_at) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ requisition.department }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date Required</th>
                                        <td>{{ $filters.formatDate(requisition.required_date) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Requested by</th>
                                        <td>{{ requisition.created_by?.name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved by</th>
                                        <td>{{ requisition.approved_by?.name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <div class="el-tag" :class="requisition.status_class">
                                                {{ requisition.status_text }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="requisition.rejection_notes.length > 0">
                                        <th>Rejection Notes</th>
                                        <td>
                                            {{ requisition.rejection_notes[0].note }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">Item List</div>
                            <div class="table-responsive">
                                <table class="table no-borders">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Qty On Stock</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Requested qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Pending for PO</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Incoming Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Committed Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Issued Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Action</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="(item, index) in requisition.requisition_items" :key="index">
                                            <tr @click="setDetail(index)" :style="selectedRow == index ? 'background-color:#b0b8f4; font-weight: bold;' : ''">
                                                <td>
                                                    <div class="text-purple font-semibold">
                                                        <Link :href="`/products/${item.product_id}`"><span>{{ item.product ? item.product.sku : 'OTHER' }}</span></Link>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-purple font-semibold">
                                                        <Link :href="`/products/${item.product_id}`"><span>{{ item.product_name }}</span></Link>
                                                    </div>
                                                </td>
                                                <td>{{ item.product ? (item.product.stock ? item.product.stock : 0) : 0 }}</td>
                                                <td>{{ item.requested_qty }}</td>
                                                <td>{{ item.pending_order_qty }}</td>
                                                <td>{{ item.ordered_qty }}</td>
                                                <td>{{ item.committed_qty }}</td>
                                                <td>{{ item.issued_qty }}</td>
                                                <td>
                                                    <a v-if="requisition.status == 1" href="javascript:void(0)" @click="cancelItemHandler(item)">
                                                        <span class="icon fa fa-x text-danger"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">Item Detail</div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <input type="text" :value="formDetail.category" disabled>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Item ID</label>
                                                <input type="text" :value="formDetail.item_id" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Item Name</label>
                                        <input type="text" :value="formDetail.item_name" disabled>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Warehouse ID</label>
                                                <input type="text" v-model="form.name" placeholder="Role Name">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Required Date</label>
                                                <input type="text" :value="formDetail.required_date" disabled>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Request Qty</label>
                                                <input type="text" :value="formDetail.requested_qty" disabled>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Qty on Stock</label>
                                                <input type="text" v-model="form.name" placeholder="Role Name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Budget Amount</label>
                                        <input type="text" v-model="form.name" placeholder="Role Name">
                                    </div>
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" v-model="form.name" placeholder="Role Name">
                                    </div>
                                    <img
                                        class="image float-right"
                                        :src="formDetail.image ? '/' + formDetail.image : '/images/no-image.png'"
                                        alt="qr"
                                        style="max-height: 200px; width: auto;"
                                        >
                                    <!-- <img class="image float-right" :src="'/images/no-image.png'"> -->
                                </div>
                            </div>

                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">PO List</div>
                            <div class="table-responsive">
                                <table class="table no-borders">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="flex items-center justify-between"><span>PO NO</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Supplier</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Created Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Delivery Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Status</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(order, orderIndex) in requisition.orders" :key="orderIndex">
                                        <td>
                                            <b class="text-purple">
                                                <Link :href="`/orders/${order.id}`"><span>{{ order.code }}</span></Link>
                                            </b>
                                        </td>
                                        <td>{{ order.vendor.name }}</td>
                                        <td>{{ $filters.formatDateTime(order.created_at) }}</td>
                                        <td>{{ $filters.formatDate(order.delivery_date) }}</td>
                                        <td>
                                            <div class="el-tag" :class="order.status_class">
                                                {{ order.status_text }}
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group">
                            <Link class="btn btn-purple" href="/requisitions"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                            <Link v-if="['Draft', 'Rejected'].includes(requisition.statuses[requisition.status])" class="btn btn-light btn-light--brounded" :href="`/requisitions/${requisition.id}/edit`">
                                <span class="icon fa-regular fa-pencil"></span>
                                <span>Edit</span>
                            </Link>
                        </div>
                        <div class="btn-group">
                            <a v-if="['pending for approval'].includes(requisition.statuses[requisition.status].toLowerCase()) && permissions.includes('approve-requisition')" class="btn btn-green" @click="approve(requisition.id)" href="javascript:void(0)"><em class="icon fa-sharp fa-regular fa-check"></em><span>Approve</span></a>
                            <a v-if="['pending for approval'].includes(requisition.statuses[requisition.status].toLowerCase()) && permissions.includes('approve-requisition')" class="btn btn-danger" @click="openRejectModal(requisition.id)" href="javascript:void(0)"><em class="icon fa-sharp fa-regular fa-x"></em><span>Reject</span></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import { computed, ref } from "vue";
import { usePage, Link, useForm } from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import { Inertia } from "@inertiajs/inertia";
import { useToast } from "vue-toastification";

const toast = useToast();

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    requisition: Object,
});

const created = computed(() => usePage().props.value.flash.created);

const selectedItem = ref({
    process: '',
    url: '',
    uom: '',
    product_name: '',
    requested_qty: 0,
    item_left: 0,
});

const submitForm = () => {
    form.post(selectedItem.value.url, {
        onSuccess: (response) => {
            closeModal.value.click();
            toast.success(created.value, {
                icon: "fa-solid fa-check",
                timeout: 3000,
            });
        },
        onError: (errors) => {
            console.error('Error committing item:', errors)
        }
    })
};

const form = useForm({
    qty: null,
});

const closeModal = ref(null);

const approve = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Requisition will be approved.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/requisitions/${id}/approve`, {
                preserveScroll: true,
            });
        }
    });
};

const rejectForm = useForm({
    reason: null,
});

function openRejectModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

const rejectionNotes = ref({});

function reject() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
    modal.hide();
    rejectForm.post(`/requisitions/${props.requisition.id}/reject`, {
        onSuccess: () => {
            rejectForm.reset();
        }
    });
}

// Cancel Item Functionality
function cancelItemHandler(item) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/requisitions/${props.requisition.id}/cancel-item/${item.id}`, {}, {
                onSuccess: () => {
                    toast.success('Item has been cancelled successfully.', {
                        icon: "fa-solid fa-check",
                        timeout: 3000,
                    });
                },
                onError: (errors) => {
                    console.error('Error cancelling item:', errors);
                    toast.error('Failed to cancel item. Please try again.', {
                        icon: "fa-solid fa-x",
                        timeout: 3000,
                    });
                }
            });
        }
    });
}

const selectedRow = ref({})
const formDetail = ref({})

const setDetail = (index) => {
    if (index == selectedRow.value) {
        selectedRow.value = null
        formDetail.value = {}
    } else {
        selectedRow.value = index

        var item = props.requisition.requisition_items[index]
        formDetail.value = {
            category: item.product.category.name,
            item_id: item.product.sku,
            item_name: item.product_name,
            required_date: item.required_date,
            requested_qty: item.requested_qty,
            image: item.product.photos && item.product.photos.length > 0 ? item.product.photos[0].path : null
        }
    }
}
</script>
