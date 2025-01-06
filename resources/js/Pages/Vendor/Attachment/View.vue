<template>
    <Layout>
        <Details :vendor="vendor"></Details>
        <div class="my-tabs mt-26">
            <Tab :vendor="vendor"></Tab>
            <div class="tab-container">
                <div class="tab1 tab-content d-block">
                    <div class="box rounded-md shadow-default bg-white">
                        <div class="flex flex-wrap gap-16 justify-between py-20 px-25">
                            <div class="text-18 font-medium">Attachments</div>
                            <div class="btn-group" v-if="permissions.includes('create-vendor')">
                                <div class="flex flex-wrap gap-16 justify-between">
                                    <div class="btn-group">
                                        <a class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#uploadAttachment" ref="openModal" href="javascript:void(0)">Upload Attachment <i class="fa fa-upload"></i></a>
                                    </div>

                                    <Modal :vendor="vendor"></Modal>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" v-if="attachments.data.length >= 1">
                                <thead>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Name</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>File URL</span>
                                        <span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span>
                                    </div>
                                </th>
                                <th><span>Actions</span></th>
                                </thead>
                                <tbody>
                                <tr v-for="attachment in attachments.data" :key="attachment.id">
                                    <td>
                                        {{ attachment.name }}
                                    </td>
                                    <td>
                                        <a :href="`/storage/${attachment.file_url}`" target="_blank">
                                            View File
                                        </a>
                                    </td>
                                    <td>
                                        <div class="el-actions">
                                            <a v-if="permissions.includes('delete-vendor')" class="text-red" @click="destroy(attachment.id)" href="javascript:void(0)">
                                                <em class="fa-regular fa-trash-can"></em>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="py-20 px-25" v-else>
                                Nothing to show.
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                            <p>Showing {{ attachments.from }} to {{ attachments.to }} of {{ attachments.total }} entries</p>
                            <Pagination :links="attachments.links"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
<script setup>
import Layout from "../../../Components/Layout.vue";
import Details from "../../Vendor/Detail.vue";
import {computed} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import Tab from "@/Pages/Vendor/Tab.vue";
import Modal from "@/Pages/Vendor/Attachment/UploadForm.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    vendor: Object,
    attachments: Object,
});

const destroy = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#009CDB",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Inertia.delete(`/vendors/${props.vendor.id}/attachments/${id}`, {
                preserveScroll: true,
            });
        }
    });
}

</script>
