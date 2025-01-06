<template>
    <div class="modal fade" id="uploadRequirementUsed" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Add Items
                        </div>
                        <p>Select item and quantity</p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group select2-w-100">
                        <label>Item Name</label>
                        <Select2
                            v-model="form.project_order_requirement_id"
                            :class="{ 'is-invalid': form.errors.project_order_requirement_id }"
                            placeholder="Select Item"
                            class="flex-fill"
                            :settings="{
                                dropdownParent:'#uploadRequirementUsed',
                                ajax: {
                                    url: '/data/requirements',
                                    dataType: 'json',
                                    method: 'POST',
                                    data: function (params) {
                                        return {
                                            search: params.term,
                                            project_order_id: projectOrder.id,
                                            _token: csrf,
                                        };
                                    },
                                    processResults: function (data, params) {
                                        form.quantity = null;
                                        return {
                                            results: data.map(function (item) {
                                                return {
                                                    text: `REQ:${item.id} | ${item.storage_item.storage.code}-${item.storage_item.product.sku} | ${item.storage_item.product.name}`,
                                                    id: item.id,
                                                    data: item,
                                                };
                                            })
                                        };
                                    },
                                },
                            }"
                            @select="(selected) => form.max_quantity = selected.data.storage_item.quantity"
                        >
                        </Select2>
                        <div v-if="form.errors.project_order_requirement_id" class="invalid-feedback d-block">{{ form.errors.project_order_requirement_id }}</div>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input class="form-control" v-model="form.quantity" type="number" :max="form.max_quantity" placeholder="0">
                        <div v-if="form.errors.quantity" class="invalid-feedback d-block">{{ form.errors.quantity }}</div>
                    </div>
                    <div class="form-group">
                        <label>Date required</label>
                        <VueDatePicker
                            v-model="form.date_required"
                            :class="{ 'is-invalid': form.errors.date_required }"
                            placeholder="Date required"
                            :enable-time-picker="false"
                            :format="format"
                            :min-date="new Date()"
                        />
                        <div v-if="form.errors.date_required" class="invalid-feedback d-block">{{ form.errors.date_required }}</div>
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
    csrf: String,
    projectOrder: Object,
});

const form = useForm({
    project_order_requirement_id: null,
    quantity: null,
    max_quantity: null,
    date_required: null,
});

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

const closeModal = ref(null);

const submitForm = () => {
    form.post(`/project-orders/${props.projectOrder.id}/requirements-used`, {
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
