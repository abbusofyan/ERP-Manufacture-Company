<template>
    <Layout>

        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Inventory</div>
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
                        <Link href="/products">List</Link>
                    </li>
                    <li class="active"><span>{{ product.name }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form action="">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="flex justify-between gap-20">
                                <div class="text-18 font-medium pb-17  border-0 border-b-[1px] border-solid border-[#EBE9F1]">{{ product.name }}</div>
                                <button class="btn btn-purple" type="button" @click="openAuditLogModal()">Audit Log</button>
                            </div>
                            <div class="row mt-36">
                                <div class="col-6">
                                    <table class="table-2-el w-full">
                                        <tbody>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <div :class="Number(product.status) === 1 ? 'green' : 'orange'" class="el-tag">
                                                        {{ product.status_text }}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Type</th>
                                                <td>{{product.type}}</td>
                                            </tr>
                                            <tr>
                                                <th>Category</th>
                                                <td>{{ product.category.name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Item ID</th>
                                                <td>{{ product.sku }}</td>
                                            </tr>
                                            <tr>
                                                <th>Item Name</th>
                                                <td>{{ product.name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Description</th>
                                                <td>{{ product.description }}</td>
                                            </tr>
                                            <tr>
                                                <th>UOM</th>
                                                <td>{{ product.uom.code }}</td>
                                            </tr>
                                            <tr>
                                                <th>Weight (kg)</th>
                                                <td>{{ product.weight ?? 0 }} Kg</td>
                                            </tr>
                                            <tr>
                                                <th>Dimension <small>(LxBxH)</small> </th>
                                                <td>{{ (product.dimension_l ?? 0) + ' x ' + (product.dimension_w ?? 0) + ' x ' + (product.dimension_h ?? 0) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Selling Price</th>
                                                <td>$ {{ product.selling_price ?? 0 }} ({{$filters.formatDate(product.effective_date_selling_price)}})</td>
                                            </tr>
                                            <tr>
                                                <th>QR CODE</th>
                                                <td>
                                                    <div v-if="showQR" class="image">
                                                        <img class="float-right" src="../../../assets/img/qr.png" alt="qr">
                                                    </div>
                                                    <br>
                                                    <b>
                                                        <a v-if="!showQR" class="text-purple" href="#" @click.prevent="toggleQR">Show QR Code</a>
                                                        <a v-else class="text-purple" href="#" @click.prevent="toggleQR">Hide QR Code</a>
                                                    </b>
                                                    <br>
                                                    <b>
                                                        <a class="text-purple" href="../../../assets/img/qr.png" download>Click to download QR</a>
                                                    </b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-20">
                                        <div class="col-6" v-if="usePage().props.value.auth.user.role.toLowerCase() != 'purchase'">
                                            <div class="image-container float-end">
                                                <img v-if="product.sales_photos.length > 0" class="image" :src="'/' + product.sales_photos[0].path" alt="qr">
                                                <img v-else class="image" :src="'/images/no-image.png'">
                                            </div>
                                            <p class="text-center text-24 font-medium text-purple mb-10">Picture (sales)</p>
                                            <div class="form-group mt-30 float-end" v-if="user && user.roles && user.roles.some(role => role.name === 'Sales')">
                                                <form @submit.prevent="form.post(`/products/${product.id}/upload-sales-photo`)">
                                                    <div class="fileContainer">
                                                        <label class="fileInputLabel" for="fileInput1">
                                                            <input class="fileInput" id="fileInput1"
                                                            type="file"
                                                            :class="{ 'is-invalid': form.errors.photos }"
                                                            @input="form.photos.push($event.target.files[0])"
                                                            accept="image/png, image/jpg, image/jpeg">
                                                            <span v-if="form.file_url">{{ form.file_url['name'] }}</span>
                                                            <span v-else>Choose File</span>
                                                            <span class="browse">Browse</span>
                                                        </label>
                                                        <div class="mt-12 text-[#82868B]">Allowed file types: png, jpg, jpeg.</div>
                                                        <button class="btn btn-purple col-12 btn-block btn-purple" type="submit">Submit</button>
                                                        <div class="files">
                                                            <div v-for="(photo, index) in form.photos" :key="index" class="fileColumn">
                                                                <p class="name">
                                                                    {{ photo.name }}
                                                                    <i class="icon-remove fa-regular fa-xmark cursor-pointer" @click="delPhoto(index)"></i>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="image-container float-end"> <!-- Added float-end -->
                                                <img v-if="product.photos.length > 0" class="image" :src="'/' + product.photos[0].path" alt="qr">
                                                <img v-else class="image" :src="'/images/no-image.png'">
                                            </div>
                                            <p class="text-center text-24 font-medium text-purple mb-10">Picture (Non-sales)</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group ml-10 mr-10">
                                            <div class="custom-checkbox style-1">
                                                <input type="checkbox" id="type-single-item" :checked="!product.assembly && !product.finish_goods" disabled>
                                                <label for="type-single-item">
                                                    Single Item
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mr-10">
                                            <div class="custom-checkbox style-1">
                                                <input type="checkbox" id="type-assembly" :checked="product.assembly" disabled>
                                                <label for="type-assembly">
                                                    Assembly Item
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mr-10">
                                            <div class="custom-checkbox style-1">
                                                <input type="checkbox" id="type-fg" :checked="product.finish_goods" disabled>
                                                <label for="type-fg">
                                                    Finish Goods
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-2-el w-full">
                                        <tbody>
                                            <tr>
                                                <th class="pb-0">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Case Pack</th>
                                                <td>{{ product.case_pack }}</td>
                                            </tr>
                                            <tr>
                                                <th>Lead Time</th>
                                                <td>{{ product.lead_time ?? '0' }} {{product.lead_time > 1 ? 'days' : 'day'}}</td>
                                            </tr>
                                            <tr>
                                                <th>Auto Re-order</th>
                                                <td>
                                                    <div :class="product.auto_reorder === 1 ? 'green' : 'orange'" class="el-tag">
                                                        {{ product.auto_reorder_text }} - Lv: {{ product.reorder_level }} - Qty: {{ product.quantity_to_reorder }}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Re-order Level</th>
                                                <td>{{ product.reorder_level }}</td>
                                            </tr>
                                            <tr>
                                                <th>Qty to Reorder</th>
                                                <td>{{ product.quantity_to_reorder }}</td>
                                            </tr>
                                            <tr>
                                                <th>Expenses Acc</th>
                                                <td>{{ product.expense_acc }}</td>
                                            </tr>
                                            <tr>
                                                <th>Remark</th>
                                                <td>{{ product.remark }}</td>
                                            </tr>
                                            <tr>
                                                <th>Lowest Cost</th>
                                                <td>$ {{ lowestCost ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th>Highest Cost</th>
                                                <td>$ {{ highestCost ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Cost</th>
                                                <td>$ {{ lastCost ?? 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="text-center text-24 font-medium text-purple mb-10 mt-30">Documents</p>
                                            <span v-for="document in props.documents" :key="document.id">
                                                <a class="btn btn-purple w-100 mb-10 d-flex justify-content-between align-items-center" :href="'/' + document.file_url" target="_blank">
                                                    <span>{{ document.name }}</span>
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span> -->

                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="row">
                                <div class="col">
                                    <a :href="`/inventory-warehouse?search=${product.sku}&order=id&by=desc&paginate=10&store_id=`">
                                        <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                            <div class="text-24 font-medium text-purple">
                                                {{product.stock ? product.stock - product.committed_qty : 0}}
                                            </div>
                                            <div class="font-medium">Stock</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="#" @click="openCommitListModal()">
                                        <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                            <div class="text-24 font-medium text-purple">
                                                {{product.committed_qty}}
                                            </div>
                                            <div class="font-medium">Committed</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="#" @click="openOrderListModal()">
                                        <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                            <div class="text-24 font-medium text-purple">
                                                {{product.ordered_qty}}
                                            </div>
                                            <div class="font-medium">Ordered</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="#" @click="openRequestListModal()">
                                        <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                            <div class="text-24 font-medium text-purple">
                                                {{product.requested_qty}}
                                            </div>
                                            <div class="font-medium">Requested</div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <template v-if="product.assembly || product.finish_goods">
                                <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                                <div class="text-18 font-medium mb-17">Bill of Materials</div>
                                <div class="table-container">
                                    <div class="table-responsive pb-0">
                                        <table class="table no-borders">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span>Category</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                                        </div>
                                                    </th>
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
                                                            <span>Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="product.assembly" v-for="(item, assembly_item_index) in product.assembly.materials" :key="assembly_item_index">
                                                    <td>{{item.product.category.prefix + ' | ' + item.product.category.name}}</td>
                                                    <td>{{item.product.sku}}</td>
                                                    <td>
                                                        <b class="text-purple">
                                                            <Link :href="`/products/${item.product.id}`"><span>{{ item.product.name }}</span></Link>
                                                        </b>
                                                    </td>
                                                    <td>{{item.product.uom.code}}</td>
                                                    <td>{{item.qty}}</td>
                                                </tr>

                                                <tr v-else v-for="(item, finish_goods_item_index) in product.finish_goods.items" :key="finish_goods_item_index">
                                                    <td>{{item.product.category.prefix + ' | ' + item.product.category.name}}</td>
                                                    <td>{{item.product.sku}}</td>
                                                    <td>
                                                        <b class="text-purple">
                                                            <Link :href="`/products/${item.product.id}`"><span>{{ item.product.name }}</span></Link>
                                                        </b>
                                                    </td>
                                                    <td>{{item.product.uom.code}}</td>
                                                    <td>{{item.qty}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>

                            <template v-if="prices && !product.assembly && !product.finish_goods">
                                <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                                <div class="text-18 font-medium mb-17">Product Price</div>
                                <div class="table-responsive pb-0">
                                    <table class="table no-borders">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="flex items-center justify-between"><span>Vendor</span></div>
                                                </th>
                                                <th>
                                                    <div class="flex items-center justify-between"><span>Price</span></div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(price, index) in prices" :key="index">
                                            <td>{{ price.vendor ? price.vendor.name : null }}</td>
                                            <td>$ {{ price.price ?? 0 }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">Location</div>
                            <div class="table-responsive pb-0">
                                <table class="table no-borders">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Location</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(store_product, index) in storeProduct" :key="index">
                                        <td>{{ store_product.store.name + ' | ' + store_product.location.name}}</td>
                                        <td>{{ store_product.stock ?? 0 }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">Requisition</div>
                            <div class="table-responsive pb-0">
                                <table class="table no-borders">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Requisition Code</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Transaction Code</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Requested Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Ordered Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Committed Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Issued Qty</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in product.requisitionItems" :key="index">
                                            <td>
                                                <b class="text-purple">
                                                    <Link :href="`/requisitions/${item.requisition.id}`"><span>{{ item.requisition.code }}</span></Link>
                                                </b>
                                            </td>
                                            <td>{{ item.requisition.requisitionable ? item.requisition.requisitionable.code : '-' }}</td>
                                            <td>{{item.requested_qty}}</td>
                                            <td>{{item.ordered_qty}}</td>
                                            <td>{{item.committed_qty}}</td>
                                            <td>{{item.issued_qty}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group">
                            <Link class="btn btn-purple" href="/products"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                            <Link class="btn btn-light btn-light--brounded" :href="`/products/${product.id}/edit`">Edit</Link>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-red btn-orange--brounded" href="javascript:void(0)" @click="changeStatus(product.id, 'inactive')"><img src="../../../assets/img/switch-1.png"><span>Move to Inactive</span></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <OrderListModal :data="product.listOrder" :status_text_arr="order_status_text_arr" :status_class_arr="order_status_class_arr"></OrderListModal>

        <RequestListModal :data="product.listRequest"
            :type_arr="requisition_type_arr"
            :status_text_arr="requisition_status_text_arr"
            :status_class_arr="requisition_status_class_arr">
        </RequestListModal>

        <CommitListModal :data="product.listCommit"
            :type_arr="requisition_type_arr"
            :status_text_arr="requisition_status_text_arr"
            :status_class_arr="requisition_status_class_arr">
        </CommitListModal>

        <AuditLogModal :data="product.histories"></AuditLogModal>

    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {onMounted, ref} from "vue";
import {useForm, usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import OrderListModal from "@/Pages/Product/OrderListModal.vue";
import RequestListModal from "@/Pages/Product/RequestListModal.vue";
import AuditLogModal from "@/Pages/Product/AuditLogModal.vue";

const props = defineProps({
    product: Object,
    prices: Object,
    storeProduct: Object,
    lowestCost: Number,
    highestCost: Number,
    lastCost: Number,
    documents: Array,
    order_status_text_arr: Array,
    order_status_class_arr: Array,
    requisition_status_text_arr: Array,
    requisition_status_class_arr: Array,
    requisition_type_arr: Array
});

const destroy = () => {
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
            Inertia.post(`/products/${props.product.id}/switch-status`, {
                preserveScroll: true,
            });
        }
    });
};

const form = useForm({
    photos:[],
});

const showQR = ref(false)

const toggleQR = () => {
    showQR.value = !showQR.value
}

const changeStatus = (id, newStatus) => {
    Swal.fire({
        title: "Are you sure?",
        text: "The item will be " + newStatus,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/products/${id}/switch-status`, {
                preserveScroll: true,
            });
        }
    });
};

function openOrderListModal() {
    const modal = new bootstrap.Modal(document.getElementById('OrderListModal'));
    modal.show();
}

function openRequestListModal() {
    const modal = new bootstrap.Modal(document.getElementById('RequestListModal'));
    modal.show();
}

function openCommitListModal() {
    const modal = new bootstrap.Modal(document.getElementById('CommitListModal'));
    modal.show();
}

function openAuditLogModal() {
    const modal = new bootstrap.Modal(document.getElementById('AuditLogModal'));
    modal.show();
}

let closeModal = ref(null)

</script>


<style scoped>
.content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start; /* Align the content at the top */
  flex-wrap: wrap; /* Wrap on smaller screens */
}

.details {
  flex: 1;
}

.image-container {
  flex: 1;
  text-align: center; /* Align the image to the right */
}

.image {
  max-width: 100%; /* Makes the image responsive */
  height: auto; /* Maintains the aspect ratio */
  max-height: 300px; /* Set max height to avoid overflow */
  display: inline-block; /* Ensure it behaves well within the container */
}

.blue-line {
  width: 100%;
  border-bottom: 2px solid blue; /* The blue line */
  margin-top: 20px; /* Space between the image and the line */
}

.table-container {
    max-height: 400px; /* Adjust as needed */
    overflow-y: auto;
}

.table-responsive {
    display: inline-block;
    width: 100%; /* Ensures responsive table width */
    overflow-x: auto; /* Horizontal scrolling */
}

.table-2-el tr:not(:last-child) th, .table-2-el tr:not(:last-child) td {
    padding-bottom: 2rem;
}

.table-2-el tr th {
    width: 20%;
    color: #626CC6;
    font-weight: 500;
}

.table-2-el tr td {
    width: 50%;
}

.float-right-custom {
    float: right;
}
</style>
