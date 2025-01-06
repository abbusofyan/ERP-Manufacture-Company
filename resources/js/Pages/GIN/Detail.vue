<template>
    <Layout>

        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">
                {{ gin.code }}
            </div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/goods-issue-notes">Good Issue Note</Link>
                    </li>
                    <li class="active"><span>{{ gin.code }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form action="">
                    <div class="boxes">
                        <div class="box">
                            <div class="text-18 font-medium flex justify-between gap-20 pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                Good Issue Note Information
                            </div>
                            <div class="flex mt-36 max-md:flex-col gap-[8rem]">
                                <table class="table-1-el w-full">
                                    <tr>
                                        <th>No.</th>
                                        <td>{{ gin.code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Transaction Type</th>
                                        <td>{{ gin.transaction_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Transaction No</th>
                                        <td>{{ gin.transaction_code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <div class="el-tag" :class="gin.status_class">
                                                {{ gin.status_text }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="-mx-25">
                                <div class="table-responsive mt-36">
                                    <table class="table select-rows">
                                        <thead>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>UoM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Requested QTY</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Issuing QTY</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Returned QTY</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Action</span></div>
                                        </th>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in form.items">
                                            <td>{{ item.detail.requisition_item.product.sku }}</td>
                                            <td>{{ item.detail.requisition_item.product.name }}</td>
                                            <td>{{ item.detail.requisition_item.product.uom.code }}</td>
                                            <td>{{ item.order_qty }}</td>
                                            <td>
                                                <div class="m-0 p-0" v-if="gin.status == 1">
                                                    <input type="text" class="form-sm" v-model="item.issuing_qty">
                                                    <div v-if="form.errors[`items.${index}.issuing_qty`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.issuing_qty`] }}</div>
                                                </div>
                                                <span v-else>{{ item.issuing_qty }}</span>
                                            </td>
                                            <td>
                                                {{ item.returned_qty }}
                                            </td>
                                            <td>
                                                <div v-if="gin.status != 1" class="more">
                                                    <div class="icon"><img src="../../../assets/img/more.svg" alt=""></div>
                                                    <ul class="more-panel">
                                                        <li v-if="permissions.includes('update-gin')">
                                                            <a href="javascript:void(0)" @click="openModal(item.id)">
                                                                <span class="icon"><img src="../../../assets/img/return.svg" alt=""></span><span class="text-orange">Return to Inventory</span>
                                                            </a>
                                                            <!-- <a class="text-orange" data-bs-toggle="modal" data-bs-target="#returnModal" ref="openModal" href="javascript:void(0)"><span class="icon"><img src="../../../assets/img/return.svg" alt=""></span> Return to Inventory</a> -->
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <div class="-mx-25">
                                <div class="pt-20 px-25">
                                    <div class="text-18 font-medium flex justify-between gap-20 pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                        Goods Return
                                    </div>
                                </div>
                                <div class="table-responsive mt-36">
                                    <table class="table select-rows">
                                        <thead>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item ID</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Item Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>UoM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Returned QTY</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Reason</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between"><span>Datetime</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                        </th>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in gin.returns">
                                            <td>{{ item.gin_item?.requisition_item?.product.sku }}</td>
                                            <td>{{ item.gin_item?.requisition_item?.product.name }}</td>
                                            <td>{{ item.gin_item?.requisition_item?.product.uom.code }}</td>
                                            <td>{{ item.return_qty }}</td>
                                            <td>{{ item.reason }}</td>
                                            <td>{{ $filters.formatDateTime(item.created_at) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group flex flex-wrap gap-16 justify-between mt-[3.6rem]">
                        <div class="">
                            <Link class="btn btn-purple" href="/goods-issue-notes"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                            <button v-if="gin.status == 1" type="button" class="btn btn-purple ml-10" @click="confirm"><span class="icon fa-regular fa-check"></span><span>Confirm</span></button>
                        </div>
                        <!-- <a v-if="gin.returns.length === 0" @click="returnGIN" class="btn btn-orange btn-orange--brounded" href="javascript:void(0)"><em class="icon fa-regular fa-arrow-rotate-left"></em><span>Return To Inventory</span></a> -->
                    </div>
                </form>
            </div>
        </div>
    </Layout>

    <div class="modal fade" id="confirmModal" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <form @submit.prevent="confirmGIN">
                    <div class="modal-header">
                        <div class="col-md-12 mt-3 text-center">
                            <div class="text-24 text-bold-500">
                                Confirm Goods Issue Note
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>User</label>
                            <Select2 class="select2-w-100"
                                     v-model="form.issued_to"
                                     :options="props.users"
                                     placeholder="Select User"
                            />
                            <div v-if="form.errors.issued_to" class="invalid-feedback d-block">{{ form.errors.issued_to }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 mb-3 text-center">
                            <button class="btn btn-purple mr-10" type="submit" :disabled="form.processing">Submit</button>
                            <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="returnModal" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <form @submit.prevent="returnInventory">
                    <div class="modal-header">
                        <div class="col-md-12 mt-3 text-center">
                            <div class="text-24 text-bold-500">
                                Return item to inventory
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Returning Quantity <span class="required">*</span></label>
                            <input type="number" class="form-control" v-model="form.return_qty" :class="{ 'is-invalid': form.errors.return_qty }" placeholder="Input quantity">
                            <div v-if="form.errors.return_qty" class="invalid-feedback d-block">{{ form.errors.return_qty }}</div>
                        </div>
                        <div class="form-group">
                            <label>Remark <span class="required">*</span></label>
                            <textarea class="form-control" type="text" :class="{ 'is-invalid': form.errors.remark }" v-model="form.remark" placeholder="Type Remark"></textarea>
                            <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 mb-3 text-center">
                            <button class="btn btn-purple mr-10" type="submit" :disabled="form.processing">Submit</button>
                            <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {computed, onMounted, onUpdated, ref} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import {useToast} from "vue-toastification";

const permissions = computed(() => usePage().props.value.auth.user.permissions);
const toast = useToast();
const created = computed(() => usePage().props.value.flash.created);

const props = defineProps({
    gin: Object,
    users: Array
});

const form = useForm({
    gin_id: null,
    gin_item_id: null,
    items: [],
    issued_to: null,
    return_qty: null,
    remark: null,
});

const closePopup = (data) => {
    form.remark = null;
    $.fancybox.close()
};

// onUpdated(() => {
//     if (created.value) {
//         closePopup()
//         toast.success(created.value, {
//             icon: "fa-solid fa-check",
//             timeout: 3000,
//         });
//     }
// })

const returnGIN = () => {
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
            Inertia.get(`/goods-issue-notes/${props.gin.id}/return`, {}, {
                preserveScroll: true,
            });
        }
    });
};

const confirm = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Item will be issued",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            openConfirmModal()
            // Inertia.get(`/goods-issue-notes/${props.gin.id}/return`, {}, {
            //     preserveScroll: true,
            // });
        }
    });
};

const gin_id = ref(null);

function openModal(id) {
    form.gin_item_id = id;
    const modal = new bootstrap.Modal(document.getElementById('returnModal'));
    modal.show();
}

function openConfirmModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

function returnInventory() {
    form.post(`/goods-issue-notes/${props.gin.id}/return`, {
        preserveScroll: true,
        onSuccess: () => {
            // Reset the form upon successful submission
            form.reset();

            // Optionally, display a success toast if you're staying on the same page
            toast.success('Item returned successfully!', {
                icon: "fa-solid fa-check",
                timeout: 5000,
            });
        },
        onError: (errors) => {
            if (errors.return_qty) {
                Swal.fire({
                    title: "Error",
                    text: errors.return_qty,
                    icon: "error",
                    confirmButtonText: "OK",
                });
            } else if (errors.remark) {
                Swal.fire({
                    title: "Error",
                    text: errors.remark,
                    icon: "error",
                    confirmButtonText: "OK",
                });
            } else {
                Swal.fire({
                    title: "Error",
                    text: "An unexpected error occurred. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            }
        },
        onFinish: () => {
            if (props.gin) {
                form.gin_id = props.gin.id
                props.gin.items.forEach((item, i) => {
                    form.items.push({
                        id: item.id,
                        order_qty: props.gin.status == 2 ? item.order_qty : item.requisition_item.pending_issue_qty,
                        issuing_qty: item.issued_qty ? item.issued_qty : item.requisition_item.pending_issue_qty,
                        returned_qty: item.returned_qty,
                        detail: item
                    })
                });
            }
        },
    });

    // Close the modal immediately if you want to navigate away
    const modal = bootstrap.Modal.getInstance(document.getElementById('returnModal'));
    modal.hide();
}

function confirmGIN() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    modal.hide();
    form.post(`/goods-issue-notes/${props.gin.id}/confirm`, {
        onSuccess: () => {
            form.reset();
        }
    });
}


onMounted(() => {
    if (props.gin) {
        form.gin_id = props.gin.id
        props.gin.items.forEach((item, i) => {
            form.items.push({
                id: item.id,
                order_qty: props.gin.status == 2 ? item.order_qty : item.requisition_item.pending_issue_qty,
                issuing_qty: item.issued_qty ? item.issued_qty : item.requisition_item.pending_issue_qty,
                returned_qty: item.returned_qty,
                detail: item
            })
        });
    }
});
</script>
