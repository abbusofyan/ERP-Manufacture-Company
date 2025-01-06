<template>
    <header class="header sticky z-99 top-0">
        <div class="toggle-btn"><img src="../../assets/img/clear-all.svg" alt=""></div>
        <div class="account">
            <div class="account-toggle">
                <div class="account-info">
                    <div class="name">{{ username }}</div>
                    <div class="role">{{ role }}</div>
                </div>
                <div class="avatar">
                    <div class="img">
                        <img v-if="!avatar" src="../../assets/img/avatar.png" alt="Avatar">
                        <img v-else :src="`/storage/${avatar}`" alt="Avatar">
                    </div>
                    <div class="ping"></div>
                </div>
            </div>
            <ul class="account-panel">
                <li>
                    <Link href="/account">View profile</Link>
                </li>
                <li>
                    <Link href="/account/profile/edit">Edit profile</Link>
                </li>
                <li>
                    <Link href="/account/password/change">Change password</Link>
                </li>
                <li>
                    <Link href="/logout" method="post" as="button">Log out</Link>
                </li>
            </ul>
        </div>
    </header>
</template>

<script setup>
import {computed} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";

const username = computed(() => usePage().props.value.auth.user.username);
const avatar = computed(() => usePage().props.value.auth.user.avatar);
const role = computed(() => usePage().props.value.auth.user.role);

const expand = () => {
    document.body.classList.add("layout-menu-collapsed");
    document.body.classList.add("layout-menu-expanded");
};
</script>
