<template>
    <div class="border-[1px] border-solid border-[#626CC6] mt-[2.6rem] d-flex flex-wrap rounded-[.5rem] overflow-hidden">
        <div class="flex-fill">
            <a v-if="tab === 3" class="btn btn-lg btn-purple w-100 rounded-0" href="javascript:void(0)">Requirements</a>
            <Link v-else class="btn btn-lg btn-purple--brounded w-100 rounded-0" :href="`/service-orders/${serviceOrder.id}/requirements`"><span>Requirements</span></Link>
        </div>
        <div class="flex-fill">
            <a v-if="tab === 4" class="btn btn-lg btn-purple w-100 rounded-0" href="javascript:void(0)">Item Used</a>
            <Link v-else class="btn btn-lg btn-purple--brounded w-100 rounded-0" :href="`/service-orders/${serviceOrder.id}/requirements-used`"><span>Item Used</span></Link>
        </div>
        <div class="flex-fill">
            <a v-if="tab === 1" class="btn btn-lg btn-purple w-100 rounded-0" href="javascript:void(0)">Process</a>
            <Link v-else class="btn btn-lg btn-purple--brounded w-100 rounded-0" :href="`/service-orders/${serviceOrder.id}/processes`"><span>Process</span></Link>
        </div>
        <div class="flex-fill">
            <a v-if="tab === 6" class="btn btn-lg btn-purple w-100 rounded-0" href="javascript:void(0)">Outsourced</a>
            <Link v-else class="btn btn-lg btn-purple--brounded w-100 rounded-0" :href="`/service-orders/${serviceOrder.id}/outsourced`"><span>Outsourced</span></Link>
        </div>
        <div class="flex-fill">
            <a v-if="tab === 2" class="btn btn-lg btn-purple w-100 rounded-0" href="javascript:void(0)">Attachments</a>
            <Link v-else class="btn btn-lg btn-purple--brounded w-100 rounded-0" :href="`/service-orders/${serviceOrder.id}/attachments`"><span>Attachments</span></Link>
        </div>
        <div class="flex-fill">
            <a v-if="tab === 5" class="btn btn-lg btn-purple w-100 rounded-0" href="javascript:void(0)">Service Report</a>
            <Link v-else class="btn btn-lg btn-purple--brounded w-100 rounded-0" :href="`/service-orders/${serviceOrder.id}/reports`"><span>Service Report</span></Link>
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import {Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    tab: Object,
    serviceOrder: Object,
});
</script>
