import './bootstrap';
import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/inertia-vue3'
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";
import moment from "moment";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

import '../assets/vendor/fonts/fontawesome.css';
import '../assets/vendor/fonts/tabler-icons.css';
import '../assets/vendor/fonts/flag-icons.css';
import '../assets/css/demo.css';
import '../assets/css/atomic.min.css';
import '../assets/css/core.min.css';
import '../assets/css/main.css';
import '../assets/css/dev.css';

import '../assets/vendor/css/core-dark.css';
import '../assets/vendor/js/helpers.js';
import '../assets/js/config.js';
import '../assets/vendor/libs/jquery/jquery.js';
import '../assets/vendor/libs/popper/popper.js';
import '../assets/vendor/js/bootstrap.js';
import '../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js';
import '../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js';
import '../assets/vendor/libs/node-waves/node-waves.js';
import '../assets/vendor/libs/hammer/hammer.js';
import '../assets/vendor/libs/i18n/i18n.js';
import '../assets/vendor/libs/typeahead-js/typeahead.js';
import '../assets/vendor/js/menu.js';
import '../assets/js/select2.full';
import '../assets/js/core.min.js';
import '../assets/js/main.js';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

import Swal from "sweetalert2";
import Select2 from "../js/Components/Select2.vue";
import FsLightbox from "fslightbox-vue/v3";


// Import CSS files
import 'tui-image-editor/dist/tui-image-editor.css';

const options = {
    position: "top-right", timeout: 15000, closeOnClick: true, pauseOnFocusLoss: true, pauseOnHover: true, draggable: true, draggablePercent: 0.6, showCloseButtonOnHover: false, closeButton: "button", newestOnTop: true, filterBeforeCreate: (toast, toasts) => {
        if (toasts.filter(t => t.content === toast.content).length !== 0) {
            return false;
        }

        return toast;
    }
};

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ), setup({el, App, props, plugin}) {
        const app = createApp({render: () => h(App, props)})
            .use(plugin)
            .use(Toast);

        app.config.globalProperties.$filters = {
            formatDateTime(value) {
                if (value) {
                    return moment(String(value)).format("DD-MM-YY hh:mm A");
                } else {
                    return '-';
                }
            }, formatTime(value) {
                if (value) {
                    return moment(String(value)).format("hh:mm A");
                } else {
                    return '-';
                }
            }, formatDate(value) {
                if (value == null) return '--';

                if (value) {
                    return moment(String(value)).format("DD-MM-YY");
                } else {
                    return '-';
                }
            },
            formatDateOnly(value) {
                if (value == null) return '--';

                if (value) {
                    return moment(String(value)).format("DD");
                } else {
                    return '-';
                }
            },
            format(value) {
                const day = value.getDate();
                const month = value.getMonth() + 1;
                const year = value.getFullYear();

                return `${day}/${month}/${year}`;
            },
            countTotalPrice(subtotal, discount, freight, gstRate, rounding) {
                const netSubtotal = subtotal - discount;
                const gstAmount = (gstRate / 100) * netSubtotal;
                const total = netSubtotal + freight + gstAmount + rounding
                return Number(parseFloat(total).toFixed(2));
            }
        }

        const formattedDate = (datetime) => {
            const options = {
                year: "numeric", month: "short", day: "2-digit", hour: "2-digit", minute: "2-digit",
            };

            const date = new Date(datetime);
            return date.toLocaleString("en-US", options);
        }

        app.component('VueDatePicker', VueDatePicker);
        app.component('Select2', Select2);
        app.component('Swal', Swal);
        app.component('FsLightbox', FsLightbox);
        app.mount(el);
    },
}).then(r => {
})
