<template>
    <div class="modal fade" id="uploadAttachment" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <!-- SVG Icon -->
                        <div class="text-24 text-bold-500">
                            Upload or Capture Photo
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <!-- Option Selection -->
                    <div class="form-group text-center">
                        <button
                            :class="['btn mr-2', option === 'upload' ? 'btn-purple' : 'btn-secondary']"
                            @click="setOption('upload')"
                        >
                            Upload File
                        </button>
                        <button
                            :class="['btn', option === 'capture' ? 'btn-purple' : 'btn-secondary']"
                            @click="setOption('capture')"
                        >
                            Take Photo
                        </button>
                    </div>

                    <!-- Upload File Option -->
                    <div v-if="option === 'upload'" class="form-group">
                        <label>Attachments</label>
                        <div class="fileContainer">
                            <label class="fileInputLabel" for="fileInput1">
                                <input
                                    class="fileInput"
                                    id="fileInput1"
                                    type="file"
                                    :class="{ 'is-invalid': form.errors.file_url }"
                                    @change="onFileChange"
                                    accept="image/png, image/jpg, image/jpeg"
                                />
                                <span v-if="form.file_url">{{ form.file_url.name }}</span>
                                <span v-else>Choose File</span>
                                <span class="browse">Browse</span>
                            </label>
                            <div class="mt-12 text-[#82868B]">Allowed file types: png, jpg, jpeg.</div>
                        </div>
                    </div>

                    <!-- Capture Photo Option -->
                    <div v-else-if="option === 'capture'" class="form-group text-center">
                        <div v-if="!capturedImage">
                            <!-- Camera device selection -->
                            <div class="mb-14">
                                <label>Select Camera:</label>
                                <select v-model="selectedDeviceId" class="form-control" @change="changeCamera">
                                    <option v-for="device in cameraDevices" :key="device.deviceId" :value="device.deviceId">
                                        {{ device.label || 'Camera ' + (cameraDevices.indexOf(device) + 1) }}
                                    </option>
                                </select>
                            </div>
                            <video
                                ref="video"
                                autoplay
                                playsinline
                                width="100%"
                                style="border: 1px solid #ccc;"
                            ></video>
                            <button class="btn btn-purple--brounded mt-10" @click="capturePhoto">Capture Photo</button>
                        </div>
                        <div v-else>
                            <img
                                :src="capturedImage"
                                alt="Captured Photo"
                                style="max-width: 100%; border: 1px solid #ccc;"
                            />
                            <button class="btn btn-purple--brounded mt-10" @click="retakePhoto">Retake Photo</button>
                        </div>
                    </div>

                    <!-- Remarks Field -->
                    <div class="form-group" v-if="serviceOrder.stage === 1">
                        <label>Remark</label>
                        <textarea
                            class="form-control"
                            v-model="form.remarks"
                            rows="3"
                            placeholder="Remark"
                        ></textarea>
                        <div v-if="form.errors.remarks" class="invalid-feedback d-block">
                            {{ form.errors.remarks }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 mb-3 text-center">
                        <a class="btn btn-purple mr-10" @click="submitForm" href="javascript:void(0)">Submit</a>
                        <a
                            class="btn btn-purple btn-purple--brounded"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                            ref="closeModal"
                            href="javascript:void(0)"
                        >Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Code comment by English
// This script adds a camera device selection before starting the stream.
// It enumerates available cameras, sets the selected device, and restarts the camera stream using the chosen device.

import {useForm} from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2';
import {ref, watch, onBeforeUnmount, onMounted} from 'vue';

const props = defineProps({
    serviceOrder: Object,
});

const form = useForm({
    file_url: null,
    remarks: null,
});

const closeModal = ref(null);
const option = ref('upload');
const video = ref(null);
const canvas = document.createElement('canvas');
const stream = ref(null);
const capturedImage = ref(null);
const cameraDevices = ref([]);
const selectedDeviceId = ref(null);

// Stop all camera streams
const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
        stream.value = null;
    }
};

// Start camera with selected device
const startCamera = async () => {
    stopCamera();
    if (!selectedDeviceId.value && cameraDevices.value.length > 0) {
        selectedDeviceId.value = cameraDevices.value[0].deviceId;
    }

    try {
        stream.value = await navigator.mediaDevices.getUserMedia({video: {deviceId: selectedDeviceId.value ? {exact: selectedDeviceId.value} : undefined}});
        video.value.srcObject = stream.value;
    } catch (error) {
        console.error('Error accessing camera:', error);
        await Swal.fire({
            title: 'Error',
            text: 'Cannot access camera. Please allow camera access.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
        // option.value = 'upload';
    }
};

// Enumerate cameras
const getCameras = async () => {
    const devices = await navigator.mediaDevices.enumerateDevices();
    cameraDevices.value = devices.filter(device => device.kind === 'videoinput');
};

// Change camera device and restart
const changeCamera = () => {
    if (option.value === 'capture') {
        startCamera();
    }
};

const setOption = (selectedOption) => {
    option.value = selectedOption;
};

const onFileChange = (event) => {
    form.file_url = event.target.files[0];
};

const capturePhoto = () => {
    canvas.width = video.value.videoWidth;
    canvas.height = video.value.videoHeight;
    canvas.getContext('2d').drawImage(video.value, 0, 0);
    capturedImage.value = canvas.toDataURL('image/png');
    form.file_url = dataURLtoFile(capturedImage.value, 'captured_photo.png');
    stopCamera();
};

const retakePhoto = () => {
    capturedImage.value = null;
    form.file_url = null;
    startCamera();
};

const dataURLtoFile = (dataurl, filename) => {
    let arr = dataurl.split(',');
    let mime = arr[0].match(/:(.*?);/)[1];
    let bstr = atob(arr[1]);
    let n = bstr.length;
    let u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, {type: mime});
};

watch(option, (newOption) => {
    if (newOption === 'capture') {
        startCamera();
    } else {
        stopCamera();
        capturedImage.value = null;
        form.file_url = null;
    }
});

onBeforeUnmount(() => {
    stopCamera();
});

onMounted(() => {
    getCameras();
});

const submitForm = () => {
    if (!form.file_url) {
        Swal.fire({
            title: 'Error',
            text: 'Please provide a photo or attachment before submitting.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
        return;
    }

    form.post(`/service-orders/${props.serviceOrder.id}/attachments`, {
        onSuccess: () => {
            closeModal.value.click();
            form.reset();
            capturedImage.value = null;
            option.value = 'upload';
            Swal.fire({
                title: 'Success!',
                text: 'Your submission has been saved!',
                icon: 'success',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                cancelButtonColor: '#626CC6',
            });
        },
    });
};
</script>

<style scoped>
.btn {
    padding: 0.5rem 1rem;
}
</style>
