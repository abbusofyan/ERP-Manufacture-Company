<template>
    <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] pb-24 mb-[2.6rem]">
        <div class="row">
            <div v-for="(tab, index) in stages" class="col-lg-3">
                <div class="progress-item">
                    <div class="d-flex gap-16">
                        <div class="icon" :class="{'active':Number(index) === Number(projectOrder.stage)}" v-html="tab.icon"></div>
                        <div class="flex-fill">
                            <div class="text-15 text-bold">
                                {{ tab.title }}
                            </div>
                            <template v-if="Number(index) > Number(projectOrder.stage)">
                                <div class="el-tag gray">
                                    Pending
                                </div>
                            </template>
                            <template v-else>
                                <template v-if="Number(index) === Number(projectOrder.stage)">
                                    <div v-if="projectOrder.current_process" class="el-tag" :class="projectOrder.current_process.status_class">
                                        {{ projectOrder.current_process.status_text }}
                                    </div>
                                    <div v-else-if="Number(projectOrder.stage) === 4" class="el-tag green">
                                        Completed
                                    </div>
                                    <div v-else class="el-tag gray">
                                        Pending
                                    </div>
                                </template>
                                <div v-else class="el-tag green">
                                    Completed
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";

const props = defineProps({
    stages: Object,
    projectOrder: Object,
});
</script>
