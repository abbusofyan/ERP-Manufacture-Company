<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="assembly" class="big-title">Edit Assembly</div>
            <div v-else class="big-title">Create Assembly</div>
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
                        <span v-if="assembly">Edit Assembly</span>
                        <span v-else>Create Assembly</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="handleSubmit">
        <!-- <form @submit.prevent="assembly ? form.put(`/assembly/${assembly.id}`) : form.post('/assembly')"> -->
            <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>Product Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Type</label>
                                    <Select2
                                        v-model="form.type"
                                        class="select2-w-100"
                                        :class="{ 'is-invalid': form.errors.type }"
                                        placeholder="Select Item Type"
                                        :options="[{ id: 'Inventory', text: 'Inventory' }, { id: 'Non-Inventory', text: 'Non-Inventory' }]"
                                    />
                                    <div v-if="form.errors.type" class="invalid-feedback d-block">{{ form.errors.type }}</div>
                                </div>
                                <div class="form-group">
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Category<span class="required">*</span></label>
                                    <Select2 v-model="form.category_id" :class="{ 'is-invalid': form.errors.category_id }" :options="categories" placeholder="Select Category"/>
                                    <div v-if="form.errors.category_id" class="invalid-feedback d-block">{{ form.errors.category_id }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Case Pack</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.case_pack }" v-model="form.case_pack" placeholder="Case Pack" disabled>
                                    <div v-if="form.errors.case_pack" class="invalid-feedback d-block">{{ form.errors.case_pack }}</div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Item ID<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.code }" v-model="form.code" placeholder="Item ID">
                                    <div v-if="form.errors.code" class="invalid-feedback d-block">{{ form.errors.code }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Lead Time</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.lead_time }" v-model="form.lead_time" placeholder="Lead Time" disabled>
                                    <div v-if="form.errors.lead_time" class="invalid-feedback d-block">{{ form.errors.lead_time }}</div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Item Name<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Item Name">
                                    <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.description }" v-model="form.description" placeholder="Description">
                                    <div v-if="form.errors.description" class="invalid-feedback d-block">{{ form.errors.description }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>UOM</label>
                                    <Select2 v-model="form.uom_id" :class="{ 'is-invalid': form.errors.uom_id }" :options="uom" placeholder="Select UOM"/>
                                    <div v-if="form.errors.uom_id" class="invalid-feedback d-block">{{ form.errors.uom_id }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Expenses Acc</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.expense_acc }" v-model="form.expense_acc" placeholder="0">
                                    <div v-if="form.errors.expense_acc" class="invalid-feedback d-block">{{ form.errors.expense_acc }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Weight (Kg)</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.weight }" v-model="form.weight" placeholder="0">
                                    <div v-if="form.errors.weight" class="invalid-feedback d-block">{{ form.errors.weight }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Remark</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.remark }" v-model="form.remark">
                                    <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Dimension LxBxH</label>
                                    <div class="row">
                                        <div class="col">
                                            <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.dimension_l }" v-model="form.dimension_l" placeholder="L">
                                            <div v-if="form.errors.dimension_l" class="invalid-feedback d-block">{{ form.errors.dimension_l }}</div>
                                        </div>
                                        <div class="col">
                                            <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.dimension_w }" v-model="form.dimension_w" placeholder="B">
                                            <div v-if="form.errors.dimension_w" class="invalid-feedback d-block">{{ form.errors.dimension_w }}</div>
                                        </div>
                                        <div class="col">
                                            <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.dimension_h }" v-model="form.dimension_h" placeholder="H">
                                            <div v-if="form.errors.dimension_h" class="invalid-feedback d-block">{{ form.errors.dimension_h }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label>Location</label>
                                    <div class="row">
                                        <div class="col">
                                            <Select2
                                                v-if="!product"
                                                v-model="form.store_id"
                                                :class="{ 'is-invalid': form.errors.store_id }"
                                                placeholder="Select Warehouse"
                                                :settings="{
                                                    ajax: {
                                                        url: '/data/warehouse',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: function (params) {
                                                            return {
                                                                search: params.term,
                                                                _token: csrf,
                                                            };
                                                        },
                                                        processResults: function (data) {
                                                            return {
                                                                results: data.map(function (warehouse) {
                                                                    return {
                                                                            text: `${warehouse.name}`,
                                                                            id: warehouse.id,
                                                                            data: warehouse,
                                                                        };
                                                                    })
                                                                };
                                                            },
                                                        },
                                                    }"
                                            />
                                            <div v-if="form.errors.store_id" class="invalid-feedback d-block">
                                                {{ form.errors.store_id }}
                                            </div>
                                        </div>
                                        <div class="col">
                                            <Select2
                                            v-if="!product"
                                                v-model="form.location_id"
                                                :class="{ 'is-invalid': form.errors.location_id }"
                                                placeholder="Select Location"
                                                :settings="{
                                                    ajax: {
                                                        url: '/data/locations',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: function (params) {
                                                            return {
                                                                store_id: form.store_id,
                                                                search: params.term,
                                                                _token: csrf,
                                                            };
                                                        },
                                                        processResults: function (data) {
                                                            return {
                                                                results: data.map(function (location) {
                                                                    return {
                                                                            text: `${location.name}`,
                                                                            id: location.id,
                                                                            data: location,
                                                                        };
                                                                    })
                                                                };
                                                            },
                                                        },
                                                    }"
                                            />
                                            <div v-if="form.errors.location_id" class="invalid-feedback d-block">
                                                {{ form.errors.location_id }}
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Selling Price</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.selling_price }" v-model="form.selling_price" placeholder="0">
                                    <div v-if="form.errors.selling_price" class="invalid-feedback d-block">{{ form.errors.selling_price }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Landed Cost</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.landed_cost }" v-model="form.landed_cost" placeholder="0">
                                    <div v-if="form.errors.landed_cost" class="invalid-feedback d-block">{{ form.errors.landed_cost }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-3 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Lowest Cost</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.selling_price }" placeholder="0" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Highest Cost</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.landed_cost }" placeholder="0" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Last Cost</label>
                                    <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.landed_cost }" placeholder="0" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]"><span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">2</span><span>File Information</span></div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div>
                                    <div class="form-group">
                                        <label v-if="user && user.roles && user.roles.some(role => role.name === 'Sales')">Upload Item Photo (Sales)</label>
                                        <label v-else>Upload Item Photo (Non-sales)</label>
                                        <div class="fileContainer">
                                            <PhotoUpload @update:files="handleFilesUpdate" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group">
                                        <label>Part Book</label>
                                        <div class="fileContainer">
                                            <label class="fileInputLabel" for="fileInput2">
                                                <input class="fileInput" id="fileInput2"
                                                       type="file"
                                                       :class="{ 'is-invalid': form.errors.documents }"
                                                       @input="form.documents.push($event.target.files[0])"
                                                       accept="application/pdf">
                                                <span>Choose File</span>
                                                <span class="browse">Browse</span>
                                            </label>
                                            <div class="mt-12 text-[#82868B]">Allowed file types: pdf.</div>
                                            <div class="files">
                                                <div v-for="(document, index) in form.documents" :key="index" class="fileColumn">
                                                    <p class="name">
                                                        {{ document.name }}
                                                        <i class="icon-remove fa-regular fa-xmark cursor-pointer" @click="delDocument(index)"></i>
                                                    </p>
                                                </div>
                                                <div v-for="(document, index) in documents" :key="index" class="fileColumn">
                                                    <p class="name">
                                                        {{ document.name }}
                                                        <i class="icon-remove fa-regular fa-xmark cursor-pointer" @click="delExistDocument(index)"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <Tab :products="products" :csrf="csrf"></Tab>
            <div class="box py-20 px-25 mt-20 rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="btn-group">
                        <button
                            class="btn btn-purple"
                            type="button"
                            :disabled="form.processing"
                            @click="confirmSubmit"
                        >
                            Save Changes
                        </button>
                        <!-- <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button> -->
                        <Link class="btn btn-light btn-light--brounded" href="/assembly">Discard</Link>
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
import PhotoUpload from "../../Components/PhotoUpload.vue";
import Swal from "sweetalert2";

