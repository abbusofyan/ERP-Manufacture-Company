<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="uom" class="big-title">Edit UOM</div>
            <div v-else class="big-title">Create New UOM</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/products">Inventory</Link>
                    </li>
                    <li>
                        <Link href="/uom">UOM</Link>
                    </li>
                    <li>
                        <span v-if="uom">Edit UOM</span>
                        <span v-else>Create UOM</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="uom ? form.put(`/uom/${uom.id}`) : form.post('/uom')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>UOM Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Code<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.code }" v-model="form.code" placeholder="Code">
                                    <div v-if="form.errors.code" class="invalid-feedback d-block">{{ form.errors.code }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Description<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.description }" v-model="form.description" placeholder="Description">
                                    <div v-if="form.errors.description" class="invalid-feedback d-block">{{ form.errors.description }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="uom" type="submit" :disabled="form.processing">Update UOM</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create UOM</button>
                        <Link class="btn btn-light btn-light--brounded" href="/uom">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted} from "vue";

const props = defineProps({
    uom: Object,
});

const form = useForm({
    code: null,
    description: null,
});

onMounted(() => {
    if (props.uom) {
        form.code = props.uom.code;
        form.description = props.uom.description;
    }
});
</script>
