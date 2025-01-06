<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="store" class="big-title">Edit Warehouse</div>
            <div v-else class="big-title">Create New Warehouse</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/locations">Location</Link>
                    </li>
                    <li>
                        <Link href="/stores">Warehouse</Link>
                    </li>
                    <li>
                        <span v-if="store">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="store ? form.put(`/stores/${store.id}`) : form.post('/stores')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>Warehouse Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div>
                                    <div class="form-group">
                                        <label>Warehouse Name<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Store Name">
                                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="mb-14">Status<span class="required">*</span></label>
                                        <div class="flex flex-wrap gap-[3rem] items-center">
                                            <div class="custom-checkbox style-2">
                                                <input type="radio" v-model="form.status" value="1" id="1">
                                                <label for="1">Active</label>
                                            </div>
                                            <div class="custom-checkbox style-2">
                                                <input type="radio" v-model="form.status" value="0" id="0">
                                                <label for="0">Inactive</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="store" type="submit" :disabled="form.processing">Update Warehouse</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create Warehouse</button>
                        <Link class="btn btn-light btn-light--brounded" href="/stores">Discard</Link>
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
    store: Object,
});

const form = useForm({
    name: null,
    status: 1,
});

onMounted(() => {
    if (props.store) {
        form.name = props.store.name;
        form.status = props.store.status;
    }
});
</script>
