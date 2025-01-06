<!-- SetStandardTimeModal.vue -->
<template>
    <div class="modal fade" :id="`setStandardTimeModal-${index}`" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-24 pt-36 pb-30">
                <div class="modal-header">
                    <div class="col-md-12 mt-3 text-center">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                            <path d="M40 26.666V39.2712" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M40 51.7969L40 51.9553" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.6664 63.3337H63.3331C65.5415 63.3182 67.599 62.21 68.8271 60.3745C70.0553 58.539 70.2947 56.2144 69.4664 54.167L45.7998 13.3337C44.6256 11.2116 42.3917 9.89453 39.9664 9.89453C37.5412 9.89453 35.3072 11.2116 34.1331 13.3337L10.4664 54.167C9.65441 56.1662 9.86016 58.4347 11.0186 60.2551C12.177 62.0755 14.1449 63.2226 16.2998 63.3337" stroke="#FF9F43" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="text-24 text-bold-500">
                            Set Standard Time
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Standard Time (hours)</label>
                        <input type="number" class="form-control" v-model="standardTime" placeholder="0"/>
                        <div v-if="formErrors.standard_time" class="invalid-feedback d-block">{{ formErrors.standard_time }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 mb-3 text-center">
                        <a class="btn btn-purple mr-10" @click="submit" href="javascript:void(0)">Submit</a>
                        <a class="btn btn-purple btn-purple--brounded" data-bs-dismiss="modal" aria-label="Close" href="javascript:void(0)">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    process: Object,
    index: Number,
    formErrors: Object,
});

const emits = defineEmits(['submit']);

const standardTime = ref(props.process.standard_time || null);

const submit = () => {
    emits('submit', props.process.id, standardTime.value, `setStandardTimeModal-${props.index}`);
};
</script>
