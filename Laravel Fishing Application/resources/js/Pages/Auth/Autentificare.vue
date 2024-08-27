<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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
                        <h1 class="text-4xl flex justify-around items-center font-bold tracking-tight sm:text-center sm:text-6xl text-white ">
                            Autentificare
                            <img src="https://www.arena.fish/logo.png" width="100" alt="">

                        </h1>
                    </div>

                    <div class="mt-10">
                        <h1 class="text-2xl font-semibold tracking-wider text-white">
                            Creează-ți cont acum cu un singur click.
                        </h1>

                        <p class="mt-4 text-gray-400">
                            Puteti folosi fie conectarea cu Google, fie completati emailul si veti primi un link de conectare pe email, nu este necesar sa tineti minte nicio parola!
                        </p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="mt-10">
                            <InputLabel for="email" value="Adresa de Email" class="text-white" />

                            <TextInput id="email" type="text" placeholder="email@example.com" class="mt-1 block w-full"
                                       v-model="form.email" required autocomplete="email" />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white text-white focus:ring-4 focus:outline-none focus:ring-blue-800">
                                Autentificare
                            </PrimaryButton>
                        </div>
                    </form>

                    <div class="flex items-center justify-between mt-10">
                        <span class="w-1/5 border-b text-white lg:w-1/4"></span>

                        <a href="#" class="text-xs text-center text-white uppercase  hover:underline">sau</a>

                        <span class="w-1/5 border-b text-white lg:w-1/4"></span>
                    </div>

                    <!-- GOOGLE REGISTER -->
                    <a href="/google/redirect"
                       class="flex items-center cursor-pointer justify-center mt-4 text-gray-600 transition-colors duration-300 transform border rounded-lg hover:bg-gray-50">
                        <div class="px-4 py-2">
                            <svg class="w-6 h-6" viewBox="0 0 40 40">
                                <path
                                    d="M36.3425 16.7358H35V16.6667H20V23.3333H29.4192C28.045 27.2142 24.3525 30 20 30C14.4775 30 10 25.5225 10 20C10 14.4775 14.4775 9.99999 20 9.99999C22.5492 9.99999 24.8683 10.9617 26.6342 12.5325L31.3483 7.81833C28.3717 5.04416 24.39 3.33333 20 3.33333C10.7958 3.33333 3.33335 10.7958 3.33335 20C3.33335 29.2042 10.7958 36.6667 20 36.6667C29.2042 36.6667 36.6667 29.2042 36.6667 20C36.6667 18.8825 36.5517 17.7917 36.3425 16.7358Z"
                                    fill="#FFC107" />
                                <path
                                    d="M5.25497 12.2425L10.7308 16.2583C12.2125 12.59 15.8008 9.99999 20 9.99999C22.5491 9.99999 24.8683 10.9617 26.6341 12.5325L31.3483 7.81833C28.3716 5.04416 24.39 3.33333 20 3.33333C13.5983 3.33333 8.04663 6.94749 5.25497 12.2425Z"
                                    fill="#FF3D00" />
                                <path
                                    d="M20 36.6667C24.305 36.6667 28.2167 35.0192 31.1742 32.34L26.0159 27.975C24.3425 29.2425 22.2625 30 20 30C15.665 30 11.9842 27.2359 10.5975 23.3784L5.16254 27.5659C7.92087 32.9634 13.5225 36.6667 20 36.6667Z"
                                    fill="#4CAF50" />
                                <path
                                    d="M36.3425 16.7358H35V16.6667H20V23.3333H29.4192C28.7592 25.1975 27.56 26.805 26.0133 27.9758C26.0142 27.975 26.015 27.975 26.0158 27.9742L31.1742 32.3392C30.8092 32.6708 36.6667 28.3333 36.6667 20C36.6667 18.8825 36.5517 17.7917 36.3425 16.7358Z"
                                    fill="#1976D2" />
                            </svg>
                        </div>

                        <span class="w-5/6 px-4 py-3 font-bold text-center">Conectează-te cu Google</span>
                    </a>
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
            }),
        }
    },

    mounted() {
        console.log(this.$page.props);
    },

    methods: {
        submit() {
            this.form.post(route('autentificare.request', this.form.email), {
                onSuccess: () => this.form.reset(),
                onError: () => console.log("Erroare autentificare."),
                onFinish: () => [],
            });
        }
    },
}

</script>
