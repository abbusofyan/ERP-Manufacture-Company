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
                        <span>Assembly</span>
                    </li>
                    <li>
                        <span>Replace Item ID</span>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="form.post('/assembly/replace-item')">
            <div class="box pt-26 px-24 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1] flex justify-between gap-3 items-center">
                    <span>Replace Item</span>
                    <button class="btn btn-purple" type="submit" :disabled="form.processing">Save Changes</button>
                </div>

                <div class="grid md:grid-cols-2 gap-[3rem]">
                    <div class="form-group">
                        <h3 class="mb-20">Existing Item</h3>
                        <label>Category<span class="required">*</span></label>
                        <Select2
                            v-model="form.existing_item_category_id"
                            placeholder="Type Name"
                            :settings="{
                            ajax: {
                                url: '/data/categories',
                                dataType: 'json',
                                method: 'POST',
                                data: function (params) {
                                  return {
                                    search: params.term,
                                    _token: csrf,
                                  };
                                },
                                processResults: function (data, params) {
                                    return {
                                        results: data.map(function (item) {
                                            return {
                                                text: `${item.prefix} | ${item.name}`,
                                                id: item.id,
                                                data: item,
                                            };
                                        })
                                    };
                                }
                            }
                        }"
                        />
                    </div>
                    <div class="form-group">
                        <h3 class="mb-20">New Item</h3>
                        <label>Category<span class="required">*</span></label>
                        <Select2
                            v-model="form.new_item_category_id"
                            placeholder="Type Name"
                            :settings="{
                            ajax: {
                                url: '/data/categories',
                                dataType: 'json',
                                method: 'POST',
                                data: function (params) {
                                  return {
                                    search: params.term,
                                    _token: csrf,
                                  };
                                },
                                processResults: function (data, params) {
                                    return {
                                        results: data.map(function (item) {
                                            return {
                                                text: `${item.prefix} | ${item.name}`,
                                                id: item.id,
                                                data: item,
                                            };
                                        })
                                    };
                                }
                            }
                        }"
                        />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-[3rem]">
                    <div class="form-group">
                        <label>Select Item<span class="required">*</span></label>
                        <Select2
                            v-model="form.existing_item_selected"
                            @select="setTableBody($event, item)"
                            :settings="{
                                ajax: {
                                    url: '/data/products-by-category',
                                    dataType: 'json',
                                    method: 'POST',
                                    data: function (params) {
                                      return {
                                        search: params.term,
                                        category_id: form.existing_item_category_id,
                                        _token: csrf,
                                      };
                                    },
                                    processResults: function (data, params) {
                                        return {
                                            results: data.map(function (item) {
                                                return {
                                                    text: `${item.sku} | ${item.name}`,
                                                    id: item.id,
                                                    data: item,
                                                };
                                            })
                                        };
                                    }
                                }
                            }"
                        />
                        <div v-if="form.errors.existing_item_selected" class="invalid-feedback d-block">{{ form.errors.existing_item_selected }}</div>
                    </div>
                    <div class="form-group">
                        <label>Select Item<span class="required">*</span></label>
                        <Select2
                            v-model="form.new_item_selected"
                            :settings="{
                                ajax: {
                                    url: '/data/products-by-category',
                                    dataType: 'json',
                                    method: 'POST',
                                    data: function (params) {
                                      return {
                                        search: params.term,
                                        category_id: form.new_item_category_id,
                                        _token: csrf,
                                      };
                                    },
                                    processResults: function (data, params) {
                                        return {
                                            results: data.map(function (item) {
                                                return {
                                                    text: `${item.sku} | ${item.name}`,
                                                    id: item.id,
                                                    data: item,
                                                };
                                            })
                                        };
                                    }
                                }
                            }"
                        />
                        <div v-if="form.errors.new_item_selected" class="invalid-feedback d-block">{{ form.errors.new_item_selected }}</div>
                    </div>
                </div>

            </div>
            <div class="box rounded-md shadow-default bg-white">
                <div class="table-responsive">
                    <table class="table select-rows">
                        <thead>
                        <tr>
                            <th></th>
                            <th>
                                Assembly ID
                            </th>
                            <th>
                                Assembly Name
                            </th>
                            <th>
                                Category
                            </th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-if="assemblies.length === 0">
                                <td colspan="5" class="text-center">No assembly found</td>
                            </tr>
                            <tr v-for="(assembly, index) in assemblies" :key="index">
                                <td>
                                    <div class="custom-checkbox style-1">
                                        <input type="checkbox"
                                               :id="'log_card_' + index"
                                               :value="assembly"
                                               v-model="form.assemblies">
                                        <label :for="'log_card_' + index">{{index + 1}}</label>
                                    </div>
                                </td>
                                <td>{{ assembly.code }}</td>
                                <td>{{ assembly.name }}</td>
                                <td>{{ assembly.category }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="form.errors.assemblies" class="invalid-feedback d-block">{{ form.errors.assemblies }}</div>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import Layout from "../../Components/Layout.vue";
import Pagination from "../../Components/Pagination.vue";
import Swal from "sweetalert2";
import debounce from "lodash.debounce";
import {useForm} from '@inertiajs/inertia-vue3';


const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    csrf: String,
});

const form = useForm({
    existing_item_category_id: null,
    new_item_category_id: null,
    existing_item_selected: null,
    new_item_selected: null,
    assemblies: []
});

const assemblies = ref([])

const setTableBody = async (product) => {
    try {
        const response = await axios.post('/data/assembly/replace-item/items', {
            product_id: product.id,
            _token: props.csrf,
        });
        assemblies.value = response.data;
    } catch (error) {
        console.error('Error fetching assembly items:', error);
    }
};

watch(() => form.existing_item_category_id, (selected_category_id) => {
    form.existing_item_selected = null;
    assemblies.value = []
})

</script>
