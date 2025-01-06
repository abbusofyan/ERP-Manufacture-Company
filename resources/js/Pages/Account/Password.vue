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
                <li data-tab="tab1">
                    <span class="icon"><img class="svg" src="../../../assets/img/user.svg" alt="user"></span><span>Overview</span>
                </li>
                <li data-tab="tab2">
                    <span class="icon"><img class="svg" src="../../../assets/img/lock.svg" alt="security"></span><span>Security</span>
                </li>
            </ul>
            <div class="tab-container">
                <div class="tab2 tab-content d-block">
                    <div class="box pt-20 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                        <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Change Password</div>
                        <div class="form-wrap mt-24">
                            <form action="">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Current password</label>
                                        <input class="form-control" type="password" value="" required="">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input class="form-control" type="password" value="" required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm New Password</label>
                                        <input class="form-control" type="password" value="" required="">
                                    </div>
                                </div>
                                <div class="mt-20 font-semibold mb-10">Password requirements:</div>
                                <ul class="list-inside">
                                    <li class="mb-7 last:mb-0">Minimum 8 characters long - the more, the better</li>
                                    <li class="mb-7 last:mb-0">At least one lowercase character</li>
                                    <li class="mb-7 last:mb-0">At least one number, symbol, or whitespace character</li>
                                </ul>
                            </form>
                            <div class="btn-group mt-20"><a class="btn btn-purple" href="">Save Changes</a><a class="btn btn-light btn-light--brounded" href="#">Discard</a></div>
                        </div>
                    </div>
                </div>
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
