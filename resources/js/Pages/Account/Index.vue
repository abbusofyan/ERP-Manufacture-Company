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
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">User Information</div>
            <div class="grid sm:grid-cols-2 gap-5 items-start">
                <div class="info-el">
                    <div class="avatar">
                        <img v-if="!user.avatar" src="../../../assets/img/avatar.png" alt="Avatar">
                        <img v-else :src="`/storage/${user.avatar}`" alt="Avatar">
                    </div>
                    <div class="content">
                        <div class="name">{{ user.name }}</div>
                        <div class="email">{{ user.email }}</div>
                        <div v-if="user.roles" class="role-tag">{{ user.roles[0].name }}</div>
                        <div class="btn-group mt-16">
                            <Link class="btn btn-purple" href="/account/profile/edit">Edit Profile</Link>
                        </div>
                    </div>
                </div>
                <table class="table-info">
                    <tr>
                        <th>
                            <div class="flex items-center">
                                <span class="icon"><img src="../../../assets/img/user-purple.svg" alt="Username"></span>
                                <span class="pl-10">Username</span>
                            </div>
                        </th>
                        <td>{{ user.username }}</td>
                    </tr>
                    <tr>
                        <th>
                            <div class="flex items-center">
                                <span class="icon"><img src="../../../assets/img/email-purple.svg" alt="Email"></span>
                                <span class="pl-10">Email</span>
                            </div>
                        </th>
                        <td>{{ user.email }}</td>
                    </tr>
                    <tr v-if="user.roles">
                        <th>
                            <div class="flex items-center">
                                <span class="icon"><img src="../../../assets/img/star-purple.svg" alt="Role"></span>
                                <span class="pl-10">Role</span>
                            </div>
                        </th>
                        <td>{{ user.roles[0].name }}</td>
                    </tr>
                    <tr>
                        <th>
                            <div class="flex items-center">
                                <span class="icon"><img src="../../../assets/img/phone-purple.svg" alt="Contact"></span>
                                <span class="pl-10">Contact</span>
                            </div>
                        </th>
                        <td>{{ user.phone }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {computed} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    user: Object,
});
</script>
