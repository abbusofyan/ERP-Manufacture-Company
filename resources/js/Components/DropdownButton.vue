<template>
    <div class="dropdown" @click.self="closeDropdown">
        <button class="dropdown-toggle btn btn-purple" @click="toggleDropdown">
            <slot name="button">Dropdown</slot>
            <span class="ml-10 fa fa-angle-down" v-if="isOpen"></span>
            <span class="ml-10 fa fa-angle-up" v-else></span>
        </button>

        <div v-show="isOpen" class="dropdown-content">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const isOpen = ref(false);

function toggleDropdown() {
    isOpen.value = !isOpen.value;
}

function closeDropdown() {
    isOpen.value = false;
}
</script>

<style>
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle:focus {
    outline: none;
}

.dropdown-content {
    position: absolute;
    right: 0; /* Align dropdown menu to the right of the button */
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 1;
    min-width: 200px; /* Adjusted width to fit text */
    border-radius: 5px;
    overflow: hidden;
}

.dropdown-content .dropdown-item {
    width: 100%;
    background: none;
    border: none;
    padding: 10px;
    text-align: left;
    cursor: pointer;
}

.dropdown-content .dropdown-item:hover {
    background-color: #f1f1f1;
}

.dropdown-content[style*="display: block"] {
    display: block !important;
}
</style>
