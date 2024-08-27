<script setup>
import Layout from '@/Layouts/Layout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import CreateConcurs from '../Dashboard/Actiuni/CreateConcurs.vue';
import CreateLac from '../Dashboard/Actiuni/CreateLac.vue';
import CreateMansa from '../Dashboard/Actiuni/CreateMansa.vue';
import CreateStand from '../Dashboard/Actiuni/CreateStand.vue';

</script>

<template>
    <Layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-2">
                <div class="mb-4" v-if="$props.warning">
                    <div v-if="$props.warning.status" class="bg-green-200 overflow-hidden shadow-sm rounded-lg mt-4">
                        <div class="p-6 text-gray-900 font-bold">{{ $props.warning.message }}</div>
                    </div>
                    <div v-else class="bg-red-200 overflow-hidden shadow-sm rounded-lg mt-4">
                        <div class="p-6 text-gray-900 font-bold">{{ $props.warning.message }}</div>
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl text-white font-bold">
                        Actiuni
                    </h1>
                    <div class="flex overflow-auto">
                        <CreateConcurs class="m-2" />
                        <CreateStand class="m-2" :lacuri="lacuri" />
                        <CreateLac class="m-2" />
                        <CreateMansa class="m-2" :concursuri="concursuri" :lacuri="lacuri" />
                    </div>
                </div>

                <div class="mt-4">
                    <h1 class="text-2xl text-white font-bold">
                        Gestioneaza
                    </h1>
                    <div class="flex overflow-auto">
                        <PrimaryButton @click="show('gestioneaza_concurs')" class="m-2">Concursurile Tale</PrimaryButton>
                    </div>
                </div>

                <div class="max-w-7xl mt-10">
                    <div v-if="gestioneaza_concurs">
                        <div class="relative overflow-x-auto">
                            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-4 mt-">
                                <div v-for="(concurs, index) in concursuri" :key="index"
                                    class="flex flex-col items-center border rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 border-gray-700 bg-gray-800 hover:bg-gray-700">
                                    <div class="flex flex-col justify-between p-4 leading-normal">
                                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-white">
                                            {{ concurs['Nume'] }}
                                        </h5>
                                        <p class="mb-3 font-normal text-gray-400">
                                            <li>
                                                ID: {{ concurs['id'] }}
                                            </li>
                                            <li>
                                                Nume Org. : {{ concurs['Nume Organizator'] }}
                                            </li>
                                            <li>
                                                Descriere: {{ concurs['Descriere Concurs'] }}
                                            </li>
                                            <li>
                                                Regulament: {{ concurs['Regulament Concurs'] }}
                                            </li>
                                            <li>
                                                Incepere: {{ concurs['Start Concurs'] }}
                                            </li>
                                            <li>
                                                Incheiere: {{ concurs['Inchidere Concurs'] }}
                                            </li>
                                        </p>
                                        <div>
                                            <a :href="route('gestioneaza.concurs', concurs['id'])">
                                                <PrimaryButton>Configureaza</PrimaryButton>
                                            </a>
                                        </div>
                                    </div>
                                </div>
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
        concursuri: Array,
        lacuri: Array,
        warning: Array
    },

    data: () => ({
        gestioneaza_concurs: false
    }),

    methods: {
        show(gestionare) {
            switch (gestionare) {
                case 'gestioneaza_concurs':
                    if (this.gestioneaza_concurs) {
                        return this.gestioneaza_concurs = false
                    }

                    return this.gestioneaza_concurs = true
                    break;

                default:
                    break;
            }
        }
    },
}
</script>