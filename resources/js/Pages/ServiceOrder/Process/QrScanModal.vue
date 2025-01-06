<!-- QrScanModal.vue -->
<template>
    <div class="modal fade" :id="modalId" tabindex="-1" aria-labelledby="qrScanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="modal-title big-title text-center w-100" id="qrScanModalLabel">
                        Scan card QR to get started
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="d-flex justify-content-center mb-20">
                        <p>Please scan the QR code or upload an image to retrieve user information.</p>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-7">
                            <!-- QR Code Scanner -->
                            <div class="d-flex justify-content-center mb-20">
                                <qrcode-stream
                                    class="rounded-[.5rem] overflow-hidden max-w-[50rem]"
                                    @decode="onDecode"
                                    @error="onError"
                                ></qrcode-stream>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-20">
                                <!-- Upload Image Input -->
                                <p class="mb-10">Use QR code image instead</p>
                                <input
                                    type="file"
                                    ref="fileInput"
                                    accept="image/*"
                                    @change="handleFileUpload"
                                    style="display: none;"
                                />
                                <button
                                    type="button"
                                    class="btn btn-purple btn-purple--brounded"
                                    @click="triggerFileInput"
                                >
                                    Upload Image
                                </button>
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
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-50" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-purple" @click="confirmQrCode">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { QrcodeStream } from 'vue3-qrcode-reader';
import jsqr from 'jsqr';
import Swal from 'sweetalert2';

const props = defineProps({
    action: {
        type: String,
        required: true, // 'continue' or 'create-first'
    },
    serviceOrderId: {
        type: [String, Number],
        required: true,
    },
    processId: {
        type: [String, Number],
        default: null, // Required for 'continue' action
    },
    modalId: {
        type: String,
        default: 'qrScanModal',
    },
});

const emits = defineEmits(['confirm']);

const qrUserInfo = ref(null);
const userDetails = ref(null);
const fileInput = ref(null);

const form = useForm({
    user_id: null,
});

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
                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsqr(imgData.data, canvas.width, canvas.height);
                if (code) {
                    qrUserInfo.value = code.data;
                    userDetails.value = formatUserInfo(code.data);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No QR code found in the image.',
                        icon: 'error',
                        confirmButtonText: 'OK',
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
            form.user_id = userId;

            let url = '';
            if (props.action === 'continue') {
                if (!props.processId) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Process ID is required for continue action.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                    return;
                }
                url = `/service-orders/${props.serviceOrderId}/processes/${props.processId}/continue`;
            } else if (props.action === 'create-first') {
                url = `/service-orders/${props.serviceOrderId}/processes/create-first`;
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Invalid action specified.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                });
                return;
            }

            form.post(url, {
                onError: (errors) => {
                    if (errors.user_id) {
                        Swal.fire({
                            title: 'Error',
                            text: errors.user_id,
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                },
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Operation completed successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    });
                },
                onFinish: () => {
                    const modalElement = document.getElementById(props.modalId);
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    emits('confirm');
                },
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Invalid QR data. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    } else {
        Swal.fire({
            title: 'Error',
            text: 'No QR code found. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
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
        confirmButtonText: 'OK',
    });
};
</script>
