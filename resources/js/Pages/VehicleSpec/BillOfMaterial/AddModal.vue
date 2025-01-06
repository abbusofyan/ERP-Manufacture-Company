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
                        <label>Item Type</label>
                        <Select2
                            v-model="form.item_type"
                            class="select2-w-100"
                            :class="{ 'is-invalid': form.errors.item_type }"
                            placeholder="Select Item Type"
                            :options="[{ id: 'Assembly Item', text: 'Assembly Item' }, { id: 'Single Item', text: 'Single Item' }]"
                        />
                    </div>
                    <div class="form-group">
                        <label>Item Name</label>
                        <Select2
                            v-model="form.product_id"
                            class="select2-w-100"
                            :class="{ 'is-invalid': form.errors.product_id }"
                            placeholder="Select Item"
                            @select="(event) => onItemSelect(event, index)"
                            :settings="{
                                ajax: {
                                    url: '/data/vehicle-spec/get-item-options',
                                    dataType: 'json',
                                    method: 'POST',
                                    data: function (params) {
                                        console.log(form.item_type)
                                        return {
                                            search: params.term,
                                            item_type: form.item_type,
                                            _token: csrf,
                                        };
                                    },
                                    processResults: function (data, params) {
                                        return {
                                            results: data.map(function (item) {
                                                if (typeof item.sku == 'undefined') {
                                                    return {
                                                        text: `${item.code} | ${item.name}`,
                                                        id: item.id,
                                                        type: 'BOM',
                                                        data: item
                                                    };
                                                } else {
                                                    return {
                                                        text: `${item.sku} | ${item.name}`,
                                                        id: item.id,
                                                        type: 'Material',
                                                        data: item
                                                    };
                                                }
                                            })
                                        };
                                    }
                                }
                            }"
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
import {ref, computed, inject} from "vue";

const props = defineProps({
    products: Array,
    assemblies: Array,
    csrf: String
});

const form = useForm({
    product_id: null,
    item_type: null,
    qty: null,
    materials: []
});

const selectedItem = ref([])

const parentForm = inject('form');

const submitForm = () => {
    // const selectedProduct = props.products.find(product => product.id == form.product_id);
    if (selectedItem.value.type == 'BOM') {
        parentForm.items.push({
            type: 'BOM',
            qty: form.qty,
            detail: selectedItem.value.data
        });
    } else {
        parentForm.items.push({
            type: 'Material',
            qty: form.qty,
            detail: selectedItem.value.data
        });
    }
    form.product_id = null;
    form.qty = null;
    closeModal.value.click();
};

const onItemSelect = (event, index) => {
    selectedItem.value = event
}

const closeModal = ref(null);

</script>
