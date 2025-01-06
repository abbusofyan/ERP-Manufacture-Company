<template>
    <div class="rounded-[.5rem] border-[1px] border-solid border-[#626CC6] mt-25">
        <div class="row no-gutters">
            <!-- <div class="col-lg-4">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 1" :class="{'btn-purple--brounded' : form.tab !== 1}" href="javascript:void(0)">Requirements</a>
            </div> -->
            <div class="col-lg-6">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 1" :class="{'btn-purple--brounded' : form.tab !== 1}" href="javascript:void(0)">Bill of Material</a>
            </div>
            <div class="col-lg-6">
                <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 2" :class="{'btn-purple--brounded' : form.tab !== 2}" href="javascript:void(0)">Process</a>
            </div>
        </div>
    </div>

    <form>
        <!-- <div class="tab-1" v-if="form.tab === 1">
            <RequirementForm :products="products"></RequirementForm>
        </div> -->

        <div class="tab-2" v-if="form.tab === 1">
            <BillOfMaterialForm :products="products" :csrf="csrf"></BillOfMaterialForm>
        </div>

        <div class="tab-3" v-if="form.tab === 2">
            <ProcessForm></ProcessForm>
        </div>
        <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
    </form>
</template>

<script setup>
import {usePage, useForm, Link} from "@inertiajs/inertia-vue3";
// import RequirementForm from "@/Pages/Assembly/Requirement/Form.vue"
import BillOfMaterialForm from "@/Pages/Assembly/BillOfMaterial/Form.vue"
import ProcessForm from "@/Pages/Assembly/Process/Form.vue"
import { ref } from 'vue';

const props = defineProps({
    products: Array,
    csrf: String
})

const form = useForm({
    tab: 1
})
</script>
