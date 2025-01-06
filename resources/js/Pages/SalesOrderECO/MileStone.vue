<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Sales Order (ECO)</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/sales">Refrigeration Sales</Link>
                    </li>
                    <li>
                        <span>Sales Order (ECO)</span>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="form.post(`/${shipment}/sales-order-eco/${order.id}/edit-milestone`)">
            <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white">
                <div class="boxes">
                    <div class="mb-[2.6rem]">
                        <div class="text-18 font-medium pb-17 mb-[2.6rem] border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <span>Milestones Info</span>
                        </div>

                        <div class="form-group d-flex align-items-center gap-[3rem] mb-[2.6rem]">
                            <label class="text-nowrap">Delivery Date</label>
                            <VueDatePicker v-model="form.delivery_date" :class="{ 'is-invalid': form.errors.delivery_date }" :format="format" :enable-time-picker="false" placeholder="XX-XX-XXXX"></VueDatePicker>
                            <div v-if="form.errors.delivery_date" class="invalid-feedback d-block">{{ form.errors.delivery_date }}</div>
                        </div>

                        <div class="row">
                            <div class="col-lg-7 mb-10">
                                Check The Necessary Stages
                            </div>
                            <div class="col-lg-5 mb-10">
                                Calculated Due Date
                            </div>
                        </div>

                        <!-- Draggable Component -->
                        <draggable v-model="form.items" :handle="'.drag-handle'" item-key="id">
                            <template #item="{ element: item, index }">
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <div class="flex-fill mb-16">
                                        <div class="d-flex align-items-center text-18 font-medium">
                                            <!-- Drag Handle -->
                                            <span class="drag-handle mr-10 cursor-move">
                                                <img class="cursor-pointer" height="15" width="15" src="../../../assets/img/all-directions.svg" alt="drag" />
                                            </span>
                                            <div class="custom-checkbox style-1 mr-10">
                                                <input :id="`item-${index}`" class="form-check" type="checkbox" :class="{ 'is-invalid': form.errors[`items.${index}.active`] }" v-model="item.active">
                                                <label :for="`item-${index}`"></label>
                                                <div v-if="form.errors[`items.${index}.active`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.active`] }}</div>
                                            </div>
                                            <div class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-10 border-solid text-15 text-primary">
                                                {{ index + 1 }}
                                            </div>
                                            <div class="flex-fill pl-15">
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors[`items.${index}.stage`] }" v-model="item.stage" placeholder="Type here">
                                                <div v-if="form.errors[`items.${index}.stage`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.stage`] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-fill mb-10">
                                        <VueDatePicker v-model="item.calculated_due_date" :class="{ 'is-invalid': form.errors[`items.${index}.calculated_due_date`] }" :format="format" :enable-time-picker="false" placeholder="XX-XX-XXXX"></VueDatePicker>
                                        <div v-if="form.errors[`items.${index}.calculated_due_date`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.calculated_due_date`] }}</div>
                                    </div>
                                    <div class="flex-fill mb-10">
                                        <select class="form-select" v-model="item.status">
                                            <option v-for="(value, key) in status" :value="key" :key="key">{{ value }}</option>
                                        </select>
                                        <div v-if="form.errors[`items.${index}.status`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.status`] }}</div>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                        <!-- End of Draggable Component -->

                        <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mt-[2.6rem]"></div>
                        <div class="btn-group mt-[5.6rem]">
                            <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                            <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/sales-order-eco/create?rs_id=${order.refrigeration_sale.id}`">Discard</Link>
                        </div>
                        <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                    </div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted} from "vue";
import draggable from "vuedraggable"; // Import the draggable component

const props = defineProps({
    shipment: Object,
    order: Object,
    mileStone: Object,
    status: Array,
});

const form = useForm({
    id: null,
    delivery_date: null,
    items: [],
});

const itemObj = () => {
    return {
        id: null,
        stage: null,
        calculated_due_date: null,
        active: false,
        status: null,
    };
};

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
};

onMounted(() => {
    if (props.mileStone) {
        form.delivery_date = props.mileStone.delivery_date;
        form.id = props.mileStone.id;

        form.items = props.mileStone.items;

        for (let i = form.items.length; i < 6; i++) {
            form.items.push(itemObj());
        }
    }
});
</script>
