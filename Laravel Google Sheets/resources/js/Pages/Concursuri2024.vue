<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';

import Layout from '@/Layouts/Layout.vue';
</script>

<template>
    <Layout>
        <div class="relative px-6 lg:px-8 overflow-hidden">
            <div class="mx-auto max-w-7xl pt-20">
                <div>
                    <h1 class="text-7xl text-white text-start rounded-xl mb-4">Concursuri 2024</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-">
                        <div v-for="(concurs, index) in concursuri" :key="index"
                            class="flex flex-col items-center border  rounded-lg shadow md:flex-row md:max-w-xl border-gray-700 bg-gray-800 hover:bg-gray-700">
                            <div class="flex flex-col justify-between p-4 leading-normal">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-white">
                                    {{ concurs['Nume concurs'] }}
                                </h5>
                                <p class="mb-3 font-normal text-gray-400 ">
                                    <li class="text-lg">
                                        Organizator: {{ concurs['Organizator'] }}
                                    </li>
                                    <li class="text-lg">
                                        Telefon organizator: {{ concurs['Telefon organizator'] }}
                                    </li>
                                    <li class="text-lg">
                                        Tip concurs: {{ concurs['Tip concurs'] }}
                                    </li>
                                    <li class="text-lg">
                                        Lac: {{ concurs['Lac'] }}
                                    </li>
                                    <li class="text-lg">
                                        Modalitate organizare: {{ concurs['Modalitate organizare'] }}
                                    </li>
                                    <li class="font-extrabold text-white text-lg">
                                        Data inceput: {{ concurs['Data inceput'] }}
                                    </li>
                                    <li class="font-extrabold text-white text-lg">
                                        Data inchidere: {{ concurs['Data inchidere'] }}
                                    </li>
                                </p>

                                <a v-if="concurs['Link catre pagina concurs']" :href="concurs['Link catre pagina concurs']">
                                    <PrimaryButton>Catre Concurs</PrimaryButton> 
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
export default {
    props: {
        concursuri: Array
    }
}
</script>