const props = defineProps({
    products: Array,
    assembly: Object,
    categories: Array,
    uom: Array,
    csrf: String,
});
 console.log(props.assembly.product);
const form = useForm({
    type: null,
    category_id: null,
    code: null,
    name: null,
    description: null,
    uom_id: null,
    weight: null,
    dimension_l: null,
    dimension_w: null,
    dimension_h: null,
    expense_acc: null,
    case_pack: null,
    lead_time: null,
    remark: null,
    status: 1,
    selling_price:null,
    landed_cost:null,
    documents: [],
    photos:[],
    delete_documents: [],
    processes:[],
    materials: []
});

const handleFilesUpdate = (files) => {
    form.photos = files;
    console.log(form.photos);
};

const delDocument = (index) => {
    form.documents.splice(index, 1)
};

const delPhoto = (index) => {
    form.photos.splice(index, 1)
};

const addPhoto = (event) => {
    const file = event.target.files[0];
    if (file) {
        const preview = URL.createObjectURL(file);  // Generate a preview URL
        form.photos.push({
            file: file,
            name: file.name,
            preview: preview
        });
    }
};

const delExistDocument = (index) => {
    form.delete_documents.push(props.documents[0].id)
    props.documents.splice(index, 1)
};

provide('form', form)

onMounted(() => {
    if (props.assembly) {
        form.type = props.assembly.product.type;
        form.code = props.assembly.code,
        form.name = props.assembly.product.name
        form.category_id = props.assembly.product.category_id
        form.uom_id = props.assembly.product.uom_id
        form.description = props.assembly.product.description;
        form.weight = props.assembly.product.weight;
        form.dimension_l = props.assembly.product.dimension_l;
        form.dimension_w = props.assembly.product.dimension_w;
        form.dimension_h = props.assembly.product.dimension_h;
        form.case_pack = props.assembly.product.case_pack;
        form.lead_time = props.assembly.product.lead_time;
        form.remark = props.assembly.product.remark;
        form.selling_price = props.assembly.product.selling_price;
        form.landed_cost = props.assembly.product.landed_cost;
        form.expense_acc = props.assembly.product.expense_acc;

        form.processes = props.assembly.processes.map(assembly_process => {
            return {
                'id': assembly_process.process_id,
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

const confirmSubmit = () => {
    if (form.materials.length == 0) {
        Swal.fire({
            title: "There are no BOM items added in this assembly. Do you want to proceed?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ea5455",
            cancelButtonColor: "#009CDB",
            confirmButtonText: "Yes, Proceed!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                handleSubmit()
            }
        });
    } else {
        handleSubmit()
    }
}

const handleSubmit = async () => {
    form.processing = true;
    try {
        if (props.assembly) {
        await form.put(`/assembly/${props.assembly.id}`);
    } else {
        await form.post('/assembly');
    }
    } catch (error) {
        console.error('Form submission error:', error);
    } finally {
        form.processing = false;
    }
};

</script>
