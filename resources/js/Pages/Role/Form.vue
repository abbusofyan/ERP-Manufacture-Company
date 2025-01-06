<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">{{ role ? 'Edit' : 'Create' }} Role</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/users">User Management</Link>
                    </li>
                    <li>
                        <Link href="/roles">Roles</Link>
                    </li>
                    <li class="active">
                        <Link href="#">{{ role ? 'Edit' : 'Create' }} Role</Link>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="handleSubmit" class="row g-3">
            <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">Permissions</div>
                <div class="div max-w-[50rem]">
                    <div class="form-group">
                        <label>Role Name<span class="required">*</span></label>
                        <input type="text" :class="{ 'is-invalid': form.errors.name }" v-model="form.name" placeholder="Role Name">
                        <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table no-borders">
                        <tr v-for="(permissions, module) in groupedPermissions" :key="module">
                            <td class="font-semibold !pl-0">{{ module }}</td>
                            <td v-for="permission in permissions" :key="permission">
                                <div class="custom-checkbox style-1">
                                    <input :id="permission" :value="permission" type="checkbox" v-model="form.permission_data">
                                    <label :for="permission">{{ getPermissionLabel(permission) }}</label>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="btn-group mt-[5.6rem]">
                    <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                    <a class="btn btn-light btn-light--brounded" href="javascript:void(0)" @click="toggleSelectAll">{{ form.permission_data.length === form.permissionOptions.length ? 'Unselect' : 'Select' }} All</a>
                    <Link class="btn btn-light btn-light--brounded" href="/roles">Discard</Link>
                </div>
                <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";
import { onMounted, computed } from "vue";

const props = defineProps({
    role: Object,
    rolePermissions: Array,
});

