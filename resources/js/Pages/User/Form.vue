<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="user" class="big-title">Edit User</div>
            <div v-else class="big-title">Add New User</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/users">User Management</Link>
                    </li>
                    <li>
                        <Link href="/users">User List</Link>
                    </li>
                    <li class="active">
                        <span v-if="user">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Basic Information</div>
            <div class="form-wrap max-w-[50rem]">
                <form @submit.prevent="user ? form.put(`/users/${user.id}`) : form.post('/users')">
                    <div class="form-group">
                        <label>Name<span class="required">*</span></label>
                        <input type="text" placeholder="Name" :class="{ 'is-invalid': form.errors.name }" v-model="form.name">
                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                    </div>
                    <div class="form-group">
                        <label>Role<span class="required">*</span></label>
                        <Select2 v-model="form.role" class="order-number" :class="{ 'is-invalid': form.errors.role }" :options="roles" placeholder="Type" :disabled="requisition ? 'disabled' : null"/>
                        <div v-if="form.errors.role" class="invalid-feedback d-block">{{ form.errors.role }}</div>
                    </div>
                    <div class="form-group">
                        <label>Username<span class="required">*</span></label>
                        <input type="text" :class="{ 'is-invalid': form.errors.username }" v-model="form.username" placeholder="Username" autocomplete="username-new">
                        <div v-if="form.errors.username" class="invalid-feedback d-block">{{ form.errors.username }}</div>
                    </div>
                    <div class="form-group">
                        <label>Email<span class="required">*</span></label>
                        <input type="text" :class="{ 'is-invalid': form.errors.email }" v-model="form.email" placeholder="Email" autocomplete="email-new">
                        <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
                    </div>
                    <div class="form-group">
                        <label>Phone<span class="required">*</span></label>
                        <input type="text" :class="{ 'is-invalid': form.errors.phone }" v-model="form.phone" placeholder="Phone" autocomplete="phone-new">
                        <div v-if="form.errors.phone" class="invalid-feedback d-block">{{ form.errors.phone }}</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password<span v-if="!user" class="text-danger">*</span></label>
                        <input type="password" class="form-control" :class="{ 'is-invalid': form.errors.password }" autocomplete="password-new" v-model="form.password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"/>
                        <div v-if="form.errors.password" class="invalid-feedback d-block">{{ form.errors.password }}</div>
                        <span class="form-label text-black-50">Leave empty if unchanged.</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Confirmation<span v-if="!user" class="text-danger">*</span></label>
                        <input type="password" class="form-control" :class="{ 'is-invalid': form.errors.password_confirmation }" v-model="form.password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"/>
                        <div v-if="form.errors.password_confirmation" class="invalid-feedback d-block">{{ form.errors.password_confirmation }}</div>
                        <span class="form-label text-black-50">Leave empty if unchanged.</span>
                    </div>
                    <div class="form-group">
                        <label>Status<span class="required">*</span></label>
                        <label class="switch" for="checkbox">
                            <input id="checkbox" type="checkbox" v-model="form.email_verified_at" value="true">
                            <div class="slider round"></div>
                            <div class="icon"></div>
                        </label>
                        <p class="mt-10">Account is active</p>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" :disabled="form.processing">Add User</button>
                        <Link class="btn btn-light btn-light--brounded" href="/users">Discard</Link>
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
    user: Object,
    userRole: String,
    roles: Array,
});

const form = useForm({
    name: null,
    username: null,
    phone: null,
    email: null,
    email_verified_at: null,
    role: null,
    password: null,
    password_confirmation: null,
});

onMounted(() => {
    if (props.user) {
        form.name = props.user.name;
        form.username = props.user.username;
        form.phone = props.user.phone;
        form.email = props.user.email;
        form.email_verified_at = !!props.user.email_verified_at;
        form.role = props.userRole;
    }
});
</script>
