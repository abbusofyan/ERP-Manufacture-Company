<template>
    <Layout>
        <div class="flex flex-wrap items-center gap-13 mb-26">
            <div class="big-title">Project Orders Schedule</div>
            <span class="h-[2.6rem] w-[1px] bg-[#EBE9F1] block"></span>
            <nav class="breadcrumbs">
                <ol>
                    <li>
                        <Link href="/"><span class="icon"><img src="../../../../assets/img/home.svg" alt="home"></span></Link>
                    </li>
                    <li>
                        <Link href="/project-appointments">Project Order</Link>
                    </li>
                    <li>
                        <span>Schedule</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="box rounded-md shadow-default bg-white">
            <div class="flex flex-wrap gap-16 justify-between py-20 px-25 gap-1">
                <label class="d-flex align-items-center gap-1">Show
                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select" v-model="paginate">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </label>
                <div class="flex flex-wrap gap-16 justify-between">
                    <div class="search-el ml-auto">
                        <div class="txt">Search</div>
                        <div class="form">
                            <input type="search" placeholder="Search" v-model="search">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="table-responsive pb-0" style="min-height: auto">
                        <table class="table select-rows" style="min-width: auto">
                            <thead>
                            <tr>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>No.</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Vehicle No.</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center justify-between gap-1">
                                        <span>Delivery Date</span>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr @click="setOrderId(order)" class="cursor-pointer" v-for="order in projectOrders.data" :class="{ 'row-active' : order.id == project_order_id }" :key="order.id">
                                <td class="text-primary">{{ order.code }}</td>
                                <td>{{ order.vehicle.license_plate }}</td>
                                <td>{{ $filters.formatDate(order.delivery_date) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-column items-center justify-between gap-26 mt-25 pb-25 px-25">
                        <p>Showing {{ projectOrders.from }} to {{ projectOrders.to }} of {{ projectOrders.total }} entries</p>
                        <Pagination :links="projectOrders.links"/>
                    </div>
                </div>
                <div class="col-lg-8">
                    <!-- Date Navigation -->
                    <div class="date-navigation">
                        <button @click="prevDate" :disabled="!hasPrevDate"><i class="fa fa-angle-left"></i></button>
                        <span class="text-primary"><strong>{{ formattedSelectedDate }}</strong></span>
                        <button @click="nextDate" :disabled="!hasNextDate"><i class="fa fa-angle-right"></i></button>
                    </div>
                    <!-- Timeline Display -->
                    <div class="timeline-container table-responsive pb-0" style="min-height: unset;">
                        <table class="table timeline-table">
                            <thead>
                            <tr>
                                <th class="cell-sticky-left">
                                    <div class="wrapper">Process</div>
                                </th>
                                <th></th>
                                <th v-for="slot in timeSlots" :key="slot.time">
                                    <span class="time-slot-title">{{ slot.label }}</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="process in processedProcesses" :key="process.id">
                                <!-- Estimated Timeline Row -->
                                <tr class="estimated-row">
                                    <td v-if="isFirstRow(process)" :rowspan="2" class="process-name cell-sticky-left">
                                        <div class="wrapper text-nowrap">
                                            {{ process.name }}
                                        </div>
                                    </td>
                                    <td></td>
                                    <td
                                        v-for="(slotBars, index) in process.estimatedSlots"
                                        :key="'estimated-' + index"
                                        class="timeline-cell"
                                    >
                                        <div
                                            v-for="bar in slotBars"
                                            :key="'estimated-' + bar.leftPercentage + '-' + bar.widthPercentage"
                                            class="timeline-bar estimated-bar"
                                            :style="{
                                                    left: bar.leftPercentage + '%',
                                                    width: bar.widthPercentage + '%',
                                                    backgroundColor: bar.color,
                                                    zIndex: bar.color === 'blue' ? 2 : 1,
                                                }"
                                        ></div>
                                    </td>
                                </tr>
                                <!-- Actual Timeline Row -->
                                <tr class="actual-row">
                                    <td></td>
                                    <td
                                        v-for="(slotBars, index) in process.actualSlots"
                                        :key="'actual-' + index"
                                        class="timeline-cell"
                                    >
                                        <div
                                            v-for="bar in slotBars"
                                            :key="'actual-' + bar.leftPercentage + '-' + bar.widthPercentage"
                                            class="timeline-bar actual-bar"
                                            :style="{
                                                    left: bar.leftPercentage + '%',
                                                    width: bar.widthPercentage + '%',
                                                    backgroundColor: bar.color,
                                                    zIndex: bar.color === 'green' ? 2 : 1,
                                                }"
                                        ></div>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="row px-20 pt-30 pb-30">
                        <div class="col-lg-3">
                            <div style="width: 50px; height: 10px; background-color: #6C48C5"></div>
                            <p><i>Standard Time</i></p>
                        </div>
                        <div class="col-lg-3">
                            <div style="width: 50px; height: 10px; background-color: rgb(106, 156, 137)"></div>
                            <p><i>Working Time</i></p>
                        </div>
                        <div class="col-lg-3">
                            <div style="width: 50px; height: 10px; background-color: rgb(160, 71, 71)"></div>
                            <p><i>OT Time</i></p>
                        </div>
                        <div class="col-lg-3">
                            <div style="width: 50px; height: 10px; background-color: rgb(255, 220, 127)"></div>
                            <p><i>Break Time</i></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref, watch, computed} from 'vue';
