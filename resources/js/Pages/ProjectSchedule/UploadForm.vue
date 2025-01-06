<template>
    <div class="modal fade" :id="`update-schedule-${project.id}`" ref="modal" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M40 12.5C24.8122 12.5 12.5 24.8122 12.5 40C12.5 55.1878 24.8122 67.5 40 67.5C55.1878 67.5 67.5 55.1878 67.5 40C67.5 24.8122 55.1878 12.5 40 12.5ZM7.5 40C7.5 22.0507 22.0507 7.5 40 7.5C57.9493 7.5 72.5 22.0507 72.5 40C72.5 57.9493 57.9493 72.5 40 72.5C22.0507 72.5 7.5 57.9493 7.5 40Z" fill="#FF9F43"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M40 24.166C41.3807 24.166 42.5 25.2853 42.5 26.666V39.9993C42.5 41.3801 41.3807 42.4993 40 42.4993C38.6193 42.4993 37.5 41.3801 37.5 39.9993V26.666C37.5 25.2853 38.6193 24.166 40 24.166Z" fill="#FF9F43"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M37.4995 53.334C37.4995 51.9533 38.6188 50.834 39.9995 50.834H40.0328C41.4136 50.834 42.5328 51.9533 42.5328 53.334C42.5328 54.7147 41.4136 55.834 40.0328 55.834H39.9995C38.6188 55.834 37.4995 54.7147 37.4995 53.334Z" fill="#FF9F43"/>
                        </svg>
                        <div class="text-24 text-bold-500">
                            Change Month of appointment
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Month of appointment <span class="required">*</span></label>
                        <input class="form-control" type="month" v-model="form.date" placeholder="Remark"/>
                        <div v-if="form.errors.date" class="invalid-feedback d-block">{{ form.errors.date }}</div>
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
import {useForm} from '@inertiajs/inertia-vue3';
import Swal from "sweetalert2";
import {ref, onMounted, onBeforeUnmount, nextTick, onUpdated} from "vue";

const props = defineProps({
    project: Object,
});

const form = useForm({
    date: null,
});

const closeModal = ref(null);
const modalElement = ref(null);

const submitForm = () => {
    form.put(`/project-schedules/${props.project.id}`, {
        onSuccess: async () => {
            await nextTick();
            if (closeModal.value) {
                closeModal.value.click();
            }
            handleModalHidden();

            form.reset();
            Swal.fire({
                title: 'Success!',
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

const handleModalHidden = () => {
    const modalBackdrop = document.querySelector('.modal-backdrop');
    if (modalBackdrop) {
        modalBackdrop.remove();
    }
};
</script>
