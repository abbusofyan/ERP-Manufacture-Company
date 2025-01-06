<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ goodsReceipt.code }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/goods-receipts">Goods Receipt List</Link>
                    </li>
                    <li class="active"><span>{{ goodsReceipt.code }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <form @submit.prevent="submitItemForm">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box">
                            <div class="text-18 font-medium flex justify-between gap-20 pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                Goods Receiving Notes Information
                                <div class="btn-group">
                                    <a class="btn btn-purple" target="_blank" :href="`/goods-receipts/${goodsReceipt.id}/pdf`"><span class="icon fa-regular fa-download"></span><span>View PDF</span></a>
                                </div>
                            </div>
                            <div class="flex mt-36 max-md:flex-col gap-[8rem]">
                                <table class="table-1-el w-full">
                                    <tbody>
                                        <tr>
                                            <th>GRN number</th>
                                            <td>{{ goodsReceipt.code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ $filters.formatDateTime(goodsReceipt.created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Supplier</th>
                                            <td>{{ goodsReceipt.order.vendor.name }}</td>
                                        </tr>
                                        <tr>
                                            <th>PO Number</th>
                                            <td>
                                                <div class="text-purple font-semibold">
                                                    <Link :href="`/orders/${goodsReceipt.order.id}`"><span>{{ goodsReceipt.order.code }}</span></Link>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Requester</th>
                                            <td>{{ goodsReceipt.requester_name }}</td>
                                            <!-- <td>{{ JSON.parse(goodsReceipt.requester_name).map(item => item.name).join(' ,') }}</td> -->
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <div class="el-tag" :class="goodsReceipt.status_class">
                                                    {{ goodsReceipt.status_text }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!canceling">
                                            <th>DO / Invoice Number</th>
                                            <td>
                                                <div class="col-md-6 col-lg-6 w-full pl-0">
                                                    <input v-model="formItems.do_number" :class="{ 'is-invalid': formItems.errors[`do_number`] }" type="text" placeholder="Type DO Number" :disabled="goodsReceipt.status != 1">
                                                    <div v-if="formItems.errors[`do_number`]" class="invalid-feedback d-block">{{ formItems.errors[`do_number`] }}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="w-full md:max-w-[20.6rem]" v-if="goodsReceipt.qr_path">
                                    <div class="image">
                                        <img :src="`/public/storage${goodsReceipt.qr_path}`" alt="qr">
                                    </div>
                                    <a class="mt-10 text-[#82868B] block text-[1.2rem]" href="#">Click to download QR</a>
                                </div>
                            </div>
                            <div>
                                <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                                <div class="mb-20">
                                    <em class="fa-regular fa-gear"></em><span class="ml-[.5rem] text-15 font-medum">Ordered Items</span>
                                    <div v-if="formItems.errors[`items`]" class="invalid-feedback d-block">{{ formItems.errors[`items`] }}</div>
                                </div>
                                <div class="has-add-box">
                                    <div class="inner">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Item ID</th>
                                                        <th>Item Name</th>
                                                        <th>Category</th>
                                                        <th>Total Ordered Qty</th>
                                                        <th>Balance Qty</th>
                                                        <th v-if="canceling" class="">
                                                            Cancel Quantity
                                                        </th>
                                                        <th v-else-if="goodsReceipt.status == 1" class="">
                                                            Receiving Quantity<span class="required">*</span>
                                                        </th>
                                                        <th v-else class="">
                                                            Receiving Quantity<span class="required">*</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, index) in formItems.items" :key="index">
                                                        <td>{{item.product_sku}}</td>
                                                        <td>{{item.product_name}}</td>
                                                        <td>{{item.category}}</td>
                                                        <td>{{item.ordered_qty}}</td>
                                                        <td>{{item.balance_qty}}</td>
                                                        <td v-if="canceling" class="form-group col-md-6 col-lg-2 w-full">
                                                            <input v-model="item.canceled_qty" :class="{ 'is-invalid': formItems.errors[`items.${index}.canceled_qty`] }" type="number">
                                                            <div v-if="formItems.errors[`items.${index}.canceled_qty`]" class="invalid-feedback d-block">{{ formItems.errors[`items.${index}.canceled_qty`] }}</div>
                                                        </td>
                                                        <td v-else-if="goodsReceipt.status == 1" class="form-group col-md-6 col-lg-2 w-full">
                                                            <input v-model="item.receiving_qty" :class="{ 'is-invalid': formItems.errors[`items.${index}.receiving_qty`] }" type="number">
                                                            <div v-if="formItems.errors[`items.${index}.receiving_qty`]" class="invalid-feedback d-block">{{ formItems.errors[`items.${index}.receiving_qty`] }}</div>
                                                        </td>
                                                        <td v-else class="form-group col-md-6 col-lg-2 w-full">
                                                            {{item.receiving_qty}}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- <div v-for="(item, index) in formItems.items" :key="index" class="box">
                                            <div class="flex items-start select2-w-100">
                                                <div class="w-full row">
                                                    <div class="form-group col-md-6 col-lg-6 w-full">
                                                        <label>Item Name<span class="required">*</span></label>
                                                        <input v-model="item.product_name" type="text" disabled>
                                                    </div>
                                                    <div class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Total Ordered Quantity<span class="required">*</span></label>
                                                        <input v-model="item.ordered_qty" type="number" disabled>
                                                    </div>
                                                    <div class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Balance Quantity<span class="required">*</span></label>
                                                        <input v-model="item.balance_qty" type="number" disabled>
                                                    </div>
                                                    <div v-if="canceling" class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Cancel Quantity<span class="required">*</span></label>
                                                        <input v-model="item.canceled_qty" :class="{ 'is-invalid': formItems.errors[`items.${index}.canceled_qty`] }" type="number">
                                                        <div v-if="formItems.errors[`items.${index}.canceled_qty`]" class="invalid-feedback d-block">{{ formItems.errors[`items.${index}.canceled_qty`] }}</div>
                                                    </div>
                                                    <div v-else-if="goodsReceipt.status == 1" class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Receiving Quantity<span class="required">*</span></label>
                                                        <input v-model="item.receiving_qty" :class="{ 'is-invalid': formItems.errors[`items.${index}.receiving_qty`] }" type="number">
                                                        <div v-if="formItems.errors[`items.${index}.receiving_qty`]" class="invalid-feedback d-block">{{ formItems.errors[`items.${index}.receiving_qty`] }}</div>
                                                    </div>
                                                    <div v-else class="form-group col-md-6 col-lg-2 w-full">
                                                        <label>Receiving Quantity<span class="required">*</span></label>
                                                        <input v-model="item.receiving_qty" :class="{ 'is-invalid': formItems.errors[`items.${index}.receiving_qty`] }" type="number" disabled>
                                                        <div v-if="formItems.errors[`items.${index}.receiving_qty`]" class="invalid-feedback d-block">{{ formItems.errors[`items.${index}.receiving_qty`] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] mb-26"></span>
                                        </div> -->
                                    </div>
                                </div>

                            </div>
                            <div class="-mx-25" v-if="!canceling">
                                <div class="flex justify-between gap-20 px-25 mt-[3.6rem]">
                                    <div class="text-18 font-medium">Documents</div>
                                    <div class="btn-group">
                                        <a v-if="goodsReceipt.status == 1" class="btn btn-purple" href="#popup-upload-document" data-fancybox>
                                            <span class="icon fa-regular fa-arrow-up-from-bracket"></span><span>Upload Document</span>
                                        </a>
                                        <div class="popup-el w-full max-w-650" id="popup-upload-document" style="display: none">
                                            <!-- <form @submit.prevent="submitForm" v-if="permissions.includes('update-goods-receipt')"> -->
                                            <div class="inner">
                                                <div class="icon w-[4rem] children:w-full children:h-auto mx-auto">
                                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <mask id="mask0_5142_593308" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="1" y="1" width="38" height="39">
                                                            <path
                                                            d="M18.6562 32.0488C17.9004 32.8887 16.6406 32.8887 15.8848 32.0488L11.1816 27.3457C10.3418 26.5898 10.3418 25.3301 11.1816 24.4902C11.9375 23.7344 13.1973 23.7344 13.9531 24.4902L17.3125 27.8496L25.291 19.7871C26.0469 19.0312 27.3066 19.0312 28.0625 19.7871C28.9023 20.627 28.9023 21.8867 28.0625 22.6426L18.6562 32.0488ZM38.3086 12.8164C38.6445 13.4883 38.8125 14.2441 38.8125 15V33.9805C38.8125 37.0039 36.377 39.3555 33.4375 39.3555H6.5625C3.53906 39.3555 1.1875 37.0039 1.1875 33.9805V15C1.1875 14.2441 1.27148 13.4883 1.60742 12.8164L5.13477 5.00586C5.97461 2.99023 7.90625 1.73047 10.0059 1.73047H29.9102C32.0098 1.73047 33.9414 2.99023 34.7812 5.00586L38.3086 12.8164ZM22.0156 12.4805H33.7734L31.1699 6.60156C30.918 6.09766 30.4141 5.76172 29.9102 5.76172H22.0156V12.4805ZM5.21875 33.9805C5.21875 34.7363 5.80664 35.3242 6.5625 35.3242H33.4375C34.1094 35.3242 34.7812 34.7363 34.7812 33.9805V16.5117H5.21875V33.9805ZM17.9844 5.76172H10.0059C9.50195 5.76172 8.99805 6.09766 8.74609 6.60156L6.14258 12.4805H17.9844V5.76172Z"
                                                            fill="#1C1C1E"/>
                                                        </mask>
                                                        <g mask="url(#mask0_5142_593308)">
                                                            <rect width="40" height="40" fill="#28C76F"/>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="head text-center mt-18">
                                                    <div class="title-md">Upload Document</div>
                                                </div>
                                                <div class="mt-[2.6rem]">
                                                    <label>Choose An Action<span class="required">*</span></label>
                                                    <div class="grid md:grid-cols-2 gap-[7.7rem] mt-6">
                                                        <div class="form-group">
                                                            <div class="custom-checkbox style-2">
                                                                <input type="radio" id="upload-file" value="1" v-model="form.type">
                                                                <label for="upload-file">Upload File</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="custom-checkbox style-2">
                                                                <input type="radio" id="camera-file" value="0" v-model="form.type">
                                                                <label for="camera-file">Take a Photo</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" v-if="form.type === '1'">
                                                    <input type="file" class="form-control-file"
                                                    @input="form.file_url = $event.target.files[0];"
                                                    accept="application/pdf">
                                                    <label class="note">Allowed file types: pdf.</label>
                                                    <!-- <div v-if="form.errors.file_url" class="invalid-feedback d-block">{{ form.errors.file_url }}</div> -->
                                                </div>
                                                <div class="form-group" v-else>
                                                    <div class="open-camera-wrapper">
                                                        <input type="text" v-model="form.image_name">
                                                        <a @click="openCamera" href="javascript:void(0)">Open Camera</a>
                                                    </div>
                                                    <div class="camera-wrapper-take-picture" :class="{ 'd-none': !form.cameraShowed }">
                                                        <div class="loading" v-if="form.isCameraLoading">Loading</div>
                                                        <div class="frame">
                                                            <video :class="form.isCameraShow ? 'd-block' : 'd-none'" autoplay class="feed"></video>
                                                            <canvas :class="!form.isCameraShow ? 'd-block' : 'd-none'"></canvas>
                                                        </div>
                                                        <div v-if="form.isCameraShow" class="d-flex justify-content-center mt-20">
                                                            <a class="snap btn btn-purple btn-purple--brounded" @click="takePicture" href="javascript:void(0)">SNAP</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div v-if="form.errors.image_name" class="invalid-feedback d-block">{{ form.errors.image_name }}</div> -->
                                                <!-- <div v-if="form.errors.image_url" class="invalid-feedback d-block">{{ form.errors.image_url }}</div> -->
                                                <div class="btn-group justify-center mt-[2.6rem]">
                                                    <button class="btn btn-purple" @click="submitDocument" type="submit">Submit</button>
                                                </div>
                                            </div>
                                            <!-- </form> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-36" v-if="!canceling">
                                <div class="table-responsive pb-0">
                                    <table class="table no-borders">
                                        <thead>
                                            <tr>
                                                <th>File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="formItems.errors[`attachment`] && !formItems.attachment">
                                                <td class="text-danger">{{ formItems.errors[`attachment`] }}</td>
                                            </tr>
                                            <tr v-if="formItems.attachment">
                                                <td>
                                                    {{formItems.attachment.file_url.name}}
                                                    <!-- <a class="text-purple" target="_blank" :href="`/storage/${item.file_url}`"><em class="fa-regular fa-arrow-down-to-bracket mr-10"></em><span>{{ item.file_name }}</span></a> -->
                                                </td>
                                                <td class="text-right text-red"><a @click="destroy(index)" href="javascript:void(0)"><em class="fa-regular fa-trash-can"></em></a></td>
                                            </tr>
                                            <template v-if="props.goodsReceipt.documents">
                                              <tr v-for="file in props.goodsReceipt.documents">
                                                  <td>
                                                      {{file.file_name}}
                                                  </td>
                                                  <td class="text-right text-primary">
                                                      <a :href="'/' + file.file_url" :download="file.file_name">
                                                          <i class="fas fa-download"></i>
                                                      </a>
                                                  </td>
                                              </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-16 justify-between mt-[5.6rem]">
                        <div>
                            <div class="btn-group" v-if="!canceling">
                                <Link class="btn btn-purple" href="/goods-receipts"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                                <button v-if="goodsReceipt.status == 1" type="submit" class="btn btn-purple"><span class="icon fa-regular fa-check"></span>Save</button>
                                <a class="btn btn-purple" v-if="goodsReceipt.status == 3" @click="convertToGoodsReturn(goodsReceipt.id)" href="javascript:void(0)"><span class="icon fa-regular fa-refresh"></span><span>Convert to Goods Return</span></a>
                                <!-- <button v-if="goodsReceipt.status == 2" type="button" @click="confirm" class="btn btn-purple"><span class="icon fa-regular fa-check"></span>Confirm</button> -->
                                <div v-if="Object.keys(formItems.errors).length > 0" class="invalid-feedback d-block">There was a validation error!</div>
                            </div>
                        </div>
                        <div class="btn-group" v-if="goodsReceipt.status == 1">
                            <!-- <button class="btn btn-orange" type="button" @click="cancelBalance"><span class="icon fa-regular fa-close"></span><span>{{!canceling ? 'Cancel Balance' : 'Back to goods receipt'}}</span></button> -->
                            <!-- <button v-if="canceling" type="submit" class="btn btn-orange"><span class="icon fa-regular fa-check"></span>Save Balance Cancelation</button> -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {computed, onBeforeMount, onMounted, onUpdated, ref} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Form from "@/Pages/StockAdjustment/Form.vue";
import {useToast} from "vue-toastification";

const permissions = computed(() => usePage().props.value.auth.user.permissions);
const toast = useToast();
const created = computed(() => usePage().props.value.flash.created);

const props = defineProps({
    goodsReceipt: Object,
});

const formItems = useForm({
    goods_receipt_id: null,
    do_number: null,
    type: 'receiving',
    items: [],
    attachment: null
})

const form = ref({
    file_url: null,
    image_url: null,
    image_name: null,
    type: '1',
    isCameraLoading: false,
    isCameraShow: false,
    cameraShowed: false,
});

const submitForm = () => {
    form.post(`/goods-receipts/${props.goodsReceipt.id}/upload-document`, {
        preserveScroll: true,
    })
};

const submitDocument = () => {
    formItems.attachment = form.value
    $.fancybox.close()
}

const submitItemForm = () => {
    formItems.post(`/goods-receipts/${props.goodsReceipt.id}/receive-item`, {
        preserveScroll: true,
    })
};

const destroy = (index) => {
    formItems.attachment = {}
};

// const destroy = (id) => {
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
//             Inertia.post(`/goods-receipts/${props.goodsReceipt.id}/delete-document/${id}`, {
//                 preserveScroll: true,
//             });
//         }
//     });
// };

const takePicture = () => {
    let ratio = window.innerHeight < window.innerWidth ? 16 / 9 : 9 / 16;
    const video = document.querySelector("video");

    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;

    const canvas = document.querySelector("canvas");
    canvas.width = videoWidth;
    canvas.height = videoHeight;

    const ctx = canvas.getContext("2d");
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = "high";
    ctx.drawImage(video, 0, 0, videoWidth, videoHeight);

    form.image_url = canvas.toDataURL("image/png");

    closeCamera();
}

const openCamera = () => {
    if ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) {
        form.isCameraLoading = true;
        form.isCameraShow = true;
        let constraints = {audio: false, video: {min: 640, ideal: 1280, max: 1920}, height: {min: 360, ideal: 720, max: 1080}};
        navigator.mediaDevices.getUserMedia(constraints).then(stream => {
            const videoPlayer = document.querySelector("video");
            videoPlayer.srcObject = stream;
            videoPlayer.play();
            form.isCameraLoading = false;
            form.cameraShowed = true;
        })
    } else {
        console.log('Cannot get Media Devices')
    }
}

const closeCamera = () => {
    const videoPlayer = document.querySelector("video");
    if (videoPlayer.srcObject) {
        const stream = videoPlayer.srcObject;
        const tracks = stream.getTracks();

        tracks.forEach((track) => {
            track.stop();
        });

        videoPlayer.srcObject = null;
        form.isCameraShow = false;
    }
};

const confirm = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Confirm goods receipt to make a purchase?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/goods-receipts/${props.goodsReceipt.id}/confirm`, {
                preserveScroll: true,
            });
        }
    });
};

const convertToGoodsReturn = (id) => {
    if (props.goodsReceipt.is_return) {
        return Swal.fire({
            title: "Cannot convert to goods return",
            text: "There is already an active goods return for this transaction.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ea5455",
            cancelButtonColor: "#009CDB",
            confirmButtonText: "Go to goods return!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                Inertia.get(`/goods-receipts/${id}/redirect-to-goods-return`, {
                    id: id
                }, {
                    preserveScroll: true,
                });
            }
        });
    }
    Swal.fire({
        title: "Are you sure?",
        text: "New goods return will be generated.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.get(`/goods-receipts/${id}/convert-to-goods-return`, {
                id: id
            }, {
                preserveScroll: true,
            });
        }
    });
};

onUpdated(() => {
    if (created.value) {
        $.fancybox.close()
        toast.success(created.value, {
            icon: "fa-solid fa-check",
            timeout: 3000,
        });
    }
})

onMounted(() => {
    console.log(props.goodsReceipt);
    formItems.goods_receipt_id = props.goodsReceipt.id
    formItems.do_number = props.goodsReceipt.do_number
    props.goodsReceipt.items.forEach((item, i) => {
        formItems.items.push({
            ...item,
            receiving_qty: item.receive_quantity,
            // balance_qty: item.ordered_qty - item.receive_quantity,
            canceled_qty: 0,
        });
    });
});

const canceling = ref(false)

const cancelBalance = () => {
    formItems.items = formItems.items.map(item => ({
        ...item,
        receiving_qty: 0,
        canceled_qty: 0
    }));
    console.log(formItems.items);

    canceling.value = !canceling.value;
    if (canceling.value) {
        formItems.type = 'canceling'
    } else {
        formItems.type = 'receiving'
    }
};
</script>
