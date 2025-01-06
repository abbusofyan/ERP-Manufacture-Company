<template>
    <div class="modal fade" id="addRequirement" role="dialog" style="overflow:hidden;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <div class="text-24 text-bold-500">
                            Add Material
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
                            :options="formattedProducts"
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
});

const form = useForm({
    product_id: null,
    qty: null,
    products:[]
});

const parentForm = inject('form')

const formattedProducts = computed(() => {
    return props.products.map(product => ({
        id: product.id,
        text: product.name
    }));
});

const submitForm = () => {
    const selectedProduct = props.products.find(product => product.id == form.product_id)
    console.log(selectedProduct);
    parentForm.items_used.push({
        product_id: selectedProduct.id,
        sku: selectedProduct.sku,
        name: selectedProduct.name,
        uom: selectedProduct.uom.code,
        planned_qty: form.qty,
    })
    form.product_id = null
    form.qty = null
    closeModal.value.click();
}


const closeModal = ref(null);


</script>
