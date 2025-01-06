<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="poc" class="big-title">Edit Address</div>
            <div v-else class="big-title">New Address</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/vendors">Vendors</Link>
                    </li>
                    <li>
                        <Link :href="`/vendors/${vendor.id}`">{{ vendor.name }}</Link>
                    </li>
                    <li>
                        <span v-if="poc">Edit Address</span>
                        <span v-else>New Address</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="address ? form.put(`/vendors/${vendor.id}/addresses/${address.id}`) : form.post(`/vendors/${vendor.id}/addresses`)">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span v-if="address">Edit Address</span>
                                <span v-else>New Address</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Address<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.address }" v-model="form.address" placeholder="Address">
                                    <div v-if="form.errors.address" class="invalid-feedback d-block">{{ form.errors.address }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Building Name</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.building_name }" v-model="form.building_name" placeholder="Building Name">
                                    <div v-if="form.errors.building_name" class="invalid-feedback d-block">{{ form.errors.building_name }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Postal Code<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.postal_code }" v-model="form.postal_code" placeholder="Postal Code">
                                    <div v-if="form.errors.postal_code" class="invalid-feedback d-block">{{ form.errors.postal_code }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Unit No</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.unit_no }" v-model="form.unit_no" placeholder="Unit No">
                                    <div v-if="form.errors.unit_no" class="invalid-feedback d-block">{{ form.errors.unit_no }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="address" type="submit" :disabled="form.processing">Update Address</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Save Change</button>
                        <Link class="btn btn-light btn-light--brounded" :href="`/vendors/${vendor.id}/addresses`">Discard</Link>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted} from "vue";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    address: Object,
    vendor: Object,
});

const form = useForm({
    address: null,
    unit_no: null,
    building_name: null,
    postal_code: null,
});

onMounted(() => {
    if (props.address) {
        form.address = props.address.address
        form.unit_no = props.address.unit_no;
        form.building_name = props.address.building_name;
        form.postal_code = props.address.postal_code;
    }
});
</script>
