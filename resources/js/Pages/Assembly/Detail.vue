<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Assembly</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/assembly">Assembly</Link>
                    </li>
                    <li>
                        <span>Detail Assembly</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="assembly ? form.put(`/assembly/${assembly.id}`) : form.post('/assembly')">
            <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                    <span>Assembly Information</span>
                </div>
                <div class="row">
                    <div class="col-6">
                        <table class="table-1-el w-full">
                            <tr>
                                <th>ID</th>
                                <td>{{ assembly.code }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ assembly.product.name }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ assembly.product.category.name }}</td>
                            </tr>
                            <tr>
                                <th>UOM</th>
                                <td>{{ assembly.product.uom.code }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-6">
                        <div class="row mb-20">
                            <div class="col-6" v-if="usePage().props.value.auth.user.role.toLowerCase() != 'purchase'">
                                <div class="image-container float-end">
                                    <img v-if="assembly.product.sales_photos.length > 0" class="image" :src="'/' + assembly.product.sales_photos[0].path" alt="qr">
                                    <img v-else class="image" :src="'/images/no-image.png'">
                                </div>
                                <p class="text-center text-24 font-medium text-purple mb-10">Picture (sales)</p>
                            </div>
                            <div class="col-6">
                                <div class="image-container float-end"> <!-- Added float-end -->
                                    <img v-if="assembly.product.photos.length > 0" class="image" :src="'/' + assembly.product.photos[0].path" alt="qr">
                                    <img v-else class="image" :src="'/images/no-image.png'">
                                </div>
                                <p class="text-center text-24 font-medium text-purple mb-10">Picture (Non-sales)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <Tab :products="products"></Tab>
            <div class="box py-20 px-25 mt-20 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="btn-group">
                        <Link class="btn btn-purple" href="/assembly"><i class="fa fa-chevron-left text-11"></i>Back</Link>
                        <Link class="btn btn-light btn-light--brounded" :href="`/assembly/${assembly.id}/edit`">Edit</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link, usePage} from "@inertiajs/inertia-vue3";
import {onMounted, onBeforeMount, ref, provide} from "vue";
import Tab from "@/Pages/Assembly/Tab.vue";

const props = defineProps({
    products: Array,
    assembly: Object,
});

const form = useForm({
    name: null,
    category: null,
    uom:null,
    processes:[],
    materials: []
});

provide('form', form)

onMounted(() => {
    if (props.assembly) {
        form.name = props.assembly.name
        form.category = props.assembly.category
        form.uom = props.assembly.uom
        form.processes = props.assembly.processes.map(assembly_process => {
            return {
                'department': assembly_process.process.type,
                'name': assembly_process.process.name,
                'standard_time_hour': assembly_process.process.standard_time_hour,
                'standard_time_minute': assembly_process.process.standard_time_minute,
                'manpower': assembly_process.process.manpower,
            };
        });

        form.materials = props.assembly.materials.map(material => {
            return {
                'product': material.product,
                'qty': material.qty
            };
        });
    }
})

</script>
