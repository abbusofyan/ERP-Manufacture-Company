<template>
    <div class="modal fade" id="addRequirement" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <svg class="mb-[2.6rem]" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M18.3335 7.29742L14.5002 11.1351C13.8335 11.8025 12.8335 11.8025 12.1668 11.1351C11.5002 10.4677 11.5002 9.46655 12.1668 8.79912L18.8335 2.12487C18.9168 2.04144 19.0002 1.99973 19.0835 1.95801C19.1668 1.9163 19.2502 1.87459 19.3335 1.79116C19.8335 1.6243 20.3335 1.6243 20.6668 1.79116C20.8335 1.79116 21.0002 1.95801 21.1668 2.12487L27.8335 8.79912C28.5002 9.46655 28.5002 10.4677 27.8335 11.1351C27.5002 11.4688 27.1668 11.6357 26.6668 11.6357C26.1668 11.6357 25.8335 11.4688 25.5002 11.1351L21.6668 7.29742V26.6527C21.6668 27.6539 21.0002 28.3213 20.0002 28.3213C19.0002 28.3213 18.3335 27.6539 18.3335 26.6527V7.29742ZM36.6668 33.327V28.3213C36.6668 27.3202 36.0002 26.6527 35.0002 26.6527C34.0002 26.6527 33.3335 27.3202 33.3335 28.3213V33.327C33.3335 34.3281 32.6668 34.9956 31.6668 34.9956H8.3335C7.3335 34.9956 6.66683 34.3281 6.66683 33.327V28.3213C6.66683 27.3202 6.00016 26.6527 5.00016 26.6527C4.00016 26.6527 3.3335 27.3202 3.3335 28.3213V33.327C3.3335 36.1636 5.50016 38.3327 8.3335 38.3327H31.6668C34.5002 38.3327 36.6668 36.1636 36.6668 33.327Z"
                                  fill="black"/>
                            <mask id="mask0_4587_150674" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="3" y="1" width="34" height="38">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M18.3335 7.29742L14.5002 11.1351C13.8335 11.8025 12.8335 11.8025 12.1668 11.1351C11.5002 10.4677 11.5002 9.46655 12.1668 8.79912L18.8335 2.12487C18.9168 2.04144 19.0002 1.99973 19.0835 1.95801C19.1668 1.9163 19.2502 1.87459 19.3335 1.79116C19.8335 1.6243 20.3335 1.6243 20.6668 1.79116C20.8335 1.79116 21.0002 1.95801 21.1668 2.12487L27.8335 8.79912C28.5002 9.46655 28.5002 10.4677 27.8335 11.1351C27.5002 11.4688 27.1668 11.6357 26.6668 11.6357C26.1668 11.6357 25.8335 11.4688 25.5002 11.1351L21.6668 7.29742V26.6527C21.6668 27.6539 21.0002 28.3213 20.0002 28.3213C19.0002 28.3213 18.3335 27.6539 18.3335 26.6527V7.29742ZM36.6668 33.327V28.3213C36.6668 27.3202 36.0002 26.6527 35.0002 26.6527C34.0002 26.6527 33.3335 27.3202 33.3335 28.3213V33.327C33.3335 34.3281 32.6668 34.9956 31.6668 34.9956H8.3335C7.3335 34.9956 6.66683 34.3281 6.66683 33.327V28.3213C6.66683 27.3202 6.00016 26.6527 5.00016 26.6527C4.00016 26.6527 3.3335 27.3202 3.3335 28.3213V33.327C3.3335 36.1636 5.50016 38.3327 8.3335 38.3327H31.6668C34.5002 38.3327 36.6668 36.1636 36.6668 33.327Z"
                                      fill="white"/>
                            </mask>
                            <g mask="url(#mask0_4587_150674)">
                                <rect width="40" height="40" fill="#FF9F43"/>
                            </g>
                        </svg>
                        <div class="text-24 text-bold-500">
                            Upload Attachments
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Attachments</label>
                        <div class="fileContainer">
                            <label class="fileInputLabel" for="fileInput1">
                                <input class="fileInput" id="fileInput1"
                                       type="file"
                                       :class="{ 'is-invalid': form.errors.file_url }"
                                       @input="form.file_url = $event.target.files[0];"
                                       accept="image/png, image/jpg, image/jpeg">
                                <span v-if="form.file_url">{{ form.file_url['name'] }}</span>
                                <span v-else>Choose File</span>
                                <span class="browse">Browse</span>
                            </label>
                            <div class="mt-12 text-[#82868B]">Allowed file types: png, jpg, jpeg.</div>
                        </div>
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
import {ref, computed, onMounted, watch, inject} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    order: Object,
});

const form = useForm({
    file_url: null,
    remarks: null,
});

const parentForm = inject('form')

const submitForm = () => {
    Inertia.post(`/engineering-order-attachment`, {
        file_url: form.file_url,
        remarks: form.remarks,
        engineering_order_id: props.order.id
    }, {
        onSuccess: (data) => {
            closeModal.value.click();
            form.reset();
            Swal.fire({
                title: 'Success!',
                text: "File uploaded!",
                icon: 'success',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                cancelButtonColor: '#626CC6',
            });

            parentForm.attachments = []
            parentForm.attachments = data.props.order.attachments.map(attachment => {
                return {
                    'id': attachment.id,
                    'name': attachment.name,
                    'created_at': attachment.created_at,
                };
            });
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error!',
                text: 'There was an issue updating the Items used. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
};


const closeModal = ref(null);


</script>
