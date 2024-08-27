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
                        <h1 class="text-4xl font-bold tracking-tight sm:text-center sm:text-6xl text-white">
                            Creare cont
                            <span
                                class="text-6xl text-center text-transparent bg-clip-text bg-gradient-to-br from-pink-400 to-red-600">
                                Fish Arena.
                            </span>
                        </h1>
                    </div>

                    <div class="mt-10">
                        <h1 class="text-2xl font-semibold tracking-wider text-white">
                            Creează-ți cont acum cu un singur click.
                        </h1>

                        <p class="mt-4 text-gray-400">
                            Completati datele solicitate pentru a crea un cont!
                        </p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-10">
                            <div>
                                <InputLabel for="nume" value="Nume" class="text-white" />

                                <TextInput id="nume" placeholder="Ionut" type="text" class="mt-1 block w-full"
                                           v-model="form.nume" required autocomplete="nume" />

                                <InputError class="mt-2" :message="form.errors.nume" />
                            </div>
                            <div>
                                <InputLabel for="prenume" value="Prenume" class="text-white" />

                                <TextInput id="prenume" type="text" placeholder="Alexandru" class="mt-1 block w-full"
                                           v-model="form.prenume" required autocomplete="prenume" />

                                <InputError class="mt-2" :message="form.errors.prenume" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2 mt-4">
                            <div class="w-full">
                                <InputLabel for="mobile" class="text-white font-bold text-xl mb-1"
                                            value="Numar de Telefon" />

                                <MazPhoneNumberInput  :translations="{
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
                                <div class="flex items-center border px-10 py-2 rounded-lg ">
                                    <InputLabel for="barbat" value="Barbat" class="text-white font-bold text-xl mb-1" />
                                    <Checkbox id="barbat" type="radio" name="sex" class="ml-4" v-model="form.sex" checked />
                                </div>
                                <div class="flex items-center border px-10 py-2 border-white rounded-lg">
                                    <InputLabel for="femeie" value="Femeie" class="text-white font-bold text-xl mb-1" />
                                    <Checkbox id="femeie" type="radio" name="sex" class="ml-4" v-model="form.sex" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white text-white focus:ring-4 focus:outline-none focus:ring-blue-800">
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
                nume: '',
                prenume: '',
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
                return this.form.post(route('inregistrare.store', {
                    onFinish: () => this.form.reset("nume", "prenume", "email", "data_nasterii", "mobile")
                }));
            }

            this.form.sex = this.sex
            return this.form.post(route('inregistrare.store', {
                onFinish: () => this.form.reset("nume", "prenume", "email", "data_nasterii", "mobile")
            }));
        }

        // this.form.post(route('autentificare.request'), {
        //         onSuccess: () => this.form.reset(),
        //         onError: () => console.log("Erroare autentificare."),
        //         onFinish: () => [],
        //     });
    },
}

</script>
