<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="location" class="big-title">Edit Location</div>
            <div v-else class="big-title">Create New Location</div>
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
                        <Link href="/locations">List</Link>
                    </li>
                    <li>
                        <span v-if="location">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="location ? form.put(`/locations/${location.id}`) : form.post('/locations')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>Location Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div>
                                    <div class="form-group">
                                        <label>Location Name<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Location Name">
                                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Store<span class="required">*</span></label>
                                        <Select2 :options="stores" v-model="form.store_id" required></Select2>
                                        <div v-if="form.errors.store_id" class="invalid-feedback d-block">{{ form.errors.store_id }}</div>
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
                        <button class="btn btn-purple" v-if="location" type="submit" :disabled="form.processing">Update Location</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create Location</button>
                        <Link class="btn btn-light btn-light--brounded" href="/locations">Discard</Link>
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
    location: Object,
    stores: Array,
});

const form = useForm({
    name: null,
    store_id: null,
    status: 1,
});

onMounted(() => {
    if (props.location) {
        form.name = props.location.name;
        form.status = props.location.status;
        form.store_id = props.location.store_id;
    }
});
</script>
