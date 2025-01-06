<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ projectOrder.code }}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"/></span></Link>
                    </li>
                    <li>
                        <Link href="/project-orders">Project Order</Link>
                    </li>
                    <li>
                        <Link href="/project-orders">Active Project / Repair Order</Link>
                    </li>
                    <li>
                        <span>{{ projectOrder.code }}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <div class="boxes">

                    <ProcessTab :projectOrder="projectOrder" :stages="stages"></ProcessTab>

                    <!-- Status and Actions -->
                    <div class="grid md:grid-cols-2 gap-[7.7rem] mb-[2.6rem]">
                        <table class="table-1-el w-full">
                            <tr>
                                <th class="text-nowrap">Current Status</th>
                                <td class="pl-15">
                                    {{ projectOrder.current_process ? `${projectOrder.current_process.status_text}/${projectOrder.current_process.name}` : '--' }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">In Warranty</th>
                                <td class="pl-15">
                                    <div v-if="projectOrder.status != 7" class="d-flex gap-26">
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.in_warranty" type="radio" value="1" id="in-warranty-yes" @click="updateDetail">
                                            <label for="in-warranty-yes">Yes</label>
                                        </div>
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.in_warranty" type="radio" value="0" id="in-warranty-no" @click="updateDetail">
                                            <label for="in-warranty-no">No</label>
                                        </div>
                                    </div>
                                    <div v-else>
                                        {{ form.in_warranty == 0 ? 'No' : 'Yes' }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Come Back Job</th>
                                <td class="pl-15">
                                    <div v-if="projectOrder.status != 7" class="d-flex gap-26">
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.come_back_job" type="radio" value="1" id="comeback-job-yes" @click="updateDetail">
                                            <label for="comeback-job-yes">Yes</label>
                                        </div>
                                        <div class="custom-checkbox style-2">
                                            <input v-model="form.come_back_job" type="radio" value="0" id="comeback-job-no" @click="updateDetail">
                                            <label for="comeback-job-no">No</label>
                                        </div>
                                    </div>
                                    <div v-else>
                                        {{ form.come_back_job == 0 ? 'No' : 'Yes' }}
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!--Process Button-->
                        <ButtonGroup :projectOrder="projectOrder"></ButtonGroup>
                        <!--Process Button-->

                    </div>

                    <ProcessForm :projectOrder="projectOrder"></ProcessForm>

                    <!-- Order Details -->
                    <Detail :csrf="csrf" :projectOrder="projectOrder"></Detail>
                </div>
            </div>
        </div>


        <!--Tab-->
        <Tab :projectOrder="projectOrder" :tab="tab"></Tab>
        <slot/>

    </Layout>
</template>

<script setup>
import {Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Tab from "./Tab.vue";
import ProcessTab from "./Process/Tab.vue";
import Detail from "./Detail.vue";
import ButtonGroup from "./Process/ButtonGroup.vue";
import ProcessForm from "./Process/ProcessForm.vue";
import debounce from "lodash.debounce";
import Index from "@/Pages/ProjectOrder/Requirement/Index.vue";

const props = defineProps({
    csrf: String,
    tab: Object,
    projectOrder: Object,
    filters: Object,
    processes: Object,
    stages: Object,
});

const form = useForm({
    come_back_job: 0,
    in_warranty: 0,
});

if(props.projectOrder){
    form.come_back_job = props.projectOrder.come_back_job;
    form.in_warranty = props.projectOrder.in_warranty;
}

const updateDetail = debounce(() => {
    form.post(`/project-orders/${props.projectOrder.id}/update-detail`)
}, 500)
</script>
