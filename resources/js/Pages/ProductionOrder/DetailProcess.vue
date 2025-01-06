<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Process Detail</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/production-order">Production Order</Link>
                    </li>
                    <li>
                        <span>{{props.productionOrder.code}}</span>
                    </li>
                    <li>
                        <span>Detail Process</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="process ? form.post(`/production-order/detail-process/${productionOrder.id}/${process.id}`) : form.post('/production-process-detail')">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-20" v-for="(group, groupIndex) in form.groups" :key="groupIndex">
                            <h3>{{group.group_name}}</h3><br>
                            <div v-for="(item, itemIndex) in group.items" :key="itemIndex" class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div v-if="item.form_type == 'Text Field'" class="form-group">
                                            <label>{{item.question_name}} : </label>
                                            <input class="form-control" type="text" v-model="item.value" placeholder="Value">
                                        </div>
                                        <div v-if="item.form_type == 'Checkbox'" class="form-group custom-checkbox style-1">
                                            <input type="checkbox" v-model="item.value" :id="'checkbox'+itemIndex" :checked="item.form_type == 'Checkbox' && item.value == 1">
                                            <label :for="'checkbox'+itemIndex">{{item.question_name}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-20">
                        <Link class="btn btn-purple" :href="`/production-order/${props.productionOrder.id}`"><i class="fa fa-chevron-left text-11"></i>Back</Link>
                        <button class="btn btn-purple" type="submit" @click="form.status = 1" :disabled="form.processing">Save Changes</button>
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
    productionOrder: Object,
    productionOrderProcessDetail: Object
});

const form = useForm({
    groups: [
        {
            group_name: '',
            items: [
                {
                    question_name: '',
                    form_type: '',
                    value: ''
                }
            ]
        }
    ],
    errors: {}
});

const discard = () => {
    form.reset();
};

onMounted(() => {
    if (props.process) {
        const groupedData = props.process.detail.reduce((acc, item) => {
            console.log(item);
            if (!acc[item.group_name]) {
                acc[item.group_name] = [];
            }

            acc[item.group_name].push({
                process_detail_id: item.id,
                process_id: item.process_id,
                question_name: item.question,
                form_type: item.form_type,
                value: item.value
            });
            return acc;
        }, {});

        form.groups = Object.keys(groupedData).map(groupName => {
            return {
                group_name: groupName != 'null' ? groupName : '',
                items: groupedData[groupName],
            };
        });
    }
});
</script>
