<template>
    <section class="account-page" style="background-image: url('/bg-1.png'); background-repeat: no-repeat; background-size: cover">
        <div class="form-wrap">
            <div class="logo">
                <div class="image"><img src="../../../assets/img/logo-1.svg" alt=""></div>
            </div>
            <div class="content mb-26">
                <div class="title">Forgot Password? 🔑</div>
                <div class="desc">Enter your email and we'll send you instructions to reset your password.</div>
            </div>
            <form @submit.prevent="form.post('/forgot-password')">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" :class="{ 'is-invalid': form.errors.email }" type="email" placeholder="john.doe@gmail.com" v-model="form.email">
                    <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
                </div>
                <div class="btn-group mt-16">
                    <button type="submit" class="btn btn-purple w-full" :disabled="form.processing">Send Reset Link</button>
                </div>
                <Link class="back mt-20" :href="`/login`"><em class="fa-regular fa-angle-left"></em><span> Back to log in</span></Link>
            </form>
        </div>
    </section>
</template>

<script setup>
import {useForm, Link, usePage} from "@inertiajs/inertia-vue3";
import {computed, onBeforeUpdate} from "vue";
import {useToast} from "vue-toastification";
const toast = useToast();
const created = computed(() => usePage().props.value.flash.created);

const form = useForm({
    email: null,
});

onBeforeUpdate(() => {
    if (created.value) {
        toast.success(created.value, {
            icon: "fa-solid fa-check",
            timeout: 3000,
        });
    }
});
</script>
