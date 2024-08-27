<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
</script>

<template>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-12 text-gray-900 text-center text-4xl">QRCode URL.</div>

            <div class="pb-6 max-w-3xl mx-auto">
                <form @submit.prevent="submit" class="px-6">
                    <div>
                        <div class="flex">
                            <p
                                class="flex-shrink-0 z-10 inline-flex items-center tracking-widest py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 dark:border-gray-700 dark:text-white rounded-l-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                URL</p>
                            <div class="relative w-full">
                                <input type="url" id="url" required autofocus autocomplete="url" v-model="form.url"
                                    class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-lg border-l-gray-100 border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                                    placeholder="https://localhost/">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label for="first_name"
                                class="block text-sm font-medium text-gray-900 dark:text-white">Size</label>
                            <input type="number" v-model="form.size"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="100">
                        </div>

                        <InputError class="mt-2" :message="form.errors.url" />
                    </div>
                    <div class="flex justify-around mt-4">
                        <div>
                            <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Convert
                            </PrimaryButton>
                        </div>
                    </div>
                </form>

                <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
                    <h1 class="text-xl font-bold">Your QR</h1>
                    <div v-html="this.$page.props.qrcode"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        qrcode: String
    },
    data() {
        return {
            form: useForm({
                url: '',
                size: ''
            })
        };
    },

    methods: {
        submit() {
            this.form.post(route('qrcode.generate'), {
                onFinish: () => this.form.reset('url', 'size'),
            });
        },
    }
}
</script>