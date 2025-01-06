<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">John Doe</div>
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
                    <li class="active"><span>{{ user.name }}</span></li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="text-18 font-medium flex justify-between gap-20 pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">User Information</div>
            <div class="flex mt-36 max-md:flex-col gap-[8rem] items-start">
                <table class="table-1-el w-full">
                    <tr>
                        <th>Name</th>
                        <td>{{ user.name }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ user.roles[0] ? user.roles[0].name : '---' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ user.email }}</td>
                    </tr>
                </table>
                <div class="w-full md:max-w-[20.6rem]">
                    <div class="image"><img :src="qr_code" alt="qr"></div>
                    <a class="mt-10 text-[#82868B] block text-[1.2rem]" href="javascript:void(0)" @click.prevent="downloadQrCode">Click to download QR</a>
                </div>
            </div>
            <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] mt-26"></span>
            <div class="btn-group flex flex-wrap gap-16 mt-[5.6rem]">
                <Link class="btn btn-purple" href="/users"><span class="icon fa-regular fa-chevron-left"></span><span>Back</span></Link>
                <Link class="btn btn-ligth btn-light--brounded" :href="`/users/${user.id}/edit`" v-if="permissions.includes('update-user')"><span>Edit</span></Link>
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
    qr_code: Object,
});

const downloadQrCode = () => {
    const link = document.createElement('a');
    link.download = `${props.user.name}_qr_code.png`;
    link.href = props.qr_code;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>
