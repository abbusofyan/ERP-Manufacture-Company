<template>
    <section class="account-page" style="background-image: url('/bg-1.png'); background-repeat: no-repeat; background-size: cover">
        <div class="form-wrap">
            <div class="logo">
                <div class="image"><img src="../../../assets/img/logo-1.svg" alt=""></div>
            </div>
            <div class="content mb-26">
                <div class="title">Reset Password 🔒</div>
                <div class="desc">for<a class="font-medium" href="#"> john.doe@email.com</a></div>
                <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
            </div>
            <form @submit.prevent="form.post('/reset-password')">
                <div class="form-group">
                    <label>New Password</label>
                    <input class="form-control" type="password" :class="{ 'is-invalid': form.errors.password }" placeholder="············" v-model="form.password" autofocus>
                    <div v-if="form.errors.password" class="invalid-feedback d-block">{{ form.errors.password }}</div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input class="form-control" type="password" :class="{ 'is-invalid': form.errors.password_confirmation }" placeholder="············" v-model="form.password_confirmation" autofocus>
                    <div v-if="form.errors.password_confirmation" class="invalid-feedback d-block">{{ form.errors.password_confirmation }}</div>
                </div>
                <div class="btn-group mt-16">
                    <button class="btn btn-purple w-full" :disabled="form.processing">Set New Password</button>
                </div>
                <Link class="back" href="/login"><em class="fa-regular fa-angle-left"></em><span> Back to log in</span></Link>
            </form>
        </div>
    </section>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    email: props.email,
    password: null,
    password_confirmation: null,
    token: props.token,
});
</script>
