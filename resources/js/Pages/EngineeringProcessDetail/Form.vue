<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="process" class="big-title">Edit Process </div>
            <div v-else class="big-title">New Process</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/engineering-order">Engineering</Link>
                    </li>
                    <li>
                        <Link href="/engineering-process-detail">Process Details</Link>
                    </li>
                    <li>
                        <span v-if="process">Edit Process</span>
                        <span v-else>Create New Process</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="process ? form.put(`/engineering-process-detail/${process.id}`) : form.post('/engineering-process-detail')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="grid md:grid-cols-2 gap-[7.7rem] box">
                                <div class="form-group">
                                    <label>Process Name</label>
                                    <input class="form-control" type="text" v-model="form.process_name" placeholder="Process name">
                                    <div v-if="form.errors.process_name" class="invalid-feedback d-block">{{ form.errors.process_name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Standard Time<span class="required">*</span></label>
                                    <div class="flex gap-2">
                                        <!-- Input for hours -->
                                        <input
                                            type="number"
                                            v-model="form.standard_time_hour"
                                            placeholder="Hour"
                                            class="form-control w-1/2"
                                            required
                                        />
                                        <!-- Input for minutes -->
                                        <input
                                            type="number"
                                            v-model="form.standard_time_minute"
                                            placeholder="Minute"
                                            min="0"
                                            max="59"
                                            class="form-control w-1/2"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem] box">
                                <div class="form-group">
                                </div>
                                <div class="form-group">
                                    <label>Manpower<span class="required">*</span></label>
                                    <input
                                        id="manpower"
                                        type="number"
                                        v-model="form.manpower"
                                        required
                                    />
                                    <div v-if="form.errors.manpower" class="invalid-feedback d-block">{{ form.errors.manpower }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]" v-for="(group, groupIndex) in form.groups" :key="groupIndex">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">{{ groupIndex + 1 }}</span>
                                <span>Process Detail Group</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Process Group Name</label>
                                    <div class="d-flex gap-1">
                                        <input class="form-control" type="text" v-model="group.group_name" placeholder="Process Group Name">
                                    </div>
                                    <p class="text-warning">Leave blank to disable</p>
                                    <div v-if="form.errors[`groups.${groupIndex}.group_name`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.group_name`] }}</div>
                                </div>
                            </div>
                            <div v-for="(item, itemIndex) in group.items" :key="itemIndex" class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Process Details</label>
                                    <input class="form-control" type="text" v-model="item.question_name" placeholder="Process Details">
                                    <div v-if="form.errors[`groups.${groupIndex}.items.${itemIndex}.question_name`]" class="invalid-feedback d-block">{{ form.errors[`groups.${groupIndex}.items.${itemIndex}.question_name`] }}</div>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="form-group col-lg-6">
                                            <label>Response Type<span class="required">*</span></label>
                                            <Select2
                                                v-model="item.form_type"
                                                :class="{ 'col-lg-4 is-invalid': form.errors[`groups.${groupIndex}.items.${itemIndex}.form_type`] }"
                                                :options="[{ id: 'Text Field', text: 'Text Field' }, { id: 'Checkbox', text: 'Checkbox' }]"
                                                placeholder="Select Type"
                                                required
                                                style="width: 100%;">
                                            </Select2>
                                            <div v-if="form.errors[`groups.${groupIndex}.items.${itemIndex}.form_type`]" class="invalid-feedback">{{ form.errors[`groups.${groupIndex}.items.${itemIndex}.form_type`] }}</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-danger btn-danger--brounded d-flex align-items-center" type="button" @click="deleteItem(groupIndex, itemIndex)">
                                                <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-purple gap-[7.7rem]" type="button" @click="addItem(groupIndex)">
                                <span class="icon"><img src="../../../assets/img/add.svg" alt="add"></span>
                                Add Detail</button>
                                <!-- <button class="btn btn-danger" type="button" @click="deleteGroup(groupIndex)">
                                    <span class="icon"><img src="../../../assets/img/delete.svg" alt="delete"></span>
                                    Delete Group</button> -->
                        </div>
                    </div>
                    <button class="btn btn-purple col-12 btn-block btn-purple--brounded" type="button" @click="addGroup">
                        <span class="icon"><img src="../../../assets/img/add.svg" alt="add"></span>
                        Add Process Group</button>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" @click="form.status = 1" :disabled="form.processing">Save Changes</button>
                        <a class="btn btn-light btn-light--brounded" href="javascript:void(0)" @click="discard">Cancel</a>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {useForm, usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../Components/Layout.vue';
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";
import {onMounted} from "vue";

const props = defineProps({
    process: Object,
});

const form = useForm({
    process_name: '',
    standard_time_hour: '',
    standard_time_minute: '',
    manpower: '',
    groups: [
        {
            group_name: '',
            items: [
                {
                    question_name: '',
                    form_type: ''
                }
            ]
        }
    ],
    errors: {}
});

const addItem = (groupIndex) => {
    form.groups[groupIndex].items.push({
        question_name: '',
        form_type: ''
    });
};

const addGroup = () => {
    form.groups.push({
        group_name: '',
        items: [
            {
                question_name: '',
                form_type: ''
            }
        ]
    });
};

const deleteItem = (groupIndex, itemIndex) => {
    form.groups[groupIndex].items.splice(itemIndex, 1);
};

const deleteGroup = (groupIndex) => {
    form.groups.splice(groupIndex, 1);
};


const discard = () => {
    form.reset();
};

onMounted(() => {
    if (props.process) {
        form.process_name = props.process.name;
        form.standard_time_hour = props.process.standard_time_hour;
        form.standard_time_minute = props.process.standard_time_minute;
        form.manpower = props.process.manpower;

        const groupedData = props.process.detail.reduce((acc, item) => {
            if (!acc[item.group_name]) {
                acc[item.group_name] = [];
            }
            acc[item.group_name].push({
                question_name: item.question,
                form_type: item.form_type
            });
            return acc;
        }, {});

        form.groups = Object.keys(groupedData).map(groupName => {
            return {
                group_name: groupName != 'null' ? groupName : '',
                items: groupedData[groupName]
            };
        });
    }
});
</script>
