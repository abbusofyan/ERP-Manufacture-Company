<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="storage" class="big-title">Edit Storage</div>
            <div v-else class="big-title">Create New Storage</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/storages">Storage</Link>
                    </li>
                    <li>
                        <span v-if="storage">Edit</span>
                        <span v-else>Add</span>
                    </li>
                </ol>
            </nav>
        </div>
        <form @submit.prevent="storage ? form.put(`/storages/${storage.id}`) : form.post('/storages')">
            <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
                <div class="form-wrap">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">1</span>
                                <span>Storage Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>GRN Number<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.grn_number }" v-model="form.grn_number" placeholder="GRN Number">
                                    <div v-if="form.errors.grn_number" class="invalid-feedback d-block">{{ form.errors.grn_number }}</div>
                                </div>
                                <div class="form-group">
                                    <p class="mb-14">Show All Product</p>
                                    <label class="switch" for="show-all">
                                        <input id="show-all" type="checkbox" v-model="show_all" @change="filter">
                                        <div class="slider round"></div>
                                        <div class="icon"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">2</span>
                                <span>Add Product</span>
                            </div>

                            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 pl-0 pr-0 gap-1">
                                <div class="flex flex-wrap gap-16 justify-between">
                                    <div class="search-el ml-auto">
                                        <div class="txt">Search</div>
                                        <div class="form">
                                            <input type="search" placeholder="Search" v-model="search">
                                        </div>
                                    </div>
                                </div>
                                <label class="d-flex align-items-center gap-1">Show
                                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="table-responsive pb-0">
                                <table class="table select-rows">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Product Name</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>SKU</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Category</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Brand</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>Stock</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="flex items-center justify-between gap-1">
                                                <span>UOM</span><span class="sort-ic"><img src="../../../assets/img/sort.svg" alt="sort"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <span>Action</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="product in products.data" :key="product.id">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="img-30">
                                                    <img :src="`/storage/${product.file_url}`" alt="">
                                                </div>
                                                <span class="text-bold">{{ product.name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ product.sku }}
                                        </td>
                                        <td>
                                            {{ product.category.name }}
                                        </td>
                                        <td>
                                            Mitsubishi
                                        </td>
                                        <td>
                                            {{ product.stock }}
                                        </td>
                                        <td>
                                            {{ product.uom.code }}
                                        </td>
                                        <td>
                                            <a
                                                class="btn btn-purple"
                                                href="javascript:void(0)"
                                                @click="form.selected_product.push({
                                                   product,
                                                   selected_location: null,
                                                   price: 0,
                                                   locations: []
                                               })">
                                                +&nbsp;&nbsp;Add
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex items-center justify-between gap-26 mt-25 pb-25 px-25">
                                <p>Showing {{ products.from }} to {{ products.to }} of {{ products.total }} entries</p>
                                <Pagination :links="products.links"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box rounded-md shadow-default bg-white mt-18 card-box-shadow">
                <div class="box pt-20 px-25">
                    <div class="text-18 font-medium pb-17 mb-17">
                        <span>Selected Product</span>
                        <div v-if="form.errors.selected_product" class="invalid-feedback d-block">{{ form.errors.selected_product }}</div>
                    </div>
                </div>
                <div v-if="form.selected_product.length === 0">
                    <div class="box pt-20 px-25 bg-[#F8F8F8]" :class="{ 'is-invalid form-control border-[1px] border-solid': form.errors.selected_product }">
                        <div class="text-center font-medium pb-17 mb-17">
                            <div class="d-block d-flex justify-content-center mt-18">
                                <svg width="81" height="80" viewBox="0 0 81 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M3.8335 39.9997C3.8335 60.333 20.1668 76.6663 40.5002 76.6663C60.8335 76.6663 77.1668 60.333 77.1668 39.9997C77.1668 19.6663 60.8335 3.33301 40.5002 3.33301C20.1668 3.33301 3.8335 19.6663 3.8335 39.9997ZM10.5002 39.9997C10.5002 23.333 23.8335 9.99967 40.5002 9.99967C57.1668 9.99967 70.5002 23.333 70.5002 39.9997C70.5002 56.6663 57.1668 69.9997 40.5002 69.9997C23.8335 69.9997 10.5002 56.6663 10.5002 39.9997ZM43.8335 39.9997V26.6663C43.8335 24.6663 42.5002 23.333 40.5002 23.333C38.5002 23.333 37.1668 24.6663 37.1668 26.6663V39.9997C37.1668 41.9997 38.5002 43.333 40.5002 43.333C42.5002 43.333 43.8335 41.9997 43.8335 39.9997ZM43.5002 54.6663C43.5002 54.9997 43.1668 55.333 42.8335 55.6663C42.1668 56.333 41.5002 56.6663 40.1668 56.6663C39.1668 56.6663 38.5002 56.333 37.8335 55.6663C37.1668 54.9997 36.8335 54.333 36.8335 53.333C36.8335 52.9425 36.9479 52.6663 37.0426 52.4376C37.1096 52.2758 37.1668 52.1377 37.1668 51.9997C37.1668 51.6663 37.5002 51.333 37.8335 50.9997C38.8335 49.9997 40.1668 49.6663 41.5002 50.333C41.6668 50.333 41.7502 50.4163 41.8335 50.4997C41.9168 50.583 42.0002 50.6663 42.1668 50.6663C42.5002 50.6663 42.8335 50.9997 42.8335 50.9997C43.0002 51.1663 43.0835 51.333 43.1668 51.4997C43.2502 51.6663 43.3335 51.833 43.5002 51.9997C43.8335 52.333 43.8335 52.9997 43.8335 53.333C43.8335 53.4997 43.7502 53.7497 43.6668 53.9997C43.5835 54.2497 43.5002 54.4997 43.5002 54.6663Z"
                                          fill="black"/>
                                    <mask id="mask0_2965_411576" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="3" y="3" width="75" height="74">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M3.8335 39.9997C3.8335 60.333 20.1668 76.6663 40.5002 76.6663C60.8335 76.6663 77.1668 60.333 77.1668 39.9997C77.1668 19.6663 60.8335 3.33301 40.5002 3.33301C20.1668 3.33301 3.8335 19.6663 3.8335 39.9997ZM10.5002 39.9997C10.5002 23.333 23.8335 9.99967 40.5002 9.99967C57.1668 9.99967 70.5002 23.333 70.5002 39.9997C70.5002 56.6663 57.1668 69.9997 40.5002 69.9997C23.8335 69.9997 10.5002 56.6663 10.5002 39.9997ZM43.8335 39.9997V26.6663C43.8335 24.6663 42.5002 23.333 40.5002 23.333C38.5002 23.333 37.1668 24.6663 37.1668 26.6663V39.9997C37.1668 41.9997 38.5002 43.333 40.5002 43.333C42.5002 43.333 43.8335 41.9997 43.8335 39.9997ZM43.5002 54.6663C43.5002 54.9997 43.1668 55.333 42.8335 55.6663C42.1668 56.333 41.5002 56.6663 40.1668 56.6663C39.1668 56.6663 38.5002 56.333 37.8335 55.6663C37.1668 54.9997 36.8335 54.333 36.8335 53.333C36.8335 52.9425 36.9479 52.6663 37.0426 52.4376C37.1096 52.2758 37.1668 52.1377 37.1668 51.9997C37.1668 51.6663 37.5002 51.333 37.8335 50.9997C38.8335 49.9997 40.1668 49.6663 41.5002 50.333C41.6668 50.333 41.7502 50.4163 41.8335 50.4997C41.9168 50.583 42.0002 50.6663 42.1668 50.6663C42.5002 50.6663 42.8335 50.9997 42.8335 50.9997C43.0002 51.1663 43.0835 51.333 43.1668 51.4997C43.2502 51.6663 43.3335 51.833 43.5002 51.9997C43.8335 52.333 43.8335 52.9997 43.8335 53.333C43.8335 53.4997 43.7502 53.7497 43.6668 53.9997C43.5835 54.2497 43.5002 54.4997 43.5002 54.6663Z"
                                              fill="white"/>
                                    </mask>
                                    <g mask="url(#mask0_2965_411576)">
                                        <rect x="0.5" width="80" height="80" fill="#FF9F43"/>
                                    </g>
                                </svg>
                            </div>
                            <div class="mt-18 pb-25">Please add product from the table above to continue</div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div v-for="(data, index) in form.selected_product" :key="index" class="box pt-20 px-25 bg-[#F8F8F8]">
                        <div class="font-medium pb-17 pb-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-18 text-bold text-primary">
                                    {{ data.product.name }}
                                </div>
                                <a class="btn btn-danger" href="javascript:void(0)" @click="form.selected_product.splice(index,1)">&times;&nbsp;&nbsp;Remove</a>
                            </div>
                            <div v-if="form.errors[`selected_product.${index}.locations`]" class="invalid-feedback d-block">{{ form.errors[`selected_product.${index}.locations`] }}</div>
                            <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white mt-18 card-box-shadow border-0" :class="{ 'is-invalid form-control border-[1px] border-solid': form.errors[`selected_product.${index}.locations`] }">
                                <div class="form-wrap">
                                    <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                        <div class="form-group">
                                            <table class="table w-full max-w-[24.5rem]">
                                                <tbody>
                                                <tr>
                                                    <td class="pb-10">Total Product</td>
                                                    <th class="pb-10 text-primary">
                                                        {{
                                                            data.locations.reduce(function (sum, location) {
                                                                return sum + Number(location.quantity);
                                                            }, 0)
                                                        }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td class="pb-10">Allocated Product :</td>
                                                    <th class="pb-10 text-primary">
                                                        {{ data.product.stock }}
                                                    </th>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="form-group select2-w-100">
                                            <label>Select Location<span class="required">*</span></label>
                                            <div class="d-flex flex-wrap gap-1">
                                                <div class="flex-fill">
                                                    <Select2 v-model="data.selected_location" :options="location_select" placeholder="Select Location"/>
                                                </div>
                                                <div class="text-right">
                                                    <a class="btn btn-purple"
                                                       href="javascript:void(0)"
                                                       @click="data.selected_location ? data.locations.push({
                                                       location_id: data.selected_location,
                                                       quantity: 1,
                                                       cost_price: 0
                                                       }) : null">
                                                        +&nbsp;&nbsp;Add
                                                    </a>
                                                </div>
                                            </div>
                                            <div v-if="form.errors.grn_number" class="invalid-feedback d-block">{{ form.errors.grn_number }}</div>
                                        </div>
                                    </div>
                                    <div v-if="data.locations.length === 0">
                                        <div class="box pt-20 px-25 bg-[#F8F8F8]">
                                            <div class="text-center font-medium pb-17 mb-17">
                                                <div class="d-block d-flex justify-content-center mt-18">
                                                    <svg width="81" height="80" viewBox="0 0 81 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M3.8335 39.9997C3.8335 60.333 20.1668 76.6663 40.5002 76.6663C60.8335 76.6663 77.1668 60.333 77.1668 39.9997C77.1668 19.6663 60.8335 3.33301 40.5002 3.33301C20.1668 3.33301 3.8335 19.6663 3.8335 39.9997ZM10.5002 39.9997C10.5002 23.333 23.8335 9.99967 40.5002 9.99967C57.1668 9.99967 70.5002 23.333 70.5002 39.9997C70.5002 56.6663 57.1668 69.9997 40.5002 69.9997C23.8335 69.9997 10.5002 56.6663 10.5002 39.9997ZM43.8335 39.9997V26.6663C43.8335 24.6663 42.5002 23.333 40.5002 23.333C38.5002 23.333 37.1668 24.6663 37.1668 26.6663V39.9997C37.1668 41.9997 38.5002 43.333 40.5002 43.333C42.5002 43.333 43.8335 41.9997 43.8335 39.9997ZM43.5002 54.6663C43.5002 54.9997 43.1668 55.333 42.8335 55.6663C42.1668 56.333 41.5002 56.6663 40.1668 56.6663C39.1668 56.6663 38.5002 56.333 37.8335 55.6663C37.1668 54.9997 36.8335 54.333 36.8335 53.333C36.8335 52.9425 36.9479 52.6663 37.0426 52.4376C37.1096 52.2758 37.1668 52.1377 37.1668 51.9997C37.1668 51.6663 37.5002 51.333 37.8335 50.9997C38.8335 49.9997 40.1668 49.6663 41.5002 50.333C41.6668 50.333 41.7502 50.4163 41.8335 50.4997C41.9168 50.583 42.0002 50.6663 42.1668 50.6663C42.5002 50.6663 42.8335 50.9997 42.8335 50.9997C43.0002 51.1663 43.0835 51.333 43.1668 51.4997C43.2502 51.6663 43.3335 51.833 43.5002 51.9997C43.8335 52.333 43.8335 52.9997 43.8335 53.333C43.8335 53.4997 43.7502 53.7497 43.6668 53.9997C43.5835 54.2497 43.5002 54.4997 43.5002 54.6663Z"
                                                              fill="black"/>
                                                        <mask id="mask0_2965_411576" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="3" y="3" width="75" height="74">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                  d="M3.8335 39.9997C3.8335 60.333 20.1668 76.6663 40.5002 76.6663C60.8335 76.6663 77.1668 60.333 77.1668 39.9997C77.1668 19.6663 60.8335 3.33301 40.5002 3.33301C20.1668 3.33301 3.8335 19.6663 3.8335 39.9997ZM10.5002 39.9997C10.5002 23.333 23.8335 9.99967 40.5002 9.99967C57.1668 9.99967 70.5002 23.333 70.5002 39.9997C70.5002 56.6663 57.1668 69.9997 40.5002 69.9997C23.8335 69.9997 10.5002 56.6663 10.5002 39.9997ZM43.8335 39.9997V26.6663C43.8335 24.6663 42.5002 23.333 40.5002 23.333C38.5002 23.333 37.1668 24.6663 37.1668 26.6663V39.9997C37.1668 41.9997 38.5002 43.333 40.5002 43.333C42.5002 43.333 43.8335 41.9997 43.8335 39.9997ZM43.5002 54.6663C43.5002 54.9997 43.1668 55.333 42.8335 55.6663C42.1668 56.333 41.5002 56.6663 40.1668 56.6663C39.1668 56.6663 38.5002 56.333 37.8335 55.6663C37.1668 54.9997 36.8335 54.333 36.8335 53.333C36.8335 52.9425 36.9479 52.6663 37.0426 52.4376C37.1096 52.2758 37.1668 52.1377 37.1668 51.9997C37.1668 51.6663 37.5002 51.333 37.8335 50.9997C38.8335 49.9997 40.1668 49.6663 41.5002 50.333C41.6668 50.333 41.7502 50.4163 41.8335 50.4997C41.9168 50.583 42.0002 50.6663 42.1668 50.6663C42.5002 50.6663 42.8335 50.9997 42.8335 50.9997C43.0002 51.1663 43.0835 51.333 43.1668 51.4997C43.2502 51.6663 43.3335 51.833 43.5002 51.9997C43.8335 52.333 43.8335 52.9997 43.8335 53.333C43.8335 53.4997 43.7502 53.7497 43.6668 53.9997C43.5835 54.2497 43.5002 54.4997 43.5002 54.6663Z"
                                                                  fill="white"/>
                                                        </mask>
                                                        <g mask="url(#mask0_2965_411576)">
                                                            <rect x="0.5" width="80" height="80" fill="#FF9F43"/>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="mt-18 pb-25">Please add location from the select above to continue</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div class="table-responsive pb-0">
                                            <table class="table select-rows">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span>NO.</span>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span>Store</span>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span>Location</span>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span>Quantity<label><span class="required">*</span></label></span>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span>Cost Price<label><span class="required">*</span></label></span>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <span>Action</span>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td colspan="4">

                                                    </td>
                                                    <td>
                                                        <input class="form-control max-w-[24.5rem]" type="text" v-model="data.price" placeholder="0">
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-orange--brounded" href="javascript:void(0)" @click="data.locations.forEach(data_c => {data_c.cost_price = data.price})">&check;&nbsp;&nbsp;Apply to All</a>
                                                    </td>
                                                </tr>
                                                <tr v-for="(data_c,index_c) in data.locations" :key="index_c">
                                                    <td>
                                                        {{ index_c + 1 }}
                                                    </td>
                                                    <td>
                                                        {{ locations.find(location => Number(location.id) === Number(data_c.location_id)) != null ? locations.find(location => Number(location.id) === Number(data_c.location_id)).store.name : 'N/A' }}
                                                    </td>
                                                    <td>
