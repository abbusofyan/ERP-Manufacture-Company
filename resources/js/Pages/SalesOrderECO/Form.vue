<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Local Sales Order</div>
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
                        <span>Add and Edit Milestones</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white overflow-hidden">
            <div class="boxes">
                <div class="mb-[2.6rem]">
                    <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                        <span class="w-24 h-24 inline-flex items-center justify-center border-[1px]  rounded-[50%] mr-8 border-solid">!</span>
                        <span>Customer Information</span>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th class="pb-10">Name</th>
                                </tr>
                                <tr>
                                    <td class="text-uppercase">
                                        {{ order.refrigeration_sale.customer ? order.refrigeration_sale.customer.name : '--' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <table class="table-1-el w-full">
                                <tr>
                                    <th>Company</th>
                                    <td>
                                        {{ order.refrigeration_sale.poc_name ?? '--' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Production W.O</th>
                                    <td>
                                        {{ order.refrigeration_sale.date ?? '--' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Delivery Date</th>
                                    <td>
                                        <div v-for="(mine_stone, index) in order.mine_stones" :key="index">
                                            {{ mine_stone.delivery_date ?? '--' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mt-[2.6rem]"></div>

                    <div class="d-flex justify-between flex-wrap gap-[3rem] mt-[2.6rem]">
                        <div class="text-bold">Sales Order Milestone or which stage the vehicle is at</div>
                        <Link class="btn btn-purple" :href="`/${shipment}/sales-order-eco/${order.id}/edit-milestone`">Add And Edit Milestones</Link>
                    </div>

                    <div class="row justify-content-center">
                        <template v-for="(mine_stone, index) in order.mine_stones" :key="index">
                            <template v-for="(item, index_i) in mine_stone.items" :key="index_i">
                                <div class="col-xl-2 col-lg-3 mt-[2.6rem]" v-if="item.stage && item.active" :key="index_i">
                                    <div class="stage-mine-stone-item" :class="`circle-${index_i}`">
                                        <div class="top">
                                            <div class="circle mb-16">
                                                <div class="wrapper">
                                                    <span class="text-24 text-primary text-bold">{{ index_i + 1 }}</span>
                                                </div>
                                            </div>
                                            <div class="text-15 text-center text-bold mb-10">
                                                {{ item.stage }}
                                            </div>
                                            <div class="el-tag h-auto text-center" :class="item.status_class">
                                                <span>{{ item.status_text }}</span>
                                            </div>
                                        </div>
                                        <div class="bottom">
                                            <div class="line">
                                                <div class="dot"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[.5rem] border-[1px] border-solid border-[#626CC6] mt-25">
            <div class="row no-gutters">
                <div class="col-lg-3">
                    <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 1" :class="{'btn-purple--brounded' : form.tab !== 1}" href="javascript:void(0)">Vehicle</a>
                </div>
                <div class="col-lg-3">
                    <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 2" :class="{'btn-purple--brounded' : form.tab !== 2}" href="javascript:void(0)">Production Process</a>
                </div>
                <div class="col-lg-3">
                    <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 3" :class="{'btn-purple--brounded' : form.tab !== 3}" href="javascript:void(0)">Contractors</a>
                </div>
                <div class="col-lg-3">
                    <a class="w-100 btn btn-purple rounded-0" @click="form.tab = 4" :class="{'btn-purple--brounded' : form.tab !== 4}" href="javascript:void(0)">Remarks</a>
                </div>
            </div>
        </div>

        <form @submit.prevent="form.put(`/${shipment}/sales-order-eco/${order.id}`)">
            <div class="tab-1" v-if="form.tab === 1">
                <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mt-[2.6rem]">
                    <div class="boxes">
                        <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                            <div class="text-18 font-medium mb-17">
                                <span>Vehicle Information</span>
                            </div>
                            <div class="btn-group mb-17">
                                <button class="btn btn-purple" type="submit" @click="form.type = 2" :disabled="form.processing">Save Changes</button>
                                <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales/${order.rs_id}`">Discard</Link>
                            </div>
                        </div>
                        <div class="form-group mt-16">
                            <label>Schedule Comments</label>
                            <textarea class="form-control" type="text" :class="{ 'is-invalid': form.errors.schedule_comments }" v-model="form.schedule_comments" placeholder="Type Here"></textarea>
                            <div v-if="form.errors.schedule_comments" class="invalid-feedback d-block">{{ form.errors.schedule_comments }}</div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div class="form-group">
                                <label>Vehicle Receive Date</label>
                                <VueDatePicker v-model="form.vehicle_receive_date" :class="{ 'is-invalid': form.errors.vehicle_receive_date }" :format="format" :enable-time-picker="false" placeholder="XX-XX-XXXX"></VueDatePicker>
                                <div v-if="form.errors.vehicle_receive_date" class="invalid-feedback d-block">{{ form.errors.vehicle_receive_date }}</div>
                            </div>
                            <div class="form-group">
                                <label>Actual Completion Date</label>
                                <VueDatePicker v-model="form.actual_completion_date" :class="{ 'is-invalid': form.errors.actual_completion_date }" :format="format" :enable-time-picker="false" placeholder="XX-XX-XXXX"></VueDatePicker>
                                <div v-if="form.errors.actual_completion_date" class="invalid-feedback d-block">{{ form.errors.actual_completion_date }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mt-[2.6rem]">
                    <div class="boxes">
                        <!-- Add vehicles -->
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="text-18 font-medium mb-17">
                                    <span>New Vehicle</span>
                                </div>
                                <div class="btn-group mb-17">
                                    <button class="btn btn-purple" type="submit" @click="form.type = 3" :disabled="form.processing">Save Changes</button>
                                    <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales/${order.rs_id}`">Discard</Link>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Vehicle Number<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.license_plate }" v-model="form.license_plate" placeholder="SBA 1234A">
                                    <div v-if="form.errors.license_plate" class="invalid-feedback d-block">{{ form.errors.license_plate }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Customer Name<span class="required">*</span></label>
                                    <Select2
                                        v-model="form.customer_id"
                                        :class="{ 'is-invalid': form.errors.customer_id }"
                                        :options="customers"
                                        placeholder="Select Customer"
                                        :settings="{
                                                ajax: {
                                                    url: '/data/customers',
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
                                                                    text: `${item.code} | ${item.name}`,
                                                                    id: item.id,
                                                                };
                                                            })
                                                        };
                                                    }
                                                }
                                            }"
                                    />
                                    <div v-if="form.errors.customer_id" class="invalid-feedback d-block">{{ form.errors.customer_id }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Type<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_type }" v-model="form.vehicle_type" placeholder="10' Truck">
                                    <div v-if="form.errors.vehicle_type" class="invalid-feedback d-block">{{ form.errors.vehicle_type }}</div>
                                </div>
                                <div class="form-group">
                                    <label>End User</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.end_user }" v-model="form.end_user" placeholder="End User">
                                    <div v-if="form.errors.end_user" class="invalid-feedback d-block">{{ form.errors.end_user }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Vehicle Voltage<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_voltage }" v-model="form.vehicle_voltage" placeholder="Vehicle Voltage">
                                    <div v-if="form.errors.vehicle_voltage" class="invalid-feedback d-block">{{ form.errors.vehicle_voltage }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Chassis Salesperson / Contact No.</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.contact_no }" v-model="form.contact_no" placeholder="Chassis Salesperson / Contact No.">
                                    <div v-if="form.errors.contact_no" class="invalid-feedback d-block">{{ form.errors.contact_no }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Chassis No.<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.chassis_no }" v-model="form.chassis_no" placeholder="Chassis No.">
                                    <div v-if="form.errors.chassis_no" class="invalid-feedback d-block">{{ form.errors.chassis_no }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Chassis Delivery</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.chassis_delivery }" v-model="form.chassis_delivery" placeholder="Chassis Delivery">
                                    <div v-if="form.errors.chassis_delivery" class="invalid-feedback d-block">{{ form.errors.chassis_delivery }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Vehicle Make<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_make }" v-model="form.vehicle_make" placeholder="Vehicle Make">
                                        <div v-if="form.errors.vehicle_make" class="invalid-feedback d-block">{{ form.errors.vehicle_make }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Model<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.model }" v-model="form.model" placeholder="model">
                                        <div v-if="form.errors.model" class="invalid-feedback d-block">{{ form.errors.model }}</div>
                                    </div>
                                </div>
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Vehicle Class<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_class }" v-model="form.vehicle_class" placeholder="Vehicle Class">
                                        <div v-if="form.errors.vehicle_class" class="invalid-feedback d-block">{{ form.errors.vehicle_class }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Type of Plate<span class="required">*</span></label>
                                        <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.type_of_plate }" v-model="form.type_of_plate" placeholder="Type of Plate">
                                        <div v-if="form.errors.type_of_plate" class="invalid-feedback d-block">{{ form.errors.type_of_plate }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                    <div class="form-group">
                                        <label>Warranty Mileage</label>
                                        <input class="form-control" type="number" :class="{ 'is-invalid': form.errors.warranty_mileage }" v-model="form.warranty_mileage" placeholder="Enter warranty mileage">
                                        <div v-if="form.errors.warranty_mileage" class="invalid-feedback d-block">{{ form.errors.warranty_mileage }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label>Warranty End Date</label>
                                        <VueDatePicker v-model="form.warranty_end_date" :class="{ 'is-invalid': form.errors.warranty_end_date }" :format="format" :enable-time-picker="false" placeholder="Select Date"></VueDatePicker>
                                        <div v-if="form.errors.warranty_end_date" class="invalid-feedback d-block">{{ form.errors.warranty_end_date }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Show vehicles -->
                        <div v-if="order.vehicles" v-for="vehicle in order.vehicles" class="border-[1px] border-solid border-[#EBE9F1] px-20 pt-20 pb-30 rounded-[.5rem] mb-[2.6rem]">
                            <div class="text-15 font-medium pb-10 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span>Vehicle Details</span>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <table class="table-1-el w-full">
                                        <tr>
                                            <th>Vehicle Number</th>
                                            <td>{{ vehicle.license_plate }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type</th>
                                            <td>{{ vehicle.type }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vehicle Make</th>
                                            <td>{{ vehicle.vehicle_make }}</td>
                                        </tr>
                                        <tr>
                                            <th>Model</th>
                                            <td>{{ vehicle.model }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <table class="table-1-el w-full">
                                        <tr v-if="vehicle.service_contracts">
                                            <th>CMP Contact validity</th>
                                            <td>
                                                <div v-for="contract in vehicle.service_contracts" :key="contract.id">
                                                    {{ $filters.formatDate(contract.start_duration_date) }} / {{ $filters.formatDate(contract.end_duration_date) }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="vehicle.warranty_end_date">
                                            <th>Warranty Expiry Date</th>
                                            <td>
                                                {{ vehicle.warranty_end_date }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Warranty Status</th>
                                            <td>
                                                {{ vehicle.warranty_status }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Warranty Mileage</th>
                                            <td>
                                                {{ vehicle.warranty_mileage }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mt-[2.6rem]">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                                <div class="text-18 font-medium mb-17">
                                    <span>Refrigeration Information</span>
                                </div>
                                <div class="btn-group mb-17">
                                    <button class="btn btn-purple" type="submit" @click="form.type = 4" :disabled="form.processing">Save Changes</button>
                                    <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales/${order.rs_id}`">Discard</Link>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Engine serial<span class="required">*</span></label>
                                    <div class="d-flex">
                                        <input class="form-control border-right-0" type="text" v-model="form.engine_serial_input" placeholder="Engine serial">
                                        <a class="btn btn-purple-50 mr--5" @click="form.engine_serial.push(form.engine_serial_input)" href="javascript:void(0)"><span class="text-18">+</span>Add More</a>
                                    </div>
                                    <div class="d-flex gap-[.5rem] flex-wrap">
                                        <div v-for="(item, index) in form.engine_serial" class="el-tag primary mt-10 p-10 h-auto" :key="index">
                                            {{ item }} <a @click="form.engine_serial.splice(index,1)" href="javascript:void(0)">&times;</a>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.engine_serial" class="invalid-feedback d-block">{{ form.errors.engine_serial }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Evaporator serial</label>
                                    <div class="d-flex">
                                        <input class="form-control border-right-0" type="text" v-model="form.evaporator_serial_input" placeholder="Evaporator serial">
                                        <a class="btn btn-purple-50 mr--5" @click="form.evaporator_serial.push(form.evaporator_serial_input)" href="javascript:void(0)"><span class="text-18">+</span>Add More</a>
                                    </div>
                                    <div class="d-flex gap-[.5rem] flex-wrap">
                                        <div v-for="(item, index) in form.evaporator_serial" class="el-tag primary mt-10 p-10 h-auto" :key="index">
                                            {{ item }} <a @click="form.evaporator_serial.splice(index,1)" href="javascript:void(0)">&times;</a>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.evaporator_serial" class="invalid-feedback d-block">{{ form.errors.evaporator_serial }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Refrigeration Unit Serial<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.refrigeration_unit_serial }" v-model="form.refrigeration_unit_serial" placeholder="Refrigeration Unit Serial">
                                    <div v-if="form.errors.refrigeration_unit_serial" class="invalid-feedback d-block">{{ form.errors.refrigeration_unit_serial }}</div>
                                </div>
                                <div class="form-group">
                                    <label>NEA serial</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.nea_serial }" v-model="form.nea_serial" placeholder="NEA serial">
                                    <div v-if="form.errors.nea_serial" class="invalid-feedback d-block">{{ form.errors.nea_serial }}</div>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Condenser serial<span class="required">*</span></label>
                                    <div class="d-flex">
                                        <input class="form-control border-right-0" type="text" v-model="form.condenser_serial_input" placeholder="Condenser serial">
                                        <a class="btn btn-purple-50 mr--5" @click="form.condenser_serial.push(form.condenser_serial_input)" href="javascript:void(0)"><span class="text-18">+</span>Add More</a>
                                    </div>
                                    <div class="d-flex gap-[.5rem] flex-wrap">
                                        <div v-for="(item, index) in form.condenser_serial" class="el-tag primary mt-10 p-10 h-auto" :key="index">
                                            {{ item }} <a @click="form.condenser_serial.splice(index,1)" href="javascript:void(0)">&times;</a>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.condenser_serial" class="invalid-feedback d-block">{{ form.errors.condenser_serial }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Standby unit<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.standby_unit }" v-model="form.standby_unit" placeholder="Standby unit">
                                    <div v-if="form.errors.evaporator_serial" class="invalid-feedback d-block">{{ form.errors.standby_unit }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-2" v-if="form.tab === 2">
                <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mt-[2.6rem]">
                    <div class="boxes">
                        <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                            <div class="text-18 font-medium mb-17">
                                <span>Production Work Order</span>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem]">
                            <div class="">
                                <div class="form-group">
                                    <label>Vehicle Class<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_class_wo }" v-model="form.vehicle_class_wo" placeholder="Type Here">
                                    <div v-if="form.errors.vehicle_class_wo" class="invalid-feedback d-block">{{ form.errors.vehicle_class_wo }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Vehicle Voltage<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_voltage_wo }" v-model="form.vehicle_voltage_wo" placeholder="Type Here">
                                    <div v-if="form.errors.vehicle_voltage_wo" class="invalid-feedback d-block">{{ form.errors.vehicle_voltage_wo }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Unit/Model<span class="required">*</span></label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.unit_model }" v-model="form.unit_model" placeholder="Type Here">
                                    <div v-if="form.errors.unit_model" class="invalid-feedback d-block">{{ form.errors.unit_model }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Box Size (Dimension in MM)</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-group flex-fill left-placeholder" data-placeholder="L">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.box_dimension_mm_l }" v-model="form.box_dimension_mm_l" placeholder="0">
                                            <div v-if="form.errors.box_dimension_mm_l" class="invalid-feedback d-block">{{ form.errors.box_dimension_mm_l }}</div>
                                        </div>
                                        <div class="form-group p-10">
                                            x
                                        </div>
                                        <div class="form-group flex-fill left-placeholder" data-placeholder="W">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.box_dimension_mm_w }" v-model="form.box_dimension_mm_w" placeholder="0">
                                            <div v-if="form.errors.box_dimension_mm_w" class="invalid-feedback d-block">{{ form.errors.box_dimension_mm_w }}</div>
                                        </div>
                                        <div class="form-group p-10">
                                            x
                                        </div>
                                        <div class="form-group flex-fill left-placeholder" data-placeholder="H">
                                            <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.box_dimension_mm_h }" v-model="form.box_dimension_mm_h" placeholder="0">
                                            <div v-if="form.errors.box_dimension_mm_h" class="invalid-feedback d-block">{{ form.errors.box_dimension_mm_h }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>PE Drwg (Archie)</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.pe_archie }" v-model="form.pe_archie" placeholder="Type Here">
                                    <div v-if="form.errors.pe_archie" class="invalid-feedback d-block">{{ form.errors.pe_archie }}</div>
                                </div>
                                <div class="form-group">
                                    <label>CMP/PMP (Prema)</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.pe_prema }" v-model="form.pe_prema" placeholder="Type Here">
                                    <div v-if="form.errors.pe_prema" class="invalid-feedback d-block">{{ form.errors.pe_prema }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Additional Accessories</label>
                                    <select class="form-select" :class="{ 'is-invalid': form.errors.addition_accessories }" v-model="form.addition_accessories">
                                        <option value="BOX">BOX</option>
                                        <option value="PE Drawing">PE Drawing</option>
                                        <option value="Chassis Extension / Shortening">Chassis Extension / Shortening</option>
                                    </select>
                                    <div v-if="form.errors.addition_accessories" class="invalid-feedback d-block">{{ form.errors.addition_accessories }}</div>
                                </div>
                                <div class="form-group sm:col-span-2">
                                    <label>Receipt Date</label>
                                    <VueDatePicker v-model="form.receipt_date" :class="{ 'is-invalid': form.errors.receipt_date }" :enable-time-picker="false" placeholder="Select date"></VueDatePicker>
                                    <div v-if="form.errors.receipt_date" class="invalid-feedback d-block">{{ form.errors.receipt_date }}</div>
                                </div>
                                <div class="form-group sm:col-span-2">
                                    <label>Received Date</label>
                                    <VueDatePicker v-model="form.received_date" :class="{ 'is-invalid': form.errors.received_date }" :enable-time-picker="false" placeholder="Select date"></VueDatePicker>
                                    <div v-if="form.errors.received_date" class="invalid-feedback d-block">{{ form.errors.received_date }}</div>
                                </div>
                            </div>
                            <div class="">
                                <div class="form-group">
                                    <label>Bracket</label>
                                    <div class="d-flex gap-3">
                                        <div class="custom-checkbox style-2">
                                            <input type="radio" v-model="form.bracket" value="Bracket" id="bracket">
                                            <label for="bracket">Bracket</label>
                                        </div>
                                        <div class="custom-checkbox style-2">
                                            <input type="radio" v-model="form.bracket" value="Ready" id="ready">
                                            <label for="ready">Ready</label>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.bracket" class="invalid-feedback d-block">{{ form.errors.bracket }}</div>
                                </div>
                                <div class="form-group sm:col-span-2">
                                    <label>Production Schedule to Start the box</label>
                                    <VueDatePicker v-model="form.production_schedule" :class="{ 'is-invalid': form.errors.production_schedule }" :enable-time-picker="false" placeholder="Select date"></VueDatePicker>
                                    <div v-if="form.errors.production_schedule" class="invalid-feedback d-block">{{ form.errors.production_schedule }}</div>
                                </div>
                                <div class="form-group">
                                    <label>I-Box (Physical Checking @ Prod’n floor)</label>
                                    <select class="form-select" :class="{ 'is-invalid': form.errors.ibox }" v-model="form.ibox">
                                        <option value="BOX">BOX</option>
                                        <option value="System">System</option>
                                        <option value="Flooring">Flooring</option>
                                    </select>
                                    <div v-if="form.errors.ibox" class="invalid-feedback d-block">{{ form.errors.ibox }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Final Assy (Physical Checking)</label>
                                    <div class="custom-checkbox style-1">
                                        <input type="checkbox" v-model="form.final_assy" id="final_assy">
                                        <label for="final_assy">Completed</label>
                                    </div>
                                    <div v-if="form.errors.final_assy" class="invalid-feedback d-block">{{ form.errors.final_assy }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Testing</label>
                                    <div class="custom-checkbox style-1">
                                        <input type="checkbox" v-model="form.testing" id="testing">
                                        <label for="testing">Cleaning & PDI</label>
                                    </div>
                                    <div v-if="form.errors.testing" class="invalid-feedback d-block">{{ form.errors.testing }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Vehicle No.</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.vehicle_no }" v-model="form.vehicle_no" placeholder="Type Here">
                                    <div v-if="form.errors.vehicle_no" class="invalid-feedback d-block">{{ form.errors.vehicle_no }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Log Card</label>
                                    <div class="custom-checkbox style-1">
                                        <input type="checkbox" v-model="form.log_card" id="log_card">
                                        <label for="log_card">Rcvd</label>
                                    </div>
                                    <div v-if="form.errors.log_card" class="invalid-feedback d-block">{{ form.errors.log_card }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Dummy Box for Inspection Date</label>
                                    <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.dummy_box }" v-model="form.dummy_box" placeholder="Type Here">
                                    <div v-if="form.errors.dummy_box" class="invalid-feedback d-block">{{ form.errors.dummy_box }}</div>
                                </div>
                                <div class="form-group">
                                    <label>Final Delivery</label>
                                    <select class="form-select" :class="{ 'is-invalid': form.errors.final_delivery }" v-model="form.final_delivery">
                                        <option value="Cust">Cust</option>
                                        <option value="Goldbel (SYN/WF)">Goldbel (SYN/WF)</option>
                                        <option value="Triangle / Borneo Motor/ST/Hoe Heng">Triangle / Borneo Motor/ST/Hoe Heng</option>
                                    </select>
                                    <div v-if="form.errors.final_delivery" class="invalid-feedback d-block">{{ form.errors.final_delivery }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-group mb-17 mt-25">
                            <button class="btn btn-purple" type="submit" @click="form.type = 6" :disabled="form.processing">Save Changes</button>
                            <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales/${order.rs_id}`">Discard</Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-3" v-if="form.tab === 3">
                <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mt-[2.6rem]">
                    <div class="boxes">
                        <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                            <div class="text-18 font-medium mb-17">
                                <span>Tailgate</span>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem] border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-17">
                            <div class="form-group">
                                <label>Supplier<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.tailgate_supplier }" v-model="form.tailgate_supplier" placeholder="Type Here">
                                <div v-if="form.errors.tailgate_supplier" class="invalid-feedback d-block">{{ form.errors.tailgate_supplier }}</div>
                            </div>
                            <div class="form-group">
                                <label>Completion<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.tailgate_completion }" v-model="form.tailgate_completion" placeholder="Type Here">
                                <div v-if="form.errors.tailgate_completion" class="invalid-feedback d-block">{{ form.errors.tailgate_completion }}</div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                            <div class="text-18 font-medium mb-17">
                                <span>Spray Painting</span>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-[7.7rem] border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-17">
                            <div class="form-group">
                                <label>Supplier<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.spray_painting_supplier }" v-model="form.spray_painting_supplier" placeholder="Type Here">
                                <div v-if="form.errors.spray_painting_supplier" class="invalid-feedback d-block">{{ form.errors.spray_painting_supplier }}</div>
                            </div>
                            <div class="form-group">
                                <label>Completion<span class="required">*</span></label>
                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors.spray_painting_completion }" v-model="form.spray_painting_completion" placeholder="Type Here">
                                <div v-if="form.errors.spray_painting_completion" class="invalid-feedback d-block">{{ form.errors.spray_painting_completion }}</div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem]">
                            <div class="text-18 font-medium mb-17">
                                <span>Vehicle Preparation Plan</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3">
                                <div v-for="(data, key) in form.confirmation" class="row mb-16">
                                    <div class="col-6">
                                        <div class="custom-checkbox style-1">
                                            <input type="checkbox" v-model="data.isSelected" :id="`select-${key}`">
                                            <label :for="`select-${key}`">{{ key }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="custom-checkbox style-1">
                                            <input type="checkbox" v-model="data.isConfirmed" :id="`confirm-${key}`">
                                            <label :for="`confirm-${key}`">Confirm</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-9">
                                <div class="table-responsive disable-responsive" style="min-height: 400px">
                                    <table class="table select-rows">
                                        <thead>
                                        <tr>
                                            <th class="" style="padding-left: 5px">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span>Remarks</span>
                                                </div>
                                            </th>
                                            <th class="pl-0">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span>WIP Location</span>
                                                </div>
                                            </th>
                                            <th class="pl-0">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span>Start Date</span>
                                                </div>
                                            </th>
                                            <th class="pl-0">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span>EDD</span>
                                                </div>
                                            </th>
                                            <th class="pl-0">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span>Parking Location</span>
                                                </div>
                                            </th>
                                            <th class="pl-0">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span>Done Date</span>
                                                </div>
                                            </th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in form.items" :key="index">
                                            <td class="pl-0">
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors[`items.${index}.remarks`] }" v-model="item.remarks" placeholder="Type Here">
                                                <div v-if="form.errors[`items.${index}.remarks`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.remarks`] }}</div>
                                            </td>
                                            <td class="pl-0">
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors[`items.${index}.wip_location`] }" v-model="item.wip_location" placeholder="Type Here">
                                                <div v-if="form.errors[`items.${index}.wip_location`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.wip_location`] }}</div>
                                            </td>
                                            <td class="pl-0">
                                                <VueDatePicker v-model="item.start_date" :class="{ 'is-invalid': form.errors[`items.${index}.start_date`] }" :enable-time-picker="false" placeholder="Select date"></VueDatePicker>
                                                <div v-if="form.errors[`items.${index}.start_date`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.start_date`] }}</div>
                                            </td>
                                            <td class="pl-0">
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors[`items.${index}.edd`] }" v-model="item.edd" placeholder="Type Here">
                                                <div v-if="form.errors[`items.${index}.edd`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.edd`] }}</div>
                                            </td>
                                            <td class="pl-0">
                                                <input class="form-control" type="text" :class="{ 'is-invalid': form.errors[`items.${index}.parking_location`] }" v-model="item.parking_location" placeholder="Type Here">
                                                <div v-if="form.errors[`items.${index}.parking_location`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.parking_location`] }}</div>
                                            </td>
                                            <td class="pl-0">
                                                <VueDatePicker v-model="item.done_date" :class="{ 'is-invalid': form.errors[`items.${index}.done_date`] }" :enable-time-picker="false" placeholder="Select date"></VueDatePicker>
                                                <div v-if="form.errors[`items.${index}.done_date`]" class="invalid-feedback d-block">{{ form.errors[`items.${index}.done_date`] }}</div>
                                            </td>
                                            <td>
                                                <a class="text-danger text-18" v-if="form.items.length > 1" @click="removeItem(index)" href="javascript:void(0)">&times;</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <a class="btn btn-purple mt-10" @click="addItem" href="javascript:void(0)">Add item</a>
                                </div>
                            </div>
                        </div>

                        <div class="btn-group mb-17 mt-25">
                            <button class="btn btn-purple" type="submit" @click="form.type = 7" :disabled="form.processing">Save Changes</button>
                            <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales/${order.rs_id}`">Discard</Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-4" v-if="form.tab === 4">
                <div class="box pt-20 px-25 pb-25 rounded-md shadow-default bg-white mt-[2.6rem]">
                    <div class="boxes">
                        <div class="d-flex flex-wrap justify-between border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-20">
                            <div class="text-18 font-medium mb-17">
                                <span>Remarks</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Remark</label>
                            <textarea class="form-control" type="text" :class="{ 'is-invalid': form.errors.remark }" v-model="form.remark" placeholder="Remark"></textarea>
                            <div v-if="form.errors.remark" class="invalid-feedback d-block">{{ form.errors.remark }}</div>
                        </div>
                        <div class="btn-group mb-17">
                            <button class="btn btn-purple" type="submit" @click="form.type = 5" :disabled="form.processing">Save Changes</button>
                            <Link class="btn btn-light btn-light--brounded" :href="`/${shipment}/refrigeration-sales/${order.rs_id}`">Discard</Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
        </form>
    </Layout>
</template>

<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onBeforeMount, onMounted, ref, watch} from "vue";

const props = defineProps({
    shipment: Object,
    csrf: String,
    order: Object,
    customers: Array,
});

const form = useForm({
    tab: 1,
    type: null,
    quotation_id: null,
    schedule_comments: null,
    vehicle_receive_date: null,
    actual_completion_date: null,
    vehicle_id: null,
    engine_serial: [],
    engine_serial_input: null,
    evaporator_serial: [],
    evaporator_serial_input: null,
    refrigeration_unit_serial: null,
    nea_serial: null,
    condenser_serial: [],
    condenser_serial_input: null,
    standby_unit: null,


    /** Vehicle **/
    license_plate: null,
    vehicle_type: null,
    customer_id: null,
    end_user: null,
    vehicle_voltage: null,
    contact_no: null,
    chassis_no: null,
    chassis_delivery: null,
    vehicle_make: null,
    model: null,
    vehicle_class: null,
    type_of_plate: null,
    warranty_mileage: null,
    warranty_end_date: null,


    /** Product work order - Remark **/
    vehicle_class_wo: null,
    vehicle_voltage_wo: null,
    unit_model: null,
    box_dimension_mm_l: null,
    box_dimension_mm_w: null,
    box_dimension_mm_h: null,
    pe_archie: null,
    pe_prema: null,
    addition_accessories: null,
    receipt_date: null,
    received_date: null,
    bracket: 'Bracket',
    production_schedule: null,
    ibox: null,
    final_assy: null,
    testing: null,
    vehicle_no: null,
    log_card: null,
    dummy_box: null,
    final_delivery: null,
    remark: null,


    /** Contractors **/
    tailgate_supplier: null,
    tailgate_completion: null,
    spray_painting_supplier: null,
    spray_painting_completion: null,
    confirmation: {
        "Attachment": {
            isSelected: false,
            isConfirmed: false,
        },
        "LTA Inspection": {
            isSelected: false,
            isConfirmed: false,
        },
        "PDI (Centre Final)": {
            isSelected: false,
            isConfirmed: false,
        },
        "Spray": {
            isSelected: false,
            isConfirmed: false,
        },
        "Logo": {
            isSelected: false,
            isConfirmed: false,
        },
        "Upper Structure": {
            isSelected: false,
            isConfirmed: false,
        },
    },
    items: [],
});

const format = (date) => {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

const itemObj = () => {
    return {
        id: null,
        remarks: null,
        wip_location: null,
        start_date: null,
        edd: null,
        parking_location: null,
        done_date: null,
    }
};

const addItem = () => {
    form.items.push(itemObj())
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}

onMounted(() => {
    if (props.order) {
        form.quotation_id = props.order.quotation_id;
        form.schedule_comments = props.order.schedule_comments;
        form.vehicle_receive_date = props.order.vehicle_receive_date;
        form.actual_completion_date = props.order.actual_completion_date;
        form.vehicle_id = props.order.vehicle_id;
        form.engine_serial = props.order.engine_serial ?? [];
        form.evaporator_serial = props.order.evaporator_serial ?? [];
        form.refrigeration_unit_serial = props.order.refrigeration_unit_serial;
        form.nea_serial = props.order.nea_serial;
        form.condenser_serial = props.order.condenser_serial ?? [];
        form.standby_unit = props.order.standby_unit;

        /** Product work order **/
        if (props.order.production_work_order) {
            form.vehicle_class_wo = props.order.production_work_order.vehicle_class_wo;
            form.vehicle_voltage_wo = props.order.production_work_order.vehicle_voltage_wo;
            form.unit_model = props.order.production_work_order.unit_model;
            form.box_dimension_mm_l = props.order.production_work_order.box_dimension_mm_l;
            form.box_dimension_mm_w = props.order.production_work_order.box_dimension_mm_w;
            form.box_dimension_mm_h = props.order.production_work_order.box_dimension_mm_h;
            form.pe_archie = props.order.production_work_order.pe_archie;
            form.pe_prema = props.order.production_work_order.pe_prema;
            form.addition_accessories = props.order.production_work_order.addition_accessories;
            form.receipt_date = props.order.production_work_order.receipt_date;
            form.received_date = props.order.production_work_order.received_date;
            form.bracket = props.order.production_work_order.bracket;
            form.production_schedule = props.order.production_work_order.production_schedule;
            form.ibox = props.order.production_work_order.ibox;
            form.final_assy = props.order.production_work_order.final_assy;
            form.testing = props.order.production_work_order.testing;
            form.vehicle_no = props.order.production_work_order.vehicle_no;
            form.log_card = props.order.production_work_order.log_card;
            form.dummy_box = props.order.production_work_order.dummy_box;
            form.final_delivery = props.order.production_work_order.final_delivery;
            form.remark = props.order.production_work_order.remark;
        }

        /** Contractor **/
        form.items = [itemObj()]
        if (props.order.contractor) {
            form.tailgate_supplier = props.order.contractor.tailgate_supplier;
            form.tailgate_completion = props.order.contractor.tailgate_completion;
            form.spray_painting_supplier = props.order.contractor.spray_painting_supplier;
            form.spray_painting_completion = props.order.contractor.spray_painting_completion;
            form.confirmation = props.order.contractor.confirmation;

            if (props.order.contractor.items) {
                form.items = props.order.contractor.items;
            }
        }
    }
});

onBeforeMount(() => {
    console.log(props.order)
})
</script>
