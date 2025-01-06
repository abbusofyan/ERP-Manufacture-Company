<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="milestone" class="big-title">Edit Milestone</div>
            <div v-else class="big-title">Create New Milestone</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/milestones-settings">Milestones</Link>
                    </li>
                    <li>
                        <span v-if="milestone">Edit Milestone</span>
                        <span v-else>Create Milestone</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="milestone ? form.put(`/milestones-settings/${milestone.id}`) : form.post('/milestones-settings')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                                <span>Milestone Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Name<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Milestone Name">
                                    <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Duration (Days)<span class="required">*</span></label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.duration }" v-model="form.duration" placeholder="Duration">
                                    <div v-if="form.errors.duration" class="invalid-feedback d-block">{{ form.errors.duration }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="milestone" type="submit" :disabled="form.processing">Update Milestone</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create Milestone</button>
                        <Link class="btn btn-light btn-light--brounded" href="/milestones-settings">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";
import { onMounted } from "vue";

const props = defineProps({
    milestone: Object,
});

const form = useForm({
    name: null,
    duration: null,
});

onMounted(() => {
    if (props.milestone) {
        form.name = props.milestone.name;
        form.duration = props.milestone.duration;
    }
});
</script>
