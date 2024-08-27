<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';

import Layout from '@/Layouts/Layout.vue';
</script>

<template>
    <Layout>
        <div class="relative px-6 lg:px-8 overflow-hidden">
            <div class="mx-auto max-w-3xl pt-20 pb-32 sm:pt-48 sm:pb-40">
                <div>
                    <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                        <div
                            class="relative overflow-hidden rounded-full py-1.5 px-4 text-sm leading-6 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                            <span class="text-gray-400">
                                Vezi concursurile <span
                                    class="text-transparent bg-clip-text bg-gradient-to-br from-pink-400 to-red-600">Fish Arena.
                                </span>
                                <a :href="route('concursuri')" class="font-semibold text-indigo-600">Concursuri <span
                                        aria-hidden="true">&rarr;</span> </a>
                            </span>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold tracking-tight sm:text-center sm:text-6xl text-white">
                            Bun venit pe
                            <span
                                class="text-6xl text-center text-transparent bg-clip-text bg-gradient-to-br from-pink-400 to-red-600">
                                Fish Arena.
                            </span>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

