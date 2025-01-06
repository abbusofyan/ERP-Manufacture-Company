<template>
    <div>
        <div class="btn-group align-items-start">
            <template v-if="!projectOrder.stage">
                <a class="btn btn-purple btn-lg flex-fill" href="javascript:void(0)" @click="openQrModal">Start</a>
                <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
            </template>
            <template v-else>
                <template v-if="projectOrder.current_process">
                    <template v-if="Number(projectOrder.stage) !== 5">
                        <template v-if="Number(projectOrder.current_process.status) === 1 || Number(projectOrder.current_process.status) === 4">
                            <Link class="btn btn-orange btn-lg flex-fill" method="POST" :href="`/project-orders/${projectOrder.id}/processes/${projectOrder.current_process.id}/pause`">Start</Link>
                            <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
                        </template>
                        <template v-else-if="Number(projectOrder.current_process.status) === 2">
                            <Link class="btn btn-orange btn-lg flex-fill" method="POST" :href="`/project-orders/${projectOrder.id}/processes/${projectOrder.current_process.id}/pause`">Pause</Link>
                            <a v-if="Number(projectOrder.stage) !== 1" class="btn btn-green btn-lg flex-fill" @click="submitComplete" href="javascript:void(0)">Complete</a>
                            <a v-else class="btn btn-green btn-lg flex-fill" data-bs-toggle="modal" data-bs-target="#submitDeliveryDate" ref="openModal" href="javascript:void(0)">Complete</a>
                        </template>
                        <template v-else-if="Number(projectOrder.stage) !== 5">
                            <Link class="btn btn-purple btn-lg flex-fill" method="POST" :href="`/project-orders/${projectOrder.id}/processes/${projectOrder.current_process.id}/continue`">Continue</Link>
                            <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
                        </template>
                    </template>
                </template>
                <template v-else>
                    <template v-if="Number(projectOrder.stage) === 4">
                        <Link class="btn btn-purple btn-lg flex-fill" method="POST" :href="`/project-orders/${projectOrder.id}/processes/vehicle-collected`">Vehicle Collected</Link>
                    </template>
                    <template v-else-if="Number(projectOrder.stage) !== 5">
                        <a class="btn btn-purple btn-lg flex-fill" href="javascript:void(0)" @click="openQrModal">Start</a>
                        <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
                    </template>
                </template>
            </template>
        </div>

        <!-- QR Code Scan Modal -->
        <div class="modal fade" id="qrScanModal" tabindex="-1" aria-labelledby="qrScanModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title big-title text-center w-100" id="qrScanModalLabel">Scan card QR to get started</div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-center mb-20">
                            <p>Please scan the QR code or upload an image to retrieve user information.</p>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-7">
                                <!-- QR Code Scanner -->
                                <div class="d-flex justify-content-center mb-20">
                                    <qrcode-stream class="rounded-[.5rem] overflow-hidden max-w-[50rem]" @decode="onDecode" @error="onError"></qrcode-stream>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-20">
                                    <!-- Upload Image Input -->
                                    <p class="mb-10">Use QR code image instead</p>
                                    <input type="file" ref="fileInput" accept="image/*" @change="handleFileUpload" style="display: none;">
                                    <button type="button" class="btn btn-purple btn-purple--brounded" @click="triggerFileInput">Upload Image</button>
                                    <!-- Display Scanned User Info -->
                                    <div class="mt-10" v-if="qrUserInfo">
                                        <div v-if="userDetails">
                                            <p class="text-purple">Please confirm this information</p>
                                            <div v-html="userDetails"></div>
                                        </div>
                                        <div v-else>Invalid QR data. Please try again.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-50" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-purple" @click="confirmQrCode">Confirm</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <DeliveryDateForm :projectOrder="projectOrder"></DeliveryDateForm>
    </div>
</template>
<script setup>
import {usePage, Link, useForm} from '@inertiajs/inertia-vue3';
import {ref} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import Swal from 'sweetalert2';
import DeliveryDateForm from './../Delivery/DeliveryDateForm.vue';
import {QrcodeStream} from 'vue3-qrcode-reader';
import jsqr from 'jsqr';

const props = defineProps({
    projectOrder: Object,
});

const qrUserInfo = ref(null);
const userDetails = ref(null);
const fileInput = ref(null);

const openQrModal = () => {
    new bootstrap.Modal(document.getElementById('qrScanModal')).show();
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            const imageData = reader.result;
            const image = new Image();
            image.src = imageData;
            image.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = image.width;
                canvas.height = image.height;
                ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsqr(imageData.data, canvas.width, canvas.height);
                if (code) {
                    qrUserInfo.value = code.data; // Update user info
                    userDetails.value = formatUserInfo(code.data);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No QR code found in the image.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            };
        };
        reader.readAsDataURL(file);
    }
};

const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const confirmQrCode = () => {
    if (qrUserInfo.value) {
        const userId = extractUserIdFromQr(qrUserInfo.value);
        if (userId) {
            Inertia.post(`/project-orders/${props.projectOrder.id}/processes/create-first`, {
                user_id: userId
            });
        } else {
            console.error("Failed to extract user ID from QR data.");
        }
    } else {
        Swal.fire({
            title: 'Error',
            text: 'No QR code found in the image.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    const modalElement = document.getElementById('qrScanModal');
    if (modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }
};

const extractUserIdFromQr = (qrData) => {
    try {
        const userInfo = JSON.parse(qrData);
        if (userInfo && userInfo.id) {
            return userInfo.id;
        }
    } catch (e) {
        console.error('Invalid QR data', e);
    }
    return null;
};

const formatUserInfo = (qrData) => {
    try {
        const userInfo = JSON.parse(qrData);
        if (userInfo && userInfo.id && userInfo.name) {
            return `<strong class="text-purple">Name</strong>: ${userInfo.name}<br><strong class="text-purple">Email</strong>: ${userInfo.email}`;
        }
    } catch (e) {
        console.error('Error formatting QR data', e);
    }
    return null;
};

const onDecode = (decodedString) => {
    qrUserInfo.value = decodedString;
    userDetails.value = formatUserInfo(decodedString);
};

const onError = (error) => {
    console.error('QR Code Scan Error:', error);
    Swal.fire({
        title: 'Error',
        text: 'Could not scan the QR code. Please try again.',
        icon: 'error',
        confirmButtonText: 'OK'
    });
};

const submitComplete = () => {
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
            Inertia.post(`/project-orders/${props.projectOrder.id}/processes/${props.projectOrder.current_process.id}/complete`,
                {}, {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Success !',
                            text: "Your changes have been saved",
                            icon: 'success',
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: 'OK',
                            cancelButtonColor: '#626CC6',
                        });
                    }
                });
        }
    });
};
</script>
