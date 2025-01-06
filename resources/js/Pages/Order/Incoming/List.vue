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
                            <Link :href="getPreviousMonthUrl" title="Previous month" aria-pressed="false" class="fc-prev-button fc-button fc-button-primary">
                                <span class="fc-icon fc-icon-chevron-left"></span>
                            </Link>
                            <Link :href="getNextMonthUrl" title="Next month" aria-pressed="false" class="fc-next-button fc-button fc-button-primary">
                                <span class="fc-icon fc-icon-chevron-right"></span>
                            </Link>
                            <h2 class="fc-toolbar-title" id="fc-dom-1">{{ title }}</h2></div>
                    </div>
                    <div class="fc-toolbar-chunk"></div>
                    <div class="fc-toolbar-chunk">
                        <div class="fc-button-group">
                            <Link href="?type=month" title="Month view" aria-pressed="true" class="fc-dayGridMonth-button fc-button fc-button-primary" style="border-bottom-right-radius: 0; border-top-right-radius: 0; border-right: 0">Month</Link>
                            <Link href="?type=week" title="Week view" aria-pressed="false" class="fc-timeGridWeek-button fc-button fc-button-primary" style="border-radius: 0">Week</Link>
                            <Link href="?type=list" title="List week view" aria-pressed="false" class="fc-listWeek-button fc-button fc-button-primary fc-button-active" style="border-bottom-left-radius: 0; border-top-left-radius: 0; border-left: 0">List</Link>
                        </div>
                    </div>
                </div>
            </div>
            <template v-for="(week, index) in form.calendar" :key="index">
                <template v-for="day in week" :key="day.date">
                    <div class="table-responsive pb-0">
                        <table v-if="day.orders.length > 0" class="table">
                            <thead>
                            <th>
                                {{ day.formattedDate }}
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Item ID</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Item Name</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Location</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Price</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Quantity</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            <th>
                                <div class="flex items-center justify-between gap-1"><span>Amount</span><span class="sort-ic"><img src="../../../../assets/img/sort.svg" alt="sort"></span></div>
                            </th>
                            </thead>
                            <tbody>
                            <template v-for="order in day.orders" :key="order.id">
                                <tr v-for="item in order.items" :key="item.id">
                                    <td></td>
                                    <td>{{ item.product ? item.product.sku : '' }}</td>
                                    <td>{{ item.product_name }}</td>
                                    <td></td>
                                    <td>${{ item.price ?? 0 }}</td>
                                    <td>{{ item.quantity }}</td>
                                    <td>${{ (item.quantity * item.price).toFixed(2) }}</td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </template>
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
    year: Object,
    month: Object,
});

const form = useForm({
    daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    calendar: [],
});

const generateCalendar = () => {
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);

    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    const startDate = new Date(firstDayOfMonth);
    startDate.setDate(startDate.getDate() - startDate.getDay());

    const calendar = [];

    while (startDate <= lastDayOfMonth) {
        const week = [];

        for (let i = 0; i < 7; i++) {
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

            startDate.setDate(startDate.getDate() + 1);
        }

        calendar.push(week);
    }

    form.calendar = calendar;
}

const getOrdersForDate = (date) => {
    return props.orders.filter(order => {
        const orderDate = new Date(order.edd);
        return orderDate.getDate() === date;
    });
};


const closePopup = () => {
    $.fancybox.close()
};

const getPreviousMonthUrl = computed(() => {
    const currentMonth = Number(props.month);
    const currentYear = Number(props.year);

    const previousMonth = currentMonth === 1 ? 12 : currentMonth - 1;
    const previousYear = currentMonth === 1 ? currentYear - 1 : currentYear;

    return `?type=list&month=${previousMonth}&year=${previousYear}`;
});

const getNextMonthUrl = computed(() => {
    const currentMonth = Number(props.month);
    const currentYear = Number(props.year);

    const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1;
    const nextYear = currentMonth === 12 ? currentYear + 1 : currentYear;

    return `?type=list&month=${nextMonth}&year=${nextYear}`;
});


onMounted(() => {
    generateCalendar()
})
</script>
