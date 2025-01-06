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
                <table class="table-1-el w-full">
                    <tr>
                        <th>Name</th>
                        <td>{{ assembly.name }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ assembly.category }}</td>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <td>{{ assembly.code }}</td>
                    </tr>
                </table>
            </div>
            <Tab :products="products"></Tab>
            <div class="box py-20 px-25 mt-20 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="btn-group">
                        <Link class="btn btn-purple" href="/assembly"><i class="fa fa-left"></i>Back</Link>
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
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted, onBeforeMount, ref, provide} from "vue";
import Tab from "@/Pages/Assembly/Tab.vue";

const props = defineProps({
    products: Array,
    assembly: Object,
});

const form = useForm({
    name: null,
    category: null,
    requirements:[]
});

provide('form', form)

onMounted(() => {
    if (props.assembly) {
        form.name = props.assembly.name
        form.category = props.assembly.category
        form.requirements = props.assembly.requirements.map(requirement => {
            return {
                'qty': requirement.qty,
                'product': requirement.product
            };
        });
    }
})

</script>
