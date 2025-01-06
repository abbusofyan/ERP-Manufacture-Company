<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="category" class="big-title">Edit Category</div>
            <div v-else class="big-title">Create New Category</div>
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
                        <Link href="/categories">Category</Link>
                    </li>
                    <li>
                        <span v-if="category">Edit Category</span>
                        <span v-else>Create Category</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="category ? form.post(`/categories/${category.id}`) : form.post('/categories')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>Basic Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>Name<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Location Name">
                                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Prefix<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.prefix }" v-model="form.prefix" placeholder="Prefix">
                                        <div v-if="form.errors.prefix" class="invalid-feedback d-block">{{ form.errors.prefix }}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Update Diagram</label>
                                    <input type="file" class="form-control-file"
                                           :class="{ 'is-invalid': form.errors.file_url }"
                                           @input="form.file_url = $event.target.files[0];"
                                           accept="image/png, image/jpg, image/jpeg">
                                    <div v-if="form.errors.file_url" class="invalid-feedback d-block">{{ form.errors.file_url }}</div>
                                    <label class="note">Allowed file types: png, jpg, jpeg.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="category" type="submit" :disabled="form.processing">Update Category</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create Category</button>
                        <Link class="btn btn-light btn-light--brounded" href="/categories">Discard</Link>
                    </div>
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
    category: Object,
});

const form = useForm({
    name: null,
    prefix: null,
    file_url: null,
});

onMounted(() => {
    if (props.category) {
        form.name = props.category.name;
        form.prefix = props.category.prefix;
    }
});
</script>
