<script setup>
import Layout from '@/Layouts/Layout.vue';
</script>

<template>
    <Layout>
        <div class="relative px-6 lg:px-8 overflow-hidden">
            <div class="mx-auto max-w-7xl">
                <div>
                    <div v-if="$props.warning" class="mb-10">
                        <div v-if="$props.warning.status" class="bg-gray-700 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-white font-bold">{{ $props.warning.message }}</div>
                        </div>
                        <div v-else class="bg-red-800 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-white font-bold">{{ $props.warning.message }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h1 class="text-7xl text-white text-start rounded-xl mb-4">Palmares.</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
                        <div v-for="(concurs, index) in istoric" :key="index"
                             class="flex flex-col  border rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 border-gray-700 bg-gray-800 hover:bg-gray-700">
                            <div class="flex flex-col justify-between p-4 leading-normal">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-white">
                                    {{ concurs['nume_concurs'] }}
                                </h5>
                                <p class="mb-3 font-normal text-gray-400 ml-4">
                                    <li class="text-xl">
                                        Concurent: {{ concurs['pescar'] }}
                                    </li>
                                    <li class="text-xl">
                                        Mansa: {{ concurs['mansa'] }}
                                    </li>
                                    <li class="text-xl">
                                        Lac: {{ concurs['lac'] }}
                                    </li>
                                    <li class="text-xl">
                                        Stand: {{ concurs['stand'] }}
                                    </li>
                                    <li class="font-extrabold text-white text-xl">
                                        Cantitate: {{ concurs['cantitate'] }}
                                    </li>
                                    <li class="font-extrabold text-white text-xl">
                                        Puncte: {{ concurs['puncte'] }}
                                    </li>
                                    <li class="text-xl">
                                        Loc sector: {{ concurs['loc_sector'] }}
                                    </li>
                                    <li class="text-xl">
                                        Loc general: {{ concurs['loc_general'] }}
                                    </li>
                                </p>
                                <h1 class="text-white text-xl">Data Concurs: {{ concurs['data_concurs'] }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    props: {
        istoric: Array
    }
}
</script>
