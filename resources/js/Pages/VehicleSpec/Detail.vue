<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Vehicle Specification</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/vehicle-spec">Vehicle Specification</Link>
                    </li>
                    <li>
                        <span>Detail Vehicle Specification</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="spec ? form.put(`/vehicle-spec/${spec.id}`) : form.post('/vehicle-spec')">
            <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-md-4 mb-6">
                                <p class="text-primary">Name</p>
                            </div>
                            <div class="col-md-6 mb-10">
                                <p>{{ form.name ?? '--' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-2" v-if="form.tab === 1">
                <div class="box rounded-md shadow-default mt-20 bg-white">
                    <div class="box pt-20 px-25 rounded-md shadow-default bg-white">
                        <template v-for="(header, headerIndex) in props.headers">
                            <template v-if="header.items.length > 0">
                                <div class="text-bold mb-10">{{header.name}}</div>
                                <div class="row" v-for="(item, ItemIndex) in header.items">
                                    <div class="col-lg-12">
                                        <div class="row mb-20">
                                            <div class="col-md-4 mb-6">
                                                <p class="text-primary">Item Name</p>
                                            </div>
                                            <div class="col-md-6 mb-10">
                                                <p v-if="item.product_id">{{ item.material.name }}</p>
                                                <p v-if="item.assembly_id">{{ item.assembly.name }}</p>
                                            </div>

                                            <div class="col-md-4 mb-6">
                                                <p class="text-primary">Quantity</p>
                                            </div>
                                            <div class="col-md-6 mb-10">
                                                <p>{{ item.qty ?? '--' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
            <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
            <div class="box py-20 px-25 mt-20 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="btn-group">
                        <Link class="btn btn-purple" :href="`/vehicle-spec`">Back</Link>
                        <Link class="btn btn-light btn-light--brounded" :href="`/vehicle-spec/${spec.id}/edit`">Edit</Link>
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

const props = defineProps({
    spec: Object,
    headers: Object,
    csrf: String,
});

const group_headers = [
    'FREEZER BOX',
    'FREEZER DOOR',
    'REFRIGERATION UNIT',
    'FLOORING',
    'SIDEWALL',
    'ELECTRICAL STANDBY',
    'OTHER ACCESSORIES',
];

const form = useForm({
    tab: 1,
    name: null,
    category: null,
    uom: null,
    vehicle_license_plate: null,
    vehicle_chassis_no: null,
    vehicle_voltage: null,
    vehicle_make: null,
    vehicle_model: null,
    vehicle_class: null,
    vehicle_type_of_plate: null,
    vehicle_mileage: null,
    vehicle_running_hours: null,
    groups: group_headers.map(header => ({
        group_name: `${header}`,
        detail: {
            type: null,
            item_name: null,
            item_id: null,
            qty: null,
            foc: null,
            price: null,
            discount: null
        }
    })),
});

onMounted(() => {
    if (props.spec) {
        form.name = props.spec.name;
        form.category = props.spec.category;
        form.uom = props.spec.uom;
        form.vehicle_license_plate = props.spec.vehicle_license_plate;
        form.vehicle_chassis_no = props.spec.vehicle_chassis_no;
        form.vehicle_voltage = props.spec.vehicle_voltage;
        form.vehicle_make = props.spec.vehicle_make;
        form.vehicle_model = props.spec.vehicle_model;
        form.vehicle_class = props.spec.vehicle_class;
        form.vehicle_type_of_plate = props.spec.vehicle_type_of_plate;
        form.vehicle_mileage = props.spec.vehicle_mileage;
        form.vehicle_running_hours = props.spec.vehicle_running_hours;
        if (props.spec.items && Array.isArray(props.spec.items)) {
            form.groups = form.groups.map((group, index) => {
                const specGroup = props.spec.items[index];
                if (specGroup) {
                    let type = specGroup.product_id ? 'Single Item' : 'Assembly Item';
                    let item_id = specGroup.product_id ? specGroup.product_id : specGroup.assembly_id
                    return {
                        ...group,
                        detail: {
                            type: type,
                            item_id: item_id,
                            qty: specGroup.qty || group.detail.qty,
                            foc: specGroup.foc ? true : false,
                            price: specGroup.price || group.detail.price,
                            discount: specGroup.discount || group.detail.discount,
                        }
                    };
                }
                return group;
            });
        }
    }
});

const addOther = () => {
    form.groups.push({
        group_name: "OTHER ACCESSORIES",
        detail: {
            type: null,
            item_name: null,
            item_id: null,
            qty: null,
            foc: null,
            price: null,
            discount: null
        }
    })
}

</script>
