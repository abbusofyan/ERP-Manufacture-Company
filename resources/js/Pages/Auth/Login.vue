<template>
    <section class="account-page" style="background-image: url('/bg-1.png'); background-repeat: no-repeat; background-size: cover">
        <div class="form-wrap">
            <div class="logo">
                <div class="image"><img src="../../../assets/img/logo-1.svg" alt=""></div>
            </div>
            <div class="text-18 font-semibold text-center">Welcome to Hwee Jan Web App</div>
            <form class="mt-20" @submit.prevent="form.post('/login')">
                <div class="form-group">
                    <label>Email or Username<span class="required">*</span></label>
                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.username }" placeholder="john.doe" v-model="form.username" autofocus>
                    <div v-if="form.errors.username" class="invalid-feedback d-block">{{ form.errors.username }}</div>
                </div>
                <div class="form-group">
                    <div class="flex justify-between gap-16">
                        <label>Password<span class="required">*</span></label>
                        <Link class="forgot-pass" :href="`/forgot-password`">Forgot Password?</Link>
                    </div>
                    <input class="form-control" type="password" :class="{ 'is-invalid': form.errors.password }" v-model="form.password" placeholder="············">
                    <div v-if="form.errors.password" class="invalid-feedback d-block">{{ form.errors.password }}</div>
                </div>
                <div class="custom-checkbox style-1">
                    <input type="checkbox" v-model="form.remember" id="1">
                    <label for="1">Remember Me</label>
                </div>
                <div class="btn-group mt-16">
                    <button class="btn btn-purple w-full" type="submit" :disabled="form.processing">Login</button>
                </div>
            </form>
        </div>
    </section>
</template>

<script setup>
import {useForm, Link} from "@inertiajs/inertia-vue3";

const form = useForm({
    username: null,
    password: null,
    remember: false,
});

</script>
