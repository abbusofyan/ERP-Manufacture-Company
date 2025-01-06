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
                        <input class="form-control" v-model="form.name" type="text" placeholder="Type Here"/>
                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                    </div>
                    <div class="form-group">
                        <label>Standard time (hours)</label>
                        <input class="form-control" v-model="form.standard_time" type="text" placeholder="Type Here"/>
                        <div v-if="form.errors.standard_time" class="invalid-feedback d-block">{{ form.errors.standard_time }}</div>
                    </div>
                    <div class="form-group">
                        <label>Manpower</label>
                        <input class="form-control" v-model="form.manpower" type="number" placeholder="Type Here"/>
                        <div v-if="form.errors.manpower" class="invalid-feedback d-block">{{ form.errors.manpower }}</div>
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
    serviceOrder: Object,
});

const form = useForm({
    name: null,
    standard_time: null,
    manpower: null,
});

const closeModal = ref(null);

const submitForm = () => {
    form.post(`/service-orders/${props.serviceOrder.id}/processes`, {
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
