<template>
    <div>
        <select class="form-control" :id="id" :name="name" :disabled="disabled" :required="required"></select>
    </div>
</template>

<script>
import * as jQuery from 'jquery';
// window.jQuery = jQuery;
import 'select2/dist/css/select2.min.css'
import 'select2/dist/js/select2.full';

export default {
    name: 'Select2',
    data() {
        return {
            select2: null
        };
    },
    emits: ['update:modelValue'],
    props: {
        modelValue: [String, Array],
        id: {
            type: String,
            default: ''
        },
        name: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        options: {
            type: Array,
            default: () => []
        },
        disabled: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: false
        },
        settings: {
            type: Object,
            default: () => {
            }
        },
    },
    watch: {
        options: {
            handler(val) {
                this.setOption(val);
            },
            deep: true
        },
        modelValue: {
            handler(val) {
                this.setValue(val);
            },
            deep: true
        },
    },
    methods: {
        setOption(val = []) {
            this.select2.empty();
            this.select2.select2({
                placeholder: this.placeholder,
                ...this.settings,
                data: val
            });
            this.setValue(this.modelValue);
        },
        setValue(val) {
            if (val instanceof Array) {
                this.select2.val([...val]);
            } else {
                this.select2.val([val]);
            }
            this.select2.trigger('change');
        },
        reset() {
            this.$emit('update:modelValue', null);
            this.select2.val(null).trigger('change');
        }
    },
    mounted() {
        this.select2 = $(this.$el)
            .find('select')
            .select2({
                placeholder: this.placeholder,
                ...this.settings,
                data: this.options
            })
            .on('select2:select select2:unselect', ev => {
                this.$emit('update:modelValue', this.select2.val());
                this.$emit('select', ev['params']['data']);
            });
        this.setValue(this.modelValue);
    },
    beforeUnmount() {
        this.select2.select2('destroy');
    }
};
</script>
