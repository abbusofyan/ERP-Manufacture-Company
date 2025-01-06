<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Incoming Goods</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li class="active"><span>Incoming Goods</span></li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="fc">
                <div class="fc-header-toolbar fc-toolbar fc-toolbar-ltr">
                    <div class="fc-toolbar-chunk">
                        <div class="">
                            <Link :href="getPreviousWeekUrl" title="Previous week" aria-pressed="false" class="fc-prev-button fc-button fc-button-primary">
                                <span class="fc-icon fc-icon-chevron-left"></span>
                            </Link>
                            <Link :href="getNextWeekUrl" title="Next week" aria-pressed="false" class="fc-next-button fc-button fc-button-primary">
                                <span class="fc-icon fc-icon-chevron-right"></span>
                            </Link>
                            <h2 class="fc-toolbar-title" id="fc-dom-1">{{ title }}</h2></div>
                    </div>
                    <div class="fc-toolbar-chunk"></div>
                    <div class="fc-toolbar-chunk">
                        <div class="fc-button-group">
                            <Link href="?type=month" title="Month view" aria-pressed="true" class="fc-dayGridMonth-button fc-button fc-button-primary" style="border-bottom-right-radius: 0; border-top-right-radius: 0; border-right: 0">Month</Link>
                            <Link href="?type=week" title="Week view" aria-pressed="false" class="fc-timeGridWeek-button fc-button fc-button-primary fc-button-active" style="border-radius: 0">Week</Link>
                            <Link href="?type=list" title="List week view" aria-pressed="false" class="fc-listWeek-button fc-button fc-button-primary" style="border-bottom-left-radius: 0; border-top-left-radius: 0; border-left: 0">List</Link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="incoming-calendar-wrapper">
                <table>
                    <thead>
                    <tr>
                        <td v-for="day in form.daysOfWeek" :key="day">{{ day }}</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(week, index) in form.calendar" :key="index">
                        <td v-for="day in week" :key="day.date" class="h-auto" :class="{ 'current-month': day.isCurrentMonth, 'current-date': day.isCurrentDate }">
                            <div class="item-wrapper">
                                <div class="content">
                                    <div class="date" :class="!day.isCurrentMonth ? 'disabled' : ''">
                                        {{ day.date }}
                                    </div>
                                    <div class="orders" v-if="day.isCurrentMonth">
                                        <!-- Show button -->
                                        <div v-for="(order, index) in day.orders" :key="order.id">
                                            <a class="name d-block" :class="{ 'red': day.isPastDate }" :href="`#popup-incoming-${index}`" data-fancybox>
                                                {{ order.vendor.name }}
                                            </a>
                                        </div>

                                        <!-- Show popup -->
                                        <div v-for="(order, index) in day.orders" :key="order.id">
                                            <div class="popup-el w-full max-w-650" :id="`popup-incoming-${index}`" style="display: none">
                                                <div class="inner">
                                                    <div class="head">
                                                        <div class="title-md text-uppercase">{{ order.vendor.name }}</div>
                                                    </div>
                                                    <div class="text-danger mt-1">
                                                        {{ $filters.formatDate(order.edd) }}
                                                    </div>
                                                    <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
                                                    <div class="text-primary text-bold mb-10">
                                                        Item ID
                                                    </div>
                                                    <template v-for="(item, index) in order.items" :key="index">
                                                        <p>{{ item.product_name }}</p>
                                                    </template>
                                                    <div class="btn-group mt-[2.6rem]">
                                                        <a class="btn btn-purple" href="javascript:void(0)" @click="closePopup" type="submit">Close</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="notes mt-[5rem] pb-[5.6rem]">
                    <div class="box red">
                        <span></span>
                        Pass Delivery Date Item
                    </div>
                    <div class="box green">
                        <span></span>
                        Upcoming Date item
                    </div>
                </div>
                <div></div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed, onMounted} from "vue";
import {usePage, Link, useForm} from "@inertiajs/inertia-vue3";
import Layout from "../../../Components/Layout.vue";

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const props = defineProps({
    orders: Object,
    title: Object,
    startDate: Object,
    endDate: Object,
});

const form = useForm({
    daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    calendar: [],
});

const generateCalendar = () => {
    const startDate = new Date(props.startDate);
    const endDate = new Date(props.endDate);

    const currentDate = new Date();

    const currentMonth = startDate.getMonth();
    const currentYear = startDate.getFullYear();

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    const calendar = [];

    while (startDate <= endDate) {
        const week = [];

        for (let i = 0; i < 7; i++) {
            if (startDate >= firstDayOfMonth && startDate <= lastDayOfMonth) {
                const formattedDate = startDate.toLocaleDateString('en-GB', {day: '2-digit', month: '2-digit', year: 'numeric'});

                const stringStartDate = startDate.toISOString().split('T')[0];
                const stringCurrentDate = currentDate.toISOString().split('T')[0];

                const isPastDate = stringStartDate < stringCurrentDate;

                week.push({
                    date: startDate.getDate(),
                    formattedDate: formattedDate,
                    isCurrentMonth: startDate.getMonth() === currentMonth,
                    isCurrentDate: startDate.getMonth() === currentDate.getMonth() && startDate.getDate() === currentDate.getDate() && startDate.getFullYear() === currentDate.getFullYear(),
                    orders: getOrdersForDate(startDate.getDate()),
                    showFull: false,
                    isPastDate: isPastDate,
                });
            } else {
                week.push({
                    empty: true,
                });
            }

            startDate.setDate(startDate.getDate() + 1);
        }

        calendar.push(week);
    }

    form.calendar = calendar;
};

const getOrdersForDate = (date) => {
    return props.orders.filter(order => {
        const orderDate = new Date(order.edd);
        return orderDate.getDate() === date;
    });
};


const closePopup = () => {
    $.fancybox.close()
};

const getPreviousWeekUrl = computed(() => {
    const currentStartDate = new Date(props.startDate);
    const previousWeekStartDate = new Date(currentStartDate);
    previousWeekStartDate.setDate(currentStartDate.getDate() - 7);

    const previousWeekEndDate = new Date(previousWeekStartDate);
    previousWeekEndDate.setDate(previousWeekEndDate.getDate() + 6);

    return `?type=week&startDate=${previousWeekStartDate.toISOString().split('T')[0]}&endDate=${previousWeekEndDate.toISOString().split('T')[0]}`;
});

const getNextWeekUrl = computed(() => {
    const currentEndDate = new Date(props.endDate);
    const nextWeekEndDate = new Date(currentEndDate);
    nextWeekEndDate.setDate(currentEndDate.getDate() + 7);

    const nextWeekStartDate = new Date(nextWeekEndDate);
    nextWeekStartDate.setDate(nextWeekStartDate.getDate() - 6);

    return `?type=week&startDate=${nextWeekStartDate.toISOString().split('T')[0]}&endDate=${nextWeekEndDate.toISOString().split('T')[0]}`;
});


onMounted(() => {
    generateCalendar()
})
</script>