<!--                                                        {{ locations.find(location => Number(location.id) === Number(data_c.location_id)).name }}-->
                                                    </td>
                                                    <td>
                                                        <div class="qty-custom max-w-150">
                                                            <span class="minus" @click="data_c.quantity = data_c.quantity > 1  ? Number(data_c.quantity) - 1 : data_c.quantity"></span>
                                                            <input class="form-control max-w-[24.5rem]" :class="{ 'is-invalid': form.errors[`selected_product.${index}.locations.${index_c}.quantity`] }" type="number" v-model="data_c.quantity" placeholder="0">
                                                            <span class="plus" @click="data_c.quantity = Number(data_c.quantity) + 1"></span>
                                                        </div>
                                                        <div v-if="form.errors[`selected_product.${index}.locations.${index_c}.quantity`]" class="invalid-feedback d-block">{{ form.errors[`selected_product.${index}.locations.${index_c}.quantity`] }}</div>
                                                    </td>
                                                    <td>
                                                        <input class="form-control max-w-[24.5rem]" :class="{ 'is-invalid': form.errors[`selected_product.${index}.locations.${index_c}.cost_price`] }" type="text" v-model="data_c.cost_price" placeholder="0">
                                                        <div v-if="form.errors[`selected_product.${index}.locations.${index_c}.cost_price`]" class="invalid-feedback d-block">{{ form.errors[`selected_product.${index}.locations.${index_c}.cost_price`] }}</div>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-danger--brounded" href="javascript:void(0)" @click="data.locations.splice(index_c,1)">&times;&nbsp;&nbsp;Remove</a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white mt-18 card-box-shadow">
                <div class="form-wrap">
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" type="submit" @click="form.status = 1" :disabled="form.processing">Submit</button>
                        <button class="btn btn-orange" type="submit" @click="form.status = 0" :disabled="form.processing">Save as Draft</button>
                        <Link class="btn btn-light btn-light--brounded" href="/storages">Discard</Link>
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
import {onMounted, ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import debounce from "lodash.debounce";

const props = defineProps({
    storage: Object,
    products: Object,
    filters: Object,
    locations: Array,
    location_select: Array,
});

const form = useForm({
    status: 1,
    grn_number: null,
    selected_product: [],
    grouped: [],
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);
let show_all = ref(props.filters.show_all);

const filter = () => {
    Inertia.get(
        "/storages/create",
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
            show_all: show_all.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

watch(
    search,
    debounce(() => {
        filter();
    }, 500)
);

watch([order, by, paginate], () => {
    filter();
});

const sort = (data) => {
    order.value = data;

    if (by.value == "asc") {
        by.value = "desc";
    } else {
        by.value = "asc";
    }
};

onMounted(() => {
    if (props.storage) {
        form.status = props.storage.status;
        form.grn_number = props.storage.grn_number;

        /** Show items **/
        form.grouped = [];
        props.storage.storage_items.forEach((item) => {
            if (!form.grouped[item.product_id]) {
                form.grouped[item.product_id] = {
                    product: item.product,
                    items: [],
                };
            }

            form.grouped[item.product_id].items.push(item);
        });

        form.grouped = form.grouped.filter((group) => group !== null);

        form.grouped.forEach(item => {
            let locations = [];
            item.items.forEach(item_c => {
                locations.push({
                    location_id: item_c.location ? item_c.location.id : null,
                    quantity: item_c.quantity,
                    cost_price: item_c.cost_price
                })
            })

            form.selected_product.push({
                product: item.product,
                selected_location: null,
                price: 0,
                locations: locations
            })
        })
    }
});
</script>
