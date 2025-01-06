<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Account</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li class="active">
                        <Link href="/user">User</Link>
                    </li>
                    <li class="active"><span>Account Settings</span></li>
                </ol>
            </nav>
        </div>
        <div class="my-tabs mt-26">
            <ul class="tabs-nav">
                <li class="active" data-tab="tab1">
                    <span class="icon"><img class="svg" src="../../../assets/img/user.svg" alt="user"></span><span>Overview</span>
                </li>
                <li data-tab="tab2">
                    <Link class="d-flex gap-1" href="/account/password/change">
                        <span class="icon" href="/profile/password/change">
                            <img class="svg" src="../../../assets/img/lock.svg" alt="security">
                        </span>
                        <span>Security</span>
                    </Link>
                </li>
            </ul>
            <div class="tab-container">
                <form class="tab1 tab-content d-block" @submit.prevent="user ? form.post(`/account/profile/edit`) : null">
                    <div class="box pt-20 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Profile Details</div>
                        <div class="change-image-el">
                            <input class="custom-file-input" type="file">
                            <div class="avatar">
                                <img v-if="!form.avatar" src="../../../assets/img/avatar.png" alt="Avatar">
                                <img v-else-if="form.avatar_review" :src="`${form.avatar_review}`" alt="Avatar">
                                <img v-else :src="`${form.avatar}`" alt="Avatar">
                            </div>
                            <div class="content pl-24">
                                <div class="btn-group">
                                    <input id="avatar" type="file" class="d-none" :class="{ 'is-invalid': form.errors.avatar }" @input="form.avatar = $event.target.files[0];" @change="handleFileInputChange" ref="fileInput" accept="image/*">
                                    <label class="choose-file btn btn-purple" for="avatar">Upload</label>
                                    <button class="reset-btn btn btn-light btn-light--brounded" type="button" @click="resetAvatar">Reset</button>
                                </div>
                                <div class="mt-8">Allowed file types: png, jpg, jpeg.</div>
                            </div>
                        </div>
                        <div class="form-wrap mt-24">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input class="form-control" :class="{ 'is-invalid': form.errors.name }" type="text" v-model="form.name">
                                    <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Contact</label>
                                    <input class="form-control" :class="{ 'is-invalid': form.errors.phone }" type="tel" v-model="form.phone">
                                    <div v-if="form.errors.phone" class="invalid-feedback d-block">{{ form.errors.phone }}</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" :class="{ 'is-invalid': form.errors.email }" type="email" v-model="form.email">
                                    <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="form-control" :class="{ 'is-invalid': form.errors.username }" type="text" v-model="form.username">
                                    <div v-if="form.errors.username" class="invalid-feedback d-block">{{ form.errors.username }}</div>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                                <Link class="btn btn-light btn-light--brounded" href="/account">Discard</Link>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {computed, onMounted} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: null,
    username: null,
    phone: null,
    email: null,
    avatar: null,
    avatar_review: null,
});

const handleFileInputChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            form.avatar_review = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const resetAvatar = () => {
    form.avatar = null;
    form.avatar_review = null;
    form.post(`/account/profile/edit`);
};

onMounted(() => {
    form.name = props.user.name;
    form.username = props.user.username;
    form.phone = props.user.phone;
    form.email = props.user.email;

    if (props.user.avatar) {
        form.avatar = `/storage/${props.user.avatar}`;
    }
})
</script>
