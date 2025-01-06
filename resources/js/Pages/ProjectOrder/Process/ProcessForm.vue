<template>
    <div class="modal fade" id="addProcess" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Add Process
                        </div>
                        <p>Type the process below</p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Process name <span class="required text-danger">*</span></label>
                        <textarea class="form-control" v-model="form.name" rows="3" placeholder="Type Here"></textarea>
                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
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
    projectOrder: Object,
});

const form = useForm({
    name: null,
});

const closeModal = ref(null);

const submitForm = () => {
    form.post(`/project-orders/${props.projectOrder.id}/processes`, {
        onSuccess: () => {
            closeModal.value.click();
            form.reset();
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
</script>
