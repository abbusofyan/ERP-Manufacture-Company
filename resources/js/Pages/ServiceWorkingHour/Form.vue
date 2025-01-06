<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div v-if="workingHour" class="big-title">Edit Working Hour</div>
            <div v-else class="big-title">Create New Working Hour</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/services">Services</Link>
                    </li>
                    <li>
                        <Link href="/service-working-hours">Working Hours</Link>
                    </li>
                    <li>
                        <span v-if="workingHour">Edit Working Hour</span>
                        <span v-else>Create Working Hour</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box pt-20 px-25 pb-[5.6rem] rounded-md shadow-default bg-white">
            <div class="form-wrap">
                <form @submit.prevent="handleSubmit">
                    <div class="boxes">
                        <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem]">
                            <div class="text-18 font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                                <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1</span>
                                <span>Working Hour Information</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-[7.7rem]">
                                <div class="form-group">
                                    <label>Start Time<span class="required">*</span></label>
                                    <VueDatePicker time-picker v-model="form.start_time" :max-time="form.end_time" format="HH:mm"></VueDatePicker>
                                    <div v-if="form.errors.start_time" class="invalid-feedback d-block">{{ form.errors.start_time }}</div>
                                </div>
                                <div class="form-group">
                                    <label>End Time<span class="required">*</span></label>
                                    <VueDatePicker time-picker v-model="form.end_time" :min-time="form.start_time" format="HH:mm"></VueDatePicker>
                                    <div v-if="form.errors.end_time" class="invalid-feedback d-block">{{ form.errors.end_time }}</div>
                                </div>
                            </div>

                            <!-- Default, Lunch Time, Dinner Time Fields -->
                            <div class="grid md:grid-cols-3 gap-[7.7rem]">
                                <!-- Default Checkbox -->
                                <div class="form-group" v-if="!hasDefault || form.is_default">
                                    <label>Default</label>
                                    <div class="custom-checkbox style-1">
                                        <input type="checkbox" id="is-default" v-model="form.is_default" :disabled="hasDefault && workingHour.is_default">
                                        <label for="is-default">Set as Default Working Hour</label>
                                    </div>
                                    <div v-if="form.errors.is_default" class="invalid-feedback d-block">{{ form.errors.is_default }}</div>
                                </div>

                                <!-- Lunch Time Checkbox -->
                                <div class="form-group" v-if="!hasLunchTime || form.is_lunch_time">
                                    <label>Lunch Time</label>
                                    <div class="custom-checkbox style-1">
                                        <input type="checkbox" id="is-lunch" v-model="form.is_lunch_time" :disabled="hasLunchTime && workingHour.is_lunch_time">
                                        <label for="is-lunch">Set as Lunch Time</label>
                                    </div>
                                    <div v-if="form.errors.is_lunch_time" class="invalid-feedback d-block">{{ form.errors.is_lunch_time }}</div>
                                </div>

                                <!-- Dinner Time Checkbox -->
                                <div class="form-group" v-if="!hasDinnerTime || form.is_dinner_time">
                                    <label>Dinner Time</label>
                                    <div class="custom-checkbox style-1">
                                        <input type="checkbox" id="is-dinner" v-model="form.is_dinner_time" :disabled="hasDinnerTime && workingHour.is_dinner_time">
                                        <label for="is-dinner">Set as Dinner Time</label>
                                    </div>
                                    <div v-if="form.errors.is_dinner_time" class="invalid-feedback d-block">{{ form.errors.is_dinner_time }}</div>
                                </div>
                            </div>

                            <!-- Specific Date Field -->
                            <div class="form-group" v-if="!form.is_default && !form.is_lunch_time && !form.is_dinner_time">
                                <label>Specific Date<span class="required">*</span></label>
                                <VueDatePicker
                                    v-model="form.specific_date"
                                    :class="{ 'is-invalid': form.errors.specific_date }"
                                    placeholder="Select Complete Date"
                                    :enable-time-picker="false"
                                    :format="$filters.format"
                                />
                                <div v-if="form.errors.specific_date" class="invalid-feedback d-block">{{ form.errors.specific_date }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group mt-[5.6rem]">
                        <button class="btn btn-purple" v-if="workingHour" type="submit" :disabled="form.processing">Update Working Hour</button>
                        <button class="btn btn-purple" v-else type="submit" :disabled="form.processing">Create Working Hour</button>
                        <Link class="btn btn-light btn-light--brounded" href="/service-working-hours">Discard</Link>
                    </div>
                    <div v-if="Object.keys(form.errors).length > 0" class="invalid-feedback d-block">Error! Missing or Invalid Data.</div>
                </form>
            </div>
        </div>
    </Layout>
</template>
<script setup>
import Layout from "../../Components/Layout.vue";
import {useForm, Link} from "@inertiajs/inertia-vue3";
import {onMounted, ref, watch} from "vue";
import Swal from "sweetalert2";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    date: Boolean,
    default: Boolean,
    hasDefault: Boolean,
    hasLunchTime: Boolean,
    hasDinnerTime: Boolean,
    workingHour: Object,
});

const form = useForm({
    start_time: null,
    end_time: null,
    specific_date: null,
    is_default: false,
    is_lunch_time: false,
    is_dinner_time: false,
});

onMounted(() => {
    if (props.workingHour) {
        form.start_time = timeStringToObject(props.workingHour.start_time);
        form.end_time = timeStringToObject(props.workingHour.end_time);
        form.specific_date = props.workingHour.specific_date;
        form.is_default = !!props.workingHour.is_default;
        form.is_lunch_time = !!props.workingHour.is_lunch_time;
        form.is_dinner_time = !!props.workingHour.is_dinner_time;
    } else if (props.date) {
        form.specific_date = props.date;

        if (props.default) {
            form.start_time = timeStringToObject(props.default.start_time);
            form.end_time = timeStringToObject(props.default.end_time);
        }
    }
});

// Consolidated watcher to ensure only one checkbox is selected
watch(
    () => form.is_default,
    (newVal) => {
        if (newVal) {
            form.is_lunch_time = false;
            form.is_dinner_time = false;
            form.specific_date = null;
        }
    },
    {deep: true}
);

watch(
    () => form.is_lunch_time,
    (newVal) => {
        if (newVal) {
            form.is_default = false;
            form.is_dinner_time = false;
            form.specific_date = null;
        }
    },
    {deep: true}
);

watch(
    () => form.is_dinner_time,
    (newVal) => {
        if (newVal) {
            form.is_default = false;
            form.is_lunch_time = false;
            form.specific_date = null;
        }
    },
    {deep: true}
);


const timeStringToObject = timeString => {
    const [hours, minutes, seconds] = timeString.split(':').map(Number);
    return {hours, minutes, seconds};
};

const validateTime = () => {
    const startTotalMinutes = form.start_time.hours * 60 + form.start_time.minutes;
    const endTotalMinutes = form.end_time.hours * 60 + form.end_time.minutes;

    if (endTotalMinutes <= startTotalMinutes) {
        form.errors.end_time = 'End time must be greater than start time';
        return false;
    }
    return true;
};

const handleSubmit = () => {
    if (validateTime()) {
        if (props.workingHour) {
            form.put(`/service-working-hours/${props.workingHour.id}`);
        } else {
            form.post('/service-working-hours');
        }
    } else {
        Swal.fire({
            title: 'Error',
            text: 'End time must be greater than start time',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};
</script>

