<template>
    <div class="modal fade" :id="`submitDeliveryDate${additionId ? additionId : ''}`" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <svg class="mb-[2.6rem]" width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M40 12.5C24.8122 12.5 12.5 24.8122 12.5 40C12.5 55.1878 24.8122 67.5 40 67.5C55.1878 67.5 67.5 55.1878 67.5 40C67.5 24.8122 55.1878 12.5 40 12.5ZM7.5 40C7.5 22.0507 22.0507 7.5 40 7.5C57.9493 7.5 72.5 22.0507 72.5 40C72.5 57.9493 57.9493 72.5 40 72.5C22.0507 72.5 7.5 57.9493 7.5 40Z" fill="#FF9F43"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M40 24.166C41.3807 24.166 42.5 25.2853 42.5 26.666V39.9993C42.5 41.3801 41.3807 42.4993 40 42.4993C38.6193 42.4993 37.5 41.3801 37.5 39.9993V26.666C37.5 25.2853 38.6193 24.166 40 24.166Z" fill="#FF9F43"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M37.4995 53.334C37.4995 51.9533 38.6188 50.834 39.9995 50.834H40.0328C41.4136 50.834 42.5328 51.9533 42.5328 53.334C42.5328 54.7147 41.4136 55.834 40.0328 55.834H39.9995C38.6188 55.834 37.4995 54.7147 37.4995 53.334Z" fill="#FF9F43"/>
                        </svg>
                        <div class="text-24 text-bold-500">
                            Complete the process?
                        </div>
                        <p>Select a delivery date to complete this process</p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Delivery Date <span class="required text-danger">*</span></label>
                        <VueDatePicker v-model="form.delivery_date" :class="{ 'is-invalid': form.errors.delivery_date }" :enable-time-picker="false" :format="format" placeholder="Select date"></VueDatePicker>
                        <div v-if="form.errors.delivery_date" class="invalid-feedback d-block">{{ form.errors.delivery_date }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 mb-3 text-center">
                        <a class="btn btn-purple mr-10" @click="submitForm" href="javascript:void(0)">Submit</a>
                        <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" ref="closeModal" href="javascript:void(0)">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {useForm, usePage} from '@inertiajs/inertia-vue3';
import Swal from "sweetalert2";
import {computed, onUpdated, ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    projectOrder: Object,
    additionId: String,
});

const form = useForm({
    delivery_date: null,
});

const closeModal = ref(null);

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

const submitForm = () => {
    form.post(`/project-orders/${props.projectOrder.id}/processes/${props.projectOrder.current_process.id}/complete`, {
        onSuccess: () => {
            closeModal.value.click();
            Swal.fire({
                title: 'Success !',
                text: "Your changes have been saved!",
                icon: 'success',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                cancelButtonColor: '#626CC6',
            });
        }
    });
};

onUpdated(() => {
    if (form.errors && form.errors.first_attachment) {
        const modalElement = document.getElementById(`submitDeliveryDate${props.additionId ? props.additionId : ''}`);
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
                Inertia.get(
                    `/project-orders/${props.projectOrder.id}/attachments`, {},
                    {
                        preserveState: true,
                        preserveScroll: true,
                        replace: true,
                    }
                );

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: form.errors.first_attachment,
                    showCancelButton: true,
                    cancelButtonColor: '#009CDB',
                    cancelButtonText: 'Cancel',
                });
            }
        }
    }
})
</script>