import {usePage, Link} from '@inertiajs/inertia-vue3';
import Layout from '../../../Components/Layout.vue';
import Pagination from '../../../Components/Pagination.vue';
import debounce from 'lodash.debounce';
import {Inertia} from '@inertiajs/inertia';

const permissions = usePage().props.value.auth.user.permissions;

const props = defineProps({
    projectOrders: Object,
    projectOrder: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let order = ref(props.filters.order);
let by = ref(props.filters.by);
let paginate = ref(props.filters.paginate);
const project_order_id = ref(props.filters.project_order_id);

// Extract all unique dates from timelogs
const timelogDates = computed(() => {
    if (!props.projectOrder || !props.projectOrder.project_order_process) {
        return [];
    }

    const datesSet = new Set();

    props.projectOrder.project_order_process.forEach(process => {
        process.timelogs.forEach(timelog => {
            const dateStr = timelog.start_time.split(' ')[0];
            datesSet.add(dateStr);
        });
    });

    // Convert to array and sort
    const datesArray = Array.from(datesSet).sort();
    return datesArray;
});

// Selected date
const selectedDate = ref(new Date(timelogDates.value[0])); // Initialize with the earliest date in timelogs

// Update selectedDate when timelogDates change
watch(timelogDates, (newDates) => {
    if (newDates.length > 0) {
        selectedDate.value = new Date(newDates[0]);
    } else {
        selectedDate.value = new Date(); // Fallback to today if no dates available
    }
});

// Formatted selected date for display
const formattedSelectedDate = computed(() => {
    return selectedDate.value.toLocaleDateString();
});

// Functions to change date
const prevDate = () => {
    const index = timelogDates.value.findIndex(dateStr => dateStr === selectedDateString.value);
    if (index > 0) {
        selectedDate.value = new Date(timelogDates.value[index - 1]);
    }
};

const nextDate = () => {
    const index = timelogDates.value.findIndex(dateStr => dateStr === selectedDateString.value);
    if (index < timelogDates.value.length - 1) {
        selectedDate.value = new Date(timelogDates.value[index + 1]);
    }
};

// Computed properties to disable buttons if no previous or next date
const hasPrevDate = computed(() => {
    const index = timelogDates.value.findIndex(dateStr => dateStr === selectedDateString.value);
    return index > 0;
});

const hasNextDate = computed(() => {
    const index = timelogDates.value.findIndex(dateStr => dateStr === selectedDateString.value);
    return index < timelogDates.value.length - 1;
});

// Computed string of selectedDate in 'YYYY-MM-DD' format
const selectedDateString = computed(() => {
    const year = selectedDate.value.getFullYear();
    const month = (selectedDate.value.getMonth() + 1).toString().padStart(2, '0');
    const day = selectedDate.value.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
});

const filter = () => {
    Inertia.get(
        '/project-orders-schedule',
        {
            search: search.value,
            order: order.value,
            by: by.value,
            paginate: paginate.value,
            project_order_id: project_order_id.value,
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

watch(
    [() => order.value, () => by.value, () => paginate.value, () => project_order_id.value],
    () => {
        filter();
    }
);

const sort = (field) => {
    order.value = field;

    if (by.value === 'asc') {
        by.value = 'desc';
    } else {
        by.value = 'asc';
    }
};

const setOrderId = (order) => {
    project_order_id.value = order.id;
};

// Timeline computations
const timeSlotInterval = 30; // in minutes
const timeSlotWidth = 70; // in pixels

function roundDownToNearest30(minutes) {
    return minutes - (minutes % 30);
}

function roundUpToNearest30(minutes) {
    return minutes % 30 === 0 ? minutes : minutes + (30 - (minutes % 30));
}

// Computed start and end times based on timelogs
const startOfDay = computed(() => {
    let earliestStart = null;

    if (!props.projectOrder || !props.projectOrder.project_order_process) {
        return 7 * 60 + 30; // default to 7:30 AM
    }

    props.projectOrder.project_order_process.forEach(process => {
        process.timelogs.forEach(timelog => {
            const timelogDate = timelog.start_time.split(' ')[0];
            if (timelogDate !== selectedDateString.value) return;

            const startTime = new Date(timelog.start_time.replace(' ', 'T'));
            const startMinutes = startTime.getHours() * 60 + startTime.getMinutes();

            if (earliestStart === null || startMinutes < earliestStart) {
                earliestStart = startMinutes;
            }
        });
    });

    const defaultStart = 7 * 60 + 30;
    const adjustedStart = earliestStart !== null ? Math.min(earliestStart, defaultStart) : defaultStart;
    return roundDownToNearest30(adjustedStart);
});

const endOfDay = computed(() => {
    let latestEnd = null;

    if (!props.projectOrder || !props.projectOrder.project_order_process) {
        return 21 * 60 + 30; // default to 9:30 PM
    }

    props.projectOrder.project_order_process.forEach(process => {
        process.timelogs.forEach(timelog => {
            const timelogDate = timelog.end_time.split(' ')[0];
            if (timelogDate !== selectedDateString.value) return;

            const endTime = new Date(timelog.end_time.replace(' ', 'T'));
            const endMinutes = endTime.getHours() * 60 + endTime.getMinutes();

            if (latestEnd === null || endMinutes > latestEnd) {
                latestEnd = endMinutes;
            }
        });
    });

    const defaultEnd = 21 * 60 + 30;
    const adjustedEnd = latestEnd !== null ? Math.max(latestEnd, defaultEnd) : defaultEnd;
    return roundUpToNearest30(adjustedEnd);
});

const timeSlots = computed(() => {
    const slots = [];
    let index = 0;
    const start = startOfDay.value;
    const end = endOfDay.value;
    for (let time = start; time < end; time += timeSlotInterval) {
        const hours = Math.floor(time / 60);
        const minutes = time % 60;
        const label = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        slots.push({
            time, // minutes since midnight
            label,
            index,
        });
        index++;
    }
    return slots;
});

const processedProcesses = computed(() => {
    if (!props.projectOrder || !props.projectOrder.project_order_process) {
        return [];
    }

    const selectedDateStr = selectedDateString.value;
    const start = startOfDay.value;

    return props.projectOrder.project_order_process
        .map((process) => {
            // Filter timelogs for the selected date
            const timelogs = process.timelogs
                .filter((timelog) => {
                    const timelogDate = timelog.start_time.split(' ')[0];
                    return timelogDate === selectedDateStr;
                })
                .sort((a, b) => new Date(a.start_time) - new Date(b.start_time));

            if (timelogs.length === 0) {
                return null;
            }

            // Build actual slots array
            const actualSlots = timeSlots.value.map(slot => {
                return []; // Each slot is an array of bars
            });

            let activePeriods = []; // To store active periods

            timelogs.forEach((timelog, index) => {
                const startTime = new Date(timelog.start_time.replace(' ', 'T'));
                const endTime = new Date(timelog.end_time.replace(' ', 'T'));

                const startMinutes = startTime.getHours() * 60 + startTime.getMinutes();
                const endMinutes = endTime.getHours() * 60 + endTime.getMinutes();

                // Store active period
                activePeriods.push({ startMinutes, endMinutes, type: timelog.type });

                // Calculate break between previous and current timelog (except for the first timelog)
                if (index > 0) {
                    const previousEnd = activePeriods[index - 1].endMinutes;
                    const currentStart = activePeriods[index].startMinutes;

                    if (currentStart > previousEnd) {
                        // Break time exists, mark it in yellow
                        for (let j = 0; j < timeSlots.value.length; j++) {
                            const slotStart = start + j * timeSlotInterval;
                            const slotEnd = slotStart + timeSlotInterval;

                            const overlapStart = Math.max(previousEnd, slotStart);
                            const overlapEnd = Math.min(currentStart, slotEnd);
                            const overlap = Math.max(0, overlapEnd - overlapStart);

                            if (overlap > 0) {
                                const barStartPercentage = ((overlapStart - slotStart) / timeSlotInterval) * 100;
                                const barWidthPercentage = (overlap / timeSlotInterval) * 100;

                                actualSlots[j].push({
                                    leftPercentage: barStartPercentage,
                                    widthPercentage: barWidthPercentage,
                                    color: '#FFDC7F',
                                    type: timelog.type,
                                });
                            }
                        }
                    }
                }
            });

            // Process actual active periods
            activePeriods.forEach((period) => {
                for (let j = 0; j < timeSlots.value.length; j++) {
                    const slotStart = start + j * timeSlotInterval;
                    const slotEnd = slotStart + timeSlotInterval;

                    // Calculate overlap between active period and timeslot
                    const overlapStart = Math.max(period.startMinutes, slotStart);
                    const overlapEnd = Math.min(period.endMinutes, slotEnd);
                    const overlap = Math.max(0, overlapEnd - overlapStart);

                    if (overlap > 0) {
                        const barStartPercentage = ((overlapStart - slotStart) / timeSlotInterval) * 100;
                        const barWidthPercentage = (overlap / timeSlotInterval) * 100;

                        actualSlots[j].push({
                            leftPercentage: barStartPercentage,
                            widthPercentage: barWidthPercentage,
                            color: period.type === 'ot' ? '#A04747' : '#6A9C89',
                            type: period.type,
                        });
                    }
                }
            });

            // **Adjust estimated timeline start to match the earliest working (non-overtime) time**
            let estimatedStartMinutes = null;
            const firstWorkingPeriod = activePeriods.find(period => period.type === 'working');

            if (firstWorkingPeriod) {
                estimatedStartMinutes = firstWorkingPeriod.startMinutes; // Set to first working start time
            } else {
                estimatedStartMinutes = start; // Fallback to timeslot start if no working period exists
            }

            const estimatedEndMinutes = estimatedStartMinutes + (process.standard_time * 60 || 60); // Convert hours to minutes

            // Build estimated slots array
            const estimatedSlots = timeSlots.value.map(slot => {
                return []; // Each slot is an array of bars
            });

            for (let j = 0; j < timeSlots.value.length; j++) {
                const slotStart = start + j * timeSlotInterval;
                const slotEnd = slotStart + timeSlotInterval;

                // Calculate overlap between estimated period and timeslot
                const overlapStart = Math.max(estimatedStartMinutes, slotStart);
                const overlapEnd = Math.min(estimatedEndMinutes, slotEnd);
                const overlap = Math.max(0, overlapEnd - overlapStart);

                if (overlap > 0) {
                    const barStartPercentage = ((overlapStart - slotStart) / timeSlotInterval) * 100;
                    const barWidthPercentage = (overlap / timeSlotInterval) * 100;

                    estimatedSlots[j].push({
                        leftPercentage: barStartPercentage,
                        widthPercentage: barWidthPercentage,
                        color: '#6C48C5', // Estimated time color
                    });
                }
            }

            return {
                ...process,
                actualSlots,
                estimatedSlots,
            };
        })
        // Filter out processes with no timelogs on the selected date
        .filter(process => process !== null);
});

// Helper to determine if it's the first row for the process
const isFirstRow = (process) => {
    return true; // Always render the process name in the first row
};
</script>

<style scoped>
.timeline-container {
    width: 100%;
    overflow-x: auto;
}

.timeline-table {
    width: auto;
    border-collapse: collapse;
    table-layout: fixed;
}

.timeline-table th,
.timeline-table td {
    text-align: center;
    padding: 0;
    min-width: 70px;
    width: 70px;
    position: relative;
}

.cell-sticky-left {
    position: sticky;
    left: 0;
    z-index: 1;
    min-width: 120px;
    width: 120px;
}

.process-name {
    text-align: left;
    padding-left: 5px;
}

.timeline-cell {
    padding: 0;
    overflow: hidden;
    position: relative; /* Ensure that timeline-bar is positioned relative to the cell */
}

.timeline-bar {
    position: absolute;
    top: 0;
    bottom: 0;
    height: 100%;
}

.date-navigation {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.date-navigation button {
    margin: 0 10px;
    padding: 5px 10px;
}

.wrapper {
    width: 100%;
}
</style>
