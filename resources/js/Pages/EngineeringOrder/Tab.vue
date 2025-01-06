<template>
    <div class="rounded-[.5rem] border-[1px] border-solid border-[#626CC6] mt-25">
        <div class="row no-gutters">
            <div class="col-lg-3">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 1" :class="{'btn-purple--brounded' : form.tab !== 1}" href="javascript:void(0)">Requirement</a>
            </div>
            <div class="col-lg-2">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 2" :class="{'btn-purple--brounded' : form.tab !== 2}" href="javascript:void(0)">Item Used</a>
            </div>
            <div class="col-lg-2">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 3" :class="{'btn-purple--brounded' : form.tab !== 3}" href="javascript:void(0)">Process</a>
            </div>
            <div class="col-lg-2">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 4" :class="{'btn-purple--brounded' : form.tab !== 4}" href="javascript:void(0)">Summary</a>
            </div>
            <div class="col-lg-3">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 5" :class="{'btn-purple--brounded' : form.tab !== 5}" href="javascript:void(0)">Attachment & Test Result</a>
            </div>
        </div>
    </div>

    <form>
        <div class="tab-1" v-if="form.tab === 1">
            <RequirementForm></RequirementForm>
        </div>

        <div class="tab-2" v-if="form.tab === 2">
            <BillOfMaterialForm :materials="materials" :products="products" :order="order"></BillOfMaterialForm>
        </div>

        <div class="tab-3" v-if="form.tab === 3">
            <ProcessForm :order="order"></ProcessForm>
        </div>

        <div class="tab-4" v-if="form.tab === 4">
            <SummaryForm :order="order"></SummaryForm>
        </div>

        <div class="tab-5" v-if="form.tab === 5">
            <AttachmentForm :order="order"></AttachmentForm>
        </div>
        <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
    </form>
</template>

<script setup>
import {usePage, useForm, Link} from "@inertiajs/inertia-vue3";
import RequirementForm from "@/Pages/EngineeringOrder/Requirement/Table.vue"
import BillOfMaterialForm from "@/Pages/EngineeringOrder/BillOfMaterial/Form.vue"
import ProcessForm from "@/Pages/EngineeringOrder/Process/Form.vue"
import AttachmentForm from "@/Pages/EngineeringOrder/Attachment/Form.vue"
import SummaryForm from "@/Pages/EngineeringOrder/Summary/Form.vue"
import { ref } from 'vue';

const props = defineProps({
    materials: Object,
    products: Object,
    order: Object
})

const form = useForm({
    tab: 1
})
</script>
