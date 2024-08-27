<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

import Layout from '@/Layouts/Layout.vue';

import axios from 'axios';
</script>

<template>
    <Layout>
        <div class="py-12 p-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden sm:rounded-lg text-center">
                    <h1 class="text-5xl text-white text-start rounded-xl">Concursuri</h1>
                    <div class="mb-4" v-if="$props.warning">
                        <div v-if="$props.warning.status" class="bg-green-200 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-gray-900 font-bold">{{ $props.warning.message }}</div>
                        </div>
                        <div v-else class="bg-red-200 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-gray-900 font-bold">{{ $props.warning.message }}</div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-10">
                    <div class="rounded-lg bg-black bg-opacity-20" v-for="(concurs, index) in   concursuri  " :key="index">
                        <div class="rounded-lg overflow-hidden ">
                            <!-- <img v-bind:src="'/storage/Concursuri/' + concurs['Poza Concurs']" alt="concurs_poza"> -->
                            <!-- <img :src="'../storage/Concursuri/' + concurs['Poza Concurs']" alt=""> -->
                            <img class="w-full" src="https://img.freepik.com/free-photo/painting-mountain-lake-with-mountain-background_188544-9126.jpg?size=626&ext=jpg&ga=GA1.1.1222169770.1701820800&semt=sph"
                                alt=""> 
                            <div class="px-6 py-4">
                                <div class="font-bold text-xl mb-2 text-white">{{ concurs.Nume }}</div>
                                <p class="text-white text-base">
                                    {{ concurs['Descriere Concurs'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pt-4 pb-2 flex justify-between items-center">
                            <PrimaryButton @click="openModal(concurs.id)" v-if="$page.props.auth.user">Inscriete</PrimaryButton>
                        </div>
                    </div>

                    <Modal :show=" modalBoolean " @close=" closeModal ">
                        <div class="p-6 rounded-xl">
                            <h1 class="text-5xl text-white rounded-xl">{{ concurs.Nume }}</h1>

                            <div class="grid grid-cols-1 sm:grid-cols{{ -2 m }}d:grid-cols-2 lg:grid-cols-2 gap-4 mt-10">
                                <div class="bg-black bg-opacity-50 p-4 rounded-lg w-full">
                                    <h1 class="text-white">Concurs Descriere</h1>
                                    <p class="text-white font-bold mt-4">
                                        {{ concurs['Descriere Concurs'] }}
                                    </p>
                                </div>
                                <div class="bg-black bg-opacity-50 p-4 rounded-lg w-full">
                                    <h1 class="text-white">Concurs Reguli</h1>
                                    <p class="text-white font-bold mt-4">
                                        {{ concurs['Regulament Concurs'] }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-2 mt-4 text-center">
                                <div>
                                    <div class="rounded-lg w-full mb-2" v-for="(  mansa, index  ) in   manse  " :key=" index ">
                                        <div class="flex justify-center items-center gap-6 w-full">
                                            <h1 class="bg-black bg-opacity-50 p-1 rounded-lg text-white px-12 w-full">
                                                {{ mansa['Nume Mansa'] }}
                                                <Checkbox :checked=" false " :id=" index "
                                                    @click="adaugaMansa(mansa, index)" type="checkbox" class="ml-4" />
                                            </h1>
                                            <PrimaryButton @click="getMansaDetails(mansa.id)"
                                                class="inline-block bg-black text-white bg-opacity-50 rounded-full text-sm font-semibold">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7.82054 20.7313C8.21107 21.1218 8.84423 21.1218 9.23476 20.7313L15.8792 14.0868C17.0505 12.9155 17.0508 11.0167 15.88 9.84497L9.3097 3.26958C8.91918 2.87905 8.28601 2.87905 7.89549 3.26958C7.50497 3.6601 7.50497 4.29327 7.89549 4.68379L14.4675 11.2558C14.8581 11.6464 14.8581 12.2795 14.4675 12.67L7.82054 19.317C7.43002 19.7076 7.43002 20.3407 7.82054 20.7313Z"
                                                        fill="#FFF" />
                                                </svg>
                                            </PrimaryButton>
                                        </div>
                                    </div>
                                </div>
                                <div v-if=" mansaDetails ">
                                    <div class="bg-black bg-opacity-50 rounded-lg w-full text-start">
                                        <div class="p-4">
                                            <h1 class="text-white text-center mb-4">Detalii Mansa</h1>
                                            <p class="text-white font-bold">Concurs: {{ mansaDetails['Nume Concurs'] }}</p>
                                            <p class="text-white font-bold">Mansa: {{ mansaDetails['Nume Mansa'] }}</p>
                                            <p class="text-white font-bold">Lac: {{ mansaDetails['Nume Lac'] }}</p>
                                            <p class="text-white font-bold">Incepere: {{ mansaDetails['Start Mansa'] }}</p>
                                            <p class="text-white font-bold">Incheiere: {{ mansaDetails['Final Mansa'] }}</p>
                                            <p class="text-white font-bold">Status: {{ mansaDetails['Status Mansa'] }}</p>
                                            <p class="text-white font-bold">Participanti: {{ mansaDetails['Participanti'] }}
                                                / {{ mansaDetails['Participanti Maximi'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-2 mt-10">
                                <div class="rounded-lg w-full">
                                    <span
                                        class="inline-block bg-black text-white bg-opacity-50 rounded-full px-3 py-1 text-sm font-semibold">Incepere:
                                        {{ concurs['Start Concurs'] }}</span>
                                </div>
                                <div class="rounded-lg w-full">
                                    <span
                                        class="inline-block bg-black text-white bg-opacity-50 rounded-full px-3 py-1 text-sm font-semibold">Incheiere:
                                        {{ concurs['Inchidere Concurs'] }}</span>
                                </div>
                                <div>
                                    <span
                                        class="inline-block bg-black text-white bg-opacity-50 rounded-full px-3 py-1 text-sm font-semibold">
                                        Organizator: {{ concurs['Nume Organizator'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mt-4">
                                <SecondaryButton class="m-2 text-sm" @click=" closeModal ">Inchide</SecondaryButton>
                                <PrimaryButton class="m-2 text-sm" @click=" inscriete ">INSCRIETE</PrimaryButton>
                            </div>
                        </div>
                    </Modal>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    props: {
        concursuri: Object,
        warning: Array
    },

    data: () => ({
        modalBoolean: false,

        manse_selectate: [],
        manse: '',
        mansaDetails: '',
        concurs: '',
        storagePath: '',
    }),

    mounted() { this.getStoragePath() },

    methods: {
        async adaugaMansa(mansa, index) {
            var checked = document.getElementById(index).checked;

            if (checked) {
                const find = this.manse_selectate.find(element => { if (mansa.id === element.id) return true; return false; });

                if (find !== undefined) return;

                return this.manse_selectate.push(mansa);
            }

            const newArr = this.manse_selectate.filter(object => {
                return object.id !== mansa.id;
            });

            return this.manse_selectate = newArr;
        },

        async inscriete() {
            const response = await axios.post(route('inscriere'), {
                manse: this.manse_selectate,
                concurs: this.concurs
            });

            if (response.data) {
                this.manse_selectate = [];
                this.closeModal();
            }
        },

        async openModal(id) {
            const response = await axios.post(route('get.concurs'), {
                id: id
            });

            this.concurs = response.data.concurs
            this.manse = response.data.manse
            this.modalBoolean = true;
        },

        closeModal() {
            this.modalBoolean = false;
            this.manse_selectate = [];
        },

        async getMansaDetails(id) {
            const response = await axios.post(route('get.mansa.details'), {
                id: id,
            });
            this.mansaDetails = response.data
        },

        async getStoragePath() {
            try {
                const response = await axios.post(route('get.storage.path'));
                this.storagePath = response.data;
            } catch (error) {
                console.log(error);
            }
        }
    },
}
</script>