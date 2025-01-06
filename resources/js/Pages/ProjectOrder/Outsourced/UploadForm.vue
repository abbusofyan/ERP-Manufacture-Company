<template>
    <div class="modal fade" id="uploadOutsourced" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M40 26.666V39.2712" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M40 51.7969L40 51.9553" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.6664 63.3337H63.3331C65.5415 63.3182 67.599 62.21 68.8271 60.3745C70.0553 58.539 70.2947 56.2144 69.4664 54.167L45.7998 13.3337C44.6256 11.2116 42.3917 9.89453 39.9664 9.89453C37.5412 9.89453 35.3072 11.2116 34.1331 13.3337L10.4664 54.167C9.65441 56.1662 9.86016 58.4347 11.0186 60.2551C12.177 62.0755 14.1449 63.2226 16.2998 63.3337" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="text-24 text-bold-500">
                            Upload Outsourced Item
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" class="form-control" v-model="form.name" placeholder="Item Name" />
                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" class="form-control" v-model="form.quantity" placeholder="Quantity" />
                        <div v-if="form.errors.quantity" class="invalid-feedback d-block">{{ form.errors.quantity }}</div>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" v-model="form.price" placeholder="Price" />
                        <div v-if="form.errors.price" class="invalid-feedback d-block">{{ form.errors.price }}</div>
                    </div>
                    <div class="form-group">
                        <label>Upload Document</label>
                        <div class="fileContainer">
                            <label class="fileInputLabel" for="fileInput">
                                <input class="fileInput" id="fileInput"
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
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from "sweetalert2";
import { ref } from "vue";

const props = defineProps({
    projectOrder: Object,
});

const form = useForm({
    name: '',
    quantity: '',
    price: '',
    file_url: null,
});

const closeModal = ref(null);

const submitForm = () => {
    form.post(`/project-orders/${props.projectOrder.id}/outsourced`, {
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
