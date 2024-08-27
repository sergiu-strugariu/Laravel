<script setup>
import Layout from "@/Layouts/Layout.vue";
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
                                Asociaza-ti palaresul pe <span
                                class="text-transparent bg-clip-text bg-gradient-to-br from-pink-400 to-red-600">Fish Arena.
                                </span>
                                <a :href="route('asociaza')" class="font-semibold text-indigo-600">Palmares <span
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
                        <div class="mt-8 flex gap-x-4 sm:justify-center">
                            <div class="relative inline-flex  group">
                                <div
                                    class="absolute transitiona-all duration-1000 opacity-70 -inset-px bg-gradient-to-r from-[#44BCFF] via-[#FF44EC] to-[#FF675E] rounded-xl blur-lg group-hover:opacity-100 group-hover:-inset-1 group-hover:duration-200 animate-tilt">
                                </div>
                                <a href="#"
                                   class="relative inline-flex items-center justify-center px-8 py-4 text-sm text-white transition-all duration-200 bg-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                                    Concursuri
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>

</script>