const form = useForm({
    name: null,
    permission_data: [],
    permissionOptions: [
        'view-any',

        'view-user',
        'create-user',
        'update-user',
        'delete-user',

        'view-customer',
        'view-customer-revenue',
        'create-customer',
        'update-customer',
        'delete-customer',

        'view-bank',
        'create-bank',
        'update-bank',
        'delete-bank',

        'view-discount',
        'create-discount',
        'update-discount',
        'delete-discount',

        'view-vendor',
        'create-vendor',
        'update-vendor',
        'delete-vendor',

        'view-requisition',
        'create-requisition',
        'update-requisition',
        'delete-requisition',
        'approve-requisition',

        'view-requisition_to_gin',
        'create-requisition_to_gin',
        'update-requisition_to_gin',
        'delete-requisition_to_gin',

        'view-requisition_to_order',
        'create-requisition_to_order',
        'update-requisition_to_order',
        'delete-requisition_to_order',

        'view-order',
        'create-order',
        'update-order',
        'delete-order',

        'view-store',
        'create-store',
        'update-store',
        'delete-store',

        'view-location',
        'create-location',
        'update-location',
        'delete-location',

        'view-product',
        'create-product',
        'update-product',
        'delete-product',

        'view-storage',
        'create-storage',
        'update-storage',
        'delete-storage',

        'view-stock',
        'create-stock',
        'update-stock',
        'delete-stock',

        'view-transfer',
        'create-transfer',
        'update-transfer',
        'delete-transfer',
        'approve-transfer',

        'view-uom',
        'create-uom',
        'update-uom',
        'delete-uom',

        'view-category',
        'create-category',
        'update-category',
        'delete-category',

        'view-service_appointment',
        'create-service_appointment',
        'update-service_appointment',
        'delete-service_appointment',

        'view-service_contract',
        'create-service_contract',
        'update-service_contract',
        'delete-service_contract',

        'view-service_invoice',
        'create-service_invoice',
        'update-service_invoice',
        'delete-service_invoice',

        'view-service_order',
        'create-service_order',
        'update-service_order',
        'delete-service_order',

        'view-service_order_attachment',
        'create-service_order_attachment',
        'update-service_order_attachment',
        'delete-service_order_attachment',

        'view-service_order_process',
        'create-service_order_process',
        'update-service_order_process',
        'delete-service_order_process',

        'view-service_order_report',
        'create-service_order_report',
        'update-service_order_report',
        'delete-service_order_report',

        'view-service_order_requirement',
        'create-service_order_requirement',
        'update-service_order_requirement',
        'delete-service_order_requirement',

        'view-service_order_requirement_used',
        'create-service_order_requirement_used',
        'update-service_order_requirement_used',
        'delete-service_order_requirement_used',

        'view-service_quotation',
        'create-service_quotation',
        'update-service_quotation',
        'delete-service_quotation',

        'view-service_schedule',
        'create-service_schedule',
        'update-service_schedule',
        'delete-service_schedule',

        'view-service_technician',
        'create-service_technician',
        'update-service_technician',
        'delete-service_technician',

        'view-project_appointment',
        'create-project_appointment',
        'update-project_appointment',
        'delete-project_appointment',

        'view-project_contract',
        'create-project_contract',
        'update-project_contract',
        'delete-project_contract',

        'view-project_invoice',
        'create-project_invoice',
        'update-project_invoice',
        'delete-project_invoice',

        'view-project_order',
        'create-project_order',
        'update-project_order',
        'delete-project_order',

        'view-project_order_attachment',
        'create-project_order_attachment',
        'update-project_order_attachment',
        'delete-project_order_attachment',

        'view-project_order_process',
        'create-project_order_process',
        'update-project_order_process',
        'delete-project_order_process',

        'view-project_order_report',
        'create-project_order_report',
        'update-project_order_report',
        'delete-project_order_report',

        'view-project_order_requirement',
        'create-project_order_requirement',
        'update-project_order_requirement',
        'delete-project_order_requirement',

        'view-project_order_requirement_used',
        'create-project_order_requirement_used',
        'update-project_order_requirement_used',
        'delete-project_order_requirement_used',

        'view-project_quotation',
        'create-project_quotation',
        'update-project_quotation',
        'delete-project_quotation',

        'view-project_schedule',
        'create-project_schedule',
        'update-project_schedule',
        'delete-project_schedule',

        'view-project_technician',
        'create-project_technician',
        'update-project_technician',
        'delete-project_technician',

        'view-vehicle',
        'create-vehicle',
        'update-vehicle',
        'delete-vehicle',

        'view-goods-receipt',
        'create-goods-receipt',
        'update-goods-receipt',
        'delete-goods-receipt',

        'view-gin',
        'create-gin',
        'update-gin',
        'delete-gin',

        'view-hygiene',
        'create-hygiene',
        'update-hygiene',
        'delete-hygiene',

        'view-quotation',
        'create-quotation',
        'update-quotation',
        'delete-quotation',

        'view-refrigeration_sale',
        'create-refrigeration_sale',
        'update-refrigeration_sale',
        'delete-refrigeration_sale',

        'view-sales_order',
        'create-sales_order',
        'update-sales_order',
        'delete-sales_order',

        'view-sales_order_eco',
        'create-sales_order_eco',
        'update-sales_order_eco',
        'delete-sales_order_eco',

        'view-assembly',
        'create-assembly',
        'update-assembly',
        'activate-assembly',
        'inactivate-assembly',

        'view-production_order',
        'create-production_order',
        'update-production_order',
        'delete-production_order',

        'view-production_process_detail',
        'create-production_process_detail',
        'update-production_process_detail',
        'delete-production_process_detail',

        'view-engineering_order',
        'create-engineering_order',
        'update-engineering_order',
        'delete-engineering_order',

        'view-engineering_process_detail',
        'create-engineering_process_detail',
        'update-engineering_process_detail',
        'delete-engineering_process_detail',

        'view-purchase_goods_return',
        'create-purchase_goods_return',
        'update-purchase_goods_return',
        'delete-purchase_goods_return',
    ],
});

const permissionLabels = {
    'view': 'View',
    'create': 'Create',
    'update': 'Update',
    'delete': 'Delete',
    'activate': 'Activate',
    'inactivate': 'Inactivate',
};

const groupedPermissions = computed(() => {
    const groups = {};
    form.permissionOptions.forEach(permission => {
        const [action, ...rest] = permission.split('-');
        const module = rest.join('-');
        if (!groups[module]) groups[module] = [];
        groups[module].push(permission);
    });
    return groups;
});

const getPermissionLabel = (permission) => {
    const [action] = permission.split('-');
    return permissionLabels[action] || action.charAt(0).toUpperCase() + action.slice(1);
};

const toggleSelectAll = () => {
    if (form.permission_data.length === form.permissionOptions.length) {
        form.permission_data = [];
    } else {
        form.permission_data = [...form.permissionOptions];
    }
};

const handleSubmit = () => {
    if (props.role) {
        form.put(`/roles/${props.role.id}`);
    } else {
        form.post('/roles');
    }
};

onMounted(() => {
    if (props.role) {
        form.name = props.role.name;
        form.permission_data = props.rolePermissions;
    }
});
</script>
