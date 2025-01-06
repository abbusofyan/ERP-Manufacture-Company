<template>
    <div class="modal fade" :id="`uploadPaid-${projectInvoice.id}`" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="11.6665" y="15" width="23.3333" height="16.6667" rx="2" stroke="#28C76F" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="23.3333" cy="23.3333" r="3.33333" stroke="#28C76F" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M28.3333 14.9987V11.6654C28.3333 9.82442 26.841 8.33203 25 8.33203H8.33333C6.49238 8.33203 5 9.82442 5 11.6654V21.6654C5 23.5063 6.49238 24.9987 8.33333 24.9987H11.6667" stroke="#28C76F" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="text-24 text-bold-500">
                            Paid Invoice
                        </div>
                        <p>Enter the amount paid</p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Total Amount</label>
                        <p><strong>${{ projectInvoice.remaining_amount }} (Total: ${{ projectInvoice.project_quotation.total }})</strong></p>
                    </div>
                    <div class="form-group">
                        <label>Amount Paid <span class="required">*</span></label>
                        <input class="form-control" v-model="form.amount" placeholder="Remark"/>
                        <div v-if="form.errors.amount" class="invalid-feedback d-block">{{ form.errors.amount }}</div>
                    </div>
                    <div class="form-group">
                        <label>Mode of Payment</label>
                        <textarea class="form-control" v-model="form.mode_of_payment" rows="3" placeholder="Remark"></textarea>
                        <div v-if="form.errors.mode_of_payment" class="invalid-feedback d-block">{{ form.errors.mode_of_payment }}</div>
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
import {ref} from "vue";

const props = defineProps({
    projectInvoice: Object,
});

const form = useForm({
    amount: null,
    mode_of_payment: null,
});

const closeModal = ref(null);

const submitForm = () => {
    form.post(`/project-invoices/${props.projectInvoice.id}/paid`, {
        onSuccess: () => {
            closeModal.value.click();
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
</script>
