<!-- ButtonGroup.vue -->
<template>
    <div>
        <div class="btn-group align-items-start">
            <template v-if="!serviceOrder.stage">
                <a class="btn btn-purple btn-lg flex-fill" href="javascript:void(0)" @click="openQrModal('create-first')">Start</a>
                <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
            </template>
            <template v-else>
                <template v-if="serviceOrder.current_process">
                    <template v-if="Number(serviceOrder.stage) !== 5">
                        <template v-if="Number(serviceOrder.current_process.status) === 1 || Number(serviceOrder.current_process.status) === 4">
                            <Link class="btn btn-orange btn-lg flex-fill" method="POST" :href="`/service-orders/${serviceOrder.id}/processes/${serviceOrder.current_process.id}/pause`">Start</Link>
                            <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
                        </template>
                        <template v-else-if="Number(serviceOrder.current_process.status) === 2">
                            <Link class="btn btn-orange btn-lg flex-fill" method="POST" :href="`/service-orders/${serviceOrder.id}/processes/${serviceOrder.current_process.id}/pause`">Pause</Link>
                            <a v-if="Number(serviceOrder.stage) !== 1 && serviceOrder.delivery_date != null" class="btn btn-green btn-lg flex-fill" @click="submitComplete" href="javascript:void(0)">Complete</a>
                            <a v-else class="btn btn-green btn-lg flex-fill" data-bs-toggle="modal" data-bs-target="#submitDeliveryDate" href="javascript:void(0)">Complete</a>
                        </template>
                        <template v-else-if="Number(serviceOrder.stage) !== 5">
                            <a class="btn btn-purple btn-lg flex-fill" href="javascript:void(0)" @click="openQrModal('continue', serviceOrder.current_process.id)">Continue</a>
                            <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
                        </template>
                    </template>
                </template>
                <template v-else>
                    <template v-if="Number(serviceOrder.stage) === 4">
                        <Link class="btn btn-purple btn-lg flex-fill" method="POST" :href="`/service-orders/${serviceOrder.id}/processes/vehicle-collected`">Vehicle Collected</Link>
                    </template>
                    <template v-else-if="Number(serviceOrder.stage) !== 5">
                        <a class="btn btn-purple btn-lg flex-fill" href="javascript:void(0)" @click="openQrModal('create-first')">Start</a>
                        <a class="btn btn-light-50 btn-lg flex-fill" href="javascript:void(0)">Complete</a>
                    </template>
                </template>
            </template>
        </div>

        <!-- QR Code Scan Modal -->
        <QrScanModal
            :action="qrAction"
            :serviceOrderId="serviceOrder.id"
            :processId="qrProcessId"
            :modalId="'qrScanModalButtonGroup'"
            @confirm="onQrConfirm"
        />

        <!-- Delivery Date Modal -->
        <DeliveryDateForm :serviceOrder="serviceOrder"></DeliveryDateForm>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Inertia } from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2';
import DeliveryDateForm from './../Delivery/DeliveryDateForm.vue';
import QrScanModal from './QrScanModal.vue';

const props = defineProps({
    serviceOrder: Object,
});

const qrAction = ref('');
const qrProcessId = ref(null);

const openQrModal = (action, processId = null) => {
    qrAction.value = action;
    qrProcessId.value = processId;
    new bootstrap.Modal(document.getElementById('qrScanModalButtonGroup')).show();
};

const onQrConfirm = () => {
    // Handle any additional logic after QR confirmation
};

const submitComplete = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea5455',
        cancelButtonColor: '#009CDB',
        confirmButtonText: 'Yes, Proceed!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.post(`/service-orders/${props.serviceOrder.id}/processes/${props.serviceOrder.current_process.id}/complete`, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your changes have been saved.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    });
                },
            });
        }
    });
};
</script>
