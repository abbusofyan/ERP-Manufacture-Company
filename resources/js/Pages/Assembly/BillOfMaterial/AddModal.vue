<template>
    <div class="modal fade" id="addRequirement" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Add Requirement
                        </div>
                        <div class="text-12 text-bold-500">
                            Select item and quantity
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item Name</label>
                        <Select2 class="select2-w-100"
                            v-model="form.product_id"
                            :settings="{
                                dropdownParent:'#addRequirement',
                                ajax: {
                                    url: '/data/products',
                                    dataType: 'json',
                                    method: 'POST',
                                    data: function (params) {
                                      return {
                                        search: params.term,
                                        _token: csrf,
                                      };
                                    },
                                    processResults: function (data, params) {
                                        return {
                                            results: data.map(function (item) {
                                                return {
                                                    text: `${item.sku} | ${item.name}`,
                                                    id: item.id,
                                                    sku: item.sku,
                                                    data: item,
                                                };
                                            })
                                        };
                                    }
                                }
                            }"
                            @select="(selected) => {
                                selectedProduct = selected.data
                            }"
                            placeholder="Select Item"
                        />
                    </div>
                    <div class="form-group">
                        <label>Qty</label>
                        <input class="form-control" type="number" v-model="form.qty">
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

const props = defineProps({
    products: Array,
    csrf: String
});

const form = useForm({
    product_id: null,
    qty: null,
    products:[]
});

const parentForm = inject('form')

const selectedProduct = ref({})

const submitForm = () => {
    // console.log(parentForm.materials[0].product.id);
    const occurrenceCount = parentForm.materials.filter(item => item.product.id === selectedProduct.value.id).length;
    if (occurrenceCount > 0) {
        return Swal.fire({
            title: "Cannot select the same item",
            icon: "warning",
            confirmButtonColor: "#ea5455",
            cancelButtonColor: "#009CDB",
            confirmButtonText: "OK!",
        })
    }
    parentForm.materials.push({
        qty: form.qty,
        product: selectedProduct.value
    })
    form.product_id = null
    form.qty = null
    closeModal.value.click();
}

const formattedProducts = computed(() => {
    return props.products.map(product => ({
        id: product.id,
        text: product.name,
        data: product
    }));
});

const closeModal = ref(null);


</script>
