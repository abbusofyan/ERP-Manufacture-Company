<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{order.production_order.code}}</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/engineering-order">Engineering Order</Link>
                    </li>
                    <li>
                        <span>{{order.production_order.code}}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="assembly ? form.put(`/assembly/${order.id}`) : form.post('/assembly')">
            <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                    <span>Engineering Order Detail</span>
                </div>
                <div class="row">
                    <div class="col">
                        <table class="table-1-el w-full">
                            <tbody>
                                <tr>
                                    <th class="text-nowrap">Quotation No</th>
                                    <td class="pl-15"> : {{order.production_order.quotation.code}}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Engineering Order No</th>
                                    <td class="pl-15"> : {{ order.production_order.code }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Confirmed By</th>
                                    <td class="pl-15"> : Test User</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Category</th>
                                    <td class="pl-15"> : {{ order.production_order.category }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <table class="table-1-el w-full">
                            <tbody>
                                <tr>
                                    <th class="text-nowrap">Planned Start Date</th>
                                    <td class="pl-15"> : {{order.start_date}}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Planned Completed Date</th>
                                    <td class="pl-15"> : {{ order.completion_date }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Delivery Date</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <Tab :materials="materials" :products="products" :order="order"></Tab>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import {onMounted, ref, provide, computed} from "vue";
import Tab from "@/Pages/EngineeringOrder/Tab.vue";
import {useToast} from "vue-toastification";

const toast = useToast();

const updated = computed(() => {
    usePage().props.value.flash.updated
});

const props = defineProps({
    order: Object,
    products: Object,
    materials: {
        type: Object,
        default: {}
    },
});

const form = useForm({
    name: null,
    category: null,
    uom:null,
    requirements: [],
    materials: [],
    items_used: [],
    processes:[],
    summaries:[],
    attachments: []
});

provide('form', form)

onMounted(() => {
    if (props.order) {
        let specs = props.order.production_order.quotation.specs
        form.requirements = specs.map(spec => {
            return {
                'code': spec.detail_spec.code,
                'name': spec.detail_spec.name,
                'category': spec.detail_spec.category,
                'uom': spec.detail_spec.uom,
            };
        });

        form.items_used = props.order.items.map(item => {
            if (!item.product) {
                return {}
            }
            return {
                'product_id': item.product_id,
                'sku': item.product.sku,
                'name': item.product.name,
                'uom': item.product.uom.code,
                'planned_qty': item.planned_qty,
            };
        });

        form.processes = props.order.production_order.processes.map(process => {
            return {
                'id': process.id,
                'department': process.department,
                'name': process.name,
                'standard_time_hour': process.standard_time_hour,
                'standard_time_minute': process.standard_time_minute,
                'manpower': process.manpower,
                'started_at': process.started_at,
                'ended_at': process.ended_at,
                'total_time': process.total_time,
                'overtime': process.overtime,
                'efficiency': process.efficiency,
                'process_id': process.id,
                'details': process.detail
            };
        });

        form.summaries = props.order.production_order.process_timelogs
        .filter(log => log.production_order_process.department === 'Engineering') // Filter the logs
        .map(log => {
            return {
                'id': log.id,
                'technician_name': log.user.name,
                'start_time': log.start_time,
                'end_time': log.end_time,
                'process': log.production_order_process.name,
            };
        });

        form.attachments = props.order.attachments.map(attachment => {
            return {
                'id': attachment.id,
                'name': attachment.name,
                'created_at': attachment.created_at,
            };
        });
    }
})

</script>
