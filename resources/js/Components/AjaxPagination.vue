<template>
    <div class="pagi mt-8">
        <div class="pagi-item pagi-action pagi-prev" :class="{ 'is-disabled': !links[0].url }">
            <a @click="fetchStoreProduct(getPage(links[0].url))" class="page-link" preserve-scroll>
                <em class="fa-solid fa-chevron-left"></em>
            </a>
        </div>
        <ul class="pagi-list">
            <template v-for="(link, index) in links" :key="index">
                <li class="pagi-item" :class="{ disabled: !link.url, active: link.active }" v-if="index !== 0 && index !== links.length - 1">
                    <a @click="fetchStoreProduct(getPage(link.url))" class="page-link" preserve-scroll>
                        <span :class="link.active ? 'text-white' : ''">
                            {{ link.label }}
                        </span>
                    </a>
                </li>
            </template>
        </ul>
        <div class="pagi-item pagi-action pagi-next" :class="{ 'is-disabled': !links[links.length - 1].url }">
            <a @click="fetchStoreProduct(getPage(links[links.length - 1].url))" class="page-link" preserve-scroll>
                <em class="fa-solid fa-chevron-right"></em>
            </a>
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    links: Array,
    fetchStoreProduct: {
        type: Function,
        required: true,
    },
});

const getPage = (fullUrl) => {
    const url = new URL(fullUrl);
    const page = url.searchParams.get("page");
    return page
}
</script>
