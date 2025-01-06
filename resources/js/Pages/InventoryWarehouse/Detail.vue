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
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">{{ product.name }}</div>
                            <div class="flex justify-between gap-20">
                                <div class="img w-full max-w-[24.5rem] children:w-full">
                                </div>
                                <div class="btn-group"><a class="btn btn-purple" href="#"><span class="icon"><img src="../../../assets/img/documents.svg" alt="icon"></span><span>View PDF</span></a></div>
                            </div>
                            <div class="row mt-36">
                                <div class="col-6">
                                    <table class="table-1-el w-full">
                                        <tbody>
                                            <tr>
                                                <th>Type</th>
                                                <td class="pb-0">
                                                    <div class="row">
                                                        <div class="form-group ml-10 mr-10">
                                                            <div class="custom-checkbox style-1">
                                                                <input type="checkbox" id="type-single-item" :checked="product.type == 1" disabled>
                                                                <label for="type-single-item">
                                                                    Single Item
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mr-10">
                                                            <div class="custom-checkbox style-1">
                                                                <input type="checkbox" id="type-assembly" :checked="product.type == 2" disabled>
                                                                <label for="type-assembly">
                                                                    Assembly Item
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mr-10">
                                                            <div class="custom-checkbox style-1">
                                                                <input type="checkbox" id="type-fg" :checked="product.type == 3" disabled>
                                                                <label for="type-fg">
                                                                    Finish Goods
                                                                </label>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <div :class="Number(product.status) === 1 ? 'green' : 'orange'" class="el-tag">
                                                        {{ product.status_text }}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Created date</th>
                                                <td>{{ $filters.formatDateTime(product.created_at) }}</td>
                                            </tr>
                                            <tr>
                                                <th>SKU</th>
                                                <td>{{ product.sku }}</td>
                                            </tr>
                                            <tr>
                                                <th>Category</th>
                                                <td>{{ product.category.name }}</td>
                                            </tr>
                                            <tr>
                                                <th>UOM</th>
                                                <td>{{ product.uom.code }}</td>
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
                                                <th>Lead Time (days)</th>
                                                <td>{{ product.lead_time ?? '-' }}</td>
                                            </tr>
                                            <tr v-if="product.remark">
                                                <th>Remark</th>
                                                <td>{{ product.remark }}</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>
                                                    <div class="image"><img class="float-right" src="../../../assets/img/qr.png" alt="qr"></div>
                                                    <a class="mt-10 text-[#82868B] block text-[1.2rem]" href="#">Click to download QR</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-3">
                                    <p class="text-center text-24 font-medium text-purple">Picture (sales)</p>
                                    <div class="image-container">
                                        <!-- <img v-if="product.sales_photos" class="image float-right" :src="'/storage/' + product.sales_photos[0].path" alt="qr"> -->
                                    </div>
                                    <div class="form-group mt-30">
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
                                <div class="col-3">
                                    <p class="text-center text-24 font-medium text-purple">Picture (Non-sales)</p>
                                    <div class="image-container">
                                        <img v-if="product.photos.length > 0" class="image float-right" :src="'/storage/' + product.photos[0].path" alt="qr">
                                    </div>
                                </div>
                            </div>
                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="row">
                                <div class="col">
                                    <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                        <div class="text-24 font-medium text-purple">
                                            {{product.stock ? product.stock - product.committed_qty : 0}}
                                        </div>
                                        <div class="font-medium">Stock</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                        <div class="text-24 font-medium text-purple">
                                            {{product.committed_qty}}
                                        </div>
                                        <div class="font-medium">Committed</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                        <div class="text-24 font-medium text-purple">
                                            {{product.ordered_qty}}
                                        </div>
                                        <div class="font-medium">Ordered</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="item text-right py-15 px-20 rounded-md border border-[#EBE9F1] border-solid">
                                        <div class="text-24 font-medium text-purple">
                                            {{product.requested_qty}}
                                        </div>
                                        <div class="font-medium">Requested</div>
                                    </div>
                                </div>
                            </div>

                            <template v-if="prices">
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
                            <!-- <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                            <div class="text-18 font-medium mb-17">Log List</div>
                            <div class="table-responsive pb-0">
                                <table class="table no-borders">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="flex items-center justify-between"><span>No.</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Type</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Quantity</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Instock</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Store</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Location</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Created By</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                            <th>
                                                <div class="flex items-center justify-between"><span>Date</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span></div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div> -->
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div class="btn-group">
                            <Link class="btn btn-purple" href="/products"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                            <Link class="btn btn-light btn-light--brounded" :href="`/products/${product.id}/edit`">Edit</Link>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-red btn-red--brounded" href="javascript:void(0)" @click="destroy"><em class="fa-regular fa-xmark"></em><span>Remove</span></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {onMounted} from "vue";
import {useForm, usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    product: Object,
    prices: Object,
    storeProduct: Object,
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
            Inertia.delete(`/products/${props.product.id}`, {
                preserveScroll: true,
            });
        }
    });
};

const form = useForm({
    photos:[],
});

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
</style>
