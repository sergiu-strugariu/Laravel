<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

import VueDatePicker from '@vuepic/vue-datepicker';
import MazPhoneNumberInput from 'maz-ui/components/MazPhoneNumberInput';

import { useForm } from '@inertiajs/vue3';
</script>

<template>
    <section class="bg-gray-900">
        <div class="flex justify-center min-h-screen">
            <div class="hidden bg-cover lg:block lg:w-2/5"
                style="background-image: url('https://images.pexels.com/photos/1630039/pexels-photo-1630039.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1')">
            </div>



            <div class="flex items-center w-full max-w-3xl p-8 mx-auto lg:px-12 lg:w-3/5">
                <div class="w-full">
                    <div v-if="$props.warning" class="mb-10">
                        <div v-if="$props.warning.status" class="bg-gray-700 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-white font-bold">{{ $props.warning.message }}</div>
                        </div>
                        <div v-else class="bg-red-800 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-white font-bold">{{ $props.warning.message }}</div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h1 class="text-4xl font-bold tracking-tight sm:text-center sm:text-6xl ">
                            <span class="text-white">Inregistrare</span>
                            <span
                                class="text-6xl text-center text-transparent dark:bg-gradient-to-brc dark:from-pink-400 dark:to-red-600 bg-clip-text bg-gradient-to-br from-pink-400 to-red-600">
                                Fish Arena.
                            </span>
                        </h1>
                    </div>

                    <div class="mt-10">
                        <h1 class="text-2xl font-semibold tracking-wider text-white">
                            Creează-ți cont acum cu un singur click.
                        </h1>

                        <p class="mt-4 text-gray-400">
                            Hai vă setăm totul, astfel încât să vă puteți verifica contul și să începeți să participati la
                            concursuri.
                        </p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2 mt-4">
                            <div class="w-full">
                                <InputLabel for="mobile" class="text-white font-bold text-xl mb-1"
                                    value="Numar de Telefon" />

                                <MazPhoneNumberInput :translations="{
                                    countrySelector: {
                                        placeholder: 'Prefix',
                                        error: 'Alege tara',
                                        searchPlaceholder: 'Cauta tara',
                                    },
                                    phoneInput: {
                                        placeholder: 'Numar de Telefon',
                                        example: 'Exemplu:',
                                    },
                                }" :custom-countries-list="{
    Ro: 'Romania',
}" show-code-on-list @update="results = $event" class="bg-transparent" country-locale="ro-RO" v-model="form.mobile"
                                    no-flags="true" :preferred-countries="['RO']" default-country-code="RO"
                                    color="transparent" />

                                <InputError class="mt-2" :message="form.errors.mobile" />
                            </div>
                            <div class="w-full">
                                <InputLabel for="data_nasterii" value="Data Nasterii"
                                    class="text-white font-bold text-xl mb-1" />

                                <VueDatePicker v-model="form.data_nasterii" required class="rounded-lg mt-2">
                                </VueDatePicker>

                                <InputError class="mt-2" :message="form.errors.data_nasterii" />
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-around mt-4">
                                <div
                                    class="flex items-center px-10 py-2 rounded-lg text-black">
                                    <InputLabel for="barbat" value="Barbat"
                                        class="text-white  font-bold text-xl mb-1" />
                                    <Checkbox id="barbat" type="radio" name="sex" class="ml-4" v-model="form.sex" checked />
                                </div>
                                <div
                                    class="flex items-center px-10 py-2 rounded-lg">
                                    <InputLabel for="femeie" value="Femeie" class="text-white font-bold text-xl mb-1" />
                                    <Checkbox id="femeie" type="radio" name="sex" class="ml-4" v-model="form.sex" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="">
                                Inregistrare
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script>

export default {
    props: {
        warning: Array,
    },

    data() {
        return {
            form: useForm({
                email: '',
                mobile: '',
                data_nasterii: '',
                sex: Boolean
            }),

            sex: true,
            email: ''
        }
    },

    methods: {
        submit() {
            const urlParams = new URLSearchParams(window.location.search);

            this.sex = document.getElementById('barbat').checked
            this.form.email = urlParams.get('email');

            if (!this.sex) {
                this.form.sex = this.sex
                return this.form.post(route('provider.update', {
                    onFinish: () => this.form.reset("email", "data_nasterii", "mobile")
                }));
            }

            this.form.sex = this.sex
            return this.form.post(route('provider.update', {
                onFinish: () => this.form.reset("email", "data_nasterii", "mobile")
            }));
        }
    },
}

</script>